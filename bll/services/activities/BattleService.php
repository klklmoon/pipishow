<?php
/**
 * @author hexin
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */

class BattleService extends PipiService{

	const ACTIVITY_NAME = '女神争夺战';
	const START_TIME = "2013-08-14 12:00:00";	//活动开始时间
	const END_TIME = "2013-08-16 23:59:59";		//活动结束时间
	const BATTLE_16_START_TIME = "2013-08-17 12:00:00"; //16强开始
	const BATTLE_16_END_TIME = "2013-08-17 23:59:59"; //16强结束
	const BATTLE_8_START_TIME = "2013-08-19 12:00:00"; //8强开始
	const BATTLE_8_END_TIME = "2013-08-19 23:59:59"; //8强结束
	const BATTLE_4_START_TIME = "2013-08-21 12:00:00"; //4强开始
	const BATTLE_4_END_TIME = "2013-08-21 23:59:59"; //4强结束
	const BATTLE_2_START_TIME = "2013-08-23 12:00:00"; //2强开始
	const BATTLE_2_END_TIME = "2013-08-23 23:59:59"; //2强结束
	const RICE_BALL_ID = 45;					//女神id(198),测试用三叶草(45)
	const NUMBER_LIST = 20;						//榜单人数
	//国庆活动、中秋活动、暖冬活动、圣诞活动model可通用
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
	
	//查询主播榜
	private  function getDoteyTop($start_time,$end_time)
	{
		$doteyRank=self::$moonFestivalModel->getDoteyRank($start_time,$end_time,self::RICE_BALL_ID,40);
		$uids = array_keys($this->buildDataByIndex($doteyRank, 'uid'));
		$userInfos = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
		$result = array();
		$i=1;
		foreach ($doteyRank as $row)
		{
			$isDotey = self::$userService->hasBit((int)$userInfos[$row['uid']]['ut'],USER_TYPE_DOTEY);
	
			if(!$isDotey)
				continue;
				
			$result[$row['uid']]=array(
				'rank_order'=>$i,
				'dk'=>$userInfos[$row['uid']]['dk'],
				'nk'=>$userInfos[$row['uid']]['nk'],
				'gift_num'=>$row['gift_num'],
				'isDotey'=>$isDotey,
				'update_time'=>date("m-d H:i",$row['max_time'])
			);
	
			if($i>=self::NUMBER_LIST)
				break;
			$i++;
		}
	
		return $result;
	}
	
	//查询富豪榜前三名
	private function getUsers($start_time,$end_time,$dotey_uids, $num = 3)
	{
		$result = $uids = array();
		foreach($dotey_uids as $dotey_uid){
			$result[$dotey_uid] = self::$moonFestivalModel->getUsers($start_time,$end_time,self::RICE_BALL_ID,$dotey_uid,$num);
			$uids = array_merge($uids, array_keys($this->buildDataByIndex($result[$dotey_uid], 'uid')));
		}
		$userInfos = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
		foreach($result as &$users){
			foreach($users as &$user){
				$user['rk'] = $userInfos[$user['uid']]['rk'];
				$user['nk'] = $userInfos[$user['uid']]['nk'];
			}
		}
		return $result;
	}
	
	//获取页面数据
	public function getActivityPageData()
	{
		$stime=strtotime(self::START_TIME);
		$etime=strtotime(self::END_TIME);
		$pageData=$this->getDoteyTop($stime,$etime);
		if(!empty($pageData)){
			$dotey_uids = array_keys($pageData);
			$users = $this->getUsers($stime,$etime,$dotey_uids);
			foreach($pageData as $uid => &$data){
				$data['users'] = $users[$uid];
			}
		}
		return $pageData;
	}
	
