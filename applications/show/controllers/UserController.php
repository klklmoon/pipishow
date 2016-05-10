<?php

define('USER_REGISTER_COOKIE_VTIME',3600*3);
/**
 * 用户相关的控制层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PublicController.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package 
 */
class UserController extends PipiController {
	const openPathSalt = "@%#R*@#$^*&BD$%Dfu6587gc291";
	
	protected $captchaParams  = array(
		'imageOptions'=>array('class'=>'fright','width'=>95,'height'=>40),
		'clickableImage'=>true,
		'showRefreshButton'=>false
	);
	
	public function actions(){
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,  //背景颜色
				'minLength'=>4,  //最短为4位
				'maxLength'=>4,   //是长为4位
				'transparent'=>true,  //显示为透明，当关闭该选项，才显示背景颜色
				'width'=>$this->captchaParams['imageOptions']['width'],
				'height'=>$this->captchaParams['imageOptions']['height'],
			),
		);
	}
	
	public function actionLogin(){
		$username = Yii::app()->request->getPost('username');
		$password = Yii::app()->request->getPost('password');
		$code = Yii::app()->request->getPost('code');
		$remember = Yii::app()->request->getPost('remember');
		$loginModel = new UserLoginForm();
		$loginModel->username = $username;
		$loginModel->password = $password;
		$loginModel->code = $code;
		
		if(!$loginModel->validate()){
			$error = $loginModel->getErrors();
			$errors=array();
			if(isset($error['code'])){
				$errors['errorMessage']=$error['code'][0];
				$errors['errorCode']=4;
			}elseif(isset($error['password'])){
				$errors['errorMessage']=$error['password'][0];
				$errors['errorCode']=2;
			}elseif(isset($error['username'])){
				$errors['errorMessage']=$error['username'][0];
				$errors['errorCode']=1;
			}else{
				$errors['errorMessage']=array_pop($error);
				$errors['errorCode']=5;
			}
			$response = array('status'=>'fail','message'=>$errors['errorMessage'],'code'=>$errors['errorCode']);
		}else{
			//绑定过手机的发送手机验证码验证
			$valid_phone=Yii::app()->request->cookies['valid_phone_'.$loginModel->getIdentity()->getId()];
			if($loginModel->getIdentity()->getId()&&!$valid_phone){
				$uid = $loginModel->getIdentity()->getId();
				$userService = new UserService();
				$user = $userService->getUserBasicByUids(array($uid));
				$user = $user[$uid];
				if($user['reg_mobile']){
					$extend = $userService->getUserExtendByUids(array($uid));
					if(!empty($extend)){
						$extend = $extend[$uid];
						if($extend['login_verify']){
							$response = array('status'=>'valid_phone','message'=>'','data'=>array('uid'=>$loginModel->getIdentity()->getId(),'username'=>$username,'password'=>$password,'remember'=>$remember));
							echo json_encode($response);
							Yii::app()->end();
						}
					}
				}
			}
			
			$duration = 0;
			if($remember){
				$duration = 3600*8;
			}
			Yii::app()->user->login($loginModel->getIdentity(),$duration);
			
			/*
			 * 下面新注册的用户都写入user_consume_attribute表信息，那么登陆的用户存在user_base比user_consume_attribute表多2百万的情况，即
			 * 老系统导入的非乐天用户，原先Thinkphp的老系统登陆时会把非乐天用户转化为乐天的user_base表用户，那我们现在也一样，把user_consume_attribute
			 * 表作为乐天用户表，非乐天的用户第一次登陆写入user_consume_attribute表信息即可
			 * @author hexin
			 * @date 2013-07-01
			 */
			$consumeService = new ConsumeService();
			$consume = $consumeService->getConsumesByUids(Yii::app()->user->id);
			if(empty($consume)){
				$consumeService->saveUserConsumeAttribute(array('uid'=>Yii::app()->user->id,'rank'=>0));
			}
			
			$response = array('status'=>'success','message'=>'');
			
		}
		echo json_encode($response);
		Yii::app()->end();
	}
	
	public function actionOpenLogin(){
		$type = Yii::app()->request->getParam('type');
		$code = Yii::app()->request->getParam('code');
		if(!in_array($type,array('qq','safe','sina','renren','baidu'))){
			$this->redirect($this->getTargetHref('index.php',false,true));
			Yii::app()->end();
		}
		$className = ucfirst($type).'OAuth';
		/* @var $openCommponent OAuth */
		$openCommponent = new $className();
		if(!$code){
			$loginUrl = $openCommponent->getAuthorizeURL('code');
			$referer = Yii::app()->request->getUrlReferrer();
			$referer = $referer ? $referer : Yii::app()->request->getHostInfo();
			Yii::app()->session['open_referer'] = $referer;
			$this->redirect($loginUrl);
		}else{
			try{
				$openCommponent->getAccessToken('code');
			}catch(OAuthException $e){
				$this->render('error',array('errorMsg'=>'您的开放账号存在异常，请稍后再登陆！'));
				die();
			}
			$openUser = $openCommponent->getUserInfo();
			if(empty($openUser)){
				$this->redirect(Yii::app()->request->getHostInfo());
			}
			$platform = $openCommponent->getOpenPlatform();
			$password = $platform.'_'.$openUser['open_id'].self::openPathSalt;
			$userService = new UserService();
			$userOauth = $userService->getUserOauthByOpenFlatform($platform,$openUser['open_id']);
			//已绑定 直接登录
			if($userOauth){
				$userBasic = $userService->getUserBasicByUids(array($userOauth['uid']));
				$userBasic = $userBasic[$userOauth['uid']];
				if(empty($userBasic)){
					$this->render('error',array('errorMsg'=>'您的开放账号不存在！'));
// 					$this->redirect($this->getTargetHref('index.php?r=public/error',false,true));
					die();
				}
				$identify = new PipiUserIdentity($userBasic['username'],$password);
				$identify->openUserInfo = $userOauth;
	 		   	if($identify->authenticate()){
	 		   		Yii::app()->user->login($identify,USER_REGISTER_COOKIE_VTIME);
	 		   		$referer = Yii::app()->session['open_referer'];
					unset(Yii::app()->session['open_referer']);
					
					$this->redirect($this->getTargetHref($referer,false,true));
	 		   	}else{
// 	 		   		$this->redirect($this->getTargetHref('index.php?r=public/error',false,true));
	 		   		$this->render('error',array('errorMsg'=>$identify->errorMessage));
	 		   	}
	 		   	die();
			}
			
			//新用户注册
			$userBasic['uid'] = $userService->getNextUid();
			$userBasic['username'] = $platform.'_'.$userBasic['uid'];
			$userBasic['password'] = $password;
			$userBasic['user_type'] = 1;
			$userBasic['reg_source'] = $userService->getUserRegEnSource($platform);
			if(isset($openUser['nickname']) && $openUser['nickname'] &&  $userService->getUserBasicByNickNames(array($openUser['nickname']))){
				$userBasic['nickname'] = $platform.'_'.$userBasic['uid'];
			}else{
				$userBasic['nickname'] = $openUser['nickname'];
			}
			
			$returnUser = $userService->saveUserBasic($userBasic);
			
			if(!$userService->getNotice() && $returnUser){
				$oauth['uid'] = $userBasic['uid'];
				$oauth['openid'] = $openUser['open_id'];
				$oauth['open_platform'] = $platform;
				$oauth['onickname'] = $openUser['nickname'];
				$userService->saveUserOauth($oauth);
				
				$consumeService = new ConsumeService();
				$consumeService->saveUserConsumeAttribute(array('uid'=>$userBasic['uid'],'rank'=>0));
			  	$identify = new PipiUserIdentity($userBasic['username'],$userBasic['password']);
	 		   	if($identify->authenticate()){
	 		   		$returnUrl = Yii::app()->request->getUrlReferrer();
					$returnUrl = $returnUrl ? $returnUrl : Yii::app()->user->returnUrl;
					Yii::app()->user->login($identify,USER_REGISTER_COOKIE_VTIME);
					$cookies = Yii::app()->request->getCookies();
					//注册推广来源
					$reg = array();
					if(isset($cookies['reg_sign']) && !empty($cookies['reg_sign']->value)){
						$reg['sign'] = $cookies['reg_sign']->value;
						$reg['referer'] = $cookies['reg_referer']->value;//注册页面的，前一个页面，由cookie维护
						$cookies->remove('reg_referer',array('expire'=>-3600,'value'=>null,'domain'=>DOMAIN,'path'=>'/'));
						$cookies->remove('reg_sign',array('expire'=>-3600,'value'=>null,'domain'=>DOMAIN,'path'=>'/'));
					}else{
						$reg['sign'] = 'nature';
						$reg['referer'] = Yii::app()->request->getUrlReferrer();
					}
					
					if($reg){
						$reg['curl'] = $returnUrl;//这里是Ajax注册，所以注册的当前页面就是用户的前一页面
						$reg['access_time'] = time();
						$reg['uid'] = $userBasic['uid'];
						$partnerService = new PartnerService();
						$partnerService->saveRegLog($reg);
					}
					$referer = Yii::app()->session['open_referer'];
					unset(Yii::app()->session['open_referer']);
					$this->redirect($this->getTargetHref($referer,false,true));
	 		   }else{
	 		   		$this->redirect($this->getTargetHref('index.php?r=public/error',false,true));
	 		   }
 			}
				
		}
		
	}
	
	public function actionLogout(){
		if(!Yii::app()->user->isGuest)
			Yii::app()->user->logout();
			
		$returnUrl = Yii::app()->request->getUrlReferrer();
		$returnUrl = $returnUrl ? $returnUrl : Yii::app()->user->returnUrl;
		$this->redirect($this->getTargetHref($returnUrl,false,true));
	}
	
	public function actionIsLogin(){
		echo (int) !Yii::app()->user->isGuest;
	}
	//用户注册
	public function actionRegister(){
		$username = Yii::app()->request->getPost('username');
		$password = Yii::app()->request->getPost('password');
		$confirm_password = Yii::app()->request->getPost('confirm_password');
		//$nickname = Yii::app()->request->getPost('nickname');
		$code = Yii::app()->request->getPost('code');
		$sign = Yii::app()->request->getPost('sign');
		
		$user['nickname'] = $username;
		$user['username'] = $username;
		//$user['reg_email'] = 'suqian@pipi.cn';
		$user['password'] = $password;
		$user['user_type'] = 1;
		
		$registerForm = new UserRegisterForm();
		$registerForm->nickname = $username;
		$registerForm->username = $username;
		$registerForm->password = $password;
		$registerForm->confirm_password = $confirm_password;
		$registerForm->code = $code;
		$registerForm->reg_ip = Yii::app()->request->userHostAddress;
		
		if(!$registerForm->validate()){
			$error = $registerForm->getErrors();
			$errors=array();
			if(isset($error['code'])){
				$errors['errorMessage']=$error['code'][0];
				$errors['errorCode']=4;
			}elseif(isset($error['password'])){
				$errors['errorMessage']=$error['password'][0];
				$errors['errorCode']=2;
			}elseif(isset($error['username'])){
				$errors['errorMessage']=$error['username'][0];
				$errors['errorCode']=1;
			}else{
				$errors['errorMessage']=array_pop($error);
				$errors['errorCode']=5;
			}
			$response = array('status'=>'fail','message'=>$errors['errorMessage'],'code'=>$errors['errorCode']);
		}else{
			$userService = new UserService();
			$_user = $userService->saveUserBasic($user);
			if($userService->getNotice() || empty($_user)){
				$notices = $userService->getNotice();
				$response = array('status'=>'fail','message'=>array_pop($notices),'code'=>1);
			}else{
			   $consumeService = new ConsumeService();
			   $consumeService->saveUserConsumeAttribute(array('uid'=>$_user['uid'],'rank'=>0));
			   $identify = new PipiUserIdentity($username,$password);
	 		   if($identify->authenticate()){
					Yii::app()->user->login($identify,USER_REGISTER_COOKIE_VTIME);
					$cookies = Yii::app()->request->getCookies();
					//注册推广来源
					$reg = array();
					$returnUrl = Yii::app()->request->getUrlReferrer();
					$returnUrl = $returnUrl ? $returnUrl : Yii::app()->user->returnUrl;
					if(isset($cookies['reg_sign']) && !empty($cookies['reg_sign']->value)){
						$reg['sign'] = $cookies['reg_sign']->value;
						$reg['referer'] = $cookies['reg_referer']->value;//注册页面的，前一个页面，由cookie维护
						$cookies->remove('reg_referer',array('expire'=>-3600,'value'=>null,'domain'=>DOMAIN,'path'=>'/'));
						$cookies->remove('reg_sign',array('expire'=>-3600,'value'=>null,'domain'=>DOMAIN,'path'=>'/'));
					}elseif($sign){
						$reg['sign'] =$sign;
					}else{
						$reg['sign'] = 'nature';
						$reg['referer'] = $returnUrl;
					}
					
					if($reg){
						$reg['curl'] = $returnUrl;//这里是Ajax注册，所以注册的当前页面就是用户的前一页面
						$reg['access_time'] = time();
						$reg['uid'] = Yii::app()->user->id;
						$partnerService = new PartnerService();
						$partnerService->saveRegLog($reg);
					}
					//新注册用户设置当天失效cookie
					if($sign){
						$operateService=new OperateService();
						$url=$operateService->getSpreadPrograme();
						$url.='?sign='.$sign;
						$response = array('status'=>'success','message'=>'','data'=>$url);
					}else{
						$response = array('status'=>'success','message'=>'','data'=>$_user['uid']);
					}
				}else{
					$response = array('status'=>'fail','message'=>$identify->errorMessage,'code'=>5);
	 		   }
			}
		}
		echo json_encode($response);
		Yii::app()->end();
	}
	//用户头像
	public function actionAvatar(){
		$uid = Yii::app()->request->getParam('uid');
		$input = Yii::app()->request->getParam('input');
		$step = Yii::app()->request->getParam('a');
		$type = Yii::app()->request->getParam('avatartype');
		$isLogin =(int) !Yii::app()->user->isGuest ;
		$uid = $uid ? $uid : Yii::app()->user->id;
		//$uid = 333333;
		if(!$isLogin || Yii::app()->user->id != $uid){
			$this->redirect($this->getTargetHref('index.php?r=user/login',false,true));
			die();
		}
		
		Yii::app()->detachEventHandler('onEndRequest',array(Yii::app()->log,'processLogs'));
		$upload = new PipiFlashUpload();
		echo $upload->renderHtml($uid);
		if($upload->processRequest($input,$step)){
			exit();
		}
		$this->viewer['avatar'] = $upload->getFileUrl($uid);
		$this->renderPartial('avatar',array());
	}
	//用户属性
	public function actionAttribute(){
		$isScriptFile = Yii::app()->request->getParam('isScriptFile');
		Yii::app()->detachEventHandler('onEndRequest',array(Yii::app()->log,'processLogs'));
		if($this->isLogin){
			$uid = Yii::app()->user->id;
			$user = $this->userService->getUserFrontsAttributeByCondition($uid,true,$this->isDotey);
		    $userJson = $user ? json_encode($user) : '{}';
		}else{
			$userJson =  '{}';
		}
		if($isScriptFile)
			echo $userJson;
		else{
			$accountBind = $check = 0;
			$newMsg = array();
			$messageCount = array('system' => 0, 'family' => 0, 'site' => 0);
			
			if($this->isLogin){
				//是否出现绑定提示，1为出现，0为不出现
				$userBasic = $this->userService->getVadidatorUser(Yii::app()->user->name,'username');
				$regMobile = $userBasic['reg_mobile'];
				$regEmail = $userBasic['reg_email'];
				$regTime = $userBasic['create_time'];
				if(time() - $regTime > 24*3600){
					if(empty($regEmail) && empty($regMobile)){
						$accountBind = 1;//上线更新为1
					}
				}
				
				//取出最后一条未读站内信
				$messageService = new MessageService();
				$messageRelationModel = new MessageRelationModel();
				$messageContentModel = new MessageContentModel();
				$message = $messageRelationModel->getUserMessageByUidsCondition(array('uid'=>$uid,'is_own'=>0,'is_read'=>0,'limit'=>1));
				$siteMessage = $messageContentModel->getUserUnReadSiteMessageNumsByUid($uid,1);
				if($siteMessage && $message){
					$message = array_pop($message);
					$siteMessage = array_pop($siteMessage);
					if($siteMessage['create_time'] > $message['create_time']){
						$message = $siteMessage;
					}
				}elseif($siteMessage){
					$message =  array_pop($siteMessage);
				}elseif($message){
					$message =  array_pop($message);
				}
				if($message){
					$newMsg['uid'] = $uid;
					$newMsg['title'] = $message['title'];
					$newMsg['s_title'] = $message['sub_title'];
					$newMsg['content'] = $message['content'];
					$newMsg['category'] = $message['category'];
					$newMsg['s_category'] = $message['sub_category'];
				}
				
				//是否签过到
				$checkinModel = new UserCheckinModel();
				$check = intval($checkinModel->isCheckin($uid));
				
				//消息提醒的未读消息数量
				$messageService = new MessageService();
				$unReads = $messageService->getUserMessageUnReads($uid);
				if($unReads){
					$messageCount['system'] = $unReads[$uid]['system_push'] > 0 ? $unReads[$uid]['system_push'] : 0;
					$familyCount = $unReads[$uid]['family_join']+$unReads[$uid]['family_manage']+$unReads[$uid]['family_upgrade'];
					$messageCount['family'] = $familyCount > 0 ? $familyCount : 0;
				}
				$messageCount['site'] = $messageService->countUserUnReadSiteMessagesByUid($uid);
			}
			echo "var user_attribute = {$userJson};\n";
			echo "var exchangeUrl='".$this->goExchange()."';\n";
			echo "var account_bind = {$accountBind};\n";
			echo "var newMessage = ".json_encode($newMsg).";\n";
			echo "var check = {$check};\n";
			echo "var messageCount = ".json_encode($messageCount).";\n";
		}
	}
	
	public function actionAttention(){
		if(!$this->isLogin){
			echo -1;
			exit();
		}
		$fansUid = Yii::app()->user->id;
		$attentionUid = Yii::app()->request->getPost('uid');
		if($fansUid <= 0 || $attentionUid <=0){
			echo -2;
			exit();
		}
		if($fansUid == $attentionUid){
			echo -3;
			exit();
		}
		$weiboService = new WeiboService();
		$weiboService->attentionUser($attentionUid,$fansUid);
		$doteyService = new DoteyService();
		if($doteyService->getDoteyInfoByUid($attentionUid)){
			$weiboService->attentionDotey($attentionUid,$fansUid);
		}
		echo 0;
		exit();
		
	}
	
	public function actionCancelAttention(){
		if(!$this->isLogin){
			echo -1;
			exit();
		}
		$fansUid = Yii::app()->user->id;
		$attentionUid = Yii::app()->request->getPost('uid');
		if($fansUid <= 0 || $attentionUid <=0){
			echo -2;
			exit();
		}
		$weiboService = new WeiboService();
		$weiboService->cancelAttentionedUser($attentionUid,$fansUid);
		$doteyService = new DoteyService();
		if($doteyService->getDoteyInfoByUid($attentionUid)){
			$weiboService->cancelDoteyAttentionedUser($attentionUid,$fansUid);
		}
		echo 0;
		exit();
	}
	
	public function actionRichRank(){
		$userService = new UserService();
		$type = Yii::app()->request->getParam('type');
		$rank = $userService->getUserRichRank($type);
		$this->renderPartial('richrank',array('rank'=>$rank));
	}
	
	public function actionFriendlyRank(){
		$userService = new UserService();
		$type = Yii::app()->request->getParam('type');
		$rank = $userService->getUserFriendlyRank($type);
		$this->renderPartial('friendlyrank',array('rank'=>$rank));
	}
	public function actionDoteyRank(){
		$userService = new UserService();
		$type = Yii::app()->request->getParam('type');
		$loginUid = $this->isLogin ? Yii::app()->user->id : 0;
		$rank = $userService->getUserCharmRank($type,$loginUid);
		if($type == 'today'){
			$desc = '今日护花';
		}elseif($type == 'week'){
			$desc = '本周护花';
		}elseif($type == 'month'){
			$desc = '本月护花';
		}elseif($type == 'super'){
			$desc = '超级护花';
		}
		$this->renderPartial('charmrank',array('rank'=>$rank,'desc'=>$desc));
	}
	
	public function actionFindPass(){
		$type = Yii::app()->request->getParam('type');
		$step = Yii::app()->request->getParam('step');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/account/zhanghu.css?token='.$this->hash,'all');
		if(empty($step)){
			$this->render('findpass');
		}elseif($step == 'method'){
			$cookie = Yii::app()->request->getCookies();
			$username = $cookie['findpass']->value;
			if(empty($username)){
				$this->redirect($this->createUrl('user/findPass'));
				Yii::app()->end();
			}
			$userService = new UserService();
			$user = $userService->getVadidatorUser($username,'username');
			
			if(empty($user)){
				$this->redirect($this->createUrl('user/findPass'));
				Yii::app()->end();
			}
			if(!isset($user['reg_mobile'])){
				$user['reg_mobile'] = '';
			}
			$this->render('findpass_method',$user);
		}elseif($step == 'waitMail'){
			$cookie = Yii::app()->request->getCookies();
			$username = $cookie['findpass']->value;
			if(empty($username)){
				$this->redirect($this->createUrl('user/findPass'));
				Yii::app()->end();
			}
			$userService = new UserService();
			$userBindService = new UserBindService();
			$user = $userService->getVadidatorUser($username,'username');
			
			if(empty($user)){
				$this->redirect($this->createUrl('user/findPass'));
				Yii::app()->end();
			}
			
			if(!preg_match("/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/",$user['reg_email'])){
				$this->redirect($this->createUrl('user/findPass'));
				Yii::app()->end();
			}
			
			
			$params = array();
			$emailInfo = explode('@',$user['reg_email']);
			$emailLen = strlen($emailInfo[0]);
			if($emailLen >= 4){
				$params['protected_email'] = substr($emailInfo[0],0,3).'***'.$emailInfo[0][$emailLen-1].'@'.$emailInfo[1];
			}else{
				$params['protected_email'] = $emailInfo[0][0].'***@'.$emailInfo[1];
			}
			$params['suffix'] = $emailInfo[1];
			$mailList = $userBindService->getMailList();
			$params['mail_href'] = isset($mailList[strtolower($emailInfo[1])]) ? $mailList[$emailInfo[1]] : 'http://mail.'.$emailInfo[1];
			$this->render('findpass_waitmail',$params);
		}elseif($step == 'mobile'){
			$cookie = Yii::app()->request->getCookies();
			$username = $cookie['findpass']->value;
			if(empty($username)){
				$this->redirect($this->createUrl('user/findPass'));
				Yii::app()->end();
			}
			$userService = new UserService();
			$userBindService = new UserBindService();
			$user = $userService->getVadidatorUser($username,'username');
			
			if(empty($user)){
				$this->redirect($this->createUrl('user/findPass'));
				Yii::app()->end();
			}
			$params = array();
			$this->render('findpass_mobile',$params);
		}elseif($step == 'kefu'){
			$this->render('findpass_kefu');
			Yii::app()->end();
		}elseif($step ==  'mail'){
			$cookie = Yii::app()->request->getCookies();
			$username = $cookie['findpass']->value;
			if(empty($username)){
				$this->redirect($this->createUrl('user/findPass'));
				Yii::app()->end();
			}
			$userService = new UserService();
			$userBindService = new UserBindService();
			$user = $userService->getVadidatorUser($username,'username');
			
			if(empty($user)){
				$this->redirect($this->createUrl('user/findPass'));
				Yii::app()->end();
			}
			
			if(!preg_match("/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/",$user['reg_email'])){
				$this->redirect($this->createUrl('user/findPass'));
				Yii::app()->end();
			}
			
			
			$params = array();
			$emailInfo = explode('@',$user['reg_email']);
			$emailLen = strlen($emailInfo[0]);
			$this->render('findpass_mail',array('suffix'=>$emailInfo[1]));
			Yii::app()->end();
		}elseif($step == 'scuccess'){
			$this->render('findpass_scuccess');
		}
	}
	
	
	public function actionFindUserName(){
		 $username = Yii::app()->request->getParam('username');
		 $code = Yii::app()->request->getParam('code');
		 
		 $cookie = Yii::app()->request->getCookies();
		 unset($cookie['findpass']);

		 if(empty($username)){
		 	echo json_encode(array('status'=>'fail','type'=>'username','message'=>'用户名不能为空'));
		 	Yii::app()->end();
		 }
		 
		 if(empty($code)){
		 	echo json_encode(array('status'=>'fail','type'=>'code','message'=>'验证码不能为空'));
		 	Yii::app()->end();
		 }
		 $captcha = $this->createAction('captcha');
		 $realCode = $captcha->getVerifyCode(false);
		 if($realCode != $code){
			echo json_encode(array('status'=>'fail','type'=>'code','message'=>'验证码错误'));
		 	Yii::app()->end();
		 }
		 
		 $userService = new UserService();
		 $user = $userService->getVadidatorUser($username,'username');
		 if(empty($user)){
		 	echo json_encode(array('status'=>'fail','type'=>'username','message'=>'用户不存在'));
		 	Yii::app()->end();
		 }
		 $captcha->getVerifyCode(true);
		 $cookie = new CHttpCookie('findpass',$user['username']);
		 Yii::app()->request->cookies['findpass']=$cookie;
		 echo json_encode(array('status'=>'scuccess','type'=>'','message'=>''));
		 Yii::app()->end();
		 
	}
	
	public function actionPassword(){
		$type = Yii::app()->request->getParam('type');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/account/zhanghu.css?token='.$this->hash,'all');
		$userBindService = new UserBindService();
		if($type == 'mail'){
			$ticket =  Yii::app()->request->getParam('ticket');
			$uid = Yii::app()->request->getParam('uid');
			if(empty($ticket) || $uid <= 0){
				$this->render('findpass_valid',array('type'=>$type));
				 Yii::app()->end();
			}
		
			$userBind = $userBindService->getNewBindByUid($uid,BIND_TYPE_FINDPASS_MAIL);
			
			if(empty($userBind)){
				$this->render('findpass_valid',array('type'=>$type));
				 Yii::app()->end();
			}
			
			$validTicket = $userBindService->getValidTicketByUid($uid,BIND_TYPE_FINDPASS_MAIL);
			
			if(empty($validTicket) || $validTicket['is_used']){
				$this->render('findpass_valid',array('type'=>$type));
				 Yii::app()->end();
			}elseif($validTicket['ticket'] != $ticket || $validTicket['create_time'] < time()-7*3600*24){
				$this->render('findpass_valid',array('type'=>$type));
				 Yii::app()->end();
			}else{
				$this->render('findpass_password',array('uid'=>$uid,'ticket'=>$ticket,'type'=>$type));
				Yii::app()->end();
			}
		}elseif($type == 'mobile'){
			$code = Yii::app()->request->getParam('code');
			$phone = Yii::app()->request->getParam('phone');
			$cookie = Yii::app()->request->getCookies();
			$username = $cookie['findpass']->value;
			if(empty($username)){
				$this->redirect($this->createUrl('user/findPass'));
				Yii::app()->end();
			}
			
			$userService = new UserService();
			$user = $userService->getVadidatorUser($username,'username');
			if(empty($user)){
				echo json_encode(array('status'=>'fail','type'=>'phone','message'=>'用户不存在'));
			 	Yii::app()->end();
			}
		
			if(!$user['reg_mobile'] ||  preg_match("/^\d{11}$/", $user['reg_mobile']) < 0){
				echo json_encode(array('status'=>'fail','type'=>'phone','message'=>'您还没有绑定过手机号'));
				Yii::app()->end();
			}
			if(preg_match("/^\d{11}$/", $phone) <= 0){
				echo json_encode(array('status'=>'fail','type'=>'phone','message'=>'手机号码格式不正确'));
				Yii::app()->end();
			}

			if(preg_match("/^\d{4}$/", $code) <= 0){
				echo json_encode(array('status'=>'fail','type'=>'code','message'=>'短信验证码必须是4个数字'));
				Yii::app()->end();
			}
			
			
			$userNewBind = $userBindService->getNewBindByUid($user['uid'],BIND_TYPE_FINDPASS_MOBILE);
			if(empty($userNewBind)){
				echo json_encode(array('status'=>'fail','type'=>'code','message'=>'您还没有发送短信验证码'));
				Yii::app()->end();
			}
			
			if($phone != $user['reg_mobile']){
				echo json_encode(array('status'=>'fail','type'=>'phone','message'=>'您输入的绑定手机号码不正确'));
				Yii::app()->end();
			}
			
			$userTicket = $userBindService->getValidTicketByUid($user['uid'],BIND_TYPE_FINDPASS_MOBILE);
			
			if(empty($userTicket) || $userTicket['ticket'] != $code || $userTicket['bind_id'] != $userNewBind['bind_id']){
				echo json_encode(array('status'=>'fail','type'=>'code','message'=>'短信验证码不正确'));
				Yii::app()->end();
			}
			
			if($userTicket['is_used'] || $userTicket['create_time'] < time() - SMS_EXPIRED_TIME){
				echo json_encode(array('status'=>'fail','type'=>'code','message'=>'短信验证码已已过期，请重新获取'));
				Yii::app()->end();
			}
			echo json_encode(array('status'=>'scuccess','message'=>$code));
			Yii::app()->end();
		}
	}
	
	public function actionSetPassword(){
		$type = Yii::app()->request->getParam('type');
		$password = Yii::app()->request->getParam('password');
		$rePassword = Yii::app()->request->getParam('repassword');
		
		
// 		if(strlen($password) <= 4 || strlen($password) > 20){
// 			echo json_encode(array('status'=>'fail','message'=>'密码长度必须在4-20个字符之间'));
// 		 	Yii::app()->end();
// 		}
		
		if($password != $rePassword){
			echo json_encode(array('status'=>'fail','message'=>'两次输入密码不一致'));
		 	Yii::app()->end();
		}
		$userBindService = new UserBindService();
		$userService = $this->userService;
		if($type == 'mail'){
			$ticket = Yii::app()->request->getParam('ticket');
			$uid = Yii::app()->request->getParam('uid');
			if($uid <= 0 || empty($ticket)){
				echo json_encode(array('status'=>'fail','message'=>'参数有误'));
			 	Yii::app()->end();
			}
			
			$user = $userService->getUserBasicByUids(array($uid));
			if(empty($user)){
				echo json_encode(array('status'=>'fail','message'=>'该用户不存在'));
			 	Yii::app()->end();
			}
			$userBind = $userBindService->getNewBindByUid($uid,BIND_TYPE_FINDPASS_MAIL);
				
			if(empty($userBind)){
				echo json_encode(array('status'=>'fail','message'=>'验证出错了'));
			 	Yii::app()->end();
			}
				
			$validTicket = $userBindService->getValidTicketByUid($uid,BIND_TYPE_FINDPASS_MAIL);
			
			if(empty($validTicket) || $validTicket['is_used']){
				echo json_encode(array('status'=>'fail','message'=>'邮件验证过期了'));
			 	Yii::app()->end();
			}elseif($validTicket['ticket'] != $ticket || $validTicket['create_time'] < time()-7*3600*24){
				echo json_encode(array('status'=>'fail','message'=>'邮件验证过期了'));
			 	Yii::app()->end();
			}else{
				$userInfoChange = array('uid'=>$uid, 'password'=>$password);
				$newUserInfo = $userService->saveUserBasic($userInfoChange);
				if($newUserInfo['password']==$userService->encryPassword($password,$user[$uid]['reg_salt'])){
					$userTicketModel = new UserTicketModel();
					$userTicketModel->updateByPk($validTicket['pass_id'],array('is_used'=>1));
					$cookie = Yii::app()->request->getCookies();
					unset($cookie['findpass']);
					echo json_encode(array('status'=>'scuccess','message'=>'修改密码成功了'));
			 		Yii::app()->end();
				}
				echo json_encode(array('status'=>'scuccess','message'=>'修改密码失败了'));		
				Yii::app()->end();
			}
		}elseif($type == 'mobile'){
			$cookie = Yii::app()->request->getCookies();
			$username = $cookie['findpass']->value;
			if(empty($username)){
				$this->redirect($this->createUrl('user/findPass'));
				Yii::app()->end();
			}
			
			$userService = new UserService();
			$user = $userService->getVadidatorUser($username,'username');
			if(empty($user)){
				echo json_encode(array('status'=>'fail','message'=>'用户不存在'));
			 	Yii::app()->end();
			}
			$uid = $user['uid'];
			$ticket = Yii::app()->request->getParam('ticket');
			$userBind = $userBindService->getNewBindByUid($uid,BIND_TYPE_FINDPASS_MOBILE);
				
			if(empty($userBind)){
				echo json_encode(array('status'=>'fail','message'=>'验证出错了'));
			 	Yii::app()->end();
			}
				
			$validTicket = $userBindService->getValidTicketByUid($uid,BIND_TYPE_FINDPASS_MOBILE);
			
			if(empty($validTicket) || $validTicket['is_used']){
				echo json_encode(array('status'=>'fail','message'=>'短信验证码过期了'));
			 	Yii::app()->end();
			}elseif($validTicket['ticket'] != $ticket || $validTicket['create_time'] < time()-SMS_EXPIRED_TIME){
				echo json_encode(array('status'=>'fail','message'=>'短信验证码过期了'));
			 	Yii::app()->end();
			}else{
				$userInfoChange = array('uid'=>$uid, 'password'=>$password);
				$newUserInfo = $userService->saveUserBasic($userInfoChange);
				if($newUserInfo['password']==$userService->encryPassword($password,$user['reg_salt'])){
					$userTicketModel = new UserTicketModel();
					$userTicketModel->updateByPk($validTicket['pass_id'],array('is_used'=>1));
					$cookie = Yii::app()->request->getCookies();
					unset($cookie['findpass']);
					echo json_encode(array('status'=>'scuccess','message'=>'修改密码成功了'));
			 		Yii::app()->end();
				}
				echo json_encode(array('status'=>'scuccess','message'=>'修改密码失败了'));		
				Yii::app()->end();
			}
		}
	}
	
	public function actionFind(){
		$type = Yii::app()->request->getParam('type');
		$cookie = Yii::app()->request->getCookies();
		$username = $cookie['findpass']->value;
		if(empty($username)){
			$this->redirect($this->createUrl('user/findPass'));
			Yii::app()->end();
		}
		
		$userService = new UserService();
		$user = $userService->getVadidatorUser($username,'username');
		if(empty($user)){
			echo json_encode(array('status'=>'fail','message'=>'用户不存在'));
		 	Yii::app()->end();
		}
		
		$userBindService = new UserBindService();
		
		if($type == 'mail'){
			$postMail = Yii::app()->request->getParam('email');
			$email = $user['reg_email'];
			if(empty($email)){
				echo json_encode(array('status'=>'fail','message'=>'您还没有绑定邮箱哦'));
		 		Yii::app()->end();
			}
			if(!preg_match("/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/",$email)){
				echo json_encode(array('status'=>'fail','message'=>'邮箱格式错误'));
		 		Yii::app()->end();
			}
			
			if($postMail && $postMail != $email){
				echo json_encode(array('status'=>'fail','message'=>'您找回密码的邮箱不正确'));
		 		Yii::app()->end();
			}
			$userBind['uid'] = $user['uid'];
			$userBind['method'] = BIND_TYPE_FINDPASS_MAIL;
			$userBind['method_content'] = $email;
			$existBind = $userBindService->getValidBindByUid($user['uid'],BIND_TYPE_FINDPASS_MAIL,$email);
			if(!$existBind){
				$bindId = $userBindService->saveUserBind($userBind);
			}else{
				$bindId = $existBind['bind_id'];
				$userBindModel = UserBindModel::model();
				$userBindModel->updateByPk($bindId,array('create_time'=>time()));
				
			}
			
			if($bindId){
				$userTicket['uid'] = $user['uid'];
				$userTicket['bind_id'] = $bindId;
				$userTicket['type'] = BIND_TYPE_FINDPASS_MAIL;
				if($userBindService->saveUserTicket($userTicket)){
					$return = $userBindService->sendFindPassMail($user['uid'],$email);
					echo json_encode(array('status'=>'scuccess','message'=>$email));
					Yii::app()->end();
				}else{
					echo json_encode(array('status'=>'fail','message'=>'绑定出现错误'));
					Yii::app()->end();
				}
			}
		 	Yii::app()->end();
		}elseif($type == 'mobile'){
			$phone = Yii::app()->request->getParam('phone');
			if(!$user['reg_mobile'] ||  preg_match("/^\d{11}$/", $user['reg_mobile']) <= 0){
				echo json_encode(array('status'=>'fail','message'=>'您没有绑定过手机号码'));
				Yii::app()->end();
			}
			if(preg_match("/^\d{11}$/", $phone) <= 0){
				echo json_encode(array('status'=>'fail','message'=>'手机号码格式不正确'));
				Yii::app()->end();
			}
			
			if($phone != $user['reg_mobile']){
				echo json_encode(array('status'=>'fail','message'=>'您输入的手机号码不正确'));
				Yii::app()->end();
			}
			$count = $userBindService->countTodayValidTicket($user['uid'],BIND_TYPE_FINDPASS_MOBILE);
			if($count >= 3){
				echo json_encode(array('status'=>'fail','message'=>'您今天手机找回密码的次数超过三次了'));
				Yii::app()->end();
			}
			$userBind['uid'] = $user['uid'];
			$userBind['method'] = BIND_TYPE_FINDPASS_MOBILE;
			$userBind['method_content'] = $phone;
			$existBind = $userBindService->getValidBindByUid($user['uid'],BIND_TYPE_FINDPASS_MOBILE,$phone);
			
			if(!$existBind){
				$bindId = $userBindService->saveUserBind($userBind);
			}else{
				$bindId = $existBind['bind_id'];
				$userBindModel = UserBindModel::model();
				$userBindModel->updateByPk($bindId,array('create_time'=>time()));
				
			}
			
			if($bindId){
				$userTicket['uid'] = $user['uid'];
				$userTicket['bind_id'] = $bindId;
				$userTicket['type'] = BIND_TYPE_FINDPASS_MOBILE;
				$userTicket['ticket'] = $userBindService->getPhoneCode();
				if($userBindService->saveUserTicket($userTicket)){
					$return = $userBindService->sendFindPassSms($phone,$userTicket['ticket']);
					if($return['status'] == 'success'){
						echo json_encode(array('status'=>'scuccess','message'=>$phone));
					}else{
						echo json_encode(array('status'=>'fail','message'=>$return['info']));
					}
					Yii::app()->end();
				}else{
					echo json_encode(array('status'=>'fail','message'=>'绑定出现错误'));
					Yii::app()->end();
				}
			}
			Yii::app()->end();
				
	
		}
	}
	

	
	public function actionViewStar(){
		if(!$this->isLogin){
			echo json_encode(array('status'=>'fail','message'=>'您还没有登录哦'));
			Yii::app()->end();
		}
		$uid = Yii::app()->user->id;
		if($uid <= 0){
			echo json_encode(array('status'=>'fail','message'=>'操作错误'));
			Yii::app()->end();
		}
		$userStarRankModel = new StarsRankModel();
		$userStarsRecordModel = new StarsRecordModel();
		$userPipiEggModel = UserPipiEggRecordsModel::model();
		$userJson = $this->userService->getUserFrontsAttributeByCondition($uid,true,$this->isDotey);
		$star = $userJson['st']?$userJson['st']:0;
		$newStarsRecord = $userStarsRecordModel->getNewStarRecords();
		$newStarsRecord = $newStarsRecord->attributes;
		$consumePipieggs = $userPipiEggModel->sumPipieggs(array('uid'=>$uid,'startTime'=>$newStarsRecord['start_time'],'endTime'=>$newStarsRecord['end_time']));
		$consumePipieggs = $consumePipieggs[0]['sum_pipiegg'];
		$realstar = $userStarRankModel->getStars($consumePipieggs);
		$realstar = $realstar->stars ? $realstar->stars : 0;
		$nextStar = $realstar+1;
		$returnArray['cst'] = $realstar>=$star?$realstar:$star;
		$returnArray['rst'] = $realstar;
		if($nextStar<=10){
			$userStars = $userStarRankModel->findByAttributes(array('stars'=>$nextStar));
			$returnArray['nst'] = $nextStar;
			$returnArray['npipiegg'] = number_format($userStars->pipiegg - $consumePipieggs, 2, '.', '');
		}
		$returnArray['startTime']=$newStarsRecord['start_time'];
		$returnArray['endTime']=$newStarsRecord['end_time']-1;
		echo json_encode(array('status'=>'success','message'=>$returnArray));
		Yii::app()->end();
	}
	
	public function actionCheckUserAward(){
		if(!$this->isLogin){
			echo json_encode(array('status'=>'fail','message'=>'您还没有登录哦'));
			Yii::app()->end();
		}
		$uid = Yii::app()->user->id;
		if($uid <= 0){
			echo json_encode(array('status'=>'fail','message'=>'操作错误'));
			Yii::app()->end();
		}
		$service = new FirstChargeGiftsService();
		$flag_one=$service->collectGifts($uid, FirstChargeGiftsService::ACTIVITY_TYPE_ONE);
		$flag_two=$service->collectGifts($uid, FirstChargeGiftsService::ACTIVITY_TYPE_TWO);
		if($flag_one!=1&&$flag_two!=1){
			exit(json_encode(array('flag'=>1)));
		}
		exit(json_encode(array('flag'=>0)));
	}
	
	public function actionGetPhoneCode(){
		$uid = Yii::app()->request->getParam('uid');
		$userService = new UserService();
		$user = $userService->getUserBasicByUids(array($uid));
		if(isset($user[$uid]['reg_mobile'])){
			$pipiSMS=new PipiSMS();
			$code=$pipiSMS->getSMSCode();
			$flag=$pipiSMS->directSendSMSs(array($user[$uid]['reg_mobile']),'您好，欢迎登录皮皮乐天，您的安全登录验证码为：'.$code);
			if($flag){
				exit(json_encode(array('flag'=>1,'message'=>$flag)));
			}
			exit(json_encode(array('flag'=>0,'message'=>'手机验证码已发送失败')));
		}
		exit(json_encode(array('flag'=>0,'message'=>'账户未绑定手机号')));
	}
	
	public function actionValidPhoneCode(){
		$username = Yii::app()->request->getParam('username');
		$password = Yii::app()->request->getParam('password');
		$remember = Yii::app()->request->getParam('remember');
		$code = Yii::app()->request->getParam('code');
		if($code){
			$pipiSMS=new PipiSMS();
			$flag=$pipiSMS->validSMSCode($code);
			if($flag){
				$duration = 0;
				if($remember){
					$duration = 3600*8;
				}
				$loginModel = new UserLoginForm();
				$loginModel->username=$username;
				$loginModel->password=$password;
				$loginModel->validate();
				Yii::app()->user->login($loginModel->getIdentity(),$duration);
				$consumeService = new ConsumeService();
				$consume = $consumeService->getConsumesByUids(Yii::app()->user->id);
				if(empty($consume)){
					$consumeService->saveUserConsumeAttribute(array('uid'=>Yii::app()->user->id,'rank'=>0));
				}
				$cookie =new CHttpCookie('valid_phone_'.Yii::app()->user->id,1);
				$cookie->expire =time()+24*3600;
				Yii::app()->request->cookies['valid_phone']=$cookie;
				exit(json_encode(array('flag'=>1,'message'=>'')));
			}else{
				if(!Yii::app()->user->isGuest){
					Yii::app()->user->logout();
				}
				exit(json_encode(array('flag'=>0,'message'=>'验证码错误，请重新输入')));
			}
		}else{
			exit(json_encode(array('flag'=>0,'message'=>'请输入验证码')));
		}
		
	}
	
	
	
}

?>