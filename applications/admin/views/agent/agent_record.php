<?php
$this->breadcrumbs = array('代理管理','销售记录');
?>
<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:9%;margin-left:10px;padding:3px 3px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!isset($isAjax) || !$isAjax){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>销售记录</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<?php }?>
		<div class="box-content">
			<form class="form-horizontal" id="search" action="<?php echo $this->createUrl('agent/record');?>" method="post">
				<fieldset>
				代理id：<?php echo CHtml::textField('agent_id',isset($condition['agent_id'])?$condition['agent_id']:'',	array('class'=>'input-small'));?>
				<?php echo CHtml::submitButton('search',array('class'=>'btn','value'=>'搜索','id'=>'search_submit'));?>
				
				<?php echo CHtml::submitButton('recordsExcel',array('class'=>'btn','value'=>'导出Excel','id'=>'recordsExcel'));?>
				</fieldset>
			</form>
			<table class="table table-bordered" id="agent_list_table">
				<thead>
					<tr>
						<th>购买时间</th>
						<th>代理人</th>
						<th>玩家</th>
						<th>购买道具</th>
						<th>购买数量</th>
						<th>购买价格（皮蛋）</th>
						<th>提成金额（元）</th>
					</tr>
				</thead>
				<tbody>
					<?php if($list['count']>0){?>
				  	<?php foreach($list['list'] as $row){?>
				  	<tr>
						<td><?php echo date("Y-m-d H:i:s",$row['create_time']);?> </td>
						<td><?php echo isset($row['agent_nickname'])?$row['agent_nickname']:"";?>(<?php echo $row['agent_id']?>)</td>
						<td><?php echo isset($row['user_nickname'])?$row['user_nickname']:"";?>(<?php echo $row['uid']?>)</td>
						<td><?php echo isset($row['goods_name'])?$row['goods_name']:"";?></td>
						<td><?php echo $row['goods_num'];?></td>
						<td>	<?php echo intval($row['pipieggs']);?></td>
						<td>	<?php echo $row['agent_income'];?></td>
					</tr>
				  	<?php }?>
				  	<tr>
				  		<td colspan="7">
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
					            'pages' => $list['pager'],    
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
						<td colspan="11">没有查到数据</td>
					</tr>
					</tbody>
				  	<?php }?>
				  </tbody>
			</table>
		</div>
	</div>
		
</div>

<!-- 浮层 -->
<div class="modal hide fade" id="user_list_manage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>代理主播管理</h3>
	</div>
	<div class="modal-body" id="user_list_manage_body"></div>
</div>
<script type="text/javascript">
//下载Excel
$('#recordsExcel').click(function(){
	var action = $(this).parents('form').attr('action');
	$(this).parents('form').attr('action',"<?php echo $this->createUrl('agent/record',array('op'=>'exportRecords'));?>");
	return true;
});
$('#search_submit').click(function(){
	var action = $(this).parents('form').attr('action');
	$(this).parents('form').attr('action',"<?php echo $this->createUrl('agent/record');?>");
	return true;
});
</script>
