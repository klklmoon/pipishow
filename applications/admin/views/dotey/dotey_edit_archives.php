<?php
$this->breadcrumbs = array('用户管理','编辑直播间信息');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 编辑直播间信息</h2>
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('dotey/editArchives',array('op'=>'editArchives'));?>" method="post">
				<fieldset>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">直播间名称</label>
					<div class="controls">
						<?php 
							echo CHtml::textField('archives[title]',$info['title'],array('class'=>'input-large'));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">是否推荐</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('archives[recommond]',$info['recommond'],$archivesSer->getArchivesRecommond(),array('class'=>'input-small','size'=>1));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">是否显示</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('archives[is_hide]',$info['is_hide'],$archivesSer->getArchivesIsHide(),array('class'=>'input-small','size'=>1));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">节目分类</label>
					<div class="controls">
						<?php 
							echo CHtml::listBox('archives[cat_id]',$info['cat_id'],$this->formatArchivesCat($archivesSer->getAllArchiveCat()),array('class'=>'input-small','size'=>1));
						?>
					</div>
				  </div>
				  
				  <!-- 
				  <div class="control-group">
					<label class="control-label" for="focusedInput">公聊公告</label>
					<div class="controls">
						<?php 
							echo CHtml::textField('archives[notice]',$info['notice'],array('class'=>'input-large'));
						?>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="focusedInput">私聊公告</label>
					<div class="controls">
						<?php 
							echo CHtml::textField('archives[private_notice]',$info['private_notice'],array('class'=>'input-large'));
						?>
					</div>
				  </div>
				   -->
				  <div class="control-group">
					<label class="control-label" for="focusedInput">直播服务器</label>
					<div class="controls">
						<?php 
						echo CHtml::listBox('server[server_id]',$info['server_id'],$info['live_server'],array('class'=>'input-large','size'=>1));
						echo CHtml::hiddenField('server[id]',$info['server_rel_id']);
						echo CHtml::hiddenField('server[archives_id]',$info['archives_id']);
						?>
					</div>
				  </div>
				  
				  <?php echo CHtml::hiddenField('archives[archives_id]',$info['archives_id']);?>
				  <?php echo CHtml::hiddenField('archives_id',$info['archives_id']);?>
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>