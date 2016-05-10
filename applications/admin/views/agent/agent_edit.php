<?php
$this->breadcrumbs = array('代理管理','编辑代理信息');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 编辑代理信息</h2>
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('agent/list', array('op'=>'editDo'));?>" method="post" enctype="multipart/form-data">
				<fieldset>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">账号名/昵称</label>
					<div class="controls">
						<?php echo $user['username']?>/<?php echo $agent['agent_nickname']?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">代理人姓名</label>
					<div class="controls">
						<?php echo CHtml::textField('form[agent_name]',$agent['agent_name'],array('class'=>'input-small focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_realname">代理人姓名不能为空</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">手机</label>
					<div class="controls">
						<?php echo CHtml::textField('form[agent_mobile]',$agent['agent_mobile'],array('class'=>'input-small focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_mobile">手机不能为空</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">QQ</label>
					<div class="controls">
						<?php echo CHtml::textField('form[agent_qq]',$agent['agent_qq'],array('class'=>'input-small focused'));?>
					  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_qq">QQ不能为空</span>
					</div>
				  </div>
				  
				  <div class="form-actions">
				  	<?php echo CHtml::hiddenField('form[uid]',$agent['uid']);?>
					<button type="submit" class="btn btn-primary" id="submit_award" value="提交">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>