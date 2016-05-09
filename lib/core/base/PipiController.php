<?php

/**
 *　皮皮乐天基础控制器层，所有应用控制器基类
 *
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su Qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PipiController.php 17828 2014-02-07 07:00:48Z hexin $
 * @package 
 */
class PipiController extends CController{
	
	public $layout='_main';

	/**
	 * @var array 视图层的变量容器
	 */
	public $viewer = array();
	
	/**
	 * Enter description here ...
	 * @var array
	 */
	public $breadcrumbs = array();
	
	/**
	 * @var string yii预定义核心JS、CSS库
	 */
	public  $coreFrontPath;
	
	/**
	 * @var string 皮皮乐天自定义核心JS、CSS库
	 */
	public  $pipiFrontPath;
	
	/**
	 * @var string 前端资源浏览器缓存清楚
	 */
	public  $hash;
	
	/**
	 * @var CClientScript
	 */
	public $cs;
	
	/**
	 * @var boolean 是否登录
	 */
	public $isLogin = false;
	
	/**
	 * 快播用户的uid和用户类型;
	 */
	public $tuli_user_info_type = 0;
	public $tuli_user_info_uid = 0;
	
	/**
	 * @var UserService
	 */
	protected $userService;
	
	/**
	 * @var OperateService
	 */
	protected $operateService = null;
	
	/**
	 * @var WebConfigService
	 */
	protected $webConfigService = null;
	
	protected $target = '';
	
	protected $isDotey = 0;
	/**
	 * 
	 * @var string 页面关键字
	 */
	protected $keywords = '';
	/**
	 * @var string 页面描述
	 */
	protected $description = '';
	/**
	 * @var string 页面位置
	 */
	protected $scr = '';
	
	/**
	 * @var 直播页面的主播ID
	 */
	protected $doteyId = '';
	/**
	 * @var string 域名类类型 比如show.pipi.cn 取show
	 */
	protected $domain_type = '';
	protected $token = '';
	
	protected $isPipiDomain = false;
	
	public function __construct($id,$module){
		parent::__construct($id,$module);
	}
	
	public function init(){
		$this->hash = sprintf('%x',crc32(SOFT_VERSION));
		$this->isLogin = !Yii::app()->user->isGuest;
		$this->userService = new UserService();
		$this->operateService =  new OperateService();
		$this->webConfigService = new WebConfigService();
		$hrefTarget = Yii::app()->request->getParam('target','_blank');
		$this->target = in_array($hrefTarget,array('_blank','_self','_parent','_self')) ? $hrefTarget : '_blank';
		$this->initHeader();
		/* @var $clientScript CClientScript */
		$clientScript = Yii::app()->getClientScript();
		$this->cs = $clientScript;
		$this->registerCoreJsCss($clientScript);
		$this->registerPiPiJsCss($clientScript);
	}
	
	/**
	 * 注册Yii定义的通用JS、CSS类库
	 * 
	 * @param CClientScript $cs
	 */
	public function registerCoreJsCss(CClientScript $cs){
		$coreFrontPath = $cs->getCoreScriptUrl();
		$cs->registerCoreScript('jquery');
		$cs->registerCoreScript('cookie');
		$this->coreFrontPath = $coreFrontPath;
	}
	
	/**
	 * 此动作执行在init()方法之前
	 * @see CController::beforeAction()
	 */
	public function beforeAction($action){
		//取得直播间广告
		if($this->getId() == 'archives' && $action->getId() == 'index'){
			$this->doteyId = Yii::app()->request->getParam('uid');
		}
		return true;
	}
	
