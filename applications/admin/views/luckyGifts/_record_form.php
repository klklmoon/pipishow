<form class="form-horizontal" action="" method="post" id="_set_record">
	<fieldset>
	  <div class="control-group">
	  	<label class="control-label" for="focusedInput">奖池金额</label>
	  	<div class="controls">
	  		<?php
				echo CHtml::textField('rsetup[value]',0, array('class'=>'input-small'));
				echo CHtml::hiddenField('rsetup[id]',0);
			?>
			<span class="label label-important" style="margin-left:10px;display:none;" id="info_rsetup_value"></span>
	  	</div>
	  </div>
	  <div class="control-group">
	  	<label class="control-label" for="focusedInput">奖池A值</label>
	  	<div class="controls">
	  		<?php
				echo CHtml::textField('rsetup[chance]',0, array('class'=>'input-small'));
			?>
			<span class="label label-important" style="margin-left:10px;display:none;" id="info_rsetup_chance"></span>
	  	</div>
	  </div>
	  <div class="form-actions">
		<button type="submit" class="btn btn-primary">更新</button>
		<button type="button" class="btn btn-primary" id="_record_reset">重置</button>
	  </div>
	</fieldset>
</form>