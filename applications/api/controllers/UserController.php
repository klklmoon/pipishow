<?php
define('USER_REGISTER_COOKIE_VTIME',3600*3);
/**
 * 子站统一登录，对外接口提供判断
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PublicController.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package 
 */
class UserController extends PipiApiController {
	
	
	public function actionLogout(){
		Yii::app()->user->logout();
	}
	
	public function actionIsLogin(){
		if(!Yii::app()->user->isGuest){
			$userService = new UserService();
			$user = $userService->getUserBasicByUids(array(Yii::app()->user->id));
			if($user){
				$user = $user[Yii::app()->user->id];
			}
			$this->responseClient('success',array('uid'=>Yii::app()->user->id,'nickname'=>$user['nickname']));
		}else{
			$this->responseClient('fail','no login');
		}
		
		
	}
	
	
	public function actionLogin(){
		$username = Yii::app()->request->getParam('username');
		$password = Yii::app()->request->getParam('password');
		$code = Yii::app()->request->getParam('code');
		$remember = Yii::app()->request->getParam('remember');
		$loginModel = new UserLoginForm();
		$loginModel->username = $username;
		$loginModel->password = $password;
		$loginModel->apiPassword = true;
		$loginModel->code = $code;
		$loginModel->is_code = false;
		if(!$loginModel->validate()){
			$error = $loginModel->getErrors();
			$this->responseClient('fail',array_pop($error));
		}else{
			$duration = 0;
			if($remember){
				$duration = 3600*8;
			}
			if(Yii::app()->user->login($loginModel->getIdentity(),$duration)){
				$userService = new UserService();
				$user = $userService->getUserBasicByUids(array(Yii::app()->user->id));
				if($user){
					$user = $user[Yii::app()->user->id];
				}
				$this->responseClient('success',array('uid'=>Yii::app()->user->id,'nickname'=>$user['nickname']));
			}else{
				$this->responseClient('fail','login exception');
			}
		}
	}
	
	
	public function actionRegister(){
		
		$username = Yii::app()->request->getParam('username');
		$password = Yii::app()->request->getParam('password');//必须是对明文密码md5过后传过来的
		$confirm_password = Yii::app()->request->getParam('confirm_password');
		$nickname = Yii::app()->request->getParam('nickname');
		$code = Yii::app()->request->getParam('code');
		$source = Yii::app()->request->getParam('source');
		
		//接口里不需要检查confirm_password的情况
		if(!$confirm_password) $confirm_password = $password;
		//对方系统里没有nickname的情况，无法传入nickname，只有先注册，再重置nickname了
		if(!$nickname) $nickname = $username;
		
		$user['nickname'] = $nickname;
		$user['username'] = $username;
		//$user['reg_email'] = 'suqian@pipi.cn';
		$user['password'] = $password;
		$user['user_type'] = 1;
		$user['apiPassword'] = true;
		
		$registerForm = new UserRegisterForm();
		$registerForm->nickname = $nickname;
		$registerForm->username = $username;
		$registerForm->password = $password;//必须是对明文密码md5过后传过来的
		$registerForm->confirm_password = $confirm_password;
		$registerForm->code = $code;
		$registerForm->reg_ip = Yii::app()->request->userHostAddress;
		$registerForm->is_code = false;
		
		if(!$registerForm->validate()){
			$errors = $registerForm->getErrors();
			$this->responseClient('fail',array_pop($errors));
		}else{
			$userService = new UserService();
			if($source <= 0){
				$user['reg_source'] = USER_REG_SOURCE_SOUSHI_GAME;
			}else{
				$user['reg_source'] = $source;
			}
			$_user = $userService->saveUserBasic($user);
			
			if(empty($_user)){
				$this->responseClient('fail',$userService->getNotice());
			}
			
			//重置乐天必须要的nickname
			if(!Yii::app()->request->getParam('nickname')){
				$resetUser['uid'] = $_user['uid'];
				$resetUser['nickname'] = $this->app['app_enname'].'_'.$_user['uid'];
				$userService->saveUserBasic($resetUser);
			}
			
			if($userService->getNotice()){
				$notices = $userService->getNotice();
				$this->responseClient('fail',array_pop($notices));
			}else{
			   $consumeService = new ConsumeService();
			   $consumeService->saveUserConsumeAttribute(array('uid'=>$_user['uid'],'rank'=>0));
			   $identify = new PipiUserIdentity($username,$password);
			   $identify->api = true;
	 		   if($identify->authenticate()){
					Yii::app()->user->login($identify,USER_REGISTER_COOKIE_VTIME);
					//注册推广来源
					$reg = array();
					$returnUrl = Yii::app()->request->getUrlReferrer();
					$returnUrl = $returnUrl ? $returnUrl : Yii::app()->user->returnUrl;
					$reg['sign'] = $this->app['app_enname'];
					$reg['referer'] = $returnUrl;
					$reg['curl'] = $returnUrl;//这里是Ajax注册，所以注册的当前页面就是用户的前一页面
					$reg['access_time'] = time();
					$reg['uid'] = Yii::app()->user->id;
					$partnerService = new PartnerService();
					$partnerService->saveRegLog($reg);
					
					$this->responseClient('success',array('uid'=>Yii::app()->user->id,'nickname'=>$nickname));
	 		   }else{
	 		   		$this->responseClient('fail',$identify->errorMessage);
	 		   }
			}
		}
	}
	
