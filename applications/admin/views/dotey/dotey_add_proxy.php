<?php
$this->breadcrumbs = array('主播管理','新增主播代理');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 新增主播代理</h2>
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('dotey/addproxy',array('op'=>'addProxy'));?>" method="post" enctype="multipart/form-data">
				<fieldset>
				  <?php if (isset($info['uid'])){?>
				  <!-- 编辑时出现 -->
				  <div class="control-group">
					<label class="control-label" for="focusedInput">账号名/昵称</label>
					<div class="controls">
						<?php echo $info['username']?>/<?php echo $info['nickname']?>
					</div>
				  </div>
				  <?php }else{?>
				   <!-- 新增时出现 -->
				  <div class="control-group">
					<label class="control-label">主播用户名/UID</label>
					<div class="controls">
					  <input class="input-small focused" id="indexForm_dotey_name" name="dotey_info" type="text" value="">
					  <input class="btn" type="button" value="验证" name="valid_dotey_info" id="valid_dotey_info">
					  <span class="label label-important"  id="valid_dotey_info_noty" style="margin-left:10px; display:none;" isSubmit="1"></span>
					</div>
				  </div>
				  <!-- 用户结果集合 -->
				  <div id="dotey_info_uids" style="display:none;"> </div>
				  <?php }?>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">代理机构名</label>
					<div class="controls">
						<?php echo CHtml::textField('form[agency]',isset($info['agency'])?$info['agency']:'',array('class'=>'input-small focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_agency">代理机构名不能为空</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">代理人姓名</label>
					<div class="controls">
						<?php echo CHtml::textField('form[realname]',isset($info['realname'])?$info['realname']:'',array('class'=>'input-small focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_realname">代理人姓名不能为空</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">前台是否显示</label>
					<div class="controls">
						<?php echo Chtml::listBox('form[is_display]', isset($info['is_display'])?$info['is_display']:'',array(0=>'隐藏','显示'), array('size'=>1,'class'=>'input-small','empty'=>'-请选择-'))?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_is_display">请选择在前台是否显示</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">是否允许查询</label>
					<div class="controls">
						<?php echo Chtml::listBox('form[query_allow]', isset($info['query_allow'])?$info['query_allow']:'',array(0=>'禁止','允许'), array('size'=>1,'class'=>'input-small','empty'=>'-请选择-'))?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_is_display">是否允许查询不能为空</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">常用QQ</label>
					<div class="controls">
						<?php echo CHtml::textField('form[qq]',isset($info['qq'])?$info['qq']:'',array('class'=>'input-small focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_qq">常用QQ不能为空</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">手机联系</label>
					<div class="controls">
						<?php echo CHtml::textField('form[mobile]',isset($info['mobile'])?$info['mobile']:'',array('class'=>'input-small focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_mobile">手机联系不能为空</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">开户银行</label>
					<div class="controls">
						<?php echo CHtml::textField('form[bank]',isset($info['bank'])?$info['bank']:'',array('class'=>'input-small focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_bank">开户银行不能为空</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">银行账号</label>
					<div class="controls">
						<?php echo CHtml::textField('form[bank_account]',isset($info['bank_account'])?$info['bank_account']:'',array('class'=>'input-small focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_account">银行账号不能为空</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">身份证号</label>
					<div class="controls">
						<?php echo CHtml::textField('form[id_card]',isset($info['id_card'])?$info['id_card']:'',array('class'=>'input-small focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_id_card">身份证号不能为空</span>
					</div>
				  </div>
				  
				  			  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">身份证复印件</label>
					<div class="controls">
						<?php echo CHtml::fileField('id_card_pic',isset($info['id_card_pic'])?$info['id_card_pic']:'',array('class'=>'input-small focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_id_card_pic">身份证复印件必传</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">代理公司名称</label>
					<div class="controls">
						<?php echo CHtml::textField('form[company]',isset($info['company'])?$info['company']:'',array('class'=>'input-small focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_company">代理公司名称不能为空</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">公司营业执照复印件</label>
					<div class="controls">
						<?php echo CHtml::fileField('business_license',isset($info['business_license'])?$info['business_license']:'',array('class'=>'input-small focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_business_license">公司营业执照复印件必传</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">备注记录</label>
					<div class="controls">
						<?php echo CHtml::textArea('form[note]',isset($info['note'])?$info['note']:'');?>
						<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_note">备注记录不能为空</span>
					</div>
				  </div>
				  
				  <div class="form-actions">
				  	<?php if (isset($info['uid'])){?>
				  	<?php echo CHtml::hiddenField('form[uid]',$info['uid']);?>
				  	<?php }?>
				  	<?php echo CHtml::hiddenField('form[type]',DOTEY_MANAGER_PROXY);?>
					<button type="submit" class="btn btn-primary" id="submit_award" value="提交">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script>
$(function(){
	//表单提交前的动作
	$('#submit_award').click(function(e){
		var isSubmit = true;
		var fnum = 0;

		<?php if (!isset($info['uid'])){?>
		if($('#dotey_info_uids').children('.control-group').length == 0){
			$('#valid_dotey_info_noty').html('主播代理用户不能为空').show();
			return false;
		}else{
			$('#valid_dotey_info_noty').html('').hide();
		}
		<?php }?>
		
		$(':input').each(function(id){
			if(id>1){
				if(!$(this).attr('value') 
						&& $(this).attr('id') != 'business_license' 
						&& $(this).attr('id') != 'id_card_pic' 
						&& $(this).attr('id') != 'form_bank' 
						&& $(this).attr('id') != 'form_bank_account' 
						&& $(this).attr('id') != 'form_note' 
						&& $(this).attr('id') != 'form_id_card'){
					$(this).next().show();
					++fnum;
					isSubmit = false;
				}else{
					$(this).next().hide();
				}
			}
		});

		if(fnum >0){
			return false;
		}
	});
	
	//验证主播
	$("#valid_dotey_info").click(function(){
		var doteyName = $("#indexForm_dotey_name").attr('value');
		if(doteyName){
			$("#valid_dotey_info_noty").html("").hide();
			$.ajax({
				url:"<?php echo $this->createUrl("dotey/addproxy");?>",
				type:"post",
				dataType:"text",
				data:{"op":"checkDoteyInfo","doteyName":doteyName},
				success:function(msg){
					var data = msg.split('#xx#');
					if(data[0] == 1){
						var doteyUid 		= data[1];
						var doteyUsername 	= data[2];
						var doteyNickname 	= data[3];
						var doteyRealname 	= data[4];
						var isReturn = false;
						$("input[name='form[uid][]']").each(function(){
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
						html += '<input class="input-small focused" id="form_uid_'+(num+1)+'" name="form[uid]" type="text" value="'+doteyUid+'" readonly="readonly">';
						html += '<i class="icon-remove" style="margin-left:20px;" onclick="'+"$(this).parents('.control-group').detach()"+'"></i></div></div>';  
						$('#dotey_info_uids').html(html).show();
						$('#form_realname').attr('value',doteyRealname);
					}else{
						$("#valid_dotey_info_noty").html(msg).show();
					}
				}
			});
		}else{
			$("#valid_dotey_info_noty").html("请输入主播名称或主播ID").show();
		}
	});
})
</script>