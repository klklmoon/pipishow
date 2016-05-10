<?php
class GiftForm extends PipiFormModel {
	
	public $cat_id;
	public $zh_name;
	public $en_name;
	public $shop_type;
	public $gift_type;
	public $sort;
	public $image;
	public $pipiegg;
	public $charm;
	public $charm_points;
	public $dedication;
	public $egg_points;
	public $sell_nums;
	public $is_display;
	public $sell_grade;
	public $update_time;
	
	public $num;
	public $timeout;
	public $effect_type;
	public $position;
	public $effect;
	
	public function rules(){
		return array(
			array('zh_name,en_name,shop_type,gift_type,pipiegg,is_display,charm,charm_points,dedication,egg_points,sell_nums,sell_grade,sort', 'required'),
			array('num,timeout,effect_type,position', 'required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'cat_id'		=> '礼物分类',
			'zh_name'		=> '礼物名称',
			'en_name'		=> '礼物标识',
			'shop_type'		=> '商品类型',
			'gift_type'		=> '礼物类型',
			'image'			=> '礼物图片',
			'pipiegg'  		=> '皮蛋价格',
			'charm'  		=> '魅力值',
			'charm_points'  => '魅力点',
			'dedication'  	=> '贡献值',
			'egg_points'  	=> '皮点',
			'sell_num'  	=> '出售数量',
			'sell_grade'  	=> '出售等级',
			'update_time'  	=> '更新时间',
			'sort'  		=> '排序',
			'sell_nums'		=> '可售数量',

			'num'  			=> '效果数量',
			'timeout'  		=> '时长',
			'effect_type'  	=> '效果类型',
			'position'  	=> '显示位置',
			'effect'  		=> '动画文件',
		);
	}
	
}