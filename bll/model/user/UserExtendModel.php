<?php
/**
 * 用户扩展信息存储
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: UserBasicModel.php 8366 2013-04-01 14:56:32Z suqian $ 
 * @package model
 * @subpackage user
 */
class UserExtendModel extends PipiActiveRecord {
	
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_extend}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	/**
	 * 取得用户扩展信息列表
	 * @param mixed $uids
	 * @return UserBasicModel
	 */
	public function getUserExtendByUids(array $uids){
		if(empty($uids))
			return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('uid',$uids);
		return $this->findAll($criteria);
	}
}

?>