<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:140px;margin-left:10px;padding:3px 5px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('user/usersearch',array('op'=>'bindDo'));?>">
				<fieldset>	  
				  <div class="control-group">
					<label class="control-label">手机验证操作：</label>
					<div class="controls">
					  <?php echo CHtml::listBox('bind_type', '', array('bind_mobile'=>'添加手机验证','unbind_mobile'=>'解除手机验证','unbind_all'=>'解绑手机和邮件'),array('size'=>1,'class'=>'input-small','style'=>'width:120px;'));?>
					</div>
					<div id="input_mobile" style="margin-top:5px;">
						<label class="control-label">手机号：</label>
						<div class="controls">
						<?php echo CHtml::textField('reg_mobile','',array('class'=>'input-small'));?>
						</div>
					</div>
				  </div>
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary" id='family_submit'>提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('#bind_type').change(function(){
		if(this.selectedIndex != 0){
			$('#input_mobile').hide();
			$('#reg_mobile').val('');
		}else{
			$('#input_mobile').show();
		}
	});

	$('#family_submit').click(function(){
		var uid='<?php echo $uid;?>';
		var bind_type = $('#bind_type').val();
		var reg_mobile = $('#reg_mobile').val();
		$.ajax({
			type:'post',
			url:'<?php echo $this->createUrl('user/usersearch');?>',
			dataType:'html',
			data:{'op':'bindDo','uid':uid,'bind_type':bind_type,'reg_mobile':reg_mobile},
			success:function(msg){
				if(msg != 1){
					alert(msg);
				}else{
					window.location.href="<?php echo $this->createUrl('user/usersearch',array('uid'=>$uid));?>";
				}
			}
		});
	});
	
	//提交 前动作 确认所有 file都需要上传文件
	$(":submit").click(function(){
		return false;
	});
	
})
</script>