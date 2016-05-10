<?php
$this->breadcrumbs = array('用户管理','编辑主播的基本信息');
$archivesSer = new ArchivesService();
$cats = $archivesSer->getAllArchiveCat();
$archivesCat = array();
if($cats){
	foreach($cats as $cat){
		$archivesCat[$cat['cat_id']] = $cat['name']; 
	}
}
$doteyStatus = $this->userSer->getUserStatus();
$doteyStatus[USER_STATUS_OFF] = '停播';
$doteyStatus[USER_STATUS_ON] = '开播';

?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 编辑主播的基本信息</h2>
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('dotey/editdotey',array('op'=>'editDoteyBase'));?>" method="post" enctype="multipart/form-data">
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
					<label class="control-label" for="focusedInput">真实姓名</label>
					<div class="controls">
						<?php echo CHtml::textField('user[realname]',$uinfo['realname'],array('class'=>'input-large focused'));?>
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
				  <div id="archives_cates_list" style="border:1px solid red;padding:1px;color:red;margin-bottom:5px;">
				  		<div class="span12">
				  			<span title="提示" class="icon icon-color icon-pin"></span>
				  			只能勾选用户类型为主播时填写下面的信息才会生效
				  		</div>
				  		<div class="control-group">
							<label class="control-label" for="focusedInput">直播间标题</label>
							<div class="controls">
								<?php echo CHtml::textField('archives[title]',$archivesInfo['title'],array('class'=>'input-large focused'));?>
							</div>
						  </div>
						
						<div class="control-group">
							<label class="control-label" for="focusedInput">档期分类</label>
							<div class="controls">
								<?php echo CHtml::hiddenField('archives[cat_id]',$catInfo['cat_id']);?>
								<?php
									//批量创建档期
									echo CHtml::textField('cat_name',$catInfo['name'],array('readonly'=>true,'class'=>'input-small'));
								?>
							</div>
					  	</div>
					  	
					  	<div class="control-group">
							<label class="control-label" for="focusedInput">是否显示</label>
							<div class="controls">
								<?php 
									$select = isset($archivesInfo['is_hide'])?$archivesInfo['is_hide']:'';
									echo CHtml::listBox('archives[is_hide]', $select, $archivesSer->getArchivesIsHide(),array('size'=>1,'class'=>'input-small'));
								?>
							</div>
						  </div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">主播状态</label>
					<div class="controls">
						<?php 
							echo CHtml::hiddenField('old_user_status', $uinfo['user_status']);
							echo CHtml::listBox('user[user_status]', $uinfo['user_status'], $doteyStatus,array('size'=>1,'empty'=>'-请选择-','class'=>'input-small'));
						?>
					</div>
				  </div>
				   <div class="control-group" id="op_desc" style="display:none">
				   	<label class="control-label" for="focusedInput" id="op_desc_title"></label>
					<div class="controls">
						<?php 
							echo CHtml::textField('oped[op_desc]','',array('class'=>'input-large focused'));
						?>
					</div>
				   </div>
				   <div class="control-group">
					<label class="control-label" for="focusedInput">签约平台</label>
					<div class="controls">
						<?php 
							echo CHtml::checkBoxList('dotey[sign_type]', $this->doteySer->checkSignType($uinfo['sign_type']), $this->doteySer->getDoteySignType(),array('size'=>1,'class'=>'input-small','separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;','labelOptions'=>array('class'=>'checkbox inline')));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">主播来源</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('dotey[dotey_type]', $uinfo['dotey_type'], $this->doteySer->getDoteyType(),array('size'=>1,'class'=>'input-small'));
						?>
					</div>
				  </div>
				  
				  <div class="control-group" id="doteyTypeTutor">
					<label class="control-label" for="focusedInput">导师</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('dotey[tutor_uid]', DOTEY_MANAGER_TUTOR.'#XX#'.$uinfo['tutor_uid'], $this->getProxyAndTutorListOption(false,DOTEY_MANAGER_TUTOR),array('size'=>1,'class'=>'input-small','empty'=>'-选择导师-'));
						?>
					</div>
				  </div>
				  
				  <div class="control-group" id="doteyTypeProxy">
					<label class="control-label" for="focusedInput">代理</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('dotey[proxy_uid]', DOTEY_MANAGER_PROXY.'#XX#'.$uinfo['proxy_uid'], $this->getProxyAndTutorListOption(false,DOTEY_MANAGER_PROXY),array('size'=>1,'class'=>'input-small','empty'=>' '));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">明星图</label>
					<div class="controls">
						<?php 
							echo CHtml::fileField('dotey_display_big','');
						?></br>
						<img width="40%" height="40%" src="<?php echo $this->doteySer->getShowAdminDoteyUpload($uinfo['uid'], 'big','display').'?r='.time();?>" />
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">封面图</label>
					<div class="controls">
						<?php 
							echo CHtml::fileField('dotey_display_small','');
						?></br>
						<img width="40%" height="40%" src="<?php echo $this->doteySer->getShowAdminDoteyUpload($uinfo['uid'], 'small','display').'?r='.time();?>" />
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">身份证</label>
					<div class="controls">
						<?php echo CHtml::textField('userextend[id_card]',$uinfo['id_card'],array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">开户银行</label>
					<div class="controls">
						<?php echo CHtml::textField('userextend[bank]',$uinfo['bank'],array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">银行账户</label>
					<div class="controls">
						<?php echo CHtml::textField('userextend[bank_account]',$uinfo['bank_account'],array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">生日</label>
					<div class="controls">
						<?php echo CHtml::textField('userextend[birthday]',date('Y-m-d',$uinfo['birthday']),array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">所在地省</label>
					<div class="controls">
			  			<select id="userextend_province" name="userextend[province]" class='input-small'> </select>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">所在地市</label>
					<div class="controls">
			  			<select id="userextend_city" name="userextend[city]" class="input-small"></select>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">手机</label>
					<div class="controls">
						<?php echo CHtml::textField('userextend[mobile]',$uinfo['mobile'],array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">QQ</label>
					<div class="controls">
						<?php echo CHtml::textField('userextend[qq]',$uinfo['qq'],array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  
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
var DTP = "<?php echo DOTEY_TYPE_PROXY?>";
var DTD = "<?php echo DOTEY_TYPE_DIRECT?>";
function changeDoteyType(dotey_type){
	if(dotey_type == DTP){
		$('#doteyTypeTutor').hide();
		$('#doteyTypeProxy').show();
		$('#dotey_tutor_uid option:selected').attr('selected',false);
	}else if(dotey_type == DTD){
		$('#doteyTypeTutor').show();
		$('#doteyTypeProxy').hide();
		$('#dotey_proxy_uid option:selected').attr('selected',false);
	}else{
		$('#doteyTypeTutor').hide();
		$('#doteyTypeProxy').hide();
		$('#dotey_tutor_uid option:selected').attr('selected',false);
		$('#dotey_proxy_uid option:selected').attr('selected',false);
	}
}

//城市初始化
init("userextend_province",'<?php echo isset($uinfo['province'])?$uinfo['province']:'选择省份'?>',"userextend_city",'<?php echo isset($uinfo['city'])?$uinfo['city']:'选择城市'?>');

$('#userextend_birthday').click(function(){
	WdatePicker();
});

$(function() {
	//初始化用户类型
	$('input[name="user[user_type][]"]').each(function(){
		var utype = $(this).attr('value');
		var checked = $(this).attr('checked');
		if(utype == UTD){
			if(checked){
				//$('#archives_cates_list').show();
			}else{
				//$('#archives_cates_list').hide();
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
				url:'<?php echo $this->createUrl('dotey/EditDotey',array('op'=>'checkSignFamily'));?>',
				dataType:'html',
				type:'post',
				data:{'uid':UID},
				success:function(msg){
					if(msg == 1){
						var isCheck = utype == UTD?true:false;
						$(obj).attr('checked',isCheck);
						alert('是家族主播，现在不能变更主播身份');
					}
				}
			});
		}
	});
	//初始化主播类型
	var dotey_type = $('#dotey_dotey_type option:selected').val();
	changeDoteyType(dotey_type);

	$('#dotey_dotey_type').change(function(){
		var dt = $(this).attr('value');
		if(dt){
			changeDoteyType(dt);
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
				if($('#user_user_type_'+i).attr('value') == UTD){
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
			alert('开播状态不能为空，请选择');
			return false;
		}

		var ostatus = $('#old_user_status').attr('value');
		var nstatus = $('#user_user_status').attr('value');
		if(ostatus != nstatus){
			if(!$('#oped_op_desc').attr('value')){
				alert('切换开播状态的操作理由不能为空');
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
				$('#op_desc_title').html('禁播理由');
				$('#op_desc').show();
			}else if(svalue == <?php echo USER_STATUS_ON;?>){
				$('#op_desc').hide();
				$('#op_desc_title').html('开播理由');
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
				url:"<?php echo $this->createUrl('dotey/editdotey');?>",
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