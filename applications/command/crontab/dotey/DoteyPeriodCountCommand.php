<?php
/**
 * 主播时段统计脚本,主要用于主播排序，脚本一天跑一次
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package controllers
 * @subpackage days
 */
class DoteyPeriodCountCommand extends PipiConsoleCommand {
	
	/**
		 * @var CDbConnection 新版消费库操作
		 */
		protected $consume_db;
		
		/**
		 * @var CDbConnection 新版消费库记录操作
		 */
		protected $consume_records_db;
		
		/**
		 * @var CDbConnection 新版消费库记录操作
		 */
		protected $user_db;
		
		/**
		 * @var CDbConnection 通用库操作
		 */
		protected $common_db;
		
		/**
		 * @var CDbConnection 档期库操作
		 */
		protected $archives_db;
		
		public function beforeAction($action,$params){
			parent::beforeAction($action, $params);
			$this->consume_db = Yii::app()->db_consume;
			$this->consume_records_db =  Yii::app()->db_consume_records;
			$this->user_db = Yii::app()->db_user;;
			$this->common_db = Yii::app()->db_common;
			$this->archives_db = Yii::app()->db_archives;
			return true;
		}
		
		public function actionIndex(){
			$doteyCommand = $this->user_db->createCommand();
			$consumeRecordsCommand = $this->consume_records_db->createCommand();
			$consumeCommand = $this->consume_db->createCommand();
			$commonCommand = $this->common_db->createCommand();
			$archivesCommand = $this->archives_db->createCommand();
			$doteyCommand->setText('select * from web_dotey_base where status = 1');
			$doteys = $doteyCommand->queryAll();
			$commonCommand->setText('delete from web_dotey_period_count');
			$commonCommand->execute();
			$commonCommand->setText('replace into web_dotey_period_count (uid,yesterday_songs,week_songs,month_songs,super_songs,yesterday_charms,week_charms,month_charms,super_charms,yesterday_livetime,week_livetime,month_livetime,super_livetime) values (
			:uid,:yesterday_songs,:week_songs,:month_songs,:super_songs,:yesterday_charms,:week_charms,:month_charms,:super_charms,:yesterday_livetime,:week_livetime,:month_livetime,:super_livetime)');
			$timestamp = time();
			list($ystartTime,$yendTime) = $this->pushDownDaysTime(1,false);
			list($wstartTime,$wendTime) = $this->pushDownWeekTime(0,false);
			list($mstartTime,$mendTime) = $this->pushDownMonthTime(0,false);
			echo "开始转移主播时段数据\n\r";
			$i = 0;
			foreach($doteys as $dotey){
				$uid = $dotey['uid'];
				$commonCommand->bindValue(':uid',$uid);
				
				//昨日点歌数
				$yesterDaysongs = $this->getDoteySongsByTimes($uid,$ystartTime,$yendTime,$consumeCommand);
				if($yesterDaysongs){
					$commonCommand->bindValue(':yesterday_songs',(int)$yesterDaysongs['songs']);
				}else{
					$commonCommand->bindValue(':yesterday_songs',0);
				}
				
				//本周点歌数
				$weeksongs = $this->getDoteySongsByTimes($uid,$wstartTime,$wendTime,$consumeCommand);
				if($weeksongs){
					$commonCommand->bindValue(':week_songs',(int)$weeksongs['songs']);
				}else{
					$commonCommand->bindValue(':week_songs',0);
				}
				
				//本月点歌数
				$monthsongs = $this->getDoteySongsByTimes($uid,$mstartTime,$mendTime,$consumeCommand);
				if($monthsongs){
					$commonCommand->bindValue(':month_songs',(int)$monthsongs['songs']);
				}else{
					$commonCommand->bindValue(':month_songs',0);
				}
				
				//超级点唱数
				$supersongs = $this->getDoteySongsByTimes($uid,0,$timestamp,$consumeCommand);
				if($supersongs){
					$commonCommand->bindValue(':super_songs',(int)$supersongs['songs']);
				}else{
					$commonCommand->bindValue(':super_songs',0);
				}
				
				//昨日魅力数
				$yesterCharms = $this->getDoteyCharmByTimes($uid,$ystartTime,$yendTime,$consumeRecordsCommand);
				if($yesterCharms){
					$commonCommand->bindValue(':yesterday_charms',(int)$yesterCharms['charm_total']);
				}else{
					$commonCommand->bindValue(':yesterday_charms',0);
				}
				
				//本周魅力数
				$weekCharms = $this->getDoteyCharmByTimes($uid,$wstartTime,$wendTime,$consumeRecordsCommand);
				if($weekCharms){
					$commonCommand->bindValue(':week_charms',(int)$weekCharms['charm_total']);
				}else{
					$commonCommand->bindValue(':week_charms',0);
				}
				
				//本月魅力数
				$monthCharms = $this->getDoteyCharmByTimes($uid,$mstartTime,$mendTime,$consumeRecordsCommand);
				if($monthCharms){
					$commonCommand->bindValue(':month_charms',(int)$monthCharms['charm_total']);
				}else{
					$commonCommand->bindValue(':month_charms',0);
				}
				
				//超级魅力数
				$superCharms = $this->getDoteyCharmByTimes($uid,0,$timestamp,$consumeRecordsCommand);
				if($superCharms){
					$commonCommand->bindValue(':super_charms',(int)$superCharms['charm_total']);
				}else{
					$commonCommand->bindValue(':super_charms',0);
				}
				
				//昨日直播时长
				$yesterLive = $this->getDoteyLiveTime($uid,$ystartTime,$yendTime,$archivesCommand);
				if($yesterLive){
					$commonCommand->bindValue(':yesterday_livetime',(int)$yesterLive['duration']);
				}else{
					$commonCommand->bindValue(':yesterday_livetime',0);
				}
				
				//本周直播时长
				$weekLive = $this->getDoteyLiveTime($uid,$wstartTime,$wendTime,$archivesCommand);
				if($weekLive){
					$commonCommand->bindValue(':week_livetime',(int)$weekLive['duration']);
				}else{
					$commonCommand->bindValue(':week_livetime',0);
				}
				
				//本月直播时长
				$monthLive = $this->getDoteyLiveTime($uid,$mstartTime,$mendTime,$archivesCommand);
				if($monthLive){
					$commonCommand->bindValue(':month_livetime',(int)$monthLive['duration']);
				}else{
					$commonCommand->bindValue(':month_livetime',0);
				}
				
				//超级直播时长
				$superLive = $this->getDoteyLiveTime($uid,0,$timestamp,$archivesCommand);
				if($superLive){
					$commonCommand->bindValue(':super_livetime',(int)$superLive['duration']);
				}else{
					$commonCommand->bindValue(':super_livetime',0);
				}
				
				$commonCommand->execute();
			}
			
			echo "转移主播时段数据结束\n\r共转移 {$i}条数据\n\r";
		}
		
