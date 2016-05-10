<?php
$this->breadcrumbs=array(
		'表情管理',
	);
?>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-book"></i>表情列表</h2>
			<div class="box-icon">
				<a href="javascript:void(0);" class="btn btn-setting btn-round" title="添加表情"><i class="icon-plus"></i></a>
				<a href="javascript:void(0);" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>id</th>
						<th>名称</th>
						<th>类型</th>
						<th>转义码</th>
						<th>图标</th>
						<th>排序</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$faceService=new FaceService();
						foreach($list as $k=>$v){
							$faceType=$faceService->getFaceType($v['type']);
					?>
					<tr>
						<td><?php echo $v['id'];?></td>
						<td><?php echo $v['name'];?></td>
						<td><?php echo $faceType[$v['type']];?></td>
						<td><?php echo $v['code'];?></td>
						<td><img src="<?php echo '/statics/fontimg/express/'.$v['type'].'/'.$v['image'];?>" /></td>
						<td><?php echo $v['displayorder'];?></td>
						<td>
							<a class="btn" href="javascript:void(0);" title="修改"><i class="icon-edit" data="<?php echo $v['id'];?>"></i></a>
							<a class="btn" href="javascript:void(0);" title="删除"><i class="icon-trash" data="<?php echo $v['id'];?>"></i></a>
						</td>
					<tr>
					<?php } ?>
				</tbody>
			</table>
			<div class="pagination pagination-centered">
			<?php    
			$this->widget('CLinkPager',array(
				'header'=>'',  
				'firstPageCssClass' => '',  
				'firstPageLabel' => '首页',    
				'lastPageLabel' => '末页',  
				'lastPageCssClass' => '',  
				'previousPageCssClass' =>'prev disabled',  
				'prevPageLabel' => '上一页',    
				'nextPageLabel' => '下一页', 
				'nextPageCssClass' => 'next', 
				'selectedPageCssClass' => 'active',
				'internalPageCssClass' => '',
				'htmlOptions' => array('class'=>''),
				'pages' => $pager,    
				'maxButtonCount'=>8    
				)
			);
			?>
			</div>
		</div>
	</div>
</div>


<div class="modal hide fade span3" id="global_face_edit" style="left:40%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>修改表情</h3>
	</div>
	<div class="modal-body" id="global_face_edit_body">
		<table>
			<tr>
				<td>名称</td>
				<td><input type="text" name="name" /></td>
			</tr>
			<tr>
				<td>类型</td>
				<td><select name="faceType">
					<option value="common" selected>普通</option>
					<option value="vip">VIP</option>
					<option value="aristocrat">贵族</option>
					</select></td>
			</tr>
			<tr>
				<td>图标</td>
				<td><input type="text" name="image" /></td>
			</tr>
			<tr><td></td><td id="faceImg"></td></tr>
			<tr>
				<td>排序</td>
				<td><input type="text" name="displayorder" value="0"/></td>
			</tr>
		</table>
		<button class="btn btn-large btn-danger">确认</button>
		<button class="btn btn-large btn-success">取消</button>
	</div>
</div>

<div class="modal hide fade span3" id="global_face_del" style="left:40%;">
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
	$('.icon-edit').parent('a').click(function(){
		var id = $(this).find('i').attr('data');
		var td = $(this).parents('tr').children('td');
		var name = $(td[1]).html();
		var type = $(td[2]).html();
		var image=$(td[4]).find('img').attr('src');
		var displayorder=$(td[5]).html();
		$('input[name=name]').val(name);
		$('select[name=faceType]').attr('value',type);
		var count=$('select[name=faceType] option').length;
		for(i=0;i<count;i++){
			if($('select[name=faceType] option:eq('+i+')').text()==type){
				$('select[name=faceType] option:eq('+i+')').attr('selected',true);
			}
		}
		$('#faceImg').empty().html('<img src="'+image+'" />');
		$('input[name=displayorder]').val(displayorder);
		$('#global_face_edit').modal('show');
		
		$('#global_face_edit_body').find('button.btn-danger').unbind('click');
		$('#global_face_edit_body').find('button.btn-danger').bind('click',function(){
			name = $('input[name=name]').val();
			type = $('select[name=faceType]').val();
			image = $('input[name=image]').val();
			displayorder = $('input[name=displayorder]').val();
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/editFace');?>",
				dataType:'json',
				data:{'id':id, 'name':name,'type':type, 'image':image,'displayorder':displayorder},
				success:function(e){
					$(td[1]).html(e.data.name);
					$(td[2]).html(e.data.type);
					$(td[3]).html(e.data.code);
					if(e.data.image!=null&&e.data.image!=''){
						$(td[4]).find('img').attr('src',e.data.image);
					}
					$(td[5]).html(e.data.displayorder);
					$("#global_face_edit").modal('hide');
				}
			});
			
		});
	});
	
	$('.btn-success').click(function(){
		$("#global_face_edit").modal('hide');
	});

	//新增表情
	$('.icon-plus').click(function(){
		$('input[name=name]').val('');
		$('input[name=image]').val('');
		$('#faceImg').empty()
		var td = $(this).parents('tr').children('td');
		$('#global_face_edit').children('.modal-header').children('h3').html('添加表情');
		$('#global_face_edit').modal('show');
		$('#global_face_edit_body').find('button.btn-danger').unbind('click');
		$('#global_face_edit_body').find('button.btn-danger').bind('click',function(){
			name = $('input[name=name]').val();
			faceType=$('select[name=faceType]').val();
			image=$('input[name=image]').val();
			displayorder=$('input[name=displayorder]').val();
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/addFace');?>",
				dataType:'json',
				data:{name:name,type:faceType,image:image,displayorder:displayorder},
				success:function(e){
					$("#global_face_edit").modal('hide');
				}
			});
		});
	});

	// 删除表情
	$('.icon-trash').parent('a').click(function(){
		var id = $(this).find('i').attr('data');
		var _tr = $(this).parents('tr');
		$('#global_face_del .btn-danger').unbind();
		$('#global_face_del .btn-danger').bind('click',function(){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/delFace');?>",
				dataType:'json',
				data:{'id':id},
				success:function(e){
					if(e.result){
						_tr.hide();
					}
					$("#global_face_del").modal('hide');
				}
			});
		});
		$('#global_face_del').modal('show');
	});
});
</script>
