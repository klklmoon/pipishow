<?php
/**
 * 主播排行榜脚本
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package controllers
 * @subpackage days
 */
class DoteyTopCommand extends PipiConsoleCommand {
	
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
		
		/**
		 * @var CDbConnection 微博数据库操作对象
		 */
		protected $weibo_db;
		
		public function beforeAction($action,$params){
			parent::beforeAction($action, $params);
			$this->consume_db = Yii::app()->db_consume;
			$this->consume_records_db =  Yii::app()->db_consume_records;
			$this->user_db = Yii::app()->db_user;;
			$this->common_db = Yii::app()->db_common;
			$this->archives_db = Yii::app()->db_archives;
			//$this->weibo_db = Yii::app()->db_weibo;
			return true;
		}
		
		public function actionIndex(){
		
		}
		
		public function actionSuperCharm(){
			$consumeRecordsCommand = $this->consume_records_db->createCommand();
			$consumeCommand = $this->consume_db->createCommand();
			$consumeCommand->setText('SELECT uid,rank,charm from web_user_consume_attribute ORDER BY charm DESC LIMIT 60');
			$consumes = $consumeCommand->queryAll();
			$userService = new UserService();
			$consumeService = new ConsumeService();
			$archiveService = new ArchivesService();
			$attents = array();
			foreach ($consumes as $consume){
				$doteyUid = $consume['uid'];
				$daren = $this->getDoteySuperDaren($doteyUid,$consumeRecordsCommand);
				if(empty($daren)){
					continue;
				}
				$attents[$doteyUid] = $daren; 
				$attents[$doteyUid]['charm'] = $consume['charm'];
			}
			$doteyUids = array_keys($attents);
			$uids = array_keys($userService->buildDataByIndex($attents,'sender_uid'));
			$uids =array_unique(array_merge($doteyUids,$uids));
			$users = $userService->getUserBasicByUids($uids);
			$userConsumes = $consumeService->getConsumesByUids($uids);
			$archives = $archiveService->getArchivesByUids($doteyUids,true,0);
			$archives = $archiveService->buildDataByIndex($archives,'uid');
			$returnArray = array();
			$i = 0;
			foreach($attents as $uid=>$attent){
				if($i >= 15){
					break;
				}
				$user = $users[$uid];
				$archive = isset($archives[$uid]) ? $archives[$uid] : array();
				if($user['user_status'] == 0 && ($archive && $archive['is_hide'] == 0)){
					$userConsume = $userConsumes[$uid];
					$sendUser = $users[$attent['sender_uid']];
					$sendUserConsume = $userConsumes[$attent['sender_uid']];
					$returnArray[$i]['d_uid'] = $uid;
					$returnArray[$i]['d_nickname'] = $user['nickname'] ? $user['nickname'] : $user['username'];
					$returnArray[$i]['d_rank'] = $userConsume['dotey_rank'];
					$returnArray[$i]['charm'] = $attent['charm'];
					$returnArray[$i]['nickname'] = $sendUser['nickname'] ? $sendUser['nickname'] : $sendUser['username'];
					$returnArray[$i]['rank'] = $sendUserConsume['rank'];
					$i++;
				}
			}
			$otherRedisModel = new OtherRedisModel();
			if($otherRedisModel->setDoteyCharmRank('dotey_charm_super_rank',$returnArray)){
				echo "主播粉丝超级魅力榜写入成功\n\r";
			}else{
				echo "主播粉丝超级魅力榜写入失败\n\r";
			}
			
			
		}
		
		
		public function actionSuperFriendly(){
			$consumeRecordsCommand = $this->consume_records_db->createCommand();
			
			$consumeRecordsCommand->setText("SELECT uid,sum(dedication) dedication FROM `web_user_dedication_records` where source = 'userGifts' GROUP BY uid ORDER BY dedication DESC LIMIT 50");
			$consumes = $consumeRecordsCommand->queryAll();
			$userService = new UserService();
			$consumeService = new ConsumeService();
			$consumes = $userService->buildDataByIndex($consumes,'uid');
			
			$uids =array_unique(array_keys($consumes));
			$users = $userService->getUserBasicByUids($uids);
			$userConsumes = $consumeService->getConsumesByUids($uids);
			$returnArray = array();
			$i = 0;
			foreach($consumes as $uid=>$consume){
				if($i >= 15){
					break;
				}
				$user = $users[$uid];
				$userConsume = $userConsumes[$uid];
				$returnArray[$i]['uid'] = $uid;
				if($user['user_status'] == 0){
					$returnArray[$i]['nickname'] = $user['nickname'] ? $user['nickname'] : $user['username'];
					$returnArray[$i]['rank'] = $userConsume['rank'];
					$returnArray[$i]['dedication'] = $consume['dedication'];
					$i++;
				}
			}
			
			$otherRedisModel = new OtherRedisModel();
			if($otherRedisModel->setUserFriendlyRank('user_friendly_super_rank',$returnArray)){
				echo "用户超级情谊榜写入成功scuccess\n\r";
			}else{
				echo "用户超级情谊榜写入失败fail\n\r";
			}
			
		}
		
