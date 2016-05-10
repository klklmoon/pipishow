<?php
/**
 * 家族荣誉内容
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午3:28:20 hexin $ 
 * @package
 */
class FamilyHonorModel extends PipiActiveRecord {
	/**
	 * 
	 * @param string $className
	 * @return FamilyHonorModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{family_honor}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_family;
	}
	
	/**
	 * 获取家族荣誉内容
	 * @param int $family_id
	 * @param int $limit
	 * @return array
	 */
	public function getHonor($family_id, $limit = 1, $last_id = 0){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->condition = 'family_id='.$family_id.($last_id != 0 ? ' and id < '.$last_id : '');
		$criteria->order = 'id desc';
		$criteria->limit = $limit;
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
}