	public function actionUserInfoByUid(){
		if($this->uid <= 0){
			$this->responseClient('fail',Yii::t('common','Parameter is empty'));
		}
		
		$userService = new UserService();
		$user = $userService->getUserBasicByUids(array($this->uid));
		if($user){
			$user = $user[$this->uid];
			if(isset($user['password'])){
				unset($user['password']);
			}
			if(isset($user['reg_salt'])){
				unset($user['reg_salt']);
			}
		}
		$this->responseClient('success',$user);
	}
	
	public function actionUserInfoByName(){
		$userName = Yii::app()->request->getParam('username');
		if(empty($userName)){
			$this->responseClient('fail',Yii::t('common','Parameter is empty'));
		}
		
		$userService = new UserService();
		$user = $userService->getVadidatorUser($userName,USER_LOGIN_USERNAME);
		if($user){
			if(isset($user['password'])){
				unset($user['password']);
			}
			if(isset($user['reg_salt'])){
				unset($user['reg_salt']);
			}
			$this->responseClient('success',$user);
		}
		$this->responseClient('fail',Yii::t('user','The user does not exist'));
	}
	
	public function actionPassword(){
		if($this->uid <= 0){
			$this->responseClient('fail',Yii::t('common','Parameter is empty'));
		}
		//orgpwd、newpwd、repwd这三个值必须是对用户初始明文md5加密过后传过来的
	    $pswd = Yii::app()->request->getParam('orgpwd');
	    $newpswd = Yii::app()->request->getParam('newpwd');
	    $repswd = Yii::app()->request->getParam('repwd');
		$uid = Yii::app()->request->getParam('uid');
		$loginUid = Yii::app()->user->id;
		/*
		if(!Yii::app()->user->isGuest){
			$this->responseClient('fail',Yii::t('common','Illegal login request'));
		}
		if($uid != $loginUid){
			$this->responseClient('fail',Yii::t('common','Illegal login request'));
		}*/
		if(empty($pswd) || empty($newpswd) || empty($repswd)){
			$this->responseClient('fail',Yii::t('common','Password can not be blank'));
		}
		if($newpswd != $repswd){
			$this->responseClient('fail',Yii::t('common','Inconsistent password twice'));
		}
		$userService = new UserService();
		$userInfo = $userService->getUserBasicByUids(array($uid));
		
		if(empty($userInfo)){
			$this->responseClient('fail',Yii::t('common','This user does not exist'));
		}
		if($userInfo){
			$pswd = md5($pswd.$userInfo[$uid]['reg_salt']);
			if($pswd != $userInfo[$uid]['password']){
				$this->responseClient('fail',Yii::t('common','Original password wrong'));
			}else{
				$userInfoChange = array('uid'=>$uid, 'password'=>$newpswd,'apiPassword'=>true);
				if(empty($userInfo[$uid]['nickname']) || trim($userInfo[$uid]['nickname']) == ''){
					$userInfoChange['nickname'] =  $this->app['app_enname'].'_'.$uid;
				}
				$newUserInfo = $userService->saveUserBasic($userInfoChange);
				if($newUserInfo['password']== md5($newpswd.$userInfo[$uid]['reg_salt'])){
					$this->responseClient('success',Yii::t('common',''));
				}
				$this->responseClient('fail',Yii::t('common',$userService->getError()));
			}
		}			
	}
	
	public function actionExchange(){
		if($this->isLogin){
			$uid = Yii::app()->user->id;
			$username = Yii::app()->user->name;
			$time = time();
			return Yii::app()->params['exchange'].'?act=login&id='.$uid.'&v='.md5('login'.$uid.$username.Yii::app()->params['verification_code']);
		}else {
			return '#';
		}
	}

	public function actionGetUserAvatar()
	{
		$uid = Yii::app()->request->getParam('dotey_uid');
		$userServ = new UserService();
		$avtar = $userServ->getUserAvatar($uid);
		$this->responseClient('success', $avtar);
	}
}

?>