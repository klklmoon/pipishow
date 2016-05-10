<?php 
	$this->breadcrumbs = array(
		'用户角色关联编辑'
	);
?>

<?php 
$rawStartLabel = '<div class="row-fluid sortable ui-sortable">';
$rawEndLabel = '</div>';
$node = 4;
?>
<?php 
	$form = $this->beginWidget('CActiveForm',array(
		'id' => 'role_add',
		'action' => $this->createUrl('purview/userRoleAdd'),
		//'enableAjaxValidation' => true,
		'enableClientValidation' => true,
		'errorMessageCssClass' => 'alert alert-block validationError ',
		'clientOptions' => array(
				'validateOnSubmit' => true,								
			),
		'htmlOptions' => array(
				'class' => 'form-horizontal',
				//'errorCssClass' => ' error',
				//'successCssClass' => 'success',
			),
	));
?>
<?php if(isset($rangeRoles)){?>
<div class="row-fluid sortable ui-sortable">
	<?php 
	foreach($rangeRoles as $rangeName=>$roles){
		if(count($roles)<=0) continue;
		if($node%4==0){
			echo $rawStartLabel;
		}
	?>
	<div class="box span3">
		<div class="box-header well" data-original-title="">
			<h2><?php echo $rangeName;?></h2>
			<?php if (!isset($isAjax)){?>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
				<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
			<?php }?>
		</div>
		<div class="box-content" style ="overflow-y: auto;height:150px;">
				<?php 
					echo CHtml::checkBoxList(
						get_class($model).'[role_id]['.$rangeName.']', 
						isset($checkUserRoles[$rangeName])?$checkUserRoles[$rangeName]:'', 
						$roles,
						array(
							'labelOptions'=>array('class'=>'checkbox inline'),
						)
					)
				?>
		</div>
	</div>
	<?php 
		if($node%4==1){
			echo $rawEndLabel;
		}
		
		if ($node == 0){
			$node = 4;
		}
		--$node;
	}
	?>
</div>
<?php }?>		
			
			
			
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
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
				<?php echo $form->label($model,'username', array( 'class'=>'control-label' ) ); ?>
				<div class="controls">
					<?php 
						echo $form->textField($model,'username',
								array(
									'class'=>'input-large focused search-query',
									'value'=>isset($userInfos['username'])?$userInfos['username']:'',
								)
						);
					?>
					<?php echo CHtml::button('button',array('class'=>'btn','value'=>'检测用户','id'=>'purview_check_username'))?><span class="label label-important" id="check_username_info" style="display:none;margin-left:10px;"></span>
					<?php echo $form->error($model,'username');?>
				</div>
			</div>
			
			<div class="form-actions">
				<?php if(isset($userRoleInfo['uid'])){?>
				<?php echo $form->hiddenField($model,'uid',array('value'=>$userRoleInfo['uid']));?>
				<?php }?>
				<?php echo Chtml::submitButton('Submit',array('class'=>'btn btn-primary','value'=>'提交'));?>
			</div>
		</fieldset>
		</div>
	</div><!--/span-->
	
</div>
<?php $this->endWidget();?>

<script type="text/javascript">
//全选 和反选
function checkList(id){
	if(id != null){
		var ck = "checked";
		var ckd = 'checked';
		if($("input[name='"+id+"']").parents('div').children().first().attr('class')){
			ck = "";
			ckd = '';
		}
		$("input[name='"+id+"[]']").each(
			function(){
				$(this).parent("span").attr('class',ck);
				$(this).checked= ckd;
			}
		);
	}
}
//检测用户名的合法性
$("#purview_check_username").click(function(){
	var checkId = "<?php echo '#'.get_class($model).'_username';?>";
	var username = $(checkId).attr('value');
	if(username){
		$.ajax({
			url:"<?php echo $this->createUrl('purview/userroleadd');?>",
			data:{'username':username,'op':'checkUserName'},
			dataType:'html',
			type:'post',
			success:function(msg){
				if(msg == 1){
					$("#check_username_info").html(username+" 验证通过").show();
				}else{
					$("#check_username_info").html(username+" 未通过").show();
				}
			}
		});
	}else{
		$("#check_username_info").html("用户名为空").show();
	}
});
</script>

