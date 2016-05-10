<?php
$this->breadcrumbs = array('运营工具','添加会员勋章');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 添加会员勋章</h2>
		</div>
		<?php }?>
		<div class="box-content">
			<?php if(!empty($notices)){?>
			<div class="alert alert-block">
			<?php foreach($notices as $notice){?>
				<p><?php echo is_array($notice)?$notice[0]:$notice;?></p>
			<?php }?>
			</div>
			<?php }?>
			<form class="form-horizontal" action="<?php echo $this->createUrl('operators/addusermedal',array('op'=>'addUserMedal'));?>" method="post" enctype="multipart/form-data">
				<fieldset>
				  <div class="control-group">
					<label class="control-label">用户名/UID</label>
					<div class="controls">
					  <input class="input-small focused" id="indexForm_dotey_name" name="dotey_info" type="text" value="">
					  <input class="btn" type="button" value="验证" name="valid_dotey_info" id="valid_dotey_info">
					  <span class="label label-important"  id="valid_dotey_info_noty" style="margin-left:10px; display:none;"></span>
					</div>
				  </div>
				  
				  <!-- 用户结果集合 -->
				  <div id="dotey_info_uids" class="box" style="padding:5px;display:none;"> </div>
				  
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">发送类型</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('medal[type]', '', $userMedal->getGrantTypeList(),array('class'=>'input-small','size'=>1));
						?>
						<span class="label label-important"  id="valid_medal_type" style="margin-left:10px; display:none;"></span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">勋章</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('medal[mid]', '', $mids,array('class'=>'input-small','size'=>1));
						?>
						<span class="label label-important"  id="valid_medal_mid" style="margin-left:10px; display:none;"></span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">有效时间</label>
					<div class="controls">
						<input type="text" class="input-large" name="medal[vtime]" id="medal_vtime"/>
						<span class="label label-important"  id="valid_medal_vtime" style="margin-left:10px; display:none;"></span>
					</div>
				  </div>
				  
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary" id="submit_medal">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script>
//注册开始时间
$( '#medal_vtime' ).datepicker(
	{ 
		showButtonPanel: true,
		changeMonth: true,
		changeYear: true,
		dateFormat:'yy-mm-dd'
	}
);

//表单提交前的动作
$('#submit_medal').click(function(){
	if($('#dotey_info_uids').children('.control-group').length <= 0){
		$('#valid_dotey_info_noty').html('添加对象不能为空').show();
		return false;
	}else{
		$('#valid_dotey_info_noty').html('').hide();
	}
	
	if(!$('#medal_type').attr('value')){
		$('#valid_medal_type').html('请选择发送类型').show();
		return false;
	}else{
		$('#valid_medal_type').html('').hide();
	}

	if(!$('#medal_mid').attr('value') ){
		$('#valid_medal_mid').html('请选择勋章').show();
		return false;
	}else{
		$('#valid_medal_mid').html('').hide();
	}

	if(!$('#medal_vtime').attr('value') ){
		$('#valid_medal_vtime').html('请填写有效日期').show();
		return false;
	}else{
		$('#valid_medal_vtime').html('').hide();
	}
	
	return true;
});

//验证主播
$("#valid_dotey_info").click(function(){
	var doteyName = $("#indexForm_dotey_name").attr('value');
	if(doteyName){
		$("#valid_dotey_info_noty").html("").hide();
		$.ajax({
			url:"<?php echo $this->createUrl("operators/addusermedal");?>",
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
					$("input[name='medal[uid][]']").each(function(){
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
					html += '<input class="input-small focused" id="medal_uid_'+(num+1)+'" name="medal[uid][]" type="text" value="'+doteyUid+'" readonly="readonly">';
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
</script>