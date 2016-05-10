<?php
class BroadcastController extends PipiAdminController {

	/**
	 * @var BroadcastService 广播服务层
	 */
	public $service;

	/**
	 * @var array 允许的操作
	 */
	public $allowOp = array('addSetup','undoDisable','doDisable');

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
		$this->service = new BroadcastService();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
	}
	
	/**
	 * 全站广播
	 */
	public function actionSiteBroadcast(){
		$tab = Yii::app()->request->getParam('tab','clist');
		if (!in_array($tab, array('clist','setup','dlist'))){
			$tab = 'clist';
		}
		
		if($this->isAjax){
			$func = '_Broadcast'.ucfirst($tab);
			exit($this->$func ());
		}else{
			$this->assetsMy97Date();
			$data = $this->_BroadcastClist(true);
			$this->render('broadcast_content_list',array('tab'=>$tab,'data'=>$data));
		}
	}
	
	/**
	 * 获取广播内容列表
	 */
	private function _BroadcastClist($isReturn = false){
		if($this->op == 'doDisable'){
			if (in_array($this->op, $this->allowOp)){
				$uid = Yii::app()->request->getParam('uid',0);
				if($this->service->saveBroadcastDisable($uid)){
					exit('1');
				}else{
					exit('禁止广播失败');
				}
			}else{
				exit('禁止广播失败');
			}
		}
		
		$condition = $this->_getCondition();
		$clist = $this->service->getBroadcastContentList($condition,$this->offset,$this->pageSize);
		
		if($clist['list']){
			$uids = array();
			$aids = array();
			$doteyUids = array();
			foreach($clist['list'] as $v){
				$uids[$v['uid']] = $v['uid'];
				$doteyUids[$v['dotey_uid']] = $v['dotey_uid'];
				if(!in_array($v['aid'], $aids)){
					$aids[] = $v['aid'];
				}
			}
			$userService = new UserService();
			$archivesService = new ArchivesService();
			$clist['uinfo'] = $userService->getUserBasicByUids($uids);
			if($doteyUids){
				$clist['doteyInfo'] = $userService->getUserBasicByUids($doteyUids);
			}
			$clist['ainfo'] = $archivesService->getArchivesByArchivesIds($aids);
			$clist['disableInfo'] = $this->service->getBroadcastDisableByUids($uids);
		}
		$pager = new CPagination($clist['count']);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		return $this->renderPartial('_broadcast_clist',array('pager'=>$pager,'clist'=>$clist),$isReturn);
	}
	
	/**
	 * 广播设置
	 */
	private function _BroadcastSetup(){
		$webConfigService = new WebConfigService();
		$service = new ConsumeService();
		
		$ckey = $webConfigService->getBroadcastSetupKey();
		if($this->op == 'addSetup'){
			if (in_array($this->op, $this->allowOp)){
				$power = Yii::app()->request->getParam('power',false);
				$urank = Yii::app()->request->getParam('urank',0);
				$price = Yii::app()->request->getParam('price',BroadcastService::DEFAULT_PRICE);
				$config['c_key'] = $ckey;
				$config['c_type'] = 'array';
				$config['c_value']['power'] = $power;
				$config['c_value']['urank'] = $urank;
				$config['c_value']['price'] = $price;
				if($webConfigService->saveWebConfig($config)){
					exit('更新成功');
				}else{
					exit('更新失败');
				}
			}else{
				exit('更新失败');
			}
		}
		
		$setInfo = $this->service->getBroadcastSetup();
		$rank = $service->getUserRankFromRedis();
		$_rank = array();
		foreach($rank as $k=>$v){
			$_rank[$k] = $v['name'];
		}
		ksort($_rank);
		return $this->renderPartial('_broadcast_setup',array('rank'=>$_rank,'setInfo'=>$setInfo),true);
	}
	
	/**
	 * 广播禁用列表
	 */
	private function _BroadcastDlist(){
		if($this->op == 'undoDisable'){
			if (in_array($this->op, $this->allowOp)){
				$uid = Yii::app()->request->getParam('uid',false);
				if($this->service->deleteDisable($uid)){
					exit('1');
				}else{
					exit('取消禁播失败');
				}
			}else{
				exit('取消禁播失败');
			}
		}
		
		$condition = $this->_getCondition();
		$dlist = $this->service->getBroadcastDisableList($condition,$this->offset,$this->pageSize);
		if ($dlist['list']){
			$uids = array_keys($dlist['list']);
			$userService = new UserService();
			$dlist['uinfo'] = $userService->getUserBasicByUids($uids);
		}
		$pager = new CPagination($dlist['count']);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		$this->renderPartial('_broadcast_dlist',array('pager'=>$pager,'dlist'=>$dlist,'condition'=>$condition));
	}
	
	/**
	 * 获取查询条件
	 * @return multitype:string unknown Ambigous <mixed, unknown> 
	 */
	private function _getCondition(){
		$condition = array();
		$stime = Yii::app()->request->getParam('stime');
		$etime = Yii::app()->request->getParam('etime');
		$uid = Yii::app()->request->getParam('uid');
		$dotey_uid = Yii::app()->request->getParam('dotey_uid');
		$aid = Yii::app()->request->getParam('aid');
		
		if ($stime) {
			$condition['stime'] = $stime;
		}
		if ($etime) {
			$condition['etime'] = $etime;
		}
		if ($uid) {
			$condition['uid'] = $uid;
		}
		if ($dotey_uid) {
			$condition['dotey_uid'] = $dotey_uid;
		}
		if ($aid) {
			$condition['aid'] = $aid;
		}
		if($condition){
			foreach($condition as $k=>$v){
				if(empty($v)){
					unset($condition[$k]);
				}
			}
		}
		$condition['tab'] = Yii::app()->request->getParam('tab');
		$condition['op'] = $this->op;
		return $condition;
	}
}
