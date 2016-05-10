<?php
class PropsController extends PipiAdminController {
	
	/**
	 * @var PropsService 道具服务层
	 */
	public $propsSer;
	
	/**
	 * @var array 允许的操作
	 */
	public $allowOp = array('delPropsCat','delCatAttr','getCatAttr','delProps');
	
	/**
	 * @var string 当前操盘
	 */
	public $op;
	
	/**
	 * @var boolean 是否是Ajax请求
	 */
	public $isAjax;
	public $p;
	public $offset;
	public $pageSize = 20;
	
	public function init(){
		parent::init();
		$this->propsSer = new PropsService();
		$this->op = Yii::app()->request->getParam('op');
		$this->isAjax = Yii::app()->request->isAjaxRequest;
		if(!($this->p = Yii::app()->request->getParam('page'))){
			$this->p = 1;
		}
		$this->offset = ($this->p -1)*$this->pageSize;
	}
	
	/**
	 * 道具分类管理
	 */
	public function actionPropsCat(){
		$cateList = $this->propsSer->getPropsCatList();
		$isDisplay = array(0=>'隐藏',1=>'显示');
		$this->render('props_category',array('cateList'=>$cateList,'isDisplay'=>$isDisplay));
	}
	
	/**
	 * 添加道具分类 
	 */
	public function actionAddPropsCat(){
		//是否是添加或编辑操作
		if($data = Yii::app()->request->getParam('propscat')){
			$this->addPorpsCatDo($data);
		}
		
		//是否是删除操作
		if($this->op == 'delPropsCat' && in_array($this->op, $this->allowOp)){
			$this->delPropsCatDo();
		}
		
		$this->showAddPropsCat();
	}
	
	/**
	 * 道具列表管理
	 */
	public function actionPropsList(){
		$model = new PropsForm();
		if(!($condition = Yii::app()->request->getParam(get_class($model)))){
			$condition = array();
		}
		$this->render('props_list',
			array(
				'dataProvider'=>$this->propsSer,
				'condition'=>$condition,
				'model'=>$model
				)
			);
	}
	
	/**
	 * 添加道具
	 */
	public function actionAddProps(){
		//是否有获取分类属性的操作性为
		if($this->op == 'getCatAttr' && in_array($this->op, $this->allowOp)){
			$this->getCatAttrDo();
		}

		//是否删除道具
		if($this->op == 'delProps' && in_array($this->op, $this->allowOp)){
			$this->delPropsDo();
		}
		
		//是否执行添加或修改操作
		if(Yii::app()->request->getParam('props')){
			$this->addPropsDo();
		}
		
		$this->showAddPropsDo();
	}
	
	/**
	 * 购买记录
	 */
	public function actionBuyRecords(){
		$userPropSer = new UserPropsService();
		$sources = $userPropSer->getSourceTypeList();
		$allCat = $this->getPropsCat(2);
		$rs = $this->propsSer->getPropsByCondition();
		$allProps = array();
		if ($rs){
			foreach ($rs as $v){
				$allProps[$v['prop_id']] = $v['name'] ;
			}
		}
		
		$condition = $this->searchCondition();
		$rs = $userPropSer->getUserPropsRecordsByCondition($condition,$this->offset,$this->pageSize);
		$count = $rs['count'];
		$list = $rs['list'];
		
		$uids = array();
		$propIds = array();
		if ($list){
			#分类信息
			foreach ($list as $k=>&$v){
				$v['cat_name'] = isset($allCat['cat_id'])?$allCat['cat_id']:'';
				$uids[$v['uid']] = $v['uid'];
				$propIds[$v['prop_id']] = $v['prop_id'];
			}
			#道具信息
			$propsRs = $this->propsSer->getPropsByIds($propIds);
			#用户信息
			$userSer = new UserService();
			$uinfos = $userSer->getUserBasicByUids($uids);
			
			foreach ($list as $k=>&$v){
				$v['prop_info'] = isset($propsRs[$v['prop_id']])?$propsRs[$v['prop_id']]:array();
				$v['user_info'] = isset($uinfos[$v['uid']])?$uinfos[$v['uid']]:array();
			}
		}
		
		$pager = new CPagination($count);
		$pager->pageSize = $this->pageSize;
		$pager->params = $condition;
		$this->render('props_buy_records',array('condition'=>$condition,'list'=>$list,'pager'=>$pager,'allCat'=>$allCat,'sources'=>$sources,'allProps'=>$allProps));
	}
	
