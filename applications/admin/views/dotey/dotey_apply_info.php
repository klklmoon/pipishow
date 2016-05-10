<?php
$this->breadcrumbs = array('主播管理','主播申请');
$sources = $this->getProxyAndTutorListOption();
$types = $this->doteySer->getDoteyBaseStatus();
$whether = $this->doteySer->getWhetherDotey();
$genders = $this->doteySer->getDoteyGender();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 编辑主播申请</h2>
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('dotey/doteyapply',array('op'=>'editApplyInfo'));?>" method="post" enctype="multipart/form-data">
				<fieldset>
				<div class="control-group">
					<label class="control-label">用户名</label>
					<div class="controls">
					 	<?php 
							echo $userInfo['username'];
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label">姓名</label>
					<div class="controls">
					 	<?php 
							echo CHtml::textField('user[realname]',$userInfo['realname'] ,array('class'=>'input-small'));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label">手机</label>
					<div class="controls">
					 	<?php 
							echo CHtml::textField('ext[mobile]', $extInfo['mobile'],array('class'=>'input-small'));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label">QQ</label>
					<div class="controls">
					 	<?php 
							echo CHtml::textField('ext[qq]', $extInfo['qq'],array('class'=>'input-small'));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label">身份证号</label>
					<div class="controls">
					 	<?php 
							echo CHtml::textField('ext[id_card]', $extInfo['id_card'],array('class'=>'input-large'));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label">性别</label>
					<div class="controls">
					 	<?php 
							echo CHtml::listBox('ext[gender]', $extInfo['gender'],$genders,array('class'=>'input-small','size'=>1));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label">开户姓名</label>
					<div class="controls">
					 	<?php 
							echo CHtml::textField('ext[bank_user]', $extInfo['bank_user'],array('class'=>'input-large'));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label">开户银行</label>
					<div class="controls">
					 	<?php 
							echo CHtml::textField('ext[bank]', $extInfo['bank'],array('class'=>'input-large'));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label">银行卡号</label>
					<div class="controls">
					 	<?php 
							echo CHtml::textField('ext[bank_account]', $extInfo['bank_account'],array('class'=>'input-large'));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label">主播经验</label>
					<div class="controls">
					 	<?php 
					 		$v = ($info['has_experience'] == 1)?$whether[$info['has_experience']].'_'.$info['live_address']:'无';
							echo $v;
						?>
					</div>
				  </div>
				  
				  <div class="form-actions">
				  	<?php echo CHtml::hiddenField('uid',$userInfo['uid']);?>
					<button type="submit" class="btn btn-primary" id="submit_award">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>