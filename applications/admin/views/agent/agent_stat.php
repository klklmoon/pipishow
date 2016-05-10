<?php
$this->breadcrumbs = array('代理管理','销售统计');
?>
<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:9%;margin-left:10px;padding:3px 3px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!isset($isAjax) || !$isAjax){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>月度销售统计表</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<?php }?>
		<div class="box-content">
			<form class="form-horizontal" id="search" action="<?php echo $this->createUrl('agent/stat');?>" method="post">
				<fieldset>
				月份：<?php echo CHtml::listBox('month', $month,$monthList,array('class'=>'input-small','size'=>1,'style'=>'width:100px;'));?>
				<?php echo CHtml::submitButton('search',array('class'=>'btn','value'=>'搜索','id'=>'search_submit'));?>
				代理提成金额：<?php echo $amount['sum_income'];?>元 &nbsp;
				道具销售总金额:<?php echo intval($amount['sale_pipieggs']);?>皮蛋
				<?php echo CHtml::submitButton('statExcel',array('class'=>'btn','value'=>'导出Excel','id'=>'statExcel'));?>
				</fieldset>
			</form>
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>代理人</th>
						<th>授权状态</th>
						<th>销售笔数</th>
						<th>销售金额（皮蛋）</th>
						<th>提成收入（元）</th>
						<th>操作项</th>
					</tr>
				</thead>
				<tbody>
					<?php if($list['count']>0){?>
				  	<?php foreach($list['list'] as $agent){?>
				  	<tr>
						<td><?php echo $agent['agent_nickname'];?>(<?php echo $agent['agent_id']?>) </td>
						<td><?php echo $agent['agent_status'] == 0 ? '正常':'停用';?> </td>
						<td><?php echo $agent['sale_count'];?> </td>
						<td><?php echo intval($agent['sale_pipieggs']);?> </td>
						<td><?php echo $agent['sum_income'];?> </td>
						<td>
							<span class="btn" title="查看销售记录">
							<a href="<?php echo $this->createUrl('agent/record',array('agent_id'=>$agent['agent_id']));?>">查看销售记录</a>
							</span>
						</td>
					</tr>
				  	<?php }?>
				  	<tr>
				  		<td colspan="6">
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
$('#statExcel').click(function(){
	$(this).parents('form').attr('action',"<?php echo $this->createUrl('agent/stat',array('op'=>'exportStat'));?>");
	return true;
});
$('#search_submit').click(function(){
	$(this).parents('form').attr('action',"<?php echo $this->createUrl('agent/stat');?>");
	return true;
});
</script>