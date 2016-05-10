<?php
$this->breadcrumbs=array(
		'等级设置',
	);
?>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-book"></i>用户(富豪)等级设置</h2>
			<div class="box-icon">
				<a href="javascript:void(0);" class="btn btn-setting btn-round" title="添加用户等级"><i class="icon-plus"></i></a>
				<a href="javascript:void(0);" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table class="table table-bordered" id="userrank_list_table">
				<thead>
					<tr>
						<td>等级序号</td>
						<td>等级称谓</td>
						<td>所需贡献值</td>
						<td>管理数</td>
						<td>操作</td>
					</tr>
				</thead>
				<tbody>
					<?php foreach($user_list as $k=>$v): ?>
					<tr>
						<td><?php echo $v['rank'];?></td>
						<td><?php echo $v['name'];?></td>
						<td><?php echo $v['dedication'];?></td>
						<td><?php echo $v['house_m_num'];?></td>
						<td>
							<a class="btn" href="javascript:void(0);" title="编辑"><i class="icon-edit" data="<?php echo $v['rank_id'];?>"></i></a>
							<a class="btn" href="javascript:void(0);" title="删除"><i class="icon-trash" data="<?php echo $v['rank_id'];?>"></i></a>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-book"></i>主播(魅力)等级设置</h2>
			<div class="box-icon">
				<a href="javascript:void(0);" class="btn btn-setting btn-round" title="添加主播等级"><i class="icon-plus"></i></a>
				<a href="javascript:void(0);" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table class="table table-bordered" id="doteyrank_list_table">
				<thead>
					<tr>
						<td>等级序号</td>
						<td>等级称谓</td>
						<td>所需魅力值</td>
						<td>管理数</td>
						<td>分成比例</td>
						<td>兑换率</td>
						<td>操作</td>
					</tr>
				</thead>
				<tbody>
					<?php foreach($dotey_list as $k=>$v): ?>
					<tr>
						<td><?php echo $v['rank'];?></td>
						<td><?php echo $v['name'];?></td>
						<td><?php echo $v['charm'];?></td>
						<td><?php echo $v['house_m_num'];?></td>
						<td><?php echo $v['divieded_scale'];?>%</td>
						<td><?php echo $v['divieded_rate'];?></td>
						<td>
							<a class="btn" href="javascript:void(0);" title="编辑"><i class="icon-edit" data="<?php echo $v['rank_id'];?>"></i></a>
							<a class="btn" href="javascript:void(0);" title="删除"><i class="icon-trash" data="<?php echo $v['rank_id'];?>"></i></a>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!--修改用户等级浮层-->
<div class="modal hide fade span3" id="global_userrank_edit" style="left:40%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>修改用户等级</h3>
	</div>
	<div class="modal-body">
		<table>
			<tr><td>等级序号</td><td><input type="text" name="user_level" /></td></tr>
			<tr><td>等级称谓</td><td><input type="text" name="user_name" /></td></tr>
			<tr><td>所需贡献值</td><td><input type="text" name="user_dedication" /></td></tr>
			<tr><td>管理数</td><td><input type="text" name="user_house_m_num" /></td></tr>
		</table>
		<button class="btn btn-large btn-danger">确认</button>
		<button class="btn btn-large btn-success">取消</button>
	</div>
</div>

<!--修改主播等级浮层-->
<div class="modal hide fade span3" id="global_doteyrank_edit" style="left:40%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>修改用户等级</h3>
	</div>
	<div class="modal-body">
		<table>
			<tr><td>等级序号</td><td><input type="text" name="dotey_level" /></td></tr>
			<tr><td>等级称谓</td><td><input type="text" name="dotey_name" /></td></tr>
			<tr><td>所需魅力值</td><td><input type="text" name="dotey_dedication" /></td></tr>
			<tr><td>管理数</td><td><input type="text" name="dotey_house_m_num" /></td></tr>
			<tr><td>分成比例</td><td><input type="text" name="dotey_divieded_scale" /></td></tr>
			<tr><td>兑换率</td><td><input type="text" name="dotey_divieded_rate" /></td></tr>
		</table>
		<button class="btn btn-large btn-danger">确认</button>
		<button class="btn btn-large btn-success">取消</button>
	</div>
