<?php
/**
 * 用户道具属性数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserPropsAttributeModel.php 8476 2013-04-07 13:56:31Z suqian $ 
 * @package model
 * @subpackage props 
 */
class UserPropsAttributeModel extends PipiActiveRecord {

	public function tableName(){
		return '{{user_props_attribute}}';
	}
	
	/**
	 * @param string $className
	 * @return UserPropsAttributeModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	
}

?>