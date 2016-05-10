 <div class="loginin" style="display:none;">
       		<span id="login_error"></span>	
        	<form id="form_login" action="/index.php?r=user/login" method="post" onsubmit="return false;">
            <p class="name logtext"><input name="username" id="login_username" type="text" value="输入账号"></p>
            <p class="password logtext"><input name="password" id="login_password" type="password" value="输入密码"></p>
            <p class="code logtext clearfix"><input name="code" class="fleft" id="login_code" type="text" value="验证码">
            <?php 
            
				 $captchaParams  = array(
					'imageOptions'=>array('class'=>'fright','width'=>95,'height'=>40,'id'=>'login_code_img'),
					'clickableImage'=>true,
					'showRefreshButton'=>false,
					'captchaAction'=>'user/captcha'
				);
            $this->widget('CCaptcha',$captchaParams); 
            ?></p>
           	<p class="login-sate logtext"><label class="fleft"><input name="remember" id="remember" type="checkbox" value="1">记住登录状态</label><a class="fright" href="<?php echo $this->createUrl('user/findPass') ?>" title="忘记密码?" target="<?php echo $this->target?>">忘记密码?</a></p>
            <input class="loginbtn" value="登录" type="submit" id="loginbtn"/>            
             <?php $this->renderPartial('application.views.user.openlogin'); ?>
            </form>
 </div>
<div class="popbox telvail" id="TelVail">
    <div class="poph">
        <span>手机安全登录验证</span>
        <a onclick="$.mask.hide('TelVail');" class="closed" title="关闭"></a>
    </div>
    <div class="popcon uselive">
        <p>此账号已申请手机安全登录验证保护，请在下面对话框输入发送到您手机的验证码</p>
        <p><label>验证码</label><input type="text" value="" style="width:160px;" id="code" class="intext"><a class="vailbtn" href="javascript:void(0);" onclick="$.UserLogin.getPhoneCode()">获取验证码</a><span>（手机验证码已发送至您的手机，请在<em class="pink">180</em>秒内输入验证码）</span></p>
        <p><input class="shiftbtn" type="button" value="确  定" onclick="$.UserLogin.validPhoneCode()"><label class="error"></label></p>
    </div>
