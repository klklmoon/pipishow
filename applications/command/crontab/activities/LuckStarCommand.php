<?php
/**
 * @author leiwei <leiwei@pipi.cn> 2013-08-26
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2013 show.pipi.cn
 * @license
 */
class LuckStarCommand extends PipiConsoleCommand {
	
	protected $consume_records_db;
	
	public function beforeAction($action,$params){
		parent::beforeAction($action, $params);
		$this->consume_records_db =  Yii::app()->db_consume_records;
		return true;
	}
	
	public function actionSaveLuckStar(){
		$changeRelation = Yii::app()->params->change_relation;
		$rmbToEgg = isset($changeRelation['rmb_to_pipiegg'])?$changeRelation['rmb_to_pipiegg']:1;
		//活动配置
		$luckStarConfig=array(
			'gift_id'=>182,  //幸运星Id
			'type'=>3,		  //奖励类型
			'multiple'=>500,  //倍数
			'award'=>200*$rmbToEgg,	  //奖励皮蛋数
		);
		$consumeRecordsCommand = $this->consume_records_db->createCommand();
		$consumeRecordsCommand->setText("SELECT uid,count(*) as num FROM `web_user_pipiegg_records`  WHERE source='activity' AND sub_source='luckStar' GROUP BY uid LIMIT 0,3");
		$superRecord = $consumeRecordsCommand->queryAll();
		$superLuckStar=array();
		$luckGiftService=new LuckyGiftService();
		$userJsonInfoService=new UserJsonInfoService();
		if($superRecord){
			foreach($superRecord as $key=>$row){
				$superLuckStar[$key]['uid']=$row['uid'];
				$userInfo=$userJsonInfoService->getUserInfo($row['uid'],false);
				$superLuckStar[$key]['nickname']=$userInfo['nk'];
				$superLuckStar[$key]['rank']=$userInfo['rk'];
				$superLuckStar[$key]['num']=$row['num'];
			}
			
			
		}
		$today=date('Y-m-d');
		//今日幸运星
		$todayCondition['target_id']=$luckStarConfig['gift_id'];
		$todayCondition['type']=$luckStarConfig['type'];
		$todayCondition['num']=$luckStarConfig['multiple'];
		$todayCondition['stime']=strtotime($today.' 22:00:00')-86400;
		$todayCondition['etime']=strtotime($today.' 22:00:00');
		
		$todayStar=$luckGiftService->getUserAwardRecords($todayCondition);
		$todayLuckStar=array();
		if(isset($todayStar['uid'])){
			$todayLuckStar['create_time']=$todayStar['create_time'];
			$userInfo=$userJsonInfoService->getUserInfo($todayStar['uid'],false);
			$todayLuckStar['nickname']=$userInfo['nk'];
			$todayLuckStar['rank']=$userInfo['rk'];
			$todayLuckStar['award']=$luckStarConfig['award'];
		}
		
		
		//昨日幸运星
		$yesterdayCondition['target_id']=$luckStarConfig['gift_id'];
		$yesterdayCondition['type']=$luckStarConfig['type'];
		$yesterdayCondition['num']=$luckStarConfig['multiple'];
		$yesterdayCondition['stime']=strtotime($today.' 22:00:00')-86400*2;
		$yesterdayCondition['etime']=strtotime($today.' 22:00:00')-86400;
		$yesterdayStar=$luckGiftService->getUserAwardRecords($yesterdayCondition);
		$yesterdayLuckStar=array();
		if(isset($yesterdayStar['uid'])){
			$yesterdayLuckStar['create_time']=$yesterdayStar['create_time'];
			$_userInfo=$userJsonInfoService->getUserInfo($yesterdayStar['uid'],false);
			$yesterdayLuckStar['nickname']=$_userInfo['nk'];
			$yesterdayLuckStar['rank']=$_userInfo['rk'];
			$yesterdayLuckStar['award']=$luckStarConfig['award'];
		}
		echo "写入每日幸运星数据:".json_encode(array('tStar'=>$todayLuckStar,'yStar'=>$yesterdayLuckStar,'sStar'=>$superLuckStar))."\r\n";
		$luckGiftService->saveLuckStar(array('tStar'=>$todayLuckStar,'yStar'=>$yesterdayLuckStar,'sStar'=>$superLuckStar));
	}	
}