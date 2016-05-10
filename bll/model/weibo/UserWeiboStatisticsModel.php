<?php
/**
 * 微博之微博统计数据访问层
 * 
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserWeiboStatisticsModel.php 10068 2013-05-13 04:51:09Z leiwei $ 
 * @package model
 * @subpackage weibo
 */
class UserWeiboStatisticsModel extends PipiActiveRecord {

	public function tableName(){
		return '{{user_weibo_statistics}}';
	}
	
	/**
	 * @param string $className
	 * @return MessageConfigModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_weibo;
	}
	
	public function getWeiboStatisticsByUid($uid){
		$criteria = $this->getDbCriteria();
		$criteria->condition="uid=:uid";
		$criteria->params['uid']=$uid;
		return $this->find($criteria);
	}
	
}

