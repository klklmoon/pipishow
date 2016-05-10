<?php
define('USER_REGISTER_COOKIE_VTIME',3600*3);
/**
 * 快播_图丽  相关操作
 *
 * the last known user to change this file in the repository  <$LastChangedBy: Guoshaobo $>
 * @author Guoshaobo <guoshaobo@pipi.cn>
 * @version $Id: TuliController.php 8317 2013-03-29 01:19:47Z guoshaobo $
 * @package
 */
class TuliController extends PipiController {
		
	const from = 'tuli';
	
	const op_new_wndow = false;
	
	public $tuliParams;
	
	public function init(){
		parent::init();
		$this->tuliParams = Yii::app()->params->tuli;
	}
	
	public function actionIndex()
	{
		$this->actionCallback();
	}
	
	public function actionCallback()
	{
		header('P3P: CP="CAO PSA OUR"');
		$from = Yii::app()->request->getParam('from');
		if($from != self::from){
			// @todo 判断不是来自图丽的非法URL
		}
		$url = Yii::app()->request->getParam('url');
		$token = Yii::app()->request->getParam('token');
		if(!empty($url)){
			if($token){
				$str = (strrpos($url,'?') ? '&' : '?' );
				$url .= $str . 'token='.$token;
// 				if(!$this->isLogin){	// 不做登录判断, 因为可能会替换用户
					$tuliUser = $this->getUrlInfo($token);
					if(!empty($tuliUser['ok'])){
						$res = $this->login($tuliUser['data'], $url);
						if($res['res']){
							Yii::app()->session['tuli_user_info'] = $tuliUser;
						}else{
							if(isset(Yii::app()->session['tuli_user_info'])){
								unset(Yii::app()->session['tuli_user_info']);
							}
						}
					}else{
						Yii::app()->user->logout();
					}
// 				}
			}else{
				Yii::app()->user->logout();
			}
			$_str = (strrpos($url,'?') ? '&' : '?' );
			$url .= $_str . 'from=tuli';
			echo '<script>window.location.href="'.$url.'";</script>';
// 			echo $url;exit;
// 			$this->redirect($url);
// 			echo '<script>window.reload();</script>';
		}else{
			$this->redirect(Yii::app()->request->hostInfo);
		}
	}
	
	public function login($user = array(),$url = '')
	{
		$return = array('res'=>false, 'msg'=>'');
		$tuliUid = self::from . '_' . $user['user_type'] . '_' . $user['user_id'];
		$nickname = $user['nickname'];
		
		$userService = new UserService();
		$userOauth = $userService->getUserOauthByOpenFlatform(self::from,$tuliUid);
		$password = $tuliUid.'_'.Yii::app()->params->tuli['identity'];
		// 判断用户是否已经存在
		if($userOauth){
			$userBasic = $userService->getUserBasicByUids(array($userOauth['uid']));
			$userBasic = $userBasic[$userOauth['uid']];
			if(empty($userBasic)){
				return '用户数据错误';
			}
			$identify = new PipiUserIdentity($userBasic['username'],$password);
			$identify->openUserInfo = $userOauth;
			if($identify->authenticate()){
				Yii::app()->user->login($identify,USER_REGISTER_COOKIE_VTIME);
				$referer = Yii::app()->session['open_referer'];
				unset(Yii::app()->session['open_referer']);
				$return['res'] = true;
				$return['msg'] = '';
			}else{
				$return['msg'] = $identify->errorMessage;
			}
			return $return;
		}
		
		//新用户注册
		$userBasic['uid'] = $userService->getNextUid();
		$userBasic['password'] = $password;
		$userBasic['user_type'] = 1;
		$userBasic['reg_source'] = $userService->getUserRegEnSource(self::from);
		if($nickname &&  $userService->getUserBasicByNickNames(array($nickname))){
			$userBasic['nickname'] = self::from.'_'.$userBasic['uid'];
		}else{
			$userBasic['nickname'] = $nickname;
		}
		if($tuliUid &&  $userService->getVadidatorUser($tuliUid,USER_LOGIN_USERNAME)){
			$userBasic['username'] = self::from.'_'.$userBasic['uid'];
		}else{
			$userBasic['username'] = $tuliUid;
		}
		$userService->saveUserBasic($userBasic);
		
		if(!$userService->getNotice()){
			$oauth['uid'] = $userBasic['uid'];
			$oauth['openid'] = $tuliUid;
			$oauth['open_platform'] = self::from;
			$oauth['onickname'] = $nickname;
			$userService->saveUserOauth($oauth);
			
			$consumeService = new ConsumeService();
			$consumeService->saveUserConsumeAttribute(array('uid'=>$userBasic['uid'],'rank'=>0));
			$identify = new PipiUserIdentity($userBasic['username'],$userBasic['password']);
			if($identify->authenticate()){
				Yii::app()->user->login($identify,USER_REGISTER_COOKIE_VTIME);
				//注册推广来源
				$reg['sign'] = 'tuli';
				$reg['referer'] = $url;//注册页面的，前一个页面，由cookie维护
				$reg['curl'] = $url;//这里是Ajax注册，所以注册的当前页面就是用户的前一页面
				$reg['access_time'] = time();
				$reg['uid'] = $userBasic['uid'];
				$partnerService = new PartnerService();
				$partnerService->saveRegLog($reg);
					
				$referer = Yii::app()->session['open_referer'];
				unset(Yii::app()->session['open_referer']);
				$return['res'] = true;
				$return['msg'] = '注册完成, 登录成功';
			}else{
				$return['msg'] = $identify->errorMessage;
			}
		}else{
			$return['msg'] = $userService->getNotice();
		}
		return $return;
	}
	
