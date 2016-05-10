<?php

class GiftController extends PipiAdminController{
	
	/**
	 * @var GiftService 礼物服务层
	 */
	public $giftSer;
	
	public $allowOp = array('addGiftCat','delGiftCat','addGiftDo','updateGift','delGiftEffect','delGift');
	public $op;
	
	/**
	 * @var boolean 是否是Ajax请求
	 */
	public $isAjax;
	
	public $p;
	public $offset;
	public $pageSize = 20;
	
	public function init() {
		parent::init();
		$this->giftSer = new GiftService();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
	}
	
	/**
	 * 礼物分类管理 
	 */
	public function actionGiftCat() {
		$category = $this->giftSer->getGiftCategory();
		$this->render('gift_cat',array('cateInfo'=>$category));
	}
	
	/**
	 * 添加礼物分类 
	 */
	public function actionAddGiftCat(){
		//是否删除
		if(Yii::app()->request->isAjaxRequest && $this->op == 'delGiftCat'){
			if($cid = Yii::app()->request->getParam('cid')){
				if($this->giftSer->delGiftCategoryByCatIds(array($cid))){
					echo(1);
				}
			}
			exit;
		}
		
		//是否是编辑
		$cInfos =array();
		if($cid = Yii::app()->request->getParam('cid')){
			if($cInfos = $this->giftSer->getGiftCategoryByCatIds(array($cid))){
				$cInfos = $cInfos[$cid];
			}
		}
		
		//异步加载表单
		if(Yii::app()->request->isAjaxRequest && $this->op == 'addGiftCat'){
			$isAjax = true;
			exit($this->renderPartial('gift_add_cat',array('isAjax'=>false,'cinfo'=>$cInfos)));			
		}
		
		//处理添加请求
		if($data = Yii::app()->request->getParam('giftcat')){
			if($this->giftSer->saveGiftCategory($data)){
				$this->redirect($this->createUrl('gift/giftcat'));
			}
		}
		
		$this->render('gift_add_cat',array('isAjax'=>false,'notices'=>$this->giftSer->getNotice(),'cinfo'=>$cInfos));
	}
	
	/**
	 * 礼物列表管理
	 */
	public function actionGiftList(){
		$condition = $this->getSearchCondition();#获取礼物列表
		$giftList = $this->giftSer->getGiftByCondition($this->p-1,$this->pageSize,$condition);

		//分页实例化
		$pager = new CPagination($giftList['count']);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
		
		$allStatus = array('0'=>'隐藏','1'=>'显示','2'=>'删除');#所有状态
		$consumeSer = new ConsumeService();
		$allGrade = $consumeSer->getAllUserRanks('rank');#所有等级
		$allShopType = $this->giftSer->getShopType();#所有shop_type
		$allGiftType = $this->giftSer->getGiftType();#所有gift_type
		$allGiftCat = $this->giftSer->getGiftCategory();#获取礼物分类
		
		$this->render('gift_list',array('allGiftCat'=>$allGiftCat,'pager'=>$pager,'giftList'=>$giftList['list'],'allStatus'=>$allStatus,'allGrade'=>$allGrade,'allShopType'=>$allShopType,'allGiftType'=>$allGiftType,'condition'=>$condition));
	}

