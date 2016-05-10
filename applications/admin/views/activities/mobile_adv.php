<?php
$this->breadcrumbs = array('活动管理','手机端活动公告');
?>
<div class="row-fluid sortable ui-sortable">
  	<div class="box span12">
  		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>手机端活动公告</h2>
			<div class="box-icon">
				<a href="javascript:void(0);" class="btn btn-setting btn-round" title="手机端活动公告"><i class="icon-plus"></i></a>
				<a href="javascript:void(0);" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		
		<div class="box-content">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th width="230px">广告图片</th>
						<th>标题</th>
						<th>排序</th>
						<th>连接地址</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if($list):?>
					<?php foreach($list as $k=>$v):?>
					<tr>
						<td>
							<?php if($v['image']):?>
							<img width="200px;" src="<?php echo Yii::app()->params['images_server']['url'].DIR_SEP.$v['image'];?>" />
							<?php else:?>
								未上传
							<?php endif;?>
						</td>
						<td><?php echo $v['title'];?></td>
						<td><?php echo $v['sort'];?></td>
						<td><?php echo $v['url'];?></td>
						<td>
							<a class="btn" title="编辑"><i class="icon-edit" operateId="<?php echo $v['adv_id'];?>"></i></a>
							<a class="btn" title="删除"><i class="icon-trash" operateId="<?php echo $v['adv_id'];?>"></i></a>
							<em class="hide" 
								data_adv_id="<?php echo $v['adv_id'];?>" 
								data_title="<?php echo $v['title'];?>" 
								data_sort="<?php echo $v['sort'];?>"
								data_url="<?php echo $v['url']?>"
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
		<h3>手机端活动公告</h3>
	</div>
	<div class="modal-body" id="global_chatword_edit_body">
		<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('activities/mobileAdv');?>" enctype="multipart/form-data" id="myForm">
		<input type="hidden" name="adv[adv_id]" value="0" />
		<input type="hidden" name="op" value="addAdv" />
		<table>
			<tr>
				<td>标题</td>
				<td><input type="text" name="adv[title]" /></td>
			</tr>
			<tr>
				<td>广告图片</td>
				<td>
					<input type="file" name="image" />
				</td>
			</tr>
			<tr>
				<td>排序</td>
				<td><input type="text" name="adv[sort]" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>值越大越靠前</td>
			</tr>
			<tr>
				<td>链接地址</td>
				<td><input type="text" name="adv[url]" /></td>
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
		$('#global_video_edit input[name="adv[adv_id]"]').val(0);
		$('#global_video_edit input[name="adv[sort]"]').val(0);
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
		
		$('#global_video_edit input[name="adv[adv_id]"]').val(operateId);
		$('#global_video_edit input[name="op"]').val('editAdv');
		$('#global_video_edit input[name="adv[title]"]').val(_em.attr('data_title'));
		$('#global_video_edit input[name="adv[sort]"]').val(_em.attr('data_sort'));
		$('#global_video_edit input[name="adv[url]"]').val(_em.attr('data_url'));
		
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
					url:'<?php echo $this->createUrl('activities/mobileAdv');?>',
					dataType:'html',
					data:{'op':'deleteAdv','id':operateId},
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