<?php
$this->breadcrumbs = array('标签管理','标签列表');
?>
<div class="row-fluid sortable ui-sortable">
  	<div class="box span12">
  		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>标签列表</h2>
			<div class="box-icon">
				<a href="javascript:void(0);" class="btn btn-setting btn-round" title="添加标签"><i class="icon-plus"></i></a>
				<a href="javascript:void(0);" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		
		<div class="box-content">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>标签名称</th>
						<th>排序值</th>
						<th>主播数</th>
						<th>状态</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if($list):?>
					<?php foreach($list as $k=>$v):?>
					<tr>
						<td><?php echo $v['tag_name'];?></td>
						<td><?php echo $v['sort'];?></td>
						<td><?php echo $v['use_nums'];?></td>
						<td><?php echo $v['is_display'] ? '显示' : '隐藏';?></td>
						<td>
							<a class="btn" title="编辑"><i class="icon-edit" operateId="<?php echo $v['tag_id'];?>"></i></a>
							<a class="btn" title="删除"><i class="icon-trash" operateId="<?php echo $v['tag_id'];?>"></i></a>
							<em class="hide" 
								data_tag_name="<?php echo $v['tag_name'];?>" 
								data_sort="<?php echo $v['sort'];?>"
								data_is_display="<?php echo $v['is_display']?>"
							></em>
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
		<h3>印象标签</h3>
	</div>
	<div class="modal-body" id="global_chatword_edit_body">
		<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('tag/list');?>" enctype="multipart/form-data" id="myForm">
		<input type="hidden" name="form[tag_id]" value="0" />
		<input type="hidden" name="op" value="addTag" />
		<table>
			<tr>
				<td>标签名称</td>
				<td><input type="text" name="form[tag_name]" /></td>
			</tr>
			<tr>
				<td>排序</td>
				<td><input type="text" name="form[sort]" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>值越大越靠前</td>
			</tr>
			<tr>
				<td>状态</td>
				<td>
					<lable><input type="radio" name="form[is_display]" value='0'  /> 隐藏</lable>
					<lable><input type="radio" name="form[is_display]" value='1'  /> 显示</lable>
				</td>
			</tr>
		</table>
		</form>
		<button class="btn btn-large btn-danger">确认</button>
		<button class="btn btn-large btn-success">取消</button>
	</div>
</div>

<div class="modal hide fade span3" id="loading" style="left:40%;top:50%;width:152px">
	<div class="box span12">
		<div class="box-content">
			<div class="tab-content" style="overflow-x:hidden;overflow-y:hidden;">
				<div id="loading" style="text-align:center">努力加载中...<div class="center"></div></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(function(){
	$('.box-icon .icon-plus').parent('a').click(function(e){
		$('#global_video_edit input[name="form[tag_id]"]').val(0);
		$('#global_video_edit input[name="form[sort]"]').val(0);
		$('#global_video_edit input[name="form[is_display]"]').eq(1).attr('checked', true);
		$('#global_video_edit').modal('show');
	});
	
	// 添加新的广告
	$('#global_video_edit .btn-danger').click(function(){
		$('#global_video_edit form').submit();
	});
	
	// 广告编辑
	$('div.box-content i.icon-edit').parent('a').click(function(){
		var operateId = $(this).children('i.icon-edit').attr('operateId');
		var _em = $(this).parents('tr').children('td').find('em');
		
		$('#global_video_edit input[name="form[tag_id]"]').val(operateId);
		$('#global_video_edit input[name="op"]').val('editTag');
		$('#global_video_edit input[name="form[tag_name]"]').val(_em.attr('data_tag_name'));
		$('#global_video_edit input[name="form[sort]"]').val(_em.attr('data_sort'));
		$('#global_video_edit input[name="form[is_display]"]').eq(parseInt(_em.attr('data_is_display'))).attr('checked', true);
		
		$('#global_video_edit').modal('show');
	});
	
	// 删除
	$('div.box-content i.icon-trash').click(function(){
		if(confirm('是否删除？')){
			var operateId = $(this).attr('operateId');
			var obj = this;
			if(operateId){
				$('#loading').modal('show');
				$.ajax({
					type:'post',
					url:'<?php echo $this->createUrl('tag/list');?>',
					dataType:'html',
					data:{'op':'deleteTag','id':operateId},
					success:function(msg){
						$("#loading").modal('hide');
						if(msg == 1){
							$(obj).parents("tr").detach();
						}else{
							alert(msg);
						}
					}
				});
			}
		}
	});
	
	$('#global_video_edit .btn-success').click(function(){
		$("#global_video_edit").modal('hide');
	});
});
</script>