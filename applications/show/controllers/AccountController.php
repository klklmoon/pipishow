<?php

class AccountController extends PipiController
{
	protected $uid;
	protected $normal;
	
	protected $agentsService=null;
	//是否有代理身份
	protected $isAgent=0;
	
	public function init(){
		parent::init();
		
		$this->uid = Yii::app()->user->id;
		if(empty($this->uid)){
			echo '<script type="text/javascript">window.location="'.(Yii::app()->request->getHostInfo()).'";</script>';
			if(!Yii::app()->request->isFlashRequest){
				exit();
			}
		}
		if(empty($this->agentsService))
			$this->agentsService=new AgentsService();
		//检测是否曾经有代理身份
		$agents=$this->agentsService->getAgentByUids($this->uid);
		$this->isAgent=isset($agents[$this->uid])?true:false;
		
		// 注册css和js
		$assetManager = Yii::app()->getAssetManager();
		$assetManager->excludeFiles = array('.svn','.gitignore','images','admin');
		//$pipiFrontPath = $assetManager->publish(Yii::getPathOfAlias('root.statics'));
		$pipiFrontPath=$this->pipiFrontPath;
		$this->cs->registerScriptFile($pipiFrontPath.'/js/account/My97DatePicker/WdatePicker.js?token='.$this->hash,CClientScript::POS_END);
		$this->cs->registerScriptFile($pipiFrontPath.'/js/area/city_data.js?token='.$this->hash,CClientScript::POS_END);
		$this->cs->registerScriptFile($pipiFrontPath.'/js/area/datajs.js?token='.$this->hash,CClientScript::POS_END);
		
		$this->cs->registerScriptFile($pipiFrontPath.'/js/account/account.js?token='.$this->hash,CClientScript::POS_END);
		$this->cs->registerScriptFile($pipiFrontPath.'/js/account/copyToClipboard.js?token='.$this->hash,CClientScript::POS_END);
	}
	
	public function actionIndex()
	{
		$this->actionMain();
	}
	
	public function actionTest()
	{
// 		$uid = $this->uid;
// 		$this->render('test');
	}
	
	/**
	 * 基本信息
	 */
	public function actionMain()
	{
		
		$uid = Yii::app()->request->getParam('uid');
		$input = Yii::app()->request->getParam('input');
		$step = Yii::app()->request->getParam('a');
		$uid = $uid ? $uid : Yii::app()->user->id;
		
		Yii::app()->detachEventHandler('onEndRequest',array(Yii::app()->log,'processLogs'));
		$upload = new PipiFlashUpload();
		$uploadHtml = $upload->renderHtml($uid);
		if($upload->processRequest($input,$step)){
			exit();
		}
		if(Yii::app()->request->isAjaxRequest){
			$ajaxReturn = array('result'=>false, 'msg'=>'操作失败, 请重新操作');
			$data = array();
			$data['update_desc'] = array('atr'=>time());
			$res = $this->userService->saveUserJson($uid, $data);
			if($res) {
				$ajaxReturn['result'] = true;
				$ajaxReturn['msg'] = '操作成功';
			}
			exit(json_encode($ajaxReturn));
		}
		$this->viewer['avatar'] = $this->userService->getUserAvatar($uid,'middle');
	
		$userInfo = $this->userService->getUserExtendByUids(array($uid));
		$userInfo = $userInfo[$uid];
		$this->_left('main',array('acount_extend_info'=>$userInfo,'upload_avatar'=>$uploadHtml));
	}
	
	/**
	 * 修改基本信息
	 */
	public function actionEdit()
	{	$uid = $this->uid;
		if(Yii::app()->request->isAjaxRequest){
			$ajaxReturn = array('result'=>false,'msg'=>'操作失败,请重新操作');
			$userBasic = array();
			$userExtend = array();
			$userExtend['uid'] = $userBasic['uid'] = Yii::app()->request->getParam('uid');
			$nickname = $userBasic['nickname'] = Yii::app()->request->getParam('nickname');
			$gender = Yii::app()->request->getParam('gender');
			$birthday = Yii::app()->request->getParam('birthday');
			$province = Yii::app()->request->getParam('province');
			$city = Yii::app()->request->getParam('city');
			
			if($uid!=$userBasic['uid']){
				$ajaxReturn['msg'] = '非法请求, 请重新登录后操作!';
				exit(json_encode($ajaxReturn));
			}
			$userService = $this->userService;
// 			$user = $userService->getUserExtendByUids(array($uid));
// 			if(empty($user[$uid])){
// 				$ajaxReturn['msg'] = '非法用户, 请重新登录后操作!!';
// 				exit(json_encode($ajaxReturn));
// 			}
			$check_result = $this->check_show_nickname($nickname);
			if($check_result){
				$ajaxReturn['msg'] = $check_result;
				exit(json_encode($ajaxReturn));
			}
			if($province=='选择省份'){
				$province = '';
			}
			if($city=='选择城市'){
				$city = '';
			}
			//修改昵称
			$res = $userService->saveUserJson($uid, $userBasic);
			
			if(!$res) {
				$ajaxReturn['msg'] = '昵称修改失败, 请重试';
				exit(json_encode($ajaxReturn));
			}else{
				if($nickname){
					$archivesService=new ArchivesService();
					$archives=$archivesService->getArchivesByUid($uid);
					$archivesCat=$archivesService->getAllArchiveCatByEnName('common');
					$title=$nickname.'的直播间';
					foreach($archives as $row){
						if($row['cat_id']==$archivesCat['cat_id']){
							$archivesService->saveArchives(array('archives_id'=>$row['archives_id'],'title'=>$title));
						}
					}
				}
				if(!isset($_POST['gender'])){
					$ajaxReturn['result'] = true;
					$ajaxReturn['msg'] = '修改成功!';
					exit(json_encode($ajaxReturn));
				}
			}
			
			
			$userExtend['gender'] = $gender;
			$userExtend['birthday'] = strtotime($birthday);
			$userExtend['province'] = $province;
			$userExtend['city'] = $city;
			$res = $userService->saveUserExtend($userExtend);
			if($res) {
				$ajaxReturn['result'] = true;
				$ajaxReturn['msg'] = '修改成功!';
				exit(json_encode($ajaxReturn));
			}
		}
	}
	
	/**
	 * 修改密码
	 */
	public function actionPassword()
	{
		$uid = $this->uid;
		if(Yii::app()->request->isAjaxRequest)
		{
			$pswd = Yii::app()->request->getParam('password');
			$newpswd = Yii::app()->request->getParam('newpswd');
			$repswd = Yii::app()->request->getParam('repswd');
			$postUid = Yii::app()->request->getParam('uid');
			if($uid != $postUid){
				exit('非法请求, 请重新登录');
			}
			if(empty($pswd) || empty($newpswd) || empty($repswd))
			{
				exit('密码不能为空');
			}
			if($newpswd != $repswd)
			{
				exit('新密码和确认密码不一致, 请检查后重新输入');
			}
			$userService = $this->userService;
			$userInfo = $userService->getUserBasicByUids(array($uid));
			if($userInfo){
				$pswd = $userService->encryPassword($pswd, $userInfo[$uid]['reg_salt']);
				if($pswd != $userInfo[$uid]['password']){
					exit('原密码错误, 请重试');
				}else{
					$userInfoChange = array('uid'=>$uid, 'password'=>$newpswd);
					$newUserInfo = $userService->saveUserBasic($userInfoChange);
					if($newUserInfo['password']==$userService->encryPassword($newpswd,$userInfo[$uid]['reg_salt'])){
						exit('密码修改成功!');
					}
					exit('修改密码失败, 请稍后重试');
				}
			}else{
				exit('非法请求, 请重新登录后再操作');
			}
			
		}
		$this->_left('password', '', 'main');
	}
	
	/**
	 * 我的消息
	 */
	public function actionMessage()
	{
		$uid = $this->uid;
		$page = Yii::app()->request->getParam('page');
		$msgType = Yii::app()->request->getParam('type');
		$msgType = in_array($msgType,array('system','family','site')) ? $msgType : 'system';
		$this->viewer['curSelect'][$msgType] = 'class="menuvisted"';
		$page = $page > 0 ? $page : 1;
		$limit = 10;
		$offset = ($page - 1) * $limit ;
		$messageService = new MessageService();
		$unReads = $messageService->getUserMessageUnReads($uid);
		if($unReads){
			$unSystemReads = $unReads[$uid]['system_push'] > 0 ? $unReads[$uid]['system_push'] : 0;
			$unFamilyReads =  $unReads[$uid]['family_join']+$unReads[$uid]['family_manage']+$unReads[$uid]['family_upgrade'];
			$unSiteReads = $messageService->countUserUnReadSiteMessagesByUid($uid);
		}else{
			$unSystemReads = $unFamilyReads = $unSiteReads = $count = 0;
			$unSiteReads = $messageService->countUserUnReadSiteMessagesByUid($uid);
		}
		$condition = array('limit'=>$limit,'offset'=>$offset);
		$message = array();
		if($msgType == 'system'){
			$message = $messageService->getUserReceiveMessagesByUid($uid,MESSAGE_CATEGORY_SYSTEM,MESSAGE_CATEGORY_SYSTEM_PUSH,$condition);
			$count =  $messageService->countUserReceiveMessages($uid,MESSAGE_CATEGORY_SYSTEM,MESSAGE_CATEGORY_SYSTEM_PUSH,array());
		}elseif($msgType == 'family'){
			$message = $messageService->getUserReceiveMessagesByUid($uid,MESSAGE_CATEGORY_FAMILY,null,$condition);
			$count =  $messageService->countUserReceiveMessages($uid,MESSAGE_CATEGORY_FAMILY,null,array());
		}elseif($msgType == 'site'){
			$message = $messageService->getUserReceiveSiteMessagesByUid($uid,$limit,$offset);
			$count =  $messageService->countUserReceiveSiteMessagesByUid($uid);
		}
		


		$data['msgList']= $message;
		$data['msgType'] = $msgType;
		$data['page_url'] = '&type='.$msgType;
		$data['system_unread'] = $unSystemReads;
		$data['family_unread'] = $unFamilyReads;
		$data['site_unread'] = $unSiteReads;
		$data['count'] = $count;
		$data['page'] = $page;
		$data['page_num'] = ceil($count / $limit);
		$this->_left('message', $data, 'message');
	}
	
	/**
	 * 我的消息_删除消息
	 */
	public function actionDelMsg(){
		$ajaxReturn = array('result'=>false, 'msg'=>'操作失败, 请重新操作');
		if(Yii::app()->request->isAjaxRequest){
			$msgId = Yii::app()->request->getParam('id');
			$action = Yii::app()->request->getParam('action');
			$result = false;
			$msgServ = new MessageService();
			if($action=='del'){
				$result = $msgServ->delMessageByIds(array($msgId));
			}elseif($action=='read'){
				$result = $msgServ->markReadMessage($msgId);
			}
			if($result){
				$ajaxReturn['result'] = $result;
				$ajaxReturn['msg'] = '操作成功';
			}
		}
		exit(json_encode($ajaxReturn));
	}
	
	/**
	 * 我的物品
	 */
	public function actionItems()
	{
		$uid = $this->actionBag();
	}
	
	/**
	 * 我的物品_礼物背包
	 */
	public function actionBag()
	{
		$data = array();
		$uid = $this->uid;
		$data['account_bags'] = $this->getBagsData($uid);
		$this->_left('bag',$data,'items');
	}
	
	/**
	 * 我的物品_道具
	 */
	public function actionProps()
	{
		$uid = $this->uid;
		$data = $this->getPropsData($uid,'prop');
		
		$data['fly'] = $this->getPropsData($uid,'flyscreen');
		$data['broadcast']=$this->getPropsData($uid,'broadcast');
		$this->_left('props',$data,'items');
	}
	
	public function actionNumber(){
		$userNumberService = new UserNumberService();
		$userPropsService = new UserPropsService();
		$userRechargeModel = new UserRechargeRecordsModel();
		$lastCharge = $userRechargeModel->getLastCharge($this->uid);
		$userNumbers = $userNumberService->getUserNumberList($this->uid);
		$data['userProps'] = $userPropsService->getUserPropsAttributeByUid($this->uid);
		$data['userNumbers'] = $userNumbers;
		$data['lastChangeTime'] = $lastCharge['ctime'];
		$this->_left('number',$data,'items');
	}
	
