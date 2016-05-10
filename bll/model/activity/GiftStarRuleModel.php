<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class GiftStarRuleModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return GiftStarRuleModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{long_giftstar_rule}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_activity;
	}
	
	//返回指定周编号和礼物序号对应规则记录
	public function getGiftStarRuleByGiftWeekOrder($weekId,$giftWeekOrder)
	{
		$criteria = $this->getDbCriteria();
		$criteria->condition='week_id = :week_id AND gift_week_order = :gift_week_order';
		$criteria->params=array(':week_id'=>$weekId,':gift_week_order'=>$giftWeekOrder);
		return $this->find($criteria);
	}
	
	//返回指定周编号对应主播等级限制列表
	public function getWeekDoteyGradeListByWeekId($weekId)
	{
		$giftStarRuleList=$this->getGiftStarRuleListByWeekId($weekId);
		$rankList=array();
		foreach ($giftStarRuleList as $giftStarRuleRow)
		{
			$rankList=array_merge($rankList,explode(',',$giftStarRuleRow['contention_rule']));
		}
		return $rankList;
	}
	
	//返回指定周编号对应礼物规则设定
	public function getGiftStarRuleListByWeekId($weekId)
	{
		$criteria = $this->getDbCriteria();
		$criteria->condition='week_id = :week_id';
		$criteria->params=array(':week_id'=>$weekId);
		$giftStarRuleList=$this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		return $giftStarRuleList;
	}
	
	//返回指定周编号和礼物id对应礼物规则设定
	public function getGiftStarRuleByGiftId($weekId,$giftId)
	{
		$criteria = $this->getDbCriteria();
		$criteria->addColumnCondition(array(
			'week_id'=>$weekId,
			'gift_id'=>$giftId
			));
		$giftStarRule=$this->find($criteria);
		return $giftStarRule->attributes;
	}
	
	public function getRuleByCondition($offset=0,$pageSize=10, array $condition=array()){
		$criteria = $this->getDbCriteria();
		if (!empty($condition['week_id'])){
			$criteria->compare('week_id', $condition['week_id'],true);
		}
	
		if (isset($condition['monday_date'])){
			$criteria->compare('monday_date', $condition['monday_date']);
		}
	
		if (isset($condition['gift_id'])){
			$criteria->compare('gift_id', $condition['gift_id']);
		}
	
		$criteria->limit=$pageSize;
		$criteria->offset = $offset*$pageSize;
		$criteria->order = 'week_id DESC';
		return $this->findAll($criteria);
	}
	
	public function getRuleCountByCondition(array $condition=array()){
		$criteria = $this->getDbCriteria();
			if (!empty($condition['week_id'])){
			$criteria->compare('week_id', $condition['week_id'],true);
		}
	
		if (isset($condition['monday_date'])){
			$criteria->compare('monday_date', $condition['monday_date']);
		}
	
		if (isset($condition['gift_id'])){
			$criteria->compare('gift_id', $condition['gift_id']);
		}
		return $this->count($criteria);
	}
	
	public function getRuleByIds(array $ruleIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('rule_id',$ruleIds);
		return $this->findAll($criteria);
	}
}