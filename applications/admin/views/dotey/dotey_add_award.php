<?php
$this->breadcrumbs = array('主播管理','新增平台奖励');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 新增平台奖励</h2>
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('dotey/addaward',array('op'=>'addAward'));?>" method="post">
				<fieldset>
				  <div class="control-group">
					<label class="control-label">主播用户名/UID</label>
					<div class="controls">
					  <input class="input-small focused" id="indexForm_dotey_name" name="dotey_info" type="text" value="">
					  <input class="btn" type="button" value="验证" name="valid_dotey_info" id="valid_dotey_info">
					  <span class="label label-important"  id="valid_dotey_info_noty" style="margin-left:10px; display:none;" isSubmit="1"></span>
					</div>
				  </div>
				  
				  <!-- 用户结果集合 -->
				  <div id="dotey_info_uids" class="box" style="padding:5px;display:none;"> </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">奖励类型</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('form[type]', '', $this->getAwardType(),array('size'=>1,'class'=>'input-small','empty'=>'-请选择-'));
						?>
						<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_type"></span>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">数量</label>
					<div class="controls">
						<?php echo CHtml::textField('form[quantity]','',array('class'=>'input-small focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_quantity"></span>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">奖励理由</label>
					<div class="controls">
						<?php echo CHtml::textArea('form[reason]','');?>
						<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_reason"></span>
					</div>
				  </div>
				  
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary" id="submit_award">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script>
$(function(){
	//表单提交前的动作
	$('#submit_award').click(function(){
		if($('#dotey_info_uids').children('.control-group').length <= 0){
			$('#valid_dotey_info_noty').html('奖励对象不能为空').show();
			return false;
		}else{
			$('#valid_dotey_info_noty').html('').hide();
		}
		
		if(!$('#form_type').attr('value') < 0){
			$('#info_form_type').html('请选择奖励类型').show();
			return false;
		}else{
			$('#info_form_type').html('').hide();
		}

		if(!$('#form_quantity').attr('value') || isNaN($('#form_quantity').attr('value'))){
			$('#info_form_quantity').html('请填写奖励的数量且值为数字类型').show();
			return false;
		}else{
			$('#info_form_quantity').html('').hide();
		}

		if(!$('#form_reason').attr('value')){
			$('#info_form_reason').html('请填写奖励原因').show();
			return false;
		}else{
			$('#info_form_reason').html('').hide();
		}
	});
	//验证主播
	$("#valid_dotey_info").click(function(){
		var doteyName = $("#indexForm_dotey_name").attr('value');
		if(doteyName){
			$("#valid_dotey_info_noty").html("").hide();
			$.ajax({
				url:"<?php echo $this->createUrl("dotey/addaward");?>",
				type:"post",
				dataType:"text",
				data:{"op":"checkDoteyInfo","doteyName":doteyName},
				success:function(msg){
					var data = msg.split('#xx#');
					if(data[0] == 1){
						var doteyUid 		= data[1];
						var doteyUsername 	= data[2];
						var doteyNickname 	= data[3];
						var isReturn = false;
						$("input[name='form[uid][]']").each(function(){
							if($(this).attr('value') == doteyUid){
								isReturn = true;
							}
						});

						if(isReturn){
							$('#valid_dotey_info_noty').html(doteyUsername+' 已经存在，不能重复添加').show();
							return false;
						}else{
							$('#valid_dotey_info_noty').html('').hide();
						}
						
						var num = $('#dotey_info_uids').children('.control-group').length;
						var html = '<div class="control-group">';
						html += '<label class="control-label">'+doteyUsername+'</label>';
						html += '<div class="controls">';
						html += '<input class="input-small focused" id="form_uid_'+(num+1)+'" name="form[uid][]" type="text" value="'+doteyUid+'" readonly="readonly">';
						html += '<i class="icon-remove" style="margin-left:20px;" onclick="'+"$(this).parents('.control-group').detach()"+'"></i></div></div>';  
						$('#dotey_info_uids').append(html).show();
					}else{
						$("#valid_dotey_info_noty").html(msg).show();
					}
				}
			});
		}else{
			$("#valid_dotey_info_noty").html("请输入主播名称或主播ID").show();
		}
	});
})
</script>