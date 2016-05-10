<?php
/**
 * 靓号model
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su Qian <suqian@pipi.cn>
 * @version $Id: NumberModel.php 13232 2013-07-22 08:28:29Z suqian $ 
 * @package model
 * @subpackage number
 */
class NumberModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return NumberModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{number}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function getNumberList($bit = null,$limit = NULL,$offSet = NULL){
		$command = $this->getDbCommand();
		$sql = 'SELECT * FROM web_number a WHERE NOT EXISTS (SELECT number FROM web_user_number b WHERE a.number = b.number AND b.status = 0 )';
		if(!is_null($bit)){
			$sql .= ' AND number_type = '.(int)$bit;
		}
		if(!is_null($limit)){
			$sql .= ' LIMIT '.$limit;
		}
		
		if(!is_null($offSet)){
				$sql .= ' OFFSET '.$offSet;
		}
		return $command->setText($sql)->queryAll();
	}
	
	public function countNumberList($bit = null){
		$command = $this->getDbCommand();
		$sql = 'SELECT count(*) FROM web_number a WHERE NOT EXISTS (SELECT number FROM web_user_number b WHERE a.number = b.number AND b.status = 0 )';
		if(!is_null($bit)){
			$sql .= ' AND number_type = '.(int)$bit;
		}
		return $command->setText($sql)->queryScalar();
	}
	
	public function searchNumberList(Array $condition = array(),$offset = 0, $limit = 20, $isLimit = true){
		$criteria = $this->getDbCriteria();
		
		if (isset($condition['type']) && is_numeric($condition['type'])){
			$criteria->compare('number_type', $condition['type']);
		}
		
		if (!empty($condition['number'])){
			$criteria->compare('number', $condition['number']);
		}
		
		if (!empty($condition['create_time_start'])){
			$criteria->addCondition('create_time>='.strtotime($condition['create_time_start']));
		}
		
		if (!empty($condition['create_time_end'])){
			$criteria->addCondition('create_time<='.strtotime($condition['create_time_end']));
		}
		
		$result['count'] = $this->count($criteria);
		if ($isLimit) {
			$criteria->offset = $offset;
			$criteria->limit = $limit;
		}
		$result['list'] = $this->findAll($criteria);
		return $result;
	}

}