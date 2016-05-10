<?php
$this->breadcrumbs=array(
		'服务器列表',
	);
?>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-book"></i>服务器列表</h2>
			<div class="box-icon">
				<a href="javascript:void(0);" class="btn btn-setting btn-round" title="添加服务器"><i class="icon-plus"></i></a>
				<a href="javascript:void(0);" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>id</th>
						<th>主播所访问服务器</th>
						<th>用户所访问服务器</th>
						<th>已分派主播</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($list as $k=>$v){?>
					<tr>
						<td><?php echo $k;?></td>
						<td><?php echo $v['import_host'];?></td>
						<td><?php echo $v['export_host'];?></td>
						<td><?php echo $v['use_num'];?></td>
						<td><a class="btn" href="javascript:void(0);" title="修改"><i class="icon-edit" data="<?php echo $k;?>"></i></a></td>
					<tr>
					<?php } ?>
				</tbody>
			</table>
			
		</div>
	</div>
</div>


<div class="modal hide fade span3" id="global_servlist_edit" style="left:40%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>修改服务器列表</h3>
	</div>
	<div class="modal-body" id="global_chatword_edit_body">
		<table>
			<tr><td>主播服务器</td><td><input type="text" name="doteyServ" /></td></tr>
			<tr><td>用户服务器</td><td><input type="text" name="userServ" /></td></tr>
		</table>
		<button class="btn btn-large btn-danger">确认</button>
		<button class="btn btn-large btn-success">取消</button>
	</div>
</div>


<script type="text/javascript">
$(function(){
	$('.icon-edit').parent('a').click(function(){
		var id = $(this).find('i').attr('data');
		var td = $(this).parents('tr').children('td');
		var doteyServ = $(td[1]).html();
		var userServ = $(td[2]).html();
		$('input[name=doteyServ]').val(doteyServ);
		$('input[name=userServ]').val(userServ);
		$('#global_servlist_edit').modal('show');
		
		$('#global_chatword_edit_body').find('button.btn-danger').unbind('click');
		$('#global_chatword_edit_body').find('button.btn-danger').bind('click',function(){
			
			doteyServ = $('input[name=doteyServ]').val();
			userServ = $('input[name=userServ]').val();
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/editServerList');?>",
				dataType:'json',
				data:{'id':id, 'doteyServ':doteyServ, 'userServ':userServ},
				success:function(e){
					$(td[1]).html(e.data.import_host);
					$(td[2]).html(e.data.export_host);
					$("#global_servlist_edit").modal('hide');
				}
			});
			
		});
	});
	
	$('.btn-success').click(function(){
		$("#global_servlist_edit").modal('hide');
	});

	//新增直播服务器
	$('.icon-plus').click(function(){
		$('input[name=doteyServ]').val('');
		$('input[name=userServ]').val('');
		$('#global_servlist_edit').children('.modal-header').children('h3').html('添加直播服务器');
		$('#global_servlist_edit').modal('show');
		$('#global_chatword_edit_body').find('button.btn-danger').unbind('click');
		$('#global_chatword_edit_body').find('button.btn-danger').bind('click',function(){
			doteyServ = $('input[name=doteyServ]').val();
			userServ = $('input[name=userServ]').val();
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/addservice');?>",
				dataType:'json',
				data:{'doteyServ':doteyServ, 'userServ':userServ},
				success:function(e){
					$("#global_servlist_edit").modal('hide');
					window.location.reload();
				}
			});
			
		});
	});
});
</script>