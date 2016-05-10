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
class DoteyIndexRightCommand extends PipiConsoleCommand {
	
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
			$this->user_db = Yii::app()->db_user;
			$this->common_db = Yii::app()->db_common;
			$this->archives_db = Yii::app()->db_archives;
			return true;
		}
		/**
		 * 推荐新秀主播，每小时跑一次
		 */
		public function actionRookieDotey(){
			$archivesDbCommand = $this->archives_db->createCommand();
			$timeStamp = time() - 90*3600*24;
			$archivesDbCommand->setText('SELECT DISTINCT archives_id  FROM( SELECT archives_id,MIN(live_time) live_time FROM web_live_records  WHERE live_time > 0 GROUP BY archives_id) a  WHERE   live_time >= '.$timeStamp);
			$archives = $archivesDbCommand->queryAll();
			if(empty($archives)){
				return array();
			}
			$archivesService = new ArchivesService();
			$archives = $archivesService->buildDataByIndex($archives,'archives_id');
			$archivesDbCommand->setText('SELECT uid,archives_id FROM web_archives WHERE sub_id = 0 and is_hide = 0 AND archives_id IN ('.implode(',',array_keys($archives)).')');
			$archivesInfo = $archivesDbCommand->queryAll();
			
			if(empty($archivesInfo)){
				return array();
			}
			$archivesInfo = $archivesService->buildDataByIndex($archivesInfo,'uid');
			
			$consumeCommand = $this->consume_db->createCommand();
			$consumeCommand->setText('SELECT uid ,dotey_rank FROM web_user_consume_attribute WHERE dotey_rank >= 5 AND uid IN ('.implode(',',array_keys($archivesInfo)).')');
			$attriubtes = $consumeCommand->queryAll();
			
			if(empty($attriubtes)){
				return array();
			}
			$attriubtes = $archivesService->buildDataByIndex($attriubtes,'uid');
			$consumeRecordsCommand = $this->consume_records_db->createCommand();
			$startTime = time() - 3*24*3600;
			$consumeRecordsCommand->setText('SELECT uid,sum(charm) charm_total FROM `web_dotey_charm_records` where uid IN ('.implode(',',array_keys($attriubtes)).') and create_time >= '.$startTime.' GROUP BY uid ORDER BY charm_total DESC LIMIT 100');
			$doteyCharms = $consumeRecordsCommand->queryAll();
			if(empty($doteyCharms)){
				return array();
			}
			$userService = new UserService();
			$users = $userService->getUserBasicByUids(array_keys($userService->buildDataByIndex($doteyCharms,'uid')));
			$commonCommand = $this->common_db->createCommand();
			$commonCommand->setText('delete from web_index_rightdata where type = 0');
			$commonCommand->execute();
			$commonCommand->setText('INSERT INTO web_index_rightdata (uid,type,charms,username,nickname) VALUES (:uid,:type,:charms,:username,:nickname)');
			$i = 0;
			foreach($doteyCharms as $doteyCharm){
				if($i>=20){
					break;
				}
				$user = array();
				if(isset($users[$doteyCharm['uid']])){
					$user = $users[$doteyCharm['uid']];
				}
				$commonCommand->bindValue(':uid',$doteyCharm['uid']);
				$commonCommand->bindValue(':type',0);
				$commonCommand->bindValue(':charms',$doteyCharm['charm_total']);
				$commonCommand->bindValue(':username',isset($user['username']) ? $user['username'] : '');
				$commonCommand->bindValue(':nickname',isset($user['nickname']) ? $user['nickname'] : '');
				$commonCommand->execute();
				$i++;
			}
			
		}
		
		
		/**
		 * 最新加入的主播  每天跑一次
		 */
		public function actionNewJoinDotey(){
			
			$archivesDbCommand = $this->archives_db->createCommand();
			$timeStamp = time() - 30*3600*24;
			$archivesDbCommand->setText('SELECT DISTINCT archives_id  FROM( SELECT archives_id,MIN(live_time) live_time FROM web_live_records  WHERE live_time > 0 GROUP BY archives_id) a  WHERE   live_time >= '.$timeStamp);
			$archives = $archivesDbCommand->queryAll();
			if(empty($archives)){
				return array();
			}
			$archivesService = new ArchivesService();
			$archives = $archivesService->buildDataByIndex($archives,'archives_id');
			$archivesDbCommand->setText('SELECT uid,archives_id FROM web_archives WHERE sub_id = 0 and is_hide = 0 AND archives_id IN ('.implode(',',array_keys($archives)).')');
			$archivesInfo = $archivesDbCommand->queryAll();
			
			if(empty($archivesInfo)){
				return array();
			}
			$archivesInfo = $archivesService->buildDataByIndex($archivesInfo,'uid');
			$userService = new UserService();
			$users = $userService->getUserBasicByUids(array_keys($archivesInfo));
			
			$commonCommand = $this->common_db->createCommand();
			$commonCommand2 = $this->common_db->createCommand();
			$commonCommand->setText('delete from web_index_rightdata where type = 1');
			$commonCommand->execute();
			$commonCommand->setText('INSERT INTO web_index_rightdata (uid,type,charms,username,nickname) VALUES (:uid,:type,:charms,:username,:nickname)');
			foreach($archivesInfo as $archive){
				//判断是否在新秀主播范围内
				$commonCommand2->setText('select uid from web_index_rightdata where type =0 AND uid='.$archive['uid']);
				if($commonCommand2->queryScalar()){
					continue;
				}
			
				$user = array();
				if(isset($users[$archive['uid']])){
					$user = $users[$archive['uid']];
				}
				$commonCommand->bindValue(':uid',$archive['uid']);
				$commonCommand->bindValue(':type',1);
				$commonCommand->bindValue(':charms',0);
				$commonCommand->bindValue(':username',isset($user['username']) ? $user['username'] : '');
				$commonCommand->bindValue(':nickname',isset($user['nickname']) ? $user['nickname'] : '');
				$commonCommand->execute();
			}
		}
	
		/**
		 * 明星主播 每月1日凌晨6:00
		 */
		public function actionStarDotey(){
			$consumeRecordsCommand = $this->consume_records_db->createCommand();
			$archivesDbCommand = $this->archives_db->createCommand();
			list($startTime,$endTime) = $this->pushDownMonthTime(1,false);
			$consumeRecordsCommand->setText('SELECT uid,sum(charm) charm_total FROM `web_dotey_charm_records` WHERE  create_time >= '.$startTime.' AND create_time <= '.$endTime.' GROUP BY uid ORDER BY charm_total DESC LIMIT 20');
			$doteyCharms = $consumeRecordsCommand->queryAll();
			if(empty($doteyCharms)){
				return array();
			}
			$archivesService = new ArchivesService();
			$doteyCharms = $archivesService->buildDataByIndex($doteyCharms,'uid');
			
			$archivesDbCommand->setText('SELECT uid,archives_id FROM web_archives WHERE sub_id = 0 and is_hide = 0 AND uid IN ('.implode(',',array_keys($doteyCharms)).')');
			$archivesInfo = $archivesDbCommand->queryAll();
			if(empty($archivesInfo)){
				return array();
			}
			
			$userService = new UserService();
			$users = $userService->getUserBasicByUids(array_keys($userService->buildDataByIndex($archivesInfo,'uid')));
			
			$commonCommand = $this->common_db->createCommand();
			$commonCommand->setText('delete from web_index_rightdata where type = 2');
			$commonCommand->execute();
			$commonCommand->setText('INSERT INTO web_index_rightdata (uid,type,charms,username,nickname) VALUES (:uid,:type,:charms,:username,:nickname)');
			
			$i = 0;
			foreach($archivesInfo as $archive){
				if($i >= 15){
					break;
				}
				$doteyCharm = isset($doteyCharms[$archive['uid']]) ? $doteyCharms[$archive['uid']] : array();
				$user = array();
				if(isset($users[$archive['uid']])){
					$user = $users[$archive['uid']];
				}
				$commonCommand->bindValue(':uid',$archive['uid']);
				$commonCommand->bindValue(':type',2);
				$commonCommand->bindValue(':charms',$doteyCharm ? $doteyCharm['charm_total'] : 0);
				$commonCommand->bindValue(':username',isset($user['username']) ? $user['username'] : '');
				$commonCommand->bindValue(':nickname',isset($user['nickname']) ? $user['nickname'] : '');
				$commonCommand->execute();
				
				$i++;
			}
			
			
		}
		
		
}