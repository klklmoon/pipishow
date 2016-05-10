<?php 
	$this->breadcrumbs = array(
		'创建权限项'
	);
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!$isAjax){?>
		<div data-original-title="" class="box-header well">
			<h2><i class="icon-edit"></i> 创建权限项</h2>
		</div>
		<?php }?>
		<div class="box-content" style="padding-right:5px;">
			<?php 
			$form = $this->beginWidget('CActiveForm',array(
				'id' => 'purview_add',
				'action' => $this->createUrl('purview/purAdd'),
				//'enableAjaxValidation' => true,
				'enableClientValidation' => true,
				'errorMessageCssClass' => 'alert alert-block validationError ',
				'clientOptions' => array(
						'validateOnSubmit' => true,								
					),
				'htmlOptions' => array(
						'class' => 'form-horizontal',
					),
			));
		?>
		<fieldset>
			<?php 
				echo $form->errorSummary( $model,
						'<div class="alert alert-info"> <h4 class="alert-heading">警告!</h4></div>',
						null, array('class'=>'alert alert-block summaryError', )
					);
			?>
			<div class="control-group">
				<?php echo $form->label($model,'group',array('class'=>'control-label'));?>
				<div class="controls">
					<?php 
						$checkGroup = isset($purviewInfo['group'])?$purviewInfo['group']:$groupFlag;
						echo Chtml::listBox(
							'all_group_list', $checkGroup, $groups, array( 'size' => 1, )
						);
						?>
					<?php echo CHtml::button('button',array('class'=>'btn','value'=>'创建分组','id'=>'purview_create_group'))?>
				</div class="controls">
				<div class="controls">
					<?php 
						$isHidden = (isset($purviewInfo['group']) || $groupFlag)?'':'hide';
						$groupName = isset($purviewInfo['group'])?$purviewInfo['group']:$groupFlag;
						echo $form->textField($model,'group',array( 'class'=>'input-large focused '.$isHidden,'value'=>$groupName));
						echo $form->error($model,'group');
					?>
				</div>
		  	</div> 
		  	 
		  	 <div class="control-group">
				<?php echo $form->label($model,'purview_name', array( 'class'=>'control-label' ) ); ?>
				<div class="controls">
					<?php 
						echo $form->textField($model,'purview_name', array( 'class'=>'input-large focused','value'=>isset($purviewInfo['purview_name'])?$purviewInfo['purview_name']:'') );
					?>
					<?php echo $form->error($model,'purview_name');?>
				</div>
			</div>
			
			<div class="control-group">
				<?php echo $form->label($model,'module', array( 'class'=>'control-label' ) ); ?>
				<div class="controls">
					<?php 
						echo $form->textField($model,'module', array( 'class'=>'input-large focused','value'=>isset($purviewInfo['module'])?$purviewInfo['module']:'') );
					?>
					<?php echo $form->error($model,'module');?>
				</div>
			</div>
			
			<div class="control-group">
				<?php echo $form->label($model,'controller', array( 'class'=>'control-label' ) ); ?>
				<div class="controls">
					<?php 
						echo $form->textField($model,'controller', array( 'class'=>'input-large focused','value'=>isset($purviewInfo['controller'])?$purviewInfo['controller']:'') );
					?>
					<?php echo $form->error($model,'controller');?>
				</div>
			</div>
			
			<div class="control-group">
				<?php echo $form->label($model,'action', array( 'class'=>'control-label' ) ); ?>
				<div class="controls">
					<?php 
						echo $form->textField($model,'action', array( 'class'=>'input-large focused','value'=>isset($purviewInfo['action'])?$purviewInfo['action']:'') );
					?>
					<?php echo $form->error($model,'action');?>
				</div>
			</div>
			
			<div class="control-group">
				<?php echo $form->label($model,'is_tree_display',array('class'=>'control-label'));?>
				<div class="controls">
					<?php 
						$isDisplay = isset($purviewInfo['is_tree_display'])?$purviewInfo['is_tree_display']:0;
					?>
					<?php echo CHtml::listBox( get_class($model).'[is_tree_display]', $isDisplay,array(0=>'隐藏',1=>'显示') , array('size'=>1));?>
					<?php echo $form->error($model,'is_tree_display');?>
				</div>
		  	</div>
		  	<div class="control-group">
				<?php echo $form->label($model,'range',array('class'=>'control-label'));?>
				<div class="controls">
					<?php 
						echo CHtml::CheckBoxList(
							get_class($model).'[range]', $hasRange,$range , array('size'=>1,'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
							'labelOptions'=>array('class'=>'checkbox inline'))
					);?>
					<?php echo $form->error($model,'range');?>
				</div>
		  	</div>
		  	
			<div class="form-actions">
				<?php if (isset($purviewInfo['purview_id'])){?>
				<?php echo $form->hiddenField($model,'purview_id',array('value'=>$purviewInfo['purview_id']));?>
				<?php }?>
				<?php echo Chtml::submitButton('Submit',array('class'=>'btn btn-primary','value'=>'提交'));?>
			</div>
		</fieldset>
		</div>
	</div>
	<?php $this->endWidget();?>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#purview_create_group").click(function(){
				var groupId = "#<?php echo get_class($model)?>_group";
				$(groupId).val('');
				$(groupId).show();
			});
			
			$("#all_group_list").click(function(){
				var groupId = "#<?php echo get_class($model)?>_group";
				$(groupId).show();
				$(groupId).val(this.value);
			});
			
		});
	</script>
</div>