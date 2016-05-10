<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author Su Peng <supeng@pipi.cn>
 * @version $Id: AdvController.php 11266 2013-05-30 08:59:39Z guoshaobo $ 
 * @package 
 * tags
 */
class AdvController extends PipiAdminController {

	/**
	 * @var OperateService 道具服务层
	 */
	public $operateSer;
	
	/**
	 * @var array 允许的操作
	 */
	public $allowOp = array('delOperate','addHomeBanner', 'addVideo', 'editVideo', 'addTopBanner', 'addLiveAdv', 'addNavigate');
	
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
	
	public function init(){
		parent::init();
		$this->operateSer = new OperateService();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
	}
	
	/**
	 * 首页通栏管理
	 */
	public function actionHomeBanner(){
		//是否是添加修改首页厨窗
		if($this->op == 'addHomeBanner' && in_array($this->op,$this->allowOp)){
			$this->addHomeBannerDo();	
		}
		
		//是否是删除首页厨窗
		if($this->op == 'delOperate' && in_array($this->op,$this->allowOp)){
			$this->delOperateDo();
		}
		
		$homeBanner = $this->operateSer->getOperateByCategory(CATEGORY_INDEX,CATEGORY_INDEX_BANNER);
		$this->render('adv_home_banner',array('homeData'=>$homeBanner));
	}
	