	/**
	 * 注册Yii定义的通用Css类库
	 * 
	 * @param CClientScript $cs
	 */
	public function registerPiPiJsCss(CClientScript $cs){
		
		/* @var $assetManager CAssetManager */
		$assetManager = Yii::app()->getAssetManager();
		$assetManager->excludeFiles = array('.svn','.gitignore','images','admin');
		//无需发布静态文件，直接访问即可，提高页面访问效率
// 		$pipiFrontPath = $assetManager->publish(Yii::getPathOfAlias('root.statics'));
		$pipiFrontPath = $this->getHost().'/statics';
		if($this->layout=='_main'){
			$cs->registerScriptFile($pipiFrontPath.'/js/global/common.js?token='.$this->hash,CClientScript::POS_HEAD);
			$cs->registerScriptFile($pipiFrontPath.'/js/global/validform.js?token='.$this->hash,CClientScript::POS_HEAD);
			$cs->registerCssFile($pipiFrontPath.'/css/global/global.css?token='.$this->hash,'all');
			$cs->registerCssFile($pipiFrontPath.'/css/global/common.css?token='.$this->hash,'all');
			$cs->registerCssFile($pipiFrontPath.'/css/global/dialog.css?token='.$this->hash,'all');
		}else{
			$cs->registerScriptFile($pipiFrontPath.'/js/common/common.js?token='.$this->hash,CClientScript::POS_HEAD);
			$cs->registerCssFile($pipiFrontPath.'/css/common/global.css?token='.$this->hash,'all');
			$cs->registerCssFile($pipiFrontPath.'/css/common/dialog.css?token='.$this->hash,'all');
		}
		$cs->registerScriptFile($pipiFrontPath.'/js/common/jquery.form.js?token='.$this->hash,CClientScript::POS_END);
		$cs->registerScriptFile($pipiFrontPath.'/js/common/jquery.lazyload.min.js?token='.$this->hash,CClientScript::POS_END);
		$cs->registerScriptFile($pipiFrontPath.'/js/common/jquery.md5.js?token='.$this->hash,CClientScript::POS_END);
		$cs->registerScriptFile($this->createUrl('user/attribute'),CClientScript::POS_HEAD);
		if($this->domain_type == 'pptv'){
			$cs->registerScriptFile( Yii::app()->params['pptv']['login_js'].'?token='.$this->hash,CClientScript::POS_END);
		}elseif($this->domain_type == 'tuli'){
			$cs->registerScriptFile( Yii::app()->params['tuli']['load_js'].'?token='.$this->hash,CClientScript::POS_END);
		}
		$this->pipiFrontPath = $pipiFrontPath;
	}
	
	/**
	 * 获取静态文件的域名
	 * @return string
	 */
	private function getHost(){
		$config = Yii::app()->params['images_server'];
		$host = '';
		if($config['cdn_open']){
			if(SOFT_VERSION != 'v1.0.0.1'){
				$time = strtotime('20'.SOFT_VERSION.'00');
				if(DEV_ENVIRONMENT == 'release' && time() - $time > 3 * 3600) $host = $config['cdn_url'];
			}
		}
		return $host;
	}
	
