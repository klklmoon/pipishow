<?php

/**
 * 频道数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: ChannelModel.php 9671 2013-05-06 13:51:21Z suqian $ 
 * @package model
 * @subpackage dotey
 */
class ChannelModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return ChannelModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{channel}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	public function rules(){
		return array(
			array('channel_name','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('channel_name','required'),
			array('channel_name','unique'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'channel_name'=>'频道名称',
		);
	}
	
	/**
	 * 删除频道信息
	 * 
	 * @param array $channelIds
	 * @return int
	 */
	public function delChannelByChannelIds(array $channelIds){
		if(empty($channelIds))
			return 0;
			
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('channel_id', $channelIds);
		return $this->deleteAll($criteria);
	}
	
	public function getAllParentChannel($channelId = '',$channelName = ''){
		$criteria = $this->getDbCriteria();
		if($channelId){
			$criteria->compare('channel_id', $channelId);
		}
		
		if($channelName){
			$criteria->compare('channel_name', $channelName);
		}
		
		return $this->findAll($criteria);
	}
	
}

