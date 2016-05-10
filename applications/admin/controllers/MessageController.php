<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author Su Peng <supeng@pipi.cn>
 * @version $Id: MessageController.php 10460 2013-05-20 12:52:25Z supeng $ 
 */
class MessageController extends PipiAdminController {
	
	/**
	 * @var MessageService 主播服务层
	 */
	public $service;
	
	/**
	 * @var array 允许的操作
	 */
	public $allowOp = array( 'addPush','addPushDo','delPush','getRank','checkTarget','checkUserInfo','addMessage','addMessageDo','getSCategory','delMessage' );

	/**
	 * @var string 当前操作
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
		$this->service = new MessageService();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
	}
	
	/**
	 * 消息提醒
	 */
	public function actionInfoCall(){
		//是否删除
		if($this->op == 'checkUserInfo' && in_array($this->op,$this->allowOp)){
			$this->checkUserInfo();
		}
		
		//是否删除
		if($this->op == 'delMessage' && in_array($this->op,$this->allowOp)){
			$this->delMessage();
		}
		
		//是否删除
		if($this->op == 'getSCategory' && in_array($this->op,$this->allowOp)){
			$this->getSCategory();
		}
		
		//是否新增与编辑
		if($this->op == 'addMessage' && in_array($this->op,$this->allowOp)){
			$notices = $this->addMessage();
		}
		
		//是否新增与编辑
		if($this->op == 'addMessageDo' && in_array($this->op,$this->allowOp)){
			$notices = $this->addMessageDo();
		}
		
		$condition = $this->getCondition();
		$data = $this->service->searchMessage($condition,$this->offset,$this->pageSize,true);
		$uinfos = array();
		if($data['list']){
			$uids = array();
			foreach ($data['list'] as &$v){
				$uids[$v['uid']] = $v['uid'];
				$uids[$v['receive_uid']] = $v['receive_uid'];
				if (isset($v['extra'])){
					$v['extra'] = json_decode($v['extra'],true);
				}
			}
			if($uids){
				$userService = new UserService();
				$uinfos = $userService->getUserBasicByUids($uids);
			}
		}
		
		$pager = new CPagination($data['count']);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		
		$this->render('message_info_call',array('data'=>$data,'uinfos'=>$uinfos,'pager'=>$pager,'condition'=>$condition));
	}
	
	/**
	 * 消息推送
	 */
	public function actionPush(){
		$this->assetsMy97Date();
		//是否删除
		if($this->op == 'checkTarget' && in_array($this->op,$this->allowOp)){
			$this->checkTarget();
		}
	
		//是否删除
		if($this->op == 'delPush' && in_array($this->op,$this->allowOp)){
			$this->delPush();
		}
	
		//是否新增与编辑
		if($this->op == 'addPush' && in_array($this->op,$this->allowOp)){
			$notices = $this->addPush();
		}
	
		//是否新增与编辑
		if($this->op == 'addPushDo' && in_array($this->op,$this->allowOp)){
			$notices = $this->addPushDo();
		}
		
		//是否新增与编辑
		if($this->op == 'getRank' && in_array($this->op,$this->allowOp)){
			$notices = $this->getRank();
		}
	
		$condition = $this->getCondition();
		$condition['type'] = isset($condition['type'])?$condition['type']:MESSAGE_PUSH_TYPE_GLOBAL;
		$data = $this->service->searchPush($condition,$this->offset,$this->pageSize,true);
	
		$pager = new CPagination($data['count']);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
	
		$this->render('message_push',array('data'=>$data,'pager'=>$pager,'condition'=>$condition,'t'=>$condition['type']));
	}
	
	public function delMessage(){
		$mid = Yii::app()->request->getParam('mid');
		if(is_numeric($mid)){
			if($this->service->delMessageByIds(array($mid))){
				exit('1');
			}
		}
		exit('删除失败');
	}
	
