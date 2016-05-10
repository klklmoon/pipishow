<?php
/**
 * 提供iphone数据调用接口
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $
 * @package
 */
define('USER_REGISTER_COOKIE_VTIME',3600*3);
class ApiController extends PipiController {
	const openPathSalt = "@%#R*@#$^*&BD$%Dfu6587gc291";
	
	public $params=array();

	protected $sign;

	public $checkSign=true;
	
	public $logPath;

	public $validTime;
	
	public $page=1;
	
	public $pagesize=50;
	
	private $apnsHost = 'gateway.sandbox.push.apple.com';   //沙盒地址
	//private $apnsHost = 'gateway.push.apple.com';           //发布地址
	
	private $verifyReceipt='https://sandbox.itunes.apple.com/verifyReceipt'; //订单二次验证沙盒地址
	//private $verifyReceipt='https://buy.itunes.apple.com/verifyReceipt'; //订单二次验证发布地址
	
	private $alipayOrderInfoUrl='http://183.247.180.167/Mobilepay/get_alipay_orderinfo';
	//private $alipayOrderInfoUrl='http://useraccount.pipi.cn/Mobilepay/get_alipay_orderinfo';
	
	private $pay19OrderInfoUrl='http://183.247.180.167/Mobilepay/payment_19pay';
	
	private $tenpayOrderInfoUrl='http://183.247.180.167/Mobilepay/get_tenpay_url';
	
	private $payAppsecret='1f2e0z63fe55d0m193ob1a66il230e69e9a33425';
	
	private $apnsPort = '2195';
	private $sslPem = 'ck.pem';
	private $passPhrase = 'letian';
	
	private $apnsConnection;
	
	private $apple_product_list=array(array('product_id'=>'com.Letian.one1','pipiegg'=>400,'dollar'=>0.99),
				array('product_id'=>'com.Letian.five','pipiegg'=>2100,'dollar'=>4.99),
				array('product_id'=>'com.Letian.ten','pipiegg'=>4200,'dollar'=>9.99),
				array('product_id'=>'com.Letian.twenty','pipiegg'=>8400,'dollar'=>19.99),
				array('product_id'=>'com.Letian.thirty','pipiegg'=>12600,'dollar'=>29.99),
				array('product_id'=>'com.Letian.fifty','pipiegg'=>21000,'dollar'=>49.99)
			);
	private $sessionId=null;

	protected $action=array('register','login','getArchivesInfo','getArchivesList',
							'getGiftList','getChatSource','getUserInfo',
							'sendGift','sendBagGift','getArchivesToken',
							'sendGift','getUserAttentionArchives','getUserGiftSendRecords',
							'getUserViewRecords','saveUserViewRecords','saveSuggest',
							'addAttention','removeAttention','checkin',
							'captch','openLogin','getPipieggList',
							'pushNotice','getTruckGiftRecord','getUserIsAttention',
							'cancelPushNotice','getUserBag','isCheckin',
							'checkRecharge','checkReceiptData','getAttentions','getFans',
							'modifyPassword','modifyNickname','modifyArea',
							'modifyAvatar','getOnline','getRankList','getGiftRank',
							'searchUser','getVip','getVipPurview','buyVip','getCar','buyCar','version',
							'checkLogin','getTags','getNotices','getAlipayOrder','getArchivesParkingCar'
							,'parkingArchivesCar','getDynamicList','getDynamicInfo','dynamic','deleteDynamic',
							'getCommentList','comment','deleteComment','getUserAlbum','getBigPic','uploadAlbum',
							'deleteAlbum','modifyGender','getpay19Order','gettenpayOrder','getArchivesUser','getDoteySong','demandSong','getCategory');

	public function init(){
		foreach(array_merge($_GET,$_POST) as $key=>$val){
             $this->params[$key]=$val;
        }
        unset($this->params['submit']);
        $this->validTime=Yii::app()->params['api_config']['valid_time'];
        if(isset($this->params['session_id'])){
        	$this->sessionId=$this->params['session_id'];
        }
		unset($this->params['r']);
		$this->logPath=DATA_PATH.'runtimes/phoneApi.txt';
	}
	public function actionIndex(){
		if(self::checkSign()){
			switch($this->params['action']){
				case 'register':$this->register();break;
				case 'login':$this->login();break;
				case 'getArchivesInfo':$this->getArchivesInfo();break;
				case 'getArchivesList':$this->getArchivesList();break;
				case 'getGiftList':$this->getGiftList();break;
				case 'getChatSource':$this->getChatSource();break;
				case 'getUserInfo':$this->getUserInfo();break;
				case 'getArchivesToken':$this->getArchivesToken();break;
				case 'sendGift':$this->sendGift();break;
				case 'getUserAttentionArchives':$this->getUserAttentionArchives();break;
				case 'getUserGiftSendRecords':$this->getUserGiftSendRecords();break;
				case 'getUserViewRecords':$this->getUserViewRecords();break;
				case 'saveUserViewRecords':$this->saveUserViewRecords();break;
				case 'saveSuggest':$this->saveSuggest();break;
				case 'addAttention':$this->addAttention();break;
				case 'removeAttention':$this->removeAttention();break;
				case 'checkin':$this->checkin();break;
				case 'captch':$this->captch();break;
				case 'openLogin':$this->openLogin();break;
				case 'getPipieggList':$this->getPipieggList();break;
				case 'pushNotice':$this->pushNotice();break;
				case 'getTruckGiftRecord':$this->getTruckGiftRecord();break;
				case 'getUserIsAttention':$this->getUserIsAttention();break;
				case 'cancelPushNotice' :$this->cancelPushNotice();break;
				case 'getUserBag':$this->getUserBag();break;
				case 'isCheckin':$this->isCheckin();break;
				case 'checkRecharge':$this->checkRecharge();break;
				case 'checkReceiptData':$this->checkReceiptData();break;
				case 'getAttentions':$this->getAttentions();break;
				case 'getFans':$this->getFans();break;
				case 'modifyPassword':$this->modifyPassword();break;
				case 'modifyNickname':$this->modifyNickname();break;
				case 'modifyArea':$this->modifyArea();break;
				case 'modifyAvatar':$this->modifyAvatar();break;
				case 'getOnline':$this->getOnline();break;
				case 'getRankList':$this->getRankList();break;
				case 'getGiftRank':$this->getGiftRank();break;
				case 'searchUser':$this->searchUser();break;
				case 'getVip':$this->getVip();break;
				case 'getVipPurview':$this->getVipPurview();break;
				case 'buyVip':$this->buyVip();break;
				case 'getCar':$this->getCar();break;
				case 'buyCar':$this->buyCar();break;
				case 'version':$this->version();break;
				case 'getTags':$this->getTags();break;
				case 'getNotices':$this->getNotices();break;
				case 'getAlipayOrder':$this->getAlipayOrder();break;
				case 'checkLogin':$this->checkLogin();break;
				case 'getArchivesParkingCar':$this->getArchivesParkingCar();break;
				case 'parkingArchivesCar':$this->parkingArchivesCar();break;
				case 'getDynamicList':$this->getDynamicList();break;
				case 'dynamic':$this->dynamic();break;
				case 'deleteDynamic':$this->deleteDynamic();break;
				case 'getCommentList':$this->getCommentList();break;
				case 'getDynamicInfo':$this->getDynamicInfo();break;
				case 'comment':$this->comment();break;
				case 'deleteComment':$this->deleteComment();break;
				case 'getUserAlbum':$this->getUserAlbum();break;
				case 'getBigPic':$this->getBigPic();break;
				case 'uploadAlbum':$this->uploadAlbum();break;
				case 'deleteAlbum':$this->deleteAlbum();break;
				case 'modifyGender':$this->modifyGender();break;
				case 'getpay19Order':$this->getpay19Order();break;
				case 'gettenpayOrder':$this->gettenpayOrder();break;
				case 'getArchivesUser':$this->getArchivesUser();break;
				case 'getDoteySong':$this->getDoteySong();break;
				case 'demandSong':$this->demandSong();break;
				case 'getCategory':$this->getCategory();break;
				default :break;

			}

		}
	}
	
	
	
	public function actionCaptcha(){
		$uuid=Yii::app()->request->getParam('v');
		$CCaptchaAction=new CCaptchaAction(Yii::app()->getController(), Yii::app()->getId());
		$CCaptchaAction->backColor=0xFFFFFF;
		$CCaptchaAction->minLength=4;
		$CCaptchaAction->maxLength=4;
		$CCaptchaAction->transparent=true;
		$CCaptchaAction->width=95;
		$CCaptchaAction->height=40;
		$code=$CCaptchaAction->getVerifyCode(true);
		$CCaptchaAction->renderImage($code);
		$phoneService=new PhoneService();
		$phoneService->savePhoneCode($uuid,$code,300);
		error_log(date("Y-m-d H:i:s")."存入redis中code:".$code."\n\r",3,$this->logPath);
	}
	
	public function actionPushLivingNotice(){
		$userIosService=new UserIosService();
		$archivesService=new ArchivesService();
		$userService=new UserService();
		$pushUser=$userIosService->getUserIosByNotice();
		$liveRecordsModel=new LiveRecordsModel();
		$livingArchives=$liveRecordsModel->getLivingArchives();
		$livingArchives=$archivesService->arToArray($livingArchives);
		$living=array();
		if($livingArchives){
			foreach($livingArchives as $row){
				if($row['live_time']<=time()&&$row['live_time']>=(time()-600)){
					$archivesInfo=$archivesService->getArchivesByArchivesId($row['archives_id']);
					if($archivesInfo){
						$living[$archivesInfo['uid']]=$archivesInfo;
					}
				}
			}
		}
		$archives=array();
		$weiboService=new WeiboService();
		if($pushUser){
			foreach($pushUser as $row){
				if($row['type']==0 &&$row['notice']==1){
					$attention=$weiboService->getUserAttentionsByUid($row['uid']);
					foreach($attention as $val){
						if(isset($living[$val['uid']])){
							$archives[]=array('uid'=>$row['uid'],
								'device_token'=>$row['device_token'],
								'badge'=>$row['badge'],
								'sound'=>$row['sound'],
								'livingArchives'=>$living[$val['uid']]);
						}
					}
				}
			}
		}
		if($archives){
			foreach($archives as $row){
				if(!empty($row['livingArchives'])){
					$userBase=$userService->getUserBasicByUids(array($row['livingArchives']['uid']));
					$pushData=array('aid'=>(int)$row['livingArchives']['archives_id'],'uid'=>(int)$row['livingArchives']['uid'],'nk'=>$userBase[$row['livingArchives']['uid']]['nickname']);
					$title='您关注的'.$userBase[$row['livingArchives']['uid']]['nickname'].'开始直播了，快来看吧~';
					$flag=self::sendToAPNS($row['device_token'], $pushData,$title, $row['badge'],$row['sound']);
					if($flag){
						echo '用户uid:'.$row['uid'].',用户设备号：'.$row['device_token'].',内容：'.$title.'<br/>';
					}
				}
	
			}
		}
	}

	public function register(){
		$username=isset($this->params['username'])?$this->params['username']:'';
		$password=isset($this->params['password'])?$this->params['password']:'';
		$confirm_password=isset($this->params['confirm_password'])?$this->params['confirm_password']:'';
		$email=isset($this->params['email'])?$this->params['email']:'';
		$gender=isset($this->params['gender'])?$this->params['gender']:'';
		$nickname=isset($this->params['nickname'])?$this->params['nickname']:'';
		$code=isset($this->params['code'])?$this->params['code']:'';
		$uuid=isset($this->params['uuid'])?$this->params['uuid']:'';
		$device_id=isset($this->params['device_id'])?$this->params['device_id']:'';
		$channel_id=isset($this->params['channel_id'])?$this->params['channel_id']:'';
		if(empty($username)||empty($password)||empty($confirm_password)){
			$this->jsonpReturn('-16',2,Yii::t('api','params not empty'));
		}
		if(strlen($username)<4||strlen($username)>16){
			$this->jsonpReturn('-21',2,Yii::t('api','Account in length from 4 to 16 characters'));
		}
		if($nickname){
			if(strlen($nickname)<2||strlen($nickname)>16){
				$this->jsonpReturn('-22',2,Yii::t('api','Nickname in length from 2 to 16 characters'));
			}
		}
		if($gender){
			if(!in_array($gender,array(0,1,2))){
				$this->jsonpReturn('-23',2,Yii::t('api','Gender parameter error'));
			}
		}

		if($password!=$confirm_password){
			$this->jsonpReturn('-24',2,Yii::t('api','The two passwords do not match'));
		}
		
		if($email){
			if(!preg_match('/([\w\.\_]{2,10})@(\w{1,}).([a-z]{2,4})/',$email)){
				$this->jsonpReturn('-25',2,Yii::t('api','Email format error'));
			}
		}
		if($code&&$uuid){
			$phoneService=new PhoneService();
			$orgCode=$phoneService->getPhoneCode($uuid);
			error_log(date("Y-m-d H:i:s")."验证码：".$orgCode."\n\r",3,$this->logPath);
			if($code!=$orgCode){
				$this->jsonpReturn('-26',2,Yii::t('api','The code is incorrect'));
			}
		}
		$reg_ip=Yii::app()->request->userHostAddress;
		$user['nickname'] = $nickname?$nickname:$username;
		$user['username'] = $username;
		$user['reg_email'] = $email;
		$user['password'] = $password;
		$user['user_type'] = 1;
		$user['reg_source'] = 4;
		$user['reg_ip']=$reg_ip;
		$registerForm = new UserRegisterForm();
		$registerForm->nickname = $nickname?$nickname:$username;
		$registerForm->username = $username;
		$registerForm->password = $password;
		$registerForm->confirm_password = $confirm_password;
		$registerForm->reg_ip = $reg_ip;
		$registerForm->code = $code;
		$registerForm->is_code = false;
		
		if(!$registerForm->validate()){
			$errors = $registerForm->getErrors();
			$this->jsonpReturn('-27',2,array_pop($errors));
		}
		$userService = new UserService();
		$_user = $userService->saveUserBasic($user);
		if($userService->getNotice()){
			$errors=$userService->getNotice();
			if(isset($errors['username'])){
				$this->jsonpReturn('-28',2,Yii::t('api','The username {username} is occupied',array('{username}'=>$username)));
			}
			if(isset($errors['nickname'])){
				$this->jsonpReturn('-28',2,Yii::t('api','The nickname {nickname} is occupied',array('{nickname}'=>$nickname)));
			}
		}
		$consumeService = new ConsumeService();
	    $consumeService->saveUserConsumeAttribute(array('uid'=>$_user['uid'],'rank'=>0));
		$identify = new PipiUserIdentity($username,$password);
		if(!$identify->authenticate()||$consumeService->getError()){
			$this->jsonpReturn('-29',2,Yii::t('api','register failed'));
		}
		Yii::app()->user->login($identify,USER_REGISTER_COOKIE_VTIME);
		Yii::app()->getSession()->regenerateID(true);
		$sessionId=Yii::app()->getSession()->getSessionID();
		$sessionRedisModel=new SessionRedisModel();
		$sessionRedisModel->saveMobileUserSessionId($_user['uid'], $sessionId);
		if($device_id&&$channel_id){
			$userIosService=new UserIosService();
			$androidSet['uid']=$_user['uid'];
			$androidSet['type']=MOBILE_ANDROID_TYPE;
			$androidSet['user_id']=$device_id;
			$androidSet['channel_id']=$channel_id;
			$userAndroidSet=$userIosService->getUserAndroidByCondition($androidSet);
			if(!$userAndroidSet){
				$androidSet['notice']=IOS_PUSH_NOTICE_ON;
				$userIosService->saveUserAndroidSet($androidSet);
			}
		}
		if($gender){
			$userService->saveUserExtend(array('uid'=>$_user['uid'],'gender'=>$gender));
		}
		$this->jsonpReturn('200',2,'success',array('uid'=>(int)$_user['uid'],'username'=>$username,'email'=>$email,'session_id'=>$sessionId));
	}
	
	

	public function login(){
		$username=isset($this->params['username'])?$this->params['username']:'';
		$password=isset($this->params['password'])?$this->params['password']:'';
		$device_id=isset($this->params['device_id'])?$this->params['device_id']:'';
		$channel_id=isset($this->params['channel_id'])?$this->params['channel_id']:'';
		if(empty($username)||empty($password)){
			$this->jsonpReturn('-16',3,Yii::t('api','params not empty'));
		}
		if(strlen($username)<4||strlen($username)>16){
			$this->jsonpReturn('-31',3,Yii::t('api','Account in length from 4 to 16 characters'));
		}

		$loginModel = new UserLoginForm();
		$loginModel->username = $username;
		$loginModel->password = $password;
		if(!$loginModel->validate()){
			$this->jsonpReturn('-33', 3, Yii::t('api','Username or passwrod error'));
		}
		Yii::app()->user->login($loginModel->getIdentity(),0);
		$userService=new UserService();
		$userBase=$userService->getUserBasicByUserNames(array($username));
		$userBase=array_pop($userBase);
		Yii::app()->getSession()->regenerateID(true);
		$sessionId=Yii::app()->getSession()->getSessionID();
		$sessionRedisModel=new SessionRedisModel();
		$sessionRedisModel->saveMobileUserSessionId($userBase['uid'], $sessionId);
		if($device_id&&$channel_id){
			$userIosService=new UserIosService();
			$androidSet['uid']=$userBase['uid'];
			$androidSet['type']=MOBILE_ANDROID_TYPE;
			$androidSet['user_id']=$device_id;
			$androidSet['channel_id']=$channel_id;
			$userAndroidSet=$userIosService->getUserAndroidByCondition($androidSet);
			if(!$userAndroidSet){
				$androidSet['notice']=IOS_PUSH_NOTICE_ON;
				$userIosService->saveUserAndroidSet($androidSet);
			}
		}
		$userService=new UserService();
		$userInfo['big_avatar']=$userService->getUserAvatar($userBase['uid'],'big');
		$userInfo['middle_avatar']=$userService->getUserAvatar($userBase['uid'],'middle');
		$userInfo['small_avatar']=$userService->getUserAvatar($userBase['uid'],'small');
		$userExtend=$userService->getUserExtendByUids(array($userBase['uid']));
		$consumeService=new ConsumeService();
		$userConsume=$consumeService->getConsumesByUids($userBase['uid']);
		(int)$userInfo['pipiegg']=$userConsume[$userBase['uid']]['pipiegg']-$userConsume[$userBase['uid']]['freeze_pipiegg'];
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByUid($userBase['uid']);
		$archives_id=0;
		if($archives){
			$archives=array_pop($archives);
			$archives_id=$archives['archives_id'];
		}
		$data=array('uid'=>(int)$userBase['uid'],
			'archives_id'=>(int)$archives_id,
			'nickname'=>$userBase['nickname'],
			'gender'=>(int)$userExtend[$userBase['uid']]['gender'],
			'big_avatar'=>$userInfo['big_avatar'],
			'middle_avatar'=>$userInfo['middle_avatar'],
			'small_avatar'=>$userInfo['small_avatar'],
			'pipiegg'=>$userInfo['pipiegg'],
			'session_id'=>$sessionId);
		$this->jsonpReturn('200',3,'success',$data);
	}
	
