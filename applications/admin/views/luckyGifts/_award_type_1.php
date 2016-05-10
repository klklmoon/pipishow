<div class="control-group">
	<label class="control-label" for="focusedInput">礼物</label>
	<div class="controls">
		<?php echo CHtml::listBox('setup[target_id]','',$this->getGiftListOption(),array('class'=>'input-medium','size'=>1,'empty'=>' '));?>
	  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_gift_id"></span>
	</div>
  </div>
<div class="control-group">
	<label class="control-label" for="focusedInput">数量</label>
	<div class="controls">
		<?php echo CHtml::textField('setup[award]','',array('class'=>'input-small focused'));?>
	  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_num"></span>
	</div>
  </div>
<script>
	function checkAwardInfo(){
		if(!$('#setup_target_id').attr('value') || isNaN($('#setup_target_id').attr('value'))){
			$('#info_form_gift_id').html('请选择相关的礼物').show();
			return false;
		}else{
			$('#info_form_gift_id').html('').hide();
		}
		
		if(!$('#setup_award').attr('value') || isNaN($('#setup_award').attr('value'))){
			$('#info_form_num').html('请填写相关数量且值为数字').show();
			return false;
		}else{
			$('#info_form_num').html('').hide();
		}
		return true;
	};
</script>