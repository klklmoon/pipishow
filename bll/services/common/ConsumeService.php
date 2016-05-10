<?php
/**
 * 皮蛋操作服务层，处理事务，并发的问题
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: ConsumeService.php 16951 2013-12-18 01:37:32Z leiwei $ 
 * @package service
 * @subpackage common
 */

define('SOURCE_GIFTS','gifts');
define('SOURCE_USERGIFTS','userGifts');
define('SOURCE_PROPS','props');
define('SOURCE_SONGS','songs');
define('SOURCE_SENDS','sends');
define('SOURCE_EXCHANGE','exchange');
define('SOURCE_RECHARGE','recharge');
define('SOURCE_ACTIVITY','activity');
define('SOURCE_GAMES','games');
define('SOURCE_TASKS', 'tasks');
define('SOURCE_FAMILY', 'family');

define('SUBSOURCE_GIFTS_BUY','buyGifts');
define('SUBSOURCE_LUCK_GIFTS_BUY','buyLuckGifts');
define('SUBSOURCE_GIFTS_BAG','bagGifts');
define('SUBSOURCE_LUCK_GIFTS_BAG','bagLuckGifts');
define('SUBSOURCE_SENDS_ADMIN','admin');
define('SUBSOURCE_SENDS_ACTIVITY','activity');
define('SUBSOURCE_PROPS_VIP','vip');
define('SUBSOURCE_PROPS_CAR','car');
define('SUBSOURCE_PROPS_LABEL','label');
define('SUBSOURCE_PROPS_REMOVELABEL','remove_label');
define('SUBSOURCE_PROPS_FLYSCREEN','flyscreen');
define('SUBSOURCE_PROPS_GUARDIAN','guardian');
define('SUBSOURCE_PROPS_MONTHCARD','monthcard');
define('SUBSOURCE_PROPS_DICE','dice');
define('SUBSOURCE_PROPS_NUMBER','number');
define('SUBSOURCE_EXCHANGE_MONEY','money');
define('SUBSOURCE_EXCHANGE_EGG','exchange_egg');
define('SUBSOURCE_RECHARGE_ADDPIPIEGG','addpipiegg');
define('SUBSOURCE_ACTIVITY_YIRUITE','yiruite');
define('SUBSOURCE_ACTIVITY_GIFTSTAR','GiftStar');
define('SUBSOURCE_ACTIVITY_HAPPYBIRTHDAY','HappyBirthday');
define('SUBSOURCE_ACTIVITY_HALLOWEEN','Halloween');
define('SUBSOURCE_ACTIVITY_2YEARS','2years');
define('SUBSOURCE_GAMES_OPENBOX','OpenBox');
define('SUBSOURCE_GAMES_BREAKGOLDEGG','BreakGoldegg');
define('SUBSOURCE_GAMES_SCRATCHCARD','ScratchCard');
define('SUBSOURCE_GAMES_SOFASITDOWN','SofaSitdown');
define('SUBSOURCE_SONGS_DEMANDSONG','demandSong');
define('SUBSOURCE_TASKS_TASK', 'task');
define('SUBSOURCE_FAMILY_CREATE', 'create');
define('SUBSOURCE_FAMILY_MEDAL', 'medal');
define('SUBSOURCE_FAMILY_MEDAL_UPDATE', 'medalUpdate');
define('SUBSOURCE_FAMILY_CREATE_RETURN', 'createReturn');
define('SUBSOURCE_LUCK_GIFT_AWARD','luckGifts');
define('SUBSOURCE_LUCK_STAR','luckStar');              //每日幸运星奖励

define('CLIENT_ARCHIVES',0);	#档期
define('CLIENT_ACTIVITES',1);	#活动
define('CLIENT_ADMIN',2);		#后台
define('CLIENT_SHOP',3);		#商城
define('CLIENT_RECHARGE',4);	#充值
define('CLIENT_EXCHANGE',5);	#兑换
define('CLIENT_FAMILY', 6);		#家族
define('CLIENT_MOBILE', 7);		#移动端

define('AWARD_TYPE_CHARM',0);		#魅力值奖励
define('AWARD_TYPE_CHARMPOINTS',1);	#魅力点奖励
define('AWARD_TYPE_CASH',2);		#现金奖励


define('EXCHANGE_EGGPOINT',0);
define('EXCHANGE_CHARMPOINT',1);
define('EXCHANGE_MONEY',2);
define('EXCHANGE_ADMIN',3);
define('EXCHANGE_ART',4);

define('GIVEAWAY_TYPE_GIFT',1);			#赠送礼物
define('GIVEAWAY_TYPE_PROPS', 2);		#赠送道具
define('GIVEAWAY_TYPE_CHARM',3);		#赠送魅力值
define('GIVEAWAY_TYPE_CHARMPOINTS', 4);	#赠送魅力点
define('GIVEAWAY_TYPE_DEDICATION', 5);	#赠送贡献值
define('GIVEAWAY_TYPE_PIPIEGGS', 6);	#赠送皮蛋


define('TRANS_CHARMPOINTS_TO_PIPIEGGS',50);#魅力点转换成皮点的转换率 50:1

class ConsumeService extends PipiService{
	
	/**
	 * 添加皮蛋事务
	 * 
	 * @param int $uid 用户ID
	 * @param float $eggs 皮蛋数量
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function addEggs($uid, $eggs) {
		if($uid <= 0 || $eggs <= 0){
			return 0;
		}
		return ConsumeModel::model()->addEggs($uid, $eggs);
	}
	
	/**
	 * 添加担保消费的冻结皮蛋事务
	 * 
	 * @param int $uid 用户ID
	 * @param float $eggs 皮蛋数量
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function addFreezeEggs($uid, $eggs){
		if($uid <= 0 || $eggs <= 0){
			return 0;
		}
		return ConsumeModel::model()->addFreezeEggs($uid, $eggs);
	}
	
	/**
	 * 撤销添加担保消费的冻结皮蛋事务
	 *
	 * @param int $uid 用户ID
	 * @param float $eggs 皮蛋数量
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function unAddFreezeEggs($uid, $eggs){
		if($uid <= 0 || $eggs <= 0){
			return 0;
		}
		return ConsumeModel::model()->unAddFreezeEggs($uid, $eggs);
	}
	
	/**
	 * 消费皮蛋事务
	 * 
	 * @param int $uid 用户ID
	 * @param float $eggs 皮蛋数量
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function consumeEggs($uid, $eggs){
		if($uid <= 0 || $eggs <= 0){
			return 0;
		}
		return ConsumeModel::model()->consumeEggs($uid, $eggs);
	}
	
	/**
	 * 冻结皮蛋事务
	 * 
	 * @param int $uid 用户ID
	 * @param float $eggs 皮蛋数量
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function freezeEggs($uid, $eggs){
		if($uid <= 0 || $eggs <= 0){
			return 0;
		}
		return ConsumeModel::model()->freezeEggs($uid, $eggs);
	}
	
	/**
	 * 释放冻结皮蛋事务
	 * 
	 * @param int $uid 用户ID
	 * @param float $eggs 皮蛋数量
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function unFreezeEggs($uid, $eggs){
		if($uid <= 0 || $eggs <= 0){
			return 0;
		}
		return ConsumeModel::model()->unFreezeEggs($uid, $eggs);
	}
	
	/**
	 * 主播确认演唱已点歌记录事物
	 * 
	 * @param int $recordId 点歌记录id
	 * return int 执行结果，1为成功，0为失败
	 */
	public function actSong($recordId){
		if($recordId<=0){
			return 0;
		}
		return ConsumeModel::model()->actSong($recordId);
	}
	
	/**
	 * 主播取消点歌给用户返还皮蛋事物
	 * 
	 * @param int $recordId  点歌记录id
	 * @param int $uid       用户Id
	 * @param float $eggs    返还皮蛋数
	 * @return int 执行结果，1为成功，0为失败
	 */
	public function cancelSongReturnEggs($recordId,$uid,$eggs){
		if($recordId<=0||$uid <= 0 || $eggs <= 0){
			return 0;
		}
		return ConsumeModel::model()->cancelSongReturnEggs($recordId,$uid,$eggs);
	}
	
