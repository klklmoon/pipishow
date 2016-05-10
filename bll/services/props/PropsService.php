<?php

define('PROPS_ATTR_INPUT',0);
define('PROPS_ATTR_RADIO',1);
define('PROPS_ATTR_CHECKBOX',2);
define('PROPS_ATTR_SELECT',3);
define('PROPS_ATTR_TEXTAREA',4);
define('PROPS_ATTR_FILE',5);

define('PROPS_STATUS_USE',0);
define('PROPS_STATUS_HIDDEN',1);
define('PROPS_STATUS_SEND',2);

/**
 * the last known user to change this file in the repository  <$LastChangedBy: zfzhang $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: PropsService.php 17198 2014-01-02 06:48:32Z zfzhang $ 
 * @package service
 * @subpackage props
 */
class PropsService extends PipiService {
	
	protected $formElementsList = array(PROPS_ATTR_CHECKBOX,PROPS_ATTR_SELECT,PROPS_ATTR_RADIO);
	
	/**
	 * 创建道具
	 * 
	 * @param array $props 道具信息
	 * @param array $attributes　道具属性信息
	 * 
	 * @return int 0表示失败 　>=1返回道具ＩＤ表示成功
	 */
	public function saveProps(array $props,array $attributes = array()){
		if(isset($props['prop_id']) && $props['prop_id'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$propsModel =  new PropsModel();
		if(isset($props['prop_id'])){
			$orgPropsModel = $propsModel->findByPk($props['prop_id']);
			if(empty($orgPropsModel)){
				return $this->setNotice('props',Yii::t('props','The props does not exist'),0);
			}
			$this->attachAttribute($orgPropsModel,$props);
			if(!$orgPropsModel->validate()){
				return $this->setNotices($orgPropsModel->getErrors(),array());
			}
			$orgPropsModel->save();
			$insertId = $props['prop_id'];
		}else{
			$props['create_time'] = time();
			$this->attachAttribute($propsModel,$props);
			if(!$propsModel->validate()){
				return $this->setNotices($propsModel->getErrors(),array());
			}
			$propsModel->save();
			$insertId = $propsModel->getPrimaryKey();
		}
		if($insertId && $attributes){;
			$this->savePropsAttribute($insertId,$attributes);
			if($this->isAdminAccessCtl()){
				if(isset($props['prop_id'])){
					$op_desc = '编辑 道具('.$insertId.')';
				}else{
					$op_desc = '新增 道具('.$insertId.')';
				}
				$this->saveAdminOpLog($op_desc);
			}
		}
		return $insertId;
	}
	
	/**
	 * 批量创建道具属性
	 * 
	 * @param int $prop_id　道具ＩＤ
	 * @param array $propsAttribute　道具对应的属性
	 * @return boolean false表示失败 true表示成功
	 */
	public function savePropsAttribute($prop_id,array $attribute){
		if($prop_id <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$propsAttrModel = new PropsAttributeModel();
		$orgPropsAttrModels = $propsAttrModel->findAll('prop_id = :id',array(':id'=>$prop_id));
		$newData = array();
		$key = 0;
		if(!$orgPropsAttrModels){
			foreach($attribute as $attr_id=>$value){
				$newData[$key]['attr_id'] = $attr_id;
				$newData[$key]['value'] = is_array($value) ? implode(',',$value) : $value;
				$newData[$key]['prop_id'] = $prop_id;
				$key++;
			}
			if(!$newData){
				return  false;
			}
			$propsAttrModel->batchInsert($newData);
		}else{
			foreach($attribute as $attr_id=>$value){
				$value = is_array($value) ? implode(',',$value) : $value;
				/* @var $orgPropsAttrModel PropsAttributeModel */
				foreach($orgPropsAttrModels as $orgPropsAttrModel){
					if($orgPropsAttrModel['attr_id'] == $attr_id){
						if($orgPropsAttrModel['value'] != $value){
							$upData = array();
							$upData['value'] = $value;
							$this->attachAttribute($orgPropsAttrModel,$upData);
							$orgPropsAttrModel->save();
						};
						continue 2;
					}

				}
				$newData[$key]['attr_id'] = $attr_id;
				$newData[$key]['value'] = $value;
				$newData[$key]['prop_id'] = $prop_id;
				$key++;
			}
			if($newData){

				$propsAttrModel->batchInsert($newData);
			}
		}
		
		return true;
	}
	
	/**
	 * 存储单个道具属性
	 * 
	 * @param array $attribute
	 * @return int
	 */
	public function saveSinglePropsAttribute(array $attribute){
		if(!isset($attribute['pattr_id']) && ($attribute['attr_id'] <= 0 || $attribute['prop_id'] <= 0)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}	
		$propsAttributeModel=new PropsAttributeModel();;
		if(!isset($attribute['pattr_id'])){
			$attrInfoModels =$propsAttributeModel->getPropsAttrInfoByPropIdOrAttrId($attribute['prop_id'], $attribute['attr_id']);
			if($attrInfoModels){
				$attrInfoModel = array_pop($attrInfoModels);
				$attribute['pattr_id'] = $attrInfoModel->pattr_id;
			}
		}else{
			$attrInfoModel = $propsAttributeModel->findByPk($attribute['pattr_id']);
		}
		
		if(!isset($attribute['pattr_id'])){
			$this->attachAttribute($propsAttributeModel,$attribute);
			$propsAttributeModel->save();
			$insertId=$propsAttributeModel->getPrimaryKey();
		}else{
			if(!$attrInfoModel){
				return $this->setError(Yii::t('props','the props category attribute does not exist'),0);
			}
			$this->attachAttribute($attrInfoModel,$attribute);
			$attrInfoModel->save();
			$insertId = $attribute['pattr_id'];
		}
		return $insertId;
	}
	/**
	 * 创建道具分类
	 * 
	 * @param array $propsCategory　道具分类信息
	 * @return int 0表示失败　 >=1返回分类ＩＤ表示成功
	 */
	public function savePropsCategory(array $propsCategory){
		if(isset($propsCategory['cat_id']) && $propsCategory['cat_id'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$propsCategoryModel = new PropsCategoryModel();
		if(isset($propsCategory['cat_id'])){
			$orgPropsCategoryModel = $propsCategoryModel->findByPk($propsCategory['cat_id']);
			if(empty($orgPropsCategoryModel)){
				return $this->setNotice('props',Yii::t('props','the props category does not exist'),0);
			}
			$this->attachAttribute($orgPropsCategoryModel,$propsCategory);
			if(!$orgPropsCategoryModel->validate()){
				return $this->setNotices($orgPropsCategoryModel->getErrors(),array());
			}
			$orgPropsCategoryModel->save();
			$insertId = $propsCategory['cat_id'];
		}else{
			$propsCategory['create_time'] = time();
			$this->attachAttribute($propsCategoryModel,$propsCategory);
			if(!$propsCategoryModel->validate()){
				return $this->setNotices($propsCategoryModel->getErrors(),array());
			}
			$propsCategoryModel->save();
			$insertId = $propsCategoryModel->getPrimaryKey();
		}
		if ($insertId && $this->isAdminAccessCtl()){
			if(isset($propsCategory['cat_id'])){
				$op_desc = '编辑 道具分类('.$insertId.')';
			}else{
				$op_desc = '新增 道具分类('.$insertId.')';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $insertId;
	}
	
	/**
	 * 创建道具分类属性
	 * 
	 * @param array $abttribute
	 * @return int 0表示失败　 >=1返回分类属性ＩＤ表示成功
	 */
	public function savePropsCatAttribute(array $abttribute){
		if(!isset($abttribute['cat_id'])){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$category = $this->getPropsCatByIds($abttribute['cat_id']);
		if(empty($category)){
			return $this->setError('道具分类不存在',0);
		}
		$category = $category[$abttribute['cat_id']];
		$tmp = explode('_',$abttribute['attr_enname']);
		if($tmp[0] != $category['en_name']){
			$abttribute['attr_enname'] = $category['en_name'].'_'.$abttribute['attr_enname'];
		}
		
		$propsCatAttrModel = new PropsCatAttributeModel();
		if(isset($abttribute['attr_id'])){
			$orgPropsCatAttrModel = $propsCatAttrModel->findByPk($abttribute['attr_id']);
			if(empty($orgPropsCatAttrModel)){
				return $this->setNotice('props',Yii::t('props','the props category attribute does not exist'),0);
			}
			$this->attachAttribute($orgPropsCatAttrModel,$abttribute);
			if(!$orgPropsCatAttrModel->validate()){
				return $this->setNotices($orgPropsCatAttrModel->getErrors(),array());
			}
			$orgPropsCatAttrModel->save();
			$insertId = $abttribute['attr_id'];
		}else{
			$abttribute['create_time'] = time();
			$this->attachAttribute($propsCatAttrModel,$abttribute);
			if(!$propsCatAttrModel->validate()){
				return $this->setNotices($propsCatAttrModel->getErrors(),array());
			}
			$propsCatAttrModel->save();
			$insertId = $propsCatAttrModel->getPrimaryKey();
		}
		
		if($insertId && $this->isAdminAccessCtl()){
			if(isset($abttribute['attr_id'])){
				$op_desc = '编辑 道具分类属性('.$insertId.')';
			}else{
				$op_desc = '新增 道具分类属性('.$insertId.')';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $insertId;
	}
	
	/**
	 * 存储和修改道具配置
	 * 争对道具分类的配置，争对每个道具的配置
	 * 
	 * @param array $config
	 * @return boolean
	 */
	public function savePropsConfig(array $config){
		if(!isset($config['prop_category']) || !is_array($config['config'])){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		if(isset($config['prop_enname']) && !isset($config['prop_id'])){
			$config['prop_id'] = PropsModel::model()->findByAttributes(array('en_name'=>$config['prop_enname']))->prop_id;
		}
		$propsConfigModel = new PropsConfigModel();
		$propName = isset($config['prop_enname']) ? $config['prop_enname'] : '';
		$config['config'] = serialize($config['config']);
		
		$orgPropsConfigModel = $propsConfigModel->findByPk(array('prop_category'=>$config['prop_category'],'prop_enname'=>$propName));
		if($orgPropsConfigModel){
			$this->attachAttribute($orgPropsConfigModel,$config);
			return $orgPropsConfigModel->save();
		}
		$this->attachAttribute($propsConfigModel,$config);
		return $propsConfigModel->save();
		
	}
	
	/**
	 * 取得道具配置信息
	 * 
	 * @param string $category 道具分类
	 * @param string $propName 道具属性ID
	 * @return array
	 */
	public function getPropsConfigByCategoryOrName($category,$propName = ''){
		if(empty($category)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$propsConfigModel = PropsConfigModel::model();
		$orgPropsConfigModel = $propsConfigModel->getPropsConfigByCategoryOrName($category,$propName);
		
		if(empty($orgPropsConfigModel)){
			return array();
		}
		$orgPropsConfigModel->config = unserialize($orgPropsConfigModel->config); 
		return $orgPropsConfigModel->attributes;
	}
	/**
	 * 取得道具信息
	 * 
	 * @param int|array $ids 道具ID
	 * @param boolean $hasCategory 同时是否取得道具分类
	 * @param boolean $hasAttribute 同时是否取得道具属性
	 * @return array 返回道具信息
	 */
	public function getPropsByIds($ids,$hasCategory = false,$hasAttribute = false){
		if(empty($ids)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$propsModel = PropsModel::model();
		$ids = is_array($ids) ? $ids : array($ids);
		$propModels = $propsModel->getPropsByIds($ids);
		$props = $this->arToArray($propModels);
		$props = $this->buildProps($props);
		if($hasCategory){
			$this->mergePropsCategory($props);
		}
		if($hasAttribute){
			$this->mergePropsAttribute($props);
		}
		return $props;
	}
	
	/**
	 * 按分类取得可用道具信息
	 * 
	 * @param int $ids 道具ID
	 * @param boolean $hasCategory 同时是否取得道具分类
	 * @param boolean $hasAttribute 同时是否取得道具属性
	 * @return array 返回道具信息
	 */
	public function getPropsByCatId($id,$hasCategory = false,$hasAttribute = false){
		if($id <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$propsModel = PropsModel::model();
		$propModels = $propsModel->findAll('status = 0 AND cat_id = :cat_id',array(':cat_id'=>$id));
		$props = $this->arToArray($propModels);
		$props = $this->buildProps($props);
		if($hasCategory){
			$this->mergePropsCategory($props);
		}
		if($hasAttribute){
			$this->mergePropsAttribute($props);
		}
		return $props;
	}
	
	/**
	 * 根具分类ID取得道具分类信息
	 * 
	 * @param int|array $ids 道具分类ID
	 * @param boolean $hasAttribute 同时是否取得分类属性
	 * @return array 返回道具信息
	 */
	public function getPropsCatByIds($ids,$hasAttribute = false){
		$propsCategoryModel = new PropsCategoryModel();
		$ids = is_array($ids) ? $ids : array($ids);
		$propCategoryModels = $propsCategoryModel->getPropsCategoryListByIds($ids);
		$data = $this->arToArray($propCategoryModels);
		$data = $this->buildDataByIndex($data,'cat_id');
		if($hasAttribute){
			$this->mergePropsCatgoryAttr($data);
		}
		return $data;
	}
	
	/**
	 * 根具分类属性ID取得道具分类属性信息
	 * 
	 * @param int|array $ids 道具分类属性ID
	 * @param int $type 0表示按属性ID，1表示按分类ID
	 * @return array 返回道具信息
	 */
	public function getPropsCatAttrtByIds($ids,$type = 0){
		if(empty($ids)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$propsCatAttrModel = new PropsCatAttributeModel();
		$ids = is_array($ids) ? $ids : array($ids);
		$propsCatAttrModels = $propsCatAttrModel->getPropsCatAttrtByIds($ids,$type);
		$data = $this->arToArray($propsCatAttrModels);
		$data = $this->buildDataByIndex($data,'attr_id');
		
		foreach($data as $key=>$_data){
			if($_data['attr_value'] && in_array($_data['attr_type'],$this->formElementsList)){
				$data[$key]['list'] = array();
				parse_str($_data['attr_value'],$data[$key]['list']); 
			}
		}
		return $data;
	}
	
	/**
	 * 返回道具分类列表
	 * 
	 * @return array
	 */
	public function getPropsCatList(){
		$propsCategoryModel = PropsCategoryModel::model();
		$listModels = $propsCategoryModel->findAll();
		$list = $this->arToArray($listModels);
		return $this->buildDataByIndex($list,'cat_id');
	}
	
	/**
	 * 取得道具属性
	 * 
	 * @param string $propIds　道具ＩＤ
	 * @return array
	 */
	public function getPropsAttributeByPropIds($propIds){
		if(empty($propIds)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$propsAttributeDal = PropsAttributeModel::model();
		$propIds = is_array($propIds) ? $propIds : array($propIds);
		$attributes = $propsAttributeDal->getPropsAttributeByPropIds($propIds);
		return $this->buildPropsAttribute($attributes);
	}
	/**
	 * 按英文名城获取道具分类信息
	 * 
	 * @param string $enname
	 * @return array
	 */
	public function getPropsCategoryByEnName($enname){
		if(empty($enname)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$categoryModel = new PropsCategoryModel();
		$category = $categoryModel->findByAttributes(array('en_name'=>$enname));
		if($category){
			return $category->attributes;
		}
		return array();
	}
	/**
	 * 按英文名城获取道具信息
	 * 
	 * @param string $enname
	 * @return array
	 */
	public function getPropsByEnName($enname){
		if(empty($enname)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$propsModel = new PropsModel();
		$props = $propsModel->findByAttributes(array('en_name'=>$enname));
		if($props){
			return $props->attributes;
		}
		return array();
	}
	/**
	 * 获取分类属性类型列表
	 * 
	 * @param string $item
	 * @return string|array
	 */
	public function getPropsCatAttrTypeList($item = null){
		//id和一维数据key值必须一样
		$list = array(
			 PROPS_ATTR_INPUT => array('id'=>PROPS_ATTR_INPUT,'name'=>'文本输入'),
			 PROPS_ATTR_RADIO => array('id'=>PROPS_ATTR_RADIO,'name'=>'单选框'),
			 PROPS_ATTR_CHECKBOX => array('id'=>PROPS_ATTR_CHECKBOX,'name'=>'复选框'),
			 PROPS_ATTR_SELECT => array('id'=>PROPS_ATTR_SELECT,'name'=>'列表'),
			 PROPS_ATTR_TEXTAREA => array('id'=>PROPS_ATTR_TEXTAREA,'name'=>'文本域'),
			 PROPS_ATTR_FILE => array('id'=>PROPS_ATTR_FILE,'name'=>'文件上传'),
		);
		return is_null($item) ? $list : $list[$item];
	}
	
	/**
	 * 获取道具状态类型列表
	 * 
	 * @param string $item
	 * @return string|array
	 */
	public function getPropsStatusList($item = null){
		$list = array(
			PROPS_STATUS_USE => '使用',
			PROPS_STATUS_HIDDEN =>'隐藏',
			PROPS_STATUS_SEND =>'赠品',
		);
		return is_null($item) ? $list : $list[$item];
	}
	
	/**
	 * 取得道具分类属性下的HTML模块
	 * @param int $cat_id
	 * @param int $prop_id
	 * @return array
	 */
	public function getPropsCatgoryHtml($cat_id,$prop_id = 0){
		if($cat_id <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		
		$list = $this->getPropsCatAttrtByIds($cat_id,1);
		
		if($prop_id){
			$propsAttr = $this->getPropsAttributeByPropIds($prop_id);
			$propsAttr = $propsAttr[$prop_id];
			$propsAttr = $this->buildDataByIndex($propsAttr,'attr_id');
		}else{
			$propsAttr = array();
		}
		
		$newKey = 0;$newData = array();
		foreach($list as $attr_id=>$_list){
			$newData[$newKey] = array();
			$newData[$newKey]['html'] = '';
			$attrType = $_list['attr_type'];
			if($attrType  == PROPS_ATTR_INPUT){
				$defaultValue = isset($propsAttr[$attr_id]) ? $propsAttr[$attr_id]['value'] : $_list['attr_value'];
				$newData[$newKey]['html'] = "<input name='attribute[{$_list['attr_id']}]' value='{$defaultValue}' type='input' class='input-small focused' />";
			}elseif($attrType == PROPS_ATTR_FILE){
				$defaultValue = isset($propsAttr[$attr_id]) ? $propsAttr[$attr_id]['value'] : $_list['attr_value'];
				$newData[$newKey]['html'] = "<input name='attribute[file_{$_list['attr_id']}]'  type='hidden' class='input-small focused' value='{$_list['attr_id']}'/>";
				$newData[$newKey]['html'] .= "<input name='attribute_{$_list['attr_id']}'  type='file' class='input-small focused'/>";
				if($defaultValue){
					$fileInfo = explode('.',$defaultValue);
					$fileType = $fileInfo[count($fileInfo)-1];
					if(in_array($fileType,array('png','gif','jpeg','jpg','bmp'))){
						$defaultValue = Yii::app()->params['images_server']['url'].$defaultValue;
						$newData[$newKey]['html'] .= "<img src='{$defaultValue}' />";
					}elseif(in_array($fileType,array('swf'))){
						$newData[$newKey]['html'] .= "<embed width='50px'  height='50px' src='{$defaultValue}' wmode='transparent' bgcolor='#fff' quality='high' type='application/x-shockwave-flash'>";
					}
				}
			}elseif($attrType == PROPS_ATTR_TEXTAREA){
				$defaultValue = isset($propsAttr[$attr_id]) ? $propsAttr[$attr_id]['value'] : $_list['attr_value'];
				$newData[$newKey]['html'] = "<textarea name='attribute[{$_list['attr_id']}]' rows='10' cols='50'>{$defaultValue}</textarea>";
			}elseif(in_array($attrType,$this->formElementsList) ){
				if(!is_array($_list['list'])){
					continue;
				}
				
				$defaultValue = isset($propsAttr[$attr_id]) ? $propsAttr[$attr_id]['value'] : '';
				if($defaultValue != ''){
					$defaultValue = explode (',',$defaultValue);
				}
				foreach($_list['list'] as $_key=>$_value){
					$selected = $checked = '';
					if($attrType == PROPS_ATTR_RADIO){
						
						if($defaultValue && $_key == $defaultValue[0]){
							$checked = 'checked';
						}
						$newData[$newKey]['html'] .= "<input name='attribute[{$_list['attr_id']}]' value='{$_key}' type='radio' {$checked}/>{$_value}&nbsp;";
					}elseif($attrType == PROPS_ATTR_CHECKBOX){
						
						if($defaultValue && array_search($_key,$defaultValue) !== false){
							$checked = 'checked';
						}
						$newData[$newKey]['html'] .= "<input name='attribute[{$_list['attr_id']}][]' value='{$_key}' type='checkbox' {$checked}/>{$_value}&nbsp;";
					}elseif($attrType == PROPS_ATTR_SELECT){
						
						if($defaultValue && array_search($_key,$defaultValue) !== false){
							$selected = 'selected';
						}
						$newData[$newKey]['html'] .= "<option  value='{$_key}' {$selected}>{$_value}</option>";
					}
				}
				
				if($attrType == PROPS_ATTR_SELECT){
					$multiple= $list[$attr_id]['is_multi'] ? 'multiple="multiple"' : '';
					$selectedName = $list[$attr_id]['is_multi'] ? "attribute[{$_list['attr_id']}][]" : "attribute[{$_list['attr_id']}]";
					$newData[$newKey]['html'] = "<select style='width:150px' name='{$selectedName}' {$multiple}>{$newData[$newKey]['html']}</select>";
				}
				unset($list[$attr_id]['list']);
			}
			
			$defaultValue = '';
			$newData[$newKey]['name'] = $list[$attr_id]['attr_name'];
			$newData[$newKey]['attr_id'] = $list[$attr_id]['attr_id'];
			$newKey++;
		}
		return $newData;
		
	}
	
	/**
	 * 删除道具
	 * 
	 * @param int|array $ids 删除道具
	 * @param int $type 类型  0表示道具标识，1表示按分类标识
	 * @return int
	 */
	public function delPropsByIds($ids,$type = 0){
		if(empty($ids)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$ids = is_array($ids) ? $ids  : array($ids);
		$propsModel = PropsModel::model();
		if($propsModel->delPropsByIds($ids) && $type == 0){
			$this->delPropsAttribute($ids,0);
			if($this->isAdminAccessCtl()){
				$this->saveAdminOpLog('删除道具('.implode(',', $ids).')');
			}
		}
		return true;
	}
	
	/**
	 * 删除道具分类
	 * 
	 * @param int|array $ids
	 * @return int
	 */
	public function delPropsCatgoryByIds($ids){
		if(empty($ids)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$ids = is_array($ids) ? $ids  : array($ids);
		$propsCatModel = PropsCategoryModel::model();
		$affectedRows = $propsCatModel->delPropsCatgoryByIds($ids);
		$this->delPropsCatAttribute($ids,1);
		if($affectedRows){
			if ($this->isAdminAccessCtl()){
				$this->saveAdminOpLog('删除道具分类('.implode(',', $ids).')');
			}
		}
		return $affectedRows;
	}
	
	/**
	 * 删除道具分类属性
	 * 
	 * @param int|array $ids
	 * @param int $type 0表示属性ID，1表示分类ID
	 */
	public function delPropsCatAttribute($ids,$type = 0){
		if(empty($ids)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$ids = is_array($ids) ? $ids  : array($ids);
		
		if($type == 0){
			$this->delPropsAttribute($ids,1);
		}elseif($type == 1){
			$propsAttributeModel = PropsAttributeModel::model();
			$propsAttributeModel->delPropsAttributeByCatIds($ids);
		}
		
		$propsCatAttrModel = PropsCatAttributeModel::model();
		$flag = $propsCatAttrModel->delPropsCatAttributeByIds($ids,$type);
		if ($flag && $this->isAdminAccessCtl()){
			$this->saveAdminOpLog('删除道具分类属性('.implode(',', $ids).')');
		}
		return $flag;
	}
	
	/**
	 * 删除道具属性
	 * 
	 * @param int $ids
	 * @param int $type 0表示按道具ID，1表示按属性ID,2表示按道具属性标识ID
	 * @return int
	 */
	public function delPropsAttribute($ids,$type = 0){
		if(empty($ids)){
			return $this->setError(Yii::t('common','Parameter is empty'),0);
		}
		$ids = is_array($ids) ? $ids  : array($ids);
		
		$propsAttrModel = PropsAttributeModel::model();
		$flag = $propsAttrModel->delPropsAttributeByIds($ids,$type);
		if($this->isAdminAccessCtl() && $flag){
			$this->saveAdminOpLog('删除道具属性('.implode(',', $ids).')');
		}
		return $flag;
	}
	
	public function buildPropsAttribute(array &$data){
		if(empty($data)){
			return array();
		}
		
		foreach($data as $key=>$_data){
			if($_data['attr_type'] == PROPS_ATTR_FILE && $_data['value']){
				$data[$key]['org_value'] = $_data['value'];
				$data[$key]['value'] = '/props/'.$_data['value'];
			}
		}
		return  $this->buildDataByIndex($data,'prop_id');;
	}
	
	public function buildProps(array &$data){
		if(empty($data)){
			return array();
		}
		
		foreach($data as $key=>$_data){
			if($_data['image']){
				$_data['org_image'] = $_data['image'];
				$data[$key]['image'] = '/props/'.$_data['image'];
			}
			
			if($_data['game_image']){
				$_data['org_game_image'] = $_data['game_image'];
				$data[$key]['game_image'] = '/props/'.$_data['game_image'];
			}
			
			if(isset($data['status']) && !empty($data['status'])){
				$data[$key]['status_desc'] = $this->getPropsStatusList($data['status']);
			}
		}
		return $this->buildDataByIndex($data,'prop_id');;
	}
	
	/**
	 * 合并道具分类
	 * 
	 * @param array $data
	 * @return array
	 */
	public function mergePropsCategory(array &$data){
		if(!$data){
			return $data;
		}
		$cat_ids = array_keys($this->buildDataByIndex($data,'cat_id'));
		$catData = $this->getPropsCatByIds($cat_ids);
		foreach($data as $key=>$_data){
			if(isset($catData[$_data['cat_id']])){
				$data[$key]['category'] = $catData[$_data['cat_id']];
			}
		}
		return $data;
	}
    /**
	 * 合并道具分类的分类属性
	 * 
	 * @param array $data
	 * @return array
	 */
	public function mergePropsCatgoryAttr(array &$data){
		$cat_ids = array_keys($data);
		$attrData = $this->getPropsCatAttrtByIds($cat_ids,1);
		
		if(empty($attrData)){
			return $data;
		}
		$attrData = $this->buildDataByIndex($attrData,'cat_id');
		foreach($data   as $key => $_data){
			if(isset($attrData[$_data['cat_id']])){
				$data[$key]['attribute'] = $attrData[$_data['cat_id']];
			}
		}
		return $data;
	}
	
	/**
	 * 合并道具属性
	 * 
	 * @param array $data
	 * @return array
	 */
	public function mergePropsAttribute(array &$data){
		if(!$data){
			return $data;
		}
		$propIds = array_keys($this->buildDataByIndex($data,'prop_id'));
		$attributes = $this->getPropsAttributeByPropIds($propIds);
		
		foreach($data as $prop_id=>$_data){
			if(isset($attributes[$prop_id])){
				$data[$prop_id]['attribute'] = $attributes[$prop_id];
			}
		}
		return $data;
		
	}
	
	/**
	 * 道具分类属性搜索
	 *
	 * @author supeng
	 * @param array $data
	 * @return CActiveDataProvider
	 */
	public function searchPropsCatAttr(Array $data = array()){
		$catAttrModel = new PropsCatAttributeModel();
		$this->attachAttribute($catAttrModel, $data);
		$dataProvider = $catAttrModel->search();
		return $dataProvider;
	}
	
	/**
	 * 道具搜索
	 *
	 * @author supeng
	 * @param array $data
	 * @return CActiveDataProvider
	 */
	public function searchProps(Array $data = array()){
		$propsModel = new PropsModel();
		$this->attachAttribute($propsModel, $data);
		$dataProvider = $propsModel->search();
		return $dataProvider;
	}
	
	/**
	 * 根据属性获取道具信息
	 * 
	 * @author guoshaobo
	 * @param array $condition
	 * @return array
	 */
	public function getPropsByCondition(array $condition = array())
	{
		$propsModel = new PropsModel();
		$res = $propsModel->findAllByAttributes($condition);
		$data = $this->arToArray($res);
		if ($data){
			$data = $this->buildDataByIndex($data, 'prop_id');
		}
		return $data;
	}
	
	/**
	 * 获得道具logo图片
	 * @param string $fileName
	 * @return string
	 */
	public function getPropsUrl($fileName){
		return $this->getUploadUrl().'props/'.$fileName;
	}
	
	//检测用户有效vip
	public function checkValidVipByUid($uid)
	{
		$timeStamp=time();
		
		$userJsonInfoService = new UserJsonInfoService();
		$userInfo=$userJsonInfoService->getUserInfo($uid,false);
		//存在已失效的vip或不存在vip才检测
		if((isset($userInfo['vip']) && $userInfo['vip']['vt']>0 && $userInfo['vip']['vt']<$timeStamp) || !isset($userInfo['vip']))
		{
			$userPropsService = new UserPropsService();
			$userPropsAttriubte = array();
			$userJson['vip'] = array('t' => 1, 'h' => 0, 'img' => '', 'vt' => 0);
			$userPropsAttriubte['uid'] = $uid;
			
			$yellowProps = $this->getPropsByEnName('vip_yellow');
			$yellowVips = $userPropsService->getUserValidPropsOfBagByPropId($uid, $yellowProps['prop_id'], $timeStamp);
			if ($yellowVips) {
				$yellowVips = array_pop($yellowVips);
			}
			
			$purpleProps = $this->getPropsByEnName('vip_purple');
			$purpleVips = $userPropsService->getUserValidPropsOfBagByPropId($uid, $purpleProps['prop_id'], $timeStamp);
			if ($purpleVips) {
				$purpleVips = array_pop($purpleVips);
			}
			
			if ($purpleVips ) { // 处理紫色vip
				$userPropsAttriubte['vip_type'] = 2;
				$userPropsAttriubte['vip'] = $purpleProps['prop_id'];
				$userJson['vip']['t'] = 2;
				$userJson['vip']['img'] = '/props/' . $purpleProps['image'];
				$userJson['vip']['vt'] = $purpleVips['valid_time'];
	
				$userPropsService->saveUserPropsAttribute($userPropsAttriubte); // 存储用户道具属性
				$userJsonInfoService->setUserInfo($uid, $userJson); // 更新用户信息
				$zmq = $userPropsService->getZmq();
				$zmq->sendZmqMsg(609, array('type' => 'update_json', 'uid' => $uid, 'json_info' => $userJson));
			}
			elseif($yellowVips)	// 处理黄色vip
			{
				$userPropsAttriubte['vip_type'] = 1;
				$userPropsAttriubte['vip'] = $yellowProps['prop_id'];;
				$userJson['vip']['t'] = 1;
				$userJson['vip']['img'] = '/props/' . $yellowProps['image'];
				$userJson['vip']['vt'] = $yellowVips['valid_time'];
	
				$userPropsService->saveUserPropsAttribute($userPropsAttriubte); // 存储用户道具属性
				$userJsonInfoService->setUserInfo($uid, $userJson); // 更新用户信息
				$zmq = $userPropsService->getZmq();
				$zmq->sendZmqMsg(609, array('type' => 'update_json', 'uid' => $uid, 'json_info' => $userJson));
			}
		}

	}
	
	//检测并停用另一个vip
	public function checkAndStopVip($uid,$prop_id)
	{
		$userPropsBagModel = new UserPropsBagModel();
		//检测背包中是否有已处于启用状态的vip
		$category = $this->getPropsCategoryByEnName('vip');
		$cat_id = $category['cat_id'];
		
		$option=array();
		$option['condition'] = 'uid = :uid AND cat_id = :cat_id ';
		$option['params'] = array(':uid'=>$uid,':cat_id'=>$cat_id);
		$userPropsBagList=$this->arToArray($userPropsBagModel->findAll($option));
		if(count($userPropsBagList)>0)
		{
			foreach ($userPropsBagList as $userPropsBagRow)
			{
				//如果有另一个启用中的vip，则将它切换为停用
				if($userPropsBagRow['prop_id']!=$prop_id && $userPropsBagRow['use_status']==0)
				{
					$this->stopVipOfBag($userPropsBagRow['uid'],$userPropsBagRow['prop_id']);
				}
			}
		}
	}
	
	//开启道具背包中的vip
	public function openVipOfBag($uid,$prop_id)
	{
		/*
		 * 获得道具背包中的vip信息
		*/
		$userPropsService = new UserPropsService();
		$userPropsBagModel = new UserPropsBagModel();
		$_userPropsBagModel = $userPropsBagModel->findByAttributes(array('uid'=>$uid,'prop_id'=>$prop_id));
		$currentTime=time();
			
		//启用vip	
		$bag=array();
		$bag['uid'] = $uid;
		$bag['prop_id'] = $prop_id;
		$bag['use_status']=0;
		if($_userPropsBagModel->valid_time!=0 || $_userPropsBagModel->num!=0 )
		{
			//重新计算过期时间
			$remain_days=$_userPropsBagModel->num-$this->getVipUsedDays($uid,$prop_id);
			$bag['valid_time'] = strtotime(date("Y-m-d 00:00:00",$currentTime))+$remain_days*3600*24-1;
		}
		$bag['update_time']=$currentTime;		//启用时间
		//var_dump($bag);exit;
		//检测背包中是否有已处于启用状态的vip
		$this->checkAndStopVip($uid, $prop_id);
		//更新背包
		$this->attachAttribute($_userPropsBagModel, $bag);
		$_userPropsBagModel->save();
		//更新userJson
		return $this->updateUserJsonOfVip($uid, $prop_id);
	}
	
	
	//停用道具背包中的vip
	public function stopVipOfBag($uid,$prop_id)
	{
		/*
		 * 获得道具背包中的vip信息
		 */
		$userPropsService = new UserPropsService();
		$userPropsBagModel = new UserPropsBagModel();
		$_userPropsBagModel = $userPropsBagModel->findByAttributes(array('uid'=>$uid,'prop_id'=>$prop_id));
		$currentTime=time();
		
		if(isset($_userPropsBagModel->valid_time) && $_userPropsBagModel->valid_time>0)
			$this->saveVipUseRecords($uid, $prop_id, $_userPropsBagModel,$currentTime);
		
		//更新背包中vip状态
		$bag=array();
		$bag['uid'] = $uid;
		$bag['prop_id'] = $prop_id;
		$bag['use_status']=1;
		$bag['update_time']=$currentTime;		//停用时间
			
		//更新背包
		$this->attachAttribute($_userPropsBagModel, $bag);
		$_userPropsBagModel->save();
		//更新userJson
		return $this->updateUserJsonOfVip($uid, $prop_id,1);
	}
	
	//存储vip记时记录
	public function saveVipUseRecords($uid,$prop_id,$_userPropsBagModel,$currentTime)
	{
		/*
		 * 检测当天是否已经有计时记录
		*/
		$userPropsUseModel = new UserPropsUseModel();
		$option=array();
		$option['condition'] = 'uid = :uid AND prop_id = :prop_id AND use_type=1 AND create_time >= :stime AND create_time <= :etime';
		$option['params'] = array(
			':uid'=>$uid,
			':prop_id'=>$prop_id,
			':stime'=>strtotime(date("Y-m-d 00:00:00",$currentTime)),
			':etime'=>strtotime(date("Y-m-d 23:59:59",$currentTime))
		);
		$option['order'] = 'use_id DESC';
		$_userPropsUse=$userPropsUseModel->find($option);
		if($_userPropsUse)
		{
			$records=array();
			$records['create_time'] = $currentTime;		//此次计时停用时间
			$records['valid_time']=$_userPropsBagModel->update_time;//此次计时启用时间
			$records['num'] = $this->getVipTimingDays($records['valid_time'],$records['create_time']);	//vip记时天数
			//存储计时记录
			$this->attachAttribute($_userPropsUse,$records);
			$flag = $_userPropsUse->save();
		}
		else
		{
			/*
			 *插入vip使用计时记录
			*/
			$records=array();
			$records['uid'] = $uid;
			$records['prop_id'] = $prop_id;
			$records['cat_id'] = $_userPropsBagModel->cat_id;
			$records['use_type'] = 1;			//vip
			$records['create_time'] = $currentTime;		//此次计时停用时间
			$records['valid_time']=$_userPropsBagModel->update_time;//此次计时启用时间
			$records['num'] = $this->getVipTimingDays($records['valid_time'],$records['create_time']);	//vip记时天数
			//存储计时记录
			$this->attachAttribute($userPropsUseModel,$records);
			$flag = $userPropsUseModel->save();
		}
	}
	
	/**
	 * 存储vip到背包
	 * 
	 * @param int $source 0表示前台购买，1表示后台赠送辑
	 */
	public function saveVipToBag($bag,$buyDays,$source=0)
	{
		$userPropsBagModel = new UserPropsBagModel();
		$_userPropsBagModel = $userPropsBagModel->findByAttributes(array('uid'=>$bag['uid'],'prop_id'=>$bag['prop_id']));
		$currentTime=time();
		$todayTime=strtotime(date("Y-m-d 00:00:00",$currentTime))-1;
		
		//没有传入vip启停用状态，设为停用
		if(!isset($bag['use_status']))
			$bag['use_status']=1;
		
		//背包中有该vip
		if($_userPropsBagModel)
		{
			//如果用户背包中道具有效期不是永久就处理，否则不作任何操作
			if($_userPropsBagModel->num> 0 && $_userPropsBagModel->valid_time>0){
				//背包中原启停用状态与传入启停用状态相等（表示启停用状态不变）
				if($_userPropsBagModel->use_status==$bag['use_status'])
				{
					//如果背包中原状态与传入状态都为停用，则无需更新valid_time
					if($bag['use_status']==1)
					{
						$bag['valid_time'] = $currentTime;
					}
					else	//如果背包中原状态与传入状态都为启用，需重新计算valid_time(如果原有效期大于当前时间，则原有效期加上传入天数时间，否则为当前时间加传入天数时间)
					{
						$old_valid_time=strtotime(date("Y-m-d 23:59:59",$_userPropsBagModel->valid_time));
						$bag['valid_time'] = ($_userPropsBagModel->valid_time>$currentTime)?($old_valid_time+$buyDays*3600*24):($todayTime+$buyDays*3600*24);
					}
				}
				else	//背包中原启停用状态与传入启停用状态不等（表示启停用状态改变）
				{
					//启用购买逻辑
					if($source==0)
					{
						//改变为停用
						if($bag['use_status']==1)
						{
							$bag['update_time']=$currentTime;		//停用时间
							//记录使用记录
							$this->saveVipUseRecords($bag['uid'], $bag['prop_id'], $_userPropsBagModel,$currentTime);
						}
						else //改变为启用
						{
							$bag['update_time']=$currentTime;		//启用时间
							$remain_days=$_userPropsBagModel->num-$this->getVipUsedDays($bag['uid'],$bag['prop_id']);
							$bag['valid_time'] = $todayTime+($buyDays+$remain_days)*3600*24;
						}
					}
					else {
						if(isset($bag['use_status']))
							unset($bag['use_status']);
						if($_userPropsBagModel->use_status==0)
						{
							$old_valid_time=strtotime(date("Y-m-d 23:59:59",$_userPropsBagModel->valid_time));
							$bag['valid_time'] = ($_userPropsBagModel->valid_time>$currentTime)?($old_valid_time+$buyDays*3600*24):($todayTime+$buyDays*3600*24);
						}
					}
				}
				//天数
				$bag['num'] = $_userPropsBagModel->num+$buyDays;
			}
			//如果传入天数为永久，则把背包中有效期设为永久
			if($buyDays==0)
			{
				$bag['valid_time'] = 0;
				$bag['num']=0;
			}
			
			$this->attachAttribute($_userPropsBagModel, $bag);
			$flag=$_userPropsBagModel->save();
			if($flag && isset($bag['use_status']) && $bag['use_status']==0)
			{
				//检测背包中是否有已处于启用状态的vip
				$this->checkAndStopVip($bag['uid'], $bag['prop_id']);
				$this->updateUserJsonOfVip($bag['uid'], $bag['prop_id']);
			}
			elseif($flag && isset($bag['use_status']) && $bag['use_status']==1)
			{
				$this->updateUserJsonOfVip($bag['uid'], $bag['prop_id'],1);
			}
		}
		else	//背包中没有该vip
		{
			if($buyDays==0)
				$bag['valid_time'] = 0;
			else
				$bag['valid_time'] = $todayTime+$buyDays*3600*24;
			$bag['num'] = $buyDays;
			$this->attachAttribute($userPropsBagModel, $bag);
			$flag=$userPropsBagModel->save();
			if($flag && isset($bag['use_status']) && $bag['use_status']==0)
			{
				//检测背包中是否有已处于启用状态的vip
				$this->checkAndStopVip($bag['uid'], $bag['prop_id']);
				$this->updateUserJsonOfVip($bag['uid'], $bag['prop_id']);
			}
			elseif($flag && isset($bag['use_status']) && $bag['use_status']==1)
			{
				$this->updateUserJsonOfVip($bag['uid'], $bag['prop_id'],1);
			}
		}
		return $flag;
	}
	
	//计算vip计时天数
	public function getVipTimingDays($startTime,$endTime)
	{
		if(empty($endTime) || empty($startTime))
			return 0;
		$tsTime=strtotime(date("Y-m-d 00:00:00",$startTime));
		$teTime=strtotime(date("Y-m-d 23:59:59",$endTime))+1;
		return ($teTime-$tsTime)/(3600*24);
	}
	
	//返回vip已使用天数
	public function getVipUsedDays($uid,$prop_id)
	{	
		$userPropsUseModel = new UserPropsUseModel();

		$criteria = $userPropsUseModel->getDbCriteria();
		$criteria->compare('uid', $uid);
		$criteria->compare('prop_id', $prop_id);
		$criteria->compare('use_type', 1);
		$criteria->select = 'sum(num) as UsedDays,max(create_time) as MaxStoptime ';
		$statRow=$userPropsUseModel->getCommandBuilder()->createFindCommand($userPropsUseModel->tableName(), $criteria)->queryRow();

		$currentTime=time();
		$today_sdate=date("Y-m-d",$currentTime);
		$maxstop_sdate=date("Y-m-d",$statRow['MaxStoptime']);
		return ($today_sdate==$maxstop_sdate)?$statRow['UsedDays']-1:$statRow['UsedDays'];
	}
	
	//返回最早一次购买或赠送vip的时间
	public function getMinBuyVipTime($uid,$prop_id)
	{
		$userrPropsRecordsModel=new UserPropsRecordsModel();
		$criteria = $userrPropsRecordsModel->getDbCriteria();
		
		$criteria->compare('uid', $uid);
		$criteria->compare('prop_id', $prop_id);
		$criteria->select = 'min(create_time) as minByVipTime ';
		return $userrPropsRecordsModel->getCommandBuilder()->createFindCommand($userrPropsRecordsModel->tableName(), $criteria)->queryScalar();
	}
	
	//更新userJson中的vip信息,$use_status==0表示启用
	public function updateUserJsonOfVip($uid,$prop_id,$use_status=0)
	{
		$timeStamp=time();
		$userJsonInfoService = new UserJsonInfoService();
		
/* 		//更新为停用直接返回
		if($use_status==1)
		{
			$userJson=array();
			$userJson['vip']['us']=1;
			$userJsonInfoService->setUserInfo($uid, $userJson); // 更新用户信息
			$zmq = $this->getZmq();
			return $zmq->sendZmqMsg(609, array('type' => 'update_json', 'uid' => $uid, 'json_info' => $userJson));
		}	 */	
		
		$userPropsService = new UserPropsService();
		
		//取得vip道具信息
		$yellowProps = $this->getPropsByEnName('vip_yellow');
		$purpleProps = $this->getPropsByEnName('vip_purple');
		
		//取得背包中为启用状态的vip
		$userPropsBagModel = new UserPropsBagModel();
		$_userPropsBagModel = $userPropsBagModel->findByAttributes(array(
			'uid'=>$uid,
			'prop_id'=>$prop_id,
			//'use_status'=>0
		));
		
		$userPropsAttributeModel = new UserPropsAttributeModel();
		$_userPropsAttributeModel = $userPropsAttributeModel->findByPk($uid);
		
		//背包中没有启用的vip，则直接返回
		if(!isset($_userPropsBagModel->bag_id))
			return false;
				
		if ($purpleProps['prop_id']==$prop_id ) { // 处理紫色vip
			$userPropsAttriubte = array();
			$userPropsAttriubte['uid'] = $uid;
			$userPropsAttriubte['vip_type'] = 2;
			$userPropsAttriubte['vip'] = $purpleProps['prop_id'];

			$userJson=array();
			if(isset($_userPropsAttributeModel->is_hidden) && $_userPropsAttributeModel->is_hidden==1)
				$userJson['vip']['h'] = 1;
			else
				$userJson['vip']['h'] = 0;
			//停用
			if($use_status)
			{

				$userJson['vip']['t'] = 2;
				$userJson['vip']['img'] = '/props/' . $purpleProps['image'];
				$userJson['vip']['vt'] = $_userPropsBagModel->valid_time;
				$userJson['vip']['us']=1;
			}
			else				//启用
			{
				$userJson['vip']['t'] = 2;
				$userJson['vip']['img'] = '/props/' . $purpleProps['image'];
				$userJson['vip']['vt'] = $_userPropsBagModel->valid_time;
				$userJson['vip']['us']=0;
			}

			$userPropsService->saveUserPropsAttribute($userPropsAttriubte); // 存储用户道具属性
			$userJsonInfoService->setUserInfo($uid, $userJson); // 更新用户信息
			$zmq = $this->getZmq();
			return $zmq->sendZmqMsg(609, array('type' => 'update_json', 'uid' => $uid, 'json_info' => $userJson));
		}
		elseif($yellowProps['prop_id']==$prop_id)	// 处理黄色vip
		{
			$userPropsAttriubte = array();
			$userPropsAttriubte['uid'] = $uid;
			$userPropsAttriubte['vip_type'] = 1;
			$userPropsAttriubte['vip'] = $yellowProps['prop_id'];

			$userJson=array();
			if(isset($_userPropsAttributeModel->is_hidden) && $_userPropsAttributeModel->is_hidden==1)
				$userJson['vip']['h'] = 1;
			else
				$userJson['vip']['h'] = 0;
			//停用
			if($use_status)
			{
				$userJson['vip']['t'] = 1;
				$userJson['vip']['img'] = '/props/' . $yellowProps['image'];
				$userJson['vip']['vt'] = $_userPropsBagModel->valid_time;
				$userJson['vip']['us']=1;
			}
			else				//启用
			{
				$userJson['vip']['t'] = 1;
				$userJson['vip']['img'] = '/props/' . $yellowProps['image'];
				$userJson['vip']['vt'] = $_userPropsBagModel->valid_time;
				$userJson['vip']['us']=0;
			}

			$userPropsService->saveUserPropsAttribute($userPropsAttriubte); // 存储用户道具属性
			$userJsonInfoService->setUserInfo($uid, $userJson); // 更新用户信息
			$zmq = $this->getZmq();
			return $zmq->sendZmqMsg(609, array('type' => 'update_json', 'uid' => $uid, 'json_info' => $userJson));
		}
	}
	
}

?>