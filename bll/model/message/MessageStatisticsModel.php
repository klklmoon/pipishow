<?php
/**
 * 消息中心之消息统计数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: MessageRelationModel.php 14036 2013-08-21 01:37:01Z guoshaobo $ 
 * @package model
 * @subpackage message
 */
class MessageStatisticsModel extends PipiActiveRecord {

	public function tableName(){
		return '{{message_statistics}}';
	}
	
	/**
	 * @param unknown_type $className
	 * @return MessageStatisticsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	

}

