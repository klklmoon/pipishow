<?php

/**
 * 用户最近观看的的主播档期数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserArchivesViewModel.php 8875 2013-04-19 09:56:44Z suqian $ 
 * @package model
 * @subpackage dotey
 */
class UserArchivesViewModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return UserArchivesViewModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_archives_view}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_archives;
	}
	
	/**
	 * 取得用户最近观看的直播的档期
	 * 
	 * @param int $uid 用户ID
	 * @return array
	 * 
	 **/
	public function getUserLatestSeeArchives($uid){
		if($uid <= 0){
			return array();
		}
		
		$criteria = $this->getDbCriteria();
		$criteria->condition = 'uid = :uid';
		$criteria->params = array(':uid'=>$uid);
		$criteria->order='view_time DESC';
		return $this->findAll($criteria);
	}
	
}

