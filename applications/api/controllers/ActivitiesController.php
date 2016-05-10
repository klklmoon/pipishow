<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author supeng <supeng@pipi.cn>
 * @version $Id: ActivitiesController.php 8317 2013-03-29 01:19:47Z supeng $ 
 * @package api
 * @subpackage consume
 */
class ActivitiesController extends PipiApiController {

	/**
	 * 活动（天使守护）批量更新守护星
	 */
	public function actionUpdateGuardStar(){
		$uid 	= Yii::app()->request->getParam('suid');
		$dotey_uid 	= Yii::app()->request->getParam('dotey_uid');
		$stars 	= Yii::app()->request->getParam('stars');
		$cycle 	= Yii::app()->request->getParam('cycle');
		$stime 	= Yii::app()->request->getParam('stime');
		$etime	= Yii::app()->request->getParam('etime');
		
		//正确性校验
		if (count($uid) <= 0 || 
			count($uid) != count($dotey_uid) || 
			count($uid) != count($stars) || 
			count($uid) != count($cycle) ||
			count($uid) != count($stime) || 
			count($uid) != count($etime)){
			$this->responseClient(-1, 'parameter is error');
		}
		//数据正确性校验
		foreach($uid as $k => $v){
			if($v <= 0){
				unset($dotey_uid[$k]);
				unset($stars[$k]);
				unset($cycle[$k]);
				unset($stime[$k]);
				unset($etime[$k]);
			}
		}
		if(empty($uid)){
			$this->responseClient(-2, 'parameter is empty');
		}
		$guardSer = new GuardAngelService();
		foreach($uid as $k => $v){
			$_uid = $v;
			$_dotey_uid = $dotey_uid[$k];
			$_cycle = $cycle[$k];
			$_star = $stars[$k];
			$_stime = $stime[$k];
			$_etime = $etime[$k];
			$guardSer->updateGuardAngel($_uid, $_dotey_uid, $_cycle, $_star, $_stime, $_etime);
		}
		$this->responseClient(0, 'success');
	}
	
	/**
	 * 活动（天使守护）批量删除守护星
	 */
	public function actionDelGuardStar(){
		$guardSer = new GuardAngelService();
		$insRs = $guardSer->insertNextCycle();
		if(isset($insRs['record_id'])){
			$record_id = $insRs['record_id'];
			$userList = $guardSer->getAllGuardUserList();
			if($userList && $guardSer->delAllRelation()){
				$guardSer->delGuardAngelDoteyRanking();
				$guardSer->delGuardAngelUserRanking();
				foreach($userList as $v){
					$guardSer->clearGuardStarByUid($v['uid']);
				}
				$this->responseClient(0, 'success');
			}else{
				$guardSer->delRecords($record_id);
				$this->responseClient(-1, 'guard relation list is empty');
			}
		}
		$this->responseClient(-2, 'insert next cycle fail');
	}
}

?>