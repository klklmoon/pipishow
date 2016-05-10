<?php
/**
 * 新手任务
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-7-18 下午1:17:16 hexin $ 
 * @package
 */
class TaskController extends PipiController{
	
	/**
	 * 获取任务列表
	 */
	public function actionTaskList(){
		$taskService = new TaskService();
		$tasklist = array();
		if(!$this->isLogin || $this->isLogin && $taskService->checkDate()){
			$uid=Yii::app()->user->id;
			$tasklist = $taskService->getTaskList($uid);
			$allDone = 1;
			foreach($tasklist as $t){
				$allDone *= $t['reward'];
			}
			if($allDone) $tasklist = array();
			foreach($tasklist as &$t){
				$t['pic'] = $taskService->getTaskImage($t['pic']);
			}
		}
		$this->renderPartial('task', array('tasklist'	=> $tasklist));
	}
	
	/**
	 * 做任务
	 */
	public function actionDoTask(){
		if(!$this->isLogin) $this->renderToJson(-1,'请先登陆');
		$taskService = new TaskService();
		if(!$taskService->checkDate()) $this->renderToJson(1,'任务完成!');
		
		$tid=intval(Yii::app()->request->getParam('tid'));
		$uid=Yii::app()->user->id;
		
		$r = $taskService->doTask($tid, $uid);
		$this->renderToJson($r);
	}
}