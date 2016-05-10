<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author supeng <supeng@pipi.cn>
 * @version $Id: BroadcastContentModel.php 9671 2013-05-06 13:51:21Z BroadcastContentModel $ 
 * @package model
 * @subpackage common
 */
class BroadcastContentModel extends PipiActiveRecord
{
	/**
	 * @param string $className
	 * @return BroadcastContentModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_broadcast_content}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	public function getBroadcastContentList(Array $condition = array(),$offset=0,$pageSize=20){
		$criteria = $this->getDbCriteria();
		if(!empty($condition['uid'])){
			$criteria->compare('uid',$condition['uid']);
		}
		
		if(!empty($condition['aid'])){
			$criteria->compare('aid',$condition['aid']);
		}
		
		if(!empty($condition['dotey_uid'])){
			$criteria->compare('dotey_uid',$condition['dotey_uid']);
		}
		
		if(!empty($condition['stime'])){
			$criteria->addCondition('ctime>='.strtotime($condition['stime']));
		}
		
		if(!empty($condition['etime'])){
			$criteria->addCondition('ctime<='.strtotime($condition['etime']));
		}
		
		$criteria->order = 'ctime DESC';
		$criteria->offset = $offset;
		$criteria->limit = $pageSize;
		
		$result['count'] = $this->count($criteria);
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
}

?>