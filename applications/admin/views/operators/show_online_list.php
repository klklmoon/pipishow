<?php
$this->breadcrumbs = array('运营管理','直播在线人数统计');
?>
<style type="text/css">
	#basicGChart { width: 100%; height: 300px }
</style>
<script>
var title = '在线人数统计';

$(function () {
	var time_str = <?php echo $time_str;?>;
	var total_num = <?php echo $total_num;?>;
	var max_num = <?php echo $max_num;?>;

	//日期数组
	$('#basicGChart').gchart({
		type: 'line',//图表类型
		title: '直播在线人数曲线图', //图表标题
		series: [
			$.gchart.series(total_num,'red')//图表数据
		],
		axes: [//图表坐标轴
			$.gchart.axis('left', 0, max_num, 'blue'),
			$.gchart.axis('bottom',time_str,'008000')
		],
		minValue: 0, 
		maxValue: max_num, 
		legend: 'top'});
});

</script>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('operators/getshowonline');?>" method="post">
				<fieldset>
					<div class="control-group">
					<span>查询时间:</span>
					<?php echo CHtml::textField('start_date',$start_date,array('class'=>'date_ui input-small'));?>&nbsp;至&nbsp;
					<?php echo CHtml::textField('end_date',$end_date,array('class'=>'date_ui input-small'));?>
					<span>是否分页:</span>
					<?php echo CHtml::checkBox('page_flag',$page_flag);?>
					<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
					</div>
				</fieldset>
			</form>
		</div>
		<!-- 曲线图 -->
		<div class="box-content">
			<div id="basicGChart"> </div>
		</div>
		
		<div class="box-content">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed;" class="table table-bordered">
				<tr class="tr2">
		            <td width="20%">统计时间</td>
		            <td width="20%">总在线人数</td>
		            <td width="20%">电信在线</td>
		            <td width="20%">网通在线</td>
		            <td width="20%">移动在线</td>
		            <td width="20%">教育网在线</td>
				</tr>
				<tbody id="content">
					<?php if ($list){?>
					<?php foreach($list as $v){?>
					<tr>
						<td><?php echo $v['time']?></td>
						<td><?php echo $v['total_num']?></td>
						<td><?php echo $v['tel_num']?></td>
						<td><?php echo $v['cnc_num']?></td>
						<td><?php echo $v['cnc_num']?></td>
						<td><?php echo $v['edu_num']?></td>
					</tr>
					<?php }?>
					<?php }?>
				</tbody>
    	<?php if ($page_flag) { ?>
    	<tr class="tr2">
            <td colspan="10" align="left">
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
        <?php } ?>
	</table>
		</div>
	</div>
	
</div>
<script>
$(function() {
	$('#start_date').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd'});
	});
	$('#end_date').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd'});
	});
	$(':submit').click(function(){
		var start_date=$("input[name='start_date']").val();
		var end_date=$("input[name='end_date']").val();
		d1Arr=start_date.split('-');
		d2Arr=end_date.split('-');
		v1=new Date(d1Arr[0],d1Arr[1],d1Arr[2]);
		v2=new Date(d2Arr[0],d2Arr[1],d2Arr[2]);
		if( v1.getTime() > v2.getTime()){
			alert('开始日期不能大于结束日期');
			return false;
		}
		return true;
	});
});
</script>