<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package model
 * @subpackage archives
 */
class ChatServerModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return ChatServerModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{chat_server}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_archives;
	}
	
	public function rules(){
		return array(
			array('archives_id,domain','required')
		);
	}
	
	public function attributeLabels(){
		return array(
			'archives_id'=>'档期ID',
			'policy_port'=>'协议端口号',
			'data_port'=>'数据端口号',
			'domain'=>'聊天地址'
		);
	}
	
	/**
	 * 根据档期Id获取聊天进程信息
	 * @param array  $archivesIds  档期Id
	 */
	public function getChatServerByArchivesIds($archivesIds){
		if(empty($archivesIds)) return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('archives_id',$archivesIds);
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据档期Id获取聊天进程信息
	 * @param array  $archivesId  档期Id
	 */
	public function getChatServerByArchivesId($archivesId){
		if(empty($archivesId)) return array();
		$criteria = $this->getDbCriteria();
		$criteria->addColumnCondition(array('archives_id'=>$archivesId));
		return $this->find($criteria);
	}
	
	/**
	 * 根据聊天进程id删除聊天进程
	 * @param array $chatIds
	 * @return boolean
	 */
	public function delChatServerByChatIds(array $chatIds){
		if(empty($chatIds)) return false;
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('chat_id', $chatIds);
		return $this->deleteAll($criteria);
	}
}

?>