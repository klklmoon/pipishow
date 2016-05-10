<?php

/**
 * 节目封面表单验证
 *
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author Su qian <leiwei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z leiwei $
 * @package formModel
 */
class UserLoginForm extends PipiFormModel {
	public $avatar_big;
	protected $identity;

	public function rules()
	{
		return array(
			array('avatar_big','required','message'=>'节目封面不能为空'),
		);
	}

	public function attributeLabels(){
		return array(
			'avatar_big' => '节目封面',
		);
	}
}