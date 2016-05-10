<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: ArchivesCategoryModel.php 11283 2013-05-30 09:57:21Z supeng $ 
 * @package model
 * @subpackage archives
 */
class ArchivesCategoryModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return ArchivesCategoryModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{archives_category}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_archives;
	}
	
	public function rules(){
		return array(
			array('name,en_name','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('name,en_name','required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'name'=>'档期分类名称',
			'en_name'=>'档期分类英文名称',
		);
	}
	
	
	/**
	 * 根据条件获取档期分类
	 * @param array $condition 档期分类查询条件
	 * @return array
	 */
	public function getArchivesCatByCondition(array $condition=array()){
		$criteria = $this->getDbCriteria();
		$condition&&$criteria->addCondition($condition);
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据分类ID获取档期分类
	 * @param array $catIds 分类ID
	 * @return array
	 */
	public function getArchivesCatByCatIdS(array $catIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('cat_id',$catIds);
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据分类ID删除档期分类
	 * @param array $catIds
	 * @return boolean
	 */
	public function delArchivesCatByCatIds(array $catIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('cat_id',$catIds);
		return $this->delete($criteria);
	}
	
	public function getAllArchiveCatByEnName($enName){
		$criteria = $this->getDbCriteria();
		$criteria->compare('en_name', $enName);
		return $this->find($criteria);
	}
}

?>