	public function delPush(){
		$pushId = Yii::app()->request->getParam('pushId');
		if(is_numeric($pushId)){
			if($this->service->delPushByIds(array($pushId))){
				exit('1');
			}
		}
		exit('删除失败');
	}
	
	public function addMessage(){
		$mid = (int)Yii::app()->request->getParam('mid');
		if($mid){
			if($mid <= 0){
				exit('获取编辑信息失败');
			}
		}
		exit($this->renderPartial('message_add_info_call'));
	}
	
	public function addPush(){
		$mid = (int)Yii::app()->request->getParam('mid');
		if($mid){
			if($mid <= 0){
				exit('获取编辑信息失败');
			}
		}
		exit($this->renderPartial('message_add_push'));
	}
	
	public function addMessageDo(){
		$form = Yii::app()->request->getParam('_form',array());
		if ($form){
			if(empty($form['uid']) || !isset($form['type']) || !isset($form['stype']) || empty($form['title']) || empty($form['content'])){
				exit('添加消息失败');
			}
			
			$uids = is_array($form['uid'])?$form['uid']:array($form['uid']);
			$message['uid'] = $this->op_uid;
			$message['title'] = $form['title'];
			$message['content'] = $form['content'];
			$message['category'] = $form['type'];
			$message['sub_category'] = $form['stype'];
			$message['is_read'] = 0;
			$message['extra'] = $form['extra'];
			$message['to_uid'] = (is_array($uids) && $uids)?implode(',', $uids):'';
			if($form['stype'] == MESSAGE_CATEGORY_SYSTEM_SITE) $message['is_site'] = 1;
			
			if(!$this->service->sendMessage($message)){
				print_r($this->service->getNotice());exit;
			}
			$this->redirect($this->createUrl('message/infoCall'));
		}
		exit($this->renderPartial('message_add_info_call'));
	}
	
	public function addPushDo(){
		$form = Yii::app()->request->getParam('_form',array());
		if ($form){
			if(!isset($form['type'])  || empty($form['content'])){
				exit('添加推送消息失败');
			}
				
			$type = $form['type'];
			$content = $form['content'];
			$title = isset($form['title'])?$form['title']:'';
			$tips = isset($form['tips'])?$form['tips']:'';
			$send_time = isset($form['send_time'])?((strtotime($form['send_time'])>time())?strtotime($form['send_time']):time()):time();
			if($type == MESSAGE_PUSH_TYPE_DOTEY || $type == MESSAGE_PUSH_TYPE_USER){
				$rank = $form['rank'];
			}else{
				$rank = '';
			}
			$create_time = time();
			if($type == MESSAGE_PUSH_TYPE_LIVE){
				$window = isset($form['window'])?$form['window']:0;
			}else{
				$window = '';
			}
			
			if($type == MESSAGE_PUSH_TYPE_GLOBAL){
				$target_id = '';
			}else{
				$target_id = (isset($form['target_id']) && is_array($form['target_id']))?implode(',', $form['target_id']):'';
				if($target_id){
					$rank = '';
				}
			}
			$extra = $form['extra'];
			$extra['uid'] = $this->op_uid;
			
			if($title){
				$push['title'] = $title;
			}
			$push['type'] = $type;
			$push['target_id'] = $target_id;
			$push['rank'] = $rank;
			$push['tips'] = $tips;
			$push['content'] = $content;
			$push['window'] = $window;
			$push['create_time'] = $create_time;
			$push['send_time'] = $send_time;
			$push['is_send'] = 0;
			$push['extra'] = $extra;
			
			if($this->service->pushMessage($push)){
				$this->redirect($this->createUrl('message/push',array('type'=>$type)));
			}else{
				$notices = $this->service->getNotice();
				$this->render('message_add_push',array('notices'=>$notices));
			}
		}
		exit('error');
	}
	
