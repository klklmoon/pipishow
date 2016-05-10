<?php
/**
 * @author Su qian <suqian@pipi.cn> 2013-8-21
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2110 show.pipi.cn
 * @license 
 */

define('ACTIVITY_WEEK_TOESDAY',2);

define('USER_LOVE_MEDAL',23);
define('USER_DIAMOND_MEDAL',2);

class HappyTuesdayCommand extends PipiConsoleCommand {
	
	/**
	 * @var CDbConnection 新版消费库记录操作
	 */
	protected $user_db;
	/**
	 * @var CDbConnection 新版消费库记录操作
	 */
	protected $activity_db;
	/**
	 * @var CDbConnection 新版消费库记录操作
	 */	
	protected $consume_records_db;
	
	/**
	 * @var CDbConnection 新版消费库记录操作
	 */
	protected $consume_db;
	
	public function beforeAction($action,$params){
		parent::beforeAction($action, $params);
		$this->consume_db = Yii::app()->db_consume;
		$this->consume_records_db =  Yii::app()->db_consume_records;
		$this->user_db = Yii::app()->db_user;;
		$this->activity_db = Yii::app()->db_activity;
		//$this->common_db = Yii::app()->db_common;
		//$this->archives_db = Yii::app()->db_archives;
		//$this->weibo_db = Yii::app()->db_weibo;
		return true;
	}
		
	public function actionTuesDayRank(){
		$userDbCommand = $this->user_db->createCommand();
		$consumeRecordsCommand = $this->consume_records_db->createCommand();
		$activityDbCommand = $this->activity_db->createCommand();
		$timeStamp = time();
		$week = date('w',$timeStamp);
		if($week != ACTIVITY_WEEK_TOESDAY){
			echo 'error';
			return;
		}
		$endTime = strtotime(date('Y-m-d',$timeStamp).' 23:59:59');
		$doteyBases = $userDbCommand->setText('select * from web_dotey_base')->queryAll();
		$consumeService = new ConsumeService();
		
		$activityDbCommand->setText('delete from web_long_tuesday_doteyrank')->execute();
		$activityDbCommand->setText('delete from web_long_tuesday_medal')->execute();
		$activityDbCommand->setText('insert into web_long_tuesday_doteyrank (uid,dotey_rank,charm) values (:uid,:dotey_rank,:charm)');
		foreach($doteyBases as $doteyBase){
			$sumCharm = $consumeRecordsCommand->setText("select sum(charm) as sum_charm from web_dotey_charm_records where uid={$doteyBase['uid']} and create_time<={$endTime}")->queryScalar();
			$doteyRank=$consumeService->getDoteyRankByCharm($sumCharm);
			
			$activityDbCommand->bindValue(':uid',$doteyBase['uid']);
			$activityDbCommand->bindValue(':dotey_rank',isset($doteyRank['rank']) ? $doteyRank['rank'] : 0);
			$activityDbCommand->bindValue(':charm',$sumCharm >0 ? $sumCharm : 0);
			
			$activityDbCommand->execute();
		}
	}
	

