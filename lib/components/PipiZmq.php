<?php
/**
 * @author leiwei <leiwei@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
define('BRODCAST_MSG',606);  //广播消息代码
define('FORBIDEN_MSG',607);  //禁言消息代码
define('GIFT_MSG',608);      //礼物消息代码
define('EVENT_MSG',609);     //事件消息代码
class PipiZmq extends CApplicationComponent{
	
	public $hosts=array();
	
	protected $overTime=500000;
	
	protected $spaceTime=10000;
	
	protected $maxSize=4096;
	
	
	
	/**
	 * @param string $dns
	 * @param string $str
	 * @return boolean
	 */
	public function sendMsg($dns,$str){
		$context=new ZMQContext();
		$queue = new ZMQSocket($context, ZMQ::SOCKET_REQ);
		$connect=$queue->connect($dns);
		$queue->setSockOpt (ZMQ::SOCKOPT_LINGER, $this->overTime/1000);
		$sending=true;
		$sended=false;
		$retries = $this->overTime/$this->spaceTime;
		do{
			try {
				if($sending){
					if ($queue->send($str, ZMQ::MODE_NOBLOCK) !== false) {
						$sending=false;
						$sended=true;
						break;
					}
				}
			} catch (ZMQSocketException $e) {
				$sended=false;
			}
			usleep ($this->spaceTime);
		}while (1 && --$retries);
		if($sended==false) return false;
		$retries = $this->overTime/$this->spaceTime;
		$receiving = true;
		do {
			try {
				if ($receiving) {
					$message = $queue->recv (ZMQ::MODE_NOBLOCK);
					if ($message) {
						$receiving = false;
						return $message;
					}
				}
			} catch (ZMQSocketException $e) {
				return false;
			}
			usleep ($this->spaceTime);
		} while (1 && --$retries);
		return false;
	}
	
	
	/**
	 * @param int $code 事件信令
	 * @param array $data 事件数据
	 * @return boolean
	 */
	public function sendZmqMsg($code, array $data){
		if(!$this->validZmqData($code,$data)){
			return false;
		}
		$received=false;
		$eventRedis=new EventRedisModel();
		$zmqEventKey=$eventRedis->getEventIncrementId();
		if(!$zmqEventKey) return false;
		array_unshift($data,$code,$zmqEventKey);
		if(array_key_exists('json_info',$data)) 
			$data['json_info']=json_encode($data['json_info']);
		if(array_key_exists('json_content', $data))
			$data['json_content']=json_encode($data['json_content']);
		$str=implode('|',$data);
		//超过最大字节限制，不发送
		if(sizeof($str)>=$this->maxSize) return false;
		$zmqList=Yii::app()->zmq->hosts;
		//根据流水号来取模发送到相应的服务器上
		$zmqSeverNum=count($zmqList);
		$key=$zmqEventKey%$zmqSeverNum;
		$dns="tcp://".$zmqList[$key]['host'].":".$zmqList[$key]['port'];
		$received=$this->sendMsg($dns,$str);
		//如果发送失败，从列表中轮循的发
// 		if($received===false){
// 			unset($zmqList[$key]);
// 			foreach($zmqList as $row){
// 				$_dns="tcp://".$row['host'].":".$row['port'];
// 				if($this->sendMsg($_dns,$str)==false){
// 					continue;
// 				}else{
// 					$received=true;
// 					break;
// 				}
// 			}
// 		}
		
		//接受返回消息失败或发送失败写入redis
		$loginPath=DATA_PATH.'runtimes/zmqLog.txt';
		if($received==false){
			$eventRedis->saveEventList($str);
			error_log(date("Y-m-d H:i:s")."发送失败，写入redis中zmq消息服务器：".$dns.",内容：".$str."\n\r",3,$loginPath);
		}
		
		error_log(date("Y-m-d H:i:s")."zmq消息服务器：".$dns.",内容：".$str."\n\r",3,$loginPath);
		return $zmqEventKey;
	}
	
	/**
	 * 发送zmq广播消息
	 * @param array $data  广播消息数据
	 * @return boolean 0->发送失败,1->发送成功
	 */
	public function sendBrodcastMsg(array $data){
		if(empty($data))
			return false;
		return $this->sendZmqMsg(BRODCAST_MSG,$data);
	}
	
	/**
	 * 发送zmq禁言消息
	 * @param array $data  禁言消息数据
	 * @return boolean 0->发送失败,1->发送成功
	 */
	public function sendForbidenMsg(array $data){
		if(empty($data))
			return false;
		return $this->sendZmqMsg(FORBIDEN_MSG,$data);
	}
	
	/**
	 * 发送zmq礼物消息
	 * @param array $data 礼物消息数据
	 * @return boolean 0->发送失败,1->发送成功
	 */
	public function sendGiftMsg(array $data){
		if(empty($data))
			return false;
		return $this->sendZmqMsg(GIFT_MSG,$data);
	}
	
	/**
	 * 发送zmq事件消息
	 * @param array $data 事件消息数据
	 * @return boolean 0->发送失败,1->发送成功
	 */
	public function sendEventMsg(array $data){
		if(empty($data))
			return false;
		return $this->sendZmqMsg(EVENT_MSG,$data);
	}
	
	
	
	/**
	 * 验证zmq消息格式
	 * @param int $code
	 * @param array $data 发送zmq消息数据
	 * @return boolean
	 */
	protected function validZmqData($code,array $data){
		$result=true;
		switch ($code){
			case BRODCAST_MSG:
				if(empty($data['archives_id'])||empty($data['domain'])||empty($data['type'])){
					$result=false;
				}
				break;
			case FORBIDEN_MSG:
				if(empty($data['archives_id'])||empty($data['domain'])||empty($data['uid'])||empty($data['nickname'])||!isset($data['type'])||empty($data['period'])||empty($data['status'])){
					$result=false;
				}
				break;
			case GIFT_MSG:
				if(empty($data['archives_id'])||empty($data['domain'])){
					$result=false;
				}
				break;
			case EVENT_MSG:
				if(empty($data['type'])||empty($data['uid'])){
					$result=false;
				}
				break;
			default:$result=false;break;				
		}
		return $result;
	}
	
	
}