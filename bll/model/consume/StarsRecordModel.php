<?php
/**
 * 用户星级说明数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: StarsRecordModel.php 13919 2013-08-14 05:41:49Z suqian $ 
 * @package model
 * @subpackage consume
 */
class StarsRecordModel extends PipiActiveRecord {
	public function tableName(){
		return '{{stars_record}}';
	}
	
	/**
	 * @param string $className
	 * @return StarsRecordModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}
	
	public  function getNewUserStarRecords($uid){
		$criteria = $this->getDbCriteria();
		$criteria->condition = ' uid = :uid ';
		$criteria->order = 'start_time DESC';
		$criteria->limit = 1;
		$criteria->params[':uid'] = $uid;
		return $this->find($criteria);
	}
	
	public function getNewStarRecords(){
		$criteria = $this->getDbCriteria();
		$criteria->order = 'record_id DESC ';
		$criteria->limit = 1;
		return  $this->find($criteria);
	}
	
}

?>