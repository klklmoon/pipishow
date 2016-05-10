<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */

class NationalDayService extends PipiService{

	const ACTIVITY_NAME='国庆节';
	const START_TIME="2013-09-30 20:00:00";		//活动开始时间
	const END_TIME="2013-10-07 23:59:59";			//活动结束时间
	const WELCOME_NATIONAL_DAY_GIFT_ID=192;					//喜迎国庆id
	//国庆活动和中秋活动model可通用
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
	
	//查询主播榜（喜迎国庆）
	private  function getDoteyWelcomeNationalDayRank($start_time,$end_time)
	{
		$doteyRank=self::$moonFestivalModel->getDoteyRank($start_time,$end_time,self::WELCOME_NATIONAL_DAY_GIFT_ID);
		$result=array();
		$i=1;
		foreach ($doteyRank as $row)
		{
			$doteyInfo=self::$userService->getUserFrontsAttributeByCondition($row['uid'],true,true);
			$isDotey = self::$userService->hasBit((int)$doteyInfo['ut'],USER_TYPE_DOTEY);

			if(!$isDotey)
				continue;
			
			$result[$row['uid']]=array(
				'rank_order'=>$i,
				'dk'=>$doteyInfo['dk'],
				'nk'=>$doteyInfo['nk'],
				'gift_num'=>$row['gift_num'],
				'isDotey'=>$isDotey
			);

			if($i>=10)
				break;
			$i++;
		}

		return $result;
	}
	
	//查询富豪榜（喜迎国庆）
	private function getUserWelcomeNationalDayRank($start_time,$end_time)
	{
		$userRank=self::$moonFestivalModel->getUserRank($start_time,$end_time,self::WELCOME_NATIONAL_DAY_GIFT_ID);
		$result=array();
		$i=1;
		foreach ($userRank as $row)
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
		$pageData['DoteyWelcomeNationalDayRank']=$this->getDoteyWelcomeNationalDayRank($stime,$etime);
		$pageData['UserWelcomeNationalDayRank']=$this->getUserWelcomeNationalDayRank($stime,$etime);
		return $pageData;
	}
}

?>