		public function actionDoteySuperGift(){
			$consumeRecordsCommand = $this->consume_records_db->createCommand();
			$consumeRecordsCommand->setText('SELECT uid,sum(num) gift_total  from  web_dotey_charm_records  where source = "gifts"  GROUP BY uid ORDER BY gift_total desc limit 15');
			$doteyAttentions =$consumeRecordsCommand->queryAll();
			$attents = array();
			foreach($doteyAttentions as $dotey){
				$doteyUid = $dotey['uid'];
				$attents[$doteyUid] = $this->getDoteySuperDaren($doteyUid,$consumeRecordsCommand); 
				$attents[$doteyUid]['gift_total'] = $dotey['gift_total'];
			}
			$userService = new UserService();
			$consumeService = new ConsumeService();
			$uids = array_keys($userService->buildDataByIndex($attents,'sender_uid'));
			$uids =array_unique(array_merge(array_keys($attents),$uids));
			$users = $userService->getUserBasicByUids($uids);
			$userConsumes = $consumeService->getConsumesByUids($uids);
			$returnArray = array();
			$i = 0;
			foreach($attents as $uid=>$attent){
				
				$user = $users[$uid];
				$userConsume = $userConsumes[$uid];
				$sendUser = $users[$attent['sender_uid']];
				$sendUserConsume = $userConsumes[$attent['sender_uid']];
				$returnArray[$i]['d_uid'] = $uid;
				$returnArray[$i]['d_nickname'] = $user['nickname'] ? $user['nickname'] : $user['username'];
				$returnArray[$i]['d_rank'] = $userConsume['dotey_rank'];
				$returnArray[$i]['num'] = $attent['gift_total'];
				$returnArray[$i]['nickname'] = $sendUser['nickname'] ? $sendUser['nickname'] : $sendUser['username'];
				$returnArray[$i]['rank'] = $sendUserConsume['rank'];
				$i++;
			}
		
			$otherRedisModel = new OtherRedisModel();
			if($otherRedisModel->setDoteyReceiveGiftRank('dotey_gift_super_rank',$returnArray)){
				echo "主播收礼排行榜写入成功\n\r";
			}else{
				echo "主播收礼排行榜写入成功\n\r";
			}
			
		}
		
