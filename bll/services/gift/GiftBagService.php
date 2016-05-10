<?php
define('GIFT_SEND_BAG',1);  //背包送礼

define('BAGSOURCE_TYPE_SHOP',0);
define('BAGSOURCE_TYPE_LIVE',1);
define('BAGSOURCE_TYPE_GAME',2);
define('BAGSOURCE_TYPE_ADMIN',3);
define('BAGSOURCE_TYPE_ACTIVITY',4);
define('BAGSOURCE_TYPE_AWARD',5);      //中奖赠品
define('SHOP_GIFT_BUY_LIMIT',9999); //商城购买礼物数量限制
/**
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: GiftBagService.php 16958 2013-12-18 03:06:10Z leiwei $ 
 * @package 
 */
class GiftBagService extends PipiService {
	
	/**
	 * 背包送礼
	 *
	 * @param int $uid          送礼uid
	 * @param int $to_uid       接受者uid
	 * @param int $archivesId   所在直播间档期Id
	 * @param int $giftId       礼物id
	 * @param int $num          礼物数量
	 */
	public function sendBagGift($uid, $to_uid, $archivesId, $giftId, $num,$remark=null) {
		if ($uid <= 0 || $to_uid <= 0 || $archivesId <= 0 || $giftId <= 0 || $num <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is error'), -1);
		}
		$userGiftBag=$this->getUserBagByGiftIds($uid,$giftId);
		
		if(empty($userGiftBag)||$userGiftBag['num']-$num<0){
			return $this->setError(Yii::t('giftBag', 'Gift quantity not sufficient'), 0);
		}
		$giftBag=$this->sendFromUserBagByUid($uid, $giftId, $num);
		if($giftBag<=0){
			return $this->setError(Yii::t('giftBag', 'Gift bag reduce failed'),0);
		}
		if($remark){
			$remark=htmlspecialchars($remark,ENT_QUOTES);
		}
		$giftService=new GiftService();
		$gift = $giftService->getGiftByIds($giftId);
		$giftPrice = $gift[$giftId]['pipiegg'] * $num;
		$userService = new UserService();
		$userBasic = $userService->getUserBasicByUids(array($uid, $to_uid));
		$giftEffect = $giftService->getGiftEffectByNum($giftId, $num);
		$consumeService = new ConsumeService();
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
				$dedicationRecords['source']=SOURCE_GIFTS;
				$charmRecords['source']=SOURCE_GIFTS;
				$charmPointsRecords['source']=SOURCE_GIFTS;
			}else{
				$records['recevier_type']=1;
				$json_content['recevier_type']=1;
				$dedicationRecords['source']=SOURCE_USERGIFTS;
				$charmRecords['source']=SOURCE_USERGIFTS;
				$charmPointsRecords['source']=SOURCE_USERGIFTS;
			}
			
			$charmRecords['uid'] = $to_uid;
			$charmRecords['sender_uid'] = $uid;
			$charmRecords['target_id'] = $archivesId;
			$charmRecords['charm'] = $gift[$giftId]['charm'] * $num;
			$charmRecords['num'] = $num;
			$charmRecords['sub_source']=SUBSOURCE_GIFTS_BAG;
			$charmRecords['info']=$gift[$giftId]['zh_name'].'x'.$num;
			

