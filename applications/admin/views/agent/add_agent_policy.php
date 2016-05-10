<?php
$this->breadcrumbs = array('代理管理','添加代理政策');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!$this->isAjax){?>
		<div data-original-title="" class="box-header well">
			<h2><i class="icon-edit"></i> 添加代理政策</h2>
		</div>
		<?php }?>
		<div class="box-content">
			<?php if(isset($agentpolicys) && count($agentpolicys)>0){?>
			<div class="alert alert-block" style="margin-left:60px;margin-right:200px;clear:both;">
				<button type="button" class="close" data-dismiss="alert">×</button>
			<?php foreach($agentpolicys as $agentpolicy){?>
				<p><?php echo isset($agentpolicy[0])?$agentpolicy[0]:$agentpolicy;?></p>
			<?php }?>
			</div>
			<?php }?>
			
			<form class="form-horizontal" id="purview_add" action="<?php echo $this->createUrl('agent/agentpolicy',array('op'=>'addAgentPolicy'));?>" method="post">		
				<fieldset>
					<div class="control-group">
						<label class="control-label">标题</label>				
						<div class="controls">
							<?php 
								$v1 = isset($info['title'])?$info['title']:'';
								echo CHtml::textField('agentpolicy[title]',$v1,array('class'=>'input-large'));
							?>
						</div>
				  	</div> 
				  	
			  	 	<div class="control-group">
						<label class="control-label">内容</label>			
						<div class="controls">
							<?php 
								$v2 = isset($postInfo['content'])?$postInfo['content']:'';
								echo CHtml::textArea('agentpolicy[content]',$v2,array('class'=>'cleditor'));
							?>
						</div>
					</div>
			
					<div class="form-actions">
						<input class="btn btn-primary" value="提交" type="submit" name="yt1" id="sub_submit_channel">
						<?php 
						if(isset($info['thread_id'])){
							echo CHtml::hiddenField('agentpolicy[thread_id]',$info['thread_id']);							
							echo CHtml::hiddenField('post_id',$postInfo['post_id']);							
						}
						?>
					</div>
				</fieldset>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
    CKEDITOR.replace('agentpolicy[content]',{
        filebrowserBrowseUrl : '/browser/browse.php',
        filebrowserUploadUrl : '<?php echo $this->createUrl('public/ckeditorupload',array('type'=>'agent_policy'));?>',
        filebrowserImageBrowseUrl :  '<?php echo $this->createUrl('public');?>/ckfinder/ckfinder.html?Type=Images',
        filebrowserFlashBrowseUrl :  '/ckfinder/ckfinder.html?Type=Flash',
        filebrowserImageUploadUrl  :  '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
        filebrowserFlashUploadUrl  :  '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',
		baseHref:'',
		width:'80%',
		height:300 ,
	});
</script>
