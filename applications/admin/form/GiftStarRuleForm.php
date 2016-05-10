<?php
class GiftStarRuleForm extends PipiFormModel {
	
	public $rule_id;
	public $week_id;
	public $monday_date;
	public $gift_week_order;
	public $gift_id;
	public $contention_rule;
	public $create_time;
	
	public function rules(){
		return array(
			array('gift_id,contention_rule', 'required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'rule_id'		=> '规则id',
			'week_id'		=> '周编号',
			'monday_date'		=> '周一日期',
			'gift_week_order'		=> '周礼物序号',
			'gift_id'		=> '礼物id',
			'contention_rule'			=> '主播等级限制',
			'create_time'  		=> '创建时间',
		);
	}
	
}