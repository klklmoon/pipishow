<?php
/**
 * 用户道具分类信息数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: PropsCategoryModel.php 8476 2013-04-07 13:56:31Z suqian $ 
 * @package model
 * @subpackage props
 */
class PropsCategoryModel extends PipiActiveRecord {

	public function tableName(){
		return '{{props_category}}';
	}
	
	/**
	 * @param string $className
	 * @return PropsCategoryModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	
    public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function rules(){
		return array(
			array('name,en_name','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('name,en_name','required'),
			array('name,en_name','unique'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'name'=>'道具分类名称',
			'en_name'=>'道具分类英文名称',
		);
	}
	
	/**
	 * 取得道具分类基本信息列表
	 * 
	 * @param mixed $uids
	 * @return PropsCategoryModel
	 */
	public function getPropsCategoryListByIds(array $catIds){
		if(empty($catIds))
			return array();
			
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('cat_id',$catIds);
		return $this->findAll($criteria);
	}
	
	
	/**
	 * 删除道具分类
	 * 
	 * @param array $ids
	 * @return int
	 */
	public function delPropsCatgoryByIds(array $ids){
		if(empty($ids))
			return array();
			
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('cat_id',$ids);
		return $this->deleteAll($criteria);
	}
}

