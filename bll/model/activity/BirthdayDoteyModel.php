<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class BirthdayDoteyModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return BirthdayDoteyModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{long_birthday_dotey}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_activity;
	}
	
	//查询主播生日
	public function getDoteyBirthdayByDate($queryDate=null)
	{
		$userCommand=Yii::app()->db_user->createCommand();
		//查询主播生日
		if(empty($queryDate))
		{
			$userCommand->setText("select a.uid,b.birthday from	web_dotey_base as a
				inner join web_user_extend as b on a.uid=b.uid where a.status=1");
		}
		else
		{
			$userCommand->setText("select a.uid,b.birthday from	web_dotey_base as a
				inner join web_user_extend as b on a.uid=b.uid where a.status=1 and 
				FROM_UNIXTIME(b.birthday,'%m-%d')>{$queryDate}");
		}

		$doteyList=$userCommand->queryAll();
		return $doteyList;
	}
	
	//查询30天内有过直播过的主播
	public function getRangThirtyLivedDoteysId($stime,$etime)
	{
		$archivesCommand=Yii::app()->db_archives->createCommand();
		$sql="select DISTINCT A.uid,A.title from web_archives as A inner join web_live_records as B 
			on A.archives_id=B.archives_id where A.is_hide=0 and B.live_time>{$stime} 
			and B.live_time<{$etime}";
		$archivesCommand->setText($sql);
		$doteyList=$archivesCommand->queryAll();
		
		return count($doteyList)>0?$doteyList:array();
	}
	
	//统计月份生日公主榜
	public function getMonthPrincessRank($yearMonth,$doteys,$birthdayGiftList,$limit=0)
	{
		if(count($doteys)<1)
			return array();
		$start_time=strtotime($yearMonth."-01 00:00:00");
		$end_time=strtotime(date("Y-m-d",strtotime("$yearMonth-01 +1 month -1 day"))." 23:59:59");
		$consumeRecordsCommand=Yii::app()->db_consume_records->createCommand();
		if(empty($limit))
		{
			$sql="select b.uid,sum(a.num) as gift_num,sum(a.charm) as sum_charm,max(a.create_time) as max_time
				 from web_user_giftsend_records as a inner join web_user_giftsend_relation_records as b 
				on a.record_id=b.record_id where b.is_onwer=0 and a.create_time>={$start_time} and 
				a.create_time<={$end_time} and b.uid in (".implode(",",$doteys).") and 
					a.gift_id in (".implode(",",$birthdayGiftList).") group by b.uid order by sum_charm desc,
						max_time asc";
		}
		else
		{
			$sql="select b.uid,sum(a.num) as gift_num,sum(a.charm) as sum_charm,max(a.create_time) as max_time
				 from web_user_giftsend_records as a inner join web_user_giftsend_relation_records as b 
				on a.record_id=b.record_id where b.is_onwer=0 and a.create_time>={$start_time} and 
				a.create_time<={$end_time} and b.uid in (".implode(",",$doteys).") and 
				a.gift_id in (".implode(",",$birthdayGiftList).") group by b.uid order by sum_charm desc,
					max_time asc limit {$limit}";
		}
		$consumeRecordsCommand->setText($sql);
		$rankList=$consumeRecordsCommand->queryAll();
		return $rankList;
	}
	
	//获取指定主播指定礼物分组统计明细
	public function getSendGiftRecordsByDotey($start_time,$end_time,$dotey_id,$birthdayGiftList)
	{
		$consumeRecordsCommand=Yii::app()->db_consume_records->createCommand();
		$sql="select a.gift_id,sum(a.num) as gift_num from web_user_giftsend_records as a 
		inner join web_user_giftsend_relation_records as b	on a.record_id=b.record_id where b.is_onwer=0 
		and b.uid={$dotey_id} and a.create_time>={$start_time} and	a.create_time<={$end_time} and 
		a.gift_id in (".implode(",",$birthdayGiftList).") group by a.gift_id order by gift_num asc";
		$consumeRecordsCommand->setText($sql);
		$doteyGiftList=$consumeRecordsCommand->queryAll($sql);
		return $doteyGiftList;
	}
	
	//统计月份指定主播用户榜
	public function getMonthUserRankByDotey($yearMonth,$dotey_id,$birthdayGiftList,$limit=0)
	{
		$start_time=strtotime($yearMonth."-01 00:00:00");
		$end_time=strtotime(date("Y-m-d",strtotime("$yearMonth-01 +1 month -1 day"))." 23:59:59");
		$consumeRecordsCommand=Yii::app()->db_consume_records->createCommand();
		if(empty($limit))
		{
			$sql="select a.uid,sum(a.dedication) as sum_dedication,max(a.create_time) as max_time
				 from web_user_giftsend_records as a inner join web_user_giftsend_relation_records as b 
				on a.record_id=b.record_id where b.uid={$dotey_id} and b.is_onwer=0 and 
				a.create_time>={$start_time} and a.create_time<={$end_time} and  
					a.gift_id in (".implode(",",$birthdayGiftList).") group by a.uid order by sum_dedication desc,
						max_time asc";
		}
		else
		{
			$sql="select a.uid,sum(a.dedication) as sum_dedication,max(a.create_time) as max_time
				 from web_user_giftsend_records as a inner join web_user_giftsend_relation_records as b 
				on a.record_id=b.record_id where b.uid={$dotey_id} and b.is_onwer=0 and 
				a.create_time>={$start_time} and a.create_time<={$end_time} and  
				a.gift_id in (".implode(",",$birthdayGiftList).") group by a.uid order by sum_dedication desc,
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
	
	public function getBirthdayDoteyRecords($offset=0,$pageSize=10, array $condition=array()){
		$criteria = $this->getDbCriteria();
		$criteria->compare('year', $condition['year']);
		$criteria->compare('month', $condition['month']);
	
		$criteria->limit=$pageSize;
		$criteria->offset = $offset*$pageSize;
		$criteria->order = 'dotey_id DESC';
		return $this->findAll($criteria);
	}
	
	public function getBirthdayDoteyRecordsCount(array $condition=array()){
		$criteria = $this->getDbCriteria();
		$criteria->compare('year', $condition['year']);
		$criteria->compare('month', $condition['month']);
		return $this->count($criteria);
	}
}