	public function getArchivesInfo(){
		$archives_id=isset($this->params['archives_id'])?$this->params['archives_id']:'';
		if($archives_id<=0){
			$this->jsonpReturn('-13',4, Yii::t('api','illegal params'));
		}
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByArchivesId($archives_id);
		if(empty($archives)){
			$this->jsonpReturn('-41',4, Yii::t('api','illegal params'));
		}
		$timeStamp=time();
		$archivesInfo=array();
		$archivesInfo['archives_id']=(int)$archives['archives_id'];
		$archivesInfo['record_id']=isset($archives['live_record']['record_id'])?(int)$archives['live_record']['record_id']:'';
		$archivesInfo['title']=$archives['title'];
		$archivesInfo['uid']=(int)$archives['uid'];
		$dotey=$archivesService->getArchivesUserByArchivesIds($archives_id);
		$doteyUser=array();
		foreach($dotey[$archives_id] as $row){
			$doteyUser[]=(int)$row['uid'];
		}
		$archivesInfo['dotey']=$doteyUser;
		$userService=new UserService();
		$userBase=$userService->getUserBasicByUids(array($row['uid']));
		$archivesInfo['nickname']=$userBase[$row['uid']]['nickname'];
		$archivesInfo['big_avatar']=$userService->getUserAvatar($archives['uid'],'big');
		$archivesInfo['middle_avatar']=$userService->getUserAvatar($archives['uid'],'middle');
		$archivesInfo['small_avatar']=$userService->getUserAvatar($archives['uid'],'small');
		$doteyService=new DoteyService();
		$archivesInfo['big_cover']=$doteyService->getDoteyUpload($archives['uid'],'big','display');
		$archivesInfo['middle_cover']=$doteyService->getDoteyUpload($archives['uid'],'small','display');
		$common_notice=unserialize($archives['notice']);
		$archivesInfo['common_notice']=isset($common_notice['content'])?$common_notice['content']:'';
		$archivesInfo['common_url']=isset($common_notice['url'])?$common_notice['url']:'';
		$private_notice=unserialize($archives['private_notice']);
		$archivesInfo['private_notice']=isset($private_notice['content'])?$private_notice['content']:'';
		$archivesInfo['private_url']=isset($common_notice['url'])?$common_notice['url']:'';
		$archivesInfo['status']=isset($archives['live_record']['status'])?(int)$archives['live_record']['status']:'';
		$server=$archivesService->getArchivesLiveServerByArchivesId($archives['archives_id']);
		$serverIds=array();
		foreach($server as $val){
			$serverIds=$val['server_id'];
		}
		$source=$archivesService->getLiveServerByServerIds($serverIds);
		$live_server=array();
		foreach($source as $_source){
			$m3u8=$rtmp=array();
			if(isset($archives['live_record'])&&$archives['live_record']['live_model']==1){
				$m3u8[]=str_replace('rtmp', 'http', $_source['export_host']).'/'.$row['archives_id'].'/playlist.m3u8';
			}
			$rtmp[]=$_source['export_host'].'/'.$row['archives_id'];
		}
		$live_server['m3u8']=$m3u8;
		$live_server['rtmp']=$rtmp;
		$archivesInfo['source']=$live_server;
		$chat_server=$archivesService->getChatServerByArchivesId($archives['archives_id']);
		$archivesInfo['domain']=$chat_server['domain']?$chat_server['domain']:'';
		$archivesInfo['policy_port']=$chat_server['policy_port']?(int)$chat_server['policy_port']:'';
		$archivesInfo['data_port']=$chat_server['data_port']?(int)$chat_server['data_port']:'';
		$archivesInfo['start_time']=(isset($archives['live_record']['start_time'])&&$archives['live_record']['start_time']>0)?(int)$archives['live_record']['start_time']:'';
		$start_date=(isset($archives['live_record']['start_time'])&&$archives['live_record']['start_time']>0)?PipiDate::getFurtureDate((int)$archives['live_record']['start_time'],$timeStamp,'Y-m-d H:i'):'';
		$archivesInfo['start_date']=$start_date[0];
		$archivesInfo['live_time']=(isset($archives['live_record']['live_time'])&&$archives['live_record']['live_time']>0)?(int)$archives['live_record']['live_time']:'';
		$live_date=(isset($archives['live_record']['live_time'])&&$archives['live_record']['live_time']>0)?PipiDate::getLastDate((int)$archives['live_record']['live_time'],$timeStamp,'Y-m-d H:i'):'';
		$archivesInfo['live_date']=$live_date[0];
		$archivesInfo['end_time']=(isset($archives['live_record']['end_time'])&&$archives['live_record']['end_time']>0)?(int)$archives['live_record']['end_time']:'';
		$end_date=(isset($archives['live_record']['end_time'])&&$archives['live_record']['end_time']>0)?PipiDate::getLastDate((int)$archives['live_record']['end_time'],$timeStamp,'Y-m-d H:i'):'';
		$archivesInfo['end_date']=$end_date[0];
		
		$webConfSer = new WebConfigService();
		$c_key = $webConfSer->getGiftMsgPushKey();
		$giftSet=$webConfSer->getWebConfig($c_key);
		(int)$archivesInfo['global_gift']=isset($giftSet['c_value']['global'])?$giftSet['c_value']['global']:8000;
		(int)$archivesInfo['min_gift']=isset($giftSet['c_value']['private'])?$giftSet['c_value']['private']:10;
		
		$gift_rank=array();
		$giftStarRankService=new GiftStarRankService();
		$gift_rank=$giftStarRankService->getWeekRankByUid($archives['uid']);
		
		$archivesInfo['gift_rank']=$gift_rank;
		
		$archivesService=new ArchivesService();
		$userRankData=$archivesService->getArchivesDedicationFromRedis($archives_id);
		$archives_dedication=array();
		if($userRankData['archives_dedication_'.$archives_id]){
			foreach($userRankData['archives_dedication_'.$archives_id] as $key=>$row){
				$archives_dedication[$key]['uid']=$row['uid'];
				$archives_dedication[$key]['archives_id']=$archives_id;
				$archives_dedication[$key]['dedication']=$row['dedication'];
				$archives_dedication[$key]['avatar']=$userService->getUserAvatar($row['uid'],'small');
				$archives_dedication[$key]['user_rank']=$row['rank'];
				$archives_dedication[$key]['nickname']=$row['nickname'];
			}
		}
		$week_dedication=array();
		if($userRankData['week_dedication_'.$archives_id]){
			foreach($userRankData['week_dedication_'.$archives_id] as $key=>$row){
				$week_dedication[$key]['uid']=$row['uid'];
				$week_dedication[$key]['archives_id']=$archives_id;
				$week_dedication[$key]['dedication']=$row['dedication'];
				$week_dedication[$key]['avatar']=$userService->getUserAvatar($row['uid'],'small');
				$week_dedication[$key]['user_rank']=$row['rank'];
				$week_dedication[$key]['nickname']=$row['nickname'];
			}
		}
		
		$month_dedication=array();
		if($userRankData['month_dedication_'.$archives_id]){
			foreach($userRankData['month_dedication_'.$archives_id] as $key=>$row){
				$month_dedication[$key]['uid']=$row['uid'];
				$month_dedication[$key]['archives_id']=$archives_id;
				$month_dedication[$key]['dedication']=$row['dedication'];
				$month_dedication[$key]['avatar']=$userService->getUserAvatar($row['uid'],'small');
				$month_dedication[$key]['user_rank']=$row['rank'];
				$month_dedication[$key]['nickname']=$row['nickname'];
			}
		}
		$super_dedication=array();
		if($userRankData['super_dedication_'.$archives_id]){
			foreach($userRankData['super_dedication_'.$archives_id] as $key=>$row){
				$super_dedication[$key]['uid']=$row['uid'];
				$super_dedication[$key]['archives_id']=$archives_id;
				$super_dedication[$key]['dedication']=$row['dedication'];
				$super_dedication[$key]['avatar']=$userService->getUserAvatar($row['uid'],'small');
				$super_dedication[$key]['user_rank']=$row['rank'];
				$super_dedication[$key]['nickname']=$row['nickname'];
			}
		}
		$archivesInfo['user_rank']=array($archives_dedication,$week_dedication,$month_dedication,$super_dedication);
		$this->jsonpReturn('200',4, 'success',$archivesInfo);
	}

	public function getArchivesList(){
		$status=isset($this->params['status'])?$this->params['status']:'';
		$type=isset($this->params['type'])?$this->params['type']:'';
		$cat_id=isset($this->params['cat_id'])?$this->params['cat_id']:'';
		$pagesize=isset($this->params['pagesize'])?$this->params['pagesize']:50;
		$page=isset($this->params['page'])?$this->params['page']:1;
		$orgStatus=array('0'=>1,//待直播
			'1'=>2,//正在直播
			'2'=>4,//结束直播
			'-1'=>8 //无效直播
		);
		if($type){
			if(!in_array($type,array('rank','tag'))){
				$this->jsonpReturn('-54',5,'分类类型错误');
			}
		}
		
		if(isset($pagesize)){
			if(!is_numeric($pagesize)||$pagesize<=0||strlen($pagesize)>10){
				$this->jsonpReturn('-52',5,Yii::t('api','illegal pagesize'));
			}
		}
		
		if(isset($page)){
			if(!is_numeric($page)||$page<=0||strlen($page)>10){
				$this->jsonpReturn('-53',5,Yii::t('api','illegal page'));
			}
		}
		$archivesService=new ArchivesService();
		$indexPageService=new IndexPageService();
		$archives=$living=$waitLive=array();
		if($status){
			if($status==1){
				if($type){
					if($type=='rank'){
						$catArchives=$indexPageService->getDoteyByRank($cat_id);
					}elseif($type='tag'){
						$catArchives=$indexPageService->getDoteyByTag($cat_id);
					}
					$waitLive=$catArchives['wait'];
				}else{
					$waitLive=$archivesService->getAllWillLiveArchives();
				}
			}else if($status==2){
				if($type){
					if($type=='rank'){
						$catArchives=$indexPageService->getDoteyByRank($cat_id);
					}elseif($type='tag'){
						$catArchives=$indexPageService->getDoteyByTag($cat_id);
					}
					$living=$catArchives['living'];
				}else{
					$living = $archivesService->getAllLivingArchives();
				}
			}elseif($status==3){
				if($type){
					if($type=='rank'){
						$catArchives=$indexPageService->getDoteyByRank($cat_id);
					}elseif($type='tag'){
						$catArchives=$indexPageService->getDoteyByTag($cat_id);
					}
					$waitLive=$catArchives['wait'];
					$living=$catArchives['living'];
				}else{
					$waitLive=$archivesService->getAllWillLiveArchives(true);
					$living = $archivesService->getAllLivingArchives();
				}
			}elseif($status==5){
				if($type){
					if($type=='rank'){
						$catArchives=$indexPageService->getDoteyByRank($cat_id);
					}elseif($type='tag'){
						$catArchives=$indexPageService->getDoteyByTag($cat_id);
					}
					$waitLive=$catArchives['wait'];
					$living=$catArchives['living'];
				}else{
					$waitLive=$archivesService->getAllWillLiveArchives(true);
					$living = $archivesService->getAllLivingArchives();
				}
			}else{
				$this->jsonpReturn('-51',5,Yii::t('api','illegal archives status'));
			}
		}else{
			if($type){
				if($type=='rank'){
					$catArchives=$indexPageService->getDoteyByRank($cat_id);
				}elseif($type='tag'){
					$catArchives=$indexPageService->getDoteyByTag($cat_id);
				}
				$waitLive=$catArchives['wait'];
				$living=$catArchives['living'];
			}else{
				$waitLive=$archivesService->getAllWillLiveArchives(false);
				$living = $archivesService->getAllLivingArchives();
			}
		}
		$archives=array_merge($living,$waitLive);
		$archivesData=array();
		if($archives){
			$timeStamp=time();
			$giftStarService=new GiftStarService();
			$weekId=$giftStarService->getThisWeekId();
			$lastWeekId=$weekId-1;
			$lastGiftStar=$giftStarService->getFirstDoteysByWeekId($lastWeekId);
			$lastGiftStarDoteyUid=array();
			foreach($lastGiftStar as $row){
				$lastGiftStarDoteyUid[]=$row['dotey_id'];
			}
			$doteyUid=$archivesIds=array();
			foreach($archives as $row){
				$doteyUid[]=$row['uid'];
				$archivesIds[]=$row['archives_id'];
			}
			$userListService=new UserListService();
			$onlineNum=$userListService->getArchivesOnlineNumByArchivesIds($archivesIds);
			$userJsonInfoService=new UserJsonInfoService();
			$userJson=$userJsonInfoService->getUserInfos($doteyUid,false);
			$userService=new UserService();
			$doteyService=new DoteyService();
			foreach($archives as $key=>$row){
				$archivesData[$key]['archives_id']=(int)$row['archives_id'];
				$archivesData[$key]['title']=$row['title'];
				$archivesData[$key]['uid']=(int)$row['uid'];
				$archivesData[$key]['rank']=isset($userJson[$row['uid']]['nk'])?$userJson[$row['uid']]['nk']:'';
				$archivesData[$key]['rank']=isset($userJson[$row['uid']]['rk'])?$userJson[$row['uid']]['rk']:0;
				$archivesData[$key]['dotey_rank']=isset($userJson[$row['uid']]['dk'])?$userJson[$row['uid']]['dk']:0;
				$archivesData[$key]['record_id']=isset($row['live_record']['record_id'])?(int)$row['live_record']['record_id']:0;
				$archivesData[$key]['big_cover']=$doteyService->getDoteyUpload($row['uid'],'big','display');
				$archivesData[$key]['middle_cover']=$doteyService->getDoteyUpload($row['uid'],'small','display');
				$common_notice=unserialize($row['notice']);
				$archivesData[$key]['common_notice']=isset($common_notice['content'])?$common_notice['content']:'';
				$private_notice=unserialize($row['private_notice']);
				$archivesData[$key]['private_notice']=isset($private_notice['content'])?$private_notice['content']:'';
				$archivesData[$key]['status']=(isset($row['live_record']['status'])&&$row['live_record']['status']==1)?1:0;
				$archivesData[$key]['start_time']=(isset($row['live_record']['start_time'])&&isset($row['live_record']['start_time'])>0)?(int)$row['live_record']['start_time']:'';
				$start_date=(isset($row['live_record']['start_time'])&&$row['live_record']['start_time']>0)?PipiDate::getFurtureDate((int)$row['live_record']['start_time'],$timeStamp,'Y-m-d H:i'):'';
				$archivesData[$key]['start_date']=isset($start_date[0])?$start_date[0]:'';
				$archivesData[$key]['live_time']=(isset($row['live_record']['live_time'])&&$row['live_record']['live_time']>0)?(int)$row['live_record']['live_time']:'';
				$live_date=(isset($row['live_record']['live_time'])&&$row['live_record']['live_time']>0)?PipiDate::getLastDate((int)$row['live_record']['live_time'],$timeStamp,'Y-m-d H:i'):'';
				$archivesData[$key]['live_date']=isset($live_date[0])?$live_date[0]:'';
				$archivesData[$key]['end_time']=(isset($row['live_record']['end_time'])&&$row['live_record']['end_time']>0)?(int)$row['live_record']['end_time']:'';
				$end_date=(isset($row['live_record']['end_time'])&&$row['live_record']['end_time']>0)?PipiDate::getLastDate((int)$row['live_record']['end_time'],$timeStamp,'Y-m-d H:i'):'';
				$archivesData[$key]['end_date']=isset($end_date[0])?$end_date[0]:'';
				$archivesData[$key]['is_new']=0;
				$archivesData[$key]['on_board']=in_array($row['uid'],$lastGiftStarDoteyUid)?1:0;
				$archivesData[$key]['online_count']=isset($onlineNum[$row['archives_id']])?$onlineNum[$row['archives_id']]:0;
			}
		}
		$dataList=array();
		if($archivesData){
			$dataList=array_slice($archivesData, ($page-1)*$pagesize,$pagesize);
		}
		$this->jsonpReturn('200',5,'success',array('total'=>(int)count($archivesData),'list'=>$dataList));
	}


	/**
	 * 获取礼物接口
	 */
	public function getGiftList(){
		$cat_id=isset($this->params['cat_id'])?$this->params['cat_id']:0;
		$gift=array();
		$giftService=new GiftService();
		if($cat_id>0){
			$gift=$giftService->getGiftByCatIds($cat_id,true);
			$gift=array_pop($gift);
		}else{
			$gift=$giftService->getGiftList(array('shop_type'=>1,'is_display'=>1),true);

		}
		
		$giftList=array();
		if($gift){
			foreach($gift as $row){
				$effect=array();
				foreach($row['effects'] as $val){
					$effect[]=array('num'=>(int)$val['num'],'timeout'=>(int)$val['timeout'],'effect'=>$giftService->getGiftEffectUrl($val['effect']));
				}
				$giftList[]=array('gift_id'=>(int)$row['gift_id'],
								'zh_name'=>	$row['zh_name'],
								'en_name'=>$row['en_name'],
								'cat_id'=>(int)$row['cat_id'],
								'pipiegg'=>(float)$row['pipiegg'],
								'image'=>$giftService->getGiftUrl($row['image']),
								'effect'=>$effect
					);
			}
		}
		$this->jsonpReturn('200',6,'success',array('total'=>(int)count($giftList),'list'=>$giftList));
	}
	
