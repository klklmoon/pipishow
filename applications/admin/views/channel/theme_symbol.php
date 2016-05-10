<?php
$this->breadcrumbs = array('频道管理','标志管理');
?>
<style type="text/css">
	dl{clear:left;width:auto;margin:0px;}
	dt,dd{float:left;width:160px;margin-left:5px;padding:2px 2px;}
</style>
<!-- 搜索 -->
<div class="row-fluid sortable ui-sortable">
	<div class="box" style="margin:0px;">
		<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('channel/symbolManage',array('op'=>'updateSymbol'));?>" method="post" enctype="multipart/form-data">
			<fieldset>
			<div class="box" style="margin:0px;">
				<div class="box-header well">
					<h2 style="font-size:10px;color:#000;">唱区标志</h2>
					<div class="box-icon">
						<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
					</div>
				</div>
				<div class="box-content" style="overflow-y: auto;max-height:300px;" id="addToTransScale">
					  	<div class="control-group">
					  	<dl style="margin-top:10px;">
							<dt>英文名称</dt>
							<dt>描述</dt>
							<dt>图标</dt>
						</dl>
						<dl class="effect"> </dl>
							<dl class="effect">
								<dd>
									<?php echo CHtml::textField('sing_area[flag]',$info['sing_area']['flag'],array('class'=>'input-small','readonly'=>true));?>
								</dd>
								<dd>
									<?php echo CHtml::textField('sing_area[desc]',$info['sing_area']['desc'],array('class'=>'input-small'));?>
								</dd>
								<dd>
									<img src="<?php echo  $adminUrl.$info['sing_area']['pic']; ?>" />
									<?php echo CHtml::fileField('sing_area[pic]',$info['sing_area']['pic'],array('class'=>'input-small'));?>
								</dd>
							</dl>
					  </div>
				</div>
			</div>
			<div class="box" style="margin:0px;">
				<div class="box-header well">
					<h2 style="font-size:10px;color:#000;">唱将标志</h2>
					<div class="box-icon">
						<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
					</div>
				</div>
				<div class="box-content" style="overflow-y: auto;max-height:300px;" id="addToEffectDay">
					<div class="control-group">
					  	<dl style="margin-top:10px;">
							<dt>英文名称</dt>
							<dt>描述</dt>
							<dt>图标</dt>
						</dl>
						<dl class="effect"> </dl>
							<dl class="effect">
								<dd>
									<?php echo CHtml::textField('sing_general[flag]',$info['sing_general']['flag'],array('class'=>'input-small','readonly'=>true));?>
								</dd>
								<dd>
									<?php echo CHtml::textField('sing_general[desc]',$info['sing_general']['desc'],array('class'=>'input-small'));?>
								</dd>
								<dd>
									<img src="<?php echo  $adminUrl.$info['sing_general']['pic']; ?>" />
									<?php echo CHtml::fileField('sing_general[pic]',$info['sing_general']['pic'],array('class'=>'input-small'));?>
								</dd>
							</dl>
					  </div>
				</div>
			</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary">提交</button>
			</div>
			</fieldset>
		</form>
	</div>
</div>