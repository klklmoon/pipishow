<?php 
	$this->breadcrumbs = array(
		'添加角色权限'
	);
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!$isAjax){?>
		<div data-original-title="" class="box-header well">
			<h2><i class="icon-edit"></i> 创建角色</h2>
		</div>
		<?php }?>
		<div class="box-content">
			<?php 
			$form = $this->beginWidget('CActiveForm',array(
				'id' => 'role_add',
				'action' => $this->createUrl('purview/roleAdd'),
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
				echo $form->errorSummary(
						$model,
						'<div class="alert alert-info"> <h4 class="alert-heading">警告!</h4></div>',
						null,
						array('class'=>'alert alert-block summaryError', )
					);
			?>
			
			<div class="control-group">
				<?php echo $form->label($model,'role_name', array( 'class'=>'control-label' ) ); ?>
				<div class="controls">
					<?php 
						echo $form->textField($model,'role_name',
								array(
									'class'=>'input-large focused',
									'value'=>isset($roleInfo['role_name'])?$roleInfo['role_name']:'',
								)
						);
					?>
					<?php echo $form->error($model,'role_name');?>
				</div>
			</div>
			
			<div class="control-group">
				<?php echo $form->label($model,'role_type',array('class'=>'control-label'));?>
				<div class="controls">
					<?php 
						echo Chtml::listBox(get_class($model).'[role_type]', 
							isset($roleInfo['role_type'])?$roleInfo['role_type']:'', 
							$this->purSer->getPurviewRange(),
							array( 'size' => 1,
								'empty' => '--请选择--',
								/*'ajax' => array(
									'type' => 'post',
									'url' => Yii::app()->createUrl('purview/roleAdd'),
									'data' => array('role_type'=>'js:this.value','op' => 'checkRoleGroupItems','role_id'=>isset($roleInfo['role_id'])?$roleInfo['role_id']:''),
									'update' => '#'.get_class($model)."_groups_items",
								)*/
							)
						);
					?>
					<?php echo $form->error($model,'role_type');?>
				</div>
		  	</div>  
		  	
		  	<div class="control-group">
			  <?php echo $form->label($model,'description',array('class'=>'control-label'));?>
			  <div class="controls">
			  	<?php echo $form->textArea($model,'description',array('rows'=>'2','value'=>isset($roleInfo['description'])?$roleInfo['description']:''));?>
			  	<?php echo $form->error($model,'description');?>
			  </div>
			</div>
			
			<div class="control-group">
				<?php echo $form->label($model,'groups_items',array('class'=>'control-label'));?>
				<div id="<?php echo get_class($model)."_groups_items";?>">
				</div>
			</div>
			
			<div class="form-actions">
				<?php if(isset($roleInfo['role_id'])){?>
				<?php echo $form->hiddenField($model,'role_id',array('value'=>$roleInfo['role_id']));?>
				<?php }?>
				<?php echo $form->hiddenField($model,'sub_id',array('value'=>'-1'));?>
				<?php echo Chtml::submitButton('Submit',array('class'=>'btn btn-primary','value'=>'添加'));?>
				<?php echo Chtml::resetButton('reset',array('class'=>'btn','value'=>'取消'));?>
			</div>
		</fieldset>
		<?php $this->endWidget();?>
		</div>
	</div><!--/span-->
	
</div>

<script type="text/javascript">
var roleTypeSelect = "<?php echo '#'.get_class($model)."_role_type"?>";
$(roleTypeSelect).click(function(){
	var roleId = "<?php echo isset($roleInfo['role_id'])?$roleInfo['role_id']:'';?>";
	$.ajax({
		url:"<?php echo $this->createUrl("purview/roleadd");?>",
		type:"post",
		dataType:'html',
		data:{"role_type":this.value,"op":"checkRoleGroupItems","role_id":roleId},
		success:function(msg){
			var updateId = "<?php echo '#'.get_class($model).'_groups_items';?>";
			$(updateId).html(msg);
		}
	});	
});

var uploadRoleType = "<?php echo isset($roleInfo['role_type'])?$roleInfo['role_type']:'';?>";
if(uploadRoleType){
	var roleId = "<?php echo isset($roleInfo['role_id'])?$roleInfo['role_id']:'';?>";
	$.ajax({
		url:"<?php echo $this->createUrl("purview/roleadd");?>",
		type:"post",
		dataType:'html',
		data:{"role_type":uploadRoleType,"op":"checkRoleGroupItems","role_id":roleId},
		success:function(msg){
			var updateId = "<?php echo '#'.get_class($model).'_groups_items';?>";
			$(updateId).html(msg);
		}
	});	
}

//全选 和反选
function checkList2(id){
	if(id != null){
		var ck = "checked";
		if($("input[name='"+id+"']").parent('span').attr('class') == 'checked'){
			ck = "";
		}
		
		$("input[name='"+id+"[]']").each(
			function(){
				$(this).parent("span").attr('class',ck);
			}
		);
	}
}
</script>
