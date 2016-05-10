<?php
if($family['status'] == 0){
	$title = '家族筹备中';
	$status = 0;
	if($family['uid'] == Yii::app()->user->id){
		$url = 'family/kick';
	}else{
		$url = 'family/quit';
	}
}else{
	$title = '家族筹备完成，等待官方确认';
	$status = 1;
}
?>

<div class="w1000 bord mt40">
	<h1><?php echo $family['name'];?> <?php echo $title;?></h1>
	<?php if(!$status){?>
	<div class="cb_con">
    	- 请在3天内邀请到8名绅士03以上玩家作为家族初创成员 -<br/>
    	- 超过3天未找齐成员，筹备中的家族将自动解散 -<br/>
    	<p>距离筹备时限结束还剩<a class="pink"><?php $time = ceil(($family['create_time'] + 86400*3 - time()) / 3600); echo $time >= 0 ? $time : 0;?></a>小时</p>
	</div>
	<?php }?>
   
	<div class="info_table">
		<form action="<?php echo $this->createUrl('family/prepare', array('family_id' => $family['id'])); ?>" method="post">
        <ul class="info_content clearfix">
            <li><label>家族长：</label><div class="long_filed"><em class="lvlr lvlr-<?php echo $user['rk'];?> mr10"></em><span class="pink"><?php echo $user['nk'];?></span>（<?php echo $user['uid'];?>）</div></li>
            <li><label>族徽：</label><div class="short_filed"><img src="/images/family/<?php echo $family['id'];?>/medal_13.jpg" /></div></li>
            <li class="shortlineh"><label>初创成员：</label>
            <div class="long_filed">已邀请到（<span class="pink"><?php echo count($members);?>/8</span>）人加入<?php if(!$status){?>，还差<span class="pink"><?php echo count($members) >= 8 ? 0 : (8 - count($members));?></span>人即可达成要求。<?php }else{?>.<?php }?></div></li>
            <?php foreach($members as $m){ ?>
            <li class="shortlineh">
            	<label>&nbsp;</label>
            	<div class="long_filed">
            		<div style="display: inline-block; float:left; width:280px;">
            		<em class="lvlr lvlr-<?php echo $m['rank'];?> mr10"></em>
            		<span><?php echo $m['nickname'];?></span>
            		（<?php echo $m['uid'];?>）
            		</div>
            		<div style="display: inline-block; float:right;">
            		<?php if(!$status){?>
            			<?php if($family['uid'] == Yii::app()->user->id || $m['uid'] == Yii::app()->user->id){?>
            			<a style="" href="<?php echo $this->createUrl($url, array('family_id' => $family['id'],'uids' => $m['uid']));?>"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/yc.jpg"  class="vett_mid ml20"/></a>
            			<?php }?>
            		<?php }?>
            		</div>
            	</div>
            </li>
            <?php } ?>
            <?php if(!$status){?>
            <li style="margin-top:30px;">
            	<div class="short_filed" style="width:100%;text-align:center">
            		<?php if($family['uid'] == Yii::app()->user->id){ ?>
            		<input type="submit" class="btn_enter mr15" value="完成筹备" />
            		<?php }elseif(!$in){ ?>
            		<input type="button" class="btn_enter mr15" value="加入家族" onclick="location.href='<?php echo $this->createUrl('family/join', array('family_id' => $family['id']));?>';" />
            		<?php } ?>
            	</div>
            </li>
            <?php }?>
		</ul>
        </form>
   </div><!--.info_table-->
   
   <?php if(!$status){?>
   <div class="howadd">
      <strong>如何邀请他人加入筹备中的家族</strong>
      <div class="asw">
       <p>1.筹备家族的玩家，操作浮沉内会显示出“家族主页”选项，其他玩家点击选项，打开本家族筹备页面，点击“加入家族”即可。</p>
       <p><img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/tjimg.jpg" /></p>
       <p>2.筹备家族的玩家，可以复制家族主页的链接地址发送给他人，来完成邀请加入。</p>
       <p><input id="url" class="webaddress" type="text"  value="http://show.pipi.cn<?php echo $this->createHomeUrl($family['id']);?>" /><input name="copy" id="copy" class="copy" type="button" value="复制家族链接地址"></p>
      </div> 
   </div><!-- howadd -->
   
   <div style="width:400px; margin:10px 0 0 125px;">*超过3天未找齐成员，筹备中的家族将自动解散后，缴纳费用不再退还。</div>
   <?php }?>
</div>
<script type="text/javascript">
$(function(){
	$('#copy').click(function(){
		var text = $('#url').val();
		if(copyToClipboard(text)){ 
			alert("复制成功 "); 
		}
	});
});
function copyToClipboard(txt) {   
    if(window.clipboardData) {   
            window.clipboardData.clearData();   
            window.clipboardData.setData("Text", txt);   
    } else if(navigator.userAgent.indexOf("Opera") != -1) {   
         window.location = txt;   
    } else if (window.netscape) {   
         try {   
              netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");   
         } catch (e) {   
              alert("被浏览器拒绝！\n请在浏览器地址栏输入'about:config'并回车\n然后将'signed.applets.codebase_principal_support'设置为'true'");   
         }   
         var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);   
         if (!clip)   
              return false;   
         var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);   
         if (!trans)   
              return false;   
         trans.addDataFlavor('text/unicode');   
         var str = new Object();   
         var len = new Object();   
         var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);   
         var copytext = txt;   
         str.data = copytext;   
         trans.setTransferData("text/unicode",str,copytext.length*2);   
         var clipid = Components.interfaces.nsIClipboard;   
         if (!clip)   
              return false;   
         clip.setData(trans,null,clipid.kGlobalClipboard);   
         return true;
    }   
}
</script>