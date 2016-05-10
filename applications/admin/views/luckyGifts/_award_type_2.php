<div class="control-group">
	<label class="control-label" for="focusedInput">道具分类</label>
	<div class="controls">
		<?php echo CHtml::listBox('setup[cat_id]','',$this->getAllowSendPropsCat(),array('class'=>'input-small','size'=>1,'empty'=>' '));?>
	  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_cat_id"></span>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="focusedInput">道具名称</label>
	<div class="controls">
		<?php echo CHtml::listBox('setup[target_id]','',array(),array('class'=>'input-medium','size'=>1,'empty'=>' '));?>
	  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_prop_id"></span>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="focusedInput">数量</label>
	<div class="controls">
		<?php echo CHtml::textField('setup[award]',1,array('class'=>'input-small focused'));?>
	</div>
</div>
 
<script>
function checkPropList(cat_id,isContinue,target_id,award){
	if(cat_id){
		//获取道具
		$.ajax({
			url:"<?php echo $this->createUrl("luckyGifts/index");?>",
			type:"post",
			dataType:'html',
			data:{"cat_id":cat_id,"op":"checkPropList",'tab':'giftAward','isEgg':0},
			success:function(msg){
				if(msg == 1){
					$('#info_form_cat_id').html('请选择道具分类').show();
				}else if(msg == 2){
					$('#info_form_prop_id').html('道具列表为空,不能对该道具进行操作').show();
				}else{
					$('#info_form_prop_id').html('').hide();
					$('#info_form_cat_id').html('').hide();
					$('#setup_target_id').html(msg);
					if(isContinue){
						$('#setup_target_id option').each(function(i){
							if(parseInt($(this).attr('value')) == parseInt(target_id)){
								$(this).attr('selected','selected');
							}
						});
						$('#setup_award').attr('value',award);
					}
				}
			}
		});	
	}
}

$('#setup_cat_id').click(function(){
	var cat_id = $(this).attr('value');
	checkPropList(cat_id,false,0,0);
});

function checkAwardInfo(){
	if(!$('#setup_cat_id').attr('value')){
		$('#info_form_cat_id').html('请选择道具分类').show();
		return false;
	}else{
		$('#info_form_cat_id').html('').hide();
	}

	if(!$('#setup_target_id').attr('value')){
		$('#info_form_prop_id').html('请选择道具').show();
		return false;
	}else{
		$('#info_form_prop_id').html('').hide();
	}

	if(!$('#setup_award').attr('value')){
		$('#info_form_prop_id').html('请填写相应数量').show();
		return false;
	}else{
		$('#info_form_prop_id').html('').hide();
	}
	return true;
};
</script>