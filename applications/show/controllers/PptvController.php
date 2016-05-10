<?php
define('USER_REGISTER_COOKIE_VTIME',3600*3);

/**
 * pptv 相关操作
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PublicController.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package 
 */
class PptvController extends PipiController {
	
	const tm_check=30;
	
	const gid='pipi';
	
	const platform = 'pptv';
	
	protected $mainUrl = '';
	
	protected $pptvUrl = '';
	/**
	 * 每个请求都经过签名认证
	 * @see CController::beforeAction()
	 */
	public function beforeAction($action){
		parent::beforeAction($action);
		$this->mainUrl = Yii::app()->params['pptv']['main_url'];
		$this->pptvUrl = Yii::app()->params['pptv']['pptv_url'];
		return true;
	}
	
	public function actionPptvLogin(){
			
		$pptvUid=Yii::app()->request->getParam('username');
		$pptvTm=Yii::app()->request->getParam('tm');
		$pptvToken=Yii::app()->request->getParam('token');
		
		$localToken=md5(self::gid.$pptvUid.Yii::app()->params['pptv']['app_key'].$pptvTm);
		$timeDiff=time()-intval(trim($pptvTm));
		
		if($localToken==$pptvToken && $timeDiff<self::tm_check){
			$postData=array(
				'uid'=>$pptvUid,
				'tm'=>$pptvTm,
				//'goto'=>urlencode('http://pptv.pipi.cn/10874265'),
			);
			$postData['token']=$this->buildPptvToken($pptvUid,$pptvTm);
			$request=array();
			foreach($postData as $key=>$value){
				$request[]="{$key}={$value}";
			}
			$requestStr=implode('&',$request);
		
			$url= $this->mainUrl.'/index.php?r=pptv/login&'.$requestStr;
			header('Location: '.$url);
		}else{
			echo "非正常登录";
		}
	}
	/**
	 * pptv用户登录
	 * @edit guoshaobo 修改为本页面跳转
	 */
	public function actionLogin(){
		header('P3P: CP="CAO PSA OUR"');
		$pptvUid=Yii::app()->request->getParam('uid');
		$pptvNickname=urldecode(Yii::app()->request->getParam('nickname'));
		$pptvGoto=Yii::app()->request->getParam('goto');
		$pptvTm=Yii::app()->request->getParam('tm');
		$pptvToken=Yii::app()->request->getParam('token');
		$return = Yii::app()->request->getParam('return');
		
		$returnUrl = Yii::app()->request->getUrlReferrer();
		$returnUrl = $returnUrl ? $returnUrl : Yii::app()->request->getHostInfo();
		if(!$pptvGoto){
			$pptvGoto = $returnUrl;
		}
		if(!$this->validteToken($pptvToken,$pptvUid,$pptvTm)){
			if(!$return){
// 				$this->redirect($this->getTargetHref($pptvGoto,false,true));
				$this->redirect($pptvGoto);
				die();
			}else{
				die(json_encode(array('state'=>0,'message'=>'token error')));
			}
		}
		$platform = 'pptv';
		Yii::app()->session['open_referer'] = $returnUrl;
		if(!$pptvNickname){
			$pptvNickname = $pptvUid;
		}
		
		$userService = new UserService();
		$userOauth = $userService->getUserOauthByOpenFlatform(self::platform,$pptvUid);
		$password = self::platform.'_'.$pptvUid.Yii::app()->params['app_key'];
		if($userOauth){
			$userBasic = $userService->getUserBasicByUids(array($userOauth['uid']));
			$userBasic = $userBasic[$userOauth['uid']];
			if(empty($userBasic)){
// 				$this->redirect($this->getTargetHref('index.php?r=public/error',false,true));
				$this->redirect($this->createUrl('public/error'));
				die();
			}
			$identify = new PipiUserIdentity($userBasic['username'],$password);
			$identify->openUserInfo = $userOauth;
 		   	if($identify->authenticate()){
 		   		Yii::app()->user->login($identify,USER_REGISTER_COOKIE_VTIME);
 		   		$referer = Yii::app()->session['open_referer'];
				unset(Yii::app()->session['open_referer']);
				if(!$return){
// 					$this->redirect($this->getTargetHref($pptvGoto,false,true));
					$this->redirect($pptvGoto);
					die();
				}else{
					die(json_encode(array('state'=>1,'message'=>'登录成功')));
				}
 		   	}else{
 		   		if(!$return){
//  		   			$this->redirect($this->getTargetHref('index.php?r=public/error',false,true));
 		   			$this->redirect($this->createUrl('public/error'));
 		   		}else{
 		   			die(json_encode(array('state'=>0,'message'=>$identify->errorMessage)));
 		   		}
 		   	}
 		   	die();
		}
		
		//新用户注册
		$userBasic['uid'] = $userService->getNextUid();
		$userBasic['password'] = $password;
		$userBasic['user_type'] = 1;
		$userBasic['reg_source'] = $userService->getUserRegEnSource(self::platform);
		if($pptvNickname &&  $userService->getUserBasicByNickNames(array($pptvNickname))){
			$userBasic['nickname'] = self::platform.'_'.$userBasic['uid'];
		}else{
			$userBasic['nickname'] = $pptvNickname;
		}
		if($pptvUid &&  $userService->getVadidatorUser($pptvUid,USER_LOGIN_USERNAME)){
			$userBasic['username'] = self::platform.'_'.$userBasic['uid'];
		}else{
			$userBasic['username'] = $pptvUid;
		}
		$userService->saveUserBasic($userBasic);
		
		if(!$userService->getNotice()){
			$oauth['uid'] = $userBasic['uid'];
			$oauth['openid'] = $pptvUid;
			$oauth['open_platform'] = self::platform;
			$oauth['onickname'] = $pptvNickname;
			$userService->saveUserOauth($oauth);
			
			$consumeService = new ConsumeService();
			$consumeService->saveUserConsumeAttribute(array('uid'=>$userBasic['uid'],'rank'=>0));
		  	$identify = new PipiUserIdentity($userBasic['username'],$userBasic['password']);
 		   	if($identify->authenticate()){
				Yii::app()->user->login($identify,USER_REGISTER_COOKIE_VTIME);
				//注册推广来源
				$reg['sign'] = 'pptv';
				$reg['referer'] = $pptvGoto;//注册页面的，前一个页面，由cookie维护
				$reg['curl'] = $returnUrl;//这里是Ajax注册，所以注册的当前页面就是用户的前一页面
				$reg['access_time'] = time();
				$reg['uid'] = $userBasic['uid'];
				$partnerService = new PartnerService();
				$partnerService->saveRegLog($reg);
					
				$referer = Yii::app()->session['open_referer'];
				unset(Yii::app()->session['open_referer']);
				if(!$return){
// 					$this->redirect($this->getTargetHref($pptvGoto,false,true));
					$this->redirect($pptvGoto);
					die();
				}else{
					die(json_encode(array('state'=>1,'message'=>'注册成功')));
				}
 		   }else{
 		  		if(!$return){
// 					$this->redirect($this->getTargetHref('index.php?r=public/error',false,true));
					$this->redirect($this->createUrl('public/error'));
					die();
				}else{
					die(json_encode(array('state'=>0,'message'=>$identify->errorMessage)));
				}
 		   }
 		}else{
 			if(!$return){
// 				$this->redirect($this->getTargetHref('index.php?r=public/error',false,true));
				$this->redirect($this->createUrl('public/error'));
				die();
			}else{
				die(json_encode(array('state'=>0,'message'=>$userService->getNotice())));
			}
 			
 		}
	}
	
