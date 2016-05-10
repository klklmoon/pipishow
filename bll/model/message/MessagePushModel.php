<?php
/**
 * 消息中心之消息推送数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: MessageRelationModel.php 14036 2013-08-21 01:37:01Z guoshaobo $ 
 * @package model
 * @subpackage message
 */
class MessagePushModel extends PipiActiveRecord {

	public function tableName(){
		return '{{message_push}}';
	}
	
	/**
	 * @param unknown_type $className
	 * @return MessagePushModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	public function delPushByIds(array $pushIds){
		if(empty($pushIds)){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('push_id',$pushIds);
		return $this->deleteAll($criteria);
	}
	
	public function searchPush(Array $condition = array(),$offset = 0,$pageSize=20,$isLimit=true){
		$result = array();
		$criteria = $this->getDbCriteria();
		$criteria->order = 'push_id DESC';
	
		if (!empty($condition['target_id'])){
			$criteria->compare('target_id', $condition['target_id']);
		}
	
		if (isset($condition['type']) && is_numeric($condition['type'])){
			$criteria->compare('type', $condition['type']);
		}
		
		if (isset($condition['rank']) && is_numeric($condition['rank'])){
			$criteria->compare('rank', $condition['rank']);
		}
		
		if (isset($condition['is_send']) && is_numeric($condition['is_send'])){
			$criteria->compare('is_send', $condition['is_send']);
		}
	
		if (!empty($condition['create_time_on'])){
			$criteria->addCondition('create_time>='.strtotime($condition['create_time_on']));
		}
	
		if (!empty($condition['create_time_end'])){
			$criteria->addCondition('create_time<='.strtotime($condition['create_time_end']));
		}
		
		if (!empty($condition['send_time_on'])){
			$criteria->addCondition('send_time>='.strtotime($condition['send_time_on']));
		}
		
		if (!empty($condition['send_time_end'])){
			$criteria->addCondition('send_time<='.strtotime($condition['send_time_end']));
		}
	
		if (!empty($condition['receive_uid'])){
			$criteria->compare('receive_uid', $condition['receive_uid']);
		}
	
		$result['count'] = $this->count($criteria);
		if ($isLimit){
			$criteria->offset = $offset;
			$criteria->limit = $pageSize;
		}
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
}