	/*
	 *  AJAX 检验主播信息的合法性
	 */
	public function checkUserInfo(){
		if (!$this->isAjax){
			exit('不合法请求');
		}
	
		$userName = Yii::app()->request->getParam('userName');
		if(empty($userName)){
			exit('请输入用户信息后进行校验 ');
		}
	
		$userSer = new UserService();
		$doteySer = new DoteyService();
		if(!is_numeric($userName)){
			if(!($userInfo = $userSer->getVadidatorUser($userName,0))){
				exit('不合法用户，请重新输入');
			}
			$uid = $userInfo['uid'];
		}else{
			$uid = (int)$userName;
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
	
	/**
	 * AJAX 检验主播信息的合法性
	 */
	public function checkDoteyInfo($doteyName){
		if (!$this->isAjax){
			exit('不合法请求');
		}
	
		if(empty($doteyName)){
			exit('请输入主播信息后进行校验 ');
		}
	
		$userSer = new UserService();
		if(!is_numeric($doteyName)){
			$userInfo = $userSer->getVadidatorUser($doteyName,0);
			if(!$userInfo){
				exit('不合法用户，请重新输入');
			}
			$uid = $userInfo['uid'];
		}else{
			$uid = (int)$doteyName;
		}
	
		if ($uid){
			$doteySer = new DoteyService();
			if(!($doteyInfo = $doteySer->getDoteyInfoByUid($uid))){
				exit('该用户不是主播，请确认');
			}
			
			if(!($userInfo = $userSer->getUserBasicByUids(array($uid)))){
				exit('不合法用户，请重新输入');
			}else{
				$userInfo = $userInfo[$uid];
				exit('1'.'#xx#'.$userInfo['uid'].'#xx#'.$userInfo['username'].'#xx#'.$userInfo['nickname'].'#xx#'.$userInfo['realname']);
			}
			exit('验证有误，请核对主播信息');
		}else{
			exit('不合法用户，请重新输入');
		}
	}
	
	public function checkLiveInfo($doteyName){
		if (!$this->isAjax){
			exit('不合法请求');
		}
		
		if(empty($doteyName)){
			exit('请输入主播信息后进行校验 ');
		}
		
		$userSer = new UserService();
		if(!is_numeric($doteyName)){
			$userInfo = $userSer->getVadidatorUser($doteyName,0);
			if(!$userInfo){
				exit('不合法用户，请重新输入');
			}
			$uid = $userInfo['uid'];
		}else{
			$uid = (int)$doteyName;
		}
		
		if ($uid){
			$doteySer = new DoteyService();
			if(!($doteyInfo = $doteySer->getDoteyInfoByUid($uid))){
				exit('该用户不是主播，请确认');
			}
			
			$archivesSer = new ArchivesService();
			$archivesInfo = $archivesSer->getArchivesByUids($uid,false);
			if($archivesInfo){
				$archivesInfo = array_shift($archivesInfo);
				exit('1'.'#xx#'.$archivesInfo['archives_id'].'#xx#'.$archivesInfo['title'].'#xx#'.$archivesInfo['uid'].'#xx#'.$archivesInfo['cat_id']);
			}
			exit('验证有误，请核对主播信息');
		}else{
			exit('不合法用户，请重新输入');
		}
	}
	
	/*
	 *  AJAX 检验主播信息的合法性
	*/
	public function checkTarget(){
		if (!$this->isAjax){
			exit('不合法请求');
		}
	
		$userName = Yii::app()->request->getParam('userName');
		$type = Yii::app()->request->getParam('type');
		if(empty($userName)){
			exit('请输入目标对象进行校验 ');
		}
		
		if (!in_array($type, array(MESSAGE_PUSH_TYPE_DOTEY,MESSAGE_PUSH_TYPE_LIVE,MESSAGE_PUSH_TYPE_USER))){
			exit('推送类型有误');
		}
	
		if($type == MESSAGE_PUSH_TYPE_USER){
			$this->checkUserInfo();
		}else if($type == MESSAGE_PUSH_TYPE_DOTEY){
			$this->checkDoteyInfo($userName);
		}else if($type == MESSAGE_PUSH_TYPE_LIVE){
			$this->checkLiveInfo($userName);
		}else{
			exit('检验目标对象失败');
		}
	}
	
	/**
	 * 获取查询条件
	 */
	public function getCondition(){
		$condition = Yii::app()->request->getParam('form',array());
		if($condition){
			return $condition;
		}
		$is_read = Yii::app()->request->getParam('is_read',null);
		if (is_numeric($is_read))
			$condition['is_read'] = $is_read;
		
		$is_send = Yii::app()->request->getParam('is_send',null);
		if (is_numeric($is_send))
			$condition['is_send'] = $is_send;
		
		$type = Yii::app()->request->getParam('type',null);
		if (is_numeric($type))
			$condition['type'] = $type;
		
		$target_id = Yii::app()->request->getParam('target_id',null);
		if (is_numeric($target_id))
			$condition['target_id'] = $target_id;
		
		$username = Yii::app()->request->getParam('username',null);
		if ($username)
			$condition['username'] = $username;
		
		$nickname = Yii::app()->request->getParam('nickname',null);
		if ($nickname)
			$condition['nickname'] = $nickname;
		
		$uid = Yii::app()->request->getParam('uid',null);
		if ($uid){
			$condition['uid'] = $uid;
		}
		
		$receive_uid = Yii::app()->request->getParam('receive_uid',null);
		if ($receive_uid){
			$condition['receive_uid'] = $receive_uid;
		}
		$create_time_on = Yii::app()->request->getParam('create_time_on',null);
		if ($create_time_on)
			$condition['create_time_on'] = $create_time_on;
		$create_time_end = Yii::app()->request->getParam('create_time_end',null);
		if ($create_time_end)
			$condition['create_time_on'] = $create_time_end;
		$send_time_on = Yii::app()->request->getParam('send_time_on',null);
		if ($send_time_on)
			$condition['send_time_on'] = $send_time_on;
		$send_time_end = Yii::app()->request->getParam('send_time_end',null);
		if ($create_time_end)
			$condition['send_time_end'] = $send_time_end;
		
		return $condition;
	}
	
	public function getRank(){
		$t = Yii::app()->request->getParam('type',null);
		if(!is_null($t) && $t >=0){
			$consumeService = new ConsumeService();
			$rank = array();
			if ($t == MESSAGE_PUSH_TYPE_USER){
				$rank = $consumeService->getUserRankFromRedis();
			}else if ($t == MESSAGE_PUSH_TYPE_DOTEY){
				$rank = $consumeService->getDoteyRankFromRedis();
			}
			if($rank){
				$html = '';
				foreach ($rank as $v){
					if ($t == MESSAGE_PUSH_TYPE_USER){
						if($v['rank'] < 7)
							continue;
					}
					$html .= "<option value='".$v['rank']."'>{$v['name']}</option>";
				}
				if($html){
					exit($html);
				}
			}
		}
		exit('1');
	}
	
	public function getWindow(){
		return array(
				0=>'全部窗口',
				1=>'公聊窗口',
				2=>'私聊窗口',
			);
	}
	
	public function getCategory(){
		return $this->service->getMessageCateGoryList();
	}
	
	public function getSCategory(){
		$t = Yii::app()->request->getParam('type',null);
		if(!is_null($t) && $t >=0){
			$info = $this->service->getMessageSubCategoryList($t);
			if($info){
				$html = '';
				foreach ($info['child'] as $k=>$v){
					$html .= "<option value='".$k."'>{$v}</option>";
				}
				if($html){
					exit($html);
				}
			}
		}else{
			return $this->service->getMessageSubCategoryList();
		}
		exit('1');
	}
	
	public function getReadStatus(){
		return array(0=>'未读',1=>'已读');
	}
	
	public function getIsSendStatus(){
		return array(0=>'未发送',1=>'已发送');
	}
	
	public function getExtraFlag(){
		return $this->service->getExtraFlag();
	}
	
	public function getPushType($t=null){
		return $this->service->getPushType($t);
	}
}
