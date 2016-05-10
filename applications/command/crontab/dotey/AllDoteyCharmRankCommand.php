<?php
/**
 * 所有主播魅力值排行
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package 
 */
class AllDoteyCharmRankCommand extends PipiConsoleCommand {
		
		/**
		 * @var CDbConnection 用户库
		 */
		protected $user_db;
		
		/**
		 * @var CDbConnection 新版消费库记录操作
		 */
		protected $consume_records_db;
		
		public function beforeAction($action,$params){
			parent::beforeAction($action, $params);
			$this->consume_records_db =  Yii::app()->db_consume_records;
			$this->user_db =  Yii::app()->db_user;
			return true;
		}
		
		public function actionTodayRank(){
			$start_time = microtime(true);
			$consumeDbCommand = $this->consume_records_db->createCommand();
			$userDbCommand=$this->user_db->createCommand();
			list($start_time,$end_time) = $this->pushDownDaysTime(0,false);
			$userDbCommand->setText("SELECT * FROM `web_dotey_base` WHERE `status`=1");
			$doteyInfo=$userDbCommand->queryAll();
			$doteyRank=array();
			foreach($doteyInfo as $row){
				$consumeDbCommand->setText("SELECT sum(charm) as charm FROM `web_dotey_charm_records` where uid={$row['uid']} AND create_time>={$start_time} AND create_time<={$end_time}");
				$doteyCharm=$consumeDbCommand->queryAll();
				$doteyRank[$row['uid']]=$doteyCharm[0]['charm']>0?$doteyCharm[0]['charm']:0;
			}
			arsort($doteyRank);
			$i=1;
			$dotey=array();
			foreach($doteyRank as $key=>$val){
				$dotey[$key]=$i;
				$i++;
			}
			$otherRedisModel=new OtherRedisModel();
			$otherRedisModel->saveAllDoteyCharmTodayRank($dotey);
			$end_time = microtime(true);
			echo date("Y-m-d H:i:s").' '.__CLASS__.':'.__FUNCTION__.' 脚本运行'.round(($end_time-$start_time)/1000, 4).'秒'."\n";
		}
		
		public function actionWeekRank(){
			$start_time = microtime(true);
			$consumeDbCommand = $this->consume_records_db->createCommand();
			$userDbCommand=$this->user_db->createCommand();
			list($start_time,$end_time) = $this->pushDownWeekTime(0,false);
			$userDbCommand->setText("SELECT * FROM `web_dotey_base` WHERE `status`=1");
			$doteyInfo=$userDbCommand->queryAll();
			$doteyRank=array();
			foreach($doteyInfo as $key=>$row){
				$consumeDbCommand->setText("SELECT sum(charm) as charm FROM `web_dotey_charm_records` where uid={$row['uid']} AND create_time>={$start_time} AND create_time<={$end_time}");
				$doteyCharm=$consumeDbCommand->queryAll();
				$doteyRank[$row['uid']]=$doteyCharm[0]['charm']>0?$doteyCharm[0]['charm']:0;
			}
			arsort($doteyRank);
			$i=1;
			$dotey=array();
			foreach($doteyRank as $key=>$val){
				$dotey[$key]=$i;
				$i++;
			}
			$otherRedisModel=new OtherRedisModel();
			$otherRedisModel->saveAllDoteyCharmWeekRank($dotey);
			$end_time = microtime(true);
			echo date("Y-m-d H:i:s").' '.__CLASS__.':'.__FUNCTION__.' 脚本运行'.round(($end_time-$start_time)/1000, 4).'秒'."\n";
		}
	
		/**
		 * 定时查询保存主播等级人数
		 */
		public function actionDoteyRankCount(){
			$uids = DoteyBaseModel::model()->getAllDoteyUids();
			$consumeService = new ConsumeService();
			$info = $consumeService->getConsumesByUids($uids);
			$array = array('皇冠主播' => 0, '蓝钻主播' => 0, '红心主播' => 0);
			foreach($info as $dotey){
				if($dotey['dotey_rank'] >= 11) $array['皇冠主播'] += 1;
				elseif($dotey['dotey_rank'] >= 6 && $dotey['dotey_rank'] < 11) $array['蓝钻主播'] += 1;
				else $array['红心主播'] += 1;
			}
			OtherRedisModel::getInstance()->setDoteyRankCount($array);
		}
	
		
		
}