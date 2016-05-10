<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" action="" method="post" id="_set_pool">
				<fieldset>
				  <div class="control-group">
				  	<label class="control-label" for="focusedInput">奖池储金</label>
				  	<div class="controls">
				  		<?php 
							echo CHtml::textField('setup[value]',0, array('class'=>'input-small'));
						?>
				  	</div>
				  </div>
				  <div class="control-group">
				  	<label class="control-label" for="focusedInput">A值</label>
				  	<div class="controls">
				  		<?php 
							echo CHtml::textField('setup[chance]',0, array('class'=>'input-small'));
							echo CHtml::hiddenField('setup[id]',0);
						?>
				  	</div>
				  </div>
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">更新</button>
					<button type="button" class="btn btn-primary" id="_poolset_reset">重置</button>
				  </div>
				</fieldset>
			</form>
			
			<table class="table table-bordered" id="luckygifts_poolset_list">
			  <thead>
				  <tr>
					  <th>ID</th>
					  <th>奖池储金</th>
					  <th>A值</th>
					  <th>操作</th>
				  </tr>
			  </thead>   
			  <tbody>
			  	<?php if(!empty($list)){?>
			  	<?php foreach($list as $info){?>
			  	<tr id="__<?php echo $info['id'];?>">
			  		<td><?php echo $info['id'];?></td>
			  		<td><?php echo $info['value'];?></td>
			  		<td><?php echo $info['chance'];?></td>
			  		<td>
			  			<span class="icon icon-color icon-edit" poolId="<?php echo $info['id'];?>"></span>
			  			<span class="icon icon-color icon-close" poolId="<?php echo $info['id'];?>"></span>
			  		</td>
			  	</tr>
			  	<?php }?>
			  	<?php }else{?>
			  		<tbody>
				  		<tr><td colspan="4">没有配置的数据</td></tr>
			  		</tbody>
			  	<?php }?>
			  </tbody>
		  </table>
		</div>
	</div>
</div>

<script style="text/javascript">
$(function() {
	var url = "<?php echo $this->createUrl('luckyGifts/index');?>";
	
	$(':submit').click(function(){
		var value=$('input[name="setup[value]"]').val();
		var chance=$('input[name="setup[chance]"]').val();
		var id=$('input[name="setup[id]"]').val();

		$.ajax({
			url:url,
			type:'post',
			dataType:'html',
			data:{'value':value,'chance':chance,'id':id,'op':'addSetup','tab':'poolSet'},
			success:function(msg){
				if(msg == 'fail'){
					alert('更新失败');
				}else if(msg == 'success'){
					$('#__'+id+' > td').eq(1).html(value);
					$('#__'+id+' > td').eq(2).html(chance);
				}else{
					var _html = '<tr id="__'+msg+'">'+
				  		'<td>'+msg+'</td>'+
				  		'<td>'+value+'</td>'+
				  		'<td>'+chance+'</td>'+
				  		'<td>'+
				  			'<span class="icon icon-color icon-edit" poolId="'+msg+'"></span>'+
				  			'<span class="icon icon-color icon-close" poolId="'+msg+'"></span>'+
				  		'</td>'+
				  	'</tr>';
				  	$('#luckygifts_poolset_list > tbody > tr').last().after(_html);
				}
			}
		});
		return false;
	});
	//删除
	$('.icon-close').live('click',function(){
		var id = $(this).attr('poolId');
		var obj = this;
		if(id){
			$.ajax({
				url:url,
				type:'post',
				dataType:'html',
				data:{'id':id,'op':'delSetup','tab':'poolSet'},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						alert(msg);
					}		
				}
			});
		}
	});
	//编辑
	$('.icon-edit').live('click',function(){
		var id = $(this).attr('poolId');
		if(id){
			var _v = $('#__'+id+' > td').eq(1).html();
			var _c = $('#__'+id+' > td').eq(2).html();
			$('#setup_value').attr('value',_v);
			$('#setup_chance').attr('value',_c);
			$('#setup_id').attr('value',id);
		}
	});
	$('#_poolset_reset').live('click',function(){
		$(':input').each(function(i){
			$(this).attr('value','');
		});
	});
});
</script>