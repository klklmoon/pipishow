<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */

class MoonFestivalService extends PipiService{

	const ACTIVITY_NAME='中秋节';
	const START_TIME="2013-09-18 20:00:00";		//活动开始时间
	const END_TIME="2013-09-20 23:59:59";			//活动结束时间
	const MOON_CAKE_GIFT_ID=122;						//月饼id
	const HAPPY_MOON_FESTIVAL_GIFT_ID=190;					//中秋快乐id
	protected static $moonFestivalModel;
	protected static $userService;
	
	public function __construct(PipiController $pipiController = null)
	{
		parent::__construct($pipiController);
		if(empty(self::$moonFestivalModel))
		{
			self::$moonFestivalModel=new MoonFestivalModel();
		}
		if(empty(self::$userService))
		{
			self::$userService=new UserService();
		}
	}
	
	//查询主播榜（月饼榜）
	private  function getDoteyMoonCakeRank($start_time,$end_time)
	{
		$moonCakeRank=self::$moonFestivalModel->getDoteyRank($start_time,$end_time,self::MOON_CAKE_GIFT_ID);
		$result=array();
		$i=1;
		foreach ($moonCakeRank as $row)
		{
			$doteyInfo=self::$userService->getUserFrontsAttributeByCondition($row['uid'],true,true);
			$result[$row['uid']]=array(
				'rank_order'=>$i,
				'dk'=>$doteyInfo['dk'],
				'nk'=>$doteyInfo['nk'],
				'gift_num'=>$row['gift_num']
			);

			if($i>=10)
				break;
			$i++;
		}
		return $result;
	}
	
	//查询主播榜（中秋快乐榜）
	private function getDoteyHappyMoonFestivalRank($start_time,$end_time)
	{
		$doteyHappyMoonFestivalRank=self::$moonFestivalModel->getDoteyRank($start_time,$end_time,self::HAPPY_MOON_FESTIVAL_GIFT_ID);
		$result=array();
		$i=1;
		foreach ($doteyHappyMoonFestivalRank as $row)
		{
			$doteyInfo=self::$userService->getUserFrontsAttributeByCondition($row['uid'],true,true);
			$result[$row['uid']]=array(
				'rank_order'=>$i,
				'dk'=>$doteyInfo['dk'],
				'nk'=>$doteyInfo['nk'],
				'gift_num'=>$row['gift_num']
			);

			if($i>=10)
				break;
			$i++;
		}
		return $result;
	}
	
	//查询富豪榜（月饼榜）
	private function getUserMoonCakeRank($start_time,$end_time)
	{
		$userMoonCakeRank=self::$moonFestivalModel->getUserRank($start_time,$end_time,self::MOON_CAKE_GIFT_ID);
		$result=array();
		$i=1;
		foreach ($userMoonCakeRank as $row)
		{
			$userInfo=self::$userService->getUserFrontsAttributeByCondition($row['uid'],true,false);
			$result[$row['uid']]=array(
				'rank_order'=>$i,
				'rk'=>$userInfo['rk'],
				'nk'=>$userInfo['nk'],
				'gift_num'=>$row['gift_num']
			);
			$i++;
		}
		return $result;
	}
	
	//查询富豪榜（中秋快乐榜）
	private function getUserHappyMoonFestivalRank($start_time,$end_time)
	{
		$userHappyMoonFestivalRank=self::$moonFestivalModel->getUserRank($start_time,$end_time,self::HAPPY_MOON_FESTIVAL_GIFT_ID);
		$result=array();
		$i=1;
		foreach ($userHappyMoonFestivalRank as $row)
		{
			$userInfo=self::$userService->getUserFrontsAttributeByCondition($row['uid'],true,false);
			$result[$row['uid']]=array(
				'rank_order'=>$i,
				'rk'=>$userInfo['rk'],
				'nk'=>$userInfo['nk'],
				'gift_num'=>$row['gift_num']
			);
			$i++;
		}
		return $result;
	}
	
	//获取页面数据
	public function getActivityPageData()
	{
		$stime=strtotime(self::START_TIME);
		$etime=strtotime(self::END_TIME);
		$pageData=array();
		$pageData['DoteyMoonCakeRank']=$this->getDoteyMoonCakeRank($stime,$etime);
		$pageData['DoteyHappyMoonFestivalRank']=$this->getDoteyHappyMoonFestivalRank($stime,$etime);
		$pageData['UserMoonCakeRank']=$this->getUserMoonCakeRank($stime,$etime);
		$pageData['UserHappyMoonFestivalRank']=$this->getUserHappyMoonFestivalRank($stime,$etime);
		return $pageData;
	}
}