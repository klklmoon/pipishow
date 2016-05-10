<?php
/**
 * 微博之用户微博访问层
 * 
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserWeiboModel.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package model
 * @subpackage message
 */
class UserWeiboModel extends PipiActiveRecord {

	public function tableName(){
		return '{{user_weibo}}';
	}
	
	/**
	 * @param string $className
	 * @return MessageConfigModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
}

