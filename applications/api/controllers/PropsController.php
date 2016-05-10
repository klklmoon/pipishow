<?php
/**
 * 道具接口
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class PropsController extends PipiApiController {

	
	public function actionPropsBag(){
		 $prop_name = Yii::app()->request->getParam('prop_name');
		 $uid = $this->uid;
		 $num = Yii::app()->request->getParam('num');
		 $numUpdate = Yii::app()->request->getParam('num_update');
		 $vtime = Yii::app()->request->getParam('vtime');
		 
		 $filename = DATA_PATH.'runtimes/propsbag_consume.log';
		 $jsonString = json_encode($_REQUEST);
		
		 if(empty($prop_name) || $uid <= 0 || ($numUpdate && $num <= 0) || $vtime <= 0){
			$this->responseClient('fail',Yii::t('common','Parameter is empty'));
		 }
		 $props = PropsModel::model()->findByAttributes(array('name'=>$prop_name));
		 if(empty($props)){
		 	$this->responseClient('fail',Yii::t('props','The props does not exist'));
		 }
		 $props = $props->attributes;
		 $prop_id = $props['prop_id'];
		 
		 $propsService = new PropsService();
		 $userPropsService = new UserPropsService();
		
		 $props = $propsService->getPropsByIds($prop_id,true,true);
		 $props = $props[$prop_id];
		
		 $timeStamp = time();
		 $records['uid'] = $uid;
		 $records['prop_id'] = $prop_id;
		 $records['amount'] = $num;
		 $records['vtime'] = $timeStamp+$vtime;
		 $records['source'] = PROPSRECORDS_SOURCE_GAME;
		 $records['cat_id'] = $props['cat_id'];
		 $record_sid =  $userPropsService->saveUserPropsRecords($records,$props);
		 if($record_sid <= 0){
		 	$error = '';
		 	if($userPropsService->getError()){
		 		$error = $userPropsService->getError();
		 	}elseif($userPropsService->getNotice()){
		 		$notice = $userPropsService->getNotice();
		 		$error = array_pop($notice);
		 	}
			$this->responseClient('fail',$error);
		 }
		 
		 $userPropsBagModel = new UserPropsBagModel();
		 $userProps = $userPropsBagModel->findByAttributes(array('uid'=>$uid,'prop_id'=>$prop_id));
		 if(empty($userProps)){
			//vip特殊处理
		 	if($props['category']['en_name'] == 'vip'){
		 		$vipBag=array();
		 		$vipBag['uid']=$uid;
		 		$vipBag['prop_id']=$prop_id;
		 		$vipBag['cat_id']=$props['cat_id'];
		 		$vipBag['record_sid']=$record_sid;
		 		$buyDays=$propsService->getVipTimingDays($timeStamp, $vtime > 1 ? $timeStamp+$vtime : 0);
		 		$flag=$propsService->saveVipToBag($vipBag, $buyDays);
		 	}
		 	else
		 	{
		 		$userPropsBagModel->uid = $uid;
		 		$userPropsBagModel->prop_id = $prop_id;
		 		$userPropsBagModel->num = $num;
		 		$userPropsBagModel->valid_time = $vtime > 1 ? $timeStamp+$vtime : 0;
		 		$userPropsBagModel->cat_id = $props['cat_id'];
		 		$userPropsBagModel->record_sid = $record_sid;
		 		$flag=$userPropsBagModel->save();
		 	}
		 	
		 	if($flag){
		 		error_log( date('Y-m-d H:i',time()).' success '.$jsonString."\r\n",3,$filename);
		 		
		 		$propsService->updateUserJsonOfVip($uid, $prop_id,1);
			 	
		 		$this->responseClient('success','');
		 	}else{
		 		error_log( date('Y-m-d H:i',time()).' failed '.$jsonString."\r\n",3,$filename);
		 		$this->responseClient('fail',Yii::t('props','You insert props bag failed'));
		 	}
		 }else{
		 	//vip特殊处理
		 	if($props['category']['en_name'] == 'vip'){
		 		$vipBag=array();
		 		$vipBag['uid']=$uid;
		 		$vipBag['prop_id']=$prop_id;
		 		$vipBag['cat_id']=$userProps->cat_id;
		 		$vipBag['record_sid']=$record_sid;
		 		$buyDays=$propsService->getVipTimingDays($timeStamp, $vtime > 1 ? $timeStamp+$vtime : 0);
		 		$flag=$propsService->saveVipToBag($vipBag, $buyDays);
		 		$propsService->updateUserJsonOfVip($uid, $prop_id,1);
		 	}
		 	else
		 	{
			 	$counters = array();
			 	if($userProps->valid_time == 0){
			 		$userProps->valid_time = 0;
			 	} else if($userProps->valid_time > $timeStamp && $vtime > 1){
			 		$counters['valid_time'] = $vtime;
			 	}else{
			 		if($vtime > 1){
			 			$userProps->valid_time = $timeStamp+$vtime;
			 		}
			 	}
			 	$userProps->save();
			 	if($numUpdate){
			 		$counters['num'] = $num;
			 	}
			 	if($counters){
			 		$userPropsBagModel->updateCounters($counters,' uid = '.$uid .' AND  prop_id = '.$prop_id);
			 	}
		 	}
		 	
			 error_log( date('Y-m-d H:i',time()).' success '.$jsonString."\r\n",3,$filename);	
		 	$this->responseClient('success','');
		 }
	}
	
	public function actionPropsList(){
		 $propsService = new PropsService();
		 $props = $propsService->getPropsByCondition();
		 $props = $propsService->buildDataByIndex($props,'prop_id');
		 $propsAttributes = $propsService->getPropsByIds(array_keys($props),false,true);
		 
		 foreach($props as $prop){
			$picture=$propsService->getPropsUrl($prop['game_image']);
			$attribute = array();
			if(isset($propsAttributes[$prop['prop_id']]['attribute'])){
				$attribute = $propsAttributes[$prop['prop_id']]['attribute'];
			}
			$propList[]=array('prop_id'=>$prop['prop_id'],'en_description'=>$prop['en_name'],'zh_description'=>$prop['name'],'price'=>$prop['pipiegg'],'picture'=>$picture,'attribute'=>$attribute);
		 }
		 $this->responseClient('success',$propList);
		
	}
}

?>