	/**
	 * 添加礼物操作
	 */
	public function actionAddGift(){
		//是否是删除动画效果动作
		if($this->op == 'delGiftEffect' && in_array($this->op, $this->allowOp)){
			$this->delGiftEffect();	
		}
		
		//是否是删除礼物动作
		if($this->op == 'delGift' && in_array($this->op, $this->allowOp)){
			$this->delGift();
		}
		
		//是否执行添加动作
		if(Yii::app()->request->getParam('GiftForm')){
			$this->addGiftDo();
		}
		//获取礼物分类
		$allGiftCat = array();
		if($cat = $this->giftSer->getGiftCategory()){
			foreach ($cat as $v){
				$allGiftCat[$v['category_id']] = $v['cat_name'];
			}
		}
		
		//所有状态
		$allStatus = array('0'=>'隐藏','1'=>'显示');
		
		//所有等级
		$consumeSer = new ConsumeService();
		$allGrade = array();
		if($grades = $consumeSer->getAllUserRanks('rank')){
			foreach ($grades as $grade){
				$allGrade[$grade['rank']] = $grade['name'];
			}
		}
		
		//所有shop_type
		$allShopType = $this->giftSer->getShopType();
		
		//所有gift_type
		$allGiftType = $this->giftSer->getGiftType();
		
		//是否是编辑
		$upGiftInfo = array();
		if ($gift_id = Yii::app()->request->getParam('gift_id')){
			$upGiftInfo = $this->giftSer->getGiftByIds(array($gift_id),true);
			$upGiftInfo = $upGiftInfo[$gift_id];
		}

		if(Yii::app()->request->isAjaxRequest){
			$this->renderPartial('gift_add',array('isAjax'=>true,'allGiftCat'=>$allGiftCat,'allStatus'=>$allStatus,'allGrade'=>$allGrade,'allShopType'=>$allShopType,'allGiftType'=>$allGiftType,'upGiftInfo'=>$upGiftInfo));
		}else{
			$this->render('gift_add',array('isAjax'=>false,'allGiftCat'=>$allGiftCat,'allStatus'=>$allStatus,'allGrade'=>$allGrade,'allShopType'=>$allShopType,'allGiftType'=>$allGiftType,'upGiftInfo'=>$upGiftInfo));
		}
	}
	
	
	/**
	 * 礼物销售统计
	 */
	public function actionSaleStatus(){
		$condition = $this->getSearchCondition();#获取礼物列表
		$giftList = $this->giftSer->getGiftByCondition($this->p-1,$this->pageSize,$condition);
		$count = $giftList['count'];
		$giftList = $giftList['list'];
		
		//分页实例化
		$pager = new CPagination($count);
		$pager->pageSize= $this->pageSize;
		$pager->params = $condition;
		
		if($giftList){
			foreach($giftList as $k=>$v){
				$giftList[$k]['allSum'] = 0;
				$giftList[$k]['lastMonth'] = 0;
				$giftList[$k]['theMonth'] = 0;
				$giftList[$k]['lastWeek'] = 0;
				$giftList[$k]['theWeek'] = 0;
				$giftList[$k]['lastDay'] = 0;
			}
			
			$giftIds = array_keys($giftList);
			$giftBagSer = new GiftBagService();
			#累计销售
			$allSum = $giftBagSer->getSumGiftBagRecords($giftIds);
			$allSum2 = $this->giftSer->getSumSendGiftRecords($giftIds);
			if($allSum){
				foreach($allSum as $k=>$v){
					$giftList[$v['gift_id']]['allSum'] += $v['num'];
				}
			}
			if($allSum2){
				foreach($allSum2 as $k=>$v){
					$giftList[$v['gift_id']]['allSum'] += $v['num'];
				}
			}
			
			#上月销售
			$lastMonthStart = strtotime('-1 months',strtotime(date('Y-m-1 00:00:00',time())));
			$lastMonthEnd = strtotime(date('Y-m-1 00:00:00',time()));
			$lastSum = $giftBagSer->getSumGiftBagRecords($giftIds, $lastMonthStart, $lastMonthEnd);
			$lastSum2 = $this->giftSer->getSumSendGiftRecords($giftIds, $lastMonthStart, $lastMonthEnd);
			if($lastSum){
				foreach($lastSum as $k=>$v){
					$giftList[$v['gift_id']]['lastMonth'] += $v['num'];
				}
			}
			if($lastSum2){
				foreach($lastSum2 as $k=>$v){
					$giftList[$v['gift_id']]['lastMonth'] += $v['num'];
				}
			}
			#本月销售
			$theMonthStart = strtotime(date('Y-m-1 00:00:00',time()));
			$theMonthEnd  = time();
			$lastSum = $giftBagSer->getSumGiftBagRecords($giftIds, $theMonthStart, $theMonthEnd);
			$lastSum2 = $this->giftSer->getSumSendGiftRecords($giftIds, $theMonthStart, $theMonthEnd);
			if($lastSum){
				foreach($lastSum as $k=>$v){
					$giftList[$v['gift_id']]['theMonth'] += $v['num'];
				}
				unset($lastSum);
			}
			if($lastSum2){
				foreach($lastSum2 as $k=>$v){
					$giftList[$v['gift_id']]['theMonth'] += $v['num'];
				}
				unset($lastSum2);
			}
			#上周销售
			$lastWeekStart = strtotime('-1 weeks',strtotime('-'.(date('w',time())-1).' days'));
			$lastWeekStart = strtotime(date('Y-m-d 00:00:00',$lastWeekStart));
			$lastWeekEnd  = strtotime('+1 weeks',$lastWeekStart);
			$lastWeek = $giftBagSer->getSumGiftBagRecords($giftIds, $lastWeekStart, $lastWeekEnd);
			$lastWeek2 = $this->giftSer->getSumSendGiftRecords($giftIds, $lastWeekStart, $lastWeekEnd);
			if($lastWeek){
				foreach($lastWeek as $k=>$v){
					$giftList[$v['gift_id']]['lastWeek'] += $v['num'];
				}
			}
			if($lastWeek2){
				foreach($lastWeek2 as $k=>$v){
					$giftList[$v['gift_id']]['lastWeek'] += $v['num'];
				}
			}
			#本周销售
			$theWeekStart = strtotime('-'.(date('w',time())-1).' days');
			$theWeekStart = strtotime(date('Y-m-d 00:00:00',$theWeekStart));
			$theWeekEnd = time();
			$theWeek = $giftBagSer->getSumGiftBagRecords($giftIds, $theWeekStart, $theWeekEnd);
			$theWeek2 = $this->giftSer->getSumSendGiftRecords($giftIds, $theWeekStart, $theWeekEnd);
			if($theWeek){
				foreach($theWeek as $k=>$v){
					$giftList[$v['gift_id']]['theWeek'] += $v['num'];
				}
			}
			if($theWeek2){
				foreach($theWeek2 as $k=>$v){
					$giftList[$v['gift_id']]['theWeek'] += $v['num'];
				}
			}
			#昨日销售
			$lastDayStart = strtotime(date('Y-m-d 00:00:00',strtotime('-1 days')));
			$lastDayEnd = strtotime(date('Y-m-d 00:00:00',time()));
			$lastDay = $giftBagSer->getSumGiftBagRecords($giftIds, $lastDayStart, $lastDayEnd);
			$lastDay2 = $this->giftSer->getSumSendGiftRecords($giftIds, $lastDayStart, $lastDayEnd);
			if($lastDay){
				foreach($lastDay as $k=>$v){
					$giftList[$v['gift_id']]['lastDay'] += $v['num'];
				}
			}
			if($lastDay2){
				foreach($lastDay2 as $k=>$v){
					$giftList[$v['gift_id']]['lastDay'] += $v['num'];
				}
			}
		}
		
		$allStatus = array('0'=>'隐藏','1'=>'显示','2'=>'删除');#所有状态
		$allShopType = $this->giftSer->getShopType();#所有shop_type
		$allGiftType = $this->giftSer->getGiftType();#所有gift_type
		$allGiftCat = $this->giftSer->getGiftCategory();#获取礼物分类
		
		$this->render('gift_sale_status',array('allGiftCat'=>$allGiftCat,'pager'=>$pager,'giftList'=>$giftList,'allStatus'=>$allStatus,'allShopType'=>$allShopType,'allGiftType'=>$allGiftType,'condition'=>$condition));
	}
	
