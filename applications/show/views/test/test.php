		<script type="text/javascript">
		<?php /*?>
        var swfVersionStr = "10.2.0";
        var xiSwfUrlStr = "/statics/swf/playerProductInstall.swf";
        var flashvars = {};
        flashvars.id="ZPhoto";
        var params = {};
        params.id="Photo";
        params.quality = "high";
        params.bgcolor = "0";
        params.allowscriptaccess = "sameDomain";
        params.allowfullscreen = "true";
		params.wmode="opaque";
        var attributes = {};
        attributes.id = "ZPhoto";
        attributes.name = "ZPhoto";
        attributes.align = "middle";
        swfobject.embedSWF(
        "/statics/swf/ZPhoto.swf?v="+Math.random(), "flashContent", 
        "100%", "100%", 
        swfVersionStr, xiSwfUrlStr, 
        flashvars, params, attributes);
        swfobject.createCSS("#flashContent", "display:block;text-align:left;");
        <? */?>
        function setplayer(){
        	
        }
        function setSize(){
			return [220,130];
        }
        </script>
        <div style="width:304px;height:380px;margin:auto;">
        <?php /*?>
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
        <!--<input type="button" value="继续" onclick="enterCameraMedia();" /><input type="button" value="拍照" onclick="enterPhotoMedia();" /><input type="button" value="上传" onclick="uploadPhoto();" />-->
        <noscript>
        <? */?>
            <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="304" height="380" id="ZPhoto">
                <param name="movie" value="/statics/swf/ZPhoto.swf" />
                <param name="quality" value="high" />
                <param name="bgcolor" value="#ffffff" />
                <param name="allowScriptAccess" value="sameDomain" />
                <param name="allowFullScreen" value="true" />
				<param name="wmode" value="transparent" />
                <!--[if !IE]>-->
                <object type="application/x-shockwave-flash" data="/statics/swf/ZPhoto.swf" width="100%" height="100%">
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
        <?php /*?>
        </noscript>
        <? */?>
		</div>