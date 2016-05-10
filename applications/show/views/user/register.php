<div class="register" style="display:none;">
        	<span id="register_error"></span>	
        	<form id="form_register" action="/index.php?r=user/register" method="post" onsubmit="return false;">
            <p class="name logtext"><label class="fleft">账号</label><input name="username" id="register_username" type="text" value="输入账号"  class="fright"></p>
            <p class="user logtext"><label class="fleft">昵称</label><input name="nickname" id="register_nickname" type="text" value="输入昵称"  class="fright"></p>
            <p class="password logtext"><label class="fleft">密码</label><input name="password" id="register_password" type="password" value=""  class="fright"></p>
            <p class="aginpwd logtext"><label class="fleft">确认</label><input name="confirm_password" id="register_confirm_password" type="password" value=""  class="fright"></p>
            <p class="code logtext clearfix">
			<input name="code" id="register_code" type="text" value="验证码" class="fleft">
 			<?php 
            
				 $captchaParams  = array(
					'imageOptions'=>array('class'=>'fright','width'=>95,'height'=>40,'id'=>'register_code_img'),
					'clickableImage'=>true,
					'showRefreshButton'=>false,
					'captchaAction'=>'user/captcha'
				);
            	$this->widget('CCaptcha',$captchaParams); 
            ?>

			</p>
            <input class="loginbtn" type="submit" value="确&nbsp;&nbsp;认&nbsp;&nbsp;注&nbsp;&nbsp;册" id="regiserbtn"/>
            </form>
            <?php $this->renderPartial('application.views.user.openlogin'); ?>
        </div>
 

<script type="text/javascript">

