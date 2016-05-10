<div>
	<div class="box-content form-horizontal">
		<div class="control-group">
		  <?php echo CHtml::label('操作说明','ops_reason',array('class'=>'control-label'));?>
		  <div class="controls">
		  	<?php echo CHtml::textArea('ops_reason','');?>
		  	<span class="label label-important"  id="ops_reason_tip"style="margin-left:10px;display:none;">操作说明不能为空</span>
		  </div>
		</div>
		
		<div class="control-group">
		  <div class="controls" id="uidflag">
		  	<?php echo CHtml::hiddenField('ops_id',$id);?>
		  	<?php echo CHtml::button('button',array('class'=>'btn','value'=>'确认','id'=>'confirm_button_submit'));?>
		  </div>
		</div>
	</div>
</div>

<script type="text/javascript">
//家族一般状态切换 包括显示状态，隐藏状态 ，审核状态
$('#confirm_button_submit').bind('click',function(e){
	if(!$('#ops_reason').attr('value')){
		$('#ops_reason_tip').show();
		return false;
	}else{
		$('#ops_reason_tip').hide();
	}
	
	var id = $('#ops_id').attr('value');
	var reason = $('#ops_reason').attr('value');
	
	$.ajax({
		type:'post',
		url:"<?php echo $this->createUrl('family/contracted',array('op'=>'toNormal'));?>",
		dataType:'html',
		data:{'id':id,'reason':reason},
		success:function(msg){
			e.preventDefault();
			$('#user_list_manage').modal('hide');
			if(msg != 1){
				alert(msg);
			}else{
				var url='<?php echo $this->createUrl('family/apply');?>';
				window.location.href=url;
			}
			return false;
		}
	});
	return false;
});
</script>