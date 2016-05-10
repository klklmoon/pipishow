<?php

/**
 * 消息中心之内容数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: MessageContentModel.php 14639 2013-09-03 08:33:10Z suqian $ 
 * @package model
 * @subpackage message
 */
class MessageContentModel extends PipiActiveRecord {
	
	public function tableName(){
		return '{{message_content}}';
	}
	
	/**
	 * @param unknown_type $className
	 * @return MessageContentModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	public function rules(){
		return array(
			array('title,content,sub_title,extra','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('content','required'),
		);
	}
	
	public function delMessageByIds(array $ids){
		if(empty($ids)){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('message_id',$ids);
		return $this->deleteAll($criteria);
	}

	public function searchMessage(Array $condition = array(),$offset = 0,$pageSize=20,$isLimit=true){
		$result = array();
		$criteria = $this->getDbCriteria();
		$criteria->order = 'message_id DESC';
		
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if (!empty($condition['create_time_on'])){
			$criteria->addCondition('create_time>='.strtotime($condition['create_time_on']));
		}
		
		if (!empty($condition['create_time_end'])){
			$criteria->addCondition('create_time<='.strtotime($condition['create_time_end']));
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
	
	public function getUserReceiveSiteMessagesByUid($limit,$offset){
		$criteria = $this->getDbCriteria();
		$criteria->condition = 'is_site = 1 ';
		$criteria->order = ' create_time DESC ';
		$criteria->offset = $offset;
		$criteria->limit = $limit;
		return $this->findAll($criteria);
	}
	public function countUserReceiveSiteMessagesByUid(){
		$criteria = $this->getDbCriteria();
		$criteria->condition = 'is_site = 1';
		return $this->count($criteria);
	}
	
	public function getUserUnReadSiteMessagesByUid($uid,array $messageIds = array()){
		if($uid <= 0){
			return array();
		}
		$command = $this->getDbCommand();
		$sql = "SELECT message_id,title FROM web_message_content a WHERE  a.is_site = 1 AND NOT EXISTS (SELECT * FROM web_message_push_read b WHERE a.message_id = b.message_id AND b.uid = {$uid})";
		if($messageIds){
			$sql .= ' AND  message_id IN ('.implode(',',$messageIds).')';
		}
		return $command->setText($sql)->queryAll();
	}
	
	public function countUserUnReadSiteMessagesByUid($uid){
		if($uid <= 0){
			return 0;
		}
		$command = $this->getDbCommand();
		$sql = "SELECT count(*) FROM web_message_content a WHERE  a.is_site = 1 AND NOT EXISTS (SELECT * FROM web_message_push_read b WHERE a.message_id = b.message_id AND b.uid = {$uid})";
		return $command->setText($sql)->queryScalar();
	}
	
	public function countDelSiteMessagesByUid($uid){
		if($uid <= 0){
			return 0;
		}
		$command = $this->getDbCommand();
		$sql = "SELECT count(*) FROM web_message_content a WHERE  a.is_site = 1 AND  EXISTS (SELECT * FROM web_message_push_read b WHERE a.message_id = b.message_id AND b.is_del=1 AND b.uid = {$uid})";
		return $command->setText($sql)->queryScalar();
	}
	
	public function getUserDelSiteMessagesByUid($uid,array $messageIds = array()){
		if($uid <= 0){
			return array();
		}
		$command = $this->getDbCommand();
		$sql = "SELECT message_id,title FROM web_message_content a WHERE  a.is_site = 1 AND  EXISTS (SELECT * FROM web_message_push_read b WHERE a.message_id = b.message_id AND b.is_del AND b.uid = {$uid})";
		if($messageIds){
			$sql .= ' AND  message_id IN ('.implode(',',$messageIds).')';
		}
		return $command->setText($sql)->queryAll();
	}
	
	public function getUserUnReadSiteMessageNumsByUid($uid,$limit = 20){
		if($uid <= 0){
			return array();
		}
		$command = $this->getDbCommand();
		$sql = "SELECT title,sub_title,content,extra,message_id,category,sub_category,create_time FROM web_message_content a WHERE  a.is_site = 1 AND NOT EXISTS (SELECT * FROM web_message_push_read b WHERE a.message_id = b.message_id   AND  b.uid = {$uid}) ORDER BY a.create_time DESC LIMIT {$limit}";
		return $command->setText($sql)->queryAll();
	}
}

