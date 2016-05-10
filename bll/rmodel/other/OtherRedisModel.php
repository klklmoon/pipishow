<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: OtherRedisModel.php 17832 2014-02-11 10:15:02Z hexin $ 
 * @package rmodel
 * @subpackage other
 */
class OtherRedisModel {
	private static $instance;
	
	/**
	 * 返回OtherRedisModel对象的单例
	 * @return OtherRedisModel
	 */
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 取得other redis服务器连接
	 * return PipiRedisConnection
	 */
	public function getCacheRedisConnection(){
		return Yii::app()->redis_cache;
	}
	
	/**
	 * 将正在直播的信息放入Redis缓存
	 * 
	 * @param int $archiveRecordId 直播记录ID
	 * @param array $records
	 * @author su qian
	 * @return boolean
	 */
	public function  setLivingToRedisByArchiveRecordId($archiveRecordId,array $records){
		$redisCache = $this->getCacheRedisConnection();
		$livingKey = $this->getOtherRedisKey('dotey_living');
		if($archiveRecordId > 0){
			if(!isset($records['archives_id']) || !isset($records['start_time']) || !isset($records['end_time']) ){
				return $this->setError(Yii::t('common','Parameter is empty'),array());
			}
			$living = $this->getLivingFromRedis();
			if(empty($living)){
				$living[$archiveRecordId] = $records;
			}else{
				if(isset($living[$archiveRecordId])){
					unset($living[$archiveRecordId]);
				}
				$living[$archiveRecordId] = $records;
			}
		}else{
			//必须是以记录ID为key的二维数组
			$living=$records;
		}
		return $redisCache->set($livingKey,json_encode($living));
	}
	
	/**
	 * 获取正在直播的信息
	 * 
	 * @param int $archiveRecordId 直播记录ID
	 * @author su qian
	 * @return array
	 */
	public function getLivingFromRedis($archiveRecordId = null){
		$livingKey = $this->getOtherRedisKey('dotey_living');
		$redisCache = $this->getCacheRedisConnection();
		$living = $redisCache->get($livingKey);
		if(empty($living)){
			return array();
		}
		$living = json_decode($living,true);
		return $archiveRecordId && isset($living[$archiveRecordId]) ? $living[$archiveRecordId] : $living;
	}
	/**
	 * 注销掉正在直播的信息
	 * 
	 * @param int $archiveRecordId 直播记录ID
	 * @author suqian
	 * @return boolean true表示注销成功 false表示注销失败
	 */
	public function unsetLivingFromRedis($archiveRecordId = null){
		$livingKey = $this->getOtherRedisKey('dotey_living');
		$redisCache = $this->getCacheRedisConnection();
		$living = $this->getLivingFromRedis();
		if(empty($living)){
			return true;
		}
		if(isset($living[$archiveRecordId])){
			if($living){
				unset($living[$archiveRecordId]);
				return $redisCache->set($livingKey,json_encode($living));
			}
			return false;
		}
		return false;
	}
	
	
	
	
	/**
	 * 将待直播的信息放入Redis缓存
	 * 
	 * @param int $archiveRecordId 直播记录ID
	 * @param array $records
	 * @author su qian
	 * @return boolean
	 */
	public function  setWillLiveToRedisByArchiveRecordId($archiveRecordId,array $records){
		$willLiveKey = $this->getOtherRedisKey('dotey_will_live');
		$redisCache = $this->getCacheRedisConnection();
		if($archiveRecordId > 0){
			if(!isset($records['archives_id']) || !isset($records['start_time']) || !isset($records['end_time']) ){
				return $this->setError(Yii::t('common','Parameter is empty'),array());
			}
			$willLive = $this->getWillLiveFromRedis();
			if(empty($willLive)){
				$willLive[$archiveRecordId] = $records;
			}else{
				if(isset($willLive[$archiveRecordId])){
					unset($willLive[$archiveRecordId]);
				}
				$willLive[$archiveRecordId] = $records;
			}
		}else{
			//必须是以记录ID为key的二维数组
			$willLive=$records;
		}
		return $redisCache->set($willLiveKey,json_encode($willLive));
	}
	
	/**
	 * 获取待直播的信息
	 * 
	 * @param int $archiveRecordId 直播记录ID
	 * @author su qian
	 * @return array
	 */
	public function getWillLiveFromRedis($archiveRecordId = null){
		$willLiveKey = $this->getOtherRedisKey('dotey_will_live');
		$redisCache = $this->getCacheRedisConnection();
		$willLive = $redisCache->get($willLiveKey);
		if(empty($willLive)){
			return array();
		}
		$willLive = json_decode($willLive,true);
		return $archiveRecordId && isset($willLive[$archiveRecordId]) ? $willLive[$archiveRecordId] : $willLive;
	}
	/**
	 * 注销掉待直播的信息
	 * 
	 * @param int $archiveRecordId 直播记录ID
	 * @author suqian
	 * @return boolean true表示注销成功 false表示注销失败
	 */
	public function unsetWillLiveFromRedis($archiveRecordId = null){
		$willLiveKey = $this->getOtherRedisKey('dotey_will_live');
		$redisCache = $this->getCacheRedisConnection();
		$willLive = $this->getWillLiveFromRedis();
		if(empty($willLive)){
			return true;
		}
		if(isset($willLive[$archiveRecordId])){
			if($willLive){
				unset($willLive[$archiveRecordId]);
				return $redisCache->set($willLiveKey,json_encode($willLive));
			}
			return false;
		}
		return false;
	}
	
	/**
	 * 存储主播信息到Redis缓存
	 * 
	 * @param int $uid
	 * @param array $doteys
	 * @author su qian
	 * @return boolean
	 */
	public function setDoteyInfoToRedisByUid($uid,array $doteys = array()){
		if($uid <= 0){
			return array();
		}
		if(empty($doteys)){
			//todo 可能添加根据ID查找主播信息
			return array();
		}
		$key = $this->getOtherRedisKey('dotey_info');
		$redisCache = $this->getCacheRedisConnection();
		$userService = new UserService();
		$extendUser = $userService->getUserExtendByUids(array($uid));
		if(isset($doteys['update_desc']) && is_string($doteys['update_desc'])){
			$doteys['update_desc'] = json_decode($doteys['update_desc'],true);
		}
		if($extendUser){
			$extendUser = $extendUser[$uid];
			$doteys['birthday'] = $extendUser['birthday'];
			$doteys['province'] = $extendUser['province'];
			$doteys['city'] = $extendUser['city'];
			$doteys['description'] = $extendUser['description'];
			$doteys['profession'] = $extendUser['profession'];
		}
		return $redisCache->set($key.$uid,json_encode($doteys));
	}
	
