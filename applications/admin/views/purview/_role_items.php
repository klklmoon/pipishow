<?php 
foreach ($role_items as $label=>$items){
	$selected = isset($role[$label])?array_keys($role[$label]):'';
?>
<div class="controls">
	<h5>
		<?php echo $label;?>&nbsp;&nbsp;
		<?php echo CHtml::checkBox(
				get_class($model).'[groups_items]['.$label.']',
				false,
				array(
					'value' => '全选',
					'id' => get_class($model).'[groups_items]['.$label.']',
					'onclick' => 'checkList(this.id)',
					)
			)?>
		<label for="<?php echo get_class($model).'[groups_items]['.$label.']';?>" class="checkbox inline">全选</label>
	</h5>
	<?php 
		echo CHtml::checkBoxList(
			get_class($model).'[groups_items]['.$label.']', 
			$selected, 
			$items,
			array(
				'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
				'labelOptions'=>array('class'=>'checkbox inline'),
			)
		)
	?>
  	</div>
  	<?php }?>
<script type="text/javascript">
//全选与反选
function checkList(id){
	var names = id+'[]';
	var ids=document.getElementsByName(names); 
	var idName=document.getElementById(id); 

	var ck = idName.checked;
		
	for(i=0;i<ids.length;i++)  
		ids[i].checked=ck;
}  
</script>