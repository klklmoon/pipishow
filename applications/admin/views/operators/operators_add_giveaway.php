<?php
$this->breadcrumbs = array('主播管理','新增赠品');
$types = $consumeSer->getGiveawayType();
unset($types[GIVEAWAY_TYPE_PIPIEGGS]);
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 新增赠品</h2>
		</div>
		<?php }?>
		<div class="box-content">
			<?php if(!empty($notices)){?>
			<div class="alert alert-block">
			<?php foreach($notices as $notice){?>
				<p><?php echo $notice[0];?></p>
			<?php }?>
			</div>
			<?php }?>
			<form class="form-horizontal" action="<?php echo $this->createUrl('operators/addgiveaway',array('op'=>'addGiveaway'));?>" method="post">
				<fieldset>
				<div class="control-group">
					<label class="control-label" for="focusedInput">赠品类型</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('_form[type]', '', $types,array('size'=>1,'class'=>'input-small','empty'=>'-请选择-'));
						?>
						<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_type"></span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label">用户名/UID</label>
					<div class="controls">
					  <input class="input-small focused" id="indexForm_dotey_name" name="dotey_info" type="text" value="">
					  <input class="btn" type="button" value="验证" name="valid_dotey_info" id="valid_dotey_info" isDotey="0">
					  <span class="label label-important"  id="valid_dotey_info_noty" style="margin-left:10px; display:none;" isSubmit="1"></span>
					</div>
				  </div>
				  
				  <!-- 用户结果集合 -->
				  <div id="dotey_info_uids" class="box" style="padding:5px;display:none;"> 
				  	
				  </div>
				  
				  <!-- 其它输入项 -->
				  <div id="dotey_info_text" class="box" style="padding:5px;display:none;"> 
				  	
				  </div>
				  
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary" id="submit_award">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script>
var TYPE_GIFT = '<?php echo GIVEAWAY_TYPE_GIFT;?>';
var TYPE_CHARMPOINTS = '<?php echo GIVEAWAY_TYPE_PROPS;?>';
var TYPE_CHARM = '<?php echo GIVEAWAY_TYPE_CHARM;?>';
var TYPE_CHARMPOINTS = '<?php echo GIVEAWAY_TYPE_CHARMPOINTS;?>';
var TYPE_DEDICATION = '<?php echo GIVEAWAY_TYPE_DEDICATION;?>';
var TYPE_PIPIEGGS = '<?php echo GIVEAWAY_TYPE_PIPIEGGS;?>';

$(function(){
	//改变状态动作 初始化数据
	$('#_form_type').change(function(e){
		var type = $(this).attr('value');
		$('#dotey_info_uids').html('').hide();
		if(type == TYPE_CHARM || type == TYPE_CHARMPOINTS){
			$('#valid_dotey_info').attr('isDotey',1);
		}else{
			$('#valid_dotey_info').attr('isDotey',0);
		}

		if(type >= TYPE_GIFT ){
			$.ajax({
				url:"<?php echo $this->createUrl('operators/addgiveaway');?>",
				type:'post',
				dataType:'html',
				data:{'type':type,'op':'getGiveawayView'},
				success:function(msg){
					$('#dotey_info_text').html(msg).show();
				}
			});
		}
	});
	//验证主播
	$("#valid_dotey_info").click(function(){
		var doteyName = $("#indexForm_dotey_name").attr('value');
		var isDotey = $("#valid_dotey_info").attr('isDotey');
		
		if(doteyName){
			$("#valid_dotey_info_noty").html("").hide();
			$.ajax({
				url:"<?php echo $this->createUrl("operators/addgiveaway");?>",
				type:"post",
				dataType:"text",
				data:{"op":"checkDoteyInfo","doteyName":doteyName,'isDotey':isDotey},
				success:function(msg){
					var data = msg.split('#xx#');
					if(data[0] == 1){
						var doteyUid 		= data[1];
						var doteyUsername 	= data[2];
						var doteyNickname 	= data[3];
						var isReturn = false;
						$("input[name='_form[uid][]']").each(function(){
							if($(this).attr('value') == doteyUid){
								isReturn = true;
							}
						});

						if(isReturn){
							$('#valid_dotey_info_noty').html(doteyUsername+' 已经存在，不能重复添加').show();
							return false;
						}else{
							$('#valid_dotey_info_noty').html('').hide();
						}
						
						var num = $('#dotey_info_uids').children('.control-group').length;
						var html = '<div class="control-group">';
						html += '<label class="control-label">'+doteyUsername+'</label>';
						html += '<div class="controls">';
						html += '<input class="input-small focused" id="_form_uid_'+(num+1)+'" name="_form[uid][]" type="text" value="'+doteyUid+'" readonly="readonly">';
						html += '<i class="icon-remove" style="margin-left:20px;" onclick="'+"$(this).parents('.control-group').detach()"+'"></i></div></div>';  
						$('#dotey_info_uids').append(html).show();
					}else{
						$("#valid_dotey_info_noty").html(msg).show();
					}
				}
			});
		}else{
			$("#valid_dotey_info_noty").html("请输入主播名称或主播ID").show();
		}
	});
	
	//表单提交前的动作
	$('#submit_award').click(function(){
		if(!$('#_form_type').attr('value')){
			$('#info_form_type').html('请选择赠品类型').show();
			return false;
		}else{
			$('#info_form_type').html('').hide();
		}
		
		if($('#dotey_info_uids').children('.control-group').length <= 0){
			$('#valid_dotey_info_noty').html('赠送对象不能为空').show();
			return false;
		}else{
			$('#valid_dotey_info_noty').html('').hide();
		}
		
		return checkGiveawayInfo();
	});
	
})
</script>