	public function getChatSource(){
		
	}

	public function getUserInfo(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		if($uid<=0||preg_match('/\d{1,10}$/',$uid)==false){
			$this->jsonpReturn('-13',8,Yii::t('api','illegal params'));
		}
		$userService=new UserService();
		$userBase=$userService->getUserBasicByUids(array($uid));
		if(empty($userBase)){
			$this->jsonpReturn('-81',8,Yii::t('api','illegal uid'));
		}
		$consumeService=new ConsumeService();
		$userConsume=$consumeService->getConsumesByUids($uid);
		$userInfo=array();
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByUid($uid);
		$archives_id=0;
		if($archives){
			$archives=array_pop($archives);
			$archives_id=(int)$archives['archives_id'];
		}
		$userInfo['uid']=(int)$uid;
		$userInfo['nickname']=$userBase[$uid]['nickname'];
		$userRank=$consumeService->getUserRanksInfoByGrades($userConsume[$uid]['rank']);
		(int)$userInfo['pipiegg']=$userConsume[$uid]['pipiegg']-$userConsume[$uid]['freeze_pipiegg'];
		$userInfo['big_avatar']=$userService->getUserAvatar($uid,'big');
		$userInfo['middle_avatar']=$userService->getUserAvatar($uid,'middle');
		$userInfo['small_avatar']=$userService->getUserAvatar($uid,'small');
		$userInfo['rank']=(int)$userConsume[$uid]['rank'];
		$userInfo['dotey_rank']=isset($userConsume[$uid]['dotey_rank'])?(int)$userConsume[$uid]['dotey_rank']:0;
		$userExtend=$userService->getUserExtendByUids(array($uid));
		$userInfo['province']=$userExtend[$uid]['province']?$userExtend[$uid]['province']:'';
		$userInfo['city']=$userExtend[$uid]['city']?$userExtend[$uid]['city']:'';
		$userInfo['gender']=(int)$userExtend[$uid]['gender'];
		$weiboService=new WeiboService();
		$weiboBase=$weiboService->getWeiboStatisticsByUid($uid);
		$userInfo['fans_count']=(int)$weiboBase['fans'];
		$userInfo['follow_count']=(int)$weiboBase['attentions'];
		if($archives_id>0){
			$liveRecord=$archivesService->getLiveRecordByArchivesId($archives_id);
			$userInfo['is_living']=(isset($liveRecord['status'])&&$liveRecord['status']==1)?1:0;
		}else{
			$userInfo['is_living']=0;
		}
		$userAttribute=$userService->getUserFrontsAttributeByCondition($uid,true,true);
		$consumeService = new ConsumeService();
		$userAttribute['de']=isset($userAttribute['de'])?$userAttribute['de']:0;
		$userAttribute['nxde']=isset($userAttribute['nxde'])&&$userAttribute['nxde']>=0?$userAttribute['nxde']:99999999;
		(int)$userInfo['upgrade_dedication']=number_format(($userAttribute['de']-$userAttribute['cude'])/($userAttribute['nxde']-$userAttribute['cude']),2)*100;
		(int)$userInfo['current_dedication']=$userAttribute['nxde']-$userAttribute['cude'];
		$userAttribute['ch']=isset($userAttribute['ch'])?$userAttribute['ch']:0;
		$userAttribute['nxch']=isset($userAttribute['nxch'])&&$userAttribute['nxch']>0?$userAttribute['nxch']:99999999;
		(int)$userInfo['upgrade_charm']=number_format(($userAttribute['ch']-$userAttribute['cuch'])/($userAttribute['nxch']-$userAttribute['cuch']),2)*100;
		(int)$userInfo['current_charm']=$userAttribute['nxch']-$userAttribute['cuch'];
		
		(int)$userInfo['archives_id'] = $archives_id;
		
		$this->jsonpReturn('200',8,'success',$userInfo);
	}
	
	public function getArchivesToken(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$archives_id=isset($this->params['archives_id'])?$this->params['archives_id']:0;
		if($uid<=0||preg_match('/\d{1,10}$/',$uid)==false){
			$this->jsonpReturn('-91',9,Yii::t('api','illegal uid'));
		}
		if($archives_id<=0||preg_match('/\d{1,10}$/',$archives_id)==false){
			$this->jsonpReturn('-92',9,Yii::t('api','illegal archives_id'));
		}
		$archivesService=new ArchivesService();
		$token=$archivesService->createChatToken($uid, $archives_id);
		$this->jsonpReturn('200',9,'success',array('uid'=>(int)$uid,'archives_id'=>(int)$archives_id,'token'=>$token));
	}
	
	public function sendGift(){
		$archivesId=$this->params['archives_id'];
		$from_uid=$this->params['from_uid'];
		$to_uid=$this->params['to_uid'];
		$giftId=$this->params['gift_id'];
		$giftNum=$this->params['num'];
		$giftType=$this->params['gift_type'];
		$giftType=empty($giftType)?'common':$giftType;
		if($archivesId<=0||$to_uid<=0||$giftId<=0||$giftNum<=0){
			$this->jsonpReturn('-13',10,Yii::t('api','illegal params'));
		}
		if(!$this->isLogin($from_uid)){
			$this->jsonpReturn('-17',10,Yii::t('user','You are not logged'));
		}
		if($giftNum>100000){
			$this->jsonpReturn('-101',10, Yii::t('gift','Gift numeber not greater than 100000'));
		}
		$giftTypeList=array('common','bag');
		if(!in_array($giftType, $giftTypeList)){
			$this->jsonpReturn('-102',10, Yii::t('gift','The wrong gift way'));
		}
		$giftService=new GiftService();
		$lastSendGiftTime=$giftService->getLastSendGiftTime($from_uid);
		if(time()-$lastSendGiftTime<2){
			$this->jsonpReturn('-103',10,Yii::t('gift','Gifts are processing operation...'));
		}
		if($giftType=='common'){
			try{
				$result=$giftService->sendGift($from_uid,$to_uid,$archivesId,$giftId,$giftNum);
			}catch (Exception $e){
				$error=$e->getMessage();
				$filename = DATA_PATH.'runtimes/user_attribute_exception.txt';
				error_log('用户送礼异常：'.$error."\n\r",3,$filename);
			}
			if(!$result){
				$msg=$giftService->getError();
				if($msg=='皮蛋不足'){
					$this->jsonpReturn('-104',10,Yii::t('gift',$msg));
				}else{
					$this->jsonpReturn('-105',10,Yii::t('gift',$msg));
				}
			}else{
				$this->jsonpReturn('200',10,'success');
			}
		}else if($giftType=='bag'){
			$giftBagService=new GiftBagService();
			try{
				$result=$giftBagService->sendBagGift($from_uid,$to_uid,$archivesId,$giftId,$giftNum);
			}catch (Exception $e){
				$error=$e->getMessage();
				$filename = DATA_PATH.'runtimes/user_attribute_exception.txt';
				error_log('用户背包送礼异常：'.$error."\n\r",3,$filename);
			}
			if(!$result){
				$msg=$giftBagService->getError();
				$this->jsonpReturn('-106',10,Yii::t('giftBag',$msg));
			}else{
				$this->jsonpReturn('200',10,'success');
			}
		}
	}
	
	public function getUserAttentionArchives(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$pagesize=isset($this->params['pagesize'])?$this->params['pagesize']:50;
		$page=isset($this->params['page'])?$this->params['page']:1;
		if($uid<=0){
			$this->jsonpReturn('-13',11,Yii::t('api','illegal params'));
		}
		if(isset($pagesize)){
			if(!is_numeric($pagesize)||$pagesize<=0||strlen($pagesize)>10){
				$this->jsonpReturn('-111',11,Yii::t('api','illegal pagesize'));
			}
		}
		
		if(isset($page)){
			if(!is_numeric($page)||$page<=0||strlen($page)>10){
				$this->jsonpReturn('-112',11,Yii::t('api','illegal page'));
			}
		}
		$archivesService=new ArchivesService();
		$archives=$archivesService->getUserAttentionArchives($uid,true);
		$archivesList=array();
		$userService=new UserService();
		$doteyService=new DoteyService();
		if(!empty($archives['living'])){
			foreach($archives['living'] as $row){
				$userAttribute=$userService->getUserFrontsAttributeByCondition($row['uid'],true,true);
				$user_rank=isset($userAttribute['rk'])?$userAttribute['rk']:0;
				$dotey_rank=isset($userAttribute['dk'])?$userAttribute['dk']:0;
				$archivesList[]=array('archives_id'=>(int)$row['archives_id'],
									   'dotey_id'=>(int)$row['uid'],
									   'user_rank'=>(int)$user_rank,
									   'dotey_rank'=>(int)$dotey_rank,		
					                   'title'=>$row['title'],
									   'sub_title'=>$row['sub_title'],
									   'start_time'=>(int)$row['start_time'],
									   'live_time'=>(int)$row['live_time'],
									   'big_avatar'=>$userService->getUserAvatar($row['uid'],'big'),
					                   'middle_avatar'=>$userService->getUserAvatar($row['uid'],'middle'),
					                   'small_avatar'=>$userService->getUserAvatar($row['uid'],'small'),
									   'big_cover'=>$doteyService->getDoteyUpload($row['uid'],'big','display'),
									    'middle_cover'=>$doteyService->getDoteyUpload($row['uid'],'middle','display'),
										'status'=>isset($row['status'])?(int)$row['status']:''
									);
			}
		}
		if(!empty($archives['wait'])){
			foreach($archives['wait'] as $row){
				$userAttribute=$userService->getUserFrontsAttributeByCondition($row['uid'],true,true);
				$user_rank=isset($userAttribute['rk'])?$userAttribute['rk']:0;
				$dotey_rank=isset($userAttribute['dk'])?$userAttribute['dk']:0;
				$archivesList[]=array('archives_id'=>(int)$row['archives_id'],
					'dotey_id'=>(int)$row['uid'],
					'user_rank'=>(int)$user_rank,
					'dotey_rank'=>(int)$dotey_rank,
					'title'=>$row['title'],
					'sub_title'=>$row['sub_title'],
					'start_time'=>(int)$row['start_time'],
					'live_time'=>(int)$row['live_time'],
					'big_avatar'=>$userService->getUserAvatar($row['uid'],'big'),
					'middle_avatar'=>$userService->getUserAvatar($row['uid'],'middle'),
					'small_avatar'=>$userService->getUserAvatar($row['uid'],'small'),
					'big_cover'=>$doteyService->getDoteyUpload($row['uid'],'big','display'),
					'middle_cover'=>$doteyService->getDoteyUpload($row['uid'],'middle','display'),
					'status'=>isset($row['status'])?(int)$row['status']:''
				);
			}
		}
		
		if($archivesList){
			$archivesList=array_slice($archivesList,($page-1)*$pagesize,$pagesize);
		}
		$this->jsonpReturn('200',11,'success',array('total'=>(int)count($archivesList),'list'=>$archivesList));
	}
	
	public function getUserGiftSendRecords(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$pagesize=isset($this->params['pagesize'])?$this->params['pagesize']:20;
		$page=isset($this->params['page'])?$this->params['page']:0;
		if($uid<=0){
			$this->jsonpReturn('-13',12,Yii::t('api','illegal params'));
		}
		if(isset($pagesize)){
			if(!is_numeric($pagesize)||$pagesize<=0||strlen($pagesize)>10){
				$this->jsonpReturn('-121',12,Yii::t('api','illegal pagesize'));
			}
		}
		
		if(isset($page)){
			if(!is_numeric($page)||$page<=0||strlen($page)>10){
				$this->jsonpReturn('-122',12,Yii::t('api','illegal page'));
			}
		}
		$giftList=array();
		$giftService=new GiftService();
		$records=$giftService->getUserGiftSendRecordsByUid($uid,$page-1,$pagesize);
		if($records){
			$giftList['total']=(int) $records['count'];
			foreach($records['list'] as $key=>$row){
				$info=unserialize($row['info']);
				$giftList['list'][]=array('from_uid'=>(int)$row['uid'],
													'from_nickname'=>$info['sender'],
													'to_uid'=>(int)$row['to_uid'],
													'to_nickname'=>$info['receiver'],
													'picture'=>$giftService->getGiftUrl($info['gift_image']),
													'gift_name'=>$info['gift_zh_name'],
													'gift_num'=>(int)$row['num'],
													'pipiegg'=>(float)$row['pipiegg']
												);
			}
		}
		$this->jsonpReturn('200',12,'success',$giftList);
	}
	
	public function getUserViewRecords(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$pagesize=isset($this->params['pagesize'])?$this->params['pagesize']:50;
		$page=isset($this->params['page'])?$this->params['page']:1;
		if($uid<=0){
			$this->jsonpReturn('-13',13, Yii::t('api','illegal params'));
		}
		if(isset($pagesize)){
			if(!is_numeric($pagesize)||$pagesize<=0||strlen($pagesize)>10){
				$this->jsonpReturn('-131',13,Yii::t('api','illegal pagesize'));
			}
		}
		
		if(isset($page)){
			if(!is_numeric($page)||$page<=0||strlen($page)>10){
				$this->jsonpReturn('-132',13,Yii::t('api','illegal page'));
			}
		}
		$archivesService=new ArchivesService();
		$archives=$archivesService->getUserLatestSeeArchives($uid);
		$archivesList=array();
		if($archives){
			$userService=new UserService();
			$doteyService=new DoteyService();
			if(isset($archives['living'])){
				foreach($archives['living'] as $row){
					$archivesList[]=array(
							'archives_id'=>(int)$row['archives_id'],
							'dotey_id'=>(int)$row['uid'],
							'title'=>$row['title'],
							'sub_title'=>isset($row['sub_title'])?$row['sub_title']:'',
							'live_time'=>isset($row['live_time'])?(int)$row['live_time']:'',
							'start_time'=>isset($row['start_time'])?(int)$row['start_time']:'',
							'status'=>isset($row['status'])?(int)$row['status']:'',
							'big_avatar'=>$userService->getUserAvatar($row['uid'],'big'),
							'middle_avatar'=>$userService->getUserAvatar($row['uid'],'middle'),
							'small_avatar'=>$userService->getUserAvatar($row['uid'],'small'),
							'big_cover'=>$doteyService->getDoteyUpload($row['uid'],'big','display'),
							'middle_cover'=>$doteyService->getDoteyUpload($row['uid'],'middle','display')
						);
				}
			}
			if(isset($archives['wait'])){
				foreach($archives['wait'] as $row){
					$archivesList[]=array(
						'archives_id'=>(int)$row['archives_id'],
						'dotey_id'=>(int)$row['uid'],
						'title'=>$row['title'],
						'sub_title'=>isset($row['sub_title'])?$row['sub_title']:'',
						'live_time'=>isset($row['live_time'])?(int)$row['live_time']:'',
						'start_time'=>isset($row['start_time'])?(int)$row['start_time']:'',
						'status'=>isset($row['status'])?(int)$row['status']:'',
						'big_avatar'=>$userService->getUserAvatar($row['uid'],'big'),
						'middle_avatar'=>$userService->getUserAvatar($row['uid'],'middle'),
						'small_avatar'=>$userService->getUserAvatar($row['uid'],'small'),
						'big_cover'=>$doteyService->getDoteyUpload($row['uid'],'big','display'),
						'middle_cover'=>$doteyService->getDoteyUpload($row['uid'],'middle','display')
					);
				}
			}
			
		}
		if($archivesList){
			$archivesList=array_slice($archivesList, ($page-1)*$pagesize,$pagesize);
		}
		$this->jsonpReturn('200',13,'success',array('total'=>(int)count($archivesList),'list'=>$archivesList));
	}
	
	public function saveUserViewRecords(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$archives_id=isset($this->params['archives_id'])?$this->params['archives_id']:0;
		$record_id=isset($this->params['record_id'])?$this->params['record_id']:0;
		if($uid<=0||$archives_id<=0||$record_id<=0){
			$this->jsonpReturn('-13',14, Yii::t('api','illegal params'));
		}
		$archivesService=new ArchivesService();
		$archives['uid']=$uid;
		$archives['archives_record_id']=$record_id;
		$archives['archives_id']=$archives_id;
		if(!$archivesService->saveLatestSeeArchives($archives)){
			$this->jsonpReturn('141',14,Yii::t('api','User view save failed'));
		}
		$this->jsonpReturn('200',14,'success');
	}
	
	public function saveSuggest(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$qq=isset($this->params['qq'])?$this->params['qq']:'';
		$contact=isset($this->params['contact'])?$this->params['contact']:'';
		$content=isset($this->params['content'])?$this->params['content']:'';
		$version=isset($this->params['version'])?$this->params['version']:'';
		$soft_version=isset($this->params['soft_version'])?$this->params['soft_version']:'';
		$model=isset($this->params['model'])?$this->params['model']:'';
		$net_mode=isset($this->params['net_mode'])?$this->params['net_mode']:'';
		if($uid<=0){
			$this->jsonpReturn('-13',15, Yii::t('api','illegal params'));
		}
		if(empty($content)){
			$this->jsonpReturn('-152',15, Yii::t('api','The content not empty'));
		}
		if(strlen($content)>500){
			$this->jsonpReturn('-153',15, Yii::t('api','The content of not more than 500 words'));
		}
		$operateService=new OperateService();
		$data['uid']=$uid;
		$data['type']=SUGGEST_TYPE_PHONE;
		if(empty($qq)&&empty($contact)){
			$data['contact']='手机端';
		}elseif(empty($contact)&&!empty($qq)){
			$data['contact']=$qq;
		}else{
			$data['contact']=$qq.'|'.$contact;
		}
		$data['content']=$content;
		$version&&$data['info']['version']=$version;
		$soft_version&&$data['info']['soft_version']=$soft_version;
		$model&&$data['info']['model']=$model;
		$net_mode&&$data['info']['net_mode']=$net_mode;
		if(!$operateService->saveSuggest($data)){
			$this->jsonpReturn('-154',15, Yii::t('api','Suggest save failed'));
		}
		$this->jsonpReturn('200',15,'success');
	}
	
