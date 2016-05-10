<?php
$this->breadcrumbs = array('用户管理','编辑用户基本信息');
$archivesSer = new ArchivesService();
$cats = $archivesSer->getAllArchiveCat();
$archivesCat = array();
if($cats){
	foreach($cats as $cat){
		$archivesCat[$cat['cat_id']] = $cat['name']; 
	}
}

/* $consumeService = new ConsumeService();
$uranks = $this->formatUserRank();
$consumeInfo = $consumeService->getConsumesByUids($uinfo['uid']);
$consumeInfo = $consumeInfo[$uinfo['uid']]; */
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 编辑用户基本信息</h2>
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('user/uinfoedit');?>" method="post">
				<fieldset>
					<div class="control-group">
					<label class="control-label" for="focusedInput">用户图像</label>
					<div class="controls">
						<?php 
							$avatar_use = $this->userSer->getUserAvatar($uinfo['uid'],'small');
						?>
						<img src="<?php echo $avatar_use;?>"/>
						<a class="btn" id="remove_user_avatar" uid="<?php echo $uinfo['uid'];?>"><i class='icon-remove'></i>清除图像</a>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">用户名称</label>
					<div class="controls">
					  <?php echo CHtml::textField('username',$uinfo['username'],array('class'=>'input-large focused','readonly'=>'readonly'));?>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">昵称</label>
					<div class="controls">
						<?php echo CHtml::textField('user[nickname]',$uinfo['nickname'],array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">用户类型</label>
					<div class="controls">
						<?php 
							echo CHtml::checkBoxList(
								'user[user_type]', 
								$this->userSer->checkUserType($uinfo['user_type']),
								$this->userSer->getUserBaseType(),
								array(
									'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
									'labelOptions'=>array('class'=>'checkbox inline'),
								));
						?>
					</div>
				  </div>
				  
				  <div class="control-group" id="archives_cates_list" style="display:<?php if($uinfo['user_type'] == USER_TYPE_DOTEY){?>block;<?php }else{?>none;<?php }?>">
					<label class="control-label" for="focusedInput">档期分类</label>
					<div class="controls">
						<?php 
							//单选
							//echo CHtml::listBox('archives[cat_id]',$cat_ids, $archivesCat,array('size'=>1,'empty'=>'-请选择-','class'=>'input-small'));
							//批量创建档期
							echo CHtml::listBox('archives[cat_id]',$cat_ids, $archivesCat,array('size'=>1,'empty'=>'-请选择-','class'=>'input-large','size'=>4,'multiple'=>true));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">禁用状态</label>
					<div class="controls">
						<?php 
							echo CHtml::hiddenField('old_user_status', $uinfo['user_status']);
							echo CHtml::listBox('user[user_status]', $uinfo['user_status'], $this->userSer->getUserStatus(),array('size'=>1,'empty'=>'-请选择-','class'=>'input-small'));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">广播状态</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('broadcast_status', $broadcastStatus, array(1=>'禁止',0=>'正常'),array('size'=>1,'empty'=>'-请选择-','class'=>'input-small'));
						?>
					</div>
				  </div>
				  
				  <!-- 
				  <div class="control-group">
					<label class="control-label" for="focusedInput">用户等级</label>
					<div class="controls">
						<?php 
							//echo CHtml::listBox('consume[rank]', $consumeInfo['rank'], $uranks,array('size'=>1,'empty'=>' ','class'=>'input-small'));
						?>
					</div>
				  </div>
				   -->
				  
				   <div class="control-group" id="op_desc" style="display:none">
				   	<label class="control-label" for="focusedInput" id="op_desc_title"></label>
					<div class="controls">
						<?php 
							echo CHtml::textField('oped[op_desc]','',array('class'=>'input-large focused'));
						?>
					</div>
				   </div>
				  <?php echo CHtml::hiddenField('user[uid]',$uinfo['uid']);?>
				  <?php echo CHtml::hiddenField('op','updateUinfo');?>
				  <?php echo CHtml::hiddenField('uid',$uinfo['uid']);?>
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script style="text/javascript">
var UID = "<?php echo $uinfo['uid'];?>";	
var UTD = "<?php echo USER_TYPE_DOTEY?>"
var UTAD = "<?php echo USER_TYPE_ADMIN?>"
$(function() {
	//初始化
	$('input[name="user[user_type][]"]').each(function(){
		var utype = $(this).attr('value');
		var checked = $(this).attr('checked');
		if(utype == <?php echo USER_TYPE_DOTEY;?>){
			if(checked){
				$('#archives_cates_list').show();
			}else{
				$('#archives_cates_list').hide();
			}
		}
	});
	//点击档期类型
	$('input[name="user[user_type][]"]').click(function(){
		var utype = $(this).attr('value');
		var checked = $(this).attr('checked');
		var obj = this;
		if(utype == UTD || utype == UTAD){
			$.ajax({
				url:'<?php echo $this->createUrl('user/UInfoEdit',array('op'=>'checkSignFamily'));?>',
				dataType:'html',
				type:'post',
				data:{'uid':UID},
				success:function(msg){
					if(msg == 1){
						var isCheck = utype == UTD?true:false;
						$(obj).attr('checked',isCheck);
						alert('是家族主播，现在不能变更主播身份');
					}else{
						if(checked){
							$('#archives_cates_list').show();
						}else{
							$('#archives_cates_list').hide();
						}
					}
				}
			});
		}
	});
	
	$(':submit').click(function(){
		var isSubmit = true;

		if(!$('#user_nickname').attr('value')){
			alert('昵称不能为空');
			return false;
		}

		if($('input[name="user[user_type][]"]:checked').length == 0){
			alert('主播类型不能为空');
			return false;
		}
		
		$('input[name="user[user_type][]"]').each(function(i){
			var ischecked = $(this).attr('checked');
			if(ischecked){
				if($('#user_user_type_'+i).attr('value') == "<?php echo USER_TYPE_DOTEY;?>"){
					if(!$('#archives_cat_id').attr('value')){
						isSubmit = false;
					}
				}
			}
		});

		if(!isSubmit){
			alert('档期分类不能为空');
			return false;
		}

		if(!$('#user_user_status').attr('value')){
			alert('用户状态不能为空，请选择');
			return false;
		}

		var ostatus = $('#old_user_status').attr('value');
		var nstatus = $('#user_user_status').attr('value');
		if(ostatus != nstatus){
			if(!$('#oped_op_desc').attr('value')){
				alert('切换状态的操作理由不能为空');
				return false;
			}
		}
		
		return true;
	});

	$('#user_user_status').change(function(){
		var svalue = $(this).attr('value');
		var ostatus = $('#old_user_status').attr('value');
		if(ostatus != svalue && svalue.length>0){
			if(svalue == <?php echo USER_STATUS_OFF;?> ){
				$('#op_desc').hide();
				$('#op_desc_title').html('禁用理由');
				$('#op_desc').show();
			}else if(svalue == <?php echo USER_STATUS_ON;?>){
				$('#op_desc').hide();
				$('#op_desc_title').html('启用理由');
				$('#op_desc').show();
			}else{
				$('#op_op_desc').attr('value','');
				$('#op_desc').hide();
			}
		}else{
			$('#op_op_desc').attr('value','');
			$('#op_desc').hide();
		}
	});
	//清除图像 
	$('#remove_user_avatar').click(function(){
		var uid = $(this).attr('uid');
		var obj = this;
		if(uid){
			$.ajax({
				url:"<?php echo $this->createUrl('user/uinfoedit');?>",
				type:'post',
				dataType:'text',
				data:{'uid':uid,'op':'removeUserAvatar'},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('.control-group').detach();
					}else if(msg == 2){
						alert('默认图像不能删除');
					}else{
						alert('删除失败');
					}
				}
			});
		}
	});
});
</script>