	/**
	 * 道具分类属性管理
	 */
	public function actionCatAttr(){
		$model = new PropsCategoryAttributeForm();
		if(!($condition = Yii::app()->request->getParam(get_class($model)))){
			$condition = array();
		}
		
		if ($cat_id = Yii::app()->request->getParam('cat_id')){
			$condition['cat_id'] = $cat_id;
		}
		
		if ($this->isAjax){
			$this->renderPartial('props_category_attr',
				array(
					'dataProvider'=>$this->propsSer,
					'condition'=>$condition,
					'model'=>$model
				)
			);
		}else{
			$this->render('props_category_attr',
				array(
					'dataProvider'=>$this->propsSer,
					'condition'=>$condition,
					'model'=>$model
				)
			);
		}
	}
	
	/**
	 * 添加道具分类属性
	 */
	public function actionAddCatAttr(){
		$notices = '';
		
		//是否是删除
		if($this->op =='delCatAttr' && in_array($this->op, $this->allowOp)){
			$this->delCatAttrDo();
		}
		
		//执行修改和添加动作
		if(Yii::app()->request->getParam('propscatattr')){
			$notices = $this->addCatAttrDo();
		}
		
		//修改信息
		$cinfo = array();
		if($attr_id = Yii::app()->request->getParam('attr_id')){
			if($cinfo = $this->propsSer->getPropsCatAttrtByIds(array($attr_id))){
				$cinfo = $cinfo[$attr_id];
			}
		}
		
		if ($this->isAjax){
			$this->renderPartial('props_catetory_attr_add',array('cinfo'=>$cinfo,'notices'=>$notices));
		}else{
			$this->render('props_catetory_attr_add',array('cinfo'=>$cinfo,'notices'=>$notices));
		}
	}
	
	
	/**
	 * 显示添加道具分类的模板
	 */
	public function showAddPropsCat($notices = ''){
		$isDisplay = array(0=>'隐藏',1=>'显示');
		
		//是否有修改的动作
		$cinfo = array();
		if ($cat_id = Yii::app()->request->getParam('cat_id')){
			$cinfo = $this->propsSer->getPropsCatByIds(array($cat_id));
			$cinfo = $cinfo[$cat_id];
		}
		
		if ($this->isAjax){
			$this->renderPartial('props_category_add',array('isDisplay'=>$isDisplay,'notices'=>$notices,'cinfo'=>$cinfo));
		}else{
			$this->render('props_category_add',array('isDisplay'=>$isDisplay,'notices'=>$notices,'cinfo'=>$cinfo));
		}
		exit;
	}
	
	/**
	 * 渲染添加道具属性操作
	 *
	 * @param array $notices
	 */
	public function showAddPropsDo($notices = array()){
		//是否有修改信息
		$cinfo = array();
		if($prop_id = Yii::app()->request->getParam('prop_id')){
			if($cinfo = $this->propsSer->getPropsByIds($prop_id)){
				$cinfo = $cinfo[$prop_id];
			}
		}
	
		if ($this->isAjax){
			$this->renderPartial('props_add',array('cinfo'=>$cinfo,'notices'=>$notices));
		}else{
			$this->render('props_add',array('cinfo'=>$cinfo,'notices'=>$notices));
		}
		exit;
	}
	

	/**
	 * 转换分类
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $row
	 * @param unknown_type $c
	 */
	public function transPropsCat($data,$row,$c){
		if (isset($data->cat_id)){
			if($cinfo = $this->propsSer->getPropsCatByIds(array($data->cat_id))){
				echo $cinfo[$data->cat_id]['name'];
			}
		}
	}
	
