<?php
$this->breadcrumbs = array('礼物之星','规则说明');
?>
<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:140px;margin-left:10px;padding:3px 5px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!isset($isAjax) || !$isAjax){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>规则说明</h2>
		</div>
		<?php }?>
		<div class="box-content">
					<?php if(isset($notices) && count($notices)>0){?>
			<div class="alert alert-block" style="margin-left:60px;margin-right:200px;clear:both;">
				<button type="button" class="close" data-dismiss="alert">×</button>
			<?php foreach($notices as $notice){?>
				<p><?php echo isset($notice[0])?$notice[0]:$notice;?></p>
			<?php }?>
			</div>
			<?php }?>
			<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('activities/giftstarset');?>" enctype="multipart/form-data">
				<fieldset>
					<div class="control-group">
						<label class="control-label" for="SetForm[week_id]">周编号</label>
						<div class="controls">
						<?php echo $updateSetInfo['week_id'];?>
						</div>
					</div>
				  
				<div class="control-group">
					<label class="control-label" for="SetForm[monday_date]">周一日期</label>
					<div class="controls">
					<?php echo $updateSetInfo['monday_date'];?>
					</div>
				  </div>		
				  
					<div class="control-group">
						<label class="control-label">特别说明</label>			
						<div class="controls">
							<?php 
								$v2 = isset($updateSetInfo['illustration'])?$updateSetInfo['illustration']:'';
								echo CHtml::textArea('SetForm[illustration]',$v2,array('class'=>'cleditor'));
							?>
						</div>
					</div>
				  
				   <?php if(isset($updateSetInfo['set_id'])){?>
				  <input name="SetForm[set_id]" type="hidden" id="SetForm[set_id]" value="<?php echo $updateSetInfo['set_id'];?>">
				  <?php }?>
				  
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
    CKEDITOR.replace('SetForm[illustration]',{
		baseHref:'',
		width:'80%',
		height:300 ,
	});
</script>