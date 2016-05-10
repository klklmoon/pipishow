<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: OtherRedisModel.php 8564 2013-04-10 14:09:35Z leiwei $ 
 * @package rmodel
 * @subpackage token
 */
class TokenRedisModel {
	
	
	/**
	 * 取得token redis服务器连接
	 * return PipiRedisConnection
	 */
	public function getTokenRedisConnection(){
		return Yii::app()->redis_token;
	}
	
	
	
	/**
	 * @param int $uid           //用户uid
	 * @param int $archives_id   //档期Id
	 * @param string $token      //存储的token
	 * @param int $expirTime     //失效时间
	 */
	public function saveToken($uid,$archives_id,$token,$expirTime){
		if($uid<=0||$archives_id<=0) return false;
		$tokenRedis=$this->getTokenRedisConnection();
		$key=$this->getTokenRedisKey().$uid.'_'.$archives_id;
		if($tokenRedis->set($key,$token)){
			return $tokenRedis->expire($key,$expirTime);
		}else{
			return false;
		}
		
	}
	
	public function getToken($uid,$archives_id){
		if($uid<=0||$archives_id<=0) return false;
		$tokenRedis=$this->getTokenRedisConnection();
		$key=$this->getTokenRedisKey().$uid.'_'.$archives_id;
		return $tokenRedis->get($key);
	}
	
	/**
	 * 获取token redis的键值
	 * @return string
	 */
	private function getTokenRedisKey(){
		$key = Yii::getKeyConfig('redis','token');
		if(empty($key)){
			return $this->setError(Yii::t('common','{config} config is empty',array('{config}'=>'(redis other key)')),0);
		}
		return $key;
	}
}

?>