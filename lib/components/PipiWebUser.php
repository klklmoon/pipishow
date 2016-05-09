<?php

/**
 * 用户登陆组件
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PipiWebUser.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package componets
 */
class PipiWebUser extends CWebUser {

	
	public function afterLogin($fromCookie){
		$returnUrl = Yii::app()->request->getUrlReferrer();
		$returnUrl = $returnUrl ? $returnUrl : Yii::app()->request->getHostInfo();
		$userService = new UserService();
		$records = array();
		$records['uid'] = $this->getId();
		$records['login_page'] = $returnUrl;
		if(Yii::app()->session['open_referer']){
			$records['login_type'] = 1;
		}else{
			$records['login_type'] = 0;
		}
		$userService->saveUserLoginRecords($records);
	}
}
