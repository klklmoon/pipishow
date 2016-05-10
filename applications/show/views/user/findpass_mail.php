<div class="clearfix w1000 mt30">
    
    <div class="main" style="width:1000px;">
        <ul class="main-menu clearfix" id="MianList" style="width:960px;">
            <li class="menuvisted"><a href="javascript:void(0)">找回密码</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
              <p>绑定邮箱： &nbsp;<input name="email" id="email" type="text"> &nbsp;&nbsp;<a class="pink" id="email_tip">提示：您绑定的邮箱后缀为<strong> @<?php echo $suffix?></strong></a> </p>
              <p><a href="javascript:void(0)" class="modify_1 ml68" onclick="return findPass('mail');">下一步</a> &nbsp;&nbsp; <a href="<?php echo $this->createUrl('user/findPass&step=kefu') ?>" class="pink ml55">通过客服找回密码</a></p>
           </div><!-- .cooper-list 账户安全 -->   
           
           <div class="cooper-list onhide">
           </div><!-- .cooper-list 修改密码 -->
            
      </div>
     </div>       
</div>
<script type="text/javascript">
$('#email').blur(
		function(){validEmail();}
);

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
		$('#email_tip').addClass('pink');
	}
	return flag;
}
function findPass(type){
	var email = $('#email').val();
	if(validEmail()){
		$.ajax({
			type:"POST",
			url:"index.php?r=user/find",
			data:{type:type,email:email},
			dataType:"json",
			async: false, 
			success:function(response){
				if(response.status == 'fail'){
					$.controllCss('#email_tip','','pink',response.message);
				}else{
					$.controllCss('#email_tip','','','');
					window.location = "<?php echo $this->createUrl('user/findPass&step=waitMail') ?>";
				}
			}
		});
	}
	return false;
}

</script>