<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: UserBagRecordsModel.php 11722 2013-06-06 08:06:40Z supeng $ 
 * @package model
 * @subpackage gift
 */
class UserBagRecordsModel extends PipiActiveRecord {
	
	/**
	 * @param unknown_type $className
	 * @return UserBagRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_bag_records}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}
	
	public function rules(){
		return array(
			array('uid,gift_id,num','numerical'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'uid'=>'用户uid',
			'gift_id' =>'礼物Id',
			'num' =>'数量',
		);
	}
	
	public function getUserBagRecords($uid,$offset=0,$pageSize=10,array $condition=array()){
		$criteria = $this->getDbCriteria();
		$condition['uid']=$uid;
		$criteria->addColumnCondition($condition);
		$criteria->order = 'record_id desc';
		$criteria->limit=$pageSize;
		$criteria->offset = $offset;
		return $this->findAll($criteria);
	}
	
	/**
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @return multitype:multitype: number Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown > Ambigous <string, unknown, mixed> 
	 */
	public function getUserBagRecordsByCondition(array $condition=array(),$offset=0,$pageSize=10){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if (!empty($condition['gift_id'])){
			$criteria->compare('gift_id', $condition['gift_id']);
		}
		
		if (isset($condition['source']) && $condition['source']>=0){
			$criteria->compare('source', $condition['source']);
		}
		
		if (!empty($condition['create_time_on'])){
			$criteria->addCondition('create_time>='.strtotime($condition['create_time_on']));
		}
		
		if (!empty($condition['create_time_end'])){
			$criteria->addCondition('create_time<'.strtotime($condition['create_time_end']));
		}
		
		$result['count'] = $this->count($criteria);
		$criteria->order = 'create_time DESC';
		$criteria->limit=$pageSize;
		$criteria->offset = $offset;
		$result['list'] = $this->findAll($criteria);
		
		return $result;
	}
	
	/**
	 * @author supeng
	 * @param array $giftIds
	 * @param unknown_type $startTime
	 * @param unknown_type $endTime
	 */
	public function getSumGiftBagRecords(Array $giftIds,$startTime=null,$endTime=null){
		$criteria = $this->getDbCriteria();
		$criteria->alias = 'g';
		$criteria->select = "sum(g.num) as num,g.gift_id";
		$criteria->group = 'g.gift_id';
		
		$criteria->compare('g.gift_id', $giftIds);
		if($startTime)$criteria->addCondition('g.create_time>='.$startTime);
		if($endTime)$criteria->addCondition('g.create_time<'.$endTime);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
}

?>