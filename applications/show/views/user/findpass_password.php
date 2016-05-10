<div class="clearfix w1000 mt30">
    
    <div class="main" style="width:1000px;">
        <ul class="main-menu clearfix" id="MianList" style="width:960px;">
            <li class="menuvisted"><a href="#">找回密码</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
          
           <div class="cooper-list">
          	  <input type="hidden"  name="uid" value="<?php echo $uid?>" id="uid"/>
          	  <input type="hidden"  name="ticket" value="<?php echo $ticket?>" id="ticket"/>
              <p>新密码:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              		<input type="password" id="password" name="password"/><a id="tip_password" class="pink"></a>
              </p>
              <p>确认新密码:&nbsp;&nbsp;<input type="password" id="re_password" name="re_password"/><a id="tip_repassword" class="pink"></a>
              </p>
              <p><a href="javascript:void(0)" onclick="return setPassword()"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/account/conf.jpg" style="display:block; margin-left:65px;" /></a></p>
           </div>
      </div>
     </div>       
</div>
<script type="text/javascript">
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
	var uid = $('#uid').val();
	var ticket = $('#ticket').val();
	var password = $('#password').val();
	var rePassword = $('#re_password').val();
	var type = "<?php echo $type?>";
	if(uid <= 0){
		return false;
	}
	var flag = validPassword();
	if(flag){
		flag = validEqualPassword();
	}
	if(flag){
		$.ajax({
			type:"POST",
			url:"index.php?r=user/setPassword",
			data:{uid:uid,password:$.md5(password),repassword:$.md5(rePassword),ticket:ticket,type:type},
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