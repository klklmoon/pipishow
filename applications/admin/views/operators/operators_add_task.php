<?php
$this->breadcrumbs = array('运营工具','添加新手任务');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!$this->isAjax){?>
		<div data-original-title="" class="box-header well">
			<h2><i class="icon-edit"></i> 添加新手任务</h2>
		</div>
		<?php }?>
		<div class="box-content">
		
			<div class="alert alert-block" style="margin-left:60px;margin-right:200px;clear:both;">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<p>新添加的新手任务需要通知开发人员编写对应的任务处理代码才能正常领取奖励！</p>
			<?php foreach($notices as $notice){?>
				<p><?php echo isset($notice[0])?$notice[0]:$notice;?></p>
			<?php }?>
			</div>
			
			<form class="form-horizontal" id="purview_add" action="<?php echo $this->createUrl('operators/addTask',array('op'=>'addTask'));?>" method="post" enctype="multipart/form-data">		
				<fieldset>
					<div class="control-group">
						<label class="control-label">标题</label>				
						<div class="controls">
							<?php 
								$v1 = isset($info['name'])?$info['name']:'';
								echo CHtml::textField('data[name]',$v1,array('class'=>'input-large'));
							?>
						</div>
				  	</div> 
				  	
			  	 	<div class="control-group">
						<label class="control-label">文字内容</label>			
						<div class="controls">
							<?php 
								$v2 = isset($info['content'])?$info['content']:'';
								echo CHtml::textField('data[content]',$v2,array('class'=>'input-large'));
							?>
							可不填
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">图片内容</label>
						<div class="controls">
							<?php $v3 = isset($info['pic'])?$info['pic']:''; ?>
							<?php echo CHtml::fileField('pic',$v3,array('class'=>'input-small'));?></br>
							<?php if($v3){ ?>
						 	<img src="<?php echo $v3;?>" width="60%" height="60%"/>
							<?php } ?>
							可不填
						</div>
				  	</div>
				  	
				  	<div class="control-group">
						<label class="control-label">链接地址</label>			
						<div class="controls">
							<?php 
								$v2 = isset($info['url'])?$info['url']:'';
								echo CHtml::textField('data[url]',$v2,array('class'=>'input-large'));
							?>
							可不填
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">皮蛋数</label>			
						<div class="controls">
							<?php 
								$v4 = isset($info['pipiegg'])?$info['pipiegg']:'0.0';
								echo CHtml::textField('data[pipiegg]',$v4,array('class'=>'input-large'));
							?>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label" for="focusedInput">前台是否启用</label>
						<div class="controls">
							<?php echo Chtml::listBox('data[status]', isset($info['status'])?$info['status']:'1',array(0=>'停用','启用'), array('size'=>1,'class'=>'input-small','empty'=>'-请选择-'))?>
						  	<span class="label label-important" style="margin-left:10px;display:none;" id="info_form_is_display">请选择在前台是否启用</span>
						</div>
				  	</div>
			
					<div class="form-actions">
						<input class="btn btn-primary" value="提交" type="submit" name="yt1" id="sub_submit_channel">
						<?php 
						if(isset($info['tid'])){
							echo CHtml::hiddenField('data[tid]',$info['tid']);
						}
						?>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</div>

