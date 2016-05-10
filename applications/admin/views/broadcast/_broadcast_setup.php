<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" action="" method="post">
				<fieldset>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">是否开启</label>
					<div class="controls">
						<span id="user_user_type">
						<?php echo CHtml::radioButtonList('setup[power]' ,isset($setInfo['power'])?$setInfo['power']:0, array(1=>'开启',0=>'关闭'),
								array(
									'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
									'labelOptions'=>array('class'=>'checkbox inline'),
								)
							);
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
				  	<label class="control-label" for="focusedInput">等级限制</label>
				  	<div class="controls">
				  		<?php 
							echo CHtml::listBox('setup[urank]',isset($setInfo['urank'])?$setInfo['urank']:0, $rank,array('size'=>1,'empty'=>'-请选择-','class'=>'input-small'));
						?>
				  	</div>
				  </div>
				  
				  <div class="control-group">
				  	<label class="control-label" for="focusedInput">广播价格</label>
				  	<div class="controls">
				  		<?php 
							echo CHtml::textField('setup[price]',isset($setInfo['price'])?$setInfo['price']:10, array('class'=>'input-small'));
						?>
				  	</div>
				  </div>
				   
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">更新</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
</div>

<script style="text/javascript">
$(function() {
	$(':submit').click(function(){
		var power=$('input[name="setup[power]"]:checked').val();
		var urank=$('select option:selected').val();
		var price=$('input[name="setup[price]"]').val();
		var url = "<?php echo $this->createUrl('Broadcast/siteBroadcast');?>";

		$.ajax({
			url:url,
			type:'post',
			dataType:'html',
			data:{'power':power,'urank':urank,'price':price,'op':'addSetup','tab':'setup'},
			success:function(msg){
				alert(msg);
			}
		});
		return false;
	});
});
</script>