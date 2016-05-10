<?php
$this->breadcrumbs = array('添加道具分类属性');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 添加道具分类属性</h2>
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('props/addcatattr');?>" method="post">
				<fieldset>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">属性分类</label>
					<div class="controls">
						<?php 
							$select1 = isset($cinfo['cat_id'])?$cinfo['cat_id']:'';
							echo CHtml::listBox('propscatattr[cat_id]', $select1, $this->getPropsCat(2),array('size'=>1,'class'=>'input-small','empty'=>'-请选择-'));
						?>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">属性名称</label>
					<div class="controls">
					  <input class="input-large focused" id="propscatattr[attr_name]" name="propscatattr[attr_name]" type="text" value="<?php echo isset($cinfo['attr_name'])?$cinfo['attr_name']:'';?>">
					  <span class="label label-important" style="margin-left:10px;">填写中文名称</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">属性英文标识</label>
					<div class="controls">
					  <input class="input-large focused" id="propscatattr[attr_enname]" name="propscatattr[attr_enname]" type="text" value="<?php echo isset($cinfo['attr_enname'])?$cinfo['attr_enname']:'';?>">
						<span class="label label-important" style="margin-left:10px;">填写英文名称</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">属性类型</label>
					<div class="controls">
						<?php 
							$select2 = isset($cinfo['attr_type'])?$cinfo['attr_type']:'';
							echo CHtml::radioButtonList('propscatattr[attr_type]', $select2, $this->getCatAttrTypes(),array('separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;','labelOptions'=>array('class'=>'checkbox inline')));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">是否多选</label>
					<div class="controls">
						<?php 
							$select3 = isset($cinfo['is_multi'])?$cinfo['is_multi']:'';
							echo CHtml::radioButtonList('propscatattr[is_multi]', $select3, array(0=>'否',1=>'是'),array('separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;','labelOptions'=>array('class'=>'checkbox inline')));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">是否显示</label>
					<div class="controls">
						<?php 
							$select4 = isset($cinfo['is_display'])?$cinfo['is_display']:'';
							echo CHtml::radioButtonList('propscatattr[is_display]', $select4, array(0=>'否',1=>'是'),array('separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;','labelOptions'=>array('class'=>'checkbox inline')));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">属性值</label>
					<div class="controls">
						<?php 
							$value = isset($cinfo['attr_value'])?$cinfo['attr_value']:'';
							echo Chtml::textArea('propscatattr[attr_value]',$value);
						?>
						<div class="alert alert-info" style="width:300px;margin-top:12px;">
							<button type="button" class="close" data-dismiss="alert">×</button>
							如果是列表类型的话，列表默认值分隔为</br>
							key=value&key1=value1&key2=value2
						</div>
						
					</div>
				  </div>
				  
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				  <?php if(isset($cinfo['attr_id'])){?>
				  <input id="propscatattr[attr_id]" name="propscatattr[attr_id]" type="hidden" value="<?php echo $cinfo['attr_id'];?>">
				  <?php }?>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>