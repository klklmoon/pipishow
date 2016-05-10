<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package model
 * @subpackage archivesUser
 */
class ArchivesUserModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return ArchivesUserModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{archives_user}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_archives;
	}
	
	public function rules(){
		return array(
			array('uid,archives_id','required')
		);
	}
	
	public function attributeLabels(){
		return array(
			'archives_id'=>'档期ID',
			'uid'=>'用户uid',
		);
	}
	
	/**
	 * 根据档期Id获取获取主播用户
	 * @param array $archivesIds 档期Id
	 * @return array 
	 */
	public function getArchivesUserByarchivesIds(array $archivesIds){
		if(empty($archivesIds)) return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('archives_id',$archivesIds);
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据主播uid获取所属档期
	 * @param array $uids 主播uid
	 * @return array 
	 */
	public function getArchivesUserByUids(array $uids){
		if(empty($uids)) return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('uid',$uids);
		return $this->findAll($criteria);
	}
}

?>