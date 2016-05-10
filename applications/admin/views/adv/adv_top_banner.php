<?php
$this->breadcrumbs = array('广告管理','顶部通栏');
?>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>顶部通栏</h2>
			<div class="box-icon">
				<a href="javascript:void(0);" class="btn btn-setting btn-round" title="添加顶部通栏"><i class="icon-plus"></i></a>
				<a href="javascript:void(0);" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th width="230px">广告图片</th>
						<th>alt文字</th>
						<th>投放范围</th>
						<th>连接地址</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if($list):?>
					<?php foreach($list as $k=>$v):?>
					<tr>
						<td>
							<?php if($v['piclink']):?>
							<img width="200px;" src="<?php echo Yii::app()->params['images_server']['url'].'/operate/'.$v['piclink'];?>" />
							<?php else:?>
								未上传
							<?php endif;?>
						</td>
						<td><?php echo $v['subject'];?></td>
						<td><?php echo $config['position'][$v['content']['position']];?></td>
						<td><?php echo $v['textlink'];?></td>
						<td>
							<a class="btn" title="编辑"><i class="icon-edit" operateId="<?php echo $v['operate_id'];?>"></i></a>
							<a class="btn" title="删除"><i class="icon-trash" operateId="<?php echo $v['operate_id'];?>"></i></a>
							<em class="hide" 
								data_position="<?php echo $v['content']['position'];?>" 
								data_target="<?php echo $v['content']['target'];?>"
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
		<h3>添加顶部通栏</h3>
	</div>
	<div class="modal-body" id="global_chatword_edit_body">
		<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('adv/topBanner',array('op'=>'addTopBanner'));?>" enctype="multipart/form-data" id="myForm">
		<input type="hidden" name="indexForm[operate_id]" value="0" />
		<table>
			<tr>
				<td>alt文字</td>
				<td><input type="text" name="indexForm[subject]" /></td>
			</tr>
			<tr>
				<td>广告图片</td>
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
		$('#adv_video_channels').hide();
		$('#adv_video_target').hide();
		$('#global_video_edit input[name="indexForm[operate_id]"]').val(0);
		$('#global_video_edit').modal('show');
	});
	
	// 添加新的顶部通栏
	$('#global_video_edit .btn-danger').click(function(){
		$('#global_video_edit form').submit();
	});
	
	// 顶部通栏编辑
	$('div.box-content i.icon-edit').parent('a').click(function(){
		var operateId = $(this).children('i.icon-edit').attr('operateId');
		var _tr = $(this).parents('tr');
		var _td = _tr.children('td');
		var _em = _tr.find('em');
		
		var position = $(_em).attr('data_position');
		var target = $(_em).attr('data_target');
		var channels = _em.attr('data_channels');
		
		$('#global_video_edit input[name="indexForm[operate_id]"]').val(operateId);
		$('#global_video_edit input[name="indexForm[subject]"]').val($(_td[1]).html());
		$('#global_video_edit input[name="indexForm[textlink]"]').val($(_td[3]).html());
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