<?php
$this->breadcrumbs = array('家族管理','家族设置');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" action="" method="post">
				<fieldset>
					
				 <div class="control-group">
					<label class="control-label" for="focusedInput">是否开启家族功能</label>
					<div class="controls">
						<span id="user_user_type">
						<?php echo CHtml::radioButtonList('setup[global_enable]' ,isset($setInfo['global_enable'])?$setInfo['global_enable']:'', array(1=>'开启',0=>'关闭'),
								array(
									'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
									'labelOptions'=>array('class'=>'checkbox inline'),
								)
							);
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">是否开启家族申请</label>
					<div class="controls">
						<span id="user_user_type">
						<?php echo CHtml::radioButtonList('setup[apply_enable]' ,isset($setInfo['apply_enable'])?$setInfo['apply_enable']:'', array(1=>'开启',0=>'关闭'),
								array(
									'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
									'labelOptions'=>array('class'=>'checkbox inline'),
								)
							);
						?>
					</div>
				  </div>
				  
				  <div class="control-group" id="urank">
				  	<label class="control-label" for="focusedInput">创建家族用户等级限制</label>
				  	<div class="controls">
				  		<?php 
							echo CHtml::listBox('setup[urank]',isset($setInfo['urank'])?$setInfo['urank']:'', $rank,array('size'=>1,'empty'=>'-请选择-','class'=>'input-small'));
						?>
				  	</div>
				  </div>
				  
				  <div class="control-group" id="drank">
				  	<label class="control-label" for="focusedInput">创建家族主播等级限制</label>
				  	<div class="controls">
				  		<?php 
							echo CHtml::listBox('setup[drank]',isset($setInfo['drank'])?$setInfo['drank']:'', $drank,array('size'=>1,'empty'=>'-请选择-','class'=>'input-small'));
						?>
				  	</div>
				  </div>
				  
				  <div class="control-group">
				  	<label class="control-label" for="focusedInput">创建家族价格</label>
				  	<div class="controls">
				  		<?php 
							echo CHtml::textField('setup[create_price]',isset($setInfo['create_price'])?$setInfo['create_price']:'', array('class'=>'input-small'));
						?>
				  	</div>
				  </div>
				  
				  <div class="control-group">
				  	<label class="control-label" for="focusedInput">获取族徽价格</label>
				  	<div class="controls">
				  		<?php 
							echo CHtml::textField('setup[medal_price]',isset($setInfo['medal_price'])?$setInfo['medal_price']:'', array('class'=>'input-small'));
						?>
				  	</div>
				  </div>
				   
				  <div class="control-group">
				  	<label class="control-label" for="focusedInput">修改族徽价格</label>
				  	<div class="controls">
				  		<?php 
							echo CHtml::textField('setup[update_medal_price]',isset($setInfo['update_medal_price'])?$setInfo['update_medal_price']:'', array('class'=>'input-small'));
						?>
				  	</div>
				  </div>
				  
				  <div class="control-group">
				  	<label class="control-label" for="focusedInput">家族主播强退天数</label>
				  	<div class="controls">
				  		<?php 
							echo CHtml::textField('setup[focus_quit]',isset($setInfo['focus_quit'])?$setInfo['focus_quit']:'0', array('class'=>'input-small'));
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
		var global_enable=$('input[name="setup[global_enable]"]:checked').val();
		var apply_enable=$('input[name="setup[apply_enable]"]:checked').val();
		var urank=$('#urank select option:selected').val();
		var drank=$('#drank select option:selected').val();
		var create_price=$('input[name="setup[create_price]"]').val();
		var medal_price=$('input[name="setup[medal_price]"]').val();
		var update_medal_price=$('input[name="setup[update_medal_price]"]').val();
		var url = "<?php echo $this->createUrl('family/setting');?>";

		if(isNaN(create_price) || isNaN(update_medal_price) || isNaN(medal_price)){
			alert('参数有误,请确认');
			return false;
		}else{
			$.ajax({
				url:url,
				type:'post',
				dataType:'html',
				data:{'apply_enable':apply_enable,'global_enable':global_enable,'urank':urank,'drank':drank,'create_price':create_price,'medal_price':medal_price,'update_medal_price':update_medal_price,'op':'addSetup'},
				success:function(msg){
					alert(msg);
				}
			});
		}
		return false;
	});
});
</script>