<?php
/**
 * 用户靓号model
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su Qian <suqian@pipi.cn>
 * @version $Id: UserUserNumberModel.php 13232 2013-07-22 08:28:29Z suqian $ 
 * @package model
 * @subpackage number
 */
class UserNumberModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return UserNumberModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_number}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function getUseNumber(array $number){
		$dbCriteria = $this->getDbCriteria();
		$dbCriteria->condition = ' status = 0';
		$dbCriteria->addInCondition('number',$number);
		return $this->findAll($dbCriteria);
		
	}
	
	public function getUserNumber($uid){
		$dbCriteria = $this->getDbCriteria();
		$dbCriteria->condition = 'uid = :uid';
		$dbCriteria->params = array(':uid'=>$uid);
		$dbCriteria->order = 'create_time DESC';
		return $this->findAll($dbCriteria);
	}

	public function searchUserNumberList(Array $condition = array(),$offset = 0, $limit = 20, $isLimit = true){
		$criteria = $this->getDbCriteria();
	
		if (!empty($condition['uids'])){
			$criteria->compare('uid', $condition['uids']);
		}
		
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if (isset($condition['type']) && is_numeric($condition['type'])){
			$criteria->compare('reward_type', $condition['type']);
		}
		
		if (isset($condition['status']) && is_numeric($condition['status'])){
			$criteria->compare('status', $condition['status']);
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