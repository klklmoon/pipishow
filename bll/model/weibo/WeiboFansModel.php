<?php
/**
 * 微博之微博粉丝数据访问层
 * 
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: WeiboFansModel.php 17313 2014-01-08 08:13:25Z leiwei $ 
 * @package model
 * @subpackage weibo
 */
class WeiboFansModel extends PipiActiveRecord {

	public function tableName(){
		return '{{user_fans}}';
	}
	
	/**
	 * @param string $className
	 * @return WeiboFansModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	public function getUserFansByUid($uid){
		if($uid <= 0){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->condition = 'uid = :uid';
		$criteria->order = ' create_time DESC ';
		$criteria->params = array(':uid'=>$uid);
		return $this->findAll($criteria);
	}
	
	public function getUserAttentionsByUid($uid){
		if($uid <= 0){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->condition = 'fans_uid = :uid';
		$criteria->order = ' create_time DESC ';
		$criteria->params = array(':uid'=>$uid);
		return $this->findAll($criteria);
	}
	
	public function getUserFansByCondition(array $condition){
		$criteria = $this->getDbCriteria();
		if(isset($condition['uid'])){
			$criteria->condition = 'uid = :uid';
			$criteria->params[':uid'] =  $condition['uid'];
		}
		if(isset($condition['limit'])){
			$criteria->limit = $condition['limit'];
		}
		if(isset($condition['offset'])){
			$criteria->offset = $condition['offset'];
		}
		$criteria->order = ' create_time DESC ';
		$count = 0;
		if(isset($condition['isCount'])){
			$count = $this->count($criteria);
		}
		return array($this->findAll($criteria),$count);
	}
	
	public function getUserAttentionsByCondition(array $condition){
		$criteria = $this->getDbCriteria();
		if(isset($condition['fans_uid'])){
			$criteria->condition = 'fans_uid = :fans_uid';
			$criteria->params[':fans_uid'] =  $condition['fans_uid'];
		}
		if(isset($condition['limit'])){
			$criteria->limit = $condition['limit'];
		}
		if(isset($condition['offset'])){
			$criteria->offset = $condition['offset'];
		}
		$criteria->order = ' create_time DESC ';
		$count = 0;
		if(isset($condition['isCount'])){
			$count = $this->count($criteria);
		}
		return array($this->findAll($criteria),$count);
	}
	
}

