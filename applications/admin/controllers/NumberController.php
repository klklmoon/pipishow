<?php
/**
 * 靓号管理
 * 
 * @author supeng
 */
class NumberController extends PipiAdminController {
	
	/**
	 * @var UserNumberService
	 */
	protected $numService;
	
	/**
	 * @var UserService
	 */
	protected $userService;
	
	/**
	 * @var array
	 */
	protected $allowOp = array('delNumber','checkNumberUsed','checkUserInfo','recoverUserNumber');
	
	/**
	 * @var string 当前操作
	 */
	protected $op;
	
	/**
	 * @var boolean 是否是Ajax请求
	 */
	protected $isAjax;
	
	protected $pageSize = 20;
	
	protected $offset;
	
	/**
	 * @var int page lable
	 */
	protected $p;
	
	public function init(){
		parent::init();
		$this->numService = new UserNumberService();
		$this->userService = new UserService();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
	}
	
	/**
	 * 靓号列表
	 */
	public function actionList(){
		if($this->op == 'delNumber' && in_array($this->op, $this->allowOp)){
			$this->_delNumber();
		}
		
		$this->assetsMy97Date();
		$condition = $this->_getSearchCondition();
		$list = $this->numService->searchNumberList($condition,$this->offset,$this->pageSize);
		
		$pager = new CPagination($list['count']);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		$this->render('number_list',array('condition'=>$condition,'pager'=>$pager,'list'=>$list));
	}
	
	/**
	 * 添加编辑靓号 
	 */
	public function actionAddNumber(){
		$number = intval(Yii::app()->request->getParam('number',false));
		$numbers = Yii::app()->request->getParam('numbers',array());
		$isNew = Yii::app()->request->getParam('isNew',true);
		$numberInfo = array();
		if($number){
			$numberInfo = $this->numService->getNumberById($number);
		}
		
		if($numbers){
			if ($isNew){
				$numberInfo = $this->numService->getNumberById($numbers['number']);
				if($numberInfo){
					$notices = array(array('该靓号已经存在，只能进行编辑'));
					$this->render('number_add',array('numberInfo'=>$numberInfo,'notices'=>$notices));
					exit;
				}
			}
			if($this->numService->saveNumber($numbers)){
				$this->redirect($this->createUrl('number/list',array('number'=>$numbers['number'])));
			}else{
				exit($this->render('number_add',array('numberInfo'=>$numberInfo,'notices'=>$this->numService->getNotice())));
			}
		}else{
			exit($this->renderPartial('number_add',array('numberInfo'=>$numberInfo)));
		}
		exit('失败');
	}
	
	/**
	 * 靓号回收记录 
	 */
	public function actionRecover(){
		$this->assetsMy97Date();
		$condition = $this->_getSearchCondition();
		$list = $this->numService->searchUserNumberRecoverList($condition,$this->offset,$this->pageSize);
		$uinfos = array();
		
		if($list['list']){
			$uids = array();
			foreach($list['list'] as $v){
				$uids[$v['uid']] = $v['uid'];
				$uids[$v['opertor_uid']] = $v['opertor_uid'];
			}
				
			if ($uids){
				$uinfos = $this->userService->getUserBasicByUids($uids);
			}
		}
		$pager = new CPagination($list['count']);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('number_recover_list',array('condition'=>$condition,'pager'=>$pager,'list'=>$list,'uinfos' => $uinfos));
	}
	
	/**
	 * 用户靓号列表 
	 */
	public function actionUserNumber(){
		if($this->op == 'recoverUserNumber' && in_array($this->op, $this->allowOp)){
			$this->_recoverUserNumber();
		}
		
		$this->assetsMy97Date();
		$condition = $this->_getSearchCondition();
		$list = $this->numService->searchUserNumberList($condition,$this->offset,$this->pageSize);
		$uinfos = array();
		
		if($list['list']){
			$uids = array();
			foreach($list['list'] as $v){
				$uids[$v['uid']] = $v['uid'];
			}
			
			if ($uids){
				$uinfos = $this->userService->getUserBasicByUids($uids);
			}
		}
		$pager = new CPagination($list['count']);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('number_user_list',array('condition'=>$condition,'pager'=>$pager,'list'=>$list,'uinfos' => $uinfos));
	}
	