	public function addAttention(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$dotey_uid=isset($this->params['dotey_uid'])?$this->params['dotey_uid']:0;
		if($uid<=0||$dotey_uid<=0){
			$this->jsonpReturn('-13',16, Yii::t('api','illegal params'));
		}
		if($uid==$dotey_uid){
			$this->jsonpReturn('-161',16, Yii::t('api','Do not pay attention to yourself'));
		}
		$weiboService=new WeiboService();
		$userService=new UserService();
		$userBase=$userService->getUserBasicByUids(array($dotey_uid,$uid));
		if(!isset($userBase[$uid])&&!isset($userBase[$dotey_uid])){
			$this->jsonpReturn('-162',16, Yii::t('api','uid or dotey_uid not exits'));
		}
		if($userService->hasBit(intval($userBase[$dotey_uid]['user_type']),USER_TYPE_DOTEY)&&$userBase[$dotey_uid]['user_status']!=USER_STATUS_OFF){
			$weiboService->attentionDotey($dotey_uid,$uid);
		}
		if(!$weiboService->attentionUser($dotey_uid,$uid)){
			$error= $weiboService->getNotice();
			$this->jsonpReturn('-163',16,$error['weibo_attention']);
		}
		$this->jsonpReturn('200',16,'success');
	}
	
	
	public function removeAttention(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$dotey_uid=isset($this->params['dotey_uid'])?$this->params['dotey_uid']:0;
		if($uid<=0||$dotey_uid<=0){
			$this->jsonpReturn('-13',17, Yii::t('api','illegal params'));
		}
		if($uid==$dotey_uid){
			$this->jsonpReturn('-171',17, Yii::t('api','Do not pay attention to yourself'));
		}
		$weiboService=new WeiboService();
		$userService=new UserService();
		$userBase=$userService->getUserBasicByUids(array($dotey_uid,$uid));
		if(!isset($userBase[$uid])&&!isset($userBase[$dotey_uid])){
			$this->jsonpReturn('-162',16, Yii::t('api','uid or dotey_uid not exits'));
		}
		if($userService->hasBit(intval($userBase[$dotey_uid]['user_type']),USER_TYPE_DOTEY)&&$userBase[$dotey_uid]['user_status']!=USER_STATUS_OFF){
			$weiboService->cancelDoteyAttentionedUser($dotey_uid,$uid);
		}
		if(!$weiboService->cancelAttentionedUser($dotey_uid,$uid)){
			$this->jsonpReturn('-172',17, Yii::t('api','Remove attention failed'));
		}
		$this->jsonpReturn('200',17,'success',array('dotey_uid'=>(int)$dotey_uid));
	}
	
	public function checkin(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		if($uid<=0){
			$this->jsonpReturn('-13',18, Yii::t('api','illegal params'));
		}
		if(!$this->isLogin($uid)){
			$this->jsonpReturn('-17',18,Yii::t('user','You are not logged'));
		}
		$userService=new UserService();
		$userBasice=$userService->getUserBasicByUids(array($uid));
		if(!$userBasice){
			$this->jsonpReturn('-181',18, Yii::t('api','illegal uid'));
		}
		// 检查是否购买月卡
		$propsService = new PropsService();
		$category = $propsService->getPropsCategoryByEnName('monthcard');
		$cat_id = $category['cat_id'];
		$props = $propsService->getPropsByCatId($cat_id);
		$userPropsService = new UserPropsService();
		$propsIds = array_keys($props);
		$propId = $propsIds[0];
		$isMoon = $userPropsService->getUserValidPropsOfBagByPropId($uid,$propId,time());
		$userGiftService = new UserGiftService();
		$monthCheckin=array();
		if($isMoon){
			$etime = $isMoon[0]['valid_time'];
			$stime = $etime - (30 *24*60*60);
			$etime = mktime(1,0,0,date('m',$etime),date('d',$etime)+1,date('Y',$etime));
			$num = $userGiftService->countMonthGift($uid, $stime, $etime);
			if($num > 0){
				$monthCheckin = $this->checkins($uid, CHENKIN_MONTHCARD, CHECKIN_GIFT_MONTHCARD, CHECKIN_GIFT_MONTHCARD_NUM);
				if($monthCheckin['result']=='1'){
					$update_num = 1;
					$userPropsService->saveUserPropsBag(array('uid'=>$uid, 'prop_id'=>$propId, 'num'=>1));
				}
			}
		}
		$normalCheckin = $this->checkins($uid, CHENKIN_NORMAL, CHECKIN_GIFT_NORMAL);
		
		if($normalCheckin['result']=='1'){
			$data[]=$normalCheckin['info'];
			if(isset($monthCheckin['info'])&&is_array($monthCheckin['info'])){
				$data[]=$monthCheckin['info'];
			}
			$this->jsonpReturn('200',18, 'success',array('list'=>$data));
		}elseif($normalCheckin['result']=='2'){
			$msg = $isMoon ?Yii::t('api','Today has been signed'): Yii::t('api','Today has been signed');
			$this->jsonpReturn('-182',18, $msg );
		}else{
			$this->jsonpReturn('-183',18, $msg );
		}
	}
	
	
	
	public function captch(){
		$uuid=uniqid();
		$url='http://'.$_SERVER['HTTP_HOST'].$this->createUrl('api/captcha',array('v' => $uuid));
		$this->jsonpReturn('200',19,'success',array('url'=>$url,'uuid'=>$uuid) );
	}
	
	
	//开放登陆
	public function openLogin(){
		$type=isset($this->params['type'])?$this->params['type']:'qq';
		$open_id = isset($this->params['open_id'])?$this->params['open_id']:'';
		$device_id=isset($this->params['device_id'])?$this->params['device_id']:'';
		$channel_id=isset($this->params['channel_id'])?$this->params['channel_id']:'';
		if(empty($open_id)){
			$this->jsonpReturn('-13',20, Yii::t('api','illegal params'));
		}
		if(!in_array($type,array('qq','safe','sina','renren','baidu'))){
			$this->jsonpReturn('-201',20, Yii::t('api','open login not exits') );
		}
		error_log(date("Y-m-d H:i:s")."开放登陆：".$type.",open_id：".$open_id."\n\r",3,$this->logPath);
		$password = $type.'_'.$open_id.self::openPathSalt;
		$userService = new UserService();
		$userOauth = $userService->getUserOauthByOpenFlatform($type,$open_id);
		//已绑定 直接登录
		if($userOauth){
			$userBasic = $userService->getUserBasicByUids(array($userOauth['uid']));
			$userBasic = $userBasic[$userOauth['uid']];
			if(empty($userBasic)){
				$this->jsonpReturn('-202',20, Yii::t('api','pipi account not exit') );
			}
			$identify = new PipiUserIdentity($userBasic['username'],$password);
			$identify->openUserInfo = $userOauth;
			$userInfo['big_avatar']=$userService->getUserAvatar($userOauth['uid'],'big');
			$userInfo['middle_avatar']=$userService->getUserAvatar($userOauth['uid'],'middle');
			$userInfo['small_avatar']=$userService->getUserAvatar($userOauth['uid'],'small');
			$userExtend=$userService->getUserExtendByUids(array($userOauth['uid']));
			$consumeService=new ConsumeService();
			$userConsume=$consumeService->getConsumesByUids($userOauth['uid']);
			(int)$userInfo['pipiegg']=$userConsume[$userOauth['uid']]['pipiegg']-$userConsume[$userOauth['uid']]['freeze_pipiegg'];
			$archivesService=new ArchivesService();
			$archives=$archivesService->getArchivesByUid($userOauth['uid']);
			$archives_id=0;
			if($archives){
				$archives=array_pop($archives);
				$archives_id=$archives['archives_id'];
			}
			Yii::app()->getSession()->regenerateID(true);
			$sessionId=Yii::app()->getSession()->getSessionID();
			$data=array('uid'=>(int)$userOauth['uid'],
				'archives_id'=>(int)$archives_id,
				'nickname'=>$userBasic['nickname'],
				'gender'=>(int)$userExtend[$userBase['uid']]['gender'],
				'big_avatar'=>$userInfo['big_avatar'],
				'middle_avatar'=>$userInfo['middle_avatar'],
				'small_avatar'=>$userInfo['small_avatar'],
				'pipiegg'=>$userInfo['pipiegg'],
				'session_id'=>$sessionId);
			if($identify->authenticate()){
				Yii::app()->user->login($identify,USER_REGISTER_COOKIE_VTIME);
				$sessionRedisModel=new SessionRedisModel();
				$sessionRedisModel->saveMobileUserSessionId($userBase['uid'], $sessionId);
				if($device_id&&$channel_id){
					$userIosService=new UserIosService();
					$androidSet['uid']=$userBase['uid'];
					$androidSet['type']=MOBILE_ANDROID_TYPE;
					$androidSet['user_id']=$device_id;
					$androidSet['channel_id']=$channel_id;
					$userAndroidSet=$userIosService->getUserAndroidByCondition($androidSet);
					if(!$userAndroidSet){
						$androidSet['notice']=IOS_PUSH_NOTICE_ON;
						$userIosService->saveUserAndroidSet($androidSet);
					}
				}
				error_log(date("Y-m-d H:i:s")."开放登陆用户登陆成功：".json_encode(array('uid'=>(int)$userOauth['uid'],'nickname'=>$userBasic['username']))."\n\r",3,$this->logPath);
				$this->jsonpReturn('200',20,'success',$data);
			}else{
				error_log(date("Y-m-d H:i:s")."开放登陆用户登陆失败\n\r",3,$this->logPath);
				$this->jsonpReturn('-203',20, Yii::t('api','open login failed') );
			}
		}else{
			//新用户注册
			$userBasic['uid'] = $userService->getNextUid();
			$userBasic['username'] = $type.'_'.$userBasic['uid'];
			$userBasic['nickname'] = $type.'_'.$userBasic['uid'];
			$userBasic['password'] = $password;
			$userBasic['user_type'] = 1;
			$userBasic['reg_source'] = $userService->getUserRegEnSource($type);
			$userService->saveUserBasic($userBasic);
			if(!$userService->getNotice()){
				$oauth['uid'] = $userBasic['uid'];
				$oauth['openid'] = $open_id;
				$oauth['open_platform'] = $type;
				$oauth['onickname'] = $userBasic['nickname'];
				$userService->saveUserOauth($oauth);
				$consumeService = new ConsumeService();
				$consumeService->saveUserConsumeAttribute(array('uid'=>$userBasic['uid'],'rank'=>0));
				$identify = new PipiUserIdentity($userBasic['username'],$userBasic['password']);
				if($identify->authenticate()){
					Yii::app()->user->login($identify,USER_REGISTER_COOKIE_VTIME);
					Yii::app()->getSession()->regenerateID(true);
					$sessionId=Yii::app()->getSession()->getSessionID();
					$sessionRedisModel=new SessionRedisModel();
					$sessionRedisModel->saveMobileUserSessionId($userBase['uid'], $sessionId);
					if($device_id&&$channel_id){
						$userIosService=new UserIosService();
						$androidSet['uid']=$userBase['uid'];
						$androidSet['type']=MOBILE_ANDROID_TYPE;
						$androidSet['user_id']=$device_id;
						$androidSet['channel_id']=$channel_id;
						$userAndroidSet=$userIosService->getUserAndroidByCondition($androidSet);
						if(!$userAndroidSet){
							$androidSet['notice']=IOS_PUSH_NOTICE_ON;
							$userIosService->saveUserAndroidSet($androidSet);
						}
					}
					error_log(date("Y-m-d H:i:s")."开放登陆新用户登陆成功：".json_encode(array('uid'=>(int)$userBasic['uid'],'nickname'=>$userBasic['username']))."\n\r",3,$this->logPath);
					$data=array('uid'=>(int)$userBasic['uid'],
						'archives_id'=>0,
						'nickname'=>$userBasic['username'],
						'gender'=>0,
						'big_avatar'=>$userService->getUserAvatar($userBasic['uid'],'big'),
						'middle_avatar'=>$userService->getUserAvatar($userBasic['uid'],'middle'),
						'small_avatar'=>$userService->getUserAvatar($userBasic['uid'],'small'),
						'pipiegg'=>0,
						'session_id'=>$sessionId);
					$this->jsonpReturn('200',20,'success',$data);
				}else{
					error_log(date("Y-m-d H:i:s")."开放登陆新用户登陆失败\n\r",3,$this->logPath);
					$this->jsonpReturn('-203',20, Yii::t('api','open login failed') );
				}
			}
		}
	}
	
	public function getPipieggList(){
		$this->jsonpReturn('200',21,'successful',array('list'=>$this->apple_product_list));
	}
	
	public function pushNotice(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$device_token=isset($this->params['device_token'])?$this->params['device_token']:'';
		//$device_token='71aca2ba466a8bc97231987f917a77ce68890f217007aca5117a0902 14dcf918';
		$badge=isset($this->params['badge'])?$this->params['badge']:1;
		$sound=isset($this->params['sound'])?$this->params['sound']:'default';
		if($uid<=0||empty($device_token)){
			$this->jsonpReturn('-13',22, Yii::t('api','illegal params'));
		}
		$userIosService=new UserIosService();
		$set['uid']=$uid;
		$set['device_token']=$device_token;
		$set['notice']=IOS_PUSH_NOTICE_ON;
		$set['badge']=$badge;
		$set['sound']=$sound;
		$IosSet=$userIosService->saveUserIosSet($set);
		if($IosSet){
			$this->jsonpReturn('200', 22, 'success');
		}
		$this->jsonpReturn('-221', 22,'Push notice failed');
		
	}
	
	
	/**
	 * 获取当前跑道礼物记录
	 */
	public function getTruckGiftRecord(){
		$truckGiftService=new TruckGiftService();
		$record=array();
		$data=$truckGiftService->getTruckGiftRecord();
		if($data){
			$giftService=new GiftService();
			$record['uid']=(int)$data['uid'];
			$record['nickname']=$data['nickname'];
			$record['to_uid']=(int)$data['to_uid'];
			$record['to_nickname']=$data['to_nickname'];
			$record['gift_name']=$data['zh_description'];
			$record['num']=(int)$data['num'];
			$record['pipiegg']=(float)$data['pipiegg'];
			$record['picture']=$giftService->getGiftUrl($data['picture']);
			$record['remark']=$data['remark'];
		}
		$this->jsonpReturn('200',23,'successful',$record);
	}
	
	/**
	 * 用户是否关注主播
	 */
	public function getUserIsAttention(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$doteyId=isset($this->params['doteyId'])?$this->params['doteyId']:0;
		if($uid<=0||$doteyId<=0){
			$this->jsonpReturn('-13',24, Yii::t('api','illegal params'));
		}
		$weiboService=new WeiboService();
		$userService=new UserService();
		$userBase=$userService->getUserBasicByUids(array($doteyId,$uid));
		if(!isset($userBase[$uid])&&!isset($userBase[$doteyId])){
			$this->jsonpReturn('-162',16, Yii::t('api','uid or dotey_uid not exits'));
		}
		if($userService->hasBit(intval($userBase[$doteyId]['user_type']),USER_TYPE_DOTEY)&&$userBase[$doteyId]['user_status']!=USER_STATUS_OFF){
			$isAttention=$weiboService->isAttentionDotey($doteyId,$uid);
		}else{
			$isAttention=$weiboService->isAttentionUser($doteyId,$uid);
		}
		$isAttention=$isAttention?1:0;
		$this->jsonpReturn('200', 24, 'success',array('attention'=>$isAttention));
	}
	
	public function cancelPushNotice(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$device_token=isset($this->params['device_token'])?$this->params['device_token']:'';
		if($uid<=0||empty($device_token)){
			$this->jsonpReturn('-13',25, Yii::t('api','illegal params'));
		}
		$userIosService=new UserIosService();
		$set['uid']=$uid;
		$set['device_token']=$device_token;
		$set['notice']=IOS_PUSH_NOTICE_OFF;
		$IosSet=$userIosService->saveUserIosSet($set);
		if($IosSet){
			$this->jsonpReturn('200', 25, 'success');
		}
		$this->jsonpReturn('-251', 25,'取消推送通知失败');
	}
	
	public function getUserBag(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		if($uid<=0){
			$this->jsonpReturn('-13',26, Yii::t('api','illegal params'));
		}
		$giftBagService=new GiftBagService();
		$giftService=new GiftService();
		$userBag=$giftBagService->getUserGiftBagByUids($uid);
		$giftIds=$bagList=array();
		if($userBag){
			foreach($userBag[$uid] as $row){
				$giftIds[]=$row['gift_id'];
			}
			$giftInfo=$giftService->getGiftByIds($giftIds);
			foreach($userBag[$uid] as $row){
				if($row['num']>0){
					$_userBag['gift_id']=(int)$row['gift_id'];
					$_userBag['num']=(int)$row['num'];
					$_userBag['zh_name']=$giftInfo[$row['gift_id']]['zh_name'];
					$_userBag['pipiegg']=(float)$giftInfo[$row['gift_id']]['pipiegg'];
					$_userBag['image']=$giftService->getGiftUrl($giftInfo[$row['gift_id']]['image']);
					$bagList[]=$_userBag;
				}
			}
		}
		$this->jsonpReturn('200', 26, 'success',array('list'=>$bagList));
	}
	
