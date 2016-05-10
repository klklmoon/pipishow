<?php
define('TASK_TYPE_YIRUITE','yiruite');
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class TaskService extends PipiService {
	protected $startDate="2013-08-08"; //新手任务功能上线日，上线日前的注册用户不能再领取新手任务

	public function saveTaskRecord(array $taskRecord){
		if(!isset($taskRecord['uid']) || $taskRecord['uid'] <= 0){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		
		$taskRecordModel = new UserTaskRecordsModel();
		$taskRecord['create_time'] = time();
		$this->attachAttribute($taskRecordModel,$taskRecord);
		$taskRecordModel->save();
		return $taskRecordModel->getPrimaryKey();
	}
	
	public function getTaskRecordsByTypeAndSerial($type,$serial){
		if(empty($type) || empty($serial)){
			return $this->setError(Yii::t('common','Parameter is empty'),array());
		}
		$taskRecordModel = UserTaskRecordsModel::model();
		$criteria = $taskRecordModel->getDbCriteria();
		$criteria->condition = ' task_type = :taskType AND task_serial  = :taskSerial ';
		$criteria->params = array(':taskType'=>$type,':taskSerial'=>$serial);
		$taskModel = $taskRecordModel->find($criteria);
		if($taskModel){
			return $taskModel->attributes;
		}
		return array();
	}
	
	/**
	 * 获取新手任务列表
	 * @author hexin 2013-07-18
	 * @param int $uid
	 * @return array
	 */
	public function getTaskList($uid){
		$tasks = $this->getAllTask();
		$tids = array();
		foreach($tasks as $k => &$t){
			if($t['status'] == 0 ){
				unset($tasks[$k]);
				continue;
			}
			$t['done'] = 0;
			$t['reward'] = 0;
			$tids[] = $t['tid'];
		}
		if(!empty($tasks) && $uid > 0){
			$records = UserTaskRecordsModel::model()->getTaskRecords($uid, $tids);
			$records = $this->buildDataByIndex($records, 'target_id');
			foreach($tasks as &$t){
				if(isset($records[$t['tid']])){
					$t['done'] = 1;
					$t['reward'] = $records[$t['tid']]['reward'];
				}else{
					$t['done'] = intval($this->taskCheck($t['tid'], $uid));
				}
			}
		}
		return $tasks;
	}
	
	/**
	 * 检查注册日期，可否算新手
	 * @return boolean
	 */
	public function checkDate(){
		$userService = new UserService();
		$user = $userService->getUserBasicByUserNames(array(Yii::app()->user->name));
		$user = array_pop($user);
		return $user['create_time'] >= strtotime($this->startDate);
	}
	
	/**
	 * 获取新手任务详情
	 * @param int $tid
	 * @return array
	 */
	public function getTask($tid){
		$tasks = $this->getAllTask();
		$tasks = $this->buildDataByIndex($tasks, 'tid');
		return $tasks[$tid];
	}
	
	/**
	 * 新手任务领取奖励
	 * @param int $tid
	 * @param int $uid
	 * @return number
	 */
	public function doTask($tid, $uid){
		$changeRelation = Yii::app()->params->change_relation;
		$taskRecordsModel = UserTaskRecordsModel::model();
		$records = $taskRecordsModel->getTaskRecords($uid, array($tid));
		$records = $this->buildDataByIndex($records, 'target_id');
		if(isset($records[$tid]) && $records[$tid]['reward'] == 1) return 4;
		
		$task = $this->getTask($tid);
		if($task['status'] == 0) return 2;
		if($tid > 8) return 2;//新添加的未通知开发编码对应处理方法的新任务
		//非安全绑定任务的检查安全绑定任务的完成状态
		if(!($tid == 3)){
			$userService = new UserService();
			$user = $userService->getUserBasicByUids(array($uid));
			$user = array_pop($user);
			if(empty($user['reg_mobile'])) return 5;
		}
		if(!$this->taskCheck($tid, $uid)) return 3; //任务完成状态检查
		
		$pipiegg = $task['pipiegg'];
		if($taskRecordsModel->reward($tid, $uid, $pipiegg)){
			$consume = new ConsumeService();
			$consume->updateUserJsonInfo($uid, array('pipiegg' => true));
			$record = array(
				'uid'			=> $uid,
				'from_target_id'=> $tid,
				'to_target_id'	=> 0,
				'pipiegg'		=> $pipiegg,
				'record_sid'	=> 0,
				'num'			=> 0,
				'source'		=> SOURCE_TASKS,
				'sub_source'	=> SUBSOURCE_TASKS_TASK,
				'extra'			=> '新手任务奖励 - '.$task['name'],
				'client'		=> CLIENT_ARCHIVES
			);
			if(!$consume->saveUserPipiEggRecords($record)){
				return -2;
			}
			return 1;
		}else return 3;
	}
	
	/**
	 * 任务完成状态检查
	 * @param int $tid
	 * @param int $uid
	 * @return boolean
	 */
	private function taskCheck($tid, $uid){
		//注册任务
		if($tid == 1){
			//经过登陆检查，注册时间检查，任务领取记录检查，都未完成的直接就可以领取，此处什么也不需要做
			return true;
				
			//绑定安全邮箱
		}elseif($tid == 2){
			$userService = new UserService();
			$user = $userService->getUserBasicByUids(array($uid));
			$user = array_pop($user);
			if(!empty($user['reg_email'])) return true;
				
			//绑定安全手机
		}elseif($tid == 3){
			$userService = new UserService();
			$user = $userService->getUserBasicByUids(array($uid));
			$user = array_pop($user);
			if(!empty($user['reg_mobile'])) return true;
				
			//初次签到
		}elseif($tid == 4){
			if(UserCheckinModel::model()->hasCheckin($uid)) return true;
				
			//关注一位主播
		}elseif($tid == 5){
			$weibo = new WeiboService();
			if(count($weibo->getUserAttentionsByUid($uid))) return true;
		
			//初次送出礼物
		}elseif($tid == 6){
			$gift = new GiftService();
			$record = $gift->getUserGiftSendRecordsByUid($uid);
			if($record['count'] > 0) return true;
				
			//观看直播满10小时
		}elseif($tid == 7){
			$partner = new PartnerService();
			$hours = $partner->getViewDurationByUid($uid);
			if($hours >= 10) return true;
				
			//首次完成充值
		}elseif($tid == 8){
			$userRechargeModel  =  UserRechargeRecordsModel::model();
			$recharge = $userRechargeModel->getFirstCharge($uid, $this->startDate);
			if(!empty($recharge)) return true;
				
		}
		return false;
	}
	
	/**
	 * 读新手任务列表
	 * @return array
	 */
	public function getAllTask(){
		$cache = new OtherRedisModel();
		$tasks = $cache->getTaskList();
		if(empty($tasks)){
			$tasks = $this->saveTaskToCache();
		}
		return $tasks;
	}
	
	/**
	 * 存新手任务列表
	 * @return array
	 */
	private function saveTaskToCache(){
		$tasks = TaskModel::model()->findAll();
		$tasks = $this->arToArray($tasks);
		if(!empty($tasks)){
			$cache = new OtherRedisModel();
			$cache->setTaskList($tasks);
		}
		return $tasks;
	}
	
	/**
	 * 更新新手任务
	 * @param array $task
	 * @return int
	 */
	public function adminSaveTask(array $task){
		if (isset($task['tid']) && $task['tid'] <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
		$model = new TaskModel();
		if(isset($task['tid'])) $model = $model->findByPk($task['tid']);
		$this->attachAttribute($model, $task);
		if (!$model->validate()) {
			return $this->setNotices($model->getErrors(), 0);
		}
		$model->save();
		$this->saveTaskToCache();
		return $model->getPrimaryKey();
	}
	
	/**
	 * 获取后台显示的新手任务图片地址
	 * @param string $fileName
	 * @return string
	 */
	public function getAdminTaskImage($fileName){
		return $this->getShowAdminUrl().'task/'.$fileName;
	}
	
	/**
	 * 获取前台显示的新手任务图片地址
	 * @param string $fileName
	 * @return string
	 */
	public function getTaskImage($fileName){
		return empty($fileName) ? '' : $this->getUploadUrl().'task/'.$fileName;
	}
}

?>