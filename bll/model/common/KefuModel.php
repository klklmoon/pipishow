<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: KefuModel.php 10145 2013-05-14 04:49:25Z suqian $ 
 * @package
 */
class KefuModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return KefuModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{kefu}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	public function delKefuByIds(array $ids){
		if(empty($ids)){
			return array();
		}
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('id',$ids);
		return $this->deleteAll($criteria);
	}
	
	public function getAllKefu(){
		$criteria = $this->getDbCriteria();
		$criteria->order = 'create_time DESC';
		return $this->findAll($criteria);
	}
	
	/**
	 * 获取客服列表
	 * @param array $condition
	 * @param unknown_type $offset
	 * @param unknown_type $pageSize
	 * @return Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown >
	 */
	public function getKefuList(Array $condition = array(),$offset=0,$pageSize=10){
		$result = array();
		$result['count'] = 0;
		$result['list'] = array();
		
		$criteria = $this->getDbCriteria();
		
		if (isset($condition['kefu_type'])){
			$criteria->compare('kefu_type', $condition['kefu_type']);
		}
		if (isset($condition['contact_type'])){
			$criteria->compare('contact_type', $condition['contact_type']);
		}
		$result['count'] = $this->count($criteria);
		$criteria->order = ' create_time DESC';
		$criteria->offset = $offset;
		$criteria->limit = $pageSize;
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
	
	
}