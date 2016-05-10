<?php
$this->breadcrumbs = array('家族管理','收入查询');
?>
<style type="text/css">
	.table td, .table th {padding:2px;}
</style>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('family/income');?>" method="post" style="margin:0px;" enctype="multipart/form-data">
				<fieldset>
				<div class="control-group">
					<span>结算时间:</span>
					<?php echo CHtml::listBox('month', $month, $monthList,array('class'=>'input-small','size'=>1,'style'=>'width:100px;'));?>
					<span>家族ID:</span> 
					<?php echo CHtml::textField('familyId',isset($condition['familyId'])?$condition['familyId']:'',array('class'=>'input-small'));?>
					<span>导入陪玩UID:</span>
					<?php echo CHtml::fileField('filter_uids','',array('class'=>'input-small'));?>
					<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
					<?php echo CHtml::submitButton('dl_excel',array('class'=>'btn','value'=>'下载Excel','id'=>'dl_excel'));?>
				</div>
				</fieldset>
			</form>
			<?php if(!empty($message)){?>
			<div style="padding:10px;"><?php echo $message;?></div>
			<?php }?>
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>昵称（UID）- 等级</th>
						<th>当月获得魅力点（总）</th>
						<th>当月获得魅力点（有效）</th>
						<th>当月获得魅力点（无效）</th>
						<th>当月已兑换金额</th>
						<th>族长收入</th>
					</tr>
				</thead>
				<tbody>
				  	<?php if(!empty($data['list'])){?>
				  	<?php foreach($data['list'] as $uinfo){?>
				  	<tr>
						<td><?php echo $uinfo['nickname'];?>（<?php echo $uinfo['uid'];?>）- <?php echo $uinfo['rank']?></td>
						<td><?php echo $uinfo['points'];?> </td>
						<td><?php echo $uinfo['points_valid'];?> </td>
						<td><?php echo $uinfo['points_invalid'];?> </td>
						<td><?php echo $uinfo['exchange'];?> </td>
						<td><?php echo $uinfo['family_rmb'];?> </td>
					</tr>
				  	<?php }?>
				  	<?php }else{?>
					<tr>
						<td colspan="6">没有配置的数据</td>
					</tr>
				  	<?php }?>
				</tbody>
			</table>
		</div>
	</div>
	<!--/span-->
</div>

<script>
$(function() {
	//download excel
	$('#dl_excel').click(function(e){
		var action = $(this).parents('form').attr('action');
		$(this).parents('form').attr('action',action+'&op=dlIncome');
		return true;
	});
});
</script>