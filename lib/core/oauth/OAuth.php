<?php
/**
 * 开放平台
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
abstract class OAuth extends CApplicationComponent {
	/**
	 * @var string 返回的数据类型
	 */
	public $data_type = 'json';
	
	/**
	 * @var int 第三方APPID
	 */
	protected $client_id;
	/**
	 * @var string 第三方KEY
	 */
	protected $client_secret;
	/**
	 * @var string 访问令牌
	 */
	protected $access_token;
	/**
	 * @var string 刷新令牌
	 */
	protected $refresh_token;
	/**
	 * @var string 开放平台类型
	 */
	protected $open_platfrom;
	/**
	 * @var OAuthHttp
	 */
	protected static $http;
	/**
	 * @var array 开放平台注册配置
	 */
	protected $config;

	public function __construct() {
		if (self::$http == null) {
			self::$http = new OAuthHttp();
		}
		$className = get_class($this);
		if(!strpos($className,'OAuth')){
			throw new OAuthException('类的继承方式不对');
		}
		$this->open_platfrom = strtolower(strtr($className,array('OAuth'=>'')));
		$open = Yii::app()->params['open'];
		if(empty($open) || !isset($open[$this->open_platfrom])){
			throw new OAuthException('不支持该开放平台');
		}
		
		$open = $open[$this->open_platfrom];
		$this->client_id = $open['client_id'];
		$this->client_secret = $open['client_secret'];
		$this->config =  $open;
	}

	public function authorizeURL() {}

	public function AccessTokenUrl() {}

	public function getAuthorizeRedirectUrl($response_type = 'code'){
		if($response_type == 'code'){
			return Yii::app()->request->getHostInfo().'/index.php?r=user/openLogin&type='.$this->open_platfrom;
		}
	}
	
	
	public function processToken($response){
		return $response;
	}
	
	public function writeTokenToSession(){
		if(empty($this->access_token) || !isset($this->access_token['access_token'])){
			throw new OAuthException('token is error');
		}
		$key = $this->open_platfrom.'_token';
		$this->access_token['create_time'] = time();
		Yii::app()->session[$key] = $this->access_token;
	}
	
	
	public function getUserInfo(){}
	public function checkSessionAccessToken(){}
	
	public function getUserInfoByAccessToken($access_token){}
	/**
	 * authorize接口
	 *
	 * @param string $response_type 支持的值包括 code、token 默认值为code
	 * @param string $state 用于保持请求和回调的状态。在回调时,会在Query Parameter中回传该参数
	 * @param string $display 授权页面类型 可选范围 详情见具体的各个开放平台
	 * @return string
	 */
	public function getAuthorizeURL($response_type = 'code', $state = NULL, $display = NULL) {
		$params = array();
		$params['client_id'] = $this->client_id;
		$params['redirect_uri'] = $this->getAuthorizeRedirectUrl($response_type);
		$params['response_type'] = $response_type;
		$params['state'] = $state;
		$params['display'] = $display;
		return $this->authorizeURL() . "?" . http_build_query($params);
	}

	/**
	 * 换取access_token接口
	 *
	 * @param string $type 请求的类型,可以为:code,token
	 * @return array
	 */
	public function getAccessToken($type = 'code') {
		$params = array();
		$params['client_id'] = $this->client_id;
		$params['client_secret'] = $this->client_secret;
		if ($type === 'token') {
			$params['grant_type'] = 'refresh_token';
			$params['refresh_token'] = $this->getRefershToken();
		} elseif ($type === 'code') {
			$params['grant_type'] = 'authorization_code';
			$params['code'] = Yii::app()->request->getParam('code');
			$params['redirect_uri'] =  $this->getAuthorizeRedirectUrl();
		}else {
			throw new OAuthException("wrong auth type");
		}
		$response = self::$http->oAuthRequest($this->accessTokenURL(), 'POST', $params);
		$this->access_token = $this->processToken($response);
		$this->writeTokenToSession();
		return $this->access_token;
	}
	
	public function getOpenPlatform(){
		return $this->open_platfrom;
	}
	
}

