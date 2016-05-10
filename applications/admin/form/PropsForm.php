<?php
class PropsForm extends PipiFormModel {
	
	public $prop_id;
	public $name;
	public $en_name;
	public $cat_id;
	public $pipiegg;
	public $image;
	public $charm;
	public $charm_points;
	public $status;
	public $rank;
	public $dedication;
	public $egg_points;
	public $sort;
	public $create_time;
	
	public function rules(){
		return array();
	}
	
	public function attributeLabels(){
		return array(
			'prop_id' 	=> 	'道具ID',
			'name'		=>	'道具名称',
			'en_name'	=>	'道具标识',
			'cat_id'	=>	'道具分类',
			'pipiegg'	=>	'价格',
			'image'		=>  '图片',
			'charm'		=>  '魅力值',
			'charm_points' => '魅力点',
			'status' 	=> 	'状态',
			'rank' 		=>	'获取等级',
			'dedication'=>	'贡献值',
			'egg_points'=> 	'皮点',
			'sort'		=> 	'排序',
			'create_time' => '创建时间'
		);
	}
	
	
}