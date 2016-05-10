<div class="apply_box clearfix">

<div class="w1000">

<div class="apply_content clearfix">
	
    	<h3>申请成为主播</h3>
    	<ul class="info_content clearfix">
    		<?php if(!empty($error)){?>
            <li style="height:auto;">
              <label></label>
              <div class="short_filed" style="color:red;">
              	<div style="padding:0 30px;">
              	<?php foreach($error as $e){?>
              	* <?php echo $e[0];?><br/>
              	<?php }?>
              	</div>
              </div>
            </li>
            <?php }?>
            <?php if($isLogin){ ?>
    		<form id="applyForm" action="<?php echo $this->createUrl('dotey/apply', $edit ? array('edit' => 1) : array());?>" method="POST" enctype="multipart/form-data">
       	    <li>
       	      <label><span class="tips">*</span>昵称 ：</label>
       	      <div class="short_filed"><?php echo $user['nickname'];?></div>
       	    </li>
       	    <?php }else{ ?>
       	    <li>
       	      <label><span class="tips">*</span></label>
       	      <div class="short_filed"><a href="javascript:$.User.loginController('login');" style="color:#FF0099;">请先登录，再可填写申请表</a></div>
       	    </li>
       	    <?php } ?>
       	    <?php if(!empty($proxy)){ ?>
            <li>
              <label><span class="tips">*</span>代理签约 ：</label>
              <div class="short_filed"><?php echo $proxy['agency'];?></div>
            </li>
            <input type="hidden" name="p" value="<?php echo $proxy['uid'];?>" />
            <?php } ?>
            <?php if(!empty($finder)){ ?>
            <li>
              <label><span class="tips">*</span>星探签约 ：</label>
              <div class="short_filed"><?php echo $finder['agency'];?></div>
            </li>
            <input type="hidden" name="f" value="<?php echo $finder['uid'];?>" />
            <?php } ?>
            <li>
              <label><span class="tips">*</span>姓名 ：</label>
              <div class="short_filed"><input class="text" name="realname" type="text" maxlength=20 value="<?php echo isset($applyInfo['realname']) ? $applyInfo['realname'] : '';?>" <?php echo $isLogin ? '' : 'disabled="disabled"'; ?> /></div>
            </li>  
            <li>
              <label><span class="tips">*</span>性    别 ：</label>
              <div class="short_filed">
              	&nbsp;<input type="radio" name="gender" class="gender" value="1" <?php echo isset($applyInfo['gender']) && $applyInfo['gender'] == 1 ? "checked" : "";?> <?php echo $isLogin ? '' : 'disabled="disabled"'; ?> />&nbsp;男  &nbsp;&nbsp;&nbsp;&nbsp;
              	&nbsp;<input type="radio" name="gender" class="gender" value="2" <?php echo isset($applyInfo['gender']) && $applyInfo['gender'] == 2 ? "checked" : "";?> <?php echo $isLogin ? '' : 'disabled="disabled"'; ?> />&nbsp;女
              </div>
            </li>
            <li>
              <label><span class="tips">*</span>手机联系 ：</label>
              <div class="short_filed"><input name="mobile" class="text" type="text" maxlength=20 value="<?php echo isset($applyInfo['mobile']) ? $applyInfo['mobile'] : '';?>" <?php echo $isLogin ? '' : 'disabled="disabled"'; ?> /></div>
            </li>
            <li>
              <label><span class="tips">*</span>常用QQ ：</label>
              <div class="short_filed"><input name="qq" class="text" type="text" maxlength=20 value="<?php echo isset($applyInfo['qq']) ? $applyInfo['qq'] : '';?>" <?php echo $isLogin ? '' : 'disabled="disabled"'; ?> /></div>
            </li>
            <li>
              <label><span class="tips">*</span>身份证号 ：</label>
              <div class="short_filed"><input name="id_card" class="text" type="text" maxlength=20 style="width:210px;" value="<?php echo isset($applyInfo['id_card']) ? $applyInfo['id_card'] : '';?>" <?php echo $isLogin ? '' : 'disabled="disabled"'; ?> /></div>
            </li>
            <li>
              <label><span class="tips">*</span>开户姓名 ：</label>
              <div class="short_filed"><input name="bank_user" class="text" type="text" maxlength=20 style="width:210px;" value="<?php echo isset($applyInfo['bank_user']) ? $applyInfo['bank_user'] : '';?>" <?php echo $isLogin ? '' : 'disabled="disabled"'; ?> /></div>
            </li>
            <li>
              <label><span class="tips">*</span>开户银行 ：</label>
              <div class="short_filed"><input name="bank" class="text" type="text" maxlength=20 style="width:210px;" value="<?php echo isset($applyInfo['bank']) ? $applyInfo['bank'] : '';?>" <?php echo $isLogin ? '' : 'disabled="disabled"'; ?> /></div>
            </li>
            <li>
              <label><span class="tips">*</span>银行卡号 ：</label>
              <div class="short_filed"><input name="bank_account" class="text" type="text" maxlength=20 style="width:210px;" value="<?php echo isset($applyInfo['bank_account']) ? $applyInfo['bank_account'] : '';?>" <?php echo $isLogin ? '' : 'disabled="disabled"'; ?> /></div>
            </li>
            
            <li style="height: auto;">
              <label style="float:left;"><span class="tips">*</span>节目封面 ：</label>
              <div class="short_filed">
              	<?php if($isLogin){?>
              	<?php if(!empty($applyInfo['cover'])){?>
              	<img id="cover_image" src="<?php echo $applyInfo['cover'];?>?<?php echo rand(100000, 999999)?>" />
              	<?php }?>
              	<span id = 'btn_cover'></span><br />
              	<input type='button' id='btn_edit' value='修改节目封面' style="display:<?php if(!empty($applyInfo['cover'])){?>block<?php }else{?>none<?php }?>;" />
              	<div id="showFlash" style="display:<?php if(!empty($applyInfo['cover'])){?>none<?php }else{?>block<?php }?>;"><?php echo $flashHtml;?></div>
              	<?php }else{?>
              	<input type="button" id="btn_cover" name="btn_cover" value="上传节目封面" disabled="disabled" />
              	<?php }?>
              	<input type="hidden" name="cover" value="<?php echo empty($applyInfo['cover']) ? '' : $applyInfo['cover'];?>" />
              </div>
            </li>
            
            <li>
              <label><span class="tips">*</span>主播经验 ：</label>
              <div class="short_filed">
              	&nbsp;<input type="radio" id="has_experience_1" name="has_experience" class="has_experience" value="1" <?php echo isset($applyInfo['has_experience']) && $applyInfo['has_experience'] == 1 ? 'checked' : '';?> <?php echo $isLogin ? '' : 'disabled="disabled"'; ?> />&nbsp;有  &nbsp;&nbsp;&nbsp;&nbsp;
              	&nbsp;<input type="radio" id="has_experience_0" name="has_experience" class="has_experience" value="0" <?php echo isset($applyInfo['has_experience']) && $applyInfo['has_experience'] == 0 ? 'checked' : '';?> <?php echo $isLogin ? '' : 'disabled="disabled"'; ?> />&nbsp;没有&nbsp;&nbsp;&nbsp;
              	<input id="live_address" name="live_address" class="radio" type="text" value='<?php echo isset($applyInfo['has_experience']) && $applyInfo['has_experience'] == 1 ? $applyInfo['live_address'] : '请填直播间链接地址';?>' maxlength=250 style="width:140px; height:28px; <?php echo isset($applyInfo['has_experience']) && $applyInfo['has_experience'] != 1 ? 'color:#CCCCCC; display:none;' : '';?><?php echo $isLogin ? '' : 'display:none;'; ?>"; <?php echo isset($applyInfo['has_experience']) && $applyInfo['has_experience'] != 1 ? 'disabled=disabled':'';?> />
              </div>
            </li>
            <li>
              <label><span class="tips">*</span>我的导师 ：</label>
              <div class="short_filed">
              	<select name="tutor_uid" <?php echo $isLogin ? '' : 'disabled="disabled"'; ?>>
              		<option value="">选择导师</option>
              		<?php foreach($tutors as $t){ ?>
              		<?php if(!empty($t['extend']['qq']) && !empty($t['user']['nickname'])){?>
              		<option value="<?php echo $t['uid'];?>" <?php echo $tutor_uid == $t['uid'] ? 'selected' : '';?>><?php echo $t['user']['nickname'];?></option>
              		<?php }?>
              		<?php } ?>
              	</select>
              </div>
            </li>
            <li style="height: auto;">
              <label style="float:left;"><span class="tips">&nbsp;</span>主播招募：</label>
              <div class="short_filed">
              	<?php
              	$keFuList = $this->operateService->getAllKefuFromCache();
              	$kefu = array();
              	foreach($keFuList as $kf){
					if($kf['contact_type'] == KEFU_QQ && $kf['kefu_type'] == KEFU_QQ_DOTEY){
						$kefu[] = $kf;
					}
              	}
              	?>
              	<?php $i = 0; foreach($kefu as $t){?>
              	<?php if(!empty($t['contact_account']) && !empty($t['contact_name'])){ echo $i > 0 && $i % 2 == 0 ? '<br/>' : ''; $i++; ?>
              	<a style="display:inline-block; width:160px;" target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo $t['contact_account'];?>&amp;site=qq&amp;menu=yes">
	            	<img border="0" src="http://wpa.qq.com/pa?p=3:<?php echo $t['contact_account'];?>:45" alt="<?php echo $t['contact_name'];?>" title="<?php echo $t['contact_name'];?>" style="vertical-align:middle;">
	           		<?php echo $t['contact_name'];?>(QQ<?php echo $t['contact_account'];?>)
	           	</a>
	           	<?php }?>
	           	<?php }?>
              </div>
            </li>
            <?php if($isLogin){ ?>
            <li>
              <label></label>
              <div class="short_filed">
              	<input type="checkbox" id="agree" name="agree" class="checkbox" value="1" <?php echo isset($applyInfo) ? 'checked' : '';?> />我已阅读并同意
              	<a href="<?php echo $this->createUrl('dotey/agreement');?>" target="_blank" title="《签约主播用户协议》">《签约主播用户协议》</a>
              </div>
            </li>
            <li><label></label><div class="short_filed"><input type="submit" value="提 交" class="btn_next" /></div></li>
      		</form>
      		<?php } ?>
      </ul>
    