		public function getDoteyCharmByTimes($uid,$startTime,$endTime,CDbCommand $command){
			$command->setText('SELECT uid,sum(charm) charm_total FROM `web_dotey_charm_records` where uid=:uid and create_time >= :startTime and create_time <=:endTime limit 1');
			$command->bindParam(':uid',$uid);
			$command->bindParam(':startTime',$startTime);
			$command->bindParam(':endTime',$endTime);
			return $command->queryRow();
		}
		
		public function getDoteySongsByTimes($uid,$startTime,$endTime,CDbCommand $command){
			$command->setText('SELECT to_uid uid,count(*) songs from web_user_song where to_uid = :uid and is_handle = 1 and create_time >= :startTime and create_time <=:endTime limit 1');
			$command->bindParam(':uid',$uid);
			$command->bindParam(':startTime',$startTime);
			$command->bindParam(':endTime',$endTime);
			return $command->queryRow();
		}
		
		public function getDoteyLiveTime($uid,$startTime,$endTime,CDbCommand $command){
			$command->setText('SELECT GROUP_CONCAT(archives_id) archives_id FROM web_archives where uid = :uid and sub_id = 0');
			$command->bindParam(':uid',$uid);
			$archives_id = $command->queryScalar();
			if(empty($archives_id)){
				return array();
			}
			$command->setText('SELECT sum(duration) duration FROM web_live_records WHERE archives_id IN ('.$archives_id.') AND live_time >= :startTime and live_time <=:endTime limit 1');
			$command->bindParam(':startTime',$startTime);
			$command->bindParam(':endTime',$endTime);
			$duration = $command->queryScalar();
			return array('uid'=>$uid,'duration'=>$duration);
		}
		
		
		
}