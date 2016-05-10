<?php


/**
 * 消息中心之关系数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: MessageRelationModel.php 14613 2013-09-03 06:09:37Z suqian $ 
 * @package model
 * @subpackage message
 */
class MessageRelationModel extends PipiActiveRecord {

	public function tableName(){
		return '{{message_relation}}';
	}
	
	/**
	 * @param unknown_type $className
	 * @return MessageRelationModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	
	
	public function getUserMessageByUidsCondition(array $condition ){
		$criteria = $this->getDbCriteria();
		$criteria->select = ' a.uid ,b.uid ruid,receive_uid,title,sub_title,content,is_read,extra,category,sub_category,b.create_time,relation_id,a.message_id ';
		$this->buildMessageCriteria($criteria,$condition);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
	}
	
	public function countUserMessageByUidsCondition(array $condition)
	{
		$criteria = $this->getDbCriteria();
		$criteria->select = ' count(*) as nums ';
		$this->buildMessageCriteria($criteria,$condition);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryScalar();
	}
	
	public function delRelationByMessageIds(array $messageIds){
		if(empty($messageIds)){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('message_id',$messageIds);
		return $this->deleteAll($criteria);
	}
	
	public function delRelationByIds(array $ids){
		if(empty($ids)){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('relation_id',$ids);
		return $this->deleteAll($criteria);
	}
	
	protected function buildMessageCriteria(CDbCriteria  $criteria,array $condition){
		$criteria->alias = 'a';
		$criteria->order = 'a.create_time DESC ';
		$criteria->join = ' force index(idx_uid_isown_ctime) JOIN  web_message_content b on a.message_id = b.message_id ';
		
		
		if(isset($condition['uid'])){
			$criteria->condition .= ' a.uid = :uid ';
			$criteria->params += array(':uid'=>$condition['uid']);
		}
		
		if(isset($condition['is_own'])){
			$criteria->condition .= ($criteria->condition  ? 'AND' : '') . ' a.is_own = :is_own ';
			$criteria->params += array(':is_own'=>$condition['is_own']);
		}
		
		if(isset($condition['category'])){
			$criteria->condition .= ($criteria->condition  ? 'AND' : '') . ' b.category = :category ';
			$criteria->params += array(':category'=>$condition['category']);
		}
		
		if(isset($condition['sub_category'])){
			if(is_array($condition['sub_category'])){
				$criteria->addInCondition('sub_category',$condition['sub_category']);
			}else{
				$criteria->condition .= ($criteria->condition  ? 'AND' : '') . ' b.sub_category = :sub_category ';
				$criteria->params += array(':sub_category'=>$condition['sub_category']);
			}
		}
		
		if(isset($condition['is_read'])){
			$criteria->condition .= ($criteria->condition  ? 'AND' : '') . ' a.is_read = :is_read ';
			$criteria->params += array(':is_read'=>$condition['is_read']);
		}
		
		if(isset($condition['limit'])){
			$criteria->limit = 	$condition['limit'];
		}
		
		if(isset($condition['offset'])){
			$criteria->offset = $condition['offset'];
		}
		
		if(!isset($condition['order'])){
			$criteria->order = ' a.create_time DESC ';
		}else{
			$criteria->order = $condition['order'];
		}
	}
}

