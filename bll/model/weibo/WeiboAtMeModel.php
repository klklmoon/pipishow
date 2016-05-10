<?php
/**
 * 微博之@我的微博数据访问层
 * 
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: WeiboAtMeModel.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package model
 * @subpackage weibo
 */
class WeiboAtMeModel extends PipiActiveRecord {

	public function tableName(){
		return '{{user_atme}}';
	}
	
	/**
	 * @param string $className
	 * @return MessageConfigModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
}

