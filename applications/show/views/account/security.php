<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>

 <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">账户安全</a></li>
            <?php if ($this->domain_type != 'tuli'):?>
            <li><a href="#">修改密码</a></li>
            <?php endif;?>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
           	  <?php if(!$type):?>
              	<ul class="safelist">
              	<?php
                if(!$mobile):
                ?>
                <li>
                   <i class="phone-none"></i>
                   <div class="safelist_intro fleft"><a class="pink"><strong>绑定手机：</strong>您尚未绑定手机</a><br/>
                   忘记密码时，您可以通过绑定的手机找回密码。</div> 
                   <a href="<?php echo $this->createUrl('account/security&type=mobile&step=bind') ?>" class="modify_1 fright mt30">绑定手机</a>
                </li>
                <?php else:?>
                <li>
                   <i class="phone-done"></i>
                   <div class="safelist_intro fleft"><a class="pink"><strong>安全手机：</strong> <?php echo $proteted_mobile ?></a><br/>
                   忘记密码时，您可以通过绑定的手机找回密码。</div> 
                   <a href="<?php echo $this->createUrl('account/security&type=unMobile&step=unBind') ?>" class="modify_1 fright mt30">解除绑定</a>
                </li>
                <?php endif;?>
              	<?php if(!$email):?>
                <li>
                   <i class="email-none"></i>
                   <div class="safelist_intro fleft"><a class="pink"><strong>绑定邮箱：</strong>您尚未绑定邮箱</a><br/>忘记密码时，您可以通过绑定的邮箱找回密码。</div> 
                   <a href="<?php echo $this->createUrl('account/security&type=mail&step=bind') ?>" class="modify_1 fright mt30">绑定邮箱</a>
                </li>
                <?php else:?>
                <li>
                  <i class="email-done"></i>
                   <div class="safelist_intro fleft"><a class="pink"><strong>安全邮箱：</strong><?php echo $protected_email ?> </a><br/>
                  忘记密码时，您可以通过绑定的邮箱找回密码。</div> 
                  <a href="<?php echo $this->createUrl('account/security&type=unMail&step=unBind') ?>" class="modify_1 fright mt30">解除绑定</a>
                </li>
                <?php endif;?>
              </ul>
              <?php elseif($type == 'mobile'):?>
	          		<?php if($step == 'bind'):?>
	          			<P>说明：忘记密码时，您可以通过绑定的手机找回密码。请您认真填写自己的手机，杜绝安全隐患。</P>
            			<p>绑定手机： &nbsp;<input name="phone" id="phone" type="text" style="width:150px;"/> &nbsp;&nbsp; <a class="pink" id="sms_tip">请您认真填写自己的手机号码</a> </p>
             			<p>短信验证码： &nbsp;<input name="code" id="code" type="text" style="width:150px;"/> &nbsp;&nbsp;<a href="javascript:void(0)"  onclick="return getMobileSms()" class="modify_1" id='get_sms'>免费领取</a><a class="pink" id='code_tip' style="display:none;">&nbsp;&nbsp;短信验证码已已过期，请重新获取</a></p>
			             <div class="sendbj retry_getsms" style="height:auto;display:none;">
			             	短信验证码正发送至您的手机，请收到验证码后30分钟内填写。（每天有3次免费发送 验证码的机会，收不到短信可选择下方的重新发送）
			             	<a  class="modify_1" href="javascript:void(0)" onclick="return retryGetMobileSms();" id="send_mail">重发发送</a> <a style="display:none;" href="javascript:void(0)" onclick="return false;" id="cal_time">60秒后重新发送</a>
			             </div>
			             <p class="retry_getsms"><a href="javascript:void(0)" class="modify_1" onclick="return bindMobile()">绑定手机</a></p>
	          		<?php elseif($step == 'verify'):?>
               		
               			
               			 	<div class="sendbj">
              					<div class="fleft">您的手机 <strong class="pink"><?php echo $bind_mobile;?></strong>  已经绑定成功！</div>
             				</div>
             				<div class="safetips mt30"><strong style=" color:#000;">温馨提示：</strong><br/>
                				忘记密码时，您可以通过绑定的手机找回密码。
                			</div> 
               			 
	          		<?php endif;?>
	          <?php elseif($type == 'unMobile'):?>
	           		<?php if($step == 'unBind'):?>
	           			<P>说明：忘记密码时，您可以通过绑定的手机找回密码。请您认真填写自己的手机，杜绝安全隐患。</P>
             			<p>已绑定手机：<?php echo $proteted_mobile?> </p>
            			<p>短信验证码： &nbsp;<input name="code" id="code" type="text" style="width:150px;"/> &nbsp;&nbsp;<a href="javascript:void(0)" class="modify_1"  onclick="return unSetMobileSms('<?php echo $mobile?>')" id="get_sms">免费领取</a><a class="pink" id='code_tip' style="display:none;">&nbsp;&nbsp;获取超过3次，请明天重新获取</a></p>
            			 <div class="sendbj retry_getsms" style="height:auto;display:none;">短信验证码正发送至您的手机，请收到验证码后30分钟内填写。（每天有3次免费发送 验证码的机会，收不到短信可选择下方的重新发送）
            			 <a  class="modify_1" href="javascript:void(0)" onclick="return retryUnSetMobileSms('<?php echo $mobile?>');" id="send_mail">重发发送</a> <a style="display:none;" href="javascript:void(0)" onclick="return false;" id="cal_time">60秒后重新发送</a>
             			</div>
           				 <p  class="retry_getsms"><a href="javascript:void(0)" class="modify_1" onclick="return unBindMobile('<?php echo $mobile?>')">解除绑定</a></p>
           				  
           			<?php elseif($step == 'verify'):?>
               		
               		
               			 	<div class="sendbj">
              					 <div class="fleft">您的手机<strong class="pink"> <?php echo $protected_mobile?></strong> 解绑成功！</div>
             				</div>
             				<div class="safetips mt30"><strong style=" color:#000;">温馨提示：</strong><br/>忘记密码时，您可以通过绑定的手机找回密码。</div> 
            			    <p><a class="modify_1" href="<?php echo $this->createUrl('account/security&type=mobile&step=bind') ?>">绑定手机</a></p>
               			
               			 
	           		<?php endif;?>
              <?php elseif($type == 'mail'):?>
              		<?php if($step=='bind'):?>
	              	  <form id="form_mail" action="<?php echo $this->createUrl('account/security&type=mail&step=verify') ?>" method="post">
			              <div class="cooper-list">
			            	 <P>说明：忘记密码时，您可以通过绑定的邮箱找回密码。请您认真填写自己的常用邮箱，以防遗忘。</P>
			            	 <p>邮箱： &nbsp;<input name="email" id="email" type="text"/> &nbsp;&nbsp;<span id='email_tip'>请您认真填写自己的常用邮箱</span></p>
			            	 <p><a href="javascript:void(0)" onclick="return doBindSendVerify();" class="modify_1 ml45">绑定邮箱</a></p>
			          	 </div>
			          </form>
			         <?php elseif($step == 'send'):?>
			         
             			<P>说明：忘记密码时，您可以通过绑定的邮箱找回密码。请您认真填写自己的常用邮箱，以防遗忘。</P>
             			<div class="sendbj fleft" style="width:auto"><div class="fleft">绑定验证邮件已发送到您的邮箱
             		    <strong class="pink"><?php echo $bind_email ?></strong>,请查收</div> <a href="<?php echo $mail_href ?>" class="modify_1 ml20 fleft" target="_blank">前往邮箱</a></div>
             			<div class="safetips mt30 fleft"><strong style=" color:#000;">温馨提示：</strong><br/>
						               验证邮件7天内有效，请尽快完成验证。<br/>
						               邮件到达时间可能长达2-3分钟，请耐心等待。<br/>                   
						               如果长时间还未收到邮件，请检查垃圾邮件或者选择&nbsp;<a class="pink" href="javascript:void(0)" onclick="return retryBindSend('<?php echo $bind_email ?>');" id="send_mail">重发验证邮件</a> <a style="display:none;" href="javascript:void(0)" onclick="return false;" id="cal_time">60秒后重新发送</a><br/>                    
						               如果邮箱填写有误或者一直无法收到邮件，建议您&nbsp;<a class="pink" href="<?php echo $this->createUrl('account/security&type=mail&step=bind') ?>">修改邮箱</a>
               			</div>                    
          			 
          			 <?php elseif($step == 'verify'):?>
          			
          				 <?php if($verify):?>
				             <div class="sendbj">
				             	<div class="fleft">您的邮箱 <strong class="pink"><?php echo $bind_email;?></strong> 已经绑定成功！</div>
				             </div>
				              <div class="safetips mt30"><strong style=" color:#000;">温馨提示：</strong><br/>
				              	 忘记密码时，您可以通过绑定的邮箱找回密码。
				              </div>    
               			 <?php else:?>
        
		             		<div class="sendbj"><div class="fleft">抱歉，您的验证邮件已失效，请通过以下链接重新发送验证邮件！</div></div>
			             	<div class="safetips mt30"><strong style=" color:#000;">温馨提示：</strong><br/>
			              	 验证邮件7天内有效，请尽快完成验证。<br/>
			               	邮件到达时间可能长达2-3分钟，请耐心等待。<br/>
			               	如果长时间还未收到邮件，请检查垃圾邮件或者选择&nbsp;<a class="pink" href="javascript:void(0)" onclick="return retryBindSend('<?php echo $bind_email ?>');" id="send_mail">重发验证邮件</a> <a style="display:none;" href="javascript:void(0)" onclick="return false;" id="cal_time">60秒后重新发送</a>
			               </div>  
			             <?php endif;?>  
	          	 	<?php endif;?>
	          <?php elseif($type == 'unMail'):?>
	          		<?php if($step == 'unBind'):?>
			             <div class="sendbj">
			             	<div class="fleft">发送解绑验证邮件到您的邮箱 <strong class="pink"><?php echo $email?></strong></div>
			             </div>
	            		 <p><a class="modify_1" href="javascript:void(0)" onclick="return unBindSendVerify('<?php echo $email?>');">立即发送</a></p>
	            	<?php elseif($step == 'send'):?>
	            		<P>说明：邮箱解绑后，您可能无法找回密码。</P>
             			<div class="sendbj fleft" style="width:auto"><div class="fleft">解绑验证邮件已发送到您的邮箱 <strong class="pink"><?php echo $email?></strong> ,请查收</div> <a href="<?php echo $mail_href?>" class="modify_1 ml20 fleft">前往邮箱</a></div>
             			<div class="safetips mt30 fleft"><strong style=" color:#000;">温馨提示：</strong><br/>
						               验证邮件7天内有效，请尽快完成验证。<br/>
						               邮件到达时间可能长达2-3分钟，请耐心等待。<br/>                   
						               如果长时间还未收到邮件，请检查垃圾邮件或者选择&nbsp;<a class="pink" href="javascript:void(0)" onclick="return retryUnBindVerify('<?php echo $email ?>');" id="send_mail">重发验证邮件</a> <a style="display:none;" href="javascript:void(0)" onclick="return false;" id="cal_time">60秒后重新发送</a>                    
               			</div>
               			
               		<?php elseif($step == 'verify'):?>
               		
               			 <?php if($verify):?>
				             <div class="sendbj">
               					 <div class="fleft">您的邮箱<strong class="pink"> <?php  echo $email ?></strong> 解绑成功！</div>
            				 </div>
            				 <div class="safetips mt30"><strong style=" color:#000;">温馨提示：</strong><br/>
               					忘记密码时，您可以通过绑定的邮箱找回密码。
               				 </div> 
            				 <p><a class="modify_1" href="<?php echo $this->createUrl('account/security&type=mail&step=bind') ?>">绑定邮箱</a></p>  
               			 <?php else:?>
        
		             		<div class="sendbj"><div class="fleft">抱歉，您的验证邮件已失效，请通过以下链接重新发送验证邮件！</div></div>
			             	<div class="safetips mt30"><strong style=" color:#000;">温馨提示：</strong><br/>
			              	 验证邮件7天内有效，请尽快完成验证。<br/>
			               	邮件到达时间可能长达2-3分钟，请耐心等待。<br/>
			               	如果长时间还未收到邮件，请检查垃圾邮件或者选择&nbsp;<a class="pink" href="javascript:void(0)" onclick="return retryUnBindVerify('<?php echo $email ?>');" id="send_mail">重发验证邮件</a> <a style="display:none;" href="javascript:void(0)" onclick="return false;" id="cal_time">60秒后重新发送</a>
			               </div>  
			             <?php endif;?>  
	          		<?php endif;?>
	          <?php elseif($type == 'public'):?>
	          		<div class="sendbj">
              			<div class="fleft"><strong class="pink"><?php echo Yii::app()->request->getParam('message')?></strong> </div>
             		</div>
              <?php endif;?>
         </div><!-- .cooper-list 账户安全 -->   
           
           <div class="cooper-list onhide">
           	  <input type="hidden" id="uid" name="uid" value="<?php echo $this->viewer['login_uid']?>" />
              <p>原密码&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="password" id="password" name="password"/></p>
              <p>新密码&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="password" id="newpswd" name="newpswd"/></p>
              <p>确认新密码&nbsp;&nbsp;<input type="password" id="renewpswd" name="renewpswd"></p>
              <p><a href="javascript:void(0)" ><img id="change_pswd"  src="<?php echo $this->pipiFrontPath;?>/fontimg/account/conf.jpg" style="display:block; margin-left:65px;" /></a></p>
           </div><!-- .cooper-list 修改密码 -->
            
      </div>
     </div>            
