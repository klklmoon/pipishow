<?php 
	$contrller = Yii::app()->getController();
	$staticPath = $contrller->pipiFrontPath;
?>
<!--[iflt IE 6]>
<styletype="text/css">
html{_text-overflow:ellipsis}
#gotopbtn{_position:absolute;_top:expression(eval(document.documentElement.scrollTop+ 400))}
</style>
<![endif]-->
<script type="text/javascript">
var is_ajax_user_attribute = false;
</script>
<script type="text/javascript" src="<?php echo $this->createUrl('archives/sensWord')?>"></script>
<script type="text/javascript">
	var faceList=<?php echo json_encode($faceList);?>;
</script>
<div id="SucMove" class="popbox">
	<div class="poph noline">
    	<a title="关闭" class="closed" onClick="$.mask.hide('SucMove');"></a>
    </div>
    <div class="popcon" id="popcon"></div>
    
 </div>
 <!--礼物数量-->
<div id="GiftCount" class="popbox">
	<div class="poph giftcount">
    	<a title="关闭" class="closed" onClick="$.mask.hide('GiftCount');"></a>
    </div>
    <div class="popcon giftnum">
    	<div class="clearfix giftnumtext">
        	<span class="fleft pink">礼物数量</span>
            <div class="fleft changenum"><input type="text" id="gift_num" value="1" onchange="Gift.changeEgg()" onblur="value=value.replace(/[^\d]/g,'');value=value>Gift.maxSendNum?Gift.maxSendNum:value;value=value<=0?1:value;" onkeyup="value=value.replace(/[^\d]/g,'');value=value>Gift.maxSendNum?Gift.maxSendNum:value;value=value<=0?1:value;"><span title="+1" class="add" onclick="Gift.addGiftNum('gift_num')"></span><span title="-1" class="reduce" onclick="Gift.reduceGiftNum('gift_num')"></span></div>
        </div>
        <p id="giftTip">赠送礼物需要<em class="pink" id="pipiegg"></em></p>
        <p><input class="surebtn" id="confimGift" type="button" onclick="Gift.confirmSendGift();" value="送给TA "></p>
    </div>
</div><!--#GiftCount-->
<!--你有新消息-->
<div class="yourinfo" id="yourinfo">
    <p class="yourinfo-h"><span>你有新消息！</span><em onclick="hidePrivateBox()"></em></p>
    <dl>
        <dt><img src="<?php echo $dotey['middle_avatar'];?>"></dt>
        <dd>
            <em class="pink ellipsis"><?php echo $dotey['nickname'];?></em>
            <p>刚刚私聊你了！</p>
            <p>请<a href="javascript:void(0)" onclick="$.User.loginController('register')"><img src="<?php echo $staticPath;?>/fontimg/common/smallregbtn.jpg"></a>查看</p>
        </dd>
    </dl>
</div><!--.yourinfo-->

<div class="popbox luckgiftbox" id="newUserAward">
    <div class="poph">
        <span>恭喜您成为皮皮乐天幸运用户</span>
        <a title="关闭" class="closed" onClick="$.mask.hide('newUserAward');"></a>
    </div>
    <div class="popcon">
        <ul class="luckgift-list">
            <li><a><img src="<?php echo $staticPath;?>/fontimg/common/luckgift1.jpg"><span>小礼品</span></a></li>
            <li><a><img src="<?php echo $staticPath;?>/fontimg/common/luckgift2.jpg"><span>首冲勋章</span></a></li>
            <li><a><img src="<?php echo $staticPath;?>/fontimg/common/luckgift3.jpg"><span>高级贴条</span></a></li>
            <li><a><img src="<?php echo $staticPath;?>/fontimg/common/luckgift4.jpg"><span>黄色VIP</span></a></li>
            <li><a><img src="<?php echo $staticPath;?>/fontimg/common/luckgift5.jpg"><span>二手奥拓</span></a></li>
            <li><a><img src="<?php echo $staticPath;?>/fontimg/common/luckgift6.jpg"><span>飞屏</span></a></li>
        </ul>
        <p class="gradline"><input class="surebtn" rel="<?php echo $this->createUrl('activities/firstchargegifts')?>" onclick="hideNewUserAward()" type="button" value="领&nbsp;&nbsp;&nbsp;&nbsp;取"></p>
    </div>
</div>

<div id="RunwayIntro" class="popbox runwaybox">
    <div class="poph">
        <span>跑道说明</span>
    </div>
    <div class="popcon starcon">
         <p>1.向主播送出“跑道礼物”，可以将礼物和祝语展示在“跑道”里，持续两小时。</p>
         <p>2.跑道在所有直播间里显示，同一时间跑道上只展示1条内容。</p>
         <p>3.当有跑道礼物在跑道里展示时，新跑道礼物会接续在前一个礼物跑完一遍后，再出现。</p>
         <p>4.当有跑道礼物在跑道里展示时，若后续跑道礼物的单笔送出总价高于或持平前者，则代替前者持续显示2小时。若单笔总价低于前者，则只展示一遍，然后持续展示前者。</p>
         <p class="mt10 runwaybtn"><input type="button" onclick="$.mask.hide('RunwayIntro')" value="确&nbsp;&nbsp;定" class="shiftbtn"></p>
    </div>
</div><!--#RunwayIntro-->
<div class="popbox" id="truckGift">
	<div class="poph">
	 	<span>选择跑道礼物</span>
        <a title="关闭" class="closed" onClick="$.mask.hide('truckGift');"></a>
    </div>
     <div class="popcon lenPopcon">
     	<p class="sendObj clearfix">
     		<label class="fleft">赠送对象</label>
     		<select class="fleft" id="truck_dotey_id">
     			<?php if(isset($archives['dotey_list'])):?>
     				<?php foreach($archives['dotey_list'] as $row):?>
     					<option value="<?php echo $row['uid'];?>"><?php echo $row['nickname'];?></option>
     				<?php endforeach;?>	
     			<?php endif;?>
     		</select>
        </p>
        <div class="runway-gift">
        	<?php 
	        	$giftService=new GiftService();
	        	$truckGift=$giftService->getFrontGiftList();
	        	$truckGiftList=array();
	        	foreach($truckGift as $row){
	        		if($giftService->hasBit(intval($row['gift_type']), GIFT_TYPE_TRUCK)){
	        			$truckGiftList[]=$row;
	        		}
	        	}
	        	
        	?>
        	<ul class="clearfix" id="truck-gift-list">
        		<?php if($truckGiftList):?>
        			<?php foreach($truckGiftList as $val):?>
        				<li onclick="Gift.selectTruckGift(this,<?php echo $val['gift_id'];?>,<?php echo $val['pipiegg'];?>)"><img src="<?php echo $giftService->getGiftUrl($val['image']);?>"><p style="text-align:center"><?php echo $val['zh_name'];?></p></li>
        			<?php endforeach;?>
        		<?php endif;?>
        	</ul>
        </div>
        <p class="runway-num clearfix">
            <label class="fleft">送出数量</label>
            <input class="fleft" type="text" id="truck-gift-num" value="1" onchange="Gift.changeTruckGift()" onblur="value=value.replace(/[^\d]/g,'');value=value>Gift.maxSendNum?Gift.maxSendNum:value;" onkeyup="value=value.replace(/[^\d]/g,'');value=value>Gift.maxSendNum?Gift.maxSendNum:value;">
            <span class="fright" id="truck_pipiegg">礼物总价：0皮蛋</span>
        </p>
        <p class="runway-tips pink" id="truck-tips"></p>
        <p>跑道礼物寄语（18字以内）</p>
        <p class="runway-text mt10"><input class="intext" type="text" id="truck_remark" onfocus="if(value=='加油，让我们一起飞！'){value=''}" onblur="if(value==''){this.value='加油，让我们一起飞！'}" value="加油，让我们一起飞！"></p>
        <p class="runwayok clearfix">
            <input type="button" value="确&nbsp;&nbsp;定" id="truck-confimGift" onclick="Gift.confirmSendTruckGift()" class="fleft shiftbtn">
            <input type="button" value="取&nbsp;&nbsp;消" class="shiftbtn" onclick="$.mask.hide('truckGift');">
        </p>
     </div>  
