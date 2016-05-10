<?php
$this->breadcrumbs = array('广告管理','导航字链');
?>


<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>导航字链</h2>
			<div class="box-icon">
				<a href="javascript:void(0);" class="btn btn-setting btn-round" title="添加导航字链"><i class="icon-plus"></i></a>
				<a href="javascript:void(0);" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>标题文字</th>
						<th>连接地址</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if($list):?>
					<?php foreach($list as $k=>$v):?>
					<tr>
						<td><?php echo $v['subject'];?></td>
						<td><a href="<?php echo $this->createUrl($v['textlink']);?>"><?php echo $v['textlink'];?></a></td>
						<td>
							<a class="btn" title="编辑"><i class="icon-edit" operateId="<?php echo $v['operate_id'];?>"></i></a>
							<a class="btn" title="删除"><i class="icon-trash" operateId="<?php echo $v['operate_id'];?>"></i></a>
						</td>
					</tr>
					<?php endforeach;?>
					<?php endif;?>
				</tbody>
			</table>
		</div>
	</div>
</div>


<div class="modal hide fade span3" id="global_video_edit" style="left:40%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>导航字链</h3>
	</div>
	<div class="modal-body" id="global_chatword_edit_body">
		<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('adv/navigate',array('op'=>'addNavigate'));?>" enctype="multipart/form-data" id="myForm">
		<input type="hidden" name="indexForm[operate_id]" value="0" />
		<table>
			<tr>
				<td>标题文字</td>
				<td><input type="text" name="indexForm[subject]" /></td>
			</tr>
			<tr>
				<td>链接地址</td>
				<td><input type="text" name="indexForm[textlink]" /></td>
			</tr>
		</table>
		</form>
		<button class="btn btn-large btn-danger">确认</button>
		<button class="btn btn-large btn-success">取消</button>
	</div>
</div>


<script type="text/javascript">
$(function(){
	$('.box-icon .icon-plus').parent('a').click(function(e){
		document.getElementById("myForm").reset();
		$('#global_video_edit input[name="indexForm[operate_id]"]').val(0);
		$('#global_video_edit').modal('show');
	});
	
	// 添加新的导航字链
	$('#global_video_edit .btn-danger').click(function(){
		$('#global_video_edit form').submit();
	});
	
	// 导航字链编辑
	$('div.box-content i.icon-edit').parent('a').click(function(){
		var operateId = $(this).children('i.icon-edit').attr('operateId');
		var _tr = $(this).parents('tr');
		var _td = _tr.children('td');
		
		
		$('#global_video_edit input[name="indexForm[operate_id]"]').val(operateId);
		$('#global_video_edit input[name="indexForm[subject]"]').val($(_td[0]).html());
		$('#global_video_edit input[name="indexForm[textlink]"]').val($(_td[1]).find('a').html());
		
		$('#global_video_edit').modal('show');
	});
	
	// 删除
	$('div.box-content i.icon-trash').click(function(){
		var operateId = $(this).attr('operateId');
		var obj = this;
		if(operateId){
			$.ajax({
				type:'post',
				url:'<?php echo $this->createUrl('adv/homebanner');?>',
				dataType:'html',
				data:{'op':'delOperate','operateId':operateId},
				success:function(msg){
					if(msg == 1){
						$(obj).parents("tr").detach();
					}else{
						alert(msg);
					}
				}
			});
		}
	});
	
	$('#global_video_edit .btn-success').click(function(){
		$("#global_video_edit").modal('hide');
	});
});
</script>