	/**
	 * 转换状态
	 *
	 * @param array $data
	 * @param int $row
	 * @param int $c
	 */
	public function transStatus($data,$row,$c) {
		if (isset($data->is_display)){
			if($data->is_display == 1){
				echo '<span class="label label-success">显示</span>';
			}else{
				echo '<span class="label label-important">隐藏</span>';
			}
		}
	}
	
	/**
	 * 转换道具属性类型
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $row
	 * @param unknown_type $c
	 */
	public function transCatAttrTypes($data,$row,$c) {
		if (isset($data->attr_type)){
			$type = $data->attr_type;
			$allTypes = $this->getCatAttrTypes();
			echo $allTypes[$type];
		}
	}
	
	/**
	 * 转换道具属性多选值
	 * @param unknown_type $data
	 * @param unknown_type $row
	 * @param unknown_type $c
	 */
	public function transIsMulti($data,$row,$c) {
		if (isset($data->is_multi)){
			if($data->is_multi == 1){
				echo '<span class="label label-success">多选</span>';
			}else{
				echo '<span class="label label-important">单选</span>';
			}
		}
	}

	/**
	 * 转换道具可获取的等级
	 * 
	 * @param unknown_type $data
	 * @param unknown_type $row
	 * @param unknown_type $c
	 */
	public function transRank($data,$row,$c) {
		if (isset($data->rank)){
			$allRanks = $this->getAllUserRank(2);
			echo isset($allRanks[$data->rank])?$allRanks[$data->rank]:'不限';
		}
	}
	
	
	/**
	 * 执行删除道具分类属性动作
	 */
	public function delCatAttrDo(){
		if (!$this->isAjax){
			throw new CHttpException(405);
		}
		
		if ($attr_id = Yii::app()->request->getParam('attr_id')){
			if($this->propsSer->delPropsCatAttribute(array($attr_id))){
				echo 1;
			}else{
				echo "删除失败 可能已经不在在该属性了";
			}
		}else{
			echo '删除失败 缺少参数';
		}
		exit;
	}
	
	/**
	 * 删除道具分类
	 *
	 * @throws CHttpException
	 */
	public function delPropsCatDo() {
		if (!$this->isAjax){
			throw new CHttpException(405);
		}
	
		if ($cat_id = Yii::app()->request->getParam('cid')){
			if($this->propsSer->delPropsCatgoryByIds(array($cat_id))){
				echo 1;
			}else{
				echo '系统内部错误 删除失败';
			}
		}else{
			echo "删除失败 缺少参数 ";
		}
		exit;
	}

	/**
	 * 删除道具动作
	 */
	public function delPropsDo(){
		if (!$this->isAjax){
			throw new CHttpException(405);
		}
	
		if (!($prop_id = Yii::app()->request->getParam('prop_id'))){
			echo '删除失败 缺少参数';
		}else{
			if($this->propsSer->delPropsByIds(array($prop_id))){
				echo 1;
			}else{
				echo "删除失败 可能已经不在在该道具了";
			}
				
		}
		exit;
	}
	
	/**
	 * @param int $type 返回类型 1：表示等级详细信息 2：表示等级名称
	 * @return multitype:unknown 
	 */
	public function getAllUserRank($type = 1){
		$allGrade = array();
		
		$consumeSer = new ConsumeService();
		if($grades = $consumeSer->getAllUserRanks()){
			foreach ($grades as $grade){
				if($type == 1){
					$allGrade[$grade['rank']] = $grade;
				}elseif ($type == 2){
					$allGrade[$grade['rank']] = $grade['name'];
				}
			}
		}
		
		return $allGrade;
	}
	
	
	/**
	 * 获取所有道具状态 
	 * @return array
	 */
	public function getPropsStatus(){
		return array(0=>'使用',1=>'停用',2=>'赠送');
	}

