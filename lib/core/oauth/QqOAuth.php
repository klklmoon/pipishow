<?php
/**
 * QQ开放平台操作
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class QqOAuth extends OAuth{
	
	public function authorizeURL() {
		return 'https://graph.qq.com/oauth2.0/authorize';
	}

	public function AccessTokenUrl() {
		return 'https://graph.qq.com/oauth2.0/token';
	}

	public function getOpenIdUrl(){
		return 'https://graph.qq.com/oauth2.0/me';
	}
	
	public function checkSessionAccessToken(){
		$key = $this->open_platfrom.'_token';
		$accessToken = Yii::app()->session[$key];
		if(empty($accessToken) || $accessToken['expires_in']+$accessToken['create_time'] < time()){
			Yii::app()->request->redirect($this->getAuthorizeURL('code'));
		}
		return $accessToken;
	}
	
	public function processToken($response){
		 if(strpos($response, "callback") !== false){
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);
            if(isset($msg->error)){
                throw new OAuthException($msg->error.": ".$msg->error_description);
            }
        }
        $params = array();
        parse_str($response, $params);
		return $params;
	}
	
	public function getOpenId(){
		$accessToken = $this->checkSessionAccessToken();
		$params['access_token'] = $accessToken['access_token'];
		$response = self::$http->oAuthRequest($this->getOpenIdUrl(),'GET',$params);
		if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
        }

        $open = json_decode($response);
        if(isset($open->error)){
            throw new OAuthException($msg->error);
        }
        return $open;
	}
	
	public function getUserInfo(){
		$open = $this->getOpenId();
		$accessToken = $this->checkSessionAccessToken();
		$params['access_token'] = $accessToken['access_token'];
		$params['oauth_consumer_key'] = $this->client_id;
		$params['format'] = $this->data_type;
		$params['openid'] = $open->openid;
		$userApi = 'https://graph.qq.com/user/get_user_info';
		$response = self::$http->oAuthRequest($userApi,'GET',$params);
		$response = json_decode($response,true);
		$response['openid'] = $open->openid;
		$returnUser = array();
		if($response){
			$returnUser['open_uid'] = 0;
			$returnUser['open_id'] = $open->openid;
			$returnUser['nickname'] = $response['nickname'];
		}
		return $returnUser;
		
	}
	
	
	public function getOpenIdByAccessToken($access_token){
		$params['access_token']=$access_token;
		$response = self::$http->oAuthRequest($this->getOpenIdUrl(),'GET',$params);
		if(strpos($response, "callback") !== false){
	
			$lpos = strpos($response, "(");
			$rpos = strrpos($response, ")");
			$response = substr($response, $lpos + 1, $rpos - $lpos -1);
		}
	
		$open = json_decode($response);
		if(isset($open->error)){
			throw new OAuthException($msg->error);
		}
		return $open;
	}
	
	public function getUserInfoByAccessToken($openid,$access_token){
		$params['access_token'] = $access_token;
		$params['oauth_consumer_key'] = $this->client_id;
		$params['format'] = $this->data_type;
		$params['openid'] = $openid;
		$userApi = 'https://graph.qq.com/user/get_user_info';
		$response = self::$http->oAuthRequest($userApi,'GET',$params);
		$response = json_decode($response,true);
		$response['openid'] = $openid;
		$returnUser = array();
		if($response){
			$returnUser['open_uid'] = 0;
			$returnUser['open_id'] = $openid;
			$returnUser['nickname'] = isset($response['nickname'])?$response['nickname']:'';
		}
		return $returnUser;
	}
	
	
}

?>