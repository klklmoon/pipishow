<?php
class TaskModel extends PipiActiveRecord {
	/**
	 * @param TaskModel $className
	 * @return TaskModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{task}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
}