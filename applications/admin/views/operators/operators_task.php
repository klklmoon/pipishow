<?php
$this->breadcrumbs = array('运营工具','新手任务');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2>新手任务列表</h2>
			<div class="box-icon">
				<a class="btn btn-round" href="#" title="添加新手任务"><i class="icon-plus"></i></a>
			</div>
		</div>
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('operators/task');?>" method="post">
			
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th width="60px;">任务编号</th>
						  <th width="150px;">任务名称</th>
						  <th>任务描述</th>
						  <th width="80px;">奖励皮蛋数</th>
						  <th width="200px;">任务地址</th>
						  <th width="60px;">任务状态</th>
						  <th width="40px;">操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $data){?>
				  	<tr>
				  		<td><?php echo $data['tid'];?></td>
				  		<td><?php echo $data['name'];?></td>
				  		<td><?php echo $data['content'];?></td>
				  		<td><?php echo $data['pipiegg'];?></td>
				  		<td><?php echo $data['url'];?></td>
				  		<td><?php echo $data['status']?"启用":"停用";?></td>
				  		<td>
				  			<a class="btn" href="#" dataId="<?php echo $data['tid'];?>" title="编辑"> <i class="icon-edit"></i></a>
				  		</td>
				  	</tr>
				  	
				  	<?php }?>
				  	<?php }else{?>
				  		<tbody>
					  		<tr><td colspan="4">没有配置的数据</td></tr>
				  		</tbody>
				  	<?php }?>
				  </tbody>
			  </table>
			  </form>
		</div>
	</div><!--/span-->
</div>

<!-- 浮层 -->
<div class="modal hide fade span10" id="newsNotice_list_manage" style="left:5%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>新手任务管理</h3>
	</div>
	<div class="modal-body" id="newsNotice_list_manage_body">
	</div>
</div>

<script>
$(function() {
	//新手任务帮助
	$(".icon-plus").click(function(e){
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('operators/addTask');?>",
			dataType:'html',
			success:function(msg){
				if(msg){
					$('#newsNotice_list_manage_body').html(msg);
				}else{
					$('#newsNotice_list_manage_body').html('信息获取失败');
				}
				e.preventDefault();
				$('#newsNotice_list_manage').modal('show');
			}
		});	
	});
	
	//编辑操作
	$(".box-content .icon-edit").click(function(e){
		var dataId = $(this).parents('a').attr('dataId');
		if(dataId){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('operators/addTask');?>",
				dataType:'html',
				data:{'dataId':dataId,'op':'getTaskInfo'},
				success:function(msg){
					if(msg){
						$('#newsNotice_list_manage_body').html(msg);
					}else{
						$('#newsNotice_list_manage_body').html('信息获取失败');
					}
					e.preventDefault();
					$('#newsNotice_list_manage').modal('show');
				}
			});
		}
	});
	
});
</script>