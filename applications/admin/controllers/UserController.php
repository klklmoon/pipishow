<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su Peng <594524924@qq.com>
 * @version $Id: UserController.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package 
 */
class UserController extends PipiAdminController {
	
	/**
	 * @var UserService 道具服务层
	 */
	public $userSer;
	
	/**
	 * @var array 允许的操作
	 */
	public $allowOp = array('showUinfo', 'updateUinfo', 'dlSendGiftExcel', 'restoreStopLive', 'removeUserAvatar', 
		'dlViolationExcel', 'dlBriskExcel','dlPaySearchExcel','dlUGiftStatExcel','dlVODQueryExcel','dlVODStatExcel',
		'batchUpdateUS','checkSignFamily','bindDo','bind','loginVerify');
	
	/**
	 * @var string 当前操盘
	 */
	public $op;
	
	/**
	 * @var boolean 是否是Ajax请求
	 */
	public $isAjax;
	
	public $pageSize = 20;
	
	public $offset;
	
	/**
	 * @var int page lable
	 */
	public $p;
	
	public function init(){
		parent::init();
		$this->userSer = new UserService();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
	}
	
	/**
	 * 管理员登录
	 */
	public function actionLogin() {
		$this->layout = 'loginMain';
		$msg = '';
		
		if(isset($_POST['login'])){
			$username = (string)$_POST['login']['username'];
			$password = (string)$_POST['login']['password'];
			if (!empty($username) && !empty($password)){
				$userIdentity = new PipiUserIdentity($username,$password);
				if($userIdentity->authenticate()) {
					Yii::app()->user->login($userIdentity,86400);
					$this->redirect($this->createUrl('index/index'));
					Yii::end();
				} else {
					$msg = $userIdentity->errorMessage;
				}
			}else{
				$msg = Yii::t('user','Enter the user name and password can not be empty');
			}
		}
		
		$assetManager = Yii::app()->getAssetManager();
		$static = '/statics';
		$jsHash = sprintf('%x',crc32($static.SOFT_VERSION));
		$this->cs->registerScriptFile($static.'/js/common/jquery.md5.js?token='.$jsHash,CClientScript::POS_HEAD );
		
		$this->render('login',array('msg'=>$msg));
	}
	
	/**
	 * 管理员退出登录
	 */
	public function actionLogout() {
		Yii::app()->user->logout();
		$this->redirect($this->createUrl('user/login'));
	}
	
