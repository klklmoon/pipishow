<div class="clearfix w1000 mt30">
    
    <div class="main" style="width:1000px;">
        <ul class="main-menu clearfix" id="MianList" style="width:960px;">
            <li class="menuvisted"><a href="javascript:void(0)">找回密码</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list" id="sendMesg">
					
            <p>绑定手机： &nbsp;<input name="phone" id="phone" type="text" style="width:150px;"/> &nbsp;&nbsp; <a class="pink" id="sms_tip">您认真填写自己的手机号码</a> </p>
             <p>短信验证码： &nbsp;<input name="code" id="code" type="text" style="width:150px;"/> &nbsp;&nbsp;
             <a class="pink" id='code_tip' style="display:none;">&nbsp;&nbsp;短信验证码已已过期，请重新获取</a>&nbsp;
             <a href="javascript:void(0)"  onclick="return getMobileSms()" class="modify_1" id='get_sms'>免费领取</a>
             &nbsp;&nbsp; <a href="<?php echo $this->createUrl('user/findPass&step=kefu') ?>" class="pink ml55">通过客服找回密码</a>
             </p>
             </p>
             <div class="sendbj retry_getsms" style="height:auto;display:none;">
             	短信验证码正发送至您的手机，请收到验证码后30分钟内填写。（每天有3次免费发送 验证码的机会，收不到短信可选择下方的重新发送）
             	<a  class="modify_1" href="javascript:void(0)" onclick="return retryGetMobileSms();" id="send_mail">重发发送</a> <a style="display:none;" href="javascript:void(0)" onclick="return false;" id="cal_time">60秒后重新发送</a>
             </div>
             <p class="retry_getsms" style="display:none;"><a href="javascript:void(0)" class="modify_1" onclick="return verfiyMobile()">下一步</a></p>
           </div> 
           
           <div class="cooper-list" id="setPassword" style="display: none;">
          	  <input type="hidden"  name="ticket" value="" id="ticket"/>
              <p>新密码:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              		<input type="password" id="password" name="password"/><a id="tip_password" class="pink"></a>
              </p>
              <p>确认新密码:&nbsp;&nbsp;<input type="password" id="re_password" name="re_password"/><a id="tip_repassword" class="pink"></a>
              </p>
              <p><a href="javascript:void(0)" onclick="return setPassword()"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/account/conf.jpg" style="display:block; margin-left:65px;" /></a></p>
           </div>  
           <div class="cooper-list onhide"></div> 
      </div>
     </div>       
</div>

<script type="text/javascript">
$('#phone').blur(
		function(){validSms();}
);
var seconds = 59;
function countDown(seconds){
    $('#cal_time').html(seconds+'秒后重新发送');
    $('#cal_time').css({'display':''});
    $('#send_mail').css({'display':'none'});
    var timeId = setTimeout("countDown(seconds--,1000)",1000);
    if(seconds <= 0){
         clearTimeout(timeId);
         $('#cal_time').css({'display':'none'});
         $('#send_mail').css({'display':''});
     };
}


function validSms (){
	var flag = true;
	flag = $.empty('#phone','#sms_tip','手机号码不能为空');
	if(flag){
		flag = $.phone('#phone','#sms_tip','手机号码必须是11位数字');
	}
	if(flag){
		$.controllCss('#sms_tip','','','');
		$('#sms_tip').css({'color':''});
	}else{
		$('#sms_tip').css({'color':'red','display':'inline'});
	}
	return flag;
}

function validCode(){
	var flag = true;
	flag = $.empty('#code','#code_tip','短信验证码不能为空');
	if(flag){
		flag = $.int('#code','#code_tip','短信验证码必须是4位数字');
	}

	if(flag){
		$.controllCss('#code_tip','','','');
		$('#code_tip').css({'color':''});
	}else{
		$('#code_tip').css({'color':'red','display':'inline'});
	}
	return flag;
}

function retryGetMobileSms(){
	var phone = $('#phone').val();
	if(validSms()){
		$.ajax({
			type:"POST",
			url:"index.php?r=user/find&type=mobile",
			data:{phone:phone},
			dataType:"json",
			async: false, 
			success:function(response){
				if(response.status == 'fail'){
					$.controllCss('#sms_tip','','error',response.info);
					$('#sms_tip').css({'color':'red','display':'inline'});
				}else{
					if(seconds <=0){
						seconds = 59;
					}
					countDown(seconds);
				}
			}
		});
	}
	return false;
}

function getMobileSms(){
	var phone = $('#phone').val();
	if(validSms()){
		$.ajax({
			type:"POST",
			url:"index.php?r=user/find&type=mobile",
			data:{phone:phone},
			dataType:"json",
			async: false, 
			success:function(response){
				if(response.status == 'fail'){
					$.controllCss('#sms_tip','','error',response.message);
					$('#sms_tip').css({'color':'red','display':'inline'});
				}else{
					$('#get_sms').css({'display':'none'});
					$('.retry_getsms').css({'display':''});
				}
			}
		});
	}
	return false;
}

function verfiyMobile(){
	var phone = $('#phone').val();
	var code = $('#code').val();
	var flag = validSms();
	if(flag){
		flag = validCode();
	}
	if(flag){
		$.ajax({
			type:"POST",
			url:"index.php?r=user/password&type=mobile",
			data:{phone:phone,code:code},
			dataType:"json",
			async: false, 
			success:function(response){
				if(response.status == 'fail'){
					if(response.type == 'code'){
						$.controllCss('#code_tip','','error',response.message);
						$('#code_tip').css({'color':'red','display':'inline'});
					}else{
						$.controllCss('#sms_tip','','error',response.message);
						$('#sms_tip').css({'color':'red','display':'inline'});
					}
				}else{
					$('#ticket').val(response.message);
					$('#sendMesg').css({'display':'none'});
					$('#setPassword').css({'display':''});
					
				}
			}
		});
	}
	return false;
}

$('#password').blur(
		function(){validPassword();}
);

$('#re_password').blur(
		function(){validEqualPassword();}
);

function validPassword (){
	var flag = true;
	flag = $.empty('#password','#tip_password','密码不能为空');
	if(flag){
		flag = $.len($('#password').val().length,4,20,'#tip_password','密码长度必须在4-20个字符之间');
	}
	if(flag){
		$.controllCss('#tip_password','','','');
	}else{
		$('#tip_password').addClass('pink');
	}
	return flag;
}

function validEqualPassword(){
	var flag = true;
	flag = $.equal($('#password').val(),$('#re_password').val(),'#tip_repassword','两次输入的密码不一致');
	if(flag){
		$.controllCss('#tip_repassword','','','');
	}else{
		$('#tip_repassword').addClass('pink');
	}
	return flag;
	
}

function setPassword(){

	var ticket = $('#ticket').val();
	var password = $('#password').val();
	var rePassword = $('#re_password').val();
	var type = "mobile";
	
	var flag = validPassword();
	if(flag){
		flag = validEqualPassword();
	}
	if(flag){
		$.ajax({
			type:"POST",
			url:"index.php?r=user/setPassword",
			data:{password:$.md5(password),repassword:$.md5(rePassword),ticket:ticket,type:type},
			dataType:"json",
			async: false, 
			success:function(response){
				if(response.status == 'fail'){
					$.controllCss('#tip_repassword','','pink',response.message);
					return false;
				}else{
					window.location = "<?php echo $this->createUrl('user/findPass&step=scuccess') ?>";
				}
			}
		});
	}
	return false;
}
</script>