<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: ArchivesService.php 17803 2014-01-23 08:22:25Z hexin $
 * @package service
 * @subpackage archives
 */

define('DOTEY_STATUS',1);     //主播审核通过
define('INVALID_LIVE',-1);    //无效直播记录
define('WIIL_START_LIVE',0);  //待直播
define('START_LIVE',1);       //开始直播
define('END_LIVE',2);         //结束直播
define('UNABLE_TOURIST_CHAT',0);      //禁止游客发言
define('ENABLE_TOURIST_CHAT',1);      //允许游客发言
define('UNABLE_GLOBAL_CHAT',0);  //禁止全局发言
define('ENABLE_GLOBAL_CHAT',1);  //允许全局发言
define('DOTEY_LIVE_EFFECTDAY_UNIT',2);//主播开播记录有效天的默认小时单位
define('ARCHIVES_HIDDEN',1);          //档期隐藏
define('ARCHIVES_NOT_HIDDEN',0);          //档期不隐藏
define('ARCHIVES_FLASH_MODEL',0);   //flash播放模式
define('ARCHIVES_ACTX_MODEL',1);     //actx播放模式
class ArchivesService extends PipiService {
	
	protected static $secrect='65452b54d3e9c889a462d83f62866a52';
	
	protected static $expireTime=60; //token失效时间
	
	/**
	 *
	 * @var ChannelDoteySortService
	 */
	protected static $channelDoteySortService = null;
	/**
	 *
	 * @var OtherRedisModel
	 */
	protected static $otherRedisModel = null;

	public function __construct(PipiController $pipiController = null){
		parent::__construct($pipiController);
		if(self::$channelDoteySortService == null){
			self::$channelDoteySortService = new ChannelDoteySortService();
		}
		if(self::$otherRedisModel == null){
			self::$otherRedisModel = new OtherRedisModel();
		}
	}
	/**
	 * 存储档期信息
	 * @param array $archives 档期信息
	 * @param array $doteyIds 档期所属的其他主播uid
	 * @return array
	 */
	public function saveArchives(array $archives,array $doteyIds=array()){
		$archivesModel=new ArchivesModel();
		if(isset($archives['archives_id'])){
			if($archives['archives_id'] <= 0)
				return $this->setError(Yii::t('common','Parameter is empty1'),0);
			if(!$orgarchivesModel=$archivesModel->findByPk($archives['archives_id']))
				return $this->setError(Yii::t('common','Parameter is empty2'),0);
			$this->attachAttribute($orgarchivesModel,$archives);
			if(!$orgarchivesModel->validate())
				return $this->setError($orgarchivesModel->getErrors(),0);
			$orgarchivesModel->save();
			$insertId = $archives['archives_id'];
		}else{
			$archives['create_time']=time();
			$this->attachAttribute($archivesModel,$archives);
			if(!$archivesModel->validate())
				return $this->setError($archivesModel->getErrors(),0);
			$archivesModel->save();
			$insertId = $archivesModel->getPrimaryKey();
			if($insertId){
				$uids=array();
				if($doteyIds){
					$uids=$doteyIds;
				}
				array_push($uids, $archives['uid']);
				$this->batchSaveArchivesUser($insertId,$uids);
			}
		}
		//存储档期信息到redis
		if($insertId){
			self::saveArchivesRedisByArchivesId($insertId);
			if ($this->isAdminAccessCtl()){
				if(isset($archives['archives_id'])){
					$op_desc = '编辑 档期信息(archives_id='.$insertId.')';
				}else{
					$op_desc = '新增 档期信息(archives_id='.$insertId.')';
				}
				$this->saveAdminOpLog($op_desc,$archives['uid']);
			}
		}
		return $insertId;
	}


	/**
	 * 存储单个档期用户信息
	 * @param array $user 档期所属用户信息
	 * @return int
	 */
	public function saveArchivesUser(array $user){
		$archivesUserModel=new ArchivesUserModel();
		if(isset($user['id'])){
			if($user['id']<=0)
				return $this->setError(Yii::t('common','Parameter is empty3'),0);
			if(!$archivesUserModel=$archivesUserModel->findByPk($user['id']))
				return $this->setError(Yii::t('common','Data not exists'),0);
			$archivesUserModel=$archivesUserModel->findByPk($user['id']);
		}
		$this->attachAttribute($archivesUserModel,$user);
		if(!$archivesUserModel->validate()){
			return $this->setNotices($archivesUserModel->getErrors(),array());
		}
		$archivesUserModel->save();
		return $archivesUserModel->getPrimaryKey();
	}

	/**
	 * 档期批量添加主播用户
	 * @param int $archivesId 档期id
	 * @param array $uids 档期拥有者uid
	 */
	public function batchSaveArchivesUser($archivesId,array $uids){
		if($archivesId<=0||empty($uids))
			return $this->setError(Yii::t('common','Parameter is empty4'),0);
		$archivesUserModel=new ArchivesUserModel();
		$newData=array();
		foreach($uids as $key=>$_uid){
			$newData[$key]['archives_id']=$archivesId;
			$newData[$key]['uid']=$_uid;
		}
		if(!$newData){
			return false;
		}
		return $archivesUserModel->batchInsert($newData);
	}

	/**
	 * 存储档期开播记录
	 * @param array $record
	 * @return array
	 */
	public function saveArchivesLiveRecords(array $record){
		$liveRecordsModel=new LiveRecordsModel();
		if(isset($record['record_id'])){
			if($record['record_id'] <= 0)
				return $this->setError(Yii::t('common','Parameter is empty5'),0);
			if(!$liveRecordsModel=$liveRecordsModel->findByPk($record['record_id']))
				return $this->setError(Yii::t('common','Data not exists'),0);
		}
		$this->attachAttribute($liveRecordsModel,$record);
		if(!$liveRecordsModel->validate()){
			return $this->setNotices($liveRecordsModel->getErrors(),array());
		}
		$liveRecordsModel->save();
		$flag = $liveRecordsModel->getPrimaryKey();
		if($flag && $this->isAdminAccessCtl()){
			$this->saveAdminOpLog('编辑 档期开播记录(record_id='.$flag.')');
		}
		return $flag;
	}

	/**
	 * 存储档期分类信息
	 * @param array $category 分类信息
	 * @return int
	 */
	public function saveArchivesCat(array $category){
		$archivesCategoryModel=new ArchivesCategoryModel();
		if(isset($category['cat_id'])){
			if($category['cat_id'] <= 0)
				return $this->setError(Yii::t('common','Parameter is empty6'),0);
			if(!$archivesCategoryModel=$archivesCategoryModel->findByPk($category['cat_id']))
				return $this->setError(Yii::t('common','Data not exists'),0);
		}
		$this->attachAttribute($archivesCategoryModel,$category);
		if(!$archivesCategoryModel->validate()){
			return $this->setNotices($archivesCategoryModel->getErrors(),array());
		}
		$archivesCategoryModel->save();
		return $archivesCategoryModel->getPrimaryKey();
	}

