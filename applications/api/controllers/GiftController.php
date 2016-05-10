<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package api
 * @subpackage userBag
 */
class GiftController extends PipiApiController {
	
	
	public function actionGiftList(){
		$giftService=new GiftService();
		$gift=$giftService->getGiftList();
		$giftList=array();
		foreach($gift as $row){
			if($giftService->hasBit(intval($row['gift_type']),GIFT_TYPE_MAIN)){
				$picture=$giftService->getGiftUrl($row['image']);
				$giftList[]=array('present_id'=>$row['gift_id'],'en_description'=>$row['en_name'],'zh_description'=>$row['zh_name'],'price'=>$row['pipiegg'],'picture'=>$picture);
			}
		}
		$this->responseClient('success',$giftList);
	}
	
	public function actionBag(){
		$gift_name= Yii::app()->request->getParam('gift_name');
		$uid= Yii::app()->request->getParam('uid');
		$num= Yii::app()->request->getParam('num');
		$time= Yii::app()->request->getParam('time');
		$source = Yii::app()->request->getParam('source');
		$filename = DATA_PATH.'runtimes/user_gift_bag.log';
		$jsonParam= json_encode($_REQUEST);
		if(empty($gift_name)||$uid<=0||$num<=0){
			$this->responseClient('fail',Yii::t('common','Parameter is empty'));
		}
		$userService=new UserService();
		$userInfo=$userService->getUserFrontsAttributeByCondition($uid,true);
		$gifBagtService=new GiftBagService();
		$giftService=new GiftService();
		$gift=$giftService->getGiftByGiftName($gift_name);
		if(empty($gift)){
			$this->responseClient('fail',Yii::t('gift','Gift not exits'));
		}
		$gifts['uid']=$uid;
		$gifts['gift_id']=$gift['gift_id'];
		$gifts['num']=$num;
		$type=$gifBagtService->getBagSource(BAGSOURCE_TYPE_GAME);
		$records['info']=serialize(array('uid'=>$uid,'nickname'=>$userInfo['nk'],'gift_id'=>$gift['gift_id'],'gift_name'=>$gift_name,'num'=>$num,'remark'=>$type));
		$records['source']=2;
		$records['sub_source']=$source;
		$records['create_time']=$time ? $time : time();
		$record=$gifBagtService->saveUserGiftBagByUid($gifts,$records);
		
		if(!$record){
			error_log( date('Y-m-d H:i',time()).' fail '.$jsonParam."\r\n",3,$filename);
			$this->responseClient('fail',Yii::t('giftBag','Gift send to bag failed'));
		}
		error_log( date('Y-m-d H:i',time()).' success '.$jsonParam."\r\n",3,$filename);
		$this->responseClient('success',Yii::t('success',Yii::t('giftBag','Gift send to bag success')));
	}
}

?>