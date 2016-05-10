<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package api
 * @subpackage archives
 */
class ArchivesController extends PipiApiController {
	
	
	/**
	 * 给档期分配chatServer
	 */
	public function actionSaveChatServer(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$policy_port=Yii::app()->request->getParam('policy_port');
		$data_port=Yii::app()->request->getParam('data_port');
		$domain=Yii::app()->request->getParam('domain');
		$server=array();
		if(is_array($archives_id)){
			foreach($archives_id as $key=>$row){
				$server[$row]['archives_id']=$row;
				$server[$row]['policy_port']=$policy_port[$key];
				$server[$row]['data_port']=$data_port[$key];
				$server[$row]['domain']=$domain[$key];
			}
			
		}else{
			Yii::app()->end(-2,true);
		}
		$archivesService=new ArchivesService();
		$chatServer=$archivesService->getChatServerByArchivesIds($archives_id);
		$newServer=array();
		foreach($chatServer as $row){
			$newServer[$row['archives_id']]=$row;
		}
		foreach($newServer as $key=>$row){
			$newChatServer['chat_id']=$row['chat_id'];
			isset($server[$row['archives_id']]['policy_port'])&&$newChatServer['policy_port']=$server[$row['archives_id']]['policy_port'];
			isset($server[$row['archives_id']]['data_port'])&&$newChatServer['data_port']=$server[$row['archives_id']]['data_port'];
			isset($server[$row['archives_id']]['domain'])&&$newChatServer['domain']=$server[$row['archives_id']]['domain'];
			$archivesService->saveChatServer($newChatServer);
		}
		
		Yii::app()->end(0,true);
	}
	
	/**
	 * 批量保存档期的在线人数纪录
	 * @author hexin
	 */
	public function actionSaveOnlineRecord(){
		$archives = Yii::app()->request->getParam('archives_id');
		$domain = Yii::app()->request->getParam('domain');
		$total = Yii::app()->request->getParam('total');
		$online_total = Yii::app()->request->getParam('online_total');
		if(count($archives) <= 0 || count($archives) != count($domain) || count($archives) != count($total) || count($archives) != count($online_total)){
			Yii::app()->end(-1, true);
		}
		$record = array();
		$flag = false;
		foreach($archives as $k => $archives_id){
			if(intval($archives_id) <= 0) continue;
			$record = array(
				'archives_id'	=> intval($archives_id),
				'domain'		=> $domain[$k],
				'total'			=> intval($total[$k]),
				'online_total'	=> intval($online_total[$k])
			);
			$model = new ArchivesOnlineRecordModel();
			$f_model = $model -> findByPk(intval($archives_id));
			if(!empty($f_model)) $model = $f_model;
			foreach($record as $key => $val){
				$model->$key = $val;
			}
			if(!empty($f_model)){
				if($model -> update(array('total','online_total'))) $flag = 0;
			}else{
				if($model -> save()) $flag = 0;
			}
		}
		Yii::app()->end($flag, true);
	}
	
	/**
	 * 批量保存直播时长
	 * @author hexin
	 */
	public function actionLiveLength(){
		$record_id = Yii::app()->request->getParam('record_id');
		$duration  = Yii::app()->request->getParam('duration');
		if(count($record_id) <= 0 || count($record_id) != count($duration)){
			Yii::app()->end(-1, true);
		}
		foreach($record_id as $k => $v){
			if($v <= 0){
				unset($record_id[$k]);
				unset($duration[$k]);
			}
		}
		if(empty($record_id)){
			Yii::app()->end(-2, true);
		}
		$archivesService = new ArchivesService();
		$flag = -3;
		$records = $archivesService -> getLiveRecordByRecordIds($record_id);
		foreach($record_id as $k => $v){
			if(isset($records[$v])){
				$record = $records[$v];
				$record['duration'] = $duration[$k];
				$archivesService -> saveArchivesLiveRecords($record); //更新数据库
				$archivesService -> saveArchivesRedisByArchivesId($record['archives_id']); //更新redis
				$flag = 0;
			}
		}
		Yii::app()->end($flag, true);
	}
	
	/**
	 * 批量结束档期
	 * @author hexin
	 */
	public function actionEndStatus(){
		$record_id = Yii::app()->request->getParam('record_id');
		//$status = Yii::app()->request->getParam('status');
		if(!is_array($record_id) || count($record_id) <= 0){
			Yii::app()->end(-1, true);
		}
		foreach($record_id as $k => $v){
			if($v <= 0){
				unset($record_id[$k]);
			}
		}
		if(empty($record_id)){
			Yii::app()->end(-2, true);
		}
		$flag = -3;
		$archivesService = new ArchivesService();
		$records = $archivesService -> getLiveRecordByRecordIds($record_id);
		$archives_ids = array_keys($archivesService->buildDataByIndex($records, 'archives_id'));
		$archives = $archivesService->getArchivesByArchivesIds($archives_ids);
		foreach($archives as $k => $a){
			//限制只有开播状态的才可以结束
			if(isset($a['live_record']) && $a['live_record']['status'] == START_LIVE){
				$archivesService->stopArchivesLive($a['uid'], $a['archives_id'], strtotime('-15 minutes', time()));
				$flag = 0;
			}
		}
		Yii::app()->end($flag, true);
	}
}

?>