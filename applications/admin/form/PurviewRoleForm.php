<?php
class PurviewRoleForm extends PipiFormModel {
	
	public $role_id;
	public $role_name;
	public $role_type;
	public $sub_id;
	public $is_use;
	public $description;
	public $groups_items;
	
	public function rules(){
		return array(
			array('role_name,description', 'filter', 'filter'=>array(new CHtmlPurifier(),'purify')),
			array('role_name,role_type,sub_id,description', 'required'),
			array('role_name', 'length', 'min'=>1, 'max'=>90),
			array('is_use', 'in', 'range'=>array(0,1)),
			array('role_type', 'checkRange'),
			array('description', 'length', 'min'=>0, 'max'=>255),
		);
	}
	
	public function attributeLabels(){
		return array(
			'role_name'		=> '角色名称',
			'role_type'		=> '角色类型',
			'description'	=> '角色描述',
			'is_use'		=> '是否可用',
			'role_id'		=> '角色标识',
			'sub_id'		=> '作用ID',
			'groups_items'  => '权限分组',
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
}