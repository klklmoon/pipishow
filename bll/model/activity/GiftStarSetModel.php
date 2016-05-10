<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */

class GiftStarSetModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return GiftStarSetModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{long_giftstar_set}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_activity;
	}
	
	public function getGiftStarSetByWeekId($weekId)
	{
		$criteria = $this->getDbCriteria();
		$criteria->condition='week_id = :week_id';
		$criteria->params=array(':week_id'=>$weekId);
		return $this->find($criteria);
	}
	
	public function getSetByCondition($offset=0,$pageSize=10, array $condition=array()){
		$criteria = $this->getDbCriteria();
			if (!empty($condition['week_id'])){
			$criteria->compare('week_id', $condition['week_id'],true);
		}
	
		if (isset($condition['monday_date'])){
			$criteria->compare('monday_date', $condition['monday_date']);
		}
	
		$criteria->limit=$pageSize;
		$criteria->offset = $offset*$pageSize;
		$criteria->order = 'week_id DESC';
		return $this->findAll($criteria);
	}
	
	public function getSetCountByCondition(array $condition=array()){
		$criteria = $this->getDbCriteria();
		if (!empty($condition['week_id'])){
			$criteria->compare('week_id', $condition['week_id'],true);
		}
	
		if (isset($condition['monday_date'])){
			$criteria->compare('monday_date', $condition['monday_date']);
		}
	
		return $this->count($criteria);
	}
	
	public function getSetByIds(array $setIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('set_id',$setIds);
		return $this->findAll($criteria);
	}
}
