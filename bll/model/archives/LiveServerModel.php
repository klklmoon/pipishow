<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: ArchivesModel.php 8975 2013-04-23 01:59:13Z leiwei $ 
 * @package model
 * @subpackage archives
 */
class LiveServerModel extends PipiActiveRecord {
	
	/**
	 * @param unknown_type $className
	 * @return LiveServerModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{live_server}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_archives;
	}
	
	public function rules(){
		return array(
			array('import_host,export_host','required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'import_host'=>'视频输入地址',
			'export_host'=>'视频输出地址',
		);
	}
	
	/**
	 * 获取所有视频服务器地址
	 * @return array
	 */
	public function getLiveServer(){
		$criteria = $this->getDbCriteria();
		return $this->findAll($criteria);
	}
	
	/**
	 * 获取使用最少的视频服务器信息
	 * @return array
	 */
	public function getMinUserLiveServer(){
		$criteria = $this->getDbCriteria();
		$criteria->addCondition('server_id>4');
		$criteria->order='use_num ASC';
		$criteria->limit=1;
		return $this->find($criteria);	
	}
	
	/**
	 * 根据视频服务ID获取视频服务器
	 * @param array $serverIds  视频服务ID
	 * @return array
	 */
	public function getLiveServerByserverIds(array $serverIds){
		if(empty($serverIds)) return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('server_id',$serverIds);
		return $this->findAll($criteria);
	}
	
	
	
	/**
	 * 根据视频服务ID删除视频服务器
	 * @param array $serverIds 视频服务ID
	 * @return array
	 */
	public function delLiveServerByserverIds(array $serverIds){
		if(empty($serverIds)) return array();
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('server_id',$serverIds);
		return $this->delete($criteria);
	}
	
	/**
	 * 增加视频服务器使用量
	 * @param int $serverId 聊天服务ID
	 * @return boolen
	 */
	public function addLiveServerUseByServerId($serverId){
		if($serverId<=0) return array();
		$condition='server_id=:server_id';
		$params[':server_id']=$serverId;
		return $this->updateCounters(array('use_num'=>1),$condition,$params);
	}
	
	
	
}

?>