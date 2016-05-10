<?php

/**
 * 用户登录表单验证
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package formModel
 * @subpackage user
 */
class UserLoginForm extends PipiFormModel {
	public $username;
	public $password;
	public $code;
	public $is_code = true;
	public $apiPassword = false;
	protected $identity;
	
	public function rules()
	{
		$rules = array(
			array('username','required','message'=>'用户名不能为空'),
			array('password','required','message'=>'密码不能为空'),
			array('password','authenticate','message'=>'错误的用户名或密码'),
		
		);
		if($this->is_code){
			$rules[] = array('code', 'captcha', 'allowEmpty'=>true);
		}
		return $rules;
	}
	
	public function authenticate($attribute,$params){
       $this->identity = new PipiUserIdentity($this->username,$this->password);
       $this->identity->api = $this->apiPassword;
	    if( $this->identity->authenticate())
	        return true;
	    else
	        $this->addError('password',$this->identity->errorMessage); 
    }
    
    public function getIdentity(){
    	return $this->identity;
    }
    
	public function attributeLabels(){
		return array(
			'username' => '用户姓名',
			'password' => '用户密码',
			'code' => '验证码',
			
		);
	}
}