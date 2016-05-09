<?php
/**
 * 新手任务小工具
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-7-17 上午8:51:10 hexin $ 
 * @package
 */
class TaskWidget extends PipiWidget{
	protected $allow = false;
	protected $allow_url = array(
		'index/index',
		'archives/index',
		'channel/category',
		'channel/songs'
	); //允许出现新手任务的页面
	protected $pipiFrontPath;
	
	public function init(){
		parent::init();
		/* @var $contrller PipiController */
		$controller = Yii::app()->getController();
		if(in_array($controller->getId()."/".$controller->getAction()->getId(), $this->allow_url)){
			$this->allow = true;
			if($controller->isLogin){
				$task = new TaskService();
				if(!$task->checkDate()) $this->allow = false;
			}
		}
		/* @var $clientScript CClientScript */
		$clientScript = Yii::app()->getClientScript();
		$this->pipiFrontPath = $controller->pipiFrontPath;
		$clientScript->registerCssFile($this->pipiFrontPath.'/css/task/task.css?token='.$controller->hash);
		$clientScript->registerScriptFile($this->pipiFrontPath.'/js/task/task.js?token='.$controller->hash,CClientScript::POS_END);
	}
	
	public function run(){
		if($this->allow){
			$controller = Yii::app()->getController();
			if($controller->getId()."/".$controller->getAction()->getId() == $this->allow_url[1]){
				$is_archive = true;
			}
			$this->render('task', array('is_archive'=>$is_archive));
		}
	}
}