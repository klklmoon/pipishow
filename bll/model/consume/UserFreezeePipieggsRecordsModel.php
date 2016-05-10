<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: UserFreezeePipieggsRecordsModel 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package model
 * @subpackage UserFreezeePipieggs
 */
class UserFreezeePipieggsRecordsModel extends PipiActiveRecord {
	/**
	 * @param string $className
	 * @return UserFreezeePipieggsRecords
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_freezeepipieggs_records}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}
}

?>