</div>  
<div id="GradBox" class="popbox"></div>
<?php
	if(isset($archives['background'])&&!empty($archives['background'])){
		$background=unserialize($archives['background']);
	}else{
		$archivesService=new ArchivesService();
		$defaultBg=$archivesService->getArchivesBackGround();
		$background=$defaultBg[0];
	}

	$background=empty($background)?array('top'=>'','big'=>'','bgcolor'=>''):$background;

	?>
<?php if(Yii::app()->user->id==$archives['uid']):?>
<!--直播预告框-->
<div id="LiveNotice" class="popbox">
	<div class="poph">
    	<span>直播预告</span>
    	<a title="关闭" class="closed" onClick="$.mask.hide('LiveNotice');"></a>
    </div>
    <div class="popcon">
    	<ul>
        	<li><label>开播时间</label><input class="intext" type="text" id="start_time" onClick="javascript:ShowCalendar(this.id,1)"></li>
            <li><label>直播主题</label><input class="intext" type="text" id="liveSubject"><input type="hidden" id="startType"/></li>
            <li><input class="surebtn" onclick="Show.liveNotice()" type="button" value="确&nbsp;&nbsp;&nbsp;&nbsp;定"></li>
        </ul>
    </div>
</div><!--#LiveNotice-->
<div id="CastIng" class="popbox">
	<div class="poph giftcount">
    	<a title="关闭" class="closed" onClick="$.mask.hide('CastIng');"></a>
    </div>
    <div class="popcon uselive"></div>
</div><!--#UseCast-->
<div id="TalkNotice" class="popbox">
		<div class="poph">
	    	<span>房间公告</span>
	    	<a title="关闭" class="closed" onClick="$.mask.hide('TalkNotice');"></a>
	    </div>
	    <div class="popcon">
	    	<ul class="talk-notice">
	    		<?php
	    			if(isset($archives['notice'])){
						$notice=unserialize($archives['notice']);
	    				$common_notice=empty($notice['content'])?'不超过80个汉字':$notice['content'];
	    				$common_url=empty($notice['url'])?'http://':$notice['url'];
					}else{
						$common_notice='不超过80个汉字';
						$common_url='http://';
					}
					if(isset($archives['private_notice'])){
						$pnotice=unserialize($archives['private_notice']);
						$private_notice=empty($pnotice['content'])?'不超过80个汉字':$pnotice['content'];
						$private_url=empty($pnotice['url'])?'http://':$pnotice['url'];
					}else{
						$private_notice='不超过80个汉字';
						$private_url='http://';
					}
	    		?>
	        	<li>
	            	<label>公聊窗口</label>
	            	<textarea id="commonNotice" onfocus="if(this.innerHTML=='不超过80个汉字'){this.innerHTML=''}" onblur="if(this.innerHTML==''){this.innerHTML='不超过80个汉字'}" onKeyUp="Show.textup('commonNotice')" onKeyDown="Show.textdown(event,'commonNotice')"><?php echo $common_notice;?></textarea>
	            	<input class="intext" type="text" value="<?php echo $common_url;?>" id="commonUrl">
	            </li>
	            <li>
	            	<label>私聊窗口</label>
	            	<textarea id="privateNotice" onfocus="if(this.innerHTML=='不超过80个汉字'){this.innerHTML=''}" onblur="if(this.innerHTML==''){this.innerHTML='不超过80个汉字'}" onKeyUp="Show.textup('privateNotice')" onKeyDown="Show.textdown(event,'privateNotice')"><?php echo $private_notice;?></textarea>
	            	<input class="intext" type="text" value="<?php echo $private_url;?>" id="privateUrl">
	            </li>
	            <li><input class="surebtn" type="button" onclick="Show.modifyNotice()" value="确&nbsp;&nbsp;&nbsp;&nbsp;定"></li>
	        </ul>
	    </div>
	</div>
	<!--发言设置-->
	<div id="SaySet" class="popbox">
		<div class="poph">
	    	<span>发言设置</span>
	    	<a title="关闭" class="closed" onClick="$.mask.hide('SaySet');"></a>
	    </div>
	    <div class="popcon">
	    	<ul class="speak-set">
	        	<li class="clearfix"><label class="fleft"><input name="tourist_set" <?php if($chat_set['tourist_set']==0){echo "checked";}?> type="checkbox">允许游客发言</label><label class="fleft"><input type="checkbox" <?php if($chat_set['global_set']==0){echo "checked";}?> name="global_set" >允许所有人发言</label></li>
	            <li><input class="surebtn" type="button" onclick="Show.chatSet()" value="确&nbsp;&nbsp;&nbsp;&nbsp;定"></li>
	        </ul>
	    </div>
	</div><!--#SaySet-->
	 <?php 
        $upload=new PipiFlashUpload();
        $upload->tmpFolder= 'dotey';
        $upload->realFolder = 'dotey';
        $upload->filePrefix = 'dotey_';
        $doteyCover=$upload->getSaveFile($archives['uid'],'small','display');
        
    ?>
	<!--节目封面-->
	<div id="Covers" class="popbox">
		<div class="poph">
	    	<span>节目封面</span>
	    	<a title="关闭" class="closed" onClick="$.mask.hide('Covers');"></a>
	    </div>
	    <div class="popcon"></div>
	</div><!--#Covers-->
	<iframe width="0" height="0" style="display:none;" id="coverframe" name="coverframe"></iframe>
	<!--转移观众-->
	<div id="MoveViewer" class="popbox">
		<div class="poph">
	    	<span>转移观众</span>
	    	<a title="关闭" class="closed" onClick="$.mask.hide('MoveViewer');"></a>
	    </div>
	    <div class="popcon">
	    	<ul>
	        	<li><label><em class="black">将我直播间里的观众转移到</em></label></li>
	        	<li class="newurl clearfix"><label class="fleft">http://show.pipi.cn/</label><input class="fleft" type="text" id="target_uid"></li>
	            <li><label>填写其他正在直播间短位地址ID。</label></li>
	            <li><input class="surebtn" onclick="Show.moveViewer()" type="button" value="确&nbsp;&nbsp;&nbsp;&nbsp;定"></li>
	        </ul>
	    </div>
	</div><!--#MoveViewer-->
<!--直播间背景设置1-->
<div id="BgSet" class="popbox">
	<div class="poph">
    	<span>直播间背景设置</span>
    	<a title="关闭" class="closed" onClick="$.mask.hide('BgSet');"></a>
    </div>
    <div class="popcon" id="BgSetTab">
        <p class="bgset bgset-hd clearfix"><label class="fleft  on"><input name="bgset"   checked type="radio">自定义背景</label><label class="fleft"><input name="bgset"  type="radio">系统背景</label></p>
        <div class="bgset-bd">
        <form id="back_form" method="post" action="<?php echo $this->createUrl('/dotey/uploadBack');?>" target="tarframe" enctype="multipart/form-data">
       		<ul class="cover">
       			<li><input type="file" id="backImg" name="backImg" ></li>
              	<li>支持JPG格式，上传大小不超过500kb</li>
                <li id="backUpload" style="display:none"><p class="uping"><img src="<?php echo $staticPath;?>/fontimg/common/uploading.jpg"></p></li>
                <!--<li><label><em class="black">顶部高度</em></label><input class="intext" type="text" name="paddtop" id="paddtop" value="0"></li>
                <li><label>高度单位像素(px)，限制0-50</label></li>  -->
                <li><input class="surebtn" type="button" onclick="Show.BgSet()" value="确&nbsp;&nbsp;&nbsp;&nbsp;定"></li>
            </ul>
            </form>
            <?php
            	$archviesService=new ArchivesService();
            	$defaultBg=$archviesService->getArchivesBackGround();
            ?>
            <ul class="bgpic-set  clearfix"></ul>
        </div>
    </div>
