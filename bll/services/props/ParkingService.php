<?php
/**
 * 停车位服务层
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */

class ParkingService extends PipiService{
	
	protected static $userJsonInfoService;
	
	public function __construct(PipiController $pipiController = null)
	{
		parent::__construct($pipiController);
	
		if(empty(self::$userJsonInfoService))
		{
			self::$userJsonInfoService=new UserJsonInfoService();
		}
	
	}
	
	//获取档期停车位列表
	public function getParkingListByArchives($archives_id){
		$otherRedisModel=new OtherRedisModel();
		$parkingList=$otherRedisModel->getArchivesParkingList($archives_id);
		return $parkingList;
	}
	
	//更新档期停车位列表
	public function updateCarToParkingList($uid,$archives_id)
	{
		$userinfo=self::$userJsonInfoService->getUserInfo($uid,false);
		
		//座架有效
		$timeStamp=time();
		if(isset($userinfo['car']) && $userinfo['car'] && is_array($userinfo['car']) && $userinfo['car']['vt'] >0 && $userinfo['car']['vt'] < $timeStamp){
			$zmq=$this->getZmq();
			$newdata=array('archives_id'=>$archives_id);
			return $zmq->sendZmqMsg(609,array('type'=>'rob_parking','uid'=>$uid,'json_info'=>$newdata));
		}
		else 
		{	
			return false;
		}
	}
	
}

?>