<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PublicController.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package 
 */
class PublicController extends PipiApiController {
	protected  $isCheckApp = false;
	protected $isCheckToken = false;
	
	public function actionError(){
		if(Yii::app()->errorHandler->error){
	    	$error=Yii::app()->errorHandler->error;
	    	if(Yii::app()->request->isAjaxRequest){
	    		 $this->responseClient('fail',$error['message']);
	    	}else{
	    		$this->responseClient($error['code'],$error['message']);
	    	}
	    }else{
	    	$this->responseClient('fail','联系网站管理员');
	    }
	}

}

?>