<div id="LoginMask">
    <div class="partloginbox" id="LoginBox">
        <div class="loginbox" id="loginController">
        <form id="form_login" action="/index.php?r=user/login" method="post" onsubmit="return false;">
            <dl class="logincon entryArea">
                <dt>登录 - 经常登录的，都是主播惦念的小伙伴！</dt>
                <dd>
                    <label>用户名</label>
                    <input name="username" id="login_username" type="text" value="输入账号" dataType="*4-20" nullmsg="请填写用户名" errormsg="限4~20字符" onblur="if(value==''){value='输入账号'}" onfocus="if(value=='输入账号'){value=''}">
                    <p class="pink"><em></em><span></span></p>
                </dd>
                <dd>
                    <label>密码</label>
                    <input name="password" id="login_password" type="password" value="输入密码" dataType="*4-20" nullmsg="请填写密码" errormsg="限4~20位">
                	<p class="pink"><em></em><span></span></p>
                </dd>
                <dd class="rember">
                    <label><input name="remember" id="remember" type="checkbox" value="1">&nbsp;记住登录状态</label>
                    <a href="<?php echo $this->createUrl('user/findPass') ?>" target="<?php echo $this->target?>">忘记密码？</a>
                </dd>
                <dd class="code">
                    <label>验证码</label>
                    <input name="code" id="login_code" type="text" value="验证码" nullmsg="请填写验证码" errormsg="" onblur="if(value==''){value='验证码'}" onfocus="if(value=='验证码'){value=''}" datatype="*" nullmsg="请填写验证码">
                    <?php 
	            		$captchaParams  = array(
							'imageOptions'=>array('class'=>'fright','width'=>65,'height'=>40,'id'=>'login_code_img','title'=>'看不清换一张'),
							'clickableImage'=>true,
							'showRefreshButton'=>true,
							'buttonLabel'=>'看不清换一张',
							'captchaAction'=>'user/captcha'
						);
		            	$this->widget('CCaptcha',$captchaParams); 
	            	?>
                    <p class="pink"><em></em><span></span></p>
                </dd>
                <dd class="btn">
                	<input class="forbtn loginbtn DD_belapng" value="立即登录" type="submit" id="loginbtn"/>  
                </dd>
            </dl><!--.entryArea-->
            </form>
            <form id="form_register" action="/index.php?r=user/register" method="post" onsubmit="return false;">
            <dl class="logincon registerArea">
                <dt>10秒注册 - 开始与主播聊天互动！</dt>
                <dd>
                    <label>用户名</label>
                    <input name="username" id="register_username" type="text" value="输入账号" dataType="username" nullmsg="请填写用户名" errormsg="限4~15字的中文、英文、数字组成" onblur="if(value==''){value='输入账号'}" onfocus="if(value=='输入账号'){value=''}">
                    <p class="pink"><em></em><span>限4~15字的中文、英文、数字组成</span></p>
                </dd>
                <dd class="aline">
                    <label>创建密码</label>
                    <input name="password" id="register_password" type="password" value="" dataType="*4-20" nullmsg="请填写密码" errormsg="限4~20位">
                    <p class="pink"><em></em><span>限4~20位</span></p>
                </dd>
                <dd class="aline">
                    <label>确认密码</label>
                    <input name="confirm_password" id="register_confirm_password" type="password" value="" datatype="*" recheck="password" nullmsg="请再输入一次密码！" errormsg="您两次输入的密码不一致">
                    <p class="pink"><em></em><span></span></p>
                </dd>
                <dd class="code aline">
                    <label>验证码</label>
                    <input name="code" id="register_code" type="text" value="验证码" onblur="if(value==''){value='验证码'}" onfocus="if(value=='验证码'){value=''}" datatype="*" nullmsg="请填写验证码">
                    <?php 
            			$captchaParams  = array(
							'imageOptions'=>array('class'=>'fright','width'=>65,'height'=>40,'id'=>'register_code_img','title'=>'看不清换一张'),
							'clickableImage'=>true,
							'showRefreshButton'=>true,
							'buttonLabel'=>'看不清换一张',
							'captchaAction'=>'user/captcha'
						);
		            	$this->widget('CCaptcha',$captchaParams); 
		            ?>
                    <p class="pink"><em></em><span></span></p>
                </dd>
                <dd class="clause">
                    <label><input name="agree" id="agree" type="checkbox" checked value="1">&nbsp;我已阅读并同意</label>
                    <a href="#">《皮皮乐天服务条例》</a>
                    <p class="pink"><em></em><span></span></p>
                </dd>
                <dd class="btn">
                	<input class="forbtn loginbtn DD_belapng" type="submit" value="确认注册" id="regiserbtn"/>
                </dd>
            </dl><!--.registerArea-->
            </form>
            <div class="login-r">
                <p class="Ttext">已有账号</p>
                <p><a class="forbtn regetbtn DD_belapng" href="javascript:void(0);">直接登陆</a></p>
                <p class="otherlog">
                    <span>其他账号登陆</span>
                    <a href="<?php $this->getTargetHref('index.php?r=user/openLogin&type=qq')?>" target="<?php echo $this->target?>"><img src="<?php echo $this->pipiFrontPath.'/fontimg/common/';?>/logQQ.png"></a>
                </p>
            </div><!--..login-r-->
            <a href="javascript:void(0);" class="close"></a>
			<div class="warnLog"><p><em class="warnicon DD_belapng"></em></p></div>
        </div><!--.loginbox-->
    </div>
