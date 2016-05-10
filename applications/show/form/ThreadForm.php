<?php
/**
 * 发帖
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午5:50:59 hexin $ 
 * @package
 */
class ThreadForm extends PipiFormModel {
	public $title;
	public $content;
	public $code;
	public $codeEnable = true;
	
	public function rules()
	{
		$rules = array(
			array('title', 'required', 'message'=>'标题不能为空'),
			array('title', 'length', 'min' => 2, 'max' => 35, 'message'=>'35字以内'),
			array('title,content', 'required', 'message'=>'发帖内容不完整'),
			array('title,content', 'filter', 'filter'=>array(new CHtmlPurifier(),'purify'))
		);
		if($this->codeEnable){
			$rules[] = array('code', 'required', message=>'请输入验证码');
			$rules[] = array('code', 'captcha');
		}
		return $rules;
	}
	
	public function attributeLabels(){
		return array(
			'title'		=> '标题',
			'content'	=> '内容'
		);
	}
}