	public function actionLogout()
	{
		Yii::app()->user->logout();
		exit(json_encode(array('result'=>true)));
	}
	
	/**
	 * 从图丽获取用户信息
	 */
	public function actionGetUserInfoFromTuli()
	{
		$token = Yii::app()->request->getParam('token');
		$userInfo = $this->getUrlInfo($token);
		exit(json_encode($userInfo));
	}
	
	/**
	 * 支付回调函数
	 */
	public function actionPayBack()
	{
		$result = array("ok"=>false,"reason"=>"","data"=>"");
		$key = Yii::app()->params->tuli['key'];
		// @todo 判断IP地址是否来自可靠源
		$ip = Yii::app()->request->userHostAddress;

		$auth = self::from;
		$auth_uid = Yii::app()->request->getParam('uid');
		$amount = Yii::app()->request->getParam('amount');
		$order_num = Yii::app()->request->getParam('order_num');
		$timestamp = Yii::app()->request->getParam('timestamp');
		$user_type = '1';
		$sign = Yii::app()->request->getParam('sign');
		if(empty($auth_uid) || empty($amount) || empty($timestamp) || empty($sign)){
			$result['reason'] = '参数错误';
			$result['data'] = '1';
			exit(json_encode($result));
		}
		if(strtolower($sign)!=(md5($auth_uid.$amount.$order_num.$timestamp.$key))){
			$result['reason'] = '参数错误';
			$result['data'] = '2';
			exit(json_encode($result));
		}
		
		$userService = new UserService();
		$userOauth = $userService->getUserOauthByOpenFlatform($auth,'tuli_'.$user_type.'_'.$auth_uid);
		if(!$userOauth){
			$result['reason'] = '用户不存在';
			$result['data'] = '3';
			exit(json_encode($result));
		}
		$userBasic = $userService->getUserBasicByUids(array($userOauth['uid']));
		$userBasic = $userBasic[$userOauth['uid']];
		if(empty($userBasic)){
			$result['reason'] = '用户不存在';
			$result['data'] = '4';
			exit(json_encode($result));
		}
		
		$changeRelation = Yii::app()->params->change_relation;
		$amount = $amount*$changeRelation['rmb_to_pipiegg'];
		
		$uid = $userBasic['uid'];
		$consumeServ = new ConsumeService();
		$rechargeData = array();
		$rechargeData['auth'] = $auth;
		$rechargeData['auth_uid'] = $auth_uid;
		$rechargeData['pipiegg'] = $amount;
		$rechargeData['order_id'] = $order_num;
		$rechargeData['create_time'] = time();
		$rechargeData['send_time'] = $timestamp;
		$rechargeData['info'] = json_encode($rechargeData);
		$res = $consumeServ->authRrecharge($uid, $rechargeData);
		if($res == '1'){
			$result['reason'] = '订单已经处理,请勿重复提交.';
		}elseif($res=='9'){
			$result['ok'] = true;
			$result['reason'] = '充值成功';
			//将皮蛋写入账户
			$newArray = array('uid'=>$uid,'pipiegg'=>$amount);
			$consumeServ->appendConsumeData($newArray);
		}else{
			$result['reason'] = '充值失败';
		}
		exit(json_encode($result));
	}
	
