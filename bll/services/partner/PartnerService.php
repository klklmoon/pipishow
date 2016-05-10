<?php
/**
 * 推广注册服务层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package service
 * @subpackage partner
 */
class PartnerService extends PipiService {
	/**
	 * 存储注册推广来源日志
	 * 
	 * @param array $log
	 * @return int
	 */
	public function saveRegLog(array $log){
		if(isset($log['uid']) && ($uid = $log['uid']) <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		
		$regLogModel = new RegLogModel();
		$this->attachAttribute($regLogModel,$log);
		if(!$regLogModel->validate()){
			return $this->setNotices($regLogModel->getErrors(),array());
		}
		$regLogModel->save();
		return $insertId = $regLogModel->getPrimaryKey();
	}
	/**
	 * 批量存储主播在线时长数据
	 * 
	 * @param array $online
	 * @return boolean
	 */
	public function batchSaveLoginStateOnLine(array $online){
		if(empty($online) || $this->getArrayDim($online) < 2){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$loginStatOnlineModel = new LoginStatOnlineModel();
		return $loginStatOnlineModel->batchInsert($online,true);
	}
	
	/**
	 * 获取用户观看直播的总时长
	 * @param int $uid
	 * @return number
	 */
	public function getViewDurationByUid($uid){
		$time = LoginStatOnlineModel::model()->getDurationByUid($uid);
		return floor($time/3600);
	}
}

?>