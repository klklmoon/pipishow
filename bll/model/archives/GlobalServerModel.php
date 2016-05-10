<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: ArchivesModel.php 8975 2013-04-23 01:59:13Z leiwei $ 
 * @package model
 * @subpackage archives
 */
class GlobalServerModel extends PipiActiveRecord {
	
	/**
	 * @param unknown_type $className
	 * @return GlobalServerModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{global_server}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_archives;
	}
	
	public function rules(){
		return array(
			array('domain','required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'domain'=>'聊天服务器地址',
			'use_num'=>'使用数量',
		);
	}
	
	/**
	 * 获取所有聊天服务器地址
	 * @return array
	 */
	public function getGlobalServer(){
		$criteria = $this->getDbCriteria();
		return $this->findAll($criteria);
	}
	
	/**
	 * 获取使用最少的聊天服务器信息
	 * @return array
	 */
	public function getMinUserGlobalServer(){
		$criteria = $this->getDbCriteria();
		$criteria->order='use_num ASC';
		$criteria->limit=1;
		return $this->find($criteria);	
	}
	
	/**
	 * 根据聊天服务ID获取视频服务器
	 * @param array $serverIds  聊天服务ID
	 * @return array
	 */
	public function getGlobalServerByserverIds(array $serverIds){
		if(empty($serverIds)) return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('global_server_id',$serverIds);
		return $this->findAll($criteria);
	}
	
	
	
	/**
	 * 根据聊天服务ID删除视频服务器
	 * @param array $serverIds 聊天服务ID
	 * @return array
	 */
	public function delGlobalServerByserverIds(array $serverIds){
		if(empty($serverIds)) return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('global_server_id',$serverIds);
		return $this->delete($criteria);
	}
	
	/**
	 * 增加聊天服务器使用量
	 * @param int $serverId 聊天服务ID
	 * @return boolen
	 */
	public function addGlobalServerUseByServerId($serverId){
		if($serverId<=0) return array();
		$condition='global_server_id=:server_id';
		$params[':server_id']=$serverId;
		return $this->updateCounters(array('use_num'=>1),$condition,$params);
	}
	
	
	public function reduceGlobalServerUserByServerId($serverId){
		if($serverId<=0) return array();
		$condition='global_server_id=:server_id';
		$params[':server_id']=$serverId;
		return $this->updateCounters(array('use_num'=>-1),$condition,$params);
	}
	
	
	
}

?>