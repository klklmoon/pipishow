<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class SinaOAuth extends OAuth {

	public function authorizeURL() {
		return 'https://api.weibo.com/oauth2/access_token';
	}

	public function AccessTokenUrl() {
		return 'https://api.weibo.com/oauth2/authorize';
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
		$response = json_decode($response, true);
		if (isset($response['error_code'])) {
			throw new OAuthException($response['error']);
		}
		return $response;
	}

	public function getUserId() {
		$accessToken = $this->checkSessionAccessToken();
		$params['access_token'] = $accessToken['access_token'];
		$userApi = 'http://open.weibo.com/wiki/2/account/get_uid';
		$response = self::$http->oAuthRequest($userApi, 'GET', $params);
		$response = json_decode($response, true);
		return $response['uid'];
	}

	public function getUserInfo() {
		$accessToken = $this->checkSessionAccessToken();
		$sina_uid = $this->getUserId();
		$params['access_token'] = $accessToken['access_token'];
		$params['uid'] = $this->id_format($sina_uid);
		
		$userApi = 'http://open.weibo.com/wiki/2/users/show';
		$response = self::$http->oAuthRequest($userApi, 'POST', $params);
		$response = json_decode($response, true);
		$returnUser = array();
		if ($response) {
			$returnUser['open_uid'] = $sina_uid;
			$returnUser['open_id'] = $sina_uid;
			$returnUser['nickname'] = $response['screen_name'];
		}
		return $returnUser;
	
	}

	protected function id_format(&$id) {
		if (is_float($id)) {
			$id = number_format($id, 0, '', '');
		} elseif (is_string($id)) {
			$id = trim($id);
		}
		return $id;
	}
}

?>