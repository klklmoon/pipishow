<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2014-01-07 下午5:05:24 hexin $ 
 * @package
 */
class MobileAdvModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return MobileAdvModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{long_mobile_adv}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_activity;
	}
}