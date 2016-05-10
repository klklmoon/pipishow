<?php
/**
 * 用户购买道具数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserPropsRecordsModel.php 14096 2013-08-21 07:38:20Z guoshaobo $ 
 * @package model
 * @subpackage props 
 */
class UserPropsRecordsModel extends PipiActiveRecord {

	public function tableName(){
		return '{{user_props_records}}';
	}
	
	/**
	 * @param string $className
	 * @return UserPropsRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}
	
	/**
	 * 搜索用户购买道具记录
	 * @param $uid
	 * @param $limit
	 * @param $offset
	 * @param $condition
	 * @return array
	 */
	public function searchUserPropsRecord($uid, $limit = 10, $offset = 0,$condition = array())
	{
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		$criteria = $this->getDbCriteria();
		$condition['uid']=$uid;

		if (!empty($condition['start_time'])){
			$criteria->addCondition('ctime>='.strtotime($condition['start_time']));
			unset($condition['start_time']);
		}
		
		if (!empty($condition['end_time'])){
			$criteria->addCondition('ctime<='.strtotime($condition['end_time']));
			unset($condition['end_time']);
		}
		if (isset($condition['source']) && $condition['source']>=0){
			$criteria->compare('source', $condition['source']);
			unset($condition['source']);
		}
		if($condition){
			$criteria->addColumnCondition($condition);
		}
		
		$result['count'] = $this->count($criteria);
		
		$criteria->limit=$limit;
		$criteria->offset = $offset;
		$criteria->order = 'ctime desc';
		
		if($data = $this->findAll($criteria)){
			foreach($data as $d){
				$result['list'][$d->attributes['record_id']] = $d->attributes;
			}
		}
		return  $result;
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @return Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown >
	 */
	public function getUserPropsRecordsByCondition($condition = array(),$offset=0, $pageSize=10){
		$result['count'] = 0;
		$result['list'] = array();
		$criteria = $this->getDbCriteria();
		
		if(!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if (!empty($condition['create_time_on'])){
			$criteria->addCondition('ctime>='.strtotime($condition['create_time_on']));
		}
		
		if (!empty($condition['create_time_end'])){
			$criteria->addCondition('ctime<='.strtotime($condition['create_time_end']));
		}
		
		if (!empty($condition['cat_id'])){
			$criteria->addCondition('cat_id='.$condition['cat_id']);
		}

		if (isset($condition['source'])){
			if($condition['source'] != null){
				$criteria->addCondition('source='.$condition['source']);
			}
		}
		
		if (!empty($condition['prop_id'])){
			$criteria->addCondition('prop_id='.$condition['prop_id']);
		}
		
		$result['count'] = $this->count($criteria);
		
		$criteria->limit=$pageSize;
		$criteria->offset = $offset;
		$criteria->order = 'ctime desc';
		
		$result['list'] = $this->findAll($criteria);
		return  $result;
	}
}

?>