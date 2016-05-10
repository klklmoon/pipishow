<?php
/**
 * @author hexin
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2013 show.pipi.cn
 * @license
 */
class BattleCommand extends PipiConsoleCommand {
	
	protected $consume_records_db;
	
	public function beforeAction($action,$params){
		parent::beforeAction($action, $params);
		$this->consume_records_db =  Yii::app()->db_consume_records;
		return true;
	}
	
	/**
	 * 16、8、4、2对战，0点产生的有顺序的主播对战结果，0点执行一次
	 */
	public function actionBattle(){
		$day = date('Y-m-d');
// 		$day = '2013-08-24';
		$service = new BattleService();
		$battle = 0;
		//初始16强,不足16的补足16
		if($day == date('Y-m-d', strtotime('+1 day', strtotime(BattleService::END_TIME)))){
			$battle = 16;
			$doteys = $uids = $temp = $result = array();
			$temp = MoonFestivalModel::model()->getDoteyRank(strtotime(BattleService::START_TIME),strtotime(BattleService::END_TIME),BattleService::RICE_BALL_ID, 40);
			foreach($temp as $r){
				$uids[] = $r['uid'];
			}
			$userInfos = UserJsonInfoService::getInstance()->getUserInfos($uids, false);
			$i = 1;
			$userService = new UserService();
			foreach($temp as $t){
				$isDotey = $userService->hasBit((int)$userInfos[$t['uid']]['ut'],USER_TYPE_DOTEY);
				if($isDotey){
					$result[] = $t;
				}else continue;
				if($i>=16) break;
				$i++;
			}
			
			for($i = 1; $i <= 16; $i++){
				if(!isset($result[$i-1])) $doteys[$i-1] = array('uid'=>0, 'result'=>false);
				else $doteys[$i-1] = array('uid'=>$result[$i-1]['uid'], 'result'=>false);
			}
		//16强对战结果
		}elseif($day == date('Y-m-d', strtotime('+1 day', strtotime(BattleService::BATTLE_16_END_TIME)))){
			$battle = 8;
			$doteys = OtherRedisModel::getInstance()->getBattle(16);
			$doteys = $service->battle($doteys, BattleService::BATTLE_16_START_TIME, BattleService::BATTLE_16_END_TIME);
		//8强对战结果
		}elseif($day == date('Y-m-d', strtotime('+1 day', strtotime(BattleService::BATTLE_8_END_TIME)))){
			$battle = 4;
			$doteys = OtherRedisModel::getInstance()->getBattle(8);
			$doteys = $service->getBattleRank($doteys);
			$doteys = $service->battle($doteys, BattleService::BATTLE_8_START_TIME, BattleService::BATTLE_8_END_TIME);
		//4强对战结果
		}elseif($day == date('Y-m-d', strtotime('+1 day', strtotime(BattleService::BATTLE_4_END_TIME)))){
			$battle = 2;
			$doteys = OtherRedisModel::getInstance()->getBattle(4);
			$doteys = $service->getBattleRank($doteys);
			$doteys = $service->battle($doteys, BattleService::BATTLE_4_START_TIME, BattleService::BATTLE_4_END_TIME);
		//2强对战结果
		}elseif($day == date('Y-m-d', strtotime('+1 day', strtotime(BattleService::BATTLE_2_END_TIME)))){
			$battle = 1;
			$doteys = OtherRedisModel::getInstance()->getBattle(2);
			$doteys = $service->getBattleRank($doteys);
			$doteys = $service->battle($doteys, BattleService::BATTLE_2_START_TIME, BattleService::BATTLE_2_END_TIME);
		}
		if($battle > 0){
			OtherRedisModel::getInstance()->setBattle($battle, $doteys);
		}
	}
	
}