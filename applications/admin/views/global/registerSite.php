<?php
$this->breadcrumbs=array(
		'注册设置',
	);
?>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-wrench"></i> 防灌注册设置</h2>
			<div class="box-icon">
				<a href="javascript:void(0);" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<form action="" method="post" id="web_site_config" style="margin-bottom:1px">
			<table class="table" style="margin-bottom:1px">
				<thead>
				<tr>
					<td>注册间隔时间:</td>
					<td><input name="minute" value="<?php echo isset($reg_config['minute']) ? $reg_config['minute'] : '10';?>" /> 分钟</td>
					<td>注册次数:</td>
					<td><input name="rate" value="<?php echo isset($reg_config['rate']) ? $reg_config['rate'] : '3';?>" /> 次</td>
					<td>
						<a class="btn btn-large btn-success">确认</a>
					</td>
				</tr>
				</thead>
			</table>
			</form>
		</div>
	</div>
</div>


<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-book"></i> 黑名单IP设置</h2>
			<div class="box-icon">
				<a href="javascript:void(0);" class="btn btn-setting btn-round" title="添加IP"><i class="icon-plus"></i></a>
				<a href="javascript:void(0);" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table class="table table-striped table-bordered bootstrap-datatable" center="center">
				<thead>
					<tr>
						<th>IP</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if($bad_ip):?>
					<?php foreach($bad_ip as $k=>$v):?>
					<tr>
						<td><?php echo $v;?></td>
						<td>
							<a class="btn btn-larger"><i class="icon-edit" data="<?php echo $k;?>"></i></a>
							<a class="btn btn-larger"><i class="icon-trash" data="<?php echo $k;?>"></i></a>
						</td>
					</tr>
					<?php endforeach;?>
					<?php endif;?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!--添加IP-->
<div class="modal hide fade span3" id="global_blackip_edit" style="left:40%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>添加黑名单IP</h3>
	</div>
	<div class="modal-body">
		<p>IP : <input name="ip" /></p>
		<button class="btn btn-large btn-danger">确认</button>
		<button class="btn btn-large btn-success">取消</button>
	</div>
</div>

<!--弹窗错误_ip地址不正确-->
<div>
	
</div>

<script type="text/javascript">
$(function(){
	// 修改注册设置
	var global_register_minute = <?php echo isset($reg_config['minute']) ? $reg_config['minute'] : '10';?>;
	var global_register_rate = <?php echo isset($reg_config['rate']) ? $reg_config['rate'] : '3';?>;
	$('a.btn-success').click(function(){
		var minute = $('input[name=minute]').val();
		var rate = $('input[name=rate]').val();
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('global/register');?>",
			dataType:'json',
			data:{'minute':minute, 'rate':rate, 'type' : 1},
			success:function(e){
				if(e.result){
					$('input[name=minute]').val(minute);
					$('input[name=rate]').val(rate);
				}
			}
		});
	});
	
	// 添加ip
	$("a.btn-setting[title='添加IP']").click(function(){
		$('#global_blackip_edit').modal('show');
		$('#global_blackip_edit').find('h3').html('添加黑名单IP');
		$('#global_blackip_edit .btn-danger').unbind('click');
		$('#global_blackip_edit .btn-danger').bind('click',function(){
			var ip = $('#global_blackip_edit input[name=ip]').val();
			if(ip.length>=7){
				$.ajax({
					type:'post',
					url:"<?php echo $this->createUrl('global/register');?>",
					dataType:'json',
					data:{'ip':ip, 'type' : 2},
					success:function(e){
						$('#global_blackip_edit').modal('hide');
						if(e.result){
							window.location.reload();
						}
					}
				});
			}
		});
	});
	
	// 修改黑名单
	$('div.box-content i.icon-edit').parent('a').click(function(){
		var key = $(this).find('i.icon-edit').attr('data');
		var td = $(this).parents('tr').children('td');
		var _ip = $(td[0]).html();
		$('#global_blackip_edit input[name=ip]').val(_ip);
		$('#global_blackip_edit').find('h3').html('修改黑名单IP');
		
		$('#global_blackip_edit').modal('show');
		$('#global_blackip_edit .btn-danger').unbind('click');
		$('#global_blackip_edit .btn-danger').bind('click',function(){
			var ip = $('#global_blackip_edit input[name=ip]').val();
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/register');?>",
				dataType:'json',
				data:{'ip':ip, 'key':key, 'type' : 2},
				success:function(e){
					$('#global_blackip_edit').modal('hide');
					if(e.result){
						window.location.reload();
					}
				}
			});
		});
	});
	
	// 删除IP
	$('div.box-content i.icon-trash').parent('a').click(function(){
		var key = $(this).find('i.icon-trash').attr('data');
		var _tr = $(this).parents('tr');
		// console.log($(this).parents('tr'));
		$.ajax({
			type:'post',
				url:"<?php echo $this->createUrl('global/register');?>",
				dataType:'json',
				data:{'key':key, 'type' : 2, 'del':1},
				success:function(e){
					if(e.result){
						$(_tr).hide();
					}
					$('#global_blackip_edit').modal('hide');
				}
		});
	});
	
	$('#global_blackip_edit .btn-success').click(function(){
		$("#global_blackip_edit").modal('hide');
	});
});
</script>