</div>
<script type="text/javascript">
var timerID;
$.UserLogin = {
		target:{},
		username_init : '输入账号',
		password_init : '输入密码',
		code_init : '验证码',
		options : {
		   target: '',       //把服务器返回的内容放入id为output的元素中    
		   beforeSubmit: function(formData, jqForm, options){return $.UserLogin.showRequest(formData, jqForm, options);},  //提交前的回调函数
		   success: function(responseText, statusText){$.UserLogin.showResponse(responseText, statusText);},      //提交后的回调函数
		   //url: url,                 //默认是form的action， 如果申明，则会覆盖
		   //type: type,               //默认是form的method（get or post），如果申明，则会覆盖
		   dataType: 'json',         
		   clearForm: false,            //成功提交后，清除所有表单元素的值
		   //resetForm: true,          //成功提交后，重置所有表单元素的值
		   timeout: 3000               //限制请求的时间，当请求大于3秒后，跳出请求
		},

		bindEvent : function(){
			var _this = this;
			$("#login_password").live('focus',function(){
				if($.isEqual($('#login_password').val(),_this.password_init)){
					$(this).val('');
				}
			});

			$("#login_username").live('focus',function(){
				if($.isEqual($('#login_username').val(),_this.username_init)){
					$(this).val('');
				}
			});

			$("#login_code").live('focus',function(){
				if($.isEqual($('#login_code').val(),_this.code_init)){
					$(this).val('');
				}
			});

			$("#login_username").live('blur',function(){
				_this.validLoginUserName();
			});


			$("#login_password").live('blur',function(){
				_this.validLoginPassWord();
			});

			$("#login_code").live('blur',function(){
				_this.validLoginCode();
				
			});

			$("#form_login").submit(function(){
				if($.UserLogin.validLoginPassWord()){
					$('#login_password').val($.md5($('#login_password').val()));
					$(this).ajaxSubmit(_this.options);
				}
				return false;  
			});

		},
		
		validLoginUserName : function (){
			var flag = true;
			flag = $.empty('#login_username','#login_error','账号不能为空');

			if(flag){
				flag = $.reverseEqual($('#login_username').val(),this.username_init,'#login_error','账号不能为空');
			}
			if(flag){
				$.controllCss('#login_error','','','');
			}else{
				//$('#login_username').focus();
			}
			return flag;
		},

		validLoginPassWord : function (){
			var error = $('#login_error');
			var flag = true;
			flag = $.empty('#login_password','#login_error','密码不能为空');

			if(flag){
				flag = $.reverseEqual($('#login_password').val(),this.password_init,'#login_error','密码不能为空');
			}
			
			if(flag){
				$.controllCss('#login_error','','','');
			}else{
				//$('#login_password').focus();
			}
			return flag;
		},

		validLoginCode : function (){

			var error = $('#login_error');
			var flag = true;
			flag = $.empty('#login_code','#login_error','验证码不能为空');
			if(flag){
				flag = $.reverseEqual($('#login_code').val(),this.code_init,'#login_error','验证码不能为空');
			}

			if(flag){
				$.controllCss('#login_code','','','');
			}else{
				//$('#login_code').focus();
			}
			return flag;
		},

		showRequest : function (formData, jqForm, options){
			var domForm = jqForm[0];
			var flag = true;;
			flag = this.validLoginUserName();
//			if(flag){
//				flag = this.validLoginPassWord();
//			}
			if(flag){
				flag = this.validLoginCode();
			}
			return flag;
			
		},

		showResponse : function (responseText, statusText){
			if(responseText.status == 'fail'){
				$.controllCss('#login_error','','error',responseText.message);
				$('#login_code_img').click();
				$('#login_code').val('');
				$('#login_password').val('');
			}else if(responseText.status =='valid_phone'){
				this.target.uid=responseText.data.uid;
				this.target.username=responseText.data.username;
				this.target.password=responseText.data.password;
				this.target.remember=responseText.data.remember;
				$.mask.hide('loginController');
				$.mask.show('TelVail');	
			}else{
				$.controllCss('#login_error','','','');
				location.reload();
			}
		},
		getPhoneCode:function(){
			var obj=this;
			$.ajax({
				type:'POST',
				url:'index.php?r=user/getPhoneCode',
				data:{uid:obj.target.uid},
				dataType:'JSON',
				success:function(data){
					if(data.flag==0){
						$('.vailbtn').css('display','block').siblings('span').css('display','none');
						$("#TelVail .error").html(data.message);
					}else{
						$('.vailbtn').css('display','none').siblings('span').css('display','block');
						$.UserLogin.startClock();	
				   }
				}
			})
			
		},
		startClock:function(){
			var timeLeft=180;
			if(timerID){
				clearTimeout(timerID);
			} 
			start();
			function start(){
				if(timeLeft>0){
					timerID = setTimeout(start,1000);
					timeLeft=timeLeft-1;
					$('#TelVail .pink').text(timeLeft);
					$("#TelVail .error").empty();  
				}else{
					$('.vailbtn').html('重新获取验证码');
					$("#TelVail .error").empty();
					$('.vailbtn').css('display','block').siblings('span').css('display','none');
					clearTimeout(timerID); 
				}
				
			}
		},
		validPhoneCode:function(){
			var obj=this;
			var code=$("#TelVail #code").val();
			if(code==null||code==''){
				$("#TelVail .error").html('请填写验证码');
				return false;
			}else{
				$.ajax({
					type:'POST',
					url:'index.php?r=user/validPhoneCode',
					data:{code:code,username:obj.target.username,password:obj.target.password,remember:obj.target.remember},
					dataType:'JSON',
					success:function(data){
						if(data.flag==0){
							$("#TelVail .error").html(data.message);
							$('.vailbtn').html('重新获取验证码');
							$('.vailbtn').css('display','block').siblings('span').css('display','none');
							clearTimeout(timerID); 
						}else{
							location.reload();
						}
					}

				})
			}
			
		}
			
};

</script>