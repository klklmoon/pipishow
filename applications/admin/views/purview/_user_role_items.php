	<h5>
		<?php echo CHtml::checkBox(
				get_class($model).'checkAll',
				false,
				array(
					'value' => '全选',
					'id' => get_class($model).'[role_id]',
					'onclick' => 'checkList(this.id)',
					)
			)?>
		<label for="<?php echo get_class($model).'[role_id]';?>" class="checkbox inline">全选</label>
	</h5>
	<?php 
		echo CHtml::checkBoxList(
			get_class($model).'[role_id]', 
			$check, 
			$allRoles,
			array(
				'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
				'labelOptions'=>array('class'=>'checkbox inline'),
			)
		)
	?>
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