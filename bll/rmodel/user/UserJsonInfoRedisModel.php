<?php
/**
 * 系统读取最频繁的用户属性信息，存储在redis中
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author He Xin <hexin@163.com>
 * @version $Id: UserBasicModel.php 8366 2013-04-01 14:56:32Z suqian $ 
 * @package model
 * @subpackage user
 */
class UserJsonInfoRedisModel{
	private $cycle = 0;		//并发时事务失败时需要再次更新
	private $cycle_max = 3;	//并发时事务失败时再次更新的循环次数的最大值，这里默认更新3次失败后全部交给GlobalServer来处理
	private static $instance;
	
	/**
	 * @param string $className
	 * @return UserJsonInfoRedisModel
	 */
	public static function model(){
		if(!isset(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function getRedisConnection(){
		return Yii::app()->redis_userinfo;
	}
	
	public function getRedisKey($uid){
		global $keyConfig;
		return $keyConfig['redis']['user_info']['key'].":".$uid;
	}
	
	public function getProperty(){
		global $keyConfig;
		return $keyConfig['redis']['user_info']['property'];
	}
	
	/**
	 * 存储user_info
	 * 
	 * @param int $uid
	 * @param array $data
	 * @return boolean
	*/
	public function setUserInfo($uid, $data){
		if($uid <= 0 || empty($data)){
			return false;
		}
		
		$key = $this->getRedisKey($uid);
		$this->getRedisConnection()->getClient()->watch($key);
		$json = $this->getRedisConnection()->getClient()->get($key);
		if($json) 
			$array = json_decode($json, true);
		else 
			$array = array();
		
		$property = $this->getProperty();
		foreach($data as $k => $v){
			if(in_array($k, $property)){
				if($v === array() || $v === null || $v === ''){
					unset($data[$k]);
					unset($array[$k]);
				}else{
					$array[$k] = $v;
				}
			}
		}
		$this->getRedisConnection()->getClient()->multi();
		$this->getRedisConnection()->getClient()->set($key, json_encode($array));
		$return = $this->getRedisConnection()->getClient()->exec();
		//并发更新失败时需要再次更新的情况
		if(!$return){
			if($this->cycle >= $this->cycle_max){
				//redis当掉的情况下，保存error log并发送zmq事件消息
				Yii::log('redis update error: '.json_encode($data),CLogger::LEVEL_ERROR,'pipi.server.redis.userInfo');
				$sends = array(
					'type'			=> 'failed_update',
					'uid'			=> $uid,
					'json_content'	=> $data,
				);
				Yii::app()->zmq->sendZmqMsg(609, $sends);
			}else{
				$this->cycle++;
				$return = $this->setUserInfo($uid, $data);
			}
		}else{
			$return = $return[0];
		}
		$this->cycle = 0;
		return $return;
	}
	
	/**
	 * 获取user_info
	 * 
	 * @param int $uid
	 * @return string 返回Json字符串
	 */
	public function getUserInfo($uid){
		if($uid <= 0){
			return "{}";
		}
		$json = $this->getRedisConnection()->getClient()->get($this->getRedisKey($uid));
		return $json ? $json : "{}";
	}
	
	/**
	 * 批量获取user_info
	 * @param array $uids
	 * @return array 返回Json字符串数组
	 */
	public function getUserInfos(array $uids){
		if(empty($uids)){
			return array();
		}
		$keys = array();
		foreach($uids as $uid){
			$keys[] = $this->getRedisKey($uid);
		}
		$jsons = $this->getRedisConnection()->getClient()->mget($keys);
		return $jsons;
	}
	
	/**
	 * 批量删除user_info
	 * @param array $uids
	 * @return boolean
	 */
	public function deleteUserInfos(array $uids){
		if(empty($uids)){
			return false;
		}
		$keys = array();
		foreach($uids as $uid){
			$keys[] = $this->getRedisKey($uid);
		}
		return $this->getRedisConnection()->getClient()->delete($keys);
	}
}