	/**
	 * 添加用户靓号 
	 */
	public function actionAddUserNumber(){
		if($this->op == 'checkNumberUsed' && in_array($this->op, $this->allowOp)){
			$this->_checkNumberUsed();
		}
		
		if($this->op == 'checkUserInfo' && in_array($this->op, $this->allowOp)){
			$this->_checkUserInfo();
		}
		
		$number = Yii::app()->request->getParam('number');
		if ($number) {
			$numberInfo = $this->numService->getNumberById($number);
			$numbers = Yii::app()->request->getParam('numbers');
			$last_recharge_time = (isset($numbers['last_recharge_time']) && $numbers['last_recharge_time']>0)?$numbers['last_recharge_time']*86400+time():0;
			if ($numberInfo){
				$notices = array();
				if($numbers && is_array($numbers)){
					if(isset($numbers['uid'])){
						if($this->numService->adminSendNumber($numbers['uid'], $numberInfo['number'],$last_recharge_time)){
							$this->redirect($this->createUrl('number/userNumber',array('uid'=>$numbers['uid'])));
						}else{
							$notices = $this->numService->getNotice();
						}
					}else{
						$notices = array(array('赠送对象不能为空'));
					}
				}
				
				if ($this->isAjax){
					exit($this->renderPartial('number_user_add',array('numberInfo'=>$numberInfo)));
				}else{
					$this->render('number_user_add',array('numberInfo'=>$numberInfo,array('notices'=>$notices)));
				}
			}
			exit('不存在该靓号信息');
		}
		exit('参数信息有误');
	}
	
	/**
	 * 靓号购买记录 
	 */
	public function actionBuyRecords(){
		$this->assetsMy97Date();
		$condition = $this->_getSearchCondition();
		$list = $this->numService->searchBuyNumberRecordsList($condition,$this->offset,$this->pageSize);
		$uinfos = array();
		
		if($list['list']){
			$uids = array();
			foreach($list['list'] as $v){
				$uids[$v['uid']] = $v['uid'];
				$uids[$v['proxy_uid']] = $v['proxy_uid'];
				$uids[$v['sender_uid']] = $v['sender_uid'];
			}
		
			if ($uids){
				$uinfos = $this->userService->getUserBasicByUids($uids);
			}
		}
		$pager = new CPagination($list['count']);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('number_buy_records',array('condition'=>$condition,'pager'=>$pager,'list'=>$list,'uinfos' => $uinfos));
	}
	
	private function _delNumber(){
		if (!$this->isAjax){
			exit('不是合法请求');
		}
		
		$number = Yii::app()->request->getParam('number');
		if($number){
			if($this->numService->delNumber($number)){
				exit('1');
			}else{
				exit('不存在该靓号信息');
			}
		}else{
			exit('参数有误');
		}
		exit('删除失败');
	}
	
	private function _recoverUserNumber(){
		if (!$this->isAjax){
			exit('不是合法请求');
		}
		
		$rid = Yii::app()->request->getParam('rid');
		$reason = Yii::app()->request->getParam('reason');
		if($rid && $reason){
			$rids = explode('_', $rid);
			$uid = $rids[0];
			$number = $rids[1];
			if(is_numeric($uid) && $number){
				$UNumber = $this->numService->isUseNumber($number,$uid);
				if($UNumber){
					$data['number'] = $UNumber['number'];
					$data['uid'] = $UNumber['uid'];
					$data['status'] = 1;
					if($this->numService->saveUserNumber($data)){
						$recover['uid'] = $UNumber['uid'];
						$recover['opertor_uid'] = $this->op_uid;
						$recover['record_id'] = $UNumber['record_id'];
						$recover['number'] = $UNumber['number'];
						$recover['recover_type'] = 1;
						$recover['reason'] = $reason;
						$this->numService->saveUserNumberRecover($recover);
						$this->numService->recoverUPropsAttr($UNumber['uid'],$UNumber['number']);
						exit('1');
					}
					exit('靓号回收失败，系统内部错误');
				}else{
					exit('该靓号无法进行回收处理');
				}
			}else{
				exit('参数有误');
			}
		}
		exit('回收失败');
	}
	
	private function _checkNumberUsed(){
		if (!$this->isAjax){
			exit('不是合法请求');
		}
	
		$number = Yii::app()->request->getParam('number');
		if($number){
			if($this->numService->getNumberById($number)){
				if($this->numService->isUseNumber($number)){
					exit('该靓号已经被使用了，不能赠送');
				}else{
					exit('1');
				}
			}else{
				exit('不存在该靓号信息');
			}
		}else{
			exit('参数有误');
		}
		exit('检测靓号有效性失败');
	}
	
