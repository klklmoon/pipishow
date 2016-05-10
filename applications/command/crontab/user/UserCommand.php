<?php
/**
 * 用户相关脚本
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package controllers
 * @subpackage days
 */
class UserCommand extends PipiConsoleCommand {
	
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
		protected $family_db;
		
		public function beforeAction($action,$params){
			parent::beforeAction($action, $params);
			return true;
		}
		
		public function actionIndex(){
		
		}
		
		public function actionRecyleNumber(){
			$this->consume_db = Yii::app()->db_consume;
			$this->user_db = Yii::app()->db_user;
			
			$consumeCommand = $this->consume_db->createCommand();
			$userCommond = $this->user_db->createCommand();
			$archivesService = new ArchivesService();
			$count = $consumeCommand->setText('SELECT count(*) FROM web_user_number WHERE status = 0')->queryScalar();
			$consumeCommand->setText('');
			$pageSize = 10000;
			$page = ceil($count / $pageSize);
			$userJsonService = new UserJsonInfoService();
			$userReChargeModel = new UserRechargeRecordsModel();
			$userNumberService = new UserNumberService();
			$userPropsService = new UserPropsService();
			$a=0;
			for($i=0;$i<=$page;$i++){
				$offset = $i*$pageSize;
				echo "第{$i}组数据\n\r";
				$consumeCommand->setText('');
				$users = $consumeCommand->from('web_user_number')->where('status = 0')->select('uid,number,record_id,create_time,last_recharge_time')->limit($pageSize,$offset)->queryAll();
				$j=0;
				foreach($users as $user){
					$dotey = $userCommond->from('web_dotey_base')->where('uid = '.$user['uid'])->queryRow();
					if($dotey){
						$archives = $archivesService->getArchivesByUids(array($user['uid']));
						if($archives){
							$archives = array_pop($archives);
						}
						if(isset($archives['live_record']['start_time']) && ((time() - $archives['live_record']['start_time']) / (3600*24)) > 60){
							$userProps = $userPropsService->getUserPropsAttributeByUid($user['uid']);
							$userNumberService->saveUserNumber(array('uid'=>$user['uid'],'number'=>$user['number'],'status'=>1));
							$recover['uid'] = $user['uid'];
							$recover['opertor_uid'] = 0;
							$recover['record_id'] = $user['record_id'];
							$recover['number'] = $user['number'];
							$recover['recover_type'] = 0;
							$recover['reason'] = '超过60天没有直播，系统自动回收';
							$recover['last_live_time'] = isset($archives['live_record']) ? $archives['live_record']['start_time'] : 0;
							$userNumberService->saveUserNumberRecover($recover);
							if($userProps && $user['number'] == $userProps['number']){
								$userJson['num'] = array();
								$useingNumber['uid'] = $user['uid'];
								$useingNumber['number'] = 0;
								$useingNumber['number_short_desc'] = '';
								$userPropsService->saveUserPropsAttribute($useingNumber);
								$userJsonService->setUserInfo($user['uid'],$userJson);
								$zmq = $userJsonService->getZmq();
								$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$user['uid'],'json_info'=>$userJson));
							}
						}else{
							$chargeTime = time();
							if($user['last_recharge_time'] && $chargeTime > $user['last_recharge_time']){
								$userProps = $userPropsService->getUserPropsAttributeByUid($user['uid']);
								$userNumberService->saveUserNumber(array('uid'=>$user['uid'],'number'=>$user['number'],'status'=>1));
								$recover['uid'] = $user['uid'];
								$recover['opertor_uid'] = 0;
								$recover['record_id'] = $user['record_id'];
								$recover['number'] = $user['number'];
								$recover['recover_type'] = 0;
								$recover['reason'] = '靓号已过期，系统自动回收';
								$recover['last_recharge_time'] = $chargeTime;
								$userNumberService->saveUserNumberRecover($recover);
								
								if($userProps && $user['number'] == $userProps['number']){
									$userJson['num'] = array();
									$useingNumber['uid'] = $user['uid'];
									$useingNumber['number'] = 0;
									$useingNumber['number_short_desc'] = '';
									$userPropsService->saveUserPropsAttribute($useingNumber);
									$userJsonService->setUserInfo($user['uid'],$userJson);
									$zmq = $userJsonService->getZmq();
									$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$user['uid'],'json_info'=>$userJson));
								}
								
							}
						}
						
					}else{
						$charge = $userReChargeModel->getLastCharge($user['uid']);
						if($charge){
							$chargeTime = $charge['ctime'];
							if($user['last_recharge_time'] && $chargeTime >= $user['last_recharge_time']){
								$days = ceil (($chargeTime- $user['last_recharge_time']) / (3600*24));
								if($days > 60){
									$userProps = $userPropsService->getUserPropsAttributeByUid($user['uid']);
									$userNumberService->saveUserNumber(array('uid'=>$user['uid'],'number'=>$user['number'],'status'=>1));
									$recover['uid'] = $user['uid'];
									$recover['opertor_uid'] = 0;
									$recover['record_id'] = $user['record_id'];
									$recover['number'] = $user['number'];
									$recover['recover_type'] = 0;
									$recover['reason'] = '超过60天没有充值，系统自动回收';
									$recover['last_recharge_time'] = $chargeTime;
									$userNumberService->saveUserNumberRecover($recover);
									
									if($userProps && $user['number'] == $userProps['number']){
										$userJson['num'] = array();
										$useingNumber['uid'] = $user['uid'];
										$useingNumber['number'] = 0;
										$useingNumber['number_short_desc'] = '';
										$userPropsService->saveUserPropsAttribute($useingNumber);
										$userJsonService->setUserInfo($user['uid'],$userJson);
										$zmq = $userJsonService->getZmq();
										$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$user['uid'],'json_info'=>$userJson));
									}
								}
							}
						}else{
							$chargeTime = time();
							if($user['last_recharge_time'] && $chargeTime > $user['last_recharge_time']){
								$userProps = $userPropsService->getUserPropsAttributeByUid($user['uid']);
								$userNumberService->saveUserNumber(array('uid'=>$user['uid'],'number'=>$user['number'],'status'=>1));
								$recover['uid'] = $user['uid'];
								$recover['opertor_uid'] = 0;
								$recover['record_id'] = $user['record_id'];
								$recover['number'] = $user['number'];
								$recover['recover_type'] = 0;
								$recover['reason'] = '靓号已过期，系统自动回收';
								$recover['last_recharge_time'] = $chargeTime;
								$userNumberService->saveUserNumberRecover($recover);
								
								if($userProps && $user['number'] == $userProps['number']){
									$userJson['num'] = array();
									$useingNumber['uid'] = $user['uid'];
									$useingNumber['number'] = 0;
									$useingNumber['number_short_desc'] = '';
									$userPropsService->saveUserPropsAttribute($useingNumber);
									$userJsonService->setUserInfo($user['uid'],$userJson);
									$zmq = $userJsonService->getZmq();
									$zmq->sendZmqMsg(609,array('type'=>'update_json','uid'=>$user['uid'],'json_info'=>$userJson));
								}
								
							}
						}
					}
					
				}
			}
			
		}
		
	
		public function actionFamilyRecords(){
			$this->family_db = Yii::app()->db_family;
			$familyCommand = $this->family_db->createCommand();
			$familyICommand = $this->family_db->createCommand();
			$count = $familyCommand->setText('SELECT count(*) FROM web_family_member')->queryScalar();
			$familyCommand->setText('');
			$pageSize = 10000;
			$page = ceil($count / $pageSize);
			$familyICommand->setText('delete from web_family_exit_records')->execute();
			$familyICommand->setText('INSERT INTO web_family_exit_records (uid,op_uid,family_id,is_dotey,live_type,join_time,quit_time,create_time) VALUES (:uid,:op_uid,:family_id,:is_dotey,:live_type,:join_time,:quit_time,:create_time)');
			for($i=0;$i<=$page;$i++){
				$offset = $i*$pageSize;
				echo "第{$i}组数据++\n\r";
				$familyCommand->setText('');
				$users = $familyCommand->from('web_family_member')->limit($pageSize,$offset)->queryAll();
				$familyCommand->setText('');
				foreach($users as $user){
					//如果在成员表有记录，那么表示该成员第一次加入未有退出记录表，或者表示曾经退出过后最新加入
					$familyICommand->bindParam(':uid',$user['uid']);
					$familyICommand->bindValue(':op_uid',0);
					$familyICommand->bindParam(':family_id',$user['family_id']);
					$familyICommand->bindValue(':live_type',0);
					$familyICommand->bindParam(':join_time',$user['create_time']);
					$familyICommand->bindValue(':quit_time',0);
					$familyICommand->bindParam(':create_time',$user['create_time']);
					$familyICommand->bindParam(':is_dotey',$user['family_dotey']);
					$familyICommand->execute();
				}
				
			}
			
			$count = $familyCommand->setText('SELECT count(*) FROM web_family_quit_records')->queryScalar();
			$familyCommand->setText('');
			$page = ceil($count / $pageSize);
			for($i=0;$i<=$page;$i++){
				$offset = $i*$pageSize;
				$quitRecords = $familyCommand->from('web_family_quit_records')->limit($pageSize,$offset)->queryAll();
				echo "第{$i}组数据--\n\r";
				foreach($quitRecords as $quitRecord){
					$familyICommand->bindParam(':uid',$quitRecord['uid']);
					$familyICommand->bindParam(':op_uid',$quitRecord['op_uid']);
					$familyICommand->bindParam(':family_id',$quitRecord['family_id']);
					$familyICommand->bindParam(':live_type',$quitRecord['type']);
					$familyICommand->bindParam(':join_time',$quitRecord['join_time']);
					$familyICommand->bindParam(':quit_time',$quitRecord['quit_time']);
					$familyICommand->bindParam(':create_time',$quitRecord['quit_time']);
					$familyICommand->bindParam(':is_dotey',$quitRecord['is_dotey']);
					$familyICommand->execute();
				}
			}
			
			
		}
			
		
}