	/**
	 * 从redis取得主播信息
	 * 
	 * @param int $uid
	 * @author suqian
	 * @return  array
	 */
	public function getDoteyInfoFromRedisByUid($uid){
		if($uid <= 0){
			return array();
		}
		$key = $this->getOtherRedisKey('dotey_info');
		$redisCache = $this->getCacheRedisConnection();
		$doteyData =  $redisCache->get($key.$uid);
		return $doteyData ? json_decode($doteyData,true) : array();
	
	}
	
	/**
	 * 批量获取主播信息
	 * 
	 * @param array $uids
	 * @return array
	 */
	public function getDoteyInfoFromRedisByUids(array $uids){
		if(empty($uids)){
			return array();
		}
		$key = $this->getOtherRedisKey('dotey_info');
		$redisCache = $this->getCacheRedisConnection();
		$batKeys = array();
		foreach($uids as $uid){
			$batKeys[] = $key.$uid;
		}
		$doteyData = $redisCache->mget($batKeys);
		if(!$doteyData){
			return array();
		}
		foreach($doteyData as $key=>$data){
			$_data = json_decode($data,true);
			if(!is_array($_data)){
				continue;
			}
			$doteyData[$key] = $_data;
		}
		return $doteyData;
		
	}
	
	/**
	 * 删除主播的缓存信息，主要用在删除已经拒绝申请的主播表信息
	 * @param int|array $uids
	 */
	public function deleteDoteyInfoByUids($uids){
		$uids = is_array($uids) ? $uids : array(intval($uids));
		$key_pre = $this->getOtherRedisKey('dotey_info');
		$keys = array();
		foreach($uids as $uid){
			$keys[] = $key_pre.$uid;
		}
		$redisCache = $this->getCacheRedisConnection();
		return $redisCache->delete($keys);
	}
	
	public function saveGiftList(array $giftList){
		$otherRedis=$this->getCacheRedisConnection();
		$giftKey=$this->getOtherRedisKey('giftList');
		return $otherRedis->set($giftKey,json_encode($giftList));
	}
	
	public function getGiftList(){
		$otherRedis=$this->getCacheRedisConnection();
		$giftKey=$this->getOtherRedisKey('giftList');
		$data=$otherRedis->get($giftKey);
		return json_decode($data,true);
	}
	
	
	
	/**
	 * 获取主播歌单
	 * @param int $doteyId
	 * return string
	 */
	public function getDoteySongByDoteyId($doteyId){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey();
		$doteySongKey=$this->getOtherRedisKey('doteySong');
		$data=$otherRedis->get($doteySongKey.$doteyId);
		return json_decode($data);
	}
	
	/**
	 * 写入主播歌单
	 * @param int $doteyId
	 * @param array $data
	 * return int
	 */
	public function saveDoteySongByDoteyId($doteyId,array $data){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey();
		$doteySongKey=$this->getOtherRedisKey('doteySong');
		return $otherRedis->set($doteySongKey.$doteyId,json_encode($data));
	}
	
	/**
	 * 根据档期ID获取用户列表
	 * @param int $archivesId 档期ID
	 * @return array
	 */
	public function getUserList($archivesId){
		$otherRedis=$this->getCacheRedisConnection();
		$userList=$this->getOtherRedisKey('userList');
		$data=$otherRedis->get($userList.$archivesId);
		if(empty($data)) return array();
		return json_decode($data,true);
	}
	
	public function getUserListByArchivesIds(array $archivesIds){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('userList');
		foreach($archivesIds as $row){
			$keys[]=$key.$row;
		}
		$data=$otherRedis->mget($keys);
		$userList=array();
		if($data){
			foreach($data as $row){
				$list=json_decode($row,true);
				$userList[$list['archives_id']]=$list;
			}
		}
		return $userList;
	}
	
	/**
	 * @param int $archivesId
	 * @param array $userList
	 */
	public function saveUserList($archivesId,array $userList){
		$otherRedis=$this->getCacheRedisConnection();
		$userList=$this->getOtherRedisKey('userList');
		return $otherRedis->set($userList.$archivesId,json_encode($userList));
	}
	
	/**
	 * @param int $archivesId 档期ID
	 * @param array $archives 档期信息
	 */
	public function saveArchives($archivesId,array $archives){
		if($archivesId<=0) return false;
		if(empty($archives)) return false;
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('archives');
		return $otherRedis->set($key.$archivesId,json_encode($archives));
	}
	
	/**
	 * 获取档期信息
	 * @param array $archivesIds  档期ID
	 * @return array
	 */
	public function getArchives($archivesIds){
		if(empty($archivesIds)) return array();
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('archives');
		$archivesIds=is_array($archivesIds)?$archivesIds:array($archivesIds);
		$archivesKey=$ids=array();
		foreach($archivesIds as $row){
			$archivesKey[]=$key.$row;
		}
		$archives=$otherRedis->mget($archivesKey);
		if(empty($archives)){
			return array();
		}
		$archivesDao=array();
		foreach($archives as $key=>$data){
			$_data = json_decode($data,true);
			if(!is_array($_data)){
				continue;
			}
			$archivesDao[$archivesIds[$key]] = $_data;
		}
		return $archivesDao;
	}
	