	/**
	 * 通过分类ID获取分类属性项操作
	 */
	public function getCatAttrDo() {
		if (!$this->isAjax){
			throw new CHttpException(405);
		}
		
		$prop_id = Yii::app()->request->getParam('prop_id');
		if(!($cat_id = Yii::app()->request->getParam('cat_id'))){
			echo 1; #缺少参数
		}

		if(!($html = $this->propsSer->getPropsCatgoryHtml($cat_id,$prop_id))){
			echo 2; #获取分类属性失败
		}else{
			$_html = '';
			foreach ($html as $v){
				$_html .= '<div class="control-group">';
				$_html .= '<label class="control-label" for="focusedInput">'.$v['name'].'</label>';
				$_html .= '<div class="controls">';
				$_html .= $v['html'];
				$_html .= '</div></div>';
			}
			echo $_html;
		}
		exit;
	}
	
	/**
	 * @param int $type 1：返回明细 2:返回分类名称
	 */
	public function getPropsCat($type = 1){
		$newCat = array();
		if($allCat = $this->propsSer->getPropsCatList()){
			if($type == 1){
				$newCat = $allCat;
			}else{
				foreach ($allCat as $cat_id => $catInfo){
					$newCat[$catInfo['cat_id']] = $catInfo['name'];
				}
			}
		}
		return $newCat;
	}
	
	
	/**
	 * 获取所有属性类型
	 * 
	 * @return array 
	 */
	public function getCatAttrTypes(){
		$allAttrTypes = array();
		if($allTypes = $this->propsSer->getPropsCatAttrTypeList()){
			foreach($allTypes as $type){
				$allAttrTypes[$type['id']] = $type['name'];
			}
		}
		return $allAttrTypes;
	}
	
	/**
	 * 执行添加或修改属性操作
	 */
	public function addPropsDo() {
		$notices = '';
		$props = Yii::app()->request->getParam('props');
		$attribute = Yii::app()->request->getParam('attribute');

		if (empty($props)) {
			$this->propsSer->setNotice('parameters', '提交的信息有误');
		}
		
		$model = new PropsForm();
		//展示图标上传
		if($this->uploadPropsImage($model,$props) && $this->uploadPropsGameImage($model,$props)) {
			foreach ($attribute as $key=>$value)
			{
				$key_arr=explode('_', $key);
				if($key_arr[0]=='file')
				{
					//上传道具图片
					if ($this->uploadPropsAttribute($model,$attribute,$value)){
						
						if(isset($attribute[$key])) unset($attribute[$key]);
					
					}else{
						$notices = $model->getErrors();
					}
				}
			}
			
			//存储信息
			if ($this->propsSer->saveProps($props,$attribute)){
				$this->redirect($this->createUrl('props/propslist'));
			}else{
				$this->propsSer->setNotice('op_info', '添加或删除失败');
				$notices = $this->propsSer->getNotice();
			}
			
		}else{
			$notices = $model->getErrors();
		}
			
		$this->showAddPropsDo($notices);
	}

	/**
	 * 添加道具分类
	 *
	 * @param array $data
	 */
	public function addPorpsCatDo(Array $data){
		if($this->propsSer->savePropsCategory($data)){
			$this->redirect($this->createUrl('props/propscat'));
		}else{
			$this->showAddPropsCat($this->propsSer->getNotice());
		}
	}
	
	/**
	 * 执行添加修改道具分类属性操作
	 */
	public function addCatAttrDo(){
		$notices = '';
		if($data = Yii::app()->request->getParam('propscatattr')){
			if($this->propsSer->savePropsCatAttribute($data)){
				$this->redirect($this->createUrl('props/catattr'));
			}else{
				$notices = $this->propsSer->getNotice();
			}
		}
		return $notices;
	}
	
