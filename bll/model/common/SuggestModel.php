<?php
class SuggestModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return SuggestModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_suggest}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	public function delKefuByIds(array $suggest_id){
		if(empty($suggest_id)){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('suggest_id',$suggest_id);
		return $this->deleteAll($criteria);
	}
	
		
	/**
	 * 获取反馈列表
	 * 
	 * @author supeng
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @return Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown >
	 */
	public function getSuggestList(Array $condition = array(),$offset=0,$pageSize=10){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		
		if (!empty($condition['suggestId'])){
			$criteria->compare('suggest_id', $condition['suggestId']);
		}
		
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		if (isset($condition['type']) && $condition['type']>=0){
			$criteria->compare('type', $condition['type']);
		}
		
		if (isset($condition['is_handle']) && $condition['is_handle']>=0){
			$criteria->compare('is_handle', $condition['is_handle']);
		}
		
		if (!empty($condition['create_time_on'])){
			$criteria->addCondition('create_time>='.strtotime($condition['create_time_on']));
		}
		
		if (!empty($condition['create_time_end'])){
			$criteria->addCondition('create_time<'.strtotime($condition['create_time_end']));
		}
		
		$result['count'] = $this->count($criteria);
		$criteria->order = ' create_time DESC';
		$criteria->offset = $offset;
		$criteria->limit = $pageSize;
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
}