	public function actionSendMedal(){
		$consumeRecordsCommand = $this->consume_records_db->createCommand();
		$consumeCommand = $this->consume_db->createCommand();
		$activityCommand = $this->activity_db->createCommand();
		
		$changeRelation = Yii::app()->params->change_relation;
		$rmbToPipiegg = isset($changeRelation['rmb_to_pipiegg'])?$changeRelation['rmb_to_pipiegg']:1;
		$medalService = new UserMedalService();
		$timeStamp = time();
		$week = date('w',$timeStamp);
		if($week != ACTIVITY_WEEK_TOESDAY){
			echo 'error';
			return;
		}
		$starTime =  strtotime(date('Y-m-d',$timeStamp).' 00:00:00');
		$endTime = strtotime(date('Y-m-d',$timeStamp).' 23:59:59');
		$consumes = $consumeRecordsCommand->setText("SELECT a.uid,to_uid,sum(pipiegg) s_pipiegg FROM web_user_giftsend_records a LEFT JOIN web_user_giftsend_relation_records b ON a.record_id=b.record_id WHERE a.recevier_type = 0 AND a.create_time >= {$starTime} AND a.create_time <={$endTime} and  is_onwer = 1  GROUP BY a.uid,to_uid ORDER BY a.uid,s_pipiegg DESC")->queryAll();
		if(empty($consumes)){
			return;
		}
		$consumes = $medalService->buildDataByKey($consumes,'uid');
		$userMedalModel = new UserMedalModel();
		foreach($consumes as $uid=>$consume){	
			foreach($consume as $_consume){
				$_loveMedal = array('uid'=>$uid,'mid'=>USER_LOVE_MEDAL,'type'=>2);
				$_diomandMedal = array('uid'=>$uid,'mid'=>USER_DIAMOND_MEDAL,'type'=>2);
				$doteyRank = $activityCommand->setText('select * from web_long_tuesday_doteyrank where uid = '.$_consume['to_uid'])->queryRow();
				$aleadyLoveDays = $activityCommand->setText("select * from web_long_tuesday_medal where uid = {$uid} and mid = {$_loveMedal['mid']} and dotey_uid={$_consume['to_uid']}")->queryRow();
				$aleadyDiomandDays = $activityCommand->setText("select * from web_long_tuesday_medal where uid = {$uid} and mid = {$_diomandMedal['mid']} and dotey_uid={$_consume['to_uid']}")->queryRow();
				
				$countLoveDays = $activityCommand->setText("select count(*) from web_long_tuesday_medal where uid = {$uid} and mid = {$_loveMedal['mid']}")->queryScalar();
				$countDiomandDays = $activityCommand->setText("select count(*) from web_long_tuesday_medal where uid = {$uid} and mid = {$_diomandMedal['mid']}")->queryScalar();
				if($doteyRank['dotey_rank'] <= 14){
					if(!$aleadyLoveDays && $countLoveDays < 5 && $_consume['s_pipiegg'] >= 100*$rmbToPipiegg){
						$loveMedal = $userMedalModel->findByAttributes($_loveMedal);
						if($loveMedal){
							if($loveMedal['vtime'] > $timeStamp){
								$loveMedal->updateCounters(array('vtime'=>3600*24),'rid = '.$loveMedal['rid']);
							}else{
								$loveMedal->vtime = $timeStamp+3600*24;
								$loveMedal->update();
							}
							unset($loveMedal);
						}else{
							$_loveMedal['ctime'] = $timeStamp;
							$_loveMedal['vtime'] = $timeStamp+3600*24;
							$newUserMedalModel = new UserMedalModel();
							$medalService->attachAttribute($newUserMedalModel,$_loveMedal);
							$newUserMedalModel->save();
							unset($newUserMedalModel);
						}
						$activityCommand->setText("INSERT INTO web_long_tuesday_medal (uid,mid,dotey_uid) values ({$uid},{$_loveMedal['mid']},{$_consume['to_uid']})")->execute();
						$medalService->sendZmqForUserMedal($uid);
					}
				}else{
					if(!$aleadyDiomandDays && $countDiomandDays < 5 &&  $_consume['s_pipiegg'] >= 200*$rmbToPipiegg){
						$userDimondMedal = $userMedalModel->findByAttributes($_diomandMedal);
						if($userDimondMedal){
							if($userDimondMedal['vtime'] > $timeStamp){
								$userDimondMedal->updateCounters(array('vtime'=>3600*24),'rid = '.$userDimondMedal['rid']);
							}else{
								$userDimondMedal->vtime = $timeStamp+3600*24;
								$userDimondMedal->update();
							}
							unset($userDimondMedal);
						}else{
							$_diomandMedal['ctime'] = $timeStamp;
							$_diomandMedal['vtime'] = $timeStamp+3600*24;
							$newUserMedalModel = new UserMedalModel();
							$medalService->attachAttribute($newUserMedalModel,$_diomandMedal);
							$newUserMedalModel->save();
							unset($newUserMedalModel);
						}
						$activityCommand->setText("INSERT INTO web_long_tuesday_medal (uid,mid,dotey_uid) values ({$uid},{$_diomandMedal['mid']},{$_consume['to_uid']})")->execute();
						$medalService->sendZmqForUserMedal($uid);
					}
					
				}
			
			}
		}
	}
}