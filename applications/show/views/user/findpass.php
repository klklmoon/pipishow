 <div class="clearfix w1000 mt30">
 <div class="main" style="width:1000px;">
        <ul class="main-menu clearfix" id="MianList" style="width:960px;">
            <li class="menuvisted"><a href="javascript:void(0)">找回密码</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
             <p>用户名： &nbsp;<input name="username" id="username" type="text"/> &nbsp;&nbsp;<a class="pink" id="username_tip">请输入您皮皮用户名</a></p>
             <p>验证码： &nbsp;<input name="code" id="code" type="text" style="width:150px;"/> &nbsp;&nbsp;<a class="pink" id="code_tip"></a>
             	<?php 
				 $captchaParams  = array(
					'imageOptions'=>array('width'=>95,'height'=>40,'id'=>'login_code_img','style'=>'vertical-align:middle;'),
					'clickableImage'=>true,
					'showRefreshButton'=>false,
					'captchaAction'=>'user/captcha'
				);
            	$this->widget('CCaptcha',$captchaParams); 
            ?>
              </p>
             <p><a href="javascript:void(0)" class="modify_1 ml55" onclick="return findUserName();">下一步</a></p>
           </div>
           <div class="cooper-list onhide"></div>
      </div>
     </div>
</div>
<script type="text/javascript">
$('#username').blur(
		function(){validUsername();}
);

$('#code').blur(
		function(){validCode();}
);

function validUsername (){
	var flag = true;
	flag = $.empty('#username','#username_tip','用户名称不能为空');
	if(flag){
		$.controllCss('#username_tip','','','');
	}else{
		$('#username_tip').addClass('pink');
	}
	return flag;
}

function validCode(){
	var flag = true;
	flag = $.empty('#code','#code_tip','验证码不能为空');

	if(flag){
		$.controllCss('#code_tip','','','');
	}else{
		$('#code_tip').addClass('pink');
	}
	return flag;
}

function findUserName(){
	var username = $('#username').val();
	var code = $('#code').val();
	var flag = validUsername();
	if(flag){
		flag = validCode();
	}
	if(flag){
		$.ajax({
			type:"POST",
			url:"index.php?r=user/findUserName",
			data:{username:username,code:code},
			dataType:"json",
			async: false, 
			success:function(response){
				if(response.status == 'fail'){
					if(response.type == 'username'){
						$.controllCss('#username_tip','','pink',response.message);
					}else if(response.type == 'code'){
						$.controllCss('#code_tip','','pink',response.message);
						$('#login_code_img').click();
					}
				}else{
					$.controllCss('#code_tip','','','');
					window.location = "<?php echo $this->createUrl('user/findPass&step=method') ?>";
				}
			}
		});
	}
	return false;
}
</script>