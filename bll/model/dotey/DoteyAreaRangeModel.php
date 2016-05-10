<?php

/**
 * 主播区域分类数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: DoteyAreaRangeModel.php 8510 2013-04-09 05:02:37Z suqian $ 
 * @package model
 * @subpackage dotey
 */
class DoteyAreaRangeModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return DoteyAreaRangeModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{dotey_area_range}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
}

