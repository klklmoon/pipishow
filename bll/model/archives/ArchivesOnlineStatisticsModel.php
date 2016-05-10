<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: ArchivesOnlineStatisticsModel.php 9684 2013-05-07 03:58:35Z supeng $ 
 * @package model
 * @subpackage archives
 */
class ArchivesOnlineStatisticsModel extends PipiActiveRecord {
	
	/**
	 * @param unknown_type $className
	 * @return ArchivesOnlineStatisticsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{sess_stat}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_archives;
	}
	
	public function getSessStatSumByCondition(Array $archivesIds,Array $condition = array(),$isSort = false){
		$criteria = $this->getDbCriteria();
		
		$criteria->addInCondition('archives_id', $archivesIds);
		$criteria->alias = 'a';
		$criteria->group = 'archives_id';
		
		if($isSort){
			$criteria->select = ' a.archives_id,max(a.total) as total,create_time';
			$criteria->order = ' total DESC ';
			return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		}else{
			$criteria->select = 'a.archives_id, count(*) as count,sum(total) as sum ';
			return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		}
	
	}
	
}

?>