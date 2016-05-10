<?php
class PropsCategoryAttributeForm extends PipiFormModel {
	
	public $attr_id;
	public $cat_id;
	public $attr_name;
	public $attr_enname;
	public $is_display;
	public $attr_value;
	public $attr_type;
	public $is_multi;
	public $create_time;
	
	public function rules(){
		return array();
	}
	
	public function attributeLabels(){
		return array(
			'attr_id'	=> '属性标识',
			'cat_id'	=> '所属分类',
			'attr_name'	=> '属性名称',
			'attr_enname'=> '属性标识',
			'is_display'=> '是否显示',
			'attr_value'=> '属性值',
			'attr_type'  => '属性类型',
			'is_multi'  => '是否多选',
			'create_time'  => '创建时间',
		);
	}
	
}