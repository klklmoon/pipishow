<?php 
$status = array(1=>'等级1',2=>'等级2',3=>'等级3',4=>'等级4',5=>'等级5',6=>'等级6');
?>
<div>
	<div class="box-content form-horizontal">
		<div class="control-group">
		  <?php echo CHtml::label('家族等级','ops_level',array('class'=>'control-label'));?>
		  <div class="controls">
		  	<?php echo CHtml::listBox('ops_level',$info['level'],$status,array('class'=>'input-small','size'=>1,'empty'=>' '));?>
		  </div>
		</div>
		
		<div class="control-group">
		  <div class="controls" id="uidflag">
		  	<?php echo CHtml::hiddenField('ops_family_id',$info['id']);?>
		  	<?php echo CHtml::button('button',array('class'=>'btn','value'=>'确认','id'=>'confirm_button_submit2'));?>
		  </div>
		</div>
	</div>
</div>
<script>
$(function(){
	//家族一般状态切换 包括显示状态，隐藏状态 ，审核状态
	$('#confirm_button_submit2').live('click',function(e){
		var family_id = $('#ops_family_id').attr('value');
		var level = $('#ops_level').attr('value');

		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('family/apply',array('op'=>'familyUpgrade'));?>",
			dataType:'html',
			data:{'familyId':family_id,'level':level},
			success:function(msg){
				if(msg == 1){
					window.location.href='<?php echo $this->createUrl('family/apply',array('familyId'=>$info['id']));?>';
				}else{
					alert(msg);
				}
				e.preventDefault();
				$('#user_list_manage').modal('hide');
			}
		});
		return false;
	});
});
</script>