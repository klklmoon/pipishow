<?php
$this->breadcrumbs = array('首页管理','首页强推');
?>
<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:140px;margin-left:10px;padding:3px 5px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!isset($isAjax) || !$isAjax){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>首页强推管理</h2>
		</div>
		<?php }?>
		<div class="box-content">
			<?php if(isset($notices) && count($notices)>0){?>
			<div class="alert alert-block" style="margin-left:60px;margin-right:200px;clear:both;">
				<button type="button" class="close" data-dismiss="alert">×</button>
			<?php foreach($notices as $notice){?>
				<p><?php echo isset($notice[0])?$notice[0]:$notice;?></p>
			<?php }?>
			</div>
			<?php }?>
			<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('index/livepush',array('op'=>'addLivePush'));?>" enctype="multipart/form-data">
				<fieldset>
				  <div class="control-group">
					<label class="control-label" for="rmd_global">全局推荐</label>
					<div class="controls">
						<?php echo CHtml::checkBox(
									'rmd[global]',
									$cvalue['global'],
									array( 'title'=>'全局推荐', 'value'=>'1', )
							);?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="rmd_custom">自定义</label>
					<div class="controls">
						<?php echo CHtml::checkBoxList(
									'rmd[custom]',
									$cvalue['custom'],
									array(2=>'主播强推',4=>'今日推荐'),
									array(
										'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
										'labelOptions'=>array('class'=>'checkbox inline'),
										)
							);?>
					</div>
				  </div>
				  <dl style="border-top:1px #999999 dotted"></dl>
				 <div class="control-group">
					<label class="control-label" for="indexForm[dotey_name]">主播用户名/ID</label>
					<div class="controls">
					  <input class="input-small focused" id="indexForm_dotey_name" name="dotey_info" type="text" value="">
					  <input class="btn" type="button" value="验证" name="valid_dotey_info" id="valid_dotey_info">
					  <span class="label label-important"  id="valid_dotey_info_noty" style="margin-left:10px; display:none;" isSubmit="1"></span>
					</div>
				  </div>
				  
				  <div class="control-group" style="display:none;" id="show_addEffect">
				      <label class="control-label"></label>
				      <div class="controls">
						  <a href="javascript:;" id="addEffect" class="btn">+添加到首页强推</a>
				      </div>
				   </div>
				  
				  <dl style="border-top:1px #999999 dotted"></dl>
				  	首页强推推荐列表（最大上限为50个推荐位）
				  	<div class="control-group">
				  	<dl style="margin-top:10px;">
						<dt>强推主播</dt>
						<dt>排序</dt>
						<dt>描述</dt>
						<dt>链接地址</dt>
					</dl>
					<dl class="effect"> </dl>
					<?php if(isset($livePushData)){?>
					<?php foreach($livePushData as $k=>$data){?>
					<dl class="effect" id="<?php echo $data['target_id'];?>">
						<dd>
							≥
							<input name="indexForm[target_id][<?php echo $k?>]" type="hidden" id="indexForm[target_id][<?php echo $data['target_id']?>]" class="input-small focused" size="10" value="<?php echo $data['target_id'];?>">
							<input name="indexForm[target_name][<?php echo $k?>]" type="text" readonly="true" id="indexForm[target_name][<?php echo $data['target_id']?>]" class="input-small focused" size="10" value="<?php echo isset($doteyInfo[$data['target_id']])?$doteyInfo[$data['target_id']]:$data['target_id'];?>">
						</dd>
						<dt>
							<input name="indexForm[operate_id][<?php echo $k?>]" type="hidden" id="indexForm[operate_id][<?php echo $data['operate_id']?>]" value="<?php echo $data['operate_id'];?>">
							<input name="indexForm[sort][<?php echo $k?>]" type="text" id="indexForm[sort][<?php echo $data['sort']?>]" class="input-small focused" size="10" value="<?php echo $data['sort'];?>">
						</dt>
						<dt>
							<input name="indexForm[subject][<?php echo $k?>]" type="text" id="indexForm[subject][<?php echo $data['subject']?>]" class="input-small focused" size="10" value="<?php echo $data['subject'];?>">
						</dt>
						<dt>
							<input name="indexForm[textlink][<?php echo $k?>]" type="text" id="indexForm[textlink][<?php echo $data['textlink']?>]" class="input-small focused" size="10" value="<?php echo $data['textlink'];?>">
						</dt>
						<dt>
							<i class="icon-remove" operateId=<?php echo $data['operate_id'];?>></i>
						</dt>
					</dl>
					<?php }?>
					<?php }?>
				  </div>
				  
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				  
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script type="text/javascript">
$(document).ready(function(){
	//验证主播
	$("#valid_dotey_info").click(function(){
		var doteyName = $("#indexForm_dotey_name").attr('value');
		if(doteyName){
			$("#valid_dotey_info_noty").html("").hide();
			$.ajax({
				url:"<?php echo $this->createUrl("index/sidermd");?>",
				type:"post",
				dataType:"text",
				data:{"op":"checkDoteyInfo","doteyName":doteyName},
				success:function(msg){
					var data = msg.split('#xx#');
					if(data[0] == 1){
						$("#valid_dotey_info_noty").html(data[3]+'&nbsp;&nbsp; 验证通过').show();
						$("#addEffect").attr('doteyId',data[1]);
						$("#addEffect").attr('doteyName',data[2]);
						$("#addEffect").attr('doteyNick',data[3]);
						$("#show_addEffect").show();
						
					}else{
						$("#valid_dotey_info_noty").html(msg).show();
						$("#show_addEffect").hide();
					}
				}
			});
		}else{
			$("#valid_dotey_info_noty").html("请输入主播名称或主播ID").show();
			$("#show_addEffect").hide();
		}
	});
	
	//添加侧栏推荐 
	$("#addEffect").click(function(){
		var effectNum = $(".effect").length;
		var doteyId = $(this).attr('doteyId');
		var doteyName = $(this).attr('doteyName');
		var doteyNick = $(this).attr('doteyNick');

		if(doteyId && doteyName && doteyNick){
			if(effectNum>50){
				alert('最多只能添加50个强推主播');
			}else{
				if($('#'+doteyId).attr('class') == 'effect' ){
					$("#valid_dotey_info_noty").html(doteyName+' 已经存在于强推主播列表中，不能重复添加').show();
					$(this).removeAttr('doteyId','');
					$(this).removeAttr('doteyName','');
					$(this).removeAttr('doteyNick','');
					$("#show_addEffect").hide();
				}else{
					var effectHtml = "<dl class='effect' id='"+doteyId+"'><dd>≥";
					effectHtml += "<input name='indexForm[target_id][]' type='hidden' id='indexForm[target_id]["+effectNum+"]' class='input-small focused' size='10' value='"+doteyId+"'/>";
					effectHtml += "<input name='indexForm[target_name][]' type='text' readonly='true' id='indexForm[target_name]["+effectNum+"]' class='input-small focused' size='10' value='"+doteyNick+"'/></dd>";
					effectHtml += "<dt><input name='indexForm[sort][]' type='text' id='indexForm[sort]["+effectNum+"]' class='input-small focused' size='10' value=''/></dt>";
					effectHtml += "<dt><input name='indexForm[subject][]' type='text' id='indexForm[subject]["+effectNum+"]' class='input-small focused' size='10' value=''/></dt>";
					effectHtml += "<dt><input name='indexForm[textlink][]' type='text' id='indexForm[textlink]["+effectNum+"]' class='input-small focused' size='10' value=''/></dt>";
					effectHtml += "<dt><i class='icon-remove' onclick=\"$(this).parents('dl').detach()\"></i></dt></dl>";
					$(".effect:last").after(effectHtml);
					effectNum++;
					$(this).removeAttr('doteyId','');
					$(this).removeAttr('doteyName','');
					$("#show_addEffect").hide();
				}
			}
		}else{
			$(this).hide();
			$("#valid_dotey_info_noty").html('请先检验出合法的用户名后再添加').show();
		}
	});
	
	//删除首页厨窗
	$("dt .icon-remove").click(function(){
		var operateId = $(this).attr('operateId');
		var obj = this;
		if(operateId){
			$.ajax({
				type:'post',
				url:'<?php echo $this->createUrl('index/livepush');?>',
				dataType:'html',
				data:{'op':'delOperate','operateId':operateId},
				success:function(msg){
					if(msg == 1){
						$(obj).parents("dl").detach();
					}else{
						alert(msg);
					}
				}
			});
		}			
	});

	$("input[name='rmd[global]']").click(function(){
		var checked = $(this).attr('checked');
		if(checked){
			$("input[name='rmd[custom][]']").each(function(){
				$(this).attr('checked',false);
			});
		}
	});

	$("input[name='rmd[custom][]']").click(function(){
		var checked = $(this).attr('checked');
		if(checked){
			$("input[name='rmd[global]']").attr('checked',false);
		}
	});

	$(':submit').click(function(){
		var _global = $("input[name='rmd[global]']:checked").length;
		var _custom = $("input[name='rmd[custom][]']:checked").length;

		if(_global == 0 && _custom == 0){
			alert('强推配置不能为空,请选择！');
			return false;
		}
		return true;
	});
})
</script>