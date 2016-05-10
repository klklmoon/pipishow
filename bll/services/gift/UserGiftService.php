<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author guoshaobo <guoshaobo@pipi.cn>
 * @version $Id: UserGiftService.php 9848 2013-05-08 12:57:17Z guoshaobo $
 * @package
 */

define('CHENKIN_NORMAL', 1);
define('CHENKIN_MONTHCARD', 2);
define('CHENKIN_BROADCAST', 3);

define('REWARD_GIFT',1);
define('REWARD_EGG',2);
define('REWARD_PROPS',3);

define('CHECKIN_GIFT_MONTHCARD','hongmeigui');
define('CHECKIN_GIFT_NORMAL','sanyecao');

define('CHECKIN_GIFT_MONTHCARD_NUM',3);

class UserGiftService extends PipiService {
	
	/**
	 * 判断是否已经签到
	 * @param unknown_type $uid
	 * @param unknown_type $type
	 * @param unknown_type $time
	 * @return mix|boolean
	 */
	public function getIsCheckin($uid, $type = CHENKIN_NORMAL, $time = 0)
	{
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),true);
		}
		if($time<=0){
			$time = mktime(0,0,0,date('m'),date('d'),date('Y'));
		}
		$userCheckinModel = new UserCheckinModel();
		$res = $userCheckinModel->getCheckinByUid($uid, $type, $time);
		if(count($res)>0){
			return true;
		}
		return false;
	}
	
	public function getUserCheckinDays($uid){
		if($uid <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),true);
		}
		$userCheckinModel = new UserCheckinModel();
		return $userCheckinModel->checkinDays($uid);
	}
	/**
	 * 添加签到记录
	 * @param array $checkin
	 * @return mix|boolean
	 */
	public function saveCheckinRecord(array $checkin = array())
	{
		if(($uid = $checkin['uid'])<=0) {
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		$userCheckinModel = new UserCheckinModel();
		$checkin['create_time'] = $checkin['create_time'] > 0 ? $checkin['create_time'] :time();
		$this->attachAttribute($userCheckinModel,$checkin);
		if(!$userCheckinModel->validate()){
			return $this->setNotices($userCheckinModel->getErrors(),false);
		}
		$res = $userCheckinModel->save();
		if($res){
			$userJson = new UserJsonInfoService();
			$userInfo = $userJson->getUserInfo($uid,false);
			if(isset($userInfo['mc'])){
				$userInfo['mc']['num'] = $userInfo['mc']['num'] - $checkin['num'] > 0 ? $userInfo['mc']['num'] - $checkin['num'] : 0;
				$userJson->setUserInfo($uid, $userInfo);
				$checkinJson['mc'] = $userInfo['mc'];
				$zmq = $this->getZmq();
				$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>$checkinJson));
			}
			return $res;
		}
		return false;
	}
	
	/**
	 * 统计月卡签到已经领取的礼物数量
	 */
	public function countMonthGift($uid, $stime, $etime)
	{
		$num = CHECKIN_GIFT_MONTHCARD_NUM * 30;
		if($uid<=0 || $stime<=0 || $etime<=0) {
			return $this->setError(Yii::t('common','Parameter is empty'), $num);
		}
		$userCheckinModel = new UserCheckinModel();
		$res = $userCheckinModel->countMonthGift($uid, CHENKIN_MONTHCARD, $stime, $etime);
		if($res){
			return $num - $res['nums'];
		}
		return $num;
	}
	
	/**
	 * 签到某个礼物或道具
	 * @param int $uid
	 * @param int $type
	 * @param int $targetType
	 * @param int $targetId
	 * @param int $num
	 * @return boolean
	 */
	public function checkin($uid, $type, $targetType, $targetId, $num = 1){
		if($uid <= 0) return false;
		$model = new UserCheckinModel();
		$r = $model->isCheckinItem($uid, $type, $targetType, $targetId, strtotime(date('Y-m-d')." 00:00:00"), strtotime(date('Y-m-d')." 23:59:59"));
		if(empty($r)){
			//是礼物
			if($targetType == REWARD_GIFT){
				$userInfo = UserJsonInfoService::getInstance()->getUserInfo($uid, false);
				$giftService = new GiftService();
				$gifts = $giftService->getGiftByIds($targetId);
				$records['info']=serialize(array(
					'uid'=>$uid,
					'nickname'=>$userInfo['nk'],
					'gift_id'=>$targetId,
					'gift_name'=>$gifts[$targetId]['zh_name'],
					'num'=>$num,
					'remark'=>'签到赠送'));
				$records['source']=3;
				$gift = array('uid'=>$uid,'gift_id'=>$targetId,'num'=>$num);
				$giftBagService = new GiftBagService();
				$giftBagService->saveUserGiftBagByUid($gift, $records);
				$info = $gifts[$targetId]['zh_name'].'*'.$num;
				$pipiegg = $gifts[$targetId]['pipiegg'] * $num;
			//是道具
			}elseif($targetType == REWARD_PROPS){
				$propsService = new PropsService();
				$props = $propsService->getPropsByIds($targetId);
				$userPropsService=new UserPropsService();
				$record['uid']=$uid;
				$record['prop_id']=$targetId;
				$record['cat_id']=$props[$targetId]['cat_id'];
				$record['amount']=$num;
				$record['source']=PROPSRECORDS_SOURCE_ADMIN;
				$record['info']='系统赠送('.$props[$targetId]['name'].'*'.$num.')';
				$record['vtime']=strtotime(date('Y-m-d',strtotime("+1 day")));
				$recordId=$userPropsService->saveUserPropsRecords($record);
				$broadcast=$userPropsService->getUserValidPropsOfBagByPropId($uid,$targetId);
				$broadcast=array_pop($broadcast);
				$userPropsBagModel = new UserPropsBagModel();
				$userPropsBag =$userPropsBagModel->findByAttributes(array('uid'=>$uid,'prop_id'=>$targetId));
				if($userPropsBag){
					$orguserPropsBagModel = $userPropsBagModel->findByPk($userPropsBag['bag_id']);
					$propBag['bag_id']=$userPropsBag['bag_id'];
					$propBag['record_sid']=$recordId;
					$propBag['num']=$num;
					$propBag['valid_time']=strtotime(date('Y-m-d',strtotime("+1 day")));
					$userPropsService->attachAttribute($orguserPropsBagModel, $propBag);
					$orguserPropsBagModel->save();
				}else{
					$propBag['uid']=$uid;
					$propBag['record_sid']=$recordId;
					$propBag['prop_id']=$targetId;
					$propBag['cat_id'] = $props[$targetId]['cat_id'];
					$propBag['num']=$num;
					$propBag['valid_time']=strtotime(date('Y-m-d',strtotime("+1 day")));
					$userPropsService->attachAttribute($userPropsBagModel, $propBag);
					$userPropsBagModel->save();
				}
				$info = $props[$targetId]['name'].'*'.$num;
				$pipiegg = $props[$targetId]['pipiegg'] * $num;
			//其他
			}
			
			$checkin = array(
				'uid'=>$uid,
				'type'=>$type,
				'reward_type'=>$targetType,
				'target_id'=>$targetId,
				'num'=>$num,
				'info'=>$info,
				'pipiegg'=>$pipiegg,
				'create_time'=>time(),
			);
			return $this->saveCheckinRecord($checkin);
		}
		return false;
	}
}

?>