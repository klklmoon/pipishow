<div class="control-group">
	<label class="control-label" for="focusedInput">倍数</label>
	<div class="controls">
		<?php echo CHtml::textField('setup[award]','',array('class'=>'input-small focused'));?>
	  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_num"></span>
	</div>
  </div>
<script>
	function checkAwardInfo(){
		if(!$('#setup_award').attr('value') || isNaN($('#setup_award').attr('value'))){
			$('#info_form_num').html('请填写皮蛋数量').show();
			return false;
		}else{
			$('#info_form_num').html('').hide();
		}
		return true;
	};
</script>