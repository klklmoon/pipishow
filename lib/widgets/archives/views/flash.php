<script>
<?php if($flash['model']=='live'):?>
<?php 
	$archivesService=new ArchivesService();
	$live_archives_list=$archivesService->getRecommondLiveArchives(8);
?>
var live_archives_list='<?php echo json_encode($live_archives_list)?>';
<?php endif;?>
var source='<?php if($flash['model']=='record'){ echo $flash['source']['import_host'].'/'.$flash['archivesId'];}else{ echo $flash['source']['export_host'].'/'.$flash['archivesId'];}?>';
</script>
<?php if($flash['model']=='record'):?>
<div class="model">
        <a href="javascript:void(0);" id="LModel" class="l-model rover" title="flash模式"></a>
        <a href="javascript:void(0);" id="RModel"  title="插件模式"></a>
</div>
<?php endif;?>
<ul class="medal-box">
<?php if($flash['giftStar']['flag']==1):?>
	<li class="medal-1"><img src="<?php echo $flash['flashPath'];?>/fontimg/activities/giftstar/giftstarmedal.gif"></li>
<?php endif;?>
<?php if(isset($flash['giftStar']['lastWeekInfo']) && count($flash['giftStar']['lastWeekInfo'])>0):?>
<li>
<?php if(isset($flash['giftStar']['lastWeekInfo'][0])):?>
    <img src="<?php echo $flash['giftStar']['lastWeekInfo'][0]['gift_img_url']; ?>">
 <?php endif;?>
 <?php if(isset($flash['giftStar']['lastWeekInfo'][1])):?>
    <img src="<?php echo $flash['giftStar']['lastWeekInfo'][1]['gift_img_url']; ?>">
 <?php endif;?>
 </li>
 <?php endif;?>
 <?php if(isset($flash['giftStar']['thisWeekInfo']) && count($flash['giftStar']['thisWeekInfo'])>0):?>
 <li>
  <?php if(isset($flash['giftStar']['thisWeekInfo'][0])):?>
    <img src="<?php echo $flash['giftStar']['thisWeekInfo'][0]['gift_img_url']; ?>">
 <?php endif;?> 
 <?php if(isset($flash['giftStar']['thisWeekInfo'][1])):?>
    <img src="<?php echo $flash['giftStar']['thisWeekInfo'][1]['gift_img_url']; ?>">
 <?php endif;?>  
 </li>
  <?php endif;?>  
</ul>

<?php if(isset($flash['doteyHalloween']) && $flash['doteyHalloween']['is_display']==true):?>
<div class="wsjday">
<embed width="100"  height="100" src="<?php echo $flash['flashPath'].$flash['doteyHalloween']['swf'];?>" wmode="transparent" bgcolor="#fff" quality="high" type="application/x-shockwave-flash">
</div>
<?php endif;?>

<?php 
	$operateService=new OperateService();
	$ads=$operateService->getLiveAdv($flash['archives_id'], Yii::app()->user->id);
?>

