<?php
/**
 * 主播节目分类搜索计划任务脚本
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package controllers
 * @subpackage days
 */
class DoteyCategoryIndexCommand extends PipiConsoleCommand {
	
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
		/**
		 * 主播分类搜索统计,每6分钟跑一次脚本
		 */
		public function actionIndex(){
			$archivesDbCommand = $this->archives_db->createCommand();
			$commonDbCommand = $this->common_db->createCommand();
			$archivesDbCommand->setText('SELECT a.record_id,a.live_time,a.start_time,a.status,a.sub_title,b.archives_id,b.title,b.uid FROM `web_live_records` a RIGHT JOIN web_archives b ON a.archives_id = b.archives_id WHERE a.`status` IN (0,1) and b.is_hide = 0');
			$archives = $archivesDbCommand->queryAll();
			
			if(empty($archives)){
				return array();
			}
			
			$channelService = new ChannelService();
			$consumeService = new ConsumeService();
			$uids = array_keys($channelService->buildDataByIndex($archives,'uid'));//必须在前面
			$archives = $channelService->buildDataByIndex($archives,'archives_id');//一个用户可能有多个档期
			//print_r($archives);die();
			list($area_channel_id,) = $channelService->getChannelIdByChannelName(array(CHANNEL_AREA,''),'name');
			$channels = $channelService->getChannelDoteyByUids($uids,$area_channel_id);
			if($channels){
				$channels = $channelService->buildDataByKey($channels,'uid');
			}
			$consumes = $consumeService->getConsumesByUids($uids);
			$newData = '';
			foreach($archives as $archive_id => $archive){
				$dim = $channelService->getArrayDim($archive);
				if($dim > 1){
					$archive = array_pop($archive);
				}
				$uid = $archive['uid'];
				$consume = $channel = array();
				if(isset($consumes[$uid])){
					$consume = $consumes[$uid];
				}else{
					$consume['dotey_rank'] = 0;
				}
				if(isset($channels[$uid])){
					$channel = $channels[$uid];
					$bitChannel = 0;
					foreach($channel as $_ch){
						$bitChannel = $channelService->grantBit($bitChannel,(int)$_ch['sub_channel_id']);
					}
				}else{
					$bitChannel = 0;
				}
				$newData .= ($newData ? ',' : '').'('.$uid.',"'.$archive['archives_id'].'","'.$consume['dotey_rank'].'",'.$bitChannel.','.$archive['status'].',"'.mysql_escape_string($archive['title']).'","'.mysql_escape_string($archive['sub_title']).'",'.$archive['live_time'].','.$archive['start_time'].')';
			}
			$commonDbCommand->setText('delete from web_dotey_category_index');
			$commonDbCommand->execute();
			$commonDbCommand->setText( ' insert into web_dotey_category_index (uid,archives_id,rank,channel_area_id,status,title,sub_title,live_time,start_time) values '.$newData);
			$flag = $commonDbCommand->execute();
			if($flag){
				echo "写入档期节目成功success\n\r";
			}else{
				echo "写入档期节目失败fail\n\r";
			}
			
		}
		