	/**
	 * 注销登录
	 * 取消token验证
	 */
	public function actionLogout(){
		header('P3P: CP="CAO PSA OUR"');
		if(!Yii::app()->user->isGuest)
			Yii::app()->user->logout();
			
		$returnUrl = Yii::app()->request->getUrlReferrer();
		$returnUrl = $returnUrl ? $returnUrl : Yii::app()->user->returnUrl;
		$this->redirect($returnUrl); //*/
		/*
		$pptvUid=Yii::app()->request->getParam('uid');
		$pptvTm=Yii::app()->request->getParam('tm');
		$pptvToken=Yii::app()->request->getParam('token');
		$return=Yii::app()->request->getParam('return');
		$returnUrl = Yii::app()->request->getUrlReferrer();
		$returnUrl = $returnUrl ? $returnUrl : Yii::app()->request->getHostInfo();
		if(!$pptvGoto){
			$pptvGoto = $returnUrl;
		}
		if(!$this->validteToken($pptvToken,$pptvUid,$pptvTm)){
			if(!$return){
				$this->redirect($pptvGoto);
				die();
			}else{
				$result = array(
					'state'=>0,
					'message'=>'token is error',
					'result'=>array(),
				);
				die(json_encode($result));
			}
		}
		
		if(!Yii::app()->user->isGuest)
			Yii::app()->user->logout();
			
		if(!$return){
			$this->redirect($pptvGoto);
			die();
		}else{
			$result = array(
				'state'=>1,
				'message'=>'退出成功',
				'result'=>array(),
			);
			die(json_encode($result));
		}//*/
	}
	
	
	public function actionCharge(){
		
		$pptvUid=	Yii::app()->request->getParam('uid');
		$pptvTm= Yii::app()->request->getParam('tm');
		$pptvToken=	Yii::app()->request->getParam('token');
		$pptvPage=Yii::app()->request->getParam('page');
		$pptvPerpage=Yii::app()->request->getParam('perpage');
		
		if($pptvPage <= 0){
			$pptvPage = 1;
		}
		if(!$this->validteToken($pptvToken,$pptvUid,$pptvTm)){
			$result = array(
				'state'=>0,
				'message'=>'token is error',
				'result'=>array(),
			);
			die(json_encode($result));
		}
		
		$userService = new UserService();
		$userOauth = $userService->getUserOauthByOpenFlatform(self::platform,$pptvUid);
		if(empty($userOauth) || $userOauth['uid']<0){
			$result = array(
				'state'=>0,
				'message'=>'不存在此用户',
				'result'=>array(),
			);
			die(json_encode($result));
		}
		
		$weiboService = new WeiboService();
		$condition['fans_uid'] = $userOauth['uid'];
		$condition['isCount'] = true;
		$condition['limit'] = $pptvPerpage;
		$condition['offset'] = ($pptvPage-1)*$pptvPage;
		$weiboService = new WeiboService();
		list($attentions,$count) = $weiboService->getDoteyAttentionsByCondition($condition);
		if(empty($attentions)){
			$result = array(
				'state'=>0,
				'message'=>'您还没有关注的主播',
				'result'=>array(),
			);
			die(json_encode($result));
		}
		$uids = array_keys($weiboService->buildDataByIndex($attentions,'uid'));
		$archivesService = new ArchivesService();
		$archives = $archivesService->getArchivesByUids($uids);
		if(empty($archives)){
			$result = array(
				'state'=>0,
				'message'=>'没有正在直播的数据',
				'result'=>array(),
			);
			die(json_encode($result));
		}
		
		$pageTotal=ceil($count/$pptvPerpage);
		
		
		$consumeService = new ConsumeService();
		$otherReidsModel = new OtherRedisModel();
		$doteyService = new DoteyService();
		$users = $userService->getUserBasicByUids($uids);
		$userExtends = $userService->getUserExtendByUids($uids);
		$userConsumes = $consumeService->getConsumesByUids($uids);
		$i = 0;
		$roomlist = array();
		foreach($archives as $archive){
			if(!isset($archive['live_record']) || !$archive['live_record']){
				continue;
			}
			$archives_id = $archive['archives_id'];
			$userOnline = $otherReidsModel->getUserList($archives_id);
			$uid = $archive['uid'];
			$roomlist[$i]['roomid'] = $uid;
			$roomlist[$i]['room_url'] = $this->pptvUrl.$this->mainUrl.'/'.$uid;
			$roomlist[$i]['room_name'] = $archive['title'];
			$roomlist[$i]['room_pic'] = $doteyService->getDoteyUpload($archive['uid'],'small','display');
			if($archive['live_record']['status'] == '1'){
				$roomlist[$i]['living_time']=$archive['live_record']['live_time'];
			}else{
				$roomlist[$i]['living_time']=0;
			}
			if(isset($users[$uid])){
				$roomlist[$i]['anchor_name'] = $users[$uid]['nickname'];
			}else{
				$roomlist[$i]['anchor_name'] = '';
			}
			if(isset($userConsumes[$uid])){
				$roomlist[$i]['level'] = $userConsumes[$uid]['dotey_rank'];
			}else{
				$roomlist[$i]['level'] = 0;
			}
			if(isset($userExtends[$uid])){
				$roomlist[$i]['Info'] = $userExtends[$uid]['description'];
				$roomlist[$i]['ext']['birthday'] = $userExtends[$uid]['birthday'];
				$roomlist[$i]['ext']['horoscope'] = '';
			}else{
				$roomlist[$i]['Info'] = '';
				$roomlist[$i]['ext']['birthday'] = '';
				$roomlist[$i]['ext']['horoscope'] = '';
			}
			if($userOnline){
				$roomlist[$i]['online'] = $userOnline['total'];
			}else{
				$roomlist[$i]['online'] = '';
			}
			$i++;
		}			
		
		$result=array(
				'state'=>1,
				'pageIndex'=>$pptvPage,
				'pageRecord'=>$count,
				'pageTotal'=>$pageTotal,
				'message'=>'success',
				'result'=>$roomlist,	
		);
	   echo json_encode($result);
	}
	
	
	public function actionRoomlist(){
		
		$pptvLimit=	Yii::app()->request->getParam('limit');
		$pptvTm= Yii::app()->request->getParam('tm');
		$pptvToken=	Yii::app()->request->getParam('token');
		
// 		if(!$this->validteToken($pptvToken,$pptvLimit,$pptvTm)){
// 			$result = array(
// 				'state'=>0,
// 				'message'=>'token is error',
// 				'result'=>array(),
// 			);
// 			die(json_encode($result));
// 		}
		$archivesService = new ArchivesService();
		$archives = $archivesService->getLivingArchives(Yii::app()->user->id,true,false);
		$liveNum=isset($archives['living'])?count($archives['living']):0;
		//如果正在直播的不足，补充待直播数据
		if($liveNum<$pptvLimit){
			$willLiveArchives = $archivesService->getWillLiveArchives(Yii::app()->user->id,true,false);
			$willNum=isset($willLiveArchives['wait'])&&(($pptvLimit-$liveNum)>count($willLiveArchives['wait']))?count($willLiveArchives['wait']):$pptvLimit-$liveNum;
			$willArchives=array_splice($willLiveArchives['wait'], 0,$willNum);
			if(isset($archives['living'])){
				$archives['living']=array_merge($archives['living'],$willArchives);
			}else{
				$archives['living']=$willArchives;
			}
		}
		if(empty($archives['living'])){
			$result = array(
				'state'=>0,
				'message'=>'没有正在直播的数据',
				'result'=>array(),
			);
			die(json_encode($result));
		}
		$userService = new UserService();
		$consumeService = new ConsumeService();
		$otherReidsModel = new OtherRedisModel();
		
		$uids = array_keys($userService->buildDataByIndex($archives['living'],'uid'));
		$users = $userService->getUserBasicByUids($uids);
		$userExtends = $userService->getUserExtendByUids($uids);
		$userConsumes = $consumeService->getConsumesByUids($uids);
		$i = 0;
		$roomlist = array();
		foreach($archives['living'] as $archive){
			$archives_id = $archive['archives_id'];
			$userOnline = $otherReidsModel->getUserList($archives_id);
			$uid = $archive['uid'];
			$roomlist[$i]['roomid'] = $uid;
			$roomlist[$i]['room_url'] = $this->pptvUrl.$this->mainUrl.'/'.$uid;
			$roomlist[$i]['room_name'] = $archive['title'];
			$roomlist[$i]['room_pic'] = $archive['display_small'];
			$roomlist[$i]['living_time']=$archive['live_time'];
			if(isset($users[$uid])){
				$roomlist[$i]['anchor_name'] = $users[$uid]['nickname'];
			}else{
				$roomlist[$i]['anchor_name'] = '';
			}
			if(isset($userConsumes[$uid])){
				$roomlist[$i]['level'] = $userConsumes[$uid]['dotey_rank'];
			}else{
				$roomlist[$i]['level'] = '';
			}
			if(isset($userExtends[$uid])){
				$roomlist[$i]['Info'] = $userExtends[$uid]['description'];
				$roomlist[$i]['ext']['birthday'] = $userExtends[$uid]['birthday'];
				$roomlist[$i]['ext']['horoscope'] = '';
			}else{
				$roomlist[$i]['Info'] = '';
				$roomlist[$i]['ext']['birthday'] = '';
				$roomlist[$i]['ext']['horoscope'] = '';
			}
			if($userOnline){
				$roomlist[$i]['online'] = $userOnline['total'];
			}else{
				$roomlist[$i]['online'] = rand(21,30);
			}
			$i++;
			if($i>$pptvLimit)
				break;
		}			
		
		$result=array(
				'state'=>1,
				'message'=>'success',
				'result'=>$roomlist,	
		);
	
	   echo json_encode($result);
	}
	
