<?php
/**
 * Redis数据恢复
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class RedisInitCommand extends CConsoleCommand {

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
				$otherReidsModel->saveArchivesIdsByUid($row['uid'],0,array($row['uid']=>$archivesUids));
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
		
	/**
	 * 取得待直播的档期
	 *
	 * @author su qian
	 * @return array
	 */
	public function actionWillLiveArchives(){
		$archiveservice = new ArchivesService();
		$channelDoteySortService = new ChannelDoteySortService();
		$otherRedisModel = new OtherRedisModel();
		$liveRecordsModel = LiveRecordsModel::model();
		$willLive = $liveRecordsModel->getWillLiveArchives();
		if(empty($willLive)){
			return array();
		}
		$willLive = $archiveservice->arToArray($willLive);
		usort($willLive, array($channelDoteySortService,'sortWaitArchivesByTimes'));
		$willLive = $archiveservice->buildDataByIndex($willLive,'record_id');
		$otherRedisModel->setWillLiveToRedisByArchiveRecordId(0,$willLive);
	
	}
}

?>