$.UserRegister = {
		username_init : '输入账号',
		nickname_init : '输入昵称',
		password_init : '输入密码',
		confirm_password_init: '确认密码',
		code_init : '验证码',
		options : {
		   target: '',       //把服务器返回的内容放入id为output的元素中    
		   beforeSubmit: function(formData, jqForm, options){return $.UserRegister.showRequest(formData, jqForm, options);},  //提交前的回调函数
		   success: function(responseText, statusText){$.UserRegister.showResponse(responseText, statusText);},      //提交后的回调函数
		   //url: url,                 //默认是form的action， 如果申明，则会覆盖
		   //type: type,               //默认是form的method（get or post），如果申明，则会覆盖
		   dataType: 'json',         
		   clearForm: false,            //成功提交后，清除所有表单元素的值
		   //resetForm: true,          //成功提交后，重置所有表单元素的值
		   timeout: 3000               //限制请求的时间，当请求大于3秒后，跳出请求
		},

		bindEvent : function(){
			var _this = this;
			$("#register_password").live('focus',function(){
				if($.isEqual($('#register_password').val(),_this.password_init)){
					$(this).val('');
				}
			});

			$("#register_username").live('focus',function(){
				if($.isEqual($('#register_username').val(),_this.username_init)){
					$(this).val('');
				}
			});

			$("#register_nickname").live('focus',function(){
				if($.isEqual($('#register_nickname').val(),_this.nickname_init)){
					$(this).val('');
				}
			});
			
			$("#register_code").live('focus',function(){
				if($.isEqual($('#register_code').val(),_this.code_init)){
					$(this).val('');
				}
			});

			$("#register_username").live('blur',function(){
				_this.validUserName();
				
			});
			$("#register_nickname").live('blur',function(){
				_this.validNickName();
			});
			
			$("#register_password").live('blur',function(){
				_this.validPassWord();
			});
			$("#register_confirm_password").live('blur',function(){
				_this.validConfirmPassword();
			});
			$("#register_code").live('blur',function(){
				_this.validCode();
				
			});

			$("#form_register").submit(function(){
				if($.UserRegister.validPassWord() && $.UserRegister.validConfirmPassword()){
					$('#register_password').val($.md5($('#register_password').val()));
					$('#register_confirm_password').val($.md5($('#register_confirm_password').val()));
					$(this).ajaxSubmit(_this.options);
				}
				return false;  
						
			});

		},
		
		validUserName : function (){
			var flag = true;
			flag = $.empty('#register_username','#register_error','账号不能为空');

			if(flag){
				flag = $.reverseEqual($('#register_username').val(),this.username_init,'#register_error','账号不能为空');
			}

			if(flag){
				flag = $.pipiUserName('#register_username','#register_error','账号由4-15个字母、数字、下划线组成');
			}
			if(flag){
				$.controllCss('#register_error','','','');
			}else{
				//$('#register_username').focus();
			}

			
			return flag;
		},

		validNickName : function(){
			var flag = true;
			flag = $.empty('#register_nickname','#register_error','昵称不能为空');
			if(flag){
				flag = $.reverseEqual($('#register_nickname').val(),this.nickname_init,'#register_error','昵称不能为空');
			}

			if(flag){
				flag = $.illegal('#register_nickname','#register_error','昵称中有非法字符');
			}

			if(flag){
				flag = $.len($('#register_nickname').val().length,2,16,'#register_error','昵称在2-16个字符之间');
			}
			if(flag){
				$.controllCss('#register_error','','','');
			}else{
				//$('#register_nickname').focus();
			}
			return flag;
		},

		validPassWord : function (){
			var error = $('#register_error');
			var flag = true;
			flag = $.empty('#register_password','#register_error','密码不能为空');
			if(flag){
				flag = $.reverseEqual($('#register_password').val(),this.password_init,'#register_error','密码不能为空');
			}

			if(flag){
				//flag = $.pipiPassWord('#register_password','#register_error','密码必须同时包括数字和字母');
			}

			if(flag){
				flag = $.len($('#register_password').val().length,4,20,'#register_error','密码长度必须在4-20个字符之间');
			}
			
			if(flag){
				$.controllCss('#register_error','','','');
			}else{
				//$('#register_password').focus();
			}
			return flag;
		},

		validConfirmPassword:function(){
			var error = $('#register_error');
			var flag = true;
			return flag = $.equal($('#register_password').val(),$('#register_confirm_password').val(),'#register_error','确认密码不相等');
			
		},

		validCode : function (){

			var error = $('#register_error');
			var flag = true;
			flag = $.empty('#register_code','#register_error','验证码不能为空');
			if(flag){
				flag = $.reverseEqual($('#register_code').val(),this.code_init,'#register_error','验证码不能为空');
			}

			if(flag){
				$.controllCss('#register_code','','','');
			}else{
				//$('#register_code').focus();
			}
			return flag;
		},

		showRequest : function (formData, jqForm, options){
			var domForm = jqForm[0];
			var flag = true;;
			flag = this.validUserName();

			if(flag){
				flag = this.validNickName();
			}
			
//			if(flag){
//				flag = this.validPassWord();
//			}

//			if(flag){
//				flag = this.validConfirmPassword();
//			}
			if(flag){
				flag = this.validCode();
			}
			return flag;
			
		},

		showResponse : function (responseText, statusText){
			if(responseText.status == 'fail'){
				$.controllCss('#register_error','','error',responseText.message);
				$('#register_code_img').click();
				$('#register_code').val('');
				$('#register_password').val('');
				$('#register_confirm_password').val('');
			}else{
				$.controllCss('#register_error','','','');
				var uid=responseText.data;
				$.cookie('new_register_user',uid,{path: '/',domain:cookie_domain,expire:360000});
				var sign=$.Global.getParam('sign');
				if(sign=='' || sign == null){
					location.reload();
				}else{
					var thref =  location.href;
					thref = thref.substr(0,thref.indexOf('sign'));
					threfLengh = thref.length;
					var lastChar = thref[threfLengh-1];
					if(lastChar == '?'){
						thref = thref.substr(0,thref.indexOf('?'));
					}else if(lastChar == '&'){
						thref = thref.substr(0,thref.indexOf('&'));
					}
					$.cookie('reg_sign',null);
					$.cookie('reg_referer',null);
					window.location.href = thref;
					
				}
			}
		}
			
};

</script>