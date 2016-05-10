<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <aoxue.1988.su.qian@163.com>
 * @version $Id: PublicController.php 8317 2013-03-29 01:19:47Z suqian $ 
 * @package 
 */
class PartnerController extends PipiApiController {
	
	//protected  $isCheckApp = false;
	//protected $isCheckToken = false;
	
	public function actionLoginStatOnline(){
		
	   $uids = Yii::app()->request->getParam('suid');
	   $doteyIds =Yii::app()->request->getParam('dotey_id');
	   $archivesIds = Yii::app()->request->getParam('archives_id');
	   $time_online = Yii::app()->request->getParam('time_online');
	   if(!is_array($uids) || !is_array($doteyIds) || !is_array($archivesIds) || !is_array($time_online)){
	   		echo -1;
	   		Yii::app()->end(-1,true);
	   }
	   if(empty($uids)  || empty($doteyIds) || empty($archivesIds) || empty($time_online) ){
	   		echo -2;
	   		Yii::app()->end(-2,true);
	   }
	   $data = array();
	   $i = 0;
	   foreach($uids as $key=>$uid){
	   		$data[$i]['uid'] = $uid;
	   		$data[$i]['dotey_id'] = $doteyIds[$key];
	   		$data[$i]['archives_id'] = $archivesIds[$key];
	   		$data[$i]['time_online'] = $time_online[$key];
	   		$data[$i]['create_time'] = time();
	   }
	   
	   if(empty($data)){
	   		echo -3;
	   		Yii::app()->end(-3,true);
	   }
	   $partnerService = new PartnerService();
	   echo (int)$partnerService->batchSaveLoginStateOnLine($data);
	}

}

?>