	public function isCheckin(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		if($uid<=0){
			$this->jsonpReturn('-271',27, Yii::t('api','illegal params'));
		}
		
		$userService=new UserService();
		$userBasice=$userService->getUserBasicByUids(array($uid));
		if(!$userBasice){
			$this->jsonpReturn('-272',27, Yii::t('api','illegal uid'));
		}
		// 检查是否购买月卡
		$propsService = new PropsService();
		$category = $propsService->getPropsCategoryByEnName('monthcard');
		$cat_id = $category['cat_id'];
		$props = $propsService->getPropsByCatId($cat_id);
		$userPropsService = new UserPropsService();
		$propsIds = array_keys($props);
		$propId = $propsIds[0];
		$isMoon = $userPropsService->getUserValidPropsOfBagByPropId($uid,$propId,time());
		$userGiftService = new UserGiftService();
		$isCheckin=false;
		if($isMoon){
			$etime = $isMoon[0]['valid_time'];
			$stime = $etime - (30 *24*60*60);
			$etime = mktime(1,0,0,date('m',$etime),date('d',$etime)+1,date('Y',$etime));
			$num = $userGiftService->countMonthGift($uid, $stime, $etime);
			if($num > 0){
				$isCheckin = $userGiftService->getIsCheckin($uid,CHENKIN_MONTHCARD);
			}
		}
		$isCheckin = $userGiftService->getIsCheckin($uid,CHENKIN_NORMAL);
		$isCheckin=$isCheckin?1:0;
		$this->jsonpReturn('200',27,'success',array('isCheckin'=>$isCheckin));
	}
	
	
	public function checkRecharge(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$productId=isset($this->params['product_id'])?$this->params['product_id']:'';
		$amount=isset($this->params['amount'])?$this->params['amount']:1;
		if($uid<=0||empty($productId)){
			$this->jsonpReturn('-281',28, Yii::t('api','illegal params'));
		}
		if(!$this->isLogin($uid)){
			$this->jsonpReturn('-17',28,Yii::t('user','You are not logged'));
		}
		$is_product=false;
		foreach($this->apple_product_list as $row){
			if($productId==$row['product_id']){
				$money=$row['dollar']*$amount;
				$pipiegg=$row['pipiegg']*$amount;
				$is_product=true;
			}
		}
		if($is_product==false){
			$this->jsonpReturn('-283',28, Yii::t('api','Product not found'));
		}
		$userRechargeService=new UserRechargeService();
		if($userRechargeService->checkUserRechargeLimitByDay($uid)==false){
			$this->jsonpReturn('-284',28, Yii::t('api','The day of purchase to reach the maximum'));
		}
		$records['ruid']=$uid;
		$records['money']=$money;
		$records['rorderid']=$this->getorderid();
		$records['currencycode']=CURRENCY_USD;
		$records['pipiegg']=$pipiegg;
		$records['rsource']='Apple Store';
		$records['summary']=$productId;
		$orderId=$userRechargeService->saveUserRechargeRecords($records);
		if($orderId<=0){
			$this->jsonpReturn('-284',28, Yii::t('api','Users recharge record is written exception'));
		}
		$this->jsonpReturn('200',28, 'success',array('order_id'=>$orderId));
	}
	
	public function checkReceiptData(){
		$orderId=isset($this->params['order_id'])?$this->params['order_id']:0;
		$receipt=isset($this->params['receipt'])?$this->params['receipt']:'';
		if($orderId<=0||empty($receipt)){
			$this->jsonpReturn('-291',29, Yii::t('api','illegal params'),array('order_id'=>$orderId));
		}
		
		$userRechargeService=new UserRechargeService();
		$rechargeRecords=UserRechargeRecordsModel::model()->findByPk($orderId);
		if(!$rechargeRecords||(isset($rechargeRecords['issuccess'])&&$rechargeRecords['issuccess']!=1)){
			$this->jsonpReturn('200',29, 'success',array('status'=>-2,'order_id'=>$orderId));
		}
		
		$postData = json_encode(array('receipt-data' => $receipt));
		$ch = curl_init($this->verifyReceipt);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		$response = curl_exec($ch);
		$errno    = curl_errno($ch);
		$errmsg   = curl_error($ch);
		curl_close($ch);
		if ($errno != 0) {
			$this->jsonpReturn('200',29,'success',array('status'=>-3,'order_id'=>$orderId));
		}
		$data = json_decode($response);
		if (!is_object($data)) {
			$this->jsonpReturn('200',29, 'success',array('status'=>-1,'order_id'=>$orderId));
		}
		if (!isset($data->status) || $data->status != 0) {
			$this->jsonpReturn('200',29, 'success',array('status'=>$data->status,'order_id'=>$orderId));
		}
		$responseData=array(
			'quantity'       =>  $data->receipt->quantity,
			'product_id'     =>  $data->receipt->product_id,
			'transaction_id' =>  $data->receipt->transaction_id,
			'purchase_date'  =>  $data->receipt->purchase_date,
			'app_item_id'    =>  $data->receipt->item_id,
			'bid'            =>  $data->receipt->bid,
			'bvrs'           =>  $data->receipt->bvrs
		);
		$userRechargeReocrds=new UserRechargeRecordsModel();
		if($userRechargeReocrds->updateByPk($orderId,array('rorderid'=>'ios_'.$responseData['transaction_id'],'issuccess'=>2,'ctime'=>time()))){
			$consumeService = new ConsumeService();
			if($consumeService->addEggs($rechargeRecords['uid'],$rechargeRecords['pipiegg'])){
				$consumeService->saveUserConsumeAttribute(array('uid'=>$rechargeRecords['uid'],'pipiegg'=>$rechargeRecords['pipiegg']));
				$pipiRecord['uid'] = $rechargeRecords['uid'];
				$pipiRecord['record_sid'] = $responseData['transaction_id'];
				$pipiRecord['source'] = SOURCE_RECHARGE;
				$pipiRecord['sub_source'] = SUBSOURCE_RECHARGE_ADDPIPIEGG;
				$pipiRecord['extra']= 'iphone皮蛋充值';
				$pipiRecord['pipiegg'] = $rechargeRecords['pipiegg'];
				$pipiRecord['client'] = CLIENT_MOBILE ;
				$pipiRecord['ip_address'] = $rechargeRecords['rip'];
				$pipiRecord['consume_time'] = time();
				$consumeService->saveUserPipiEggRecords($pipiRecord,1);
				$this->jsonpReturn('200',29, 'success',array('status'=>0,'order_id'=>$orderId));
			}
		}
		$this->jsonpReturn('200',29, 'success',array('status'=>-2,'order_id'=>$orderId));
	}
	
	public function getAttentions(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$pagesize=isset($this->params['pagesize'])?$this->params['pagesize']:50;
		$page=isset($this->params['page'])?$this->params['page']:1;
		if($uid<=0){
			$this->jsonpReturn('-16',30,Yii::t('api','params not empty'));
		}
		if(isset($pagesize)){
			if(!is_numeric($pagesize)||$pagesize<=0||strlen($pagesize)>10){
				$this->jsonpReturn('-301',30,Yii::t('api','illegal pagesize'));
			}
		}
		
		if(isset($page)){
			if(!is_numeric($page)||$page<=0||strlen($page)>10){
				$this->jsonpReturn('-302',30,Yii::t('api','illegal page'));
			}
		}
		$weiboService=new WeiboService();
		$userService=new UserService();
		$consumeService=new ConsumeService();
		$condition['fans_uid']=$uid;
		$condition['limit']=$pagesize;
		$condition['offset']=$page-1;
		$condition['isCount']=true;
		$fans=$weiboService->getUserAttentionsByCondition($condition);
		$fansList=array();
		if($fans[0]){
			$fansUid=array();
			foreach($fans[0] as $row){
				$fansUid[]=$row['uid'];
			}
			$userBase=$userService->getUserBasicByUids($fansUid);
			$userExtend=$userService->getUserExtendByUids($fansUid);
			$userConsume=$consumeService->getConsumesByUids($fansUid);
			foreach($fans[0] as $key=>$row){
				if(isset($userBase[$row['uid']])){
					$fansList[$key]['uid']=$row['uid'];
					$fansList[$key]['nickname']=$userBase[$row['uid']]['nickname'];
					$fansList[$key]['big_avatar']=$userService->getUserAvatar($row['uid'],'big');
					$fansList[$key]['middle_avatar']=$userService->getUserAvatar($row['uid'],'middle');
					$fansList[$key]['small_avatar']=$userService->getUserAvatar($row['uid'],'small');
					$fansList[$key]['rank']=(int)$userConsume[$row['uid']]['rank'];
					$fansList[$key]['dotey_rank']=isset($userConsume[$row['uid']]['dotey_rank'])?(int)$userConsume[$row['uid']]['dotey_rank']:0;
					$fansList[$key]['gender']=isset($userExtend[$row['uid']])?$userExtend[$row['uid']]['gender']:0;
				}
			}
		}
		$total=$fans[1];
		$this->jsonpReturn('200',30, 'success',array('total'=>$total,'list'=>$fansList));
	}
	
	public function getFans(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$pagesize=isset($this->params['pagesize'])?$this->params['pagesize']:$this->pagesize;
		$page=isset($this->params['page'])?$this->params['page']:$this->page;
		if($uid<=0){
			$this->jsonpReturn('-16',31,Yii::t('api','params not empty'));
		}
		
		if(isset($pagesize)){
			if(!is_numeric($pagesize)||$pagesize<=0||strlen($pagesize)>10){
				$this->jsonpReturn('-311',31,Yii::t('api','illegal pagesize'));
			}
		}
	
		if(isset($page)){
			if(!is_numeric($page)||$page<=0||strlen($page)>10){
				$this->jsonpReturn('-312',31,Yii::t('api','illegal page'));
			}
		}
		$weiboService=new WeiboService();
		$userService=new UserService();
		$consumeService=new ConsumeService();
		$condition['uid']=$uid;
		$condition['limit']=$pagesize;
		$condition['offset']=$page-1;
		$condition['isCount']=true;
		$fans=$weiboService->getUserFansByCondition($condition);
		$fansList=array();
		if($fans[0]){
			$fansUid=array();
			foreach($fans[0] as $row){
				$fansUid[]=$row['fans_uid'];
			}
			$userBase=$userService->getUserBasicByUids($fansUid);
			$userExtend=$userService->getUserExtendByUids($fansUid);
			$userConsume=$consumeService->getConsumesByUids($fansUid);
			foreach($fans[0] as $key=>$row){
				if(isset($userBase[$row['fans_uid']])){
					$fansList[$key]['uid']=$row['fans_uid'];
					$fansList[$key]['nickname']=$userBase[$row['fans_uid']]['nickname'];
					$fansList[$key]['big_avatar']=$userService->getUserAvatar($row['fans_uid'],'big');
					$fansList[$key]['middle_avatar']=$userService->getUserAvatar($row['fans_uid'],'middle');
					$fansList[$key]['small_avatar']=$userService->getUserAvatar($row['fans_uid'],'small');
					$fansList[$key]['rank']=(int)$userConsume[$row['fans_uid']]['rank'];
					$fansList[$key]['dotey_rank']=isset($userConsume[$row['fans_uid']]['dotey_rank'])?(int)$userConsume[$row['fans_uid']]['dotey_rank']:0;
					$fansList[$key]['gender']=isset($userExtend[$row['fans_uid']])?$userExtend[$row['fans_uid']]['gender']:0;
				}
			}
		}
		$total=$fans[1];
		$this->jsonpReturn('200',31, 'success',array('total'=>$total,'list'=>$fansList));
	}
	
	public function modifyPassword(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$old_password=isset($this->params['old_password'])?$this->params['old_password']:'';
		$new_password=isset($this->params['new_password'])?$this->params['new_password']:'';
		if($uid<=0||$old_password==''||$new_password==''){
			$this->jsonpReturn('-16',32,Yii::t('api','params not empty'));
		}
		if(!$this->isLogin($uid)){
			$this->jsonpReturn('-17',32,Yii::t('user','You are not logged'));
		}
		$userService=new UserService();
		$userBase=$userService->getUserBasicByUids(array($uid));
		$old_password = $userService->encryPassword($old_password, $userBase[$uid]['reg_salt']);
		if($userBase[$uid]['password']!=$old_password){
			$this->jsonpReturn('-321',32,Yii::t('api','The original password error'));
		}
		if(!$userService->saveUserBasic(array('uid'=>$uid,'password'=>$new_password))){
			$this->jsonpReturn('-322',32,Yii::t('api','Password change fails'));
		}
		$this->jsonpReturn('200',32,'success');
	}
	
	public function modifyNickname(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$nickname=isset($this->params['nickname'])?$this->params['nickname']:'';
		if($uid<=0||$nickname==''){
			$this->jsonpReturn('-16',33,Yii::t('api','params not empty'));
		}
		if(!$this->isLogin($uid)){
			$this->jsonpReturn('-17',33,Yii::t('user','You are not logged'));
		}
		$userService = new UserService();
		$check_result = self::check_show_nickname($uid,$nickname);
		if($check_result){
			$this->jsonpReturn('-331',33,$check_result);
		}
		$res = $userService->saveUserJson($uid, array('uid'=>$uid,'nickname'=>$nickname));
		if(!$res) {
			$this->jsonpReturn('-332',33,Yii::t('api','Nickname modification fails'));
		}
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByUid($uid);
		$archivesCat=$archivesService->getAllArchiveCatByEnName('common');
		$title=$nickname.'的直播间';
		foreach($archives as $row){
			if($row['cat_id']==$archivesCat['cat_id']){
				$archivesService->saveArchives(array('archives_id'=>$row['archives_id'],'title'=>$title));
			}
		}
		$this->jsonpReturn('200',33,'success');
	}
	
	public function modifyArea(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$province=isset($this->params['province'])?$this->params['province']:'';
		$city=isset($this->params['city'])?$this->params['city']:'';
		if($uid<=0||($province==''&&$city=='')){
			$this->jsonpReturn('-16',34,Yii::t('api','params not empty'));
		}
		if(!$this->isLogin($uid)){
			$this->jsonpReturn('-17',34,Yii::t('user','You are not logged'));
		}
		$userService=new UserService();
		$userExtend['uid']=$uid;
		$province&&$userExtend['province']=$province;
		$city&&$userExtend['city']=$city;
		$res = $userService->saveUserExtend($userExtend);
		if(!$res){
			$this->jsonpReturn('-341',34,Yii::t('api','Address modification fails'));
		}
		$this->jsonpReturn('200',34,'success');
	}
	
	public function modifyAvatar(){
		$uid=isset($this->params['uid'])?$this->params['uid']:'';
		if($uid<=0){
			$this->jsonpReturn('-16',35,Yii::t('api','params not empty'));
		}
// 		if(!$this->isLogin($uid)){
// 			$this->jsonpReturn('-17',35,Yii::t('user','You are not logged'));
// 		}
		header("Expires: 0");
		header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		header("Pragma: no-cache");
		if ( empty($_FILES['avatar']) ) {
			$this->jsonpReturn('-351',35,Yii::t('api','Avatar file not exit'));
		}
		$PipiFlashUpload=new PipiFlashUpload();
		$tmpPath = $PipiFlashUpload->getTmpSavePath();
		if (!is_dir( $tmpPath ) ) {
			mkdir( $tmpPath, 0777, true );
		}
		$format=explode('.',$_FILES['avatar']['name']);
		$tmpPath .= $PipiFlashUpload->filePrefix.$uid.'.'.$format[1];
		if (is_file($tmpPath) ) {
			unlink($tmpPath);
		}
		
		if($_FILES['avatar']['tmp_name']){
			// 把上传的图片文件保存到预定位置
			if (copy($_FILES['image']['tmp_name'], $tmpPath) || move_uploaded_file($_FILES['avatar']['tmp_name'], $tmpPath)) {
				unlink($_FILES['avatar']['tmp_name']);
				list($width, $height, $type, $attr) = getimagesize($tmpPath);
				if ( $width < 10 || $height < 10 || $width > 3000 || $height > 3000 || $type == 4 ) {
					unlink($tmpPath);
					$this->jsonpReturn('-352',35,Yii::t('api','Invalid avatar'));
				}
			} else {
				unlink($_FILES['avatar']['tmp_name']);
				$this->jsonpReturn('-352',35,Yii::t('api','Can not write to the data/tmp folder'));
			}
		}else{
			$this->jsonpReturn('-352',35,Yii::t('api','Invalid avatar'));
		}
		$this->thumbImg($tmpPath, 200, 200);
		$PipiFlashUpload->setSaveDirPath($uid);
		$bigAvatar=$PipiFlashUpload->getSaveFile($uid,'big');
		copy($tmpPath, $bigAvatar);
		$this->thumbImg($tmpPath, 120, 120);
		$middleAvatar=$PipiFlashUpload->getSaveFile($uid,'middle');
		copy($tmpPath, $middleAvatar);
		$this->thumbImg($tmpPath, 48, 48);
		$smallAvatar=$PipiFlashUpload->getSaveFile($uid,'small');
		copy($tmpPath, $smallAvatar);
		exec('/data/webservice/crontab/letianImgRsync/rsync.sh');
		$this->jsonpReturn('200',35,'success');
	}
	
	public function getOnline(){
		$webConfigService=new WebConfigService();
		$online_count=$webConfigService->getOnlineCount();
		$this->jsonpReturn('200',36,'success',array('online_count'=>$online_count));
	}
	
	public function getRankList(){
		$type=isset($this->params['type'])?$this->params['type']:'';
		if($type==''){
			$this->jsonpReturn('-16',37,Yii::t('api','params not empty'));
		}
		$rankType=array('star_today','star_week','star_month','star_super',
			'rich_today','rich_week','rich_month','rich_super');
		if(!in_array($type,$rankType)){
			$this->jsonpReturn('-371',37,Yii::t('api','Ranking Type error'));
		}
		$newType=explode('_',$type);
		$userService=new UserService();
		$rankList=$newRankList=array();
		if($newType[0]=='star'){
			$rankList=$userService->getUserCharmRank($newType[1],false);
		}else{
			$rankList=$userService->getUserRichRank($newType[1]);
		}
		if($rankList){
			$rankUid=array();
			foreach($rankList as $row){
				$rankUid[]=$row['d_uid'];
			}
			$userExtend=$userService->getUserExtendByUids($rankUid);
			foreach($rankList as $key=>$row){
				$newRankList[$key]['uid']=$row['d_uid'];
				$newRankList[$key]['nickname']=$row['d_nickname'];
				$newRankList[$key]['avatar']=$row['d_avatar'];
				$userAttribute=$userService->getUserFrontsAttributeByCondition($row['d_uid'],true,true);
				$newRankList[$key]['rank']=isset($userAttribute['rk'])?$userAttribute['rk']:0;
				$newRankList[$key]['dotey_rank']=$row['d_rank'];
				$newRankList[$key]['gender']=isset($userExtend['d_uid'])?$userExtend['d_uid']['gender']:0;
			}
		}
		$this->jsonpReturn('200',37,'success',$newRankList);
	}
	
