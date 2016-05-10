<?php

class UserIosSetModel extends PipiActiveRecord {
	
	/**
	 * @param string $className
	 * @return UserOperatedModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{user_ios_set}}';
	}
	
	public function rules(){
		return array(
			array('uid','required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'uid'=>'用户uid',
			'device_token'=>'设备号',
			'badge'=>'icon标记',
			'sound'=>'播放的音频文件',
			'notice'=>'远程通知'
		);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_user;
	}
	
	public function getUserIosByNotice($notice=true){
		$criteria = $this->getDbCriteria();
		$criteria->compare('notice', $notice);
		return $this->findAll($criteria);
	}
	
	public function getUserIosSetByCondition(array $condition){
		if($condition['uid']<=0||!$condition['device_token']){
			return false;
		}
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $condition['uid']);
		$criteria->compare('device_token', $condition['device_token']);
		isset($condition['notice'])&&$criteria->compare('notice', $condition['notice']);
		isset($condition['badge'])&&$criteria->compare('badge', $condition['badge']);
		isset($condition['sound'])&&$criteria->compare('sound', $condition['sound']);
		return $this->find($criteria);
	}
	
	public function getUserAndroidByCondition(array $condition){
		if($condition['uid']<=0||!$condition['user_id']||!$condition['channel_id']){
			return false;
		}
		$criteria = $this->getDbCriteria();
		$criteria->compare('uid', $condition['uid']);
		$criteria->compare('user_id', $condition['user_id']);
		$criteria->compare('channel_id', $condition['channel_id']);
		isset($condition['notice'])&&$criteria->compare('notice', $condition['notice']);
		isset($condition['badge'])&&$criteria->compare('badge', $condition['badge']);
		isset($condition['sound'])&&$criteria->compare('sound', $condition['sound']);
		return $this->find($criteria);
	}
	
	
	
	
	
}

