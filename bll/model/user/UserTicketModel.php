<?php
/**
 * 用户绑定服务层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: UserBasicModel.php 8366 2013-04-01 14:56:32Z suqian $ 
 * @package model
 * @subpackage user
 */
class UserTicketModel extends PipiActiveRecord {
	/**
	 * 
	 * @param unknown_type $className
	 * @return UserTicketModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_ticket}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	
}

?>