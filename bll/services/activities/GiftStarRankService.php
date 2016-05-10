<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class GiftStarRankService extends PipiService{
	
	//获取指定周id的礼物之星主播榜，构造用于显示的数据
	public function getRankByWeekId($weekId)
	{
		$giftStarService=new GiftStarService();
		
		$giftStarRuleModel=new GiftStarRuleModel();
		//获取指定周的礼物之星规则列表
		$giftStarRuleList=$giftStarRuleModel->getGiftStarRuleListByWeekId($weekId);
		$giftStarDoteyModel=new GiftStarDoteyModel();
	
		$rankList=array();
		foreach ($giftStarRuleList as $giftStarRuleRow)
		{
			//获取符合指定周规则设定的主播id
			$giftStarDoteys=$giftStarDoteyModel->getDoteysByRule($weekId,explode(',',$giftStarRuleRow['contention_rule']));
			//获取符合规则礼物之星主播榜
			$rankList[$giftStarRuleRow['gift_id']]=$this->getDoteyRankByWeekId($weekId,$giftStarRuleRow['gift_id'],$giftStarDoteys);
		}
		return $rankList;
	}
	
	//根据周id和礼物id统计主播榜
	public function getDoteyRankByWeekId($weekId,$giftId,$doteys)
	{
		$giftStarService=new GiftStarService();
		$weekStartTime=$giftStarService->getWeekStartTimestamp($weekId);
		$weekEndTime=$giftStarService->getWeekEndTimestamp($weekId);

		$giftStarRankModel=new GiftStarRankModel();
		$rankList=$giftStarRankModel->getDoteyRankByCondition($weekStartTime,$weekEndTime,$giftId,$doteys);
		return $rankList;
	}
	
	//生成指定周id的礼物之星主播榜，构造用于存储的数据
	public function createRankByWeekId($weekId)
	{
		$giftStarRuleModel=new GiftStarRuleModel();
		//获取指定周的礼物之星规则列表
		$giftStarRuleList=$giftStarRuleModel->getGiftStarRuleListByWeekId($weekId);
		$giftStarDoteyModel=new GiftStarDoteyModel();
	
		$giftStarRankModel=new GiftStarRankModel();
		$rankList=array();
		foreach ($giftStarRuleList as $giftStarRuleRow)
		{
			//获取符合指定周规则设定的主播id
			$giftStarDoteys=$giftStarDoteyModel->getDoteysByRule($weekId,explode(',',$giftStarRuleRow['contention_rule']));

			//获取符合规则礼物之星主播榜
			$gift_rank=$this->getDoteyRankByWeekId($weekId,$giftStarRuleRow['gift_id'],$giftStarDoteys);
			
			for($i=0;$i<count($gift_rank['data']);$i++)
			{
				$gift_rank['data'][$i]['week_id']=$weekId;
				$gift_rank['data'][$i]['gift_id']=$giftStarRuleRow['gift_id'];
			}
			$rankList[]=$gift_rank;
		}
		return $rankList;
	}
	
	//存储礼物之星排行榜
	public function saveRank($rankList)
	{
		$i=0;
		foreach ($rankList as $rankRow)
		{
			foreach($rankRow['data'] as $dataRow)
			{
				$giftStarRank=new GiftStarRankModel();
				$giftStarRank->week_id=$dataRow['week_id'];
				$giftStarRank->dotey_id=$dataRow['uid'];
				$giftStarRank->rank=$dataRow['rank'];
				$giftStarRank->gift_id=$dataRow['gift_id'];
				$giftStarRank->gift_num=$dataRow['gift_num'];
				$giftStarRank->create_time=time();
				$flag=$giftStarRank->save();
				if($flag)
					$i++;
			}
		}
		
		return $i;
	}
	
	//获取用于活动页显示的排行
	public function getWeekRankWeb($weekId)
	{
		$otherRedisModel=new OtherRedisModel();
		$ranklist=$otherRedisModel->getWeekGiftStarRankWeb($weekId);
		if(isset($ranklist) && count($ranklist)>0)
		{
			return $ranklist;
		}
		else
		{
			$ranklist=$this->getRankByWeekId($weekId);
			$otherRedisModel->setWeekGiftStarRankWeb($weekId,$ranklist);
			return $ranklist;
		}
		
	}
	
	//获取用于直播间显示的排行
	public function getWeekRankLingbox($weekId)
	{
		$otherRedisModel=new OtherRedisModel();
		$ranklist=$otherRedisModel->getWeekGiftStarRankLingbox($weekId);
		if(isset($ranklist) && count($ranklist)>0)
		{
			return $ranklist;
		}
		else
		{
			$ranklist=$this->createRankByWeekId($weekId);
			$otherRedisModel->setWeekGiftStarRankLingbox($weekId,$ranklist);
			return $ranklist;
		}
	}
	
	public function getWeekRankByUid($uid){
		$rankList=$this->getWeekRankWeb(0);
		$giftRank=array();
		$giftService=new GiftService();
		$i=0;
		if($rankList){
			foreach($rankList as $row){
				if($row['data']){
					foreach($row['data'] as $key=>$val){
						if($val['uid']==$uid){
							$giftRank[$i]['id']=$key+1;
							$giftRank[$i]['gift_id']=$row['gift_id'];
							$giftRank[$i]['gift_count']=$val['gift_num'];
							$giftRank[$i]['diffvalue']=$key>0?$rankList['data'][$key-1]['gift_num']-$val['gift_num']:0;
							$giftInfo=$giftService->getGiftByIds($row['gift_id']);
							$giftRank[$i]['pic']=$giftService->getGiftUrl($giftInfo[$row['gift_id']]['image']);
							$giftRank[$i]['gift_name']=$giftInfo[$row['gift_id']]['zh_name'];
							$i++;
						}
					}
				}
				
			}
		}
		return $giftRank;
	}
}