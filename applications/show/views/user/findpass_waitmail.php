<div class="clearfix w1000 mt30">
    
    <div class="main" style="width:1000px;">
        <ul class="main-menu clearfix" id="MianList" style="width:960px;">
            <li class="menuvisted"><a href="javascript:void(0)">找回密码</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
              <div class="sendbj" style="height:auto; width:520px;width:auto;">
            		  找回密码验证邮件已发送到您的邮箱 <a><?php echo $protected_email?></a> ,请查收<a href="<?php echo $mail_href?>" class="modify_1 ml20">前往邮箱</a>
              </div>
              <div class="safetips mt30"><strong style=" color:#000;">温馨提示：</strong><br/>
			               验证邮件7天内有效，请尽快完成验证。<br/>
			               邮件到达时间可能长达2-3分钟，请耐心等待。<br/>                   
			               如果长时间还未收到邮件，请检查垃圾邮件或者选择&nbsp;<a class="pink" href="javascript:void(0)" onclick="return retryBindSend();" id="send_mail">重发验证邮件</a> <a style="display:none;" href="javascript:void(0)" onclick="return false;" id="cal_time">60秒后重新发送</a>                    
			  </div>  
           </div> 
           <div class="cooper-list onhide"></div>
      </div>
     </div>       
</div>
<script type="text/javascript">
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

function retryBindSend(){
	$.ajax({
		type:"POST",
		url:"index.php?r=user/find&type=mail",
		dataType:"json",
		async: false, 
		success:function(response){
			if(response.status == 'fail'){
				alert(response.message);
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
</script>