	<div class="container-fluid">
	<div class="row-fluid">
	
		<div class="row-fluid">
			<div class="span12 center login-header">
				<h2><?php echo Yii::t('user','Welcome to PiPi');?></h2>
			</div><!--/span-->
		</div><!--/row-->
		
		<div class="row-fluid">
			<div class="well span5 center login-box">
				<div class="box-content alerts">
					<?php if (Yii::app()->user->getFlash("info")){?>
					<div class="alert alert-info">
						<button type="button" class="close" data-dismiss="alert">×</button>
						<?php echo Yii::app()->user->getFlash("info");?>
					</div>
					<?php }?>
					<?php if ($msg){?>
					<div class="alert alert-error">
						<button type="button" class="close" data-dismiss="alert">×</button>
						<?php echo $msg;?>
					</div>
					<?php }?>
					<div class="alert alert-info">
						<button type="button" class="close" data-dismiss="alert">×</button>
						<?php echo Yii::t('user','Please login with your Username and Password');?>
					</div>
				</div>
				
				<?php echo CHtml::beginForm($this->createUrl('user/login'),'post',array('name'=>'LoginForm','class'=>'form-horizontal','id'=>'LoginForm')) ?>
					<fieldset>
						<div class="input-prepend" title="Username" data-rel="tooltip">
							<span class="add-on"><i class="icon-user"></i></span>
							<?php echo CHtml::textField('login[username]','',array('class'=>'input-large span10'))?>
						</div>
						<div class="clearfix"></div>

						<div class="input-prepend" title="Password" data-rel="tooltip">
							<span class="add-on"><i class="icon-lock"></i></span>
							<?php echo CHtml::passwordField('login[password]','',array('class' => 'input-large span10'));?>
						</div>
						<div class="clearfix"></div>

						<div class="input-prepend">
							<label class="remember" for="remember"><input type="checkbox" id="remember" /><?php echo Yii::t('user','Remember me');?></label>
						</div>
						<div class="clearfix"></div>

						<p class="center span5">
							<?php echo CHtml::button('button',array('class'=>'btn btn-primary','value'=>Yii::t('user','Login'),'name'=>'button','id'=>'LoginSubmit'));?>
						</p>
					</fieldset>
				<?php echo Chtml::endForm(); ?>
			</div><!--/span-->
		</div><!--/row-->
		</div><!--/fluid-row-->
	
</div><!--/.fluid-container-->
<script style="text/javascript">
$(function() {
	$('#LoginSubmit').click(function(){
		$('#login_password').val($.md5($('#login_password').val()));
		$('#LoginForm').submit();
	})
});
</script>