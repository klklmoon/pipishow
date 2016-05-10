<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class GiftStarDoteyModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return GiftStarDoteyModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{long_giftstar_dotey}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_activity;
	}
	
	public function getDoteysByRule($weekId,$rule)
	{
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('grade',$rule);
		$criteria->addColumnCondition(array('week_id'=>$weekId));
		$doteyList=$this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		$doteyArr=array();
		foreach($doteyList as $doteyRow)
		{
			$doteyArr[]=$doteyRow['dotey_id'];
		}
		return $doteyArr;
	}
	
	//统计主播魅力值
	public function getDoteyCharmByTime($doteyId,$endTime)
	{
		$consumeRecordsCommand=Yii::app()->db_consume_records->createCommand();
		//统计主播魅力值
		$consumeRecordsCommand->setText("select sum(charm) as sum_charm from
			web_dotey_charm_records where uid={$doteyId} and create_time<={$endTime}");
		$sumCharm=$consumeRecordsCommand->queryScalar();
		return $sumCharm;
	}
}