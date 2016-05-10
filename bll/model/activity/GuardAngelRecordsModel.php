<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author su peng <suqian@pipi.cn>
 * @version $Id: GuardAngelRecordsModel.php 10145 2013-05-14 04:49:25Z supeng $ 
 * @package model
 */
class GuardAngelRecordsModel extends PipiActiveRecord{
	/**
	 * @param string $className
	 * @return GuardAngelRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getPrimaryKey(){
		return 'record_id';
	}
	
	public function tableName(){
		return '{{long_guardangel_records}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_activity;
	}
	
	/**
	 * 获取当前周期
	 * @return Ambigous <string, unknown, mixed>
	 */
	public function getCurrentCycle(){
		$criteria = $this->getDbCriteria();
		$criteria->order = 'cycle DESC';
		$criteria->limit = 1;
		$criteria->select = 'cycle';
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryScalar();
	}
	
	/**
	 * 检查是否已经守护
	 * @param int $uid
	 * @param int $dotey_uid
	 * @return Ambigous <NULL, unknown, multitype:unknown Ambigous <unknown, NULL> , mixed, multitype:, multitype:unknown >
	 */
	public function checkGuard($uid,$dotey_uid,$cycle){
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $uid);
		$criteria->compare('dotey_uid', $dotey_uid);
		$criteria->compare('cycle', $cycle);
		return $this->find($criteria);
	}
	
	/**
	 * 更新守护星记录
	 * 
	 * @param array $data
	 * 		uid,dotey_uid,cycle不能为空
	 * @return Ambigous <unknown, number>
	 */
	public function updateGuardAngelRecords($data){
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $data['uid']);
		$criteria->compare('dotey_uid', $data['dotey_uid']);
		$criteria->compare('cycle', $data['cycle']);
		return $this->getCommandBuilder()->createUpdateCommand($this->tableName(), $data, $criteria)->execute();
	}
	
}