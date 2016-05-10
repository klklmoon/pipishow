<?php
$this->breadcrumbs = array('添加道具分类');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 添加道具分类</h2>
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('props/addpropscat');?>" method="post">
				<fieldset>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">分类名称</label>
					<div class="controls">
					  <input class="input-large focused" id="propscat[name]" name="propscat[name]" type="text" value="<?php echo isset($cinfo['name'])?$cinfo['name']:'';?>">
					  <span class="label label-important" style="margin-left:10px;">填写中文名称</span>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">分类标识</label>
					<div class="controls">
					  <input class="input-large focused" id="propscat[en_name]" name="propscat[en_name]" type="text" value="<?php echo isset($cinfo['en_name'])?$cinfo['en_name']:'';?>">
						<span class="label label-important" style="margin-left:10px;">填写英文名称</span>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">是否显示</label>
					<div class="controls">
					  <select id="propscat[is_display]" name="propscat[is_display]">
					  	<?php foreach($isDisplay as $value => $option){?>
					  	<option value="<?php echo $value;?>" <?php if(isset($cinfo['is_display']) && $cinfo['is_display']== $value){?>selected<?php }?>><?php echo $option;?></option>
					  	<?php }?>
					  </select>
					</div>
				  </div>
				  
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				  <?php if(isset($cinfo['cat_id'])){?>
				  <input id="propscat[cat_id]" name="propscat[cat_id]" type="hidden" value="<?php echo $cinfo['cat_id'];?>">
				  <?php }?>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>