	/**
	 * 根据主播uid存储主播的档期ID
	 * @param int $uid 主播uid
	 * @param int $subId 分站Id,默认为主站
	 * @param array $archivesIds 档期Id
	 */
	public function saveArchivesIdsByUid($uid,$subId=0,array $archivesIds){
		if($uid<=0) return array();
		if(empty($archivesIds)) return array();
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('archives_uid');
		if($subId>0){
			$rkey=$key.$subId.'_'.$uid;
		}else{
			$rkey=$key.$uid;
		}
		return $otherRedis->set($rkey,json_encode($archivesIds));
	}
	
	
	/**
	 * 根据主播uid获取档期Id
	 * @param array $uids  档期ID
	 * @param int $subId 分站Id,默认为主站
	 * @return array
	 */
	public function getArchivesIdsByUids(array $uids,$subId=0){
		if(empty($uids)) return array();
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('archives_uid');
		$uids=is_array($uids)?$uids:array($uids);
		$uidKey=array();
		foreach($uids as $row){
			if($subId>0){
				$uidKey[]=$key.$subId.'_'.$row;
			}else{
				$uidKey[]=$key.$row;
			}
		}
		$archivesIds=$otherRedis->mget($uidKey);
		
		if(empty($archivesIds)){
			return array();
		}
		$archivesDao=array();
		foreach($archivesIds as $key=>$data){
			$_data = json_decode($data,true);
			if(!is_array($_data)){
				continue;
			}
			$archivesDao[$uids[$key]] = $_data;
		}
		return $archivesDao;
	}
	
	/**
	 * 写运营数据到redis
	 * 
	 * @param array $operate
	 * @author suqian
	 * @return array
	 */
	public function setOperateToRedis(array $operate){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('operate');
		return $otherRedis->set($key,json_encode($operate));
	}
	
	/**
	 * 写客服数据到redis
	 * @param array $kefu
	 */
	public function setKefuToRedis(array $kefu){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('kefu');
		return $otherRedis->set($key,json_encode($kefu));
	}
	
	/**
	 * 从redis取得运营数据
	 * 
	 * @author suqian
	 * @return array
	 */
	public function getOperateFromRedis(){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('operate');
		$operate = $otherRedis->get($key);
		if($operate){
			return json_decode($operate,true);
		}
		return array();
	}
	
	/**
	 * 从redis取得运营数据
	 * 
	 * @author supeng
	 * @return mixed|multitype:
	 */
	public function getKefuFromRedis(){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('kefu');
		$kefu = $otherRedis->get($key);
		if($kefu){
			return json_decode($kefu,true);
		}
		return array();
	}
	
	/**
	 * 写频道相关数据到redis
	 * 
	 * @param array $operate
	 * @author suqian
	 * @return array
	 */
	public function setChannelToRedis(array $channel){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('channel');
		return $otherRedis->set($key,json_encode($channel));
	}
	
	/**
	 * 从redis取得频道数据
	 * 
	 * @author suqian
	 * @return array
	 */
	public function getChannelFromRedis(){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('channel');
		$channel = $otherRedis->get($key);
		if($channel){
			return json_decode($channel,true);
		}
		return array();
	}
	
	/**
	 * 获取用户富豪榜
	 * 
	 * @param $key 值
	 * @param $isDecode 是否json_decode
	 * @return array
	 */
	public function getUserRichRank($key , $isDecode = true){
		if(empty($key) || !in_array($key,array('user_rich_today_rank','user_rich_week_rank','user_rich_month_rank','user_rich_super_rank'))){
			return array();
		}
		$key=$this->getOtherRedisKey($key);
		$otherRedis=$this->getCacheRedisConnection();
		$rank = $otherRedis->get($key);
		if($isDecode){
			return $rank = $rank ? json_decode($rank,true) : array();
		}
		return $rank;
	}
	
	/**
	 * 获取用户情谊榜
	 * 
	 * @param $key 值
	 * @param $isDecode 是否json_decode
	 * @return array
	 */
	public function getUserFriendlyRank($key , $isDecode = true){
		if(empty($key) || !in_array($key,array('user_friendly_today_rank','user_friendly_week_rank','user_friendly_month_rank','user_friendly_super_rank'))){
			return array();
		}
		$key=$this->getOtherRedisKey($key);
		$otherRedis=$this->getCacheRedisConnection();
		$rank = $otherRedis->get($key);
		if($isDecode){
			return $rank = $rank ? json_decode($rank,true) : array();
		}
		return $rank;
	}
	/**
	 * 存储主播魅力排行榜
	 * 
	 * @param string $key
	 * @param array $doteys
	 * @return boolean
	 */
	public function setDoteyCharmRank($key,array $doteys){
		if(!in_array($key,array('dotey_charm_super_rank')) || empty($doteys)){
			return false;
		}
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey($key);
		return $otherRedis->set($key,json_encode($doteys));
	}
	/**
	 * 获取主播魅力榜榜
	 * 
	 * @param $key 值
	 * @param $isDecode 是否json_decode
	 * @return array
	 */
	public function getDoteyCharmRank($key , $isDecode = true){
		if(empty($key) || !in_array($key,array('dotey_charm_today_rank','dotey_charm_week_rank','dotey_charm_month_rank','dotey_charm_super_rank'))){
			return array();
		}
		$key=$this->getOtherRedisKey($key);
		$otherRedis=$this->getCacheRedisConnection();
		$rank = $otherRedis->get($key);
		if($isDecode){
			return $rank = $rank ? json_decode($rank,true) : array();
		}
		return $rank;
	}
	/**
	 * 获取主播礼物排行榜
	 * 
	 * @param $key 值
	 * @param $isDecode 是否json_decode
	 * @return array
	 */
	public function getDoteyGiftRank($key , $isDecode = true){
		if(empty($key) || !in_array($key,array('dotey_gift_week_rank','dotey_gift_lastweek_rank'))){
			return array();
		}
		$key=$this->getOtherRedisKey($key);
		$otherRedis=$this->getCacheRedisConnection();
		$rank = $otherRedis->get($key);
		if($isDecode){
			return $rank = $rank ? json_decode($rank,true) : array();
		}
		return $rank;
	}
	
	/**
	 * 存储主播点歌排行榜
	 * 
	 * @param string $key
	 * @param array $doteys
	 * @return boolean
	 */
	public function setDoteySongsRank($key,array $doteys){
		if(!in_array($key,array('dotey_songs_super_rank')) || empty($doteys)){
			return false;
		}
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey($key);
		return $otherRedis->set($key,json_encode($doteys));
	}
	
