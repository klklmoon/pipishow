<?php
/**
 * 勋章管理
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author guoshaobo <guoshaobo@pipi.cn>
 * @version $Id: UserCheckinModel.php 9657 2013-05-06 12:59:31Z guoshaobo $
 * @package model
 * @subpackage consume
 */
class MedalListModel extends PipiActiveRecord{
	/**
	 * 
	 * @param unknown_type $className
	 * @return MedalListModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{medal_list}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function getMedalList($condition = array()){
		$criteria = $this->getDbCriteria();
		if (!empty($condition['mid'])){
			$criteria->compare('mid', intval($condition['mid']));	
		}
		return $this->findAll($criteria);
	}
	
}
?>