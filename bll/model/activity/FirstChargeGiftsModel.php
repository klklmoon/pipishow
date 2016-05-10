<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author su peng <suqian@pipi.cn>
 * @version $Id: FirstChargeGiftsModel.php 10145 2013-05-14 04:49:25Z supeng $ 
 * @package model
 */
class FirstChargeGiftsModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return FirstChargeGiftsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function primaryKey(){
		return 'rid';
	}
	
	public function tableName(){
		return '{{long_firstcharge_gifts}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_activity;
	}
	
	public function checkCollected($uid,$type){
		$criteria = $this->getDbCriteria();
		$criteria->compare('type', $type);
		$criteria->compare('uid', $uid);
		return $this->find($criteria);
	}
}