		public function actionUserSuperSong(){
			$consumeRecordsCommand = $this->consume_records_db->createCommand();
			$consumeCommand = $this->consume_db->createCommand();
			$commonCommand = $this->common_db->createCommand();
			
			$commonCommand->setText('SELECT DISTINCT uid FROM `web_dotey_channel` where channel_id = 1');
			$songDoteys = $commonCommand->queryAll();
			$userService = new UserService();
			$songDoteys = $userService->buildDataByIndex($songDoteys,'uid');
			
			$consumeCommand->setText('SELECT uid,count(*) songs from web_user_song WHERE  to_uid in ('.implode(',',array_keys($songDoteys)).') AND is_handle = 1  GROUP BY uid ORDER BY songs DESC LIMIT 60');
			$consumes = $consumeCommand->queryAll();
			$userService = new UserService();
			$consumeService = new ConsumeService();
			$consumes = $userService->buildDataByIndex($consumes,'uid');
			
			$uids =array_unique(array_keys($consumes));
			$users = $userService->getUserBasicByUids($uids);
			$userConsumes = $consumeService->getConsumesByUids($uids);
			$returnArray = array();
			$i = 0;
			foreach($consumes as $uid=>$consume){
				if($i >= 15){
					break;
				}
				$user = $users[$uid];
				$userConsume = $userConsumes[$uid];
				$returnArray[$i]['uid'] = $uid;
				if($user['user_status'] == 0){
					$returnArray[$i]['nickname'] = $user['nickname'] ? $user['nickname'] : $user['username'];
					$returnArray[$i]['rank'] = $userConsume['rank'];
					$returnArray[$i]['num'] = $consume['songs'];
					$i++;
				}
			}
			
			$otherRedisModel = new OtherRedisModel();
			if($otherRedisModel->setUserSongsRank('user_songs_super_rank',$returnArray)){
				echo "用户超级点唱榜写入成功\n\r";
			}else{
				echo "用户超级点唱榜写入失败\n\r";
			}
		}
		
		public function actionDoteySuperSong(){
			$consumeRecordsCommand = $this->consume_records_db->createCommand();
			$consumeCommand = $this->consume_db->createCommand();
			$commonCommand = $this->common_db->createCommand();
			
			$commonCommand->setText('SELECT DISTINCT uid FROM `web_dotey_channel` where channel_id = 1');
			$songDoteys = $commonCommand->queryAll();
			$userService = new UserService();
			$songDoteys = $userService->buildDataByIndex($songDoteys,'uid');
			
			$consumeCommand->setText('SELECT to_uid uid,count(*) songs from web_user_song where to_uid in ('.implode(',',array_keys($songDoteys)).') AND is_handle = 1 GROUP BY to_uid ORDER BY songs DESC LIMIT 60');
			$consumes = $consumeCommand->queryAll();
			$consumeService = new ConsumeService();
			$archiveService = new ArchivesService();
			$attents = array();
			foreach ($consumes as $consume){
				$doteyUid = $consume['uid'];
				$daren = $this->getDoteySongsDaren($doteyUid,$consumeCommand);
				if(empty($daren)){
					continue;
				}
				$attents[$doteyUid] = $daren; 
				$attents[$doteyUid]['songs'] = $consume['songs'];
			}
			
			$doteyUids = array_keys($attents);
			$uids = array_keys($userService->buildDataByIndex($attents,'song_uid'));
			$uids =array_unique(array_merge($doteyUids,$uids));
			$users = $userService->getUserBasicByUids($uids);
			$userConsumes = $consumeService->getConsumesByUids($uids);
			$archives = $archiveService->getArchivesByUids($doteyUids,true,0);
			$archives = $archiveService->buildDataByIndex($archives,'uid');
			$returnArray = array();
			$i = 0;
			foreach($attents as $uid=>$attent){
				if($i >= 15){
					break;
				}
				$user = $users[$uid];
				$userConsume = $userConsumes[$uid];
				$sendUser = $users[$attent['song_uid']];
				$sendUserConsume = $userConsumes[$attent['song_uid']];
				$archive = isset($archives[$uid]) ? $archives[$uid] : array();
				
				if($user['user_status'] == 0 && ($archive && $archive['is_hide'] == 0)){
					$returnArray[$i]['d_uid'] = $uid;
					$returnArray[$i]['d_nickname'] = $user['nickname'] ? $user['nickname'] : $user['username'];
					$returnArray[$i]['d_rank'] = $userConsume['dotey_rank'];
					$returnArray[$i]['num'] = $attent['songs'];
					$returnArray[$i]['nickname'] = $sendUser['nickname'] ? $sendUser['nickname'] : $sendUser['username'];
					$returnArray[$i]['rank'] = $sendUserConsume['rank'];
					$i++;
				}
			}
			
			$otherRedisModel = new OtherRedisModel();
			if($otherRedisModel->setDoteySongsRank('dotey_songs_super_rank',$returnArray)){
				echo "主播超级点唱榜写入成功\n\r";
			}else{
				echo "主播超级点唱榜写入失败\n\r";
			}
		}
		