	/**
	 * AJAX 检验主播信息的合法性
	 */
	public function checkDoteyInfo(){
		if (!$this->isAjax){
			exit('不合法请求');
		}
		
		$doteyName = Yii::app()->request->getParam('doteyName');
		if(empty($doteyName)){
			exit('请输入主播信息后进行校验 ');
		}
		
		$doteySer = new DoteyService();
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
			if(!($doteyInfo = $doteySer->getDoteyInfoByUid($uid))){
				exit('该用户不是主播，请确认');
			}else{
				if(!isset($userInfo)){
					if(!($userInfo = $userSer->getUserBasicByUids(array($uid)))){
						exit('不合法用户，请重新输入');
					}else{
						$userInfo = $userInfo[$uid];
					}
				}
				
				exit('1'.'#xx#'.$userInfo['uid'].'#xx#'.$userInfo['username'].'#xx#'.$userInfo['nickname']);
			}
			
		}else{
			exit('不合法用户，请重新输入');
		}
		
	}
	
	/**
	 * 执行首页通栏广告的添加
	 */
	public function addHomeBannerDo(){
		$post = Yii::app()->request->getParam('indexForm');
		if($post){
			$uploads = array();
			$addInfo = array();
			foreach ($post as $k=>$v){
				if(is_array($v)){
					foreach($v as $k2 => $v2){
						$addInfo[$k2][$k] = $v2;
					}
				}
			}
			$this->uploadIndexPic($uploads);
				
			//添加或修改
			foreach ($addInfo as $k=>&$v){
				$v['category'] = CATEGORY_INDEX;
				$v['sub_category'] = CATEGORY_INDEX_BANNER;
		
				if (!isset($v['piclink']) || empty($v['piclink'])){
					$v['piclink'] = array_shift($uploads);
				}
				if(!isset($v['sort']) || empty($v['sort'])){
					$v['sort'] = 0;
				}
				$this->operateSer->saveOperate($addInfo[$k]);
			}
		}
		
		$this->redirect($this->createUrl('adv/homebanner'));
	}
	
	/**
	 * 删除操作
	 */
	public function delOperateDo(){
		if (!$this->isAjax){
			exit('不合法请求');
		}
		
		$operateId = Yii::app()->request->getParam('operateId');
		if(empty($operateId)){
			exit('缺少参数 ');
		}
		
		if($this->operateSer->delOperateByOperateIds(array($operateId))){
			exit('1');
		}else{
			exit('信息有误，删除失败');
		}
		
	}
	
	/**
	 * 上传首页管理图片
	 */
	public function uploadIndexPic(Array &$update){
		if($effectFiles = CUploadedFile::getInstancesByName('indexForm')){
			foreach ($effectFiles as $effectFile){
				if($filename = $effectFile->getName()){
					$extName = $effectFile->getExtensionName();
					$newName = uniqid().'.'.$extName;
					$uploadDir = ROOT_PATH."images".DIR_SEP.'operate'.DIR_SEP;
					if (!file_exists($uploadDir)){
						mkdir($uploadDir,0777,true);
					}
					$uploadfile = $uploadDir.$newName;
					if($effectFile->saveAs($uploadfile,true)){
						$update[] = $newName;
					}else{
						$update[] = '';
					}
				}
			}
		}
		return true;
	}

	/**
	 * 顶部通栏
	 * @author guoshaobo
	 */
	public function actionTopBanner()
	{
		if(Yii::app()->request->isPostRequest && in_array($this->op,$this->allowOp)){
			if($this->op=='addTopBanner' ){
				$this->addTopBanner();
			}
		}
		
		$list = array();
		$all = $this->operateSer->getAllOperateFromCache();
		if(isset($all[CATEGORY_COMMON]) && isset($all[CATEGORY_COMMON][CATEGORY_COMMON_TOPBANNER])){
			$list = $all[CATEGORY_COMMON][CATEGORY_COMMON_TOPBANNER];
		}
		$status = array('0'=>'停用', '1'=>'启用');
		$type = array('1'=>'图片', '2'=>'flash', '3'=>'视频');
		$position = array('0'=>'全局随机', '1'=>'指定频道', '2'=>'特定直播间随机', '999'=>'指定首页');
		$channels = $this->getChannels();
		$config = array('status'=>$status, 'type'=>$type, 'position'=>$position, 'channels'=>$channels);
		$this->render('adv_top_banner', array('list'=>$list, 'config'=>$config));
	}
	
	/**
	 * 添加和编辑顶部通栏
	 * @author guoshaobo
	 */
	public function addTopBanner()
	{
		$post = Yii::app()->request->getParam('indexForm');
		$uploads = array();
		$this->uploadIndexPic($uploads);
		if(count($uploads)>0){
			$post['piclink'] = array_shift($uploads);
		}elseif($post['_piclink']){
			$post['piclink'] = $post['_piclink'];
		}else{
			unset($post['piclink']);
		}
		unset($post['_piclink']);
		
		if($post['operate_id']<=0){
			unset($post['operate_id']);
		}
		$post['category'] = CATEGORY_COMMON;
		$post['sub_category'] = CATEGORY_COMMON_TOPBANNER;
		
		$res = $this->operateSer->saveOperate($post);
		if($res){
			$this->redirect($this->createUrl('adv/topBanner'));
		}
	}
	
	/**
	 * 视频前贴
	 * @author guoshaobo
	 */
	public function actionVideo()
	{
		if(Yii::app()->request->isPostRequest && in_array($this->op,$this->allowOp)){
			if($this->op=='addVideo' ){
				$this->addVideo();
			}elseif($this->op=='editVideo'){
				$this->editVideo();
			}
		}

		$list = array();
		$all = $this->operateSer->getAllOperateFromCache();
		if(isset($all[CATEGORY_COMMON]) && isset($all[CATEGORY_COMMON][CATEGORY_COMMON_VIDEO])){
			$list = $all[CATEGORY_COMMON][CATEGORY_COMMON_VIDEO];
		}
		
		$status = array('0'=>'停用', '1'=>'启用');
		$type = array('1'=>'图片', '2'=>'flash', '3'=>'视频');
		$position = array('0'=>'全局随机', '1'=>'指定频道', '2'=>'特定直播间随机');
		$channels = $this->getChannels();
		$config = array('status'=>$status, 'type'=>$type, 'position'=>$position, 'channels'=>$channels);
		$this->render('adv_video', array('list'=>$list, 'config'=>$config));
	}
	
	/**
	 * 停/启用视频前贴
	 * @author guoshaobo
	 */
	public function editVideo()
	{
		$ajaxReturn = array('result'=>false, 'msg'=>'操作失败, 请稍后再试', 'data'=>'');
		if($this->isAjax){
			$operateId = Yii::app()->request->getParam('operateId');
			$status = Yii::app()->request->getParam('status');
			$operate = $this->operateSer->getOperateById($operateId);
			if($operate){
				$operate['content']['status'] = abs($status - 1);
				$res = $this->operateSer->saveOperate($operate);
				if($res){
					$ajaxReturn['result'] = true;
					$ajaxReturn['msg'] = '操作成功';
					$ajaxReturn['data'] = $operate;
				}
			}else{
				$ajaxReturn['msg'] = '数据错误, 请刷新页面后重试';
			}
		}else{
			$ajaxReturn['msg'] = '非法请求';
		}
		exit(json_encode($ajaxReturn));
	}
	
	/**
	 * 添加和编辑视频前贴
	 * @author guoshaobo
	 */
	public function addVideo()
	{
		$post = Yii::app()->request->getParam('indexForm');
		$uploads = array();
		$this->uploadIndexPic($uploads);
		if(count($uploads)>0){
			$post['piclink'] = array_shift($uploads);
		}elseif($post['_piclink']){
			$post['piclink'] = $post['_piclink'];
		}else{
			unset($post['piclink']);
		}
		unset($post['_piclink']);
		
		if($post['operate_id']<=0){
			unset($post['operate_id']);
		}
		$post['category'] = CATEGORY_COMMON;
		$post['sub_category'] = CATEGORY_COMMON_VIDEO;
		
		$res = $this->operateSer->saveOperate($post);
		if($res){
			$this->redirect($this->createUrl('adv/video'));
		}
	}
	
	/**
	 * 直播间广告
	 * @author guoshaobo
	 */
	public function actionLiveAdv()
	{
		if(Yii::app()->request->isPostRequest && in_array($this->op,$this->allowOp)){
			if($this->op=='addLiveAdv' ){
				$this->addLiveAdv();
			}
		}
		
		$list = array();
		$all = $this->operateSer->getAllOperateFromCache();
		if(isset($all[CATEGORY_COMMON]) && isset($all[CATEGORY_COMMON][CATEGORY_COMMON_LIVE])){
			$list = $all[CATEGORY_COMMON][CATEGORY_COMMON_LIVE];
		}
		$position = array('0'=>'全局随机', '1'=>'指定频道', '2'=>'特定直播间随机');
		$channels = $this->getChannels();
		$config = array('position'=>$position, 'channels'=>$channels);
		$this->render('adv_live', array('list'=>$list, 'config'=>$config));
	}
	
	/**
	 * 添加和编辑直播间广告
	 * @author guoshaobo
	 */
	public function addLiveAdv()
	{
		$post = Yii::app()->request->getParam('indexForm');
		$uploads = array();
		$this->uploadIndexPic($uploads);
		if(count($uploads)>0){
			$post['piclink'] = array_shift($uploads);
		}elseif($post['_piclink']){
			$post['piclink'] = $post['_piclink'];
		}else{
			unset($post['piclink']);
		}
		unset($post['_piclink']);
	
		if($post['operate_id']<=0){
			unset($post['operate_id']);
		}
		$post['category'] = CATEGORY_COMMON;
		$post['sub_category'] = CATEGORY_COMMON_LIVE;
	
		$res = $this->operateSer->saveOperate($post);
		if($res){
			$this->redirect($this->createUrl('adv/liveAdv'));
		}
	}
	
	/**
	 * 导航字链
	 * @author guoshaobo
	 */
	public function actionNavigate()
	{
		if(Yii::app()->request->isPostRequest && in_array($this->op,$this->allowOp)){
			if($this->op=='addNavigate' ){
				$this->addNavigate();
			}
		}
		
		$list = array();
		$all = $this->operateSer->getAllOperateFromCache();
		if(isset($all[CATEGORY_COMMON]) && isset($all[CATEGORY_COMMON][CATEGORY_COMMON_NAVIGATION])){
			$list = $all[CATEGORY_COMMON][CATEGORY_COMMON_NAVIGATION];
		}
		
		$this->render('adv_navigate', array('list'=>$list));
	}
	
	/**
	 * 添加/编辑导航
	 * @author guoshaobo
	 */
	public function addNavigate()
	{
		$post = Yii::app()->request->getParam('indexForm');
		
		if($post['operate_id']<=0){
			unset($post['operate_id']);
		}
		$post['category'] = CATEGORY_COMMON;
		$post['sub_category'] = CATEGORY_COMMON_NAVIGATION;
		
		$res = $this->operateSer->saveOperate($post);
		if($res){
			$this->redirect($this->createUrl('adv/navigate'));
		}
	}
	
	public function test()
	{
		$op = new OperateService();
		$res = $op->getLiveAdv(2009, 10312215);
		$res = $op->getTopBannerAdv(2009, 10312215);
		$res = $op->getLivePageAdv(2009, 10312215);
		print_r($res);
		exit;
	}
	
	/**
	 * 获取所有频道
	 * 
	 * @author guoshaobo
	 * @return multitype:unknown
	 */
	public function getChannels()
	{
		$channels = array();
		$channelServ = new ChannelService();
		$channel = $channelServ->getAllChannelFromCache();
		foreach($channel as $k=>$v){
			foreach($v as $ke=>$va){
				$channels[] = $va;
			}
		}
		return $channels;
	}
}
