<?php
class PurviewItemsForm extends PipiFormModel {
	
	public $purview_id;
	public $purview_name;
	public $group;
	public $module;
	public $controller;
	public $action;
	public $is_use;
	public $is_tree_display;
	public $range;
	
	public function checkRange($attribute,$params){
		if ($this->range){
			$purSer = new PurviewService();
			$ranges = array_keys($purSer->getPurviewRange());
			if(array_diff($this->range,$ranges)){
				$this->addError($attribute, '权限适用范围 &nbsp;&nbsp;值不匹配');
			}
		}
	}
	
	
	public function rules(){
		return array(
			array('purview_name,group', 'filter', 'filter'=>array(new CHtmlPurifier(),'purify')),
			array('purview_name,group,action,controller,is_tree_display,range', 'required'),
			array('purview_name,group', 'length', 'min'=>1, 'max'=>90),
			array('is_use', 'in', 'range'=>array(0,1)),
			array('range', 'checkRange'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'purview_id'	=> '权限ID',
			'purview_name'	=> '权限名称',
			'group'			=> 	'分组',
			'module'		=> '模块',
			'controller'	=> '控制器',
			'action'		=> '动作',
			'is_use'		=> '是否可用',
			'is_tree_display' => '菜单显示',
			'range'	=> '适用范围'
		);
	}
}