	/**
	 * 获取点歌排行榜
	 * 
	 * @param $key 值
	 * @param $isDecode 是否json_decode
	 * @return array
	 */
	public function getDoteySongsRank($key , $isDecode = true){
		if(empty($key) || !in_array($key,array('dotey_songs_today_rank','dotey_songs_week_rank','dotey_songs_month_rank','dotey_songs_super_rank'))){
			return array();
		}
		$key=$this->getOtherRedisKey($key);
		$otherRedis=$this->getCacheRedisConnection();
		$rank = $otherRedis->get($key);
		if($isDecode){
			return $rank = $rank ? json_decode($rank,true) : array();
		}
		return $rank;
	}
	
	/**
	 * 存储用户点歌排行榜
	 * 
	 * @param string $key
	 * @param array $user
	 * @return boolean
	 */
	public function setUserSongsRank($key,array $user){
		if(!in_array($key,array('user_songs_super_rank')) || empty($user)){
			return false;
		}
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey($key);
		return $otherRedis->set($key,json_encode($user));
	}
	
	/**
	 * 存储用户情谊排行榜
	 * 
	 * @param string $key
	 * @param array $user
	 * @return boolean
	 */
	public function setUserFriendlyRank($key,array $user){
		if(!in_array($key,array('user_friendly_super_rank')) || empty($user)){
			return false;
		}
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey($key);
		return $otherRedis->set($key,json_encode($user));
	}
	
	/**
	 * 获取用户点歌排行榜
	 * 
	 * @param $key 值
	 * @param $isDecode 是否json_decode
	 * @return array
	 */
	public function getUserSongsRank($key , $isDecode = true){
		if(empty($key) || !in_array($key,array('user_songs_today_rank','user_songs_week_rank','user_songs_month_rank','user_songs_super_rank'))){
			return array();
		}
		$key=$this->getOtherRedisKey($key);
		$otherRedis=$this->getCacheRedisConnection();
		$rank = $otherRedis->get($key);
		if($isDecode){
			return $rank = $rank ? json_decode($rank,true) : array();
		}
		return $rank;
	}
	/**
	 * 存储主播送礼排行榜
	 * 
	 * @param string $key
	 * @param array $gift
	 * @return boolean
	 */
	public function setDoteyReceiveGiftRank($key,array $gift){
		if(!in_array($key,array('dotey_gift_super_rank')) || empty($gift)){
			return false;
		}
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey($key);
		return $otherRedis->set($key,json_encode($gift));
	}
	/**
	 * 获取主播送礼排行榜
	 * 
	 * @param string $key
	 * @param boolean $isDecode
	 * @return array
	 */
	public function getDoteyReceiveGiftRank($key,$isDecode = true){
		if(!in_array($key,array('dotey_gift_super_rank'))){
			return array();
		}
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey($key);
		$rank = $otherRedis->get($key);
		if($isDecode){
			return $rank = $rank ? json_decode($rank,true) : array();
		}
		return $rank;
	}
	
	/**
	 * 存储主播粉丝排行榜
	 * 
	 * @param string $key
	 * @param array $fans
	 * @return boolean
	 */
	public function setDoteyFansRank($key,array $fans){
		if(!in_array($key,array('dotey_fans_super_rank','dotey_fans_new_rank')) || empty($fans)){
			return false;
		}
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey($key);
		return $otherRedis->set($key,json_encode($fans));
	}
	/**
	 * 获取主播粉丝排行榜
	 * 
	 * @param string $key
	 * @param boolean $isDecode
	 * @return array
	 */
	public function getDoteyFansRank($key,$isDecode = true){
		if(!in_array($key,array('dotey_fans_super_rank','dotey_fans_new_rank'))){
			return array();
		}
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey($key);
		$rank = $otherRedis->get($key);
		if($isDecode){
			return $rank = $rank ? json_decode($rank,true) : array();
		}
		return $rank;
	}
	/**
	 * 设置今日推荐主播数据
	 * @param array $todayRecommand
	 * @return boolean
	 */
	public function setDoteyTodayRecommand(array $todayRecommand){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('dotey_today_recommand');
		return $otherRedis->set($key,json_encode($todayRecommand));
	}
	/**
	 * 获取今日推荐的主播
	 * 
	 * @param string $isDecode
	 * @return array
	 */
	public function getDoteyTodayRecommand($isDecode = true){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('dotey_today_recommand');
		$todayRecommand = $otherRedis->get($key);
		if($isDecode){
			return $todayRecommand = $todayRecommand ? json_decode($todayRecommand,true) : array();
		}
		return $todayRecommand;
	}
	/**
	 * 获取直播间皇冠粉丝，本场粉丝，本周粉丝，最大贡献值礼物，礼物消息等
	 * @param int $archivesId 档期Id
	 * @param string $key key值(crown,archives_dedication,week_dedication,archives_gift,most_archives_dedication,archives_dy_msg)
	 * @return array
	 */
	public function getArchivesRelationData($archivesId,$key){
		if($archivesId<=0) return array();
		$key=$this->getOtherRedisKey($key);
		$otherRedis=$this->getCacheRedisConnection();
		$archives_key=$key.$archivesId;
		$archives=$otherRedis->get($archives_key);
		return $archives=$archives?json_decode($archives,true):array();
	}
	
	/**
	 * 保存直播间超级粉丝榜
	 * @param int $archivesId
	 * @param array $data
	 * @return array
	 */
	public function setArchivesRelationData($archivesId, array $data){
		if($archivesId<=0) return array();
		$key=$this->getOtherRedisKey('super_dedication');
		$otherRedis=$this->getCacheRedisConnection();
		$archives_key=$key.$archivesId;
		return $otherRedis->set($archives_key, json_encode($data));
	}
	
	
	/**
	 * 获取全局超过80个皮蛋的礼物
	 * @return array
	 */
	public function getGlobalGiftList(){
		$key=$this->getOtherRedisKey('most_dedication');
		$otherRedis=$this->getCacheRedisConnection();
		$giftList=$otherRedis->get($key);
		return $giftList=$giftList?json_decode($giftList,true):array();
	}
	
	
	/**
	 * 存储直播间发言设置
	 * @param int $archivesId 档期ID
	 * @param array $sets (tourist_set为游客发言设置，global_set为全局发言设置)
	 * @return boolean
	 */
	public function saveChatSet($archivesId,array $sets){
		if($archivesId<=0||!is_array($sets)) return false;
		$key=$this->getOtherRedisKey('chat_set');
		$otherRedis=$this->getCacheRedisConnection();
		$chat_set_key=$key.$archivesId;
		return $otherRedis->set($chat_set_key,json_encode($sets));
	}
	
