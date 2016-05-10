<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: EventRedisModel.php 9101 2013-04-24 08:09:38Z leiwei $ 
 * @package rmodel
 * @subpackage event
 */
class EventRedisModel {
	/**
	 * 取得event redis服务器连接
	 * return PipiRedisConnection 
	 */
	public function getEventRedisConnection(){
		return Yii::app()->redis_event;
	}
	
	/**
	 * 获取event redis 中的流水号
	 * @return number
	 */
	public function getEventIncrementId(){
		$eventKey=$this->getEventRedisKey();
		$key=$eventKey['eventKey'];
		$eventRedis=$this->getEventRedisConnection();
		$zmqEventKey=$eventRedis->get($key);
		if(empty($zmqEventKey)){
			$eventRedis->set($key,1);
			$zmqEventKey=1;
		}else{
			$zmqEventKey=$eventRedis->incr($key);
		}
		return $zmqEventKey;
	} 
	
	/**
	 * 将事件未发送成功的存入redis中
	 * @param string $name 
	 * @return string
	 */
	public function saveEventList($name){ 
		$key=$this->getEventRedisKey();
		$eventListKey=$key['eventListKey'];
		$eventRedis=$this->getEventRedisConnection();
		return $eventRedis->lpush($eventListKey,$name);
	}
	
	/**
	 * 获取event redis的键值
	 * @return string
	 */
	private function getEventRedisKey(){
		$key = Yii::getKeyConfig('redis','event');
		if(empty($key)){
			return $this->setError(Yii::t('common','{config} config is empty',array('{config}'=>'(redis event key)')),0);
		}
		return $key;
	}
}

?>