			$charmPointsRecords['uid'] = $to_uid;
			$charmPointsRecords['sender_uid'] = $uid;
			$charmPointsRecords['target_id'] = $archivesId;
			$charmPointsRecords['charm_points'] = $gift[$giftId]['charm_points'] * $num;
			$charmPointsRecords['num'] = $num;
			$charmPointsRecords['sub_source']=SUBSOURCE_GIFTS_BAG;
			$charmPointsRecords['info']=$gift[$giftId]['zh_name'].'x'.$num;
		}else{
			$records['recevier_type']=1;
			$json_content['recevier_type']=1;
			$dedicationRecords['source']=SOURCE_USERGIFTS;
			
			$eggPointsRecords['uid'] = $to_uid;
			$eggPointsRecords['sender_uid'] = $uid;
			$eggPointsRecords['target_id'] = $archivesId;
			$eggPointsRecords['egg_points'] = $gift[$giftId]['egg_points'] * $num;
			$eggPointsRecords['num'] = $num;
			$eggPointsRecords['source']=SOURCE_USERGIFTS;
			$eggPointsRecords['sub_source']=SUBSOURCE_GIFTS_BAG;
			$eggPointsRecords['info']=$gift[$giftId]['zh_name'].'x'.$num;
		}
		$eventData['json_content']=$json_content;
		$zmq->sendZmqMsg(608,$eventData);
		
		$records['uid'] = $uid;
		$records['to_uid'] = $to_uid;
		$records['target_id']=$archivesId;
		$records['gift_id'] = $giftId;
		$records['num'] = $num;
		$records['gift_type'] =GIFT_SEND_BAG;
		$records['record_sid'] = $userGiftBag['bag_id'];
		$records['sender'] = empty($userBasic[$uid]['nickname']) ? $userBasic[$uid]['username'] : $userBasic[$uid]['nickname'];
		$records['receiver'] = empty($userBasic[$to_uid]['nickname']) ? $userBasic[$to_uid]['username'] : $userBasic[$to_uid]['nickname'];
		$records['position'] = $giftEffect['position'];
		$records['timeout'] = $giftEffect['timeout'];
		
		$dedicationRecords['uid'] = $uid;
		$dedicationRecords['dedication'] = $gift[$giftId]['dedication'] * $num;
		$dedicationRecords['num'] = $num;
		$dedicationRecords['from_target_id'] = $giftId;
		$dedicationRecords['to_target_id'] = $archivesId;
		$dedicationRecords['sub_source']=SUBSOURCE_GIFTS_BAG;
		$dedicationRecords['info']=$gift[$giftId]['zh_name'].'x'.$num;
		
		try{
			$giftRecords = $giftService->saveUserGiftRecords($records);
		}catch (Exception $e){
			$zmqData['type'] ='save_user_consume_records';
			$zmqData['uid'] = $uid;
			$records['step']=1;
			$records['suid']=$uid;
			unset($records['uid']);
			$zmqData['json_info'] = $records;
			$seq=$zmq->sendZmqMsg(609, $zmqData);
			$zmqData_dedication['type'] ='save_user_consume_records';
			$zmqData_dedication['uid'] = $uid;
			$dedicationRecords['seq']=$seq;
			$dedicationRecords['step']=3;
			$dedicationRecords['suid']=$uid;
			unset($dedicationRecords['uid']);
			$zmqData_dedication['json_info'] = $dedicationRecords;
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
			error_log(date("Y-m-d H:i:s")."存储用户背包礼物记录异常：".$error.",异常送礼:".json_encode($zmqData['json_info'])."\n\r",3,$filename);
			return true;
			exit;
		}
		$consumeService = new ConsumeService();
		//写入用户贡献值记录
		$dedicationRecords['record_sid'] = $giftRecords;
		try{
			$consumeService->saveUserDedicationRecords($dedicationRecords, true);
		}catch(Exception $e){
			$zmqData['type'] ='save_user_consume_records';
			$zmqData['uid'] = $uid;
			$dedicationRecords['step']=3;
			$dedicationRecords['suid']=$uid;
			unset($dedicationRecords['uid']);
			$zmqData['json_info'] = $dedicationRecords;
			$zmq->sendZmqMsg(609, $zmqData);
			$error=$e->getMessage();
			$filename = DATA_PATH.'runtimes/user_consume_records_exception.txt';
			error_log(date("Y-m-d H:i:s").'存储背包用户贡献值记录异常：'.$error.",用户贡献值记录数据:".json_encode($zmqData['json_info'])."\n\r",3,$filename);
		}
		try{
			$consumeService->saveUserConsumeAttribute(array('uid'=>$uid,'archives_id'=>$archivesId,'dedication'=>$dedicationRecords['dedication']));
		}catch (Exception $e){
			$error=$e->getMessage();
			$filename = DATA_PATH.'runtimes/user_attribute_exception.txt';
			error_log(date("Y-m-d H:i:s").'背包送礼用户贡献值异常：'.$error."\n\r",3,$filename);
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
				error_log(date("Y-m-d H:i:s").'存储背包送礼主播魅力值记录异常：'.$error.",主播魅力值记录数据:".json_encode($zmqData['json_info'])."\n\r",3,$filename);
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
				error_log(date("Y-m-d H:i:s").'存储背包送礼主播魅力点记录异常：'.$error.",主播魅力点记录数据:".json_encode($zmqData['json_info'])."\n\r",3,$filename);
			}
			try{
				$consumeService->saveUserConsumeAttribute(array('uid'=>$to_uid,'archives_id'=>$archivesId,'charm_points'=>$charmPointsRecords['charm_points'],'charm'=>$charmRecords['charm']));
			}catch (Exception $e){
				$error=$e->getMessage();
				$filename = DATA_PATH.'runtimes/user_attribute_exception.txt';
				error_log(date("Y-m-d H:i:s").'背包送礼用户魅力值和魅力点异常：'.$error."\n\r",3,$filename);
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
				error_log(date("Y-m-d H:i:s").'存储背包送礼用户皮点记录异常：'.$error.",用户皮点记录数据:".json_encode($zmqData['json_info'])."\n\r",3,$filename);
			}
			try{
				$consumeService->saveUserConsumeAttribute(array('uid'=>$to_uid,'archives_id'=>$archivesId,'egg_points'=>$eggPointsRecords['egg_points']));
			}catch (Exception $e){
				$error=$e->getMessage();
				$filename = DATA_PATH.'runtimes/user_attribute_exception.txt';
				error_log(date("Y-m-d H:i:s").'背包送礼用户皮点异常：'.$error."\n\r",3,$filename);
			}
		}
		//幸运礼物参与中奖
		if($this->hasBit(intval($gift[$giftId]['gift_type']), GIFT_TYPE_LUCK)){
			if($award){
				$luckRecord['uid']=$uid;
				$luckRecord['archives_id']=$archivesId;
				$luckRecord['record_sid']=$giftRecords;
				$luckRecord['source']=$dedicationRecords['source'];
				$luckRecord['sub_source']=SUBSOURCE_LUCK_GIFTS_BAG;
				$luckGiftService->sendGiftAward($luckRecord, $award);
			}
		}
		//存储上一次送的时间
		$giftService->saveLastSendGiftTime($uid);
		return true;
	}
	
	/**
	 * 获取用户背包中的礼物
	 * @param array|int $uids
	 * @return array
	 */
	public function getUserGiftBagByUids($uids){
		if(empty($uids)||$uids<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$uids=is_array($uids)?$uids:array($uids);
		$userGiftBagModel=$this->getUserGiftBagModel();
		$data=$userGiftBagModel->getUserGiftBagByUids($uids);
		$userBag=array();
		if($data){
			$list=$this->arToArray($data);
			$userBag=$this->buildGiftBagByIndex($list,'uid');
		}
		
		return $userBag;
	}
	
	/**
	 * 根据礼物Id获取用户背包中的礼物
	 * @param int $uid
	 * @param mix $giftIds
	 * @return array
	 */
	public function getUserBagByGiftIds($uid,$giftIds){
		if($uid<=0||empty($giftIds))
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$userGiftBagModel=$this->getUserGiftBagModel();
		$giftIds=is_array($giftIds)?$giftIds:array($giftIds);
		$data=$userGiftBagModel->getUserBagByGiftIds($uid,$giftIds);
		return $data?$data->attributes:array();
	}
	
	
	
	/**
	 * 礼物送入用户背包
	 * @param array $gift 背包信息
	 * @param array $records 背包记录信息
	 * @return boolen
	 */
	public function saveUserGiftBagByUid(array $gift,array $records){
		if($gift['uid']<=0||$gift['gift_id']<=0||$gift['num']<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$recordId = false;
		$userGiftBagModel=$this->getUserGiftBagModel();
		$userGiftBag=$userGiftBagModel->getUserGiftBagByUidGiftId($gift['uid'],$gift['gift_id']);
		if(!$userGiftBag){
			$this->attachAttribute($userGiftBagModel,$gift);
			if(!$userGiftBagModel->validate()){
				return $this->setNotices($userGiftBagModel->getErrors(),array());
			}
			$bag_id=$userGiftBagModel->save();
		}else{
			try{
				$bag_id=$userGiftBagModel->updateCounters(array('num'=>$gift['num']),'uid=:uid AND gift_id=:gift_id',array(':uid'=>$gift['uid'],'gift_id'=>$gift['gift_id']));
			}catch(Exception $e){
				$zmq=$this->getZmq();
				$eventData['type']='update_user_bag';
				$eventData['uid']=$gift['uid'];
				$eventData['json_info']=array('suid'=>$gift['uid'],'gift_id'=>$gift['gift_id'],'num'=>$gift['num'],'time'=>time());
				$zmq->sendZmqMsg(609, $eventData);
				$errorInfo=$e instanceof PDOException ? $e->errorInfo : '';
				$filename = DATA_PATH.'runtimes/userbag_exception.txt';
				$jsonString = json_encode($eventData);
				error_log( date('Y-m-d H:i',time()).' '.$jsonString.' '.$errorInfo."\n\r",3,$filename);
				
			}
			
		}
		if($bag_id){
			$records=array_merge($gift,$records);
			$recordId=$this->saveUserBagRecords($records);
			if ($recordId && $this->isAdminAccessCtl()){
				$this->saveAdminOpLog('新增 礼物['.$gift['gift_id'].'] 数量['.$gift['num'].'] 给用户['.$gift['uid'].'] 背包记录['.$recordId.']',$gift['uid']);
			}
		}
		return $recordId;
	}
	
	
	/**
	 * 礼物从用户背包送出
	 * @param int $giftId
	 * @param int $uid
	 * @return boolen
	 */
	public function sendFromUserBagByUid($uid,$giftId,$send_num){
		if($uid<=0||$giftId<=0||$send_num<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$userGiftBagModel=$this->getUserGiftBagModel();
		return $userGiftBagModel->reduceGiftBag($uid,$giftId,$send_num);
	}
	
	public function buyShopGift($uid,$giftId,$num){
		if($uid<=0||$giftId<=0||$num<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$giftService=new GiftService();
		$gift=$giftService->getGiftByIds($giftId);
		if(empty($gift)){
			return $this->setError(Yii::t('common','Parameters are wrong'),0);
		}
		if($num>SHOP_GIFT_BUY_LIMIT){
			return $this->setError(Yii::t('giftBag','Shop number is not greater than 9999'),0);
		}
		$userService=new UserService();
		$userInfo=$userService->getUserFrontsAttributeByCondition($uid,true);
		if($userInfo['rk']<$gift[$giftId]['sell_grades']){
			return $this->setError(Yii::t('giftBag','Restrictions on the purchase'),0);
		}
		if($gift[$giftId]['buy_limit']==1){
			if($gift[$giftId]['sell_nums']<=0){
				return $this->setError(Yii::t('giftBag','Restrictions on the purchase'),0);
			}
		}
		
		$giftPrice=$gift[$giftId]['pipiegg']*$num;
		$consumeService = new ConsumeService();
		$consume = $consumeService->consumeEggs($uid, $giftPrice);
		if ($consume <= 0) {
			return $this->setError(Yii::t('common', 'Pipiegg not enough'), 0);
		}
		$gifts['uid']=$uid;
		$gifts['gift_id']=$giftId;
		$gifts['num']=$num;
		$records['info']=serialize(array('uid'=>$uid,'nickname'=>$userInfo['nk'],'gift_id'=>$giftId,'gift_name'=>$gift[$giftId]['zh_name'],'num'=>$num,'remark'=>'商城购买'));
		$records['source']=0;
		$recordId=$this->saveUserGiftBagByUid($gifts,$records);
		if($recordId<=0){
			return $this->setError(Yii::t('giftBag','Gift send to bag failed'),0);
		}
		if($gift[$giftId]['buy_limit']==1){
			if($gift[$giftId]['sell_nums']>0){
				$giftService->reduceGiftSellNum($giftId,-$num);
			}
		}
		//写入皮蛋log
		$pipieggRecords['uid'] = $uid;
		$pipieggRecords['pipiegg'] = $giftPrice;
		$pipieggRecords['from_target_id'] = $giftId;
		$pipieggRecords['num'] = $num;
		$pipieggRecords['to_target_id'] = $archivesId;
		$pipieggRecords['record_sid'] = $recordId;
		$pipieggRecords['source']='gifts';
		$pipieggRecords['sub_source']='buyGifts';
		$pipieggRecords['client']=1;
		$pipieggRecords['extra']=$gift[$giftId]['zh_name'].'x'.$num;
		$consumeService->saveUserPipiEggRecords($pipieggRecords, false);
		try{
			$consumeService->saveUserConsumeAttribute(array('uid'=>$uid,'pipiegg'=>$pipieggRecords['pipiegg']));
		}catch (Exception $e){
			$error=$e->getMessage();
			$filename = DATA_PATH.'runtimes/user_attribute_exception.txt';
			error_log('商城礼物购买用户皮蛋异常：'.$error."\n\r",3,$filename);
		}
		return $recordId;
	}
	
	
	/**
	 * 写入用户入背包记录
	 * @param array $records
	 * @return boolean
	 */
	public function saveUserBagRecords(array $records){
		if($records['uid']<=0||$records['gift_id']<=0||$records['num']<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$userBagRecordsModel=$this->getUserBagRecordsModel();
		$records['create_time']=isset($records['create_time'])?$records['create_time']:time();
		$this->attachAttribute($userBagRecordsModel,$records);
		if(!$userBagRecordsModel->validate()){
			return $this->setNotices($userBagRecordsModel->getErrors(),array());
		}
		$userBagRecordsModel->save();
		return  $userBagRecordsModel->getPrimaryKey();
	}
	
	
	/**
	 * 获取用户礼物入背包记录
	 * @param int $uid 用户uid
	 * @param int $offset 偏移量
	 * @param int $pageSize 页码
	 * @param array $condition 其它条件
	 * @return array 返回结果集和符合条件的总数
	 */
	public function getUserBagRecords($uid,$offset=0,$pageSize=10,array $condition=array()){
		if($uid<=0)
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		$userBagRecordsModel=$this->getUserBagRecordsModel();
		$data=$userBagRecordsModel->getUserBagRecords($uid,$offset,$pageSize,$condition);
		$list=$this->arToArray($data);
		$bagRecords[$uid]=$this->buildDataByIndex($list,'record_id');
		$criteria = $userBagRecordsModel->getDbCriteria();
		$count=$userBagRecordsModel->count($criteria);
		$bagRecords['count']=$count;
		return $bagRecords;
	}
	
	/**
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @return mix|Ambigous <string, unknown, mixed>
	 */
	public function getUserBagRecordsByCondition(array $condition=array(),$offset=0,$pageSize=10){
		if(!empty($condition['username']) || !empty($condition['nickname']) || !empty($condition['realname'])){
			$UserService = new UserService();
			$info = $UserService->searchUserList($offset,$pageSize,$condition,false);
			if($info['uids']){
				$condition['uid'] = $info['uids'];
			}else{
				return array('count'=>0,'list'=>array());
			}
		}
		
		$userBagRecordsModel=$this->getUserBagRecordsModel();
		$result=$userBagRecordsModel->getUserBagRecordsByCondition($condition,$offset,$pageSize);
		if ($result['list']){
			$result['list'] = $this->arToArray($result['list']);
		}
		return $result;
	}
	
	/**
	 * 礼物被购买统计
	 * 
	 * @author supeng
	 * @param array $giftIds
	 * @param unknown_type $startTime
	 * @param unknown_type $endTime
	 */
	public function getSumGiftBagRecords(Array $giftIds,$startTime=null,$endTime=null){
		if (!$giftIds){
			return $this->setError(Yii::t('common', 'Parameter is empty'),false);
		}
		
		$userBagRecordsModel = new UserBagRecordsModel();
		$data = $userBagRecordsModel->getSumGiftBagRecords($giftIds,$startTime,$endTime);
		if ($data){
			$data = $this->buildDataByIndex($data, 'gift_id');
		}
		return $data;
	}
	
	/**
	 * @author supeng
	 * @param int $type
	 * @return Ambigous <multitype:string , string>
	 */
	public function getBagSource($type = null){
		$source = array(
			BAGSOURCE_TYPE_SHOP=>'商城购买',
			BAGSOURCE_TYPE_LIVE=>'直播间购买',
			BAGSOURCE_TYPE_GAME=>'游戏',
			BAGSOURCE_TYPE_ADMIN=>'后台赠送',
			BAGSOURCE_TYPE_ACTIVITY =>'活动领取',
			BAGSOURCE_TYPE_AWARD=>'中奖赠品'
		);
		return is_null($type) ? $source : $source[$type];
	}
	
	public function buildGiftBagByIndex($data,$key){
		$list=$this->array_sort($data, $key,'asc');
		foreach($list as $row){
			$newData[$row[$key]][]=$row;
		}
		return $newData;
	}
	
	private function getUserGiftBagModel(){
		return new UserGiftBagModel();
	}
	
	private function getUserBagRecordsModel(){
		return new UserBagRecordsModel();
	}
	
	
}

?>