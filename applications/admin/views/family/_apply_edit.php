<?php
$this->breadcrumbs = array('家族管理','编辑家族');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 编辑家族的基本信息</h2>
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('family/apply',array('op'=>'editApply'));?>" method="post" enctype="multipart/form-data">
				<fieldset>
					<div class="control-group">
					<label class="control-label" for="focusedInput">用户名</label>
					<div class="controls">
						<span style="cursor:pointer;" class="label label-warning">
				  		<?php echo $uinfo['username'];?></span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">家族总人数</label>
					<div class="controls">
						<span style="cursor:pointer;" class="label label-warning">
				  		<?php echo $info['member_total'];?></span>
					</div>
				  </div>
				  
				 <div class="control-group">
					<label class="control-label" for="focusedInput">昵称</label>
					<div class="controls">
						<?php echo CHtml::textField('user[nickname]',$uinfo['nickname'],array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">QQ</label>
					<div class="controls">
						<?php echo CHtml::textField('euser[qq]',isset($UEInfo['qq'])?$UEInfo['qq']:'',array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">手机</label>
					<div class="controls">
						<?php echo CHtml::textField('euser[mobile]',isset($UEInfo['mobile'])?$UEInfo['mobile']:'',array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  
					<div class="control-group">
					  <label class="control-label" for="GiftForm[image]">家族封面图</label>
					  <div class="controls">
					  	<?php 
							$cover = 'http://showadmin'.DOMAIN.'/images/'.$this->famService->getFamilyUploadPath().DIR_SEP.$info['cover'];
						?>
						<img src="<?php echo $cover;?>"/>
						<?php echo CHtml::fileField('cover','',array('class'=>'input-small focused','size'=>19));?>
					  </div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">家族名称</label>
					<div class="controls">
					  <?php echo CHtml::textField('apply[name]',$info['name'],array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">徽章名称</label>
					<div class="controls">
					  <?php echo CHtml::textField('apply[medal]',$info['medal'],array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">家族公告</label>
					<div class="controls">
					  <?php echo CHtml::textArea('eapply[announcement]',isset($einfo['announcement'])?$einfo['announcement']:'');?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">家族长提成比例</label>
					<div class="controls">
					  <?php echo CHtml::textField('eapply[scale]',isset($einfo['config']['scale'])?$einfo['config']['scale']:'0.1');?>
					</div>
				  </div>
				  
				  <?php echo CHtml::hiddenField('familyId',$info['id']);?>
				  <?php echo CHtml::hiddenField('isEdit',true);?>
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script style="text/javascript">
$(function() {
	$(':submit').click(function(){
		var isSubmit = true;

		if(!$('#user_nickname').attr('value')){
			alert('昵称不能为空');
			return false;
		}

		if(!$('#apply_name').attr('value')){
			alert('家族名称不能为空');
			return false;
		}

		if(!$('#apply_medal').attr('value')){
			alert('家族徽章不能为空');
			return false;
		}

		return true;
	});
});
</script>