	/**
	 * 获取直播间发言设置 (tourist_set为游客发言设置，global_set为全局发言设置)
	 * @param int $archivesId 档期ID
	 * @return array
	 */
	public function getChatSet($archivesId){
		if($archivesId<=0) return array();
		$key=$this->getOtherRedisKey('chat_set');
		$otherRedis=$this->getCacheRedisConnection();
		$chat_set_key=$key.$archivesId;
		$chatSet=$otherRedis->get($chat_set_key);
		return $chatSet=$chatSet?json_decode($chatSet,true):array();
	}
	/**
	 * 存储用户所有等级信息
	 * 
	 * @param array $allRank
	 * @return boolean
	 */
	public function setAllUserRank(array $allRank){
		if(empty($allRank)){
			return array();
		}
		$key=$this->getOtherRedisKey('user_all_rank');
		$otherRedis=$this->getCacheRedisConnection();
		return $otherRedis->set($key,json_encode($allRank));
	}
	/**
	 * 取得所有用户等级
	 * 
	 * @return array
	 */
	public function getAllUserRank(){
		$key=$this->getOtherRedisKey('user_all_rank');
		$otherRedis=$this->getCacheRedisConnection();
		$allRank = $otherRedis->get($key);
		if(empty($allRank)){
			return array();
		}
		return json_decode($allRank,true);
	}
	
	/**
	 * 存储用户所有主播等级信息
	 * 
	 * @param array $allRank
	 * @return boolean
	 */
	public function setAllDoteyRank(array $allRank){
		if(empty($allRank)){
			return array();
		}
		$key=$this->getOtherRedisKey('dotey_all_rank');
		$otherRedis=$this->getCacheRedisConnection();
		return $otherRedis->set($key,json_encode($allRank));
	}
	/**
	 * 取得所有主播等级
	 * 
	 * @return array
	 */
	public function getAllDoteyRank(){
		$key=$this->getOtherRedisKey('dotey_all_rank');
		$otherRedis=$this->getCacheRedisConnection();
		$allRank = $otherRedis->get($key);
		if(empty($allRank)){
			return array();
		}
		return json_decode($allRank,true);
	}
	
	/**
	 * 存储最后一次赠送飞屏的时间戳
	 */
	public function saveLastFlyscreenTime(){
		$key=$this->getOtherRedisKey('last_fly_screen_time');
		$otherRedis=$this->getCacheRedisConnection();
		return  $otherRedis->set($key,time());
	}
	
	/**
	 * 获取最后一次送飞屏的时间戳
	 * @return int
	 */
	public function getLastFlyscreenTime(){
		$key=$this->getOtherRedisKey('last_fly_screen_time');
		$otherRedis=$this->getCacheRedisConnection();
		$last_time=$otherRedis->get($key);
		return $last_time?$last_time:0;
	}
	
	
	/**
	 * 保存聊天敏感词
	 * @edit by guoshaobo
	 * @param array $word
	 */
	public function saveChatWord(array $word = array())
	{
		$key = $this->getOtherRedisKey('chat_word');
		$otherRedis = $this->getCacheRedisConnection();
		return $otherRedis->set($key, json_encode($word));
	}
	
	/**
	 * 获取聊天敏感词
	 * @edit by guoshaobo
	 */
	public function getChatWord()
	{
		$key = $this->getOtherRedisKey('chat_word');
		$otherRedis = $this->getCacheRedisConnection();
		return $otherRedis->get($key);
	}
	
	/**
	 * 根据uid获取主播今日魅力排行第几名
	 * @param int $uid
	 * @return int
	 */
	public function getAllDoteyCharmTodayRank($uid){
		$key = $this->getOtherRedisKey('all_dotey_charm_today_rank');
		$otherRedis = $this->getCacheRedisConnection();
		$allRank=$otherRedis->get($key);
		$allRank=$allRank?json_decode($allRank,true):array();
		return isset($allRank[$uid])?$allRank[$uid]:0;
	}
	
	/**
	 * 存储主播今日魅力排行
	 * @param array $allRank
	 */
	public function saveAllDoteyCharmTodayRank($allRank){
		$key = $this->getOtherRedisKey('all_dotey_charm_today_rank');
		$otherRedis = $this->getCacheRedisConnection();
		return $otherRedis->set($key,json_encode($allRank));
	}
	
	/**
	 * 根据uid获取主播本周魅力排行
	 * @param int $uid
	 * @return array
	 */
	public function getAllDoteyCharmWeekRank($uid){
		$key = $this->getOtherRedisKey('all_dotey_charm_week_rank');
		$otherRedis = $this->getCacheRedisConnection();
		$allRank=$otherRedis->get($key);
		$allRank=$allRank?json_decode($allRank,true):array();
		return isset($allRank[$uid])?$allRank[$uid]:0;
	}
	
	/**
	 * 存储主播本周魅力排行
	 * @param array $allRank
	 */
	public function saveAllDoteyCharmWeekRank($allRank){
		$key = $this->getOtherRedisKey('all_dotey_charm_week_rank');
		$otherRedis = $this->getCacheRedisConnection();
		return $otherRedis->set($key,json_encode($allRank));
	}
	
	
	/**
	 * 获取直播间是否允许点歌
	 * @param int $archives_id 档期Id
	 * @return int
	 */
	public function getArchivesAllowSong($archives_id){
		$key = $this->getOtherRedisKey('allow_song');
		$otherRedis = $this->getCacheRedisConnection();
		$song_set=$otherRedis->get($key.$archives_id);
		return empty($song_set)?1:$song_set;
	}
	
	/**
	 * 存储直播间点歌状态
	 * @param int $archives_id
	 * @param int $allow 1->允许 2->禁止
	 */
	public function saveArchivesAllowSong($archives_id,$allow){
		$key = $this->getOtherRedisKey('allow_song');
		$otherRedis = $this->getCacheRedisConnection();
		return $otherRedis->set($key.$archives_id,$allow);
	}
	
