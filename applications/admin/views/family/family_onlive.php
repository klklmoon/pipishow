<?php
$this->breadcrumbs = array('家族管理','开播查询');
$days = date('t',strtotime($condition['live_time_on']));
?>
<style type="text/css">
	.table td, .table th {padding:2px;}
</style>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch"
				action="<?php echo $this->createUrl('family/live');?>"
				method="post">
				<fieldset>
					<div class="control-group">
					<span>家族ID:</span> 
					<?php echo CHtml::textField('familyId',isset($condition['familyId'])?$condition['familyId']:'',array('class'=>'input-small'));?>
					<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
					<?php echo CHtml::submitButton('dl_excel',array('class'=>'btn','value'=>'下载Excel','id'=>'dl_excel'));?>
				</div>
				</fieldset>
			</form>
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>昵称-UID</th>
						<th>有效天数</th>
						<th>小时数</th>
						<?php for ($i=1;$i<=$days;$i++){?>
						<th><?php echo $i;?></th>
						<?php }?>
					</tr>
				</thead>
				<tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $uinfo){?>
				  	<tr>
						<td><?php echo $uinfo['nickname'].'-'.$uinfo['uid'];?> </td>
						<td><?php echo count($uinfo['has_days']);?> </td>
						<td><?php echo number_format($uinfo['has_hours']/3600,2);?> </td>
						<?php for ($i=1;$i<=$days;$i++){?>
						<td>
							<?php echo !empty($uinfo['detail'][$i])?number_format($uinfo['detail'][$i]/3600,2):0;?>
						</td>
						<?php }?>
					</tr>
				  	<?php }?>
				  	<tr>
						<td colspan="<?php echo $days+3;?>">
							<div class="pagination pagination-centered">
				  			<?php    
							$this->widget('CLinkPager',array(
					            'header'=>'',  
								'firstPageCssClass' => '',  
					            'firstPageLabel' => '首页',    
					            'lastPageLabel' => '末页',  
					            'lastPageCssClass' => '',  
								'previousPageCssClass' =>'prev disabled',  
					            'prevPageLabel' => '上一页',    
					            'nextPageLabel' => '下一页', 
								'nextPageCssClass' => 'next', 
								'selectedPageCssClass' => 'active',
								'internalPageCssClass' => '',
								'htmlOptions' => array('class'=>''),
					            'pages' => $pager,    
					            'maxButtonCount'=>8    
								)
							);
							?>
							</div>
						</td>
					</tr>
				  	<?php }else{?>
				<tbody>
					<tr>
						<td colspan="<?php echo $days+3;?>">没有配置的数据</td>
					</tr>
				</tbody>
				  	<?php }?>
				  </tbody>
			</table>
		</div>
	</div>
	<!--/span-->
</div>

<script>
$(function() {
	//注册开始时间
	$( '#form_live_time_on' ).datepicker(
		{ 
			//showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm'
		}
	);
	//download excel
	$('#dl_excel').click(function(e){
		var action = $(this).parents('form').attr('action');
		$(this).parents('form').attr('action',action+'&op=dlLive');
		return true;
	});
});
</script>