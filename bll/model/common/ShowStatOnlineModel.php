<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author Su Peng <suqian@pipi.cn>
 * @version $Id: ShowStatOnlineModel.php 10145 2013-05-14 04:49:25Z supeng $ 
 * @package
 */
class ShowStatOnlineModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return ShowStatOnlineModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{showstat_online}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	public function delShowStatByIds(array $ids){
		if(empty($ids)){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('id',$ids);
		return $this->deleteAll($criteria);
	}
	
	public function getAllShowStatForTime($time){
		$criteria = $this->getDbCriteria();
		$criteria->compare('time', $time);
		$criteria->order = 'create_time DESC';
		return $this->find($criteria);
	}
	
	/**
	 * 获取客服列表
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @return Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown >
	 */
	public function getShowStatList(Array $condition = array(),$offset=0,$pageSize=10,$isLimit=true){
		$criteria = $this->getDbCriteria();
		$this->getCondition($criteria,$condition);
		
		if(!empty($condition['_select'])){
			$criteria->select = $condition['_select'];
		}
		
		if(!empty($condition['_order'])){
			$criteria->order = $condition['_order'];
		}
		
		if($isLimit){
			$criteria->offset = $offset;
			$criteria->limit = $pageSize;
		}
		return $this->findAll($criteria);
	}
	
	public function getShowStatListCount(Array $condition = array()){
		$criteria = $this->getDbCriteria();
		$this->getCondition($criteria,$condition);
		return $this->count($criteria);
	}
	
	public function getCondition(CDbCriteria &$criteria,$condition = array()){
		if(!empty($condition['start_date'])){
			$criteria->addCondition('time>='.$condition['start_date']);
		}
		
		if(!empty($condition['end_date'])){
			$criteria->addCondition('time<='.$condition['end_date']);
		}
		
		if(!empty($condition['time'])){
			$criteria->addCondition('time='.$condition['time']);
		}
		
		if(!empty($condition['like_time'])){
			$criteria->addSearchCondition('time', $condition['like_time']);
		}
	}
}