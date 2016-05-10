<?php
$this->breadcrumbs = array('全局配置','礼物消息推送');
?>
<style type="text/css">
	dl{clear:left;width:auto;margin:0px;}
	dt,dd{float:left;width:130px;margin-left:5px;padding:2px 2px;}
</style>
<!-- 搜索 -->
<div class="row-fluid sortable ui-sortable">
	<div class="box" style="margin:0px;">
		<div class="box-content">
		<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('global/giftmsgpush');?>" method="post">
			<fieldset>
			<div class="control-group" style="margin:0px;">
				
				<?php if($notices){?>
				<div class="alert alert-error">
					<button type="button" class="close" data-dismiss="alert">×</button>
					<strong>提示：</strong> <?php echo $notices;?>
				</div>
				<?php }?>
				<h2 style="font-size:12px;color:#000;">本直播间礼物消息</h2>
				<div class="box-content">
					<span>单笔送礼价格大于或等于<?php echo CHtml::textField('msg[private]',isset($keyInfo['private'])?$keyInfo['private']:'10',array('class'=>'input-mini'))?>时，在本直播间公聊窗口内显示礼物消息。（默认值设定：10）</span>
				</div>
				<h2 style="font-size:12px;color:#000;">全局超礼消息</h2>
				<div class="box-content">
					<span>单笔送礼价格大于或等于<?php echo CHtml::textField('msg[global]',isset($keyInfo['global'])?$keyInfo['global']:'8000',array('class'=>'input-mini'))?>时，在全局范围内推送显示超级礼物消息。（默认值设定：8000）</span>
				</div>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				</div>
			</div>
			</fieldset>
		</form>
	</div>
</div>

<!-- 浮层 -->
<div class="modal hide fade" id="dotey_award_manage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>主播报酬管理</h3>
	</div>
	<div class="modal-body" id="dotey_award_manage_body"></div>
</div>

<script type="text/javascript">