</div>
</div><!-- .w1000 -->

<script type="text/javascript">
$(function(){
	jQuery.validator.addMethod("live_address", function(value, element) {
	    return value != '' && value != '请填直播间链接地址';
	}, "请填写");

	jQuery.validator.addMethod("mobile", function(value, element) {
		return /^1[358]{1}\d{9}$/g.test(value);
	}, "手机号错误");

	jQuery.validator.addMethod('idCard', function(value, element) {
		return value.length == 15 || value.length == 18;
	}, "请填正确的身份证号");

	$('#applyForm').validate({
		onfocusout: function(element) { $(element).valid(); },
		onkeyup: function(element) { $(element).valid(); },
		ignore: '',
		rules:{
			realname:{
				required: true,
				rangelength:[2,10]
			},
			gender:{required: true},
			mobile:{
				required: true,
				number: true,
				mobile: true
			},
			qq:{
				required: true,
				number: true
			},
			id_card:{
				required: true,
				idCard: true
			},
			bank_user:{required: true},
			bank:{required: true},
			bank_account:{required: true},
			cover:{required: true},
			has_experience:{
				required:function(){
					if($('#has_experience_1').attr('checked') == 'checked') return false;
					else return true;
				}
			},
			live_address:{
				live_address:function(){
					if($('#has_experience_1').attr('checked') == 'checked') return true;
					else return false;
				},
			},
			tutor_uid:{required: true},
			agree:{required: true}
		},
		messages:{
			realname:{
				required: "请填写姓名",
				rangelength: "姓名须2到10个字"
			},
			gender:{required: "请填写性别"},
			mobile:{
				required: "请填写手机号",
				number: "请填写数字"
			},
			qq:{
				required: "请填写QQ号",
				number: "请填写数字"
			},
			id_card:{required: "请填写身份证号"},
			bank_user:{required: "请填写开户姓名"},
			bank:{required: "请填写开户银行"},
			bank_account:{required: "请填写银行卡号"},
			cover:{required: "请上传节目封面"},
			has_experience:{required: "请填写主播经验"},
			live_address:{url: "请填完整地址"},
			tutor_uid:{required: "请选择导师"},
			agree:{required: "请先阅读主播协议"}
		},
		success: function(label) {
		    label.html("&nbsp;").addClass("valid");
		},
		errorPlacement: function (error, element) {
	        if (element.is(':radio') || element.is(':checkbox') || element.attr('name') == 'profession' || element.attr('name') == 'profession_text') {
	            var eid = element.attr('name');
	            error.appendTo(element.parent());
	        }
	        else {
	            error.insertAfter(element);
	        }
	    }
	});
});
</script>
<script type="text/javascript">
$(function(){
	$('#has_experience_1').click(function(){
		$('#live_address').attr('disabled', false);
		$('#live_address').show();
	});
	$('#has_experience_0').click(function(){
		$('#live_address').hide();
		$('#live_address').attr('disabled', true);
		$('.error[for="live_address"]').remove();
		$('#live_address').after('<label class="error valid" for="live_address" style="display:inline;"> </label>');
	});

	$('#live_address').focus(function(){
		if(this.value=='请填直播间链接地址'){this.value=''; this.style.color='#000000';}
	}).blur(function(){
		if(this.value==''){this.value='请填直播间链接地址'; this.style.color='#CCCCCC';}
	});

	$('#btn_edit').click(function(){
		$(this).hide('slow');
		$('#showFlash').show('slow');
	});
	<?php /*?>
	$('#btn_cover').click(function(){
		createBoxy('cover','上传节目封面');
	});
	<? */?>
})
</script>
<script type="text/javascript">
var boxy = null;
var upload_type = null;
function createBoxy(type, title){
	upload_type = type;
	$.ajax({
		url: '<?php echo $this->createUrl('dotey/upload');?>',
		type: 'GET',
		dataType: 'html',
		data: {type: type, title: title},
		success:function(html){
			if(html == '') { alert('登陆会话超时，请重新登陆'); window.location.reload();}
			else boxy = new Boxy(html,{title: title, closeText: 'X', modal:true, closeable:true, unloadOnHide:true});
		}
	});
}
function uploaded() {
	if($('#cover_image').length>0){
		$('#cover_image').attr('src', '<?php echo $this->getUploadSingleton()->getTempFileUrl();?>?'+new Date().getTime());
	}else{
		$('#btn_cover').before('<img id="cover_image" src="<?php echo $this->getUploadSingleton()->getTempFileUrl();?>?<?php echo rand(100000, 999999)?>" />');
	}
	$('.error[for="btn_cover"]').remove();
	$('input[name="cover"]').val('cover');
	$('#btn_cover').after('<label class="error valid" for="btn_cover" style="display:inline;"> </label>');
	$('#showFlash').hide('slow');
	$('#btn_edit').show('slow');
}
</script>