<div class="control-group">
	<label class="control-label" for="focusedInput">道具分类</label>
	<div class="controls">
		<?php echo CHtml::listBox('_form[cat_id]','',$this->getAllowSendPropsCat(),array('class'=>'input-small','size'=>1,'empty'=>' '));?>
	  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_cat_id"></span>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="focusedInput">道具名称</label>
	<div class="controls">
		<?php echo CHtml::listBox('_form[prop_id]','',array(),array('class'=>'input-small','size'=>1,'empty'=>' '));?>
	  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_prop_id"></span>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="focusedInput">有效天</label>
	<div class="controls">
		<?php echo CHtml::textField('_form[days]',0,array('class'=>'input-small focused'));?>
	  	<span class="label label-success" style="margin-left:10px;">0:表示为永久</span>
	  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_days"></span>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="focusedInput">数量</label>
	<div class="controls">
		<?php echo CHtml::textField('_form[num]',1,array('class'=>'input-small focused'));?>
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
$('#_form_cat_id').click(function(){
	var cat_id = $(this).attr('value');
	if(cat_id){
		//获取道具
		$.ajax({
			url:"<?php echo $this->createUrl("operators/addgiveaway");?>",
			type:"post",
			dataType:'html',
			data:{"cat_id":cat_id,"op":"checkPropList"},
			success:function(msg){
				if(msg == 1){
					$('#info_form_cat_id').html('请选择道具分类').show();
				}else if(msg == 2){
					$('#info_form_prop_id').html('道具列表为空,不能对该道具进行操作').show();
				}else{
					$('#info_form_prop_id').html('').hide();
					$('#info_form_cat_id').html('').hide();
					$('#_form_prop_id').html(msg);
				}
			}
		});	
	}
});

function checkGiveawayInfo(){
	if(!$('#_form_cat_id').attr('value')){
		$('#info_form_cat_id').html('请选择道具分类').show();
		return false;
	}else{
		$('#info_form_cat_id').html('').hide();
	}

	if(!$('#_form_prop_id').attr('value')){
		$('#info_form_prop_id').html('请选择道具').show();
		return false;
	}else{
		$('#info_form_prop_id').html('').hide();
	}
	
	if(!$('#_form_info').attr('value')){
		$('#info_form_info').html('请填写赠送原因').show();
		return false;
	}else{
		$('#info_form_info').html('').hide();
	}
};
</script>