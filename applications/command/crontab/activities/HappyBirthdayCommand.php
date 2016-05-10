<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class HappyBirthdayCommand extends PipiConsoleCommand {

	protected $happyBirthdayService;

	public function beforeAction($action,$params){
		parent::beforeAction($action, $params);
		$this->happyBirthdayService=new HappyBirthdayService();
		return true;
	}
	
	//收录主播生日记录
	public function actionInitBirthdayDotey()
	{
		$result=$this->happyBirthdayService->initBirthdayDotey();
		echo "初始化了{$result}条生日快乐活动主播记录。 \n";
	}
	
	//生成页面排行榜数据
	public function actionCreateHappyBirthdayPageData()
	{
		$happyBirthdayService=$this->happyBirthdayService;
		$otherRedisModel=new OtherRedisModel();
		$pageData=array();
		$pageData['todayBirthdayDoteys']=$happyBirthdayService->getTodayBirthdayDoteys();
		$pageData['honorRank']=$happyBirthdayService->getHonorRank();
		$pageData['thisMonthRank']=$happyBirthdayService->getThisMonthRank();
		$pageData['activityGiftList']=$happyBirthdayService->getActivityGiftList();
		$pageData['batchPrice']=$happyBirthdayService->getBatchPrice();
		$pageData['monthDoteyList']=$happyBirthdayService->getThisMonthBirthdayDoteys();
		$pageData['monthHonorRankData']=$happyBirthdayService->getAllMonthHonorRank();
		$pageData['birthdayArchives']=$happyBirthdayService->getBirthdayArchives();
		
		$result=$otherRedisModel->setHappyBirthdayPageData($pageData);
		if($result)
		{
			echo "生成成功 \n";
		}
		else
		{
			echo "生成失败 \n";
		}
	}
	
	//存储上月数据，每月只执行一次
	public function actionSaveLastMonthData()
	{
		$sdate=date("Y-m-d");
		$lastMonth=date("Y-m",strtotime($sdate." -1 month"));
		$happyBirthdayService=$this->happyBirthdayService;
		$result=$happyBirthdayService->saveMonthData($lastMonth);
		echo "处理了{$result['doteyRankCounts']}条主播排行数据，{$result['userRankCounts']}用户排行数据 \n";
	}
}