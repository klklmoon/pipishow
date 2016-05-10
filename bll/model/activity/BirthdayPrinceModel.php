<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class BirthdayPrinceModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return BirthdayPrinceModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{long_birthday_prince}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_activity;
	}

	//统计月份生日王子榜
	public function getMonthPrinceRank($yearMonth,$doteys,$birthdayGiftList,$limit=0)
	{
		if(count($doteys)<1)
			return array();
		$start_time=strtotime($yearMonth."-01 00:00:00");
		$end_time=strtotime(date("Y-m-d",strtotime("$yearMonth-01 +1 month -1 day"))." 23:59:59");
		$consumeRecordsCommand=Yii::app()->db_consume_records->createCommand();
		if(empty($limit))
		{
			$sql="select a.uid,sum(a.num) as gift_num,sum(a.dedication) as sum_dedication,max(a.create_time) as max_time
			from web_user_giftsend_records as a inner join web_user_giftsend_relation_records as b
			on a.record_id=b.record_id where b.is_onwer=0 and a.create_time>={$start_time} and
			a.create_time<={$end_time} and b.uid in (".implode(",",$doteys).") and
			a.gift_id in (".implode(",",$birthdayGiftList).") group by a.uid order by sum_dedication desc,
				max_time asc";
		}
		else
		{
			$sql="select a.uid,sum(a.num) as gift_num,sum(a.dedication) as sum_dedication,max(a.create_time) as max_time
			from web_user_giftsend_records as a inner join web_user_giftsend_relation_records as b
			on a.record_id=b.record_id where b.is_onwer=0 and a.create_time>={$start_time} and
			a.create_time<={$end_time} and b.uid in (".implode(",",$doteys).") and
			a.gift_id in (".implode(",",$birthdayGiftList).") group by a.uid order by sum_dedication desc,
			max_time asc limit {$limit}";
		}
		$consumeRecordsCommand->setText($sql);
		$rankList=$consumeRecordsCommand->queryAll();
		return $rankList;
	}
	
	//获取指定用户送给指定主播的指定礼物分组统计明细
	public function getSendGiftRecordsByUser($start_time,$end_time,$uid,$doteys,$birthdayGiftList)
	{
		if(count($doteys)<1)
			return array();
		$consumeRecordsCommand=Yii::app()->db_consume_records->createCommand();
		$sql="select a.gift_id,sum(a.num) as gift_num from web_user_giftsend_records as a
		inner join web_user_giftsend_relation_records as b	on a.record_id=b.record_id where b.is_onwer=0
		and a.uid={$uid} and b.uid in (".implode(",",$doteys).") and a.create_time>={$start_time} and 
		a.create_time<={$end_time} and	a.gift_id in (".implode(",",$birthdayGiftList).") 
			group by a.gift_id order by gift_num asc";
		$consumeRecordsCommand->setText($sql);
			$userGiftList=$consumeRecordsCommand->queryAll($sql);
			return $userGiftList;
	}
	
	//统计月份指定用户守护主播
	public function getMonthDoteyRankByUser($yearMonth,$uid,$doteys,$birthdayGiftList,$limit=0)
	{
		if(count($doteys)<1)
			return array();
		$start_time=strtotime($yearMonth."-01 00:00:00");
		$end_time=strtotime(date("Y-m-d",strtotime("$yearMonth-01 +1 month -1 day"))." 23:59:59");
		$consumeRecordsCommand=Yii::app()->db_consume_records->createCommand();
		if(empty($limit))
		{
			$sql="select b.uid,sum(a.charm) as sum_charm,max(a.create_time) as max_time
			from web_user_giftsend_records as a inner join web_user_giftsend_relation_records as b
			on a.record_id=b.record_id where a.uid={$uid} and b.is_onwer=0 and a.create_time>={$start_time} and
			a.create_time<={$end_time} and b.uid in (".implode(",",$doteys).") and
			a.gift_id in (".implode(",",$birthdayGiftList).") group by b.uid order by sum_charm desc,
				max_time asc";
		}
		else
		{
		$sql="select b.uid,sum(a.charm) as sum_charm,max(a.create_time) as max_time
		from web_user_giftsend_records as a inner join web_user_giftsend_relation_records as b
		on a.record_id=b.record_id where a.uid={$uid} and b.is_onwer=0 and a.create_time>={$start_time} and
		a.create_time<={$end_time} and b.uid in (".implode(",",$doteys).") and
		a.gift_id in (".implode(",",$birthdayGiftList).") group by b.uid order by sum_charm desc,
		max_time asc limit {$limit}";
		}
		$consumeRecordsCommand->setText($sql);
		$rankList=$consumeRecordsCommand->queryAll();
		for($i=0;$i<count($rankList);$i++)
		{
			$rankList[$i]['rank']=$i+1;
		}
		return $rankList;
	}
}