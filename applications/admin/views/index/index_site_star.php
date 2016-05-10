<?php
$this->breadcrumbs = array('首页管理','明星主播版块');
?>
<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:140px;margin-left:10px;padding:3px 5px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!isset($isAjax) || !$isAjax){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>明星主播版块</h2>
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
			<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('index/sitestar',array('op'=>'addSiteStar'));?>" enctype="multipart/form-data">
				<fieldset>
				 <div class="control-group">
					<label class="control-label" for="forum_desc">版块描述：</label>
					<div class="controls">
					  <textarea rows="3" cols="60" id="forum_desc" name="forum_desc"><?php echo $forum_desc;?></textarea>
					</div>
				  </div>
				<dl style="border-top:2px #999999 solid"></dl>
				 <div class="control-group">
					<label class="control-label" for="indexForm[dotey_name]">主播名/ID</label>
					<div class="controls">
					  <input class="input-small focused" id="indexForm_dotey_name" name="dotey_info" type="text" value="">
					  <input class="btn" type="button" value="验证" name="valid_dotey_info" id="valid_dotey_info">
					  <span class="label label-important"  id="valid_dotey_info_noty" style="margin-left:10px; display:none;" isSubmit="1"></span>
					</div>
				  </div>
				  
				  <div class="control-group" style="display:none;" id="show_addEffect">
				      <label class="control-label"></label>
				      <div class="controls">
						  <a href="javascript:;" id="addEffect" class="btn">+添加侧栏一</a>
				      </div>
				   </div>
				  
				  <dl style="border-top:1px #999999 dotted"></dl>
				  	<div class="control-group">
				  	<dl style="margin-top:10px;">
						<dt>主播用户</dt>
						<dt>是否自动</dt>
						<!-- <dt>排序</dt> -->
						<dt>标题</dt>
						<dt>链接地址</dt>
						<!-- <dt>明星图片</dt> -->
					</dl>
					<dl class="effect"> </dl>
					<?php if(isset($sideRightData)){?>
						<?php foreach($sideRightData as $k=>$data){?>
						<dl class="effect" id="<?php echo $data['uid'];?>">
							<dd>
							≥
							<input name="autormd[nickname][<?php echo $data['uid']?>]" type="text" readonly="true" id="autormd[nickname][<?php echo $data['uid']?>]" class="input-small focused" size="10" value="<?php echo $data['nickname'];?>">
							</dd>
						<dt>
							<span class="label label-success">是</span>
						</dt>
						<dt>
							<span class="label label-warning">无</span>
						</dt>
						<dt>
							<span class="label label-warning">无</span>
						</dt>
						<dt>
						</dt>
					</dl>
						<?php }?>
					<?php }?>
					<?php if(isset($starData)){?>
					<?php foreach($starData as $k=>$data){?>
					<dl class="effect" id="<?php echo $data['target_id'];?>">
						<dd>
							≥
							<input name="indexForm[target_id][<?php echo $k?>]" type="hidden" id="indexForm[target_id][<?php echo $data['target_id']?>]" class="input-small focused" size="10" value="<?php echo $data['target_id'];?>">
							<input name="indexForm[content][nickname][<?php echo $k?>]" type="text" readonly="true" id="indexForm[content][nickname][<?php echo $data['target_id']?>]" class="input-small focused" size="10" value="<?php echo isset($data['content']['nickname'])?$data['content']['nickname']:'';?>">
							<input name="indexForm[content][username][<?php echo $k?>]" type="hidden" readonly="true" id="indexForm[content][username][<?php echo $data['target_id']?>]" class="input-small focused" size="10" value="<?php echo isset($data['content']['username'])?$data['content']['username']:'';?>">
							<input name="indexForm[operate_id][<?php echo $k?>]" type="hidden" id="indexForm[operate_id][<?php echo $data['operate_id']?>]" value="<?php echo $data['operate_id'];?>">
						</dd>
						<dt>
							<span class="label label-warning">否</span>
						</dt>
						<!-- 
						<dt>
							<input name="indexForm[sort][<?php echo $k?>]" type="text" id="indexForm[sort][<?php echo $data['sort']?>]" class="input-small focused" size="10" value="<?php echo $data['sort'];?>">
						</dt>
						 -->
						<dt>
							<input name="indexForm[subject][<?php echo $k?>]" type="text" id="indexForm[subject][<?php echo $data['subject']?>]" class="input-small focused" size="10" value="<?php echo $data['subject'];?>">
						</dt>
						<dt>
							<input name="indexForm[textlink][<?php echo $k?>]" type="text" id="indexForm[textlink][<?php echo $data['textlink']?>]" class="input-small focused" size="10" value="<?php echo $data['textlink'];?>">
						</dt>
						<!-- <dt style="width:230px;">
							<input name="indexForm[piclink][<?php echo $k?>]" type="hidden" id="indexForm[piclink][<?php echo $data['piclink']?>]" value="<?php echo $data['piclink'];?>">
							<input name="indexForm[piclink][<?php echo $k?>]" id="indexForm[piclink][<?php echo $data['operate_id']?>]" type="file" class="input-small focused" size="10" value="<?php echo $data['piclink'];?>">
						</dt> -->
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
				url:"<?php echo $this->createUrl("index/homewindow");?>",
				type:"post",
				dataType:"text",
				data:{"op":"checkDoteyInfo","doteyName":doteyName},
				success:function(msg){
					var data = msg.split('#xx#');
					if(data[0] == 1){
						$("#valid_dotey_info_noty").html(data[3]+'&nbsp;&nbsp; 验证通过').show();
						$("#addEffect").attr('doteyId',data[1]);
						$("#addEffect").attr('doteyName',data[2]);
						$("#addEffect").attr('nickName',data[3]);
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
	
	//添加厨窗操作
	$("#addEffect").click(function(){
		var effectNum = $(".effect").length;
		var doteyId = $(this).attr('doteyId');
		var doteyName = $(this).attr('doteyName');
		var nickName = $(this).attr('nickName');

		if(doteyId && doteyName && nickName){
			if(effectNum>500){
				alert('最多只能推荐500个');
			}else{
				if($('#'+doteyId).attr('class') == 'effect' ){
					$("#valid_dotey_info_noty").html(doteyName+' 已经存在于侧栏推荐一，不能重复添加').show();
					$(this).removeAttr('doteyId','');
					$(this).removeAttr('doteyName','');
					$("#show_addEffect").hide();
				}else{
					var effectHtml = "<dl class='effect' id='"+doteyId+"'><dd>≥";
					effectHtml += "<input name='indexForm[target_id][]' type='hidden' id='indexForm[target_id]["+effectNum+"]' class='input-small focused' size='10' value='"+doteyId+"'/>";
					effectHtml += "<input name='indexForm[content][nickname][]' type='text' readonly='true' id='indexForm[content][nickname]["+effectNum+"]' class='input-small focused' size='10' value='"+nickName+"'/>";
					effectHtml += "<input name='indexForm[content][username][]' type='hidden' readonly='true' id='indexForm[content][username]["+effectNum+"]' class='input-small focused' size='10' value='"+doteyName+"'/></dd>";
					effectHtml += "<dt><span class='label label-warning'>否</span></dt>";
					effectHtml += "<!-- <dt><input name='indexForm[sort][]' type='text' id='indexForm[sort]["+effectNum+"]' class='input-small focused' size='10' value=''/></dt>-->";
					effectHtml += "<dt><input name='indexForm[subject][]' type='text' id='indexForm[subject]["+effectNum+"]' class='input-small focused' size='10' value=''/></dt>";
					effectHtml += "<dt><input name='indexForm[textlink][]' type='text' id='indexForm[textlink]["+effectNum+"]' class='input-small focused' size='10' value=''/></dt>";
					//effectHtml += "<dt style='width:230px;'><input name='indexForm[piclink][]' id='indexForm[piclink]["+effectNum+"]' type='file' class='input-small focused' size='10' /></dt>";
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
				url:'<?php echo $this->createUrl('index/sitestar');?>',
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

	//选择文件时触发
	$("dl :file").change(function(){
		var prev = $(this).prev(':input');
		if(prev){
			prev.detach();
		}
		
	});
	
	//提交 前动作 确认所有 file都需要上传文件
	$(":submit").click(function(){
		$("dl :file").each(function(){
			if(!($(this).prev().attr('value'))){
				if(!$(this).attr('value')){
					$(this).next('span').remove();
					$(this).after("<span>明星图片不能为空</span>");
				}else{
					$(this).next('span').remove();
				}
			}
		});
		return true;
	});
	
})
</script>