<?php
/**
 * 注册推广日志记录
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: KefuModel.php 10145 2013-05-14 04:49:25Z suqian $ 
 * @package model
 * @subpackage partner
 */
class RegLogModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return RegLogModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{reg_log}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_partner;
	}
	
	
	
	
	
	
}