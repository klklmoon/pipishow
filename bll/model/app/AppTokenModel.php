<?php

/**
 * app小应用 与系统token交互数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: AppTokenModel.php 9671 2013-05-06 13:51:21Z suqian $ 
 * @package model
 * @subpackage app
 */
class AppTokenModel extends PipiActiveRecord {
	/**
	 * @param string $className
	 * @return AppTokenModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{app_token}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_purview;
	}
	

	
}