</div><!--#BgSet-->
<iframe width="0" height="0" style="display:none;" id="tarframe" name="tarframe"><li id="msg"></li></iframe>
<div id="OnlineHelp" class="popbox helpbox">
	<div class="poph">
		 <span>直播在线帮助</span>
		 <a title="关闭" class="closed" onClick="$.mask.hide('OnlineHelp');"></a>
	</div>
	<?php 
		$doteyService=new DoteyService();
		$doteyBase=$doteyService->getDoteyInfoByUid($archives['uid']);
		$doteyTutor=$doteyinfor=array();
		if(isset($doteyBase['tutor_uid'])){
			$userService=new UserService();
			$doteyinfor=$userService->getUserBasicByUids(array($doteyBase['tutor_uid']));
			$doteyTutor=$userService->getUserExtendByUids(array($doteyBase['tutor_uid']));
			$doteyinfor=array_pop($doteyinfor);
			$doteyTutor=array_pop($doteyTutor);	
	}
		
	?>
	<div class="popcon clearfix">
		 <dl class="helpcon">
            <dt>主播导师</dt>
            <dd class="qqlink">
            <?php if(isset($doteyTutor['qq'])):?>
            <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $doteyTutor['qq'];?>&site=qq&menu=yes"><img src="http://wpa.qq.com/pa?p=3:<?php echo $doteyTutor['qq'];?>:45" alt="<?php echo $doteyinfor['nickname'];?>" title="<?php echo $doteyinfor['nickname'];?>"></a>
            <?php endif;?>
            </dd>
            <dd>直播过程中遇到任何疑惑和问题，可以找自己的导师咨询了解。基础操作设置请先查看<a class="pink" href="<?php echo $this->createUrl('public/doteyHelp');?>" target="_blank">直播帮助</a></dd>
        </dl>
        <?php 
			$operateService=new OperateService();
			$kefu=$operateService->getAllKefu();
		?>
		<dl class="helpcon">
            <dt>技术支持</dt>
            <dd class="qqlink">
            <?php foreach($kefu as $row):?>
	          	<?php if($row['kefu_type']==KEFU_QQ_TEC_SUPPORT):?>
	            <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $row['contact_account'];?>&site=qq&menu=yes"><img src="http://wpa.qq.com/pa?p=3:<?php echo $row['contact_account'];?>:45" alt="<?php echo $row['contact_name'];?>" title="<?php echo $row['contact_name'];?>"></a>
	            <?php endif;?>
            <?php endforeach;?>
            <dd>在线时间：上午10:00-凌晨24:00<br>若遇到直播画面/声音问题请向技术人员求助。</dd>
        </dl>
        <dl class="helpcon">
            <dt>值班客服</dt>
            <dd class="qqlink">
            <?php foreach($kefu as $row):?>
	          	<?php if($row['kefu_type']==KEFU_QQ_WORK):?>
	            <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $row['contact_account'];?>&site=qq&menu=yes"><img src="http://wpa.qq.com/pa?p=3:<?php echo $row['contact_account'];?>:45" alt="<?php echo $row['contact_name'];?>" title="<?php echo $row['contact_name'];?>"></a>
	            <?php endif;?>
            <?php endforeach;?>
            </dd>
            <dd>在线时间：上午10:00-凌晨24:00<br>若遇到用户充值，有人恶意捣乱等问题请联系客服求助。</dd>
        </dl>
        <dl class="helpcon">
            <dt>意见反馈、投诉</dt>
            <dd><input class="shiftbtn" type="button" onclick="window.open('<?php echo $this->createUrl('public/suggest',array('type'=>4));?>')" value="提建议\投诉"></dd>
            <dd>对平台功能与工作人员服务有意见建议或投诉，请点击按钮，通过独立通道提交。</dd>
        </dl>
        <input class="surebtn" type="button" onclick="$.mask.hide('OnlineHelp');" value="关&nbsp;&nbsp;&nbsp;&nbsp;闭">
	</div>
</div>
<!--推荐给好友-->
<div id="ToFriend" class="popbox">
	<div class="poph">
    	<span>推荐给好友</span>
    	<a title="关闭" class="closed" onClick="$.mask.hide('ToFriend');"></a>
    </div>
    <div class="popcon">
    	<ul>
        	<li>您可以复制以下地址，发送给您的好友，邀请他们观看您的直播节目。</li>
        	<li class="tofriend-text"><textarea>推荐你看“<?php echo $archives['title'];?>”的视频互动直播节目直播间地址:<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/'.$archives['uid'];?></textarea></li>
            <li>IE浏览器可一键复制，使用其他浏览器，请手动复制</li>
            <li><input class="surebtn" type="button" onclick="Show.copyUrl('<?php echo $archives['title'];?>','<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/'.$archives['uid'];?>')" value="一键复制"></li>
        </ul>
    </div>
</div><!--#ToFriend-->
<?php endif;?>
<div class="reward airbox">
    <a href="javascript:void(0);" id="SendAir" class="send-air"><span>发布广播</span></a>
</div>
<div class="broadContent">
    <div class="broadList">
        <ul class="broadCastList">
        	<?php 
        		if($broadcastList){
					$faceService=new FaceService();
	        		foreach($broadcastList as $key=>$row){
            ?>
        		<li id="broadcast_<?php echo $key;?>" rel="<?php echo $row['timeout'];?>"><a target="_blank" href="/<?php echo $row['dotey_uid'];?>"><em class="pink"><?php echo $row['nickname'];?>（<?php echo $row['uid'];?>）：</em><?php echo $faceService->filterFace($row['content'],$row['isVip']);?>（<?php echo date('H:i',$row['time']);?>来自<em class="pink"><?php echo $row['title'];?></em>）</a></li>
            <?php 
					}
				}
		    ?>
        </ul>
    </div>
</div>
<div id="BroadCast" class="popbox">
    <div class="poph">
        <span>开播啦！</span>
        <a title="关闭" class="closed" onClick="$.mask.hide('BroadCast');"></a>
    </div>
    <div class="broad-con">
        <dl class="broad-list clearfix">
            <dt><img id="bg_msg_img" src="http://showimg.pipi.cn/default/avatar/avatar_default_small.png"><a href="#" id="bg_msg_cancel">取消关注</a></dt>
            <dd class="mid-list"><p class="pink" style="width:90px;" id="bg_msg_dnk">主播昵称</p><p>刚刚开始直播</p></dd>
            <dd><a href="#" class="lookBtn"><img src="<?php echo $staticPath?>/fontimg/common/lookBtn.png"></a></dd>
        </dl>
    </div>
</div><!--#BroadCast-->

<!--直播间引导-->
<div class="guideMask">
    <div class="guide1">
        <div class="guide1con">
            <a href="javascript:void(0);" class="newguidebtn"></a>
            <a href="javascript:void(0);" class="nottipbtn"></a>
        </div>
    </div><!--.guide1-->
    <div class="guide2">
        <div class="guide2con">
            <div class="guide2btn">
                <a href="javascript:void(0);" class="knowbtn"></a>
                <a href="javascript:void(0);" class="nottipbtn"></a>
            </div>
        </div>
    </div><!--.guide2-->
    <div class="guide3">
        <div class="guide3con">
            <a href="javascript:void(0);" class="knowbtn"></a>
            <a href="javascript:void(0);" class="nottipbtn"></a>
        </div>
    </div><!--.guide3-->
    <div class="guide4">
        <div class="guide4btn">
            <a href="javascript:void(0);" class="knowbtn"></a>
            <a href="javascript:void(0);" class="nottipbtn"></a>
        </div>
    </div><!--.guide4-->
    <div class="guide5">
        <div class="guide5con">
            <a href="javascript:void(0);" class="nowregbtn"></a>
        </div>
    </div><!--.guide5-->
</div>

