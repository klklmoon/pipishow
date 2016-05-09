<?php

/**
 * 用户登陆标识
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PipiUserIdentity.php 16342 2013-11-04 07:59:57Z hexin $ 
 * @package componets
 */
class PipiUserIdentity extends CUserIdentity {
	/**
	 * @var  array 仅用于开放平台登录
	 */
	public $openUserInfo = array();
	
	public $api = false;
	
	const ERROR_USER_DISABLED = 3;
	/**
	 * Enter description here ...
	 * @var us
	 */
	private $uid;
	
	
	public function authenticate(){
		$userService = new UserService();
		if($this->openUserInfo && is_array($this->openUserInfo)){
			//仅用于开放平台登录
			$this->uid = $this->openUserInfo['uid'];
			$this->errorCode=self::ERROR_NONE;
			$user = $userService->getUserBasicByUids(array($this->uid));
			$user = $user[$this->uid];
			if($user['user_status'] == 1){
				$this->errorCode = self::ERROR_USER_DISABLED;
				$this->errorMessage =  Yii::t('user','Your account has been closed, contact the administrator');
			}else{
				$this->errorCode=self::ERROR_NONE;
			}
			return !$this->errorCode;
		}
		
		$user = array();
		if($this->api){
			$user['apiPassword'] = true;
		}
		$flag = $userService->vadidatorPassword($this->username,$this->password,USER_LOGIN_USERNAME,$user);
		if(!$flag){
			//判断是否是靓号登录
			if($this->username > 0){
				$userNumberModel = new UserNumberModel();
				$number = $userNumberModel->findByAttributes(array('number'=>$this->username,'status'=>0));
				if($number){
					$user = $userService->getUserBasicByUids(array($number->uid));
					if($user){
						$user = $user[$number->uid];
						$flag = $userService->vadidatorPassword($user['username'],$this->password,USER_LOGIN_USERNAME,$user);
						$this->username = $user['username'];
					}
				}
			}
		}
		if(empty($user)){
			$this->errorCode=self::ERROR_USERNAME_INVALID;
			$this->errorMessage = Yii::t('user','You enter the user name or password incorrect');
		}else{
			if($flag === false){
				 $this->errorCode=self::ERROR_PASSWORD_INVALID;
				 $this->errorMessage = Yii::t('user','You enter the user name or password incorrect');
			}else{
				 if($user['user_status'] == 1){
				 	$this->errorCode = self::ERROR_USER_DISABLED;
				 	$this->errorMessage =  Yii::t('user','Your account has been closed, contact the administrator');
				 }else{
					 $this->errorCode=self::ERROR_NONE;
				 }
			}
			$this->uid = $user['uid'];
		}
		return !$this->errorCode;
	 }
 
       

     public function getId(){
         return $this->uid;
     }
}
