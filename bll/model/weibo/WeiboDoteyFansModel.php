<?php
/**
 * 微博之微博粉丝(用户是主播时，冗余一份数据)数据访问层
 * 
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: WeiboDoteyFansModel.php 8728 2013-04-16 12:40:22Z suqian $ 
 * @package model
 * @subpackage weibo
 */
class WeiboDoteyFansModel extends PipiActiveRecord {

	public function tableName(){
		return '{{dotey_fans}}';
	}
	
	/**
	 * @param string $className
	 * @return WeiboDoteyFansModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	public function getDoteyFansByUid($uid, $offset = 0, $limit = 'all'){
		if($uid <= 0){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->condition = 'uid = :uid';
		$criteria->order = ' create_time DESC ';
		$criteria->offset = $offset;
		if($limit != 'all'){
			$criteria->limit = $limit;
		}
		$criteria->params = array(':uid'=>$uid);
		return $this->findAll($criteria);
	}
	
	public function getDoteyAttentionsByUid($uid){
		if($uid <= 0){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->condition = 'fans_uid = :uid';
		$criteria->order = ' create_time DESC ';
		$criteria->params = array(':uid'=>$uid);
		return $this->findAll($criteria);
	}
	
	public function getDoteyAttentionsByCondition(array $condition){
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
	
	public function getPointDoteyAttentionsByUid($uid, array $attentionsIds){
		if($uid <= 0 || empty($attentionsIds)){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->condition = 'fans_uid = :uid';
		$criteria->params = array(':uid'=>$uid);
		$criteria->addInCondition('uid',$attentionsIds);
		return $this->findAll($criteria);
	}
	
	public function countDoteyFans($doteyIds)
	{
		$criteria = $this->getDbCriteria();
		$criteria->select = 'count(*) as nums, uid';
		$criteria->addInCondition('uid', $doteyIds);
		$criteria->group = 'uid';
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
	}
}

