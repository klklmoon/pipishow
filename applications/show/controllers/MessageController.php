<?php

/**
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package 
 */
class MessageController extends PipiController {

	
	public function actionMarkReadMessage(){
		if(!$this->isLogin){
			exit(json_encode(array('status'=>false, 'message'=>'请登录后,再进行操作')));
			Yii::app()->end();
		}
		
		$uid = Yii::app()->user->id;
		$type = Yii::app()->request->getParam('type');
		$messageId = Yii::app()->request->getParam('message_id');
		$messageService = new MessageService();
		if($messageService->markReadMessage($uid,$messageId,$type)){
			exit(json_encode(array('status'=>true, 'message'=>'')));
		}
		exit(json_encode(array('status'=>false, 'message'=>'操作失败')));
	}
	
	public function actionDelMessage(){
		if(!$this->isLogin){
			exit(json_encode(array('status'=>false, 'message'=>'请登录后,再进行操作')));
			Yii::app()->end();
		}
		
		$uid = Yii::app()->user->id;
		$messageId = Yii::app()->request->getParam('message_id');
		$type = Yii::app()->request->getParam('type');
		$messageService = new MessageService();
		if($messageService->delUserMessage($uid,$messageId,$type)){
			exit(json_encode(array('status'=>true, 'message'=>'')));
		}
		exit(json_encode(array('status'=>false, 'message'=>'操作失败')));
		
	}
}

?>