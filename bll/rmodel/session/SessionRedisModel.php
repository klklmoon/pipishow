<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: SessionRedisModel.php 17132 2014-01-07 07:55:07Z leiwei $ 
 * @package rmodel
 * @subpackage other
 */
class SessionRedisModel {
	private static $instance;
	
	/**
	 * 返回SessionRedisModel对象的单例
	 * @return SessionRedisModel
	 */
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 取得session redis服务器连接
	 * return PipiRedisConnection
	 */
	public function getCacheRedisConnection(){
		return Yii::app()->redis_session;
	}
	
	/**
	 * 获取用户的sessionid
	 * @param int $uid
	 * @return string
	 */
	public function getMobileUserSessionId($uid){
		if($uid<=0) return false;
		$key=$this->getOtherRedisKey('mobile_user_session');
		$sessionRedis = $this->getCacheRedisConnection();
		return $sessionRedis->get($key.$uid);
	}
	
	/**
	 * 存储用户的sessionid
	 * @param int $uid
	 * @param string $sessionId
	 * @return boolean
	 */
	public function saveMobileUserSessionId($uid,$sessionId){
		if($uid<=0||empty($sessionId)) return false;
		$key=$this->getOtherRedisKey('mobile_user_session');
		$sessionRedis = $this->getCacheRedisConnection();
		if($sessionRedis->set($key.$uid,$sessionId)){
			$expire=Yii::app()->params['api_config']['session_timeout'];
			return $sessionRedis->expire($key.$uid,$expire);
		}
		return false;
	}
	
	 /**
	 * 获取session redis的键值
	 * @return string
	 */
	private function getOtherRedisKey($subKey = null){
		$config = Yii::getKeyConfig('redis','user_session');
		if(empty($config)){
			return trigger_error(Yii::t('common','{config} config is empty',array('{config}'=>'(redis other key)')),E_USER_ERROR);
		}
		return $subKey ? $config[$subKey] : $config;
	}
	
}

?>