	/**
	 * 获取网站配置
	 * @author guoshaobo
	 */
	public function getWebSiteConfig($key)
	{
		$cacheKey = $this->getOtherRedisKey('web_site_config');
		$otherRedis = $this->getCacheRedisConnection();
		$cacheValue = $otherRedis->get($cacheKey);
		$cacheValue = $cacheValue ? json_decode($cacheValue,true):array();
		return isset($cacheValue[$key]) ? $cacheValue[$key] : false;
	}
	/**
	 * 保存网站配置
	 * @author guoshaobo
	 */
	public function saveWebSiteConfig($key, $value)
	{
		$cacheKey = $this->getOtherRedisKey('web_site_config');
		$otherRedis = $this->getCacheRedisConnection();
		$cacheValue = $otherRedis->get($cacheKey);
		$cacheValue = $cacheValue ? json_decode($cacheValue,true):array();
		$cacheValue[$key] = $value;
		return $otherRedis->set($cacheKey, json_encode($cacheValue));
	}
	
	/**
	 * @param array $keys 直播间相关的key
	 * @return array
	 */
	public function  getArchivesDataFromRedis(array $keys){
		if(empty($keys)) return array();
		$otherRedis = $this->getCacheRedisConnection();
		$values=$otherRedis->mget($keys);
		$cacheValue=array();
		foreach($values as $key=>$row){
			$cacheValue[$keys[$key]]=json_decode($row,true);
		}
		return $cacheValue;
	}
	
	/**
	 * 存储本直播间生效的贴条用户
	 * @param int $archivesId 档期Id
	 * @param array $label  直播间贴条用户
	 * @return boolean
	 */
	public function saveArchviesLabel($archivesId,array $label){
		if($archivesId<=0) return false;
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('labelList');
		return $otherRedis->set($key.$archivesId,json_encode($label));
		
	}
	
	/**
	 * 获取本直播间生效的贴条用户
	 * @param int $archivesId  档期Id
	 * @return array
	 */
	public function getArchivesLabel($archivesId){
		if($archivesId<=0) return false;
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('labelList');
		$cacheData=$otherRedis->get($key.$archivesId);
		$labelList=$cacheData?json_decode($cacheData,true):array();
		$newLabelList=array();
		if($labelList){
			foreach($labelList as $key=>$row){
				if($row['vt']>time()){
					$newLabelList[$key]=$row;
				}
			}
		}
		if(count($labelList)>count($newLabelList)){
			$this->saveArchviesLabel($archivesId, $newLabelList);
		}
		return $newLabelList;
	}
	
	
	/**
	 * 存储本直播间设置的房管
	 * @param int $archivesId 档期Id
	 * @param array $manage  直播房管
	 * @return boolean
	 */
	public function saveArchviesManage($archivesId,array $manage){
		if($archivesId<=0) return false;
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('manageList');
		return $otherRedis->set($key.$archivesId,json_encode($manage));
	
	}
	
	/**
	 * 获取本直播间的房管
	 * @param int $archivesId  档期Id
	 * @return array
	 */
	public function getArchivesManage($archivesId){
		if($archivesId<=0) return array();
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('manageList');
		$cacheData=$otherRedis->get($key.$archivesId);
		return $cacheData?json_decode($cacheData,true):array();
	}
	
	
	/**
	 * 获取本直播间的禁言用户
	 * @param int $archivesId 档期Id
	 * @return array
	 */
	public function getArchivesForbid($archivesId){
		if($archivesId<=0) return array();
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('archives_forbid');
		$cacheData=$otherRedis->get($key.$archivesId);
		return $cacheData?json_decode($cacheData,true):array();
	}
	
	
	/**
	 * 存储本直播间被禁言用户
	 * @param int $archivesId 档期Id
	 * @param array $data     禁言用户
	 * @return boolen
	 */
	public function saveArchivesForbid($archivesId,array $data){
		if($archivesId<=0) return array();
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('archives_forbid');
		return $otherRedis->set($key.$archivesId,json_encode($data));
	}
	
	/**
	 * 获取本直播间的踢出用户
	 * @param int $archivesId 档期Id
	 * @return array
	 */
	public function getArchivesKickout($archivesId){
		if($archivesId<=0) return array();
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('archives_kickout');
		$cacheData=$otherRedis->get($key.$archivesId);
		return $cacheData?json_decode($cacheData,true):array();
	}
	
	
	/**
	 * 存储本直播间被踢出用户
	 * @param int $archivesId 档期Id
	 * @param array $data     踢出用户
	 * @return boolen
	 */
	public function saveArchivesKickout($archivesId,array $data){
		if($archivesId<=0) return array();
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('archives_kickout');
		return $otherRedis->set($key.$archivesId,json_encode($data));
	}
	
	/**
	 * 获取other redis的键值
	 * @return string
	 */
	public function getOtherRedisKey($subKey = null){
		$config = Yii::getKeyConfig('redis','other');
		if(empty($config)){
			return trigger_error(Yii::t('common','{config} config is empty',array('{config}'=>'(redis other key)')),E_USER_ERROR);
		}
		return $subKey ? $config[$subKey] : $config;
	}
	
	/**
	 * 获取首页右侧导航的新秀主播推荐描述
	 * @author supeng
	 */
	public function getIndexRightDataForPookieDotey(){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('index_right_data_type_rookiedotey');
		return $otherRedis->get($key);
	}
	
	public function setIndexRightDataForPookieDotey($info){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('index_right_data_type_rookiedotey');
		return $otherRedis->set($key,$info);
	}

	/**
	 * 获取首页右侧导航的最新加入主播推荐描述
	 * @author supeng
	 */
	public function getIndexRightDataForNewDotey(){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('index_right_data_type_newdotey');
		return $otherRedis->get($key);
	}
	
	public function setIndexRightDataForNewDotey($info){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('index_right_data_type_newdotey');
		return $otherRedis->set($key,$info);
	}
	
	/**
	 * 获取首页右侧导航的明星主播版块推荐描述
	 * @author supeng
	 */
	public function getIndexRightDataForStarDotey(){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('index_right_data_type_stardotey');
		return $otherRedis->get($key);
	}
	
	public function setIndexRightDataForStarDotey($info){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('index_right_data_type_stardotey');
		return $otherRedis->set($key,$info);
	}
	
