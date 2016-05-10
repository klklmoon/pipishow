<?php

/**
 * 用户道具基本信息之属性数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: PropsAttributeModel.php 8966 2013-04-22 12:54:48Z hexin $ 
 * @package model
 * @subpackage props 
 */
class PropsAttributeModel extends PipiActiveRecord {

	public function tableName(){
		return '{{props_attribute}}';
	}
	
	/**
	 * @param string $className
	 * @return PropsAttributeModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	
    public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	

	/**
	 * 获取道具属性
	 * 
	 * @param array $catIds
	 * @return array
	 */
	public function getPropsAttributeByPropIds(array $propIds){
		if(empty($propIds)){
			return array();
		}
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'cat_id,attr_name,attr_enname,value,attr_value,is_display,is_multi,attr_type,prop_id,a.attr_id,pattr_id';
		$criteria->join = 'INNER JOIN web_props_cat_attribute b ON a.attr_id = b.attr_id';
		$criteria->addInCondition('prop_id',$propIds);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
		
	}
	
	/**
	 * 按道具分类删除道具属性
	 * 
	 * @param array $catIds
	 * @return array
	 */
	public function delPropsAttributeByCatIds(array $catIds){
		if(empty($catIds)){
			return array();
		}
		$catIds = implode(',',$catIds);
		$sql = 'DELETE  FROM web_props_attribute WHERE attr_id IN (SELECT DISTINCT attr_id FROM web_props_cat_attribute WHERE cat_id IN (?))';
		$dbCommand = $this->getDbCommand();
		return $dbCommand->setText($sql)->execute(array($catIds));
	}
	
	/**
	 * 取得道具属性
	 * 
	 * @param int $propId 道具ID
	 * @param int $attrId 道具属性ID
	 * @return array
	 */
	public function getPropsAttrInfoByPropIdOrAttrId($propId,$attrId = 0){
		if($propId <= 0){
			return array();
		}
		$option = array();
		$option['condition'] = ' prop_id = :prop_id ';
		$option['params'][':prop_id'] = $propId;
		if($attrId > 0 ){
			$option['condition']  .= ' AND  attr_id = :attr_id ';
			$option['params'][':attr_id'] = $attrId;
		}
		return $this->findAll($option);
	}
	
	/**
	 * 删除道具属性
	 * 
	 * @param array $ids 标识ＩＤ
	 * @param int $type 类型  0表示道具标识，1表示按分类标识
	 * @return int
	 */
	public function delPropsAttributeByIds(array $ids,$type = 0){
		if(empty($ids)){
			return array();
		}
		if($type == 0){
			$field = 'prop_id';
		}elseif($type == 1){
			$field = 'attr_id';
		}else{
			$field = 'pattr_id';
		}
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->addInCondition($field,$ids);
		return $this->deleteAll($criteria);
	}
}

?>