	/**
	 * 生成对方需要xml数据(需求是1分钟更新一次, 鉴于压力, 可以5到10分钟一次)
	 */
	public function actionUpdateXml()
	{
		$time = time();
		$title = array(
				'site_name'		=> "皮皮乐天",
				'site_url'		=> 'http://co.pipi.cn/index.php?from=tuli',
				'update_time'	=> $time,
		);
		$hostname = Yii::app()->request->hostInfo;
		$doteyServ = new DoteyService();
		$userServ = new UserService();
		$userJsonServ = new UserJsonInfoService();
		$archiveServ = new ArchivesService();
		$userListService=new UserListService();
		$doteys = $doteyServ->getDoteysByCondition(array('status'=>1));
		$doteyIds = array_keys($doteys);
		$userExtends = $userServ->getUserExtendByUids($doteyIds);
		$archiveInfos = $archiveServ->getArchivesByUids($doteyIds);
		$archiveIds = array_keys($archiveInfos);
		$archiveInfos = $archiveServ->buildDataByIndex($archiveInfos,'uid');
		//$nums = $archiveServ->getSessTotalSumByCondition($archiveIds);
		$record = $archiveServ->getArchivesByUids($doteyIds);
		$infos = $userJsonServ->getUserInfos($doteyIds, false);
		$data = array();
		foreach($doteyIds as $k=>$v){
			if(isset($infos[$v])){
				$_info = is_array($infos[$v]) ? $infos[$v] : json_decode($infos[$v], true);
				if($userServ->hasBit(intval($_info['ut']),USER_TYPE_DOTEY) && $_info['us']!=USER_STATUS_OFF){
					$_record = $record[$archiveInfos[$v]['archives_id']]['live_record'];
					$_tmp['user_id'] = $v;
					$_tmp['user_avatar'] = $userServ->getUserAvatar($v,'middle');
					$_tmp['user_nickname'] = $_info['nk'];
					$_tmp['user_level'] = (int) $_info['dk'];
					$_tmp['room_cover'] = $doteyServ->getDoteyUpload($v,'small');
					$room_online=$userListService->getUserList($archiveInfos[$v]['archives_id']);
					$_tmp['room_online'] = (int)isset($room_online['total'])?$room_online['total']:0;
					$_tmp['room_url'] = $hostname . $this->createUrl('archives/index',array('uid'=>$v));
					$_tmp['room_status'] = ($_record['status']==1) ? (int) $_record['status'] : 0; // 直播状态
					$_tmp['start_time'] = $_record['start_time'] > 0 ? (int) $_record['start_time'] : 0;
					$_tmp['tag'] = '';
					$_tmp['recommend'] = 0;
					$_tmp['room_cover_mobi'] = '';
					$data[$v] = $_tmp;
				}
			}
		}
		$this->createXml($title, $data);
	}
	
	protected function createXml($title = array(),$data = array())
	{
		$fileDir = ROOT_PATH."images".DIR_SEP.'tuli'.DIR_SEP.'tuli.xml';
		$xml = new XMLWriter();
// 		$xml->openUri("php://output");
// 		$xml->openMemory();
		$xml->openUri($fileDir); 
		$xml->setIndentString('  ');
		$xml->setIndent(true);
		
		$xml->startDocument('1.0', 'utf-8');
		$xml->startElement('document');
		foreach($title as $k=>$v){
			$xml->startElement($k);
			$xml->writeCData($v);
			$xml->endElement();
		}
		$xml->startElement('items');
		if($data){
		foreach($data as $k=>$v){
			$xml->startElement('item');
				foreach($v as $t=>$n){
					$xml->startElement($t);
					$xml->writeCData($n);
					$xml->endElement();
				}
			$xml->endElement();
		}
		}
		$xml->endElement();
		$xml->endElement();
		$xml->endDocument();
		$xml->flush();
	}
	
	public function getUrlInfo($token = '')
	{
		$url = Yii::app()->params->tuli['user_url'];
		$post_data = array('token'=>$token,
				'identity'=>'22bccac1d905bfe2a9acadf10aa72337');
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$content = curl_exec($ch);
		$result=json_decode($content, true);
		curl_close($ch);
		return $result;
	}
	
	public function actionGetUInfo(){
		$uid = Yii::app()->request->getParam('uid',false);
		$return = array('res'=>false,'data'=>array());
		if ($uid && is_numeric($uid)){
			$service = new UserService();
			$uInfo = $service->getUserBasicByUids(array($uid));
			if($uInfo){
				$uInfo = $uInfo[$uid];
				$uAvatars = $service->getUserAvatarsByUids(array($uid),'small');
				$return['res'] = true;
				$return['data']['follow_id'] =  $uid;
				$return['data']['follow_avatar'] =  $uAvatars[$uid];
				$return['data']['follow_nickname'] =  $uInfo['nickname'];
				$return['data']['follow_room'] =  Yii::app()->request->hostInfo.$this->createUrl('archives/index',array('uid'=>$uid));
			}
		}
		exit(json_encode($return));
	}
}

?>