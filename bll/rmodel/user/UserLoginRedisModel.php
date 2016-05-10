<?php

/**
 * 用户登录相关redis model
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: PipiUserRedisService.php 894 2010-12-28 07:55:25Z suqian $ 
 * @package service
 * @subpackage common
 */
class UserLoginRedisModel   {

	
	
	/**
	 * 取得redis User服务器连接
	 * return PipiRedisConnection
	 */
	public function getUserRedisServer(){
		return Yii::app()->redis_user;
	}
	
	
	/**
	 * 将用户注册信息添加到redis
	 * 
	 * @param string $username 用户名做键值
	 * @param array $user
	 */
	public function saveUserBasicToRedis($username,array $user){
		if(empty($username) || empty($user)){
			return false;
		}
		$redisCache = $this->getUserRedisServer();
		$key= $this->getLoginRedisKey();
		$redisUser = array();
		$redisUser['uid']=$user['uid'];
		$redisUser['username']=$user['username'];
		$redisUser['nickname']=$user['nickname'];
		$redisUser['realname']=$user['realname'];
		$redisUser['password']=$user['password'];
		$redisUser['reg_mobile']=$user['reg_mobile'];
		$redisUser['reg_email']=$user['reg_email'];
		$redisUser['reg_salt']=$user['reg_salt'];
		$redisUser['reg_source'] = $user['reg_source'];
		$redisUser['recharge'] = $user['recharge'];
		$redisUser['recharge_usd'] = $user['recharge_usd'];
		$redisUser['create_time'] = $user['create_time'];
		$redisUser['user_type']=$user['user_type'];
		$redisUser['user_status'] = $user['user_status'];
		$redisUser['last_login_time'] = time();
		$redisUser['last_send_gift_time']=isset($user['last_send_gift_time'])?$user['last_send_gift_time']:0;
		if(isset($redisUser['update_desc']) && is_string($redisUser['update_desc'])){
			$redisUser['update_desc'] = json_decode($redisUser['update_desc'],true);
		}
		return  $redisCache->set($key.$username,json_encode($redisUser));
		
	}
	
	 /**
	 * 取得有效的用户
	 * 
	 * @param string $condition 获取条件 username,email
	 * @param string $loginType 查询类型 0表示用户名　1表示邮箱
	 * @return array
	 */
	public function getVadidatorUser($condition,$loginType){
		$user =  array();
		$redisCache = $this->getUserRedisServer();
		$key= $this->getLoginRedisKey();
		if($loginType == USER_LOGIN_USERNAME){
			$user = $redisCache->get($key.$condition);
		}else if($loginType == USER_LOGIN_EMAIL){
			//todo 暂不支持邮箱登陆
		}else{
			return array();
		}
		if(!$user){
			return array();
		}
		return json_decode($user,true);
	}
	
	/**
	 * 批量获取用户基本信息
	 * 
	 * @param array $uids
	 * @return array
	 */
	public function getUserBasicByUserNames(array $userNames){
		if(empty($userNames)){
			return array();
		}
		$key = $this->getLoginRedisKey();
		$redisCache = $this->getUserRedisServer();
		$batKeys = array();
		foreach($userNames as $username){
			$batKeys[] = $key.$username;
		}
		$userBasics = $redisCache->mget($batKeys);
		if(!$userBasics){
			return array();
		}
		foreach($userBasics as $key=>$data){
			$_data = json_decode($data,true);
			if(!is_array($_data)){
				continue;
			}
			$userBasics[$key] = $_data;
		}
		return $userBasics;
		
	}
	public function getLoginRedisKey(){
		$key = Yii::getKeyConfig('redis','user_login');
		if(empty($key)){
			return $this->setError(Yii::t('common','{config} config is empty',array('{config}'=>'(redis login key)')),0);
		}
		return $key;
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $usernames
	 */
	public function delLoginRedisForUsername($username){
		if($username){
			$redisCache = $this->getUserRedisServer();
			$key= $this->getLoginRedisKey();
			return  $redisCache->delete($key.$username);
		}
	}

}