	/**
	 * 存储档期跟视频服务器的关系
	 * @param array $liveServer  档期视频服务器id
	 * @return array
	 */
	public function saveArchivesLiveServer(array $liveServer){
		$archivesLiveServerModel=new ArchivesLiveServerModel();
		if(isset($liveServer['id'])){
			if($liveServer['id'] <= 0)
				return $this->setError(Yii::t('common','Parameter is empty7'),0);
			if(!$archivesLiveServerModel=$archivesLiveServerModel->findByPk($liveServer['id']))
				return $this->setError(Yii::t('common','Data not exists'),0);
		}
		$this->attachAttribute($archivesLiveServerModel,$liveServer);
		if(!$archivesLiveServerModel->validate()){
			return $this->setNotices($archivesLiveServerModel->getErrors(),array());
		}
		$archivesLiveServerModel->save();
		$flag = $archivesLiveServerModel->getPrimaryKey();
		if ($this->isAdminAccessCtl() && $flag){
			if(isset($liveServer['id'])){
				$op_desc = '编辑 档期与视频服务器关系(id='.$flag.')';
			}else{
				$op_desc = '新增 档期与视频服务器关系(id='.$flag.')';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return  $flag;
	}


	/**
	 * 存储视频服务器地址信息
	 * @param array $server  视频服务器信息
	 * @return array
	 */
	public function saveLiveServer(array $server){
		if(empty($server['import_host'])||empty($server['export_host']))
			return $this->setError(Yii::t('common','Parameter is empty8'),0);
		$liveServerModel=new LiveServerModel();
		if(isset($server['server_id'])){
			if(isset($server['server_id']) && $server['server_id'] <= 0)
				return $this->setError(Yii::t('common','Parameter is empty9'),0);
			if(!$liveServerModel=$liveServerModel->findByPk($server['server_id']))
				return $this->setError(Yii::t('common','Data not exists'),0);
		}
		$this->attachAttribute($liveServerModel,$server);
		if(!$liveServerModel->validate()){
			return $this->setNotices($liveServerModel->getErrors(),array());
		}
		$liveServerModel->save();
		$flag = $liveServerModel->getPrimaryKey();
		if ($flag && $this->isAdminAccessCtl()) {
			if(isset($server['server_id'])){
				$op_desc = '修改 视频服务器地址信息('.$flag.')';
			}else{
				$op_desc = '新增 视频服务器地址信息('.$flag.')';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $flag;
	}

	/**
	 * 存储聊天服务器地址信息
	 * @param array $server  聊天服务器信息
	 * @return array
	 */
	public function saveGlobalServer(array $server){
		if(empty($server['domain']))
			return $this->setError(Yii::t('common','Parameter is empty10'),0);
		$globalServerModel=new GlobalServerModel();
		if(isset($server['global_server_id'])){
			if($server['global_server_id'] <= 0){
				return $this->setError(Yii::t('common','Parameter is empty11'),0);
			}
			if(!$globalServerModel=$globalServerModel->findByPk($server['global_server_id'])){
				return $this->setError(Yii::t('common','Data not exists'),0);
			}

		}
		$this->attachAttribute($globalServerModel,$server);
		if(!$globalServerModel->validate()){
			return $this->setNotices($globalServerModel->getErrors(),array());
		}
		$globalServerModel->save();
		return $globalServerModel->getPrimaryKey();
	}

	/**
	 * 存储档期属性信息
	 * @param array $attributes 属性信息
	 * @return int
	 */
	public function saveArchivesAttribute(array $attributes){
		$archivesAttributeModel=new ArchivesAttributeModel();
		if(isset($attributes['attribute_id'])){
			if($attributes['attribute_id'] <= 0){
				return $this->setError(Yii::t('common','Parameter is empty12'),0);
			}
			if(!$archivesAttributeModel=$archivesAttributeModel->findByPk($attributes['attribute_id'])){
				return $this->setError(Yii::t('common','Data not exists'),0);
			}
		}
		$this->attachAttribute($archivesAttributeModel,$attributes);
		if(!$archivesAttributeModel->validate()){
			return $this->setNotices($archivesAttributeModel->getErrors(),array());
		}
		$archivesAttributeModel->save();
		return $archivesAttributeModel->getPrimaryKey();
	}

	/**
	 * 存储房管信息
	 * @author leiwei
	 * @param array $purviewLive
	 * @return array
	 */
	public function savePurviewLive(array $purviewLive){
		$purviewLiveModel=new PurviewLiveModel();
		if(isset($purviewLive['purview_live_id'])){
			if($purviewLive['purview_live_id'] <= 0){
				return $this->setError(Yii::t('common','Parameter is empty13'),0);
			}
			if(!$purviewLiveModel=$purviewLiveModel->findByPk($purviewLive['purview_live_id'])){
				return $this->setError(Yii::t('common','Data not exists'),0);
			}
		}
		$this->attachAttribute($purviewLiveModel,$purviewLive);
		if(!$purviewLiveModel->validate()){
			return $this->setNotices($purviewLiveModel->getErrors(),array());
		}
		$purviewLiveModel->save();
		return $purviewLiveModel->getPrimaryKey();
	}

	/**
	 * 存储聊天进程信息
	 * @param array $server
	 * @return int
	 */
	public function saveChatServer(array $server){
		if(empty($server))
			return $this->setError(Yii::t('common','Parameter is empty14'),0);
		$chatServerModel=new ChatServerModel();
		if(isset($server['chat_id'])){
			if(!$chatServerModel=$chatServerModel->findByPk($server['chat_id'])){
				return $this->setError(Yii::t('common','Data not exists'),0);
			}
		}
		$this->attachAttribute($chatServerModel,$server);
		if(!$chatServerModel->validate()){
			return $this->setNotices($chatServerModel->getErrors(),array());
		}
		$chatServerModel->save();
		$flag = $chatServerModel->getPrimaryKey();
		if ($this->isAdminAccessCtl() && $flag){
			if(isset($server['chat_id'])){
				$op_desc = '编辑 聊天进程(chat_id='.$flag.')';
			}else{
				$op_desc = '新增 聊天进程(chat_id='.$flag.')';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $flag;
	}
	/**
	 * 存储最近观看的
	 *
	 * @param array $archives
	 * @return array
	 */
	public function saveLatestSeeArchives(array $archives){
		if(!isset($archives['uid']) || $archives['uid'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty15'),false);
		}
		if(!isset($archives['archives_id']) || $archives['archives_id'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty16'),false);
		}
		$userLatestSeeModel = new UserArchivesViewModel();
		$archives['view_time'] = time();
		$this->attachAttribute($userLatestSeeModel,$archives);
		return $userLatestSeeModel->batchInsert(array($archives),false);
	}

	/**
	 * 保存直播档期用户统计数据
	 *
	 * @author supeng
	 * @param array $sess
	 * @return mix|Ambigous <NULL, multitype:NULL >
	 */
	public function saveSessStat(array $sess){
		if(empty($sess)){
			return $this->setError(Yii::t('common', 'Parameter not empty17'),false);
		}
		$sessStatModel = new ArchivesOnlineStatisticsModel();
		$sess['create_time'] = time();
		$this->attachAttribute($sessStatModel, $sess);
		if (!$sessStatModel->validate()) {
			return $this->setError($sessStatModel->getErrors(),false) ;
		}
		$sessStatModel->save();
		return  $sessStatModel->getPrimaryKey();
	}


	/**
	 * 根据ID删除房管
	 * @author leiwei
	 * @param array|int $ids
	 * @return mix|boolean
	 */
	public function delPurviewLiveByIds($ids){
		if(empty($ids))
			return $this->setError(Yii::t('common','Parameter is empty18'),0);
		$ids=is_array($ids)?$ids:array($ids);
		$purviewLiveModel=new PurviewLiveModel();
		return $purviewLiveModel->delPurviewLiveByIds($ids);
	}

	/**
	 * 创建直播间
	 * @param array $archives  档期信息
	 * @param array $attributes 档期附加信息
	 * @param array $doteyIds   归属档期的其他主播uid
	 * @return int
	 */
	public function createArchives(array $archives,array $doteyIds=array()){
		if(empty($archives)||$archives['uid']<=0)
			return $this->setError(Yii::t('common','Parameter not empty19'),0);
		if(!$archives['title']||!$archives['cat_id']||!$archives['uid'])
			return $this->setError(Yii::t('archives','title or cat_id not empty'),0);
		$archives['sub_id']=empty($archives['sub_id'])?0:$archives['sub_id'];
		$archives['create_time']=time();
		if($this->getArchivesBycondition(array('uid'=>$archives['uid'],'cat_id'=>$archives['cat_id'],'sub_id'=>$archives['sub_id'])))
			return $this->setError(Yii::t('archives','archives is exit'),0);
 		$userService=new UserService();
		$doteyBase=$userService->getUserBasicByUids(array($archives['uid']));
		if(!$doteyBase||$doteyBase[$archives['uid']]['user_status']==USER_STATUS_OFF||!$this->hasBit(intval($doteyBase[$archives['uid']]['user_type']),USER_TYPE_DOTEY))
			return $this->setError(Yii::t('archives','user is not dotey'),0);
		$archivesId=$this->saveArchives($archives,$doteyIds);
		if(!$archivesId){
			return $this->setError(Yii::t('archives','archives save error'),0);
		}
		//分配视频服务器
		$liveServer=$this->getMinUserLiveServer();
		if(empty($liveServer))
			return $this->setError(Yii::t('archives','live_server not exits'),0);
		$server['archives_id']=$archivesId;
		$server['server_id']=$liveServer['server_id'];
		//增加视频服务器使用情况
		$liveServerId=$this->saveArchivesLiveServer($server);
		if($liveServerId){
			$this->addLiveServerUseByServerId($liveServer['server_id']);
		}
		//写入聊天进程信息
		$chatServer=$this->getMinUserGlobalServer();
		if(empty($chatServer))
			return $this->setError(Yii::t('archives','chat_server not exits'),0);
		$chatServerId=$this->saveChatServer(array('archives_id'=>$archivesId,'domain'=>$chatServer['domain']));
		if($chatServerId){
			$this->addGlobalServerUseByServerId($chatServer['global_server_id']);
		}
		//按档期id存储档期信息到redis
		$this->getArchivesByArchivesIds($archivesId);
		//按uid存储档期信息到redis
		$this->getArchivesByUids($archives['uid']);

		//更改主播redis中pk信息
		$doteyIds[]=$archives['uid'];
		foreach($doteyIds as $_uid){
			$this->saveDoteyPurviewRank($_uid,$archivesId,true);
		}

		//发送创建直播间事件
		$zmq=$this->getZmq();
		$eventData['type']='create_archives';
		$eventData['uid']=$archives['uid'];
		$json_info['aid']=$archivesId;
		$json_info['a_domain']=DOMAIN;
		$json_info['s_domain']=$chatServer['domain'];
		$eventData['json_content']=$json_info;
		$zmq->sendZmqMsg(609,$eventData);
		return $archivesId;
	}

	/**
	 * 修改直播公告
	 * @param int $archivesId 档期ID
	 * @param array $notices 公聊或私聊公告信息
	 * @return int
	 */
	public function modifyArchivesNotice($archivesId,array $notices){
		if($archivesId<=0||empty($notices))
			return $this->setError(Yii::t('common','Parameter not empty20'),0);
		$archives['archives_id']=$archivesId;
		isset($notices['notice'])&&$archives['notice']['content']=$notices['notice'];
		isset($notices['url'])&&$archives['notice']['url']=$notices['url'];
		isset($notices['private_notice'])&&$archives['private_notice']['content']=$notices['private_notice'];
		isset($notices['private_url'])&&$archives['private_notice']['url']=$notices['private_url'];
		$archives['notice']=serialize($archives['notice']);
		$archives['private_notice']=serialize($archives['private_notice']);
		if($this->saveArchives($archives)){
			$archivesDao=$this->getArchivesByArchivesId($archivesId);
			self::$otherRedisModel->saveArchives($archivesId,$archivesDao);
			$userJson=new UserJsonInfoService();
			$userInfo=$userJson->getUserInfo($doteyId,false);
			$notices['nickname']=$userInfo['nk'];
			$notices['type']='modifyNotice';
			$zmq=$this->getZmq();
			$zmqData['archives_id']=$archivesId;
			$zmqData['domain']=DOMAIN;
			$zmqData['type']='localroom';
			$zmqData['json_content']=$notices;
			$zmq->sendZmqMsg(606, $zmqData);
		}
		return true;
	}

	/**
	 * 直播间开播预告
	 * @param int $uid           主播uid
	 * @param int $archivesId    档期Id
	 * @param int $start_time    开播时间
	 * @param string $sub_title  开播预告
	 * @return array
	 */
	public function createArchivesLive($uid,$archivesId,$start_time,$sub_title){
		if($archivesId<=0||$uid<=0||$start_time<=0||empty($sub_title))
			return $this->setError(Yii::t('common','Parameter not empty21'),0);
		$archives=$this->getArchivesByArchivesId($archivesId);
		if(isset($archives['live_record']['status'])){
			if($archives['live_record']['status']==WIIL_START_LIVE||$archives['live_record']['status']==START_LIVE)
				return $this->setError(Yii::t('archives','Archives live_record not end'),0);
		}
		if($archives['uid']!=$uid)
			return $this->setError(Yii::t('archives','Not the archives owner'),0);

		$record['archives_id']=$archivesId;
		$record['sub_title']=htmlspecialchars($sub_title,ENT_QUOTES);
		$record['status']=WIIL_START_LIVE;
		$record['start_time']=$start_time;
		$record['create_time']=time();

		$recordId=$this->saveArchivesLiveRecords($record);
		if(!$recordId)
			return $this->setError(Yii::t('archives','Archives live_records data save failed'),0);
		//存储即待直播数据到redis
		$records=$this->getLiveRecordByRecordIds($recordId);
		self::$otherRedisModel->setWillLiveToRedisByArchiveRecordId($recordId,$records[$recordId]);
		//按档期Id存储档期信息
		$newArchives=$this->getArchivesByArchivesId($archivesId);
		self::$otherRedisModel->saveArchives($archivesId,$newArchives);
		return $recordId;
	}
	
	
	/**
	 * 开始直播记录
	 * @param int $archivesId 所属档期ID
	 * @param int $uid 档期所有者ID
	 * @return int 
	 */
	public function startArchivesLive($uid,$archivesId,$live_model=ARCHIVES_FLASH_MODEL){
		if($archivesId<=0||$uid<=0)
			return $this->setError(Yii::t('common','Parameter not empty22'),0);
		$archives=$this->getArchivesByArchivesId($archivesId);
		if($archives['uid']!=$uid)
			return $this->setError(Yii::t('archives','Not the archives owner'),0);
		$liveRecord=isset($archives['live_record'])?$archives['live_record']:array();
		if(empty($liveRecord)||$liveRecord['status']!=WIIL_START_LIVE){
			return $this->setError(Yii::t('archives','State operation not allowed'),0);
		}
		//开始直播
		$record=array();
		$record['archives_id']=$archivesId;
		$record['record_id']=$liveRecord['record_id'];
		$record['status']=START_LIVE;
		$record['live_model']=$live_model;
		$record['live_time']=time();
		$recordId=$this->saveArchivesLiveRecords($record);
		$records=$this->getLiveRecordByRecordIds($recordId);
		self::$otherRedisModel->setLivingToRedisByArchiveRecordId($recordId,$records[$recordId]);
		self::$otherRedisModel->unsetWillLiveFromRedis($recordId);
		//更新redis中档期数据
		if($recordId){
			$archives=self::$otherRedisModel->getArchives($archivesId);
			$archives=array_pop($archives);
			$archiveRecord=$this->getLiveRecordByRecordIds($recordId);
			$archives['live_record']=$archiveRecord[$recordId];
			self::$otherRedisModel->saveArchives($archivesId, $archives);
			$zmq=$this->getZmq();
			$eventData['type']='start_live';
			$eventData['uid']=$archives['uid'];
			$eventData['json_info']=array('archives_id'=>$archivesId,'domain'=>DOMAIN,'record_id'=>$liveRecord['record_id']);
			$zmq->sendZmqMsg(609,$eventData);
			
			if ($this->isAdminAccessCtl()){
				$op_desc = '开播(record_id='.$recordId.')';
				$this->saveAdminOpLog($op_desc,$uid);
			}
		}
		return $recordId;
	}


	/**
	 * 结束直播记录
	 * @param int $archivesId 所属档期ID
	 * @param int $uid 档期所有者ID
	 * @param int end_time 结束时间
	 * @return int 
	 */
	public function stopArchivesLive($uid,$archivesId, $end_time = 0){
		if($archivesId<=0||$uid<=0)
			return $this->setError(Yii::t('common','Parameter not empty23'),0);
		$archives=$this->getArchivesByArchivesId($archivesId);
		if($archives['uid']!=$uid)
			return $this->setError(Yii::t('archives','Not the archives owner'),0);
		$liveRecord=isset($archives['live_record'])?$archives['live_record']:array();
		if(empty($liveRecord)||$liveRecord['status']!=START_LIVE){
			return $this->setError(Yii::t('archives','State operation not allowed'),0);
		}
		$record=array();
		$record['archives_id']=$archivesId;
		$record['record_id']=$liveRecord['record_id'];
		$record['status']=END_LIVE;
		$record['end_time']=$end_time > 0 ? $end_time : time();
		$recordId=$this->saveArchivesLiveRecords($record);
		self::$otherRedisModel->unsetLivingFromRedis($recordId);
		//更新redis中档期数据
		if($recordId){
			$archives=self::$otherRedisModel->getArchives($archivesId);
			$archives=array_pop($archives);
			$archiveRecord=$this->getLiveRecordByRecordIds($recordId);
			$archives['live_record']=$archiveRecord[$recordId];
			self::$otherRedisModel->saveArchives($archivesId, $archives);
			$zmq=$this->getZmq();
			$eventData['type']='stop_live';
			$eventData['uid']=$archives['uid'];
			$eventData['json_info']=array('archives_id'=>$archivesId,'domain'=>DOMAIN,'record_id'=>$liveRecord['record_id']);
			$zmq->sendZmqMsg(609,$eventData);
			if ($this->isAdminAccessCtl()){
				//后台结束档期，发送广播消息来结束前台信号
				$zmq->sendBrodcastMsg(array('archives_id'=>$archivesId,'domain'=>DOMAIN,'type'=>'localroom','json_content'=>array('type'=>'stop_live','uid'=>$archives['uid'])));
				$op_desc = '停播(record_id='.$recordId.')';
				$this->saveAdminOpLog($op_desc,$uid);
			}
		}
		return $recordId;
	}


	/**
	 * 存储直播间发言设置
	 * @param int $archivesId 档期ID
	 * @param array $sets 设置信息
	 * @return mix|boolean
	 */
	public function saveChatSet($archivesId,array $sets){
		if($archivesId<=0)
			return  $this->setError(Yii::t('common','Parameter not empty24'),-1);
		if(!isset($sets['tourist_set'])&&!isset($sets['global_set'])){
			return  $this->setError(Yii::t('common','Parameters are wrong'),-2);
		}
		$sets['tourist_set']=empty($sets['tourist_set'])?UNABLE_TOURIST_CHAT:ENABLE_TOURIST_CHAT;
		$sets['global_set']=empty($sets['global_set'])?UNABLE_GLOBAL_CHAT:ENABLE_GLOBAL_CHAT;
		//存储直播间发言设置到redis
		self::$otherRedisModel->saveChatSet($archivesId, $sets);
		$zmq=$this->getZmq();
		$zmqData['archives_id']=$archivesId;
		$zmqData['domain']=DOMAIN;
		$zmqData['type']='localroom';
		$json_content=array('type'=>'chatSet','tourist_set'=>$sets['tourist_set'],'global_set'=>$sets['global_set']);
		$zmqData['json_content']=$json_content;
		$zmq->sendZmqMsg(606,$zmqData);
		return true;
	}


	/**
	 * 转移直播间用户
	 * @param int $archivesId
	 * @param int $target_uid
	 * @param int $sub_id
	 * @return mix
	 */
	public function moveViewer($archivesId,$target_uid,$sub_id=0){
		if($archivesId<=0||$target_uid<=0)
			return  $this->setError(Yii::t('common','Parameter not empty25'),0);
		$archives=$this->getArchivesByUids($target_uid,true,$sub_id);
		if($archives){
			$archives=array_pop($archives);
		}
		if(empty($archives)||!isset($archives['live_record'])){
			return  $this->setError(Yii::t('archives','Archives not has live_record'),0);
		}
		if($archives['live_record']['status']!=START_LIVE){
			return  $this->setError(Yii::t('archives','Archives live_record not start'),0);
		}
		//发送localroom消息
		$zmq=$this->getZmq();
		$zmqData['archives_id']=$archivesId;
		$zmqData['domain']=DOMAIN;
		$zmqData['type']='localroom';
		$sendArchives=$this->getArchivesByArchivesId($archivesId);
		$json_content=array('type'=>'moveViewer','send_uid'=>$sendArchives['uid'],'target_uid'=>$target_uid);
		$zmqData['json_content']=$json_content;
		$zmq->sendZmqMsg(606,$zmqData);
		return true;
	}

	/**
	 * 获取档期的发言设置
	 * @param int $archivesId 档期Id
	 * @return array
	 */
	public function getChatSet($archivesId){
		//初始直播间发言设置
		$chatSets=array('tourist_set'=>0,'global_set'=>0);
		$chatSet=self::$otherRedisModel->getChatSet($archivesId);
		return $chatSet=$chatSet?$chatSet:$chatSets;
	}

	/**
	 * 添加直播间房管
	 * @param int $uid 用户uid
	 * @param int $doteyId 主播doteyId
	 * @param int $archivesId 档期ID
	 * @return boolean
	 */
	public function addManage($uid,$doteyId,$archivesId){
		if($uid<=0||$archivesId<=0)
			return  $this->setError(Yii::t('common','Parameter not empty26'),0);
		$archivesUser=self::getArchivesUserByArchivesIds($archivesId);
		$isArchivesDotey=false;
		foreach($archivesUser[$archivesId] as $row){
			if($row['uid']==$doteyId){
				$isArchivesDotey=true;
			}
		}
		if($isArchivesDotey==false){
			return  $this->setError(Yii::t('archives','No permission to operate'),0);
		}
		$condition['uid']=$uid;
		$condition['archives_id']=$archivesId;
		$data=$this->getPurviewLiveByCondition($condition);
		if($data)
			return  $this->setError(Yii::t('archives','The user manage is exits'),0);
		$consumeService=new ConsumeService();
		$userInfo=$consumeService->getConsumesByUids(array($uid,$doteyId));
		$doteyManageCount=$this->getPurviewLiveCountByArchivesId($archivesId);
		$doteyRankInfo=$consumeService->getDoteyRanksInfoByGrades($userInfo[$doteyId]['dotey_rank']);
		if($doteyManageCount>=$doteyRankInfo[$userInfo[$doteyId]['dotey_rank']]['house_m_num'])
			return  $this->setError(Yii::t('archives','The dotey manage reaches the upper limit'),0);
		$userManageCount=$this->getPurviewLiveCountByUids($uid);
		$userRankInfo=$consumeService->getUserRanksInfoByGrades($userInfo[$uid]['rank']);
		if($userManageCount>=$userRankInfo[$userInfo[$uid]['rank']]['house_m_num'])
			return  $this->setError(Yii::t('archives','The user manage reaches the upper limit'),0);
		$purviewLive['uid']=$uid;
		$purviewLive['archives_id']=$archivesId;
		$result=$this->savePurviewLive($purviewLive);
		if($result){
			$this->savePurviewManageJsonInfo($uid,$archivesId,true);
			$zmq=$this->getZmq();
			$zmqData = array();
			$usersInfo=new UserJsonInfoService();
			$from_user_data=$usersInfo->getUserInfo($doteyId,false);
			$zmqData['archives_id']=$archivesId;
			$zmqData['domain']=DOMAIN;
			$zmqData['uid'] = $doteyId;
			$zmqData['nickname'] = $from_user_data['nk'];
			$to_user_data=$usersInfo->getUserInfo($uid,false);
			$zmqData['to_uid'] = $uid;
			$zmqData['to_nickname'] = $to_user_data['nk'];
			$zmqData['type'] = 7;
			$zmqData['period'] = 30;
			$zmqData['status'] =1;
			$zmq->sendZmqMsg(607,$zmqData);
		}
		//写入房管到redis
		$userListService=new UserListService();
		$userListService->saveArchivesManageList($archivesId);
		return $result;
	}

	/**
	 * 解除直播间房管
	 * @param int $uid    用户Id
	 * @param int $doteyId 主播Id
	 * @param int $archivesId
	 */

	public function removeManage($uid,$doteyId,$archivesId,$send_uid=0){
		if($uid<=0||$archivesId<=0||$doteyId<=0)
			return  $this->setError(Yii::t('common','Parameter not empty27'),0);
		$archivesUser=self::getArchivesUserByArchivesIds($archivesId);
		$isArchivesDotey=false;
		foreach($archivesUser[$archivesId] as $row){
			if($row['uid']==$doteyId){
				$isArchivesDotey=true;
			}
		}
		if($isArchivesDotey==false){
			return  $this->setError(Yii::t('archives','No permission to operate'),0);
		}
		$condition['uid']=$uid;
		$condition['archives_id']=$archivesId;
		$data=$this->getPurviewLiveByCondition($condition);
		$data=array_pop($data);
		if(!$data)
			return  $this->setError(Yii::t('archives','The user manage not exits'),0);
		$result=$this->delPurviewLiveByIds($data['purview_live_id']);
		if($result){
			$this->savePurviewManageJsonInfo($uid,$archivesId,false);
			$zmq=$this->getZmq();
			$zmqData = array();
			$usersInfo=new UserJsonInfoService();
			if($send_uid>0){
				$doteyId=$send_uid;
			}
			$from_user_data=$usersInfo->getUserInfo($doteyId,false);
			$zmqData['archives_id']=$archivesId;
			$zmqData['domain']=DOMAIN;
			$zmqData['uid'] = $doteyId;
			$zmqData['nickname'] = $from_user_data['nk'];
			$to_user_data=$usersInfo->getUserInfo($uid,false);
			$zmqData['to_uid'] = $uid;
			$zmqData['to_nickname'] = $to_user_data['nk'];
			$zmqData['type'] = 6;
			$zmqData['period'] = 30;
			$zmqData['status'] =1;
			$zmq->sendZmqMsg(607,$zmqData);
		}
		//写入房管到redis
		$userListService=new UserListService();
		$userListService->saveArchivesManageList($archivesId);
		return $result;
	}



	/**
	 * 根据单个档期id获取档期属性
	 * @param int $archivesId 档期id
	 * @return array
	 */
	public function getArchivesByArchivesId($archivesId){
		
		if(empty($archivesId))
			return $this->setError(Yii::t('common','Parameter not empty28'),0);
		$archivesModel=new ArchivesModel();
		$data=$archivesModel->getArchivesByArchivesId($archivesId);
		$archives=array();
		if($data){
			$archives=$data->attributes;
			$archives['live_record']=$this->getLiveRecordByArchivesId($archivesId);
		}
		return $archives;
	}
	

	/**
	 * 根据创建者uid获取档期
	 * @param int $uid  档期创建者uid
	 * @param int $sub_id 分站Id,默认为主站
	 * @return array
	 */
	public function getArchivesByUid($uid,$sub_id=0){
		if(empty($uid))
			return $this->setError(Yii::t('common','Parameter not empty'),0);
		$archivesModel=new ArchivesModel();
		$data=$archivesModel->getArchivesByUid($uid,$sub_id);
		return $data?$this->arToArray($data):array();
	}

	/**
	 * 根据档期ID获取档期信息,redis中有从redis中获取，没有则从数据库中获取
	 * @param int|array $archivesIds 档期ID
	 * @return array
	 */
	public function getArchivesByArchivesIds($archivesIds){
		if(empty($archivesIds))
			return $this->setError(Yii::t('common','Parameter not empty29'),0);
 		$archivesIds=is_array($archivesIds)?$archivesIds:array($archivesIds);
 		$archivesList=array();
		$archivesList=self::$otherRedisModel->getArchives($archivesIds);
		$unArchivesIds = array();
		if(!$archivesList){
			$unArchivesIds = $archivesIds;
		}else{
			foreach ($archivesIds as $archivesId){
				if(!in_array($archivesId,array_keys($archivesList))){
					$unArchivesIds[] = $archivesId;
				}
			}
		}
		
		if($unArchivesIds){
			$archivesModel =  new ArchivesModel();
			$archives = $archivesModel->getArchivesByArchivesIds($unArchivesIds);
			$liveRecord=$this->getLiveRecordByArchivesIds($unArchivesIds);
			$liveRecords=array();
			if($liveRecord){
				foreach($liveRecord as $_records){
					$liveRecords[$_records['archives_id']] = $_records;
				}
			}
			if($archives){
				foreach($archives as $_archives){
					$archivesList[$_archives->archives_id] = $_archives->attributes;
					if(isset($liveRecords[$_archives->archives_id])){
						$archivesList[$_archives->archives_id]['live_record']=$liveRecords[$_archives->archives_id];
					}else{
						$archivesList[$_archives->archives_id]['live_record']=array();
					}
					self::$otherRedisModel->saveArchives($_archives->archives_id,$archivesList[$_archives->archives_id]);
				}
			}
			
		}
		return $archivesList;
	}

	/**
	 * 根据档期创建者获取档期信息和最近的直播记录
	 * @param array $uids 档期创建者
	 * @param boolen $sort 是否按照档期Id重建键值
	 * @param int $subId 分站id，默认为主站
	 * @return array
	 */
	public function getArchivesByUids($uids,$sort=true,$subId=0){
		if(empty($uids))
			return $this->setError(Yii::t('common','Parameter not empty30'),0);
		$uids=is_array($uids)?$uids:array($uids);
		$archivesUids=self::$otherRedisModel->getArchivesIdsByUids($uids,$subId);
		$unUids =$Uids= array();
		$archivesModel =  new ArchivesModel();
		$archivesIds=array();
		$archivesList=array();
		if(!$archivesUids){
			$unUids = $uids;
		}else{
			$archivesIds=array();
			foreach ($uids as $uid){
				if(!in_array($uid,array_keys($archivesUids))){
					$unUids[] = $uid;
				}
			}
			foreach($archivesUids as $_archives){
				foreach($_archives as $ids){
					$archivesIds[]=$ids;
				}
			}
			if($archivesIds){
				$archivesList=$this->getArchivesByArchivesIds($archivesIds);
			}
		}
		if($unUids){
			$archives = $archivesModel->getArchivesByUids($unUids,$subId);
			if($archives){
				$archives=$this->arToArray($archives);
				foreach($archives as $_archives){
					$archivesIds[]=$_archives['archives_id'];
				}
				$liveRecord=$this->getLiveRecordByArchivesIds($archivesIds);
				$liveRecord = $this->buildDataByIndex($liveRecord,'archives_id');
				$archivesList=array();
				foreach($archives as $row){
					$row['live_record']=array();
					if(isset($liveRecord[$row['archives_id']])){
						$row['live_record']=$liveRecord[$row['archives_id']];
					}
					$archivesList[$row['archives_id']]=$row;
				}
				$archivesDao=$this->buildDataByKey($archivesList,'uid','archives_id');
				foreach($archivesDao as $key=>$row){
					self::$otherRedisModel->saveArchivesIdsByUid($key,$subId,array_keys($row));
				}
			}

		}
		return $archivesList;
	}


	/**
	 * 根据档期Id获取聊天进程信息
	 * @param int|array $archivesIds 档期Id
	 * @return array
	 */
	public function getChatServerByArchivesIds($archivesIds){
		if(empty($archivesIds))
			return $this->setError(Yii::t('common','Parameter not empty31'),0);
		$archivesIds=is_array($archivesIds)?$archivesIds:array($archivesIds);
		$chatServerModel=new ChatServerModel();
		$chatServer=$chatServerModel->getChatServerByArchivesIds($archivesIds);
		return $this->arToArray($chatServer);
	}

	public function getChatServerByArchivesId($archivesId){
		if(empty($archivesId))
			return $this->setError(Yii::t('common','Parameter not empty'),0);
		$chatServerModel=new ChatServerModel();
		$chatServer=array();
		$chatServer=$chatServerModel->getChatServerByArchivesId($archivesId);
		return $chatServer->attributes;
	}
	
	public function delChatServerByChatIds($chatIds){
		if(empty($chatIds))
			return $this->setError(Yii::t('common','Parameter not empty'),0);
		$chatIds=is_array($chatIds)?$chatIds:array($chatIds);
		$chatServerModel=new ChatServerModel();
		$flag = $chatServerModel->delChatServerByChatIds($chatIds);
		if ($this->isAdminAccessCtl() && $chatIds && $flag){
			$op_desc = '删除 聊天进程(chat_ids='.implode(',', $chatIds).')';
			$this->saveAdminOpLog($op_desc);
		}
		return $flag;
	}

	/**
	 * 根据条件获取档期信息
	 * @param array $condition
	 * @return array
	 */
	public function getArchivesBycondition(array $condition=array(),$buildIndex = 'archives_id'){
		$archivesModel=new ArchivesModel();
		$archies=$archivesModel->getArchivesBycondition($condition);
		$data=$this->arToArray($archies);
		return $this->buildDataByIndex($data,$buildIndex);
	}
	
	public function getArchivesListByCondition($offset=0,$pageSize=20,array $condition=array()){
		$archivesModel=new ArchivesModel();
		$archives=array();
		$archives=$archivesModel->getArchives($offset,$pageSize,$condition);
		return $this->arToArray($archives);
	}
	
	/**
	 * 根据档期id获取档期的主播
	 * @param int|array $archivesIds
	 * @return array
	 */
	public function getArchivesUserByArchivesIds($archivesIds){
		if(empty($archivesIds)) 
			return $this->setError(Yii::t('common','Parameter not empty'),0);
		$archivesIds=is_array($archivesIds)?$archivesIds:array($archivesIds);
		$archivesUserModel=new ArchivesUserModel();
		$data=$archivesUserModel->getArchivesUserByarchivesIds($archivesIds);
		$list=$this->arToArray($data);
		return $this->buildDataByKey($list, 'archives_id');
	}
	/**
	 * 获取所有的档期分类
	 * @author supeng
	 */
	public function getAllArchiveCat(){
		$archivesCatModel = new ArchivesCategoryModel();
		return $this->buildDataByIndex($this->arToArray($archivesCatModel->findAll()), 'cat_id');
	}

	/**
	 * 根据英文标识获取分类信息
	 * 
	 * @author supeng
	 * @param string $enName
	 * @return Ambigous <multitype:, multitype:unknown Ambigous <multitype:unknown , unknown> >
	 */
	public function getAllArchiveCatByEnName($enName){
		$archivesCatModel = new ArchivesCategoryModel();
		return $archivesCatModel->getAllArchiveCatByEnName($enName)->attributes;
	}
	
	/**
	 * 获取档期最后播出的记录
	 * @return array
	 */
	public function getLatestLiveRecord(){
		$liveRecordModel=new LiveRecordsModel();
		$data=$liveRecordModel->getLatestLiveRecord();
		return $this->arToArray($data);
	}
	
	
	/**
	 * 根据直播记录id获取记录信息
	 * @param int|array $recordIds 记录Id
	 * @return array
	 */
	public function getLiveRecordByRecordIds($recordIds){
		if(empty($recordIds))
			return $this->setError(Yii::t('common','Parameter not empty32'),0);
		$recordIds=is_array($recordIds)?$recordIds:array($recordIds);
		$liveRecordModel=new LiveRecordsModel();
		$data=$liveRecordModel->getLiveRecordByRecordIds($recordIds);
		$recordList=$this->arToArray($data);
		return $this->buildDataByIndex($recordList,'record_id');
	}

	/**
	 * 根据档期ID获取最近直播记录
	 * @param int $archivesId
	 * @return array
	 */
	public function getLiveRecordByArchivesId($archivesId){
		if(empty($archivesId))
			return $this->setError(Yii::t('common','Parameter not empty33'),0);
		$liveRecordModel=new LiveRecordsModel();
		$record=$liveRecordModel->getLiveRecordByarchiveId($archivesId);
		$liveRecord=array();
		if($record){
			$recordId=$record->attributes['record_id'];
			$liveRecord=$this->getLiveRecordByRecordIds($recordId);
			$liveRecord=$liveRecord[$recordId];
		}
		return $liveRecord;
	}


	/**
	 * 根据档期ID获取最近直播记录
	 * @param int|array $archivesIds
	 * @return array
	 */
	public function getLiveRecordByArchivesIds($archivesIds){
		if(empty($archivesIds))
			return $this->setError(Yii::t('common','Parameter not empty34'),0);
		$archivesIds=is_array($archivesIds)?$archivesIds:array($archivesIds);
		$liveRecordModel=new LiveRecordsModel();
		$record=$liveRecordModel->getLatestLiveIdByArchiveIds($archivesIds);
		$recordIds=array();
		foreach($record as $_record){
			$recordIds[]=$_record->attributes['record_id'];
		}
		$liveRecord=array();
		if($recordIds){
			$liveRecord=$this->getLiveRecordByRecordIds($recordIds);
		}
		return $liveRecord;
	}

	/**
	 * 根据条件查询直播记录
	 */
	public function getLiveRecordByCondition($archivesIds, $condition = array(), $offset = 0, $limit = 10)
	{
		$liveRecordModel = new LiveRecordsModel();
		$records = $liveRecordModel->getLiveRecordsByCondition($archivesIds,$condition, $offset, $limit);
		$records['list'] = $this->arToArray($records['list']);
		return $records;
	}

	/**
	 * 根据条件查询直播记录
	 */
	public function getLiveRecordsByCondition($archivesIds, $condition = array())
	{
		$liveRecordModel = new LiveRecordsModel();
		$records = $liveRecordModel->getLiveRecords($archivesIds,$condition);
		return $this->buildDataByKey($this->arToArray($records),'archives_id');
	}
	/**
	 * @author supeng
	 * @param array $condition
	 * @return Ambigous <multitype:, multitype:NULL >
	 */
	public function getLiveRecordsByFilter(Array $condition){
		$liveRecordModel = new LiveRecordsModel();
		return $this->buildDataByIndex($liveRecordModel->getLiveRecordsByFilter($condition), 'uid');
	}

	/**
	 * 获取档期直播间人数统计
	 * @author supeng
	 * @param array $archivesIds
	 */
	public function getSessTotalSumByCondition(Array $archivesIds){
		if (empty($archivesIds)){
			return $this->setError(Yii::t('common', 'Parameter not empty35'),false);
		}
		$sessStat = new ArchivesOnlineRecordModel();
		$result = $sessStat->getSessTotalSumByCondition($archivesIds);
		return $this->buildDataByIndex($result, 'archives_id');
	}

	/**
	 * 获取档期直播间人数统计
	 * @author supeng
	 * @param array $archivesIds
	 */
	public function getSessStatSumByCondition(Array $archivesIds,Array $condition = array(),$isSort = false){
		if (empty($archivesIds)){
			return $this->setError(Yii::t('common', 'Parameter not empty36'),false);
		}
		$sessTotal = new ArchivesOnlineStatisticsModel();
		return $this->buildDataByIndex($sessTotal->getSessStatSumByCondition($archivesIds,$condition,$isSort),'archives_id');
	}

	/**
	 * 根据条件统计直播记录
	 */
	public function getLiveRecordsByMonth($archivesIds, $condition)
	{
		$liveRecordModel = new LiveRecordsModel();
		$res = $liveRecordModel->getLiveRecordsByMonth($archivesIds, $condition);
		return $this->arToArray($res);
	}

	/**
	 * 根据档期分类ID获取分类属性
	 * @param int|array $catIds
	 * @return array
	 */
	public function getAttributeByCatIds($catIds){
		if(empty($catIds))
			return $this->setError(Yii::t('common','Parameter not empty37'),0);
		$catIds=is_array($catIds)?$catIds:array($catIds);
		$archivesAttributeModel=new ArchivesAttributeModel();
		$data=$archivesAttributeModel->getArchivesCatByCatIds($catIds);
		return $this->arToArray($data);
	}


	/**
	 * 获取所有视频服务器地址
	 * @return array
	 */
	public function getLiveServer(){
		$liveServerModel=new LiveServerModel();
		$live=$liveServerModel->getLiveServer();
		$data=$this->arToArray($live);
		return $this->buildDataByIndex($data, 'server_id');
	}


	/**
	 * 获取使用最少的视频服务器信息
	 * @return array
	 */
	public function getMinUserLiveServer(){
		$liveServerModel=new LiveServerModel();
		$live=$liveServerModel->getMinUserLiveServer();
		return $live->attributes;
	}

	public function addLiveServerUseByServerId($serverId){
		if($serverId<=0)
			return $this->setError(Yii::t('common','Parameter not empty38'),0);
		$liveServerModel=new LiveServerModel();
		return $liveServerModel->addLiveServerUseByServerId($serverId);
	}

	/**
	 * 根据视频服务器id获取视频服务器地址
	 * @param int|array $serverIds 视频服务器ID
	 * @return array
	 */
	public function getLiveServerByServerIds($serverIds){
		if(empty($serverIds))
			return $this->setError(Yii::t('common','Parameter not empty39'),0);
		$serverIds=is_array($serverIds)?$serverIds:array($serverIds);
		$liveServerModel=new LiveServerModel();
		$live=$liveServerModel->getLiveServerByserverIds($serverIds);
		$data=$this->arToArray($live);
		return $this->buildDataByIndex($data, 'server_id');
	}

	/**
	 * 根据档期id获取视频服务器Id
	 * @param int|array $archivesId 档期ID
	 * @return array
	 */
	public function getArchivesLiveServerByArchivesId($archivesId){
		if(empty($archivesId))
			return $this->setError(Yii::t('common','Parameter not empty40'),0);
		$archivesLiveServerModel=new ArchivesLiveServerModel();
		$data=$archivesLiveServerModel->getArchivesLiveServerByArchivesId($archivesId);
		return $this->arToArray($data);
	}


	/**
	 * 获取所有聊天服务器地址
	 * @return array
	 */
	public function getGlobalServer(){
		$globalServerModel=new GlobalServerModel();
		$server=$globalServerModel->getGlobalServer();
		$data=$this->arToArray($server);
		return $this->buildDataByIndex($data, 'global_server_id');
	}


	/**
	 * 获取使用最少的聊天服务器信息
	 * @return array
	 */
	public function getMinUserGlobalServer(){
		$globalServerModel=new GlobalServerModel();
		$server=$globalServerModel->getMinUserGlobalServer();
		return $server->attributes;
	}

	public function addGlobalServerUseByServerId($serverId){
		if($serverId<=0)
			return $this->setError(Yii::t('common','Parameter not empty41'),0);
		$globalServerModel=new GlobalServerModel();
		return $globalServerModel->addGlobalServerUseByServerId($serverId);
	}
	
	public function reduceGlobalServerUserByServerId($serverId){
		if($serverId<=0)
			return $this->setError(Yii::t('common','Parameter not empty42'),0);
		$globalServerModel=new GlobalServerModel();
		return $globalServerModel->reduceGlobalServerUserByServerId($serverId);
	}
	

	/**
	 * 根据聊天服务器id获取聊天服务器地址
	 * @param int|array $serverIds 聊天服务器ID
	 * @return array
	 */
	public function getGlobalServerByServerIds($serverIds){
		if(empty($serverIds))
			return $this->setError(Yii::t('common','Parameter not empty43'),0);
		$serverIds=is_array($serverIds)?$serverIds:array($serverIds);
		$globalServerModel=new GlobalServerModel();
		$server=$globalServerModel->getGlobalServerByserverIds($serverIds);
		$data=$this->arToArray($server);
		return $this->buildDataByIndex($data, 'global_server_id');
	}

	/**
	 * 获取正在直播的档期，今日推荐的优先取出，不够从正在直播的档期中取出
	 * @param int $num 获取的条数
	 * @return array
	 */
	public function getRecommondLiveArchives($num,$target='_self'){
		if($num<=0)
			return $this->setError(Yii::t('common','Parameter not empty44'),0);
		$otherRedisModel=new OtherRedisModel();
		$archives=$otherRedisModel->getLivingFromRedis();
		$archivesIds=array();
		foreach($archives as $row){
			$archivesIds[]=$row['archives_id'];
		}
		$archivesArr=$this->getArchivesByArchivesIds($archivesIds);
		$archivesArr=$this->array_sort($archivesArr,'recommond');
		$archivesList=array();
		foreach($archivesArr as $row){
			if($row['is_hide']==ARCHIVES_NOT_HIDDEN){
				$archivesList[]=$row;
			}
		}
		$archivesList=count($archivesList)>$num?array_slice($archivesList, 0,$num):$archivesList;
		$doteyService=new DoteyService();
		$live_archives_list=array();
		$i=1;
		$j=0;
		while($i<=count($archivesList)){
			$doteyImage=$doteyService->getDoteyUpload($archivesList[$i-1]['uid'],'small');
			$doteyImage2=$doteyService->getDoteyUpload($archivesList[$i]['uid'],'small');
			$live_archives_list['imageArr'][$j]=array(
				isset($archivesList[$i-1]['uid'])?$doteyImage:'',
				isset($archivesList[$i]['uid']) ? $doteyImage2: '',
				isset($archivesList[$i-1]['uid']) ? ROOT_URL.$archivesList[$i-1]['uid'] : '',
				isset($archivesList[$i]['uid']) ? ROOT_URL.$archivesList[$i]['uid'] : '',
				(isset($archivesList[$i-1]['recommond'])&&$archivesList[$i-1]['recommond']==1) ? 0 :1,
				(isset($archivesList[$i]['recommond'])&&$archivesList[$i-1]['recommond']==1) ? 0:1,
				$target,
				$target
			);
			$live_archives_list['nameArr'][$j]=array(
				isset($archivesList[$i-1]['title']) ? $archivesList[$i-1]['title'] : '',
				isset($archivesList[$i]['title']) ? $archivesList[$i]['title'] : '',
				isset($archivesList[$i-1]['live_record']['live_time']) ?$this->changeTimeType(time()-$archivesList[$i-1]['live_record']['live_time']).'前开播' : '',
				isset($archivesList[$i]['live_record']['live_time']) ?  $this->changeTimeType(time()-$archivesList[$i]['live_record']['live_time']).'前开播' : '',
				isset($archivesList[$i-1]['uid']) ? ROOT_URL.$archivesList[$i-1]['uid'] : '',
				isset($archivesList[$i]['uid']) ?ROOT_URL.$archivesList[$i]['uid'] : '',
				$target,
				$target
			);
			$i+=2;
			$j++;
		}
		return $live_archives_list;
	}

	/**
	 * 根据条件查看房管
	 * @param array $condition
	 * @return array
	 */
	public function getPurviewLiveByCondition(array $condition=array()){
		$purviewLiveModel=new PurviewLiveModel();
		$data=$purviewLiveModel->getPurviewLiveByCondition($condition);
		return $this->arToArray($data);
	}

	/**
	 * 根据用户uid获取所属的房管
	 * @param int|array $uids 用户uid
	 * @return array
	 */
	public function getPurviewLiveByUids($uids){
		if(empty($uids))
			return $this->setError(Yii::t('common','Parameter is empty45'),0);
		$uids=is_array($uids)?$uids:array($uids);
		$purviewLiveModel=new PurviewLiveModel();
		$data=$purviewLiveModel->getPurviewLiveByUids($uids);
		$list=$this->arToArray($data);
		$purviewList=$this->buildDataByKey($list, 'uid');
		$purview=array();
		foreach($purviewList as $key=>$_purview){
			foreach($_purview as $row){
				$purview[$key][]=$row['archives_id'];
			}
		}
		return $purview;
	}

	/**
	 * 根据档期Id获取房管的用户uid
	 * @param int|array $archviesIds
	 * @return array
	 */
	public function getPurviewLiveByArchivesIds($archviesIds){
		if(empty($archviesIds))
			return $this->setError(Yii::t('common','Parameter is empty46'),0);
		$archviesIds=is_array($archviesIds)?$archviesIds:array($archviesIds);
		$purviewLiveModel=new PurviewLiveModel();
		$data=$purviewLiveModel->getPurviewLiveByArchivesIds($archviesIds);
		$list=$this->arToArray($data);
		$purviewList=$this->buildDataByKey($list, 'archives_id');
		$purview=array();
		foreach($purviewList as $key=>$_purview){
			foreach($_purview as $row){
				$purview[$key][]=$row['uid'];
			}
		}
		return $purview;
	}

	/**
	 * 获取用户拥有的房管数
	 * @author leiwei
	 * @param int|array $uids 房管拥有的uid
	 * @return int
	 */
	public function getPurviewLiveCountByUids($uids){
		if(empty($uids))
			return $this->setError(Yii::t('common','Parameter is empty47'),0);
		$uids=is_array($uids)?$uids:array($uids);
		$purviewLiveModel=new PurviewLiveModel();
		return $purviewLiveModel->getPurviewLiveCountByUids($uids);
	}

	/**
	 * 获取档期拥有的房管数
	 * @author leiwei
	 * @author guoshaobo 参数错误, $uids修改为$archivesId
	 * @param int|array $uids 房管拥有的uid
	 * @return int
	 */
	public function getPurviewLiveCountByArchivesId($archivesId){
		if(empty($archivesId))
			return $this->setError(Yii::t('common','Parameter is empty48'),0);
		$archivesId=is_array($archivesId)?$archivesId:array($archivesId);
		$purviewLiveModel=new PurviewLiveModel();
		return $purviewLiveModel->getPurviewLiveCountByArchivesIds($archivesId);
	}

	/**
	 * 获取直播间皇冠粉丝，本场粉丝，本周粉丝，最大贡献值礼物，礼物消息等
	 * @param int $archivesId
	 * @param int $key key值(crown,archives_dedication,week_dedication,archives_gift,most_archives_dedication,archives_dy_msg)
	 */
	public function getArchivesRelationData($archivesId,$key){
		$relationKey=array('crown','archives_dedication','week_dedication','archives_friendly','week_archives_friendly','archives_gift','most_archives_dedication','archives_dy_msg');
		if($archivesId<=0||empty($key)||!in_array($key,$relationKey)){
			return $this->setError(Yii::t('common','Parameter are wrong'),0);
		}
		return self::$otherRedisModel->getArchivesRelationData($archivesId,$key);
	}
	
	public function getArchives($offset=0,$pageSize=20,$status=''){
		$archivesModel=new ArchivesModel();
		$data=$archivesModel->getArchives();
		return $this->arToArray($data);
	}


	/**
	 * 获取档期最后播出的记录信息
	 *
	 * @param array $archiveIds 档期ID
	 * @author suqian
	 * @return array
	 */
	public function getLatestLiveInfoByArchiveIds($archiveIds){
		if(empty($archiveIds)){
			return $this->setError(Yii::t('common','Parameter is empty49'),0);
		}
		$archiveIds = is_array($archiveIds) ? $archiveIds : array($archiveIds);
		$liveModel = LiveRecordsModel::model();
		$models = $liveModel->getLatestLiveInfoByArchiveIds($archiveIds);
		return $this->buildDataByIndex($this->arToArray($models),'record_id');
	}
	/**
	 * 获取正在直播的档期
	 *
	 * @param int $uid 用户ID
	 * @param boolean $ifHasDotey 是否有主播
	 * @param boolean $ifHasAttention 是否关注
	 * @author su qian
	 * @return array
	 */
	public function getLivingArchives($uid,$ifHasDotey = false,$ifHasAttention = false){
		$living = self::$otherRedisModel->getLivingFromRedis();
		if(empty($living)){
			$liveRecordsModel = LiveRecordsModel::model();
			$living = $liveRecordsModel->getLivingArchives();
			if(empty($living)){
				return array();
			}
			$living = $this->arToArray($living);
			usort($living, array(self::$channelDoteySortService,'sortLivingArchivesByTimes'));
			$living = $this->buildDataByIndex($living,'record_id');
			self::$otherRedisModel->setLivingToRedisByArchiveRecordId(0,$living);
		}
		
		if(empty($living)){
			return array();
		}

		$archivesIds =  array_keys($this->buildDataByIndex($living,'archives_id'));
		$archives = $this->getArchivesByArchivesIds($archivesIds);
		
		self::$channelDoteySortService->filterArchives($archives,1);
		self::$channelDoteySortService->buildLiveArchives($archives,$uid,1,$ifHasDotey,$ifHasAttention);
		return self::$channelDoteySortService->sortLiveArchives($archives,CHANNEL_DOTEY_SORT_STARTTIME,1,true);

	}
	
	public function getAllLivingArchives(){
		$living = self::$otherRedisModel->getLivingFromRedis();
		if(empty($living)){
			$liveRecordsModel = LiveRecordsModel::model();
			$living = $liveRecordsModel->getLivingArchives();
			if(empty($living)){
				return array();
			}
			$living = $this->arToArray($living);
			usort($living, array(self::$channelDoteySortService,'sortLivingArchivesByTimes'));
			$living = $this->buildDataByIndex($living,'record_id');
			self::$otherRedisModel->setLivingToRedisByArchiveRecordId(0,$living);
		}else{
			usort($living, array(self::$channelDoteySortService,'sortLivingArchivesByTimes'));
		}
		
		if(empty($living)){
			return array();
		}
		
		$archivesIds =  array_keys($this->buildDataByIndex($living,'archives_id'));
		$archives = $this->getArchivesByArchivesIds($archivesIds);
		return self::$channelDoteySortService->filterArchives($archives,1);
	}

	/**
	 * 取得待直播的档期
	 *
	 * @param int $uid 用户ID
	 * @param boolean $ifHasDotey 是否有主播
	 * @param boolean $ifHasAttention 是否关注
	 * @author su qian
	 * @return array
	 */
	public function getWillLiveArchives($uid,$ifHasDotey = false,$ifHasAttention = false){
		$willLive = self::$otherRedisModel->getWillLiveFromRedis();
		if(empty($willLive)){
			$liveRecordsModel = LiveRecordsModel::model();
			$willLive = $liveRecordsModel->getWillLiveArchives();
			if(empty($willLive)){
				return array();
			}
			$willLive = $this->arToArray($willLive);
			usort($willLive, array(self::$channelDoteySortService,'sortWaitArchivesByTimes'));
			$willLive = $this->buildDataByIndex($willLive,'record_id');
			self::$otherRedisModel->setWillLiveToRedisByArchiveRecordId(0,$willLive);
		}

		if(empty($willLive)){
			return array();
		}

		$archivesIds =  array_keys($this->buildDataByIndex($willLive,'archives_id'));
		$archives = $this->getArchivesByArchivesIds($archivesIds);
		self::$channelDoteySortService->filterArchives($archives,2,3);
		self::$channelDoteySortService->buildLiveArchives($archives,$uid,2,$ifHasDotey,$ifHasAttention);
		return self::$channelDoteySortService->sortLiveArchives($archives,CHANNEL_DOTEY_SORT_WAIT_STARTTIME,2);

	}
	
	public function getAllWillLiveArchives($return=false){
		$willLive = self::$otherRedisModel->getWillLiveFromRedis();
		if(empty($willLive)){
			$liveRecordsModel = LiveRecordsModel::model();
			$willLive = $liveRecordsModel->getWillLiveArchives();
			if(empty($willLive)){
				return array();
			}
			$willLive = $this->arToArray($willLive);
			usort($willLive, array(self::$channelDoteySortService,'sortWaitArchivesByTimes'));
			$willLive = $this->buildDataByIndex($willLive,'record_id');
			self::$otherRedisModel->setWillLiveToRedisByArchiveRecordId(0,$willLive);
		}else{
			usort($willLive, array(self::$channelDoteySortService,'sortWaitArchivesByTimes'));
		}
		
		if(empty($willLive)){
			return array();
		}
		
		$archivesIds =  array_keys($this->buildDataByIndex($willLive,'archives_id'));
		$archives = $this->getArchivesByArchivesIds($archivesIds);
		return $return?self::$channelDoteySortService->filterArchives($archives,2,3):$archives;
	}

	/**
	 * 取得用户管理的直播的档期
	 *
	 * @param int $uid 用户ID
	 * @param boolean $ifHasDotey 是否包含主播信息
	 * @param boolean $ifHasAttention 是否关注信息
	 * @author su qian
	 * @return array
	 */
	public function getUserManagerArchives($uid,$ifHasDotey = false,$ifHasAttention = false){
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty50'),0);
		}
		$managerArchives = $this->getPurviewLiveByUids($uid);
		if(empty($managerArchives)){
			return array();
		}
		$managerArchives = $managerArchives[$uid];
		$archives = $this->getArchivesByArchivesIds($managerArchives);
		self::$channelDoteySortService->filterArchives($archives,0);
		self::$channelDoteySortService->buildLiveArchives($archives,$uid,0,$ifHasDotey,$ifHasAttention);
		return self::$channelDoteySortService->sortLiveArchives($archives,CHANNEL_DOTEY_SORT_STARTTIME,0);

	}
	/**
	 * 取得用户关注的主播
	 *
	 * @param int $uid
	 * @param boolean $ifHasDotey
	 * @return boolean
	 */
	public function getUserAttentionArchives($uid,$ifHasDotey = false){
		if($uid <= 0){
			return array();
		}
		$weiboService = new WeiboService();

		$fans = $weiboService->getDoteyAttentionsByUid($uid);
		if(empty($fans)){
			return array();
		}

		$uids = array_keys($this->buildDataByIndex($fans,'uid'));
		$archives = $this->getArchivesByUids($uids,true);
		self::$channelDoteySortService->filterArchives($archives,0);
		self::$channelDoteySortService->buildLiveArchives($archives,$uid,0,$ifHasDotey,false);
		foreach($archives as $key=>$archive){
			$archives[$key]['is_attention'] = 1;
		}
		return self::$channelDoteySortService->sortLiveArchives($archives,CHANNEL_DOTEY_SORT_STARTTIME,0);
	}
	/**
	 * 取得用户最近观看的直播的档期
	 *
	 * @param int $uid 用户ID
	 * @param boolean $ifHasDotey 是否包含主播信息
	 * @param boolean $ifHasAttention 是否关注信息
	 * @author su qian
	 * @return array
	 */
	public function getUserLatestSeeArchives($uid,$ifHasDotey = false,$ifHasAttention =false){
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty52'),0);
		}
		$model = UserArchivesViewModel::model();
		$userLatestView = $model->getUserLatestSeeArchives($uid);
		if(empty($userLatestView)){
			return array();
		}
		$userLatestView = $this->arToArray($userLatestView);
		$archivesIds = array_keys($this->buildDataByIndex($userLatestView,'archives_id'));
		$archives = $this->getArchivesByArchivesIds($archivesIds);
		self::$channelDoteySortService->filterArchives($archives,0);
		self::$channelDoteySortService->buildLiveArchives($archives,$uid,0,$ifHasDotey,$ifHasAttention);
		return self::$channelDoteySortService->sortLiveArchives($archives,CHANNEL_DOTEY_SORT_STARTTIME,0);
	}

	/**
	 * 取得最近注册主播的直播的档期
	 *
	 * @param int $uid 用户ID
	 * @param int $days 最近的天数
	 * @param boolean $ifHasAttention 是否关注
	 * @author su qian
	 * @return array
	 */
	public function getLatestRegisterDoteyArchives($uid,$days = 30,$ifHasAttention = false){
		if($days && $days <= 0){
			return $this->setError(Yii::t('common','Parameter is empty53'),0);
		}
		$doteyService = new DoteyService();
		$condition['status'] = 1;
		if($days){
			$condition['startRegisterTime'] = time()- $days*3600*24;
		}
		$doteys = $doteyService->getDoteysByCondition($condition);
		if(empty($doteys)){
			return array();
		}
		$archives = $this->getArchivesByUids(array_keys($doteys),true);
		self::$channelDoteySortService->filterArchives($archives,0);
		self::$channelDoteySortService->buildLiveArchives($archives,$uid,0,$doteys,$ifHasAttention);
		return self::$channelDoteySortService->sortLiveArchives($archives,CHANNEL_DOTEY_SORT_STARTTIME,0);
	}

	
	//给档期附加是否是今日推荐主播
	public function addTodayRecommandForArchives(array &$archives,$uid,$ifHasDotey = false,$ifHasAttention =false)
	{
		
 		$todayRecommand=$this->getAllTodayRecommand($uid,$ifHasDotey,$ifHasAttention);
		if(isset($archives['living']) && count($archives['living'])>0)
		{
			if(isset($todayRecommand['living']) && count($todayRecommand['living'])>0)
			{
				$todayRecommand['living']=$this->buildDataByIndex($todayRecommand['living'], 'archives_id');
				$archives['living']=$this->buildDataByIndex($archives['living'], 'archives_id');
				
				foreach($todayRecommand['living'] as $_archiveId =>$_archive){
					if(isset($archives['living'][$_archiveId])){
						unset($archives['living'][$_archiveId]);
					}
					
				}
				if(isset($todayRecommand['living']) || isset($archives['living'])){
					$archives['living'] = array_merge(array_values($todayRecommand['living']),array_values($archives['living']));
				}
			}
		} 
		
		return $archives;
	}
	
	/**
	 * 取得主播今日推荐
	 *
	 * @return array
	 */
	public function getAllTodayRecommand($uid,$ifHasDotey = false,$ifHasAttention =false){
		$todayRecommandArchives = self::$otherRedisModel->getDoteyTodayRecommand();
		if(empty($todayRecommandArchives)){
			$todayRecommandModel = DoteyTodayRecommandModel::model();
			$todayRecommandArchives = $todayRecommandModel->getAllTodayRecommand();
			$todayRecommandArchives = $this->arToArray($todayRecommandArchives);
			self::$otherRedisModel->setDoteyTodayRecommand($todayRecommandArchives);
		}
		$archivesIds = array_keys($this->buildDataByIndex($todayRecommandArchives,'archives_id'));
		$archives = $this->getArchivesByArchivesIds($archivesIds);
		$operateService = new OperateService();
		$operate = $operateService->getOperateByCategoryFromCache(CATEGORY_INDEX);
		$operate = isset($operate[CATEGORY_INDEX_TODAYRECOMMAND]) ? $operate[CATEGORY_INDEX_TODAYRECOMMAND] : array();
		$doteyUids = array();
		foreach($operate as $_operate){
			$doteyUids[] = $_operate['target_id'];
		}
		if($doteyUids){
			$recommands = $this->getArchivesByUids($doteyUids);
			foreach($archives as $_archiveId =>$_archive){
				if(isset($recommands[$_archiveId])){
					unset($recommands[$_archiveId]);
				}
			}
			//合并后台推荐的
			if($archives && $recommands){
				$archives = array_merge(array_values($archives),array_values($recommands));
			}
		}
		
		foreach ($archives as &$_archive)
		{
			$_archive['today_recommand']=true;
		}
		
		self::$channelDoteySortService->filterArchives($archives,1);
		self::$channelDoteySortService->buildLiveArchives($archives,$uid,1,$ifHasDotey,$ifHasAttention);
		return self::$channelDoteySortService->sortLiveArchives($archives,CHANNEL_DOTEY_SORT_STARTTIME,1,false);
	}	
	
	//给档期附加是否是上周唱将主播状态
	public function addStarSingerForArchives(array &$archives)
	{
		$otherRedisModel=new OtherRedisModel();
		$starSingers=$otherRedisModel->getLastWeekStarSinger();
		
		if(isset($archives['living']) && count($archives['living'])>0)
		{
			foreach ($archives['living'] as &$_live)
			{
				if(in_array($_live['uid'],$starSingers))
				{
					$_live['star_singer']=true;
				}
				else
				{
					$_live['star_singer']=false;
				}
			}
		}
	
		if(isset($archives['wait']) && count($archives['wait'])>0)
		{
			foreach ($archives['wait'] as &$_live)
			{
				if(in_array($_live['uid'],$starSingers))
				{
					$_live['star_singer']=true;
				}
				else
				{
					$_live['star_singer']=false;
				}
			}
		}
		return ;
	}	
	
	/**
	 * 获取直播状态
	 * @author supeng
	 */
	public function getLiveStatus(){
		return array(
			INVALID_LIVE => '无效',
			WIIL_START_LIVE => '待开始',
			START_LIVE => '正在直播',
			END_LIVE => '直播结束'
		);
	}

	/**
	 * 获取直播有效天单位(小时)
	 *
	 * @author supeng
	 * @param array $uid
	 */
	public function getLiveEffectDaysUnit(Array $uids){
		$webConfigSer = new WebConfigService();
		$doteySer = new DoteyService();
		//获取报酬Keys
		$doteyPayKeys = $webConfigSer->getDoteyPayKey($doteySer);
		$effectDirectKey = $doteyPayKeys[DOTEY_TYPE_DIRECT]['effectDay'];
		$effectFullTimeKey = $doteyPayKeys[DOTEY_TYPE_FULLTIME]['effectDay'];
		$effectProxyKey = $doteyPayKeys[DOTEY_TYPE_PROXY]['effectDay'];

		$configDirect = $webConfigSer->getWebConfig($effectDirectKey);
		$configFullTime = $webConfigSer->getWebConfig($effectFullTimeKey);
		$configProxy = $webConfigSer->getWebConfig($effectProxyKey);

		//获取主播类型
		$infos = $doteySer->getDoteyInfoByUids($uids);
		//允许的范围
		$allowDType = array_keys($doteySer->getDoteyType());

		//主播doteyType集合
		$types = array();
		if ($infos){
			foreach($infos as $v){
				$types[$v['dotey_type']][$v['uid']] = $v['uid'];
			}
		}

		//结果集合
		$result = array();
		if ($types){
			foreach ($types as $dtype => $duids){
				if(in_array($dtype, $allowDType)){
					foreach ($duids as $uid){
						if ($dtype == DOTEY_TYPE_DIRECT){
							if ($configDirect){
								if(key_exists($uid, $configDirect['c_value'])){
									$result[$uid] = $configDirect['c_value'][$uid]['day'];
								}else{
									if(isset($configDirect['c_value'][0])){
										$result[$uid] = $configDirect['c_value'][0]['day'];
									}else{
										$result[$uid] = DOTEY_LIVE_EFFECTDAY_UNIT;
									}
								}
							}else{
								$result[$uid] = DOTEY_LIVE_EFFECTDAY_UNIT;
							}
						}

						if ($dtype == DOTEY_TYPE_FULLTIME){
							if ($configProxy){
								if(key_exists($uid, $configProxy['c_value'])){
									$result[$uid] = $configProxy['c_value'][$uid]['day'];
								}else{
									if(isset($configProxy['c_value'][0])){
										$result[$uid] = $configProxy['c_value'][0]['day'];
									}else{
										$result[$uid] = DOTEY_LIVE_EFFECTDAY_UNIT;
									}
								}
							}else{
								$result[$uid] = DOTEY_LIVE_EFFECTDAY_UNIT;
							}
						}

						if ($dtype == DOTEY_TYPE_PROXY){
							if ($configFullTime){
								if(key_exists($uid, $configFullTime['c_value'])){
									$result[$uid] = $configFullTime['c_value'][$uid]['day'];
								}else{
									if(isset($configFullTime['c_value'][0])){
										$result[$uid] = $configFullTime['c_value'][0]['day'];
									}else{
										$result[$uid] = DOTEY_LIVE_EFFECTDAY_UNIT;
									}
								}
							}else{
								$result[$uid] = DOTEY_LIVE_EFFECTDAY_UNIT;
							}
						}
					}
				}else{
					foreach ($duids as $uid){
						$result[$uid] = DOTEY_LIVE_EFFECTDAY_UNIT;
					}
				}
			}
		}

		return $result;
	}


	

	/**
	 * 查询直播记录
	 *
	 * @author supeng
	 * @param array $condition
	 * @param int $offset
	 * @param int $pageSize
	 * @param bollean $isLimit
	 * @return array
	 */
	public function searchLiveRecordByCondition(Array $condition = array() ,$offset=0,$pageSize=10,$isLimit = true){
		$liveRecordModel = new LiveRecordsModel();

		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$doteySer = new DoteyService();
			$uids = $doteySer->searchDoteyUidsByCodition($condition);
			if ($uids){
				if (is_array($uids)){
					if (!empty($condition['uids'])){
						$condition['uids'] = array_intersect($condition['uids'],$uids);
					}else{
						$condition['uids'] = $uids;
					}
				}
			}else{
				return array('count'=>0,'list'=>array());
			}
		}

		$records = $liveRecordModel->searchLiveRecordsByCondition($condition,$offset,$pageSize,$isLimit);
		return $records;
	}
	
	/**
	 * 查询直播记录 进行去重统计
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 * @return multitype:number multitype: |Ambigous <multitype:multitype:, multitype:multitype: number NULL Ambigous <string, unknown, mixed> >
	 */
	public function searchDuplicateLiveRecordsByCondition(Array $condition = array() ,$offset=0,$pageSize=10,$isLimit = true){
		$liveRecordModel = new LiveRecordsModel();
	
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$doteySer = new DoteyService();
			$uids = $doteySer->searchDoteyUidsByCodition($condition);
			if ($uids){
				if (is_array($uids)){
					if (!empty($condition['uids'])){
						$condition['uids'] = array_intersect($condition['uids'],$uids);
					}else{
						$condition['uids'] = $uids;
					}
				}
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
	
		$records = $liveRecordModel->searchDuplicateLiveRecordsByCondition($condition,$offset,$pageSize,$isLimit);
		if (isset($records['list'])){
			$records['list'] = $this->buildDataByIndex($records['list'], 'record_id');
		}
		return $records;
	}
	
	/**
	 * 修改聊天服务器
	 * @param int $archives_id
	 * @param int $uid
	 * @param int $plus 0->减少;1->添加
	 * 
	 */
	public function changeChatServer($archives_id,$uid,$plus=true){
		if($archives_id<=0||$uid<=0)
			return $this->setError(Yii::t('common', 'Parameter is wrong!'),0);
		$chat=$this->getChatServerByArchivesId($archives_id);
		$zmq=$this->getZmq();
		if($plus){
			if($chat)
				return $this->setError(Yii::t('archives','Chat server is'));
			$globalServer=$this->getMinUserGlobalServer();
			if(!$globalServer){
				return $this->setError(Yii::t('archives','chat_server not exits'));
			}
			$chatServer['archives_id']=$archives_id;
			$chatServer['domain']=$globalServer['domain'];
			$chat_id=$this->saveChatServer($chatServer);
			if($chat_id){
				$this->addGlobalServerUseByServerId($globalServer['global_server_id']);
			}
			
			$eventData['type']='create_archives';
			
		}else{
			if(!$chat){
				return $this->setError(Yii::t('archives','chat_server not exits'));
			}
			$this->delChatServerByChatIds($chat['chat_id']);
			$this->reduceGlobalServerUserByServerId($globalServer['global_server_id']);
			$eventData['type']='stop_archives';
		}
		$eventData['uid']=$uid;
		$json_info['aid']=$archives_id;
		$json_info['a_domain']=DOMAIN;
		$json_info['s_domain']=$globalServer['domain'];
		$eventData['json_content']=$json_info;
		return $zmq->sendZmqMsg(609,$eventData);
	}

	/**
	 * 通过档期ID 统计档期直播时长
	 *
	 * @author supeng
	 * @param array $archivesIds
	 * @param array $condition
	 * @return mix|multitype:number multitype: |mixed
	 */
	public function searchLiveRecordByArchivesIds(Array $archivesIds,Array $condition = array()){
		if(empty($archivesIds) || !is_array($archivesIds)){
			return $this->setError(Yii::t('common', 'Parameter is wrong!'),0);
		}

		$liveRecordModel = new LiveRecordsModel();
		return $liveRecordModel->searchLiveRecordByArchivesIds($archivesIds,$condition);
	}



	/**
	 * 根据档期Id存储档期信息到redis
	 * @param int $archivesId 档期Id
	 * @return boolean
	 */
	public function saveArchivesRedisByArchivesId($archivesId){
		if($archivesId<=0)
			return $this->setError(Yii::t('common', 'Parameter is empty54'),0);
		$archives=self::getArchivesByArchivesId($archivesId);
		$otherRedisModel=new OtherRedisModel();
		return $otherRedisModel->saveArchives($archivesId,$archives);
	}


	/**
	 * 获取所有直播间背景
	 * @return array
	 */
	public function getArchivesBackGround(){
		$archivesBackgroundModel=new ArchivesBackgroundModel();
		$background=$archivesBackgroundModel->getArchivesBackground();
		return $this->arToArray($background);
	}

	/**
	 * 存储主播的userJsonInfo的pk信息
	 * @param int $uid
	 * @param int $archives_id
	 * @param boolen $plus 0->表示删除,1->表示添加
	 * @return boolean
	 */
	public function saveDoteyPurviewRank($uid,$archives_id,$plus=true){
		if($uid<=0||$archives_id<=0)
			return $this->setError(Yii::t('common','Parameter is empty55'),0);
		$userJson=new UserJsonInfoService();
		$newUserJson=$userJson->getUserInfo($uid,false);
		if($plus){
			if(isset($newUserJson['pk'][3])&&!empty($newUserJson['pk'][3])){
				if(!in_array($archives_id,$newUserJson['pk'][3])){
					array_push($newUserJson['pk'][3],$archives_id);
				}

			}else{
				$newUserJson['pk'][3][]=$archives_id;
			}
		}else{
			if(isset($newUserJson['pk'][3])){
				if(in_array($archives_id,$newUserJson['pk'][3])){
					$key=array_search($archives_id,$newUserJson['pk'][3]);
					if(isset($newUserJson['pk'][3][$key])){
						unset($newUserJson['pk'][3][$key]);
					}
				}
			}
		}
		$zmq=$this->getZmq();
		$zmqData['type']='update_json';
		$zmqData['uid']=$uid;
		$json_info['pk']=$newUserJson['pk'];
		$zmqData['json_info']=$json_info;
		$zmq->sendZmqMsg(609, $zmqData);
		return $userJson->setUserInfo($uid,$json_info);
	}


	/**
	 * 存储房管的userJsonInfo的pk信息
	 * @param int $uid
	 * @param int $archives_id
	 * @param boolen $plus 0->表示删除,1->表示添加
	 * @return boolean
	 */
	public function savePurviewManageJsonInfo($uid,$archives_id,$plus=true){
		if($uid<=0||$archives_id<=0)
			return $this->setError(Yii::t('common','Parameter is empty56'),0);
		$userJson=new UserJsonInfoService();
		$newUserJson=$userJson->getUserInfo($uid,false);
		if($plus){
			if(isset($newUserJson['pk'][2])&&!empty($newUserJson['pk'][2])){
				if(!in_array($archives_id,$newUserJson['pk'][2])){
					array_push($newUserJson['pk'][2],$archives_id);
				}
			
			}else{
				$newUserJson['pk'][2][]=$archives_id;
			}
			
		}else{
			if(isset($newUserJson['pk'][2])){
				if(in_array($archives_id,$newUserJson['pk'][2])){
					$key=array_search($archives_id,$newUserJson['pk'][2]);
					if(isset($newUserJson['pk'][2][$key])){
						array_splice($newUserJson['pk'][2],$key,1);
					}
				}
			}
		}
		$zmq=$this->getZmq();
		$zmqData['type']='update_json';
		$zmqData['uid']=$uid;
		$json_info['pk']=$newUserJson['pk'];
		$zmqData['json_info']=$json_info;
		$zmq->sendZmqMsg(609, $zmqData);
		return $userJson->setUserInfo($uid,$json_info);
	}

	/**
	 * 存储总管的userJsonInfo的pk信息
	 * 
	 * @author supeng
	 * @param int $uid
	 * @param boolen $plus 0->表示删除,1->表示添加
	 * @return boolean
	 */
	public function saveGeneralManageJsonInfo($uid,$plus=true){
		if($uid <= 0)
			return $this->setError(Yii::t('common','Parameter is empty57'),0);
		$userJson=new UserJsonInfoService();
		$newUserJson=$userJson->getUserInfo($uid,false);
		if($plus){
			$newUserJson['pk'][4] = array();
		}else{
			if(isset($newUserJson['pk'][4])){
				unset($newUserJson['pk'][4]);
			}
		}
		$zmq=$this->getZmq();
		$zmqData['type']='update_json';
		$zmqData['uid']=$uid;
		$json_info['pk']=$newUserJson['pk'];
		$zmqData['json_info']=$json_info;
		$zmq->sendZmqMsg(609, $zmqData);
		return $userJson->setUserInfo($uid,$json_info);
	}

	/**
	 * 根据uid获取主播今天魅力值排行名次
	 * @param int $uid
	 * @return int
	 */
	public function getAllDoteyCharmTodayRank($uid){
		if($uid<=0)
			return $this->setError(Yii::t('common','Parameter are wrong'),0);
		$otherRedisModel=new OtherRedisModel();
		$ranking=$otherRedisModel->getAllDoteyCharmTodayRank($uid);
		return $ranking>0?$ranking:'还未开播';
	}

	/**
	 * 根据uid获取主播本周魅力值排行名次
	 * @param int $uid
	 * @return int
	 */
	public function getAllDoteyCharmWeekRank($uid){
		if($uid<=0)
			return $this->setError(Yii::t('common','Parameter are wrong'),0);
		$otherRedisModel=new OtherRedisModel();
		$ranking=$otherRedisModel->getAllDoteyCharmWeekRank($uid);
		return $ranking>0?$ranking:'还未开播';
	}

	/**
	 * 查询直播间
	 *
	 * @author supeng
	 * @param array $condition
	 * @param int $offset
	 * @param int $pageSize
	 * @param bollean $isLimit
	 * @return array
	 */
	public function searchArchivesByCondition(Array $condition = array() ,$offset=0,$pageSize=10,$isLimit = true){
		$archivesModel = new ArchivesModel();

		if (!empty($condition['nickname'])){
			$doteySer = new DoteyService();
			$uids = $doteySer->searchDoteyUidsByCodition(array('nickname'=>$condition['nickname']));
			if(is_array($uids)){
				$condition['uid'] = $uids;
			}else{
				return array('count'=>0,'list'=>array());
			}
		}

		$records = $archivesModel->searchArchivesByCondition($condition,$offset,$pageSize,$isLimit);
		if($records['list']){
			$records['list'] = $this->buildDataByIndex($records['list'], 'archives_id');
		}
		return $records;
	}
	
	public function getArchivesDataFromRedis($uid,$archivesId){
		$otherRedisModel=new OtherRedisModel();
		$keys[]='allow_song_'.$archivesId;
		$keys[]='chat_set_'.$archivesId;
		$keys[]='archives_dedication_'.$archivesId;
		$keys[]='archives_gift_'.$archivesId;
		$keys[]='archives_dy_msg_'.$archivesId;
		$keys[]='most_dedication';
		$keys[]='crown_'.$archivesId;
		$keys[]='test_userlist_archives_'.$archivesId;
		$keys[]='all_dotey_charm_today_rank_uid_';
		$keys[]='all_dotey_charm_week_rank_uid_';
		$keys[]='dotey_info_'.$uid;
		$keys[]='archives_friendly_'.$archivesId;
		$keys[]='week_archives_friendly_'.$archivesId;
		$cacheData=$otherRedisModel->getArchivesDataFromRedis($keys);
		$userListService=new UserListService();
		$cacheData['all_dotey_charm_today_rank_uid_']=isset($cacheData['all_dotey_charm_today_rank_uid_'])?$cacheData['all_dotey_charm_today_rank_uid_'][$uid]:0;
		$cacheData['all_dotey_charm_week_rank_uid_']=isset($cacheData['all_dotey_charm_week_rank_uid_'])?$cacheData['all_dotey_charm_week_rank_uid_'][$uid]:0;
		$cacheData['test_userlist_archives_'.$archivesId]=$userListService->operateUserList($cacheData['test_userlist_archives_'.$archivesId],$archivesId);
		return $cacheData;
	}
	
	public function getArchivesDedicationFromRedis($archivesId){
		$otherRedisModel=new OtherRedisModel();
		$keys[]='archives_dedication_'.$archivesId;
		$keys[]='week_dedication_'.$archivesId;
		$keys[]='month_dedication_'.$archivesId;
		$keys[]='super_dedication_'.$archivesId;
		return $otherRedisModel->getArchivesDataFromRedis($keys);
	}
	

	/**
	 * @author supeng
	 * @return multitype:string
	 */
	public function getArchivesRecommond(){
		return array( 0=>'不推荐',1=>'推荐');
	}
	
	/**
	 * 获取直播间短地址
	 * @param int $uid
	 * @return string
	 */
	public function getArchivesShortUrl($uid){
		if($uid){
			return 'http://'.$_SERVER['HTTP_HOST'].'/'.$uid;
		}else{
			return 'http://'.$_SERVER['HTTP_HOST'];
		}
	}
	
	public function createChatToken($uid,$arhicvesId){
		if($uid<=0||$arhicvesId<=0)
			return $this->setError(Yii::t('common','Parameter are wrong'),0);
		$token='';
		if($uid){
			$tokenRedisModel=new TokenRedisModel();
			if($tokenRedisModel->getToken($uid,$arhicvesId)){
				$token=$tokenRedisModel->getToken($uid,$arhicvesId);
			}else{
				$token=md5(time().$uid.$arhicvesId.self::$secrect.rand(10000,9999));
				$tokenRedisModel->saveToken($uid,$arhicvesId,$token,self::$expireTime);
			}
		}
		return $token;
	}

	/**
	 * @supeng
	 * @return multitype:string
	 */
	public function getArchivesIsHide(){
		return array( 0=>'显示',1=>'隐藏');
	}
	
	/**
	 * @author supeng
	 * @param array $uids
	 * @param unknown_type $cat_ids
	 * @return mixed
	 */
	public function sataticsLiveRecords(Array $uids,$cat_ids){
		$liveRecordsModel = new LiveRecordsModel();
		$data =  $liveRecordsModel->statiticsLiveRecords($uids,$cat_ids);
		return $this->buildDataByIndex($data, 'uid');
	}
	
	protected function changeTimeType($seconds){
		if($seconds>86400){
			$hour=intval($seconds/3600);
			$minute=$seconds-$hour*3600;
			$time =$hour.'时'. gmstrftime('%M分', $minute);
		}else{
			$time = gmstrftime('%H时%M分', $seconds);
		}
		return $time;
	}

}

?>