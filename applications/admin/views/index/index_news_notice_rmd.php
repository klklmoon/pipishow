<?php
$this->breadcrumbs = array('首页管理','首页公告推荐');
?>
<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:140px;margin-left:10px;padding:3px 5px;}
</style>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!isset($this->isAjax) || !$this->isAjax){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>首页公告推荐</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" title="添加首页公告推荐"><i class="icon-plus-sign"></i></a>
			</div>
		</div>
		<?php }?>
		
		<div class="box-content">
			<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('index/newsnoticermd',array('op'=>'addNewsNoticeRmd'));?>" enctype="multipart/form-data">
				<fieldset>
					<?php if($info){?>
					<div class="control-group" id="show_addEffect" style="display:none;">
				      <label class="control-label"></label>
				      <div class="controls">
						  <a href="javascript:;" id="addEffect" targetId="<?php echo $info['thread_id'];?>" subject="<?php echo $info['title'];?>"></a>
				      </div>
				   </div>
					<?php }?>
				  
				  
				 	<!-- <dl style="border-top:1px #999999 dotted"></dl> -->
				  	首页公告推荐最多只能推荐5个
				  	<div class="control-group">
				  	<dl style="margin-top:10px;">
						<dt>文章标题</dt>
						<dt>排序</dt>
						<dt>链接地址</dt>
						<dt>文章ID</dt>
					</dl>
					<dl class="effect"> </dl>
					<?php if(isset($newsNoticeData)){?>
					<?php foreach($newsNoticeData as $k=>$data){?>
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
						<dt>
							<input name="indexForm[target_id][<?php echo $k?>]" type="text" id="indexForm_target_id_<?php echo $data['target_id']?>" class="input-small focused" size="10" value="<?php echo $data['target_id'];?>" readonly="readonly">
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
	var target_id = $("#addEffect").attr('targetId');
	var subject = $("#addEffect").attr('subject');
	var existsTargetId = "#indexForm_target_id_"+target_id;
	var exists = $(existsTargetId).attr('value');
	if(target_id && subject && exists != target_id ){
		var effectNum = $(".effect").length;

		if(effectNum>5){
			alert('最多只能添加5个');
		}else{
			var effectHtml = "<dl class='effect'><dd>≥";
			effectHtml += "<input name='indexForm[subject][]' type='text' id='indexForm[subject]["+effectNum+"]' class='input-small focused'  value='"+subject+"' readonly='readonly'/></dd>";
			effectHtml += "<dt><input name='indexForm[sort][]' type='text' id='indexForm[sort]["+effectNum+"]' class='input-small focused'  value=''/></dt>";
			effectHtml += "<dt><input name='indexForm[textlink][]' type='text' id='indexForm[textlink]["+effectNum+"]' class='input-small focused'  value=''/></dt>";
			effectHtml += "<dt><input name='indexForm[target_id][]' id='indexForm[target_id]["+effectNum+"]' type='text' class='input-small focused' value='"+target_id+"' readonly='readonly' /></dt>";
			effectHtml += "<dt><i class='icon-remove' onclick=\"$(this).parents('dl').detach()\"></i></dt></dl>";
			$(".effect:last").after(effectHtml);
			effectNum++;
		}
	}
	
	//删除首页厨窗
	$("dt .icon-remove").click(function(){
		var operateId = $(this).attr('operateId');
		var obj = this;
		if(operateId){
			$.ajax({
				type:'post',
				url:'<?php echo $this->createUrl('index/newsnoticermd');?>',
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
	//添加公告
	$(".icon-plus-sign").click(function(e){
		var effectNum = $(".effect").length;
		if(effectNum>5){
			alert('最多只能添加5个');
		}else{
			window.location.href = "<?php echo $this->createUrl("operators/newsnotice");?>";
		}
	});
})
</script>