$(document).ready(function(){
	//验证主播
	$("#valid_dotey_info").click(function(){
		var doteyName = $("#indexForm_dotey_name").attr('value');
		if(doteyName){
			$("#valid_dotey_info_noty").html("").hide();
			$.ajax({
				url:"<?php echo $this->createUrl("dotey/rewardpolicy");?>",
				type:"post",
				dataType:"text",
				data:{"op":"checkDoteyInfo","doteyName":doteyName},
				success:function(msg){
					var data = msg.split('#xx#');
					if(data[0] == 1){
						$("#valid_dotey_info_noty").html(data[3]+'&nbsp;&nbsp; 验证通过').show();
						$("#addTransScale").html('+To兑换公式').attr('doteyId',data[1]);
						$("#addTransScale").attr('nickName',data[3]);
						$("#addEffectDay").html('+To到有效天').attr('doteyId',data[1]);
						$("#addEffectDay").attr('nickName',data[3]);
						$("#addMonthReward").html('+To到月度奖金').attr('doteyId',data[1]);
						$("#addMonthReward").attr('nickName',data[3]);
						$("#show_addEffect").show();
					}else{
						$("#valid_dotey_info_noty").html(msg).show();
						$("#show_addEffect").hide();
					}
				}
			});
		}else{
			$("#valid_dotey_info_noty").html("请输入主播名称或主播ID").show();
			$("#addTransScale").html("+全局兑换公式").attr('doteyId',0);
			$("#addTransScale").attr('nickName','全局');
			$("#addEffectDay").html('+全局有效天').attr('doteyId',0);
			$("#addEffectDay").attr('nickName','全局');
			$("#addMonthReward").html('+月度奖金').attr('doteyId',0);
			$("#addMonthReward").attr('nickName','全局');
			$("#show_addEffect").show();
		}
	});
	//添加到兑换比例
	$("#addTransScale").click(function(){
		var effectNum = $("#addToTransScale .effect").length;
		var doteyId = $(this).attr('doteyId');
		var nickName = $(this).attr('nickName');
		if(doteyId  && nickName){
			$('#addToTransScale').show();
			if($('#scale_'+dotey_type+'_'+doteyId).attr('class') == 'effect' ){
				$("#valid_dotey_info_noty").html(nickName+' 已经存在于魅力点公式中，不能重复添加').show();
				$(this).removeAttr('doteyId','');
				$(this).removeAttr('nickName','');
				$("#show_addEffect").hide();
			}else{
				var effectHtml = "<dl class='effect' id='scale_"+dotey_type+'_'+doteyId+"'><dd>";
				effectHtml += "<input name='scale["+doteyId+"][dotey_type]' type='hidden' id='scale["+doteyId+"][dotey_type]' class='input-small focused' size='10' value='"+dotey_type+"'/>";
				effectHtml += "<input name='scale["+doteyId+"][uid]' type='hidden' id='scale["+doteyId+"][uid]' class='input-small focused' size='10' value='"+doteyId+"'/>";
				effectHtml += "<input name='_scale[nickname][]' type='text' readonly='true' id='_scale[nickname]["+effectNum+"]' class='input-small focused' size='10' value='"+nickName+"'/></dd>";
				effectHtml += "<dt><input name='scale["+doteyId+"][scale]' type='text' id='scale["+doteyId+"][scale]' class='input-small focused' size='10' value=''/></dt>";
				effectHtml += "<dt><i class='icon-remove' onclick=\"$(this).parents('dl').detach()\"></i></dt></dl>";
				$("#addToTransScale .effect:last").after(effectHtml);
				effectNum++;
				$(this).removeAttr('doteyId','');
				$(this).removeAttr('nickName','');
			}
		}
	});
	//添加有效天
	$("#addEffectDay").click(function(){
		var effectNum = $("#addToEffectDay .effect").length;
		var doteyId = $(this).attr('doteyId');
		var nickName = $(this).attr('nickName');
		if(doteyId  && nickName){
			$('#addToEffectDay').show();
			if($('#effect_'+dotey_type+'_'+doteyId).attr('class') == 'effect' ){
				$("#valid_dotey_info_noty").html(nickName+' 已经存在于有效天当中，不能重复添加').show();
				$(this).removeAttr('doteyId','');
				$(this).removeAttr('nickName','');
				$("#show_addEffect").hide();
			}else{
				var effectHtml = "<dl class='effect' id='effect_"+dotey_type+'_'+doteyId+"'><dd>";
				effectHtml += "<input name='effectday["+doteyId+"][dotey_type]' type='hidden' id='effectday["+doteyId+"][dotey_type]' class='input-small focused' size='10' value='"+dotey_type+"'/>";
				effectHtml += "<input name='effectday["+doteyId+"][uid]' type='hidden' id='effectday["+doteyId+"][uid]' class='input-small focused' size='10' value='"+doteyId+"'/>";
				effectHtml += "<input name='_effectday[nickname][]' type='text' readonly='true' id='_effectday[nickname]["+effectNum+"]' class='input-small focused' size='10' value='"+nickName+"'/></dd>";
				effectHtml += "<dt><input name='effectday["+doteyId+"][day]' type='text' id='effectday["+doteyId+"][day]' class='input-small focused' size='10' value=''/></dt>";
				effectHtml += "<dt><i class='icon-remove' onclick=\"$(this).parents('dl').detach()\"></i></dt></dl>";
				$("#addToEffectDay .effect:last").after(effectHtml);
				effectNum++;
				$(this).removeAttr('doteyId','');
				$(this).removeAttr('nickName','');
			}
		}
	});
	//添加到月度奖金
	$("#addMonthReward").click(function(){
		var effectNum = $("#addToMonthReward .effect").length;
		var doteyId = $(this).attr('doteyId');
		var nickName = $(this).attr('nickName');
		if(doteyId  && nickName){
			$('#addToMonthReward').show();
			var effectHtml = "<dl class='effect' id='reward_"+dotey_type+'_'+doteyId+"'><dd>";
			effectHtml += "<input name='reward["+doteyId+"][pay_type][]' type='hidden' id='reward["+doteyId+"][pay_type][]' class='input-small focused' size='10' value='"+dotey_type+"'/>";
			effectHtml += "<input name='reward["+doteyId+"][uid][]' type='hidden' id='reward["+doteyId+"][uid][]' class='input-small focused' size='10' value='"+doteyId+"'/>";
			effectHtml += "<input name='_reward[nickname][][]' type='text' readonly='true' id='_reward[nickname]["+effectNum+"][]' class='input-small focused' size='10' value='"+nickName+"'/></dd>";
			effectHtml += "<dt><input name='reward["+doteyId+"][charm_points][]' type='text' id='reward["+doteyId+"][charm_points][]' class='input-small focused' size='10' value=''/></dt>";
			effectHtml += "<dt><input name='reward["+doteyId+"][live_days][]' type='text' id='reward["+doteyId+"][live_days][]' class='input-small focused' size='10' value=''/></dt>";
			effectHtml += "<dt><input name='reward["+doteyId+"][live_times][]' type='text' id='reward["+doteyId+"][live_times][]' class='input-small focused' size='10' value=''/></dt>";
			effectHtml += "<dt><input name='reward["+doteyId+"][basic_salary][]' type='text' id='reward["+doteyId+"][basic_salary][]' class='input-small focused' size='10' value=''/></dt>";
			effectHtml += "<dt><input name='reward["+doteyId+"][bonus][]' type='text' id='reward["+doteyId+"][bonus][]' class='input-small focused' size='10' value=''/></dt>";
			effectHtml += "<dt><i class='icon-remove' onclick=\"$(this).parents('dl').detach()\"></i></dt></dl>";
			$("#addToMonthReward .effect:last").after(effectHtml);
			effectNum++;
			$(this).removeAttr('doteyId','');
			$(this).removeAttr('nickName','');
		}
	});
	//删除月度奖金
	$('.icon-remove').click(function(e){
		var pay_id = $(this).attr('payId');
		var obj = this;
		if(pay_id){
			$.ajax({
				url:"<?php echo $this->createUrl('dotey/rewardpolicy');?>",
				type:'post',
				dataType:'text',
				data:{'op':'delRewardMonth','pay_id':pay_id},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('dl').detach();
					}else{
						alert(msg);
					}
				}
			});
		}
	});
})
</script>