	/**
	 * 获取礼物之星活动页排行榜
	 * @author zhangzhifan
	 */
	public function getWeekGiftStarRankWeb($weekId){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('week_gift_star_rank_web');
		return json_decode($otherRedis->get($key),true);
	}
	
	public function setWeekGiftStarRankWeb($weekId,$info){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('week_gift_star_rank_web');
		return $otherRedis->set($key,json_encode($info));
	}

	public function getWeekGiftStarRankLingbox($weekId){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('week_gift_star_rank_lingbox');
		return json_decode($otherRedis->get($key),true);
	}
	
	public function setWeekGiftStarRankLingbox($weekId,$info){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('week_gift_star_rank_lingbox');
		return $otherRedis->set($key,json_encode($info));
	}
	
	/**
	 * (活动)守护天使 幸运主播列表 
	 * @author supeng
	 * @return mixed
	 */
	public function getLuckDoteyList(){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('activity_guardangel_luckdotey');
		return json_decode($otherRedis->get($key),true);
	}
	
	public function setLuckDoteyList($info,$expire=0){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('activity_guardangel_luckdotey');
		return $otherRedis->set($key,json_encode($info),$expire);
	}
	
	/**
	 * (活动)守护天使 主播排行列表 
	 * @author supeng
	 * @return mixed
	 */
	public function getGuardAngelDoteyRanking(){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('activity_guardangel_dotey_rank');
		return json_decode($otherRedis->get($key),true);
	}
	
	public function setGuardAngelDoteyRanking($info,$expire=0){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('activity_guardangel_dotey_rank');
		return $otherRedis->set($key,json_encode($info),$expire);
	}
	
	public function delGuardAngelDoteyRanking(){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('activity_guardangel_dotey_rank');
		return $otherRedis->delete($key);
	}
	
	/**
	 * (活动)守护天使 用户排行列表
	 * @author supeng
	 * @return mixed
	 */
	public function getGuardAngelUserRanking(){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('activity_guardangel_user_rank');
		return json_decode($otherRedis->get($key),true);
	}
	
	public function setGuardAngelUserRanking($info,$expire=0){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('activity_guardangel_user_rank');
		return $otherRedis->set($key,json_encode($info),$expire);
	}
	
	public function delGuardAngelUserRanking(){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('activity_guardangel_user_rank');
		return $otherRedis->delete($key);
	}
	
	/**
	 * 新手任务列表
	 * @author hexin
	 * @return mixed
	 */
	public function getTaskList(){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('task_list');
		return json_decode($otherRedis->get($key),true);
	}
	
	public function setTaskList($info,$expire=0){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('task_list');
		return $otherRedis->set($key,json_encode($info),$expire);
	}
	
	/**
	 * 获取直播间骰子游戏记录
	 * @param int $archives_id
	 * @return array
	 */
	public function getDiceRecord($archives_id){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('dice_game_record');
		return json_decode($otherRedis->get($key.$archives_id),true);
	}
	

	/**
	 * 获取手机端验证码
	 * @param string $id  随机键值
	 * @return string
	 */
	public function getPhoneCode($id){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('phone_code_list');
		return $otherRedis->get($key.$id);
	}
	
	/**
	 * 存储手机端验证码
	 * @param string $id  随机键值
	 * @param string $value  验证码值
	 * @param int $expirTime 失效时间
	 * @return boolean
	 */
	public function savePhoneCode($id,$value,$expirTime=60){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('phone_code_list');
		if($otherRedis->set($key.$id,$value)){
			return $otherRedis->expire($key.$id,$expirTime);
		}else{
			return false;
		}
	}

	/**
	 * 生日快乐活动页排行榜数据
	 * @author zhangzhifan
	 */
	public function getHappyBirthdayPageData(){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('happy_birthday_page');
		return json_decode($otherRedis->get($key),true);
	}
	
	/**
	 * 家族基本信息
	 * @author hexin
	 * @param $family_id
	 * @return mixed
	 */
	public function getFamily($family_id){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('family').$family_id;
		$family = $otherRedis->get($key);
		if($family) return json_decode($family,true);
		else return array();
	}
	
	/**
	 * 批量获取家族基本信息
	 * @param array $family_ids
	 * @return multitype:|multitype:mixed
	 */
	public function getFamilyIds(array $family_ids){
		if(empty($family_ids)) return array();
		$otherRedis = $this->getCacheRedisConnection();
		$keys = array();
		$key = $this->getOtherRedisKey('family');
		foreach($family_ids as $id){
			$keys[] = $key.$id;
		}
		$json = $otherRedis->mget($keys);
		$return = array();
		foreach($family_ids as $k => $id){
			if(isset($json[$k])) $return[$id] = json_decode($json[$k], true);
		}
		return $return;
	}
	
	/**
	 * 保存家族基本信息
	 * @param int $family_id
	 * @param array $info
	 * @param int $expire
	 * @return boolean
	 */
	public function setFamily($family_id, $info, $expire=0){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('family').$family_id;
		return $otherRedis->set($key,json_encode($info),$expire);
	}
	
	/**
	 * 删除家族
	 * @param int $family_id
	 */
	public function deleteFamily($family_id){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('family').$family_id;
		return $otherRedis->del($key);
	}
	
	/**
	 * 获取家族排行榜
	 * @param sting $type 榜单种类
	 * @param string $date 榜单时间类型
	 * @return multitype:|mixed
	 */
	public function getFamilyTop($type, $date='day'){
		if(!in_array($type, array('charm', 'dedication', 'medal', 'recharge'))) return array();
		if(!in_array($date, array('day', 'week', 'month', 'super'))) return array();
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('family').$type."_".$date;
		$top = $otherRedis->get($key);
		if($top) return json_decode($top, true);
		else return array();
	}
	
	/**
	 * 保存家族排行榜
	 * @param array $info
	 * @param sting $type 榜单种类
	 * @param string $date 榜单时间类型
	 * @param int $expire
	 * @return boolean
	 */
	public function setFamilyTop($info, $type, $date='day', $expire=0){
		if(!in_array($type, array('charm', 'dedication', 'medal', 'recharge'))) return false;
		if(!in_array($date, array('day', 'week', 'month', 'super'))) return false;
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('family').$type."_".$date;
		return $otherRedis->set($key,json_encode($info), $expire);
	}
	
