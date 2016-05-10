<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>皮皮乐天</title>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.min.js "></script>
<link rel="stylesheet" type="text/css" href="/statics/css/spread/common.css">
<script type="text/javascript" src="/statics/js/common/common.js"></script>
<script type="text/javascript" src="/statics/js/common/jquery.form.js"></script>
</head>
<body>
	<div class="flash-box clearfix">
		<div class="flash">
			<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="469" height="345"
			    codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0">
			    <param name="movie" value="http://afm.pipi.cn/player/show/2013/1108/flash.swf">
			    <param name="quality" value="high">
			    <param name="bgcolor" value="#F0F0F0">
			    <param name="menu" value="false">
			    <param name="wmode" value="opaque"><!--Window|Opaque|Transparent-->
			    <param name="FlashVars" value="">
			    <param name="allowScriptAccess" value="sameDomain">
			    <embed src="http://afm.pipi.cn/player/show/2013/1108/flash.swf"
			        width="469"
			        height="345"
			        align="middle"
			        quality="high"
			        bgcolor="#f0fff8"
			        menu="false"
			        play="true"
			        loop="false"
			        FlashVars=""
			        allowScriptAccess="sameDomain"
			        wmode="transparent"
			        type="application/x-shockwave-flash"
			        pluginspage="http://www.adobe.com/go/getflashplayer">
			    </embed>
			</object>
		</div>
	</div>
			
	<div id="mask">
		<div class="mask-box">
			<div class="mask-reg">
				<p id="register_error"></p>
				<p><label>账号：</label><input type="text" id="register_username"><span>长度为6-15位，字母开头</span></p>
				<p><label>密码：</label><input type="password" id="register_password"><span>长度为6-20位</span></p>
				<p><label>重复密码：</label><input type="password" id="register_confirm_password"></p>
				<p class="code"><label>验证码：</label><input type="text" id="register_code">
				<?php 
            	$captchaParams  = array(
					'clickableImage'=>true,
					'showRefreshButton'=>false,
					'captchaAction'=>'user/captcha',
					'imageOptions'=>array('width'=>95,'height'=>40,'alt'=>'点击换图','title'=>'点击换图','style'=>'cursor:pointer','id'=>'register_code_img','onclick'=>'freshCaptcha();')
				);
            	$this->widget('CCaptcha',$captchaParams); 
            ?><a href="javascript:void(0)" onclick="freshCaptcha();">换一张</a></p>
				<p class="regbtn"><a href="javascript:void(0);"><img src="/statics/fontimg/spread/regbtn.png"></a></p>
			</div><!--.mask-reg-->
		</div><!--.mask-box-->
	</div>

<script type="text/javascript">
var user_attribute={};
function GetValue() { 
    var url = location.search;
    var theRequest = new Object();
    var str,geturl,aLen,aHref;
    if (url.indexOf("?") != -1) { 
      str = url.substr(1); 
      strs = str.split("&"); 
      for(var i = 0; i < strs.length; i ++) { 
         theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]); 
      } 
    }
    return(theRequest['sign'])?theRequest['sign']:'nature';
}
function freshCaptcha(){
	$("#register_code_img").attr('src','<?php echo $this->createUrl('user/captcha',array('v'=>uniqid()))?>');
}
$(function(){
	var _this=this;
	$("#register_username").live('blur',function(){
		var flag = true;
		flag = $.empty('#register_username','#register_error','账号不能为空');
		if(flag){
			flag = $.pipiUserName('#register_username','#register_error','账号由4-15个字母、数字、下划线组成');
		}
		if(flag){
			$.controllCss('#register_error','','','');
		}
		
	});
	$("#register_password").live('blur',function(){
		var flag = true;
		flag = $.empty('#register_password','#register_error','密码不能为空');
		if(flag){
			flag = $.len($('#register_password').val().length,4,20,'#register_error','密码长度必须在4-20个字符之间');
		}
		if(flag){
			$.controllCss('#register_error','','','');
		}
	});
	$("#register_confirm_password").live('blur',function(){
		var flag = true;
		flag = $.empty('#register_confirm_password','#register_error','重复密码不能为空');
		if(flag){
			flag = $.equal($('#register_password').val(),$('#register_confirm_password').val(),'#register_error','重复密码不相等');;
		}
		if(flag){
			$.controllCss('#register_error','','','');
		}
	});
	$(".regbtn a").bind('click',function(){
		var username=$('#register_username').val();
		var password=$('#register_password').val();
		var confirm_password=$('#register_confirm_password').val();
		var code=$('#register_code').val();
		var sign=GetValue();
		$.ajax({
			type:'POST',
			url:'<?php echo $this->createUrl('user/register');?>',
			data:{username:username,nickname:username,password:password,confirm_password:confirm_password,code:code,sign:sign},
			dataType:'JSON',
			success:function(data){
				if(data.status=='success'){
					window.location.href = data.data;
				}else{
					$.controllCss('#register_error','','error',data.message);
					$('#register_code_img').click();
					$('#register_code').val('');
				}
			}
		});
	});
})


</script>
</body>
</html>
