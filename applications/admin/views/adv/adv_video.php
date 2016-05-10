<?php
$this->breadcrumbs = array('广告管理','视频前贴');
?>


<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>视频前贴</h2>
			<div class="box-icon">
				<a href="javascript:void(0);" class="btn btn-setting btn-round" title="添加视频前贴"><i class="icon-plus"></i></a>
				<a href="javascript:void(0);" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>ID</th>
						<th>贴片名称</th>
						<th>类型</th>
						<th>时长</th>
						<th>地址</th>
						<th>投放目标</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if($list): ?>
					<?php foreach($list as $k=>$v): ?>
					<tr>
						<td><?php echo $v['operate_id'];?></td>
						<td><?php echo $v['subject'];?></td>
						<td><?php echo $config['type'][$v['content']['type']];?></td>
						<td><?php echo $v['content']['time'];?></td>
						<td><?php echo $v['textlink'];?></td>
						<td><?php echo $config['position'][$v['content']['position']];?></td>
						<td>
							<a class="btn"><i class="operate_status" operateId="<?php echo $v['operate_id'];?>">已<?php echo $config['status'][$v['content']['status']];?></i></a>
							<a class="btn" title="编辑"><i class="icon-edit" operateId="<?php echo $v['operate_id'];?>"></i></a>
							<a class="btn" title="删除"><i class="icon-trash" operateId="<?php echo $v['operate_id'];?>"></i></a>
							<em class="hide" 
								data_status="<?php echo $v['content']['status'];?>" 
								data_position="<?php echo $v['content']['position'];?>" 
								data_target="<?php echo $v['content']['target'];?>"
								data_type="<?php echo $v['content']['type']?>"
								data_channels="<?php echo $v['content']['channels']?>"
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
		<h3>添加视频前贴</h3>
	</div>
	<div class="modal-body" id="global_chatword_edit_body">
		<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('adv/video',array('op'=>'addVideo'));?>" enctype="multipart/form-data" id="myForm">
		<input type="hidden" name="indexForm[operate_id]" value="0" />
		<table>
			<tr>
				<td>贴片名称</td>
				<td><input type="text" name="indexForm[subject]" /></td>
			</tr>
			<tr>
				<td>贴片类型</td>
				<td>
					<select name="indexForm[content][type]" value="">
						<?php foreach($config['type'] as $k=>$v) :?>
						<option value="<?php echo $k;?>"><?php echo $v;?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td>选择文件</td>
				<td>
					<input type="file" name="indexForm[piclink]" />
					<input type="hidden" name="indexForm[_piclink]" />
				</td>
			</tr>
			<tr>
				<td>链接地址</td>
				<td><input type="text" name="indexForm[textlink]" /></td>
			</tr>
			<tr>
				<td>时长</td>
				<td><input type="text" name="indexForm[content][time]" /></td>
			</tr>
			<tr>
				<td>状态</td>
				<td>
					<select name="indexForm[content][status]">
					<?php foreach($config['status'] as $k=>$v) :?>
						<option value="<?php echo $k;?>"><?php echo $v;?></option>
					<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td>投放目标</td>
				<td>
					<select name="indexForm[content][position]">
						<?php foreach($config['position'] as $k=>$v) :?>
						<option value="<?php echo $k;?>"><?php echo $v;?></option>
						<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<div id="adv_video_channels" class="hide">
						<select name="indexForm[content][channels]">
							<option value="0">请选择频道</option>
							<?php foreach($config['channels'] as $k=>$v): ?>
							<option value="<?php echo $v['sub_channel_id'];?>"><?php echo $v['sub_name'];?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div id="adv_video_target" class="hide">
						<textarea name="indexForm[content][target]"></textarea>
						<br /><em class="red">*</em> 填写直播间短号，英文,号间隔
					</div>
				</td>
			</tr>
		</table>
		</form>
		<button class="btn btn-large btn-danger">确认</button>
		<button class="btn btn-large btn-success">取消</button>
	</div>
</div>


<script type="text/javascript">
$(function(){
	$('#global_video_edit select[name="indexForm[content][position]"]').bind('change',function(){
		var position = $(this).val();
		if(position==1){
			$('#adv_video_channels').show();
			$('#adv_video_target').hide();
		}else if(position==2){
			$('#adv_video_channels').hide();
			$('#adv_video_target').show();
		}else{
			$('#adv_video_channels').hide();
			$('#adv_video_target').hide();
		}
	});
	
	$('.box-icon .icon-plus').parent('a').click(function(e){
		document.getElementById("myForm").reset();
		$('#global_video_edit input[name="indexForm[operate_id]"]').val(0);
		$('#adv_video_channels').hide();
		$('#adv_video_target').hide();
		$('#global_video_edit').modal('show');
	});
	
	$('#global_video_edit .btn-danger').click(function(){
		if($('#global_video_edit input[name="indexForm[subject]"]').val().length<=0){
			alert('贴片名称不能为空'); return ;
		}
		if($('#global_video_edit input[name="indexForm[content][time]"]').val().length<=0){
			alert('请填写时长'); return ;
		}
		
		$('#global_video_edit form').submit();
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
	// 启用 / 停用
	$('div.box-content i.operate_status').click(function(){
		var operateId = $(this).attr('operateId');
		var status = $(this).parents('td').find('em').attr('data_status');
		var obj = $(this);
		if(operateId){
			$.ajax({
				type:'post',
				url:'<?php echo $this->createUrl('adv/video');?>',
				dataType:'json',
				data:{'op':'editVideo','operateId':operateId, 'status':status},
				success:function(e){
					if(e.result){
						if(e.data.content.status==1){
							var txt = '启用';
						}else{
							var txt = '停用';
						}
						obj.parents('td').find('em').attr('data_status',e.data.content.status)
						obj.html('已'+txt);
					}
				}
			});
		}
	});
	
	// 编辑
	$('div.box-content i.icon-edit').parent('a').click(function(){
		var operateId = $(this).children('i.icon-edit').attr('operateId');
		var _tr = $(this).parents('tr');
		var _td = _tr.children('td');
		var _em = _tr.find('em');
		
		var status = $(_em).attr('data_status');
		var position = $(_em).attr('data_position');
		var target = $(_em).attr('data_target');
		var type = _em.attr('data_type');
		var channels = _em.attr('data_channels');
		
		$('#global_video_edit input[name="indexForm[operate_id]"]').val(operateId);
		$('#global_video_edit input[name="indexForm[subject]"]').val($(_td[1]).html());
		$('#global_video_edit input[name="indexForm[content][time]"]').val($(_td[3]).html());
		$('#global_video_edit input[name="indexForm[textlink]"]').val($(_td[4]).html());
		$('#global_video_edit select[name="indexForm[content][status]"]').val(status);
		$('#global_video_edit select[name="indexForm[content][type]"]').val(type);
		$('#global_video_edit select[name="indexForm[content][position]"]').val(position);
		$('#global_video_edit textarea[name="indexForm[content][target]"]').val(target);
		$('#global_video_edit select[name="indexForm[content][channels]"]').val(channels);
		
		if(position==1){
			$('#adv_video_channels').show();
			$('#adv_video_target').hide();
		}else if(position==2){
			$('#adv_video_channels').hide();
			$('#adv_video_target').show();
		}else{
			$('#adv_video_channels').hide();
			$('#adv_video_target').hide();
		}
		
		$('#global_video_edit').modal('show');
	});
	
	$('#global_video_edit .btn-success').click(function(){
		// $("#global_chatword_del").modal('hide');
		$("#global_video_edit").modal('hide');
	});
});
</script>