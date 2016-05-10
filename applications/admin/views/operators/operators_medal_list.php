<?php
$this->breadcrumbs = array('运营工具','勋章管理');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2>勋章列表</h2>
			<div class="box-icon">
				<a class="btn btn-round" href="#" title="添加勋章"><i class="icon-plus"></i></a>
			</div>
		</div>
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('operators/medal');?>" method="post">
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>名称</th>
						  <th>类型</th>
						  <th>描述</th>
						  <th>图标</th>
						  <th>创建时间</th>
						  <th>操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $v){?>
				  	<tr>
				  		<td><?php echo $v['name'];?></td>
				  		<td><?php echo $medalType[$v['type']];?></td>
				  		<td><?php echo $v['desc'];?></td>
				  		<td><img src="<?php echo $userMedal->getMedalIcon($v['icon']);?>"/></td>
				  		<td><?php echo date('Y-m-d H:i:s',$v['ctime']);?></td>
				  		<td>
				  			<a class="btn" href="#" mid="<?php echo $v['mid'];?>" title="编辑"> <i class="icon-edit"></i></a>
				  			<a class="btn" href="#" mid="<?php echo $v['mid'];?>" title="删除"> <i class="icon-remove"></i></a>
				  		</td>
				  	</tr>
				  	<?php }?>
				  	<?php }else{?>
				  		<tbody>
					  		<tr><td colspan="6">没有配置的数据</td></tr>
				  		</tbody>
				  	<?php }?>
				  </tbody>
			  </table>
			  </form>
		</div>
	</div><!--/span-->
</div>

<!-- 浮层 -->
<div class="modal hide fade" id="kefu_list_manage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>勋章管理</h3>
	</div>
	<div class="modal-body" id="kefu_list_manage_body">
	</div>
</div>

<script>
$(function() {
	//添加勋章
	$(".icon-plus").click(function(e){
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('operators/addMedal');?>",
			dataType:'html',
			success:function(msg){
				if(msg){
					$('#kefu_list_manage_body').html(msg);
				}else{
					$('#kefu_list_manage_body').html('信息获取失败');
				}
				e.preventDefault();
				$('#kefu_list_manage').modal('show');
			}
		});	
	});
	
	//编辑操作
	$(".box-content .icon-edit").click(function(e){
		var mid = $(this).parents('a').attr('mid');
		if(mid){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('operators/addmedal');?>",
				dataType:'html',
				data:{'mid':mid},
				success:function(msg){
					if(msg){
						$('#kefu_list_manage_body').html(msg);
					}else{
						$('#kefu_list_manage_body').html('信息获取失败');
					}
					e.preventDefault();
					$('#kefu_list_manage').modal('show');
				}
			});
		}
	});
	//删除
	$(".box-content .icon-remove").click(function(e){
		var mid = $(this).parents('a').attr('mid');
		var obj = this;
		if(mid){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('operators/addMedal');?>",
				dataType:'html',
				data:{'mid':mid,'op':'delMedal'},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						$('#kefu_list_manage_body').html(msg);
						e.preventDefault();
						$('#kefu_list_manage').modal('show');
					}
				}
			});
		}
	});
});
</script>