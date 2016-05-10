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
class MessagePushReadModel extends PipiActiveRecord {

	public function tableName(){
		return '{{message_push_read}}';
	}
	
	/**
	 * @param unknown_type $className
	 * @return MessagePushReadModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	

}

