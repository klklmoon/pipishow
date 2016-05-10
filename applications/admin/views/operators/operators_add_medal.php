<?php
$this->breadcrumbs = array('运营工具','勋章管理');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 勋章管理</h2>
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('operators/addmedal',array('op'=>'addMedal'));?>" method="post" enctype="multipart/form-data">
				<fieldset>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">名称</label>
					<div class="controls">
					  <input class="input-small focused" id="medal[name]" name="medal[name]" type="text" value="<?php echo isset($cinfo['name'])?$cinfo['name']:'';?>">
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">勋章类型</label>
					<div class="controls">
						<?php 
							$select2 = isset($cinfo['type'])?$cinfo['type']:'';
							echo CHtml::listBox('medal[type]', $select2, $userMedal->getMedalType(),array('class'=>'input-small','size'=>1));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">展示图标</label>
					<div class="controls">
						<?php if(isset($cinfo['icon'])){?>
						<img alt="" src="<?php echo $userMedal->getMedalIcon($cinfo['icon']);?>">
						<?php }?>
					  <input class="input-small focused" id="medal[icon]" name="medal[icon]" type="file" value="">
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">描述</label>
					<div class="controls">
						<textarea id="medal_desc" rows="3" name="medal[desc]"><?php echo isset($cinfo['desc'])?$cinfo['desc']:'';?></textarea>
					</div>
				  </div>
				  <?php if(isset($cinfo['mid'])){?>
				  <input id="medal_mid" name="medal[mid]" type="hidden" value="<?php echo $cinfo['mid'];?>">
				  <?php }?>
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>