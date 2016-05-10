<div class="w1000 bord mt40">
	<h1>申请创建家族</h1>
	<div class="info_table">
		<form id="applyForm" action="<?php echo $this->createUrl('family/apply'); ?>" method="post" enctype="multipart/form-data">
		<ul class="info_content clearfix">
            <li>
            	<label>申请人：</label>
            	<div class="long_filed">
            		<em class="lvlr lvlr-<?php echo $user['rk'];?> mr5"></em>
            		<span class="pink"><?php echo $user['nk'];?></span>
            		（<?php echo $user['uid'];?>）
            	</div>
            </li>
            <li>
            	<label><span class="tips">*</span>家族名称：</label>
            	<div class="long_filed">
            		<input class="text" type="text" name="name" id="name" />
            		<label class="error noback" for="name">20字以内</label>
            	</div>
            </li>
            <li>
            	<label><span class="tips">*</span>徽族简字：</label>
            	<div class="long_filed">
            		<input class="text" type="text" name="medal" id="medal" />
            		<label class="error noback" for="medal">2个汉字或2-3个英文字母</label>
            	</div>
            </li>
            <li>
            	<label>&nbsp;</label>
            	<div class="short_filed" style="width:200px;">
            		<input type="button" class="btn_uplod mr15" value="生成预览" id="make_medal" />
            		<img id="show_medal" src="<?php echo $this->pipiFrontPath;?>/fontimg/family/63.png" />
            	</div>
            </li>
            <li>
            	<label><span class="tips">*</span>家族封面：</label>
            	<div class="short_filed" style="width:500px;">
            		<input type="file" class="btn_uplod" value="上传图片" name="cover" id="cover" style="width:200px;" />
            		<label class="error noback" for="cover">仅支持jpg格式，大小不超过2Mb</label>
            	</div>
            </li>
            <?php /*<li style="height:auto;"><label>&nbsp;</label><div class="long_filed" style="height:auto;"><img src="pics/jzfm.jpg" width="180" height="100" /></div></li>*/?>
            <li>
            	<label><span class="tips">*</span>玩家姓名：</label>
            	<div class="long_filed">
            		<input class="text" type="text" name="realname" value="<?php echo $user['realname'];?>" />
            	</div>
            </li>
            <li>
            	<label><span class="tips">*</span>QQ号码：</label>
            	<div class="long_filed">
            		<input class="text" type="text" name="qq" value="<?php echo $user['qq'];?>" />
            	</div>
            </li>
            <li>
            	<label><span class="tips">*</span>手机号码：</label>
            	<div class="long_filed">
            		<input class="text" type="text" name="mobile" value="<?php echo $user['mobile'];?>" />
            	</div>
            </li>
            <li>
            	<label>缴纳费用：</label>
            	<div class="long_filed"><span class="pink"><?php echo $create_price;?></span>皮蛋<span class="ml20">未创建成功则费用退回账户</span></div>
            </li>
            <li><label>&nbsp;</label><input type="submit" value="提交申请" class="btn_sub" /></li>
        </ul>
        </form>
   </div>      
</div>
<script type="text/javascript">
$(function(){
	$('#make_medal').click(function(){
		var medal = $('#medal').val();
		if(medal == ''){
			alert('请输入族徽简字');
		}else{
			var length = 0, len = $.trim(medal).length;
			for (var i = 0; i < len; i++) {
		        charCode = $.trim(medal).charCodeAt(i);
		        if (charCode >= 0 && charCode <= 128) length += 1;
		        else length += 1.5;
		    }
		    if(!(length >= 2 && length <= 3)){
				alert("2个汉字或2-3个英文字母");
		    }else{
				$.ajax({
					url : "index.php?r=family/makeMedal",
					type : "GET",
					data:{'medal':medal},
					dataType : "text",
					success : function(text){
						$('#show_medal').attr('src', '/images/'+text+'?'+(new Date().getTime()));
				 	}
				});
		    }
		}
	});

	jQuery.validator.addMethod("mobile", function(value, element) {
		return /^1[358]{1}\d{9}$/g.test(value);
	}, "手机号错误");

	jQuery.validator.addMethod("un_rangelength", function(value, element, param) {
		var length = 0, len = $.trim(value).length, charCode = -1;
	    for (var i = 0; i < len; i++) {
	        charCode = $.trim(value).charCodeAt(i);
	        if (charCode >= 0 && charCode <= 128) length += 1;
	        else length += 1.5;
	    }
		return this.optional(element) || ( length >= param[0] && length <= param[1] );
	}, "2个汉字或2-3个英文字母");

	$('#applyForm').validate({
		onfocusout: function(element) { $('label.error').removeClass('noback');$(element).valid(); },
		onkeyup: function(element) { $('label.error').removeClass('noback');$(element).valid(); },
		rules:{
			name:{
				required: true,
				rangelength:[2,20]
			},
			medal:{
				required: true,
				un_rangelength:[2,3]
			},
			cover:{
				required:true
			},
			realname:{
				required:true,
				rangelength:[2,6]
			},
			qq:{
				required: true,
				number: true
			},
			mobile:{
				required: true,
				number: true,
				mobile: true
			}
		},
		messages:{
			name:{
				required: "请填写家族名称",
				rangelength: "20字以内"
			},
			medal:{
				required: "请填写徽章简字",
				un_rangelength: "2个汉字或2-3个英文字母"
			},
			cover:{
				required: "请上传家族封面"
			},
			realname:{
				required: "请填写真实姓名",
				rangelength: "2至6汉字以内"
			},
			qq:{
				required: "请填写QQ号码",
				number: "QQ号码必须是数字"
			},
			mobile:{
				required: "请填写手机号码",
				number: "手机号码必须是数字",
				mobile: "请填写正确的手机号"
			}
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