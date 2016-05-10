<?php
$this->breadcrumbs = array('首页管理','首页顶部动态图');
$doteySer = new DoteyService();
?>
<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:140px;margin-left:10px;padding:3px 5px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!isset($isAjax) || !$isAjax){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>首页顶部动态图</h2>
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
			<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('index/homecarousel',array('op'=>'addHomeCarousel'));?>" enctype="multipart/form-data">
				<fieldset>
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
						  <a href="javascript:;" id="addEffect" class="btn">+添加到本站明星</a>
				      </div>
				   </div>
				  
				  <dl style="border-top:1px #999999 dotted"></dl>
				  	本站明星列表
				  	<div class="control-group">
				  	<dl style="margin-top:10px;">
						<dt>明星用户</dt>
						<dt style="width:70px;">UID</dt>
						<!-- <dt>排序</dt> -->
						<dt>标题</dt>
						<dt>链接地址</dt>
						<dt style='width:240px;'>明星图片 (尺寸230*230)</dt>
					</dl>
					<dl class="effect"> </dl>
					<?php if(isset($sideData)){?>
					<?php foreach($sideData as $k=>$data){?>
					<?php $uid = $data['target_id'];?>
					<dl class="effect" id="<?php echo $data['target_id'];?>">
						<input name="indexForm[operate_id][<?php echo $k?>]" type="hidden" id="indexForm[operate_id][<?php echo $data['operate_id']?>]" value="<?php echo $data['operate_id'];?>">
						<dd>
							≥
							<input name="indexForm[target_id][<?php echo $k?>]" type="hidden" id="indexForm[target_id][<?php echo $data['target_id']?>]" class="input-small focused" size="10" value="<?php echo $data['target_id'];?>">
							<input name="indexForm[content][nickname][<?php echo $k?>]" type="text" readonly="true" id="indexForm[content][nickname][<?php echo $data['target_id']?>]" class="input-small focused" size="10" value="<?php echo isset($data['content']['nickname'])?$data['content']['nickname']:'';?>">
							<input name="indexForm[content][username][<?php echo $k?>]" type="hidden" readonly="true" id="indexForm[content][username][<?php echo $data['target_id']?>]" class="input-small focused" size="10" value="<?php echo isset($data['content']['username'])?$data['content']['username']:'';?>">
						</dd>
						<dt style="width:70px;">
							<span><?php echo $uid;?></span>
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
						<dt style="width:280px;">
							<input name="doteyinfo[display_big][<?php echo $uid?>]" id="doteyinfo[display_big][<?php echo $uid;?>]" type="file" class="focused" size="10" value="">
							<?php if (!empty($data['dynamic_big'])){?>
							<span title="查看大图" class="icon icon-color icon-link" imgSrc="<?php echo $data['dynamic_big'];?>"></span>
							<span title="查看中图" class="icon icon-color icon-link" imgSrc="<?php echo $data['dynamic_middle'];?>"></span>
							<span title="查看小图" class="icon icon-color icon-link" imgSrc="<?php echo $data['dynamic_small'];?>"></span>
							<?php }?>
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
<!-- 浮层 -->
<div class="modal hide fade" id="modal_manage">
	<div class="modal-body" id="modal_manage_body">
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
				url:"<?php echo $this->createUrl("index/homecarousel");?>",
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
	//查看图片
	$(".icon-link").click(function(e){
		var imgSrc = $(this).attr('imgSrc');
		if(imgSrc){
			var content = "<center><img src='"+imgSrc+"' /></center>";
			$('#modal_manage_body').html(content);
			e.preventDefault();
			$('#modal_manage').modal('show');
		}

	});
	//添加侧栏推荐 
	$("#addEffect").click(function(){
		var effectNum = $(".effect").length;
		var doteyId = $(this).attr('doteyId');
		var doteyName = $(this).attr('doteyName');
		var nickName = $(this).attr('nickName');

		if(doteyId && doteyName && nickName){
			if(effectNum>500){
				alert('最多只能添加500个侧栏推荐位');
			}else{
				if($('#'+doteyId).attr('class') == 'effect' ){
					$("#valid_dotey_info_noty").html(doteyName+' 已经存在于本站明星列表中，不能重复添加').show();
					$(this).removeAttr('doteyId','');
					$(this).removeAttr('doteyName','');
					$(this).removeAttr('nickName','');
					$("#show_addEffect").hide();
				}else{
					var effectHtml = "<dl class='effect' id='"+doteyId+"'><dd>≥";
					effectHtml += "<input name='indexForm[target_id][]' type='hidden' id='indexForm[target_id]["+effectNum+"]' class='input-small focused' size='10' value='"+doteyId+"'/>";
					effectHtml += "<input name='indexForm[content][nickname][]' type='text' readonly='true' id='indexForm[content][nickname]["+effectNum+"]' class='input-small focused' size='10' value='"+nickName+"'/>";
					effectHtml += "<input name='indexForm[content][username][]' type='hidden' readonly='true' id='indexForm[content][username]["+effectNum+"]' class='input-small focused' size='10' value='"+doteyName+"'/></dd>";
//					effectHtml += "<dt><input name='indexForm[sort][]' type='text' id='indexForm[sort]["+effectNum+"]' class='input-small focused' size='10' value=''/></dt>";
					effectHtml += "<dt><input name='indexForm[subject][]' type='text' id='indexForm[subject]["+effectNum+"]' class='input-small focused' size='10' value=''/></dt>";
					effectHtml += "<dt><input name='indexForm[textlink][]' type='text' id='indexForm[textlink]["+effectNum+"]' class='input-small focused' size='10' value=''/></dt>";
					effectHtml += "<dt style='width:240px;'><input name='doteyinfo[display_big]["+doteyId+"]' id='doteyinfo[display_big]["+doteyId+"]' type='file' class='input-small focused' value=''></dt>";
					effectHtml += "<dt><i class='icon-remove' onclick=\"$(this).parents('dl').detach()\"></i></dt></dl>";
					$(".effect:last").after(effectHtml);
					effectNum++;
					$(this).removeAttr('doteyId','');
					$(this).removeAttr('doteyName','');
					$(this).removeAttr('nickName','');
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
				url:'<?php echo $this->createUrl('index/homecarousel');?>',
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

})
</script>