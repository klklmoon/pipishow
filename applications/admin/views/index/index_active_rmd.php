<?php
$this->breadcrumbs = array('首页管理','专栏推荐');
?>

<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:140px;margin-left:10px;padding:3px 5px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!isset($isAjax) || !$isAjax){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>专栏推荐</h2>
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
			<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('index/activityrmd',array('op'=>'addActivityRmd'));?>" enctype="multipart/form-data">
				<fieldset>
				  <div class="control-group" id="show_addEffect">
				      <label class="control-label"></label>
				      <div class="controls">
						  <a href="javascript:;" id="addEffect" class="btn">+添加专栏推荐</a>
				      </div>
				   </div>
				  
				  <dl style="border-top:1px #999999 dotted"></dl>
				  	首页专栏推荐列表操作（最大上限为5张图片）
				  	<div class="control-group">
				  	<dl style="margin-top:10px;">
						<dt>专栏名称</dt>
						<dt>排序</dt>
						<dt>链接地址</dt>
						<dt>活动图片</dt>
					</dl>
					<dl class="effect"> </dl>
					<?php if(isset($activeData)){?>
					<?php foreach($activeData as $k=>$data){?>
					<dl class="effect">
						<dd>
							≥ <input name="indexForm[subject][<?php echo $k?>]" type="text" id="indexForm[subject][<?php echo $data['subject']?>]" class="input-small focused" size="10" value="<?php echo $data['subject'];?>">
						</dd>
						<dt>
							<input name="indexForm[operate_id][<?php echo $k?>]" type="hidden" id="indexForm[operate_id][<?php echo $data['operate_id']?>]" value="<?php echo $data['operate_id'];?>">
							<input name="indexForm[sort][<?php echo $k?>]" type="text" id="indexForm[sort][<?php echo $data['sort']?>]" class="input-small focused" size="10" value="<?php echo $data['sort'];?>">
						</dt>
						<dt>
							<input name="indexForm[textlink][<?php echo $k?>]" type="text" id="indexForm[textlink][<?php echo $data['textlink']?>]" class="input-small focused" size="10" value="<?php echo $data['textlink'];?>">
						</dt>
						<dt style="width:230px;">
							<input name="indexForm[piclink][<?php echo $k?>]" type="hidden" id="indexForm[piclink][<?php echo $data['piclink']?>]" value="<?php echo $data['piclink'];?>">
							<input name="indexForm[piclink][<?php echo $k?>]" id="indexForm[piclink][<?php echo $data['operate_id']?>]" type="file" class="input-small focused" size="10" value="<?php echo $data['piclink'];?>">
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
	//添加厨窗操作
	$("#addEffect").click(function(){
		var effectNum = $(".effect").length;

		if(effectNum>5){
			alert('最多只能添加5个动画效果');
		}else{
			var effectHtml = "<dl class='effect'><dd>≥";
			effectHtml += "<input name='indexForm[subject][]' type='text' id='indexForm[subject]["+effectNum+"]' class='input-small focused' size='10' value=''/></dd>";
			effectHtml += "<dt><input name='indexForm[sort][]' type='text' id='indexForm[sort]["+effectNum+"]' class='input-small focused' size='10' value=''/></dt>";
			effectHtml += "<dt><input name='indexForm[textlink][]' type='text' id='indexForm[textlink]["+effectNum+"]' class='input-small focused' size='10' value=''/></dt>";
			effectHtml += "<dt style='width:230px;'><input name='indexForm[piclink][]' id='indexForm[piclink]["+effectNum+"]' type='file' class='input-small focused' size='10' /></dt>";
			effectHtml += "<dt><i class='icon-remove' onclick=\"$(this).parents('dl').detach()\"></i></dt></dl>";
			$(".effect:last").after(effectHtml);
			effectNum++;
		}
	});
	
	//删除首页厨窗
	$("dt .icon-remove").click(function(){
		var operateId = $(this).attr('operateId');
		var obj = this;
		if(operateId){
			$.ajax({
				type:'post',
				url:'<?php echo $this->createUrl('index/homewindow');?>',
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
					$(this).after("<span>活动图片不能为空</span>");
				}else{
					$(this).next('span').remove();
				}
			}
		});

		if($("dl:has(span)").html()){
			return false;
		}else{
			return true;
		}
	});
	
})
</script>