<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class GiftStarRankModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return GiftStarRankModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{long_giftstar_rank}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_activity;
	}
		
	//获取指定时间段、指定礼物、指定主播的排行
	public function getDoteyRankByCondition($sTime,$eTime,$giftId,$doteys)
	{
		$rankCommand=Yii::app()->db_consume_records->createCommand();
		if(count($doteys)<1)
			return array();
		$doteyStr=implode(',',$doteys);
		$sql="select b.uid,sum(a.num) as gift_num,max(a.create_time) as min_time from web_user_giftsend_records as a
		inner join web_user_giftsend_relation_records as b on a.record_id=b.record_id where a.gift_id={$giftId} and
		b.is_onwer=0 and b.uid in ({$doteyStr}) and a.create_time>={$sTime} and a.create_time<={$eTime}
		and b.create_time>={$sTime} and b.create_time<={$eTime} group by b.uid
		order by gift_num desc,min_time asc limit 50";
		$rankCommand->setText($sql);
		$giftStarRank=$rankCommand->queryAll();
		for($i=0;$i<count($giftStarRank);$i++)
		{
			$giftStarRank[$i]['rank']=$i+1;
		}
	
		return array('gift_id'=>$giftId,'data'=>$giftStarRank);
	}
	
	
	//获取过往礼物之星第一名主播列表
	public function getFirstDoteysByWeekId($weekId)
	{
		$criteria = $this->getDbCriteria();
		$criteria->addColumnCondition(array(
			'week_id'=>$weekId,
			'rank'=>1
		));
		$doteyList=$this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		return $doteyList;
	}
	
	//获取指定主播id、周编号的直播间过往礼物之星排行榜信息
	public function getGiftStarRank($doteyId,$weekId)
	{
		$criteria = $this->getDbCriteria();
		$criteria->addColumnCondition(array(
			'week_id'=>$weekId,
			'dotey_id'=>$doteyId
		));
		$doteyList=$this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryAll();
		return $doteyList;
	}
}