/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class OAuthException extends Exception {
	
}

class OAuthHttp {


	/**
	 *@var int Http链接超时时间
	 */
	public $timeout = 30;

	/**
	 * @var int Http链接超时时间
	 */
	public $connecttimeout = 30;

	/**
	 * @var boolean 是否https认证
	 */
	public $ssl_verifypeer = FALSE;
	/**
	 * @var string 客户端请求代理
	 */
	public $useragent = 'Sae T OAuth2 v0.1';
	
	/**
	 * @var string 用户请求IP
	 */
	public $remote_ip = '';
	/**
	 * @var string HTTP头信息
	 */
	public $http_info;
	
	/**
	 * @var string HTTP状态码
	 */
	public $http_code;
	
	/**
	 * @var string boundary of multipart多用于附件上传
	 */
	public static $boundary = '';
	
	/**
	 * 返回一个HTTP链接
	 * 
	 * @param $url
	 * @param $method
	 * @param $parameters
	 * @param $multi
	 * @param $accessToken
	 * @return array
	 */
	function oAuthRequest($url, $method, $parameters, $multi = false,$accessToken = NULL) {

		switch ($method) {
			case 'GET':
				$url = $url . '?' . http_build_query($parameters);
				return $this->http($url, 'GET',null,array(),$accessToken);
			default:
				$headers = array();
				if (!$multi && (is_array($parameters) || is_object($parameters))) {
					$body = http_build_query($parameters);
				} else {
					$body = $this->build_http_query_multi($parameters);
					$headers[] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
				}
				return $this->http($url, $method, $body, $headers,$accessToken);
		}
	}
	
	/**
	 * 建立一个由curl发起的HTTP请求链接
	 *
	 * @return array
	 */
	function http($url, $method, $postfields = NULL, $headers = array(),$accessToken = NULL) {
		$this->http_info = array();
		$ci = curl_init();
		/* Curl settings */
		curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent ? $this->useragent : $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_ENCODING, "");
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
		curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, 1);
		curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);

		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($postfields)) {
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
					$this->postdata = $postfields;
				}
				break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if (!empty($postfields)) {
					$url = "{$url}?{$postfields}";
				}
		}

		if ($accessToken)
			$headers[] = "Authorization: OAuth2 ".$accessToken;

		if ( !empty($this->remote_ip) ) {
			if ( defined('SAE_ACCESSKEY') ) {
				$headers[] = "SaeRemoteIP: " . $this->remote_ip;
			} else {
				$headers[] = "API-RemoteIP: " . $this->remote_ip;
			}
		} else {
			if ( !defined('SAE_ACCESSKEY') ) {
				$headers[] = "API-RemoteIP: " . $_SERVER['REMOTE_ADDR'];
			}
		}
		curl_setopt($ci, CURLOPT_URL, $url );
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );

		$response = curl_exec($ci);
		$this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		$this->http_info = array_merge($this->http_info, curl_getinfo($ci));
		curl_close ($ci);
		return $response;
	}
	
	public  function build_http_query_multi($params) {
		if (!$params) return '';

		uksort($params, 'strcmp');

		$pairs = array();

		self::$boundary = $boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		$multipartbody = '';

		foreach ($params as $parameter => $value) {

			if( in_array($parameter, array('pic', 'image')) && $value{0} == '@' ) {
				$url = ltrim( $value, '@' );
				$content = file_get_contents( $url );
				$array = explode( '?', basename( $url ) );
				$filename = $array[0];

				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
				$multipartbody .= "Content-Type: image/unknown\r\n\r\n";
				$multipartbody .= $content. "\r\n";
			} else {
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
				$multipartbody .= $value."\r\n";
			}

		}

		$multipartbody .= $endMPboundary;
		return $multipartbody;
	}
	
	public function getHeader($ch, $header) {
		$i = strpos($header, ':');
		if (!empty($i)) {
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
			$this->http_header[$key] = $value;
		}
		return strlen($header);
	}
}

?>