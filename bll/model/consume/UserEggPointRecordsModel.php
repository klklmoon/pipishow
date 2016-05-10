<?php
/**
 * 用户皮点变化数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserEggPointRecordsModel.php 8510 2013-04-09 05:02:37Z suqian $ 
 * @package model
 * @subpackage consume 
 */
class UserEggPointRecordsModel extends PipiActiveRecord {

	public function tableName(){
		return '{{user_eggpoints_records}}';
	}
	
	/**
	 * @param string $className
	 * @return UserEggPointsRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}
	
	
}

?>