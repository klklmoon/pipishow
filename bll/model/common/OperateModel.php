<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author su qian <suqian@pipi.cn>
 * @version $Id: OperateModel.php 10145 2013-05-14 04:49:25Z suqian $ 
 * @package model
 * @subpackage common
 */
class OperateModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return OperateModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{common_operate}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	public function delOperateByIds(array $ids){
		if(empty($ids)){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('operate_id',$ids);
		return $this->deleteAll($criteria);
	}
	
	public function getAllOperate(){
		$criteria = $this->getDbCriteria();
		$criteria->order = ' category,sub_category,sort DESC';
		$criteria->condition = 'category >= 0';
		return $this->findAll($criteria);
	}
	
	/**
	 * 获取指定分类的运营数据
	 * 
	 * @param string $category
	 * @param string $subCategory
	 * @return array
	 */
	public function getOperateByCategory($category,$subCategory = NULL){
		$criteria = $this->getDbCriteria();
		$criteria->order = ' sub_category ,sort DESC';
		$criteria->condition = ' category = :category ';
		$criteria->params[':category'] = $category;
		if(!is_null($subCategory)){
			$criteria->order = 'sort DESC';
			$criteria->condition .= ' AND sub_category=:subcategory ';
			$criteria->params[':subcategory'] = $subCategory; 
		}
		return $this->findAll($criteria);
		
	}
}