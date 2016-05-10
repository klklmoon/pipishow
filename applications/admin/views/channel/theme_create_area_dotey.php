<?php
$this->breadcrumbs = array('频道管理','添加地区频道主播');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!$this->isAjax){?>
		<div data-original-title="" class="box-header well">
			<h2><i class="icon-edit"></i> 添加地区频道主播</h2>
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
			
			<form class="form-horizontal" id="purview_add" action="<?php echo $this->createUrl('channel/createareadotey',array('op'=>'addAreaDotey'));?>" method="post">		
				<fieldset>
				  	<div class="control-group">
				  		<span>主播等级</span>
			  			<?php echo CHtml::listBox('doteyarea[dotey_rank]', '', $this->getDoteyRank(),array('size'=>1,'class'=>'input-small','empty'=>' '));?>
				  		<span>主播用户名/ID</span>
				  		<?php echo CHtml::textField('doteyarea[dotey_name]','',array('class'=>"input-small"));?>
					  	<span>开播时间:</span>
					  	<?php echo CHtml::textField('doteyarea[start_time]',isset($condition['start_time'])?$condition['start_time']:'',array('class'=>'date_ui input-small'));?>
			  			<span>省</span>
			  			<select id="doteyarea_province" name="doteyarea[province]" class='input-small'> </select>
			  			<span>市</span>
			  			<select id="doteyarea_city" name="doteyarea[city]" class="input-small"></select>
			  			<input class="btn" value="搜索" id="search_dotey_info" type="button">
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
	//城市初始化
	init("doteyarea_province",'<?php echo isset($condition['provine'])?$condition['provine']:'选择省份'?>',"doteyarea_city",'<?php echo isset($condition['city'])?$condition['city']:'选择城市'?>');

	//注册开始时间
	$( '#doteyarea_start_time' ).datepicker(
		{ 
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	
	//检测搜索主播用户信息
	$("#search_dotey_info").click(function(){
		var dotey_name = $('#doteyarea_dotey_name').attr('value');
		var dotey_rank = $('#doteyarea_dotey_rank').attr('value');
		var start_time = $('#doteyarea_start_time').attr('value');
		var province = $('#doteyarea_province').attr('value');
		var city = $('#doteyarea_city').attr('value');
		
		if(province == '选择省份'){
			province = null;
		}

		if(city == '选择城市'){
			city = null;
		}
		
		if(dotey_name || dotey_rank || start_time || province || city){
			$.ajax({
				url:"<?php echo $this->createUrl('channel/createareadotey');?>",
				dataType:'text',
				type:'post',
				data:{'dotey_name':dotey_name,'dotey_rank':dotey_rank,'start_time':start_time,'province':province,'city':city,'op':'getDoteyList'},
				success:function(msg){
					$('#search_dotey_info_result').html(msg);
				}
			});
		}else{
			alert('缺少搜索参数');
			return false;
		}
	});
	
	//子频道添加
	$("#sub_submit_channel").click(function(){
		if($('input[name="areacat[]"]:checked').length == 0){
			alert('地区频道分类不能为空，请勾选！');
			return false;
		}
		if($('input[name="doteylist[]"]:checked').length == 0){
			alert('您需要先筛选出符合条件的主播，并勾选才能提交');
			return false;
		}
	});
});
</script>