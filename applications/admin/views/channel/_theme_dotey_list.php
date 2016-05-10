	<?php if($areaCatList){?>
	<h5><label for="doteylist" class="checkbox inline">地区频道</label> </h5>
	<?php 
			echo CHtml::checkBoxList('areacat[]', '', $areaCatList,
				array(
					'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
					'labelOptions'=>array('class'=>'checkbox inline'),
				)
			);
			echo CHtml::hiddenField('channelId',$channelId);
	?>
	<dl style="border-top:1px #999999 dotted"></dl>
	<?php }?>
	<h5>
		<?php echo CHtml::checkBox( 'doteylist', false,
				array(
					'value' => '全选',
					'id' => 'doteylist',
					'onclick' => 'checkList(this.id)',
				)
			)?>
		<label for="doteylist" class="checkbox inline">全选</label>
	</h5>
	<?php 
		echo CHtml::checkBoxList('doteylist[]', '', $doteyInfos,
			array(
				'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
				'labelOptions'=>array('class'=>'checkbox inline','style'=>'width:30.5%;'),
			)
		);
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