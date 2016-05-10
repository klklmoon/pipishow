<?php
define('GIFT_PATH',dirname(dirname(dirname(dirname(__FILE__)))).DIR_SEP."images".DIR_SEP."gift".DIR_SEP);
define('GIFT_EFFECT_PATH',dirname(dirname(dirname(dirname(__FILE__)))).DIR_SEP."images".DIR_SEP."gift".DIR_SEP."effect".DIR_SEP);
define('GIFT_BUY',0); //直播间直接购买礼物
define('GIFT_TYPE_MAIN',1); //主站礼物
define('GIFT_TYPE_GAME',2); //游戏礼物
define('GIFT_TYPE_SHOP',4); //商城礼物
define('GIFT_TYPE_LUCK',8); //幸运礼物
define('GIFT_TYPE_TRUCK',16); //跑道礼物
define('GIFT_SEND_WORD','加油，让我们一起飞！'); //礼物寄语
/**
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: GiftService.php 16955 2013-12-18 02:46:10Z leiwei $ 
 * @package 
 */

class GiftService  extends PipiService {
	/**
	 * 直播间直接送礼
	 * 
	 * @param int $uid          送礼uid
	 * @param int $to_uid       接受者uid
	 * @param int $archivesId   所在直播间档期Id
	 * @param int $giftId       礼物id
	 * @param int $num          礼物数量
	 * @param string $remark    礼物寄语
	 */
	public function sendGift($uid, $to_uid, $archivesId, $giftId, $num,$remark=null) {
		if ($uid <= 0 || $to_uid <= 0 || $archivesId <= 0 || $giftId <= 0 || $num <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is error'), 0);
		}
		$gift = $this->getGiftByIds($giftId);
		$giftPrice = $gift[$giftId]['pipiegg'] * $num;
		$consumeService = new ConsumeService();
		$consume = $consumeService->consumeEggs($uid, $giftPrice);
		if ($consume <= 0) {
			return $this->setError(Yii::t('common', 'Pipiegg not enough'), 0);
		}
		if($remark){
			$remark=htmlspecialchars($remark,ENT_QUOTES);
		}
		$userService = new UserService();
		$userBasic = $userService->getUserBasicByUids(array($uid, $to_uid));
		$giftEffect = $this->getGiftEffectByNum($giftId, $num);
		//皮蛋扣成功后就发zmq
		$zmq=$this->getZmq();
		$eventData['archives_id']=$archivesId;
		$eventData['domain']=DOMAIN;
		$luckGiftService=new LuckyGiftService();
		if($this->hasBit(intval($gift[$giftId]['gift_type']), GIFT_TYPE_LUCK)){
			$luckGiftService->saveGiftPoolRecord($giftPrice/2,true);
			$award=$luckGiftService->getLuckyGiftAward($giftId,$num);
			$json_content['gift_type']=SUBSOURCE_LUCK_GIFT_AWARD;
			$json_content['gift_award']=$award;
		}elseif($this->hasBit(intval($gift[$giftId]['gift_type']), GIFT_TYPE_TRUCK)){
			$json_content['gift_type']='truckGifts';
			$json_content['remark']=$remark?$remark:GIFT_SEND_WORD;
			$truckGiftService=new TruckGiftService();
			$truckGift=$truckGiftService->getTruckGiftRecord();
			$truckRecord['uid']=$uid;
			$truckRecord['nickname']=str_replace('|', '',$userBasic[$uid]['nickname']);
			$truckRecord['to_uid']=$to_uid;
			$truckRecord['to_nickname']=str_replace('|', '',$userBasic[$to_uid]['nickname']);
			$truckRecord['zh_description']=$gift[$giftId]['zh_name'];
			$truckRecord['pipiegg']=$giftPrice;
			$truckRecord['num']=$num;
			$truckRecord['picture']=$gift[$giftId]['image'];
			$truckRecord['remark']=$remark?$remark:GIFT_SEND_WORD;
			if($truckGift){
				if($giftPrice>=$truckGift['pipiegg']){
					$json_content['org_nickname']=str_replace('|', '',$truckGift[$to_uid]['nickname']);
					$json_content['replace']=1;
					$truckRecord['replace']=1;
					$truckGiftService->saveTruckGiftRecord($truckRecord);
				}else{
					$truckRecord['replace']=0;
					$json_content['replace']=0;
				}
			}else{
				$json_content['replace']=1;
				$truckRecord['replace']=1;
				$truckGiftService->saveTruckGiftRecord($truckRecord);
			}
		}
		$json_content['uid']=$uid;
		$json_content['nickname']=empty($userBasic[$uid]['nickname']) ? $userBasic[$uid]['username'] : str_replace('|', '',$userBasic[$uid]['nickname']);
		$json_content['to_uid']=$to_uid;
		$json_content['to_nickname']=empty($userBasic[$to_uid]['nickname']) ? $userBasic[$to_uid]['username'] : str_replace('|', '',$userBasic[$to_uid]['nickname']);
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByArchivesId($archivesId);
		$json_content['dotey_uid']=$archives['uid'];
		$doteyBasic=$userService->getUserBasicByUids(array($archives['uid']));
		$json_content['dotey_nickname']=str_replace('|', '',$doteyBasic[$archives['uid']]['nickname']);
		$json_content['zh_description']=$gift[$giftId]['zh_name'];
		$archivesService=new ArchivesService();
		$liveRecord=$archivesService->getLiveRecordByArchivesIds($archivesId);
		$liveRecord=array_pop($liveRecord);
		$json_content['avatar']=$userService->getUserAvatar($to_uid,'small');
		$json_content['record_id']=$liveRecord['record_id'];
		$json_content['charm']=$gift[$giftId]['charm']*$num;
		$json_content['dedication']=$gift[$giftId]['dedication']*$num;
		$json_content['pipiegg']=$gift[$giftId]['pipiegg']*$num;
		$json_content['gift_num']=$num;
		$json_content['picture']=$gift[$giftId]['image'];
		$json_content['type']=$giftEffect['effect_type'];
		$json_content['position']=$giftEffect['position'];
		$json_content['flash']=$giftEffect['effect'];
		$json_content['timeout']=$giftEffect['timeout'];
		$json_content['time']=date('H:i');
		$json_content['new_time']=time();
		if($userService->hasBit(intval($userBasic[$to_uid]['user_type']),USER_TYPE_DOTEY)&&$userBasic[$to_uid]['user_status']==USER_STATUS_ON){
			$doteyList=$archivesService->getArchivesUserByArchivesIds($archivesId);
			$dotey=array();
			foreach($doteyList[$archivesId] as $row){
				$dotey[]=$row['uid'];
			}
			if(in_array($to_uid,$dotey)){
				if($this->hasBit(intval($gift[$giftId]['gift_type']), GIFT_TYPE_TRUCK)){
					$records['remark'] = $remark?$remark:GIFT_SEND_WORD;
				}
				$records['recevier_type']=0;
				$json_content['recevier_type']=0;
				$pipieggRecords['source']=SOURCE_GIFTS;
				$dedicationRecords['source']=SOURCE_GIFTS;
				$charmRecords['source']=SOURCE_GIFTS;
				$charmPointsRecords['source']=SOURCE_GIFTS;
			}else{
				$records['recevier_type']=1;
				$json_content['recevier_type']=1;
				$pipieggRecords['source']=SOURCE_USERGIFTS;
				$dedicationRecords['source']=SOURCE_USERGIFTS;
				$charmRecords['source']=SOURCE_USERGIFTS;
				$charmPointsRecords['source']=SOURCE_USERGIFTS;
			}
			
			$charmRecords['uid'] = $to_uid;
			$charmRecords['sender_uid'] = $uid;
			$charmRecords['target_id'] = $archivesId;
			$charmRecords['charm'] = $gift[$giftId]['charm'] * $num;
			$charmRecords['num'] = $num;
			$charmRecords['sub_source']=SUBSOURCE_GIFTS_BUY;
			$charmRecords['info']=$gift[$giftId]['zh_name'].'x'.$num;
			
			$charmPointsRecords['uid'] = $to_uid;
			$charmPointsRecords['sender_uid'] = $uid;
			$charmPointsRecords['target_id'] = $archivesId;
			$charmPointsRecords['charm_points'] = $gift[$giftId]['charm_points'] * $num;
			$charmPointsRecords['num'] = $num;
			$charmPointsRecords['sub_source']=SUBSOURCE_GIFTS_BUY;
			$charmPointsRecords['info']=$gift[$giftId]['zh_name'].'x'.$num;
			
		}else{
			$records['recevier_type']=1;
			$json_content['recevier_type']=1;
			$pipieggRecords['source']=SOURCE_USERGIFTS;
			$dedicationRecords['source']=SOURCE_USERGIFTS;
			
			$eggPointsRecords['uid'] = $to_uid;
			$eggPointsRecords['sender_uid'] = $uid;
			$eggPointsRecords['target_id'] = $archivesId;
			$eggPointsRecords['egg_points'] = $gift[$giftId]['egg_points'] * $num;
			$eggPointsRecords['num'] = $num;
			$eggPointsRecords['source']=SOURCE_USERGIFTS;
			$eggPointsRecords['sub_source']=SUBSOURCE_GIFTS_BUY;
			$eggPointsRecords['info']=$gift[$giftId]['zh_name'].'x'.$num;
			
		}
		$eventData['json_content']=$json_content;
		$zmq->sendZmqMsg(608,$eventData);
		
		$records['uid'] = $uid;
		$records['to_uid'] = $to_uid;
		$records['target_id']=$archivesId;
		$records['gift_id'] = $giftId;
		$records['num'] = $num;
		$records['gift_type'] =GIFT_BUY;
		$records['record_sid'] = 0;
		$records['sender'] = empty($userBasic[$uid]['nickname']) ? $userBasic[$uid]['username'] : $userBasic[$uid]['nickname'];
		$records['receiver'] = empty($userBasic[$to_uid]['nickname']) ? $userBasic[$to_uid]['username'] : $userBasic[$to_uid]['nickname'];
		$records['position'] = $giftEffect['position'];
		$records['timeout'] = $giftEffect['timeout'];
		
		//写入皮蛋log
		$pipieggRecords['uid'] = $uid;
		$pipieggRecords['pipiegg'] = $giftPrice;
		$pipieggRecords['from_target_id'] = $giftId;
		$pipieggRecords['num'] = $num;
		$pipieggRecords['to_target_id'] = $archivesId;
		$pipieggRecords['sub_source']=SUBSOURCE_GIFTS_BUY;
		$pipieggRecords['extra']=$gift[$giftId]['zh_name'].'x'.$num;
		
		//写入用户贡献值记录
		$dedicationRecords['uid'] = $uid;
		$dedicationRecords['dedication'] = $gift[$giftId]['dedication'] * $num;
		$dedicationRecords['num'] = $num;
		$dedicationRecords['from_target_id'] = $giftId;
		$dedicationRecords['to_target_id'] = $archivesId;
		$dedicationRecords['sub_source']=SUBSOURCE_GIFTS_BUY;
		$dedicationRecords['info']=$gift[$giftId]['zh_name'].'x'.$num;
		
		try{
			$giftRecords = $this->saveUserGiftRecords($records);
		}catch (Exception $e){
			$zmqData['type'] ='save_user_consume_records';
			$zmqData['uid'] = $uid;
			$records['step']=1;
			$records['suid']=$uid;
			unset($records['uid']);
			$zmqData['json_info'] = $records;
			$seq=$zmq->sendZmqMsg(609, $zmqData);
			$zmqData_pipiegg['type'] ='save_user_consume_records';
			$zmqData_pipiegg['uid'] = $uid;
			$pipieggRecords['seq']=$seq;
			$pipieggRecords['step']=2;
			$pipieggRecords['suid']=$uid;
			unset($pipieggRecords['uid']);
			$pipieggRecords['ip_address'] = Yii::app()->request->userHostAddress;
			$zmqData_pipiegg['json_info'] = $pipieggRecords;
			$zmq->sendZmqMsg(609, $zmqData_pipiegg);
			$zmqData_dedication['type'] ='save_user_consume_records';
			$zmqData_dedication['uid'] = $uid;
			$dedicationRecords['seq']=$seq;
			$dedicationRecords['step']=3;
			$dedicationRecords['suid']=$uid;
			unset($dedicationRecords['uid']);
			$zmqData_dedication['json_info'] =$dedicationRecords;
			$zmq->sendZmqMsg(609, $zmqData_dedication);
			if($userService->hasBit(intval($userBasic[$to_uid]['user_type']),USER_TYPE_DOTEY)&&$userBasic[$to_uid]['user_status']==USER_STATUS_ON){
				$zmqData_charm['type'] ='save_user_consume_records';
				$zmqData_charm['uid'] = $uid;
				$charmRecords['seq']=$seq;
				$charmRecords['step']=4;
				$charmRecords['suid']=$to_uid;
				unset($charmRecords['uid']);
				$zmqData_charm['json_info'] = $charmRecords;
				$zmq->sendZmqMsg(609, $zmqData_charm);
				$zmqData_charm_points['type'] ='save_user_consume_records';
				$zmqData_charm_points['uid'] = $uid;
				$charmPointsRecords['seq']=$seq;
				$charmPointsRecords['step']=5;
				$charmPointsRecords['suid']=$to_uid;
				unset($charmPointsRecords['uid']);
				$zmqData_charm_points['json_info'] = $charmPointsRecords;
				$zmq->sendZmqMsg(609, $zmqData_charm_points);
			}else{
				$zmqData_egg['type'] ='save_user_consume_records';
				$zmqData_egg['uid'] = $uid;
				$eggPointsRecords['seq']=$seq;
				$eggPointsRecords['step']=6;
				$eggPointsRecords['suid']=$to_uid;
				unset($eggPointsRecords['uid']);
				$zmqData_egg['json_info'] = $eggPointsRecords;
				$zmq->sendZmqMsg(609, $zmqData_egg);
			}
			$error=$e->getMessage();
			$filename = DATA_PATH.'runtimes/user_consume_records_exception.txt';
			error_log(date("Y-m-d H:i:s")."存储用户礼物记录异常：".$error.",异常送礼:".json_encode($zmqData['json_info'])."\n\r",3,$filename);
			return true;
			exit;
		}
		$pipieggRecords['record_sid'] = $giftRecords;
		try{
			$consumeService->saveUserPipiEggRecords($pipieggRecords, false);
		}catch (Exception $e){
			$zmqData['type'] ='save_user_consume_records';
			$zmqData['uid'] = $uid;
			$pipieggRecords['step']=2;
			$pipieggRecords['suid']=$uid;
			$pipieggRecords['ip_address'] = Yii::app()->request->userHostAddress;
			unset($pipieggRecords['uid']);
			$zmqData['json_info'] = $pipieggRecords;
			$zmq->sendZmqMsg(609, $zmqData);
			$error=$e->getMessage();
			$filename = DATA_PATH.'runtimes/user_consume_records_exception.txt';
			error_log(date("Y-m-d H:i:s")."存储用户皮蛋记录异常：".$error.",异常送礼:".json_encode($zmqData['json_info'])."\n\r",3,$filename);
		}
		$dedicationRecords['record_sid'] = $giftRecords;
		try{
			$consumeService->saveUserDedicationRecords($dedicationRecords, true);
		}catch (Exception $e){
			$zmqData['type'] ='save_user_consume_records';
			$zmqData['uid'] = $uid;
			$dedicationRecords['step']=3;
			$dedicationRecords['suid']=$uid;
			unset($dedicationRecords['uid']);
			$zmqData['json_info'] = $dedicationRecords;
			$zmq->sendZmqMsg(609, $zmqData);
			$error=$e->getMessage();
			$filename = DATA_PATH.'runtimes/user_consume_records_exception.txt';
			error_log(date("Y-m-d H:i:s")."存储用户贡献值记录异常:".$error.",异常送礼:".json_encode($zmqData['json_info'])."\n\r",3,$filename);
		}
		try{
			$consumeService->saveUserConsumeAttribute(array('uid'=>$uid,'archives_id'=>$archivesId,'dedication'=>$dedicationRecords['dedication'],'pipiegg'=>$pipieggRecords['pipiegg']));
		}catch (Exception $e){
			$error=$e->getMessage();
			$filename = DATA_PATH.'runtimes/user_attribute_exception.txt';
			error_log(date("Y-m-d H:i:s").'新增用户贡献值异常：'.$error."\n\r",3,$filename);
		}
		if($userService->hasBit(intval($userBasic[$to_uid]['user_type']),USER_TYPE_DOTEY)&&$userBasic[$to_uid]['user_status']==USER_STATUS_ON){
			//写入主播魅力值记录
			$charmRecords['record_sid'] = $giftRecords;
			try{
				$consumeService->saveDoteyCharmRecords($charmRecords, true);
			}catch (Exception $e){
				$zmqData['type'] ='save_user_consume_records';
				$zmqData['uid'] = $uid;
				$charmRecords['step']=4;
				$charmRecords['suid']=$to_uid;
				unset($charmRecords['uid']);
				$zmqData['json_info'] = $charmRecords;
				$zmq->sendZmqMsg(609, $zmqData);
				$error=$e->getMessage();
				$filename = DATA_PATH.'runtimes/user_consume_records_exception.txt';
				error_log(date("Y-m-d H:i:s").'存储主播魅力值记录异常：'.$error.",异常送礼:".json_encode($zmqData['json_info'])."\n\r",3,$filename);
			}
			//写入主播魅力点记录
			$charmPointsRecords['record_sid'] = $giftRecords;
			try{
				$consumeService->saveDoteyCharmPointsRecords($charmPointsRecords, true);
			}catch (Exception $e){
				$zmqData['type'] ='save_user_consume_records';
				$zmqData['uid'] = $uid;
				$charmPointsRecords['step']=5;
				$charmPointsRecords['suid']=$to_uid;
				unset($charmPointsRecords['uid']);
				$zmqData['json_info'] =$charmPointsRecords;
				$zmq->sendZmqMsg(609, $zmqData);
				$error=$e->getMessage();
				$filename = DATA_PATH.'runtimes/user_consume_records_exception.txt';
				error_log(date("Y-m-d H:i:s")."存储主播魅力点记录异常：".$error.",异常送礼:".json_encode($zmqData['json_info'])."\n\r",3,$filename);
			}
			try{
				$consumeService->saveUserConsumeAttribute(array('uid'=>$to_uid,'archives_id'=>$archivesId,'charm_points'=>$charmPointsRecords['charm_points'],'charm'=>$charmRecords['charm']));
			}catch (Exception $e){
				$error=$e->getMessage();
				$filename = DATA_PATH.'runtimes/user_attribute_exception.txt';
				error_log(date("Y-m-d H:i:s").'新增用户魅力值和魅力点异常：'.$error."\n\r",3,$filename);
			}
		}else{
			$eggPointsRecords['record_sid'] = $giftRecords;
			try{
				$consumeService->saveUserEggPointsRecords($eggPointsRecords, true);
			}catch (Exception $e){
				$zmqData['type'] ='save_user_consume_records';
				$zmqData['uid'] = $uid;
				$eggPointsRecords['step']=6;
				$eggPointsRecords['suid']=$to_uid;
				unset($eggPointsRecords['uid']);
				$zmqData['json_info'] =$eggPointsRecords;
				$zmq->sendZmqMsg(609, $zmqData);
				$error=$e->getMessage();
				$filename = DATA_PATH.'runtimes/user_consume_records_exception.txt';
				error_log(date("Y-m-d H:i:s").'存储用户皮点记录异常：'.$error.",异常送礼:".json_encode($zmqData['json_info'])."\n\r",3,$filename);
			}
			try{
				$consumeService->saveUserConsumeAttribute(array('uid'=>$to_uid,'archives_id'=>$archivesId,'egg_points'=>$eggPointsRecords['egg_points']));
			}catch (Exception $e){
				$error=$e->getMessage();
				$filename = DATA_PATH.'runtimes/user_attribute_exception.txt';
				error_log(date("Y-m-d H:i:s").'新增用户皮点异常：'.$error."\n\r",3,$filename);
			}	
		}
		//幸运礼物参与中奖
		if($this->hasBit(intval($gift[$giftId]['gift_type']), GIFT_TYPE_LUCK)){
			if($award){
				$luckRecord['uid']=$uid;
				$luckRecord['archives_id']=$archivesId;
				$luckRecord['record_sid']=$giftRecords;
				$luckRecord['source']=$dedicationRecords['source'];
				$luckRecord['sub_source']=SUBSOURCE_LUCK_GIFTS_BUY;
				$luckGiftService->sendGiftAward($luckRecord, $award);
			}
		}
		//存储上一次送的时间
		self::saveLastSendGiftTime($uid);
		return true;
	}
	
	
	/**
	 * 存储礼物分类信息
	 * @param array $category
	 * @return int
	 */
	public function saveGiftCategory(array $category) {
		if (isset($category['category_id']) && $category['category_id'] <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
		$giftCategoryModel = $this->getGiftCategoryModel();
		if (isset($category['category_id'])) {
			$orggiftCategoryModel = $giftCategoryModel->findByPk($category['category_id']);
			if (empty($orggiftCategoryModel)) {
				return $this->setNotice('gift_category', Yii::t('gift_category', 'The gift category does not exist'), 0);
			}
			$this->attachAttribute($orggiftCategoryModel, $category);
			if (!$orggiftCategoryModel->validate()) {
				return $this->setNotices($orggiftCategoryModel->getErrors(), array());
			}
			$orggiftCategoryModel->save();
			$insertId = $category['category_id'];
		} else {
			$this->attachAttribute($giftCategoryModel, $category);
			if (!$giftCategoryModel->validate()) {
				return $this->setNotices($giftCategoryModel->getErrors(), array());
			}
			$giftCategoryModel->save();
			$insertId = $giftCategoryModel->getPrimaryKey();
		}
	
		if ($insertId && $this->isAdminAccessCtl()){
			if (isset($category['category_id'])) {
				$op_desc = '编辑 礼物分类('.$insertId.')';
			}else{
				$op_desc = '新增 礼物分类('.$insertId.')';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $insertId;
	}
	
	
	/**
	 * 存储礼物信息
	 * @param array $gift 礼物信息
	 * @param boolean $has_effect 0->没有特效，1->有特效
	 * @param array $effect 特效信息
	 * @return int
	 */
	public function saveGift(array $gift, $has_effect = false, array $effect = array()) {
		if (isset($gift['gift_id']) && $gift['gift_id'] <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
		if ($has_effect && empty($effect)) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
		if(is_array($gift['gift_type'])){
			$gift_type=0;
			foreach($gift['gift_type'] as $type){
				$gift_type=$this->grantBit(intval($gift_type),intval($type));
			}
		}else{
			$gift_type=$gift['gift_type'];
		}
		$gift['gift_type'] = empty($gift_type) ? 1 : $gift_type;
		if(is_array($gift['shop_type'])){
			$shop_type=0;
			foreach($gift['shop_type'] as $_type){
				$shop_type=$this->grantBit(intval($shop_type),intval($_type));
			}
		}else{
			$shop_type=$gift['shop_type'];
		}
		$gift['shop_type'] = empty($shop_type) ? 1 : $shop_type;
		$gift['is_display'] = empty($gift['is_display']) ? 0 : 1;
		$gift['update_time'] = time();
		$giftModel = $this->getGiftModel();
		if (isset($gift['gift_id'])) {
			$orggiftModel = $giftModel->findByPk($gift['gift_id']);
			if (empty($orggiftModel)) {
				return $this->setNotice('gift', Yii::t('gift', 'The gift does not exist'), 0);
			}
			if(isset($gift['image'])){
				$this->delGiftFile($orggiftModel->attributes['image']);
			}
			$this->attachAttribute($orggiftModel, $gift);
			if (!$orggiftModel->validate()) {
				return $this->setNotices($orggiftModel->getErrors(), array());
			}
				
			$orggiftModel->save();
			$insertId = $gift['gift_id'];
		} else {
			$this->attachAttribute($giftModel, $gift);
			if (!$giftModel->validate()) {
				return $this->setNotices($giftModel->getErrors(), array());
			}
			$giftModel->save();
			$insertId = $giftModel->getPrimaryKey();
		}
		if ($insertId && $has_effect) {
			if($this->getArrayDim($effect)==1){
				$effect=array($effect);
			}
			$this->batchsaveGiftEffect($insertId,$effect);
			//存入redis
			$this->saveFrontGiftList();
		}
	
		if($insertId && $this->isAdminAccessCtl()){
			if (isset($gift['gift_id'])) {
				$op_desc = '编辑礼物('.$insertId.')';
			}else{
				$op_desc = '新增礼物('.$insertId.')';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $insertId;
	}
	
	/**
	 * 修改礼物特效
	 * @param array $effect
	 * @return boolean
	 */
	public function saveGiftEffect(array $effect) {
		if (isset($effect['effect_id']) && $effect['effect_id'] <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
		$giftEffectModel = $this->getGiftEffectModel();
		$effect['position'] =empty($effect['position'])?1:$effect['position'];
		$effect['effect_type'] = empty($effect['effect_type']) ? 1 : $effect['effect_type'];
		if (isset($effect['effect_id'])) {
			$orggiftEffectModel = $giftEffectModel->findByPk($effect['effect_id']);
			if (empty($orggiftEffectModel)) {
				return $this->setNotice('gift_effect', Yii::t('gift_effect', 'The gift effect does not exist'), 0);
			}
			if(isset($effect['effect'])){
				$this->delGiftEffectFile($orggiftEffectModel->attributes['effect'],$effect['effect']);
			}
			$this->attachAttribute($orggiftEffectModel, $effect);
			if (!$orggiftEffectModel->validate()) {
				return $this->setNotices($orggiftEffectModel->getErrors(), array());
			}
			$orggiftEffectModel->save();
			$insertId = $effect['effect_id'];
		} else {
			$this->attachAttribute($giftEffectModel, $effect);
			if (!$giftEffectModel->validate()) {
				return $this->setNotices($giftEffectModel->getErrors(), array());
			}
			$giftEffectModel->save();
			$insertId = $giftEffectModel->getPrimaryKey();
		}
		if($insertId){
			//存入redis
			$this->saveFrontGiftList();
			if($this->isAdminAccessCtl()){
				if (isset($effect['effect_id'])) {
					$op_desc = '修改礼物效果('.$insertId.')';
				}else{
					$op_desc = '新增礼物效果('.$insertId.')';
				}
				$this->saveAdminOpLog($op_desc);
			}
		}
		return $insertId;
	}
	
	
	/**
	 * 存储用户送礼记录
	 * @param array $records 送礼记录
	 * @param array $gift  礼物信息
	 * @return int
	 */
	public function saveUserGiftRecords(array $records, array $gift = array()) {
		if ($records['uid'] <= 0 || $records['to_uid'] <= 0 || $records['gift_id'] <= 0) return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		$records['gift_type']=empty($records['gift_type'])?0:$records['gift_type'];
		if($records['num']<=0){
			return $this->setError(Yii::t('common', 'gift_num is error'), 0);
		}
		$uid = $records['uid'];
		$toUids = $records['to_uid'];
		$toUids = is_array($toUids) ? $toUids : array($toUids);
		$records['to_uid'] = implode(',', $toUids);
		if (empty($gift)) {
			$gift = $this->getGiftByIds($records['gift_id']);
			if (empty($gift)) return $this->setError(Yii::t('common', 'Gift is not exist'), 0);
			$gift = $gift[$records['gift_id']];
		}
		$records['pipiegg'] = $gift['pipiegg'] * $records['num'];
		$records['charm'] = $gift['charm'] * $records['num'];
		$records['charm_points'] = $gift['charm_points'] * $records['num'];
		$records['egg_points'] = $gift['egg_points'] * $records['num'];
		$records['dedication'] = $gift['dedication'] * $records['num'];
		if (!isset($records['info'])) {
			$records['info'] = array();
		}
		if (!isset($records['info']['sender'])) $records['info']['sender'] = $this->array_get($records, 'sender');
		if (!isset($records['info']['receiver'])) $records['info']['receiver'] = $this->array_get($records, 'receiver');
		if (!isset($records['info']['position'])) $records['info']['position'] =$this->array_get($records, 'position');
		if (!isset($records['info']['picture'])) $records['info']['gift_image'] = $gift['image'];
		if (!isset($records['info']['timeout'])) $records['info']['timeout'] =$this->array_get($records, 'timeout');
		if (!isset($records['info']['remark'])) $records['info']['remark'] =$this->array_get($records, 'remark');
	
		$records['info']['gift_zh_name'] = $gift['zh_name'];
		$records['info'] = serialize($records['info']);
		$records['create_time'] = time();
		$userGiftSendRecordsModel = $this->UserGiftSendRecordsModel();
		$this->attachAttribute($userGiftSendRecordsModel, $records);
		if (!$userGiftSendRecordsModel->validate()) {
			return $this->setNotices($userGiftSendRecordsModel->getErrors(), 0);
		}
		$giftRecord=$userGiftSendRecordsModel->save();
	
		if ($giftRecord) {
			$recordId = $userGiftSendRecordsModel->getPrimaryKey();
			$relation = array();
			foreach ($toUids as $key => $toUid) {
				$relation[$key]['record_id'] = $recordId;
				$relation[$key]['uid'] = $toUid;
				$relation[$key]['create_time'] = time();
				$relation[$key]['is_onwer'] = 0;
			}
			$relation[]=array('record_id'=>$recordId,'uid'=>$uid,'is_onwer'=>1,'create_time'=>time());
			$this->saveUserGiftSendRelationRecords($relation);
		} else {
			return $this->setNotices($userGiftSendRecordsModel->getErrors(), 0);
		}
		return $recordId;
	}
	
	/**
	 * 存储用户送礼关系记录
	 * @param array $relations
	 * @return int
	 */
	public function saveUserGiftSendRelationRecords(array $relations) {
		if (empty($relations)) return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		$userGiftSendRelationRecordsModel = $this->UserGiftSendRelationRecordsModel();
		return $userGiftSendRelationRecordsModel->batchInsert($relations);
	}
	
	/**
	 * 存储最后一次赠送礼物的时间戳
	 * @param int $uid  用户的uid
	 * @return boolean 1->成功 0->失败
	 * 
	 */
	public function saveLastSendGiftTime($uid){
		if($uid<=0)
			return $this->setError(Yii::t('common', 'Parameter is empty'),0);
		$userService=new UserService();
		$userBasic=$userService->getUserBasicByUids(array($uid));
		$userLoginRedisModel=new UserLoginRedisModel();
		$userLogin=$userLoginRedisModel->getUserBasicByUserNames(array($userBasic[$uid]['username']));
		$userLogin=array_pop($userLogin);
		$userLogin['last_send_gift_time']=time();
		return $userLoginRedisModel->saveUserBasicToRedis($userBasic[$uid]['username'], $userLogin);
	}
	
	/**
	 * 获取某一用户在指定时间段内花费的皮蛋总数
	 *
	 * @param int $uid
	 * @param int $startTime
	 * @param int $endTime
	 * @return int
	 */
	public function  sumUserConsumePipieggsByTime($uid,$startTime,$endTime){
		if($uid <=0 || $startTime <=0 || $endTime <=0){
			return $this->setError(Yii::t('common', 'Parameter is empty'),0);
		}
		$userGiftSendModel = new UserGiftSendRecordsModel();
		return $userGiftSendModel->sumUserConsumePipieggsByTime($uid,$startTime,$endTime);
	}
	
	/**
	 * 通过目标ID统计送礼
	 *
	 * @author supeng
	 * @param array $targetIds
	 * @param array $condition
	 */
	public function searchGiftRecordsByTargetIds(Array $targetIds,Array $condition = array()){
		if (empty($targetIds) || !is_array($targetIds)){
			return $this->setError(Yii::t('common', 'Parameters is Wrong!'),false);
		}
		$giftRecordsModel = new UserGiftSendRecordsModel();
		return $giftRecordsModel->searchGiftRecordsByTargetIds($targetIds,$condition);
	}
	
	/**
	 * 通过UID统计收礼收入
	 *
	 * @author supeng
	 * @param array $uids
	 * @param array $condition
	 * @return mix|mixed
	 */
	public function searchGiftRecordsByUids(Array $uids,Array $condition = array()){
		if (empty($uids) || !is_array($uids)){
			return $this->setError(Yii::t('common', 'Parameters is Wrong!'),false);
		}
		$giftRecordsModel = new UserGiftSendRecordsModel();
		return $this->buildDataByIndex($giftRecordsModel->searchGiftRecordsByUids($uids,$condition), 'uid');
	}
	
	/**
	 * 批量添加礼物效果
	 * @param int $giftId 礼物Id
	 * @param array $effects 礼物效果
	 * @return mix|boolean
	 */
	public function batchSaveGiftEffect($giftId,array $effects){
		if($giftId <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$giftEffectModel = new GiftEffectModel();
		$orgGiftEffect = $this->getGiftEffectByGiftIds($giftId);
		$orgGiftEffect=array_pop($orgGiftEffect);
		$newData = array();
		if(!$orgGiftEffect){
			foreach($effects as $key=>$value){
				$newData[$key]['gift_id']=$giftId;
				$newData[$key]['effect_type']=empty($value['effect_type'])?1:$value['effect_type'];
				$newData[$key]['num']=empty($value['num'])?1:$value['num'];
				$newData[$key]['timeout']=$value['timeout'];
				$newData[$key]['position']=empty($value['position'])?1:$value['position'];
				$newData[$key]['effect']=$value['effect'];
			}
			if(!$newData){
				return  false;
			}
			$giftEffectModel->batchInsert($newData);
		}else{
			foreach($effects as $key=>$value){
				foreach($orgGiftEffect as $_orgGiftEffect){
					if(isset($value['effect_id'])){
						if($_orgGiftEffect['effect_id'] == $value['effect_id']){
							$upData = array();
							$upData = array_merge($_orgGiftEffect,$value);
							$this->saveGiftEffect($upData);
						}
					}else{
						$newData[$key]['gift_id']=$giftId;
						$newData[$key]['effect_type']=empty($value['effect_type'])?1:$value['effect_type'];
						$newData[$key]['num']=empty($value['num'])?1:$value['num'];
						$newData[$key]['timeout']=empty($value['timeout'])?1:$value['timeout'];
						$newData[$key]['position']=empty($value['position'])?1:$value['position'];
						$newData[$key]['effect']=$value['effect'];
					}
				}
			}
				
			if($newData){
				$giftEffectModel->batchInsert($newData);
			}
		}
		//存入redis
		$this->saveFrontGiftList();
		return true;
	}
	


	

	/**
	 * 删除礼物分类
	 * @param array|int  $catIds
	 * @return int
	 */
	public function delGiftCategoryByCatIds($catIds) {
		if (empty($catIds)) return array();
		if (!is_array($catIds) && !is_numeric($catIds)) return $this->setError(Yii::t('common', 'Parameter is error'), 0);
		$catIds = is_array($catIds) ? $catIds : array($catIds);
		$giftCategoryModel = $this->getGiftCategoryModel();
		$flag = $giftCategoryModel->delGiftCategoryByCatIds($catIds);
		if ($flag && $this->isAdminAccessCtl()){
			$this->saveAdminOpLog('删除礼物分类('.implode(',', $catIds).')');
		}
		return $flag;
	}

	
	
	
	
	
	/**
	 * 根据礼物id删除礼物
	 * @param int $giftId 礼物id
	 * @return int
	 */
	public function delGiftByGiftId($giftId){
		if (empty($giftId)) return false;
		$giftModel=new GiftModel();
		$gift=array();
		$gift['gift_id']=$giftId;
		$gift['is_display']=2;
		$giftModel = $giftModel->findByPk($giftId);
		if(!$giftModel){
			return false;
		}	
		$this->attachAttribute($giftModel, $gift);
		$insertId=$giftModel->save();
		if($insertId){
			$this->saveFrontGiftList();
			if($this->isAdminAccessCtl()){
				$this->saveAdminOpLog('删除礼物('.$giftId.')');
			}
		}
		return $insertId;
	}

	/**
	 * 删除礼物特效
	 * @param array $effectIds
	 * @return boolean
	 */
	public function delGiftEffectByEffectIds(array $effectIds) {
		if (empty($effectIds)) return false;
		$effectIds = is_array($effectIds) ? $effectIds : array($effectIds);
		$giftEffectModel = $this->getGiftEffectModel();
		$giftEffect=$this->getGiftEffectByEffectIds($effectIds);
		foreach($giftEffect as $effect){
			$this->delGiftEffectFile($effect['effect']);
		}
		$this->saveFrontGiftList();
		$flag = $giftEffectModel->delGiftEffectByEffectIds($effectIds);
		if($flag && $this->isAdminAccessCtl()){
			$this->saveAdminOpLog('删除 礼物效果('.implode(',',$effectIds).')');
		}
		return $flag;
	}

	/**
	 * 根据礼物Id删除礼物效果
	 * @param array|int $giftIds  礼物Id
	 * @return boolean
	 */
	public function delGiftEffectByGiftIds($giftIds) {
		if (empty($giftIds)) return false;
		$giftIds = is_array($giftIds) ? $giftIds : array($giftIds);
		$giftEffectModel = $this->getGiftEffectModel();
		$giftEffect=$this->getGiftEffectByGiftIds($giftIds);
		foreach($giftEffect as $gift){
			foreach($gift as $effect){
				$this->delGiftEffectFile($effect['effect']);
			}
		}
		$this->saveFrontGiftList();
		return $giftEffectModel->delGiftEffectByGiftIds($giftIds);
	}
	
	/**
	 * 删除礼物图片
	 * @param string $filename
	 * @param boolen
	 */
	public function delGiftFile($filename){
		if(file_exists(GIFT_PATH.$filename)){
			return @unlink(GIFT_PATH.$filename);
		}else{
			return false;
		}
	}
	
	/**
	 * 删除礼物效果图片
	 * @param string $filename
	 * @param boolen
	 */
	public function delGiftEffectFile($oldFilename,$newFilename = ''){
		if($newFilename != $oldFilename){
			if(file_exists(GIFT_EFFECT_PATH.$oldFilename)){
				return @unlink(GIFT_EFFECT_PATH.$oldFilename);
			}
		}
		return false;
	}
	
	


	public function reduceGiftSellNum($giftId,$sell_num=-1){
		if (empty($giftId)){
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
		$giftModel=new GiftModel();
		return $giftModel->updateGiftSellNum($giftId,$sell_num);
	}
	
	/**
	 * 获取礼物分类
	 * @return array
	 */
	public function getGiftCategory() {
		$giftCategoryModel = $this->getGiftCategoryModel();
		$data = $giftCategoryModel->findAll();
		$list = $this->arToArray($data);
		return $this->buildDataByIndex($list, 'category_id');
	}
	
	/**
	 * 根据分类ID获取分类信息
	 * @param array|int $catIds 礼物分类Id
	 * @return array
	 */
	public function getGiftCategoryByCatIds($catIds) {
		if (empty($catIds)) return array();
		if (!is_array($catIds) && !is_numeric($catIds)) return $this->setError(Yii::t('common', 'Parameter is error'), 0);
		$catIds = is_array($catIds) ? $catIds : array($catIds);
		$giftCategoryModel = $this->getGiftCategoryModel();
		$data = $giftCategoryModel->getGiftCategoryByCatIds($catIds);
		$list = $this->arToArray($data);
		return $this->buildDataByIndex($list, 'category_id');
	}
	
	

	/**
	 * 获取所有礼物
	 * @param array $condition 获取礼物条件
	 * @param boolen $has_effect 0->不获取效果，1->获取效果
	 * @return array
	 */
	public function getGiftList(array $condition=array(),$has_effect = false) {
		if (isset($condition['gift_type'])){
			$condition['gift_type'] = $this->getBitCondition((int)$condition['gift_type'],1, 16);
		}
		$giftModel = $this->getGiftModel();
		$data = $giftModel->getGiftListByCondition($condition);
		$list = $this->arToArray($data);
		$gift=$this->buildDataByIndex($list, 'gift_id');
		if ($has_effect) {
			$gift = $this->mergeGiftEffect($gift);
		}
		return $gift;
	}
	
	
	/**
	 * 前台按分类获取礼物
	 * @param array $condition 获取礼物条件
	 * @param boolen $has_effect 0->不获取效果，1->获取效果
	 * @return array
	 */
	public function getCatGiftList() {
		$data=$this->getFrontGiftList();
		$list=$this->buildDataByKey($data,'cat_id');
		$category=$this->getGiftCategory();
		$giftList[0]['category_id'] = '0';
		$giftList[0]['cat_name'] = '推荐';
		$giftList[0]['cat_enname'] = 'recommend';
		foreach ($data as $_data) {
			if($this->hasBit(intval($_data['shop_type']),8)){
				$giftList[0]['child'][] = $_data;
			}
		}
		foreach($list as $key=>$row){
			$newGift[$key]=$this->array_sort($row,'pipiegg','asc');
		}
		
		foreach ($category as $cat) {
			$giftList[$cat['category_id']] = $cat;
			if(isset($list[$cat['category_id']])){
				$giftList[$cat['category_id']]['child'] = $newGift[$cat['category_id']];
			}
		}
		
		return $giftList;
	}

	/**
	 * 根据礼物Id获取礼物
	 * @param array|int $giftIds  礼物Id
	 * @param boolen $has_effect 0->不获取效果，1->获取效果
	 * @return array
	 */
	public function getGiftByIds($giftIds, $has_effect = false) {
		if (empty($giftIds)) return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		$giftIds = is_array($giftIds) ? $giftIds : array($giftIds);
		$giftModel = $this->getGiftModel();
		$data = $giftModel->getGiftByIds($giftIds);
		$list = $this->arToArray($data);
		$gift=$this->buildDataByIndex($list, 'gift_id');
		if ($has_effect) {
			$gift = $this->mergeGiftEffect($gift);
		}
		return $gift;
	}

	/**
	 * 根据礼物分类id获取礼物
	 * @param array|int $catIds
	 * @param boolen $has_effect
	 * @return array
	 */
	public function getGiftByCatIds($catIds, $has_effect = false) {
		if (empty($catIds)) return array();
		$catIds = is_array($catIds) ? $catIds : array($catIds);
		$giftModel = $this->getGiftModel();
		$data = $giftModel->getGiftByCatIds($catIds);
		$list = $this->arToArray($data);
		$list=$this->buildDataByIndex($list, 'gift_id');
		if ($has_effect) {
			$list= $this->mergeGiftEffect($list);
		}
		$gift=$this->buildDataByKey($list, 'cat_id');
		return $gift;
	}

	/**
	 * 根据条件获取礼物的分页
	 * @param int $offset 获取页数
	 * @param int $pageSize 页数
	 * @param array $condition 
	 * @return array
	 */
	public function getGiftByCondition($offset = 0, $pageSize = 10, array $condition = array()) {
		if (isset($condition['gift_type'])){
			$condition['gift_type'] = $this->getBitCondition((int)$condition['gift_type'],1, 16);
		}
		$giftModel = $this->getGiftModel();
		$data = $giftModel->getGiftByCondition($offset, $pageSize, $condition);
		$list = $this->arToArray($data);
		$count=$giftModel->getGiftCountByCondition($condition);
		$giftList=array();
		$giftList['list']=$this->buildDataByIndex($list, 'gift_id');
		$giftList['count']=$count;
		return $giftList;
	}
	
	/**
	 * @param string $zh_name 礼物中文名称
	 * @param int $gift_type  礼物类型
	 */
	public function getGiftByGiftName($zh_name,$gift_type=GIFT_TYPE_MAIN){
		$giftModel = $this->getGiftModel();
		$data=$giftModel->getGiftListByCondition(array('zh_name'=>$zh_name));
		$list = $this->arToArray($data);
		$gift=array();
		foreach($list as $row){
			if($this->hasBit(intval($row['gift_type']),$gift_type)){
				$gift=$row;
			}
		}
		return $gift;
	}
	
	
	
	/**
	 * 根据礼物效果Id获取礼物效果
	 * @param int|array $effectIds 礼物效果Id
	 * @return array
	 */
	public function getGiftEffectByEffectIds($effectIds){
		if (empty($effectIds)) return array();
		$effectIds = is_array($effectIds) ? $effectIds : array($effectIds);
		$giftEffectModel = $this->getGiftEffectModel();
		$data = $giftEffectModel->getGiftEffectByEffectIds($effectIds);
		$list = $this->arToArray($data);
		return $this->buildDataByIndex($list, 'effect_id');
	}

	/**
	 * 根据礼物id获取礼物特效
	 * @param array|int $giftIds
	 * @return array
	 */
	public function getGiftEffectByGiftIds($giftIds) {
		if (empty($giftIds)) return array();
		$giftIds = is_array($giftIds) ? $giftIds : array($giftIds);
		$giftEffectModel = $this->getGiftEffectModel();
		$data = $giftEffectModel->getGiftEffectByGiftIds($giftIds);
		$list = $this->arToArray($data);
		return $this->buildDataByKey($list, 'gift_id');
	}

	/**
	 * 根据送礼数量获取礼物特效
	 * @param int $giftId  礼物id
	 * @param int $num 送出数量
	 * @return array
	 */
	public function getGiftEffectByNum($giftId, $num) {
		if ($giftId <= 0 || $num <= 0) return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		$giftEffectModel = new GiftEffectModel();
		$giftEffect=$giftEffectModel->getGiftEffectByNum($giftId, $num);
		return $giftEffect->attributes;
	}

	

	/**
	 * 获取用户送出的礼物记录
	 * 
	 * @author supeng
	 * @param int $uid 用户uid
	 * @param int $offset 偏移量
	 * @param int $pageSize 页码
	 * @param array $condition 其它条件
	 * @return array 返回结果集和符合条件的总数
	 */
	public function getUserGiftSendRecordsByUid($uid = null, $offset = 0, $pageSize = 10, array $condition = array(),$isLimit = true) {
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$UserService = new UserService();
			$info = $UserService->searchUserList($offset,$pageSize,$condition,false);
			if($info['uids']){
				$condition['uids'] = $info['uids'];
			}else{
				return array('count'=>0,'list'=>array(),'remDuplicateCount'=>0,'pipieggSum'=>0);
			}
		}
		$userGiftSendRecordsModel = $this->UserGiftSendRecordsModel();
		return $userGiftSendRecordsModel->getUserGiftSendRecordsByUid($uid, $offset, $pageSize, $condition,$isLimit);
	}
	
	/**
	 * 统计用户送礼
	 * 
	 * @author supeng
	 * @param unknown_type $uid
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param array $condition
	 * @param unknown_type $isLimit
	 * @return Ambigous <multitype:multitype:, multitype:multitype: number NULL string Ambigous <string, unknown, mixed> >
	 */
	public function getUserGiftStatByUid($uid = null, $offset = 0, $pageSize = 10, array $condition = array(),$isLimit = true) {
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$UserService = new UserService();
			$info = $UserService->searchUserList($offset,$pageSize,$condition,false);
			if($info['uids']){
				$condition['uids'] = $info['uids'];
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
		$userGiftSendRecordsModel = $this->UserGiftSendRecordsModel();
		return $userGiftSendRecordsModel->getUserGiftStatByUid($uid, $offset, $pageSize, $condition,$isLimit);
	}
	
	/**
	 * @author supeng
	 * @param array $targetIds
	 * @return mix|Ambigous <multitype:, multitype:unknown Ambigous <multitype:unknown , unknown> >
	 */
	public function getGiftRecordsSumByTargetIds(Array $targetIds){
		if (!is_array($targetIds)) return $this->setError(Yii::t('common', 'Parameter is empty'), false);
		$userGiftSendRecordsModel = $this->UserGiftSendRecordsModel();
		$result = $userGiftSendRecordsModel->getGiftRecordsSumByTargetIds($targetIds);
		return $this->buildDataByIndex($result, 'target_id');
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $targetId
	 * @param unknown_type $condition
	 * @return mix|mixed
	 */
	public function getGiftRecordsSumByTargetId($targetId,$condition=array()){
		if ($targetId <= 0) return $this->setError(Yii::t('common', 'Parameter is empty'), false);
		$userGiftSendRecordsModel = $this->UserGiftSendRecordsModel();
		$result = $userGiftSendRecordsModel->getGiftRecordsSumByTargetId($targetId,$condition);
		return $result;
	}
	
	/**
	 * 获取从直播间购买礼物的统计
	 * 
	 * @author supeng
	 * @param array $giftIds
	 * @param unknown_type $startTime
	 * @param unknown_type $endTime
	 * @return mix|Ambigous <multitype:, multitype:unknown Ambigous <multitype:unknown , unknown> , mixed>
	 */
	public function getSumSendGiftRecords(Array $giftIds,$startTime=null,$endTime=null){
		if (!$giftIds){
			return $this->setError(Yii::t('common', 'Parameter is empty'),false);
		}
	
		$userGiftSendModel = new UserGiftSendRecordsModel();
		$data = $userGiftSendModel->getSumSendGiftRecords($giftIds,$startTime,$endTime);
		if ($data){
			$data = $this->buildDataByIndex($data, 'gift_id');
		}
		return $data;
	}
	
	
	/**
	 * 获取用户收到的礼物记录
	 * @param int $uid 用户uid
	 * @param int $offset 页数
	 * @param int $pageSize 页码
	 * @param array $condition 其它条件
	 * @return array 返回结果集和符合条件的总数
	 */
	public function getUserGiftReceiveRecordsByUid($uid, $offset = 0, $pageSize = 10, array $condition = array(),$isLimit = true) {
		if ($uid <= 0) return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		$userGiftSendRecordsModel = $this->UserGiftSendRecordsModel();
		$data = $userGiftSendRecordsModel->getUserGiftReceiveRecordsByUid($uid, $offset, $pageSize, $condition,$isLimit);
		if (!empty($data['list'])){
			$data['list'] = $this->buildDataByIndex($data['list'], 'record_id');
		}
		return $data;
	}
	
	/**
	 * 统计用户收到的礼物数据
	 * @param $uid
	 * @param $condition
	 * @return array
	 */
	public function countUserGiftReceiveRecordsByUid($uid, $condition = array())
	{
		if ($uid <= 0) return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		$userGiftSendRecordsModel = $this->UserGiftSendRecordsModel();
		return $userGiftSendRecordsModel->countUserGiftReceiveRecordsByUid($uid,$condition);
	}	
	
	public function getGlobalGiftList(){
		$otherRedisModel=new OtherRedisModel();
		return $otherRedisModel->getGlobalGiftList();
	}

	
	
	
	/**
	 * @param int $type  礼物类型
	 * @return array
	 */
	public function getGiftType($type=null){
		$typeList = array(1 => '主站',
			2=>'游戏',
			4 =>'商城',
			8 => '幸运礼物',
			16 => '跑道礼物'
		);
		$type=intval($type);
		$array=array();
		if($type){
			foreach ($typeList as $key=>$row){
				if($this->hasMoreBit($type,$key)){
					$array[$key]=$typeList[$key];
				}
			}
		}else{
			$array=$typeList;
		}
		return $array;
	}
	
	
	
	
	/**
	 * 获取礼物商品属性
	 * @param int $type 商品类型
	 * @return array
	 */
	public function getShopType($type=null){
		$typeList = array(1 => '普通', 
					2=>'热卖', 
				    4 =>'新品', 
				    8 =>'推荐'
		);
		$type=intval($type);
		$array=array();
		if($type){
			foreach ($typeList as $key=>$row){
				if($this->hasBit($type,$key)){
					$array[$key]=$typeList[$key];
				}
			}
		}else{
			$array=$typeList;
		}
		return $array;
	}
	

	/**
	 * 取得礼物排行榜
	 * 
	 * @param string $type 礼物排行榜类型 今日 本周 本月 超级
	 * @return array
	 */
	public function getDoteyGiftRank($type){
		$keyConfig = Yii::getKeyConfig('redis','other');
		$list = array(
			'week'=>$keyConfig['dotey_gift_week_rank'],
			'lastweek'=>$keyConfig['dotey_gift_lastweek_rank'],
		);
		$type = !$type || in_array($type,array_keys($list)) ? $type : 'week';
		$redisModel = new OtherRedisModel();
		$rank = $redisModel->getDoteyGiftRank($list[$type]);
		return $rank;
	}
	
	

	/**
	 * 取得主播粉丝排行榜
	 * 
	 * @param string $type 粉丝排行榜类型 今日 本周 本月 超级
	 * @return array
	 */
	public function getDoteyReceiveGiftRank($type){
		$keyConfig = Yii::getKeyConfig('redis','other');
		$list = array(
			'super'=>$keyConfig['dotey_gift_super_rank'],
		);
	
		$type = !$type || in_array($type,array_keys($list)) ? $type : 'super';
		$redisModel = new OtherRedisModel();
		$rank = $redisModel->getDoteyReceiveGiftRank($list[$type],true);
		return $rank;
	}
	
	
	
	/**
	 * 获取最后一次送礼物的时间戳
	 * @return int
	 */
	public function getLastSendGiftTime($uid){
		if ($uid <= 0) return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		$userService=new UserService();
		$userBasic=$userService->getUserBasicByUids(array($uid));
		$userLoginRedisModel=new UserLoginRedisModel();
		$last_send_gift_time=0;
		if(isset($userBasic[$uid]['username'])){
			$userLogin=$userLoginRedisModel->getUserBasicByUserNames(array($userBasic[$uid]['username']));
			$userLogin=array_pop($userLogin);
			$last_send_gift_time=isset($userLogin['last_send_gift_time'])?$userLogin['last_send_gift_time']:0;
		}
		return $last_send_gift_time;
	}
	
	/**
	 * 获得礼物图片
	 * @author supeng
	 * @param unknown_type $fileName
	 * @return string
	 */
	public function getGiftUrl($fileName){
		return $this->getUploadUrl().'gift/'.$fileName;
	}
	
	/**
	 * 获取秀场后台礼物图片
	 * 
	 * @author supeng
	 * @param unknown_type $fileName
	 * @return string
	 */
	public function getShowAdminGiftUrl($fileName){
		return $this->getShowAdminUrl().'gift/'.$fileName;
	}
	/**
	 * 获得礼物效果图片
	 * @author supeng
	 * @param unknown_type $fileName
	 * @return string
	 */
	public function getGiftEffectUrl($fileName){
		return $this->getUploadUrl().'gift/effect/'.$fileName;
	}
	
	/**
	 * 获取秀场后台礼物效果图片
	 * 
	 * @author supeng
	 * @param unknown_type $fileName
	 * @return string
	 */
	public function getShowAdminGiftEffectUrl($fileName){
		return $this->getShowAdminUrl().'gift/effect/'.$fileName;
	}
	
	/**
	 * @author supeng
	 * @return multitype:string 
	 */
	public function getBuyLimitOption(){
		return array(
				0=>'不限购',
				1=>'限购',
			);
	}
	
	/**
	 * 获取前台礼物
	 * @return array
	 */
	public function getFrontGiftList(){
		$otherRedisModel=new OtherRedisModel();
		$giftList=$otherRedisModel->getGiftList();
		if(!$giftList){
			$giftList=$this->getGiftList(array('is_display'=>1),true);
			$otherRedisModel->saveGiftList($giftList);
		}
		return $giftList;
	}
	
	/**
	 * 统计某礼物在某些主播的主播收礼个数
	 * @author hexin
	 * @param int $gift_id
	 * @param array $uids
	 * @param int $startTime
	 * @param int $endTime
	 * @return array
	 */
	public function getGiftSumToDoteys($gift_id, array $uids, $startTime=null, $endTime=null){
		$giftRecords = UserGiftSendRecordsModel::model();
		return $giftRecords->getGiftSumToDoteys($gift_id, $uids, $startTime, $endTime);
	}
	
	/**
	 * 统计某礼物在某些主播的用户送礼个数
	 * @author hexin
	 * @param int $gift_id
	 * @param array $uids
	 * @param int $startTime
	 * @param int $endTime
	 * @return array
	 */
	public function getGiftSumFromUsers($gift_id, $uids, $startTime=null, $endTime=null){
		$giftRecords = UserGiftSendRecordsModel::model();
		return $giftRecords->getGiftSumFromUsers($gift_id, $uids, $startTime, $endTime);
	}
	
	/**
	 * 合并礼物特效
	 * @param array $gift
	 * @return array
	 */
	public function mergeGiftEffect(array $gift) {
		$giftIds = array();
		foreach ($gift as $row) {
			$giftIds[] = $row['gift_id'];
		}
		$effect = $this->getGiftEffectByGiftIds($giftIds);
		$giftList = array();
		foreach ($gift as $key => $row) {
			if (isset($effect[$key])) {
				if($this->getArrayDim($effect[$key])>1){
					$row['effects'] = $effect[$key];
				}else{
					$row['effects'] = array($effect[$key]);
				}
	
			}else{
				$row['effects']=array();
			}
			$giftList[$key] = $row;
		}
	
		return $giftList;
	}
	
	
	/**
	 * 存储前台礼物
	 */
	private function saveFrontGiftList(){
		$otherRedisModel=new OtherRedisModel();
		$giftList=$this->getGiftList(array('is_display'=>1),true);
		$otherRedisModel->saveGiftList($giftList);
	}

	private function getGiftCategoryModel() {
		return $giftCategoryModel = new GiftCategoryModel();
	}

	private function getGiftModel() {
		return $giftModel = new GiftModel();
	}

	private function getGiftEffectModel() {
		return $giftEffectModel = new GiftEffectModel();
	}

	private function UserGiftSendRecordsModel() {
		return $userGiftSendRecordsModel = new UserGiftSendRecordsModel();
	}

	private function UserGiftSendRelationRecordsModel() {
		return $userGiftSendRelationRecordsModel = new UserGiftSendRelationRecordsModel();
	}

}

?>
