<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2014-1-9 下午4:37:40 hexin $ 
 * @package
 */
class TagController extends PipiAdminController {	
	/**
	 * @var array 允许的操作
	 */
	public $allowOp = array('addTag', 'editTag', 'deleteTag', 'valid', 'doteyAdd', 'doteyEdit', 'doteyDelete');
	
	/**
	 * @var string  当前操作
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
	
	/**
	 * @var DoteyTagsService $service
	 */
	public $service;
	
	public function init(){
		parent::init();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		$this->p = intval(Yii::app()->request->getParam('page'));
		$this->p = $this->p < 1 ? 1 : $this->p;
		$this->offset = ($this->p -1)*$this->pageSize;
		
		$this->service = DoteyTagsService::getInstance();
	}
	
	/**
	 * 标签列表
	 */
	public function actionList(){
		if(in_array($this->op,$this->allowOp)){
			$this->{'_'.$this->op}();
		}
		
		$list = $this->service->getAllTags(true);
		$this->render('tag_list', array('list' => $list));
	}
	
	/**
	 * 添加
	 */
	private function _addTag(){
		$post = Yii::app()->request->getParam('form');
		if(isset($post['tag_id'])) unset($post['tag_id']);
		$this->service->saveTag($post);
		$this->redirect($this->createUrl('tag/list'));
	}
	
	/**
	 * 修改
	 */
	private function _editTag(){
		$post = Yii::app()->request->getParam('form');
		$this->service->saveTag($post);
		$this->redirect($this->createUrl('tag/list'));
	}
	
	/**
	 * 删除
	 */
	private function _deleteTag(){
		$id = Yii::app()->request->getParam('id');
		$return = $this->service->deleteTag($id);
		if($return) die('1');
		else die(array_shift($this->service->getNotice()));
	}
	
	/**
	 * 主播印象标签列表
	 */
	public function actionDoteyList(){
		$msg = '';
		if(in_array($this->op,$this->allowOp)){
			$msg = $this->{'_'.$this->op}();
		}
		
		$form = Yii::app()->request->getParam('form');
		$tags = $this->service->getAllTags();
		$condition = array();
		if(!empty($form['uid'])) $condition['uid'] = intval($form['uid']);
		if(!empty($form['tag_name'])){
			$condition['tag_name'] = $form['tag_name'];
			$tag_id = 0;
			foreach($tags as $tag){
				if($tag['tag_name'] == $form['tag_name']){
					$tag_id = $tag['tag_id'];
				}
			}
			if($tag_id > 0) $condition['tag_id'] = $tag_id;
		}
		$list = $this->service->getTagsByDotey(20, $this->p, $condition, true);
		$this->render('dotey_tags_list', array('list'=>$list, 'tags'=>$tags, 'message' => $msg, 'condition' => $condition));
	}
	
	/**
	 * 检查主播
	 */
	private function _valid(){
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
		
		$doteyService = new DoteyService();
		$dotey = $doteyService->getDoteysInUids(array($uid));
		if(empty($dotey)) exit('只有主播才能添加印象标签');
		
		if ($uid){
			if(!isset($userInfo)){
				if(!($userInfo = $userSer->getUserBasicByUids(array($uid)))){
					exit('不合法用户，请重新输入');
				}else{
					$userInfo = $userInfo[$uid];
				}
			}
			$tags = $this->service->getTagsByUids($uid);
			if(empty($tags)) $tagIds = '';
			else{
				$tagIds = implode(',', array_keys($this->service->buildDataByIndex($tags[$uid], 'tag_id')));
			}
			exit('1'.'#xx#'.$userInfo['uid'].'#xx#'.$userInfo['username'].'#xx#'.$userInfo['nickname'].'#xx#'.$tagIds);
		}else{
			exit('不合法用户，请重新输入');
		}
	}
	
	/**
	 * 添加
	 */
	private function _doteyAdd(){
		$post = Yii::app()->request->getParam('form');
		if(!empty($post['uid']) && !empty($post['tag_id'])){
			$this->service->addTags($post['uid'], $post['tag_id']);
		}
		$this->redirect($this->createUrl('tag/doteyList'));
	}
	
	/**
	 * 修改
	 */
	private function _doteyEdit(){
		$post = Yii::app()->request->getParam('form');
		if(!empty($post['uid']) && !empty($post['tag_id'])){
			$this->service->addTags($post['uid'], $post['tag_id']);
		}
		$this->redirect($this->createUrl('tag/doteyList'));
	}
	
	/**
	 * 删除
	 */
	private function _doteyDelete(){
		$uid = Yii::app()->request->getParam('uid');
		$tag_id = Yii::app()->request->getParam('tag_id');
		$r = $this->service->deleteTags($uid, $tag_id);
		if($r) die("1");
		else die(array_shift($this->service->getNotice()));
	}
}