		/**
		 * 主播今日推荐
		 */
		public function actionTodayRecommand(){
			$consumeRecordsDb = $this->consume_records_db->createCommand();
			$archivesDbCommand = $this->archives_db->createCommand();
			$commonDbCommand = $this->common_db->createCommand();
			list($ystartTime,$yendTime) = $this->pushDownDaysTime(1,false);
			list($wstartTime,$wendTime) = $this->pushDownWeekTime(1,false);
			$consumeService = new ConsumeService();
			$consumeRecordsDb->setText("SELECT uid,sum(charm) charms FROM `web_dotey_charm_records` WHERE create_time BETWEEN $ystartTime AND  $yendTime GROUP BY uid ORDER BY charms  DESC LIMIT 15");
			$yesterDayTop = $consumeRecordsDb->queryAll();
			$yesterDayTop = $consumeService->buildDataByIndex($yesterDayTop,'uid');
			$consumeRecordsDb->setText("SELECT uid,sum(charm) charms FROM `web_dotey_charm_records` WHERE create_time BETWEEN $wstartTime AND  $wendTime GROUP BY uid ORDER BY charms  DESC LIMIT 15");
			$weekTop = $consumeRecordsDb->queryAll();
			$weekTop = $consumeService->buildDataByIndex($weekTop,'uid');
			if(empty($yesterDayTop) && empty($weekTop)){
				return array();
			}
			//昨日排行榜与上周排行榜去重，优先与昨日排行榜为主
			foreach ($yesterDayTop as $uid=>$yTop){
				if(isset($weekTop[$uid])){
					unset($weekTop[$uid]);
				}
			}
			$newArray = array();
			foreach($yesterDayTop as $uid=>$yTop){
				$yTop['type'] = 1;//昨日魅力值排序在前
				$newArray[$uid] = $yTop;
			}
			foreach($weekTop as $uid=>$wTop){
				$wTop['type'] = 0;
				$newArray[$uid] = $wTop;
			}
			
			$uids = array_keys($newArray);
			$archivesDbCommand->setText('SELECT DISTINCT b.archives_id,b.uid FROM `web_live_records` a RIGHT JOIN web_archives b ON a.archives_id = b.archives_id WHERE b.uid in('.implode($uids,',').') and b.is_hide = 0');
			$archives = $archivesDbCommand->queryAll();
			if(empty($archives)){
				return array();
			}
			$newData = '';
			foreach ($archives as  $archive){
				$charms = $newArray[$archive['uid']];
				$newData .= ($newData ? ',' : '').'('.$archive['uid'].','.$archive['archives_id'].','.$charms['type'].','.$charms['charms'].')';
			}
			
			$commonDbCommand->setText('delete from web_dotey_today_recommand');
			$commonDbCommand->execute();
			$commonDbCommand->setText( ' insert into web_dotey_today_recommand (uid,archives_id,type,charms) values '.$newData);
			$flag = $commonDbCommand->execute();
			if($flag){
				$todayRecommandModel = new DoteyTodayRecommandModel();
				$cacheModel = new OtherRedisModel();
				$data = $todayRecommandModel->getAllTodayRecommand();
				$cacheModel->setDoteyTodayRecommand($consumeService->arToArray($data));
				echo "写入今日推荐成功success\n\r";
			}else{
				echo "写入今日推荐失败fail\n\r";
			}
		}
		
		//上周唱将主播
		public function actionLastWeekStarSinger()
		{
			$commonDbCommand = $this->common_db->createCommand();
			$userCommoand = $this->user_db->createCommand();
			
			$commonDbCommand->setText('SELECT DISTINCT uid FROM `web_dotey_channel` where channel_id = 1');
			$users = $commonDbCommand->queryAll();
			
			$userService = new UserService();
			$users = $userService->buildDataByIndex($users,'uid');
			$uids = array_keys($users);
			
			$userCommoand->setText('select uid from web_user_base where user_status=0 and uid in ('.implode(',',$uids).')');
			$users = $userCommoand->queryAll();
			$users = $userService->buildDataByIndex($users,'uid');
			$uids = array_keys($users);
			
			list($startTime,$endTime) = $this->pushDownWeekTime(1,false);
			$consumeCommand = $this->consume_db->createCommand();
			$sql='SELECT to_uid uid,count(*) songs from web_user_song where  is_handle = 1 AND to_uid IN ('.implode(',',$uids).') and update_time >= '.$startTime.' and update_time <= '.$endTime.' GROUP BY to_uid HAVING songs>0 ORDER BY songs DESC LIMIT 10';
			$consumeCommand->setText($sql);
			//echo $sql."\n";
			$starSinger = $consumeCommand->queryAll();
			$starSinger=$userService->buildDataByIndex($starSinger,'uid');
			$starSinger=array_keys($starSinger);
			$otherRedisModel=new OtherRedisModel();
			$result=$otherRedisModel->setLastWeekStarSinger($starSinger);
			if($result)
			{
				echo "上周唱将主播生成成功 \n";
			}
			else
			{
				echo "上周唱将主播生成失败 \n";
			}
			//print_r($otherRedisModel->getLastWeekStarSinger());
		}		
		
}