<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class Safe360OAuth extends OAuth{
	
	public function authorizeURL() {
		return 'https://openapi.360.cn/oauth2/authorize';
	}

	public function AccessTokenUrl() {
		return 'https://openapi.360.cn/oauth2/access_token';
	}


	public function checkSessionAccessToken() {
		$key = $this->open_platfrom . '_token';
		$accessToken = Yii::app()->session[$key];
		if (empty($accessToken) || $accessToken['expires_in'] + $accessToken['create_time'] < time()) {
			Yii::app()->request->redirect($this->getAuthorizeURL('code'));
		}
		return $accessToken;
	}

	
	public function processToken($response) {
		$response = json_decode($response,true);
		if (isset($response['error_code'])) {
			throw new OAuthException($response['error']);
		}
		return $response;
	}


	public function getUserInfo(){
		$accessToken = $this->checkSessionAccessToken();
		$params['access_token'] = $accessToken['access_token'];
		$userApi = 'https://openapi.360.cn/user/me.json';
		$response = self::$http->oAuthRequest($userApi,'GET',$params);
		$response = json_decode($response,true);
		if (isset($response['error_code'])) {
			throw new OAuthException($response['error']);
		}
		$returnUser = array();
		if($response){
			$returnUser['open_uid'] = $response['id'];
			$returnUser['open_id'] = $response['id'];
			$returnUser['nickname'] = $response['name'];
		}
		return $returnUser;
		
	}
}

?>