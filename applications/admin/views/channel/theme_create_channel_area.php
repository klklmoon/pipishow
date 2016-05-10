<?php
$this->breadcrumbs = array('频道管理','添加频道地区');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!$this->isAjax){?>
		<div data-original-title="" class="box-header well">
			<h2><i class="icon-edit"></i> 添加频道地区</h2>
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
			
			<form class="form-horizontal" id="purview_add" action="<?php echo $this->createUrl('channel/createchannelarea',array('op'=>'addChannelArea'));?>" method="post">		
				<fieldset>
					<div class="control-group">
						<label class="control-label">父频道</label>				
						<div class="controls">
							<?php 
								$channelId = Yii::app()->request->getParam('channelId');
								$select1 = isset($subInfo['channel_id'])?$subInfo['channel_id']:$channelId;
								echo CHtml::listBox('channelarea[channel_id]', $select1, $pchannel,array('size'=>'1','empty'=>'-请选择-','class'=>'input-small'));
							?>
							<span class="label label-important" style="margin-left:10px; display:none;"></span>
						</div>
				  	</div> 
				  	
				  	<div class="control-group">
						<label class="control-label">子频道</label>				
						<div class="controls" id="sub_channel_list">
							<span>
								<?php 
									if(isset($subInfo['area_channel_id'])){
										echo CHtml::checkBoxList('channelarea[area_channel_id]', $subInfo['area_channel_id'], $subInfo['sub_channel_list'],array('separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;','labelOptions'=>array('class'=>'checkbox inline')));
									}
								?>
							</span>
							<span class="label label-important" style="margin-left:10px; display:none;"></span>
						</div>
				  	</div>
				  	
				  	<div class="control-group">
						<label class="control-label">省份</label>				
						<div class="controls">
							<select id="channelarea_province" name="channelarea[province]" class='input-large'> </select>
							<span class="label label-important" style="margin-left:10px; display:none;"></span>
						</div>
				  	</div>
				  	
				  	<div class="control-group">
						<label class="control-label">城市</label>				
						<div class="controls">
							<select id="channelarea_city" name="channelarea[city][]" multiple="multiple" size="10" class="input-large"></select>
							<span class="label label-important" style="margin-left:10px; display:none;"></span>
						</div>
				  	</div>

					<div class="form-actions">
						<input class="btn btn-primary" value="提交" type="submit" name="yt1" id="sub_submit_channel">
						<?php 
						if(isset($subInfo['area_channel_id'])){
							echo CHtml::hiddenField('channelsub[area_channel_id]',$subInfo['area_channel_id']);							
							echo CHtml::hiddenField('channelsub[city]',$subInfo['city']);							
						}
						?>			
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//城市初始化
	<?php 
		if(isset($subInfo['area_channel_id'])){
	?>
	_citys = <?php echo $subInfo['city'];?>;
	init("channelarea_province","<?php echo $subInfo['province'];?>","channelarea_city",_citys);
	<?php 
		}else{
	?>
	init("channelarea_province","浙江省","channelarea_city","杭州市");
	<?php }?>
	
	
	//初始化子频道options
	$("#channelarea_channel_id").change(function(){
		var channelId = $(this).attr('value');
		if(channelId){
			$.ajax({
				url:"<?php echo $this->createUrl('channel/createchannelarea');?>",
				type:'post',
				dataType:'html',
				data:{'channelId':channelId,'op':'getSubChannel'},
				success:function(msg){
					if(msg == 1){
						$("#channelarea_channel_id").parent("div").children().eq(-1).html("请求不合法").show();
					}else if(msg == 2){
						$("#channelarea_channel_id").parent("div").children().eq(-1).html("缺少参数").show();
					}else if(msg == 3){
						$("#channelarea_channel_id").parent("div").children().eq(-1).html("没有子频道数据").show();
					}else{
						$("#channelarea_channel_id").parent("div").children().eq(-1).html('').hide();
						$("#sub_channel_list span").html(msg);
					}
				},
			});
		}
	});
	
	//提交操作
	$("#sub_submit_channel").click(function(){
		
		if(!$("#channelarea_channel_id").attr('value')){
			$("#channelarea_channel_id").parent("div").children().eq(-1).html("请选择父频道").show();
			return false;
		}else{
			$("#channelarea_channel_id").parent("div").children().eq(-1).html("").hide();
		}

		if(!$("input:checked").attr('checked')){
			$("input[name='channelarea[area_channel_id][]']").parents("div").children('span').eq(-1).html("请选择子频道").show();
			return false;
		}else{
			$("input[name='channelarea[area_channel_id][]']").parents("div").children('span').eq(-1).html("").hide();
		}
		
		if(!$("#channelarea_province").attr('value')){
			$("#channelarea_province").parent("div").children().eq(-1).html("请选择省份").show();
			return false;
		}else{
			$("#channelarea_province").parent("div").children().eq(-1).html("").hide();
		}

		if(!$("#channelarea_city").attr('value')){
			$("#channelarea_city").parent("div").children().eq(-1).html("请选择城市").show();
			return false;
		}else{
			$("#channelarea_city").parent("div").children().eq(-1).html("").hide();
		}
		
	});
	
});
</script>