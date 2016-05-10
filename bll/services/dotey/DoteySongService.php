<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: DoteySongService.php 17581 2014-01-16 07:18:23Z hexin $ 
 * @package 
 */
define('SONG_PIPIEGG',1000);
define('SONG_UNHANDLE',0);       //歌曲未处理
define('SONG_HANDLE',1);       //歌曲已处理
define('SONG_CANCEL',2);       //歌曲已取消
class DoteySongService extends PipiService {
	
	/**
	 * 用户点歌
	 * @param int $uid  点歌用户uid
	 * @param int $dotey_id  主播uid
	 * @param int $achives_id 所在的档期Id
	 * @param array $song   歌曲Id|歌曲名称和歌手
	 * @return mix|boolean
	 */
	public function demandSong($uid,$dotey_id,$achives_id,array $songs){
		if($uid<=0||$achives_id<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		
		$doteySongService=new DoteySongService();
		if(isset($songs['song_id'])){
			$song=$doteySongService->getDoteySongBySongId($songs['song_id']);
			if($dotey_id!=$song['dotey_id']){
				return $this->setError(Yii::t('doteySong','The song does not belong to the dotey'),0);
			}
		}else{
			if(empty($songs['name'])||empty($songs['singer']))
				return $this->setError(Yii::t('doteySong','Song is error'),0);
			$song['song_id']=0;
			$song['name']=$songs['name'];
			$song['singer']=$songs['singer'];
			$song['pipiegg']=SONG_PIPIEGG;
			$song['charm']=Yii::app()->params['change_relation']['pipiegg_to_charm']*SONG_PIPIEGG;
			$song['charm_points']=Yii::app()->params['change_relation']['pipiegg_to_charmpoints']*SONG_PIPIEGG;
			$song['dedication']=Yii::app()->params['change_relation']['pipiegg_to_dedication']*SONG_PIPIEGG;
			//$song['egg_points']=Yii::app()->params['change_relation']['pipiegg_to_eggpoints']*SONG_PIPIEGG;
		}
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByArchivesId($achives_id);
		if($archives['uid']!=$dotey_id){
			return $this->setError(Yii::t('archives','Archives is error'),0);
		}
		$consumeService=new ConsumeService();
		if($consumeService->consumeEggs($uid,$song['pipiegg'])<= 0){
			return $this->setError(Yii::t('common','Pipiegg not enough'),0);
		}
		$record['song_id']=$song['song_id'];
		$record['target_id']=$achives_id;
		$record['uid']=$uid;
		$record['to_uid']=$dotey_id;
		$record['name']=$song['name'];
		isset($song['singer'])&&$record['singer']=$song['singer'];
		$record['pipiegg']=$song['pipiegg'];
		$record['charm']=$song['charm'];
		$record['charm_points']=$song['charm_points'];
		$record['dedication']=$song['dedication'];
		$record['egg_points']=$song['egg_points'];
		$record['is_handle']=0;
		$recordId=$doteySongService->saveUserSongRecords($record);
		$filename = DATA_PATH.'runtimes/demand_song_records.txt';
		error_log(date("Y-m-d H:i:s")."存储用户点歌记录：".json_encode($record)."\n\r",3,$filename);
		if(!$recordId){
			return $this->setError(Yii::t('doteySong','Song records is write failed'),0);
		}
		
		$consumeService->saveUserConsumeAttribute(array('uid'=>$uid,'pipiegg'=>$record['pipiegg']));
		$userBasicService=new UserService();
		$userBasic=$userBasicService->getUserFrontsAttributeByCondition($uid,true);
		$zmq=$this->getZmq();
		$eventData['archives_id']=$achives_id;
		$eventData['domain']=DOMAIN;
		$eventData['type']='localroom';
		$json_content['type']='demandSong';
		$json_content['uid']=$uid;
		$json_content['dotey_uid']=$dotey_id;
		$json_content['song_record_id']=$recordId;
		$json_content['record_id']=$archives['live_record']['record_id'];
		$json_content['nickname']=$userBasic['nk'];
		$json_content['name']=$song['name'];
		$eventData['json_content']=$json_content;
		$zmq->sendZmqMsg(606,$eventData);
		return $recordId;
	}
	
	
	/**
	 * 主播处理用户点的歌曲
	 * @param int $recordId  点歌记录id
	 * @param int $dotey_id  主播id
	 * @param int $archivesId  档期ID
	 * @return mix|boolean
	 */
	public function actSong($recordId,$dotey_id,$archivesId){
		if($recordId<=0||$dotey_id<=0||$archivesId<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),-1);
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByArchivesId($archivesId);
		if($archives['uid']!=$dotey_id){
			return $this->setError(Yii::t('common','Parameter is error'),-2);
		}
		$songRecord=$this->getUserSongRecordsByRecordIds($recordId);
		$consumeService=new ConsumeService();
		$result=$consumeService->actSong($recordId);
		if($result<=0){
			return $this->setError(Yii::t('doteySong','Song handle failed'),0);
		}
		$filename = DATA_PATH.'runtimes/act_song_records.txt';
		error_log(date("Y-m-d H:i:s")."存储主播确认点歌记录：".json_encode($songRecord)."\n\r",3,$filename);
		$pipieggRecords['uid'] = $songRecord[$recordId]['uid'];
		$pipieggRecords['pipiegg'] = $songRecord[$recordId]['pipiegg'];
		$pipieggRecords['from_target_id'] =$songRecord[$recordId]['song_id'];
		$pipieggRecords['num'] = 1;
		$pipieggRecords['to_target_id'] = $archivesId;
		$pipieggRecords['record_sid'] = $recordId;
		$pipieggRecords['source']=SOURCE_SONGS;
		$pipieggRecords['sub_source']=SUBSOURCE_SONGS_DEMANDSONG;
		$pipieggRecords['extra']=$songRecord[$recordId]['name'].'x1';
		$consumeService->saveUserPipiEggRecords($pipieggRecords, false);
		
		//写入用户贡献值记录
		$dedicationRecords['uid'] = $songRecord[$recordId]['uid'];
		$dedicationRecords['dedication'] = $songRecord[$recordId]['dedication'];
		$dedicationRecords['num'] = 1;
		$dedicationRecords['from_target_id'] = $songRecord[$recordId]['song_id'];
		$dedicationRecords['to_target_id'] = $archivesId;
		$dedicationRecords['record_sid'] = $recordId;
		$dedicationRecords['source']=SOURCE_SONGS;
		$dedicationRecords['sub_source']=SUBSOURCE_SONGS_DEMANDSONG;
		$dedicationRecords['info']=$songRecord[$recordId]['name'].'x1';
		if($consumeService->saveUserDedicationRecords($dedicationRecords, true)){
			$consumeAttriute['uid']=$songRecord[$recordId]['uid'];
			$consumeAttriute['dedication']= $songRecord[$recordId]['dedication'];
			$consumeService->saveUserConsumeAttribute($consumeAttriute);
		}
		
			
		//写入主播魅力值记录
		$charmRecords['uid'] = $dotey_id;
		$charmRecords['sender_uid'] = $songRecord[$recordId]['uid'];
		$charmRecords['target_id'] =  $archivesId;
		$charmRecords['record_sid'] = $recordId;
		$charmRecords['charm'] = $songRecord[$recordId]['charm'];
		$charmRecords['num'] = 1;
		$charmRecords['source']=SOURCE_SONGS;
		$charmRecords['sub_source']=SUBSOURCE_SONGS_DEMANDSONG;
		$charmRecords['info']= $songRecord[$recordId]['name'].'x1';
		$consumeService->saveDoteyCharmRecords($charmRecords, true);
		//写入主播魅力点记录
		$charmPointsRecords['uid'] = $dotey_id;
		$charmPointsRecords['sender_uid'] = $songRecord[$recordId]['uid'];
		$charmPointsRecords['target_id'] = $archivesId;
		$charmPointsRecords['record_sid'] = $recordId;
		$charmPointsRecords['charm_points'] =  $songRecord[$recordId]['charm_points'];
		$charmPointsRecords['num'] = 1;
		$charmPointsRecords['source']=SOURCE_SONGS;
		$charmPointsRecords['sub_source']=SUBSOURCE_SONGS_DEMANDSONG;
		$charmPointsRecords['info']= $songRecord[$recordId]['name'].'x1';
		$consumeService->saveDoteyCharmPointsRecords($charmPointsRecords, true);
		$doteyconsumeAttriute['uid']=$dotey_id;
		$doteyconsumeAttriute['charm']= $songRecord[$recordId]['charm'];
		$doteyconsumeAttriute['charm_points']= $songRecord[$recordId]['charm_points'];
		$consumeService->saveUserConsumeAttribute($doteyconsumeAttriute);
		
		
		$userBasicService=new UserService();
		$userBasic=$userBasicService->getUserFrontsAttributeByCondition($songRecord[$recordId]['uid'],true);
		$zmq=$this->getZmq();
		$eventData['archives_id']=$archivesId;
		$eventData['domain']=DOMAIN;
		$eventData['type']='localroom';
		$json_content['type']='actsong';
		$json_content['uid']=$songRecord[$recordId]['uid'];
		$json_content['dotey_uid']=$dotey_id;
		$json_content['song_record_id']=$recordId;
		$json_content['record_id']=$archives['live_record']['record_id'];
		$json_content['charm']=$songRecord[$recordId]['charm'];
		$json_content['nickname']=$userBasic['nk'];
		$json_content['name']=$songRecord[$recordId]['name'];
		$eventData['json_content']=$json_content;
		if(!$zmq->sendZmqMsg(606,$eventData)){
			return $this->setError(Yii::t('common','Zmq Event send failed'),-3);
		}
		return true;
		
	}
	
