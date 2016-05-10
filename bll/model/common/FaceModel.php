<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author guoshaobo <leiwei@pipi.cn>
 * @version $Id: FaceModel.php 9671 2013-05-06 13:51:21Z leiwei $ 
 * @package model
 * @subpackage FaceModel
 */
class FaceModel extends PipiActiveRecord
{
	/**
	 * @param string $className
	 * @return OperateModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{face}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	public function rules(){
		return array(
			array('name,type,code,image','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('name,type,code','required'),
			array('name,code','unique'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'name'=>'表情名称',
			'type'=>'表情类型',
			'code' =>'转义码',
			'image'=>'表情图标',
			'displayorder'=>'排序'
		);
	}
	
	/**
	 * 获取所有表情
	 * @return array
	 */
	public function getAllFace(){
		$criteria = $this->getDbCriteria();
		$criteria->order='id asc,displayorder asc';
		return $this->findAll($criteria);
	}
	
	public function getFaceByIds(array $ids){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('id', $ids);
		$criteria->order='displayorder asc';
		return $this->findAll($criteria);
	}
	
	public function delFaceByIds(array $ids){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('id', $ids);
		$criteria->order='displayorder asc';
		return $this->deleteAll($criteria);
	}
}

?>