	/**
	 * 存储用户消费属性，如果没有，新增，如果有，只做在原始数据上追加数据。
	 * 在用户用户消费表的原始数据上做加操作。不做减操作，涉及到消费表的减操作和皮蛋相关的，由事务完成
	 * 
	 * @param array $consumeAttriute 消费属性
	 * @param array $newAttribute 最新数据
	 * @return boolean
	 */
	public function saveUserConsumeAttribute(array $consumeAttriute,array &$newAttribute = array()){
		if(($uid=$consumeAttriute['uid']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		$userConsumeModel = new ConsumeModel();
		$_userConsumeModel = $userConsumeModel->findByPk($uid);
		if(empty($_userConsumeModel )){
			if(isset($consumeAttriute['archives_id'])){
				unset($consumeAttriute['archives_id']);
			}
			$this->attachAttribute($userConsumeModel,$consumeAttriute);
			$userConsumeModel->save();
			$newAttribute = $userConsumeModel->attributes;
		}else{
			$counters = $jsonData = array();
			$jsonData['time'] = time();
			if(isset($consumeAttriute['charm'])){
				$counters['charm'] = $consumeAttriute['charm'];
				$jsonData['charm'] = $consumeAttriute['charm'];
			}
			
			if(isset($consumeAttriute['dedication'])){
				$counters['dedication'] = $consumeAttriute['dedication'];
				$jsonData['dedication'] = $consumeAttriute['dedication'];
			}
			
			if(isset($consumeAttriute['egg_points'])){
				$counters['egg_points'] = $consumeAttriute['egg_points'];
				$jsonData['egg_points'] = $consumeAttriute['dedication'];
			}
			
			if(isset($consumeAttriute['charm_points'])){
				$counters['charm_points'] = $consumeAttriute['charm_points'];
				$jsonData['charm_points'] = $consumeAttriute['charm_points'];
			}
			
			if($counters){
				//捕获并发时事务死锁异常
				try{
					$userConsumeModel->updateAttributeByUid($uid,$counters);
				}catch(Exception $e){
					
					$zmq = $this->getZmq();
					$zmqData['type'] ='update_user_attribute';
					$zmqData['uid'] = $uid;
					$zmqData['json_info'] = $jsonData;
					$zmq->sendZmqMsg(609, $zmqData);
					
					$errorInfo=$e instanceof PDOException ? $e->errorInfo : '';
					$filename = DATA_PATH.'runtimes/consume_exception.txt';
					
					$jsonString = json_encode($zmqData);
					error_log( date('Y-m-d H:i',time()).' '.$jsonString.' '.$errorInfo."\n\r",3,$filename);
				}
			}
			$newAttribute = $this->appendConsumeData($consumeAttriute);
		}
		
		if ($this->isAdminAccessCtl() && $newAttribute){
			if(empty($_userConsumeModel )){
				$op_desc = '新增 用户消费属性记录(UID='.$uid.')';
			}else{
				$op_desc = '编辑 用户消费属性记录(UID='.$uid.')';
			}
			$this->saveAdminOpLog($op_desc,$uid);
		}
		return $newAttribute;
	}
	
	/**
	 * 添加用户贡献值变化
	 * 
	 * @param array $records　贡献值记录
	 * @param int $plus 1表示贡献值新增，０表示减少
	 * @return int
	 */
	public function saveUserDedicationRecords(array $records,$plus = 1){
		if(($uid=$records['uid']) <= 0 || $records['dedication'] <= 0){
				return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		if(!$this->checkSource($records['source'],$records['sub_source'])){
			return $this->setError(Yii::t('common','consume source is error'),0);
		}
		$userDedicationecordsModel = new UserDedicationRecordsModel();
		$records['client'] = isset($records['client']) ? $records['client'] : CLIENT_ARCHIVES;
		$records['source'] = isset($records['source']) ? $records['source'] : SOURCE_GIFTS;
		$records['sub_source'] = isset($records['sub_source']) ? $records['sub_source'] : SUBSOURCE_GIFTS_BUY;
		$records['dedication'] = $plus ? abs($records['dedication']) : '-'.abs($records['dedication']);
		$records['create_time'] = time();
		$this->attachAttribute($userDedicationecordsModel,$records);
		$userDedicationecordsModel->save();
		$flag = $userDedicationecordsModel->getPrimaryKey();
		if ($flag && $this->isAdminAccessCtl()){
			if($plus){
				$op_desc = '增加 贡献值['.abs($records['dedication']).'] 用户['.$uid.']';
			}else{
				$op_desc = '扣减 贡献值['.abs($records['dedication']).'] 用户['.$uid.']';
			}
			$this->saveAdminOpLog($op_desc,$uid);
		}
		return $flag;
	}
	
	public function getUserDedicationRecords($uid, $offset = 0, $pagesize = 10, array $condition = array())
	{
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$condition['uid'] = $uid;
		$condition['source'] = array(SOURCE_ACTIVITY,SOURCE_SENDS);
// 		$condition['sub_source'] = SUBSOURCE_SENDS_ADMIN;

		$userDedicationecordsModel = new UserDedicationRecordsModel();
		return $userDedicationecordsModel->getUserDedicationRecords($uid, $offset, $pagesize, $condition);
	}
	
	/**
	 * 获取用户对主播的贡献统计
	 */
	public function getUserDedicationToDoteyByArchivesIds($uid, $archivesIds)
	{
		if($uid <= 0 || empty($archivesIds)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$userDedicationecordsModel = new UserDedicationRecordsModel();
		$res = $userDedicationecordsModel->getUserDedicationToDoteyByArchivesIds($uid, $archivesIds);
		return $this->buildDataByIndex($res, 'to_target_id');
	}
	
	/**
	 * 存储用户皮蛋消费
	 * 
	 * @param array $records
	 * @param int $plus 1表示收入  0表示支出
	 * @return int
	 */
	public function saveUserPipiEggRecords(array $records,$plus = 1){
		if(($uid=$records['uid']) <= 0 || $records['pipiegg'] <= 0){
				return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		if(!$this->checkSource($records['source'],$records['sub_source'])){
			return $this->setError(Yii::t('common','consume source is error'),0);
		}
		
		if(!isset($records['ip_address'])){
			$records['ip_address'] = Yii::app()->request->userHostAddress;
		}
		
		
		$userPipiEggRecordsModel  = new UserPipiEggRecordsModel();
		if(!isset($records['cbalance'])){
			$records['cbalance'] = ConsumeModel::model()->findByPk($uid)->pipiegg;
		}
		$records['source'] = isset($records['source']) ? $records['source'] : SOURCE_GIFTS;
		$records['sub_source'] = isset($records['sub_source']) ? $records['sub_source'] : SUBSOURCE_GIFTS_BUY;
		$records['client'] = isset($records['client']) ? $records['client'] : CLIENT_ARCHIVES;
		$records['pipiegg'] =  $plus  ? abs($records['pipiegg']) : '-'.abs($records['pipiegg']);
		$records['consume_time'] = time();
		
		$this->attachAttribute($userPipiEggRecordsModel,$records);
		$userPipiEggRecordsModel->save();
		return $userPipiEggRecordsModel->getPrimaryKey();
	}
	
	
	/**
	 * 存储用户冻结皮蛋记录
	 *
	 * @param array $records
	 * @param int $plus 0表示解冻  1表示冻结
	 * @return int
	 */
	public function saveUserFreezeePipiEggRecords(array $records,$plus = 1){
		if(($uid=$records['uid']) <= 0 || $records['pipiegg'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		if(!$this->checkSource($records['source'],$records['sub_source'])){
			return $this->setError(Yii::t('common','consume source is error'),0);
		}
		
		if(!isset($records['ip_address'])){
			$records['ip_address'] = Yii::app()->request->userHostAddress;
		}
		
		
	
		if(!isset($records['cbalance'])){
			$records['cbalance'] = ConsumeModel::model()->findByPk($uid)->pipiegg;
		}
		$records['source'] = isset($records['source']) ? $records['source'] : SOURCE_GIFTS;
		$records['sub_source'] = isset($records['sub_source']) ? $records['sub_source'] : SUBSOURCE_GIFTS_BUY;
		$records['client'] = isset($records['client']) ? $records['client'] : CLIENT_ARCHIVES;
		$records['pipiegg'] =  $plus  ? abs($records['pipiegg']) : '-'.abs($records['pipiegg']);
		$records['create_time'] = time();
		$userFreezeePipieggRecordsModel  = new UserFreezeePipieggsRecordsModel();
		$this->attachAttribute($userFreezeePipieggRecordsModel,$records);
		
		$userFreezeePipieggRecordsModel->save();
		return $userFreezeePipieggRecordsModel->getPrimaryKey();
	}
	
	/**
	 * 添加用户贡皮点变化
	 * 
	 * @param array $records　记录
	 * @param int $plus 1表示新增，０表示减少
	 * @return boolean
	 */
	public function saveUserEggPointsRecords(array $records,$plus = 1){
		if(($uid=$records['uid']) <= 0 || $records['egg_points'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		if(!$this->checkSource($records['source'],$records['sub_source'])){
			return $this->setError(Yii::t('common','consume source is error'),0);
		}
		$userEggPointsRecordsModel = new UserEggPointRecordsModel();
		$records['client'] = isset($records['client']) ? $records['client'] : CLIENT_ARCHIVES;
		$records['source'] = isset($records['source']) ? $records['source'] : SOURCE_GIFTS;
		$records['sub_source'] = isset($records['sub_source']) ? $records['sub_source'] : SUBSOURCE_GIFTS_BUY;
		$records['egg_points'] = $plus ? abs($records['egg_points']) : '-'.abs($records['egg_points']);
		$records['create_time'] = isset($records['create_time']) ? isset($records['create_time']) : time();
		$this->attachAttribute($userEggPointsRecordsModel,$records);
		$userEggPointsRecordsModel->save();
		return $userEggPointsRecordsModel->getPrimaryKey();
	}
	
	/**
	 * 添加用户魅力值变化记录
	 * 
	 * @param array $records　记录
	 * @param int $plus 1表示贡献值新增，０表示减少
	 * @return boolean
	 */
	public function saveDoteyCharmRecords(array $records,$plus = 1){
		if(($uid=$records['uid']) <= 0 || $records['charm'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		if(!$this->checkSource($records['source'],$records['sub_source'])){
			return $this->setError(Yii::t('common','consume source is error'),0);
		}
		$doteyCharmRecordsModel = new DoteyCharmRecordsModel();
		$records['client'] = isset($records['client']) ? $records['client'] : CLIENT_ARCHIVES;
		$records['source'] = isset($records['source']) ? $records['source'] : SOURCE_GIFTS;
		$records['sub_source'] = isset($records['sub_source']) ? $records['sub_source'] : SUBSOURCE_GIFTS_BUY;
		$records['charm'] = $plus ? abs($records['charm']) : '-'.abs($records['charm']);
		$records['create_time'] = time();
		$this->attachAttribute($doteyCharmRecordsModel,$records);
		$doteyCharmRecordsModel->save();
		$flag = $doteyCharmRecordsModel->getPrimaryKey();
		if($this->isAdminAccessCtl() && $flag){
			if ($plus){
				$op_desc = "新增 魅力值[".abs($records['charm'])."]到用户[{$uid}] 记录ID[{$flag}]";
			}else{
				$op_desc = "扣减 魅力值[".abs($records['charm'])."]到用户[{$uid}] 记录ID[{$flag}]";
			}
			$this->saveAdminOpLog($op_desc,$uid);	
		}
		return $flag;
	}
	
	/**
	 * 添加用户魅力点变化记录
	 * 
	 * @param array $records 记录
	 * @param int $plus 1表示贡献值新增，０表示减少
	 * @return boolean
	 */
	public function saveDoteyCharmPointsRecords(array $records,$plus = 1){
		if(($uid=$records['uid']) <= 0 || $records['charm_points'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		if(!$this->checkSource($records['source'],$records['sub_source'])){
 			return $this->setError(Yii::t('common','consume source is error'),0);
		}
		$doteyCharmPointsRecordsModel = new DoteyCharmPointRecordsModel();
		$records['client'] = isset($records['client']) ? $records['client'] : CLIENT_ARCHIVES;
		$records['source'] = isset($records['source']) ? $records['source'] : SOURCE_GIFTS;
		$records['sub_source'] = isset($records['sub_source']) ? $records['sub_source'] : SUBSOURCE_GIFTS_BUY;
		$records['charm_points'] = $plus ? abs($records['charm_points']) : '-'.abs($records['charm_points']);
		$records['create_time'] = isset($records['create_time']) ? isset($records['create_time']) : time();
		$this->attachAttribute($doteyCharmPointsRecordsModel,$records);
		$doteyCharmPointsRecordsModel->save();
		$flag = $doteyCharmPointsRecordsModel->getPrimaryKey();
		if($this->isAdminAccessCtl() && $flag){
			if ($plus){
				$op_desc = "新增 魅力点[".abs($records['charm_points'])."]到用户[{$uid}] 记录ID[{$flag}]";
			}else{
				$op_desc = "扣减 魅力点[".abs($records['charm_points'])."]到用户[{$uid}] 记录ID[{$flag}]";
			}
			$this->saveAdminOpLog($op_desc,$uid);
		}
		return $flag;
	}
	
	/**
	 * 存储用户等级信息
	 * 
	 * @param $rank
	 * @return int
	 */
	public function saveUserRank(array $rank){
		if(empty($rank)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$rankModel = new UserRankModel();
		if(isset($rank['rank_id'])){
			$orgRankModel = $rankModel->findByPk($rank['rank_id']);
			if(empty($orgRankModel)){
				return $this->setNotice('props',Yii::t('consume','The rank does not exist'),0);
			}
			$this->attachAttribute($orgRankModel,$rank);
			if(!$orgRankModel->validate()){
				return $this->setNotices($orgRankModel->getErrors(),array());
			}
			$orgRankModel->save();
			$insertId = $rank['rank_id'];
		}else{
			$this->attachAttribute($rankModel,$rank);
			if(!$rankModel->validate()){
				return $this->setNotices($rankModel->getErrors(),array());
			}
			$rankModel->save();
			$insertId = $rankModel->getPrimaryKey();
		}
		$this->setUserRankToRedis();
		if ($insertId && $this->isAdminAccessCtl()){
			if(isset($rank['rank_id'])){
				$op_desc = '编辑 用户等级信息(rank_id:'.$insertId.')';
			}else{
				$op_desc = '新增 用户等级信息(rank_id:'.$insertId.')';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $insertId;
	}
	/**
	 * 删除用户等级
	 * @edit by guoshaobo
	 * @param int $rankId
	 */
	public function deleteUserRank($rankId)
	{
		if($rankId<=0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$rankModel = new UserRankModel();
		$res = $rankModel->deleteByPk($rankId);
		if($res){
			$this->setUserRankToRedis();
			if ($this->isAdminAccessCtl()){
				$this->saveAdminOpLog('删除用户等级信息(rank_id:'.$rankId.')');
			}
			return $res;
		}
		return false;
	}
	
	/**
	 * 存储主播等级信息
	 * 
	 * @param $rank
	 * @return int
	 */
	public function saveDoteyRank(array $rank){
		if(empty($rank)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$rankModel = new DoteyRankModel();
		if(isset($rank['rank_id'])){
			$orgRankModel = $rankModel->findByPk($rank['rank_id']);
			if(empty($orgRankModel)){
				return $this->setNotice('props',Yii::t('consume','The rank does not exist'),0);
			}
			$this->attachAttribute($orgRankModel,$rank);
			if(!$orgRankModel->validate()){
				return $this->setNotices($orgRankModel->getErrors(),array());
			}
			$orgRankModel->save();
			$insertId = $rank['rank_id'];
		}else{
			$this->attachAttribute($rankModel,$rank);
			if(!$rankModel->validate()){
				return $this->setNotices($rankModel->getErrors(),array());
			}
			$rankModel->save();
			$insertId = $rankModel->getPrimaryKey();
		}
		$this->setDoteyRankToRedis();
		if($insertId && $this->isAdminAccessCtl()){
			if(isset($rank['rank_id'])){
				$op_desc = '编辑 主播等级信息(rank_id:'.$insertId.')';
			}else{
				$op_desc = '新增 主播等级信息(rank_id:'.$insertId.')';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $insertId;
	}
	/**
	 * 删除用户等级
	 * @edit by guoshaobo
	 * @param int $rankId
	 */
	public function deleteDoteyRank($rankId)
	{
		if($rankId<=0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$rankModel = new DoteyRankModel();
		$res = $rankModel->deleteByPk($rankId);
		if($res){
			$this->setDoteyRankToRedis();
			if ($this->isAdminAccessCtl()){
				$this->saveAdminOpLog('删除 主播等级('.$rankId.')');
			}
			return $res;
		}
		return false;
	}
	
	/**
	 * 存储主播报酬配置数据 
	 * 
	 * @author supeng
	 */
	public function saveDoteyPayConfig(Array $config){
		if(empty($config)){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$payModel = new PayModel();
		if(isset($config['pay_id'])){
			$orgPayModel = $payModel->findByPk($config['pay_id']);
			if(empty($orgPayModel)){
				return $this->setNotice('payConfig',Yii::t('common','The pay config does not exist'),false);
			}
			$this->attachAttribute($orgPayModel,$config);
			if(!$orgPayModel->validate()){
				return $this->setNotices($orgPayModel->getErrors(),array());
			}
			$orgPayModel->save();
			$insertId = $config['pay_id'];
		}else{
			if (!isset($config['pay_type'])){
				return $this->setNotice('payConfig',Yii::t('common','The pay type not empty'),false);
			}
			$this->attachAttribute($payModel,$config);
			if(!$payModel->validate()){
				return $this->setNotices($payModel->getErrors(),array());
			}
			$payModel->save();
			$insertId = $payModel->getPrimaryKey();
		}
		
		if ($insertId && $this->isAdminAccessCtl()){
			if(isset($config['is_del'])  && $config['is_del'] == 1){
				$op_desc = '删除 主播月度奖励配置(pay_id='.$insertId.') 用户(uid='.$config['uid'].')';
			}else{
				$op_desc = '编辑 主播月度奖励配置(pay_id='.$insertId.') 用户(uid='.$config['uid'].')';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $insertId;
	}
	
	/**
	 * 获取主播报酬列表 
	 * 
	 * @author supeng
	 * @param array $condition
	 * @return mix|Ambigous <multitype:, multitype:NULL >
	 */
	public function getDoteyPayConfig(Array $condition){
		if(empty($condition) || !isset($condition['pay_type'])){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		
		$payModel = new PayModel();
		return $this->arToArray($payModel->getDoteyPayConfig($condition));
	}
	
	/**
	 * 主播月度资金配置
	 * 	暂时处于停用中
	 * 
	 * @author supeng
	 * @param array $uids
	 * @return Ambigous <multitype:Ambigous <mix, Ambigous> Ambigous <mix, Ambigous, multitype:, multitype:NULL > , unknown>
	 */
	public function getDoteyPayConfigByUids(Array $uids){
		$doteySer = new DoteyService();
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
		$globalDirect = $this->getDoteyPayConfig(array('pay_type'=>DOTEY_TYPE_DIRECT,'uid'=>0));
		$globalProxy = $this->getDoteyPayConfig(array('pay_type'=>DOTEY_TYPE_PROXY,'uid'=>0));
		$globalFullTime = $this->getDoteyPayConfig(array('pay_type'=>DOTEY_TYPE_FULLTIME,'uid'=>0));
		if ($types){
			//赋予私有配置
			foreach ($types as $dtype => $duids){
				if(in_array($dtype, $allowDType)){
					$condition = array();
					$condition['pay_type'] = $dtype;
					$condition['uid'] = $duids;
					if($uconf = $this->getDoteyPayConfig($condition)){
						foreach ($uconf as $v){
							$result[$v['uid']][$v['pay_id']] = $v;
						}
					}
				}
			}
			
			//赋予默认项
			foreach ($types as $dtype => $uids){
				foreach ($uids as $uid){
					if(!isset($result[$uid])){
						if ($dtype == DOTEY_TYPE_DIRECT){
							$result[$uid] = $globalDirect;
						}
						if ($dtype == DOTEY_TYPE_PROXY){
							$result[$uid] = $globalProxy;
						}
						if ($dtype == DOTEY_TYPE_FULLTIME){
							$result[$uid] = $globalFullTime;
						}
					}
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * 获取主播报酬的月度奖金配置
	 * 
	 * @author supeng
	 * @param int $uid
	 * @param int $payType
	 * @param int $hours
	 * @param int $days
	 * @param int $charmPoints
	 * @return Ambigous <multitype:, multitype:NULL >
	 */
	public function getAllowDoteyPay($uid,$payType,$hours,$days,$charmPoints){
		if(empty($uid) || empty($payType) || empty($hours) || empty($days) || empty($charmPoints)){
			return $this->setError(Yii::t('common', 'Parameter is empty'),false);
		}
		
		$payModel = new PayModel();
		$result = $payModel->getAllowDoteyPay($uid,$payType,$hours,$days,$charmPoints);
		return $this->arToArray($result);
	}
	
	/**
	 * 取得用户消费属性
	 * 
	 * @param mixed $uids
	 * @return array
	 */
	public function getConsumesByUids($uids){
		if(empty($uids)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$uids = is_array($uids) ? $uids : array($uids);
		$consumeModel = ConsumeModel::model();
		$models = $consumeModel->getConsumesByUids($uids);
		$data = $this->arToArray($models);
		return $this->buildDataByIndex($data,'uid');
	}
	
	/**
	 * 获取匹配条件下的用户消费属性集合
	 * 
	 * @author supeng
	 * @param array $conditon
	 * @return mix|Ambigous <multitype:, multitype:unknown Ambigous <multitype:unknown , unknown> >
	 */
	public function getConsumesByConditions(Array $conditon,$offset=0,$pageSize=10,$isLimit=true, $order = ''){
		if(empty($conditon)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$consumeModel = ConsumeModel::model();
		$data = $consumeModel->getConsumesByConditons($conditon,$offset,$pageSize,$isLimit, $order);
		
		if (isset($data['list'])){
			if($order == ''){
				$data['list'] = $this->buildDataByIndex($this->arToArray($data['list']),'uid');
			}else{
				$data['list'] = $this->arToArray($data['list']);
			}
		}
		return $data;
	}
	
	/**
	 * 执行兑换皮点的动作
	 * @param $uid
	 * @param $eggPoint
	 * @return int 0表示成功, 1表示失败
	 */
	public function exchangeEggPoint($uid, $eggPoint = 0)
	{
		if($uid <= 0 || $eggPoint < 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$flag = ConsumeModel::model()->exchangeEggPoint($uid, $eggPoint);
		if ($flag && $this->isAdminAccessCtl()){
			$op_desc = '扣减/撤销 皮点['.$eggPoint.'] 用户UID['.$uid.']';
			$this->saveAdminOpLog($op_desc,$uid);
		}
		return $flag;
	}
	
	public function exchangeEggPointCharmPoint($uid, $point)
	{
		if($uid <= 0 || $point < 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$flag = ConsumeModel::model()->exchangeEggPointCharmPoint($uid, $point);
		if ($flag && $this->isAdminAccessCtl()){
			$op_desc = '兑换皮点和魅力点 ['.$point.'] 用户UID['.$uid.']';
			$this->saveAdminOpLog($op_desc,$uid);
		}
		return $flag;
	}
	
	/**
	 * 执行兑换魅力点的动作
	 * @param $uid
	 * @param $charmPoint
	 * @return int  0表示失败, 1表示成功
	 */
	public function exchangeCharmPoint($uid, $charmPoint = 0)
	{
		if($uid <= 0 || $charmPoint < 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		$flag = ConsumeModel::model()->exchangeCharmPoint($uid, $charmPoint);
		if ($flag && $this->isAdminAccessCtl()){
			$op_desc = '扣减/撤销 魅力点['.$charmPoint.'] 用户UID['.$uid.']';
			$this->saveAdminOpLog($op_desc,$uid);
		}
		return $flag;
	}
	
	/**
	 * 执行兑换魅值的动作
	 * 
	 * @author supeng
	 * @param $uid
	 * @param $charm
	 * @return int  0表示失败, 1表示成功
	 */
	public function exchangeCharm($uid, $charm = 0)
	{
		if($uid <= 0 || $charm < 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$flag = ConsumeModel::model()->exchangeCharm($uid, $charm);
		if ($flag && $this->isAdminAccessCtl()){
			$op_desc = '扣减/撤销 魅力值['.$charm.'] 用户UID['.$uid.']';
			$this->saveAdminOpLog($op_desc,$uid);
		}
		return $flag;
	}
	
	/**
	 * 保存兑换记录
	 */
	public function saveExchangeCharmPoint($exchange = array())
	{
		if(!isset($exchange['uid']) || $exchange['uid'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$consumeModel = new ExchangeRecordModel();
		$this->attachAttribute($consumeModel,$exchange);
		return $consumeModel->save();
	}
	
	/**
	 * 保存现金平台奖励记录 或才艺补贴记录
	 * 
	 * @author supeng
	 */
	public function saveCashAwardRecords($exchange = array(),$type = EXCHANGE_ADMIN)
	{
		if(!isset($exchange['uid']) || $exchange['uid'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		
		if(!isset($exchange['reason']) || empty($exchange['reason'])){
			return $this->setError(Yii::t('common','Parameter reason is empty'),false);
		}
		
		if(!in_array($type, array(EXCHANGE_ADMIN,EXCHANGE_ART))){
			return $this->setError(Yii::t('common','Parameter is wrong'),false);
		}
		
		//是否是主播
		$doteySer = new DoteyService();
		if($doteySer->getDoteyInfoByUid($exchange['uid'])){
			$add = array();
			$add['ex_type'] = $type;
			$add['handle_type'] = 1;
			$add['create_time'] = time();
			$add['uid'] = $exchange['uid'];
			$add['info'] = $exchange['reason'];
			$add['dst_amount'] = $exchange['quantity'];
			$add['op_uid'] = Yii::app()->user->getId();
			
			$consumeModel = new ExchangeRecordModel();
			$this->attachAttribute($consumeModel,$add);
			$consumeModel->save();
			$flag = $consumeModel->getPrimaryKey();
			if($this->isAdminAccessCtl() && $flag){
				$op_desc = "新增 现金[".$exchange['quantity']."]到用户[".$exchange['uid']."] 记录ID[{$flag}]";
				$this->saveAdminOpLog($op_desc,$exchange['uid']);
			}
			return $flag;
		}else{
			return $this->setError(Yii::t('common','undotey User not add cash award'),false);
		}
	}
	
	/**
	 * 撤消现金平台奖励
	 * @param unknown_type $record_id
	 * @param int $type 3 or 4
	 * @return mix|mixed
	 */
	public function saveUnCashAwardRecords($record_id,$type = 3)
	{
		if(!$record_id){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		
		if (!in_array($type, array(3,4))){
			return $this->setError(Yii::t('common','Parameter is wrong'),false);
		}
		
		$consumeModel = new ExchangeRecordModel();
		//是否是主播
		$add = array();
		$add['record_id'] = $record_id;
		$add['ex_type'] = $type;
		$add['handle_type'] = 2;
		$add['update_time'] = time();
		$add['op_uid'] = Yii::app()->user->getId();
		
		$orgCashModel = $consumeModel->findByPk($record_id);
		if(empty($orgCashModel)){
			return $this->setNotice('cash',Yii::t('common','The cash award does not exist'),false);
		}
		$this->attachAttribute($orgCashModel,$add);
		if(!$orgCashModel->validate()){
			return $this->setNotices($orgCashModel->getErrors(),array());
		}
		
		$flag = $orgCashModel->save();
		if($flag && $this->isAdminAccessCtl()){
			$type_desc = $type == 3?'平台奖励':($type == 4?'才艺补贴':'');
			$op_desc = '撤销 '.$type_desc.'-现金(record_id='.$record_id.')';
			$this->saveAdminOpLog($op_desc);
		}
		return $flag;
	}
	
	/**
	 * 获取现金平台奖励 获取才艺补贴
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 */
	public function getCashAwardByCondition(Array $condition = array(),$offset = 0,$pageSize = 10,$isLimit = true){
		$doteySer = new DoteyService();
		$uids = $doteySer->searchDoteyUidsByCodition($condition);
		if ($uids){
			if (is_array($uids)){
				$condition['uid'] = $uids;
			}
		}else{
			return array('count'=>0,'list'=>array());
		}
		
		$cashModel = new ExchangeRecordModel();
		$result = $cashModel->getCashAwardByCondition($condition,$offset,$pageSize,$isLimit);
		if ($result['list']){
			$result['list'] = $this->arToArray($result['list']);
		}
		return $result;
	}
	
	/**
	 * 获取魅力点相关的平台奖励
	 *
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 */
	public function getCharmPointsAwardByCondition(Array $condition = array(),$offset = 0,$pageSize = 10,$isLimit = true){
		$doteySer = new DoteyService();
		$uids = $doteySer->searchDoteyUidsByCodition($condition);
		if ($uids){
			if (is_array($uids)){
				$condition['uid'] = $uids;
			}
		}else{
			return array('count'=>0,'list'=>array());
		}
	
		$charmPointsModel = new DoteyCharmPointRecordsModel();
		$result = $charmPointsModel->getCharmPointsAwardByCondition($condition,$offset,$pageSize,$isLimit);
		if ($result['list']){
			$result['list'] = $this->arToArray($result['list']);
		}
		return $result;
	}
	
	/**
	 * 获取魅力点记录
	 *
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 */
	public function getCharmPointsByCondition(Array $condition = array(),$offset = 0,$pageSize = 10,$isLimit = true){
		$doteySer = new DoteyService();
		$uids = $doteySer->searchDoteyUidsByCodition($condition);
		if ($uids){
			if (is_array($uids)){
				$condition['uid'] = $uids;
			}
		}else{
			return array('count'=>0,'list'=>array());
		}
	
		if (!empty($condition['source'])){
			$_source = explode('*', $condition['source']);
			$condition['source'] = $_source[0];
			if (isset($_source[1])){
				$condition['sub_source'] = $_source[1];
			}
		}
	
		$charmPointsModel = new DoteyCharmPointRecordsModel();
		$result = $charmPointsModel->getCharmPointsByCondition($condition,$offset,$pageSize,$isLimit);
		if ($result['list']){
			$result['list'] = $this->arToArray($result['list']);
		}
		return $result;
	}
	
	/**
	 * 获取贡献值记录
	 *
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 */
	public function getDedicationByCondition(Array $condition = array(),$offset = 0,$pageSize = 10,$isLimit = true){
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$UserService = new UserService();
			$info = $UserService->searchUserList($offset,$pageSize,$condition,false);
			if($info['uids']){
				$condition['uid'] = $info['uids'];
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
	
		if (!empty($condition['source'])){
			$_source = explode('*', $condition['source']);
			$condition['source'] = $_source[0];
			if (isset($_source[1])){
				$condition['sub_source'] = $_source[1];
			}
		}
	
		$userDedicationModel = new UserDedicationRecordsModel();
		$result = $userDedicationModel->getDedicationByCondition($condition,$offset,$pageSize,$isLimit);
		if ($result['list']){
			$result['list'] = $this->arToArray($result['list']);
		}
		return $result;
	}
	
	/**
	 * 获取皮蛋记录
	 *
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 */
	public function getPipieggsByCondition(Array $condition = array(),$offset = 0,$pageSize = 10,$isLimit = true){
		if (!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$_condition = array();
			if(!empty($condition['username'])){
				$_condition['username'] = $condition['username'];
			}
			if(!empty($condition['nickname'])){
				$_condition['nickname'] = $condition['nickname'];
			}
			if(!empty($condition['realname'])){
				$_condition['realname'] = $condition['realname'];
			}
			$userSer = new UserService();
			$info = $userSer->searchUserList(null,null,$_condition,false);
			if($info['list']){
				$condition['uid'] = array_keys($info['list']);
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
	
		if (!empty($condition['source'])){
			$_source = explode('*', $condition['source']);
			$condition['source'] = $_source[0];
			if (isset($_source[1])){
				$condition['sub_source'] = $_source[1];
			}
		}
		$pipiEggsModel = new UserPipiEggRecordsModel();
		$result = $pipiEggsModel->getPipieggsByCondition($condition,$offset,$pageSize,$isLimit);
		if ($result['list']){
			$result['list'] = $this->arToArray($result['list']);
		}
		return $result;
	}
	
	/**
	 * 获取充值皮蛋记录
	 * @author guoshaobo
	 * @param unknown_type $uids
	 * @param array $subSource
	 * @return array
	 */
	public function getRechargePipieggRecord($uids, array $subSource = array(), $offset = 0, $pageSize = 10)
	{
		$pipiEggsModel = new UserPipiEggRecordsModel();
		$result = $pipiEggsModel->getRechargePipieggRecord($uids, $subSource, $offset, $pageSize);
		if ($result['list']){
			$result['list'] = $this->arToArray($result['list']);
		}
		return $result;
	}
	
	/**
	 * 获取皮蛋记录
	 *
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 */
	public function getPipieggsSumByCondition(Array $condition = array(),$offset = 0,$pageSize = 10,$isLimit = true){
		if (!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$_condition = array();
			if(!empty($condition['username'])){
				$_condition['username'] = $condition['username'];
			}
			if(!empty($condition['nickname'])){
				$_condition['nickname'] = $condition['nickname'];
			}
			if(!empty($condition['realname'])){
				$_condition['realname'] = $condition['realname'];
			}
			$userSer = new UserService();
			$info = $userSer->searchUserList(null,null,$_condition,false);
			if($info['list']){
				$condition['uid'] = array_keys($info['list']);
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
	
		if (!empty($condition['source'])){
			$_source = explode('*', $condition['source']);
			$condition['source'] = $_source[0];
			if (isset($_source[1])){
				$condition['sub_source'] = $_source[1];
			}
		}
		$pipiEggsModel = new UserPipiEggRecordsModel();
		$result = $pipiEggsModel->getPipieggsSumByCondition($condition,$offset,$pageSize,$isLimit);
		if ($result['list']){
			$result['list'] = $this->buildDataByIndex($result['list'], 'uid');
		}
		return $result;
	}
	
	/**
	 * 获取每日幸运星奖励记录
	 * @param array $condition
	 * @return array
	 */
	public function getLuckStarPipiRecord(array $condition = array()){
		$pipiEggsModel = new UserPipiEggRecordsModel();
		$data=$pipiEggsModel->getLuckStarPipiRecord($condition);
		return $this->arToArray($data);
	}
	
	/**
	 * 获取魅力值相关的平台奖励
	 *
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 */
	public function getCharmAwardByCondition(Array $condition = array(),$offset = 0,$pageSize = 10,$isLimit = true){
		$doteySer = new DoteyService();
		$uids = $doteySer->searchDoteyUidsByCodition($condition);
		if ($uids){
			if (is_array($uids)){
				$condition['uid'] = $uids;
			}
		}else{
			return array('count'=>0,'list'=>array());
		}
	
		$charmModel = new DoteyCharmRecordsModel();
		$result = $charmModel->getCharmAwardByCondition($condition,$offset,$pageSize,$isLimit);
		if ($result['list']){
			$result['list'] = $this->arToArray($result['list']);
		}
		return $result;
	}
	
	
	
	/**
	 * 获取魅力值相关记录
	 *
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 */
	public function getCharmByCondition(Array $condition = array(),$offset = 0,$pageSize = 10,$isLimit = true){
		$doteySer = new DoteyService();
		$uids = $doteySer->searchDoteyUidsByCodition($condition);
		if ($uids){
			if (is_array($uids)){
				$condition['uid'] = $uids;
			}
		}else{
			return array('count'=>0,'list'=>array());
		}
	
		if (!empty($condition['source'])){
			$_source = explode('*', $condition['source']);
			$condition['source'] = $_source[0];
			if (isset($_source[1])){
				$condition['sub_source'] = $_source[1];
			}
		}
		
		$charmModel = new DoteyCharmRecordsModel();
		$result = $charmModel->getCharmByCondition($condition,$offset,$pageSize,$isLimit);
		if ($result['list']){
			$result['list'] = $this->arToArray($result['list']);
		}
		return $result;
	}
	
	/**
	 * 获取记录变化子来源项  
	 * 
	 * @param string $source 来源;
	 * @param string $subSource 子来源
	 * @return array 获取送礼子来源   
	 */
	public function getSourceList($source=null,$subSource=null ){
		$list = array(
			SOURCE_GIFTS => array(
			  	'name'=>'礼物',
				'subsource'=>array(
					SUBSOURCE_GIFTS_BUY => '直接购买送礼',
					SUBSOURCE_GIFTS_BAG=>'背包送礼',
					SUBSOURCE_LUCK_GIFT_AWARD=>'幸运礼物奖励'
				)
			),
			SOURCE_USERGIFTS => array(
				'name'=>'礼物',
				'subsource'=>array(
					SUBSOURCE_GIFTS_BUY => '直接购买送礼',
					SUBSOURCE_GIFTS_BAG=>'背包送礼',
					SUBSOURCE_LUCK_GIFT_AWARD=>'幸运礼物奖励'
				)
			),
			SOURCE_PROPS => array(
			  	'name'=>'道具',
				'subsource'=>array(
					SUBSOURCE_PROPS_VIP => 'VIP',
					SUBSOURCE_PROPS_LABEL=>'贴条',
					SUBSOURCE_PROPS_REMOVELABEL =>'移除贴条',
					SUBSOURCE_PROPS_CAR => '座驾',
					SUBSOURCE_PROPS_MONTHCARD=>'月卡',
					SUBSOURCE_PROPS_GUARDIAN=>'守护',
					SUBSOURCE_PROPS_FLYSCREEN=>'飞屏',
					SUBSOURCE_PROPS_DICE=>'骰子',
					SUBSOURCE_PROPS_NUMBER=>'靓号',
				)
			),
			SOURCE_SENDS => array(
			  	'name'=>'赠送',
				'subsource'=>array(
					SUBSOURCE_SENDS_ADMIN => '后台管理员赠送',
					SUBSOURCE_SENDS_ACTIVITY=>'活动赠送',
				)
			),
			SOURCE_EXCHANGE => array(
				'name'=>'兑换',
				'subsource'=>array(
					SUBSOURCE_EXCHANGE_EGG => '兑换皮蛋'
				)
			),
			SOURCE_RECHARGE => array(
				'name'=>'充值',
				'subsource'=>array(
						SUBSOURCE_RECHARGE_ADDPIPIEGG => '皮蛋充值',
				 )
			),
			SOURCE_SONGS => array(
			  	'name'=>'点歌',
				'subsource'=>array(
				)
			),
			SOURCE_ACTIVITY => array(
				'name' => '活动',
				'subsource'=>array(
						SUBSOURCE_ACTIVITY_YIRUITE => '打工任务',
						SUBSOURCE_ACTIVITY_GIFTSTAR=>'礼物之星',
						SUBSOURCE_ACTIVITY_HAPPYBIRTHDAY=>'生日快乐',
						SUBSOURCE_LUCK_STAR=>'每日幸运星',
						SUBSOURCE_ACTIVITY_HALLOWEEN=>date("Y").'万圣节',
						SUBSOURCE_ACTIVITY_2YEARS=>'2周年庆'
				 )
			),
			SOURCE_GAMES =>array(
				'name' => '游戏',
				'subsource'=>array(
					 SUBSOURCE_GAMES_BREAKGOLDEGG => '砸金蛋',
				     SUBSOURCE_GAMES_OPENBOX =>'开心宝箱',
				     SUBSOURCE_GAMES_SOFASITDOWN=>'幸运沙发',
				     SUBSOURCE_GAMES_SCRATCHCARD=>'呱呱卡'
				)
			),
			SOURCE_TASKS =>array(
				'name' => '任务',
				'subsource'=>array(
					SUBSOURCE_TASKS_TASK => '新手任务奖励',
				)
			),
			SOURCE_FAMILY => array(
				'name' => '家族',
				'subsource'	=> array(
					SUBSOURCE_FAMILY_CREATE => '申请创建家族',
					SUBSOURCE_FAMILY_MEDAL => '购买家族徽章',
					SUBSOURCE_FAMILY_MEDAL_UPDATE => '修改家族徽章',
					SUBSOURCE_FAMILY_CREATE_RETURN => '创建家族审核不通过返还皮蛋',
				)
			)
		);
		
		if($source && $subSource){
			return $list[$source]['subsource'][$subSource];
		}
		
		if($source){
			return $list[$source];
		}
		
		return $list;
	}
	
	/**
	 * 检查来源是否正常
	 * 
	 * @param string $source
	 * @param string $subsource
	 * @return boolean
	 */
	public function checkSource($source,$subsource = ''){
		if(!$source){
			return false;
		}
		$list = $this->getSourceList('','');
		if(!isset($list[$source])){
			return false;
		}
		if(!in_array($source,array(SOURCE_SONGS,SOURCE_GAMES))){
			if(!$subsource){
				return false;
			}
			if(!isset($list[$source]['subsource'][$subsource])){
				return false;
			}
		}
		return true;
	}
	
	/**
	 * 根据贡献值取得当前等级
	 * 
	 * @param int $dedication
	 * @return array
	 */
	public function getUserRankByDedication($dedication){
		$userRankModel =  UserRankModel::model();
		$rankModel = $userRankModel->getUserRankByDedication($dedication);
		if($rankModel){
			return $rankModel->attributes;
		}
		return array();
	}
	/**
	 * 根据魅力值取得当前等级
	 * 
	 * @param int $charm
	 * @return array
	 */
	public function getDoteyRankByCharm($charm){
		$doteyRankModel =  DoteyRankModel::model();
		$doteyRank= $doteyRankModel->getDoteyRankByCharm($charm);
		if($doteyRank){
			return $doteyRank->attributes;
		}
		return array();
	}
	
	/**
	 * 获取所有主播等级
	 *
	 * @param int $charm
	 * @return array
	 */
	public function getDoteyAllRank(){
		$doteyRankModel =  DoteyRankModel::model();
		$doteyRank= $doteyRankModel->getDoteyAllRank();
		return $this->buildDataByIndex($this->arToArray($doteyRank), 'rank_id');
	}
	
	/**
	 * 将所有主播等级信息写到redis
	 * 
	 * @return boolean
	 */
	public function setDoteyRankToRedis(){
		$redisModel = new OtherRedisModel();
		$allDoteyRank = $this->getDoteyAllRank();
		$allDoteyRank = $this->buildDataByIndex($allDoteyRank,'rank');
		return $redisModel->setAllDoteyRank($allDoteyRank);
	}
	/**
	 * 获取所有主播等级信息
	 * 
	 * @return array
	 */
	public function getDoteyRankFromRedis(){
		$redisModel = new OtherRedisModel();
		$allDoteyRank = $redisModel->getAllDoteyRank();
		if(empty($allDoteyRank)){
			$allDoteyRank = $this->getDoteyAllRank();
			$allDoteyRank = $this->buildDataByIndex($allDoteyRank,'rank');
			$this->setDoteyRankToRedis();
		}
		return $allDoteyRank;
	}
	/**
	 * 取得用户等级详细信息
	 * 
	 * @param int|array $rank 
	 * @return array
	 */
	public function getUserRanksInfoByGrades($rank){
		$userRankModel =  UserRankModel::model();
		$ranks = $userRankModel->getRanksInfoByRanks($rank);
		$ranks = $this->arToArray($ranks);
		return $this->buildDataByIndex($ranks,'rank');
	}
	
	/**
	 * 取得主播等级详细信息
	 * 
	 * @param int|array $rank 
	 * @return array
	 */
	public function getDoteyRanksInfoByGrades($rank){
		$doteyRankModel =  DoteyRankModel::model();
		$ranks = $doteyRankModel->getRanksInfoByRanks($rank);
		$ranks = $this->arToArray($ranks);
		return $this->buildDataByIndex($ranks,'rank');
	}
	
	/**
	 * 在用户用户消费表的原始数据上做加操作。不做减操作，涉及到消费表的减操作和皮蛋相关的，由事务完成
	 * 
	 * @param array $newArray 新更新的记录
	 * @return array
	 */
	public function appendConsumeData(array &$newArray){
		if(empty($newArray)){
			return $newArray;
		}
		$_consumeModel = ConsumeModel::model()->findByPk($newArray['uid']);
		$rankUpdate = false;
		$userJson = array();
		//皮蛋消费　事务处理，这里不做更新
		if(isset($newArray['pipiegg'])){
			//已经由事务减去皮蛋
			$userJson['pe'] = $_consumeModel->pipiegg;
		}
		
		//冻结皮蛋消费　事务处理，这里不做更新
		if(isset($newArray['freeze_pipiegg'])){
			$userJson['fe'] = $_consumeModel->freeze_pipiegg;
		}
		
		if(isset($newArray['egg_points'])){
			$userJson['ep'] = $_consumeModel->egg_points;
		}
		
		if(isset($newArray['charm_points'])){
			//魅力点不能更新为０，一般做加、减操作由事务处理
			$userJson['cp'] = $_consumeModel->charm_points;
		}
		
		if(isset($newArray['charm'])){
			$newCharm = $_consumeModel->charm;
			//检查用户等级,如果用户贡献值达到下一个等级，直接更新
			$doteyRank = $this->getDoteyRankByCharm($newCharm);
			if($doteyRank && ($_consumeModel->dotey_rank != $doteyRank['rank'])){
				//写到数据库
				$newArray['dotey_rank'] = $doteyRank['rank'];
				//写到redis
				$userJson['dk'] = $newArray['dotey_rank'];
				
				$rankUpdate['dotey_rank'] = $newArray['dotey_rank'];
				
			}
			$userJson['ch'] = $newCharm;
		}
		
		
		
		if(isset($newArray['dedication'])){
			$newDedication =  $_consumeModel->dedication;;
			//检查用户等级,如果用户贡献值达到下一个等级，直接更新
			$userRank = $this->getUserRankByDedication($newDedication);
			if($userRank && ($_consumeModel->rank != $userRank['rank'])){
				//写到数据库
				$newArray['rank'] = $userRank['rank'];
				//写到redis 发送ZMP
				$userJson['rk'] = $newArray['rank'];
				
				$rankUpdate['rank'] = $newArray['rank'];
			}
			$userJson['de'] = $newDedication;
		}
		
		//直接改变用户等级 order by supeng
		/* if(isset($newArray['rank'])){
			$oldRank = $_consumeModel->rank;
			if($oldRank != $newArray['rank']){
				//写到redis 发送ZMP
				$userJson['rk'] = $newArray['rank'];
				$rankUpdate['rank'] = $newArray['rank'];
			}
		} */
		
		if($userJson){
			$jsonService = new UserJsonInfoService();
			$_userJson = $jsonService->getUserInfo($_consumeModel->uid,false);
			$messageService = new MessageService();
			$message['uid'] = 0;
			$message['to_uid'] = $_consumeModel->uid;
			$message['is_read'] = 1;
			$message['title'] = '升级啦！';
			$message['category'] = MESSAGE_CATEGORY_SYSTEM;
			$message['sub_category'] = MESSAGE_CATEGORY_SYSTEM_UPGRADE;
			$message['target_id'] =  isset($newArray['archives_id']) ? $newArray['archives_id'] : 0;
			$message['extra']= array('from'=>'系统发送','href'=>'');
			$userJsonService = new UserJsonInfoService();
			//用户消费属性值写到redis 聊天服务器登录后初始化数据从redis取
			$userJsonService->setUserInfo($_consumeModel->uid,$userJson);
			//事件包 用户消费属性值改变
			$zmq = $this->getZmq();
			$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$_consumeModel->uid,'json_info'=>$userJson));
			//主播等级变化后，发送localroom 通知主播等级变化了
			$time = date('H:i');
			if(isset($userJson['dk'])){
				$drankname = isset($doteyRank['name']) ? $doteyRank['name'] : '';
				$message['content'] = "恭喜您升级达到<span style='color:red;'>{$drankname}{$drankname['rank']}！</span>继续加油哦！";
				$messageService->sendMessage($message);
				$archivesService = new ArchivesService();
				$archives = $archivesService->getArchivesByUids($_consumeModel->uid);
				if($archives){
					$archiveId = '';
					foreach($archives as $archive){
						$archiveId .= ($archiveId ? ',' : '').$archive['archives_id'];
					}
					$zmq->sendZmqMsg(606,array('archives_id'=>$archiveId,'domain'=>DOMAIN,'type'=>'localroom','json_content'=>array('uid'=>$_consumeModel->uid,'new_time'=>time(),'name'=>$drankname,'nickname'=>(isset($_userJson['nk']) ? $_userJson['nk'] : ''),'rank'=>$userJson['dk'],'time'=>$time,'type'=>'upgrade')));
				}
			}
			
			if(isset($userJson['rk']) && isset($newArray['archives_id'])){
				$urankname = isset($userRank['name']) ? $userRank['name'] : '';
				$message['content'] = "恭喜您升级达到<span style='color:red;'>{$urankname}{$urankname['rank']}！</span>继续加油哦！";
				$messageService->sendMessage($message);
				$zmq->sendBrodcastMsg(array('archives_id'=>$newArray['archives_id'],'domain'=>DOMAIN,'type'=>'localroom','json_content'=>array('uid'=>$_consumeModel->uid,'new_time'=>time(),'name'=>$urankname,'nickname'=>(isset($_userJson['nk']) ? $_userJson['nk'] : ''), 'rank'=>$userJson['rk'],'time'=>$time,'type'=>'upgrade_user')));
			}
		}
		//一般用callback回调
		if(isset($newArray['callback'])){
			$callback = $this->array_get($newArray,'callback');
			if(is_string($callback) &&  function_exists($callback)){
				call_user_func_array($callback,array($newArray));
			} 
			if(is_array($callback)){
				list($class,$method) = $callback; 
				if(method_exists($class,$method) && is_callable($callback)){
					call_user_func_array($callback,array($newArray));
				}
			}
		}
		if($rankUpdate){
			try{
				$_consumeModel->updateAll($rankUpdate,'uid = '.$_consumeModel->uid);
			}catch(Exception $e){
				//由于更新等级引起的死锁，捕获暂不做处理，避免PHP程序终止执行
			}
		}
		return $_consumeModel->attributes;
	}
	
	/**
	 * 获取所有的用户等级
	 * 
	 * @author supeng
	 * @return array $ranks
	 */
	public function getAllUserRanks($index = 'rank_id'){
		$userRankModel =  UserRankModel::model();
		$ranks = $userRankModel->getAllRanks();
		$allRanks = $this->arToArray($ranks);
		return $this->buildDataByIndex($allRanks,$index);
	}
	
	/**
	 * 将所有用户等级信息写到redis
	 * 
	 * @return boolean
	 */
	public function setUserRankToRedis(){
		$redisModel = new OtherRedisModel();
		$allUserRank = $this->getAllUserRanks();
		$allUserRank = $this->buildDataByIndex($allUserRank,'rank');
		return $redisModel->setAllUserRank($allUserRank);
	}
	/**
	 * 获取所有用户等级信息
	 * 
	 * @return array
	 */
	public function getUserRankFromRedis(){
		$redisModel = new OtherRedisModel();
		$allUserRank = $redisModel->getAllUserRank();
		if(empty($allUserRank)){
			$allUserRank = $this->getAllUserRanks();
			$allUserRank = $this->buildDataByIndex($allUserRank,'rank');
			$this->setUserRankToRedis();
		}
		return $allUserRank;
	}
	/**
	 * 存储消费星级
	 * @author hexin
	 * @param int $uid 用户id
	 * @param int $stars 星等级
	 * @param array $record 星级纪录
	 * @return int
	 */
	public function saveStars($uid, $stars, array $record){
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$userPropsService = new UserPropsService();
		$attribute = array(
			'uid'	=> intval($uid),
			'stars'	=> $stars,
		);
		$record['uid'] = $uid;
		$r = $userPropsService -> saveUserPropsAttribute($attribute);
		if($r){
			$model = new StarsRecordModel();
			foreach($record as $key=>$value){
				$model->$key = $value;
			}
			$model -> save();
			
			$userInfoServer = new UserJsonInfoService();
			$info = $userInfoServer -> getUserInfo($uid, false);
			$info['st'] = $stars;
			$userInfoServer -> setUserInfo($uid, $info);
		}
		return $r;
	}
	
	/**
	 * 根据皮蛋数取得当前等级
	 * @author hexin
	 * @param int $pipiegg
	 * @return array
	 */
	public function getStars($pipiegg){
		$rankModel =  StarsRankModel::model()->getStars($pipiegg);
		if($rankModel){
			return $rankModel->attributes;
		}
		return array();
	}
	
	/**
	 * 取得星级详细信息
	 * @author hexin
	 * @param int|array $stars
	 * @return array
	 */
	public function getStarsInfos($stars){
		$ranks = StarsRankModel::model()->getStarsInfos($stars);
		$ranks = $this->arToArray($ranks);
		return $this->buildDataByIndex($ranks, 'stars');
	}
	
	/**
	 * 获取奖励类型
	 * 
	 * @author supeng
	 * @return multitype:string 
	 */
	public function getAwardType(){
		return array(
				AWARD_TYPE_CHARM 		=> '魅力值',
				AWARD_TYPE_CHARMPOINTS 	=> '魅力点',
				AWARD_TYPE_CASH 		=> '现金',
			);
	}
	
	/**
	 * 通过目标ID统计送礼
	 *
	 * @author supeng
	 * @param array $targetIds
	 * @param array $condition
	 * @return mix|mixe
	 */
	public function searchSongByTargetIds(Array $targetIds,Array $condition = array()){
		if (empty($targetIds) || !is_array($targetIds)){
			return $this->setError(Yii::t('common', 'Parameters is Wrong!'),false);
		}
		$userSongModel = new UserSongModel();
		return $userSongModel->searchSongByTargetIds($targetIds,$condition);
	}
	
	/**
	 * 通过目标用户ID统计送礼
	 * 
	 * @author supeng
	 * @param array $uids
	 * @param array $condition
	 * @return mix|mixed
	 */
	public function searchSongByToUids(Array $uids,Array $condition = array()){
		if (empty($uids) || !is_array($uids)){
			return $this->setError(Yii::t('common', 'Parameters is Wrong!'),false);
		}
		$userSongModel = new UserSongModel();
		return $this->buildDataByIndex($userSongModel->searchSongByToUids($uids,$condition), 'to_uid');
	}
	

	/**
	 * 获取兑换记录
	 * @param  $uids
	 * @param  $condition
	 * @param  $limit
	 * @return array
	 */
	public function getExchangeRecord($uids,$condition,$limit = 5)
	{
		if(count($uids) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$exchangeModel = new ExchangeRecordModel();
		$res = $this->arToArray($exchangeModel->getExchangeRecord($uids,$condition,$limit));
		return $res;
	}
	
	/**
	 * 获取兑换皮蛋的记录
	 * @param $uid
	 * @param $limit
	 * @return array
	 */
	public function getExchangeEggRecord($uid, $limit = 5)
	{
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$exchangeModel = new ExchangeRecordModel();
		$res = ($exchangeModel->getExchangeEggRecord($uid,$limit));
		return $res;
	}
	
	/**
	 * 统计兑换记录
	 * @param unknown_type $uid
	 * @param unknown_type $stime
	 * @param unknown_type $etime
	 * @return mix|mixed
	 */
	public function countExchangeRecord($uid, $stime, $etime, $exType)
	{
		if($uid <= 0 || $stime<=0 || $etime<=0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$exchangeModel = new ExchangeRecordModel();
		return $exchangeModel->countExchangeRecord($uid, $stime, $etime,$exType);
	}
	
	public function getExchangeRecordList(array $uids, $condition = array()){
		$exchangeModel = new ExchangeRecordModel();
		$records = $exchangeModel->getExchageRecordsList($uids,$condition);
		return $this->buildDataByKey($this->arToArray($records),'uid');
	}
	
	/**
	 * 统计兑换记录
	 * 
	 * @author supeng
	 * @param unknown_type $uid
	 * @param unknown_type $stime
	 * @param unknown_type $etime
	 * @return mix|mixed
	 */
	public function countExchangeRecordByUids($uids, $stime, $etime, $exType=1)
	{
		if(!is_array($uids) || $stime<=0 || $etime<=0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$exchangeModel = new ExchangeRecordModel();
		$result = $exchangeModel->countExchangeRecordByUids($uids, $stime, $etime,$exType);
		if ($result) {
			$result = $this->buildDataByIndex($result, 'uid');
		}
		return $result;
	}
	
	/**
	 * 获取工资结构列表
	 * @author guoshaobo
	 */
	public function getPayNormal()
	{
		$payModel = new PayModel();
		$res = $payModel->findAll();
		return $this->arToArray($res);
	}
	
	/**
	 * 获取单个主播的工资结构
	 * @author guoshaobo 
	 * @param unknown_type $uid
	 * @param unknown_type $type
	 * @return mix
	 */
	public function getDoteyPay($uid, $type)
	{
		if($uid <= 0 || $type <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$payModel = new PayModel();
		$condition = array('uid'=>$uid, 'pay_type'=>$type);
		$res = $payModel->getDoteyPayConfig($condition);
		if($res){
			$data = array();
			foreach($res as $k=>$v){
				$data[] = $v->attributes;
			}
			return $data;
		}else{
			$condition = array('pay_type'=>$type);
			$res = $payModel->getDoteyPayConfig($condition);
			if($res){
				$data = array();
				foreach($res as $k=>$v){
					$data[] = $v->attributes;
				}
				return $data;
			}
		}
		
		return false;
	}
	
	/**
	 * 统计主播魅力点兑换记录
	 * @param unknown_type $doteyIds
	 * @param unknown_type $condition
	 * @return mixed
	 */
	public function getDoteyCharmPointsRecords($doteyIds,$condition = array())
	{
		$doteyCharmPointsRecordsModel = new DoteyCharmPointRecordsModel();
		$res = $doteyCharmPointsRecordsModel->getDoteyCharmPointsRecords($doteyIds,$condition);
		if($res){
			return array_pop($res);
		}
		return false;
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $doteyIds
	 * @param unknown_type $condition
	 * @return mixed
	 */
	public function getDoteyCharmRecords($doteyIds,$condition = array())
	{
		$doteyCharmRecordsModel = new DoteyCharmRecordsModel();
		$res = $doteyCharmRecordsModel->getDoteyCharmRecords($doteyIds,$condition);
		if($res){
			$res = $this->buildDataByIndex($res, 'uid');
		}
		return $res;
	}
	
	/**
	 * 获取主播月度统计的魅力点有效值
	 * 
	 * @author supeng
	 * @author guoshaobo 数组的key值替换
	 * @param unknown_type $doteyIds
	 * @param unknown_type $condition
	 * @return mixed
	 */
	public function getMonthDoteyCharmPoints($doteyIds,$condition = array(), $build = false)
	{
		if(empty($condition)){
			return $this->setError(Yii::t('common', 'Parameter is empty'),false);
		}
		
		$doteyCharmPointsRecordsModel = new DoteyCharmPointRecordsModel();
		$res = $doteyCharmPointsRecordsModel->getMonthDoteyCharmPoints($doteyIds,$condition);
		if($build){
			$res = $this->buildDataByIndex($res, 'uid');
		}
		return $res;
	}
	
	/**
	 * 获取赠品类型
	 * 
	 * @author supeng
	 * @return array
	 */
	public function getGiveawayType(){
		return array(
				GIVEAWAY_TYPE_GIFT => '礼物',
				GIVEAWAY_TYPE_PROPS => '道具',
				GIVEAWAY_TYPE_CHARM => '魅力值',
				GIVEAWAY_TYPE_CHARMPOINTS => '魅力点',
				GIVEAWAY_TYPE_DEDICATION => '贡献值',
				GIVEAWAY_TYPE_PIPIEGGS => '皮蛋',
			);
	}
	
	/**
	 * @author supeng
	 * @return array 
	 */
	public function getClients(){
		return array(
			CLIENT_ARCHIVES => '档期',
			CLIENT_ACTIVITES => '活动',
			CLIENT_ADMIN => '后台',
			CLIENT_SHOP => '商城',
			CLIENT_RECHARGE => '充值'
		);
	}
	
	/**
	 * 先更新数据库，成功后再更新redis的userinfo
	 * @author hexin
	 * @param int $uid
	 * @param array $userInfo = array('pipiegg' => true, 'freeze_pipiegg' => true, 'egg_points' => true, 'charm_points' => true, 'charm' => true, 'dedication' => true); 指定哪些需要更新的userinfo消费字段
	 * @return boolean
	 */
	public function updateUserJsonInfo($uid, $userInfo = array()){
		if(!empty($userInfo)){
			$userInfo['uid'] = $uid;
			$this->appendConsumeData($userInfo);
			return true;
		}
		return false;
	}
	/**
	 * 充值接口，这个方法不能带折扣，含有折扣兑换皮蛋的比例不对，导致同时写入充值记录和皮蛋变化记录的数据不一致
	 * 
	 * @param array $recharge
	 * @return int
	 */
	public function recharge(array $recharge){
		if(!isset($recharge['rorderid']) || $recharge['uid'] <= 0 || !isset($recharge['sign'])){
			return $this->setError(Yii::t('common', 'Parameter is empty'),0);
		}
		
		if(!isset($recharge['money']) || $recharge['money'] <= 0){
			return $this->setError(Yii::t('recharge', 'Recharge amount is incorrect'),0);
		}
		
		if(!isset($recharge['rsource'])){
			$recharge['rsource'] = 'show';
		}
		if(!isset($recharge['rip'])){
			$recharge['rip'] = Yii::app()->request->userHostAddress;
		}
		
		if(!isset($recharge['issuccess'])){
			$recharge['issuccess'] = 1;
		}
		
		if(!isset($recharge['currencycode'])){
			$recharge['currencycode'] = 'RMB';
		}
		$newAttribute = array();
		$userBasic['uid'] = $recharge['uid'];
		$consume['uid'] = $recharge['uid'];
		$recharge['rtime'] = time();
		$recharge['ctime'] = time();
		$pipiegg = $recharge['pipiegg'];
		if($recharge['currencycode']=='USD'){
			$pipiegg = $pipiegg*6;
			$userBasic['recharge_usd'] = $recharge['money'];
		}elseif($recharge['currencycode']=='RMB'){
			$userBasic['recharge'] = $recharge['money'];
		}
		$consume['pipiegg'] = $pipiegg;
		$userRechargeModel  =  new UserRechargeRecordsModel();
		$pptvOrder=$userRechargeModel->checkSignBySource($recharge['rsource'],$recharge['sign']);
		if($pptvOrder){
			 return $this->setError(Yii::t('recharge', 'The order already exists'),false);;
		}
		$userService = new UserService();
		$consumeService = new ConsumeService();
		$primaryKey = 0;
		if($consumeService->addEggs($recharge['uid'],$pipiegg)){
			$consumeService->saveUserConsumeAttribute($consume,$newAttribute);
			$users = $userService->saveUserBasic($userBasic);
			$recharge['cpipiegg'] = $newAttribute['pipiegg'];
			if($recharge['currencycode']=='USD'){
				$recharge['cbalance'] = $users['recharge_usd'];
			}elseif($recharge['currencycode']=='RMB'){
				$recharge['cbalance'] = $users['recharge'];
			}else{
				$recharge['cbalance'] = $users['recharge'];
			}
			$this->attachAttribute($userRechargeModel,$recharge);
			$userRechargeModel->save();
			$primaryKey = $userRechargeModel->getPrimaryKey();
			if($primaryKey > 0){
				$pipiRecord['uid'] = $recharge['uid'];
				$pipiRecord['record_sid'] = $primaryKey;
				$pipiRecord['source'] = SOURCE_RECHARGE;
				$pipiRecord['sub_source'] = SUBSOURCE_RECHARGE_ADDPIPIEGG;
				$pipiRecord['extra']= serialize(array('desc'=>'皮蛋充值','platform'=>$recharge['rsource']));
				$pipiRecord['pipiegg'] = $pipiegg;
				$pipiRecord['client'] = CLIENT_RECHARGE ;
				$pipiRecord['ip_address'] = $recharge['rip'];
				$pipiRecord['consume_time'] = time();
				$pipiRecord['from_target_id'] = 1;
				$pipiRecord['to_target_id'] = $recharge['ruid'] ? $recharge : 0;
				$this->saveUserPipiEggRecords($pipiRecord,1);
			}
		}
		return $primaryKey;
	}
	
	/**
	 * 判断用户是否充值
	 * @author guoshaobo
	 * @param $uid
	 * @return bool
	 */
	public function getUserRechargeEggs($uid)
	{
		if($uid <= 0){
			return $this->setError(Yii::t('common', 'Parameter is empty'),false);
		}
		$userRechargeRecordsModel = new UserRechargeRecordsModel();
		$eggs = $userRechargeRecordsModel->getUserPipiEggsByTime($uid, 1, time());
		return ($eggs > 0) ? true : false;
	}
	
	/**
	 * 获取用户的消费统计
	 * @param  $uid
	 * @return int;
	 */
	public function sumUserConsumeRecord($uid)
	{
		if($uid <= 0){
			return $this->setError(Yii::t('common', 'Parameter is empty'),0);
		}
		$userPipieggRecordModel = new UserPipiEggRecordsModel();
		$condition = array('uid'=>$uid, 'isPlus'=>false);
		$res = $userPipieggRecordModel->sumPipieggs($condition);
		if($res && isset($res['0'])){
			if($res['0']['uid']==$uid){
				return $res['0']['sum_pipiegg'];
			}
		}
		return 0;
	}
	
	public function getDisAccount($money){
		$account = array(
			10=>1,
			20=>1,
			49.5=>0.99,
			98=>0.98,
			194=>0.97,
			475=>0.95,
			950=>0.95,
			1900=>0.95,
			4750=>0.95,
			9500=>0.95,
			19000=>0.95,
		);
		return isset($account[$money]) ? $money / $account[$money] : $money;
	}
	
	public function authRrecharge($uid, $rechargeData)
	{
		if($uid <= 0 || empty($rechargeData)){
			return false;
		}
		$authRechargeModel = new UserAuthRechargeModel();
		return $authRechargeModel->authRecharge($uid, $rechargeData);
	}
}