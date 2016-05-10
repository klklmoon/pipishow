<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su Peng <supeng@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z supeng $ 
 */

//  @edit by guoshaobo
define('WEB_SITE_COUNT', 'WEB_SITE_COUNT');
define('WEB_REGISTER_SITE','WEB_REGISTER_SITE');
define('WEB_BAD_IP','WEB_BAD_IP');

define('WEB_LIVE_PUSH_GLOGAL',1);#首页强推全局配置
define('WEB_LIVE_PUSH_CUSTOM_DOTEY',2);#首页强推自定义主播配置
define('WEB_LIVE_PUSH_CUSTOM_TODYARMD',4);#首页强推今日推荐的主播
define('WEB_LIVE_PUSH_CUSTOM_ALL',6);#首页强推全部自定义（今日主播+自定义主播）
class WebConfigService extends PipiService {
	
	private static $redisModel = null;
	
	/**
	 * 存储主播基本信息
	 *
	 * @param array $config
	 * @return boolean
	 */
	public function saveWebConfig(array $config){
		if(empty($config['c_type']) || !isset($config['c_value']) || empty($config['c_key'])){
			return $this->setError(Yii::t('common','Parameter is empty'),false);
		}
		
		if (!key_exists($config['c_type'], $this->getCType())){
			return $this->setError(Yii::t('common', 'Parameter is wrong'),false);
		}
		
		$cvalue = $this->checkCValue($config['c_type'], $config['c_value']);
		if($cvalue){
			$config['c_value'] = $cvalue;
		}else{
			return $this->setError(Yii::t('common', 'Parameter is wrong'),false);
		}
		
		$flag = false;
		$webConfigModel = new WebConfigModel();
		$orgWebConfigModel = $webConfigModel->findByPk($config['c_key']);
		if($orgWebConfigModel){
			$this->attachAttribute($orgWebConfigModel,$config);
			if(!$orgWebConfigModel->validate()){
				return $this->setNotices($orgWebConfigModel->getErrors(),false);
			}
			$flag = $orgWebConfigModel->save();
		}else{
			$this->attachAttribute($webConfigModel,$config);
			if(!$webConfigModel->validate()){
				return $this->setNotices($webConfigModel->getErrors(),false);
			}
			$flag = $webConfigModel->save();
		}
		// @edit by guoshaobo 更新redis;
		if($flag){
			$this->getWebConfig($config['c_key'], true);
			if($this->isAdminAccessCtl()){
				$this->saveAdminOpLog('更新 WEB配置 KEY='.$config['c_key']);
			}
		}
		// edit end
		return $flag;
	}
	
	/**
	 * 删除配置项
	 * 
	 * @param string $ckey
	 * @return mix
	 */
	public function delWebConfig($ckey){
		if (empty($ckey)){
			return $this->setError(Yii::t('common', 'Parameter is empty'),false);
		}
		
		$flag = WebConfigModel::model()->deleteByPk($ckey);
		if($flag && $this->isAdminAccessCtl()){
			$this->saveAdminOpLog('删除 WEB配置 KEY='.$ckey);
		}
		return $flag;
	}
	
	/**
	 * 获取配置项
	 * @edit by guoshaobo
	 * @param mix $ckey
	 * @return mix
	 */
	public function getWebConfig($ckey, $rebuild = false){
		if (empty($ckey)){
			return $this->setError(Yii::t('common', 'Parameter is empty'),false);
		}
		
		$config = $this->getWebSiteConfigCache($ckey);
		if($config && !$rebuild){
			return $config;
		}else{
			$result = WebConfigModel::model()->findByPk($ckey);
			if($result){
				$result = $result->attributes;
				$this->formatCValue($result);
				$this->saveWebSiteConfigCache($ckey, $result);
			}
			return $result;
		}
	}
	/**
	 * 从缓存中获取网站配置
	 * @author guoshaobo
	 * @param unknown_type $key
	 * @return boolean
	 */
	public function getWebSiteConfigCache($key)
	{
		$redisModel = $this->getRedisModel();
		$config = $redisModel->getWebSiteConfig($key);
		return $config;
	}
	
	/**
	 * 保存网站配置
	 * @author guoshaobo
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public function saveWebSiteConfigCache($key, $value)
	{
		$redisModel = $this->getRedisModel();
		return $redisModel->saveWebSiteConfig($key, $value);
	}
	
	/**
	 * 获取配置类型
	 * 
	 * @return array 
	 */
	public function getCType(){
		return array(
				'int'		=>	'数字',
				'string'	=> 	'字符串',
				'array'		=> 	'数组',
				'class'		=>	'对象',
				'float'		=>	'浮点数'
			);
	}
	
