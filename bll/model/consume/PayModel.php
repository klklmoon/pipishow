<?php
/**
 * 兑换记录表
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author 郭少波 <guoshaobo@pipi.cn>
 * @version $Id: PayModel.php 8510 2013-05-08 20:06:37Z guoshaobo $ 
 * @package model
 * @subpackage consume 
 */
class PayModel extends PipiActiveRecord {

	public function tableName(){
		return '{{pay}}';
	}
	
	/**
	 * @param string $className
	 * @return DoteyCharmRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	/**
	 * @author supeng
	 * @param array $condition
	 */
	public function getDoteyPayConfig(Array $condition){
		$criteria = $this->getDbCriteria();
		$criteria->order = 'uid ASC,charm_points DESC';
		
		$criteria->compare('is_del', 0);
		
		if (!empty($condition['pay_type'])){
			$criteria->compare('pay_type', $condition['pay_type']);
		}
		
		if (!empty($condition['uid'])){
			$criteria->compare('uid', $condition['uid']);
		}
		
		return $this->findAll($criteria);
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $uid
	 * @param unknown_type $payType
	 * @param unknown_type $hours
	 * @param unknown_type $days
	 * @param unknown_type $charmPoints
	 * @return Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown >
	 */
	public function getAllowDoteyPay($uid,$payType,$hours,$days,$charmPoints){
		$criteria = $this->getDbCriteria();
		$criteria->compare('is_del', 0);
		$criteria->order = 'uid ,charm_points DESC';
		$criteria->limit = '1';
		
		$criteria->compare('uid',array($uid,'0'));
		$criteria->addCondition('pay_type = '.$payType);
		$criteria->addCondition('live_times <= '.$hours);
		$criteria->addCondition('live_days <= '.$days);
		$criteria->addCondition('charm_points <= '.$charmPoints);
		return $this->findAll($criteria);
	}
}

?>