<div id="BodyCon" class="bodycon">
	<?php if(Yii::app()->user->id==$archives['uid']):?>
	<div id="ControlBox" class="w1000 controlbox">
		<div id="ControlCon" class="control-con">
		<?php
		if(isset($archives['live_record'])){
			$liveSubject=$archives['live_record']['status']==1?'结 束 直 播':'开 始 直 播';
			$liveBotton=$archives['live_record']['status']==1?'Show.stopLive(true);':'Show.startLive(true);';
		}else{
			$liveSubject='开 始 直 播';
			$liveBotton='Show.startLive(true)';
		}
		?>
	    	<ul class="control-list clearfix">
	        	<li><a href="javascript:void(0)" onclick="Show.showLiveNotice(false);" title="直播预告">直播预告</a></li>
	        	<li><a href="javascript:void(0);" onClick="Show.showCover();" title="节目封面">节目封面</a></li>
	            <!-- <li><a href="#" title="离线录像">离线录像</a></li> -->
	            <li><a href="javascript:void(0);" onClick="$.mask.show('TalkNotice');" title="房间公告">房间公告</a></li>
	            <li><a href="javascript:void(0);" onClick="$.mask.show('SaySet');" title="发言设置">发言设置</a></li>
	            <!--  <li><a href="#" title="游戏设置">游戏设置</a></li>-->
	            <li><a href="javascript:void(0);" onClick="Show.showBgSet();" title="装扮设置">装扮设置</a></li>
	            <li><a href="javascript:void(0);" onClick="$.mask.show('MoveViewer');" title="转移观众">转移观众</a></li>
	            <li><a href="javascript:void(0);" onClick="$.mask.show('OnlineHelp');" title="主播帮助">主播帮助</a></li>
	            <li><a href="javascript:void(0)" onClick="$.mask.show('ToFriend')" title="推荐给好友">推荐给好友</a></li>
	        	<li class="end"><input class="shiftbtn" type="button" onclick="<?php echo $liveBotton;?>" id="modifyLive" value="<?php echo $liveSubject;?>"></li>
	        </ul>
	        
	        <div id="liveNoticeTips" class="point <?php if($archives['live_record']&&$archives['live_record']['status']!=2):?>none<?php endif;?>">请先填直播预告，然后开播</div>
	        <div id="coverTips" class="point  pot2 <?php if(empty($archives['live_record'])||($archives['live_record']&&$archives['live_record']['status']!=0||is_file($doteyCover))){echo "none";}?>">您还没有节目封面照，请上传</div>
	    </div><!--.control-con-->
	    <div id="ControlBtn" class="controlbtn posbtn">
	    		<?php
		    		$liveTimeClass='didnot';
		    		$liveTimeTitle='未开播：';
		    		$liveTime='00时00分';
		    		if(isset($archives['live_record'])){
		    			if($archives['live_record']['status']==1){
		    				$liveTimeClass='didgo';
		    				$liveTimeTitle='已播时间：';
		    				$liveTime=$archives['live_record']['duration']>0?date('H时i分',$archives['live_record']['duration']):'00:00:00';
		    			}
		    		}
	    		?>
	    		<p class="fleft time">
	    			<span class="explain <?php echo $liveTimeClass;?>"><?php echo $liveTimeTitle;?></span><i><?php echo $liveTime;?></i>
	    		</p>
	    		<p class="sate">收起控制栏</p>
	    		</div>
	</div><!--.cotrolbbox-->

	<?php endif;?>
	<div class="w1000 mt10 topdetail clearfix" <?php if(isset($archives['background']['top'])){echo 'style="margin-top:'.$background['top'].'px"';}?>>
		<div class="fleft topdetail-l clearfix">
	    	<div class="fleft headlink" id="TopHeader">
	    		<?php 
	    		$doteyAvatar=new PipiFlashUpload();
	    		$doteyAvatar->tmpFolder= 'dotey';
	    		$doteyAvatar->realFolder = 'dotey';
	    		$doteyAvatar->filePrefix = 'dotey_';
	    		$dotey_avatar=$doteyAvatar->getSaveFile($archives['uid'],'small','display');
	    		?>
	        	<a <?php if(Yii::app()->user->id==$archives['uid']):?>href="<?php echo $this->createUrl('account/main');?>"<?php endif;?> class="headpic" title="主播"><img src="<?php echo $dotey['middle_avatar'];?>"></a><?php if(Yii::app()->user->id==$archives['uid']):?><div class="changehead"><a href="<?php echo $this->createUrl('account/main');?>"><?php if(is_file($dotey_avatar)){ echo "修改头像";}else{ echo "上传头像";}?></a></div><?php endif;?>
	        </div>
	        <h1 class="fleft">
	        	<span class="fleft mr5"><?php echo $archives['title'];?></span>
	        	<?php if(!empty($dotey['famliy_medal']['enable'])):?>
	        	<div class="fleft patern-top paterns">
		        	<a href="<?php echo $dotey['famliy_medal']['enable']['url'];?>" target="_blank"><img src="<?php echo $dotey['famliy_medal']['enable']['medal']; ?>"></a>
		        	<?php if(!empty($dotey['famliy_medal']['have'])):?>
		        	<em></em>
	                <div class="paternlistbox">
	                	<ul class="paternlist clearfix">
	                		<?php foreach($dotey['famliy_medal']['have'] as $medal):?>
                            <li><a href="<?php echo $medal['url'];?>" target="_blank"><img src="<?php echo $medal['medal'];?>"></a></li>
                            <?php endforeach;?>
	                	</ul>
	                </div>
	                <?php endif;?>
                </div>
	        	<?php endif;?>
	        </h1>
	        <p class="fleft head-text ellipsis" id="head_subject_title"><?php if(isset($archives['live_record']['status'])&&($archives['live_record']['status']==0||$archives['live_record']['status']==1)):?>[<?php echo date('H:i',$archives['live_record']['start_time']);?>开播] <?php echo isset($archives['live_record']['sub_title'])?$archives['live_record']['sub_title']:'';?><?php endif;?></p>
	        
	        <div class="fleft detail-btm clearfix">
	        	<p class="sharetip" style="display:none;">
                   <span>关注主播，及时收到她的开播提醒</span>
                   <a href="javascript:void(0);" class="close"></a>
               	</p>
	        <?php
	        	$isAttentTion=false;
		        if($this->isLogin){
		        	$weiboService = new WeiboService();
		        	$isAttentTion = $weiboService->isAttentionDotey($dotey['uid'],Yii::app()->user->id);
		        	$jsMethod=$isAttentTion?'cacnelAttentionUser':'attentionUser';
				}else{
					$jsMethod='attentionUser';
				}
		       
	        ?>  
	        
	        	<p id="attentionUser" class="fleft share"><a href="javascript:void(0)" onclick="$.User.<?php echo $jsMethod;?>(<?php echo $dotey['uid'];?>,this,'live');"><?php if($isAttentTion){ echo '已关注';}else{ echo '+关注';}?></a><span><?php echo $weibo['fans'];?></span></p>
	        	<div id="bdshare" class="bdshare_t bds_tools get-codes-bdshare">
	                <a class="bds_qzone"></a>
	                <a class="bds_tsina"></a>
	                <a class="bds_tqq"></a>
	                <span class="bds_more">更多</span>
	                <a class="shareCount"></a>
                </div>
                <script type="text/javascript" id="bdshare_js" data="type=tools&amp;uid=553717" ></script>
                <script type="text/javascript" id="bdshell_js"></script>
                <script type="text/javascript">
                	var bds_config = {
    					'bdComment':'大家好，我正在皮皮乐天上观看<?php echo $dotey['nickname'];?>的精彩直播，邀请大家强势围观！',
    					'bdText':'大家好，我正在皮皮乐天上观看<?php echo $dotey['nickname'];?>的精彩直播，邀请大家强势围观！',	
    					'searchPic':1
        			};
                	document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + Math.ceil(new Date()/3600000)
                </script>
	        	<!-- 主播勋章 -->
	        	<?php
	        		if(isset($doteyBirthdayInfo['birthday'])):
		        		$birthdayTimeStampStart=strtotime($doteyBirthdayInfo['year']."-".$doteyBirthdayInfo['month']."-".$doteyBirthdayInfo['sday']." 00:00:00")-86400*3;
		        		$birthdayTimeStampEnd=strtotime($doteyBirthdayInfo['year']."-".$doteyBirthdayInfo['month']."-".$doteyBirthdayInfo['sday']." 23:59:59");
		        		$showbirthdayCake= time()>=$birthdayTimeStampStart && time()<=$birthdayTimeStampEnd;
	        			if($showbirthdayCake):
	        	?>
	        	
	        	<div class="rater">
	        		<img src="<?php echo $staticPath?>/fontimg/activities/happybirthday/birthdaycake_big.jpg">
	        		<p><span><?php echo $doteyBirthdayInfo['month']."月".$doteyBirthdayInfo['sday']."日" ?></span><span>生日快乐</span></p>
	        	</div>
	        	
	        	<?php
	        			endif; 
	        		endif;
	        	?>
	        	<?php 
	        	if(isset($doteyBirthdayInfo['princess_medal']) && $doteyBirthdayInfo['princess_medal']==1 && time()<($doteyBirthdayInfo['medal_time']+HappyBirthdayService::MEDAL_PERIOD_OF_VALIDITY)):
	        	?>
	        	<div class="rater"><img src="<?php echo $staticPath?>/fontimg/activities/happybirthday/princessmedal.png"></div>
	        	<?php endif;?>
	        </div>
	    </div><!--.topdetail-l-->
	    <div class="fleft topdetail-r" id="giftNotice">
	    <?php foreach($topGiftList as $row):?>
	    	<p><a href="<?php $this->getTargetHref('/'.$row['d_uid']);?>" target="<?php echo $this->target;?>"><?php echo $row['time'];?><em class="name" title="<?php echo $row['nickname'];?>"><?php echo $this->cutstr($row['nickname'],8);?></em>送给<em class="name" title="<?php echo $row['d_nickname'];?>"><?php echo $this->cutstr($row['d_nickname'],8);?></em><em class="pink"><?php echo $row['gift_num'];?></em>个<?php echo $row['gift_name'];?></a><Img src="<?php echo $giftService->getGiftUrl($row['picture']);?>"></p>
	     <?php endforeach;?>
	    </div>
	</div><!--.topdetail-->
	<?php 
            $truckGiftService=new TruckGiftService();
            $truckGiftRecord=$truckGiftService->getTruckGiftRecord();
        ?>
	<div class="w1000 runway-box">
		<div class="runway-btn clearfix <?php if($truckGiftRecord):?>crun<?php endif;?>">
			<a href="javascript:void(0);" <?php if($truckGiftRecord):?>style="top:13px;"<?php endif;?> id="GowayBtn" onclick="Gift.showTruckGift()" class="goway" title="我要上跑道">我要上跑道</a>
            <a href="javascript:void(0);" <?php if($truckGiftRecord):?>style="top:13px;"<?php endif;?> onclick="$.mask.show('RunwayIntro')" class="help" title="跑道规则说明">&nbsp;？</a>
       </div><!--.runway-btn-->
        
        <div class="runway-con" <?php if($truckGiftRecord):?>style="display:block;"<?php endif;?>>
            <div class="runway-list" id="truck-list">
            	<ul id="truck-content">
            		<?php if($truckGiftRecord):?>
            			<li rel="<?php echo $truckGiftRecord['replace'];?>" ><a target="_blank" href="<?php echo $this->getTargetHref('/'.$truckGiftRecord['to_uid']); ?>"><img src="<?php echo $giftService->getGiftUrl($truckGiftRecord['picture']);?>"><em class="pink"><?php echo $truckGiftRecord['nickname'];?></em> 送给 <em class="pink"><?php echo $truckGiftRecord['to_nickname'];?></em><?php echo $truckGiftRecord['num'];?>个<?php echo $truckGiftRecord['zh_description'];?>：<em class="pink"><?php echo $truckGiftRecord['remark'];?></em>
            				<?php 
            					$num=$truckGiftRecord['num']>10?10:$truckGiftRecord['num'];
            					for($i=0;$i<$num;$i++){
            						echo '<img src="'.$giftService->getGiftUrl($truckGiftRecord['picture']).'">';
            					}
            				?>
            			</a></li>
            		<?php endif;?>
            	</ul><!--.runway-list-->
            </div>
         </div>
    </div><!--.runway-box-->
     
	<div class="w1000 mt10 clearfix">
		<div class="fleft livingbox">
		<?php if($livingArchives):?>
		 	<div class="qipao" id="qipao">
                <div class="qipao-list">
                <?php foreach($livingArchives as $row):?>
                    <a href="<?php echo $this->getTargetHref('/'.$row['uid']); ?>" target="_blank" onclick="javascript:window.open('<?php echo $this->getTargetHref('/'.$row['uid']); ?>','newwindow')" title="<?php echo $row['title'];?>">
                        <span class="pics"><img src="<?php echo $row['display_small'];?>"></span>
                        <span class="name"><em class="lvlo lvlo-<?php echo $row['rank'];?>"></em><em class="ellipsis"><?php echo $row['title'];?></em></span>
                        <i><?php echo $row['online'];?>人在观看</i>
                    </a>
                  <?php endforeach;?>  
                 </div>
                <a href="javascript:void(0);" class="closeqipao" onclick="$.mask.hide('qipao')">关&nbsp;&nbsp;闭</a>
           </div><!--.qipao-->
           <?php endif;?>
		<div class="flash-m" id="playFlash"></div>
		<?php 
			$guardService=new GuardAngelService();
			$doteyGuard=$guardService->lookDoteyRank($archives['uid']);
		?>
		<?php 
			if($doteyGuard['flag']==1):
		?>
		<?php if($dotey['uid']==Yii::app()->user->id):?>
		<div class="guar" id="dotey_guard"><?php echo $doteyGuard['message']['star'];?></div>
		<?php else:?>
		<a href="javascript:Chat.getGuard();"><div class="guar" id="dotey_guard"><?php echo $doteyGuard['message']['star'];?></div></a>
		<?php endif;?>
		<?php endif;?>
		<?php
		if($dotey['uid']==Yii::app()->user->id){
			$model='record';
		}else{
			$model='live';
		}
		$this->widget('lib.widgets.archives.FlashWidget',
			array(
				  'width'=>460,
				  'height'=>345,
				  'archivesId'=>$archives['archives_id'],
				  'model'=>$model,
				  'giftStar'	=>$giftStarInfo,
				  'doteyHalloween'=>$doteyHalloweenInfo	
				)
		);
		?>
		<?php
		$this->widget('lib.widgets.archives.GiftWidget',
			array('position'=>0,
				  'dotey'=>array('uid'=>$dotey['uid'],'nickname'=>$dotey['nickname'],'rank'=>$dotey['rank'],'purviewrank'=>$dotey['purviewrank'],'list'=>$archives['dotey_list']),

			)
		);
		?>
		</div><!--.livingbox-->
		<div class="fright chatbox clearfix">
		    <div class="fleft chat-l">
				<div class="chat-menu">
		                <ul class="clearfix">
		                    <li class="first" id="chatTab"><a class="giftover" href="javascript:void(0);" title="聊天"><i class="chaticon"></i><span>聊天</span></a></li>
		                    <li id="giftTab"><a href="javascript:void(0);" id="giftList" title="礼物"><i class="gifticon"></i><span>礼物</span></a></li>
		                    <li id="songRecord" class="last"><a href="javascript:void(0);" title="点歌"><i class="songicon"></i><span>点歌</span></a></li>
		                    <!--<li><a href="javascript:void(0);" title="语音"><i class="vocieicon"></i><span>语音</span></a></li>-->
		                    <?php if($this->isPipiDomain):?>
		                    <!-- <li class="last"><a href="javascript:void(0);" title="游戏"><i class="gameicon"></i><span>游戏</span></a></li> -->
		                    <?php endif;?>
		                </ul>
		          </div><!--.chat-menu-->
		          <?php
		          $this->widget('lib.widgets.archives.ChatWidget',
						array('width'=>450,
							'heigth'=>350,
							'archives_id'=>$archives['archives_id'],
							'live_status'=>isset($archives['live_record']['status'])?$archives['live_record']['status']:0,
							'crown'=>$crown,
							'socketIp'=>$archives['chat_server']['domain'],
							'policyPort'=>$archives['chat_server']['policy_port'],
							'port'=>$archives['chat_server']['data_port'],
							'notice'=>$archives['notice'],
							'private_notice'=>$archives['private_notice'],
							'dotey'=>array('uid'=>$dotey['uid'],'nickname'=>$dotey['nickname'],'rank'=>$dotey['rank'],'purviewrank'=>3,'list'=>$archives['dotey_list']),
							'userList'=>$userList,
							'chatSet'=>$chat_set,	
							'doteyProfile'=>$dotey['doteyProfile']
						)
					);
					?>
		          <div class="gifts-box chat-con">
	                    <div class="geili"><em class="geili-icon">本场最给力</em></div>
	                    <div class="gift-msg">
	                        <ul>
	                            <li class="first clearfix">
	                                <span class="fansname">粉丝</span>
	                                <span class="giftname">礼物</span>
	                                <div><span class="giftnum">数量</span></div>
	                            </li>
	                            <?php foreach($giftList as $row):?>
	                            <li class="clearfix"><span class="fansname ellipsis"><?php echo $row['nickname'];?></span><span class="giftname ellipsis"><img src="<?php echo $giftService->getGiftUrl($row['picture']);?>"><?php echo $row['gift_name'];?></span><div><span class="giftnum pink"><?php echo $row['gift_num'];?></span></div></li>
	                            <?php endforeach;?>
	                        </ul>
	                    </div><!--.gift-msg-->
	                </div><!--.gifts-box-->
	                <?php if(Yii::app()->user->id==$dotey['uid']):?>
	                <div id="LotAddBox" class="popbox">
						<div class="poph">
					    	<span>批量添加歌曲</span>
					    	<a title="关闭" class="closed" onClick="$.mask.hide('LotAddBox');"></a>
					    </div>
					    <div class="popcon lotaddcon">
					    	<ul class="addsong-list clearfix">
					        	<li><span class="name">歌名</span><span class="song">原唱</span></li>
					            <li><span class="name">1.<input type="text" name="song_name" class="intext"></span><span class="song"><input type="text" name="song_singer" class="intext"></span></li>
					            <li><span class="name">2.<input type="text" name="song_name" class="intext"></span><span class="song"><input type="text" name="song_singer" class="intext"></span></li>
					            <li><span class="name">3.<input type="text" name="song_name" class="intext"></span><span class="song"><input type="text" name="song_singer" class="intext"></span></li>
					            <li><span class="name">4.<input type="text" name="song_name" class="intext"></span><span class="song"><input type="text" name="song_singer" class="intext"></span></li>
					            <li><span class="name">5.<input type="text" name="song_name" class="intext"></span><span class="song"><input type="text" name="song_singer" class="intext"></span></li>
					            <li><a class="addbtn" href="javascript:Chat.confirmBatchSong()" title="添加">添加</a></li>
					        </ul>
					    </div>
					</div><!--#LotAddBox-->
					<?php endif;?>
					<div id="AlreadySong" class="popbox musicbox" style="z-index:150;">
						<div class="poph">
					    	<span>已演唱歌单</span>
					    	<a title="关闭" class="closed" onClick="$.mask.hide('AlreadySong');"></a>
					    </div>
					    <div class="musiccon">
					    	<ul class="mymusic">
					        	<li class="first clearfix"><span class="time">排序</span><span class="musicname">歌曲</span><span class="songname">点歌粉丝</span></li>
					         </ul>
					    </div>
					</div>
	                <div id="MyMusic" class="popbox musicbox">
						<div class="poph">
					    	<span><?php echo $dotey['nickname'];?>的歌单</span>
					    	<a title="关闭" class="closed" onClick="$.mask.hide('MyMusic');"></a>
					    </div>
					    <div class="musiccon">
					    	<ul class="mymusic">
					        	<li class="first clearfix"><span class="time">时间</span><span class="musicname">歌名</span><span class="songname">原唱</span><span class="control">操作</span></li>
					        </ul>
					        <div class="control-music clearfix">
					        	<a class="fleft songbtn" href="javascript:void(0)" onclick="Chat.alreadySong()" title="已演唱">已演唱</a>
					            <ul class="fright songpage"></ul>
					            <div class="fleft song-text">
					            	<label class="fleft addsong"><?php if(Yii::app()->user->id!=$dotey['uid']):?>我要点播<?php else:?>添加歌曲<?php endif;?><input type="text" class="intext" id="song_name"></label>
					                <label class="fleft addsonger">原唱<input type="text" class="intext" id="song_singer"></label>
					                <?php if(Yii::app()->user->id!=$dotey['uid']):?>
					                	<a class="fleft addbtn" href="javascript:void(0)" onclick="Chat.userDefinedSong()" title="点播">点播</a>
					                <?php else:?>
					                	<a class="fleft addbtn" href="javascript:Chat.addSong()" title="添加">添加</a>
					              		<a class="fright lotaddbtn" href="javascript:Chat.batchSong()" title="批量添加">批量添加</a>
					              	<?php endif;?>
					            </div>
					        </div><!--.control-music -->
					        <div class="music-explain">
					            <p>点歌说明：</p>
					            <p>1、您可以按主播的歌单点歌，如歌单中没有您要的点的歌，请联系主播。</p>
					            <p>2、每首1000个皮蛋，主播接受点歌后收取。</p>
					        </div><!--.music-explain-->
					    </div>
					</div><!--#MyMusic-->
		          <div class="song-box chat-con">
	                    <div class="song-btn clearfix">
	                    	<?php
	                    		$noSongClass=($allowSong==2)?'nosong':'';
	                    	?>
	                        <a href="<?php if(Yii::app()->user->id==$archives['uid']):?>javascript:Show.allowSong()<?php else:?>javascript:void(0)<?php endif;?>"  id="allowSong" class="banbtn <?php echo $noSongClass;?>"></a>
	                        <?php if(Yii::app()->user->id==$archives['uid']):?>
	                        <a href="javascript:Chat.DoteySongList(1)" class="chosebtn">管理歌曲</a>
	                        <?php else:?>
	                        <a href="javascript:Chat.DoteySongList(1)" class="chosebtn">我要点歌</a>
	                       <?php endif;?>
	                    </div>
	                    <div class="song-list">
	                        <dl>
	                            <dt class="clearfix"><span class="time">时间</span><span class="name">歌名</span><span class="faner">点歌粉丝</span><span class="control">操作</span></dt>
	                        </dl>
	                    </div>
	                </div><!--.song-box-->
		          <!--
		          <div class="voice-box chat-con">  语音</div>
		          --><!--.voice-box-->
		          
		          <div class="game-box chat-con">
		          		<div class="hd">
	                        <ul>
	                            <li class="on">小游戏</li>
	                            <!--<li id="diceGame">骰子游戏</li>  -->
	                        </ul>
	                    </div>
	                    <div class="bd" >
	                    	 <ul class="game-list gameListBoxCls clearfix"></ul>
	                    	 <!--<ul class="saizi-list" id="diceRecord"></ul>  -->
	                    </div>	
	              </div>
	              <!--.game-box-->
		    </div><!--.chat-l-->
		    <div class="fright chat-r">
	        	<div class="check"><input class="fright" id="giftEffectSet" checked type="checkbox"  onclick="Gift.giftEffect(this)"><label class="fright">动画效果</label></div>
	         	 <?php
					$this->widget('lib.widgets.archives.UserListWidget',
						array('archives_id'=>$archives['archives_id'],
							'userList'=>true,
						)
					);
					?>
	         </div>
		    <div class="fleft chat-b clearfix">
		         <span class="uptext"><input id="ChatObj" value="所有人" type="text" value=""><a href="javascript:void(0);" class="topub"></a></span>
		         <label class="fleft"><input type="checkbox" id="privateSet">私聊</label>
		         <div class="face">
                     <em id="FaceGood" class="face-good"></em>
                     <div id="FaceBox" class="face-box">
                    	<div class="face-hd">
                        	<ul>
                            	<li>普通</li>
                                <li>VIP</li>
                                <li class="end">贵族</li>
                            </ul>
                        </div>
                        <div class="face-bd">
                        	<div class="face-con plain" id="FaceGood_common"></div>
                            <div class="face-con vip" id="FaceGood_vip"><p>您还不是尊贵的VIP用户，马上去<a href="<?php echo $this->getTargetHref($this->createUrl('shop/gift'));?>" class="pink" target="_blank">商城</a>购买吧</p> </div>
                            <div class="face-con">
                            	<p>贵族表情即将闪亮登场<br><a href="#" class="pink">敬请期待</a></p>
                            </div>
                        </div>
                      </div>
                 </div>
		         <span class="text chattext"><input type="text" id="msg_input"></span>
		         <a title="发言" class="subbtn submitbtn" id="sendChatBotton" href="javascript:void(0);" onclick="Chat.sendChat()"></a>
		         <a title="飞屏" class="subbtn flybtn" onclick="Chat.sendFlyscreen()" href="javascript:void(0);"></a>
		     	 <div class="chatname ellipsis">
                	<ul>
                		<li><a href="javascript:void(0)" class="ellipsis" rel="0">所有人</a></li>
                		<?php if(isset($archives['dotey_list'])):?>
                			<?php foreach($archives['dotey_list'] as $row):?>
                			<li><a href="javascript:void(0)" class="ellipsis" rel="<?php echo $row['uid']?>"><?php echo $row['nickname']?></a></li>
                			<?php endforeach;?>
                		<?php else:?>
                       		<li><a href="javascript:void(0)" class="ellipsis" rel="<?php echo $dotey['uid']?>"><?php echo $dotey['nickname']?></a></li>
                    	<?php endif;?>
                    </ul>
                  </div>
                  <div class="firstcalltip">
                    <div class="calltipcon">随便说些，陪主播聊聊天吧</div>
                    <a href="javascript:void(0);" class="close"></a>
                  </div>
             </div><!--.chat-b-->
		</div><!--.chatbox-->
		 <div class="fleft anchorinfo">
	    	<div class="anchor-detail">
	                <dl>
	                    <dt>
	                        <div class="fleft headlink">
	                            <a title="主播" class="headpic"><img src="<?php echo $dotey['middle_avatar'];?>"></a>
	                        </div>
	                    </dt>
	                    <dd class="name"><?php echo $dotey['nickname'];?></dd>
	                     <dd class="level clearfix">
	                     	<p class="fleft">等级：<em id="doteyLevel" class="lvlo lvlo-<?php echo isset($dotey['rank'])?$dotey['rank']:0;?>"></em></p>
	                     	<div class="fleft mt5 ml20 process-box" id="doteyProcess">
                            	<span class="process"></span>
                                <span class="rate-con clearfix"><em class="now-rate"><?php echo $dotey['charm']-$dotey['cuch'];?></em><em>/</em><em class="total-rate"><?php echo $dotey['nxch']-$dotey['cuch'];?></em></span>
                            </div>
	                     </dd>
	                    <dd class="rank"><?php if($charmRank['today']>0):?>今日魅力榜第<em class="pink"><?php echo $charmRank['today'];?></em>名<?php else:?>今日魅力榜暂无排名<?php endif;?>&nbsp;&nbsp;<?php if($charmRank['week']>0):?>本周魅力榜第<em class="pink"><?php echo $charmRank['week'];?></em>名<?php else:?>本周魅力榜暂无排名<?php endif;?></dd>
	                </dl>
	            <div class="comefrom clearfix">
	            	<?php
	            		$birthday=$dotey['birthday']>0?date('Y')-date('Y',$dotey['birthday']):'未知';
	            	?>
	            	<span class="age"><em class="black">年龄：</em><?php echo $birthday;?></span>
	                <span class="ads"><em class="black">来自：</em><?php echo empty($dotey['city'])?'未知':$dotey['city'];?></span>
	                <span class="job"><em class="black">职业：</em><?php echo empty($dotey['profession'])?'未知':$dotey['profession'];?></span>
	                <?php
	                	$br = array("\n\r"=>"<br/>","\n"=>"<br/>","\r"=>"<br/>");
	                ?>
	                <p class="recommend"><em class="black">介绍：</em><?php echo empty($dotey['description'])?'这家伙很懒，什么也没有留下':strtr($dotey['description'],$br);?></p>
	        	</div><!--.comefrom-->
	        </div><!--.anchor-detail-->
	        <div id="CharmBox" class="charm-box">
	        	<ul>
	            	<?php foreach($archives_dy_msg as $row):?>
	                <li class="clearfix">
	                	<div class="small-head"><img src="<?php echo $dotey['small_avatar'];?>"></div>
	                    <div class="charm-con">
	                    <?php if($row['type']=='upgrade'):?>
	                    	<p class="charm-text">魅力等级升至<em class="lvlo lvlo-<?php echo $row['rank'];?>"></em></p>
	                    	<p class="time"><span>
	                    				<?php 
	                    					if(isset($row['new_time'])){
												if($row['new_time']>strtotime(date('Y-m-d 23:59:59',strtotime("-1 day")))){
													echo date('H:i',$row['new_time']);
												}elseif($row['new_time']>=strtotime(date('Y-m-d 00:00:00',strtotime("-1 day")))&&$row['new_time']<=strtotime(date('Y-m-d 23:59:59',strtotime("-1 day")))){
	                    							echo '昨日'.date('H:i',$row['new_time']);
	                    						}else{
	                    							echo date('Y年m月d日 H:i:s',$row['new_time']);
	                    						}
	                    					}else{
	                    						echo $row['time'];
	                    					}
                    					?>
	                    					</span></p>
	                    <?php elseif($row['type']=='gift'):?>
	                    	<p class="charm-text"><?php echo $row['d_nickname'];?>&nbsp;收到了&nbsp;<?php echo $row['nickname'];?>&nbsp;送来的&nbsp;<em class="pink"><?php echo $row['gift_num'];?></em>&nbsp;个 <?php echo $row['gift_name'];?><img src="<?php echo $giftService->getGiftUrl($row['picture']);?>"></p>
	                    	<p class="time"><span>
	                    					<?php 
		                    					if(isset($row['new_time'])){
													if($row['new_time']>strtotime(date('Y-m-d 23:59:59',strtotime("-1 day")))){
														echo date('H:i',$row['new_time']);
													}elseif($row['new_time']>=strtotime(date('Y-m-d 00:00:00',strtotime("-1 day")))&&$row['new_time']<=strtotime(date('Y-m-d 23:59:59',strtotime("-1 day")))){
		                    							echo '昨日'.date('H:i',$row['new_time']);
		                    						}else{
		                    							echo date('Y年m月d日 H:i:s',$row['new_time']);
		                    						}
		                    					}else{
		                    						echo $row['time'];
		                    					}
	                    					?>
	                   						</span></p>
	                    <?php endif;?>
	                    </div>
	                </li>
	                <?php endforeach;?>
	             </ul>
	            <div id="DownBtn" class="downbtn">查看更多动态</div>
	        </div><!--.charm-box-->
	    </div><!--.anchorinfo-->
	    <?php if($listAds):?>
	    <div class="fright mt10 rmid-ad">
	    <div class="hd">
	    	<ul>
	    		<?php for($i=1;$i<=count($listAds);$i++):?>
	    		<li><?php echo $i;?></li>
                <?php endfor;?>
             </ul>
         </div>
         <div class="bd">
         	<ul>
         		<?php foreach($listAds as $row):?>
         		<li><a href="<?php $this->getTargetHref($row['textlink']);?>" target="_blank" title="<?php echo $row['subject'];?>"><img src="<?php echo $row['src'];?>"></a></li>
         		<?php endforeach;?>	
         	</ul>
         </div>  
         </div>     
	    <?php endif;?>
	    <div class="fright fanchart" id="dedication">
	    	<div class="fanchar-menu" id="archives_dedication">
	        	<ul class="clearfix">
	            	<li class="fansover">本场粉丝榜</li>
	                <li>本周粉丝榜</li>
	            </ul>
	        </div>
	    	<div class="fans-con" id="dedication_list">
	        	<ul class="clearfix">
	        		<?php $i=1;?>
	        		<?php foreach($archives_dedication as $key=>$row):?>
	            	<li class="clearfix">
	                	<em class="fleft order <?php if($i==1):?>top1<?php elseif($i==2||$i==3):?>top2<?php endif;?>"><?php echo $key+1;?></em>
	                    <p class="name"><em class="lvlr lvlr-<?php echo $row['rank'];?>"></em><span><?php echo $row['nickname'];?></span></p>
	                    <div class="convalue">
	                    	<em class="pink"><?php echo $row['dedication'];?></em>
	                        <p>贡献值</p>
	                    </div>
	                </li>
	                <?php $i++;?>
	                <?php  endforeach;?>
	            </ul>
	            <ul class="clearfix" style="display:none">
	        		<?php $i=1;?>
	        		<?php foreach($week_dedication as $key=>$row):?>
	            	<li class="clearfix">
	                	<em class="fleft order <?php if($i==1):?>top1<?php elseif($i==2||$i==3):?>top2<?php endif;?>"><?php echo $key+1;?></em>
	                    <p class="name"><em class="lvlr lvlr-<?php echo $row['rank'];?>"></em><span><?php echo $row['nickname'];?></span></p>
	                    <div class="convalue">
	                    	<em class="pink"><?php echo $row['dedication'];?></em>
	                        <p>贡献值</p>
	                    </div>
	                </li>
	                <?php $i++;?>
	                <?php  endforeach;?>
	            </ul>
	        </div>
	    </div><!--.fanchart-->
	    <div class="fright fanchart" id="friendly">
	    	<div class="fanchar-menu" id="archives_friendly">
	        	<ul class="clearfix">
	            	<li class="fansover">本场情谊榜</li>
	                <li>本周情谊榜</li>
	            </ul>
	        </div>
	    	<div class="fans-con" id="friendly_list">
	        	<ul class="clearfix">
	        		<?php $i=1;?>
	        		<?php foreach($archives_friendly as $key=>$row):?>
	            	<li class="clearfix">
	                	<em class="fleft order <?php if($i==1):?>top1<?php elseif($i==2||$i==3):?>top2<?php endif;?>"><?php echo $key+1;?></em>
	                    <p class="name"><em class="lvlr lvlr-<?php echo $row['rank'];?>"></em><span><?php echo $row['nickname'];?></span></p>
	                    <div class="convalue">
	                    	<em class="pink"><?php echo $row['dedication'];?></em>
	                        <p>贡献值</p>
	                    </div>
	                </li>
	                <?php $i++;?>
	                <?php  endforeach;?>
	            </ul>
	            <ul class="clearfix" style="display:none">
	        		<?php $i=1;?>
	        		<?php foreach($week_archives_friendly as $key=>$row):?>
	            	<li class="clearfix">
	                	<em class="fleft order <?php if($i==1):?>top1<?php elseif($i==2||$i==3):?>top2<?php endif;?>"><?php echo $key+1;?></em>
	                    <p class="name"><em class="lvlr lvlr-<?php echo $row['rank'];?>"></em><span><?php echo $row['nickname'];?></span></p>
	                    <div class="convalue">
	                    	<em class="pink"><?php echo $row['dedication'];?></em>
	                        <p>贡献值</p>
	                    </div>
	                </li>
	                <?php $i++;?>
	                <?php  endforeach;?>
	            </ul>
	        </div>
	    </div><!--.fanchart-->
	</div><!--.w1000-->

