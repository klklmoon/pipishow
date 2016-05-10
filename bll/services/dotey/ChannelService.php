<?php

define('CHANNEL_THEME','主题分类');
define('CHANNEL_THEME_SONG','点唱专区');
define('CHANNEL_AREA','地区分类');
define('CHANNEL_AREA_NORTH','北方');
define('CHANNEL_AREA_SOUTH','南方');
define('CHANNEL_AREA_JZH','江浙沪');
/**
 * 频道服务层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: ChannelService.php 17254 2014-01-06 11:29:44Z hexin $ 
 * @package
 */
class ChannelService extends PipiService {
	/**
	 * 
	 * @var ChannelDoteySortService
	 */
	protected static $channelDoteySortService = null;
	/**
	 * 
	 * @var OtherRedisModel
	 */
	protected static $cacheRedisModel = null;
	
	public function __construct(PipiController $pipiController = null){
		parent::__construct($pipiController);
		if(self::$channelDoteySortService == null){
			self::$channelDoteySortService = new ChannelDoteySortService();
		}
	}
	
	/**
	 * 存储主频道，新首页上线后该方法废弃
	 * 
	 * @param array $channel
	 * @return int
	 */
	public function saveChannel(array $channel){
		if(isset($channel['channel_id']) && $channel['channel_id'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$channelModel =  new ChannelModel();
		if(isset($channel['channel_id'])){
			$orgChannelModel = $channelModel->findByPk($channel['channel_id']);
			if(empty($orgChannelModel)){
				return $this->setNotice('channel',Yii::t('channel','The channel does not exist'),0);
			}
			$this->attachAttribute($orgChannelModel,$channel);
			if(!$orgChannelModel->validate()){
				return $this->setNotices($orgChannelModel->getErrors(),0);
			}
			$orgChannelModel->save();
			$insertId = $channel['channel_id'];
		}else{
			$this->attachAttribute($channelModel,$channel);
			if(!$channelModel->validate()){
				return $this->setNotices($channelModel->getErrors(),0);
			}
			$channelModel->save();
			$insertId = $channelModel->getPrimaryKey();
		}
		$cacheRedisModel = $this->getCacheRedisModel();
		$cacheRedisModel->setChannelToRedis($this->getAllChannel());
		if($insertId && $this->isAdminAccessCtl()){
			if(isset($channel['channel_id'])){
				$op_desc = '编辑 父频道('.$insertId.')';
			}else{
				$op_desc = '添加 父频道('.$insertId.')';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $insertId;
	}
	/**
	 * 存储子频道，新首页上线后该方法废弃
	 * 
	 * @param array $subChannel
	 * @return array
	 */
	public function saveSubChannel(array $subChannel){
		if(isset($subChannel['sub_channel_id'])){
			$subChannelId = intval($subChannel['sub_channel_id']);
			if($subChannelId != 1 && !preg_match('/^\d+$/',log($subChannelId,2))){
				return $this->setError(Yii::t('common','Parameter is empty'),0);
			}
		}
		$channelSubModel =  new ChannelSubModel();
		if(isset($subChannel['sub_channel_id'])){
			$orgSubChannelModel = $channelSubModel->findByPk($subChannel['sub_channel_id']);
			if(empty($orgSubChannelModel)){
				return $this->setNotice('channel',Yii::t('channel','The sub channel does not exist'),0);
			}
			$this->appendChannelSubData($orgSubChannelModel,$subChannel);
			$this->attachAttribute($orgSubChannelModel,$subChannel);
			if(!$orgSubChannelModel->validate()){
				return $this->setNotices($orgSubChannelModel->getErrors(),0);
			}
			$orgSubChannelModel->save();
			$insertId = $subChannel['sub_channel_id'];
		}else{
			$channelSubModel->setPrimaryKey($this->getNextSubChannelPrimaryId());
			$this->attachAttribute($channelSubModel,$subChannel);
			if(!$channelSubModel->validate()){
				return $this->setNotices($channelSubModel->getErrors(),0);
			}
			$channelSubModel->save();
			$insertId = $channelSubModel->getPrimaryKey();
		}
		$cacheRedisModel = $this->getCacheRedisModel();
		$cacheRedisModel->setChannelToRedis($this->getAllChannel());
		if($insertId && $this->isAdminAccessCtl()){
			if(isset($subChannel['sub_channel_id'])){
				$op_desc = '编辑 子频道('.$insertId.')';
			}else{
				$op_desc = '添加 子频道('.$insertId.')';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $insertId;
	}
	
	/**
	 * 存储地区频道与分类关联
	 * 
	 * @param array $area
	 * @param array $channel
	 * @return int
	 */
	public function saveAreaChannel(array $area,array $channel){
		if(empty($area) || empty($channel)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$multiData = array();
		$i = 0;
		foreach ($channel as $_channel){
			foreach($area as $key=>$province){
				if(!is_array($province)){
					return $this->setError(Yii::t('common','Parameter is empty'),0);
				}
				foreach($province as $city){
					$multiData[$i]['area_channel_id'] = $_channel;
					$multiData[$i]['province'] = $key;
					$multiData[$i]['city'] = $city;
					$i++;
				}
				
			}
		}
		
		if(empty($multiData)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$areaChannelModel = new AreaChannelModel();
		$flag = $areaChannelModel->batchInsert($multiData,false);
		if ($flag && $this->isAdminAccessCtl()){
			$this->saveAdminOpLog('存储地区与频道的关系');
		}
		return $flag;
	}
	/**
	 * 存储主播和频道的关系
	 * 
	 * @param array $doteys 主播ID 如 $doteys=>array(24141,321141);
	 * @param array $channel 频道ID  如 $channel => array(channel_id=>array(sub_channel_id=>1,target_relation_id=>));
	 * @return int
	 */
	public function saveDoteyChannel(array $doteys,array $channel){
		if(empty($doteys) || empty($channel)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$multiData = array();
		$i = 0;
		foreach($doteys as $doteyId){
			foreach($channel as $channelId=>$subChannel){
					$multiData[$i]['uid'] = $doteyId;
					$multiData[$i]['channel_id'] = $channelId;
					$multiData[$i]['sub_channel_id'] =$subChannel['sub_channel_id'];
					$multiData[$i]['target_relation_id'] =$subChannel['target_relation_id'];
					$i++;
			}
		}
		if(empty($multiData)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$doteyChannelModel = new DoteyChannelModel();
		$flag = $doteyChannelModel->batchInsert($multiData,false);
		if($flag && $this->isAdminAccessCtl()){
			$this->saveAdminOpLog('存储主播和频道的关系');
		}
		return $flag;
	}
	
	/**
	 * 取得下一个按位计算的子频道ID，新首页上线后该方法废弃
	 * @return int
	 */
	public function getNextSubChannelPrimaryId(){
		$channelSubModel =  new ChannelSubModel();
		return $channelSubModel->getNextSubChannelPrimaryId();
	}
	
	/**
	 * 取所有的缓存频道信息，新首页上线后该方法废弃
	 * @return array
	 */
	public function getAllChannelFromCache(){
		$cacheRedisModel = $this->getCacheRedisModel();
		$allChannel = $cacheRedisModel->getChannelFromRedis();
		if(empty($allChannel)){
			$allChannel = $this->getAllChannel();
			$cacheRedisModel->setChannelToRedis($allChannel);
		}
		return $allChannel;
	}
	
	/**
	 * 取得所有的频道，新首页上线后该方法废弃
	 */
	public function getAllChannel(Array $condition = array()){
		$channelSubModel =  ChannelSubModel::model();
		$allChannel = $channelSubModel->getChannelsAllInfoByCondition($condition);
		return $this->buildChannel($allChannel);
	}
	
	/**
	 * 取指定分类下所有的缓存频道数据，没有在从数据库取，新首页上线后该方法废弃
	 * 
	 * @param int $channelId
	 * @param int $subChannelId
	 * @return array
	 */
	public function getChanneFromCachelByIds($channelId,$subChannelId = null){
		$cacheRedisModel = $this->getCacheRedisModel();
		$allChannel = $cacheRedisModel->getChannelFromRedis();
		if(empty($allChannel)){
			return $this->getChannelByCateId($channelId,$subChannelId);
		}
		if(isset($allChannel[$channelId])){
			return isset($allChannel[$channelId][$subChannelId]) ? $allChannel[$channelId][$subChannelId] : $allChannel[$channelId];
		}
		return array();
		
	}
	
	/**
	 * 取指定分类下所有的缓存频道数据，没有在从数据库取，新首页上线后该方法废弃
	 * 
	 * @param int $channelName
	 * @param int $subChannelName
	 * @return array
	 */
	public function getChanneFromCachelByNames($channelName,$subChannelName = null){
		$allChannel = $this->reverseAllChannelByNames();
		if(empty($allChannel)){
			return $this->getChannelByCateName($channelName,$subChannelName);
		}
		if(isset($allChannel[$channelName])){
			return isset($allChannel[$channelName][$subChannelName]) ? $allChannel[$channelName][$subChannelName] : $allChannel[$channelName];
		}
		return array();
		
	}
	/**
	 * 根据频道分类名找到频道分类ID，新首页上线后该方法废弃
	 * 
	 * @param array $condition 条件  如果条件为name类型,array(分类频道名称,子频道名称[可为空]);如果条件为id类类型(分类频道ID，子频道ID)。子频道可为空
	 * @param string $conditionType 为id或者name
	 * @param array $return 返回频道信息
	 * @return array
	 */
	public function getChannelIdByChannelName(array $condition,$conditionType,array &$return = array()){
		if(empty($condition) || !in_array($conditionType,array('name','id'))){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		list($channel,$subChannel) = $condition;
		if($conditionType == 'name'){
			$data = $this->getChanneFromCachelByNames($channel,$subChannel);
			if(empty($data)){
				return array();
			}
			if($subChannel == null){
				$data = array_shift($data);
				$channel =  $data['channel_id'];
			}else{
				$channel = $data['channel_id'];
				$subChannel = $data['sub_channel_id'];
			}
			$return = $data;
		}
		return array($channel,$subChannel);
	}
	/**
	 * 取得唱区的主播
	 * @param array $return 返回频道信息
	 * @return array
	 */
	public function getDoteysOfSong(array &$return = array()){
		$condition = array(CHANNEL_THEME,CHANNEL_THEME_SONG);
		list($channel,$subChannel) = $this->getChannelIdByChannelName($condition,'name',$return);
		$doteyChannelModel =  DoteyChannelModel::model();
		$data = $doteyChannelModel->getDoteysOfSong($channel,$subChannel);
	    return $this->arToArray($data);
	}
	/**
	 * 取得主播所属频道，新首页上线后该方法废弃
	 * 
	 * @param array $uids 主播ID
	 * @param string $channel 频道
	 * @param string $subChannel 子频道
	 * @return array
	 */
	public function getChannelDoteyByUids(array $uids ,$channel = null ,$subChannel = null){
		if(empty($uids) || ($channel && $channel <= 0) || ($subChannel && $subChannel <= 0)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$doteyChannelModel =  DoteyChannelModel::model();
		$models = $doteyChannelModel->getChannelDoteyByUids($uids,$channel,$subChannel);
		return $this->arToArray($models);
	}
	/**
	 * 取得专题频道与主播关系 ，新首页上线后该方法废弃
	 * 	取得分页list及总数
	 * @author supeng
	 */
	public function getChannelThemeByConditions(array $conditions,$offset = 0,$pageSize=10,$isLimit=true){
		if (empty($conditions)){
			return array();
		}
		
		if (!empty($conditions['username'])){
			$doteySer = new DoteyService();
			if($uids = $doteySer->searchDoteyUidsByCodition(array('username'=>$conditions['username']))){
				if(!$conditions['uid']){
					$conditions['uid'] = $uids;
				}else{
					$conditions['uid'] = array_intersect($uids, array($conditions['uid']));
					if(!$conditions['uid']){
						return array();
					}
				}
			}else{
				return array();
			}
		}
		$doteyChannelModel =  DoteyChannelModel::model();
		$data = $doteyChannelModel->getDoteySongByConditions($conditions,$offset,$pageSize,$isLimit);
		return $data;
	}
	
	/**
	 * 取得唱区的主播
	 * 
	 * @param int $loginUid 登录的相关用户ID
	 * @param boolean $ifHasDotey  是否同时获取主播信息
	 * @param boolean $ifHasAttention 是否获取关注的信息
	 * @param int $sortMethod 排序方式
	 * @return int $return 0表示返回所有 1表示返回正在直播的 2表示返回待直播的
	 * @return array
	 */
	public function getDoteyArchivesOfSong($loginUid = null, $ifHasDotey = false,$ifHasAttention = false,$sortMethod = null,$return = 0){
		$channel = array();
		$doteys = $this->getDoteysOfSong($channel);
		if(empty($doteys) ||  empty($channel)){
			return array();
		}
		$uids = array_keys($this->buildDataByIndex($doteys,'uid'));
	
		$archivesService = new ArchivesService();
		$archives = $archivesService->getArchivesByUids($uids,true);
		self::$channelDoteySortService->filterArchives($archives,$return);
		self::$channelDoteySortService->buildLiveArchives($archives,$loginUid,$return,$ifHasDotey,$ifHasAttention);
		if(is_null($sortMethod)){
			$sortMethod = $channel['dotey_sort'] ;
		}
		return self::$channelDoteySortService->sortLiveArchives($archives,$sortMethod,$return);
	}
	/**
	 * 反转所有频道信息 按名称排列，新首页上线后该方法废弃
	 * 
	 * @return array
	 */
	public function reverseAllChannelByNames(){
		$allChannels = $this->getAllChannelFromCache();
		$channels = array();
		foreach($allChannels as $channelId=>$channel){
			foreach($channel as $_channel){
				$cateChannel = $_channel['channel_name'];
				$sub_channel = $_channel['sub_name'];
				$channels[$cateChannel][$sub_channel] = $_channel;
			}
		}
		return $channels;
	}
	/**
	 * 获取指定的分类的的频道，新首页上线后该方法废弃
	 * 
	 * @param int $channelId 频道名称ID
	 * @param int $subChannelId 子频道名称ID
	 * @return array
	 */
	public function getChannelByCateId($channelId,$subChannelId = null){
		$condition['channel_id'] = $channelId;
		if($subChannelId){
			$condition['sub_channel_id'] = $subChannelId;
		}
		$channelModel = ChannelSubModel::model();
		$channels = $channelModel->getChannelsAllInfoByCondition($condition);
		$channels = $this->buildChannel($channels);
		if(isset($channels[$channelId])){
			return isset($channels[$channelId][$subChannelId]) ? $channels[$channelId][$subChannelId] : $channels[$channelId];
		}
		return array();
	}
	
	/**
	 * 获取指定的分类的的频道，新首页上线后该方法废弃
	 * 
	 * @param int $channelName 频道名称
	 * @param int $subChannelName 子频道名称
	 * @return array
	 */
	public function getChannelByCateName($channelName,$subChannelName = null){
		$condition['channel_name'] = $channelName;
		if($subChannelName){
			$condition['sub_name'] = $subChannelName;
		}
		$channelModel = ChannelSubModel::model();
		$channels = $channelModel->getChannelsAllInfoByCondition($condition);
		$channels = $this->buildChannel($channels,false);
		if(isset($channels[$channelName])){
			return isset($channels[$channelName][$subChannelName]) ? $channels[$channelName][$subChannelName] : $channels[$channelName];
		}
		return array();
	}
	/**
	 * 主播排序类型
	 * 
	 * @param $sort
	 * @return mixed
	 */
	public function getDoteySortList($sort = NULL){
		return self::$channelDoteySortService->getDoteySortList($sort);
	}
	
	/**
	 * 删除子频道信息，新首页上线后该方法废弃
	 * 
	 * @param array $ids
	 * @return int
	 */
	public function delSubChannelByIds(array $ids){
		if(empty($ids)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$subChannelModel = new ChannelSubModel();
		$flag = $subChannelModel->delSubChannelByIds($ids);
		$cacheRedisModel = $this->getCacheRedisModel();
		$cacheRedisModel->setChannelToRedis($this->getAllChannel());
		if($flag && $this->isAdminAccessCtl()){
			$this->saveAdminOpLog('删除 子频道信息('.implode(',', $ids).')');
		}
		return $flag;
	}
	
	/**
	 * 根据条件删除频道地区关联信息，新首页上线后该方法废弃
	 * @param array $condition
	 */
	public function delChannelAreaRel($condition = array()){
		if(empty($condition)){
			return $this->setError(Yii::t('common','Parameter is Error'),0);
		}
		
		$areaChannelModel = new AreaChannelModel();
		$flag = $areaChannelModel->delChannelAreaRel($condition);
		if($flag && $this->isAdminAccessCtl()){
			$this->saveAdminOpLog('删除 频道与地区关联数据');
		}
		return $flag;
	}
	
	public function delDoteyChannelRel($uid,$channel_id,$sub_channel_id,$target_relation_id = 0){
		$doteyChannelModel = new DoteyChannelModel();
		$flag = $doteyChannelModel->delDoteyChannelRel($uid, $channel_id, $sub_channel_id,$target_relation_id);
		if($flag && $this->isAdminAccessCtl()){
			$this->saveAdminOpLog('删除主播与频道的关系(UID:'.$uid.'-channel_id:'.$channel_id.'-sub_channel:'.$sub_channel_id.')');
		}
		return $flag;
	}
	
	/**
	 * 删除子频道信息，新首页上线后该方法废弃
	 * 
	 * @param array $ids
	 * @return int
	 */
	public function delChannelByIds(array $ids){
		if(empty($ids)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$channelModel = new ChannelModel();
		if( $rows = $channelModel->delChannelByChannelIds($ids)){
			$subChannelModel = new ChannelSubModel();
			$subChannelModel->delSubChannelByChannelIds($ids);
			
			$cacheRedisModel = $this->getCacheRedisModel();
			$cacheRedisModel->setChannelToRedis($this->getAllChannel());
			if($this->isAdminAccessCtl()){
				$this->saveAdminOpLog('删除 父频道信息(channel_ids='.implode(',', $ids).')');
			}
		}
		return true;
	}
	
	/**
	 * 获取父频道信息，新首页上线后该方法废弃
	 * 
	 * @author supeng
	 * @param id $channel_id
	 * @return array
	 */
	public function getAllParentChannel($channel_id = '',$channel_name = ''){
		$channelModel = new ChannelModel();
		$channel = $this->arToArray($channelModel->getAllParentChannel($channel_id,$channel_name));
		return $this->buildDataByIndex($channel, 'channel_id');
	}
	
	/**
	 * @return multitype:
	 */
	public function getAllowChannelArea(){
		return array( CHANNEL_AREA  );
	}
	
	/**
	 * 获取所有地区频道
	 * 
	 * @author supeng
	 * @param array $condition
	 */
	public function getAllAreaChannel($condition = array()){
		$areaChannelModel = new AreaChannelModel();
		return $areaChannelModel->getAllAreaChannel($condition);
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $channelId
	 * @param unknown_type $province
	 * @param unknown_type $city
	 * @return mixed
	 */
	public function getAreaChannelGroups($channelId,$province,$city){
		$areaChannelModel = new AreaChannelModel();
		return $areaChannelModel->getAreaChannelGroups($channelId,$province,$city);
	}
	
	/**
	 * 根据城市和读取获取频道信息，新首页上线后该方法废弃
	 * @author hexin
	 * @param string $province
	 * @param string $city
	 * @return int
	 */
	public function getChannelByArea($province, $city){
		$area = AreaChannelModel::model()->getAllAreaChannel(array('province' => $province, 'city' => $city));
		if(empty($area)) return 0;
		$sub_channel = 0;
		foreach($area as $a){
			$sub_channel |= $a['sub_channel_id'];
		}
		return $sub_channel;
	}
	
	/**
	 * 获取所有频道分类配置，新首页上线后该方法废弃
	 * 
	 * @author supeng
	 * @return multitype:multitype:string  
	 */
	public function getChannelCateConfig($channel = null){
		$config = array(
				CHANNEL_THEME => array(
						CHANNEL_THEME_SONG => CHANNEL_THEME_SONG,
					),
				CHANNEL_AREA => array(
						CHANNEL_AREA_JZH => CHANNEL_AREA_JZH,
						CHANNEL_AREA_NORTH => CHANNEL_AREA_NORTH,
						CHANNEL_AREA_SOUTH => CHANNEL_AREA_SOUTH,
					)
			);	
		if ($channel) {
			$config = isset($config[$channel])?$config[$channel]:array();
		}
		return $config;
	}
	
	/**
	 * 重建资料，新首页上线后该方法废弃
	 * 
	 * @param $operate 数据
	 * @param boolean $isId 是否按ID重建数据
	 * @return array
	 */
	protected function buildChannel(array $channel = array(),$isId = true){
		if(empty($channel)){
			return array();
		}
		$_channels = array();
		foreach($channel as $key=>$_channel){
			if($isId){
				$cateChannel = $_channel['channel_id'];
				$sub_channel = $_channel['sub_channel_id'];
			}else{
				$cateChannel = $_channel['channel_name'];
				$sub_channel = $_channel['sub_name'];
			}
			$_channels[$cateChannel][$sub_channel] = $_channel;
		}
		return $_channels;
	}
	
	protected function getCacheRedisModel(){
		if(self::$cacheRedisModel == null){
			self::$cacheRedisModel = new OtherRedisModel();
		}
		return self::$cacheRedisModel;
	}
	/**
	 * 处理更新的子频道数据，新首页上线后该方法废弃
	 * 
	 * @param array $chanelSubModel 数据库已存在的记录
	 * @param array $newArray 新更新的记录
	 * @return array
	 */
	private function appendChannelSubData(ChannelSubModel  $chanelSubModel,array &$newArray){
		if(isset($newArray['dotey_num'])){
			if($newArray['dotey_num'] <= 0){
				$newArray['dotey_num'] = $chanelSubModel->dotey_num - abs($newArray['dotey_num']);
			}else{
				$newArray['dotey_num'] = $chanelSubModel->dotey_num + $newArray['dotey_num'];
			}
		}
	}
	
	
	
}

?>