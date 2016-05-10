<?php
/**
 * 家族成员退出记录表
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su Qian <suqian@pipi.cn>
 * @version $Id: 2013-8-6 下午3:28:20 suqian $ 
 * @package
 */
class FamilyExitRecordsModel extends PipiActiveRecord {
	/**
	 * 
	 * @param string $className
	 * @return FamilyExitRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{family_exit_records}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_family;
	}

}
