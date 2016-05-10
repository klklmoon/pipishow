<?php
$this->breadcrumbs = array('靓号管理','编辑或添加靓号');
$type = $this->numService->getNumberType();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 编辑或添加靓号的基本信息</h2>
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
			<form class="form-horizontal" action="<?php echo $this->createUrl('number/addNumber');?>" method="post">
				<fieldset>
					<?php if(!isset($numberInfo['number'])){?>
					<div class="control-group">
					<label class="control-label" for="focusedInput">靓号类型</label>
					<div class="controls">
						<?php echo CHtml::listBox('numbers[number_type]',isset($numberInfo['number_type'])?$numberInfo['number_type']:'',$type,array('class'=>'input-small','size'=>1,'empty'=>'-靓号类型-'));?>
					</div>
				  </div>
				  <?php }?>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">靓号</label>
					<div class="controls">
						<?php 
						  	if(isset($numberInfo['number'])){
						  		echo CHtml::hiddenField('numbers[number]',$numberInfo['number']);
						  		echo CHtml::hiddenField('isNew',false);
						  	?>
						  		<span style="cursor:pointer;" class="label label-warning">
						  		<?php echo $numberInfo['number'];?></span>
						  	<?php 
						  	}else{
						  		echo CHtml::hiddenField('isNew',true);
						  		echo CHtml::textField('numbers[number]','',array('class'=>'input-large focused'));
						  	}
						  ?>
					</div>
				  </div>
				  
				 <div class="control-group">
					<label class="control-label" for="focusedInput">确认价</label>
					<div class="controls">
						<?php echo CHtml::textField('numbers[confirm_price]',isset($numberInfo['confirm_price'])?$numberInfo['confirm_price']:'',array('class'=>'input-large focused'));?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">寄语</label>
					<div class="controls">
					  <?php echo CHtml::textArea('numbers[short_desc]',isset($numberInfo['short_desc'])?$numberInfo['short_desc']:'');?>
					</div>
				  </div>
				  
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script style="text/javascript">
$(function() {
	var isEdit = '<?php echo isset($numberInfo['number'])?true:false;?>';
	$(':submit').click(function(){

		if(!isEdit){
			var type = $('#numbers_number_type option:selected').attr('value');
			if(isNaN(type)){
				alert('请选择靓号类型');
				return false;
			}

			var typeLen;
			if(type == <?php echo NUMBER_TYPE_FOUR?>){
				typeLen = 4;
			}else if(type == <?php echo NUMBER_TYPE_FIVE?>){
				typeLen = 5;
			}else if(type == <?php echo NUMBER_TYPE_SIX?>){
				typeLen = 6;
			}else if(type == <?php echo NUMBER_TYPE_SEVEN?>){
				typeLen = 7;
			}else{
				alert('选择的靓号类型有误');
				return false;
			}

			var number = parseInt($('#numbers_number').attr('value'));
			if(!number){
				alert('靓号不能为空');
				return false;
			}
			
			if(number.toString().length != typeLen){
				alert('靓号位数必须是'+typeLen+'位');
				return false;
			}
		}

		var price = $('#numbers_confirm_price').attr('value');
		if(!price || isNaN(price)){
			alert('确认售价不能为空且为数字');
			return false;
		}

		if(!$('#numbers_short_desc').attr('value')){
			alert('寄语不能为空');
			return false;
		}
		return true;
	});
});
</script>