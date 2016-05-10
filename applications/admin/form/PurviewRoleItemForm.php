<?php
class PurviewRoleItemForm extends PipiFormModel {
	
	public $relation_id;
	public $role_id;
	public $purview_id;
	public $is_use;

	public $purview_name;
	public $group;
	public $is_tree_display;
	
	public $role_name;
	public $role_type;
	public $sub_id;
	
	public function rules(){
		return array(
			array('role_id,purview_id,is_use', 'required'),
			array('is_use', 'in', 'range'=>array(0,1)),
		);
	}
	
	public function attributeLabels(){
		return array(
			'relation_id'	=> '关系ID',
			'purview_id'	=> '权限ID',
			'is_use'		=> '是否可用',
			'role_id'		=> '角色ID',
		);
	}
}