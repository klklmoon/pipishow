<?php
/**
 * 家族消费记录表
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午3:28:20 hexin $ 
 * @package
 */
class FamilyConsumeRecordsModel extends PipiActiveRecord {
	/**
	 * 
	 * @param string $className
	 * @return FamilyConsumeRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{family_consume_records}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_family;
	}
	
	/**
	 * 查询家族最后一次统计消费记录的时间
	 * @param int $family_id
	 * @return number
	 */
	public function getLastRecordTime($family_id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->select = 'create_time';
		$criteria->condition = 'family_id='.$family_id;
		$criteria->order = 'id desc';
		$criteria->limit = 1;
		return intval($this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryScalar());
	}
	
	/**
	 * 查询某家族的总充值积累
	 * @param int $family_id
	 * @return number
	 */
	public function getSum($family_id){
		$criteria = $this->getCommandBuilder()->createCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'sum(a.recharge + b.recharge)';
		$criteria->join = 'LEFT JOIN web_family_unload_records AS b ON a.family_id = b.family_id';
		$criteria->condition = 'a.family_id='.$family_id;
		return intval($this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryScalar());
	}
}
