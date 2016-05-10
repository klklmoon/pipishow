<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class WebConfigModel extends PipiActiveRecord {
	
	/**
	 * @param WebConfigModel $className
	 * @return WebConfigModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{website_config}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
}