	/**
	 * 上传道具展示图片
	 * 
	 * @param PropsForm $model
	 * @param array $data
	 * @return boolean
	 */
	public function uploadPropsImage(PropsForm $model,Array &$data){
		$imgFiles = CUploadedFile::getInstancesByName('props');
	 	if(is_array($imgFiles)){
	 		foreach ($imgFiles as $imgFile){
	 			$filename = $imgFile->getName();
	 			if($filename){
	 				$extName = $imgFile->getExtensionName();
	 				$newName = uniqid().'.'.$extName;
	 				$uploadDir = ROOT_PATH."images".DIR_SEP.'props'.DIR_SEP;
	 				if (!file_exists($uploadDir)){
	 					mkdir($uploadDir,0777,true);
	 				}
	 				$uploadfile = $uploadDir.$newName;
	 				if($imgFile->saveAs($uploadfile,true)){
	 					$data['image'] = $newName;
	 				}
	 			}
	 		}
		}elseif (!isset($data['prop_id'])){
			$model->addError('image', '道具展示图片不能为空');
			return false;
		}
		return true;
	}
	
	public function uploadPropsGameImage(PropsForm $model,Array &$data){
		$imgFiles = CUploadedFile::getInstancesByName('gameimg');
		if(is_array($imgFiles)){
			foreach ($imgFiles as $imgFile){
				$filename = $imgFile->getName();
				if($filename){
					$extName = $imgFile->getExtensionName();
					$newName = 'game_'.uniqid().'.'.$extName;
					$uploadDir = ROOT_PATH."images".DIR_SEP.'props'.DIR_SEP;
					if (!file_exists($uploadDir)){
						mkdir($uploadDir,0777,true);
					}
					$uploadfile = $uploadDir.$newName;
					if($imgFile->saveAs($uploadfile,true)){
						$data['game_image'] = $newName;
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * 道具属性动画或图片上传
	 * 
	 * @param PropsForm $model
	 * @param array $data
	 * @return boolean
	 */
	public function uploadPropsAttribute(PropsForm $model,Array &$data = array(),$getById){
		$imgFiles = CUploadedFile::getInstancesByName('attribute_'.$getById);
		if(is_array($imgFiles) && $data){
			foreach ($imgFiles as $imgFile){
				$filename = $imgFile->getName();
				if($filename){
					$extName = $imgFile->getExtensionName();
					$newName = uniqid().'.'.$extName;
					$uploadDir = ROOT_PATH."images".DIR_SEP.'props'.DIR_SEP;
					if (!file_exists($uploadDir)){
						mkdir($uploadDir,0777,true);
					}
					$uploadfile = $uploadDir.$newName;
					if($imgFile->saveAs($uploadfile,true)){
						$data[$getById] = $newName;
					}
				}
			}
		}
		return true;
	}
	
	public function searchCondition(){
		$condition = array();
		if (Yii::app()->request->getParam('form')){
			$condition = Yii::app()->request->getParam('form');
			return $condition;
		}
		
		if(Yii::app()->request->getParam('username')){
			$condition['username'] = Yii::app()->request->getParam('username');
		}
		
		if(Yii::app()->request->getParam('nickname')){
			$condition['nickname'] = Yii::app()->request->getParam('nickname');
		}
		
		if(Yii::app()->request->getParam('uid')){
			$condition['uid'] = Yii::app()->request->getParam('uid');
		}
		
		if(Yii::app()->request->getParam('realname')){
			$condition['realname'] = Yii::app()->request->getParam('realname');
		}
		
		if(Yii::app()->request->getParam('create_time_on')){
			$condition['create_time_on'] = Yii::app()->request->getParam('create_time_on');
		}
		
		if(Yii::app()->request->getParam('create_time_end')){
			$condition['create_time_end'] = Yii::app()->request->getParam('create_time_end');
		}
		
		if(is_numeric(Yii::app()->request->getParam('source'))){
			$condition['source'] = Yii::app()->request->getParam('source');
		}
		
		if(Yii::app()->request->getParam('cat_id')){
			$condition['cat_id'] = Yii::app()->request->getParam('cat_id');
		}
		
		if(Yii::app()->request->getParam('name')){
			$condition['name'] = Yii::app()->request->getParam('name');
		}
		
		if(Yii::app()->request->getParam('prop_id')){
			$condition['prop_id'] = Yii::app()->request->getParam('prop_id');
		}
		
		return $condition;
	}
}