<?php
define('IOS_PUSH_NOTICE_ON',1);       //开启推送
define('IOS_PUSH_NOTICE_OFF',0);      //关闭推送
define('MOBILE_IOS_TYPE',0);          //IOS端
define('MOBILE_ANDROID_TYPE',1);      //android端
/**
 * 用户IOS服务层
 *
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author leiwei <leiwei@pipi.cn>
 * @version $Id: UserIosService.php 16397 2013-11-20 08:41:50Z leiwei $
 * @package service
 */
class UserIosService extends PipiService{
	
	
	/**
	 * 存储用户IOS设置
	 * @param array $set IOS设置
	 * @return mix|boolean
	 */
	public function saveUserIosSet(array $set){
		if($set['uid']<=0||!$set['device_token'])
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		$userIosSetModel=new UserIosSetModel();
		$orguserIosSetModel=$userIosSetModel->findByAttributes(array('uid'=>$set['uid'],'device_token'=>$set['device_token']));
		if($orguserIosSetModel){
			$this->attachAttribute($orguserIosSetModel, $set);
			if (!$orguserIosSetModel->validate()) {
				return $this->setNotices($orguserIosSetModel->getErrors(), false);
			}
			$newSet=$set;
			unset($newSet['uid']);
			unset($newSet['device_token']);
			return $userIosSetModel->updateAll($newSet,'uid=:uid AND device_token=:device_token',array(':uid'=>$set['uid'],':device_token'=>$set['device_token']));
		}else{
			$this->attachAttribute($userIosSetModel, $set);
			if (!$userIosSetModel->validate()) {
				return $this->setNotices($userIosSetModel->getErrors(), false);
			}
			return $userIosSetModel->save();
		}
	}
	
	/**
	 * 存储用户android设置
	 * @param array $set android设置参数
	 * @return mix|boolean
	 */
	public function saveUserAndroidSet(array $set){
		if($set['uid']<=0||!$set['user_id']||!$set['channel_id'])
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		$userIosSetModel=new UserIosSetModel();
		$orguserIosSetModel=$userIosSetModel->findByAttributes(array('uid'=>$set['uid'],'user_id'=>$set['user_id'],'channel_id'=>$set['channel_id']));
		if($orguserIosSetModel){
			$this->attachAttribute($orguserIosSetModel, $set);
			if (!$orguserIosSetModel->validate()) {
				return $this->setNotices($orguserIosSetModel->getErrors(), false);
			}
			$newSet=$set;
			unset($newSet['uid']);
			unset($newSet['user_id']);
			unset($newSet['channel_id']);
			return $userIosSetModel->updateAll($newSet,'uid=:uid AND user_id=:user_id AND channel_id=:channel_id',array(':uid'=>$set['uid'],':user_id'=>$set['user_id'],':channel_id'=>$set['channel_id']));
		}else{
			
			$this->attachAttribute($userIosSetModel, $set);
			if (!$userIosSetModel->validate()) {
				print_r($userIosSetModel->getErrors());exit;
				return $this->setNotices($userIosSetModel->getErrors(), false);
			}
			
			return $userIosSetModel->save();
		}
	}
	
	
	/**
	 * 获取远程推送通知用户
	 * @param unknown_type $notice 是否推送
	 * @return array
	 */
	public function getUserIosByNotice($notice=IOS_PUSH_NOTICE_ON){
		$userIosSetModel=new UserIosSetModel();
		$data=$userIosSetModel->getUserIosByNotice($notice);
		return $this->arToArray($data);
	}
	
	/**
	 * 根据条件获取用户IOS设置
	 * @param array $condition
	 * @return array
	 */
	public function getUserIosByCondition(array $condition){
		if($condition['uid']<=0||empty($condition['device_token']))
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		$userIosSetModel=new UserIosSetModel();
		$userIosSet=$userIosSetModel->getUserIosSetByCondition($condition);
		return $userIosSet?$this->arToArray($userIosSet):array();
	}
	
	public function getUserAndroidByCondition(array $condition){
		if($condition['uid']<=0||empty($condition['user_id'])||empty($condition['channel_id']))
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		$userIosSetModel=new UserIosSetModel();
		$userIosSet=$userIosSetModel->getUserAndroidByCondition($condition);
		return $userIosSet?$userIosSet->attributes:array();
	}
	
	

	
}