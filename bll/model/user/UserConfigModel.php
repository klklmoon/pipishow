<?php
/**
 * 用户中心之用户配置数据访问层
 * 
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserConfigModel.php 8369 2013-04-02 06:55:05Z suqian $ 
 * @package model
 * @subpackage user
 */
class UserConfigModel extends PipiActiveRecord {

	public function tableName(){
		return '{{user_config}}';
	}
	
	/**
	 * @param string $className
	 * @return UserConfigModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
    public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	/**
	 * 取得用户消息配置
	 * 
	 * @param array $uids
	 * @return array
	 */
	public function getUserConfigByUids(array $uids){
		if(empty($uids))
			return array();
			
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('uid',$uids);
		return $this->findAll($criteria);
	}
}