		public function actionDoteySuperFans(){
			$userCommand = $this->user_db->createCommand();
			$userCommand->setText('SELECT uid,count(fans_uid) fans FROM `web_dotey_fans` GROUP BY uid ORDER BY fans DESC LIMIT 60');
			$weiboFans = $userCommand->queryAll();
			$userService = new UserService();
			$consumeService = new ConsumeService();
			$archiveService = new ArchivesService();
			$weiboFans = $userService->buildDataByIndex($weiboFans,'uid');
			
			$uids =array_unique(array_keys($weiboFans));
			$users = $userService->getUserBasicByUids($uids);
			$userConsumes = $consumeService->getConsumesByUids($uids);
			$archives = $archiveService->getArchivesByUids($uids,true,0);
			$archives = $archiveService->buildDataByIndex($archives,'uid');
			
			$returnArray = array();
			$i = 0;
			foreach($weiboFans as $uid=>$fans){
				if($i >= 15){
					break;
				}
				$user = $users[$uid];
				$userConsume = $userConsumes[$uid];
				$archive = isset($archives[$uid]) ? $archives[$uid] : array();
			
				if($user['user_status'] == 0 && ($archive && $archive['is_hide'] == 0)){
					$returnArray[$i]['d_uid'] = $uid;
					$returnArray[$i]['d_nickname'] = $user['nickname'] ? $user['nickname'] : $user['username'];
					$returnArray[$i]['d_rank'] = $userConsume['rank'];
					$returnArray[$i]['num'] = $fans['fans'];
					$i++;
				}
			}
			
			$otherRedisModel = new OtherRedisModel();
			if($otherRedisModel->setDoteyFansRank('dotey_fans_super_rank',$returnArray)){
				echo "主播超级粉丝榜写入成功success\n\r";
			}else{
				echo "主播超级粉丝榜写入失败fail\n\r";
			}
		}
		
