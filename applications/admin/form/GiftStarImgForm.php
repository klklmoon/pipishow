<?php
class GiftStarImgForm extends PipiFormModel {
	
	public $img_id;
	public $gift_id;
	public $image;
	public $order_number;
	public $summary;
	public $create_time;
	
	public function rules(){
		return array(
			array('gift_id,order_number', 'required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'img_id'		=> '图片记录id',
			'gift_id'		=> '礼物编号',
			'image'		=> '图片文件名',
			'order_number'		=> '图片序号',
			'summary'		=> '图片描述',
			'create_time'  		=> '创建时间',
		);
	}
	
}