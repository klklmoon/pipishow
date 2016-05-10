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
class UserTopCommand extends PipiConsoleCommand {
	
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
			//$this->consume_records_db =  Yii::app()->db_consume_records;
			//$this->user_db = Yii::app()->db_user;;
			//$this->common_db = Yii::app()->db_common;
			//$this->archives_db = Yii::app()->db_archives;
			//$this->weibo_db = Yii::app()->db_weibo;
			return true;
		}
		
		public function actionIndex(){
		
		}
		
		public function actionAllSuperDedication(){
			
			$consumeCommand = $this->consume_db->createCommand();
			$count = $consumeCommand->setText('SELECT count(*) FROM web_user_consume_attribute WHERE dedication > 0')->queryScalar();
			$consumeCommand->setText('');
			$pageSize = 10000;
			$page = ceil($count / $pageSize);
			$userJsonService = new UserJsonInfoService();
			$a=0;
			for($i=0;$i<=$page;$i++){
				$offset = $i*$pageSize;
				echo "第{$i}组数据\n\r";
				$consumeCommand->setText('');
				$users = $consumeCommand->from('web_user_consume_attribute')->where('dedication > 0')->select('uid,dedication')->order('dedication DESC')->limit($pageSize,$offset)->queryAll();
				$j=0;
				foreach($users as $user){
					$rank = $offset+(++$j);
					if($userJsonService->getUserInfo($user['uid'],false)){
						$a++;
						$userJsonService->setUserInfo($user['uid'],array('u_rk'=>$rank));
					}
				}
			}
			echo "总共写了{$a}条Redis数据";
		}
		
	
		
		
		
}