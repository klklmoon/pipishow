<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package 
 */
class FlashWidget extends PipiWidget {
	public $id;
	public $width=460;
	
	public $height=345;
	
	public $archivesId;
	
	public $model='live';  //直播状态:live,录制状态:record
	
	public $flashPath;
	
	public $giftStar;
	
	public $doteyHalloween;
	
	
	public function init(){
		/* @var $contrller PipiController */
		$contrller = Yii::app()->getController();
 		/* @var $clientScript CClientScript */
 		$clientScript = Yii::app()->getClientScript();
		$this->flashPath = $contrller->pipiFrontPath;
		$clientScript->registerScriptFile($this->flashPath.'/swf/archives/swfobject.js?token='.$contrller->hash,CClientScript::POS_HEAD);
	}
	
	public function getLiveServer(){
		$archivesService=new ArchivesService();
		$server=$archivesService->getArchivesLiveServerByArchivesId($this->archivesId);
		$liveServer=$archivesService->getLiveServerByServerIds($server[0]['server_id']);
		return $liveServer[$server[0]['server_id']];
	}
	
	
	public function run(){
		$flash['width']=$this->width;
		$flash['height']=$this->height;
		$flash['archivesId']=$this->archivesId;
		$flash['source']=$this->getLiveServer();
		$flash['model']=$this->model;
		$flash['flashPath']=$this->flashPath;
		$flash['giftStar']=$this->giftStar;
		$flash['doteyHalloween']=$this->doteyHalloween;
		$this->render('flash',array('flash'=>$flash));
	}
}

?>