	/**
	 * 获取主播报酬配置
	 * 	1.魅力点兑换比例公式 
	 * 	2.有效天数配置
	 * 
	 * @param DoteyService $doteySer
	 * @return array  
	 */
	public function getDoteyPayKey(DoteyService $doteySer){
		return array(
			//直营主播
			DOTEY_TYPE_DIRECT => array(
					'scale' => 'DOTEY_PAY_SCALE_'.DOTEY_TYPE_DIRECT,
					'effectDay' => 'DOTEY_PAY_EFFECTDAY_'.DOTEY_TYPE_DIRECT,
				),
			//代理主播
			DOTEY_TYPE_PROXY => array(
				'scale' => 'DOTEY_PAY_SCALE_'.DOTEY_TYPE_PROXY,
				'effectDay' => 'DOTEY_PAY_EFFECTDAY_'.DOTEY_TYPE_PROXY,
			),
			//全职主播
			DOTEY_TYPE_FULLTIME => array(
			'scale' => 'DOTEY_PAY_SCALE_'.DOTEY_TYPE_FULLTIME,
			'effectDay' => 'DOTEY_PAY_EFFECTDAY_'.DOTEY_TYPE_FULLTIME,
			),
		);
	}
	
	/**
	 * 检查值类型
	 * 
	 * @param mixed $ctype
	 * @param mixed $cvalue
	 * @return Ambigous <boolean, unknown>|Ambigous <boolean, string>|boolean
	 */
	public function checkCValue($ctype,$cvalue){
		switch ($ctype){
			case 'int':
				return is_int($cvalue)?$cvalue:false;
				break;
			case 'string':
				return is_string($cvalue)?$cvalue:false;
				break;
			case 'array':
			case 'class':
				return is_array($cvalue)?serialize($cvalue):false;
				break;
			case 'float':
				return is_float($cvalue)?$cvalue:false;
				break;
		}
	}
	
	/**
	 * 格式化值对象
	 * 
	 * @param array $row
	 */
	public function formatCValue(Array &$row){
		if ($row && isset($row['c_value']) && isset($row['c_type'])){
			switch ($row['c_type']){
				case 'array':
				case 'class':
					$row['c_value'] = unserialize($row['c_value']);
					break;
				case 'int':
					$row['c_value'] = intval($row['c_value']);
				case 'float':
					$row['c_value'] = floatval($row['c_value']);
				case 'string':
					$row['c_value'] = strval($row['c_value']);
					break;
			}
		}
	}
	
	/**
	 * 获取redisModel
	 * @author guoshaobo
	 * @return OtherRedisModel
	 */
	protected function getRedisModel()
	{
		if(self::$redisModel==null){
			self::$redisModel = new OtherRedisModel();
		}
		return self::$redisModel;
	}
	
	/**
	 * 获取首页强推KEY
	 * 
	 * @author supeng
	 * @return string
	 */
	public function getLivePushKey(){
		return 'WEB_LIVE_PUSH_TYPE';
	}
	
	public function getLivePush(){
		$cvalue = array();
		$cvalue['global'] = '';
		$cvalue['custom'] = '';
		
		$ckey = $this->getLivePushKey();
		$config = $this->getWebConfig($ckey);
		if($config){
			$value = intval($config['c_value']);
			if ($value == WEB_LIVE_PUSH_GLOGAL){
				$cvalue['global'] = WEB_LIVE_PUSH_GLOGAL;
			}else{
				if($this->hasBit($value, WEB_LIVE_PUSH_CUSTOM_ALL)){
					$cvalue['custom'][0] = WEB_LIVE_PUSH_CUSTOM_DOTEY;
					$cvalue['custom'][1] = WEB_LIVE_PUSH_CUSTOM_TODYARMD;
				}elseif ($this->hasBit($value, WEB_LIVE_PUSH_CUSTOM_DOTEY)){
					$cvalue['custom'][0] = WEB_LIVE_PUSH_CUSTOM_DOTEY;
				}elseif ($this->hasBit($value, WEB_LIVE_PUSH_CUSTOM_TODYARMD)){
					$cvalue['custom'][0] = WEB_LIVE_PUSH_CUSTOM_TODYARMD;
				}
			}
		}
		
		return $cvalue;
	}
	
	/**
	 * 获取礼物消息推送
	 * 
	 * @author supeng
	 * @return string
	 */
	public function getGiftMsgPushKey(){
		return 'WEB_GIFT_MSG_PUSH';
	}
	
	/**
	 * 获取频道标志key
	 * @author supeng
	 * @return string
	 */
	public function getChannelSymbolKey(){
		return 'CHANNEL_SYMBOL_MANAGE';
	}
	
	/**
	 * 获取频道标志
	 * @author supeng
	 * @return Ambigous <mix, boolean, unknown>
	 */
	public function getChannelSymbol(){
		return $this->getWebConfig($this->getChannelSymbolKey());
	}
	
	/**
	 * 获取广播设置key
	 * @author supeng
	 * @return string
	 */
	public function getBroadcastSetupKey(){
		return 'BROADCAST_SETUP';
	}
	
	/**
	 * 获取广播设置
	 * @author supeng
	 * @return Ambigous <mix, boolean, unknown>
	 */
	public function getBroadcastSetup(){
		return $this->getWebConfig($this->getBroadcastSetupKey());
	}
	
	public function getOnlineCount(){
		$otherRedisModel=new OtherRedisModel();
		$data=$otherRedisModel->getOnlieCount();
		return isset($data['all_total_online'])?$data['all_total_online']:0;
	}
	
	
}