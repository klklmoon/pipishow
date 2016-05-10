<?php
/**
 * 用户贡献值记录数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserDedicationRecordsModel.php 13839 2013-08-08 09:24:22Z guoshaobo $ 
 * @package model
 * @subpackage consume 
 */
class UserDedicationRecordsModel extends PipiActiveRecord {

	public function tableName(){
		return '{{user_dedication_records}}';
	}
	
	/**
	 * @param string $className
	 * @return UserDedicationRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}
	
	public function getUserDedicationRecords($uid, $offset = 0, $pageSize = 10, array $condition = array())
	{
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		$criteria = $this->getDbCriteria();
		$condition = array_merge(array('uid'=>$uid),$condition);
		if(isset($condition['source']) && is_array($condition['source'])){
			$criteria->addInCondition('source', $condition['source']);
			unset($condition['source']);
		}
		$criteria->addColumnCondition($condition);
		$result['count'] = $this->count($criteria, $condition);
		
		$criteria->order = 'create_time desc';
		$criteria->limit=$pageSize;
		$criteria->offset = $offset;
		
		$data = $this->findAll($criteria);
		if($data){
			foreach($data as $d){
				$result['list'][$d->attributes['record_id']] = $d->attributes;
			}
		}
		return  $result;
	}
	
	public function getUserDedicationToDoteyByArchivesIds($uid, $archivesIds)
	{
		$criteria = $this->getDbCriteria();
		$criteria->select = 'uid,to_target_id,sum(dedication) as dedi';
		$criteria->condition = ' client=:client and uid=:uid';
		$criteria->addInCondition('to_target_id', $archivesIds);
		$criteria->group = 'to_target_id';
		$criteria->params[':uid'] = $uid;
		$criteria->params[':client'] = 0;
		$criteria->order = ' dedi desc';
		$data = $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
		return $data;
	}
	
	/**
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 * @return multitype:multitype: number Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > Ambigous <string, unknown, mixed> 
	 */
	public function getDedicationByCondition(Array $condition = array(),$offset = 0, $pageSize = 10,$isLimit = true){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
	
		$criteria = $this->getDbCriteria();
	
		if (!empty($condition['source'])){
			$criteria->compare('source', $condition['source']);
		}
	
		if (!empty($condition['sub_source'])){
			$criteria->compare('sub_source', $condition['sub_source']);
		}
	
		if (!empty($condition['client'])){
			$criteria->compare('client', $condition['client']);
		}
	
		//获取明细记录
		if (!empty($condition['record_id'])){
			$criteria->compare('record_id', $condition['record_id']);
		}
	
		if (!empty($condition['create_time_on'])){
			$criteria->addCondition('create_time>='.strtotime($condition['create_time_on']));
		}
	
		if (!empty($condition['create_time_end'])){
			$criteria->addCondition('create_time<'.strtotime($condition['create_time_end']));
		}
	
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
	
		$result['count'] = $this->count($criteria);
		if ($isLimit){
			$criteria->limit = $pageSize;
			$criteria->offset = $offset;
		}
		$criteria->order = 'create_time DESC';
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
	
}

?>