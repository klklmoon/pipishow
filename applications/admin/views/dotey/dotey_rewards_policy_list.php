<?php
$this->breadcrumbs = array('主播管理','主播报酬政策管理');
$doteyType = $this->doteySer->getDoteyType();
?>
<style type="text/css">
	dl{clear:left;width:auto;margin:0px;}
	dt,dd{float:left;width:130px;margin-left:5px;padding:2px 2px;}
</style>
<!-- 搜索 -->
<div class="row-fluid sortable ui-sortable">
	<div class="box" style="margin:0px;">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('dotey/rewardpolicy');?>" method="post">
				<fieldset>
					<div class="control-group">
						<label class="control-label">主播类型</label>
						<div class="controls">
							<?php $select1 = isset($condition['dotey_type'])?$condition['dotey_type']:''?>
							<?php echo CHtml::listBox('form[dotey_type]', $select1, $doteyType,array('class'=>'input-small','empty'=>'','size'=>1));?>
							<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="indexForm[dotey_name]">主播用户/ID</label>
						<div class="controls">
						  <input class="input-small focused" id="indexForm_dotey_name" name="dotey_info" type="text" value="">
						  <input class="btn" type="button" value="验证" name="valid_dotey_info" id="valid_dotey_info">
						  <span class="label label-important"  id="valid_dotey_info_noty" style="margin-left:10px; display:none;" isSubmit="1"></span>
						</div>
					  </div>
					  
					  <div class="control-group" style="display:none;" id="show_addEffect">
					      <label class="control-label"></label>
					      <div class="controls">
							  <a href="javascript:;" id="addTransScale" class="btn">+To兑换公式</a>
							  <a href="javascript:;" id="addEffectDay" class="btn">+To到有效天</a>
							  <a href="javascript:;" id="addMonthReward" class="btn">+To到月度奖金设置</a>
					      </div>
					   </div>
					  
					  <dl style="border-top:1px #999999 dotted"></dl>
				</fieldset>
			</form>
		</div>
		<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('dotey/rewardpolicy',array('op'=>'addRewardPolicy'));?>" method="post">
			<fieldset>
			<!-- 魅力点提现比例 -->
			<div class="box" style="margin:0px;">
				<div class="box-header well">
					<h2 style="font-size:10px;color:#000;">魅力点提现公式</h2>
					<div class="box-icon">
						<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
					</div>
				</div>
				<div class="box-content" style="overflow-y: auto;max-height:300px;" id="addToTransScale">
					  	<div class="control-group">
					  	<dl style="margin-top:10px;">
							<dt>主播用户</dt>
							<dt>提成规则</dt>
						</dl>
						<dl class="effect"> </dl>
						<?php if(isset($result['scaleList'])){?>
							<?php foreach($result['scaleList'] as $uid=>$scale){?>
							<dl class="effect" id="scale_<?php echo $condition['dotey_type'].'_'.$uid;?>">
								<dd>
									<input name="scale[<?php echo $uid;?>][uid]" type="hidden" readonly="true" id="scale[<?php echo $uid;?>][uid]" class="input-small focused" size="10" value="<?php echo $uid;?>">
									<input name="scale[<?php echo $uid;?>][dotey_type]" type="hidden" readonly="true" id="scale[<?php echo $uid;?>][dotey_type]" class="input-small focused" size="10" value="<?php echo $condition['dotey_type'];?>">
									<input name="_scale[<?php echo $uid;?>][nickname]" type="text" readonly="true" id="scale[<?php echo $uid;?>][nickname]" class="input-small focused" size="10" value="<?php echo isset($doteyInfo[$uid])?$doteyInfo[$uid]['nickname']:'全局';?>">
								</dd>
								<dd>
									<input name="scale[<?php echo $uid;?>][scale]" type="text" id="scale[<?php echo $uid;?>][scale]" class="input-small focused" size="10" value="<?php echo $scale['scale'];?>">
								</dd>
								<dt>
									<i class="icon-remove" onclick="$(this).parents('dl').detach()"></i>
								</dt>
							</dl>
							<?php }?>
						<?php }?>
					  </div>
				</div>
			</div>
			<!-- 有效天 -->
			<div class="box" style="margin:0px;">
				<div class="box-header well">
					<h2 style="font-size:10px;color:#000;">有效天最低直播小时数</h2>
					<div class="box-icon">
						<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
					</div>
				</div>
				<div class="box-content" style="overflow-y: auto;max-height:300px;" id="addToEffectDay">
					<div class="control-group">
					  	<dl style="margin-top:10px;">
							<dt>主播用户</dt>
							<dt>有效天基数(小时)</dt>
						</dl>
						<dl class="effect"> </dl>
						<?php if(isset($result['effectDayList'])){?>
							<?php foreach($result['effectDayList'] as $uid=>$day){?>
							<dl class="effect" id="effect_<?php echo $condition['dotey_type'].'_'.$uid;?>">
								<dd>
									<input name="effectday[<?php echo $uid;?>][uid]" type="hidden" readonly="true" id="effectday[<?php echo $uid;?>][uid]" class="input-small focused" size="10" value="<?php echo $uid;?>">
									<input name="effectday[<?php echo $uid;?>][dotey_type]" type="hidden" readonly="true" id="effectday[<?php echo $uid;?>][dotey_type]" class="input-small focused" size="10" value="<?php echo $condition['dotey_type'];?>">
									<input name="_effectday[<?php echo $uid;?>][nickname]" type="text" readonly="true" id="effectday[<?php echo $uid;?>][nickname]" class="input-small focused" size="10" value="<?php echo isset($doteyInfo[$uid])?$doteyInfo[$uid]['nickname']:'全局';?>">
								</dd>
								<dd>
									<input name="effectday[<?php echo $uid;?>][day]" type="text" id="effectday[<?php echo $uid;?>][day]" class="input-small focused" size="10" value="<?php echo $day['day'];?>">
								</dd>
								<dt>
									<i class="icon-remove" onclick="$(this).parents('dl').detach()"></i>
								</dt>
							</dl>
							<?php }?>
						<?php }?>
					  </div>
				</div>
			</div>
			<!-- 月度奖金 -->
			<div class="box" style="margin:0px;">
				<div class="box-header well">
					<h2 style="font-size:10px;color:#000;">月度底薪奖金设定</h2>
					<div class="box-icon">
						<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
					</div>
				</div>
				<div class="box-content" style="overflow-y: auto;max-height:300px;" id="addToMonthReward">
					<div class="control-group">
					  	<dl style="margin-top:10px;">
							<dt>主播用户</dt>
							<dt>获得魅力点</dt>
							<dt>有效天</dt>
							<dt>小时数</dt>
							<dt>达标底薪</dt>
							<dt>达标奖金</dt>
						</dl>
						<dl class="effect"> </dl>
						<?php if(isset($result['monthReward'])){?>
							<?php foreach($result['monthReward'] as $list){?>
							<dl class="effect" id="reward_<?php echo $condition['dotey_type'].'_'.$list['uid'];?>">
								<dd>
									<input name="reward[<?php echo $list['uid'];?>][uid][]" type="hidden" readonly="true" id="reward[<?php echo $uid;?>][uid][]" class="input-small focused" size="10" value="<?php echo $list['uid'];?>">
									<input name="reward[<?php echo $list['uid'];?>][pay_type][]" type="hidden" readonly="true" id="reward[<?php echo $uid;?>][payType][]" class="input-small focused" size="10" value="<?php echo $list['pay_type'];?>">
									<input name="reward[<?php echo $list['uid'];?>][pay_id][]" type="hidden" readonly="true" id="reward[<?php echo $uid;?>][pay_id][]" class="input-small focused" size="10" value="<?php echo $list['pay_id'];?>">
									<input name="_effectday[<?php echo $list['uid'];?>][nickname][]" type="text" readonly="true" id="reward[<?php echo $list['uid'];?>][nickname][]" class="input-small focused" size="10" value="<?php echo isset($doteyInfo[$list['uid']])?$doteyInfo[$list['uid']]['nickname']:'全局';?>">
								</dd>
								<dd>
									<input name="reward[<?php echo $list['uid'];?>][charm_points][]" type="text" id="reward[<?php echo $uid;?>][charm_points][]" class="input-small focused" size="10" value="<?php echo $list['charm_points'];?>">
								</dd>
								<dd>
									<input name="reward[<?php echo $list['uid'];?>][live_days][]" type="text" id="reward[<?php echo $uid;?>][live_days][]" class="input-small focused" size="10" value="<?php echo $list['live_days'];?>">
								</dd>
								<dd>
									<input name="reward[<?php echo $list['uid'];?>][live_times][]" type="text" id="reward[<?php echo $uid;?>][live_times][]" class="input-small focused" size="10" value="<?php echo $list['live_times'];?>">
								</dd>
								<dd>
									<input name="reward[<?php echo $list['uid'];?>][basic_salary][]" type="text" id="reward[<?php echo $uid;?>][basic_salary][]" class="input-small focused" size="10" value="<?php echo $list['basic_salary'];?>">
								</dd>
								<dd>
									<input name="reward[<?php echo $list['uid'];?>][bonus][]" type="text" id="reward[<?php echo $uid;?>][bonus][]" class="input-small focused" size="10" value="<?php echo $list['bonus'];?>">
								</dd>
								<dt>
									<i class="icon-remove" payId="<?php echo $list['pay_id'];?>"></i>
								</dt>
							</dl>
							<?php }?>
						<?php }?>
					  </div>
				</div>
			</div>
			
			<div class="form-actions">
				<button type="submit" class="btn btn-primary">提交</button>
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
var dotey_type = "<?php echo $condition['dotey_type'];?>";

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