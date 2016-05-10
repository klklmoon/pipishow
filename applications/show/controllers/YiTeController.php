<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class YiTeController extends PipiController {

	//外链活动更新皮蛋数
	public function actionPipiEggs() {
		$uid = Yii::app()->request->getParam('uid'); //uid  --  用户ID
		$vcpoints = Yii::app()->request->getParam('vcpoints'); //vcpoints  --  皮蛋个数vcpoints
		$tid = Yii::app()->request->getParam('tid'); //tid  --  交易号,易瑞特提供
		$pass = Yii::app()->request->getParam('pass'); //pass  --  易瑞特传过来的密码，需要跟合作客户生成的密码进行匹配
		$key = "pipi"; //双方协商的密匙
		$tidLength = strlen($tid);
		$pwdMd5 = md5($uid . $vcpoints . $tid . $key);
		if ($uid<=0 || $vcpoints <= 0 || empty($pass) || $tidLength != 32) {
			$rs_json['uid'] = $uid;
			$rs_json['vcpoints'] = $vcpoints;
			$rs_json['tid'] = $tid;
			$rs_json['errno'] = '1001';
			$rs_str = json_encode($rs_json);
			echo $rs_str;
			die();
		} 
		if($pwdMd5 != $pass){
			$rs_json['uid'] = $uid;
			$rs_json['vcpoints'] = $vcpoints;
			$rs_json['tid'] = $tid;
			$rs_json['status'] = 'failure';
			$rs_json['errno'] = '1002';
			$rs_str = json_encode($rs_json);
			echo $rs_str;
			die();
		}
		$userService = new UserService();
		$user = $userService->getUserBasicByUids(array($uid));
		if(empty($user)){
			$rs_json['uid'] = $uid;
			$rs_json['vcpoints'] = $vcpoints;
			$rs_json['tid'] = $tid;
			$rs_json['status'] = 'failure';
			$rs_json['errno'] = '1005';
			$rs_str = json_encode($rs_json);
			echo $rs_str;
			die();
		}
		
		$taskService = new TaskService();
		$consumeService = new ConsumeService();
		$task = $taskService->getTaskRecordsByTypeAndSerial(TASK_TYPE_YIRUITE,$tid);
		
		if($task){
			$rs_json['uid'] = $uid;
			$rs_json['vcpoints'] = $vcpoints;
			$rs_json['tid'] = $tid;
			$rs_json['status'] = 'failure';
			$rs_json['errno'] = '1003';
			$rs_str = json_encode($rs_json);
			echo $rs_str;
			die();
		}
		
		$changeRelation = Yii::app()->params->change_relation;
		//$toEgg = isset($changeRelation['rmb_to_pipiegg'])?$changeRelation['rmb_to_pipiegg']:1;
		//$pipieggs = $vcpoints*$toEgg;
		$pipieggs = $vcpoints;
		if ($consumeService->addEggs($uid,$pipieggs)) {
			$records['uid'] = $uid;
			$records['pipiegg'] = $pipieggs;
			$records['source'] = SOURCE_ACTIVITY;
			$records['sub_source'] = SUBSOURCE_ACTIVITY_YIRUITE;
			$records['from_target_id'] = $uid;
			$records['client'] = CLIENT_ACTIVITES;
			$consumeService->saveUserPipiEggRecords($records,1);
			
			$taskLog['uid'] = $uid;
			$taskLog['pipiegg'] = $pipieggs;
			$taskLog['task_serial'] = $tid;
			$taskLog['task_type'] = TASK_TYPE_YIRUITE;				
			
			$taskService->saveTaskRecord($taskLog);
			$attribute['uid'] = $uid;
			$attribute['pipiegg'] = $pipieggs;
			$consumeService->appendConsumeData($attribute);
			
			$rs_json['uid'] = $uid;
			$rs_json['vcpoints'] = $vcpoints;
			$rs_json['tid'] = $tid;
			$rs_json['status'] = 'success';
			$rs_str = json_encode($rs_json);
			echo $rs_str;
			die();
		} else {
			$rs_json['uid'] = $uid;
			$rs_json['vcpoints'] = $vcpoints;
			$rs_json['tid'] = $tid;
			$rs_json['status'] = 'failure';
			$rs_json['errno'] = '1007';
			$rs_str = json_encode($rs_json);
			echo $rs_str;
			die();
		}
			
		
	}
}

?>