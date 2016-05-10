<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class HalloweenRecordsModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return HalloweenRecordsModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{short_halloween_records}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_activity;
	}
	
	//按$uid和用户类型取得万圣节兑换记录
	public function getExchangeSetmealByUid($uid,$startTime,$endTime,$user_type=0)
	{
		if($user_type)
			$setmeal_type=1;
		else
			$setmeal_type=0;
		$dbCriteria = $this->getDbCriteria();
		$dbCriteria->condition = ' uid = :uid and user_type=:user_type and setmeal_type=:setmeal_type and create_time  >= :startTime and create_time <= :endTime ';
		$dbCriteria->params = array(':uid'=>$uid,':user_type'=>$user_type,':setmeal_type'=>$setmeal_type,':startTime'=>$startTime,':endTime'=>$endTime);
		return $this->findAll($dbCriteria);
	}
	
	
	//统计用户送出的南瓜数
	public function getSumPumpkinByUser($start_time,$end_time,$uid,$gift_id)
	{
		$consumeRecordsCommand=Yii::app()->db_consume_records->createCommand();
		$sql="select sum(num) as gift_num from web_user_giftsend_records where uid={$uid}  
		 and gift_id={$gift_id} and create_time>={$start_time} and create_time<={$end_time}";
		$consumeRecordsCommand->setText($sql);
		$userPumpkin=$consumeRecordsCommand->queryScalar();
		return $userPumpkin;		
	}
	
	//统计用户收到的南瓜数
	public function getSumPumpkinByDotey($start_time,$end_time,$dotey_id,$gift_id)
	{
		$consumeRecordsCommand=Yii::app()->db_consume_records->createCommand();
		$sql="select sum(a.num) as gift_num from web_user_giftsend_records as a 
		inner join web_user_giftsend_relation_records as b	on a.record_id=b.record_id where b.is_onwer=0 
		and b.uid={$dotey_id} and a.create_time>={$start_time} and	a.create_time<={$end_time} and 
		a.gift_id={$gift_id}";
		$consumeRecordsCommand->setText($sql);
		$doteyPumpkin=$consumeRecordsCommand->queryScalar();
		return $doteyPumpkin;
	}
}