</div>
<script type="text/javascript">
$('#email').blur(
	function(){validEmail();}
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
 

function validEmail (){
	var flag = true;
	flag = $.empty('#email','#email_tip','邮箱不能为空');
	if(flag){
		flag = $.illegal('#email','#email_tip','昵称中有非法字符');
	}

	if(flag){
		flag = $.email('#email','#email_tip','邮件格式不合法');
	}
	if(flag){
		$.controllCss('#email_tip','','','');
		$('#email_tip').css({'color':''});
	}else{
		$('#email_tip').css({'color':'red','display':'inline'});
		$('#register_nickname').focus();
	}
	return flag;
}

function retryBindSend(email){
	if(!$.isEmail(email)){
		alert('邮件格式错误');
		return false;
	}
	$.ajax({
		type:"POST",
		url:"index.php?r=account/security&type=mail&step=doBindSendVerify",
		data:{email:email},
		dataType:"json",
		async: false, 
		success:function(response){
			if(response.status == 'fail'){
				alert(response.info);
				return false;
			}else{
				if(seconds <=0){
					seconds = 59;
				}
				countDown(seconds);
			}
		}
	});
	return false;
}

function retryUnBindVerify(email){
	if(!$.isEmail(email)){
		alert('邮件格式错误');
		return false;
	}

	$.ajax({
		type:"POST",
		url:"index.php?r=account/security&type=unMail&step=doUnBindSendVerify",
		data:{email:email},
		dataType:"json",
		async: false, 
		success:function(response){
			if(response.status == 'fail'){
				alert(response.info);
				return false;
			}else{
				if(seconds <=0){
					seconds = 59;
				}
				countDown(seconds);
			}
		}
	});
	return false;
}

function unBindSendVerify(email){
	
	if(!$.isEmail(email)){
		alert('邮件格式错误');
		return false;
	}

	$.ajax({
		type:"POST",
		url:"index.php?r=account/security&type=unMail&step=doUnBindSendVerify",
		data:{email:email},
		dataType:"json",
		async: false, 
		success:function(response){
			if(response.status == 'fail'){
				alert(response.info);
				return false;
			}else{
				window.location = "<?php echo $this->createUrl('account/security&type=unMail&step=send') ?>";
			}
		}
	});
	return false;
}

function doBindSendVerify(){
	var email = $('#email').val();
	if(validEmail()){
		$.ajax({
			type:"POST",
			url:"index.php?r=account/security&type=mail&step=doBindSendVerify",
			data:{email:email},
			dataType:"json",
			async: false, 
			success:function(response){
				if(response.status == 'fail'){
					$.controllCss('#email_tip','','error',response.info);
					$('#email_tip').css({'color':'red','display':'inline'});
				}else{
					window.location = "<?php echo $this->createUrl('account/security&type=mail&step=send') ?>";
				}
			}
		});
	}
	return false;
}
</script>
<script>
$('#phone').blur(
		function(){validSms();}
);

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
			url:"index.php?r=account/security&type=mobile&step=doBindSendVerify",
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
			url:"index.php?r=account/security&type=mobile&step=doBindSendVerify",
			data:{phone:phone},
			dataType:"json",
			async: false, 
			success:function(response){
				if(response.status == 'fail'){
					$.controllCss('#sms_tip','','error',response.info);
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


function bindMobile(){
	var phone = $('#phone').val();
	var code = $('#code').val();
	var flag = validSms();
	if(flag){
		flag = validCode();
	}
	if(flag){
		$.ajax({
			type:"POST",
			url:"index.php?r=account/security&type=mobile&step=bindMobile",
			data:{phone:phone,code:code},
			dataType:"json",
			async: false, 
			success:function(response){
				if(response.status == 'fail'){
					$.controllCss('#code_tip','','error',response.info);
					$('#code_tip').css({'color':'red','display':'inline'});
				}else{
					$.controllCss('#code_tip','','','');
					$('#code_tip').css({'color':'red','display':'none'});
					window.location = "<?php echo $this->createUrl('account/security&type=mobile&step=verify') ?>";
				}
			}
		});
	}
	return false;
}

function retryUnSetMobileSms(phone){

	if(!$.isInt(phone)){
		alert('手机号码格式错误');
		return false;
	}

	$.ajax({
		type:"POST",
		url:"index.php?r=account/security&type=unMobile&step=doUnBindSendVerify",
		data:{phone:phone},
		dataType:"json",
		async: false, 
		success:function(response){
			if(response.status == 'fail'){
				$.controllCss('#code_tip','','error',response.info);
				$('#code_tip').css({'color':'red','display':'inline'});
			}else{
				if(seconds <=0){
					seconds = 59;
				}
				countDown(seconds);
			}
		}
	});
	
}
function unSetMobileSms(phone){
	if(!$.isInt(phone)){
		alert('手机号码格式错误');
		return false;
	}

	$.ajax({
		type:"POST",
		url:"index.php?r=account/security&type=unMobile&step=doUnBindSendVerify",
		data:{phone:phone},
		dataType:"json",
		async: false, 
		success:function(response){
			if(response.status == 'fail'){
				$.controllCss('#code_tip','','error',response.info);
				$('#code_tip').css({'color':'red','display':'inline'});
			}else{
				
				$('#get_sms').css({'display':'none'});
				$('.retry_getsms').css({'display':''});
			}
		}
	});
	return false;

}

function unBindMobile(phone){
	if(!$.isInt(phone)){
		alert('手机号码格式错误');
		return false;
	}

	var code = $('#code').val();
	if(validCode()){
		$.ajax({
			type:"POST",
			url:"index.php?r=account/security&type=unMobile&step=unBindMobile",
			data:{phone:phone,code:code},
			dataType:"json",
			async: false, 
			success:function(response){
				if(response.status == 'fail'){
					$.controllCss('#code_tip','','error',response.info);
					$('#code_tip').css({'color':'red','display':'inline'});
				}else{
					$.controllCss('#code_tip','','','');
					$('#code_tip').css({'color':'red','display':'none'});
					window.location = "<?php echo $this->createUrl('account/security&type=unMobile&step=verify') ?>";
				}
			}
		});
	}
	return false;
}
</script>