</div><!--#LoginMask-->
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
var curLoginController = 'register';
$(function(){
	$('.login-r .regetbtn').click(function(){
		var innerchar=$('.login-r .regetbtn').text();
		if(innerchar=="直接登陆"){
			$('#login_code_img').click();
			$('.registerArea').css('display','none');
			$('.entryArea').css('display','block');
			$('.login-r .regetbtn').text("快速注册");
			$('.Ttext').text('没有账号？');
			$('#login_username').val('输入账号');
			$('#login_username').parent().find("p").removeClass().addClass('pink');
			$('#login_username').parent().find("span").text('');
			$('#login_password').val('输入账号');
			$('#login_password').parent().find("p").removeClass().addClass('pink');
			$('#login_password').parent().find("span").text('');
		}else if(innerchar=="快速注册"){
			$('#register_code_img').click();
			$('.entryArea').css('display','none');
			$('.registerArea').css('display','block');
			$('.login-r .regetbtn').text("直接登陆");
			$('.Ttext').text('已有账号');
			$('#register_username').val('输入账号');
			$('#register_username').parent().find("p").removeClass().addClass('pink');
			$('#register_username').parent().find("span").text('限4~15字的中文、英文、数字组成');
			$('#register_password').val('');
			$('#register_confirm_password').val('');
			$('#register_password').parent().find("p").removeClass().addClass('pink');
			$('#register_password').parent().find("span").text('限4~20位');
			$('#register_confirm_password').parent().find("p").removeClass().addClass('pink');
			$('#register_confirm_password').parent().find("span").empty();
			$('#register_code').val('验证码');
			$('#register_code').parent().find("p").removeClass().addClass('pink');
			$('#register_code').parent().find("span").empty();
		}	
	});
	$('.loginbox .close').click(function(){
		$.loginmask.hide('LoginBox');
		if(page_controller =='archives'){
			doteyPrivateBox();
		}
	})
	$('.entryArea input').focus(function(){
		$('.warnLog').slideUp('slow');
	})
	var loginFrom=$("#form_login").Validform({
		ajaxPost:true,
		tiptype:function(msg,o,cssctl){
			var objtip=o.obj.parent().find("p");
			if(o.type==2){
				objtip.removeClass().addClass('right');
				objtip.find('span').empty();
			}else if(o.type==3){
				objtip.removeClass().addClass('error');
				cssctl(objtip,o.type);
				objtip.find('span').text(msg);
			}
		},
		callback:function(data){
			if(data.status=='success'){
				location.reload();
			}else if(data.status=='valid_phone'){
				$.UserLogin.uid=data.data.uid;
				$.UserLogin.username=data.data.username;
				$.UserLogin.password=data.data.password;
				$.UserLogin.remember=data.data.remember;
				$.loginmask.hide('loginController');
				$.mask.show('TelVail');	
			}else{
				if(data.code==1){
					var objtip=$('#login_username').parent().find("p");
					objtip.removeClass().addClass('error');
					objtip.find('span').text(data.message);
				}else if(data.code==2){
					var objtip=$('#login_password').parent().find("p");
					$('#login_password').val('');
					$('#login_code').val('');
					objtip.removeClass().addClass('error');
					objtip.find('span').text(data.message);
				}else if(data.code==4){
					var objtip=$('#login_code').parent().find("p");
					$('#login_password').val('');
					$('#login_code').val('');
					objtip.removeClass().addClass('error');
					objtip.find('span').text(data.message);
				}else if(data.code==5){
					$('.warnLog em').after(data.message);
					$('.warnLog').slideDown('fast');
					setTimeout(function(){
						$('.warnLog').slideUp('slow');
					},50000);
				}
				$('#login_code_img').click();
			}
		},
		beforeSubmit:function(obj){
			$('#login_password').val($.md5($('#login_password').val()));
		}
		
	});
	var registerFrom=$("#form_register").Validform({
			ajaxPost:true,
			datatype:{
				username:function(gets,obj,curform,regxp){
					var reg1=/^[a-zA-Z\u4E00-\u9FA5\uf900-\ufa2d][\w\u4E00-\u9FA5\uf900-\ufa2d]{3,14}$/;
					if(reg1.test(gets)){return true;}
					return false;
				}
			},
			tiptype:function(msg,o,cssctl){
				var objtip=o.obj.parent().find("p");
				if(o.type==2){
					objtip.removeClass().addClass('right');
					objtip.find('span').empty();
				}else if(o.type==3){
					objtip.removeClass().addClass('error');
					cssctl(objtip,o.type);
					objtip.find('span').text(msg);
				}
			},
			callback:function(data){
				if(data.status=='success'){
					location.reload();
				}else{
					if(data.code==1){
						var objtip=$('#register_username').parent().find("p");
						objtip.removeClass().addClass('error');
						objtip.find('span').text(data.message);
					}else if(data.code==2){
						var objtip=$('#register_password').parent().find("p");
						$('#register_password').val('');
						$('#register_confirm_password').val('');
						$('#register_code').val('');
						objtip.removeClass().addClass('error');
						objtip.find('span').text(data.message);
					}else if(data.code==4){
						var objtip=$('#register_code').parent().find("p");
						$('#register_password').val('');
						$('#register_confirm_password').val('');
						$('#register_code').val('');
						objtip.removeClass().addClass('error');
						objtip.find('span').text(data.message);
					}else if(data.code==5){
						$('.warnLog em').after(data.message);
						$('.warnLog').slideDown('fast');
						setTimeout(function(){
							$('.warnLog').slideUp('slow');
						},50000);
					}
					$('#register_code_img').click();
				}
			},
			beforeSubmit:function(obj){
				if($("#agree").attr("checked")==''||$("#agree").attr("checked")==undefined){
					alert('请同意服务条款');
					return false;
				}
				$('#register_password').val($.md5($('#register_password').val()));
				$('#register_confirm_password').val($.md5($('#register_confirm_password').val()));
			}
		});
});
</script>
<script>
var timerID;
$.UserLogin = {
		target:{},
		getPhoneCode:function(){
			var obj=this;
			$.ajax({
				type:'POST',
				url:'index.php?r=user/getPhoneCode',
				data:{uid:obj.uid},
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
					data:{code:code,username:obj.username,password:obj.password,remember:obj.remember},
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