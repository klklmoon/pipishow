<?php
/**
 * 用户星级说明数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: StarsRankModel.php 9562 2013-05-04 07:36:11Z hexin $ 
 * @package model
 * @subpackage consume
 */
class StarsRankModel extends PipiActiveRecord {
	public function tableName(){
		return '{{stars_rank}}';
	}
	
	/**
	 * @param string $className
	 * @return StarsRankModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	/**
	 * 根据魅力值取得当前星级
	 * 
	 * @param int $pipiegg
	 * @return UserRankModel
	 */
	public function getStars($pipiegg){
	 	$criteria = $this->getDbCriteria();
	 	$criteria->condition = ' pipiegg <= :pipiegg and status = 1';
	 	$criteria->order = ' pipiegg DESC ';
	 	$criteria->limit = 1;
	 	$criteria->params[':pipiegg'] = $pipiegg;
		return $this->find($criteria);
	}
	
	/**
	 * 取得星级详细信息
	 * 
	 * @param int|array $stars
	 * @return array
	 */
	public function getStarsInfos($stars){
		if(empty($stars)){
			return array();
		}
		$stars = is_array($stars) ? $stars : array($stars);
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('stars', $stars);
		$criteria->addColumnCondition(array('status' => 1));
		return $this->findAll($criteria);
	}
	
	/**
	 * 取得所有星级的信息
	 *
	 * @return array
	 */
	public function getAllStars(){
		$criteria = $this->getDbCriteria();
		return $this->findAll($criteria);
	}
}

?>