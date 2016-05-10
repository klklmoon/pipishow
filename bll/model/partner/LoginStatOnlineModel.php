<?php
/**
 * 主播在线时长记录
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: KefuModel.php 10145 2013-05-14 04:49:25Z suqian $ 
 * @package model
 * @subpackage partner
 */
class LoginStatOnlineModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return LoginStatOnlineModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{login_stat_online}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_partner;
	}
	
	/**
	 * 获取观看直播总时长
	 * @author hexin
	 * @param int $uid
	 * @return int
	 */
	public function getDurationByUid($uid){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->select = 'sum(time_online) as duration';
		$criteria->condition = ' uid='.$uid;
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryScalar();
	}
	
	
	
	
}