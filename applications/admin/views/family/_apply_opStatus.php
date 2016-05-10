<?php 
$status = array(FAMILY_STATUS_SUCCESS=>'审核通过',FAMILY_STATUS_REFUSE=>'拒绝审核');
?>
<div>
	<div class="box-content form-horizontal">
		<div class="control-group">
		  <?php echo CHtml::label('申请状态','ops_status',array('class'=>'control-label'));?>
		  <div class="controls">
		  	<?php echo CHtml::listBox('ops_status',$info['status'],$status,array('class'=>'input-small','size'=>1,'empty'=>'-选择审核状态-'));?>
		  </div>
		</div>
		
		<div class="control-group">
		  <?php echo CHtml::label('操作说明','ops_reason',array('class'=>'control-label'));?>
		  <div class="controls">
		  	<?php echo CHtml::textArea('ops_reason','');?>
		  	<span class="label label-important"  id="ops_reason_tip"style="margin-left:10px;display:none;">操作说明不能为空</span>
		  </div>
		</div>
		
		<div class="control-group">
		  <div class="controls" id="uidflag">
		  	<?php echo CHtml::hiddenField('ops_family_id',$info['id']);?>
		  	<?php
		  		if($info['status'] == -1){
		  			$type = 1;
		  		}elseif ($info['status'] == 1){
		  			$type = 0;
		  		}else{
		  			$type = '';
		  		}
		  		echo CHtml::hiddenField('ops_type',$type);
		  	?>
		  	<?php echo CHtml::button('button',array('class'=>'btn','value'=>'确认','id'=>'confirm_button_submit'));?>
		  </div>
		</div>
	</div>
</div>

<script type="text/javascript">
$('#ops_status').change(function(){
	var type = $(this).attr('value');
	if(type == -1){
		type = 1;
	}else if (type == 1){
		type = 0;
	}else{
		type = '';
	}
	
	$('#ops_type').attr('value',type);
});
</script>