<div class="live-player" id="flash-player" style="width:<?php echo $flash['width'].'px';?>;height:<?php echo $flash['height'].'px';?>;">
	<?php if($flash['model']=='record'):?>
		<iframe name="flashiframe" src="<?php echo $flash['flashPath'];?>/swf/flashRecordPlayer.html?<?php echo rand(1000,9999)?>"  id="ifflash" width="460px" height="345px"  frameborder="0" scrolling="no"   hspace="0" vspace="0" marginwidth="0" marginheight="0"></iframe>
	<?php else:?>
	<script type="text/javascript">
        var swfVersionStr = "10.2.0";
        var xiSwfUrlStr = "<?php echo $flash['flashPath'];?>/swf/archives/playerProductInstall.swf";
        var flashvars = {};
        flashvars.id="CPlayer";
        var params = {};
        params.id="LivePlayer";
        params.quality = "high";
        params.bgcolor = "0";
        params.allowscriptaccess = "sameDomain";
        params.allowfullscreen = "true";
		params.wmode="opaque";
        var attributes = {};
        attributes.id = "CPlayer";
        attributes.name = "CPlayer";
        attributes.align = "middle";
        swfobject.embedSWF(
        "<?php echo $flash['flashPath'];?>/swf/archives/CPlayer.swf?v="+Math.random(), "flashContent", 
        "100%", "100%", 
        swfVersionStr, xiSwfUrlStr, 
        flashvars, params, attributes);
        swfobject.createCSS("#flashContent", "display:block;text-align:left;");
        function setupPlayer(id){
             player(id).setAdUrl('<?php echo $ads['src']?>','<?php echo $ads['time']?>','<?php echo $ads['url']?>','<?php echo $ads['type']-1?>');
             var live_archives=$.parseJSON(live_archives_list);
             player(id).setVideoUrl('live',source,live_archives['imageArr'],live_archives['nameArr']);	
         }
         function player(id){
             return swfobject.getObjectById(id);
		 }
         
		</script>
   		<div id="flashContent">
            <p>
                To view this page ensure that Adobe Flash Player version 
                10.2.0 or greater is installed. 
            </p>
		<script type="text/javascript"> 
                var pageHost = ((document.location.protocol == "https:") ? "https://" : "http://"); 
                document.write("<a href='http://www.adobe.com/go/getflashplayer'><img src='" 
                                + pageHost + "www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a>" ); 
		</script> 
        </div>
        <noscript>
            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%" id="LivePlayer">
                <param name="movie" value="LivePlayer.swf" />
                <param name="quality" value="high" />
                <param name="bgcolor" value="#ffffff" />
                <param name="allowScriptAccess" value="sameDomain" />
                <param name="allowFullScreen" value="true" />
				<param name="wmode" value="transparent" />
                <!--[if !IE]>-->
                <object type="application/x-shockwave-flash" data="LivePlayer.swf" width="100%" height="100%">
                    <param name="quality" value="high" />
                    <param name="bgcolor" value="#ffffff" />
                    <param name="allowScriptAccess" value="sameDomain" />
                    <param name="allowFullScreen" value="true" />
                <!--<![endif]-->
                <!--[if gte IE 6]>-->
                    <p> 
                        Either scripts and active content are not permitted to run or Adobe Flash Player version
                        10.2.0 or greater is not installed.
                    </p>
                <!--<![endif]-->
                    <a href="http://www.adobe.com/go/getflashplayer">
                        <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash Player" />
                    </a>
                <!--[if !IE]>-->
                </object>
                <!--<![endif]-->
            </object>
        </noscript>     
		<?php endif;?>
</div>
<?php if($flash['model']=='record'):?>
<div class="live-player" id="actx-player" style="display:none;">
	<iframe src="" id="ifactx" name="actxiframe" style="position:absolute;top:0; left:0; z-index:20;" width="460px" height="345px"  frameborder="0" scrolling="no"   hspace="0" vspace="0" marginwidth="0" marginheight="0"></iframe>
</div>
<?php endif;?>
<div id="flyscreen" style="position: absolute; top: 255px; left: 0; width: 0px; height: 0px;z-index:100">
	<script type="text/javascript">
        var swfVersionStr = "10.2.0";
        var xiSwfUrlStr = "<?php echo $flash['flashPath'];?>/swf/archives/playerProductInstall.swf";
        var flashvars = {};
        flashvars.id="flyMovice";
        var params = {};
        params.id="flyMovice";
        params.quality = "high";
        params.bgcolor = "#ffffff";
        params.allowscriptaccess = "sameDomain";
        params.allowfullscreen = "true";
		params.wmode="transparent";
        var attributes = {};
        attributes.id = "flyMovice";
        attributes.name = "flyMovice";
        attributes.align = "middle";
        attributes.style = "display:block";
        swfobject.embedSWF(
        "<?php echo $flash['flashPath'];?>/swf/archives/flyMovice.swf?v="+Math.random(), "flashContent1", 
        "100%", "100%", 
        swfVersionStr, xiSwfUrlStr, 
        flashvars, params, attributes);
        swfobject.createCSS("#flashContent1", "display:block;text-align:left;");
        </script>
         <style type="text/css" media="screen">  
            #flashContent1 { display:none; }
        </style>
        
        <div id="flashContent1">
            <p>
                To view this page ensure that Adobe Flash Player version 
                10.2.0 or greater is installed. 
            </p>
            <script type="text/javascript"> 
                var pageHost = ((document.location.protocol == "https:") ? "https://" : "http://"); 
                document.write("<a href='http://www.adobe.com/go/getflashplayer'><img src='" 
                                + pageHost + "www.adobe.com/images/shared/download_buttons/get_flash_player.gif' alt='Get Adobe Flash player' /></a>" ); 
            </script> 
        </div>
</div>