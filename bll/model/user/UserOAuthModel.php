<?php
/**
 * 开放平台用户注册信息存储
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: UserBasicModel.php 8366 2013-04-01 14:56:32Z suqian $ 
 * @package model
 * @subpackage user
 */
class UserOAuthModel extends PipiActiveRecord {
	/**
	 * 
	 * @param unknown_type $className
	 * @return UserOAuthModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_oauth}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	/**
	 * 取得用户开放平台注册信息列表
	 * @param mixed $uids
	 * @return array
	 */
	public function getUserOAuthyUids(array $uids){
		if(empty($uids))
			return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('uid',$uids);
		return $this->findAll($criteria);
	}
	/**
	 * 获取用户开放平台注册信息
	 * 
	 * @param string $flatform 开放平台标识
	 * @param string $open_id 开放平台用户标识
	 * @return array
	 */
	public function getUserOauthByOpenFlatform($flatform,$open_id){
		$criteria = $this->getDbCriteria();
		$criteria->condition = ' open_platform = :platform AND openid = :open_id';
		$criteria->params = array(':platform'=>$flatform,':open_id'=>$open_id);
		return $this->find($criteria);
	}
}

?>