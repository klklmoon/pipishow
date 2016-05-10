<?php

/**
 * app小应用注册信息数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: AppModel.php 9671 2013-05-06 13:51:21Z suqian $ 
 * @package model
 * @subpackage app
 */
class AppModel extends PipiActiveRecord {
	/**
	 * @param string $className
	 * @return AppModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{app}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_purview;
	}
	
	public function rules(){
		return array(
			array('app_name,app_enname,app_secret','required'),
			array('app_name,app_enname,app_secret','unique'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'app_name'=>'应用中文名称',
			'app_enname'=>'应用英文名称',
			'app_secret'=>'应用密钥'
		);
	}
	
}

