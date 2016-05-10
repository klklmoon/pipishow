<?php

/**
 * 主播时段统计数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: DoteyPeriodCountModel.php 11162 2013-05-29 09:19:32Z suqian $ 
 * @package model
 * @subpackage dotey
 */
class DoteyPeriodCountModel extends PipiActiveRecord {
	/**
	 * @param string $className
	 * @return DoteyPeriodCountModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{dotey_period_count}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	/**
	 * 获取频道时段信息
	 * 
	 * @param array $uids
	 * @return array
	 */
	public function getDoteyPeriodCountByUids(array $uids,$field='*'){
		if(empty($uids)){
			return array();
		}	
		$criteria = $this->getDbCriteria();
		$criteria->select = $field ? 'uid,'.$field : '*';
		$criteria->addInCondition('uid', $uids);
		return $this->findAll($criteria);
	}
	
	
	
}

