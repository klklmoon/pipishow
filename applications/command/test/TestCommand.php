<?php

class TestCommand extends PipiConsoleCommand {

		/**
		 * @var CDbConnection
		 */
		public $consumeSlaveDb;
		
		/**
		 * @var CDbConnection
		 */
		public $consumeRecordsSlaveDb;

		/**
		 * @var CDbConnection
		 */
		public $userSlaveDb;

		/**
		 * @var CDbConnection
		 */
		public $userRecordsSlaveDb;
		
		/**
		 * @var CDbConnection
		 */
		public $archivesSlaveDb;
		
		/**
		 * @var CDbConnection
		 */
		public $commonDb;
	
		public function actionIndex(){
			list($ystartTime,$yendTime) = $this->pushDownDaysTime(1,false);
			echo "SELECT uid,sum(charm) charms FROM `web_dotey_charm_records` WHERE create_time BETWEEN $ystartTime AND  $yendTime GROUP BY uid ORDER BY charms  DESC LIMIT 15";
		}
		public function actionSongDoteySongs(){
			$this->getReadDbConnect();
			$commonCommand = $this->commonDb->createCommand();
			$userCommoand = $this->userSlaveDb->createCommand();
			$commonCommand->setText('SELECT DISTINCT uid FROM `web_dotey_channel` where channel_id = 1');
			$users = $commonCommand->queryAll();
			$userService = new UserService();
			$users = $userService->buildDataByIndex($users,'uid');
			$uids = array_keys($users);
			list($startTime,$endTime) = $this->pushDownWeekTime(1,false);
			
			$consumeRecordsCommand = $this->consumeRecordsSlaveDb->createCommand();
			$consumeCommand = $this->consumeSlaveDb->createCommand();
			$consumeCommand->setText('SELECT to_uid uid,count(*) songs from web_user_song where  is_handle = 1 AND to_uid IN ('.implode(',',$uids).') and update_time >= '.$startTime.' and update_time <= '.$endTime.' GROUP BY to_uid ORDER BY songs DESC LIMIT 15');
			$consumes = $consumeCommand->queryAll();
			$consumeService = new ConsumeService();
			$attents = array();
			foreach ($consumes as $consume){
				$doteyUid = $consume['uid'];
				//$attents[$doteyUid] = $this->getDoteySongsDaren($doteyUid,$consumeCommand); 
				$attents[$doteyUid]['songs'] = $consume['songs'];
			}
			
			//$uids = array_keys($userService->buildDataByIndex($attents,'song_uid'));
			//$uids =array_unique(array_merge(array_keys($attents),$uids));
			$uids = array_keys($attents);
			$userCommoand->setText('select * from web_user_base where uid in ('.implode(',',$uids).')');
			$users = $userCommoand->queryAll($uids);
			$users = $userService->buildDataByIndex($users,'uid');
			//$userConsumes = $consumeService->getConsumesByUids($uids);
			$returnArray = array();
			$i = 0;
			foreach($attents as $uid=>$attent){
				
				$user = $users[$uid];
				//$userConsume = $userConsumes[$uid];
				//$sendUser = $users[$attent['song_uid']];
				//$sendUserConsume = $userConsumes[$attent['song_uid']];
				$returnArray[$i]['d_uid'] = $uid;
				$returnArray[$i]['d_nickname'] = $user['nickname'] ? $user['nickname'] : $user['username'];
				//$returnArray[$i]['d_rank'] = $userConsume['dotey_rank'];
				$returnArray[$i]['num'] = $attent['songs'];
				//$returnArray[$i]['nickname'] = $sendUser['nickname'] ? $sendUser['nickname'] : $sendUser['username'];
				//$returnArray[$i]['rank'] = $sendUserConsume['rank'];
				$i++;
			}
			
			print_r($returnArray);
			/*
			$otherRedisModel = new OtherRedisModel();
			if($otherRedisModel->setDoteySongsRank('dotey_songs_super_rank',$returnArray)){
				echo "唱区主播超级点唱榜写入成功\n\r";
			}else{
				echo "唱区主播超级点唱榜写入失败\n\r";
			}*/
			
		}
		
		
		
		
	public function actionUserSongs(){
			$this->getReadDbConnect();
			$consumeRecordsCommand = $this->consumeRecordsSlaveDb->createCommand();
			$consumeCommand = $this->consumeSlaveDb->createCommand();
			$userCommoand = $this->userSlaveDb->createCommand();
			list($startTime,$endTime) = $this->pushDownWeekTime(1,false);
			$consumeCommand->setText('SELECT uid,count(*) songs from web_user_song where is_handle = 1  and update_time >= '.$startTime.' and update_time <= '.$endTime.' GROUP BY uid ORDER BY songs DESC LIMIT 15');
			$consumes = $consumeCommand->queryAll();
			$userService = new UserService();
			$consumeService = new ConsumeService();
			$consumes = $userService->buildDataByIndex($consumes,'uid');
			
			$uids =array_unique(array_keys($consumes));
			$userCommoand->setText('select * from web_user_base where uid in ('.implode(',',$uids).')');
			$users = $userCommoand->queryAll($uids);
			$users = $userService->buildDataByIndex($users,'uid');
			//$userConsumes = $consumeService->getConsumesByUids($uids);
			$returnArray = array();
			$i = 0;
			foreach($consumes as $uid=>$consume){
				$user = $users[$uid];
				//$userConsume = $userConsumes[$uid];
				$returnArray[$i]['uid'] = $uid;
				$returnArray[$i]['nickname'] = $user['nickname'] ? $user['nickname'] : $user['username'];
				//$returnArray[$i]['rank'] = $userConsume['rank'];
				$returnArray[$i]['num'] = $consume['songs'];
				$i++;
			}
			
			print_r($returnArray);
			
			
		}
		
		private function getReadDbConnect(){
			$this->consumeSlaveDb=Yii::app()->db_consume_slave;
			$this->consumeRecordsSlaveDb=Yii::app()->db_consume_records_slave;
			$this->userSlaveDb=Yii::app()->db_user_slave;
			$this->userRecordsSlaveDb=Yii::app()->db_user_records_slave;
			$this->commonDb = Yii::app()->db_common;
		}
}

?>