<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-11-18 下午5:05:24 hexin $ 
 * @package
 */
class YearsModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return YearsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{short_2years_records}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_activity;
	}
}