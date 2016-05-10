<?php
$this->breadcrumbs=array(
		'昵称敏词管理',
	);
?>

<div class="row-fluid sortable ui-sortable">
	<div class="box span6">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-book"></i>昵称敏词管理</h2>
			<div class="box-icon">
				<a href="javascript:void(0);" class="btn btn-setting btn-round" title="添加敏感词"><i class="icon-plus"></i></a>
				<a href="javascript:void(0);" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Id</th>
						<th>敏感词</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($list) :?>
					<?php foreach($list as $k=>$v):?>
					<tr>
						<td><?php echo $k;?></td>
						<td><?php echo $v['name'];?></td>
						<td>
							<a class="btn" href="javascript:void(0);" title="编辑"><i class="icon-edit" data="<?php echo $k;?>"></i></a>
							<a class="btn" href="javascript:void(0);" title="删除"><i class="icon-trash" data="<?php echo $k;?>"></i></a>
						</td>
					</tr>
					<?php endforeach;?>
					<?php endif;?>
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

<!--修改敏感词浮层-->
<div class="modal hide fade span3" id="global_chatword_edit" style="left:40%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>修改敏感词</h3>
	</div>
	<div class="modal-body" id="global_chatword_edit_body">
		<table>
			<tr><td>敏感词</td><td><input type="text" name="word" /></td></tr>
		</table>
		<button class="btn btn-large btn-danger">确认</button>
		<button class="btn btn-large btn-success">取消</button>
	</div>
</div>

<!--删除敏感词浮层-->
<div class="modal hide fade span3" id="global_chatword_del" style="left:40%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>确认删除</h3>
	</div>
	<div class="modal-body" id="global_chatword_del_body">
		<button class="btn btn-large btn-danger">确认</button>
		<button class="btn btn-large btn-success">取消</button>
	</div>
</div>


<script type="text/javascript">
$(function(){
	// 修改敏感词
	$('.icon-edit').parent('a').click(function(){
		var id = $(this).find('i').attr('data');
		var td = $(this).parents('tr').children('td');
		var name = $(td[1]).html();
		$('#global_chatword_edit_body').find('input[name=id]').val(id);
		$('#global_chatword_edit_body').find('input[name=word]').val(name);
		$('#global_chatword_edit').modal('show');
		
		// 解除之前绑定的click
		$('#global_chatword_edit_body').find('button.btn-danger').unbind('click');
		$('#global_chatword_edit_body').find('button.btn-danger').bind('click',function(){
			
			name = $('#global_chatword_edit_body').find('input[name=word]').val();
			
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/editChatWord');?>",
				dataType:'json',
				data:{'id':id, 'name':name, 'word_type':1},
				success:function(e){
					if(e.result){
						$(td[1]).html(e.data.name);
					}
					$("#global_chatword_edit").modal('hide');
				}
			});
		});
		$('#global_chatword_edit').find('h3').html('修改敏感词');
	});
	// 添加敏感词
	$('.box-icon .icon-plus').parent('a').click(function(e){
		$("#global_chatword_edit").modal('show');
		$('#global_chatword_edit').find('h3').html('添加敏感词');
		// 解除之前绑定的click
		$('#global_chatword_edit_body').find('button.btn-danger').unbind('click');
		$('#global_chatword_edit_body').find('button.btn-danger').bind('click',function(){
			name = $('#global_chatword_edit_body').find('input[name=word]').val();
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/editChatWord');?>",
				dataType:'json',
				data:{'isAdd':'1', 'name':name, 'word_type':1},
				success:function(e){
					$("#global_chatword_edit").modal('hide');
					if(e.result){
						window.location.reload();
					}
				}
			});
		});
	});
	
	// 删除敏感词
	$('.icon-trash').parent('a').click(function(e){
		var _tr = $(this).parents('tr');
		var id = $(this).find('i').attr('data');
		$("#global_chatword_del").modal('show');
		
		$('.btn-danger').unbind('click');
		$('.btn-danger').bind('click',function(){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('global/editChatWord');?>",
				dataType:'json',
				data:{'isDel':'1', 'id':id, 'word_type':1},
				success:function(e){
					if(e.result){
						_tr.hide();
					}
					$("#global_chatword_del").modal('hide');
				}
			});
		});
	});
	$('.btn-success').click(function(){
		$("#global_chatword_del").modal('hide');
		$("#global_chatword_edit").modal('hide');
	});
});
</script>