<?php
/**
 * 家族成员卸下族徽记录表
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午3:28:20 hexin $ 
 * @package
 */
class FamilyUnloadRecordsModel extends PipiActiveRecord {
	/**
	 * 
	 * @param string $className
	 * @return FamilyUnloadRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{family_unload_records}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_family;
	}
	
}
