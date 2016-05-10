<?php

/**
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package 
 */
class HappyTuesdayActivityModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return HappyTuesdayActivity
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{long_tuesday_activity}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_activity;
	}
	
	public function alerayJoin($uid,$startTime,$endTime){
		$dbCriteria = $this->getDbCriteria();
		$dbCriteria->condition = ' uid = :uid and create_time  >= :startTime and create_time <= :endTime ';
		$dbCriteria->params = array(':uid'=>$uid,':startTime'=>$startTime,':endTime'=>$endTime);
		return $this->find($dbCriteria);
	}
}

?>