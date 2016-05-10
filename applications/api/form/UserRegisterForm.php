<?php

/**
 * 用户注册表单model
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class UserRegisterForm extends PipiFormModel {
	public $username;
	public $nickname;
	public $password;
	public $confirm_password;
	public $code;
	public $reg_ip;
	public $is_code = true;
	
	public function rules()
	{
		$rules = array(
			array('username','required','message'=>'账号不能为空'),
			array('nickname','required','message'=>'昵称不能为空'),
			array('password','required','message'=>'密码不能为空'),
			array('confirm_password','required','message'=>'确认密码不能为空'),
			array('username','length', 'max'=>15,'min'=>4,'message'=>'账号长度在4到16个字之间'),
			array('nickname','length', 'max'=>20,'min'=>2,'message'=>'账号长度在2到16个字符之间'),
			//array('password','length','max'=>20,'min'=>4,'message'=>'密码长度在6到12之间'),//网站是md5过来的密码，这个只能由第三方判断
			array('confirm_password','compare','compareAttribute'=>'password','message'=>'确认密码和原始密码不一样'),
			array('reg_ip','validateRegIP'),
			array('nickname','validateNickName'),
			
		);
		if($this->is_code){
			$rules[] = array('code', 'captcha', 'allowEmpty'=>true);
		}
		return $rules;
	}
	
	public function attributeLabels()
	{
		return array(
			'username' => '账号',
			'nickname' => '昵称',
			'password' => '密码',
			'confirm_password' => '确认密码',
			'code' => '验证码',
			'reg_ip'=>'注册IP',
			
		);
	}
	
	public function validateNickName($attribute,$params){
		$guestExp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8|\xE2\x80\xAD|\xE2\x80\xAE|\xE2\x80\xAA';
		$guestExp.='|\xE2\x80\xAB|\xE2\x80\xAC|\xE2\x80\xAF|\xEF\xA3\xB5|\xE2\x80|\xE2\x81|\x2A|\xEE\xA0|\xC2\xAD|\x7F|\xE3\x80\x80';
		$guestExp.='|\x1E\x1F|\x1E\x1E|\x1F\x1F';
		$patrnStr="/\s+|\{|\}|\||\;|\:|\'|^c:\\con\\con|(&\d{2})|(%\d{2})|[%&,\*\"\s\<\>\|\\\[\]\/\?\^\+`~]|".$guestExp."/is";
		
	
		if(preg_match($patrnStr, $this->nickname)){
			return $this->addError($attribute,'昵称中包含特殊字符');
		}
		return true;
	}
	
	public function validateRegIP($attribute,$params){
		$weiboConfigService = new WebConfigService();
		$blackip_config = $weiboConfigService->getWebConfig(WEB_BAD_IP);
		$redister_config = $weiboConfigService->getWebConfig(WEB_REGISTER_SITE);
		if(empty($blackip_config) && empty($redister_config)){
			return true;
		}
		if($blackip_config && $this->reg_ip){
			$black_ip = array_flip($blackip_config['c_value']);
			if(isset($black_ip[$this->reg_ip])){
				return $this->addError($attribute,'您所在的IP地址被限制注册了');
			}
		}
		if($redister_config &&  $this->reg_ip){
			$registerConfig = $redister_config['c_value'];
			$minute = isset($registerConfig['minute']) ? (int)$registerConfig['minute'] : 0;
			$rate = isset($registerConfig['rate']) ? (int)$registerConfig['rate'] : 0;
			if($minute > 0 && $rate > 0){
				$userBasicModel = UserBasicModel::model();
				$time = time() - $minute*60;
				$registerCount = $userBasicModel->countIpReister($this->reg_ip,$time);
				if($registerCount >= $rate){
					 return $this->addError($attribute,'您注册过于频繁，请稍后在注册');
				}
			}
		}
		return true;
	}
}