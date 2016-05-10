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
class UserBindModel extends PipiActiveRecord {
	/**
	 * 
	 * @param unknown_type $className
	 * @return UserBindModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_bind}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	public function searchUserBind(Array $condition=array(),$offset=0,$pageSize=20,$isLimit=true){
		$result = array();
		$criteria = $this->getDbCriteria();
		$criteria->order = 'create_time DESC';
		
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if (!empty($condition['uids'])){
			$criteria->compare('uid', $condition['uids']);
		}
		
		if (!empty($condition['method'])){
			$criteria->compare('method', $condition['method']);
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

?>