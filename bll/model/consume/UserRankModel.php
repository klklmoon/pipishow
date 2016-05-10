<?php
/**
 * 用户等级说明数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserRankModel.php 9757 2013-05-07 12:29:09Z suqian $ 
 * @package model
 * @subpackage consume
 */
class UserRankModel extends PipiActiveRecord {
	public function tableName(){
		return '{{user_rank}}';
	}
	
	public function rules(){
		return array(
			array('rank,name','required'),
			array('rank,name','unique'),
		);
	}
	/**
	 * @param string $className
	 * @return UserRankModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	/**
	 * 根据贡献值取得当前等级
	 * 
	 * @param int $dedication
	 * @return UserRankModel
	 */
	public function getUserRankByDedication($dedication){
	 	$criteria = $this->getDbCriteria();
	 	$criteria->condition = ' dedication <= :dedication ';
	 	$criteria->order = ' dedication DESC ';
	 	$criteria->limit = 1;
	 	$criteria->params[':dedication'] = $dedication;
		return $this->find($criteria);
	}
	
	/**
	 * 取得等级详细信息
	 * 
	 * @param int|array $grades
	 * @return array
	 */
	public function getRanksInfoByRanks($ranks){
		if(empty($ranks)){
			return array();
		}
		$ranks = is_array($ranks) ? $ranks : array($ranks);
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('rank',$ranks);
		return $this->findAll($criteria);
	}
	
	/**
	 * 取得所有等级的信息
	 *
	 * @author supeng
	 * @return array
	 */
	public function getAllRanks(){
		$criteria = $this->getDbCriteria();
		return $this->findAll($criteria);
	}
}

?>