	public function actionModifyDesc(){
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('user','You are not logged'))));
		}
		$uid = $this->uid;
		$number = Yii::app()->request->getParam('number');
		$short_desc = Yii::app()->request->getParam('short_desc');
		if($number <= 0 || empty($short_desc)){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('common','Parameters are wrong'))));;
		}
		$nLen = strlen($number);
		$userNumberModel = new UserNumberModel();
		$orgNumberModel = $userNumberModel->findByPk(array('uid'=>$uid,'number'=>$number));
		if(!$orgNumberModel){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('common','此靓号不属于你，不能修改'))));;
		}
		$userNumber['uid'] = $uid;
		$userNumber['number'] = $number;
		$userNumber['short_desc'] = $short_desc;
		$userNumberService = new UserNumberService();
		if($userNumberService->saveUserNumber($userNumber)){
			$userPropsServie = new UserPropsService();
			$userProps = $userPropsServie->getUserPropsAttributeByUid($uid);
			if($userProps && $number == $userProps['number']){
				$userJson['num']['n'] = (string)$number;
				$userJson['num']['s'] = $short_desc;
				$useingNumber['uid'] = $uid;
				$useingNumber['number'] = $number;
				$useingNumber['number_short_desc'] = $short_desc;
				$userPropsServie = new UserPropsService();
				$userJsonInfoService = new UserJsonInfoService();
				$userPropsServie->saveUserPropsAttribute($useingNumber);
				$userJsonInfoService->setUserInfo($uid,$userJson);
				$zmq = $userJsonInfoService->getZmq();
				$zmq->sendZmqMsg(609,array('type'=>'update_json',$uid,'json_info'=>$userJson));
			}
			exit(json_encode(array('flag'=>1,'message'=> '')));;
		}else{
			exit(json_encode(array('flag'=>0,'message'=>array_pop($userNumberService->getNotice()))));;
		}
	}
	public function actionUseNumber(){
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('user','You are not logged'))));
		}
		$uid = $this->uid;
		$number = Yii::app()->request->getParam('number');
		if($number <= 0){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('common','Parameters are wrong'))));;
		}
		
		$userNumberModel = new UserNumberModel();
		$orgNumberModel = $userNumberModel->findByPk(array('uid'=>$uid,'number'=>$number));
		if(!$orgNumberModel){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('common','此靓号不属于你，不能使用'))));;
		}
		
		$userJson['num']['n'] = (string)$number;
		$userJson['num']['s'] = $orgNumberModel ? $orgNumberModel->short_desc : '';
		$useingNumber['uid'] = $uid;
		$useingNumber['number'] = $number;
		$useingNumber['number_short_desc'] = $orgNumberModel ? $orgNumberModel->short_desc : '';
		$userPropsServie = new UserPropsService();
		$userJsonInfoService = new UserJsonInfoService();
		$userPropsServie->saveUserPropsAttribute($useingNumber);
		$userJsonInfoService->setUserInfo($uid,$userJson);
		$zmq = $userJsonInfoService->getZmq();
		$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>$userJson));
		exit(json_encode(array('flag'=>1,'message'=> '')));
	}
	
	public function actionUnUseNumber(){
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('user','You are not logged'))));
		}
		$uid = $this->uid;
		$number = Yii::app()->request->getParam('number');
		if($number <= 0){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('common','Parameters are wrong'))));;
		}
	
		$userNumberModel = new UserNumberModel();
		$orgNumberModel = $userNumberModel->findByPk(array('uid'=>$uid,'number'=>$number));
		if(!$orgNumberModel){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('common','此靓号不属于你，不能使用'))));;
		}
	
		
		$useingNumber['uid'] = $uid;
		$useingNumber['number'] = '';
		$useingNumber['number_short_desc'] = '';
		$userPropsServie = new UserPropsService();
		$userPropsServie->saveUserPropsAttribute($useingNumber);
		
		$userJson['num'] = array();
		UserJsonInfoService::getInstance()->setUserInfo($uid,$userJson);
		$zmq = $userPropsServie->getZmq();
		$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>$userJson));
		exit(json_encode(array('flag'=>1,'message'=> '')));
	}
	
	public function actionRecycleNumber(){
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('user','You are not logged'))));
		}
		$uid = $this->uid;
		$number = Yii::app()->request->getParam('number');
		if($number <= 0){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('common','Parameters are wrong'))));;
		}
		
		$userNumberModel = new UserNumberModel();
		$orgNumberModel = $userNumberModel->findByPk(array('uid'=>$uid,'number'=>$number));
		if(!$orgNumberModel){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('common','此靓号不属于你，不能删除'))));
		}
		if(!$orgNumberModel->status){
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('common','此靓号属于正常状态，不能删除'))));
		}
		if($orgNumberModel->delete()){
			exit(json_encode(array('flag'=>1,'message'=> Yii::t('common','删除成功'))));
		}else{
			exit(json_encode(array('flag'=>0,'message'=> Yii::t('common','删除失败'))));
		}
	}
	/**
	 * 我的物品_座驾
	 */
	public function actionCar()
	{
		$uid = $this->uid;
		$data = $this->getPropsData($uid,'car');
		$common = new ConsumeService();
		$data['ranks'] = $common->getUserRankFromRedis();
		$data['getCar'] = $this->isRankCar($uid);
		
		$this->_left('car',$data,'items');
// 		$userJson = new UserJsonInfoService();
// 		$res = $userJson->getUserInfo($uid, false);
// 		print_r($res);
	}
	
	/**
	 * 座驾更换
	 */
	public function actionUndoCar()
	{
		$ajaxReturn = array('result'=>false, 'msg'=>'操作失败, 请重新操作');
		if(Yii::app()->request->isAjaxRequest){
			$uid = $this->uid;
			$car = Yii::app()->request->getParam('prop_id');
			$car = $car >= 0 ? $car : 0 ;
			$condition = array('uid'=>$uid,'car'=>$car);
			$userPropsService = new UserPropsService();
			$propsService = new PropsService();
			if($car >0 ){
				$carInfo = $propsService->getPropsByIds(array($car),false,true);
				$carInfo = $carInfo[$car];
				$carInfo['attribute'] = $propsService->buildDataByIndex($carInfo['attribute'], 'attr_enname');
				$carInfo['flash'] = $carInfo['attribute']['car_animation']['value'];
				$carInfo['timeout'] = $carInfo['attribute']['car_animation_time']['value'];
				$bagInfo = $userPropsService->getUserValidPropsOfBagByCatId($uid,$carInfo['cat_id']);
				$bagInfo = $propsService->buildDataByIndex($bagInfo,'prop_id');
				$carInfo['valid_time'] = $bagInfo[$car]['valid_time'];
				if($carInfo['valid_time'] != 0 && $carInfo['valid_time'] < time()){
					$ajaxReturn['result'] = false;
					$ajaxReturn['msg'] = '该座驾已过期，请去往商城续费购买';
					exit(json_encode($ajaxReturn));
				}
			}else{
				$carInfo = array();
			}
			$res = $userPropsService->saveUserCar($condition,$carInfo);
			if($res){
				$ajaxReturn['result'] = true;
				$ajaxReturn['msg'] = '操作成功';
			}
		}
		exit(json_encode($ajaxReturn));
	}
	