	/**
	 * 执行添加礼物操作
	 * 	礼物基本信息
	 * 	礼物效果信息
	 */
	public function addGiftDo(){
		$notices = array();
		$isAjax = false;
		
		//礼物编辑或添加操作
		$model = new GiftForm();
		$data = Yii::app()->request->getParam('GiftForm');
		if (!empty($data)) {
			$model->attributes = $data;
			if($model->validate()){
				$isEffect = true;
				//上传礼物图片
				if($this->uploadGiftImage($model,$data)){
					//上传效果
					if($this->uploadGiftEffect($model, $data)){
						$effect = array();
						if($isEffect){
							foreach ($data['effect_type'] as $k=>$v){
								$effect[$k]['effect_type']=$v;
							}
							foreach ($data['num'] as $k=>$v){
								$effect[$k]['num']=$v;
							}
							foreach ($data['timeout'] as $k=>$v){
								$effect[$k]['timeout']=$v;
							}
							foreach ($data['position'] as $k=>$v){
								$effect[$k]['position']=$v;
							}
							
							if (isset($data['e_remark']) && !empty($data['e_remark'])){
								foreach ($data['e_remark'] as $k=>$v){
									$effect[$k]['remark']=$v;
								}
								unset($data['e_remark']);
							}
							
							
							if(isset($data['effect_id'])){
								foreach ($data['effect_id'] as $k=>$v){
									$effect[$k]['effect_id']=$v;
								}
								unset($data['effect_id']);
							}
							
							
							if(isset($data['effect'])){
								foreach ($data['effect'] as $k=>$v){
									$effect[$k]['effect']=$v;
								}
								if(isset($data['effect_tmp'])){
									foreach($effect as $k=>$e){
										if (!isset($e['effect'])){
											$effect[$k]['effect'] = array_shift($data['effect_tmp']);
										}
									}
								}
							}else{
								foreach ($data['effect_tmp'] as $k=>$e){
									$effect[$k]['effect'] = $e;
								}
							}
							
						}
						
						unset($data['effect_type']);
						unset($data['num']);
						unset($data['timeout']);
						if(isset($data['effect_tmp'])) unset($data['effect_tmp']);
						unset($data['position']);
						unset($data['effect']);

						if($this->giftSer->saveGift($data,$isEffect,$effect)){
							$this->redirect($this->createUrl('gift/giftlist'));
						}else{
							$notices = $this->giftSer->getNotice();
						}
					}else{
						$notices = $model->getErrors();
					}
				}else{
					$notices = $model->getErrors();
				}
			}else{
				$notices = $model->getErrors();
			}
		}else{
			$this->giftSer->setNotice('Parameter', Yii::t('common', 'Parameters are wrong'));
			$notices = $this->giftSer->getNotice();
		}
		
		//获取礼物分类
		$allGiftCat = array();
		if($cat = $this->giftSer->getGiftCategory()){
			foreach ($cat as $v){
				$allGiftCat[$v['category_id']] = $v['cat_name'];
			}
		}
		
		//所有状态
		$allStatus = array('0'=>'隐藏','1'=>'显示');
		
		//所有等级
		$consumeSer = new ConsumeService();
		$allGrade = array();
		if($grades = $consumeSer->getAllUserRanks()){
			foreach ($grades as $grade){
				$allGrade[$grade['rank_id']] = $grade['name'];
			}
		}
		
		//所有shop_type
		$allShopType = $this->giftSer->getShopType();
		
		//所有gift_type
		$allGiftType = $this->giftSer->getGiftType();

		$this->render('gift_add',array('jsAjax'=>$isAjax,'notices'=>$notices,'allGiftCat'=>$allGiftCat,'allStatus'=>$allStatus,'allGrade'=>$allGrade,'allShopType'=>$allShopType,'allGiftType'=>$allGiftType));
		exit;
	}
	
	
	/**
	 * 上传礼物图片
	 * 
	 * @param GiftForm $model
	 * @param array $data
	 * @return boolean
	 */
	public function uploadGiftImage(GiftForm $model,Array &$data){
		$imgFile = CUploadedFile::getInstance($model,'image');
		if(!empty($imgFile)){
			$filename = $imgFile->getName();
			if($filename){
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
	
	
	/**
	 * 上传礼物动画
	 * 
	 * @param GiftForm $model
	 * @param array $data
	 * @return boolean
	 */
	public function uploadGiftEffect(GiftForm $model,Array &$data){
		$effectFiles = CUploadedFile::getInstances($model,'effect');
		if(is_array($effectFiles) && !empty($effectFiles)){
			$err = 0;
			$count = count($effectFiles);
			foreach ($effectFiles as $effectFile){
				$filename = $effectFile->getName();
				$extName = $effectFile->getExtensionName();
				$newName = uniqid().'.'.$extName;
				$uploadDir = ROOT_PATH."images".DIR_SEP.'gift'.DIR_SEP.'effect'.DIR_SEP;
				if (!file_exists($uploadDir)){
					mkdir($uploadDir,0777,true);
				}
				$uploadfile = $uploadDir.$newName;
				if($effectFile->saveAs($uploadfile,true)){
					$data['effect_tmp'][] = $newName;
				}else{
					$err ++;
				}
			}
			if ($err == $count){
				$model->addError('image', '礼物动画 上传失败');
				return false;
			}else{
				return true;
			}
		}elseif (!isset($data['gift_id'])){
			$model->addError('image', '礼物动画 不能为空');
			return false;
		}
		return true;
	}
	
	/**
	 * 删除动画效果
	 */
	public function delGiftEffect(){
		if(Yii::app()->request->isAjaxRequest){
			if($effect_id = Yii::app()->request->getParam('effect_id')){
				if($this->giftSer->delGiftEffectByEffectIds(array($effect_id))){
					echo 1;
				}else{
					echo "不在在该动画效果";
				}
			}else{
				echo '缺少参数';
			}
			exit;
		}else{
			throw new CHttpException(405);
		}
	}

	/**
	 * 删除礼物
	 */
	public function delGift(){
		if(Yii::app()->request->isAjaxRequest){
			if($gift_id = Yii::app()->request->getParam('gift_id')){
				if($this->giftSer->delGiftByGiftId($gift_id)){
					echo 1;
				}else{
					echo "删除失败 该礼物不在在或已经被删除";
				}
			}else{
				echo '删除失败 缺少参数';
			}
			exit;
		}else{
			throw new CHttpException(405);
		}
	}
	
	public function getSearchCondition(){
		$condition = array();
		$condition = Yii::app()->request->getParam('form');
		if($condition){
			return $condition;
		}
		
		if (Yii::app()->request->getParam('zh_name')){
			$condition['zh_name'] = Yii::app()->request->getParam('zh_name');
		}
		
		if (Yii::app()->request->getParam('is_display')){
			$condition['is_display'] = Yii::app()->request->getParam('is_display');
		}
		
		if (Yii::app()->request->getParam('shop_type')){
			$condition['shop_type'] = Yii::app()->request->getParam('shop_type');
		}
		
		if (Yii::app()->request->getParam('gift_type')){
			$condition['gift_type'] = Yii::app()->request->getParam('gift_type');
		}
		
		if (Yii::app()->request->getParam('cat_id')){
			$condition['cat_id'] = Yii::app()->request->getParam('cat_id');
		}
		
		return is_array($condition)?$condition:array();
	}
}