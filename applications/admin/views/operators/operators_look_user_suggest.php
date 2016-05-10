<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
				<fieldset>
				<?php foreach($list as $v){?>
				<div class="control-group">
					<label class="control-label" for="focusedInput">用户</label>
					<div class="controls">
						<?php echo $uinfo[$v['uid']]['username'];?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="focusedInput">昵称</label>
					<div class="controls">
						<?php echo $uinfo[$v['uid']]['nickname'];?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="focusedInput">联系方式</label>
					<div class="controls">
						<?php echo $v['contact'];?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="focusedInput">内容</label>
					<div class="controls">
						<?php echo $v['content'];?>
					</div>
				</div>
				<?php if($v['attach']){?>
				<div class="control-group">
					<label class="control-label" for="focusedInput">附件</label>
					<div class="controls">
						<img src="<?php echo $operateSer->getSuggestAttach($v['attach']);?>" />
					</div>
				</div>
				<?php }?>
				<?php }?>
				  
				</fieldset>
			</form>
		</div>
	</div>
		
</div>