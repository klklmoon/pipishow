<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package 
 */
class GiftWidget extends PipiWidget {
	
	public $position;    //礼物显示位置
	
	public $giftPath;    //礼物路径
	
	public $dotey;      //主播信息
	
	public function init(){
		/* @var $contrller PipiController */
		$contrller = Yii::app()->getController();
		/* @var $clientScript CClientScript */
		$clientScript = Yii::app()->getClientScript();
		$staticPath = $contrller->pipiFrontPath;
		$clientScript->registerCssFile($staticPath.'/css/archives/livingbox.css?token='.$contrller->hash);
		$clientScript->registerScriptFile($staticPath.'/js/archives/gift.js?token='.$contrller->hash,CClientScript::POS_END);
	}
	
	public function run(){
		$giftService=new GiftService();
		$gift['dotey']=$this->dotey;
		$gift['giftList']=$giftService->getCatGiftList();
		$this->render('gift',array('gift'=>$gift));
	}
	
	
}

?>