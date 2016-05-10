<?php

/**
 * 地区与频道关系数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: AreaChannelModel.php 10354 2013-05-16 10:23:23Z supeng $ 
 * @package model
 * @subpackage dotey
 */
class AreaChannelModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return AreaChannelModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{area_channel}}';
	}
	
	public function getDbConnection(){
		return Yii::app()->db_common;
	}
	
	/**
	 * 获取所有获取频道
	 * 
	 * @author supeng
	 * @param array $condition
	 */
	public function getAllAreaChannel($condition = array()){
		$criteria = $this->getDbCriteria();
		$criteria->alias = 'a';
		$criteria->select = 'a.area_relation_id,a.province,a.city,b.sub_channel_id,b.sub_name,c.channel_name,c.channel_id';
		$criteria->join = ' LEFT JOIN web_channel_sub b ON b.sub_channel_id = a.area_channel_id LEFT JOIN web_channel c ON c.channel_id = b.channel_id ';
		
		if(isset($condition['channel_id'])){
			$criteria->condition .= ' c.channel_id = :channel_id ';
			$criteria->params += array(':channel_id'=>$condition['channel_id']);
		}
		
		if(isset($condition['area_channel_id'])){
			$criteria->condition .= ($criteria->condition  ? 'AND' : '') . ' a.area_channel_id = :area_channel_id ';
			$criteria->params += array(':area_channel_id'=>$condition['area_channel_id']);
		}
		
		if(isset($condition['province'])){
			$criteria->condition .= ($criteria->condition  ? 'AND' : '') . ' a.province = :province ';
			$criteria->params += array(':province'=>$condition['province']);
		}
		
		if(isset($condition['city'])){
			$criteria->condition .= ($criteria->condition  ? 'AND' : '') . ' a.city = :city ';
			$criteria->params += array(':city'=>$condition['city']);
		}
		
		return $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryAll();
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $channel_id
	 * @param unknown_type $province
	 * @param unknown_type $city
	 * @return mixed
	 */
	public function getAreaChannelGroups($channel_id,$province,$city){
		$criteria = $this->getDbCriteria();
		$criteria->alias = 'a';
		$criteria->join = ' LEFT JOIN web_channel_sub b ON b.sub_channel_id = a.area_channel_id LEFT JOIN web_channel c ON c.channel_id = b.channel_id ';
		$criteria->select = 'c.channel_name,c.channel_id,b.sub_channel_id,b.sub_name,a.area_relation_id,a.province,a.city';
		$criteria->compare('c.channel_id', $channel_id);
		$criteria->compare('province', $province);
		$criteria->compare('city', $city);
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		
	}
	
	/**
	 * @author supeng
	 * @param unknown_type $condition
	 * @return boolean
	 */
	public function delChannelAreaRel($condition = array()){
		if ($condition){
			$criteria = $this->getDbCriteria();
			if (!isset($condition['area_channel_id'])){
				return false;
			}
			
			$criteria->compare('area_channel_id', $condition['area_channel_id']);
			
			if(isset($condition['province'])){
				$criteria->compare('province', $condition['province']);
			}
			
			if(isset($condition['city'])){
				if(!isset($condition['province'])){
					return false;
				}
				$criteria->compare('city', $condition['city']);
			}
			return $this->deleteAll($criteria);
		}
		return false;
	}
	
	
}

