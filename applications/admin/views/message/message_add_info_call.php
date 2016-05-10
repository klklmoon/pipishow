<?php
$this->breadcrumbs = array('运营工具','消息通知');
$extras = $this->getExtraFlag();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 新增消息通知</h2>
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('message/infoCall',array('op'=>'addMessageDo'));?>" method="post">
				<fieldset>
					<div class="control-group">
					<label class="control-label" for="focusedInput">消息类型</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('_form[type]', '', $this->getCategory(),array('size'=>1,'class'=>'input-small','empty'=>'-请选择-'));
						?>
						<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_type"></span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">子类型</label>
					<div class="controls">
						<?php echo CHtml::listBox('_form[stype]','',array(),array('class'=>'input-small','size'=>1,'empty'=>' '));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_stype"></span>
					</div>
				</div>
				  
				  <div class="control-group">
					<label class="control-label">用户名/UID</label>
					<div class="controls">
					  <input class="input-small focused" id="indexForm_user_info" name="user_info" type="text" value="">
					  <input class="btn" type="button" value="验证" name="valid_user_info" id="valid_user_info">
					  <span class="label label-important"  id="valid_user_info_noty" style="margin-left:10px; display:none;"></span>
					</div>
				  </div>
				  
				  <!-- 用户结果集合 -->
				  <div id="user_info_uids" class="box" style="padding:5px;display:none;"> 
				  	
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">标题</label>
					<div class="controls">
						<?php echo CHtml::textField('_form[title]','',array('class'=>'input-large focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_title"></span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">内容</label>
					<div class="controls">
						<?php echo CHtml::textArea('_form[content]','');?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_content"></span>
					</div>
				  </div>
				  
				  <!-- 扩展信息 -->
				  <div id="user_info_uids" class="box" style="padding:5px;"> 
				  	<?php 
				  	foreach($extras as $k=>$v){
				  	?>
				  	<div class="control-group">
						<label class="control-label" for="focusedInput"><?php echo $v['name'];?></label>
						<div class="controls">
							<?php echo CHtml::textField('_form[extra]['.$k.']',$v['default'],array('class'=>'input-large focused'));?>
							<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_extra_<?php echo $k;?>"></span>
						</div>
					  </div>
					<?php 				  		
				  	}
				  	?>
				  </div>
				  
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary" id="submit_message">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script>

$(function(){
	//改变状态动作 初始化数据
	$('#_form_type').change(function(e){
		var type = $(this).attr('value');

		if(type){
			$('#info_form_type').html('').hide();
			$.ajax({
				url:"<?php echo $this->createUrl('message/infoCall');?>",
				type:'post',
				dataType:'html',
				data:{'type':type,'op':'getSCategory'},
				success:function(msg){
					if(msg == 1){
						$('#info_form_stype').html('获取子分类失败').show();
					}else{
						$('#info_form_stype').html('').hide();
						$('#_form_stype').html(msg);
					}
				}
			});
		}else{
			$('#info_form_type').html('请选择消息类型').show();
		}
	});
	
	//验证用户
	$("#valid_user_info").click(function(){
		var userName = $("#indexForm_user_info").attr('value');
		
		if(userName){
			$("#valid_user_info_noty").html("").hide();
			$.ajax({
				url:"<?php echo $this->createUrl("message/infoCall");?>",
				type:"post",
				dataType:"text",
				data:{"op":"checkUserInfo","userName":userName},
				success:function(msg){
					var data = msg.split('#xx#');
					if(data[0] == 1){
						var doteyUid 		= data[1];
						var doteyUsername 	= data[2];
						var doteyNickname 	= data[3];
						var isReturn = false;
						$("input[name='_form[uid][]']").each(function(){
							if($(this).attr('value') == doteyUid){
								isReturn = true;
							}
						});

						if(isReturn){
							$('#valid_user_info_noty').html(doteyUsername+' 已经存在，不能重复添加').show();
							return false;
						}else{
							$('#valid_user_info_noty').html('').hide();
						}
						
						var num = $('#user_info_uids').children('.control-group').length;
						var html = '<div class="control-group">';
						html += '<label class="control-label">'+doteyUsername+'</label>';
						html += '<div class="controls">';
						html += '<input class="input-small focused" id="_form_uid_'+(num+1)+'" name="_form[uid][]" type="text" value="'+doteyUid+'" readonly="readonly">';
						html += '<i class="icon-remove" style="margin-left:20px;" onclick="'+"$(this).parents('.control-group').detach()"+'"></i></div></div>';  
						$('#user_info_uids').append(html).show();
					}else{
						$("#valid_user_info_noty").html(msg).show();
					}
				}
			});
		}else{
			$("#valid_user_info_noty").html("请输入用户名称或用户ID").show();
		}
	});
	
	//表单提交前的动作
	$('#submit_message').click(function(){
		if(!$('#_form_type').attr('value')){
			$('#info_form_type').html('请选择消息类型').show();
			return false;
		}else{
			$('#info_form_type').html('').hide();
		}

		if(!$('#_form_stype').attr('value')){
			$('#info_form_stype').html('请选择消息子类型').show();
			return false;
		}else{
			$('#info_form_stype').html('').hide();
		}
		
		if($('#user_info_uids').children('.control-group').length <= 0){
			$('#valid_user_info_noty').html('发送消息对象不能为空').show();
			return false;
		}else{
			$('#valid_user_info_noty').html('').hide();
		}

		if(!$('#_form_title').attr('value')){
			$('#info_form_title').html('标题不能为空').show();
			return false;
		}else{
			$('#info_form_title').html('').hide();
		}

		if(!$('#_form_content').attr('value')){
			$('#info_form_content').html('消息内容不能为空').show();
			return false;
		}else{
			$('#info_form_content').html('').hide();
		}

		/*if(!$('#_form_extra_form').attr('value')){
			$('#info_form_extra_form').html('扩展信息来源不能为空').show();
			return false;
		}else{
			$('#info_form_extra_form').html('').hide();
		}*/
		
		return true;
	});
	
})
</script>