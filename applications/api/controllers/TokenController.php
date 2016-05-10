<?php
/**
 * token值维护
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PublicController.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package 
 */
class TokenController extends PipiApiController {
	
	
	public function __construct($id,$module){
		if(DEV_ENVIRONMENT == 'test' || DEV_ENVIRONMENT == 'local')
			$this->isCheckToken = false;
		parent::__construct($id,$module);
		
	}
	public function actionCreateToken(){
		$this->timestamp = Yii::app()->request->getParam('timestamp');
		$this->token = Yii::app()->request->getParam('token');
		$this->app_id = Yii::app()->request->getParam('app_id');
		$this->uid = Yii::app()->request->getParam('uid');
		$controller=Yii::app()->request->getParam('c');
		$action=Yii::app()->request->getParam('a');
	    $app = self::$appService->getAppInfoById($this->app_id);
	   
	    echo $this->timestamp.'<br/>';
		$baseSignString = ($this->uid ? $this->uid : '') .$controller.$action.$this->app_id.$this->timestamp;
		echo $baseSignString.'<br/>';
		echo md5(md5($baseSignString).$app['app_secret']);
		echo '<br/>';
	}
	
	public function actionGetToken(){
		
	}

}

?>