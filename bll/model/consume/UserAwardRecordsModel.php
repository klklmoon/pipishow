<?php
/**
 * 用户奖励记录数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author leiwei <leiwei@pipi.cn>
 * @version $Id: UserAwardRecordsModel.php 11980 2013-06-13 07:27:28Z leiwei $ 
 * @package model
 * @subpackage consume 
 */
class UserAwardRecordsModel extends PipiActiveRecord {

	public function tableName(){
		return '{{user_award_records}}';
	}
	
	/**
	 * @param string $className
	 * @return UserAwardRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}
	
	public function getUserAwardRecords(Array $condition = array()){
		$criteria = $this->getDbCriteria();
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		if (!empty($condition['type'])){
			$criteria->compare('type', $condition['type']);
		}
		if(!empty($condition['num'])){
			$criteria->compare('num', $condition['num']);
		}
		if (!empty($condition['target_id'])){
			$criteria->compare('target_id', $condition['target_id']);
		}
		if (isset($condition['pipiegg']) && $condition['pipiegg']>=0){
			$criteria->compare('pipiegg', $condition['pipiegg']);
		}
		if (isset($condition['stime']) && isset($condition['etime'])){
			$criteria->addBetweenCondition('create_time', $condition['stime'],$condition['etime']);
		}
		$criteria->order = 'create_time asc';
		return $this->find($criteria);
	}
	
	public function searchUserAwardRecords(Array $condition = array(),$offset=0,$pageSize=20,$isLimit=true){
		$data = array();
		$criteria = $this->getDbCriteria();
		
		if(!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if(!empty($condition['record_sid'])){
			$criteria->compare('record_sid', $condition['record_sid']);
		}
		
		if(!empty($condition['target_id'])){
			$criteria->compare('target_id', $condition['target_id']);
		}
		
		if(!empty($condition['to_target_id'])){
			$criteria->compare('to_target_id', $condition['to_target_id']);
		}
		
		if(!empty($condition['type'])){
			$criteria->compare('type', $condition['type']);
		}
		
		if(!empty($condition['source'])){
			$criteria->compare('source', $condition['source']);
		}
		
		if(!empty($condition['sub_source'])){
			$criteria->compare('sub_source', $condition['sub_source']);
		}
		
		if(!empty($condition['stime'])){
			$criteria->addCondition('create_time>='.strtotime($condition['stime']));
		}
		
		if(!empty($condition['etime'])){
			$criteria->addCondition('create_time<='.strtotime($condition['etime']));
		}
		
		if($isLimit){
			$criteria->offset = $offset;
			$criteria->limit = $pageSize;
		}
		$criteria->order = 'record_id DESC';
		$data['count'] = $this->count($criteria);
		$data['list'] = $this->findAll($criteria);
		return $data;
	}
}

?>