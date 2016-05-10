<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author su peng <suqian@pipi.cn>
 * @version $Id: OperateModel.php 10145 2013-05-14 04:49:25Z supeng $ 
 * @package model
 * @subpackage common
 */
class IndexRightDataModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return IndexRightDataModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{index_rightdata}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	public function getIndexData($type){
		if ($type >= 0 || $type){
			$criteria = $this->getDbCriteria();
			$criteria->compare('type', $type);
			return $this->findAll($criteria);
		}
		return false;
	}
}