// 	public function actionGetCar()
// 	{
// 		$ajaxReturn = array('result'=>false,'msg'=>'操作失败, 请稍后重新操作');
// 		if(Yii::app()->request->isAjaxRequest){
// 			$uid = $this->uid;
// 			$car = Yii::app()->request->getParam('car');
// 			$car_infos = $this->isRankCar($uid);
// 			$_tmp = 0;
// 			foreach ($car_infos as $v) {
// 				if($car == $v['prop_id']){
// 					$prop_id = $v['prop_id'];
// 					$cat_id = $v['cat_id'];
// 					$_tmp ++;
// 				}
// 			}
// 			if($_tmp>0){
// 				$userPropsService = new UserPropsService();
// 				// @todo add to bag
// 				$bag['prop_id']=$prop_id;
// 				$bag['cat_id'] = $cat_id;
// 				$bag['uid']=$uid;
// 				$bag['num']=1;
// 				$bag['valid_time'] = 0;
// 				$bagId = $userPropsService->saveUserPropsBag($bag);
// 				if($bagId > 0){
// 				// @todo add record
// 					$records['uid'] = $uid;
// 					$records['cat_id'] = $cat_id;
// 					$records['prop_id'] = $prop_id;
// 					$records['pipiegg'] = 0;
// 					$records['dedication'] = 0;
// 					$records['egg_points'] = 0;
// 					$records['charm'] = 0;
// 					$records['charm_points'] = 0;
// 					$records['vtime'] = 0;
// 					$records['info'] = '用户升级领取';
// 					$records['source'] = 1;
// 					$records['amount'] = 1;
// 					$userPropsService->saveUserPropsRecords($records);
// 					$ajaxReturn['result'] = true;
// 					$ajaxReturn['msg'] = '领取成功';
// 				}else{
// 					$ajaxReturn['msg'] = '领取失败, 请稍后再试';
// 				}
// 			}else{
// 				$ajaxReturn['msg'] = '操作错误, 请登录后重新操作';
// 			}
// 		}
// 		exit(json_encode($ajaxReturn));
// 	}
	
	/**
	 * 我的物品_月卡
	 */
	public function actionMoon()
	{
		$uid = $this->uid;
		$data = $this->getPropsData($uid,'monthcard');
		$userPropsService = new UserPropsService();
		$roseRecord = $condition = array();
		$condition['start_time'] = mktime(0,0,0,date('m'),1,date('Y'));
		$condition['end_time'] = mktime(0,0,0,date('m')+1,1,date('Y'));
		foreach($data['propsInfo'] as $k=>$v){
			$roseRecord[$k] = $userPropsService->getUserPropsUseCount($uid, $k, $condition);
		}
		if($data['bagInfo'][0]['valid_time']>0) {
			$userGiftService = new UserGiftService();
			$etime = $data['bagInfo'][0]['valid_time'];
			$stime = $etime - (30 *24*60*60);
			$etime = mktime(1,0,0,date('m',$etime),date('d',$etime)+1,date('Y',$etime));
			// 判断还能领取多少
			$data['monthgift']['num'] = $userGiftService->countMonthGift($uid, $stime, $etime);
		}else{
			$data['monthgift']['num'] = 0;
		}
		$data['monthgift']['all_num'] = 90;
		
		$data['roseRecord'] = $roseRecord[$k] ? $roseRecord[$k] : 0;
		$this->_left('moon',$data,'items');
	}
	
	public function actionCheckin()
	{
		$ajaxReturn = array('result'=>false, 'msg'=>'', 'is_month'=>false);
		if(Yii::app()->request->isAjaxRequest){
			$uid = $this->uid;
			// 判断用户等级是否大于等于"平民2";
			$rank = $this->getUserJsonAttribute('rk');
			$userService = new UserService();
			if($rank < 1 ){
				// 判断是否为充值用户
				$consumeServ = new ConsumeService();
				$eggs = $consumeServ->getUserRechargeEggs($uid);
				if(!$eggs){
					// 判断消费皮蛋是否超过1皮蛋
					$soncumeEgg = $consumeServ->sumUserConsumeRecord($uid);
					if($soncumeEgg < 1){
						// 判断用户是否安全用户
						$users = $userService->getUserBasicByUids(array($uid));
						$user = $users[$uid];
						$pramas['email'] = $user['reg_email'];
						$pramas['mobile'] = isset($user['reg_mobile']) ? $user['reg_mobile'] : '';
						if($user['create_time'] >= (strtotime('2013-7-18 00:00:00')) && !$pramas['email'] && !$pramas['mobile']){
						$ajaxReturn['msg'] = '请先设置安全邮箱或密保手机，才能签到领取免费礼物';
						$ajaxReturn['href'] = $this->createUrl('account/security');
						exit(json_encode($ajaxReturn));
						}
					}
				}
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
			if($isMoon){
				$ajaxReturn['is_month'] = true;
				$checkinAll = Yii::app()->request->getParam('checkinAll');
				$etime = $isMoon[0]['valid_time'];
				$stime = $etime - (30 *24*60*60);
				$etime = mktime(1,0,0,date('m',$etime),date('d',$etime)+1,date('Y',$etime));
				$num = $userGiftService->countMonthGift($uid, $stime, $etime);
				if($checkinAll=='1') {
					// 判断还能领取多少
					if($num > 0) {
						$allCheckin = $this->checkin($uid, CHENKIN_MONTHCARD,CHECKIN_GIFT_MONTHCARD,$num, 0, true);
						if($allCheckin['result']=='1'){
							$giftInfo = $allCheckin['info'];
							$ajaxReturn['msg'] = '本月剩余配额'.$allCheckin['info'].'已全部提取到背包中';
							$ajaxReturn['result'] = true;
							$update_num = $isMoon[0]['num'];
							$userPropsService->saveUserPropsBag(array('uid'=>$uid, 'prop_id'=>$propId, 'num'=>$update_num));
							exit(json_encode($ajaxReturn));
						}
					}
				}else{
					if($num > 0){
						$monthCheckin = $this->checkin($uid, CHENKIN_MONTHCARD, CHECKIN_GIFT_MONTHCARD, CHECKIN_GIFT_MONTHCARD_NUM);
						if($monthCheckin['result']=='1'){
							$giftInfo = $monthCheckin['info'].'和';
							$update_num = 1;
							$userPropsService->saveUserPropsBag(array('uid'=>$uid, 'prop_id'=>$propId, 'num'=>$update_num));
						}
					}
				}
			}
			
			$normalCheckin = $this->checkin($uid, CHENKIN_NORMAL, CHECKIN_GIFT_NORMAL);
			if($normalCheckin['result']=='1'){
				$userBase=$userService->getUserFrontsAttributeByCondition($uid,true,true);
				$broadcastNum=0;
				$propSendMsg='';
				if($userService->hasBit(intval($userBase['ut']),USER_TYPE_DOTEY)&&$userBase['us']!=USER_STATUS_OFF){
					$broadcastNum=3;
					$propSendMsg='获得'.$broadcastNum.'个每日广播（当日有效）';
				}else{
					if($rank>=8){
						$broadcastNum=1;
						$propSendMsg='获得'.$broadcastNum.'个每日广播（当日有效）';
					}else{
						$propSendMsg='升级到富豪8后，签到可额外获得每日广播1个';
					}
				}
				if($broadcastNum>0){
					$propInfo=$propsService->getPropsByEnName('day_broadcast');
					$userPropsService=new UserPropsService();
					$record['uid']=$uid;
					$record['prop_id']=$propInfo['prop_id'];
					$record['cat_id']=$propInfo['cat_id'];
					$record['amount']=$broadcastNum;
					$record['source']=PROPSRECORDS_SOURCE_ADMIN;
					$record['info']='系统赠送('.$propInfo['name'].'*'.$broadcastNum.')';
					$record['vtime']=strtotime(date('Y-m-d',strtotime("+1 day")));
					$recordId=$userPropsService->saveUserPropsRecords($record);
					$broadcast=$userPropsService->getUserValidPropsOfBagByPropId($uid,$propInfo['prop_id']);
					$broadcast=array_pop($broadcast);
					$userPropsBagModel = new UserPropsBagModel();
					$userPropsBag =$userPropsBagModel->findByAttributes(array('uid'=>$uid,'prop_id'=>$propInfo['prop_id']));
					if($userPropsBag){
						$orguserPropsBagModel = $userPropsBagModel->findByPk($userPropsBag['bag_id']);
						$propBag['bag_id']=$userPropsBag['bag_id'];
						$propBag['record_sid']=$recordId;
						$propBag['num']=$broadcastNum;
						$propBag['valid_time']=strtotime(date('Y-m-d',strtotime("+1 day")));
						$userPropsService->attachAttribute($orguserPropsBagModel, $propBag);
						$orguserPropsBagModel->save();
					}else{
						$propBag['uid']=$uid;
						$propBag['record_sid']=$recordId;
						$propBag['prop_id']=$propInfo['prop_id'];
						$propBag['cat_id'] = $propInfo['cat_id'];
						$propBag['num']=$broadcastNum;
						$propBag['valid_time']=strtotime(date('Y-m-d',strtotime("+1 day")));
						$userPropsService->attachAttribute($userPropsBagModel, $propBag);
						$userPropsBagModel->save();
					}
					$checkin = array(
						'uid'=>$uid,
						'type'=>CHENKIN_BROADCAST,
						'target_id'=>$propInfo['prop_id'],
						'num'=>1,
						'info'=>$propInfo['name'].'* 1',
						'pipiegg'=>($propInfo['pipiegg'] * 1),
						'create_time'=>time(),
					);
					$userGiftService->saveCheckinRecord($checkin);
				}
				$giftInfo .= $normalCheckin['info'];
				$ajaxReturn['result'] = true;
				$ajaxReturn['msg'] = $giftInfo.'已经存放到背包中<br/>'.$propSendMsg;
			}elseif($normalCheckin['result']=='2'){
				$ajaxReturn['msg'] = $isMoon ? '截止今天的免费礼物已领取完毕' : '今日已签过到';
			}else{
				$ajaxReturn['msg'] = $normalCheckin['info'];
			}
		}
		exit(json_encode($ajaxReturn));
	}
	
	
	
	public function actionGetUserCheckinInfo(){
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$uid = Yii::app()->user->id;
		$indexPageService=new IndexPageService();
		$monthCard=$indexPageService->getMonthCard($uid);
		$list=array();
		$data=$indexPageService->getCheckinItems($uid,true,$monthCard);
		$allCheck=0;
		foreach($data as $row){
			$list['list'][]=$row;
			foreach($row as $val){
				if($val['status']==0){
					$allCheck=1;
				}
			}
		}
		$list['allCheck']=$allCheck;
		$list['count']=$indexPageService->getCheckinDays($uid);
		$list['monthHref']=$monthCard?$this->createUrl('account/moon'):$this->createUrl('shop/monthcard');
		exit(json_encode(array('flag'=>1,'data'=>$list)));
	}
	
	public function actionUserCheckinAll(){
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$uid = Yii::app()->user->id;
		$indexPageService=new IndexPageService();
		if(!$indexPageService->checkinAll($uid)){
			$error=$indexPageService->getNotice();
			if($error){
				$error=array_pop($error);
			}else{
				$error='已签到过';
			}
			exit(json_encode(array('flag'=>0,'message'=>$error)));
		}else{
			exit(json_encode(array('flag'=>1,'message'=>'签到成功')));
		}
	}
	
	public function actionUserCheckin(){
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$uid = Yii::app()->user->id;
		$item=Yii::app()->request->getParam('item');
		$indexPageService=new IndexPageService();
		if(!$indexPageService->checkin($uid,$item)){
			$error=$indexPageService->getNotice();
			if($error){
				$error=array_pop($error);
			}
			$items=explode('_',$item);
			$monthCard = $indexPageService->getMonthCard($uid);
			if($items[0]==2&&!$monthCard){
				$message='本物品限持有月卡的用户领取';
			}else{
				$message=$error?$error:'今天已签到过';
			}
			exit(json_encode(array('flag'=>0,'message'=>$message)));
		}else{
			exit(json_encode(array('flag'=>1,'message'=>'签到成功')));
		}
	}
	
	/**
	 * 签到实现
	 * @param unknown_type $uid
	 * @param unknown_type $type
	 * @param unknown_type $en_name
	 * @param unknown_type $num
	 * @param unknown_type $time
	 * @return multitype:number string
	 */
	public function checkin($uid, $type, $en_name, $num = 1, $time =0, $checkinAll = false)
	{
		$userGiftService = new UserGiftService();
		$isCheckin = $userGiftService->getIsCheckin($uid,$type, $time);
		if(!$isCheckin || $checkinAll){
			$giftService = new GiftService();
			$roseInfo = $giftService->getGiftList(array('en_name'=>$en_name));
			$roseKey = array_keys($roseInfo);
			$roseId = $roseKey[0];
			$gift = array('uid'=>$uid,'gift_id'=>$roseId,'num'=>$num);
			$giftBagService = new GiftBagService();
			$records['info']=serialize(array('uid'=>$uid,'nickname'=>$this->viewer['user_basic']['nickname'],'gift_id'=>$roseId,'gift_name'=>$roseInfo[$roseId]['zh_name'],'num'=>$num,'remark'=>'签到赠送'));
			$records['source']=3;
			$addRose = $giftBagService->saveUserGiftBagByUid($gift, $records);
			if($addRose) {
				if($num > 1){
					$url = ($this->pipiFrontPath).'/fontimg/common/hongmeigui.png';
				}else{
					$url = ($this->pipiFrontPath).'/fontimg/common/cao.jpg';
				}
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
				$giftInfo = '<img src="'.$url.'"/>'.$giftInfo;
				$addCheckin = $userGiftService->saveCheckinRecord($checkin);
				if($addCheckin){
					return array('result'=>1,'info'=>$giftInfo);
				}
			}else{
				return array('result'=>0,'info'=>'签到失败, 请重新签到');
			}
		}else{
			return array('result'=>2,'info'=>'已经签到过');
		}
		return array('result'=>0,'info'=>'错误');
	}
	
	/**
	 * 我的物品_vip
	 */
	public function actionVip()
	{
		$uid = $this->uid;
		$data = $this->getPropsData($uid,'vip');
		$common = new ConsumeService();
		$data['ranks'] = $common->getUserRankFromRedis();
		
		$this->_left('vip',$data,'items');
	}
	
	public function actionVipHide()
	{
		$ajaxReturn = array('result'=>false,'msg'=>'操作失败, 请稍后重试');
		if(Yii::app()->request->isAjaxRequest){
			$uid = $this->uid;
			$userPropsService = new UserPropsService();
			$is_hidden = Yii::app()->request->getParam('is_hidden')==1 ? 1 : 0;
			$propsAttriute = array('uid'=>$uid, 'is_hidden'=>$is_hidden);
			$res = $userPropsService->saveVipHidden($propsAttriute);
			if($res){
				$ajaxReturn['result'] = true;
				$ajaxReturn['msg'] = '操作成功';
			}
		}
		exit(json_encode($ajaxReturn));
	}
	
	/**
	 * 开启或停用vip
	 */
	public function actionOpenOrStopVip()
	{
		$ajaxReturn = array('result'=>false,'msg'=>'操作失败, 请稍后重试');
		$uid = Yii::app()->request->getParam('uid');
		$prop_id=Yii::app()->request->getParam('prop_id');
		$use_status=Yii::app()->request->getParam('use_status');
		$propsService=new PropsService();
		if($use_status==1)
		{
			$result=$propsService->stopVipOfBag($uid, $prop_id);
		}
		else
		{
			$result=$propsService->openVipOfBag($uid, $prop_id);
		}
		if($result){
			$ajaxReturn['result'] = true;
			$ajaxReturn['msg'] = '操作成功';
		}
		exit(json_encode($ajaxReturn));
	}
	
	/**
	 * 我的物品_守护
	 */
	public function actionGuard()
	{
		$uid = $this->uid;
		$data = $this->getPropsData($uid,'guardian');
		$userService = $this->userService;
		$uid = array();
		foreach($data['bagInfo'] as $k=>$v){
			$uids[] = $v['target_id'];
		}
		$data['dotey'] = $userService->getUserBasicByUids($uids);
		$this->_left('guard',$data,'items');
	}
	
	/**
	 * 我的关注
	 */
	public function actionFollow()
	{
		$uid = $this->uid;
		// 获取用户的关注列表
		$weiboService = new WeiboService();
		$follow = $weiboService->getDoteyAttentionsByUid($uid);
		$userJson = new UserJsonInfoService();
		$propService = new GiftService();
		foreach($follow as &$v){
			$res = $userJson->getUserInfo($v['uid'],false);
			if(!$res){
				$res = $this->userService->getUserFrontsAttributeByCondition($v['uid'],true);
			}
			$doteyInfo[$v['uid']] = $res;
			$doteyIds[] =  $v['uid'];
		}
		$fansCount = $weiboService ->countDoteyFans($doteyIds);
		
		// 获取主播的档期信息
		$archivesService = new ArchivesService();
		$archivesInfos = $archivesService->getArchivesByUids($doteyIds);
		foreach($archivesInfos as $k=>$v)
		{
			$doteyInfo[$v['uid']]['archives'][] = $arIds[] = $v['archives_id'];
			$doteyInfo[$v['uid']]['live_record'] = $v['live_record'];
		}
		// 获取头像
		$userService = new UserService();
		foreach($doteyInfo as $ke=>$va){
			$doteyInfo[$ke]['av'] = $userService->getUserAvatar($va['uid'], 'middle');
		}
		// 获取用户对主播的贡献值
		$consumeService = new ConsumeService();
		$dedication = $consumeService->getUserDedicationToDoteyByArchivesIds($uid, $arIds);
		
		// 排序
		foreach($doteyInfo as $key=>$val){
			foreach($val['archives'] as $ar){
				$doteyInfo[$key]['deti'] = 0;
				if(isset($dedication[$ar])){
					$doteyInfo[$key]['deti'] += $dedication[$ar]['dedi'];
				}
			}
			$doteyInfo[$key]['fans_nums'] = $fansCount[$key]['nums'];
		}
		
		$data['dotey_info'] = $doteyInfo;
		
		$this->_left('follow',$data);
	}
	
	/**
	 * 取消关注
	 */
	public function actionUndoFollow()
	{
		$ajaxReturn = array('result'=>false,'msg'=>'操作失败, 请稍后重试');
		if(Yii::app()->request->isAjaxRequest){
			$uid = $this->uid;	
			$doteyId = Yii::app()->request->getParam('dotey_id');
			$weiboService = new WeiboService();
			$res = $weiboService->cancelAttentionedUser($doteyId, $uid);
			if($res){
				$ajaxReturn['result'] = true;
				$ajaxReturn['msg'] = '操作成功';
			}
		$ajaxReturn['msg'] = $res;
		}
		exit(json_encode($ajaxReturn));
	}
	
	/**
	 * 我的关注_管理的主播
	 */
	public function actionManage()
	{
		$uid = $this->uid;
		$archivesServ = new ArchivesService();
		$userJson = new UserJsonInfoService();
		$userService = new UserService();
		$consumeService = new ConsumeService();
		$archive = $archivesServ->getPurviewLiveByUids($uid);
		$archiveIds = $archive[$uid];
		$archiveInfo = $archivesServ->getArchivesByArchivesIds($archiveIds);

		// 获取用户对主播的贡献值
		$dedication = $consumeService->getUserDedicationToDoteyByArchivesIds($uid, $archiveIds);
		// 获取主播的信息和头像
		foreach($archiveInfo as $k => $v){
			$doteyInfo[$v['uid']] = $userJson->getUserInfo($v['uid'],false);
			$doteyInfo[$v['uid']]['av'] = $userService->getUserAvatar($v['uid'], 'middle');
			$doteyInfo[$v['uid']]['deti'] = isset($dedication[$k]) ? $dedication[$k]['dedi'] : 0;
			$doteyInfo[$v['uid']]['manage_nums'] = $archivesServ->getPurviewLiveCountByArchivesId($k);
			$doteyInfo[$v['uid']]['arid'] = $k;
			$doteyInfo[$v['uid']]['status'] = $v['live_record']['status'];
		}
// 		print_r($archiveInfo);
		
		$data['dotey_info'] = $doteyInfo;
		$this->_left('manage',$data,'follow');
	}
	/**
	 * 解除房管
	 */
	public function actionUndoManage()
	{
		$ajaxReturn = array('result'=>false,'msg'=>'操作失败,请重新操作');
		if(Yii::app()->request->isAjaxRequest){
			$doteyId = Yii::app()->request->getParam('uid');
			$arId = Yii::app()->request->getParam('arid');
			$uid = $this->uid;
			$archivesServ = new ArchivesService();
			$res = $archivesServ->removeManage($uid, $doteyId, $arId,$uid);
			if($res){
				$ajaxReturn['result'] = 'true';
				$ajaxReturn['msg'] = '操作成功';
			}
		}
		exit(json_encode($ajaxReturn));
	}
	
	/**
	 * 消费记录
	 */
	public function actionConsumer()
	{
		$this->actionBuy();
	}
	
	/**
	 * 消费记录_购买
	 */
	public function actionBuy()
	{
		$uid = $this->uid;
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$data['buy_record'] = $this->getBuyRecord($uid, $page);
		
		$this->_left('buy',$data,'consumer');
	}
	
	/**
	 * 消费记录_礼物
	 */
	public function actionBuyGift()
	{
		$uid = $this->uid;
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$data['buy_record'] = $this->getBuyGiftRecord($uid, $page);
		$list = $data['buy_record']['list'];
		foreach($list as $k=>$v){
			$gifts[] = $v['gift_id'];
		}
		$giftServ = new GiftService();
		$data['giftInfo'] = $giftServ->getGiftByIds($gifts);
		
		$this->_left('buyGift',$data,'consumer');
	}
	
	public function actionNumberBuy(){
		$uid = $this->uid;
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$limit = 10;
		$offset = ($page > 1 ? $page -1 : 0) * $limit;
		$userNumberService = new UserNumberService();
		$userNumberBuyRecords = new UserNumberRecordsModel();
		$dbCriteria = $userNumberBuyRecords->getDbCriteria();
		$dbCriteria->condition = ' uid = :uid AND source = :source ';
		$dbCriteria->params = array(':uid'=>$uid,':source'=>NUMBER_BUY_SHOP);
		$dbCriteria->order = ' create_time DESC ';
		$dbCriteria->select = ' count (*) ';
		$count = $userNumberBuyRecords->count($dbCriteria);
		$dbCriteria->select = '*';
		$dbCriteria->limit = $limit;
		$dbCriteria->offset = $offset;
		$list = $userNumberBuyRecords->findAll($dbCriteria);
		$list = $userNumberService->arToArray($list);
		
		
		$data['userNumberService'] = $userNumberService;
		$data['list'] = $list;
		$page_num = ceil($count / $limit);
		$data['count'] = array('count'=>$count, 'page'=>$page, 'page_num'=>$page_num);
		$this->_left('numberBuy', $data, 'consumer');
	}
	/**
	 * 消费记录_送礼
	 */
	public function actionSend()
	{
		$uid = $this->uid;
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$data['send_record'] = $this->getSendRecord($uid, $page);
		$this->_left('send',$data,'consumer');
	}
	
	/**
	 * 消费记录_点歌
	 */
	public function actionVod()
	{
		$uid = $this->uid;
		$page = Yii::app()->request->getParam('page');
		$page = $page ? $page : 1;
		$limit = 10;
		$offset = ($page - 1 > 0 ? $page - 1 : 0 ) * $limit ;
		$songService = new DoteySongService();
		$songs = $songService->getUserRecordsByUid($uid, $offset, $limit);
		$list = array();
		$userJsonService = new UserJsonInfoService();
		if(isset($songs[$uid])){
			if(!isset($songs[$uid][0])){
				$songs[$uid] = array($songs[$uid]);
			}
			foreach($songs[$uid] as $k=>$v){
				$_info = $userJsonService->getUserInfo($v['to_uid'], false);
				if(!$_info){
					$_info = $this->userService->getUserFrontsAttributeByCondition($v['to_uid'],true);
				}
				$v['dotey_name'] = $_info['nk'];
				$list[] = $v;
			}
		}
		$data['list'] = $list;
		$songs['page'] = $page;
		$songs['page_num'] = ceil($songs['count'] / $limit);
		$data['songs'] = $songs;
		$this->_left('Vod',$data,'consumer');
	}
	
	public function actionGame()
	{
		$uid = $this->uid;
		$page = Yii::app()->request->getParam('page');
		$page = $page ? $page : 1;
		$limit = 10;
		$offset = ($page - 1 > 0 ? $page - 1 : 0 ) * $limit ;
		$diceServ = new DiceService();
		$attribute = array('type'=>DICE_RESULT_TYPE);
		$result = $diceServ->getUserDiceRecord($uid,$offset, $limit, $attribute);
		
		$data['list'] = $result['list'];
		$games['count'] = $result['count'];
		$games['page'] = $page;
		$games['page_num'] = ceil($result['count'] / $limit);
		$data['games'] = $games;
		$this->_left('game',$data,'consumer');
	}
	
	/**
	 * 消费记录_中奖
	 */
	public function actionPrize()
	{
		$this->_left('Prize',array(),'consumer');
	}

	/**
	 * 消费记录_其他
	 */
	public function actionMyother()
	{
		$this->_left('myother',array(),'consumer');
	}
	
	/**
	 * 收礼记录
	 */
	public function actionGifts()
	{
		$uid = $this->uid;
		$giftService = new GiftService();
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$limit = 10;
		$offset = ($page > 1 ? $page - 1 : 0) * $limit;
		$condition = array('recevier_type'=>1);
		$data['page_url'] = '';
		if(isset($_REQUEST['stime'])){
			$condition['start_time'] = $_REQUEST['stime'] . '00:00:00';
			$data['page_url'] .= '&stime=' . $_REQUEST['stime'];
		}
		if(isset($_REQUEST['etime'])){
			$condition['end_time'] = $_REQUEST['etime'] . '23:59:59';
			$data['page_url'] .= '&etime=' . $_REQUEST['etime'];
		}
		$consumeService = new ConsumeService();
		$consumeInfo = $consumeService->getConsumesByUids($uid);
		$stime = isset($_REQUEST['stime']) ? strtotime($condition['start_time']) : 1;
		$etime = isset($_REQUEST['etime']) ? strtotime($condition['end_time']) : time();
		$record = $consumeService->countExchangeRecord($uid, $stime, $etime, EXCHANGE_EGGPOINT);
		$data['amounts'] = $record['amounts'];
		
		$res = $giftService->getUserGiftReceiveRecordsByUid($uid, $offset, $limit, $condition, true);
		$count = $giftService->countUserGiftReceiveRecordsByUid($uid, $condition);
		$data['count']['num'] = $count['num'];
		$data['count']['points'] = $count['egg_points'];
		$data['gifts'] = $res['list'];
		$data['count']['count'] = $res['count'];
		$data['count']['page_num'] = ceil($data['count']['count'] / $limit);
		$data['count']['page'] = $page;
		$data['cinfo'] = $consumeInfo[$uid];
		
		$ids = array();
		foreach($res['list'] as $k=>$v)
		{
			if($v['target_id'] > 0){
				$ids[] = $v['target_id'];
			}
		}
		$archiveServ = new ArchivesService();
		$data['archiveInfo'] = $archiveServ->getArchivesByArchivesIds($ids);
		
		$this->_left('gifts',$data);
	}
	

	/**
	 * 主播收礼记录
	 */
	public function actionDoteygifts()
	{
		$uid = $this->uid;
		$giftService = new GiftService();
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$limit = 10;
		$offset = ($page > 1 ? $page - 1 : 0) * $limit;
		$condition = array('recevier_type'=>0);
		$data['page_url'] = '';
		$stime = Yii::app()->request->getParam('stime');
		$etime = Yii::app()->request->getParam('etime');
		if($stime){
			$condition['start_time'] = $stime . '00:00:00';
			$data['page_url'] .= '&stime=' . $stime;
		}
		if($etime){
			$condition['end_time'] = $etime . '23:59:59';
			$data['page_url'] .= '&etime=' . $etime;
		}
	
		$res = $giftService->getUserGiftReceiveRecordsByUid($uid, $offset, $limit, $condition, true);
		$count = $giftService->countUserGiftReceiveRecordsByUid($uid, $condition);
		$data['count']['num'] = $count['num'];
		$data['count']['points'] = $count['charm_points'];
		$data['gifts'] = $res['list'];
		$data['count']['count'] = $res['count'];
		$data['count']['page_num'] = ceil($data['count']['count'] / $limit);
		$data['count']['page'] = $page;
		$this->_left('doteygifts',$data);
	}
	
	/**
	 * 获赠记录
	 */
	public function actionReceive()
	{
		$this->actionGiftreceive();
	}
	
	/**
	 * 获赠记录_礼物数据
	 */
	public function actionGiftreceive()
	{
		$uid = $this->uid;
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$limit = 10;
		$offset = ($page > 1 ? $page -1 : 0) * $limit;
		$giftBagService = new GiftBagService();
		$condition = array('uid'=>$uid, 'source'=>array(BAGSOURCE_TYPE_ADMIN,BAGSOURCE_TYPE_GAME,BAGSOURCE_TYPE_AWARD));
		$giftBag = $giftBagService->getUserBagRecordsByCondition($condition, $offset, $limit);
		
		$data['getGifts'] = $giftBag;
		$page_num = ceil($giftBag['count'] / $limit);
		$data['count'] = array('count'=>$giftBag['count'],'page'=>$page,'page_num'=>$page_num);
		$data['source'] = $giftBagService->getBagSource();
		$this->_left('giftReceive',$data,'receive');
	}
	
	/**
	 * 获赠记录_道具数据
	 */
	public function actionPropsReceive()
	{
		$uid = $this->uid;
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$limit = 10;
		$offset = ($page > 1 ? $page -1 : 0) * $limit;
		$getProps = $this->getBuyRecord($uid, $page,array(1,4,5));
		$propId = array();
		foreach($getProps['list'] as $k=>$v) {
			$propId[] = $v['prop_id'];
		}
		$propsService = new PropsService();
		$getProps['prop_info'] = $propsService->getPropsByIds($propId);
		$data['getProps'] = $getProps;
		$page_num = $getProps['count'] > 0 ? ceil($getProps['count'] / $limit) : 1;
		$data['count'] = array('count'=>$getProps['count'],'page'=>$page,'page_num'=>$page_num);
		$data['source'] = $getProps['source'];
		$this->_left('propsReceive',$data,'receive');
	}
	
	/**
	 * 获赠记录_贡献值
	 */
	public function actionExperReceive()
	{
		$uid = $this->uid;
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$giftBagService = new GiftBagService();
		$limit = 10;
		$offset = ($page > 1 ? $page -1 : 0) * $limit;
		$consumeService = new ConsumeService();
		$dedication = $consumeService->getUserDedicationRecords($uid, $offset, $limit);
		
		$data['getExper'] = $dedication;
		$page_num = ceil($dedication['count'] / $limit);
		$data['count'] = array('count'=>$dedication['count'],'page'=>$page,'page_num'=>$page_num);
		$this->_left('experReceive', $data, 'receive');
	}
	
	/**
	 * 获赠记录_魅力值
	 */
	public function actionCharmReceive()
	{
		$uid = $this->uid;
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$limit = 10;
		$offset = ($page > 1 ? $page -1 : 0) * $limit;
		
		$consumeService = new ConsumeService();
		$condition = array('uid'=>$uid, 'source_arr'=>array(SOURCE_ACTIVITY,SOURCE_SENDS));
		$res = $consumeService->getCharmByCondition($condition, $offset, $limit);
		
		$data['list'] = $res['list'];
		$count = $res['count'];
		$page_num = ceil($count / $limit);
		$data['count'] = array('count'=>$count, 'page'=>$page, 'page_num'=>$page_num);
		$this->_left('charm', $data, 'receive');
	}
	
	public function actionEggReceive()
	{
		$uid = $this->uid;
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$limit = 10;
		$offset = ($page > 1 ? $page -1 : 0) * $limit;
		

		$consumeService = new ConsumeService();
		$source = array(SOURCE_GIFTS);
		$subSource = array('activity_award', 'other_recharge', 'system',SUBSOURCE_LUCK_GIFT_AWARD);
		$res = $consumeService->getPipieggsByCondition(array('uid'=>$uid,'sub_source'=>$subSource), $offset, $limit);
		
		$data['list'] = $res['list'];
		$count = $res['count'];
		$page_num = ceil($count / $limit);
		$data['count'] = array('count'=>$count, 'page'=>$page, 'page_num'=>$page_num);
		$data['source'] = $consumeService->getSourceList();
		
		$this->_left('EggReceive', $data, 'receive');
	}
	
	public function actionNumberReceive(){
		$uid = $this->uid;
		$page = $_GET['page'] ? $_GET['page'] : 1;
		$limit = 10;
		$offset = ($page > 1 ? $page -1 : 0) * $limit;
		$userNumberService = new UserNumberService();
		$userNumberBuyRecords = new UserNumberRecordsModel();
		$dbCriteria = $userNumberBuyRecords->getDbCriteria();
		$dbCriteria->condition = ' uid = :uid  ';
		$dbCriteria->params = array(':uid'=>$uid);
		$dbCriteria->addInCondition('source',array(NUMBER_BUY_ADMIN,NUMBER_BUY_SEND));
		$dbCriteria->order = ' create_time DESC ';
		$dbCriteria->select = ' count (*) ';
		$count = $userNumberBuyRecords->count($dbCriteria);
		$dbCriteria->select = '*';
		$dbCriteria->limit = $limit;
		$dbCriteria->offset = $offset;
		$list = $userNumberBuyRecords->findAll($dbCriteria);
		$list = $userNumberService->arToArray($list);
		
		
		$data['userNumberService'] = $userNumberService;
		$data['list'] = $list;
		$page_num = ceil($count / $limit);
		$data['count'] = array('count'=>$count, 'page'=>$page, 'page_num'=>$page_num);
		$this->_left('numberReceive', $data, 'receive');
	}
	
	/**
	 * 我的家族
	 * 暂时不做
	 */
	public function actionFamily()
	{
		$this->_left('family');
	}
	
	/**
	 * 虚拟币兑换
	 */
	public function actionExchange()
	{
		$uid = $this->uid;
		$consumeService = new ConsumeService();
		$cash = $consumeService->getConsumesByUids(array($uid));
		
		$limit = 3;
		$list = $consumeService->getExchangeEggRecord($uid, $limit);
		
		$data['consume'] = $cash[$uid];
		$data['exchange_list'] = $list;
		
		$this->_left('exchange',$data);
	}
	
	/**
	 * 执行虚拟币兑换
	 */
	public function actionDoExchange()
	{
		$ajaxReturn = array('result'=>false,'msg'=>'操作失败,请重新操作');
		//若设置了Yii::app()->params['change_relation']['eggpoints_to_pipiegg']，则兑换时乘上此比例
		$eggpoints_to_pipiegg=Yii::app()->params['change_relation']['eggpoints_to_pipiegg'];
		$charmpoints_to_pipiegg=Yii::app()->params['change_relation']['charmpoints_to_pipiegg'];
		
		if(Yii::app()->request->isAjaxRequest){
			$uid = $this->uid;
			$exchange = (int)Yii::app()->request->getParam('exchange');
			$exchange = ((int)($exchange/100))*100;
			$consumeService = new ConsumeService();
			
			$myConsume = $consumeService->getConsumesByUids(array($uid));
			$ep = $myConsume[$uid]['egg_points'];
			$cp = $myConsume[$uid]['charm_points'];
			$_exchange = $cp + $ep;
			if($_exchange < $exchange || $_exchange <= 0 || $exchange <= 0){
				$ajaxReturn['msg'] = '您的皮点不足，无法兑换成功!';
				exit(json_encode($ajaxReturn));
			}
			
			// @todo 兑换皮点和魅力点
			$exchangeRes = $consumeService->exchangeEggPointCharmPoint($uid, $exchange);

			if($exchangeRes==1){

				$consumeAttriute = array('uid'=>$uid, 'pipiegg'=>($exchange*$eggpoints_to_pipiegg), 'egg_points'=>0, 'charm_points'=>0);
				$res = $consumeService->appendConsumeData($consumeAttriute);
				
				// 写用户的皮蛋记录 
				$records = array();
				$records['uid'] = $uid;
				$records['pipiegg'] = ($exchange*$eggpoints_to_pipiegg);
				$records['from_target_id'] = 1;
				$records['num'] = 1;
				$records['to_target_id'] = $uid;
				$records['source']=SOURCE_EXCHANGE;
				$records['sub_source']=SUBSOURCE_EXCHANGE_EGG;
				$records['client'] = CLIENT_EXCHANGE;
				$records['extra']='兑换皮蛋 '.($exchange*$eggpoints_to_pipiegg);
				$consumeService->saveUserPipiEggRecords($records,true);
				
				$time = time();
				if($ep > 0){
					if($ep >= $exchange){
						// 兑换值全部都是EP, 只记录EP兑换记录
						$epRecord = array('uid'=>$uid,'client'=>CLIENT_EXCHANGE,'source'=>SOURCE_EXCHANGE,'sub_source'=>SUBSOURCE_EXCHANGE_EGG,'egg_points'=>$exchange,'info'=>'皮点兑换皮蛋','create_time'=>$time);
						$consumeService->saveUserEggPointsRecords($epRecord, 0);
						$exchangeRecord = array('uid'=>$uid,'ex_type'=>EXCHANGE_EGGPOINT,'handle_type'=>1,'info'=>'兑换皮蛋','org_amount'=>$exchange,'dst_amount'=>($exchange*$eggpoints_to_pipiegg),'create_time'=>$time);
						$consumeService->saveExchangeCharmPoint($exchangeRecord);
					}else{
						
						// EP和CP使用记录
						$epRecord = array('uid'=>$uid,'client'=>CLIENT_EXCHANGE,'source'=>SOURCE_EXCHANGE,'sub_source'=>SUBSOURCE_EXCHANGE_EGG,'egg_points'=>$ep,'info'=>'皮点兑换皮蛋','create_time'=>$time);
						$res1 = $consumeService->saveUserEggPointsRecords($epRecord, 0);
						$cpRecord = array('uid'=>$uid,'client'=>CLIENT_EXCHANGE,'source'=>SOURCE_EXCHANGE,'sub_source'=>SUBSOURCE_EXCHANGE_EGG,'charm_points'=>($exchange-$ep),'info'=>'魅力点兑换皮蛋','create_time'=>$time);
						$res2 = $consumeService->saveDoteyCharmPointsRecords($cpRecord,0);
						// 兑换EP和CP记录
						$exchangeRecord = array('uid'=>$uid,'ex_type'=>EXCHANGE_EGGPOINT,'handle_type'=>1,'info'=>'兑换皮蛋','org_amount'=>$ep,'dst_amount'=>($ep *$eggpoints_to_pipiegg),'create_time'=>$time);
						$res3 = $consumeService->saveExchangeCharmPoint($exchangeRecord);
						$exchangeRecord = array('uid'=>$uid,'ex_type'=>EXCHANGE_CHARMPOINT,'handle_type'=>1,'info'=>'兑换皮蛋','org_amount'=>($exchange-$ep),'dst_amount'=>(($exchange-$ep) *$charmpoints_to_pipiegg),'create_time'=>$time);
						$res4 = $consumeService->saveExchangeCharmPoint($exchangeRecord);
					}
				}else{
					// 兑换值全部都是CP, 只记录CP兑换记录
					$cpRecord = array('uid'=>$uid,'client'=>CLIENT_EXCHANGE,'source'=>SOURCE_EXCHANGE,'sub_source'=>SUBSOURCE_EXCHANGE_EGG,'charm_points'=>$exchange,'info'=>'魅力点兑换皮蛋','create_time'=>$time);
					$consumeService->saveDoteyCharmPointsRecords($cpRecord,0);
					$exchangeRecord = array('uid'=>$uid,'ex_type'=>EXCHANGE_CHARMPOINT,'handle_type'=>1,'info'=>'兑换皮蛋','org_amount'=>$exchange,'dst_amount'=>($exchange* $charmpoints_to_pipiegg),'create_time'=>$time);
					$consumeService->saveExchangeCharmPoint($exchangeRecord);
				}
				$ajaxReturn['result'] = true;
				$ajaxReturn['msg'] = '操作成功!';
				exit(json_encode($ajaxReturn));
			}else{
				$ajaxReturn['msg'] = '兑换失败';
				exit(json_encode($ajaxReturn));
			}
		}
		exit(json_encode($ajaxReturn));
	}
	
	/**
	 * 主播资料
	 */
	public function actionDotey()
	{
		$uid = $this->uid;
		$userService = $this->userService;
		
		$archivesService = new ArchivesService();
		$doteyService = new DoteyService();
// 		$archivesInfo = $archivesService->getArchivesBycondition(array('uid'=>$uid));
		$archivesInfo = $archivesService->getArchivesByUids(array('uid'=>$uid));
		$acount_extend_info = $this->userService->getUserExtendByUids(array($uid));

		$data['doteyInfo'] = $doteyService->getDoteyInfoByUid($uid);
		$keys = array_keys($archivesInfo);
		$data['archivesInfo'] = $archivesInfo[$keys[0]];
		$data['acount_extend_info'] = isset($acount_extend_info[$uid]) ? $acount_extend_info[$uid] : array();
		$data['dotey_info'] = array_merge($data['doteyInfo'],$data['archivesInfo'],$data['acount_extend_info']);
		$this->_left('dotey',$data);
	}
	
	public function actionEditdotey()
	{
		$uid = $this->uid;
		
		if(Yii::app()->request->isAjaxRequest) {
			$ajaxReturn = array('result'=>false,'msg'=>'操作失败,请重新操作');
			// 修改真实姓名
			$realname = Yii::app()->request->getParam('realname');
			if($realname) {
				$res = $this->userService->saveUserBasic(array('uid'=>$uid,'realname'=>$realname));
				if($res['realname']!=$realname){
					$ajaxReturn['msg'] = '修改失败, 请稍后再试';
					exit(json_encode($ajaxReturn));
				}
			}
			
			// 修改档期
			$archives_id = Yii::app()->request->getParam('archives_id');
			$dotey_title = Yii::app()->request->getParam('dotey_title');
			$sub_id = Yii::app()->request->getParam('sub_id');
			$sub_title = Yii::app()->request->getParam('sub_title');
			$archiveService = new ArchivesService();
			$archives = array('archives_id'=>$archives_id);
			if($dotey_title){
				$archives['title'] = $dotey_title;
				$res = $archiveService->saveArchives($archives);
				if($res!=$archives_id){
					$ajaxReturn['msg'] = '修改失败, 请稍后再试!';
					exit(json_encode($ajaxReturn));
				}
			}
			if($sub_id){
				$record = array('record_id'=>$sub_id, 'sub_title'=>$sub_title);
				$res = $archiveService->saveArchivesLiveRecords($record);
				if($res!=$sub_id){
					$ajaxReturn['msg'] = '修改失败, 请稍后再试!';
					exit(json_encode($ajaxReturn));
				}
			}
			
			// 修改基础资料
			$birthday = Yii::app()->request->getParam('birthday');
			$province = Yii::app()->request->getParam('province');
			$city = Yii::app()->request->getParam('city');
			$profession = Yii::app()->request->getParam('profession');
			$description = Yii::app()->request->getParam('description');
			if($province=='选择省份'){
				$province = '';
			}
			if($city=='选择城市'){
				$city = '';
			}
			$userExtend = array(
					'uid'=>$uid,
					'birthday'=>strtotime($birthday),
					'province'=>$province,
					'city'=>$city,
					'profession'=>$profession,
					'description'=>$description,
				);
			$res = $this->userService->saveUserExtend($userExtend);
			if($res){
				$ajaxReturn['result'] = true;
				$ajaxReturn['msg'] = '修改成功';
				exit(json_encode($ajaxReturn));
			}
			
			exit(json_encode($ajaxReturn));
		}
	}
	/**
	 * 我的收入
	 */
	public function actionIncome()
	{
		// 汇款设置
		$uid = $this->uid;
		if(Yii::app()->request->isPostRequest){
			$userBasic['uid'] = $uid;
			$userBasic['realname'] = Yii::app()->request->getParam('realname'); 
			$res1 = $this->userService->saveUserBasic($userBasic);
			$userExtend['uid'] = $uid;
			$userExtend['id_card'] = Yii::app()->request->getParam('id_card'); 
			$userExtend['bank_account'] = Yii::app()->request->getParam('bank_account'); 
			$userExtend['bank'] = Yii::app()->request->getParam('bank'); 
			$userExtend['bank_user'] = Yii::app()->request->getParam('bank_user');
			$userExtend['mobile'] = Yii::app()->request->getParam('mobile'); 
			$userExtend['qq'] = Yii::app()->request->getParam('qq'); 
			$res2 =  $this->userService->saveUserExtend($userExtend);
			if($res1 && $res2){
				$data['edit_result'] = '<p><em>资料修改成功</em></p>';
			}
		}
		$userExtend = $this->userService->getUserExtendByUids(array($uid));
		$userBasic = $this->userService->getUserBasicByUids(array($uid));
		$data['userExtend'] = $userExtend[$uid];
		$data['userBasic'] = $userBasic[$uid];
		$this->_left('income',$data);
	}
	
	/**
	 * 我的收入_直播时长
	 */
	public function actionLivetime()
	{
		$uid = $this->uid;
		$archiveService = new ArchivesService();
		$archives = $archiveService->getArchivesBycondition(array('uid'=>$uid));
		$archivesIds = array_keys($archives);
		$days = $archiveService->getLiveEffectDaysUnit(array($uid));
		$days = $days[$uid];
		$month = array();
		for($i = 0; $i<5; $i++){
			$_time = date('Y-m',mktime(0,0,0,date('m') - $i,1,date('Y')));
// 			$month[$i-1]['month'] = $_time;
			$condition['start_time'] = strtotime($_time);
			$condition['end_time'] =  strtotime(date('Y-m',mktime(0,0,0,date('m') - $i +1,1,date('Y'))));
			$res = $archiveService->getLiveRecordsByMonth($archivesIds, $condition);
			// 统计
			$month[] = $this->countLiveRecord($res,$_time,$days);
		}
		$data['month'] =  $month;
		$this->_left('liveTime',$data,'income');
	} 
	
	public function countLiveRecord($data,$month,$_time = 2)
	{
		$_dedi = 0;
		$days = array();
		$_days = 0;
		$result = array();
		$_time = $_time * 3600;			// 天数换算成小时
		foreach($data as $k=>$v){
			$_dedi += $v['duration'];	// 总数
			$day = date('Y-m-d',$v['live_time']);
			if($_days == $day){
				$days[$_days]['dedi'] += $v['duration'];
			}else{
				$_days = $day;
				$days[$_days]['day'] = $day;
				$days[$_days]['dedi'] = $v['duration'];
			}
		}
		$eval_day = 0;
		foreach($days as $d){
			if($d['dedi']>=$_time){
				$eval_day++;
			}
		}
		$hour = floor($_dedi / 3600);
		$min = floor(($_dedi / 3600 - $hour)*60);
		$result['count'] = $_dedi;
		$result['dedi'] = $hour.'小时'.$min.'分钟';
		$result['day'] = $eval_day;
		$result['month'] =  $month;
		$result['moon'] = date('n',strtotime($month));
		$result['hour'] = $hour;
		return $result;
	}
	
	public function actionLiveList()
	{
		$uid = $this->uid;
		$page = Yii::app()->request->getParam('page');
		$page = $page ? $page : 1;
		$limit = 10;
		$offset = ($page - 1 > 0 ? $page - 1 : 0 ) * $limit ;
		$month = Yii::app()->request->getParam('month');
		if($month){
			$data['page_url'] .= '&month=' . $month;
		}
		$getTime = strtotime($month);
		$start_time = mktime(0,0,0,date('m',$getTime),1,date('Y',$getTime));
		$end_time = mktime(0,0,0,date('m',$getTime)+1,1,date('Y',$getTime));
		if($month){
			$condition = array('start_time'=>$start_time,'end_time'=>$end_time);
		}
		
		$archiveService = new ArchivesService();
		$archives = $archiveService->getArchivesBycondition(array('uid'=>$uid));
		$archivesIds = array_keys($archives);
		$liveTime = $archiveService->getLiveRecordByCondition($archivesIds,$condition, $offset, $limit);
		foreach($liveTime['list'] as &$v){
			$hour = floor($v['duration'] / 3600);
			$min = floor(($v['duration'] / 3600 - $hour)*60);
			$v['duration'] = $hour.'小时'.$min.'分钟';
		}
		$data['liveList'] = $liveTime['list'];
		
		// 统计总数
		$days = $archiveService->getLiveEffectDaysUnit(array($uid));
		$days = $days[$uid];
		$record = $archiveService->getLiveRecordsByMonth($archivesIds, $condition);
		$data['countRecord'] = $this->countLiveRecord($record,$month,$days);
		
		$count = $liveTime['count'];
		$page_num = ceil($count/$limit);
		$data['liveCount'] = array('count'=>$count,'page'=>$page,'page_num'=>$page_num);
		$this->_left('liveList',$data,'income');
	}
	
	/**
	 * 我的收入_魅力提现
	 */
	public function actionCash()
	{
		$uid = $this->uid;
		$consumeService = new ConsumeService();
		$cash = $consumeService->getConsumesByUids(array($uid));
		// 获取兑换记录
		$limit = 5;
		$condition = array('ex_type'=>EXCHANGE_MONEY);
		$list = $consumeService->getExchangeRecord(array($uid),$condition,$limit);
		// 获取收入本月月
		$stime = mktime(0,0,0,date('m'),1,date('Y'));
		$etime = mktime(0,0,0,date('m') + 1,1,date('Y'));
		// 本月收入   
		$month = $consumeService->getMonthDoteyCharmPoints(array($uid),array('monthTime'=>(date('Y-m',$stime))),true);
		// 本月兑换
		//$exchange = $consumeService->countExchangeRecord($uid,$stime,$etime,array(EXCHANGE_MONEY,EXCHANGE_CHARMPOINT));
		$exchange = $consumeService->countExchangeRecord($uid,$stime,$etime,EXCHANGE_MONEY);
		// 现在剩余
		$now_cash = $cash[$uid]['charm_points'] -0;
		$now_exchange = $exchange['amounts'] - 0;
		$now_income = $month[$uid]['points'] - 0; 
		$data['exchange_count'] = array('now_cash'=>$now_cash, 'now_exchange'=>$now_exchange, 'now_income'=>$now_income);
		
		// 获取兑现参数
		$data['exchange_value'] = $this->getDoteyCashConfig($uid);
		
		$data['cash_list'] = $list;
		$data['month_count'] = $exchange;
		$this->_left('cash',$data,'cash');
	}
	
	public function getDoteyCashConfig($uid)
	{
		$doteyService = new DoteyService();
		return $doteyService->getDoteyCashConfig($uid);
	}
	
	/**
	 * 魅力提现操作
	 */
	public function actionDocash()
	{
		$ajaxReturn = array('result'=>false,'msg'=>'非法操作, 请登录之后重新操作');
		if(Yii::app()->request->isAjaxRequest){
			$day = date('d',time());
			if($day <= 7){
				$ajaxReturn['msg'] = '每月1至7日为出账冻结期，不可兑换。';
				exit(json_encode($ajaxReturn));
			}
			$uid = $this->uid;
			$meili = Yii::app()->request->getParam('meili');
			if($meili<=0)
			{
				$ajaxReturn['msg'] = '请输入正确的金额';
				exit(json_encode($ajaxReturn));
			}
			
			$charmpoint=(int)ceil($meili/$this->getDoteyCashConfig($uid));
			$consumeService = new ConsumeService();
			
			$myConsume = $consumeService->getConsumesByUids(array($uid));
			$charm_points = $myConsume[$uid]['charm_points'];
			if($charm_points < $charmpoint || $charmpoint < 0){
				$ajaxReturn['msg'] = '魅力点不足';
				exit(json_encode($ajaxReturn));
			}
			
			$res = $consumeService->exchangeCharmPoint($uid, $charmpoint);
			if($res==1){
				
				$consumeAttriute = array('uid'=>$uid, 'charm_points'=>0);
				$res = $consumeService->appendConsumeData($consumeAttriute);
				
				$charmPoint = array();
				$charmPoint['uid'] = $uid;
				$charmPoint['charm_points'] = $charmpoint;
				$charmPoint['sender_uid'] = $uid;
				$charmPoint['target_id'] = $charmPoint['record_sid'] = $charmPoint['num'] = $charmPoint['client'] =  $charmPoint['info'] = 0;
				$charmPoint['source'] = SOURCE_EXCHANGE;
				$charmPoint['sub_source'] = SUBSOURCE_EXCHANGE_MONEY;
				$consumeService->saveDoteyCharmPointsRecords($charmPoint,0);
				
				$exchange = array();
				$exchange['uid'] = $uid;
				$exchange['ex_type'] = EXCHANGE_MONEY; 
				$exchange['handle_type'] = 1; 
				$exchange['org_amount'] = $charmpoint;
				$exchange['dst_amount'] = $meili;
				$exchange['create_time'] = time();
				$consumeService->saveExchangeCharmPoint($exchange);
				
				$ajaxReturn['result'] = true;
				$ajaxReturn['msg'] = '操作成功!';
				exit(json_encode($ajaxReturn));
			}else{
				$ajaxReturn['msg'] = '操作失败, 请稍后重试!';
				exit(json_encode($ajaxReturn));
			}
		}
		exit(json_encode($ajaxReturn));
	}
	/**
	 * 收入账单
	 */
	public function actionBillings()
	{
		$uid = $this->uid;
		// 获取直播时长, 有效天数, 魅力点, ==>底薪, 奖金
		$archiveService = new ArchivesService();
		$archives = $archiveService->getArchivesBycondition(array('uid'=>$uid));
		$archivesIds = array_keys($archives);
		
		$doteyServ = new DoteyService();
		$doteyInfo = $doteyServ->getDoteyInfoByUid($uid);
// 		print_r($doteyInfo);
		
		$type = $doteyInfo['sign_type'];
		
		$consumeService = new ConsumeService();
		
// 		$pay = $consumeService->getDoteyPay($uid, $type);
		$days = $archiveService->getLiveEffectDaysUnit(array($uid));
		$month = array();
		for($i = 0; $i<5; $i++){
			$_time = date('Y-m',mktime(0,0,0,date('m') - $i,1,date('Y')));
			$condition['start_time'] = $stime = strtotime($_time);
			$condition['end_time'] = $etime = strtotime(date('Y-m',mktime(0,0,0,date('m') - $i +1,1,date('Y'))));
			$res = $archiveService->getLiveRecordsByMonth($archivesIds, $condition);
			// 魅力提现
			$_data = $this->countLiveRecord($res,$_time,$days[$uid]);
			$count = $consumeService->countExchangeRecord($uid,$stime,$etime,EXCHANGE_MONEY);
			$_data['exchange_money'] = $count['money'] ? $count['money'] : 0;
			// 获取本月获得的魅力点
			$new_condition = array('stime'=>$stime,'etime'=>$etime);
			$lastMonth = $consumeService->getDoteyCharmPointsRecords(array($uid),$new_condition);
			$charm_points = $lastMonth ? $lastMonth['points'] : 0;
			// 获取工资发放标准
			$pay = $consumeService->getAllowDoteyPay($uid,$type,$_data['hour'],$_data['day'],$charm_points);
			$pay = array_pop($pay);
			$_data['pay'] = $pay ? array('salary'=>$pay['basic_salary'],'bonus'=>$pay['bonus']) : array('salary'=>0, 'bonus'=>0);
			
			// 平台奖励
			$count_money = $consumeService->countExchangeRecord($uid,$stime,$etime,EXCHANGE_ADMIN);
			$_data['exchange_admin'] = $count_money['money'] >0 ? $count_money['money'] : 0;
			// 才艺补贴
			$count_money = $consumeService->countExchangeRecord($uid,$stime,$etime,EXCHANGE_ART);
			$_data['exchange_art'] = $count_money['money'] >0 ? $count_money['money'] : 0;
			
			
			$month[] = $_data;
		}
		
		
		
		
		$data['month'] = $month;
		$this->_left('billings',$data,'income');
	}
	
	public function actionDoteyNotice()
	{
		$bbsServ = new BbsbaseService();
		$conditions = array(
				'forum_name'=>OPERATORS_CMS_DOTEYPOLICY_FORUMNAME,
				'forum_sname'=>OPERATORS_CMS_DOTEYPOLICY_FORUMNAME,);
		$forum = $bbsServ->getFormByConditions($conditions);
		$forum_sid = $forum ? $forum[0]['forum_sid'] : 0;
		$data = $bbsServ->getThreadList($forum_sid,1,5);
		foreach($data['list'] as $k=>$v){
			$_tmp = $bbsServ->getPostList($v['thread_id'],1,1);
			$data['list'][$k]['text'] = $_tmp[0]['content'];
		}
		$this->_left('doteyNotice',$data,'doteyNotice');
	}
	
	public function checkPay($live_time = 0, $live_day = 0, $charm_points = 0)
	{
		$result = array('salary'=>0,'bonus' => 0);
		if($this->normal){
			$normal = $this->normal;
		}else{
			$consumeService = $this->getConsumeService();
			$normal = $this->normal = $consumeService->getPayNormal();					// 获取工资发放标准
		}
		foreach($normal as $v){
			$time_key = ($live_time >= $v['live_times']) ? 1 : 0;
			$day_key = ($live_time >= $v['live_times']) ? 1 : 0;
			$point_key = ($live_time >= $v['live_times']) ? 1 : 0;
			if($time_key > 0 && $day_key>0 && $point_key > 0){
				$result['salary'] = $v['basic_salary'];
				$result['bonus'] = $v['bonus'];
				return $result;
				break;
			}
		}
		return $result;
	}
	
	/**
	 * 我的粉丝
	 */
	public function actionFans()
	{		
		$uid = $this->uid;
		$weiboService = new  WeiboService();
		$page = Yii::app()->request->getParam('page');
		$page = $page ? $page : 1;
		$limit = 10;
		$offset = ($page - 1 > 0 ? $page - 1 : 0 ) * $limit ;
		
		$res = $weiboService->countDoteyCharmPointsBuSendUid($uid, $offset, $limit);
		$count = $res['count'];
		$list = $res['list'];
		$page_num = ceil($count/$limit);
		
		$userJsonService = new UserJsonInfoService();
		foreach($list as $k=>$v){
			$uids[] = $v['sender_uid'];
			$_res = $userJsonService->getUserInfo($v['sender_uid'],false);
			if(!$_res){
				$_res = $this->userService->getUserFrontsAttributeByCondition($v['sender_uid'],true);
			}
			$list[$k]['userInfo'] = $_res;
		}
		$data['fans_list'] = $list;
		$data['count'] = array('page'=>$page,'count'=>$count,'page_num'=>$page_num);
		$this->_left('fans',$data);
	}
	/**
	 * 我的房管
	 */
	public function actionManager()
	{
		$uid = $this->uid;
		$archiveService= new ArchivesService();
		$userJson = new UserJsonInfoService();
		$archive = $archiveService->getArchivesBycondition(array('uid'=>$uid));
		$archiveIds = array_keys($archive);
		$manager = $archiveService->getPurviewLiveByArchivesIds($archiveIds);
		foreach($archiveIds as $v){
			foreach($manager[$v] as $m){
				$res = $userJson->getUserInfo($m,false);
				if(!$res){
					$res = $this->userService->getUserFrontsAttributeByCondition($m,true);
				}
				$userInfos[$m] = $res;
				$userInfos[$m]['archives_id'] = $v; 
			}
		}
		$dotey = $userJson->getUserInfo($uid,false);
		$common = new ConsumeService();
		$rank = $common->getDoteyRankFromRedis();
		$data['managers_nums'] = $rank[$dotey['dk']]['house_m_num'];
		$data['next_managers_nums'] = isset($rank[($dotey['dk']+1)]) ? $rank[($dotey['dk']+1)]['house_m_num'] : 9999;
		
		$data['managers'] = $userInfos; 
		$this->_left('manager',$data);
	}
	/**
	 * 撤销房管
	 */
	public function actionUndoManager()
	{
		$ajaxReturn = array('result'=>false, 'msg'=>'操作失败, 请重新操作');
		if(Yii::app()->request->isAjaxRequest){
			$uid = Yii::app()->request->getParam('uid');
			$doteyId = $this->uid;
			$archivesId = Yii::app()->request->getParam('aid');
			if($uid>0 && $archivesId>0){
				$archivesService = new ArchivesService();
				$res = $archivesService->removeManage($uid, $doteyId, $archivesId);
				if($res) {
					$ajaxReturn['result'] = true;
					$ajaxReturn['msg'] = '操作成功';
				}
			}
		}
		exit(json_encode($ajaxReturn));
	}
	
	/**
	 * 我的守护
	 */
	public function actionMyguard()
	{
		$this->_left('myguard');
	}
	
	public function actionSong()
	{
		$uid = $this->uid;
		$page = Yii::app()->request->getParam('page');
		$page = $page ? $page : 1;
		$limit = 10;
		$offset = ($page - 1 > 0 ? $page - 1 : 0 ) * $limit ;
		$data['page_url'] = '';
		$stime = Yii::app()->request->getParam('stime');
		$etime = Yii::app()->request->getParam('etime');
		if($stime){
			$condition['start_time'] = strtotime($stime);
			$data['page_url'] .= '&stime=' . $stime;
		}
		if($etime){
			$condition['end_time'] = strtotime($etime . ' 23:59:59');
			$data['page_url'] .= '&etime=' . $etime;
		}
// 		$status = Yii::app()->request->getParam('is_handle');
		
		$songService = new DoteySongService();
		$songs = $songService->getUserSongRecordsByDoteyId($uid, $offset, $limit,$condition);
		$userJsonService = new UserJsonInfoService();
		foreach($songs['list'] as $k=>$v){
			$_info = $userJsonService->getUserInfo($v['uid'], false);
			if(!$_info){
				$_info = $this->userService->getUserFrontsAttributeByCondition($v['uid'],true);
			}
			$songs['list'][$k]['userName'] = $_info['nk'];
		}
		// 获取本月点歌统计
		$bgtime = date('Y-m-') . '01 00:00:00';
		$res = $songService->countDoteyMonthSong($uid, $bgtime);
		if($res){
			$data['song_count'] = $res;
		}else{
			$data['song_count'] = array('nums'=>0, 'charm_points'=>0);
		}
		
		$songs['page'] = $page;
		$songs['page_num'] = ceil($songs['count'] / $limit);
		$data['songs'] = $songs;
		$this->_left('song',$data);
	}
	
	
	public function actionSecurity(){
		$pramas = array();
		$type = Yii::app()->request->getParam('type');
		$step = Yii::app()->request->getParam('step');
		$pramas['type'] = $type ? $type : '' ;
		$pramas['step'] = $step ? $step : '' ;
		$userService = new UserService();
		$userBindService = new UserBindService();
		$user = $userService->getUserBasicByUids(array($this->uid));
		$user = $user[$this->uid];
		
		$pramas['email'] = $user['reg_email'];
		$pramas['mobile'] = isset($user['reg_mobile']) ? $user['reg_mobile'] : '';
		
		if($pramas['email']){
			$emailInfo = explode('@',$pramas['email']);
			$emailLen = strlen($emailInfo[0]);
			if($emailLen >= 4){
				$pramas['protected_email'] = substr($emailInfo[0],0,3).'***'.$emailInfo[0][$emailLen-1].'@'.$emailInfo[1];
			}else{
				$pramas['protected_email'] = $emailInfo[0][0].'***@'.$emailInfo[1];
			}
		}
		
		if($pramas['mobile']){
			$mobileLen = strlen($pramas['mobile']);
			if($mobileLen == 11){
				$pramas['proteted_mobile'] = substr($pramas['mobile'],0,3).'****'.substr($pramas['mobile'],7,$mobileLen-7);
			}else{
				$pramas['proteted_mobile'] = $pramas['mobile'];
			}
		}
		if($type == 'mail'){
		
			if($step == 'doBindSendVerify'){
				$email = Yii::app()->request->getParam('email');
				$response = array();
				if($email){
					if($user['reg_email'] && preg_match("/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/", $user['reg_email'])){
						echo json_encode(array('status'=>'fail','info'=>'您已绑定过邮箱'));
						Yii::app()->end();
					}
					if(preg_match("/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/", $email) <= 0){
						echo json_encode(array('status'=>'fail','info'=>'邮箱格式不正确'));
						Yii::app()->end();
					}
					
					$userBasicModel = new UserBasicModel();
					$userByMail = $userBasicModel->findByAttributes(array('reg_email'=>$email));
					if($userByMail){
						echo json_encode(array('status'=>'fail','info'=>'邮箱已被绑定'));
						Yii::app()->end();
					}
					$userBind['uid'] = $this->uid;
					$userBind['method'] = BIND_TYPE_MAIL;
					$userBind['method_content'] = $email;
					$existBind = $userBindService->getValidBindByUid($this->uid,BIND_TYPE_MAIL,$email);
					
					if(!$existBind){
						$bindId = $userBindService->saveUserBind($userBind);
					}else{
						$bindId = $existBind['bind_id'];
						$userBindModel = UserBindModel::model();
						$userBindModel->updateByPk($bindId,array('create_time'=>time()));
						
					}
					
					if($bindId){
						$userTicket['uid'] = $this->uid;
						$userTicket['bind_id'] = $bindId;
						$userTicket['type'] = BIND_TYPE_MAIL;
						if($userBindService->saveUserTicket($userTicket)){
							$return = $userBindService->sendBindMail($this->uid,$email);
							echo json_encode(array('status'=>'scuccess','info'=>$email));
							Yii::app()->end();
						}else{
							echo json_encode(array('status'=>'fail','info'=>'绑定出现错误'));
							Yii::app()->end();
						}
					}
					Yii::app()->end();
				}
			}elseif($step == 'send'){
				if($user['reg_email'] && preg_match("/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/", $user['reg_email'])){
					$this->redirect($this->createUrl('account/security&type=public&message=您已绑定过邮箱'));
					Yii::app()->end();
				}
				
				$userBind = $userBindService->getNewBindByUid($this->uid,BIND_TYPE_MAIL);
				
				if(empty($userBind)){
					$this->redirect($this->createUrl('account/security&type=public&message=绑定出现错误'));
					Yii::app()->end();
				}
				$pramas['bind_email'] = $userBind['method_content'];
				$emailInfo = explode('@',$pramas['bind_email']);
				$mailList = $userBindService->getMailList();
				$pramas['mail_href'] = isset($mailList[strtolower($emailInfo[1])]) ? $mailList[$emailInfo[1]] : 'http://mail.'.$emailInfo[1];
				
				
			}elseif($step == 'verify'){
				if($user['reg_email'] && preg_match("/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/", $user['reg_email'])){
					$this->redirect($this->createUrl('account/security&type=public&message=您已绑定过邮箱'));
					Yii::app()->end();
				}
				$userBind = $userBindService->getNewBindByUid($this->uid,BIND_TYPE_MAIL);
				
				if(empty($userBind)){
					$this->redirect($this->createUrl('account/security&type=public&message=绑定出现错误'));
					Yii::app()->end();
				}
				$pramas['bind_email'] = $userBind['method_content'];
				
				$ticket = Yii::app()->request->getParam('ticket');
				$validTicket = $userBindService->getValidTicketByUid($this->uid,BIND_TYPE_MAIL);
				
				if(empty($validTicket) || $validTicket['is_used']){
					$pramas['verify'] = 0;
				}elseif($validTicket['ticket'] != $ticket || $validTicket['create_time'] < time()-7*3600*24){
					$pramas['verify'] = 0;
				}else{
					
					$userTicketModel = new UserTicketModel();
					$userTicketModel->updateByPk($validTicket['pass_id'],array('is_used'=>1));
					$userService->saveUserBasic(array('uid'=>$this->uid,'reg_email'=>$pramas['bind_email']));
					$pramas['verify'] = 1;
				}
			}
		}elseif($type == 'unMail'){
			if($step == 'unBind'){
				
			}elseif($step == 'doUnBindSendVerify'){
				if(!$user['reg_email'] ||  !preg_match("/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/", $user['reg_email'])){
					echo json_encode(array('status'=>'fail','info'=>'您没有绑定过安全邮箱'));
					Yii::app()->end();
				}
				$email = $user['reg_email'];
				$userBind['uid'] = $this->uid;
				$userBind['method'] = BIND_TYPE_UNMAIL;
				$userBind['method_content'] = $email;
				$existBind = $userBindService->getValidBindByUid($this->uid,BIND_TYPE_UNMAIL,$email);
				if(!$existBind){
					$bindId = $userBindService->saveUserBind($userBind);
				}else{
					$bindId = $existBind['bind_id'];
					$userBindModel = UserBindModel::model();
					$userBindModel->updateByPk($bindId,array('create_time'=>time()));
					
				}
				
				if($bindId){
					$userTicket['uid'] = $this->uid;
					$userTicket['bind_id'] = $bindId;
					$userTicket['type'] = BIND_TYPE_UNMAIL;
					if($userBindService->saveUserTicket($userTicket)){
						$return = $userBindService->sendUnBindMail($this->uid,$email);
						echo json_encode(array('status'=>'scuccess','info'=>$email));
						Yii::app()->end();
					}else{
						echo json_encode(array('status'=>'fail','info'=>'绑定出现错误'));
						Yii::app()->end();
					}
				}
				Yii::app()->end();
			}elseif($step == 'send'){
				if(!$user['reg_email'] ||  !preg_match("/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/", $user['reg_email'])){
					$this->redirect($this->createUrl('account/security&type=public&message=您没有绑定过安全邮箱'));
					Yii::app()->end();
				}
				
				$userBind = $userBindService->getNewBindByUid($this->uid,BIND_TYPE_UNMAIL);
				if(empty($userBind)){
					$this->redirect($this->createUrl('account/security&type=public&message=绑定出现错误'));
					Yii::app()->end();
				}
				if($user['reg_email'] != $userBind['method_content']){
					$this->redirect($this->createUrl('account/security&type=public&message=绑定出现错误'));
					Yii::app()->end();
				}
				$emailInfo = explode('@',$user['reg_email']);
				$mailList = $userBindService->getMailList();
				$pramas['mail_href'] = isset($mailList[strtolower($emailInfo[1])]) ? $mailList[$emailInfo[1]] : 'http://mail.'.$emailInfo[1];
			}elseif($step == 'verify'){
				if(!$user['reg_email'] ||  !preg_match("/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/", $user['reg_email'])){
					$this->redirect($this->createUrl('account/security&type=public&message=您没有绑定过安全邮箱'));
					Yii::app()->end();
				}
				
				$userBind = $userBindService->getNewBindByUid($this->uid,BIND_TYPE_UNMAIL);
				
				if(empty($userBind)){
					$this->redirect($this->createUrl('account/security&type=public&message=绑定出现错误'));
					Yii::app()->end();
				}
				
				$ticket = Yii::app()->request->getParam('ticket');
				$validTicket = $userBindService->getValidTicketByUid($this->uid,BIND_TYPE_UNMAIL);
				
				if(empty($validTicket) || $validTicket['is_used']){
					$pramas['verify'] = 0;
				}elseif($validTicket['ticket'] != $ticket || $validTicket['create_time'] < time()-7*3600*24){
					$pramas['verify'] = 0;
				}else{
					
					$userTicketModel = new UserTicketModel();
					$userTicketModel->updateByPk($validTicket['pass_id'],array('is_used'=>1));
					$userService->saveUserBasic(array('uid'=>$this->uid,'reg_email'=>''));
					$pramas['verify'] = 1;
				}
			}
		}elseif($type == 'mobile'){
			if($step == 'doBindSendVerify'){
				$phone = Yii::app()->request->getParam('phone');
				$response = array();
				if($phone){
					if($user['reg_mobile'] && preg_match("/^\d{11}$/", $user['reg_mobile'])){
						echo json_encode(array('status'=>'fail','info'=>'您已绑定过手机号码'));
						Yii::app()->end();
					}
					if(preg_match("/^\d{11}$/", $phone) <= 0){
						echo json_encode(array('status'=>'fail','info'=>'手机号码格式不正确'));
						Yii::app()->end();
					}
					
					$userBasicModel = new UserBasicModel();
					$userByMobile = $userBasicModel->findAllByAttributes(array('reg_mobile'=>$phone));
					if(count($userByMobile) >= 10){
						echo json_encode(array('status'=>'fail','info'=>'该手机号码已绑定其他多个帐号，无法再用于密保绑定'));
						Yii::app()->end();
					}
					
					$count = $userBindService->countTodayValidTicket($this->uid,BIND_TYPE_MOBILE);
					if($count >= 3){
						echo json_encode(array('status'=>'fail','message'=>'您今天绑定手机的次数超过三次了'));
						Yii::app()->end();
					}
			
					$userBind['uid'] = $this->uid;
					$userBind['method'] = BIND_TYPE_MOBILE;
					$userBind['method_content'] = $phone;
					$existBind = $userBindService->getValidBindByUid($this->uid,BIND_TYPE_MOBILE,$phone);
					
					if(!$existBind){
						$bindId = $userBindService->saveUserBind($userBind);
					}else{
						$bindId = $existBind['bind_id'];
						$userBindModel = UserBindModel::model();
						$userBindModel->updateByPk($bindId,array('create_time'=>time()));
						
					}
					
					if($bindId){
						$userTicket['uid'] = $this->uid;
						$userTicket['bind_id'] = $bindId;
						$userTicket['type'] = BIND_TYPE_MOBILE;
						$userTicket['ticket'] = $userBindService->getPhoneCode();
						if($userBindService->saveUserTicket($userTicket)){
							$return = $userBindService->sendBindSms($phone,$userTicket['ticket']);
							if($return['status'] == 'success'){
								echo json_encode(array('status'=>'scuccess','info'=>$phone));
							}else{
								echo json_encode(array('status'=>'fail','info'=>$return['info']));
							}
							Yii::app()->end();
						}else{
							echo json_encode(array('status'=>'fail','info'=>'绑定出现错误'));
							Yii::app()->end();
						}
					}
					Yii::app()->end();
				}
			}elseif($step == 'bindMobile'){
				$phone = Yii::app()->request->getParam('phone');
				$code = Yii::app()->request->getParam('code');
				$response = array();
				if($user['reg_mobile'] && preg_match("/^\d{11}$/", $user['reg_mobile'])){
					echo json_encode(array('status'=>'fail','info'=>'您已绑定过手机号码'));
					Yii::app()->end();
				}
				if(preg_match("/^\d{11}$/", $phone) <= 0){
					echo json_encode(array('status'=>'fail','info'=>'手机号码格式不正确'));
					Yii::app()->end();
				}

				if(preg_match("/^\d{4}$/", $code) <= 0){
					echo json_encode(array('status'=>'fail','info'=>'短信验证码必须是4个数字'));
					Yii::app()->end();
				}
				$userBasicModel = new UserBasicModel();
				$userByMobile = $userBasicModel->findAllByAttributes(array('reg_mobile'=>$phone));
				if(count($userByMobile) >= 10){
					echo json_encode(array('status'=>'fail','info'=>'该手机号码已绑定其他多个帐号，无法再用于密保绑定'));
					Yii::app()->end();
				}
				
				$userNewBind = $userBindService->getNewBindByUid($this->uid,BIND_TYPE_MOBILE);
				if(empty($userNewBind) || $userNewBind['method_content'] != $phone){
					echo json_encode(array('status'=>'fail','info'=>'绑定的手机号码与验证短信的手机号码不一致'));
					Yii::app()->end();
				}
				
				$userTicket = $userBindService->getValidTicketByUid($this->uid,BIND_TYPE_MOBILE);
				
				if(empty($userTicket) || $userTicket['ticket'] != $code || $userTicket['bind_id'] != $userNewBind['bind_id']){
					echo json_encode(array('status'=>'fail','info'=>'短信验证码不正确'));
					Yii::app()->end();
				}
				
				if($userTicket['is_used'] ||$userTicket['create_time'] < time() - 30*60){
					echo json_encode(array('status'=>'fail','info'=>'短信验证码已已过期，请重新获取'));
					Yii::app()->end();
				}
				
				$userTicketModel = new UserTicketModel();
				$userTicketModel->updateByPk($userTicket['pass_id'],array('is_used'=>1));
				$userService->saveUserBasic(array('uid'=>$this->uid,'reg_mobile'=>$phone));
				echo json_encode(array('status'=>'scuccess','info'=>''));
				Yii::app()->end();
			}elseif($step == 'verify'){
				$userNewBind = $userBindService->getNewBindByUid($this->uid,BIND_TYPE_MOBILE);
				$pramas['bind_mobile'] = $userNewBind['method_content'];
			}
		}elseif($type == 'unMobile'){
			if($step == 'doUnBindSendVerify'){
				$phone = $user['reg_mobile'];
				if(!$phone || preg_match("/^\d{11}$/", $phone) <= 0){
					echo json_encode(array('status'=>'fail','info'=>'您还没有绑定过手机号码'));
					Yii::app()->end();
				}
				if(preg_match("/^\d{11}$/", $phone) <= 0){
					echo json_encode(array('status'=>'fail','info'=>'手机号码格式不正确'));
					Yii::app()->end();
				}
				
				
				$userBind['uid'] = $this->uid;
				$userBind['method'] = BIND_TYPE_UNMOBILE;
				$userBind['method_content'] = $phone;
				$existBind = $userBindService->getValidBindByUid($this->uid,BIND_TYPE_UNMOBILE,$phone);
				
				if(!$existBind){
					$bindId = $userBindService->saveUserBind($userBind);
				}else{
					$bindId = $existBind['bind_id'];
					$userBindModel = UserBindModel::model();
					$userBindModel->updateByPk($bindId,array('create_time'=>time()));
					
				}
				
				if($bindId){
					$userTicket = array();
					$userTicket['uid'] = $this->uid;
					$userTicket['bind_id'] = $bindId;
					$userTicket['type'] = BIND_TYPE_UNMOBILE;
					$userTicket['ticket'] = $userBindService->getPhoneCode();
					if($userBindService->saveUserTicket($userTicket)){
						$return = $userBindService->sendBindSms($phone,$userTicket['ticket']);
						if($return['status'] == 'success'){
							echo json_encode(array('status'=>'scuccess','info'=>$phone));
						}else{
							echo json_encode(array('status'=>'fail','info'=>$return['info']));
						}
						Yii::app()->end();
					}else{
						echo json_encode(array('status'=>'fail','info'=>'绑定出现错误'));
						Yii::app()->end();
					}
				}
				Yii::app()->end();
				
			}elseif($step == 'unBindMobile'){
				$phone = Yii::app()->request->getParam('phone');
				$code = Yii::app()->request->getParam('code');
				if(!$phone || preg_match("/^\d{11}$/", $phone) <= 0){
					echo json_encode(array('status'=>'fail','info'=>'您还没有绑定过手机号码'));
					Yii::app()->end();
				}
				if(preg_match("/^\d{11}$/", $phone) <= 0){
					echo json_encode(array('status'=>'fail','info'=>'手机号码格式不正确'));
					Yii::app()->end();
				}

				if(preg_match("/^\d{4}$/", $code) <= 0){
					echo json_encode(array('status'=>'fail','info'=>'短信验证码必须是4个数字'));
					Yii::app()->end();
				}
				
				
				$userNewBind = $userBindService->getNewBindByUid($this->uid,BIND_TYPE_UNMOBILE);
				if(empty($userNewBind) || $userNewBind['method_content'] != $phone || $phone !=  $user['reg_mobile']){
					echo json_encode(array('status'=>'fail','info'=>'解绑的手机号码与验证短信的手机号码不一致'));
					Yii::app()->end();
				}
				
				$userTicket = $userBindService->getValidTicketByUid($this->uid,BIND_TYPE_UNMOBILE);
				
				if(empty($userTicket) || $userTicket['ticket'] != $code || $userTicket['bind_id'] != $userNewBind['bind_id']){
					echo json_encode(array('status'=>'fail','info'=>'短信验证码不正确'));
					Yii::app()->end();
				}
				
				if($userTicket['is_used'] ||$userTicket['create_time'] < time() - 30*60){
					echo json_encode(array('status'=>'fail','info'=>'短信验证码已已过期，请重新获取'));
					Yii::app()->end();
				}
				
				$userTicketModel = new UserTicketModel();
				$userTicketModel->updateByPk($userTicket['pass_id'],array('is_used'=>1));
				$userService->saveUserBasic(array('uid'=>$this->uid,'reg_mobile'=>''));
				echo json_encode(array('status'=>'scuccess','info'=>$phone));
				Yii::app()->end();
			}elseif($step == 'verify'){
				$userNewBind = $userBindService->getNewBindByUid($this->uid,BIND_TYPE_UNMOBILE);
				$protectedMobile = '';
				if($userNewBind){
					$protectedMobile = $userNewBind['method_content'];
					$mobileLen = strlen($protectedMobile);
					if($mobileLen == 11){
						$pramas['protected_mobile'] = substr($protectedMobile,0,3).'****'.substr($protectedMobile,7,$mobileLen-7);
					}else{
						$pramas['protected_mobile'] = $protectedMobile;
					}
				}
			}
		}
		$this->_left('security',$pramas);
	}
	
	/**
	 * 代理销售统计
	 */
	public function actionSaleStat()
	{
		$saleYear = Yii::app()->request->getParam('sale_year')?Yii::app()->request->getParam('sale_year'):date("Y");
		$agent_id = $this->uid;
		
		$data['yearList']=$this->agentsService->getRecentThreeYearList();
		
		$agents=$this->agentsService->getAgentByUids(array($agent_id));
		$userAgent=$agents[$agent_id];
		$userAgent['thisMonthIncome']=$this->agentsService->getThisMonthSaleIncome($agent_id);
		$data['userAgent']=$userAgent;
		$salestat_list=$this->agentsService->getMonthSaleStatByAgentId($saleYear, $agent_id);
		$counts=0;
		foreach ($salestat_list as $saleRow)
		{
			$counts+=$saleRow['counts'];
		}
		if($counts>0)
		{
			$data['salestat_list'] = $salestat_list;
		}
		else 
			$data['salestat_list']=null;
		$data['seletedYear']=$saleYear;
	
		$this->_left('salestat',$data);
	}
	
	
	/**
	 * 代理销售记录
	 */
	public function actionSaleRecords()
	{
		$agent_id = $this->uid;
		$page = Yii::app()->request->getParam('page')? Yii::app()->request->getParam('page') : 1;
		
		$yearMonth= Yii::app()->request->getParam('year_month')?Yii::app()->request->getParam('year_month'):date("Y-m");
		
		$data['monthList']=$this->agentsService->getRecentSixMonthList();
		$data['salerecords'] = $this->agentsService->getRecordsByMonth($yearMonth,$agent_id,$page);
		$this->agentsService->getUserInfoForSaleRecords($data['salerecords']['list']);
		
		$agents=$this->agentsService->getAgentByUids(array($agent_id));
		$userAgent=$agents[$agent_id];
		$data['userAgent']=$userAgent;
		
		$condition=array(
			'agent_id'=>	$agent_id
		);
		list($condition['sale_year'],$condition['sale_month'])=explode("-", $yearMonth);
		$data['statData']=$this->agentsService->getSaleStatByCondition($condition);
		$data['salerecordsType']="ByMonth";
		$data['seletedMonth']=$yearMonth;

		$this->_left('salerecords',$data);
	}
	
	//代理销售记录翻页
	public function actionTurnSaleRecords()
	{
		$agent_id = $this->uid;
		$page = Yii::app()->request->getParam('page')? Yii::app()->request->getParam('page') : 1;
		$salerecordsType=Yii::app()->request->getParam('salerecords_type');
		if($salerecordsType=="ByMonth")
		{
			$yearMonth= Yii::app()->request->getParam('year_month')?Yii::app()->request->getParam('year_month'):date("Y-m");
			
			$data['monthList']=$this->agentsService->getRecentSixMonthList();
			$data['salerecords'] = $this->agentsService->getRecordsByMonth($yearMonth,$agent_id,$page);
			$this->agentsService->getUserInfoForSaleRecords($data['salerecords']['list']);
			$agents=$this->agentsService->getAgentByUids(array($agent_id));
			$userAgent=$agents[$agent_id];
			$data['userAgent']=$userAgent;
			$condition=array(
				'agent_id'=>	$agent_id
			);
			list($condition['sale_year'],$condition['sale_month'])=explode("-", $yearMonth);
			$data['statData']=$this->agentsService->getSaleStatByCondition($condition);
			$data['salerecordsType']="ByMonth";
			$data['seletedMonth']=$yearMonth;
		}
		if($salerecordsType=="ByUserId")
		{
			$user_id= Yii::app()->request->getParam('user_id');
			$data['salerecords'] = $this->agentsService->getRecordsByUser($user_id,$agent_id,$page);
			$this->agentsService->getUserInfoForSaleRecords($data['salerecords']['list']);
			$agents=$this->agentsService->getAgentByUids(array($agent_id));
			$userAgent=$agents[$agent_id];
			$data['userAgent']=$userAgent;
			$condition=array(
				'user_id'=>$user_id,
				'agent_id'=>	$agent_id
			);
			$data['statData']=$this->agentsService->getSaleStatByCondition($condition);
			$data['salerecordsType']="ByUserId";
		}
		
		$this->renderPartial('turnsalerecords',$data);
	}
	
	
	//代理政策
	public function actionAgentPolicies()
	{
		$page=Yii::app()->request->getParam('page')? Yii::app()->request->getParam('page') : 1;
		$pageSize=5;
		$threadList=$this->agentsService->getAgentPloicies($page,$pageSize);
		//分页实例化
		$pager = new CPagination($threadList['count']);
		$pager->pageSize= $pageSize;
		$data=array(
			'threadList'=>$threadList,
			'pager'=>$pager,
			'forum_sid'=>$threadList['forum_sid']
		);
		$this->_left('agentpolicies',$data);
		
	}
	
	
	public function _left($action = 'main', $data = null, $page = '')
	{
		if(empty($action)) {
			$action = 'main';
		}
		
		$account_left = array(
					array('action'=>'main','name'=>'个人资料','is_check'=>($action=='main' || $page=='main')),
					array('action'=>'message','name'=>'我的消息','is_check'=>($action=='message' || $page=='message')),
					array('action'=>'items','name'=>'我的物品','is_check'=>($action=='bag' || $page=='items')),
					array('action'=>'follow','name'=>'我的关注','is_check'=>($action=='follow' || $page=='follow')),
					array('action'=>'consumer','name'=>'我的消费','is_check'=>($action=='buy' || $page=='consumer')),
 					array('action'=>'gifts','name'=>'我的收礼','is_check'=>($action=='gifts')),
					array('action'=>'receive','name'=>'我的赠品','is_check'=>($action=='receive' || $page=='receive')),
// 					array('action'=>'family','name'=>'我的家族','is_check'=>($action=='family')),
					array('action'=>'exchange','name'=>'兑换皮蛋','is_check'=>($action=='exchange')),
					array('action'=>'security','name'=>'账户安全','is_check'=>($action=='security'))
				);
		$uid = $this->uid;
		$userInfo['basic'] = $this->userService->getUserBasicByUids(array($uid));
		$userInfo['extend'] = $this->userService->getUserExtendByUids(array($uid));
		$userInfo['uid'] = $uid;
		if($this->isDotey){
				$dotey_left = array(
					array('action'=>'dotey','name'=>'主播资料','is_check'=>($action=='dotey')),
					array('action'=>'doteyNotice','name'=>'主播公告','is_check'=>($action=='doteyNotice')),
					array('action'=>'cash', 'name'=>'兑换现金', 'is_check'=>($action=='cash' || $page=='cash')),
					array('action'=>'income','name'=>'我的收入','is_check'=>($action=='income' || $page=='income')),
					array('action'=>'fans','name'=>'我的粉丝','is_check'=>($action=='fans')),
					array('action'=>'manager','name'=>'我的房管','is_check'=>($action=='manager')),
					array('action'=>'doteygifts','name'=>'收礼记录','is_check'=>($action=='doteygifts')),
// 					array('action'=>'myguard','name'=>'我的守护','is_check'=>($action=='myguard')),
					array('action'=>'song','name'=>'点歌记录','is_check'=>($action=='song')),
				);
		}else{
			$dotey_left = false;
		}
		
		if($this->isAgent)
		{
			$agent_left=array(
				array('action'=>'salestat','name'=>'销售统计','is_check'=>($action=='salestat')),
				array('action'=>'salerecords','name'=>'销售记录','is_check'=>($action=='salerecords')),
				array('action'=>'agentpolicies','name'=>'代理政策','is_check'=>($action=='agentpolicies')),
			);
		}
		else
		{
			$agent_left=false;
		}
		
		$account_data = array(
				'account_user_info'=>$userInfo,
				'account_left'=>$account_left,
				'dotey_left'=>$dotey_left,
				'agent_left'=>$agent_left,
				'account_imgurl'=>Yii::app()->params['images_server']['url'].'/'
		);
		if($data) {
			$account_data = array_merge($account_data,$data);
		}
		$this->render($action,$account_data);
	}


	/**
	 * 获取背包数据
	 */
	public function getBagsData($uid)
	{
		$bagservice = new GiftBagService();
		$giftservice = new GiftService();
		$bags = $bagservice->getUserGiftBagByUids($uid);
		$account_bags = $bags[$uid];
		$gift_ids = array();
		foreach($account_bags as $k=>$v) {
			$gift_ids[] = $v['gift_id'];
// 			$gift_ids[]['nums'] = $v['num'];
		}
		$res = $giftservice->getGiftByIds($gift_ids);
		foreach($account_bags as $ke=>&$ve){
			$ve['info'] = $res[$ve['gift_id']];
		}
		return $account_bags;
	}
	
	/**
	 * 获取道具分类信息, 拥有道具信息等(公用)
	 * @param int $uid
	 * @param string $category
	 * @return array
	 */
	public function getPropsData($uid,$category = 'car')
	{
		$returnArray = array();
		$userPropsService = new UserPropsService();
		$propsService = new PropsService();
		$category = $propsService->getPropsCategoryByEnName($category);
		if(empty($category)){
			return array();
		}
		$returnArray['category'] = $category;
		$selectNum = $category=='monthcard' ? true : false;
		$userProps = $userPropsService->getUserValidPropsOfBagByCatId($uid,$category['cat_id'], null, $selectNum);
		$propsIds = array_keys($propsService->buildDataByIndex($userProps, 'prop_id'));
		$propsInfo = $propsService->getPropsByIds($propsIds,false,true);
		foreach($propsInfo as $key => $props){
			if($props['attribute']){
				$propsInfo[$key]['attribute'] = $propsService->buildDataByIndex($props['attribute'],'attr_enname');
			}
		}
		
		$returnArray['bagInfo'] = $userProps;
		$returnArray['propsInfo'] = $propsInfo;
		$returnArray['propUsed'] = $userPropsService->getUserPropsAttributeByUid($uid);
	
		return $returnArray;
	}
	
	/**
	 * 消费记录_购买
	 */
	public function getBuyRecord($uid, $page = 1, $source = array(0))
	{
		$condition = array('uid'=>$uid);
		$limit = 10;
		$offset = ($page >= 1 ? ($page-1) : 0 ) * $limit;
		
		// 购买道具记录
		$userPropsService = new UserPropsService();
		$condition = array('source' => $source);
		$records = $userPropsService->getUserPropsRecords($uid, $limit, $offset, $condition);
		

		$records['page'] = $page;
		$records['page_num'] = ceil($records['count'] / $limit);
		$records['source'] = $userPropsService->getSourceTypeList();
		return $records;
	}
	
	public function getBuyGiftRecord($uid, $page)
	{
		$limit = 10;
		$offset = ($page >= 1 ? ($page-1) : 0 ) * $limit;
		// 购买礼物记录
		$giftServ = new GiftBagService();
		$records = $giftServ->getUserBagRecordsByCondition(array('uid'=>$uid,'source'=>BAGSOURCE_TYPE_SHOP), $offset, $limit);
		
		$records['page'] = $page;
		$records['page_num'] = ceil($records['count'] / $limit);
		return $records;
	}
	
	public function getSendRecord($uid, $page = 1)
	{
		$return = array();
		$condition = array('uid'=>$uid);
		$limit = 10;
		$offset = ($page >= 1 ? ($page-1) : 0 ) * $limit;
		
		$giftService = new GiftService();
		$records = $giftService->getUserGiftSendRecordsByUid($uid,$offset, $limit);
		
		$records['page'] = $page;
		$records['page_num'] = ceil($records['count'] / $limit);
		return $records;
	}
	
	public function isRankCar($uid = 0)
	{
		if($uid <= 0){
			return false;
		}
		// 获取用户的等级
		$rank = $this->viewer['user_attribute']['rk'];
		$propsService = new PropsService();
		$userPropsService = new UserPropsService();
		$category = $propsService->getPropsCategoryByEnName('car');
		$props = $propsService->getPropsByCondition(array('status'=>2,'cat_id'=>$category['cat_id']));
		$return = array();
		foreach ($props as $k=>$v){
			if($rank >= $v['rank']){
				$_have = $userPropsService->getUserValidPropsOfBagByPropId($uid, $v['prop_id']);
				if(!$_have){
					$return[] = $v;
				}
			}
		}
		return $return;
	}
	
	function check_show_nickname($nickname)
	{
		$guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8|\xE2\x80\xAD|\xE2\x80\xAE|\xE2\x80\xAA';
		$guestexp.='|\xE2\x80\xAB|\xE2\x80\xAC|\xE2\x80\xAF|\xEF\xA3\xB5|\xE2\x80|\xE2\x81|\x2A|\xEE\xA0|\xC2\xAD|\x7F|\xE3\x80\x80';
		$guestexp.='|\x1E\x1F|\x1E\x1E|\x1F\x1F';
		$patrnstr="/\s+|\{|\}|\||\;|\:|\'|^c:\\con\\con|(&\d{2})|(%\d{2})|[%&,\*\"\s\<\>\|\\\[\]\/\?\^\+`~]|".$guestexp."/is";
		$len=mb_strlen($nickname,'UTF8');
	
		if($len > 10 || $len < 2 )
		{
			return '请输入长度为2到10位的昵称';
		}
	
		if(preg_match($patrnstr, $nickname))
		{
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
		for ($i = 0; $i < $len - 1; $i = $i + 2)
		{
			$c = $unicode_nickname[$i];
			$c2 = $unicode_nickname[$i + 1];
			if (ord($c) > 0)
			{    // 两个字节的文字
				$temp_c=base_convert(ord($c), 10, 16);
				$temp_c2=base_convert(ord($c2), 10, 16);
	
				foreach ($badunicodes as $unicoderow)
				{
					if($unicoderow[0]==$temp_c && $unicoderow[1]==$temp_c2)
					{
						return '昵称不能包含特殊字符';
					}
				}
			}
			else
			{
				$temp_c= base_convert(ord($c2), 10, 16);
				if($temp_c==0x30 || $temp_c==0x31)
					return '昵称不能包含特殊字符';
			}
		}
		// @todo bad_word
		$wordService = new WordService();
		$badWord = $wordService->getAllChatWordList();
		foreach($badWord as $k=>$v)
		{
			$_patrnstr="/{$v['name']}/is";
			if(preg_match($_patrnstr, $nickname))
				return '昵称中包含非法字符';
		}
		// @todo only_nickname
		$userService = new UserService();
		$res = $userService->getUserBasicByNickNames(array($nickname));
		if(isset($res[$nickname]) && $res[$nickname]['uid']!=$this->uid){
			return '昵称已存在';
		}
	
		return null;
	}
	
	public function getConsumeService()
	{
		static $_service;
		if($_service){
			return $_service;
		}
		$_service = new ConsumeService();
		return $_service;
	}
}
?>