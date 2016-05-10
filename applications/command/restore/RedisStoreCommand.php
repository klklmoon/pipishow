<?php
/**
 * 重构Redis数据库数据
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class RedisStoreCommand extends CConsoleCommand {

		public function actionIndex(){
			echo 'cron is start';
		}
		
		/**
		 * 将主播信息写到Redis
		 */
		public function actionDoteyBase(){
			$doteyBaseModel = new DoteyBaseModel();
			$doteys = $doteyBaseModel->findAll(array('condition'=> 'status = 1'));
			$otherReidsModel = new OtherRedisModel();
			foreach($doteys as $dotey){
				echo $otherReidsModel->setDoteyInfoToRedisByUid($dotey['uid'],$dotey->attributes);
			}
		}
		
		/**
		 * 将当期信息初始化到Redis
		 */
		public function actionArchives(){
			$archivesModel=new ArchivesModel();
			$data=$archivesModel->findAll();
			$otherReidsModel = new OtherRedisModel();
			$archivesServer=new ArchivesService();
			foreach($data as $row){
				$archivesIds=$archivesServer->getArchivesByArchivesId($row['archives_id']);
				$otherReidsModel->saveArchives($row['archives_id'],$archivesIds);
				$archivesUids = $archivesModel->getArchivesByUids(array($row['uid']),0);
				$otherReidsModel->saveArchivesIdsByUid(array($row['uid']),0,array($row['uid']=>$archivesUids));
			}
		}
		
		public function actionRepairDoteyBase(){
			$userDoteyDbCommand =  $this->user_db->createCommand();
			//$userExtendDbCommand =  $this->user_db->createCommand();
			$userBaseDbCommand =  $this->user_db->createCommand();
			$service = new UserService();
			$userDoteyDbCommand->setText('select * from web_dotey_base');
			$doteys = $userDoteyDbCommand->queryAll();
			
			foreach($doteys as $dotey){
				$userBaseDbCommand->setText('select * from web_user_base where uid='.$dotey['uid']);
				$user = $userBaseDbCommand->queryRow();
				if(empty($user)){
					continue;
				}
				$userType = (int)$user['user_type'];
				if($dotey['status'] == 1 && !$service->hasBit($userType,USER_TYPE_DOTEY)){
					$userGType = $service->grantBit($userType,USER_TYPE_DOTEY);
					$userBaseDbCommand->setText('update web_user_base set user_type = '.$userGType .' where uid='.$dotey['uid']);
					$userBaseDbCommand->execute();
					echo $userType.'grant'.'update web_user_base set user_type = '.$userGType .' where uid='.$dotey['uid']."\n\r";
				}elseif($dotey['status'] == 2 && $service->hasBit($userType,USER_TYPE_DOTEY)){
					$userRType = $service->revokeBit($userType,USER_TYPE_DOTEY);
					$userBaseDbCommand->setText('update web_user_base set user_type = '.$userRType .' where uid='.$dotey['uid']);
					$userBaseDbCommand->execute();
					echo $userType.'revock'.'update web_user_base set user_type = '.$userRType .' where uid='.$dotey['uid']."\n\r";
				}else{
					
				}
			}
		}
		
		/**
		* 将黑名单初始化到Redis
		* @author guoshaobo
		*/
		public function actionBadWord()
		{
		$wordServ = new WordService();
		$wordServ->getChatWord();
		}
}

?>