</div>

<!--删除确认层-->
<div class="modal hide fade span3" id="global_level_del" style="left:40%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>确认删除</h3>
	</div>
	<div class="modal-body">
		<button class="btn btn-large btn-danger">确认</button>
		<button class="btn btn-large btn-success">取消</button>
	</div>
</div>

<script type="text/javascript">
$(function(){
	// 用户等级修改
	$('#userrank_list_table .icon-edit').parent('a').click(function(){
		var id = $(this).find('i').attr('data');
		var td = $(this).parents('tr').children('td');
		var user_level = $(td[0]).html();
		var user_name = $(td[1]).html();
		var user_dedication = $(td[2]).html();
		var user_house_m_num = $(td[3]).html();
		$('input[name=user_level]').val(user_level);
		$('input[name=user_name]').val(user_name);
		$('input[name=user_dedication]').val(user_dedication);
		$('input[name=user_house_m_num]').val(user_house_m_num);
		
		$('#global_userrank_edit').modal('show');
		
		$('#global_userrank_edit').find('button.btn-danger').unbind('click');
		$('#global_userrank_edit').find('button.btn-danger').bind('click',function(){
			user_level = $('input[name=user_level]').val();
			user_name = $('input[name=user_name]').val();
			user_dedication = $('input[name=user_dedication]').val();
			user_house_m_num = $('input[name=user_house_m_num]').val();
			
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/editLevel');?>",
				dataType:'json',
				data:{'id':id, 'type':'user', 'user_level':user_level, 'user_name':user_name, 'user_dedication':user_dedication,'user_house_m_num':user_house_m_num},
				success:function(e){
					if(e.result){
						$(td[0]).html(user_level);
						$(td[1]).html(user_name);
						$(td[2]).html(user_dedication);
						$(td[3]).html(user_house_m_num);
					}
					$("#global_userrank_edit").modal('hide');
				}
			});
		});
	});
	// 删除用户等级
	$('#userrank_list_table .icon-trash').parent('a').click(function(){
		var _tr = $(this).parents('tr');
		var id = $(this).find('i').attr('data');
		$('#global_level_del .btn-danger').unbind();
		$('#global_level_del .btn-danger').bind('click',function(){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/editLevel');?>",
				dataType:'json',
				data:{'id':id, 'type':'user', 'ac':'del'},
				success:function(e){
					if(e.result){
						_tr.hide();
					}
					$("#global_level_del").modal('hide');
				}
			});
		});
		$('#global_level_del').modal('show');
	});
	// 主播等级修改
	$('#doteyrank_list_table .icon-edit').parent('a').click(function(){
		var id = $(this).find('i').attr('data');
		var td = $(this).parents('tr').children('td');
		var dotey_level = $(td[0]).html();
		var dotey_name = $(td[1]).html();
		var dotey_dedication = $(td[2]).html();
		var dotey_house_m_num = $(td[3]).html();
		var dotey_divieded_scale = $(td[4]).html();
		var dotey_divieded_rate = $(td[5]).html();
		$('input[name=dotey_level]').val(dotey_level);
		$('input[name=dotey_name]').val(dotey_name);
		$('input[name=dotey_dedication]').val(dotey_dedication);
		$('input[name=dotey_house_m_num]').val(dotey_house_m_num);
		$('input[name=dotey_divieded_scale]').val(dotey_divieded_scale);
		$('input[name=dotey_divieded_rate]').val(dotey_divieded_rate);
		
		$('#global_doteyrank_edit').modal('show');
		
		$('#global_doteyrank_edit').find('button.btn-danger').unbind('click');
		$('#global_doteyrank_edit').find('button.btn-danger').bind('click',function(){
			
			dotey_level = $('input[name=dotey_level]').val();
			dotey_name = $('input[name=dotey_name]').val();
			dotey_dedication = $('input[name=dotey_dedication]').val();
			dotey_house_m_num = $('input[name=dotey_house_m_num]').val();
			dotey_divieded_scale = $('input[name=dotey_divieded_scale]').val();
			dotey_divieded_rate = $('input[name=dotey_divieded_rate]').val();
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/editLevel');?>",
				dataType:'json',
				data:{'id':id, 'type':'dotey', 'dotey_level':dotey_level, 'dotey_name':dotey_name, 'dotey_dedication':dotey_dedication,'dotey_house_m_num':dotey_house_m_num, 'dotey_divieded_scale':dotey_divieded_scale, 'dotey_divieded_rate':dotey_divieded_rate},
				success:function(e){
					if(e.result){
						$(td[0]).html(dotey_level);
						$(td[1]).html(dotey_name);
						$(td[2]).html(dotey_dedication);
						$(td[3]).html(dotey_house_m_num);
						$(td[4]).html(dotey_divieded_scale);
						$(td[5]).html(dotey_divieded_rate);
					}
					$("#global_doteyrank_edit").modal('hide');
				}
			});
		});
	});
	
	// 删除主播等级
	$('#doteyrank_list_table .icon-trash').parent('a').click(function(){
		var _tr = $(this).parents('tr');
		var id = $(this).find('i').attr('data');
		$('#global_level_del .btn-danger').unbind();
		$('#global_level_del .btn-danger').bind('click',function(){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/editLevel');?>",
				dataType:'json',
				data:{'id':id, 'type':'dotey', 'ac':'del'},
				success:function(e){
					if(e.result){
						_tr.hide();
					}
					$("#global_level_del").modal('hide');
				}
			});
		});
		$('#global_level_del').modal('show');
	});
	// 添加用户等级
	$("a.btn-setting[title='添加用户等级']").click(function(){
		$("#global_userrank_edit").modal('show');
		$('#global_userrank_edit').find('button.btn-danger').unbind('click');
		$('#global_userrank_edit').find('button.btn-danger').bind('click',function(){
			var user_level = $('input[name=user_level]').val();
			var user_name = $('input[name=user_name]').val();
			var user_dedication = $('input[name=user_dedication]').val();
			var user_house_m_num = $('input[name=user_house_m_num]').val();
			
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/editLevel');?>",
				dataType:'json',
				data:{'type':'user', 'user_level':user_level, 'user_name':user_name, 'user_dedication':user_dedication,'user_house_m_num':user_house_m_num},
				success:function(e){
					$("#global_userrank_edit").modal('hide');
				}
			});
		});
	});
	$("a.btn-setting[title='添加主播等级']").click(function(){
		$("#global_doteyrank_edit").modal('show');
		$('#global_doteyrank_edit').find('button.btn-danger').unbind('click');
		$('#global_doteyrank_edit').find('button.btn-danger').bind('click',function(){
			var dotey_level = $('input[name=dotey_level]').val();
			var dotey_name = $('input[name=dotey_name]').val();
			var dotey_dedication = $('input[name=dotey_dedication]').val();
			var dotey_house_m_num = $('input[name=dotey_house_m_num]').val();
			var dotey_divieded_scale = $('input[name=dotey_divieded_scale]').val();
			var dotey_divieded_rate = $('input[name=dotey_divieded_rate]').val();
			
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/editLevel');?>",
				dataType:'json',
				data:{'type':'dotey', 
					'dotey_level':dotey_level, 
					'dotey_name':dotey_name, 
					'dotey_dedication':dotey_dedication,
					'dotey_house_m_num':dotey_house_m_num,
					'dotey_divieded_scale':dotey_divieded_scale,
					'dotey_divieded_rate':dotey_divieded_rate,
				},
				success:function(e){
					$("#global_doteyrank_edit").modal('hide');
				}
			});
		});
	});
	
	$('.btn-success').click(function(){
		$("#global_userrank_edit").modal('hide');
		$("#global_doteyrank_edit").modal('hide');
		$('#global_level_del').modal('hide');
	});
});
</script>