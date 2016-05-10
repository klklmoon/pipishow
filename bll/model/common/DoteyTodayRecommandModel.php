<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author su qian <suqian@pipi.cn>
 * @version $Id: OperateModel.php 9671 2013-05-06 13:51:21Z suqian $ 
 * @package model
 * @subpackage common
 */
class DoteyTodayRecommandModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return DoteyTodayRecommandModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{dotey_today_recommand}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	public function getAllTodayRecommand(){
		$criteria = $this->getDbCriteria();
		$criteria->order = 'type DESC,charms DESC';
		return $this->findAll($criteria);
	}
	

}