	public function getGiftRank(){
		$type=isset($this->params['type'])?$this->params['type']:'';
		if($type==''){
			$this->jsonpReturn('-16',38,Yii::t('api','params not empty'));
		}
		$rankType=array('star_week','star_lastweek','rich_week','rich_lastweek');
		if(!in_array($type,$rankType)){
			$this->jsonpReturn('-381',38,Yii::t('api','Ranking Type error'));
		}
		$newType=explode('_',$type);
		$rankList=$newRankList=array();
		$giftService=new GiftService();
		$userService=new UserService();
		$rankList=$giftService->getDoteyGiftRank($newType[1]);
		$rankUid=array();
		if($newType[0]=='star'){
			if($rankList){
				foreach($rankList as $row){
					$rankUid[]=$row['d_uid'];
				}
				$userExtend=$userService->getUserExtendByUids($rankUid);
				foreach($rankList as $key=>$row){
					$newRankList[$key]['uid']=$row['d_uid'];
					$newRankList[$key]['nickname']=$row['d_nickname'];
					$newRankList[$key]['gift_count']=$row['num'];
					$newRankList[$key]['gift_name']=$row['gift_name'];
					$newRankList[$key]['gift_pic']=$giftService->getGiftUrl($row['picture']);
					$newRankList[$key]['rank']=$row['d_rank'];
					$newRankList[$key]['gender']=isset($userExtend[$row['d_uid']])?$userExtend[$row['d_uid']]['gender']:0;
				}
			}
		}else{
			if($rankList){
				foreach($rankList as $row){
					$rankUid[]=$row['d_uid'];
				}
				$userExtend=$userService->getUserExtendByUids($rankUid);
				foreach($rankList as $key=>$row){
					$newRankList[$key]['uid']=$row['s_uid'];
					$newRankList[$key]['nickname']=$row['s_nickname'];
					$newRankList[$key]['gift_count']=$row['s_num'];
					$newRankList[$key]['gift_name']=$row['gift_name'];
					$newRankList[$key]['gift_pic']=$giftService->getGiftUrl($row['picture']);
					$newRankList[$key]['rank']=$row['s_rank'];
					$newRankList[$key]['gender']=isset($userExtend[$row['d_uid']])?$userExtend[$row['d_uid']]['gender']:0;
				}
			}
		}
		$this->jsonpReturn('200',38,'success',$newRankList);
	}
	
	public function searchUser(){
		$key=isset($this->params['key'])?$this->params['key']:'';
		$pagesize=isset($this->params['pagesize'])?$this->params['pagesize']:20;
		$page=isset($this->params['page'])?$this->params['page']:1;
		if($key==''){
			$this->jsonpReturn('-16',39,Yii::t('api','params not empty'));
		}
		if(mb_strlen($key,'UTF8')<3){
			$this->jsonpReturn('-391',39,'关键词至少三个字符');
		}
		if(isset($pagesize)){
			if(!is_numeric($pagesize)||$pagesize<=0||strlen($pagesize)>10){
				$this->jsonpReturn('-392',39,Yii::t('api','illegal pagesize'));
			}
		}
		
		if(isset($page)){
			if(!is_numeric($page)||$page<=0||strlen($page)>10){
				$this->jsonpReturn('-393',39,Yii::t('api','illegal page'));
			}
		}
		$res=file_get_contents('http://show.pipi.cn/select/?sort=int_number%20asc,score%20desc&df=all&wt=json&indent=true&rows=200&q='.urlencode($key));
		$response=json_decode($res,true);
// 		if($response['response']['numFound']>$page*$pagesize){
// 			$this->jsonpReturn('-382',38,Yii::t('api','illegal page'));
// 		}
		
		$userList=$dataList=$data=array();
		
		if(isset($response['response']['docs'])){
			$userService=new UserService();
			$consumeService=new ConsumeService();
			$data=array_slice($response['response']['docs'], ($page-1)*$pagesize,$pagesize);
			foreach($data as $key=>$row){
				$dataList[$key]['uid']=$row['uid'];
				$dataList[$key]['nickname']=$row['nickname'];
				$dataList[$key]['avatar']=$userService->getUserAvatar($row['uid'],'small');
				$userConsume=$consumeService->getConsumesByUids($row['uid']);
				$dataList[$key]['rank']=isset($userConsume[$row['uid']]['rank'])?$userConsume[$row['uid']]['rank']:0;
				$dataList[$key]['dotey_rank']=isset($userConsume[$row['uid']]['dotey_rank'])?$userConsume[$row['uid']]['dotey_rank']:0;
				$userExtend=$userService->getUserExtendByUids(array($row['uid']));
				$dataList[$key]['gender']=isset($userExtend[$row['uid']]['gender'])?$userExtend[$row['uid']]['gender']:0;
			}
			
		}
		$userList['list']=$dataList;
		$userList['total']=$response['response']['numFound']?$response['response']['numFound']:0;
		$this->jsonpReturn('200',39,'success',$userList);
	}
	
	public function getVip(){
		$propsService=new PropsService();
		$consumeService=new ConsumeService();
		$category =$propsService->getPropsCategoryByEnName('vip');
		$id = $category['cat_id'];
		$props =  $propsService->getPropsByCatId($id,false,true);
		$userRank =  $propsService->buildDataByIndex( $consumeService->getAllUserRanks(),'rank');
		foreach($props as $key=>$prop){
			$attribute =  $propsService->buildDataByIndex($prop['attribute'],'attr_enname');
			$januaryPrice = $attribute['vip_price_january'];
			$marchPrice = $attribute['vip_price_march'];
			$junePrice = $attribute['vip_price_june'];
			$decemberPrice = $attribute['vip_price_december'];
			$props[$key]['image'] = $propsService->getUploadUrl().$props[$key]['image'];
			if(isset($januaryPrice['attr_id'])){
				$props[$key]['priceList'][]=array('id'=>$januaryPrice['attr_id'],'time'=>$januaryPrice['attr_name'],'pipiegg'=>$januaryPrice['value']);
			}
			if(isset($marchPrice['attr_id'])){
				$props[$key]['priceList'][]=array('id'=>$marchPrice['attr_id'],'time'=>$marchPrice['attr_name'],'pipiegg'=>$marchPrice['value']);
			}
			if(isset($junePrice['attr_id'])){
				$props[$key]['priceList'][]=array('id'=>$junePrice['attr_id'],'time'=>$junePrice['attr_name'],'pipiegg'=>$junePrice['value']);
			}
			if(isset($decemberPrice['attr_id'])){
				$props[$key]['priceList'][]=array('id'=>$decemberPrice['attr_id'],'time'=>$decemberPrice['attr_name'],'pipiegg'=>$decemberPrice['value']);
			}
			if($prop['rank']){
				$props[$key]['rank_desc'] = $userRank[$prop['rank']]['name'].'以上';
			}else{
				$props[$key]['rank_desc'] = '不限';
			}
			unset($props[$key]['attribute']);
		
		}
		$i=0;
		foreach($props as $key=>$row){
			$newProps[$i]['prop_id']=$row['prop_id'];
			$newProps[$i]['name']=$row['name'];
			$newProps[$i]['image']=$row['image'];
			$newProps[$i]['limit_rank']=$row['rank_desc'];
			$newProps[$i]['price']=$row['priceList'];
			$i++;
		}
		$this->jsonpReturn('200',40,'success',$newProps);
	}
	
	public function getVipPurview(){
		$vipData=array(
			array(
				'name'=>'VIP尊贵标志',
				'description'=>'将在用户列表里，显示尊贵标志',
				'yellow_vip'=>array('is_own'=>true,'remark'=>'黄色VIP'),
				'purple_vip'=>array('is_own'=>true,'remark'=>'紫色色VIP'),
				'is_open'=>true
			),
			array(
				'name'=>'VIP红名',
				'description'=>'在观众列表里，昵称以醒目红色显示',
				'yellow_vip'=>array('is_own'=>false,'remark'=>'无'),
				'purple_vip'=>array('is_own'=>true,'remark'=>'有'),
				'is_open'=>true
			),
			array(
				'name'=>'排位靠前',
				'description'=>'大幅提升排位，让你更受瞩目',
				'yellow_vip'=>array('is_own'=>true,'remark'=>'普通用户富豪15之上'),
				'purple_vip'=>array('is_own'=>true,'remark'=>'在VIP会员之上'),
				'is_open'=>true
			),
			array(
				'name'=>'入场欢迎',
				'description'=>'VIP会员进场欢迎语',
				'yellow_vip'=>array('is_own'=>true,'remark'=>'有'),
				'purple_vip'=>array('is_own'=>true,'remark'=>'有'),
				'is_open'=>true
			),
			array(
				'name'=>'VIP表情',
				'description'=>'VIP会员专用表情',
				'yellow_vip'=>array('is_own'=>true,'remark'=>'有'),
				'purple_vip'=>array('is_own'=>true,'remark'=>'有'),
				'is_open'=>true
			),
			array(
				'name'=>'免费贴条',
				'description'=>'免费贴条',
				'yellow_vip'=>array('is_own'=>true,'remark'=>'每天15个'),
				'purple_vip'=>array('is_own'=>true,'remark'=>'每天25个'),
				'is_open'=>true
			),
			array(
				'name'=>'飞屏折扣',
				'description'=>'购买优惠折扣(在PC电脑端)',
				'yellow_vip'=>array('is_own'=>true,'remark'=>'享9折'),
				'purple_vip'=>array('is_own'=>true,'remark'=>'享8折'),
				'is_open'=>true
			),
			array(
				'name'=>'隐身特权',
				'description'=>'可隐身进入直播间(发言、送礼等行为会导致隐身失效)',
				'yellow_vip'=>array('is_own'=>false,'remark'=>'无'),
				'purple_vip'=>array('is_own'=>true,'remark'=>'有'),
				'is_open'=>true
			),
			array(
				'name'=>'防踢',
				'description'=>'防止被房管踢出房间(高级VIP会员达到国公可防3冠主播踢人)',
				'yellow_vip'=>array('is_own'=>false,'remark'=>'无'),
				'purple_vip'=>array('is_own'=>true,'remark'=>'有'),
				'is_open'=>true
			),
			array(
				'name'=>'防禁言',
				'description'=>'防止被房管禁言(高级VIP会员达到国师可防3冠主播禁言)',
				'yellow_vip'=>array('is_own'=>true,'remark'=>'有'),
				'purple_vip'=>array('is_own'=>true,'remark'=>'有'),
				'is_open'=>true
			),
			array(
				'name'=>'*禁言他人(即将开放)',
				'description'=>'高级VIP会员达到国公以上可禁言普通用户(限每天5次，次数随等级提升增加)',
				'yellow_vip'=>array('is_own'=>false,'remark'=>'无'),
				'purple_vip'=>array('is_own'=>true,'remark'=>'有'),
				'is_open'=>false
			),
			array(
				'name'=>'*踢人(即将开放)',
				'description'=>'高级VIP会员达到亲王以上可以踢出普通用户(限每天1次，次数随等级提升增加)',
				'yellow_vip'=>array('is_own'=>false,'remark'=>'无'),
				'purple_vip'=>array('is_own'=>true,'remark'=>'有'),
				'is_open'=>false
			)
		);
		$this->jsonpReturn('200',41,'success',$vipData);
	}
	
	public function buyVip(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$agent_id=isset($this->params['agent_id'])?$this->params['agent_id']:0;
		$propId=isset($this->params['prop_id'])?$this->params['prop_id']:0;
		$priceId=isset($this->params['price_id'])?$this->params['price_id']:0;
		
		
		if($uid<=0 || $propId <= 0 || $priceId <= 0){
			$this->jsonpReturn('-16',42,Yii::t('api','params not empty'));
		}
		if(!$this->isLogin($uid)){
			$this->jsonpReturn('-17',42,Yii::t('user','You are not logged'));
		}
		$buyVipPropsService = new BuyVipPropsService($uid,$propId,1);
		$buyVipPropsService->isCheckExpired = false;
		$buyVipPropsService->isCheckBuy = false;
		$buyVipPropsService->priceAttrId = $priceId;
		$flag = $buyVipPropsService->buyProps();
		
		//通过代理销售
		if($flag && !empty($agent_id) && $agent_id>0)
		{
			$agentsService=new AgentsService();
			$agentRate=$agentsService->getRateByUid($agent_id);
			$_pipieggs=$buyVipPropsService->getPropsPrice();
			$saleRecords=array(
				'agent_id'=>$agent_id,
				'uid'=>$uid,
				'goods_type'=>0,	//道具
				'goods_id'=>$propId,
				'goods_num'=>1,
				'pipieggs'=>$_pipieggs,
				'agent_income'=>$_pipieggs*$agentRate
			);
			$agentsService->saveSaleRecords($saleRecords);
		}
		
		$message = $buyVipPropsService->getErrorCode();
		if($flag && empty($message)){
			$consumeService=new ConsumeService();
			$userConsume=$consumeService->getConsumesByUids($uid);
			$this->jsonpReturn('200',42,'success',array('pipiegg'=>$userConsume[$uid]['pipiegg']-$userConsume[$uid]['freeze_pipiegg']));
		}
		$this->jsonpReturn('-421',42,$message);
	}
	
	public function getCar(){
		$propsService=new PropsService();
		$consumeService=new ConsumeService();
		$category = $propsService->getPropsCategoryByEnName('car');
		$id = $category['cat_id'];
		$props = $propsService->getPropsByCatId($id,false,true);
		$userRank = $propsService->buildDataByIndex($consumeService->getAllUserRanks(),'rank');
		
		foreach($props as $key=>$prop){
			$attribute = $propsService->buildDataByIndex($prop['attribute'],'attr_enname');
			if(isset($attribute['car_is_limit']['value'])){
				$props[$key]['limit_type'] = $attribute['car_is_limit']['value'];
				$props[$key]['limit_num'] = $attribute['car_limit']['value'];
			}else{
				$props[$key]['limit_type'] = 0;
				$props[$key]['limit_num'] = 0;
			}
			$props[$key]['image'] = $propsService->getUploadUrl().$props[$key]['image'];
			//是否有座架logo
			$props[$key]['car_logo']= isset($attribute['car_logo']['value'])?$propsService->getUploadUrl().$attribute['car_logo']['value']:'';
			$seventPrice = $attribute['car_price_sevenday'];
			$ninePrice = $attribute['car_price_ninetyday'];
			$permentPrice = $attribute['car_price_permanent'];
			$yearPrice = isset($attribute['car_price_year'])?$attribute['car_price_year']:array();
			
			$props[$key]['priceList'] = array();
			if(isset($seventPrice['value'])&&$seventPrice['value']>0){
				$props[$key]['priceList'][] = array('id'=>$seventPrice['attr_id'],'pipiegg'=>$seventPrice['value'],'time'=>'7天');
			}
			if(isset($ninePrice['value'])&&$seventPrice['value']>0){
				$props[$key]['priceList'][] = array('id'=>$ninePrice['attr_id'],'pipiegg'=>$ninePrice['value'],'time'=>'90天');
			}
			if(isset($yearPrice['value'])&&$yearPrice['value']>0){
				$props[$key]['priceList'][] = array('id'=>$yearPrice['attr_id'],'pipiegg'=>$yearPrice['value'],'time'=>'1年');
			}
			if(isset($permentPrice['value'])&&$permentPrice['value']>0){
				$props[$key]['priceList'][] = array('id'=>$permentPrice['attr_id'],'pipiegg'=>$permentPrice['value'],'time'=>'永久');
			}
				
		
			if($prop['rank']){
				$props[$key]['rank_desc'] = $userRank[$prop['rank']]['name'].'以上';
			}else{
				$props[$key]['rank_desc'] = '不限';
			}
			if($prop['rank']){
				$props[$key]['rank_desc'] = $userRank[$prop['rank']]['name'].'以上';
			}else{
				$props[$key]['rank_desc'] = '不限';
			}
			unset($props[$key]['attribute']);
		
		}
		$newProps=array();
		$i=0;
		foreach($props as $key=>$row){
			$newProps[$i]['prop_id']=$row['prop_id'];
			$newProps[$i]['name']=$row['name'];
			$newProps[$i]['image']=$row['image'];
			$newProps[$i]['icon']=$row['car_logo'];
			$newProps[$i]['limit_type']=$row['limit_type'];
			$newProps[$i]['limit_num']=$row['limit_num'];
			$newProps[$i]['limit_rank']=$row['rank_desc'];
			$newProps[$i]['price']=$row['priceList'];
			$i++;
		}
		$this->jsonpReturn('200',43,'success',$newProps);
	}
	
	public function buyCar(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$agent_id=isset($this->params['agent_id'])?$this->params['agent_id']:0;
		$propId=isset($this->params['prop_id'])?$this->params['prop_id']:0;
		$priceId=isset($this->params['price_id'])?$this->params['price_id']:0;
		
		
		if($uid<=0 || $propId <= 0 || $priceId <= 0){
			$this->jsonpReturn('-16',44,Yii::t('api','params not empty'));
		}
		if(!$this->isLogin($uid)){
			$this->jsonpReturn('-17',44,Yii::t('user','You are not logged'));
		}
		$buyCarPropsService = new BuyCarPropsService($uid,$propId,1);
		$buyCarPropsService->isCheckExpired = false;
		$buyCarPropsService->isCheckBuy = false;
		$buyCarPropsService->priceAttrId = $priceId;
		$flag = $buyCarPropsService->buyProps();
		
		//通过代理销售
		if($flag && !empty($agent_id) && $agent_id>0)
		{	$agentsService=new AgentsService();
			$agentRate=$agentsService->getRateByUid($agent_id);
			$_pipieggs=$buyCarPropsService->getPropsPrice();
			$saleRecords=array(
				'agent_id'=>$agent_id,
				'uid'=>$uid,
				'goods_type'=>0,	//道具
				'goods_id'=>$propId,
				'goods_num'=>1,
				'pipieggs'=>$_pipieggs,
				'agent_income'=>$_pipieggs*$agentRate
			);
			$this->agentsService->saveSaleRecords($saleRecords);
		}
		
		$message = $buyCarPropsService->getErrorCode();
		if($flag && empty($message)){
			$consumeService=new ConsumeService();
			$userConsume=$consumeService->getConsumesByUids($uid);
			$this->jsonpReturn('200',44,'success',array('pipiegg'=>$userConsume[$uid]['pipiegg']-$userConsume[$uid]['freeze_pipiegg']));
		}
		$this->jsonpReturn('-441',44,$message);
	}
	
