<?php 
$userService=new UserService();
$forbidenService=new ForbidenService();
if($chatServer['uid']>0){
	$kickOut=$forbidenService->getArchivesKickout($this->archives_id,$chatServer['uid']);
}
?>
<script>
var archives={
		domain:'<?php echo DOMAIN;?>',
		staticPath:'<?php echo $chatServer['flashPath'];?>',
		imgSite:'<?php echo $chatServer['imgSite'];?>',
		doteyAvatar:'<?php echo $userService->getUserAvatar($chatServer['dotey']['uid'],'small');?>',
		live_status:<?php echo $chatServer['live_status'];?>,
		kickout:<?php echo isset($kickOut)?'true':'false';?>,		
		chatSet:{
			"tourist_set":<?php echo isset($chatServer['chatSet']['tourist_set'])?$chatServer['chatSet']['tourist_set']:0;?>,
			"global_set":<?php echo isset($chatServer['chatSet']['global_set'])?$chatServer['chatSet']['global_set']:0;?>
		},
		gift_message:{
			"global_message":<?php echo $chatServer['gift_global_message'];?>,
			"private_message":<?php echo $chatServer['gift_private_message'];?>		
			},
		archives_id:<?php echo $chatServer['archives_id'];?>,
		dotey:<?php echo json_encode($chatServer['dotey']);?>,
		crown:{'nickname':'<?php echo addslashes($chatServer['crown']['nickname']);?>','uid':<?php echo $chatServer['crown']['uid']?$chatServer['crown']['uid']:0;?>},
		chatServer:{
			"serverId":1,
			"policyPort":'<?php echo $chatServer['policyPort'];?>',
			"port":'<?php echo $chatServer['port'];?>',
			"socketIp":'<?php echo $chatServer['socketIp'];?>'
		},
		express:<?php if($chatServer['express']){echo 'true';}else{echo 'false';}?>,
		userList:<?php echo $chatServer['userList'];?>,		
		token:'<?php echo $chatServer['token'];?>'

};


</script>
<div class="flash-car" id="playFlashCar"></div>
<div class="chat-box chat-con">
      <p class="welcome" id="common_notice">
      <?php if(empty($chatServer['notice']['url'])):?>
      	<a><?php echo empty($chatServer['notice']['content'])?'本直播间暂未设置公告内容':$chatServer['notice']['content'];?></a>
      <?php else:?>
      	<a href="<?php $this->getTargetHref($chatServer['notice']['url']);?>" target="<?php echo $this->target;?>" title="<?php echo $chatServer['notice']['content'];?>" ><?php echo empty($chatServer['notice']['content'])?'本直播间暂未设置公告内容':$chatServer['notice']['content'];?></a>
     <?php endif;?>
     </p>
    <?php if(Yii::app()->user->id==$chatServer['dotey']['uid']&& $chatServer['doteyProfile']==true):?>
      <div id="MateCon" class="matecon clearfix">
         <p>才貌双全的主播大人，您还没有填写主播资料，立刻前往<a href="<?php $this->getTargetHref('index.php?r=account/dotey')?>" target="<?php echo $this->target;?>" class="pink">账户中心-主播资料</a>页面进行录入，便于粉丝了解你。</p>
         <a href="<?php $this->getTargetHref('index.php?r=account/dotey')?>" target="<?php echo $this->target;?>" class="fill-btn">填写资料</a>
      </div>
      <?php endif;?>
       <div class="luck-gift clearfix" id="luckGift"></div>
      <div class="chat-msg" id="commonChat">
           <div id="SetingBox" class="setingbox">
                <p class="seting"></p>
                <span class="seting-con">
                      <a href="javascript:void(0)" onclick="Chat.cleanScreen('commonChat')"  class="clear" title="清屏">清屏</a>
                      <a href="javascript:void(0)" onclick="Chat.chatSrollSet()" class="roll" id="chatSrollSet"  title="滚屏">滚屏</a>
                </span>
           </div>
            <div class="cuspopmenu" id="purviewList"><a href="#">对Ta说</a></div>
      </div><!--.chat-msg-->
     <div class="resize"></div>
     <div class="chat-msgpre" id="privateChat">
     	  <div class="setingbox">
               <p class="seting"></p>
               <span class="seting-con">
                     <a href="javascript:void(0)" onclick="Chat.cleanScreen('privateChat')"  class="clear" title="清屏">清屏</a>
               	     <a href="javascript:void(0)" onclick="Chat.privateSrollSet()" class="roll" id="privateSrollSet" title="滚屏">滚屏</a>
               </span>
          </div>
          <?php if($chatServer['uid']<=0):?>
          	<a href="javascript:void(0)" onclick="$.User.loginController('register')" class="plaselogin">>>主播私聊你了，请<em>注册登录</em>查看<<</a>
          <?php endif;?>
           <p class="chatcon" id="private_notice">
           <?php if(isset($chatServer['private_notice']['url'])&&!empty($chatServer['private_notice']['url'])):?>
           <a class="pink" href="javascript:void(0)" id="<?php echo $chatServer['dotey']['uid'].'|*|'.$chatServer['dotey']['nickname'].'|*|'.$chatServer['dotey']['rank'].'|*|'.$chatServer['dotey']['purviewrank'];?>"><?php echo $chatServer['dotey']['nickname'];?></a>对您说：<a href="<?php echo $chatServer['private_notice']['url'];?>" title="<?php $this->getTargetHref($chatServer['private_notice']['content']); ?>" target="<?php echo $this->target;?>"><?php echo $chatServer['private_notice']['content']; ?></a></p>
           <?php else:?>
           	<a class="pink" href="javascript:void(0)" id="<?php echo $chatServer['dotey']['uid'].'|*|'.$chatServer['dotey']['nickname'].'|*|'.$chatServer['dotey']['rank'].'|*|'.$chatServer['dotey']['purviewrank'];?>"><?php echo $chatServer['dotey']['nickname'];?></a>对您说：<?php echo empty($chatServer['private_notice']['content'])?'您好，欢迎来到'.$chatServer['dotey']['nickname'].'的直播间。':$chatServer['private_notice']['content']; ?></p>
           <?php endif;?>
           
      </div>
</div><!--.chat-box-->
<div id='swfframe' style="width:0px;height:0px;"></div>
