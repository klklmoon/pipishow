<?php
/**
 * 回帖
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-8-6 下午5:50:59 hexin $ 
 * @package
 */
class PostForm extends PipiFormModel {
	public $thread_id;
	public $content;
	public $code;
	public $reply_post_id = 0;
	public $codeEnable = true;
	
	public function rules()
	{
		$rules = array(
			array('thread_id, content', 'required', 'message' => '回帖内容不完整'),
			array('content', 'filter', 'filter'=>array(new CHtmlPurifier(),'purify'))
		);
		if($this->codeEnable){
			$rules[] = array('code', 'required', message=>'请输入验证码');
			$rules[] = array('code', 'captcha');
		}
		return $rules;
	}
}
