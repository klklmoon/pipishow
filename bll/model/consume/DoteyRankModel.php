<?php
/**
 * 用户等级说明数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: DoteyRankModel.php 9757 2013-05-07 12:29:09Z suqian $ 
 * @package model
 * @subpackage consume
 */
class DoteyRankModel extends PipiActiveRecord {
	public function tableName(){
		return '{{dotey_rank}}';
	}
	
	public function rules(){
		return array(
			array('rank,name','required'),
			array('rank,name','unique'),
		);
	}
	
	/**
	 * @param string $className
	 * @return DoteyRankModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	
	/**
	 * 根据魅力值取得当前等级
	 * 
	 * @param int $charm
	 * @return DoteyRankModel
	 */
	public function getDoteyRankByCharm($charm){
	 	$criteria = $this->getDbCriteria();
	 	$criteria->condition = ' charm <= :charm ';
	 	$criteria->order = ' charm DESC ';
	 	$criteria->limit = 1;
	 	$criteria->params[':charm'] = $charm;
		return $this->find($criteria);
	}
	
	public function getDoteyAllRank(){
		$criteria = $this->getDbCriteria();
		$criteria->order = ' charm DESC ';
		return $this->findAll($criteria);
	}
	
	/**
	 * 取得等级详细信息
	 * 
	 * @param int|array $grades
	 * @return array
	 */
	public function getRanksInfoByRanks($ranks){
		$ranks = is_array($ranks) ? $ranks : array(intval($ranks));
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('rank',$ranks);
		return $this->findAll($criteria);
	}
}

?>