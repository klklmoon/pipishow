<?php

/**
 * 用户道具配置数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: PropsConfigModel.php 8476 2013-04-07 13:56:31Z suqian $ 
 * @package model
 * @subpackage props
 */
class PropsConfigModel extends PipiActiveRecord {
	
	public function tableName(){
		return '{{props_config}}';
	}
	
	/**
	 * @param string $className
	 * @return PropsConfigModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	
    public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function rules(){
		return array(
			array('prop_category','required'),
			array('prop_category','unique'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'category'=>'道具分类英文名称',
		);
	}
	
	public function primaryKey(){
		return array('prop_category','prop_enname');
	}
	/**
	 * 取得道具配置信息
	 * 
	 * @param string $category 道具分类
	 * @param string $propName 道具属性ID
	 * @return PropsConfigModel
	 */
	public function getPropsConfigByCategoryOrName($category,$propName = ''){
		if(!$category){
			return $this;
		}
		$option = array();
		$option['condition'] = ' prop_category = :category ';
		$option['params'][':category'] = $category;
		if($propName){
			$option['condition']  .= ' AND  prop_enname = :propname ';
			$option['params'][':propname'] = $propName;
		}
		return $this->find($option);
	}
}

?>