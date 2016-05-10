<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: ArchivesModel.php 8975 2013-04-23 01:59:13Z leiwei $
 * @package model
 * @subpackage archives
 */
class ArchivesLiveServerModel extends PipiActiveRecord {

	/**
	 * @param unknown_type $className
	 * @return ArchivesLiveServerModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{archives_live_server}}';
	}


	public function getDbConnection(){
		return Yii::app()->db_archives;
	}

	public function rules(){
		return array(
			array('archives_id,server_id,','required'),
		);
	}

	public function attributeLabels(){
		return array(
			'archive_id'=>'档期ID',
			'server_id'=>'视频服务器ID',
		);
	}

	/**
	 * 根据档期ID获取视频服务器ID
	 * @param array $archivesIds  档期ID
	 * @return array
	 */
	public function getArchivesLiveServerByArchivesIds(array $archivesIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('archives_id',$archivesIds);
		return $this->findAll($criteria);
	}

	/**
	 * 根据档期ID获取视频服务器地址
	 * @param int $archivesIds  档期ID
	 * @return array
	 */
	public function getArchivesLiveServerByArchivesId($archivesId){
		$criteria = $this->getDbCriteria();
		$criteria->addColumnCondition(array('archives_id'=>$archivesId));
		return $this->findAll($criteria);
	}

}

?>