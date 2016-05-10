<?php 
	$this->breadcrumbs = array(
		'添加礼物分类'
	);
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 添加礼物分类</h2>
		</div>
		<?php }?>
		<div class="box-content">
			<?php if(isset($notices)){?>
			<div class="alert alert-block">
			<?php foreach($notices as $notice){?>
				<p><?php echo $notice[0];?></p>
			<?php }?>
			</div>
			<?php }?>
			<form class="form-horizontal" action="<?php echo $this->createUrl('gift/addgiftcat');?>" method="post">
				<fieldset>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">分类名称</label>
					<div class="controls">
					  <input class="input-large focused" id="giftcat[cat_name]" name="giftcat[cat_name]" type="text" value="<?php echo isset($cinfo['cat_name'])?$cinfo['cat_name']:'';?>">
					  <span class="label label-important" style="margin-left:10px;">填写中文名称</span>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">分类标识</label>
					<div class="controls">
					  <input class="input-large focused" id="giftcat[cat_enname]" name="giftcat[cat_enname]" type="text" value="<?php echo isset($cinfo['cat_enname'])?$cinfo['cat_enname']:'';?>">
						<span class="label label-important" style="margin-left:10px;">填写英文名称</span>
					</div>
				  </div>
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				  <?php if(isset($cinfo['category_id'])){?>
				  <input id="giftcat[category_id]" name="giftcat[category_id]" type="hidden" value="<?php echo $cinfo['category_id'];?>">
				  <?php }?>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>