	public function version(){
		$version=array('android'=>array(
			'pad'=>array('app_url'=>'http://','min_version'=>'1.0.0.0','current_version'=>'1.0.0.0'),
			'phone'=>array('app_url'=>'http://','min_version'=>'1.0.0.0','current_version'=>'1.0.0.0')),
			'IOS'=>array(
				'pad'=>array('app_url'=>'http://','min_version'=>'1.0.0.0','current_version'=>'1.0.0.0'),
				'phone'=>array('app_url'=>'http://itunes.apple.com/app/id689899999?mt=8','min_version'=>'1.0.0.0','current_version'=>'1.0.0.0')));
		$this->jsonpReturn('200',45,'success',$version);
	}
	
	//获取印象标签
	public function getTags(){
		$doteyTagsService=new DoteyTagsService();
		$list=$doteyTagsService->getAllTags(true);
		$tags=array();
		if($list){
			foreach($list as $row){
				$tags[]=array(
						'tag_id'=>$row['tag_id'],
						'tag_name'=>$row['tag_name'],
						'use_num'=>$row['use_nums']
					);
			}
		}
		$this->jsonpReturn('200',46,'success',$tags);
	}
	
	
	//获取移动活动公告
	public function getNotices(){
		$mobileAdvService=new MobileAdvService();
		$list=$mobileAdvService->getAllAdv();
		$adv=array();
		if($list){
			foreach($list as $row){
				$image=$mobileAdvService->getUploadUrl().$row['image'];
				$adv[]=array(
						'title'=>$row['title'],
						'url'=>$row['url'],
						'image'=>$image
					);
			}
		}
		$this->jsonpReturn('200',47,'success',$adv);
	}
	
