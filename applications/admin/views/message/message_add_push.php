<?php
$this->breadcrumbs = array('运营工具','消息通知');
$extras = $this->getExtraFlag();
$types = $this->getPushType();
$window = $this->getWindow();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 新增推送消息</h2>
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('message/push',array('op'=>'addPushDo'));?>" method="post">
				<fieldset>
					<div class="span12">
			  			<span title="提示" class="icon icon-color icon-pin"></span>
			  			当有特定的目标对象时范围限制将不起作用
			  		</div>
			  		
					<div class="control-group">
					<label class="control-label" for="focusedInput">推送类型</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('_form[type]', '', $types,array('size'=>1,'class'=>'input-small','empty'=>'-请选择-'));
						?>
						<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_type"></span>
					</div>
				  </div>
				  
				  <div class="control-group" id="rank_display">
					<label class="control-label" for="focusedInput">范围</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('_form[rank]', '', array(),array('size'=>1,'class'=>'input-small','empty'=>' '));
						?>
						<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_rank"></span>
					</div>
				  </div>
				  
				  <div class="control-group" id="window_display">
					<label class="control-label" for="focusedInput">窗口显示</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('_form[window]', 0, $window,array('size'=>1,'class'=>'input-small'));
						?>
						<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_window"></span>
					</div>
				  </div>
				  
				  <div class="control-group" id="target_display">
					<label class="control-label">目标对象</label>
					<div class="controls">
					  <input class="input-small focused" id="indexForm_user_info" name="user_info" type="text" value="">
					  <input class="btn" type="button" value="验证" name="valid_user_info" id="valid_user_info">
					  </br>
					  <span class="label label-important"  id="valid_user_info_noty">用户名/UID/主播ID</span>
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
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">Tips</label>
					<div class="controls">
						<?php echo CHtml::textArea('_form[tips]','');?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_tips"></span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">预定发送时间</label>
					<div class="controls">
						<?php echo CHtml::textField('_form[send_time]','',array('class'=>'input-large focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_send_time"></span>
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
var TYPE_GLOBAL = "<?php echo MESSAGE_PUSH_TYPE_GLOBAL?>";
var TYPE_USER = "<?php echo MESSAGE_PUSH_TYPE_USER?>";
var TYPE_LIVE = "<?php echo MESSAGE_PUSH_TYPE_LIVE?>";
var TYPE_DOTEY = "<?php echo MESSAGE_PUSH_TYPE_DOTEY?>";
$(function(){
	//注册开始时间
	$( '#_form_send_time' ).click(function(){
			WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm:ss','minDate':'<?php echo date('Y-m-d H:i:s',time());?>'});
		}
	);
	
	//改变状态动作 初始化数据
	$('#_form_type').change(function(e){
		var type = $(this).attr('value');

		if(type){
			$("#valid_user_info_noty").html("用户名/UID/主播ID").show();
			$("#user_info_uids").html('').hide();
			
			if(type == TYPE_GLOBAL){
				$('#target_display').hide();
			}else{
				$('#target_display').show();
			}

			if(type == TYPE_LIVE){
				$('#window_display').show();
			}else{
				$('#window_display').hide();
			}

			if(type == TYPE_GLOBAL || type == TYPE_LIVE){
				$("#rank_display").hide();
			}else{
				$("#rank_display").show();
				$('#info_form_type').html('').hide();
				$.ajax({
					url:"<?php echo $this->createUrl('message/push');?>",
					type:'post',
					dataType:'html',
					data:{'type':type,'op':'getRank'},
					success:function(msg){
						if(msg == 1){
							$("#rank_display").hide();
						}else{
							$('#info_form_rank').html('').hide();
							$('#_form_rank').html(msg);
							$("#rank_display").show();
						}
					}
				});
			}
		}else{
			$('#info_form_type').html('请选择推送类型').show();
		}
	});
	
	$("#valid_user_info").click(function(){
		var userName = $("#indexForm_user_info").attr('value');
		var type = $('#_form_type').attr('value');
		if(type){
			if(userName){
				$("#valid_user_info_noty").html("用户名/UID/主播ID").show();
				$.ajax({
					url:"<?php echo $this->createUrl("message/push");?>",
					type:"post",
					dataType:"text",
					data:{"op":"checkTarget","userName":userName,'type':type},
					success:function(msg){
						var data = msg.split('#xx#');
						if(data[0] == 1){
							var doteyUid 		= data[1];
							var doteyUsername 	= data[2];
							var doteyNickname 	= data[3];
							var isReturn = false;
							$("input[name='_form[target_id][]']").each(function(){
								if($(this).attr('value') == doteyUid){
									isReturn = true;
								}
							});

							if(isReturn){
								$('#valid_user_info_noty').html(doteyUsername+' 已经存在，不能重复添加').show();
								return false;
							}else{
								$('#valid_user_info_noty').html('用户名/UID/主播ID').show();
							}
							
							var num = $('#user_info_uids').children('.control-group').length;
							var html = '<div class="control-group">';
							html += '<label class="control-label">'+doteyUsername+'</label>';
							html += '<div class="controls">';
							html += '<input class="input-small focused" id="_form_target_id_'+(num+1)+'" name="_form[target_id][]" type="text" value="'+doteyUid+'" readonly="readonly">';
							html += '<i class="icon-remove" style="margin-left:20px;" onclick="'+"$(this).parents('.control-group').detach()"+'"></i></div></div>';  
							$('#user_info_uids').append(html).show();
						}else{
							$("#valid_user_info_noty").html(msg).show();
						}
					}
				});
			}else{
				$("#valid_user_info_noty").html("请输入目标对象根据推送类型而定").show();
			}
		}else{
			$("#valid_user_info_noty").html("请先选择推送类型").show();
		}
	});
	
	//表单提交前的动作
	$('#submit_message').click(function(){
		if(!$('#_form_type').attr('value')){
			$('#info_form_type').html('请选择消息推送类型').show();
			return false;
		}else{
			$('#info_form_type').html('').hide();
		}

		if(!$('#_form_content').attr('value')){
			$('#info_form_content').html('内容不能为空').show();
			return false;
		}else{
			$('#info_form_content').html('').hide();
		}
		return true;
	});
	
})
</script>