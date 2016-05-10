<?php
/**
 * 靓号购买记录model
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su Qian <suqian@pipi.cn>
 * @version $Id: UserNumberRecordsModel.php 13232 2013-07-22 08:28:29Z suqian $ 
 * @package model
 * @subpackage number
 */
class UserNumberRecordsModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return UserNumberRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_number_buyrecords}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}
	
	public function searchBuyNumberRecordsList(Array $condition = array(),$offset = 0, $limit = 20, $isLimit = true){
		$criteria = $this->getDbCriteria();
	
		if (!empty($condition['uids'])){
			$criteria->compare('uid', $condition['uids']);
		}
	
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if (!empty($condition['proxy_uid'])){
			$criteria->compare('proxy_uid', $condition['proxy_uid']);
		}
		
		if (!empty($condition['sender_uid'])){
			$criteria->compare('sender_uid', $condition['sender_uid']);
		}
	
		if (isset($condition['type']) && is_numeric($condition['type'])){
			$criteria->compare('source', $condition['type']);
		}
	
		if (!empty($condition['number'])){
			$criteria->compare('number', $condition['number']);
		}
	
		if (!empty($condition['create_time_start'])){
			$criteria->addCondition('create_time>='.strtotime($condition['create_time_start']));
		}
	
		if (!empty($condition['create_time_end'])){
			$criteria->addCondition('create_time<='.strtotime($condition['create_time_end']));
		}
	
		$result['count'] = $this->count($criteria);
		if ($isLimit && !isset($condition['uids'])) {
			$criteria->offset = $offset;
			$criteria->limit = $limit;
		}
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
	

}