	/**
	 * 用户查询 
	 */
	public function actionUserSearch(){
		if($this->op == 'bind' && in_array($this->op,$this->allowOp)){
			$this->_bind();
		}
		if($this->op == 'bindDo' && in_array($this->op,$this->allowOp)){
			$this->_bindDo();
		}
		if($this->op == 'loginVerify' && in_array($this->op,$this->allowOp)){
			$this->_loginVerify();
		}
		$userType = $this->userSer->getUserBaseType();#获取用户类型
		$userStatus = $this->userSer->getUserStatus();#获取用户状态
		
		//获取搜索条件
		$condition = $this->getSearchCondition();
		if ($uid = Yii::app()->request->getParam('uid')){
			$condition['uid'] = $uid;
		}
		
		//获取礼物列表
		$comsumeList = $extends = array();
		$userList = $this->userSer->searchUserList($this->offset,$this->pageSize,$condition);
		if($userList){
			$uids = $userList['uids'];
			//用户总的消费
			if($uids){
				$consumeSer = new ConsumeService();
				$comsumeList = ($consumeSer->getConsumesByUids($uids));
				$extends = $this->userSer->getUserExtendByUids($uids);
			}
		}
		
		#家族信息
		$this->getFamilyInfo($userList['list']);
		
		//分页实例化
		$pager = new CPagination($userList['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
		
		$assetManager = Yii::app()->getAssetManager();
		$static = '/statics';
		$jsHash = sprintf('%x',crc32($static.SOFT_VERSION));
		$this->cs->registerScriptFile($static.'/js/common/jquery.md5.js?token='.$jsHash,CClientScript::POS_HEAD );
		
		$this->render('user_search',array('pager'=>$pager,'userList'=>$userList['list'],'userType'=>$userType,'userStatus'=>$userStatus,'comsumeList'=>$comsumeList,'extends'=>$extends,'condition'=>$condition));
	}
	
	/**
	 * 实现用户信息的编辑操作
	 */
	public function actionUInfoEdit(){
		if(empty($this->op)){
			exit("非法操作");
		}
		//必须参数
		if(!($uid = Yii::app()->request->getParam('uid'))){
			exit("缺少参数");
		}
		
		//批量封号
		if ($this->op == 'batchUpdateUS' && in_array($this->op, $this->allowOp)) {
			$notices = $this->batchUpdateUS($uid);
		}
		
		//是否是修改动作
		$notices = array();
		if ($this->op == 'updateUinfo' && in_array($this->op, $this->allowOp)) {
			$notices = $this->upDataUinfoDo();
		}
		
		//是否清除用户图像
		if ($this->op == 'removeUserAvatar' && in_array($this->op, $this->allowOp)){
			exit($this->removeUserAvatar($uid));
		}
		//是否是家族主播
		if ($this->op == 'checkSignFamily' && in_array($this->op, $this->allowOp)){
			$famService = new FamilyService();
			if($famService->getDoteyMembers($uid)){
				exit('1');
			}
		}
		
		if($this->op == 'showUinfo' && in_array($this->op, $this->allowOp)){
			if(!$this->isAjax){
				exit("非法请求");
			}
		}
		
		if(!($uinfo = $this->userSer->getUserBasicByUids(array($uid)))){
			exit("无法找到该用户的合法信息");
		}
		
		$archivesSer = new ArchivesService();
		$archivesInfo = $archivesSer->getArchivesBycondition(array('uid'=>$uid));
		$cat_ids = array();
		if($archivesInfo){
			foreach ($archivesInfo as $archives){
				$cat_ids[] = $archives['cat_id'];
			}
		}
		
		$broadcastService = new BroadcastService();
		$broadcastStatus = $broadcastService->getBroadcastDisableByUid($uid)?1:0;
		if($this->isAjax){
			exit($this->renderPartial('user_base_edit',array('uinfo'=>$uinfo[$uid],'cat_ids'=>$cat_ids,'broadcastStatus'=>$broadcastStatus)));
		}else{
			$this->render('user_base_edit',array('uinfo'=>$uinfo[$uid],'notices'=>$notices,'cat_ids'=>$cat_ids,'broadcastStatus'=>$broadcastStatus));
		}
		
	}
	
	
	/**
	 * 用户送礼明细记录
	 */
	public function actionUGiftRecords(){
		$uid = Yii::app()->request->getParam('uid');
		
		$condition = array();
		$condition = $this->getGiftCondition();
		
		//是否下载Excel
		$isLimit = true;
		if ($this->op == 'dlSendGiftExcel' && in_array($this->op, $this->allowOp)){
			$isLimit = false;
		}else if($condition){
			foreach($condition as $k=>$c){
				if (empty($c)){
					unset($condition[$k]);
				}
			}
		}else{
			if(Yii::app()->request->getParam('start_time')){
				$condition['start_time'] =  Yii::app()->request->getParam('start_time');
			}
			if(Yii::app()->request->getParam('end_time')){
				$condition['end_time'] =  Yii::app()->request->getParam('end_time');
			}
		}
		
		if (empty($condition['uid']) && $uid){
			$condition['uid'] = $uid;
		}elseif($uid){
			$uid = $condition['uid'];
		}
		
		//获取记录列表
		$giftSer = new GiftService();
		$sendRecords = array();
		$sendRecords['count'] = 0;
		$sendRecords['list'] = array();
		$sendRecords['remDuplicateCount'] = 0;
		$sendRecords['pipieggSum'] = 0;
		if(!empty($condition)){
			$sendRecords = $giftSer->getUserGiftSendRecordsByUid($uid,$this->offset,$this->pageSize,$condition,$isLimit);
		}
		
		if (!$isLimit){
			$this->dlSendGiftExcel($sendRecords);
		}
		//分页实例化
		$pager = new CPagination($sendRecords['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
		
		$this->render('user_gift_records',array('pager'=>$pager,'records'=>$sendRecords,'condition'=>$condition));
	}
	
	/**
	 * 用户送礼统计
	 */
	public function actionUGiftStat(){
		$uid = Yii::app()->request->getParam('uid',null);
	
		$condition = array();
		$condition = $this->getGiftCondition();
	
		//是否下载Excel
		$isLimit = true;
		if ($this->op == 'dlUGiftStatExcel' && in_array($this->op, $this->allowOp)){
			$isLimit = false;
		}else if($condition){
			foreach($condition as $k=>$c){
				if (empty($c)){
					unset($condition[$k]);
				}
			}
		}else{
			if(Yii::app()->request->getParam('start_time')){
				$condition['start_time'] =  Yii::app()->request->getParam('start_time');
			}
			if(Yii::app()->request->getParam('end_time')){
				$condition['end_time'] =  Yii::app()->request->getParam('end_time');
			}
		}
	
		if (empty($condition['uid'])){
			$condition['uid'] = $uid;
		}else{
			$uid = $condition['uid'];
		}
	
		$sendRecords = array();
		$sendRecords['list'] = array();
		$sendRecords['count'] = 0;
		if($uid){
			//获取记录列表
			$giftSer = new GiftService();
			$sendRecords = $giftSer->getUserGiftStatByUid($uid,$this->offset,$this->pageSize,$condition,$isLimit);
		}
		
		if (!$isLimit){
			$this->dlUGiftStatExcel($sendRecords);
		}
		//分页实例化
		$pager = new CPagination($sendRecords['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
	
		$this->render('user_gift_stat',array('pager'=>$pager,'records'=>$sendRecords,'condition'=>$condition));
	}
	
	/**
	 * 重置密码
	 */
	public function actionResetPwd(){
		if(!$this->isAjax){
			exit("不合法请求");
		}
		
		if(!($uid = Yii::app()->request->getParam('uid'))){
			exit('缺少参数');
		}
		
		$new_pwd = Yii::app()->request->getParam('new_pwd');
		$confirm_new_pwd = Yii::app()->request->getParam('confirm_new_pwd');
		
		if(empty($new_pwd) || empty($confirm_new_pwd)){
			exit('密码和确认密码不能为空');
		}
		
		if($new_pwd != $confirm_new_pwd){
			exit('输入的密码和确认密码不一样');
		}
		
		if(!($info = $this->userSer->getUserBasicByUids(array($uid)))){
			exit('不存在该用户，不能修改密码');
		}
		
		$uinfo['uid'] = $uid;
		$uinfo['password'] = $new_pwd;
		if ($this->userSer->saveUserBasic($uinfo)){
			$this->userSer->saveAdminOpLog('重置 用户密码(UID='.$uid.')',$uid);
			exit("密码重置成功!");
		}else{
			$notices = array_shift($this->userSer->getNotice());
			exit("密码重置失败:".$notices[0]);
		}
		exit("密码重置失败");
	}
	
	/**
	 * 用户清除图像
	 */
	public function actionRemoveAvatar() {
		if(!$this->isAjax){
			exit("不合法请求");
		}
		
		if(!($uid = Yii::app()->request->getParam('uid'))){
			exit('缺少参数');
		}
		
		if(!($uinfo = $this->userSer->getUserBasicByUids(array($uid)))){
			exit('不存在访用户');
		}
		
		exit("正在建设中....");
	}
	
	/**
	 * 违规查询 
	 */
	public function actionViolation(){
		//恢复账号
		if($this->op == 'restoreStopLive' && in_array($this->op, $this->allowOp)){
			$this->restoreAccount();
		}
		
		$isLimit = true;
		$count = 60000;
		if($this->op == 'dlViolationExcel' && in_array($this->op, $this->allowOp)){
			$isLimit = false;
		}
		//加载资源
		$this->assetsMy97Date();
		
		$consumeSer = new ConsumeService();
		
		$condition = array();
		$condition = $this->getSearchCondition();
		$condition['user_status'] = USER_STATUS_OFF;
		
		//获取列表
		$userList['list'] = array();
		$userList['uids'] = array();
		$userList['count'] = 0;
		
		//用户列表
		$userList = $this->userSer->searchUserList($this->offset,$this->pageSize,$condition,$isLimit,$count);
		$uids = $userList['uids'];
		
		$comsumeList = array();
		$halfMonthConsume = array();
		$reason = array();
		if($uids){
			//用户总的消费信息
			$_uids = array();
			$comsumeList = $consumeSer->getConsumesByUids($uids);
			if($comsumeList){
				foreach ($comsumeList as $v){
					$_uids[] = $v['uid'];
					$userList['list'][$v['uid']]['consumeList'] = $v;
					$userList['list'][$v['uid']]['allConsume'] = $v['consume_pipiegg'];
					$userList['list'][$v['uid']]['halfMonthConsume'] = 0;
				}
			}
			
			$_nuids = array_diff($uids, $_uids);
			if ($_nuids){
				foreach($_nuids as $v){
					$userList['list'][$v]['allConsume'] = 0;
					$userList['list'][$v]['halfMonthConsume'] = 0;
				}
			}
			
			//用户总的消费
			$condition['isPlus'] = false;
			$allConsume = $this->getPipiEggsConsumeSum($uids,'all',$condition);
			if (isset($allConsume['list'])){
				foreach ($allConsume['list'] as $v){
					$userList['list'][$v['uid']]['allConsume'] = $v['sum_pipiegg'];
				}
			}
			
			//十五天消费
			$condition['isPlus'] = false;
			$halfMonthConsume = $this->getPipiEggsConsumeSum($uids,'halfMonth');
			if(isset($halfMonthConsume['list'])){
				foreach ($halfMonthConsume['list'] as $v){
					$userList['list'][$v['uid']]['halfMonthConsume'] = $v['sum_pipiegg'];
				}
			}
			
			//理由
			$reason = $this->userSer->getUserOperatedByUids($uids,USER_OPERATED_TYPE_USERSTATUS,USER_STATUS_OFF);
			if($reason){
				$uids2 = array();
				foreach ($reason as $v){
					$uids2[$v['op_uid']] = $v['op_uid'];
					$userList['list'][$v['uid']]['reason'] = $v;
				}
				if ($uids2){
					$uinfos2 = $this->userSer->getUserBasicByUids($uids2);
					if ($uinfos2){
						foreach ($uinfos2 as $uinfo){
							$userList['list'][$v['uid']]['op_uinfo'] = $uinfo;
						}
					}
				}
			}
		}
		
		if(!$isLimit){
			$this->dlViolationExcel($userList);
		}

		//分页实例化
		$pager = new CPagination($userList['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
		
		$this->render('user_violation', array('pager' => $pager, 'userList' => $userList['list'], 
			'condition' => $condition));
	}
	
	/**
	 * 用户活跃查询
	 */
	public function actionBrisk(){
		$this->assetsMy97Date();
		
		$isLimit = true;
		$isDownload = false;
		if($this->op == 'dlBriskExcel' && in_array($this->op, $this->allowOp)){
			$isLimit = false;
			$isDownload = true;
		}
		
		$records = array();
		$records['loginUserCount'] = 0;#登录总用户数
		$records['regUserCount'] = 0;#注册用户总数
		$records['briskPercent'] = 0;#活跃程度
		$records['loginStatitics'] = array();#统计列表
		
		$condition = $this->getSearchCondition();
		$condition['group'] = 'uid';
		if (empty($condition['logins'])){
			$condition['logins'] = 1;
		}
		$loginResult = $this->userSer->getLatelyLogins($condition,$this->offset,$this->pageSize,$isLimit);
		$records['loginUserCount'] = $loginResult['count'];
		$records['loginStatitics'] = $loginResult['list'];
		
		$uids = array();
		if (!empty($records['loginStatitics'])){
			$uids = array_keys($records['loginStatitics']);
		}
		$condition['uids'] = $uids;
		
		//用户所有登录天数统计
		$condition2 = $condition;
		if (isset($condition2['start_time'])){
			unset($condition2['start_time']);
		}
		if (isset($condition2['end_time'])){
			unset($condition2['end_time']);
		}
		
		//消费
		if ($uids){
			$consumeSer = new ConsumeService();
			$consumeInfo = $consumeSer->getConsumesByUids($uids);
			if($consumeInfo){
				foreach ($consumeInfo as $v){
					$records['loginStatitics'][$v['uid']]['consumeInfo'] = $v;
				}
			}
			
			$condition2['isList'] = false;
			$result = $this->userSer->getLatelyRegisters($condition2,$this->offset,$this->pageSize);
			$records['regUserCount'] = $result['regcount'];
			$userInfo = $this->userSer->getUserBasicByUids($uids);
			
			if($userInfo){
				foreach($userInfo as $v){
					$records['loginStatitics'][$v['uid']]['userInfo'] = $v;
				}
			}
		}
		
		if (!empty($records['loginUserCount'])){
			$records['briskPercent'] = number_format(($records['loginUserCount']/$records['regUserCount']),4);
		}
		
		$allLoginResult = $this->userSer->getLatelyLogins($condition2,$this->offset,$this->pageSize);
		if (!empty($allLoginResult['list'])){
			if(!empty($records['loginStatitics'])){
				foreach ($allLoginResult['list'] as $v){
					$records['loginStatitics'][$v['uid']]['all_logins'] = $v['logins'];
				}
			}
		}
		unset($condition['uids']);
		//分页
		$pager = new CPagination($records['loginUserCount']);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		if ($isDownload){
			$this->dlBriskExcel($records);
		}
		
		$this->render('user_brisk_list',array('pager'=>$pager,'records'=>$records,'condition'=>$condition));
	}
	
	/**
	 * 登录明细查询
	 */
	public function actionLoginDetail(){
		$this->assetsMy97Date();
		$condition = $this->getSearchCondition();
		$condition = $condition?$condition:array();
		if (isset($condition['remDuplicate']) && $condition['remDuplicate']){
			$result = $this->userSer->getDuplicateLogins($condition,$this->offset,$this->pageSize);
		}else{
			$result = $this->userSer->getLoginDetails($condition,$this->offset,$this->pageSize);
		}
		
		$count = $result['count'];
		$list = $result['list'];
		$uids = array();
		
		if ($list){
			foreach($list as $v){
				$uids[$v['uid']] = $v['uid'];
			}
		}
		$uinfo = array();
		if($uids){
			$uinfo = $this->userSer->getUserBasicByUids($uids);
		}
		
		//分页
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('user_login_detail',array('pager'=>$pager,'list'=>$list,'condition'=>$condition,'userInfo'=>$uinfo));
	}
	
	/**
	 * 付费查询
	 */
	public function actionPaySearch(){
		//加载资源
		$this->assetsMy97Date();
		
		$isLimit = true;
		if($this->op == 'dlPaySearchExcel' && in_array($this->op, $this->allowOp)){
			$isLimit = false;
		}
		$consumeSer = new ConsumeService();
		
		$condition = array();
		$condition = $this->getSearchCondition();
		$_condition = array();
		
		//获取列表
		$userList['list'] = array();
		$userList['uids'] = array();
		$userList['count'] = 0;
		$uids = array();
		$isConsume = false;
		
		if(isset($condition['user_rank'])){
			$_condition['rank'] = $condition['user_rank'];
		}
		if(isset($condition['dotey_rank'])){
			$_condition['dotey_rank'] = $condition['dotey_rank'];
		}
		
		if($_condition){
			//主播等级和用户等级
			$isConsume = true;
			$rs = $consumeSer->getConsumesByConditions($_condition,$this->offset,$this->pageSize,$isLimit);
			if (!empty($rs['list'])){
				$uids = array_keys($rs['list']);
				$userList['uids'] = $uids;
				$userList['count'] = $rs['count'];
				foreach($rs['list'] as $uid=>$v){
					$userList['list'][$uid]['consumeList']=$v;
				}
			}
		}else{
			//用户列表
			$userList = $this->userSer->searchUserList($this->offset,$this->pageSize,$condition,$isLimit);
			$uids = $userList['uids'];
		}
		
		if($uids){
			if(!$isConsume){
				//用户总的消费消费信息
				$_uids = array();
				$comsumeList = $consumeSer->getConsumesByUids($uids);
				if($comsumeList){
					foreach ($comsumeList as $v){
						$_uids[] = $v['uid'];
						$userList['list'][$v['uid']]['consumeList'] = $v;
						$userList['list'][$v['uid']]['allCash'] = $v['pipiegg'];
						$userList['list'][$v['uid']]['allConsume'] = $v['consume_pipiegg'];
						$userList['list'][$v['uid']]['halfMonthCash'] = 0;
						$userList['list'][$v['uid']]['halfMonthConsume'] = 0;
						$userList['list'][$v['uid']]['rangeCash'] = 0;
						$userList['list'][$v['uid']]['rangeConsume'] = 0;
					}
				}
				$_nuids = array_diff($uids, $_uids);
				if($_nuids){
					foreach ($_nuids as $v){
						$userList['list'][$v]['allCash'] = 0;
						$userList['list'][$v]['allConsume'] = 0;
						$userList['list'][$v]['halfMonthCash'] = 0;
						$userList['list'][$v]['halfMonthConsume'] = 0;
						$userList['list'][$v]['rangeCash'] = 0;
						$userList['list'][$v]['rangeConsume'] = 0;
					}
				}
			}else{
				$uinfo = $this->userSer->getUserBasicByUids($uids);
				if ($uinfo){
					foreach ($uinfo as $uid=>$v){
						$userList['list'][$uid] = array_merge($userList['list'][$uid],$v);
						$userList['list'][$uid]['allCash'] = $userList['list'][$uid]['consumeList']['pipiegg'];
						$userList['list'][$uid]['allConsume'] = $userList['list'][$uid]['consumeList']['consume_pipiegg'];
						$userList['list'][$uid]['halfMonthCash'] = 0;
						$userList['list'][$uid]['halfMonthConsume'] = 0;
						$userList['list'][$uid]['rangeCash'] = 0;
						$userList['list'][$uid]['rangeConsume'] = 0;
					}
				}
			}
			
			//全部充值记录
			$condition['isPlus'] = true;
			$allCash = $this->getPipiEggsConsumeSum($uids,'all',$condition);
			if (isset($allCash['list'])){
				foreach($allCash['list'] as $v){
					$userList['list'][$v['uid']]['allCash'] = $v['sum_pipiegg'];
				}
			}
			//全部消费记录
			$condition['isPlus'] = false;
			$allConsume = $this->getPipiEggsConsumeSum($uids,'all',$condition);
			if (isset($allConsume['list'])){
				foreach($allConsume['list'] as $v){
					$userList['list'][$v['uid']]['allConsume'] = $v['sum_pipiegg'];
				}
			}
			
			//十五天内充值
			$condition['isPlus'] = true;
			$halfMonthCash = $this->getPipiEggsConsumeSum($uids,'halfMonth');
			if (isset($halfMonthCash['list'])){
				foreach($halfMonthCash['list'] as $v){
					$userList['list'][$v['uid']]['halfMonthCash'] = $v['sum_pipiegg'];
				}
			}
			//十五天内消费
			$condition['isPlus'] = false;
			$halfMonthConsume = $this->getPipiEggsConsumeSum($uids,'halfMonth');
			if (isset($halfMonthConsume['list'])){
				foreach($halfMonthConsume['list'] as $v){
					$userList['list'][$v['uid']]['halfMonthConsume'] = $v['sum_pipiegg'];
				}
			}
			
			//时间范围内充值
			$condition['isPlus'] = true;
			$rangeCash = $this->getPipiEggsConsumeSum($uids,'custom',$condition);
			if (isset($rangeCash['list'])){
				foreach($rangeCash['list'] as $v){
					$userList['list'][$v['uid']]['rangeCash'] = $v['sum_pipiegg'];
				}
			}
			//全部消费记录
			$condition['isPlus'] = false;
			$rangeConsume = $this->getPipiEggsConsumeSum($uids,'custom',$condition);
			if (isset($rangeConsume['list'])){
				foreach($rangeConsume['list'] as $v){
					$userList['list'][$v['uid']]['rangeConsume'] = $v['sum_pipiegg'];
				}
			}
		}

		if(!$isLimit){
			$this->dlPaySearchExcel($userList);
		}
		
		//分页实例化
		$pager = new CPagination($userList['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
		$this->render('user_pay_search', array('pager' => $pager, 'userList' => $userList['list'],
			'condition' => $condition));
	}
	
	/**
	 * 点歌查询
	 */
	public function actionVODQuery(){
		$this->assetsMy97Date();
		$condition = $this->getVodSearchCondition();
		
		//是否下载Excel
		$isLimit = true;
		if ($this->op == 'dlVODQueryExcel' && in_array($this->op, $this->allowOp)){
			$isLimit = false;
		}
	
		$doteySongSer = new DoteySongService();
		//获取记录列表
		$songRecords = $doteySongSer->searchVODRecordsByCondition($this->offset,$this->pageSize,$condition,$isLimit);
		$songRecords['handlers'] = $doteySongSer->getDoteySongHandler();
	
		if (!empty($songRecords['list'])){
			$uids = array();
			$to_uids = array();
				
			foreach ($songRecords['list'] as $v){
				$uids[$v['uid']] = $v['uid'];
				$to_uids[$v['to_uid']] = $v['to_uid'];
			}
			if($to_uids){
				$songRecords['doteyInfos'] = $this->userSer->getUserBasicByUids($to_uids);
			}
			if($uids){
				$songRecords['userInfos'] = $this->userSer->getUserBasicByUids($uids);
			}
		}
		if (!$isLimit){
			$this->dlVODQueryExcel($songRecords);
		}
	
		if(isset($condition['to_uids'])){
			unset($condition['to_uids']);
		}
		//分页实例化
		$pager = new CPagination($songRecords['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
		$this->render('user_vod_query',array('pager'=>$pager,'records'=>$songRecords,'condition'=>$condition));
	}
	
	/**
	 * 有播点歌统计
	 */
	public function actionVODStat(){
		$this->assetsMy97Date();
		$condition = $this->getVodSearchCondition();
		//是否下载Excel
		$isLimit = true;
		if ($this->op == 'dlVODStatExcel' && in_array($this->op, $this->allowOp)){
			$isLimit = false;
		}
	
	
		$doteySongSer = new DoteySongService();
		//是否是唱区主播
		if(isset($condition['dotey_cat']) && $condition['dotey_cat'] == 2){
			$channelSer = new ChannelService();
			$songCondition['channel_name'] = CHANNEL_THEME;
			$songCondition['sub_name'] = CHANNEL_THEME_SONG;
			if(isset($condition['to_uids'])){
				$songCondition['uid'] = $condition['to_uids'];
			}
			if(isset($condition['to_uid'])){
				$songCondition['uid'] = $condition['to_uid'];
			}
			$info = $channelSer->getDoteysOfSong();
			if($info){
				foreach($info as $v){
					$condition['to_uids'][] = $v['uid'];
				}
			}
		}
		//获取记录列表
		$songRecords = $doteySongSer->searchVODStatByCondition($this->offset,$this->pageSize,$condition,$isLimit,false);
	
		if (!empty($songRecords['list'])){
			$uids = array();
			$to_uids = array();
			foreach ($songRecords['list'] as $v){
				if (isset($v['to_uid'])){
					$uids[$v['to_uid']] = $v['to_uid'];
				}
				$uids[$v['uid']] = $v['uid'];
			}
			if($to_uids){
				$songRecords['doteyInfos'] = $this->userSer->getUserBasicByUids($to_uids);
			}
			if($uids){
				$songRecords['userInfos'] = $this->userSer->getUserBasicByUids($uids);
			}
		}
	
		if (!$isLimit){
			$this->dlVODStatExcel($songRecords);
		}
	
		if(isset($condition['uids'])){
			unset($condition['uids']);
		}
		//分页实例化
		$pager = new CPagination($songRecords['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
		$this->render('user_vod_stat',array('pager'=>$pager,'records'=>$songRecords,'condition'=>$condition));
	}
	
	public function actionUserBind(){
		exit('这张表反映的数据不是绑定状态，只是手机或邮件的发送记录，咱不能做状态查询');
		$condition = $this->getSearchCondition();
		if ($uid = Yii::app()->request->getParam('uid')){
			$condition['uid'] = $uid;
		}
		
		if (!empty($condition['bind_tel'])){
			$condition['method'][]=$condition['bind_tel'];
		}
		
		if (!empty($condition['bind_email'])){
			$condition['method'][]=$condition['bind_email'];
		}
		
		$service = new UserBindService();
		$bindList = $service->searchUserBind($condition,$this->offset,$this->pageSize);
		$uinfos = array();
		if($bindList['list']){
			$uids = array();
			foreach($bindList['list'] as $v){
				$uids[$v['uid']] = $v['uid'];
			}
			$uinfos = $this->userSer->getUserBasicByUids($uids);
		}
		
		//分页实例化
		$pager = new CPagination($bindList['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
		$this->render('user_bind_status',array('pager'=>$pager,'uinfos'=>$uinfos,'list'=>$bindList['list'],'condition'=>$condition));
	}
	
	/**
	 * 批量更改用户状态
	 * @param array $uids
	 */
	public function batchUpdateUS(array $uids){
		if($uids){
			foreach($uids as $uid){
				$this->userSer->saveUserJson($uid, array('user_status'=>USER_STATUS_OFF));
				//存储被操作记录
				$records = array();
				$records['op_desc'] = '批量封号操作';
				$records['uid'] = $uid;
				$records['op_uid'] = $this->op_uid;
				$records['op_type'] = USER_OPERATED_TYPE_USERSTATUS;
				$records['op_value'] = USER_STATUS_OFF;
				$this->userSer->saveUserOperated($records);
			}
			exit('1');
		}else{
			exit('参数有误');
		}
	}
	
	/**
	 * 执行编辑用户基本资料的动作
	 */
	public function upDataUinfoDo(){
		if(!($uid = Yii::app()->request->getParam('uid'))){
			exit("缺少参数");
		}
		
		$upData = Yii::app()->request->getParam('user');
		if($upData){
			$redirectUrl = $this->createUrl('user/usersearch',array('uid'=>$uid));
			return $this->updateUserInfo($uid, $upData,$redirectUrl);
		}
	}
	
	/**
	 * 下载用户送礼明细记录
	 * 
	 * @param int $uid
	 * @param array $condition
	 */
	public function dlSendGiftExcel($sendRecords){
		header("content-Type: text/html; charset=UTF8");
		if($sendRecords){
			$fileName = "用户送礼明细记录_".date('Ymd',time()).'.csv';
			$this->userSer->saveAdminOpLog('下载报表(file='.$fileName.')');
			
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
			
			$temp_arr = array('礼物合计：'.$sendRecords['count'],'用户合计：'.$sendRecords['remDuplicateCount'],'消费合计：'.$sendRecords['pipieggSum']);
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
			
			$temp_arr = array('发送者(UID)','接收者(UID)','礼物名称','礼物数量','消费皮蛋','送礼时间');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
			
			$list = $sendRecords['list'];
			foreach ($list as $v) {
				$info = unserialize($v['info']);
				$sendTime = date('Y-m-d',$v['create_time']);
				$sender = isset($info['sender'])?$info['sender']:'';
				$receiver = isset($info['receiver'])?$info['receiver']:'';
				$giftName = isset($info['gift_zh_name'])?$info['gift_zh_name']:'';
				$num = isset($v['num'])?$v['num']:'';
				$pipiegg = isset($v['pipiegg'])?$v['pipiegg']:'';
				
				$temp_arr = array($sender."({$v['uid']})",$receiver."({$v['to_uid']})",$giftName,$num,$pipiegg,$sendTime);
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
			}
			fclose($output) or die("Can't close php://output");
		}
		exit;
	}
	
	/**
	 * 下载用户送礼明细记录
	 *
	 * @param int $uid
	 * @param array $condition
	 */
	public function dlUGiftStatExcel($sendRecords){
		header("content-Type: text/html; charset=UTF8");
		if($sendRecords){
			$fileName = "用户送礼统计_".date('Ymd',time()).'.csv';
			$this->userSer->saveAdminOpLog('下载报表(file='.$fileName.')');
				
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
				
			$temp_arr = array('合计：'.$sendRecords['count']);
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
				
			$temp_arr = array('发送者(UID)','接收者(UID)','皮蛋消费','总的魅力点');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
				
			$list = $sendRecords['list'];
			foreach ($list as $v) {
				$info = unserialize($v['info']);
				$sender = isset($info['sender'])?$info['sender']:'';
				$sender .= '('.$v['uid'].')';
				$receiver = isset($info['receiver'])?$info['receiver']:'';
				$receiver .= "({$v['to_uid']})";
				$pipiegg = isset($v['sum_pipiegg'])?$v['sum_pipiegg']:'';
				$charmPoints = isset($v['sum_charm_points'])?$v['sum_charm_points']:'';
	
				$temp_arr = array($sender,$receiver,$pipiegg,$charmPoints);
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
			}
			fclose($output) or die("Can't close php://output");
		}
		exit;
	}
	
	/**
	 * 下载违规用户报表
	 * 
	 * @param array $list
	 */
	public function dlViolationExcel(Array $list){
		if($list['list']){
			$fileName = "用户违规记录_".date('Ymd',time()).'.csv';
			$this->userSer->saveAdminOpLog('下载报表(file='.$fileName.')');
			$userRank = $this->formatUserRank();
			$doteyRank = $this->formatDoteyRank();
			
			header("content-Type: text/html; charset=UTF8");
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
			
			$temp_arr = array('UID','账号','昵称','禁用时间','禁用原因','用户类型','用户总消费','15天消费','富豪等级','主播等级');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
			foreach ($list['list'] as $v) {
				$c1 = $v['uid'];
				$c2 = $v['username'];
				$c3 = $v['nickname'];
				$c4 = 'null';
				$c5 = 'null';
				if(isset($v['reason'])){
	  				$c4 = date('Y-m-d H:i:s',$v['reason']['op_time']);
	  				$c5 = $v['reason']['op_desc'];
	  			}
	  			$c6 = implode(',',$this->userSer->checkUserType($v['user_type'],true));
	  			$c7 = $v['allConsume'];
	  			$c8 = $v['halfMonthConsume'];
	  			$c9 = 'null';
	  			$c10 = 'null';
	  			if(isset($v['consumeList'])){
	  				$_rank = $v['consumeList']['rank'];
	  				$c9 = $userRank[$_rank];
	  				$_drank = $v['consumeList']['dotey_rank'];
	  				$c10 = $doteyRank[$_drank];
	  			}
	  			
				$temp_arr = array($c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10);
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
			}
			fclose($output) or die("Can't close php://output");
		}
		exit;
	}
	
	/**
	 * 下载违规用户报表
	 *
	 * @param array $list
	 */
	public function dlBriskExcel(Array $list){
		if($list['loginStatitics']){
			$fileName = "用户活跃度查询_".date('Ymd',time()).'.csv';
			$this->userSer->saveAdminOpLog('下载报表(file='.$fileName.')');
			$userRank = $this->formatUserRank();
			$doteyRank = $this->formatDoteyRank();
			
			header("content-Type: text/html; charset=UTF8");
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
			
			$temp_arr = array('登录账号总数:'.$list['loginUserCount'],'总注册账号总数:'.$list['regUserCount'],'活跃度:'.($list['briskPercent']*100).'%');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
			
			$temp_arr = array('UID','账号','昵称','注册时间','用户类型','最近登录时间','登录IP','累计登录天数','登录天数','富豪等级','主播等级','皮蛋消费');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
			if (isset($list['loginStatitics'])){
				foreach ($list['loginStatitics'] as $uinfo) {
					$c1 = $uinfo['uid'];
					$c2 = '';
					$c3 = '';
					$c4 = '';
					$c5 = '';
					if(isset($uinfo['userInfo'])){
						$c2 = $uinfo['userInfo']['username'];
						$c3 = $uinfo['userInfo']['nickname'];
						$c4 = date('Y-m-d H:i:s',$uinfo['userInfo']['create_time']);
						$c5 = implode(',',$this->userSer->checkUserType($uinfo['userInfo']['user_type'],true));
					}
					$c6 = date('Y-m-d H:i:s',$uinfo['max_login_time']);
					$c7 = $uinfo['login_ip'];
					$c8 = '';
					if(isset($uinfo['all_logins'])){
						$c8 = $uinfo['all_logins'];
					}
					$c9 = $uinfo['logins'];
					$c10 = '';
					$c11 = '';
					$c12 = 0;
					if(isset($uinfo['consumeInfo'])){
						$_rank = $uinfo['consumeInfo']['rank'];
						$c10 = $userRank[$_rank];
						$_rank = $uinfo['consumeInfo']['dotey_rank'];
						$c11 = $doteyRank[$_rank];
						$c12 = $uinfo['consumeInfo']['consume_pipiegg'];
					}
				
					$temp_arr = array($c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12);
					foreach($temp_arr as $k=>$v){
						$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
					}
					fputcsv($output,$temp_arr);
				}
			}
			
			fclose($output) or die("Can't close php://output");
		}
		exit;
	}
	
	/**
	 * 下载付费查询报表
	 *
	 * @param array $list
	 */
	public function dlPaySearchExcel(Array $list){
		if($list['list']){
			$fileName = "用户付费查询记录_".date('Ymd',time()).'.csv';
			$this->userSer->saveAdminOpLog('下载报表(file='.$fileName.')');
			$userRank = $this->formatUserRank();
			$doteyRank = $this->formatDoteyRank();
				
			header("content-Type: text/html; charset=UTF8");
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
				
			$temp_arr = array('UID','账号','昵称','注册时间','富豪等级','主播等级','用户类型','累计充值','累计消费','15天充值','15天消费','有效范围内充值','有效范围内消费');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
			foreach ($list['list'] as $v) {
				$c1 = $v['uid'];
				$c2 = $v['username'];
				$c3 = $v['nickname'];
				$c4 = date('Y-m-d H:i:s',$v['create_time']);
				if(isset($v['consumeList'])){
					$_rank = $v['consumeList']['rank'];
					$c5 = $userRank[$_rank];
					$_drank = $v['consumeList']['dotey_rank'];
					$c6 = $doteyRank[$_drank];
				}
				$c7 = implode(',',$this->userSer->checkUserType($v['user_type'],true));
				$c8 = $v['allCash'];
				$c9 = $v['allConsume'];
				$c10 = $v['halfMonthCash'];
				$c11 = $v['halfMonthConsume'];
				$c12 = $v['rangeCash'];
				$c13 = $v['rangeConsume'];
				
				$temp_arr = array($c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12,$c13);
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
			}
			fclose($output) or die("Can't close php://output");
		}
		exit;
	}
	
	/**
	 * 下载主播点歌明细记录
	 *
	 * @param int $uid
	 * @param array $condition
	 */
	public function dlVODQueryExcel($songRecords){
		header("content-Type: text/html; charset=UTF8");
		if($songRecords){
			$fileName = "主播点歌明细记录_".date('Ymd',time()).'.csv';
			$this->userSer->saveAdminOpLog('下载报表(file='.$fileName.')');
	
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
	
			$temp_arr = array('合计：'.$songRecords['count']);
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
	
			$temp_arr = array('用户(UID)','主播(UID)','档期ID','歌曲名','歌手','魅力值','魅力点','皮蛋消费','贡献值','皮点','点歌时间','状态');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
	
			$list = $songRecords['list'];
			foreach ($list as $r) {
				$c1 = $songRecords['userInfos'][$r['uid']]['nickname'].'('.$r['uid'].')';
				$c2 = $songRecords['doteyInfos'][$r['to_uid']]['nickname'].'('.$r['to_uid'].')';
				$c3 = $r['target_id'];
				$c4 = $r['name'];
				$c5 = $r['singer'];
				$c6 = $r['charm'];
				$c7 = $r['charm_points'];
				$c8 = $r['pipiegg'];
				$c9 = $r['dedication'];
				$c10 = $r['egg_points'];
				$c11 = date('Y-m-d H:i:s',$r['create_time']);
				$c12 = $songRecords['handlers'][$r['is_handle']];
	
				$temp_arr = array($c1,$c2,$c3,$c4,$c5,$c6,$c7,$c8,$c9,$c10,$c11,$c12);
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
			}
			fclose($output) or die("Can't close php://output");
		}
		exit;
	}
	
	/**
	 * 下载主播点歌统计
	 *
	 * @param int $uid
	 * @param array $condition
	 */
	public function dlVODStatExcel($songRecords){
		header("content-Type: text/html; charset=UTF8");
		if($songRecords){
			$fileName = "主播点歌统计记录_".date('Ymd',time()).'.csv';
			$this->userSer->saveAdminOpLog('下载报表(file='.$fileName.')');
	
			$output = fopen('php://output','w') or die("Can't open php://output");
			header("Content-Type: application/force-download");
			header('Content-Disposition: attachment;filename="'.$fileName.'"');
			header('Cache-Control: max-age=0');
			header("Content-Transfer-Encoding: binary");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: no-cache");
	
			$temp_arr = array('合计：'.$songRecords['count']);
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
	
			$temp_arr = array('用户名(UID)','魅力值','魅力点','皮蛋消费','贡献值','皮点','总点歌数');
			foreach($temp_arr as $k=>$v){
				$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
			}
			fputcsv($output,$temp_arr);
	
			$list = $songRecords['list'];
			foreach ($list as $r) {
				$c1 = $songRecords['userInfos'][$r['uid']]['nickname'].'('.$r['uid'].')';
				$c2 = $r['sum_charm'];
				$c3 = $r['sum_charm_points'];
				$c4 = $r['sum_pipiegg'];
				$c5 = $r['sum_dedication'];
				$c6 = $r['sum_egg_points'];
				$c7 = $r['count'];
	
				$temp_arr = array($c1,$c2,$c3,$c4,$c5,$c6,$c7);
				foreach($temp_arr as $k=>$v){
					$temp_arr[$k] = mb_convert_encoding($v, "GBK", "UTF-8");
				}
				fputcsv($output,$temp_arr);
			}
			fclose($output) or die("Can't close php://output");
		}
		exit;
	}
	
	/**
	 * 获取查询条件
	 * @return Ambigous <mixed, unknown>|Ambigous <mixed, multitype:unknown , unknown>
	 */
	public function getSearchCondition(){
		$condition = array();
		if(Yii::app()->request->getParam('form')){
			return Yii::app()->request->getParam('form');
		}
		
		if(Yii::app()->request->getParam('username')){
			$condition['username'] = Yii::app()->request->getParam('username');
		}
		
		if(Yii::app()->request->getParam('realname')){
			$condition['realname'] = Yii::app()->request->getParam('realname');
		}
		
		if(Yii::app()->request->getParam('nickname')){
			$condition['nickname'] = Yii::app()->request->getParam('nickname');
		}
		
		if(Yii::app()->request->getParam('user_type') ){
			$condition['user_type'] = Yii::app()->request->getParam('user_type');
		}
		
		if(Yii::app()->request->getParam('user_status')){
			$condition['user_status'] = Yii::app()->request->getParam('user_status');
		}
		
		if(Yii::app()->request->getParam('uid')){
			$condition['uid'] = Yii::app()->request->getParam('uid');
		}
		
		if(Yii::app()->request->getParam('reg_ip')){
			$condition['reg_ip'] = Yii::app()->request->getParam('reg_ip');
		}
		if(Yii::app()->request->getParam('reg_ip_count')){
			$condition['reg_ip_count'] = Yii::app()->request->getParam('reg_ip_count');
		}
		
		if(Yii::app()->request->getParam('end_time')){
			$condition['end_time'] = Yii::app()->request->getParam('end_time');
		}
		
		if(Yii::app()->request->getParam('start_time')){
			$condition['start_time'] = Yii::app()->request->getParam('start_time');
		}
		
		if(Yii::app()->request->getParam('user_rank')){
			$condition['user_rank'] = Yii::app()->request->getParam('user_rank');
		}
		
		if(Yii::app()->request->getParam('dotey_rank')){
			$condition['dotey_rank'] = Yii::app()->request->getParam('dotey_rank');
		}
		
		if(Yii::app()->request->getParam('remDuplicate')){
			$condition['remDuplicate'] = Yii::app()->request->getParam('remDuplicate');
		}
		
		if(Yii::app()->request->getParam('bind_tel')){
			$condition['bind_tel'] = Yii::app()->request->getParam('bind_tel');
		}
		
		if(Yii::app()->request->getParam('bind_email')){
			$condition['bind_email'] = Yii::app()->request->getParam('bind_email');
		}
		
		if(Yii::app()->request->getParam('login_ip')){
			$condition['login_ip'] = Yii::app()->request->getParam('login_ip');
		}
		
		if(Yii::app()->request->getParam('condition')){
			$condition = json_decode(Yii::app()->request->getParam('condition'),true);
		}
		
		return $condition;
	}
	
	/**
	 * 获取查询条件
	 * @return Ambigous <mixed, unknown>|Ambigous <mixed, multitype:unknown , unknown>
	 */
	public function getGiftCondition(){
		$condition = array();
		if(Yii::app()->request->getParam('sendgift')){
			$condition = Yii::app()->request->getParam('sendgift');
		}
	
		if(Yii::app()->request->getParam('uid')){
			$condition['uid'] = Yii::app()->request->getParam('uid');
		}
		
		if(Yii::app()->request->getParam('username')){
			$condition['username'] = Yii::app()->request->getParam('username');
		}
	
		if(Yii::app()->request->getParam('realname')){
			$condition['realname'] = Yii::app()->request->getParam('realname');
		}
	
		if(Yii::app()->request->getParam('nickname')){
			$condition['nickname'] = Yii::app()->request->getParam('nickname');
		}
	
		if(Yii::app()->request->getParam('end_time')){
			$condition['end_time'] = Yii::app()->request->getParam('end_time');
		}
	
		if(Yii::app()->request->getParam('start_time')){
			$condition['start_time'] = Yii::app()->request->getParam('start_time');
		}
	
		if(Yii::app()->request->getParam('user_rank')){
			$condition['user_rank'] = Yii::app()->request->getParam('user_rank');
		}
	
		if(Yii::app()->request->getParam('dotey_rank')){
			$condition['dotey_rank'] = Yii::app()->request->getParam('dotey_rank');
		}
	
		if(Yii::app()->request->getParam('remDuplicate')){
			$condition['remDuplicate'] = Yii::app()->request->getParam('remDuplicate');
		}
		
		if(!empty($condition)){
			foreach($condition as $k=>$c){
				if (empty($c)){
					unset($condition[$k]);
				}
			}
		}
		
		return $condition;
	}
	
	public function getVodSearchCondition(){
		$condition = Yii::app()->request->getParam('vod');
		if(!$condition){
			if($to_uid = Yii::app()->request->getParam('to_uid')){
				$condition['to_uid'] = $to_uid;
			}
			
			if($uid = Yii::app()->request->getParam('uid')){
				$condition['uid'] = $uid;
			}
			
			if($dotey_cat = Yii::app()->request->getParam('dotey_cat')){
				$condition['dotey_cat'] = (int)$dotey_cat;
			}
			
			if($start_time = Yii::app()->request->getParam('start_time')){
				$condition['start_time'] = $start_time;
			}
			
			if($end_time = Yii::app()->request->getParam('end_time')){
				$condition['start_time'] = $end_time;
			}
			$is_handle = Yii::app()->request->getParam('is_handle');
			if((int)$is_handle >= 0){
				$condition['is_handle'] = $is_handle;
			}
			
			if(Yii::app()->request->getParam('username')){
				$condition['username'] = Yii::app()->request->getParam('username');
			}
			
			if(Yii::app()->request->getParam('realname')){
				$condition['realname'] = Yii::app()->request->getParam('realname');
			}
			
			if(Yii::app()->request->getParam('nickname')){
				$condition['nickname'] = Yii::app()->request->getParam('nickname');
			}
		}
		
		
		foreach($condition as $k=>$v){
			if(is_numeric($v)){
				if (!($v >= 0)){
					unset($condition[$k]);
				}
			}elseif (empty($v)){
				unset($condition[$k]);
			}
		}
		return $condition?$condition:array();
	}
	
	public function getUserRk(){
		$consumeService = new ConsumeService();
		return $consumeService->getUserRankFromRedis();
	}
	
	public function getDoteyRk(){
		$consumeService = new ConsumeService();
		return $consumeService->getDoteyRankFromRedis();
	}
	
	/**
	 * @param string $flag all|tel|email
	 * @return multitype:string 
	 */
	public function bindStatus($flag = 'all'){
		if($flag == 'tel'){
			return array('1'=>'已绑定手机','-1'=>'解除手机绑定');
		}elseif ($flag == 'email'){
			return array('2'=>'已绑定邮箱','-2'=>'解除邮箱绑定');
		}else{
			return array('1'=>'已绑定手机','-1'=>'解除手机绑定','2'=>'已绑定邮箱','-2'=>'解除邮箱绑定');
		}
	}
	
	public function formatUserRank(){
		$result = array();
		$consumeSer = new ConsumeService();
		$ranks = $consumeSer->getUserRankFromRedis();
		if($ranks){
			foreach($ranks as $rank){
				$result[$rank['rank']] = $rank['name'];
			}
		}
		return $result;
	}
	
	public function formatDoteyRank(){
		$result = array();
		$consumeSer = new ConsumeService();
		$ranks = $consumeSer->getDoteyRankFromRedis();
		if($ranks){
			foreach($ranks as $rank){
				$result[$rank['rank']] = $rank['name'];
			}
		}
		return $result;
	}
	
	/**
	 * 获取家族信息
	 *
	 * @param array $list
	 */
	public function getFamilyInfo(Array &$list){
		if($list){
			$uids = array_keys($list);
			$famService = new FamilyService();
			$infos = $famService->getMembersGroupByUids($uids);
			if($infos){
				$famMembers = array();
				$familyIds = array();
				foreach ($infos as $k=>$info){
					$familyIds[$info['family_id']] =  $info['family_id'];
					$famMembers[$info['uid']][$info['family_id']] = $info['family_id'];
				}
				if ($familyIds){
					$famInfos = $famService->getFamilyIds($familyIds);
					if ($familyIds){
						foreach ($famMembers as $uid=>$v){
							foreach ($v as $_familyId){
								$list[$uid]['family'][$_familyId]['family_id'] = $_familyId;
								$list[$uid]['family'][$_familyId]['family_name'] = isset($famInfos[$_familyId])?$famInfos[$_familyId]['name']:'';
							}
						}
					}
				}
			}
		}
	}
	
	private function _bind(){
		$uid = Yii::app()->request->getParam('uid');
		exit($this->renderPartial('user_bind', array('uid'=>$uid)));
	} 
	
	private function _bindDo(){
		$uid = Yii::app()->request->getParam('uid');
		$bind_type = Yii::app()->request->getParam('bind_type');
		$reg_mobile = Yii::app()->request->getParam('reg_mobile','');
		$data = array();
		if($bind_type == 'unbind_all'){
			$data['reg_email'] = '';
		}
		if($uid){
			$uBind = $this->userSer->saveUserBasic(array_merge(array('uid'=>$uid,'reg_mobile'=>$reg_mobile), $data));
			if($uBind){
				exit('1');
			}
			exit('绑定操作失败');
		}
		exit('绑定操作失败 参数有误');
	}
	
	private function _loginVerify(){
		$uid = Yii::app()->request->getParam('uid');
		$extend = $this->userSer->getUserExtendByUids(array($uid));
		if(!empty($extend)){
			$extend = $extend[$uid];
			$extend['login_verify'] ^= 1;
		}else{
			$extend['uid'] = $uid;
			$extend['login_verify'] = 1;
		}
		if($this->userSer->saveUserExtend($extend)) exit('1');
		else exit('操作失败');
	}
}

