<?php
$this->breadcrumbs = array('运营工具','添加客服');
$kefuType = $operateSer->getKefuType();
$contactType = $operateSer->getKefuContactType();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!$this->isAjax){?>
		<div data-original-title="" class="box-header well">
			<h2><i class="icon-edit"></i> 添加客服</h2>
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
			
			<form class="form-horizontal" id="purview_add" action="<?php echo $this->createUrl('operators/addkefu',array('op'=>'addKefu'));?>" method="post">		
				<fieldset>
					<div class="control-group">
						<label class="control-label">客服类型</label>				
						<div class="controls">
							<?php $select1 = isset($info['kefu_type'])?$info['kefu_type']:'';?>
							<?php echo CHtml::listBox('kefu[kefu_type]', $select1, $kefuType,array('size'=>1,'class'=>'input-small','empty'=>' '));?>
						</div>
				  	</div> 
				  	<div class="control-group">
						<label class="control-label">联系方式</label>				
						<div class="controls">
							<?php $select2 = isset($info['contact_type'])?$info['contact_type']:'';?>
							<?php echo CHtml::listBox('kefu[contact_type]', $select2, $contactType,array('size'=>1,'class'=>'input-small','empty'=>' '));?>
						</div>
				  	</div>
					
					<div class="control-group">
						<label class="control-label">联系人</label>				
						<div class="controls">
							<?php 
								$v1 = isset($info['contact_name'])?$info['contact_name']:'';
								echo CHtml::textField('kefu[contact_name]',$v1,array('class'=>'input-large'));
							?>
						</div>
				  	</div> 
				  	
				  	<div class="control-group">
						<label class="control-label">账号</label>				
						<div class="controls">
							<?php 
								$v2 = isset($info['contact_account'])?$info['contact_account']:'';
								echo CHtml::textField('kefu[contact_account]',$v2,array('class'=>'input-large'));
							?>
						</div>
				  	</div>
				  	
					<div class="form-actions">
						<input class="btn btn-primary" value="提交" type="submit" name="yt1" id="sub_submit_channel">
						<?php 
						if(isset($info['id'])){
							echo CHtml::hiddenField('kefu[id]',$info['id']);							
						}
						?>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</div>

