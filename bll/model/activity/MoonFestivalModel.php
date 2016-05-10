<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class MoonFestivalModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return MoonFestivalModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{user_giftsend_records}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_consume_records;
	}

	
	//查询主播榜（月饼榜）（中秋快乐榜），可通用主播收礼榜
	public function getDoteyRank($start_time,$end_time,$gift_id, $number = 20)
	{
		$rankCommand=Yii::app()->db_consume_records->createCommand();
		$sql="select b.uid,sum(a.num) as gift_num,max(a.create_time) as max_time from web_user_giftsend_records as a
		 inner join web_user_giftsend_relation_records as b on a.record_id=b.record_id where a.gift_id={$gift_id} and
		 a.create_time>={$start_time} and a.create_time<={$end_time} and b.is_onwer=0  
		  group by b.uid order by gift_num desc,max_time asc limit ".$number;
		$rankCommand->setText($sql);
		$doteyRank=$rankCommand->queryAll();
		return $doteyRank;
	}
	
	
	//查询富豪榜（月饼榜）（中秋快乐榜），可通用用户送礼榜
	public function getUserRank($start_time,$end_time,$gift_id, $number = 10)
	{
		$rankCommand=Yii::app()->db_consume_records->createCommand();
		$sql="select a.uid,sum(a.num) as gift_num,max(a.create_time) as max_time from web_user_giftsend_records as a
		 where a.gift_id={$gift_id} and a.create_time>={$start_time} and a.create_time<={$end_time}
		  group by a.uid order by gift_num desc,max_time asc limit ".$number;
		$rankCommand->setText($sql);
		$userRank=$rankCommand->queryAll();
		return $userRank;
	}
	
	//查询给某主播送某礼物礼数量的前三名富豪
	public function getUsers($start_time, $end_time, $gift_id, $dotey_uid, $number = 3){
		$rankCommand=Yii::app()->db_consume_records->createCommand();
		$sql="select a.uid,sum(a.num) as gift_num,max(a.create_time) as max_time from web_user_giftsend_records as a
		left join web_user_giftsend_relation_records as b on a.record_id=b.record_id
		where b.uid = {$dotey_uid} and b.is_onwer =0 and b.create_time>={$start_time} and b.create_time<={$end_time} and a.gift_id={$gift_id} 
		group by a.uid order by gift_num desc,max_time asc limit ".$number;
		$rankCommand->setText($sql);
		$userRank=$rankCommand->queryAll();
		return $userRank;
	}
	
	//返回对战获胜的主播uid
	public function getBattleResult($start_time,$end_time,$gift_id, array $dotey_uids)
	{
		if(empty($dotey_uids)) return array();
		$rankCommand=Yii::app()->db_consume_records->createCommand();
		$sql="select b.uid,sum(a.num) as gift_num,max(a.create_time) as max_time from web_user_giftsend_records as a
		left join web_user_giftsend_relation_records as b on a.record_id=b.record_id where a.gift_id={$gift_id} and
		a.create_time>={$start_time} and a.create_time<={$end_time} and b.uid in (".implode(',', $dotey_uids).") and b.is_onwer=0
		group by b.uid order by gift_num desc,max_time asc";
		$rankCommand->setText($sql);
		$dotey=$rankCommand->queryAll();
		if(empty($dotey)) return array();
		return $dotey;
	}
}