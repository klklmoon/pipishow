<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: UserGiftSendRelationRecordsModel.php 9304 2013-04-29 03:40:19Z leiwei $ 
 * @package model
 * @subpackage gift
 */
class UserGiftSendRelationRecordsModel extends PipiActiveRecord{
	
	/**
	 * @param unknown_type $className
	 * @return UserGiftSendRelationRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_giftsend_relation_records}}';
	}
	

	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}
	
	public function rules(){
		return array(
			array('uid','required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'uid'=>'送礼用户uid',
		);
	}
	
	public function getUserGiftReceiveRecordsByUid($uid,$offset=0,$pageSize=10,array $condition=array()){
		$criteria = $this->getDbCriteria();
		$condition['uid']=$uid;
		$criteria->addColumnCondition($condition);
		$criteria->limit=$pageSize;
		$criteria->offset = $offset;
		return $this->findAll($criteria);
	}
	
}

?>