<div class="control-group">
	<label class="control-label" for="focusedInput">倍数</label>
	<div class="controls">
		<?php echo CHtml::textField('setup[award]',0,array('class'=>'input-small focused','readonly'=>'readonly'));?>
	  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_num"></span>
	</div>
  </div>
<script>
	function checkAwardInfo(){
		if(!$('#setup_award').attr('value') || isNaN($('#setup_award').attr('value'))){
			$('#info_form_num').html('请填写相关数量且值为数字').show();
			return false;
		}else{
			$('#info_form_num').html('').hide();
		}
		return true;
	};
</script>