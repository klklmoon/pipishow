<form class="form-horizontal" action="" method="post" id="_set_pool">
	<fieldset>
	  <div class="control-group">
	  	<label class="control-label" for="focusedInput">幸运礼物</label>
	  	<div class="controls">
	  		<?php 
	  			echo CHtml::listBox('setup[gift_id]', '', $this->getLuckGiftOption(),array('size'=>1,'class'=>'input-small','empty'=>'请选择'));
			?>
			<span class="label label-important" style="margin-left:10px;display:none;" id="info_setup_gift_id"></span>
	  	</div>
	  </div>
	  <div class="control-group">
	  	<label class="control-label" for="focusedInput">类型</label>
	  	<div class="controls">
	  		<?php 
	  			echo CHtml::listBox('setup[type]', '', $types,array('size'=>1,'class'=>'input-small','empty'=>'请选择'));
			?>
			<span class="label label-important" style="margin-left:10px;display:none;" id="info_setup_type"></span>
	  	</div>
	  </div>
	  
	  <div id="setup_info_text"> 
	  	
	  </div>
	  
	  <div class="control-group">
	  	<label class="control-label" for="focusedInput">奖品概率</label>
	  	<div class="controls">
	  		<?php 
				echo CHtml::textField('setup[chances]',0, array('class'=>'input-small'));
				echo CHtml::hiddenField('setup[award_id]',0);
			?>
	  	</div>
	  </div>
	  <div class="form-actions">
		<button type="submit" class="btn btn-primary">更新</button>
		<button type="button" class="btn btn-primary" id="_poolset_reset">重置</button>
	  </div>
	</fieldset>
</form>