	public function actionUserinfo(){
		$pptvUid=Yii::app()->request->getParam('uid');
		$pptvTm=Yii::app()->request->getParam('tm');
		$pptvToken=Yii::app()->request->getParam('token');
		if(!$this->validteToken($pptvToken,$pptvUid,$pptvTm)){
			$result = array(
				'state'=>0,
				'message'=>'token is error',
				'result'=>array(),
			);
			die(json_encode($result));
		}
		
		$userService = new UserService();
		$consumeService = new ConsumeService();
		$userOauth = $userService->getUserOauthByOpenFlatform(self::platform,$pptvUid);
		if($userOauth){
			$uid = $userOauth['uid'];
			$uids = array($uid);
			$users = $userService->getUserBasicByUids($uids);
			$userExtends = $userService->getUserExtendByUids($uids);
			$userConsumes = $consumeService->getConsumesByUids($uids);
			
			$userObject['uid'] = $pptvUid;
			$userObject['pic'] = $userService->getUserAvatar($uid,'small');
			if(isset($users[$uid])){
				$userObject['username'] = $users[$uid]['username'];
				$userObject['nickname'] = $users[$uid]['nickname'];
			}else{
				$userObject['username'] = '';
				$userObject['nickname'] = '';
			}
			if(isset($userConsumes[$uid])){
				$userObject['coin'] = $userConsumes[$uid]['pipiegg'];
				$userObject['level'] = $userConsumes[$uid]['rank'];
			}else{
				$userObject['coin'] = 0;
				$userObject['level'] = 0;
			}
			
			if(isset($userExtends[$uid])){
				$userObject['ext']['horoscope'] = '';
				$userObject['ext']['birthday'] = $userExtends[$uid]['birthday'];
			}else{
				$userObject['ext']['horoscope'] = '';
				$userObject['ext']['birthday'] = '';
			}
			$result = array(
					'state'=>1,
					'message'=>'获取用户信息成功',
					'result'=>$userObject,
			);
			die(json_encode($result));
		}else{
			$result = array(
				'state'=>0,
				'message'=>'不存在该用户的数据',
				'result'=>array(),
			);
			die(json_encode($result));
		}
	}
	/**
	 * 订单支付接口
	 */
	public function actionPay(){
		$pptvUid=Yii::app()->request->getParam('uid');
		$pptvTm=Yii::app()->request->getParam('tm');
		$pptvToken=Yii::app()->request->getParam('token');
		$pptvAmount=Yii::app()->request->getParam('amount');
		$pptvCoin=Yii::app()->request->getParam('coin');
		$pptvOrderid=Yii::app()->request->getParam('orderid');
		$localToken=md5(Yii::app()->params['pptv']['app_key'].$pptvUid.$pptvAmount.$pptvCoin.$pptvOrderid.$pptvTm);
		$timeDiff=time()-intval(trim($pptvTm));
		//检测已授权的openid在乐天的状态
		$userService = new UserService();
		$consumeService = new ConsumeService();
		$userOauth = $userService->getUserOauthByOpenFlatform(self::platform,$pptvUid);
		$uid=$userOauth['uid'];
		$result=array();
		if($localToken==$pptvToken && $timeDiff<self::tm_check && $uid>0){
			$changeRelation = Yii::app()->params->change_relation;
			$toEgg = isset($changeRelation['rmb_to_pipiegg'])?$changeRelation['rmb_to_pipiegg']:1;
			
			//构造充值订单数据
			$data=array();
			$data['ruid']=$uid;
			$data['uid']=$uid;
			$data['currencycode']="RMB";
			$data['money']=$pptvAmount;
			$data['pipiegg']=$pptvAmount*$toEgg;
			$data['rorderid']=$this->getorderid();
			$data['rsource']="PPTV";
			$data['rtime']=time();
			$data['issuccess']=2;			
			$data['sign']=$pptvOrderid;
			$data['summary']="PPTV支付";
			$data['ctime']=time();
			//print_r($data);exit;
			//写入充值记录
			$newOrderid=$consumeService->recharge($data);
			if($newOrderid>0){
				$result=array(
						'state'=>1,
						'message'=>'充值成功',
				);
			}else{
				$result=array(
						'state'=>0,
						'message'=>'充值失败',
				);
			}

		}else{
			$result=array(
					'state'=>0,
					'message'=>'token 错误 ',
			);
		}
		die(json_encode($result));
	}
	/**
	 * 生成pptv token
	 * 
	 * @param int $pptv_uid pptv的用户ID
	 * @param int $pptv_tm  pptv生成的时间缀
	 * return string
	 */
	private function buildPptvToken($pptvUid,$pptvTm){
		return md5(Yii::app()->params['pptv']['app_key'].$pptvUid.$pptvTm);
	}
	/**
	 * 验证Token
	 * 
	 * @param string $orgToken
	 * @param mixed $pptvUid
	 * @param int $pptvTm
	 * @return boolean
	 */
	private function validteToken($orgToken,$pptvUid,$pptvTm){
		if($orgToken==$this->buildPptvToken($pptvUid,$pptvTm) && (time()-intval(trim($pptvTm)))< self::tm_check){
			return true;
		}
		return false;
	}
	

	/**
	 * 根据年月日时间秒、微秒、随机数生成惟一订单号
	 */
	private function getorderid(){
		list($msec,$sec)=explode(" ",microtime());
		$msec=substr($msec,2,6);
		$msec=empty($msec)?mt_rand(100000,999999):$msec;
		$sec=empty($sec)?date("ymdHis"):date("ymdHis",$sec);
		$orderid=$sec.$msec.mt_rand(1,9999);
		return $orderid;
	}
}

?>