	/**
	 * 取消点歌记录
	 * @param int $recordId  记录ID
	 * @param int $dotey_id  主播Id
	 * @param int $archivesId  档期ID
	 * @return boolean 
	 */
	public function cancelSong($recordId,$dotey_id,$archivesId){
		if($recordId<=0||$dotey_id<=0||$archivesId<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByArchivesId($archivesId);
		if($archives['uid']!=$dotey_id){
			return $this->setError(Yii::t('doteySong','No permission to operate'),0);
		}
		$songRecord=$this->getUserSongRecordsByRecordIds($recordId);
		
		//返还皮蛋
		$consumeService=new ConsumeService();
		$result=$consumeService->cancelSongReturnEggs($recordId,$songRecord[$recordId]['uid'],$songRecord[$recordId]['pipiegg']);
		if($result<=0){
			return $this->setError(Yii::t('doteySong','Pipiegg return failed'),0);
		}
		$filename = DATA_PATH.'runtimes/cancel_song_records.txt';
		error_log(date("Y-m-d H:i:s")."存储主播取消点歌记录：".json_encode($songRecord)."\n\r",3,$filename);
		$consumeModel=new ConsumeModel();
		$consumeModel->updateAttributeByUid($songRecord[$recordId]['uid'],array('consume_pipiegg'=>'-'.$songRecord[$recordId]['pipiegg']));
		$consumeService->saveUserConsumeAttribute(array('uid'=>$songRecord[$recordId]['uid'],'pipiegg'=>$songRecord[$recordId]['pipiegg']));
		$userBasicService=new UserService();
		$userBasic=$userBasicService->getUserFrontsAttributeByCondition($songRecord[$recordId]['uid'],true);
		$zmq=$this->getZmq();
		$eventData['archives_id']=$archivesId;
		$eventData['domain']=DOMAIN;
		$eventData['type']='localroom';
		$json_content['type']='cancelsong';
		$json_content['uid']=$songRecord[$recordId]['uid'];
		$json_content['dotey_uid']=$songRecord[$recordId]['to_uid'];
		$json_content['song_record_id']=$recordId;
		$json_content['record_id']=$archives['live_record']['record_id'];
		$json_content['nickname']=$userBasic['nk'];
		$json_content['name']=$songRecord[$recordId]['name'];
		$eventData['json_content']=$json_content;
		$zmq->sendZmqMsg(606,$eventData);
		return true;
	}
	
	/**
	 * 获取主播的歌单
	 * @param int $doteyId 主播uid
	 * @return array 返回结果集
	 */
	public function getDoteySongByDoteyId($doteyId){
		if($doteyId<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$doteySongModel=new DoteySongModel();
		$data=$doteySongModel->getDoteySongByDoteyIds(array($doteyId));
		$list=$this->arToArray($data);
		return $this->buildDataByIndex($list, 'song_id');
	}
	
	public function getDoteySongBySongId($songId){
		if($songId<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$doteySongModel=new DoteySongModel();
		$song=$doteySongModel->getDoteySongBySongId($songId);
		return $song->attributes;
	}
	
	/**
	 * 根据条件获取歌曲信息
	 * @param array $condition
	 * @return array
	 */
	public function getDoteySongByCondition(array $condition){
		if(empty($condition))
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$doteySongModel=new DoteySongModel();
		$data=$doteySongModel->getDoteySongByCondition($condition);
		$list=$this->arToArray($data);
		return $this->buildDataByIndex($list, 'song_id');
	}
	
	/**
	 * 获取主播的歌单
	 * @param int $doteyId 主播uid
	 * @param int $offset 偏移量
	 * @param int $pageSize 页码
	 * @param array $conditon 其他条件
	 * @return array 返回结果集
	 */
	public function getDoteySongByDoteyIdLimit($doteyId,$offset=0,$pageSize=10,array $condition=array()){
		if($doteyId<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$doteySongModel= new DoteySongModel();
		$criteria = $doteySongModel->getDbCriteria();
		$condition['dotey_id']=$doteyId;
		$criteria->addColumnCondition($condition);
		$songList['count']=$doteySongModel->count($criteria);
		$criteria->limit=$pageSize;
		$criteria->offset = $offset*$pageSize;
		$data=$doteySongModel->findAll($criteria);
		$songList['list']=$this->arToArray($data);
		return $songList;
	}
	
	/**
	 * 存储主播歌曲信息
	 * @param array $song 歌曲内容
	 * @return int
	 */
	public function saveDoteySong(array $song){
		if($song['dotey_id']<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$doteySongModel= new DoteySongModel();
		$song['create_time']=time();
		if(isset($song['song_id'])){
			$orgdoteySongModel=$doteySongModel->findByPk($song['song_id']);
			if(empty($orgdoteySongModel)){
				return $this->setNotice('dotey_song',Yii::t('dotey_song','The song does not exist'),0);
			}
			$this->attachAttribute($orgdoteySongModel,$song);
			if(!$orgdoteySongModel->validate()){
				return $this->setNotices($orgdoteySongModel->getErrors(),array());
			}
			$orgdoteySongModel->save();
			$insertId = $song['song_id'];
		}else{
			$this->attachAttribute($doteySongModel,$song);
			if(!$doteySongModel->validate()){
				return $this->setNotices($doteySongModel->getErrors(),array());
			}
			$doteySongModel->save();
			$insertId = $doteySongModel->getPrimaryKey();
		}
		return $insertId;
	}
	
	/**
	 * 批量存储主播歌单
	 * @param int $dotey_id  主播uid
	 * @param array $songs   歌曲信息
	 * @return mix|boolean
	 */
	public function batchSaveDoteySong($dotey_id,array $songs){
		if($dotey_id<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$doteySongModel= new DoteySongModel();
		$newData=array();
		foreach($songs as $key=>$_songs){
			$_songs['dotey_id']=$dotey_id;
			$_songs['create_time']=time();
			$newData[$key]=$_songs;
		}
		if(!$newData){
			return false;
		}
		return $doteySongModel->batchInsert($newData);
	}
	
	/**
	 * 删除主播歌单信息
	 * @param int $songId
	 * @return boolean
	 */
	public function delDoteySongBySongId($songId){
		if(empty($songId)) return array();
		$doteySongModel= new DoteySongModel();
		return $doteySongModel->delDoteySongBySongId($songId);
	}
	
	/**
	 * 根据记录Id获取点歌记录
	 * @param int|array $recordIds
	 */
	public function getUserSongRecordsByRecordIds($recordIds){
		if(empty($recordIds))
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$recordIds=is_array($recordIds)?$recordIds:array($recordIds);
		$userSongModel=new UserSongModel();
		$data=$userSongModel->getUserSongRecordsByRecordIds($recordIds);
		$list=$this->arToArray($data);
		return $this->buildDataByIndex($list, 'record_id');
	}
	
	public function getUnhandleUserSongRecordByDoteyId($doteyId){
		if($doteyId<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$userSongModel=new UserSongModel();
		$data=$userSongModel->getUnhandleUserSongRecordsBydoteyId($doteyId);
		$list=$this->arToArray($data);
		return $this->buildDataByIndex($list, 'record_id');
	}
	
	/**
	 * 根据条件获取主播的点歌记录
	 * @param int $doteyId 主播uid
	 * @param int $offset 偏移量
	 * @param int $pageSize 页码
	 * @param array $condtion 其他条件
	 * @return array 返回点歌结果集
	 */
	public function getUserSongRecordsByDoteyId($doteyId,$offset=0,$pageSize=10,array $condition=array()){
		if($doteyId<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$userSongModel= new UserSongModel();
		$criteria = $userSongModel->getDbCriteria();
		$criteria->condition.=' to_uid =:to_uid';
		$criteria->params[':to_uid'] = $doteyId;
		if(isset($condition['start_time'])){
			$criteria->condition.=' and create_time>=:start_time';
			$criteria->params[':start_time']=$condition['start_time'];
			unset($condition['start_time']);
		}
		if(isset($condition['end_time'])){
			$criteria->condition.=' AND create_time<=:end_time';
			$criteria->params[':end_time']=$condition['end_time'];
			unset($condition['end_time']);
		}
// 		$criteria->addColumnCondition($condition);
		$songRecordList=array();
		$songRecordList['count']=$userSongModel->count($criteria);
		$criteria->order = 'create_time desc';
		$criteria->limit=$pageSize;
		$criteria->offset = $offset;
		$data=$userSongModel->findAll($criteria);
		$list=$this->arToArray($data);
		$songRecordList['list']=$list;
		return $songRecordList;
	}
	
	/**
	 * 根据记录id获取在主播未处理的歌曲记录的位置
	 * @param int $recordId 点歌记录Id
	 * @param int $doteyId 主播uid
	 * @return int
	 */
	public function getCountUserSongRecordsByRecordId($recordId,$doteyId){
		if($recordId<=0||$doteyId<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$userSongModel= new UserSongModel();
		return $userSongModel->getCountUserSongRecordsByRecordId($recordId,$doteyId);
	}
	
	/**
	 * 根据条件获取用户的点歌记录
	 * @author guoshaobo 添加按时间排序
	 * @param int $uid 用户uid
	 * @param int $offset 偏移量
	 * @param int $pageSize 页码
	 * @param array $condtion 其他条件
	 * @return array 返回点歌结果集
	 */
	public function getUserRecordsByUid($uid,$offset=0,$pageSize=10,array $condition=array()){
		if($uid<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$userSongModel= new UserSongModel();
		$criteria = $userSongModel->getDbCriteria();
		$condition['uid']=$uid;
		$criteria->addColumnCondition($condition);
		$criteria->limit=$pageSize;
		$criteria->offset = $offset;
		$criteria->order = 'create_time desc';
		$data=$userSongModel->findAll($criteria);
		$list=$this->arToArray($data);
		$songRecordList=$this->buildDataByIndex($list,'uid');
		$songRecordList['count']=$userSongModel->count($criteria);
		return $songRecordList;
	}
	
	
	/**
	 * 存储用户点歌记录
	 * @param array $records 点歌信息
	 * @return int
	 */
	public function saveUserSongRecords(array $records){
		$userSongModel= new UserSongModel();
		if(isset($records['record_id'])){
			$records['update_time']=time();
			$orguserSongModel=$userSongModel->findByPk($records['record_id']);
			if(empty($orguserSongModel)){
				return $this->setNotice('user_song',Yii::t('user_song','The user song records does not exist'),0);
			}
			$this->attachAttribute($orguserSongModel,$records);
			if(!$orguserSongModel->validate()){
				return $this->setNotices($orguserSongModel->getErrors(),array());
			}
			$orguserSongModel->save();
			$insertId = $records['record_id'];
		}else{
			if($records['to_uid']<=0||$records['uid']<=0||$records['target_id']<=0)
				return $this->setError(Yii::t('common','Parameter is empty'),0);
			$records['create_time']=time();
			$this->attachAttribute($userSongModel,$records);
			if(!$userSongModel->validate()){
				return $this->setNotices($userSongModel->getErrors(),array());
			}
			$userSongModel->save();
			$insertId = $userSongModel->getPrimaryKey();
		}
		return $insertId;
	}
	
	/**
	 * 取得点歌排行榜
	 * 
	 * @param string $type 排行榜类型 今日 本周 本月 超级
	 * @param int $loginUid 登录UID
	 * @return array
	 */
	public function getDoteySongsRank($type){
		$keyConfig = Yii::getKeyConfig('redis','other');
		$list = array(
			'today'=>$keyConfig['dotey_songs_today_rank'],
			'week'=>$keyConfig['dotey_songs_week_rank'],
			'month'=>$keyConfig['dotey_songs_month_rank'],
			'super'=>$keyConfig['dotey_songs_super_rank'],
		);
	
		$type = !$type || in_array($type,array_keys($list)) ? $type : 'today';
		$redisModel = new OtherRedisModel();
		$rank = $redisModel->getDoteySongsRank($list[$type]);
		
		$uids = array();
		foreach($rank as $_rank){
			$uids[] = $_rank['d_uid'];
		}
		$userService = new UserService();
		$avatars = $userService->getUserAvatarsByUids($uids,'small');
		foreach($rank as $key=>$_rank){
			$rank[$key]['d_avatar'] = $avatars[$_rank['d_uid']];
		}
		return $rank;
	}

	/**
	 * 取得用户点歌排行榜
	 * 
	 * @param string $type 排行榜类型 今日 本周 本月 超级
	 * @param int $isAvatar 是否获取头像
	 * @return array
	 */
	public function getUserSongsRank($type,$isAvatar = true){
		$keyConfig = Yii::getKeyConfig('redis','other');
		$list = array(
			'today'=>$keyConfig['user_songs_today_rank'],
			'week'=>$keyConfig['user_songs_week_rank'],
			'month'=>$keyConfig['user_songs_month_rank'],
			'super'=>$keyConfig['user_songs_super_rank'],
		);
	
		$type = !$type || in_array($type,array_keys($list)) ? $type : 'today';
		$redisModel = new OtherRedisModel();
		$rank = $redisModel->getUserSongsRank($list[$type]);
		if($isAvatar){
			$uids = array();
			foreach($rank as $_rank){
				$uids[] = $_rank['uid'];
			}
			$userService = new UserService();
			$avatars = $userService->getUserAvatarsByUids($uids,'small');
			foreach($rank as $key=>$_rank){
				$rank[$key]['avatar'] = $avatars[$_rank['uid']];
			}
		}
		return $rank;
	}
	
	/**
	 * 统计主播歌单的统计数据(魅力值)
	 * @author guoshaobo
	 * @param unknown_type $uid
	 * @param unknown_type $stime
	 * @param unknown_type $etime
	 * @return mix|number
	 */
	public function countDoteyMonthSong($uid, $stime, $etime = 0)
	{
		if($uid <= 0 || empty($stime) ){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		if($etime <= 0){
			$etime = time();
		}
		$condition = array('live_time_on'=>$stime);
		$userSongModel= new UserSongModel();
		$res = $userSongModel->searchSongByToUids(array($uid), $condition);
		if($res){
			$res = $this->buildDataByIndex($res, 'to_uid');
			return $res[$uid];
		}
		return false;
	}
	
	/**
	 * 查看直播间是否允许点歌
	 * @param int $archives_id 档期Id
	 * @return int
	 */
	public function getArchivesAllowSong($archives_id){
		$otherRedisModel=new OtherRedisModel();
		return $otherRedisModel->getArchivesAllowSong($archives_id);
	}
	
	/**
	 * 存储直播间是否允许点歌
	 * @param int $archives_id
	 * @param boolen $allow 1->允许 2->禁止
	 * @return int
	 */
	public function saveArchivesAllowSong($archives_id,$allow=1){
		$otherRedisModel=new OtherRedisModel();
// 		$zmq=$this->getZmq();
// 		$zmqData['archives_id']=$archives_id;
// 		$zmqData['domain']=DOMAIN;
// 		$zmqData['type']='localroom';
// 		$zmqData['json_content']=array('type'=>'allowSong','archives_id'=>$archives_id,'status'=>$allow);
// 		$zmq->sendZmqMsg(606, $zmqData);
		return $otherRedisModel->saveArchivesAllowSong($archives_id,$allow);
	}
	
	private function getDoteySongRedisKey(){
		$key=Yii::getKeyConfig('redis','other');
		return $key['doteySong'];
	}
	
	/**
	 * 主播歌单
	 * 
	 * @author supeng
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $condition
	 * @param unknown_type $isLimit
	 * @return Ambigous <multitype:, multitype:NULL , multitype:multitype: number Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > Ambigous <string, unknown, mixed> >
	 */
	public function searchDoteySongByCondition($offset=0,$limit=20,$condition=array(),$isLimit=true){
		$doteySongModel = new DoteySongModel();
		$result = $doteySongModel->searchDoteySongByCondition($offset,$limit,$condition,$isLimit);
		if (!empty($result['list'])){
			$result['list'] = $this->arToArray($result['list']);
		}
		return $result;
	}
	
	/**
	 * 点歌记录
	 * 
	 * @author supeng
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $condition
	 * @param unknown_type $isLimit
	 * @return Ambigous <multitype:, multitype:NULL >
	 */
	public function searchVODRecordsByCondition($offset=0,$limit=20,$condition=array(),$isLimit=true){
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$doteySer = new DoteyService();
			$uids = $doteySer->searchDoteyUidsByCodition($condition);
			if ($uids && is_array($uids)){
				$condition['to_uids'] = $uids;
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
		$userSongModel = new UserSongModel();
		$result = $userSongModel->searchVODRecordsByCondition($offset,$limit,$condition,$isLimit);
		if (!empty($result['list'])){
			$result['list'] = $this->arToArray($result['list']);
		}
		return $result;
	}
	
	/**
	 * 点歌统计
	 * 
	 * @author supeng
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 * @param unknown_type $condition
	 * @param unknown_type $isLimit
	 * @return Ambigous <multitype:, multitype:NULL , multitype:multitype: number Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > Ambigous <string, unknown, mixed> >
	 */
	public function searchVODStatByCondition($offset=0,$limit=20,$condition=array(),$isLimit=true,$isDotey=true){
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			if($isDotey){
				$doteySer = new DoteyService();
				$uids = $doteySer->searchDoteyUidsByCodition($condition);
				if ($uids && is_array($uids)){
					$condition['to_uids'] = $uids;
				}else{
					return array('count'=>0,'list'=>array());
				}
			}else{
				$UserService = new UserService();
				$info = $UserService->searchUserList($offset,$limit,$condition,false);
				if($info['uids']){
					$condition['uids'] = $info['uids'];
				}else{
					return array('count'=>0,'list'=>array());
				}
			}
		}
		$userSongModel = new UserSongModel();
		return $userSongModel->searchVODStatByCondition($offset,$limit,$condition,$isLimit,$isDotey);
	}
	
	/**
	 * 获取点歌处理状态 
	 * 
	 * @author supeng
	 * @return multitype:string 
	 */
	public function getDoteySongHandler(){
		return array(
				0 => '未处理',
				1 => '已处理',
				2 => '已取消'
			);
	}
}

?>