	//支付接口获取订单信息
	public function getAlipayOrder(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$total_fee=isset($this->params['total_fee'])?$this->params['total_fee']:0;
		if($uid<=0||$total_fee<=0||(!is_float($total_fee)&&!is_numeric($total_fee))){
			$this->jsonpReturn('-13',48, Yii::t('api','illegal params'));
		}
		$order=array(
			'uid'=>$uid,
			'total_fee'=>$total_fee,
			'appsecret'=>$this->payAppsecret,
		);
		$sign=$this->sign($order);
		unset($order['appsecret']);
		$order['sign']=$sign;
		foreach($order as $key => $value) {
			if($key!='key')
				$params[] = $key . '=' . $value;
		}
		$postData = implode('&', $params);
		$ch = curl_init($this->alipayOrderInfoUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		$response = curl_exec($ch);
		$errno    = curl_errno($ch);
		$errmsg   = curl_error($ch);
		curl_close($ch);
		if($errno){
			$this->jsonpReturn('-481',48,$errmsg);
		}
		$response=json_decode($response,true);
		if($response['flag']==1){
			$this->jsonpReturn('200',48,'success',array('orderid'=>$response['orderid'],'orderinfo'=>$response['orderinfo']));
		}else{
			$this->jsonpReturn('-482',48,'支付宝订单生成异常');
		}
	}
	
	public function checkLogin(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$session_id=isset($this->params['session_id'])?$this->params['session_id']:'';
		if($uid<=0){
			$this->jsonpReturn('-13',49, Yii::t('api','illegal params'));
		}
		if($this->isLogin($uid)){
			$this->jsonpReturn('200',49,'success',array('isLogin'=>1));
		}else{
			$this->jsonpReturn('200',49,'success',array('isLogin'=>0));
		}
	}
	
	public function getArchivesParkingCar(){
		$archives_id=isset($this->params['archives_id'])?$this->params['archives_id']:0;
		if($archives_id<=0){
			$this->jsonpReturn('-13',50, Yii::t('api','illegal params'));
		}
		$parkingService=new ParkingService();
		$list=$parkingService->getParkingListByArchives($archives_id);
		$carList=array();
		if($list){
			$propsService=new PropsService();
			foreach($list as $row){
				$carList[]=array(
						'uid'=>$row['uid'],
						'nickname'=>$row['nk'],
						'img'=>$propsService->getPropsUrl($row['img']),
						'rank'=>$row['rk']
					);
			}
		}
		$this->jsonpReturn('200',50,'success',$carList);
	}
	
	public function parkingCarInArchives(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$archives_id=isset($this->params['archives_id'])?$this->params['archives_id']:0;
		if($archives_id<=0||$uid<=0){
			$this->jsonpReturn('-13',51, Yii::t('api','illegal params'));
		}
		$parkingService=new ParkingService();
		if($parkingService->updateCarToParkingList($uid,$archives_id)){
			$this->jsonpReturn('200',51,'success',array('isParking'=>1));
		}else{
			$this->jsonpReturn('200',51,'success',array('isParking'=>0));
		}
	}
	
	public function getDynamicList(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$type=isset($this->params['type'])?$this->params['type']:'';
		$pagesize=isset($this->params['pagesize'])?$this->params['pagesize']:$this->pagesize;
		$page=isset($this->params['page'])?$this->params['page']:$this->page;
		if($uid<=0){
			$this->jsonpReturn('-13',52, Yii::t('api','illegal params'));
		}
		if($type){
			if(!in_array($type,array('dotey','ablum','super','uprade'))){
				$this->jsonpReturn('-521',52,Yii::t('api','Dynamic type is error'));
			}
		}
		if(isset($pagesize)){
			if(!is_numeric($pagesize)||$pagesize<=0||strlen($pagesize)>10){
				$this->jsonpReturn('-522',52,Yii::t('api','illegal pagesize'));
			}
		}
		
		if(isset($page)){
			if(!is_numeric($page)||$page<=0||strlen($page)>10){
				$this->jsonpReturn('-523',52,Yii::t('api','illegal page'));
			}
		}
		$dynamicService=new DynamicService();
		$data=$dynamicService->getDynamicList($uid,$type,$pagesize,$page);
		$dynamicList=array();
		if($data){
			$userService=new UserService();
			foreach($data['list'] as $row){
				$dynamicUid[]=$row['uid'];
			}
			$dynamicType=$dynamicService->getDynamicType();
			foreach($data['list'] as $key=>$row){
				$dynamicList[$key]['tid']=$row['thread_id'];
				$dynamicList[$key]['uid']=(int)$row['uid'];
				$userInfo=$userService->getUserFrontsAttributeByCondition($row['uid'],true,true);
				$dynamicList[$key]['nickname']=$userInfo['nk'];
				$dynamicList[$key]['rank']=(int)$userInfo['rk'];
				$dynamicList[$key]['dotey_rank']=$userInfo['dk'];
				$dynamicList[$key]['avatar']=$userInfo['avatar'];
				$dynamicList[$key]['title']=$row['title'];
				$dynamicList[$key]['content']=$row['content'];
				$dynamicList[$key]['image']=$row['image'];
				$dynamicList[$key]['thumb_image']=$row['thumb'];
				$dynamicList[$key]['comments']=(int)$row['comments'];
				foreach($dynamicType as $k=>$v){
					if($v==$row['source']){
						$dynamicList[$key]['source']=$k;
					}
				}
				$dynamicList[$key]['create_time']=(int)$row['create_time'];
			}
		}
		$this->jsonpReturn('200',52,'success',array('total'=>$data['count'],'list'=>$dynamicList));
	}
	

	public function getDynamicInfo(){
		$tid=isset($this->params['tid'])?$this->params['tid']:0;
		if($tid<=0){
			$this->jsonpReturn('-13',53, Yii::t('api','illegal params'));
		}
		$dynamicService=new DynamicService();
		$userService=new UserService();
		$data=$dynamicService->getDynamic($tid);
		$dynamicType=$dynamicService->getDynamicType();
		$dynamic=array();
		if($data){
			$row=array_pop($data);
			$dynamic['tid']=$row['thread_id'];
			$dynamic['uid']=$row['uid'];
			$userInfo=$userService->getUserFrontsAttributeByCondition($row['uid'],true,true);
			$dynamic['nickname']=$userInfo['nk'];
			$dynamic['dotey_rank']=$userInfo['dk'];
			$dynamic['avatar']=$userInfo['avatar'];
			$dynamic['title']=$row['title'];
			$dynamic['content']=$row['content'];
			$dynamic['image']=$row['image'];
			$dynamic['thumb_image']=$row['thumb'];
			$dynamic['comments']=$row['comments'];
			foreach($dynamicType as $k=>$v){
				if($v==$row['source']){
					$dynamic['source']=$k;
				}
			}
			$dynamic['create_time']=$row['create_time'];
		}
	
		$this->jsonpReturn('200',53,'success',$dynamic);
	}
	
	public function dynamic(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$type=isset($this->params['type'])?$this->params['type']:'';
		$title=isset($this->params['title'])?$this->params['title']:'';
		$content=isset($this->params['content'])?$this->params['content']:'';
		if($uid<=0||empty($content)){
			$this->jsonpReturn('-13',54, Yii::t('api','illegal params'));
		}
		if(!in_array($type,array('dotey','ablum','super','uprade'))){
			$this->jsonpReturn('-541',54,Yii::t('api','Dynamic type is error'));
		}
		$org_filename='';
		if ( isset($_FILES['photo']) ) {
			$albumService=new AlbumService();
			$org_filename=$albumService->uploadPhoto($uid,'photo');
		}
		$dynamicService=new DynamicService();
		$flag=$dynamicService->dynamic($uid,$content,$type,$org_filename,$title);
		if($flag<=0){
			$msg=$dynamicService->getNotice();
			$this->jsonpReturn('-542',54,array_pop($msg));
		}
		$this->jsonpReturn('200',54,'success');
	}
	
	
	public function deleteDynamic(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$tid=isset($this->params['tid'])?$this->params['tid']:0;
		if($uid<=0||$tid<=0){
			$this->jsonpReturn('-13',55, Yii::t('api','illegal params'));
		}
		$dynamicService=new DynamicService();
		$flag=$dynamicService->deleteDynamic($uid,$tid);
		if($flag<=0){
			$msg=$dynamicService->getNotice();
			$this->jsonpReturn('-552',55,array_pop($msg));
		}
		$this->jsonpReturn('200',55,'success');
	}
	
	public function getCommentList(){
		$tid=isset($this->params['tid'])?$this->params['tid']:0;
		$type=isset($this->params['type'])?$this->params['type']:'';
		$pagesize=isset($this->params['pagesize'])?$this->params['pagesize']:$this->pagesize;
		$page=isset($this->params['page'])?$this->params['page']:$this->page;
		if($tid<=0||empty($type)){
			$this->jsonpReturn('-13',56, Yii::t('api','illegal params'));
		}
		if(!in_array($type,array('dotey','ablum','super','upgrade'))){
			$this->jsonpReturn('-561',56,Yii::t('api','Dynamic type is error'));
		}
		if(isset($pagesize)){
			if(!is_numeric($pagesize)||$pagesize<=0||strlen($pagesize)>10){
				$this->jsonpReturn('-562',56,Yii::t('api','illegal pagesize'));
			}
		}
		
		if(isset($page)){
			if(!is_numeric($page)||$page<=0||strlen($page)>10){
				$this->jsonpReturn('-563',56,Yii::t('api','illegal page'));
			}
		}
		$commentService=new CommentService();
		$data=$commentService->getCommentList($tid,$type,$pagesize,$page);
		$commentList=array();
		if($data['list']){
			foreach($data['list'] as $key=>$row){
				$commentList[$key]=array(
						'comment_id'=>$row['comment_id'],
						'uid'=>$row['uid'],
						'nickname'=>$row['nickname'],
						'rank'=>$row['rank'],
						'avatar'=>$row['pic'],
						'content'=>$row['content'],
						'create_time'=>$row['create_time']);
				isset($row['reply'])&&$commentList[$key]['reply']=array('uid'=>$row['reply']['uid'],'nickname'=>$row['reply']['nickname'],'rank'=>$row['reply']['rank']);
			}
		}
		$this->jsonpReturn('200',56,'success',array('total'=>$data['count'],'list'=>$commentList));
	}
	
	public function comment(){
		$tid=isset($this->params['tid'])?$this->params['tid']:0;
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$type=isset($this->params['type'])?$this->params['type']:'';
		$content=isset($this->params['content'])?$this->params['content']:'';
		$reply_id=isset($this->params['reply_id'])?$this->params['reply_id']:0;
		if($tid<=0||empty($type)||empty($content)||$uid<=0){
			$this->jsonpReturn('-13',57, Yii::t('api','illegal params'));
		}
		$commentService=new CommentService();
		$flag=$commentService->comment($tid, $uid, $content,$type,$reply_id);
		if($flag<=0){
			$msg=$commentService->getNotice();
			$this->jsonpReturn('-571',57,array_pop($msg));
		}
		$this->jsonpReturn('200',57,'success');
	}
	
	public function deleteComment(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$cid=isset($this->params['cid'])?$this->params['cid']:0;
		if($uid<=0|$cid<=0){
			$this->jsonpReturn('-13',58, Yii::t('api','illegal params'));
		}
		$doteyService = new DoteyService();
		$dotey = $doteyService->getDoteysInUids(array($dotey_uid));
		$commentService=new CommentService();
		if($dotey){
			$comment=$commentService->getCommnts($cid);
			if($comment){
				$dynamicService=new DynamicService();
				$dynamic=$dynamicService->getDynamic($comment[$cid]['target_id']);
				if($dynamic[$comment[$cid]['target_id']]['uid']==$uid){
					$this->jsonpReturn('-582',58,'主播只能删除自己的评论');
				}
			}
			$flag=$commentService->deleteCommentByDotey($uid,$cid);
		}else{
			$flag=$commentService->deleteCommentByUser($uid,$cid);
		}
		if($flag<=0){
			$msg=$commentService->getNotice();
			$this->jsonpReturn('-582',58,array_pop($msg));
		}
		$this->jsonpReturn('200',58,'success');
	}
	
	public function getUserAlbum(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$pagesize=isset($this->params['pagesize'])?$this->params['pagesize']:$this->pagesize;
		$page=isset($this->params['page'])?$this->params['page']:$this->page;
		if($uid<=0){
			$this->jsonpReturn('-13',59, Yii::t('api','illegal params'));
		}
		if(isset($pagesize)){
			if(!is_numeric($pagesize)||$pagesize<=0||strlen($pagesize)>10){
				$this->jsonpReturn('-591',59,Yii::t('api','illegal pagesize'));
			}
		}
		
		if(isset($page)){
			if(!is_numeric($page)||$page<=0||strlen($page)>10){
				$this->jsonpReturn('-592',59,Yii::t('api','illegal page'));
			}
		}
		$albumService=new AlbumService();
		$data=$albumService->getAlbumByUser($uid, $page,$pagesize);
		$albumList=array();
		if($data['list']){
			foreach($data['list'] as $row){
				$albumList[]=array(
						'pic_id'=>$row['photo_id'],
						'uid'=>$row['uid'],
						'org_image'=>$row['image'],
						'thumb_image'=>$row['thumb']
					);
			}
		}
		$this->jsonpReturn('200',59,'success',array('total'=>$data['count'],'list'=>$albumList));
	}
	
	public function getBigPic(){
		$pic_id=isset($this->params['pic_id'])?$this->params['pic_id']:0;
		if($pic_id<=0){
			$this->jsonpReturn('-13',60, Yii::t('api','illegal params'));
		}
		$albumService=new AlbumService();
		$data=$albumService->getPhoto($pic_id);
		if(!$data){
			$this->jsonpReturn('-601',60,'照片不存在');
		}
		$bigPic=$data['image'];
		$this->jsonpReturn('200',60,'success',array('bigPic'=>$bigPic));
	}
	
	public function uploadAlbum(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		if($uid<=0){
			$this->jsonpReturn('-13',61, Yii::t('api','illegal params'));
		}
		$albumService=new AlbumService();
		if(!$albumService->uploadAlbum($uid,'photo')){
			$error=$albumService->getNotice();
			$error=array_pop($error);
			$this->jsonpReturn('-611',61,$error);
		}
		$this->jsonpReturn('200',61,'success');
	}
	
	
	public function deleteAlbum(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$photo_id=isset($this->params['photo_id'])?$this->params['photo_id']:0;
		if($uid<=0||$photo_id<=0){
			$this->jsonpReturn('-13',62, Yii::t('api','illegal params'));
		}
		$albumService=new AlbumService();
		$flag=$albumService->delUserPhoto($uid,$photo_id);
		if(!$flag){
			$this->jsonpReturn('-621',62,'用户照片删除失败');
		}
		$this->jsonpReturn('200',62,'success');
	}
	
	public function modifyGender(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$gender=isset($this->params['gender'])?$this->params['gender']:0;
		if($uid<=0){
			$this->jsonpReturn('-16',63,Yii::t('api','params not empty'));
		}
		if(!in_array($gender,array(0,1,2))){
			$this->jsonpReturn('-631',63,'性别参数错误');
		}
		$userService=new UserService();
		$userExtend['uid']=$uid;
		$userExtend['gender']=$gender;
		$res = $userService->saveUserExtend($userExtend);
		if(!$res){
			$this->jsonpReturn('-632',63,'性别修改失败');
		}
		$this->jsonpReturn('200',63,'success');
	}
	
	
	//19pay接口获取订单信息
	public function getpay19Order(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$total_fee=isset($this->params['total_fee'])?$this->params['total_fee']:0;
		$card_no=isset($this->params['card_no'])?$this->params['card_no']:'';
		$card_pwd=isset($this->params['card_pwd'])?$this->params['card_pwd']:'';
		if($uid<=0||$total_fee<=0||(!is_float($total_fee)&&!is_numeric($total_fee))||empty($card_no)||empty($card_pwd)){
			$this->jsonpReturn('-13',64, Yii::t('api','illegal params'));
		}
		$order=array(
			'uid'=>$uid,
			'total_fee'=>$total_fee,
			'card_no'=>$card_no,
			'card_pwd'=>$card_pwd,	
			'appsecret'=>$this->payAppsecret,
		);
		$sign=$this->sign($order);
		unset($order['appsecret']);
		$order['sign']=$sign;
		foreach($order as $key => $value) {
			if($key!='key')
				$params[] = $key . '=' . $value;
		}
		$postData = implode('&', $params);
		$ch = curl_init($this->pay19OrderInfoUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		$response = curl_exec($ch);
		$errno    = curl_errno($ch);
		$errmsg   = curl_error($ch);
		curl_close($ch);
		if($errno){
			$this->jsonpReturn('-641',64,$errmsg);
		}
		$response=json_decode($response,true);
		if($response['flag']==1){
			$this->jsonpReturn('200',64,'success',array('orderid'=>$response['orderid']));
		}else{
			$this->jsonpReturn('-642',64,$response['message']);
		}
	}
	
	//财付通接口获取订单信息
	public function gettenpayOrder(){
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$total_fee=isset($this->params['total_fee'])?$this->params['total_fee']:0;
		if($uid<=0||$total_fee<=0||(!is_float($total_fee)&&!is_numeric($total_fee))){
			$this->jsonpReturn('-13',65, Yii::t('api','illegal params'));
		}
		$order=array(
			'uid'=>$uid,
			'total_fee'=>$total_fee,
			'appsecret'=>$this->payAppsecret,
		);
		$sign=$this->sign($order);
		unset($order['appsecret']);
		$order['sign']=$sign;
		foreach($order as $key => $value) {
			if($key!='key')
				$params[] = $key . '=' . $value;
		}
		$postData = implode('&', $params);
		$ch = curl_init($this->tenpayOrderInfoUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		$response = curl_exec($ch);
		$errno    = curl_errno($ch);
		$errmsg   = curl_error($ch);
		curl_close($ch);
		if($errno){
			$this->jsonpReturn('-651',65,$errmsg);
		}
		$response=json_decode($response,true);
		if($response['flag']==1){
			$this->jsonpReturn('200',65,'success',array('orderid'=>$response['orderid'],'url'=>$response['url']));
		}else{
			$this->jsonpReturn('-652',65,'财付通订单生成异常');
		}
	}
	
	public function getArchivesUser(){
		$archives_id=isset($this->params['archives_id'])?$this->params['archives_id']:0;
		$pagesize=isset($this->params['pagesize'])?$this->params['pagesize']:$this->pagesize;
		$page=isset($this->params['page'])?$this->params['page']:0;
		if($archives_id<=0){
			$this->jsonpReturn('-13',66, Yii::t('api','illegal params'));
		}
		if(isset($pagesize)){
			if(!is_numeric($pagesize)||$pagesize<=0||strlen($pagesize)>10){
				$this->jsonpReturn('-661',66,Yii::t('api','illegal pagesize'));
			}
		}
		
		if(isset($page)){
			if(!is_numeric($page)||$page<0||strlen($page)>10){
				$this->jsonpReturn('-662',66,Yii::t('api','illegal page'));
			}
		}
		$userListService=new UserListService();
		$userList=$userListService->getUserList($archives_id,$pagesize,$page);
		$this->jsonpReturn('200',66,'success',$userList);
	}
	
	public function getDoteySong(){
		$dotey_id=isset($this->params['dotey_id'])?$this->params['dotey_id']:0;
		$pagesize=isset($this->params['pagesize'])?$this->params['pagesize']:$this->pagesize;
		$page=isset($this->params['page'])?$this->params['page']:0;
		if($dotey_id<=0){
			$this->jsonpReturn('-13',67, Yii::t('api','illegal params'));
		}
		$doteySongService=new DoteySongService();
		$song=$doteySongService->getDoteySongByDoteyIdLimit($dotey_id,$page,$pagesize);
		$songList=array();
		foreach($song['list'] as $key=>$row){
			$songList[$key]['song_id']=$row['song_id'];
			$songList[$key]['name']=$row['name'];
			$songList[$key]['pipiegg']=$row['pipiegg'];
			$songList[$key]['singer']=$row['singer'];
			$songList[$key]['create_time']=$row['create_time'];
		}
		$song=array('total'=>$song['count'],'list'=>$songList);
		$this->jsonpReturn('200',67,'success',$song);
	}
	
	public function demandSong(){
		$song_id=isset($this->params['song_id'])?$this->params['song_id']:0;
		$archives_id=isset($this->params['archives_id'])?$this->params['archives_id']:0;
		$dotey_id=isset($this->params['dotey_id'])?$this->params['dotey_id']:0;
		$uid=isset($this->params['uid'])?$this->params['uid']:0;
		$song_name=isset($this->params['song_name'])?$this->params['song_name']:'';
		$song_singer=isset($this->params['song_singer'])?$this->params['song_singer']:'';
		if($uid<=0||$archives_id<=0||$dotey_id<=0){
			$this->jsonpReturn('-13',68, Yii::t('api','illegal params'));
		}
		if($song_id<=0&&($song_name==''||$song_singer=='')){
			$this->jsonpReturn('-13',68, Yii::t('api','illegal params'));
		}
		if(!$this->isLogin($uid)){
			$this->jsonpReturn('-17',68,Yii::t('user','You are not logged'));
		}
		$forbidenService=new ForbidenService();
		if($forbidenService->getArchivesKickout($archives_id,$uid)){
			$this->jsonpReturn('-681',68,Yii::t('archives','You have been kicked out of archives'));
		}
		$doteySongService=new DoteySongService();
		$allow=$doteySongService->getArchivesAllowSong($archives_id);
		if($allow==2){
			$this->jsonpReturn('-682',68,Yii::t('doteySong','Archives forbid demand song'));
		}
		$songs=array();
		if($song_id>0){
			$songs['song_id']=$song_id;
			$song=$doteySongService->getDoteySongBySongId($songs['song_id']);
			$songs['name']=$song['name'];
		}
		if($song_name && $song_singer){
			$songs['name']=$song_name;
			$songs['singer']=$song_singer;
		}
		$result=$doteySongService->demandSong($uid,$dotey_id,$archives_id,$songs);
		if($result<=0){
			$message=$doteySongService->getErrors();
			$this->jsonpReturn('-683',68,array_pop($message));
		}
		$this->jsonpReturn('200',68,'success');
	}
	
	public function getCategory(){
		//主播等级人数
		$category[]=array('name'=>'主播等级',
						'type'=>'rank',
						'sub'=>array(array('cat_id'=>1,'name'=>'皇冠主播'),
								array('cat_id'=>2,'name'=>'蓝钻主播'),
								array('cat_id'=>3,'name'=>'红心主播'))
				);
		
		//主播印象
		$tags = DoteyTagsService::getInstance()->getAllTags();
		$sub_tags=array();
		foreach($tags as $row){
			$sub_tags[]=array('cat_id'=>$row['tag_id'],'name'=>$row['tag_name']);
		}
		$category[]=array('name'=>'主播印象','type'=>'tag','sub'=>$sub_tags);
		$this->jsonpReturn('200',69,'success',$category);
	}
	
	public function actionTest(){
		//$this->getUserIsAttention();
		//$this->getAttentions();
		//$this->getUserInfo();
		//$this->addAttention();
		//$this->removeAttention();
		$this->getArchivesList();
		//$this->saveSuggest();
	}
	
	protected function check_show_nickname($uid,$nickname){
		$guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8|\xE2\x80\xAD|\xE2\x80\xAE|\xE2\x80\xAA';
		$guestexp.='|\xE2\x80\xAB|\xE2\x80\xAC|\xE2\x80\xAF|\xEF\xA3\xB5|\xE2\x80|\xE2\x81|\x2A|\xEE\xA0|\xC2\xAD|\x7F|\xE3\x80\x80';
		$guestexp.='|\x1E\x1F|\x1E\x1E|\x1F\x1F';
		$patrnstr="/\s+|\{|\}|\||\;|\:|\'|^c:\\con\\con|(&\d{2})|(%\d{2})|[%&,\*\"\s\<\>\|\\\[\]\/\?\^\+`~]|".$guestexp."/is";
		$len=mb_strlen($nickname,'UTF8');
	
		if($len > 15 || $len < 1 ){
			return '请输入长度为2到15位的昵称';
		}
	
		if(@preg_match($patrnstr, $nickname)){
			return '昵称不能包含特殊字符';
		}
	
		$badunicodes=array(
				array(0x82,0x06),
				array(0x82,0x07),
				array(0x82,0x05),
				array(0x82,0x04),
				array(0x82,0x03),
				array(0x82,0x36),
				array(0x82,0x37),
				array(0x82,0x38),
				array(0x83,0x02),
				array(0x83,0x03),
				array(0x82,0x99),
				array(0x82,0x98),
				array(0x83,0x01),
				array(0x83,0x00)
		);
	
		$unicode_nickname = iconv('UTF-8', 'UCS-2', $nickname);
		$len = strlen($unicode_nickname);
		for ($i = 0; $i < $len - 1; $i = $i + 2){
			$c = $unicode_nickname[$i];
			$c2 = $unicode_nickname[$i + 1];
			if (ord($c) > 0){    // 两个字节的文字
				$temp_c=base_convert(ord($c), 10, 16);
				$temp_c2=base_convert(ord($c2), 10, 16);
	
				foreach ($badunicodes as $unicoderow){
					if($unicoderow[0]==$temp_c && $unicoderow[1]==$temp_c2){
						return '昵称不能包含特殊字符';
					}
				}
			}else{
				$temp_c= base_convert(ord($c2), 10, 16);
				if($temp_c==0x30 || $temp_c==0x31)
					return '昵称不能包含特殊字符';
			}
		}
		// @todo bad_word
		$wordService = new WordService();
		$badWord = $wordService->getAllChatWordList();
		foreach($badWord as $k=>$v){
			$_patrnstr="/{$v['name']}/is";
			if(@preg_match($_patrnstr, $nickname))
				return '昵称中包含非法字符';
		}
		// @todo only_nickname
		$userService = new UserService();
		$res = $userService->getUserBasicByNickNames(array($nickname));
		if(isset($res[$nickname])){
			return '昵称已存在';
		}
	
		return null;
	}
	
	protected  function checkins($uid, $type, $en_name, $num = 1, $time =0, $checkinAll = false){
		$userGiftService = new UserGiftService();
		$isCheckin = $userGiftService->getIsCheckin($uid,$type, $time);
		if(!$isCheckin || $checkinAll){
			$giftService = new GiftService();
			$roseInfo = $giftService->getGiftList(array('en_name'=>$en_name));
			$roseKey = array_keys($roseInfo);
			$roseId = $roseKey[0];
			$gift = array('uid'=>$uid,'gift_id'=>$roseId,'num'=>$num);
			$giftBagService = new GiftBagService();
			$userService=new UserService();
			$userBasice=$userService->getUserBasicByUids(array($uid));
			$records['info']=serialize(array('uid'=>$uid,'nickname'=>$userBasice[$uid],'gift_id'=>$roseId,'gift_name'=>$roseInfo[$roseId]['zh_name'],'num'=>$num,'remark'=>'签到赠送'));
			$records['source']=3;
			$addRose = $giftBagService->saveUserGiftBagByUid($gift, $records);
			if($addRose) {
				$giftInfo = $roseInfo[$roseId]['zh_name'].'*'.$num;
				$checkin = array(
					'uid'=>$uid,
					'type'=>$type,
					'target_id'=>$roseId,
					'num'=>$num,
					'info'=>$giftInfo,
					'pipiegg'=>($roseInfo[$roseId]['pipiegg'] * $num),
					'create_time'=>$time,
				);
				$addCheckin = $userGiftService->saveCheckinRecord($checkin);
				if($addCheckin){
					return array('result'=>1,'info'=>array('gift_id'=>(int)$roseId,'zh_name'=>$roseInfo[$roseId]['zh_name'],'en_name'=>$roseInfo[$roseId]['en_name'],'gift_num'=>(int)$num,'picture'=>$giftService->getGiftUrl($roseInfo[$roseId]['image'])));
				}
			}else{
				return array('result'=>0,'info'=>'签到失败, 请重新签到');
			}
		}else{
			return array('result'=>2,'info'=>'已经签到过');
		}
		return array('result'=>0,'info'=>'错误');
	}
	
	protected function checkPage($page){
		if(!is_numeric($page)||$page<=0||strlen($page)>10){
			return false;
		}
		return true;
	}
	
	

	/**
	 * 验证签名
	 * @param string $sign  签名
	 * @param array $params 待签名数据
	 * @return boolen
	 */
	protected  function checkSign(){
		if($this->checkSign==false){
			return true;
		}
		$targetSign=isset($this->params['sign'])?$this->params['sign']:'';
		$appService=new AppService();
		$appData=$appService->getAppInfoByEname($this->params['appkey']);
		$this->params['appsecret']=$appData['app_secret'];
		if($appData['app_status']==0){
			$code='-15';
			$msg='The account is disabled';
			$this->jsonpReturn($code,1,$msg);
		}
		$sign=self::sign($this->params);
		$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
		error_log(date("Y-m-d H:i:s")."客户端请求地址：".$url."\n\r",3,$this->logPath);
		error_log(date("Y-m-d H:i:s")."客户端签名结果：".$targetSign.",服务端签名结果：".$sign."\n\r",3,$this->logPath);
		if(!in_array($this->params['action'],$this->action)){
			$code='-14';
			$msg='illegal action';
			$this->jsonpReturn($code,1,$msg);
			return false;
		}else if(!isset($this->params['appkey'])||empty($appData)){
			$code='-11';
			$msg='illegal appkey';
			$this->jsonpReturn($code,1,$msg);
		}else if(!$this->params['timestamp']||time()-$this->params['timestamp']>$this->validTime){
			$code='-12';
			$msg='illegal timestamp';
			$this->jsonpReturn($code,1,$msg);
		}elseif(!isset($this->params['session_id'])){
			$code='-13';
			$msg='illegal params';
			$this->jsonpReturn($code,1,$msg);
		}else if($targetSign!==$sign){
			$code='-10';
			$msg='illegal sign';
			$this->jsonpReturn($code,1,$msg);
		}else{
			return true;
		}

	}

	protected  function sign(array $params){
		$sign=array();
		unset($params['sign']);
		unset($params['PHPSESSID']);
		ksort($params);
		
		foreach($params as $key=>$row){
			$sign[]=$key.'='.$row;
		}
		error_log(date("Y-m-d H:i:s")."服务端签名串：".urlencode(implode('&',$sign))."\n\r",3,$this->logPath);
		return md5(urlencode(implode('&',$sign)));
	}


	//返回jsonp格式数据
	protected function jsonpReturn($code,$status,$msg,$data=array()){
		$result  =  array();
		$result['code']  =  $code;
		if($status>1){
			$result['status']  =  $status;
		}
		$result['msg'] =  $msg;
		if(!empty($data)){
			$result['data'] = $data;
		}
		header("Content-Type:text/html; charset=utf-8");
		$format=isset($this->params['format'])?$this->params['format']:'json';
		error_log(date("Y-m-d H:i:s")."返回结果：".json_encode($result)."\n\r",3,$this->logPath);
		if($format=='json'){
			exit(json_encode($result));
		}elseif($format=='jsonp'){
			$callback=empty($this->params['callback'])?'callback':$this->params['callback'];
			exit($callback.'('.json_encode($result).')');
		}
		
	}
	
	private function sendToAPNS($deviceToken,$message,$title,$badge,$sound='default'){
		$body['userinfo']=$message;
		$body['aps'] = array("alert" =>array('action-loc-key'=>'Open','body'=>$title));
		$body['aps']['badge'] = $badge;
		$body['aps']['sound'] = $sound;
		$body = json_encode($body);
		$streamContext = stream_context_create();
		$certPath=dirname(dirname(dirname(dirname(__FILE__)))).DIR_SEP.'statics'.DIR_SEP.'cert'.DIR_SEP.$this->sslPem;
		stream_context_set_option($streamContext, 'ssl', 'local_cert',$certPath);
		stream_context_set_option($streamContext, 'ssl', 'passphrase', $this->passPhrase);
		$this->apnsConnection = stream_socket_client('ssl://'.$this->apnsHost.':'.$this->apnsPort, $err, $errstr, 60,STREAM_CLIENT_CONNECT, $streamContext);
		if($this->apnsConnection == false){
			return $this->setError("Failed to connect {$error} {$errorString}\n",false);
		}
		$this->sendNotification($deviceToken, $body);
		return true;
	}
	
	private function sendNotification($deviceToken, $message){
		$apnsMessage =chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($message)) . $message;
		fwrite($this->apnsConnection, $apnsMessage);
		fclose($this->apnsConnection);
	}
	
	private function isLogin($uid){
		if($uid<=0) return false;
		$sessionRedisModel=new SessionRedisModel();
		$orgSessionId=$sessionRedisModel->getMobileUserSessionId($uid);
		if($orgSessionId!==$this->sessionId){
			return false;
		}
		return true;
	}
	
	private function getorderid(){
		list($msec,$sec)=explode(" ",microtime());
		$msec=substr($msec,2,6);
		$msec=empty($msec)?mt_rand(100000,999999):$msec;
		$sec=empty($sec)?date("ymdHis"):date("ymdHis",$sec);
		$orderid=$sec.$msec.mt_rand(1,9999);
		return $orderid;
	}
	
	private function thumbImg($orgImg,$maxWidth,$maxHeight){
		list($width, $height) = getimagesize($orgImg);
		if($maxWidth&&$width<$maxWidth){
			$width_scale=$maxWidth/$width;
			$with_tag=true;
		}
		if($maxWidth&&$width>=$maxWidth){
			$width_scale=$maxWidth/$width;
			$with_tag=true;
		}
		if($maxHeight&&$height<$maxHeight){
			$heigth_scale=$maxHeight/$height;
			$height_tag=true;
		}
		if($maxHeight&&$height>=$maxHeight){
			$heigth_scale=$maxHeight/$height;
			$height_tag=true;
		}
		if($with_tag&& $height_tag){
			if($width_scale<$heigth_scale){
				$scale=$width_scale;
			}else{
				$scale=$heigth_scale;
			}
		}
		if($with_tag&&!$height_tag){
			$scale=$width_scale;
		}
		if($height_tag&&!$with_tag){
			$scale=$heigth_scale;
		}
		$newWidth=number_format($width*$scale,2);
		$newHeigth=number_format($height*$scale,2);
		$image_p = imagecreatetruecolor($newWidth, $newHeigth);
		$white = imagecolorallocate($image_p, 255, 255, 255);
		imagefill($image_p, 0, 0, $white);
		$image = imagecreatefromjpeg($orgImg);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidth, $newHeigth, $width, $height);
		imagejpeg($image_p, $orgImg, 100);
	}

}

?>