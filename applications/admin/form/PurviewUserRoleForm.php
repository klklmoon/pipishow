<?php
class PurviewUserRoleForm extends PipiFormModel {
	
	public $uid;
	public $role_id;
	public $sub_id;
	public $role_name;
	public $role_type;
	public $is_use;
	public $username;
	
	public function rules() {
		return array(
			array('sub_id,uid','numerical','integerOnly'=>true),
			array('role_id,username','required'),
			array('username','checkUserName'),
			array('role_type','checkRange'),
		);
	}
	
	public function attributeLabels() {
		return array(
			'uid' => '用户ID',
			'role_id' => '角色ID',
			'is_use' => '是否可用',
			'sub_id'	=> '子系统ID',
			'role_name' => '角色名称',
			'role_type' => '角色类型',
			'username' => '用户名'
		);
	}
	
	public function checkRange($attribute,$params){
		if ($this->role_type){
			$purSer = new PurviewService();
			$ranges = array_keys($purSer->getPurviewRange());
			if(array_diff(array($this->role_type),$ranges)){
				$this->addError($attribute, '角色类型 &nbsp;&nbsp;值不匹配');
			}
		}
	}

	public function checkUserName($attribute,$params){
		if ($this->username){
			$userService = new UserService();
			if(!$userService->getVadidatorUser($this->username,0)){
				$this->addError($attribute, '用户名不在在');
			}
		}
	}
}