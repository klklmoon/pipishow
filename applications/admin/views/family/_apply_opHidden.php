<?php 
$hidden = FamilyService::getFamilyHidden();
?>
<div>
	<div class="box-content form-horizontal">
		<div class="control-group">
		  <?php echo CHtml::label('隐藏状态','ops_hidden',array('class'=>'control-label'));?>
		  <div class="controls">
		  	<?php echo CHtml::listBox('ops_hidden',$info['hidden'],$hidden,array('class'=>'input-small','size'=>1));?>
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
		  		$type = $info['hidden']?2:3; 
		  		echo CHtml::hiddenField('ops_type',$type);
		  	?>
		  	<?php echo CHtml::button('button',array('class'=>'btn','value'=>'确认','id'=>'confirm_button_submit'));?>
		  </div>
		</div>
	</div>
</div>

<script type="text/javascript">
$('#ops_hidden').change(function(){
	var type = $(this).attr('value');
	type = (type==1)?2:3;
	$('#ops_type').attr('value',type);
});
</script>