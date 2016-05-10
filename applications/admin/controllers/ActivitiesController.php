<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class ActivitiesController extends PipiAdminController {
	/**
	 * @var array 允许的操作
	 */
	protected  $allowOp = array('addGiftStarRule','addAdv','editAdv','deleteAdv');
	/**
	 * @var string 当前操作
	 */
	public $op;
	
	/**
	 * @var boolean 是否是Ajax请求
	 */
	public $isAjax;
	
	public $pageSize = 10;
	
	public $offset;
	
	/**
	 * @var int page lable
	 */
	public $p;
	
	protected $giftStarService;
	protected $giftService;
	
	public function init(){
		parent::init();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
		$this->giftStarService=new GiftStarService();
		$this->giftService=new GiftService();
	}
	
	public function getSearchCondition(){
		$condition = array();
		$condition = Yii::app()->request->getParam('form');
		if($condition){
			return $condition;
		}
	
		if (Yii::app()->request->getParam('week_id')){
			$condition['week_id'] = Yii::app()->request->getParam('week_id');
		}
	
		if (Yii::app()->request->getParam('monday_date')){
			$condition['monday_date'] = Yii::app()->request->getParam('monday_date');
		}
	
		return is_array($condition)?$condition:array();
	}
	
	public function actionGiftStarRuleList()
	{
		//初始化下周规则
		$giftStarService=$this->giftStarService;
		$weekId=$giftStarService->getThisWeekId();
		$giftStarService->initGiftStarRule($weekId+1);
		//获取规则列表
		$condition = $this->getSearchCondition();
		$this->assetsMy97Date();
		
		$ruleList = $giftStarService->getRuleByCondition($this->p-1,$this->pageSize,$condition);
		//分页实例化
		$pager = new CPagination($ruleList['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;

		$this->render('giftstar_rulelist',array('pager'=>$pager,'ruleList'=>$ruleList['list'],'condition'=>$condition));
	}

	//编辑礼物之星规则设定
	public function actionGiftStarRule()
	{
		$giftStarService=$this->giftStarService;
		
		//获取所有礼物
		$giftService=new GiftService();
		$gifts=$giftService->getGiftList();
		$giftList=array();
		foreach ($gifts as $giftRow)
		{
			$giftList[$giftRow['gift_id']]=$giftRow['zh_name'];
		}
		//获取所有主播等级
		$consumeService=new ConsumeService();
		$doteyRanks=$consumeService->getDoteyAllRank();
		$allDoteyRank=array();
		foreach ($doteyRanks as $doteyRankRow)
		{
			$allDoteyRank[$doteyRankRow['rank']]=$doteyRankRow['name'];
		}
		
		//是否执行添加动作
		if(Yii::app()->request->getParam('RuleForm')){
			$this->giftStarRuleDo();
		}
		
		//是否是编辑
		
		$updateRuleInfo = array();
		if ($rule_id = Yii::app()->request->getParam('rule_id')){
			$updateRuleInfo = $giftStarService->getRuleByIds(array($rule_id));
			$updateRuleInfo = $updateRuleInfo[$rule_id];
		}
		
		if(Yii::app()->request->isAjaxRequest){
			$this->renderPartial('giftstar_rule',array('isAjax'=>true,
				'giftList'=>$giftList,
				'allDoteyRank'=>$allDoteyRank,
				'updateRuleInfo'=>$updateRuleInfo,
				));
		}else{
			$this->render('giftstar_rule',array('isAjax'=>false,
				'giftList'=>$giftList,
				'allDoteyRank'=>$allDoteyRank,
				'updateRuleInfo'=>$updateRuleInfo,
			));
		}
		
	}
	

	/**
	 * 添加修改礼物之星礼物图片
	 */
	public function giftStarRuleDo(){
		$notices = array();
		$isAjax = false;
	
		//修改礼物之星礼物规则
		$model = new GiftStarRuleForm();
		$giftStarService=$this->giftStarService;
		$data = Yii::app()->request->getParam('RuleForm');
		$data['contention_rule']=implode(",",$data['contention_rule']);
		
		if (!empty($data)) {
			$model->attributes = $data;
			if($model->validate()){
				if($giftStarService->saveRule($data)){
					$this->redirect($this->createUrl('activities/GiftStarRuleList'));
				}else{
					$notices = $giftStarService->getNotice();
				}
			}else{
				$notices = $model->getErrors();
			}
		}else{
			$giftStarService->setNotice('Parameter', Yii::t('common', 'Parameters are wrong'));
			$notices = $giftStarService->getNotice();
		}
	
		//获取所有礼物
		$giftService=new GiftService();
		$gifts=$giftService->getGiftList();
		$giftList=array();
		foreach ($gifts as $giftRow)
		{
			$giftList[$giftRow['gift_id']]=$giftRow['zh_name'];
		}
		//获取所有主播等级
		$consumeService=new ConsumeService();
		$doteyRanks=$consumeService->getDoteyAllRank();
		$allDoteyRank=array();
		foreach ($doteyRanks as $doteyRankRow)
		{
			$allDoteyRank[$doteyRankRow['rank']]=$doteyRankRow['name'];
		}
		
		//是否是编辑
		
		$updateRuleInfo = array();
		if ($rule_id = Yii::app()->request->getParam('rule_id')){
			$updateRuleInfo = $giftStarService->getRuleByIds(array($rule_id));
			$updateRuleInfo = $updateRuleInfo[$rule_id];
		}
		
		
		$this->render('giftstar_rule',array('jsAjax'=>false,
				'notices'=>$notices,
				'giftList'=>$giftList,
				'allDoteyRank'=>$allDoteyRank,
				'updateRuleInfo'=>$updateRuleInfo,
			));
		exit;
	}
	
	//礼物搜索条件
	public function getSearchGiftCondition(){
		$condition = array();
		$condition = Yii::app()->request->getParam('form');
		if($condition){
			return $condition;
		}
	
		if (Yii::app()->request->getParam('gift_id')){
			$condition['gift_id'] = Yii::app()->request->getParam('gift_id');
		}
	
		if (Yii::app()->request->getParam('order_number')){
			$condition['order_number'] = Yii::app()->request->getParam('order_number');
		}
	
		if (Yii::app()->request->getParam('summary')){
			$condition['summary'] = Yii::app()->request->getParam('summary');
		}		
		
		return is_array($condition)?$condition:array();
	}

	//礼物之星礼物列表
	public function actionGiftStarImgList()
	{
		$condition = $this->getSearchGiftCondition();
		$giftStarService=$this->giftStarService;
		
		$giftImgList = $giftStarService->getImgByCondition($this->p-1,$this->pageSize,$condition);
		//分页实例化
		$pager = new CPagination($giftImgList['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
		
		$this->render('giftstar_imglist',array('pager'=>$pager,'giftImgList'=>$giftImgList['list'],'condition'=>$condition));		
	}
	
	//添加修改礼物之星图片
	public function actionGiftStarImg()
	{
		//获取所有礼物
		$giftService=new GiftService();
		$gifts=$giftService->getGiftList();
		$giftList=array();
		foreach ($gifts as $giftRow)
		{
			$giftList[$giftRow['gift_id']]=$giftRow['zh_name'];
		}

		//是否执行添加动作
		if(Yii::app()->request->getParam('GiftStarImgForm')){
			$this->giftStarImgDo();
		}
	
		//是否是编辑
		$giftStarService=$this->giftStarService;
		$updateGiftInfo=array();
		if ($img_id = Yii::app()->request->getParam('img_id')){
			$updateGiftInfos = $giftStarService->getGiftImgByIds(array($img_id));
			$updateGiftInfo = $updateGiftInfos[$img_id];
		}
	
		if(Yii::app()->request->isAjaxRequest){
			$this->renderPartial('giftstar_img',array('isAjax'=>true,
				'giftList'=>$giftList,
				'updateGiftInfo'=>$updateGiftInfo,
			));
		}else{
			$this->render('giftstar_img',array('isAjax'=>false,
				'giftList'=>$giftList,
				'updateGiftInfo'=>$updateGiftInfo,
			));
		}
	}
	
	/**
	 * 添加修改礼物之星图片
	 */
	public function giftStarImgDo(){
		$notices = array();
		$isAjax = false;
	
		//添加修改礼物之星图片
		$model = new GiftStarImgForm();
		$giftStarService=$this->giftStarService;
		$data = Yii::app()->request->getParam('GiftStarImgForm');
		//print_r($data);exit;
		if (!empty($data)) {
			$model->attributes = $data;
			if($model->validate()){
				if($this->uploadGiftImage($model,$data)){
					//print_r($data);exit;
					if($giftStarService->saveGiftImg($data)){
						$this->redirect($this->createUrl('activities/GiftStarImgList'));
					}else{
						$notices = $giftStarService->getNotice();
					}
				}
				else
				{
					$notices = $model->getNotice();
				}

			}else{
				$notices = $model->getErrors();
			}
		}else{
			$giftStarService->setNotice('Parameter', Yii::t('common', 'Parameters are wrong'));
			$notices = $giftStarService->getNotice();
		}
	
		//获取所有礼物
		$giftService=new GiftService();
		$gifts=$giftService->getGiftList();
		$giftList=array();
		foreach ($gifts as $giftRow)
		{
			$giftList[$giftRow['gift_id']]=$giftRow['zh_name'];
		}
	
		//是否是编辑
		$giftStarService=$this->giftStarService;
		$updateGiftInfo=array();
		if ($img_id = Yii::app()->request->getParam('img_id')){
			$updateGiftInfos = $giftStarService->getGiftImgByIds(array($img_id));
			$updateGiftInfo = $updateGiftInfos[$img_id];
		}
		
		$this->render('giftstar_img',array('jsAjax'=>false,
			'notices'=>$notices,
			'giftList'=>$giftList,
			'updateGiftInfo'=>$updateGiftInfo,
		));
		exit;
	}
	

	/**
	 * 上传礼物图片
	 *
	 * @param GiftStarImgForm $model
	 * @param array $data
	 * @return boolean
	 */
	public function uploadGiftImage(GiftStarImgForm $model,Array &$data){
		if($imgFile = CUploadedFile::getInstance($model,'image')){
			if($filename = $imgFile->getName()){
				$extName = $imgFile->getExtensionName();
				$newName = uniqid().'.'.$extName;
				$uploadDir = ROOT_PATH."images".DIR_SEP.'gift'.DIR_SEP;
				if (!file_exists($uploadDir)){
					mkdir($uploadDir,0777,true);
				}
				$uploadfile = $uploadDir.$newName;
				if($imgFile->saveAs($uploadfile,true)){
					$data['image'] = $newName;
					return true;
				}
			}else{
				$model->addError('image', '礼物图片 上传失败');
				return false;
			}
		}elseif (!isset($data['gift_id'])){
			$model->addError('image', '礼物图片不能为空');
			return false;
		}
		return true;
	}
	
	//礼物之星规则说明管理
	public function actionGiftStarSetList()
	{
		$this->assetsCKEditor();
		//初始化下周规则
		$giftStarService=$this->giftStarService;
		$weekId=$giftStarService->getThisWeekId();
		$giftStarService->initGiftStarSet($weekId+1);
		
		$condition = array();
		$condition = Yii::app()->request->getParam('form');
		
		if (Yii::app()->request->getParam('week_id')){
			$condition['week_id'] = Yii::app()->request->getParam('week_id');
		}
		
		if (Yii::app()->request->getParam('monday_date')){
			$condition['monday_date'] = Yii::app()->request->getParam('monday_date');
		}
		$condition=is_array($condition)?$condition:array();
	
		$setList = $giftStarService->getSetByCondition($this->p-1,$this->pageSize,$condition);
		$this->assetsMy97Date();
		//分页实例化
		$pager = new CPagination($setList['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
	
		$this->render('giftstar_setlist',array('pager'=>$pager,'setList'=>$setList['list'],'condition'=>$condition));
	}
	
	//添加修改礼物之星规则说明
	public function actionGiftStarSet()
	{
		$this->assetsCKEditor();
		
		//是否执行添加动作
		if(Yii::app()->request->getParam('SetForm')){
			$this->giftStarSetDo();
		}
	
		//是否是编辑
		$giftStarService=$this->giftStarService;
		$updateSetInfo=array();
		if ($set_id = Yii::app()->request->getParam('set_id')){
			$updateSetInfo = $giftStarService->getSetByIds(array($set_id));
			$updateSetInfo = $updateSetInfo[$set_id];
		}
		///print_r($updateSetInfo);exit;
		if(Yii::app()->request->isAjaxRequest){
			$this->renderPartial('giftstar_set',array('isAjax'=>true,
				'updateSetInfo'=>$updateSetInfo,
			));
		}else{
			$this->render('giftstar_set',array('isAjax'=>false,
				'updateSetInfo'=>$updateSetInfo,
			));
		}
	}
	
	/**
	 * 添加修改礼物之星规则说明
	 */
	public function giftStarSetDo(){
		$this->assetsCKEditor();
		$notices = array();
		$isAjax = false;
	
		//添加修改礼物之星图片
		$giftStarService=$this->giftStarService;
		$data = Yii::app()->request->getParam('SetForm');

		if (!empty($data)) {
			if($giftStarService->saveGiftStarSet($data)){
				$this->redirect($this->createUrl('activities/GiftStarSetList'));
			}else{
				$notices = $giftStarService->getNotice();
			}
	
		}else{
			$giftStarService->setNotice('Parameter', Yii::t('common', 'Parameters are wrong'));
			$notices = $giftStarService->getNotice();
		}
	
		//是否是编辑
		$updateSetInfo=array();
		if ($set_id = Yii::app()->request->getParam('set_id')){
			$updateSetInfo = $giftStarService->getGiftImgByIds(array($set_id));
			$updateSetInfo = $updateSetInfo[$set_id];
		}
	
		$this->render('giftstar_set',array('jsAjax'=>$isAjax,
			'notices'=>$notices,
			'giftList'=>$giftList,
			'updateSetInfo'=>$updateSetInfo,
		));
		exit;
	}
	
	/**
	 * 手机端独有的活动公告
	 */
	public function actionMobileAdv(){
		if(in_array($this->op,$this->allowOp)){
			$this->{'_'.$this->op}();
		}
		
		$list = MobileAdvService::getInstance()->getAllAdv();
		$this->render('mobile_adv', array('list'=>$list));
	}
	
	/**
	 * 添加
	 */
	private function _addAdv(){
		$post = Yii::app()->request->getParam('adv');
		if(isset($post['adv_id'])) unset($post['adv_id']);
		MobileAdvService::getInstance()->saveAdv($post, 'image');
		$this->redirect($this->createUrl('activities/MobileAdv'));
	}
	
	/**
	 * 修改
	 */
	private function _editAdv(){
		$post = Yii::app()->request->getParam('adv');
		MobileAdvService::getInstance()->saveAdv($post, 'image');
		$this->redirect($this->createUrl('activities/MobileAdv'));
	}
	
	/**
	 * 删除
	 */
	private function _deleteAdv(){
		$id = Yii::app()->request->getParam('id');
		$return = MobileAdvService::getInstance()->deleteAdv($id);
		if($return) die('1');
		else die(array_shift(MobileAdvService::getInstance()->getNotice()));
	}
}