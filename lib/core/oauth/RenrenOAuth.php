<?php
/**
 * 人人开放平台登录
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class RenrenOAuth extends OAuth {

	protected $user = array();
	
	public function authorizeURL() {
		return 'https://graph.renren.com/oauth/authorize';
	}

	public function AccessTokenUrl() {
		return 'http://graph.renren.com/oauth/token';
	}


	public function checkSessionAccessToken() {
		$key = $this->open_platfrom . '_token';
		$accessToken = Yii::app()->session[$key];
		if (empty($accessToken) || $accessToken['expires_in'] + $accessToken['create_time'] < time()) {
			Yii::app()->request->redirect($this->getAuthorizeURL('code'));
		}
		return $accessToken;
	}

	public function writeTokenToSession(){
		$this->user = $this->access_token['user'];
		unset($this->access_token['user']);
		parent::writeTokenToSession();
	}
	
	public function processToken($response) {
		$response = json_decode($response,true);
		if (isset($response['error'])) {
			throw new OAuthException($response['error_description']);
		}
		return $response;
	}

	public function getAccessToken($type = 'code') {
		$this->client_id = $this->config['api_key'];
		return parent::getAccessToken($type);
	}

	

	public function getUserInfo() {
		if(!$this->user){
			//todo 请求用户信息
		}
		
		$returnUser['open_uid'] = $this->user['id'];
		$returnUser['open_id'] = $this->user['id'];
		$returnUser['nickname'] = $this->user['name'];
		return $returnUser;
	}


}

?>