	//获取对战数据
	public function getBattle()
	{
		$now = Yii::app()->request->getParam('time', '');
		$time = empty($now) ? time() : strtotime($now);
		$res = Yii::app()->request->getParam('res','');
		if($res == 16){
			$time = strtotime(self::BATTLE_8_START_TIME);
		}elseif($res == 8){
			$time = strtotime(self::BATTLE_4_START_TIME);
		}elseif($res == 4){
			$time = strtotime(self::BATTLE_2_START_TIME);
		}elseif($res == 2){
			$time = strtotime('+1 day', strtotime(self::BATTLE_2_START_TIME));
		}
		
		if($time <= strtotime(self::BATTLE_16_END_TIME)){
			$battle = 16;
		}elseif($time <= strtotime(self::BATTLE_8_END_TIME)){
			$battle = 8;
		}elseif($time <= strtotime(self::BATTLE_4_END_TIME)){
			$battle = 4;
		}elseif($time <= strtotime(self::BATTLE_2_END_TIME)){
			$battle = 2;
		}else{
			$battle = 1;
		}
		$doteys = OtherRedisModel::getInstance()->getBattle($battle);
		$doteys = empty($doteys) ? array() : $doteys;
		
		if($res == ''){
			if($time >= strtotime(self::BATTLE_8_START_TIME) && $time <= strtotime(self::BATTLE_8_END_TIME)){
				$doteys = $this->getBattleRank($doteys);
			}elseif($time >= strtotime(self::BATTLE_4_START_TIME) && $time <= strtotime(self::BATTLE_4_END_TIME)){
				$doteys = $this->getBattleRank($doteys);
			}elseif($time >= strtotime(self::BATTLE_2_START_TIME) && $time <= strtotime(self::BATTLE_2_END_TIME)){
				$doteys = $this->getBattleRank($doteys);
			}
		}
		
		if($time < strtotime(self::BATTLE_16_START_TIME)){
			$start_time = self::START_TIME;
			$end_time = self::END_TIME;
		}elseif($time < strtotime(self::BATTLE_8_START_TIME)){
			$start_time = self::BATTLE_16_START_TIME;
			$end_time = self::BATTLE_16_END_TIME;
		}elseif($time < strtotime(self::BATTLE_4_START_TIME)){
			$start_time = self::BATTLE_8_START_TIME;
			$end_time = self::BATTLE_8_END_TIME;
		}elseif($time < strtotime(self::BATTLE_2_START_TIME)){
			$start_time = self::BATTLE_4_START_TIME;
			$end_time = self::BATTLE_4_END_TIME;
		}else{
			$start_time = self::BATTLE_2_START_TIME;
			$end_time = self::BATTLE_2_END_TIME;
		}
		
		$uids = array();
		foreach($doteys as $d){
			if($d['uid'] > 0) $uids[] = $d['uid'];
		}
		
		$data = self::$moonFestivalModel->getBattleResult(strtotime($start_time), strtotime($end_time), self::RICE_BALL_ID, $uids);
		$data = $this->buildDataByIndex($data, 'uid');
		$infos = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
		$users = $this->getUsers(strtotime($start_time),strtotime($end_time),$uids);
		$userService = new UserService();
		$avatars = $userService->getUserAvatarsByUids($uids, 'middle');
		foreach($doteys as &$d){
			$d['number'] = isset($infos[$d['uid']]['num']) ? $infos[$d['uid']]['num']['n'] : 0;
			$d['nk'] = $infos[$d['uid']]['nk'];
			$d['dk'] = $infos[$d['uid']]['dk'];
			$d['pic'] = $avatars[$d['uid']];
			$d['num'] = $data[$d['uid']]['gift_num'];
			$d['users'] = $users[$d['uid']];
		}
		return $doteys;
	}
	
	/**
	 * 获取上次对战结果中晋级的主播列表
	 * @param array $doteys
	 * @return array
	 */
	public function getBattleRank($doteys){
		$result = array();
		$count = count($doteys);
		for($i=0; $i<$count/2; $i++){
			if($doteys[$i]['result'] == true) $result[] = array('uid' => $doteys[$i]['uid'], 'result' => false);
			else $result[] = array('uid' => $doteys[$count-1-$i]['uid'], 'result' => false);
		}
		return $result;
	}
	
	/**
	 * 对战
	 * @param array $doteys
	 * @param string $start_time
	 * @param string $end_time
	 * @return array
	 */
	public function battle($doteys, $start_time, $end_time){
		$count = count($doteys);
		for($i = 0; $i <= $count / 2; $i++){
			if($doteys[$i]['uid'] > 0){
				if($doteys[$count-1-$i]['uid'] == 0) $doteys[$i]['result'] = true;
				else{
					$result = MoonFestivalModel::model()->getBattleResult(strtotime($start_time),strtotime($end_time),BattleService::RICE_BALL_ID,array($doteys[$i]['uid'], $doteys[$count-1-$i]['uid']));
					if(!empty($result) && $result[0]['uid'] == $doteys[$i]['uid']) $doteys[$i]['result'] = true;
					elseif(!empty($result) && $result[0]['uid'] == $doteys[$count-1-$i]['uid']) $doteys[$count-1-$i]['result'] = true;
				}
			}
		}
		return $doteys;
	}
}
	
?>