<!--开播啦-->

<?php 
    $broadcastService=new BroadcastService();
    $broadConfig=$broadcastService->getBroadcastSetup();
?>
<div class="popbox" id="AirBox">
    <div class="poph noline">
        <a id="AirClose" class="closed" title="关闭"></a>
    </div>
    <p class="clearfix"><span class="chartext" id="broadCastInfo">还能输入<em class="pink">50</em>个字</span><span class="agenum">价格：<?php echo $broadConfig['price'];?> 皮蛋</span></p>
    <textarea class="con" id="broadCastContent"></textarea>
    
    <div class="faceBox-btn clearfix">
    	<div class="fleft face">
    		<em class="face-good faceicon" id="broadCastFace" onclick="Ubb.showFace('broadCastFace','broadCastContent')"></em>
    		<div class="face-box" id="FaceBox">
    			<div class="face-hd">
	                 <ul>
	                  <li class="on">普通</li>
	                  <li>VIP</li>
	                  <li class="end">贵族</li>
	                  </ul>
                 </div>
                 <div class="face-bd">
                 	 <div class="face-con plain" id="broadCastFace_common" style="display: block;"></div>
                 	  <div class="face-con vip" id="broadCastFace_vip" style="display: none;">
                        <p>您还不是尊贵的VIP用户，马上去<a class="pink" href="<?php echo $this->getTargetHref($this->createUrl('shop/gift'));?>" target="_blank">商城</a>购买吧</p>
                    </div>
                    <div class="face-con" style="display: none;">
                        <p>贵族表情即将闪亮登场<br><a class="pink" href="#">敬请期待</a></p>
                    </div>
                 </div>
             </div>
         </div>
         <input type="submit" disabled="disabled" value="发&nbsp;&nbsp;布" <?php if(isset($broadConfig['power'])&&$broadConfig['power']==1):?> onclick="Chat.sendBroadcast()"<?php endif;?> class="fright shiftbtn  shifted">
    </div>	
