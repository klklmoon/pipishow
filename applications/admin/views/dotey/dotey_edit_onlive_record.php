<?php
$this->breadcrumbs = array('用户管理','编辑主播的基本信息');
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('dotey/onlive',array('op'=>'editLiveRecords'));?>" method="post">
				<fieldset>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">副标题</label>
					<div class="controls">
					  <?php echo CHtml::textField('live[sub_title]',$uinfo['sub_title'],array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">开播时间</label>
					<div class="controls">
						<?php echo CHtml::textField('live[start_time]',date('Y-m-d H:i:s',$uinfo['start_time']),array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  <?php echo CHtml::hiddenField('live[record_id]',$uinfo['record_id']);?>
				  <?php echo CHtml::hiddenField('op','editLiveRecords');?>
				  <?php echo CHtml::hiddenField('record_id',$uinfo['record_id']);?>
				  <?php echo CHtml::hiddenField('condition',$condition);?>
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script>
//注册结束时间
$( '#live_start_time' ).click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm:ss'});
	}
);
</script>