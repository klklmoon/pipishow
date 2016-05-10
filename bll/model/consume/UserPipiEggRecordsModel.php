<?php
/**
 * 用户皮蛋记录数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserPipiEggRecordsModel.php 15559 2013-09-24 00:54:15Z hexin $ 
 * @package model
 * @subpackage consume 
 */
class UserPipiEggRecordsModel extends PipiActiveRecord {

	public function tableName(){
		return '{{user_pipiegg_records}}';
	}
	
	/**
	 * @param string $className
	 * @return UserPipiEggRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}
	
	/**
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 * @return multitype:multitype: number Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > Ambigous <string, unknown, mixed>
	 */
	public function getPipieggsByCondition(Array $condition = array(),$offset = 0, $pageSize = 10,$isLimit = true){
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
			$criteria->addCondition('consume_time>='.strtotime($condition['create_time_on']));
		}
	
		if (!empty($condition['create_time_end'])){
			$criteria->addCondition('consume_time<'.strtotime($condition['create_time_end']));
		}
	
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if(!empty($condition['to_target_id'])){
			$criteria->compare('to_target_id', $condition['to_target_id']);
		}
	
		$result['count'] = $this->count($criteria);
		if ($isLimit){
			$criteria->limit = $pageSize;
			$criteria->offset = $offset;
			$pages=new CPagination($result['count']);
			$pages->pageSize = $pageSize;
			$pages->applyLimit($criteria);
			$result['pages'] = $pages;
		}
		$criteria->order = 'consume_time DESC';
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
	
	/**
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @param unknown_type $isLimit
	 * @return multitype:multitype: number Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > Ambigous <string, unknown, mixed>
	 */
	public function getPipieggsSumByCondition(Array $condition = array(),$offset = 0, $pageSize = 10,$isLimit = true){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
	
		$criteria = $this->getDbCriteria();
		$criteria->group = 'uid';
		$criteria->select = 'uid,sum(abs(pipiegg)) as sum_pipiegg';
		
		if (!isset($condition['isPlus']) && $condition['isPlus'] == true){
			$criteria->addCondition('pipiegg > 0');
		}else{
			$criteria->addCondition('pipiegg < 0');
		}
		
		if (!empty($condition['source'])){
			$criteria->compare('source', $condition['source']);
		}
	
		if (!empty($condition['sub_source'])){
			$criteria->compare('sub_source', $condition['sub_source']);
		}
	
		if (!empty($condition['client'])){
			$criteria->compare('client', $condition['client']);
		}
	
		if (!empty($condition['create_time_on'])){
			$criteria->addCondition('consume_time>='.$condition['create_time_on']);
		}
	
		if (!empty($condition['create_time_end'])){
			$criteria->addCondition('consume_time<'.$condition['create_time_end']);
		}
	
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
	
		$result['count'] = $this->count($criteria);
		if ($isLimit){
			$criteria->limit = $pageSize;
			$criteria->offset = $offset;
		}
		
		$result['list'] = $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		return $result;
	}
	
	public function getRechargePipieggRecord($uids, $subSource, $offset, $pageSize)
	{
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
	
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $uids);
		$criteria->compare('source', 'recharge');
		$criteria->compare('sub_source', $subSource);
		$result['count'] = $this->count($criteria);
		$criteria->limit = $pageSize;
		$criteria->offset = $offset;
		$criteria->order = 'consume_time DESC';
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
	
	
	/**
	 * 
	 * @param array $condition
	 * @return int
	 */
	public function sumPipieggs(array $condition = array()){
		$result = array();
		$criteria = $this->getDbCriteria();
		$criteria->select = 'uid,sum(abs(pipiegg)) as sum_pipiegg';
		
		if (!isset($condition['isPlus']) && $condition['isPlus'] == true){
			$criteria->addCondition('pipiegg > 0');
		}else{
			$criteria->addCondition('pipiegg < 0');
		}
		
		if (isset($condition['source'])){
			$criteria->compare('source', $condition['source']);
		}
	
		if (isset($condition['sub_source'])){
			$criteria->compare('sub_source', $condition['sub_source']);
		}
	
		if (isset($condition['client'])){
			$criteria->compare('client', $condition['client']);
		}
	
		if (isset($condition['startTime'])){
			$criteria->addCondition('consume_time>='.$condition['startTime']);
		}
	
		if (isset($condition['endTime'])){
			$criteria->addCondition('consume_time<'.$condition['endTime']);
		}
	
		if (isset($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}

		return  $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	/**
	 * 获取每日幸运星奖励记录
	 * @param array $condition
	 * @return array
	 */
	public function getLuckStarPipiRecord(array $condition = array()){
		$criteria = $this->getDbCriteria();
		if (isset($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		} 
		if (!empty($condition['record_sid'])){
			$criteria->compare('record_sid', $condition['record_sid']);
		}
		
		if (isset($condition['source'])){
			$criteria->compare('source', $condition['source']);
		}
		
		if (isset($condition['sub_source'])){
			$criteria->compare('sub_source', $condition['sub_source']);
		}
		
		return $this->findAll($criteria);
	}
}

?>