</div>

	
<script>
$(function(){
	var game_host = "<?php echo Yii::app()->params['letian_game']['host']?>";
	ChangeBg('<?php echo Yii::app()->params['images_server']['url'].'/background/'.$background['big']?>','<?php echo $background['bgcolor']?>');
	$.User.rankProgress('doteyProcess',<?php echo $dotey['nxch']?>,<?php echo isset($dotey['charm'])?$dotey['charm']:0?>,<?php echo isset($dotey['cuch'])?$dotey['cuch']:0?>);
	/* 游戏列表
	$.ajax({
		type: "GET",
		url: "<?php echo $this->createUrl('archives/getGames');?>",
		dataType: "json",
		success: function (resonseData) {
		
			for (var a in resonseData){
				var _resonse = resonseData[a];
				var game_link = _resonse.link;
				var game_name = _resonse.name;
				var game_icon = _resonse.sicon;
				if(game_link && game_link.indexOf('http://') == -1){
					game_link = game_host+game_link;
				}
				$(".gameListBoxCls").append("<li><a href='"+game_link+"' target='_blank' title='"+game_name+"'><img src='"+game_host+game_icon+"' alt='"+game_name+"' /></a><a href='"+game_link+"' target='_blank' title='"+game_name+"'>"+game_name+"</a></li>");
				
			}
			
		}
	});
	*/

	//族徽鼠标事件
	$('.patern-top').hover(function(){
	    $('.patern-top .paternlistbox').css('display','block');
	},function(){
	    $('.patern-top .paternlistbox').css('display','none');
	});

	//关注按钮提示框关闭
	$('.sharetip a.close').bind('click',function(){
	    $(this).parent('.sharetip').css('display','none');
	    $.cookie('indexGuide2',2,{expires: 365,path: '/',domain:cookie_domain});
	});
	var uid=$.User.getSingleAttribute('uid',true);
	if(uid > 0 && $.cookie('indexGuide2') == 1){
		$('.sharetip').show();
	}
	//点击关闭招呼提示框
	$('.firstcalltip a.close').bind('click',function(){
	    $('.firstcalltip').css('display','none');
	    $.cookie('archiveGuide2',1,{expires: 365,path: '/',domain:cookie_domain});
	});
	if($.cookie('archiveGuide') == 1 && $.cookie('archiveGuide2') != 1){
		$('.firstcalltip').show();
	}

	//直播间新手引导
	var guide_step = 1;
	function NewGuide(){
	    if(!!window.ActiveXObject&&!window.XMLHttpRequest){
	        return;
	    }else{
	        $('.guideMask').css('display','block');
	        $(window).scrollTop(200);
	        $('.guide1').css('display','block');
	        $('.guide1 .newguidebtn').bind('click',function(){
	            $('.guide1').css('display','none');
	            $('.guide2').css('display','block');
	            nextGuide();
	        });
	        $('.guide2 .knowbtn').bind('click',function(){
	            $('.guide2').css('display','none');
	            $('.reward').css('z-index','900');
	            $('.guide3').css('display','block');
	            $('.firstcalltip').show();
	        });
	        $('.guide3 .knowbtn').bind('click',function(){
	            $('.reward').css('z-index','200');
	            $('.guide3').css('display','none');
	            $('.guide4').css('display','block');
	        });
	        $('.guide4 .knowbtn').bind('click',function(){
	            $('.guide4').css('display','none');
	            $('.guide5').css('display','block');
	        });
	        $('.guide5 .nowregbtn').bind('click',function(){
	            $('.guide5').css('display','none');
	            $('.guideMask').css('display','none');
	            $.User.loginController('register');
                $(window).scrollTop(200);
	            $.cookie('archiveGuide',1,{expires: 365,path: '/',domain:cookie_domain});
	        });
	        $('.guideMask .nottipbtn').bind('click',function(){
	        	$('.guideMask').hide();
	        	$.cookie('archiveGuide',1,{expires: 365,path: '/',domain:cookie_domain});
	        });
	    }
	}

	function nextGuide(){
		if(guide_step == 1){
			setTimeout(function(){
				guide_step = 2;
				$('.guide2').css('display','none');
	            $('.reward').css('z-index','900');
	            $('.guide3').css('display','block');
	            $('.firstcalltip').show();
	            nextGuide()
			}, 6000);
		}else if(guide_step == 2){
			setTimeout(function(){
				guide_step = 3;
				$('.reward').css('z-index','200');
	            $('.guide3').css('display','none');
	            $('.guide4').css('display','block');
	            nextGuide()
			}, 6000);
		}else if(guide_step == 3){
			setTimeout(function(){
				guide_step = 4;
				$('.guide4').css('display','none');
	            $('.guide5').css('display','block');
	            nextGuide()
			}, 6000);
		}else if(guide_step == 4){
			setTimeout(function(){
				$('.guide5').css('display','none');
	            $('.guideMask').css('display','none');
	            $(window).scrollTop(0);
	            $.User.loginController('register');
	            $.cookie('archiveGuide',1,{expires: 365,path: '/',domain:cookie_domain});
			}, 6000);
		}
	}

	if(uid < 1 && $.cookie('archiveGuide') != 1){
		NewGuide();
	}
});
</script>

