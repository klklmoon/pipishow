<?php
/**
 * 消息类型接口
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class MessageController extends PipiApiController {

	
	public function actionSaveMessage(){
		 $message['uid'] = Yii::app()->request->getParam('sender_uid');
		 $message['to_uid'] = Yii::app()->request->getParam('to_uid');
		 $message['category'] = Yii::app()->request->getParam('category');
		 $message['sub_category'] = Yii::app()->request->getParam('sub_category');
		 $message['title'] = Yii::app()->request->getParam('title');
		 $message['sub_title'] = Yii::app()->request->getParam('sub_title');
		 $message['content'] = Yii::app()->request->getParam('content');
		 $message['is_read'] = (int)Yii::app()->request->getParam('is_read');
		 if(Yii::app()->request->getParam('target_id')){
		 	 $message['target_id'] = Yii::app()->request->getParam('target_id');
		 }
		 if(Yii::app()->request->getParam('extra')){
		 	 $message['extra'] = Yii::app()->request->getParam('extra');
		 }
		 
		 $messageService = new MessageService();
		 if($messageService->sendMessage($message)){
		 	$this->responseClient('success','');
		 }else{
		 	$error = '';
		 	if($messageService->getError()){
		 		$error = $messageService->getError();
		 	}elseif($messageService->getNotice()){
		 		$notice = $messageService->getNotice();
		 		$error = array_pop($notice);
		 	}
			$this->responseClient('fail',$error);
		 }
	}
	

}

?>