<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author supeng <supeng@pipi.cn>
 * @version $Id: BroadcastDisableModel.php 9671 2013-05-06 13:51:21Z BroadcastContentModel $ 
 * @package model
 * @subpackage common
 */
class BroadcastDisableModel extends PipiActiveRecord
{
	/**
	 * @param string $className
	 * @return BroadcastContentModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_broadcast_disable}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	/**
	 * @param int $uid
	 * @return Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown >
	 */
	public function getBroadcastDisableByUid($uid){
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $uid);
		$result = $this->find($criteria);
		return $result?$result->attributes:array();
	}
	
	/**
	 * @param array $uids
	 * @return Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown >
	 */
	public function getBroadcastDisableByUids($uids){
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $uids);
		return $this->findAll($criteria);
	}
	
	public function getBroadcastDisableList(Array $condition = array(),$offset=0,$pageSize=20){
		$criteria = $this->getDbCriteria();
		
		if(!empty($condition['uid'])){
			$criteria->compare('uid',$condition['uid']);
		}
		
		if(!empty($condition['stime'])){
			$criteria->addCondition('utime>='.strtotime($condition['stime']));
		}
		
		if(!empty($condition['etime'])){
			$criteria->addCondition('utime<='.strtotime($condition['etime']));
		}
		$criteria->order = 'utime DESC';
		$criteria->offset = $offset;
		$criteria->limit = $pageSize;
		
		$result['count'] = $this->count($criteria);
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
}

?>