	/**
	 * 
	 * @param unknown_type $info
	 */
	public function setHappyBirthdayPageData($info){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('happy_birthday_page');
		return $otherRedis->set($key,json_encode($info));
	}	

	/**
	 * 获取每日幸运星数据
	 * @return array
	 */
	public function getLuckStar(){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('luck_star');
		return json_decode($otherRedis->get($key),true);
	}
	
	/**
	 * 存储每日幸运星数据
	 * @param array $value
	 * @return boolean
	 */
	public function saveLuckStar(array $value){
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('luck_star');
		return $otherRedis->set($key,json_encode($value));
	}
	
	/**
	 * 上周唱将数据
	 * @author zhangzhifan
	 */
	public function getLastWeekStarSinger()
	{
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('last_week_star_singer');
		return json_decode($otherRedis->get($key),true);
	}
	
	
	public function setLastWeekStarSinger($info)
	{
		$otherRedis = $this->getCacheRedisConnection();
		$key = $this->getOtherRedisKey('last_week_star_singer');
		return $otherRedis->set($key,json_encode($info));
	}
	
	/**
	 * 取得跑道礼物记录
	 * @return array
	 */
	public function getTruckGiftRecord(){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('truck_gift');
		return json_decode($otherRedis->get($key),true);
	}
	
	/**
	 * 存储跑道礼物记录
	 * @param array $record  礼物记录
	 * @param int $expirTime 有效时间
	 * @return boolean
	 */
	public function saveTruckGiftRecord(array $record,$expirTime=7200){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('truck_gift');
		if($otherRedis->set($key,json_encode($record))){
			return $otherRedis->expire($key,$expirTime);
		}else{
			return false;
		}
		
	}
	
	/**
	 * 获取用户是否被限制发送骰子
	 * @param int $uid   用户uid
	 * @return boolean 0->不限制，1->受限
	 */
	public function getLastSendDiceTime($uid){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('last_send_dice_time').'_'.$uid;
		return $otherRedis->get($key)?true:false;
	}
	
	/**
	 * 存储用户发送骰子限制时间
	 * @param int $uid       用户uid
	 * @param int $expirTime 失效时间
	 * @return boolean       0->失败，1->成功 
	 */
	public function saveLastSendDiceTime($uid,$expirTime=20){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('last_send_dice_time').'_'.$uid;
		if($otherRedis->set($key,1)){
			return $otherRedis->expire($key,$expirTime);
		}
		return false;
	}
	
	/**
	 * 获取表情
	 * @return array
	 */
	public function getFace(){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('archives_face');
		return json_decode($otherRedis->get($key),true);
	}
	
	/**
	 * 存储表情
	 * @param array $face 表情
	 * @return boolean    0->失败，1->成功
	 */
	public function saveFace(array $face){
		if(empty($face)) return false;
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('archives_face');
		return $otherRedis->set($key,json_encode($face));
	}
	
	/**
	 * 获取全站广播
	 * @return array
	 */
	public function getFullSiteBroadcast(){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('full_site_broadcast');
		return json_decode($otherRedis->get($key),true);
	}
	
	/**
	 * 存储用户发出的全站广播
	 * @param array $content 广播内容
	 * @return boolean    0->失败，1->成功
	 */
	public function saveFullSiteBroadcast(array $content){
		if(empty($content)) return false;
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('full_site_broadcast');
		return $otherRedis->set($key,json_encode($content));
	}
	
	/**
	 * 存储代理销量榜
	 * @param array $content 代理销量榜
	 * @return boolean    0->失败，1->成功
	 */
	public function setAgentSalesTop(array $agentSalesTop){
		if(empty($agentSalesTop)) return false;
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('agent_sales_top');
		return $otherRedis->set($key,json_encode($agentSalesTop));
	}
	
	/**
	 * 获取代理销量榜
	 * @return array
	 */
	public function getAgentSalesTop()
	{
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('agent_sales_top');
		return json_decode($otherRedis->get($key),true);
	}
	
	/**
	 * 获取在线总人数
	 * @return mixed
	 */
	public function getOnlieCount(){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('all_total_online');
		return json_decode($otherRedis->get($key),true);
	}
	
	/**
	 * 皇冠主播、蓝钻主播、红心主播等级人数
	 * @param unknown_type $count
	 * @return boolean
	 */
	public function setDoteyRankCount($count){
		if(empty($count)) return false;
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('dotey_rank_count');
		return $otherRedis->set($key, json_encode($count));
	}
	
	public function getDoteyRankCount(){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('dotey_rank_count');
		return json_decode($otherRedis->get($key),true);
	}
	
	/**
	 * 印象标签主播列表
	 * @param int $tag_id
	 * @param array $uids
	 * @return boolean
	 */
	public function setTagDotey($tag_id, array $uids){
		if(empty($uids)) return false;
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('tag_dotey_uids').$tag_id;
		return $otherRedis->set($key, json_encode($uids));
	}
	
	public function getTagDotey($tag_id){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('tag_dotey_uids').$tag_id;
		return json_decode($otherRedis->get($key),true);
	}
	
	/**
	 * 获取档期停车位列表
	 * @return array
	 */
	public function getArchivesParkingList($archives_id)
	{
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('archives_parking').$archives_id;
		return json_decode($otherRedis->get($key),true);
	}
	
	/**
	 * 首页新秀主播
	 * @return mixed
	 */
	public function getBlueDotey(){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('recommend_blue_dotey');
		return json_decode($otherRedis->get($key),true);
	}
	
	/**
	 * 首页最新加入
	 * @return mixed
	 */
	public function getRedDotey(){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('recommend_red_dotey');
		return json_decode($otherRedis->get($key),true);
	}
	
	/**
	 * 女神争夺战对战结果
	 * @param int $tag_id
	 * @param array $uids
	 * @return boolean
	 */
	public function setBattle($battle, $data){
		if(empty($data)) return false;
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('battle_'.$battle);
		return $otherRedis->set($key, json_encode($data));
	}
	
	public function getBattle($battle){
		$otherRedis=$this->getCacheRedisConnection();
		$key=$this->getOtherRedisKey('battle_'.$battle);
		return json_decode($otherRedis->get($key),true);
	}
}

?>