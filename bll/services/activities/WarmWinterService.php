<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */

class WarmWinterService extends PipiService{

	const ACTIVITY_NAME='温暖一冬';
	const START_TIME="2013-12-19 19:00:00";		//活动开始时间
	const END_TIME="2013-12-21 23:59:59";			//活动结束时间
	const RICE_BALL_ID=152;					//汤圆id(152),测试用三叶草
	//国庆活动、中秋活动、暖冬活动model可通用
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
	
	//查询主播榜（暖冬）
	private  function getDoteyRiceBallRank($start_time,$end_time)
	{
		$doteyRank=self::$moonFestivalModel->getDoteyRank($start_time,$end_time,self::RICE_BALL_ID);
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
				'isDotey'=>$isDotey,
				'update_time'=>date("m-d H:i:s",$row['max_time'])
			);
	
			if($i>=10)
				break;
			$i++;
		}
	
		return $result;
	}
	
	//查询富豪榜（暖冬）
	private function getUserRiceBallRank($start_time,$end_time)
	{
		$userRank=self::$moonFestivalModel->getUserRank($start_time,$end_time,self::RICE_BALL_ID);
		$result=array();
		$i=1;
		foreach ($userRank as $row)
		{
			$userInfo=self::$userService->getUserFrontsAttributeByCondition($row['uid'],true,false);
			$result[$row['uid']]=array(
				'rank_order'=>$i,
				'rk'=>$userInfo['rk'],
				'nk'=>$userInfo['nk'],
				'gift_num'=>$row['gift_num'],
				'update_time'=>date("m-d H:i:s",$row['max_time'])
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
		$pageData['DoteyRiceBallRank']=$this->getDoteyRiceBallRank($stime,$etime);
		$pageData['UserRiceBallRank']=$this->getUserRiceBallRank($stime,$etime);
		return $pageData;
	}
}
	
?>