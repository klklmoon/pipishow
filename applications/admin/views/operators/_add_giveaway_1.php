<div class="control-group">
	<label class="control-label" for="focusedInput">礼物</label>
	<div class="controls">
		<?php echo CHtml::listBox('_form[gift_id]','',$this->getGiftListOption(),array('class'=>'input-small','size'=>1,'empty'=>' '));?>
	  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_gift_id"></span>
	</div>
  </div>
<div class="control-group">
	<label class="control-label" for="focusedInput">数量</label>
	<div class="controls">
		<?php echo CHtml::textField('_form[num]','',array('class'=>'input-small focused'));?>
	  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_num"></span>
	</div>
  </div>
  <div class="control-group">
	<label class="control-label" for="focusedInput">描述</label>
	<div class="controls">
		<?php echo CHtml::textArea('_form[info]','');?>
		<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_info"></span>
	</div>
  </div>
<script>
	function checkGiveawayInfo(){
		if(!$('#_form_gift_id').attr('value') || isNaN($('#_form_gift_id').attr('value'))){
			$('#info_form_gift_id').html('请选择赠送的礼物').show();
			return false;
		}else{
			$('#info_form_gift_id').html('').hide();
		}
		
		if(!$('#_form_num').attr('value') || isNaN($('#_form_num').attr('value'))){
			$('#info_form_num').html('请填写赠送数量且值为数字').show();
			return false;
		}else{
			$('#info_form_num').html('').hide();
		}

		if(!$('#_form_info').attr('value')){
			$('#info_form_info').html('请填写赠送原因').show();
			return false;
		}else{
			$('#info_form_info').html('').hide();
		}
	};
</script>