	public function initHeader(){
		$currentPageUrl = Yii::app()->request->getHostInfo().Yii::app()->request->getRequestUri();
		//初始化登陆用户的信息
		if($this->isLogin){
			//这里取数据先后顺序不能变动
			$uid = Yii::app()->user->id;
			$this->viewer['login_uid'] =  $uid;
			$this->viewer['login_name'] =  Yii::app()->user->name;
			$this->viewer['user_basic'] = $this->userService->getVadidatorUser(Yii::app()->user->name,'username');
			$this->isDotey = $this->userService->hasBit((int)$this->viewer['user_basic']['user_type'],USER_TYPE_DOTEY);
			$this->viewer['user_attribute'] = $this->userService->getUserFrontsAttributeByCondition($uid,true,$this->isDotey);
			$this->viewer['avatar_s'] = $this->userService->getUserAvatar($uid,'small',$this->viewer['user_attribute']);	
					
			if($this->viewer['user_attribute']){
				$car = $this->getUserJsonAttribute('car');
				$vip = $this->getUserJsonAttribute('vip');
				$propsAttribute = $userJson = array();
				$timeStamp = time();
				if($car && is_array($car) && $car['vt'] >0 && $car['vt'] < $timeStamp){
					$propsAttribute['car'] = 0;
					$userJson['car'] = '';
				}
				if($vip && is_array($vip) && $vip['us']!=1 && $vip['vt'] >0 && $vip['vt'] < $timeStamp){
					$propsAttribute['vip'] = 0;
					$propsAttribute['is_hidden'] = 0;
					$userJson['vip'] = '';
				}
				//座驾和VIP过期处理
				if($propsAttribute){
					$propsAttribute['uid'] = $uid;
					$userPropsService = new UserPropsService();
					$userJsonInfoService = new UserJsonInfoService();
					$zmq = $userJsonInfoService->getZmq();

					$userPropsService->saveUserPropsAttribute($propsAttribute);
					$userJsonInfoService->setUserInfo($uid,$userJson);
					$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$uid,'json_info'=>$userJson));
				}
			}
			//账号被封停 注销
			if($this->viewer['user_basic']['user_status'] == 1){
				Yii::app()->user->logout();
				$this->redirect($currentPageUrl);
			}
		}else{
			$this->viewer['avatar_s'] = '';
			$this->viewer['user_attribute'] = array();
			$this->viewer['user_basic'] = array();
			$this->viewer['login_uid'] = '';
			$this->viewer['login_name'] = '';
		}
		
		//初始化对外合作的信息
		$urls = parse_url($currentPageUrl);
		$hosts = explode('.',$urls['host']);
		$this->domain_type = strtolower($hosts[0]);
		// 添加cookies 暂行办法
		if($this->domain_type == 'co'){
			$from = Yii::app()->request->getParam('from');
			$token = Yii::app()->request->getParam('token');
			if ($from){
				setcookie('_from',$from,0,'');
				$this->domain_type = $from;
			}
			
			if(isset($_COOKIE['_from'])){
				$this->domain_type = $_COOKIE['_from'];
			}
			
			if ($token){
				setcookie('_token',$token,0,'');
				$this->token = $token;
			}
			
 			if(isset($_COOKIE['_token'])){
 				$this->token = $_COOKIE['_token'];
 			}
		}
		
		if($this->domain_type == 'tuli' && $this->isLogin){
			$user_info = $this->userService->getUserOuthInfo(array($uid));
			$_tmp = explode('_',$user_info[$uid]['openid']);
			$this->tuli_user_info_type = $_tmp[1];
			$this->tuli_user_info_uid = $_tmp[2];
		}
		if(in_array($this->domain_type,array('show','l','letian','1'))){
			$this->isPipiDomain = true;
		}
		$this->setPageTitle(Yii::t('seo','seo_default_title'));
	}
	
	/**
	 * 全局页面数据，仅在输出页面时用到，即页首导航及页尾的公共数据
	 */
	protected function beforeRender($view){
		//页头通栏广告
		$topHeadBannel = $this->operateService->getTopBannerAdv($this->doteyId,$this->getId(), $this->getAction()->getId());
		if($topHeadBannel){
			$topHeadBannel['piclink'] = $this->operateService->getOperateUrl().$topHeadBannel['piclink'];
			$topHeadBannel['target'] = $topHeadBannel['textlink'] ?  $this->target : '_self';
			$topHeadBanner['href'] = $topHeadBannel['textlink'] ? ' href= "'.$this->getTargetHref($topHeadBannel['textlink'],false,true).'"' : '';
		}else $topHeadBannel = array();
		$this->viewer['topHeadBanner'] = $topHeadBannel;
		
		//导航菜单
		$this->viewer['topNavigate'] = $this->operateService->getNavigate();
		
		//客服数据
		$keFuList = $this->operateService->getAllKefuFromCache();
		$qqKeFu = $qq = array();
		$kfflag = array(KEFU_QQ_WORK,KEFU_QQ_FAMILY,KEFU_QQ_TEC_SUPPORT,KEFU_QQ_DOTEY,KEFU_QQ_PROXY_RECRUIT,KEFU_QQ_SUGGEST);
		foreach($keFuList as $kf){
			if($kf['contact_type'] == KEFU_QQ){
				$qq[$kf['kefu_type']][] = $kf;
			}
		}
		foreach($kfflag as $flag){
			$typeName = $this->operateService->getKefuType($flag);
			$qqKeFu[$typeName] = isset($qq[$flag]) ? $qq[$flag] : array();
		}
		$this->viewer['qqKeFu'] = $qqKeFu;
		return true;
	}
	
	/**
	 * 取得合法的用户属性
	 * 
	 * @param string $attribute 用户属性
	 * @param boolean $isReturn 是echo还是return
	 * @param boolean $isZero 返回给调用者的是空字符串还是0
	 * @return mixed
	 */
	protected function getUserJsonAttribute($attribute,$isReturn = true,$isZero = true){
		if(!isset($this->viewer['user_attribute'])){
			return array();
		}
		$attributes = $this->viewer['user_attribute'];
		if(empty($attributes)){
			return array();
		}
		
		if(empty($attribute)){
			return $attributes;
		}
		
		if(isset($attributes[$attribute])){
			if($isReturn)
				return $attributes[$attribute];
			else 
				echo $attributes[$attribute];
		}else{
			if($isReturn){
				return $isZero ? 0 : '';
			}else{
				echo $isZero ? 0 : '';;
			}
		}
		return null;
	}
	

	/**
	 * 取得用户真实的皮蛋数
	 * 
	 * @param boolean $isReturn
	 * @return int
	 */
	protected function getUserPipieggs($isReturn = false){
		$attributes = $this->viewer['user_attribute'];
		if(isset($attributes['pe'])){
			if(isset($attributes['fe'])){
				$pipiegg = $attributes['pe'] - $attributes['fe'];
			}else{
				$pipiegg = $attributes['pe'];
			}
		}else{
			$pipiegg = 0;
		}
		if($isReturn)
		 	return $pipiegg;
		else
		 	echo $pipiegg;
	}
	
	/**
	 * 链接处理
	 * 
	 * @param $href 原始链接
	 * @param $isRewrite 是否重写
	 * @param $isReturn 是否返回
	 * @return string 返回处理的链接
	 */
	public function getTargetHref($href,$isRewrite = false,$isReturn = false){
		$href = $this->_getTargetHref($href,$isRewrite,$isReturn);
		if($this->domain_type == 'pptv'){
			$params = Yii::app()->params[$this->domain_type];
			$href = urlencode($href);
			if(strrpos($href,'http') !== false){
				$href = $params['pptv_url'].$href;
			}else{
				$href = $params['pptv_url'].$params['main_url'].'/'.$href;
			}
		}elseif($this->domain_type == 'tuli' || Yii::app()->request->getParam('from')=='tuli'){
			$params = Yii::app()->params['tuli'];
			if(strrpos($href,'http') !== false){
				$href = urlencode($href);
				$href = $params['tuli_url'].$href;
			}else{
				$href = urlencode($params['main_url'].'/'.$href);
				$href = $params['tuli_url'].$href;
			}

		}
		
		if($isReturn){
			return $href;
		}
		echo $href;
	}
	/**
	 * 本站链接处理
	 * 
	 * @param unknown_type $href 链接地址
	 * @param unknown_type $isRewrite 是否URL重写
	 * @return string
	 */
	public function _getTargetHref($href,$isRewrite = false){
		if(empty($href)){
			return '#';
		}
		if(!Yii::app()->request->getParam('target')){
			return $href;
		}
		if(!$isRewrite){
			if(strrpos($href,'?') !== false){
				$href .= '&target='.$this->target;
			}else{
				$href .= '?target='.$this->target;
			}
			return $href;
		}else{
			return $href .= '&target='.$this->target;
		}
	}
	
	public function searchCondition(array &$queryCondition = array()){
		$queryString = Yii::app()->request->getQueryString();
		parse_str($queryString,$queryCondition);
		return http_build_query($queryCondition);
	}
	
	/**
	 * 获取网站统计代码
	 * @edit by guoshaobo
	 */
	public function webSiteCount()
	{
		$config = $this->webConfigService->getWebConfig(WEB_SITE_COUNT);
		if(isset($config['c_value']) && isset($config['c_value']['content'])){
			return $config['c_value']['content'];
		}
		return '';
	}
	
	public function setPageKeyWords($keywords){
		$this->keywords = $keywords;
	}
	
	public function getPageKeyWords(){
		if($this->keywords == ''){
			$this->keywords = Yii::t('seo','seo_default_keywords');
		}
		return $this->keywords;
	}
	
	public function setPageDescription($description){
		$this->description = $description;
	}
	
	public function getPageDescription(){
		if($this->description == ''){
			$this->description = Yii::t('seo','seo_default_description');
		}
		return $this->description;
	}
	/**
	 * 统一josn格式的标准输出
	 * @author hexin
	 * @param int $status 状态码, 1为成功
	 * @param string $message 提示信息，即出错信息
	 * @param array $data 输出数据
	 */
	public function renderToJson($status, $message = '', array $data = array()){
		echo json_encode(array('status' => intval($status), 'message' => $message, 'data' => $data));
		Yii::app()->end();
	}
	
	/**
	 * 获取支付平台验证地址
	 * @author hexin
	 * @return string
	 */
	public function goExchange(){
		if($this->isLogin){
			if($this->isPipiDomain){
				$uid = Yii::app()->user->id;
				$username = Yii::app()->user->name;
				$time = time();
				return Yii::app()->params['exchange'].'?act=login&id='.$uid.'&v='.md5('login'.$uid.$username.Yii::app()->params['verification_code']);
			}elseif($this->domain_type == 'pptv'){
				return Yii::app()->params['pptv']['recharge_url'];
			}elseif($this->domain_type == 'tuli'){
				$uid = Yii::app()->user->id;
				$username = Yii::app()->user->name;
				$time = time();
				return Yii::app()->params['exchange'].'?act=login&id='.$uid.'&v='.md5('login'.$uid.$username.Yii::app()->params['verification_code']);
				//return $this->getTargetHref(Yii::app()->params['exchange'].'?act=login&id='.$uid.'&v='.md5('login'.$uid.$username.Yii::app()->params['verification_code']),false, true);
			}
		}else return '#';
	}

	
	/**
	 * 中文截字
	 * @return Ambigous <unknown, string>
	 */
	public function cutstr($string, $length, $etc = ''){
		$result = '';
		$string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
		$strlen = strlen($string);
		for ($i = 0; (($i < $strlen) && ($length > 0)); $i++){
			if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')){
				if ($length < 1.0){
					break;
				}
				$result .= substr($string, $i, $number);
				$length -= 1.0;
				$i += $number - 1;
			}else{
				$result .= substr($string, $i, 1);
				$length -= 0.5;
			}
		}
		$result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
		if ($i < $strlen){
			$result .= $etc;
		}
		return $result;
	}

}
