<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class UserTransferCommand extends CConsoleCommand {

		/**
		 * @var CDbConnection 老版ucenter读库操作
		 */
		protected $uncenter_db;
		
		/**
		 * @var CDbConnection 老版乐天读库操作
		 */
		protected $show_db;
		
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
		 * 
		 * @var CDbConnection 通用数据库操作
		 */
		protected $common_db;
		
		/**
		 * 
		 * @var CDbConnection 通用数据库操作
		 */
		protected $archives_db;
		
		protected $pageSize=1000;
		
		public function beforeAction($action,$params){
			$this->uncenter_db = Yii::app()->db_read_ucenter;
			$this->show_db = Yii::app()->db_read_pipishow;
			$this->consume_db = Yii::app()->db_consume;
			$this->consume_records_db =  Yii::app()->db_consume_records;
			$this->user_db = Yii::app()->db_user;
			$this->common_db = Yii::app()->db_common;
			$this->archives_db = Yii::app()->db_archives;
			return true;
		}
		
	
		public function actionCdnUser(){
			/* @var $userDbRecodsCommand CDbCommand */
			$userDbRecodsCommand = $this->user_db->createCommand();
			$userDbRecodsCommand->setText('SELECT count(*) FROM `web_user_base`');
			$count = $userDbRecodsCommand->queryScalar();
			$upload = new PipiFlashUpload();
			$upload->realFolder = 'avatars';
			$upload->filePrefix = 'avatar_';
			$pageSize = 1000;
			$pages = ceil($count / $pageSize);
			echo "开始修复CDN头像\n\r";
			$srcFolder = dirname(ROOT_PATH).'/letianimg/avatars/';
			for($i=1;$i<=$pages;$i++){
				echo "开始转移第{$i}组CDN数据,每组{$pageSize}人\n\r";
				$userDbRecodsCommand->setText('');
				$offset = ($i-1)*$pageSize;
				$users = $userDbRecodsCommand->from('web_user_base')->limit($pageSize,$offset)->queryAll();
				foreach($users as $user){
					$uid = $user['uid'];
					$avatar = $upload->getSaveFile($uid,'small');
					$userBasic = array();
					if(is_file($avatar)){
						$userBasic['update_desc']['atr'] = time() - Yii::app()->params['images_server']['cdn_time'] - 10;
						
						$user['update_desc'] = json_decode($user['update_desc'] ,true);
						if(is_array($user['update_desc'])){
							$userBasic['update_desc'] = array_merge($user['update_desc'],$userBasic['update_desc']);
						}
						$userBasic['update_desc'] = json_encode($userBasic['update_desc']);
						
						
						$userDbRecodsCommand->setText('');
						$userDbRecodsCommand->update('web_user_base',$userBasic,'uid = '.$uid);
						echo 1;
					}
					
				}
				echo "转移第{$i}组数据，结束\n\r";
				
				
			}
		}
		
		
		public function actionCdnDotey(){
			/* @var $userDbRecodsCommand CDbCommand */
			$userDbRecodsCommand = $this->user_db->createCommand();
			$userDbRecodsCommand->setText('SELECT * FROM `web_dotey_base`');
			$doteys = $userDbRecodsCommand->queryAll();
			$upload = new PipiFlashUpload();
			$upload->realFolder = 'dotey';
			$upload->filePrefix = 'dotey_';
			
			echo "开始修复CDN头像\n\r";
			$srcFolder = dirname(ROOT_PATH).'/letianimg/avatars/';
			
			foreach($doteys as $user){
				$uid = $user['uid'];
				$display_small = $upload->getSaveFile($uid,'small','display');
				$display_big = $upload->getSaveFile($uid,'big','display');
				
				$userBasic = array();
				if(is_file($display_small)){
					$userBasic['update_desc']['display_small'] = time() - Yii::app()->params['images_server']['cdn_time'] - 10;
					echo 1;
				}
				
				if(is_file($display_big)){
					$userBasic['update_desc']['display_big'] = time() - Yii::app()->params['images_server']['cdn_time'] - 10;
					echo 2;
				}
				
				if(isset($userBasic['update_desc']) && is_array($userBasic['update_desc'])){
					$user['update_desc'] = json_decode($user['update_desc'] ,true);
					if(is_array($user['update_desc'])){
						$userBasic['update_desc'] = array_merge($user['update_desc'],$userBasic['update_desc']);
					}
					$userBasic['update_desc'] = json_encode($userBasic['update_desc']);
					$userDbRecodsCommand->setText('');
					$userDbRecodsCommand->update('web_dotey_base',$userBasic,'uid = '.$uid);
				}
			}
	
		}
		
		public function actionTest(){
			echo Yii::app()->params['images_server']['cdn_time'];
		}
		
		
		public function actionUpdateUserAttribute(){
			$consumeCommand = $this->consume_db->createCommand();
			$consumeRecordCommand = $this->consume_records_db->createCommand();
			$consumeCommand->setText("SELECT count(*) as count from `web_user_consume_attribute` where dedication>0");
			$count=$consumeCommand->queryAll();
			$page=ceil($count[0]['count']/$this->pageSize);
			$j=1;
			echo "开始增加用户贡献值\n\r";
			for($i=1;$i<=$page;$i++){
				$consumeCommand->setText('SELECT * FROM `web_user_consume_attribute` where dedication>0 order by uid asc limit '.(($i-1)*$this->pageSize).','.$this->pageSize);
				$list=$consumeCommand->queryAll();
				foreach($list as $key=>$row){
					$dedication=$row['dedication']*2;
					$consumeCommand->setText('SELECT * FROM `web_user_rank` where dedication<='.$dedication.' order by dedication desc limit 1');
					$newRank=$consumeCommand->queryAll();
					echo "共".$count[0]['count']."个用户，正在增加第".$j."个用户的贡献值，用户uid:".$row['uid'].",贡献值:".$row['dedication']."，原始等级：".$row['rank']."，新等级：".$newRank[0]['rank']."\n\r";
					$consumeCommand->setText('UPDATE `web_user_consume_attribute` set dedication=dedication*2,rank='.$newRank[0]['rank'].' where uid='.$row['uid']);
					$consumeCommand->execute();
					$consumeRecordCommand->setText('insert into `web_user_dedication_records` (`uid`,`dedication`,`source`,`sub_source`,`client`,`info`,`create_time`) VALUES ('.$row['uid'].','.$row['dedication'].',"sends","admin",2,"后台赠送",'.time().')');
					$consumeRecordCommand->execute();
					$j++;
				}
			}
			
			echo "结束用户贡献值\n\r";
		}
		
		
		public function actionUpdateDoteyAttribute(){
			$consumeCommand = $this->consume_db->createCommand();
			$consumeRecordCommand = $this->consume_records_db->createCommand();
			$consumeCommand->setText("SELECT count(*) as count from `web_user_consume_attribute` where charm>0");
			$count=$consumeCommand->queryAll();
			$page=ceil($count[0]['count']/$this->pageSize);
			$j=1;
			echo "开始更新主播等级\n\r";
			for($i=1;$i<=$page;$i++){
				$consumeCommand->setText('SELECT * FROM `web_user_consume_attribute` where charm>0 order by uid asc limit '.(($i-1)*$this->pageSize).','.$this->pageSize);
				$list=$consumeCommand->queryAll();
				foreach($list as $key=>$row){
					$consumeCommand->setText('SELECT * FROM `web_dotey_rank` where charm<='.$row['charm'].' order by charm desc limit 1');
					$newRank=$consumeCommand->queryAll();
					echo "共".$count[0]['count']."个主播，正在修改第".$j."个主播的等级，用户uid:".$row['uid'].",魅力值:".$row['charm'].",原始等级：".$row['dotey_rank']."，新等级：".$newRank[0]['rank']."\n\r";
					$consumeCommand->setText('UPDATE `web_user_consume_attribute` set dotey_rank='.$newRank[0]['rank'].' where uid='.$row['uid']);
					$consumeCommand->execute();
					$j++;
				}
			}
				
			echo "结束更新主播等级\n\r";
		}
		
		
}