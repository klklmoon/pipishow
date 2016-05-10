<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: ArchivesAttributeModel.php 8564 2013-04-10 14:09:35Z leiwei $ 
 * @package model
 * @subpackage archives
 */
class ArchivesAttributeModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return ArchivesAttributeModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{archives_attribute}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_archives;
	}
	
	public function rules(){
		return array(
			array('name,value','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('name,value','required'),
			array('cat_id','numerical'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'cat_id'=>'档期分类ID',
			'name'=>'档期属性名称',
			'value'=>'档期属性值',
		);
	}
	
	
	/**
	 * 根据条件获取档期属性
	 * @param array $condition 档期属性查询条件
	 * @return array
	 */
	public function getArchivesAttributeByCondition(array $condition=array()){
		$criteria = $this->getDbCriteria();
		$condition&&$criteria->addCondition($condition);
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据分类ID获取档期属性
	 * @param array $catIds 分类ID
	 * @return array
	 */
	public function getArchivesCatByCatIds(array $catIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('cat_id',$catIds);
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据分类ID删除档期属性
	 * @param array $catIds
	 * @return boolean
	 */
	public function delArchivesCatByCatIds(array $catIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('cat_id',$catIds);
		return $this->delete($criteria);
	}
	
	/**
	 * 根据属性ID删除档期属性
	 * @param array $attributeIds
	 * @return boolean
	 */
	public function delArchivesAttributeByCatIds(array $attributeIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('attribute_id',$attributeIds);
		return $this->delete($criteria);
	}
	
}

?>