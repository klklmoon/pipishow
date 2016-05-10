<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: ArchivesOnlineRecordModel.php 9684 2013-05-07 03:58:35Z supeng $ 
 * @package model
 * @subpackage archives
 */
class ArchivesOnlineRecordModel extends PipiActiveRecord {
	
	public $domain;
	
	/**
	 * @param unknown_type $className
	 * @return ArchivesOnlineRecordModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{sess_total}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_archives;
	}
	
	public function getSessTotalSumByCondition(Array $archivesIds){
		$criteria = $this->getDbCriteria();
		
		$criteria->addInCondition('archives_id', $archivesIds);
		$criteria->select = '`total` ,`online_total`,domain,archives_id';
		$criteria->group = 'archives_id';
	
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
	}
	
	
}

?>