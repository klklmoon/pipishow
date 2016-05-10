<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: PurviewLiveModel.php 8659 2013-04-15 09:47:05Z hexin $ 
 * @package model
 * @subpackage purview
 */
class PurviewLiveModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return PurviewLiveModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{archives_purview}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_archives;
	}
	
	public function rules(){
		return array(
			array('uid,archives_id','required'),
			array('uid,archives_id','numerical'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'uid'=>'管理uid',
			'archives_id'=>'所属档期ID',
		);
	}
	
	/**
	 * 根据用户的uid获取属于档期的房管
	 * @param array $uids 房管的uid
	 * @return array
	 */
	public function getPurviewLiveByUids(array $uids){
		if(empty($uids)) return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('uid',$uids);
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据档期Id获取属于房管的用户
	 * @param array $archivesIds 房管的archivesIds
	 * @return array
	 */
	public function getPurviewLiveByArchivesIds(array $archivesIds){
		if(empty($archivesIds)) return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('archives_id',$archivesIds);
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据条件获取房管信息
	 * @param array $condition
	 * @return array
	 */
	public function getPurviewLiveByCondition(array $condition){
		$criteria = $this->getDbCriteria();
		$criteria->addColumnCondition($condition);
		return $this->findAll($criteria);
	}
	
	/**
	 * 统计用户的房管数
	 * @param array $uids 房管拥有者的uid
	 * @return array
	 */
	public function getPurviewLiveCountByUids(array $uids){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('uid',$uids);
		return $this->count($criteria);
	}
	
	/**
	 * 统计档期拥有的房管数
	 * @param array $archivesIds 档期的ID
	 * @return array
	 */
	public function getPurviewLiveCountByArchivesIds(array $archivesIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('archives_id',$archivesIds);
		return $this->count($criteria);
	}
	
	/**
	 * 根据ID条件删除房管
	 * @param array $ids
	 * @return boolean
	 */
	public function delPurviewLiveByIds(array $ids){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('purview_live_id', $ids);
		return $this->deleteAll($criteria);
	}
}

?>