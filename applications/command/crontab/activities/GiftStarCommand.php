<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class GiftStarCommand extends PipiConsoleCommand {
	
	protected $giftStarService;
	protected $weekId;
	
	public function beforeAction($action,$params){
		parent::beforeAction($action, $params);
		$this->giftStarService=new GiftStarService();
		$this->weekId=$this->giftStarService->getThisWeekId();
		return true;
	}
	
	//初始化指定周编号礼物之星各种设定
	public function actionInitGiftStarSet()
	{
		$giftStarService=$this->giftStarService;
		$result=$giftStarService->initGiftStarSet($this->weekId);
		if($result)
			echo "初始化礼物之星其他设定成功 \n";
		else
			echo "没有需要初始化礼物之星其他设定数据 \n";
		
		$result=$giftStarService->initGiftStarRule($this->weekId);
		echo "初始化了{$result}条礼物之星礼物规则设定记录 \n";

		$result=$giftStarService->initGiftStarDotey($this->weekId);
		echo "初始化了{$result}条参与争夺榜单主播id和参与时的级别记录 \n";
	}
	
	//初始化主播
	public function actionInitGiftStarDotey()
	{
		$result=$this->giftStarService->initGiftStarDotey($this->weekId);
		echo "初始化了{$result}条参与争夺榜单主播id和参与时的级别记录 \n";
	}
	
	//存储上周排行榜
	public function actionSaveLastWeekRank()
	{
		$giftStarRankService=new GiftStarRankService();
		$rankList=$giftStarRankService->createRankByWeekId($this->weekId-1);

		$result=$giftStarRankService->saveRank($rankList);
		echo "存储上周排行榜数据{$result} \n";
	}
	
	//奖励上周礼物之星主播
	public function actionRewardLastWeekDotey()
	{
		$giftStarService=$this->giftStarService;
		$result=$giftStarService->rewardDotey($this->weekId-1);
		echo "成功奖励了{$result}个获取上周礼物之星的主播 \n";
	}
	
	//生成用于活动页的排行
	public function actionCreateWeekRankWeb()
	{
		$giftStarRankService=new GiftStarRankService();
		$otherRedisModel=new OtherRedisModel();
		$ranklist=$giftStarRankService->getRankByWeekId($this->weekId);
		$result=$otherRedisModel->setWeekGiftStarRankWeb($this->weekId,$ranklist);
		if($result)
		{
			echo "生成成功 \n";
		}
		else
		{
			echo "生成失败 \n";
		}
	}
	
	//生成用于直播间显示的排行
	public function actionCreateWeekRankLingbox()
	{
		$giftStarRankService=new GiftStarRankService();
		$otherRedisModel=new OtherRedisModel();
		$ranklist=$giftStarRankService->createRankByWeekId($this->weekId);
		$result=$otherRedisModel->setWeekGiftStarRankLingbox($this->weekId,$ranklist);
		if($result)
		{
			echo "生成成功 \n";
		}
		else
		{
			echo "生成失败 \n";
		}
	}
}