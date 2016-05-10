<?php
$this->breadcrumbs = array('靓号管理','添加用户靓号');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 添加用户靓号</h2>
		</div>
		<?php }?>
		<div class="box-content">
			<?php if(!empty($notices)){?>
			<div class="alert alert-block">
			<?php foreach($notices as $notice){?>
				<p><?php echo $notice[0];?></p>
			<?php }?>
			</div>
			<?php }?>
			<form class="form-horizontal" action="<?php echo $this->createUrl('number/addUserNumber');?>" method="post">
				<fieldset>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">靓号</label>
					<div class="controls">
						<?php 
					  		echo CHtml::hiddenField('number',$numberInfo['number']);
					  		echo CHtml::hiddenField('numbers[number]',$numberInfo['number']);
					  	?>
				  		<span style="cursor:pointer;" class="label label-warning">
				  		<?php echo $numberInfo['number'];?></span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">寄语</label>
					<div class="controls">
						<?php 
					  		echo CHtml::hiddenField('numbers[short_desc]',$numberInfo['short_desc']);
					  	?>
				  		<span style="cursor:pointer;" class="label label-warning">
				  		<?php echo $numberInfo['short_desc'];?></span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label">用户名/UID</label>
					<div class="controls">
					  <input class="input-small focused" id="indexForm_dotey_name" name="dotey_info" type="text" value="">
					  <input class="btn" type="button" value="验证" name="valid_dotey_info" id="valid_dotey_info" isDotey="0">
					  <span class="label label-important"  id="valid_dotey_info_noty" style="margin-left:10px; display:none;" isSubmit="1"></span>
					</div>
				  </div>
				  
				  <!-- 用户结果集合 -->
				  <div id="dotey_info_uids" class="box" style="padding:5px;display:none;"> 
				  	
				  </div>
				  
				  <div class="control-group">
				  	<label class="control-label">有效时间</label>
				  	<div class="controls">
				  		<?php echo CHtml::textField('numbers[last_recharge_time]',0,array('class'=>'input-small'))?>
				  		<span class="label label-important" style="margin-left:10px;">0表示无过期时间最小单位为天数</span>
				  	</div>
				  </div>
				  
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script style="text/javascript">
$(function() {
	//验证用户
	$("#valid_dotey_info").click(function(){
		var doteyName = $("#indexForm_dotey_name").attr('value');
		
		if(doteyName){
			$("#valid_dotey_info_noty").html("").hide();
			$.ajax({
				url:"<?php echo $this->createUrl("number/addUserNumber");?>",
				type:"post",
				dataType:"text",
				data:{"op":"checkUserInfo","doteyName":doteyName},
				success:function(msg){
					var data = msg.split('#xx#');
					if(data[0] == 1){
						var doteyUid 		= data[1];
						var doteyUsername 	= data[2];
						var doteyNickname 	= data[3];
						var isReturn = false;

						$('#valid_dotey_info_noty').html('').hide();
						
						var num = $('#dotey_info_uids').children('.control-group').length;
						var html = '<div class="control-group">';
						html += '<label class="control-label">'+doteyUsername+'</label>';
						html += '<div class="controls">';
						html += '<input class="input-small focused" id="numbers_uid" name="numbers[uid]" type="text" value="'+doteyUid+'" readonly="readonly">';
						html += '<i class="icon-remove" style="margin-left:20px;" onclick="'+"$(this).parents('.control-group').detach()"+'"></i></div></div>';  
						$('#dotey_info_uids').html(html).show();
					}else{
						$("#valid_dotey_info_noty").html(msg).show();
					}
				}
			});
		}else{
			$("#valid_dotey_info_noty").html("请输入用户名称或UID").show();
		}
	});
	
	$(':submit').click(function(){
		var uid = $('#numbers_uid').attr('value');
		if(!uid || isNaN(uid)){
			alert('确认赠送对象不能为空且为数字');
			return false;
		}

		var effectTime = $('#numbers_last_recharge_time').attr('value');
		if(!effectTime || isNaN(effectTime)){
			alert('过期时间不能为空且为数字');
			return false;
		}
		return true;
	});
});
</script>