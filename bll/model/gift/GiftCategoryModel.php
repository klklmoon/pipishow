<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: GiftCategoryModel.php 8971 2013-04-22 14:30:27Z supeng $ 
 * @package model
 * @subpackage gift
 */
class GiftCategoryModel extends PipiActiveRecord{
	
	/**
	 * @param unknown_type $className
	 * @return GiftCategoryModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{gift_category}}';
	}
	

	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function rules(){
		return array(
			array('cat_name,cat_enname','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('cat_name,cat_enname','required'),
			array('cat_name,cat_enname','unique'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'cat_name'=>'礼物分类名称',
			'cat_enname'=>'礼物分类英文名称',
			'display' =>'分类是否显示'
		);
	}
	
	public function getGiftCategoryByCatIds(array $catIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('category_id',$catIds);
		$criteria->select='category_id,cat_name,cat_enname';
		return $this->findAll($criteria);
	}
	
	public function delGiftCategoryByCatIds(array $catIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('category_id',$catIds);
		return $this->deleteAll($criteria);
	}
	
}

?>