		public function actionDoteyNewFans(){
			$userCommand = $this->user_db->createCommand();
			$registerTime = time() - 3600*24*90;
			$userCommand->setText('SELECT * FROM `web_dotey_base` `t` WHERE status = 1 AND create_time >= '.$registerTime);
			$newDoteys = $userCommand->queryAll();
			if(empty($newDoteys)){
				return array();
			}
			$userService = new UserService();
			$consumeService = new ConsumeService();
			$archiveService = new ArchivesService();
			$newUids = array_keys($userService->buildDataByIndex($newDoteys,'uid'));
			$newUids = implode($newUids,',');
			$userCommand->setText('SELECT uid,count(fans_uid) fans FROM `web_dotey_fans` WHERE uid IN ('.$newUids.')GROUP BY uid ORDER BY fans DESC LIMIT 60');
			$weiboFans = $userCommand->queryAll();
			if(empty($weiboFans)){
				return array();
			}
			$weiboFans = $userService->buildDataByIndex($weiboFans,'uid');
			$uids =array_unique(array_keys($weiboFans));
			$users = $userService->getUserBasicByUids($uids);
			$userConsumes = $consumeService->getConsumesByUids($uids);
			$archives = $archiveService->getArchivesByUids($uids,true,0);
			$archives = $archiveService->buildDataByIndex($archives,'uid');
			$returnArray = array();
			$i = 0;
			foreach($weiboFans as $uid=>$fans){
				if(!isset($users[$uid]) || !isset($userConsumes[$uid]))
					continue;
					
				if($i >= 15){
					break;
				}
				$user = $users[$uid];
				$userConsume = $userConsumes[$uid];
				$archive = isset($archives[$uid]) ? $archives[$uid] : array();
			
				if($user['user_status'] == 0 && ($archive && $archive['is_hide'] == 0)){
					$returnArray[$i]['d_uid'] = $uid;
					$returnArray[$i]['d_nickname'] = $user['nickname'] ? $user['nickname'] : $user['username'];
					$returnArray[$i]['d_rank'] = $userConsume['rank'];
					$returnArray[$i]['num'] = $fans['fans'];
					$i++;
				}
			}
			
			$otherRedisModel = new OtherRedisModel();
			if($otherRedisModel->setDoteyFansRank('dotey_fans_new_rank',$returnArray)){
				echo "主播新人粉丝榜写入成功success\n\r";
			}else{
				echo "主播新人粉丝榜写入失败fail\n\r";
			}
		}
		public function getDoteyCharmByTimes($uid,$startTime,$endTime,CDbCommand $command){
			$command->setText('SELECT uid,sum(charm) charm_total FROM `web_dotey_charm_records` where uid=:uid and create_time >= :startTime and create_time <=:endTime limit 1');
			$command->bindParam(':uid',$uid);
			$command->bindParam(':startTime',$startTime);
			$command->bindParam(':endTime',$endTime);
			return $command->queryRow();
		}
		
		public function getDoteySongsByTimes($uid,$startTime,$endTime,CDbCommand $command){
			$command->setText("SELECT to_uid uid,count(*) songs from web_user_song where to_uid = :uid and is_handle = 1 and create_time >= :startTime and create_time <=:endTime limit {$limit}");
			$command->bindParam(':uid',$uid);
			$command->bindParam(':startTime',$startTime);
			$command->bindParam(':endTime',$endTime);
			return $command->queryRow();
		}
	
		
		public function getDoteySuperDaren($uid,CDbCommand  $consumeRecordCommand){
			$consumeRecordCommand->setText('SELECT sender_uid,sum(charm) charm FROM `web_dotey_charm_records` where uid = :uid GROUP BY sender_uid ORDER BY charm DESC LIMIT 5');
			$consumeRecordCommand->bindValue(':uid',$uid);
			$daren = $consumeRecordCommand->queryAll();
			$service = new PipiService();
			$users = $this->getValidUser(array_keys($service->buildDataByIndex($daren,'sender_uid')));
			$users = $service->buildDataByIndex($users,'uid');
			foreach($daren as $_daren){
				if(isset($users[$_daren['sender_uid']])){
					return $_daren;
				}
			}
			return array_pop($daren);
		}
		
		public function getDoteySongsDaren($uid,CDbCommand  $consumeCommand){
			$consumeCommand->setText('SELECT uid song_uid,count(*) song_songs from web_user_song where to_uid = :uid  and is_handle = 1 GROUP BY uid ORDER BY song_songs DESC LIMIT 5');
			$consumeCommand->bindValue(':uid',$uid);
			$daren = $consumeCommand->queryAll();
			$service = new PipiService();
			$users = $this->getValidUser(array_keys($service->buildDataByIndex($daren,'song_uid')));
			$users = $service->buildDataByIndex($users,'uid');
			foreach($daren as $_daren){
				if(isset($users[$_daren['song_uid']])){
					return $_daren;
				}
			}
			return array_pop($daren);
		}
		
		public function getValidUser(array $uids){
			if(empty($uids)){
				return array();
			}
			$userCommand = $this->user_db->createCommand();
			$userCommand->setText('select uid,user_status,username,nickname from web_user_base where uid in ('.implode(',',$uids).') and user_status != 1');
			return $userCommand->queryAll();
		}
		
		
		
}