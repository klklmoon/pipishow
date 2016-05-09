<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: templates.xml 894 2010-12-28 07:55:25Z lei wei  $ 
 * @package 
 */
class ChatWidget extends PipiWidget {
	
	public $dotey;      	 //主播信息
	
	public $notice;          //公告
	
	public $private_notice;  //私聊公告
	
	public $archives_id;     //档期Id
	
	public $flashPath;        //路径
	
	public $width=400;       //聊天窗口宽度
	
	public $heigth=350;      //聊天窗口高度
	
	public $live_status=0;     //直播状态
	
	public $crown;            //皇冠粉丝
	
	public $chatSet;          //直播间发言设置
	
	public $socketIp;        //socket地址
	
	public $policyPort;     //协议端口号
	 
	public $port;           //数据端口号
	
	public $userList;  //用户列表
	
	public $doteyProfile;   //主播资料提示
	
	public $target = '';
	
	public function init(){
		/* @var $contrller PipiController */
		$contrller = Yii::app()->getController();
		/* @var $clientScript CClientScript */
		$clientScript = Yii::app()->getClientScript();
		$this->flashPath = $contrller->pipiFrontPath;
		$clientScript->registerCSSFile($this->flashPath.'/css/archives/chatbox.css?token='.$contrller->hash);
		$clientScript->registerScriptFile($this->flashPath.'/js/archives/chat.js?token='.$contrller->hash,CClientScript::POS_END);
		$clientScript->registerScriptFile($this->flashPath.'/js/archives/jquery.textarearesizer.js?token='.$contrller->hash,CClientScript::POS_END);
		if($this->userList)
			$clientScript->registerScriptFile($this->flashPath.'/js/archives/userlist.js?token='.$contrller->hash,CClientScript::POS_END);
		$clientScript->registerScriptFile($this->flashPath.'/js/archives/dice.js?token='.$contrller->hash,CClientScript::POS_END);
		$clientScript->registerScriptFile($this->flashPath.'/js/archives/purview.js?token='.$contrller->hash,CClientScript::POS_END);
		$clientScript->registerScriptFile($this->flashPath.'/js/archives/view.js?token='.$contrller->hash,CClientScript::POS_END);
		$clientScript->registerScriptFile($this->flashPath.'/js/common/ubb.js?token='.$contrller->hash,CClientScript::POS_END);
		$hrefTarget = Yii::app()->request->getParam('target','_blank');
		$this->target = in_array($hrefTarget,array('_blank','_self','_parent','_self')) ? $hrefTarget : '_blank';
	}
	
	//产生聊天token
	public function createChatToken(){
		$token='';
		if(Yii::app()->user->id){
			$archivesService=new ArchivesService();
			$token=$archivesService->createChatToken(Yii::app()->user->id,$this->archives_id);
		}
		
		return $token;
	}
	
	
	public function run(){
		$chatServer['uid']=Yii::app()->user->id;
		$chatServer['imgSite']=Yii::app()->params->images_server['url'];
		$chatServer['dotey']=$this->dotey;
		$chatServer['crown']=$this->crown;
		$chatServer['archives_id']=$this->archives_id;
		$chatServer['live_status']=$this->live_status;
		$chatServer['notice']=unserialize($this->notice);
		$chatServer['private_notice']=unserialize($this->private_notice);
		$webConfSer = new WebConfigService();
		$c_key = $webConfSer->getGiftMsgPushKey();
		$giftSet=$webConfSer->getWebConfig($c_key);
		$chatServer['gift_global_message']=isset($giftSet['c_value']['global'])?$giftSet['c_value']['global']:8000;
		$chatServer['gift_private_message']=isset($giftSet['c_value']['private'])?$giftSet['c_value']['private']:10;
		$chatServer['width']=$this->width;
		$chatServer['heigth']=$this->heigth;
		$chatServer['flashPath']=$this->flashPath;
		$chatServer['socketIp']=$this->socketIp;
		$chatServer['policyPort']=$this->policyPort;
		$chatServer['port']=$this->port;
		$chatServer['chatSet']=$this->chatSet;
		$chatServer['userList']=json_encode($this->userList);
		$chatServer['doteyProfile']=$this->doteyProfile;
		$chatServer['token']=$this->createChatToken();
		$forbidenService=new ForbidenService();
		if($chatServer['uid']>0){
			$kickOut=$forbidenService->getArchivesKickout($this->archives_id,$chatServer['uid']);
		}
		$chatServer['kickout']=isset($kickOut)?true:false;
		$this->render('chat',array('chatServer'=>$chatServer));
	}
	
	
}

?>