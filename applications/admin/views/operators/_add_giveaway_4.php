<div class="control-group">
	<label class="control-label" for="focusedInput">魅力点</label>
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
		if(!$('#_form_num').attr('value') || isNaN($('#_form_num').attr('value'))){
			$('#info_form_num').html('请填写魅力点数量且值为数字类型').show();
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