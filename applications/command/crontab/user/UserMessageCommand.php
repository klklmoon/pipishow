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
class UserMessageCommand extends PipiConsoleCommand {
	
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
		
		/**
		 * @var CDbConnection 消息提醒数据库操作对象
		 */
		protected $message_db;
		
		public function beforeAction($action,$params){
			parent::beforeAction($action, $params);
			$this->consume_db = Yii::app()->db_consume;
			$this->user_db = Yii::app()->db_user;
			//$this->consume_records_db =  Yii::app()->db_consume_records;
			//$this->user_db = Yii::app()->db_user;;
			//$this->common_db = Yii::app()->db_common;
			//$this->archives_db = Yii::app()->db_archives;
			//$this->weibo_db = Yii::app()->db_weibo;
			return true;
		}
		
		public function actionIndex(){
		
		}
		
		public function actionPushMessage(){
			$userDbCommand = $this->user_db->createCommand();
			$consumeDbCommand = $this->consume_db->createCommand();
			$messageService = new MessageService();
			$zmq = $messageService->getZmq();
			$timestamp = time();
			$count = $userDbCommand->setText('SELECT count(*) FROM web_message_push WHERE is_send = 0 AND send_time <= '.$timestamp)->queryScalar();
			$pageSize = 1000;
			$page = ceil($count / $pageSize);
			$pushMessageModel = new MessagePushModel();
			for($i=0;$i<=$page;$i++){
				$offset = $i*$pageSize;
				echo "第{$i}组数据\n\r";
				$userDbCommand->setText('');
				$pushMessages = $userDbCommand->from('web_message_push')->where('is_send = 0 AND send_time <= '.$timestamp)->limit($pageSize,$offset)->queryAll();
				foreach ($pushMessages as $pushMessage){
					if(in_array($pushMessage['type'],array(1) )){
						//指定用户范围等级时，发系统消息时，用户等级不能超过富豪1,否则信息量太庞大
						if(!$pushMessage['target_id'] && $pushMessage['rank'] < 7){
							continue;
						}
					}
					$message = array();
					$message['title'] = $pushMessage['title'];
					$message['sub_title'] = $pushMessage['tips'];
					$message['content'] = $pushMessage['content'];
					$message['uid'] = 0;
					$message['extra'] = $pushMessage['extra'];
					$message['target_id'] = $pushMessage['push_id'];
					$message['is_read'] = 0;
					$message['is_site'] = 0;
					$message['category'] = MESSAGE_CATEGORY_SYSTEM;
					$message['sub_category'] = MESSAGE_CATEGORY_SYSTEM_PUSH;
					if($pushMessage['type'] == 0){
						$message['is_site'] = 1;
						$message['sub_category'] = MESSAGE_CATEGORY_SYSTEM_SITE;
						$messageService->sendMessage($message);
					}
					if($pushMessage['type'] == 1){
						if(!$pushMessage['target_id']){
							if($pushMessage['rank'] < 7){
								continue;
							}
							$users = $consumeDbCommand->setText('SELECT uid FROM web_user_consume_attribute WHERE rank >= '.(int)$pushMessage['rank'])->queryAll();
							if(empty($users)){
								continue;
							}
							$users = array_keys($messageService->buildDataByIndex($users,'uid'));
							$message['to_uid'] = implode(',',$users); 
						}else{
							$message['to_uid'] = $pushMessage['target_id'];
						}
						$messageService->sendMessage($message);
					}
					if($pushMessage['type'] == 3){
						if(!$pushMessage['target_id']){
							if($pushMessage['rank'] < 1){
								continue;
							}
							$users = $consumeDbCommand->setText('SELECT uid FROM web_user_consume_attribute WHERE dotey_rank >= '.(int)$pushMessage['rank'])->queryAll();
							if(empty($users)){
								continue;
							}
							$users = array_keys($messageService->buildDataByIndex($users,'uid'));
							$message['to_uid'] = implode(',',$users); 
						}else{
							$message['to_uid'] = $pushMessage['target_id'];
						}
						$messageService->sendMessage($message);
					}
					if($pushMessage['type'] == 2){
						$json_content['type']='message';
						$json_content['title']=$pushMessage['title'];
						$json_content['content']=$pushMessage['content'];
						$json_content['window'] = $pushMessage['window'];
						if(!$pushMessage['target_id']){
							$eventData['archives_id']='*';
							$eventData['domain']=DOMAIN;
							$eventData['type']='broadcast';
						}else{
							$eventData['archives_id']=$pushMessage['target_id'];
							$eventData['domain']=DOMAIN;
							$eventData['type']='localroom';
						}
						$eventData['json_content']=$json_content;
						$zmq->sendBrodcastMsg($eventData);
					}
					$pushMessageModel->updateByPk($pushMessage['push_id'],array('is_send'=>1));
					
				}
				
			}
		}
		
	
		
		
		
}