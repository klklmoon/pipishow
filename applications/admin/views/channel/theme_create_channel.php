<?php 
	$this->breadcrumbs = array('频道管理','创建频道');
?>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!$this->isAjax){?>
		<div data-original-title="" class="box-header well">
			<h2><i class="icon-edit"></i> 创建频道</h2>
		</div>
		<?php }?>
		<div class="box-content">
			<?php if(isset($notices) && count($notices)>0){?>
			<div class="alert alert-block" style="margin-left:60px;margin-right:200px;clear:both;">
				<button type="button" class="close" data-dismiss="alert">×</button>
			<?php foreach($notices as $notice){?>
				<p><?php echo isset($notice[0])?$notice[0]:$notice;?></p>
			<?php }?>
			</div>
			<?php }?>
			
			<?php if(!$ispc){?>
			<form class="form-horizontal" id="purview_add" action="<?php echo $this->createUrl('channel/createtheme',array('op'=>'addSubChannel'));?>" method="post">		
				<fieldset>
					<div class="control-group">
						<label class="control-label">父频道</label>				
						<div class="controls">
							<?php 
								$channelId = Yii::app()->request->getParam('channelId');
								$select1 = isset($subInfo['channel_id'])?$subInfo['channel_id']:$channelId;
								echo CHtml::listBox('channelsub[channel_id]', $select1, $pchannel,array('size'=>'1','empty'=>'-请选择-','class'=>'input-small'));
							?>
							<input class="btn" value="创建父频道" id="channel_create_parent" type="button">
							<span class="label label-important" style="margin-left:10px; display:none;"></span>
						</div>
				  	</div> 
				  	
				  	<div class="box" id="parent_box" style="margin:5px;display:none;padding:5px;">
				  		<div class="control-group">
							<label class="control-label">父频道名称</label>				
							<div class="controls" >
								<?php 
									echo CHtml::textField('channel[channel_name]','',array('class'=>'input-small focused'));
								?>
								<span class="label label-important" style="margin-left:10px; display:none;"></span>
							</div>
					  	</div>
					  	
					  	<div class="control-group">
							<label class="control-label">首页是否显示</label>				
							<div class="controls">
								<?php 
									echo CHtml::listBox('channel[is_show_index]', '', array(0=>'不显示',1=>'显示'),array('size'=>'1','class'=>'input-small'));
								?>
								<span class="label label-important" style="margin-left:10px; display:none;"></span>
							</div>
						</div>
						
						<div class="control-group">
							<label class="control-label">展示排序</label>				
							<div class="controls">
								<?php 
									echo CHtml::textField('channel[index_sort]','',array('class'=>'input-small focused'));
								?>
								<span class="label label-important" style="margin-left:10px; display:none;"></span>
							</div>
						</div>
						
						<div class="control-group">
							<div class="controls">
								<input class="btn" value="确定" id="channel_create_parent_do" type="button">
							</div>
						</div>
				  	</div>
		  	 
			  	 	<div class="control-group">
						<label class="control-label">子频道名称</label>				
						<div class="controls">
							<?php 
								$select2 = isset($subInfo['sub_name'])?$subInfo['sub_name']:'';
								echo CHtml::textField('channelsub[sub_name]',$select2,array('class'=>'input-large focused'));
							?>
							<span class="label label-important" style="margin-left:10px; display:none;"></span>
						</div>
					</div>
					
					<div class="control-group">
							<label class="control-label">频道描述</label>				
							<div class="controls" >
								<?php 
									$select2 = isset($subInfo['desc'])?$subInfo['desc']:'';
									echo CHtml::textField('channelsub[desc]',$select2,array('class'=>'input-large focused'));
								?>
								<span class="label label-important" style="margin-left:10px; display:none;"></span>
							</div>
					  	</div>
					  	
					<div class="control-group">
						<label class="control-label">主播排序方式</label>				
						<div class="controls">
							<?php 
								$select3 = isset($subInfo['dotey_sort'])?$subInfo['dotey_sort']:'';
								echo CHtml::listBox('channelsub[dotey_sort]', $select3, $this->channelSer->getDoteySortList(),array('size'=>'1','empty'=>'-请选择-'));
							?>
							<span class="label label-important" style="margin-left:10px; display:none;"></span>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">首页是否显示</label>				
						<div class="controls">
							<?php 
								$select4 = isset($subInfo['is_show_sindex'])?$subInfo['is_show_sindex']:'';
								echo CHtml::listBox('channelsub[is_show_sindex]', $select4, array(0=>'不显示',1=>'显示'),array('size'=>'1','class'=>'input-small'));
							?>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">展示排序</label>				
						<div class="controls">
							<?php 
								$select5 = isset($subInfo['index_ssort'])?$subInfo['index_ssort']:'';
								echo CHtml::textField('channelsub[index_ssort]',$select5,array('class'=>'input-small focused'));
							?>
						</div>
					</div>
		  	
					<div class="form-actions">
						<input class="btn btn-primary" value="提交" type="submit" name="yt1" id="sub_submit_channel">
						<?php 
						if(isset($subInfo['sub_channel_id'])){
							echo CHtml::hiddenField('channelsub[sub_channel_id]',$subInfo['sub_channel_id']);							
						}
						?>			
					</div>
				</fieldset>
			</form>
			<?php }else{?>
			<form class="form-horizontal" id="purview_add" action="<?php echo $this->createUrl('channel/createtheme',array('op'=>'addChannel'));?>" method="post">		
				<fieldset>
			  		<div class="control-group">
						<label class="control-label">父频道名称</label>				
						<div class="controls" >
							<?php 
								echo CHtml::textField('channel[channel_name]',$pcinfo['channel_name'],array('class'=>'input-small focused'));
							?>
							<span class="label label-important" style="margin-left:10px; display:none;"></span>
						</div>
				  	</div> 
				  	
				  	<div class="control-group">
						<label class="control-label">首页是否显示</label>				
						<div class="controls">
							<?php 
								echo CHtml::listBox('channel[is_show_index]', $pcinfo['is_show_index'], array(0=>'不显示',1=>'显示'),array('size'=>'1','class'=>'input-small'));
							?>
							<span class="label label-important" style="margin-left:10px; display:none;"></span>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">展示排序</label>				
						<div class="controls">
							<?php 
								echo CHtml::textField('channel[index_sort]',$pcinfo['index_sort'],array('class'=>'input-small focused'));
							?>
							<span class="label label-important" style="margin-left:10px; display:none;"></span>
						</div>
					</div>
						
				  	
					<div class="form-actions">
						<input class="btn btn-primary" value="提交" type="submit" name="yt1">
						<?php 
						if(isset($pcinfo['channel_id'])){
							echo CHtml::hiddenField('channel[channel_id]',$pcinfo['channel_id']);							
						}
						?>			
					</div>
				</fieldset>
			</form>
			<?php }?>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//显示添加父频道
	$("#channel_create_parent").click(function(){
		$('#parent_box').toggle();
	});
	
	//执行添加父频道操作
	$("#channel_create_parent_do").click(function(){
		var channel_name = $("#channel_channel_name").attr('value');
		var is_show_index = $("#channel_is_show_index").attr('value');
		var index_sort = $("#channel_index_sort").attr('value');

		if(!channel_name){
			$("#channel_channel_name").next('span').html('请输入频道名称').show();
			return false;
		}else{
			$("#channel_channel_name").next('span').hide();
		}

		$.ajax({
			url:"<?php echo $this->createUrl('channel/createtheme');?>",
			type:'post',
			dataType:'html',
			data:{"channel_name":channel_name,'op':'addChannel','is_show_index':is_show_index,'index_sort':index_sort},
			success:function(msg){
				if(msg == 1){
					$(this).next('span').html('不合法请求').show();
				}else if(msg == 2){
					$("#channel_channel_name").next('span').html('频道名不能为空').show();
				}else if(msg == 3){
					$("#channel_is_show_index").next('span').html('是否在首页显示不能为空').show();
				}else if(msg == 4){
					$("#channel_channel_name").next('span').html('该频道名称已经被取用').show();
				}else{
					$("#channel_channel_name").next('span').hide();
					$("#channel_is_show_index").next('span').hide();
					$("#channel_index_sort").next('span').hide();

					$('#parent_box').hide();
					var ohtml = $("#channelsub_channel_id").html();
					ohtml += msg;
					$("#channelsub_channel_id").html(ohtml);
				}
			}
		});	
	});

	//子频道添加
	$("#sub_submit_channel").click(function(){
		
		if(!$("#channelsub_channel_id").attr('value')){
			$("#channelsub_channel_id").parent("div").children().eq(-1).html("请选择父频道").show();
			return false;
		}else{
			$("#channelsub_channel_id").parent("div").children().eq(-1).html("").hide();
		}
		
		if(!$("#channelsub_sub_name").attr('value')){
			$("#channelsub_sub_name").next('span').html("子频道名不能为空").show();
			return false;
		}else{
			$("#channelsub_sub_name").next('span').html('').hide();
		}

		if(!$("#channelsub_desc").attr('value')){
			$("#channelsub_desc").next('span').html("频道描述不能为空").show();
			return false;
		}else{
			$("#channelsub_desc").next('span').html('').hide();
		}
		
		if(!$("#channelsub_dotey_sort").attr('value')){
			$("#channelsub_dotey_sort").next('span').html("请选择主播排序方式 ").show();
			return false;
		}else{
			$("#channelsub_dotey_sort").next('span').html("").hide();
		}
		
	});
	
});
</script>