<?php
/**
 * @author Su qian <aoxue.1988.su.qian@163.com> $date$
 * @link http://www.yiijob.com
 * @copyright Copyright &copy; 2003-2010 topchoice.com.cn
 * @license
 */

class userForm extends PipiFormModel {
	public $uname;
	public $password;
	public $nick;
	public $email;
	public $uid;
	
	public function rules()
	{
		return array(
			array('uname','required','message'=>'用户名不能为空'),
			array('uname','length', 'max'=>12,'min'=>6,'message'=>'用户名长度在6到20个字符之间'),
			array('password','required','message'=>'密码不能为空'),
			array('password','length','min'=>'6','max'=>'12','message'=>'密码长度在6到12之间'),
			array('nick','required','message'=>'用户昵称不能为空'),
			array('email','required','message'=>'邮件不能为空'),
			array('email','email','message'=>'邮件格式不正确'),
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'uname' => '用户姓名',
			'email' => '邮件',
			'password' => '用户密码',
			
		);
	}
}