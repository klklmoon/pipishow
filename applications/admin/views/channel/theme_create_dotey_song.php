<?php
$this->breadcrumbs = array('频道管理','添加点唱专区关系');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!$this->isAjax){?>
		<div data-original-title="" class="box-header well">
			<h2><i class="icon-edit"></i> 添加点唱专区关系</h2>
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
			
			<form class="form-horizontal" id="purview_add" action="<?php echo $this->createUrl('channel/createdoteysong',array('op'=>'addDoteySong'));?>" method="post">		
				<fieldset>
				  	<div class="control-group">
				  		<span>主播用户名/ID</span>
				  		<?php echo CHtml::textField('doteysong[dotey_name]','',array('class'=>"input-small"));?>
				  		&nbsp;&nbsp;&nbsp;<span>主播等级</span>
			  			<?php echo CHtml::listBox('doteysong[dotey_rank]', '', $this->getDoteyRank(),array('size'=>1,'class'=>'input-small','empty'=>' '));?>
					  	&nbsp;&nbsp;&nbsp;<span>开播时间:</span>
					  	<?php echo CHtml::textField('doteysong[start_time]',isset($condition['start_time'])?$condition['start_time']:'',array('class'=>'date_ui input-small'));?>
			  			&nbsp;&nbsp;&nbsp;<input class="btn" value="搜索" id="search_dotey_info" type="button">
				  	</div>
				  	<div class="control-group" id="search_dotey_info_result">
				  	</div>
					<div class="form-actions">
						<input class="btn btn-primary" value="提交" type="submit" name="yt1" id="sub_submit_channel">
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//注册开始时间
	$( '#doteysong_start_time' ).datepicker(
		{ 
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	
	//检测搜索主播用户信息
	$("#search_dotey_info").click(function(){
		var dotey_name = $('#doteysong_dotey_name').attr('value');
		var dotey_rank = $('#doteysong_dotey_rank').attr('value');
		var start_time = $('#doteysong_start_time').attr('value');

		if(dotey_name || dotey_rank || start_time){
			$.ajax({
				url:"<?php echo $this->createUrl('channel/createdoteysong');?>",
				dataType:'text',
				type:'post',
				data:{'dotey_name':dotey_name,'dotey_rank':dotey_rank,'start_time':start_time,'op':'getDoteyList'},
				success:function(msg){
					$('#search_dotey_info_result').html(msg);
				}
			});
		}
	});
	
	//子频道添加
	$("#sub_submit_channel").click(function(){
		if($('input:checked').length == 0){
			alert('您需要先筛选出符合条件的主播，并勾选才能提交');
			return false;
		}
	});
});
</script>