	private function _updateListDo(){
		if (!$this->isAjax){
			exit('不是合法请求');
		}
	
		$type = Yii::app()->request->getParam('type');
		$familyId = Yii::app()->request->getParam('familyId');
		$reason = Yii::app()->request->getParam('reason');
		$value = Yii::app()->request->getParam('value');
		if(is_numeric($familyId) && in_array($type, array(0,1,2,3,4,5,6)) && $reason){
			$famInfo = $this->famService->getFamily($familyId);
			if($famInfo){
				$upData['id'] = $famInfo['id'];
				if (in_array($type, array(2,3))){
					$upData['hidden'] = $value; 
				}
				
				if (in_array($type, array(0,1))){
					$upData['status'] = $value;
				}
				
				if (in_array($type, array(4,5))){
					$upData['forbidden'] = $value;
				}
				
				if(count($upData) > 1){
					if($this->famService->saveFamily($upData)){
						$resonData['family_id'] = $famInfo['id'];
						$resonData['type'] = $type;
						$resonData['reason'] = $reason;
						$resonData['op_uid'] = $this->op_uid;
						$resonData['uid'] = $famInfo['uid'];
						$resonData['name'] = $famInfo['name'];
						$resonData['level'] = $famInfo['level'];
						$this->famService->saveOperateRcord($resonData);
						exit('1');
					}
				}
			}else{
				exit('不存在该家族信息');
			}
		}else{
			exit('参数有误');
		}
		exit('编辑失败');
	}
	
	private function _getSearchCondition(){
		$condition = Yii::app()->request->getParam('form');
		
		$uid = Yii::app()->request->getParam('uid');
		if (is_numeric($uid)){
			$condition['uid'] = $uid;
		}
		
		$sender_uid = Yii::app()->request->getParam('sender_uid');
		if (is_numeric($sender_uid)){
			$condition['sender_uid'] = $sender_uid;
		}
		
		$proxy_uid = Yii::app()->request->getParam('proxy_uid');
		if (is_numeric($proxy_uid)){
			$condition['proxy_uid'] = $proxy_uid;
		}
		
		$type = Yii::app()->request->getParam('type');
		if (is_numeric($type)){
			$condition['type'] = $type;
		}
		
		$status = Yii::app()->request->getParam('status');
		if (is_numeric($status)){
			$condition['status'] = $status;
		}
		
		$username = Yii::app()->request->getParam('username');
		if($username){
			$condition['username'] = $username;
		}
		
		$nickname = Yii::app()->request->getParam('nickname');
		if($nickname){
			$condition['nickname'] = $nickname;
		}
		
		$realname = Yii::app()->request->getParam('realname');
		if($realname){
			 $condition['realname'] = $realname;
		}
		
		$number = Yii::app()->request->getParam('number');
		if($number){
			$condition['number'] = $number;
		}
		
		
		$create_time_start = Yii::app()->request->getParam('create_time_start');
		if($create_time_start){
			 $condition['create_time_start'] = $create_time_start;
		}
		
		$create_time_end = Yii::app()->request->getParam('create_time_end');
		if($create_time_end){
			$condition['create_time_end'] = $create_time_end;
		}
		
		if($condition){
			foreach ($condition as $k=>&$v){
				if ($v == ''){
					unset($condition[$k]);
				}
			}
		}
		return $condition?$condition:array();
	}
	
	/**
	 * AJAX 检验主播信息的合法性
	 */
	private function _checkUserInfo(){
		if (!$this->isAjax){
			exit('不合法请求');
		}
	
		$doteyName = Yii::app()->request->getParam('doteyName');
		if(empty($doteyName)){
			exit('请输入用户信息后进行校验 ');
		}
	
		$userSer = new UserService();
		if(!is_numeric($doteyName)){
			if(!($userInfo = $userSer->getVadidatorUser($doteyName,0))){
				exit('不合法用户，请重新输入');
			}
			$uid = $userInfo['uid'];
		}else{
			$uid = (int)$doteyName;
		}
	
		if ($uid){
			if(!isset($userInfo)){
				if(!($userInfo = $userSer->getUserBasicByUids(array($uid)))){
					exit('不合法用户，请重新输入');
				}else{
					$userInfo = $userInfo[$uid];
				}
			}
			exit('1'.'#xx#'.$userInfo['uid'].'#xx#'.$userInfo['username'].'#xx#'.$userInfo['nickname'].'#xx#'.$userInfo['realname']);
		}else{
			exit('不合法用户，请重新输入');
		}
	}
}

?>