<?php

/**
 *　皮皮乐天API基础控制器层，所有API应用控制器基类
 *
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su Qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PipiController.php 8317 2013-03-29 01:19:47Z suqian $
 * @package 
 */
class PipiApiController extends CController{

	/**
	 * @var int 第三方APP ID
	 */
	protected $app_id;
	
	/**
	 * @var int 皮皮乐天用户ＩＤ，供内部系统通信使用
	 */
	protected $uid;
	
	/**
	 * @var int 第三方ＡＰＰ请求时间缀
	 */
	protected $timestamp;
	/**
	 * 
	 * @var string 第三方请求生成的token，token每次值必须是唯一的，而且只能使用一次
	 */
	protected $token;
	/**
	 * 
	 * @var string 第三方APP信息
	 */
	protected $app = array();
	
	/**
	 * 
	 * @var boolean 检查第三方token
	 */
	protected $isCheckToken  = true;
	/**
	 * @var boolean 是否检查APP
	 */
	protected $isCheckApp = true;
	
	protected $response_type = 0;
	/**
	 * @var AppService
	 */
	protected static $appService;
	
	public function __construct($id,$module){
		if(self::$appService == null)
			self::$appService = new AppService();
		parent::__construct($id,$module);
	}
	
	/**
	 * 生成token做验证
	 * 注意：
	 * 生成token需要页面提供参数app_id,uid,timestamp,token,这四个默认参数后面的业务参数不能覆盖，尤其注意uid等的敏感性参数，这个坑后面再做优化
	 * response_type 为0时默认输出,为1是返回json格式，为2时返回jsonp格式
	 * @see CController::beforeAction()
	 */
	public function beforeAction($action){
		
		$this->app_id = Yii::app()->request->getParam('app_id');
		$this->uid = Yii::app()->request->getParam('uid');
		$this->response_type = Yii::app()->request->getParam('response_type');
		if($this->isCheckApp){
			$this->app = self::$appService->getAppInfoById($this->app_id);
			if(empty($this->app)){
				if($this->response_type == 1){
					echo json_encode(array('status'=>'fail','message'=>'app is not exist'));
				}elseif($this->response_type == 2){
					$this->jsonpReturn(array('status'=>'fail','message'=>'app is not exist'));
				}else{
					echo 'app is not exist';
				}
				return false;
			}
		}
		if($this->isCheckToken){
		 	return $this->checkToken($action);
		}
		return true;
	}
	
	/**
	 * 生成签名
	 * 
	 * @param int $uid　皮皮乐天用户ＩＤ
	 * @param int $app_id　第三方应用ＩＤ
	 * @param string $app_secret　第三方密钥
	 * @param int $timestamp 第三方请求时间缀
	 * @param CAction $action 动作对象
	 * @return array
	 */
	protected function buildToken($uid,$app_id,$app_secret,$timestamp, $action){
		$baseSignString = ($uid ? $uid : '') .$this->getId().$action->getId().$app_id.$timestamp;
		return md5(md5($baseSignString).$app_secret);
	}
	
	/**
	 *  验证第三方信息
	 * 
	 * @param CAction $action　调用的动作（接口）
	 * @return boolean true表示成功　false表示失败
	 */
	private function checkToken($action){
		$this->timestamp = Yii::app()->request->getParam('timestamp');
		$this->token = Yii::app()->request->getParam('token');
		
		if(!$this->token || $this->timestamp <= 0 || $this->app_id <= 0){
			
			if($this->response_type == 1){
				echo json_encode(array('status'=>'fail','message'=>'valid param is error'));
			}elseif($this->response_type == 2){
				$this->jsonpReturn(array('status'=>'fail','message'=>'valid param is error'));
			}else{
				echo 'valid param is error';
			}
			return false;
		}
		
		if($this->isCheckApp && empty($this->app)){
			if($this->response_type == 1){
				echo json_encode(array('status'=>'fail','message'=>'app is not exist'));
			}elseif($this->response_type == 2){
				$this->jsonpReturn(array('status'=>'fail','message'=>'app is not exist'));
			}else{
				echo 'app is not exist';
			}
			return false;
		}
		
		$token = $this->buildToken($this->uid,$this->app_id,$this->app['app_secret'],$this->timestamp,$action);
		
		if($token !== $this->token){
			if($this->response_type == 1){
				echo json_encode(array('status'=>'fail','message'=>'token is error'));
			}elseif($this->response_type == 2){
				$this->jsonpReturn(array('status'=>'fail','message'=>'token is error'));
			}else{
				echo 'token is error';
			}
			return false;
		}
		
		if(self::$appService->getAppTokenByAppId($this->app_id,$this->token)){
			if($this->response_type == 1){
				echo json_encode(array('status'=>'fail','message'=>'token is used'));
			}elseif($this->response_type == 2){
				$this->jsonpReturn(array('status'=>'fail','message'=>'token is used'));
			}else{
				echo 'token is used';
			}
			return false;
		}
		
		$tokenData['uid'] = $this->uid;
		$tokenData['app_id'] = $this->app_id;
		$tokenData['token'] = $token;
		$tokenData['valid_time'] = $this->timestamp;
		self::$appService->createAppToken($tokenData);
		return true;
		
	}
	
	//返回jsonp格式数据
	protected function jsonpReturn(array $response){
		header("Content-Type:text/html; charset=utf-8");
		$callback = Yii::app()->request->getParam('callback');
		echo ($callback ? $callback : 'callback').'('.json_encode($response).')';
	}
	
	protected function responseClient($status,$data){
		if($this->response_type == 1){
			echo json_encode(array('status'=>$status,'message'=>$data));
		}elseif($this->response_type == 2){
			$this->jsonpReturn(array('status'=>$status,'message'=>$data));
		}else{
			echo $status;
		}
		Yii::app()->end();
	}
	
}
