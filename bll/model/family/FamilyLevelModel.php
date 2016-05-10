<?php
/**
 * 家族等级
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午3:28:20 hexin $ 
 * @package
 */
class FamilyLevelModel extends PipiActiveRecord {
	/**
	 * 
	 * @param string $className
	 * @return FamilyLevelModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{family_level}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_family;
	}
	
}
