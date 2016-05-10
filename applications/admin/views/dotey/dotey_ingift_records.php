<?php
$this->breadcrumbs = array('用户管理','获取主播收礼明细记录');
?>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('dotey/doteyingift');?>" method="post">
			  <fieldset>
				<div class="control-group">
				  	<span>收礼时间:</span>
				  	<?php $uid = Yii::app()->request->getParam('uid');?>
				  	<?php echo CHtml::hiddenField('uid',$uid);?>
				  	<?php echo CHtml::textField('ingift[start_time]',isset($condition['start_time'])?$condition['start_time']:'',array('class'=>'date_ui'));?>至
				  	<?php echo CHtml::textField('ingift[end_time]',isset($condition['end_time'])?$condition['end_time']:'',array('class'=>'date_ui'));?>
				  	<?php echo CHtml::submitButton('ingift_search',array('class'=>'btn','value'=>'搜索'));?>
				  	<a class="btn" href="<?php echo $this->createUrl('dotey/doteyingift',array('condition'=>json_encode($condition),'uid'=>$uid,'op'=>'dlInGiftExcel'));?>" title="导出Excel">导出Excel</a>
				</div>
			  </fieldset>
			</form>   
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>发送者</th>
						  <th>接收者(UID) </th>
						  <th>礼物名称 </th>
						  <th>礼物数量</th>
						  <th>消费皮蛋</th>
						  <th>魅力点</th>
						  <th>魅力值</th>
						  <th>送礼时间</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($records)){?>
				  	<?php 
				  		foreach($records as $r){
				  			$info = unserialize($r['info']);
				  	?>
				  	<tr>
				  		<td><?php echo $info['sender'];?>(<?php echo $r['uid'];?>)</td>
				  		<td><?php echo $info['receiver'];?>(<?php echo $r['to_uid'];?>)</td>
				  		<td><?php echo isset($info['gift_zh_name'])?$info['gift_zh_name']:'';?></td>
				  		<td><?php echo $r['num'];?></td>
				  		<td><?php echo $r['pipiegg'];?></td>
				  		<td><?php echo $r['charm_points'];?></td>
				  		<td><?php echo $r['charm'];?></td>
				  		<td><?php echo date('Y-m-d H:i:s',$r['create_time']);?></td>
				  	</tr>
				  	<?php }?>
				  	<tr>
				  		<td colspan="8">
				  			<div class="pagination pagination-centered">
				  			<?php    
							$this->widget('CLinkPager',array(
					            'header'=>'',  
								'firstPageCssClass' => '',  
					            'firstPageLabel' => '首页',    
					            'lastPageLabel' => '末页',  
					            'lastPageCssClass' => '',  
								'previousPageCssClass' =>'prev disabled',  
					            'prevPageLabel' => '上一页',    
					            'nextPageLabel' => '下一页', 
								'nextPageCssClass' => 'next', 
								'selectedPageCssClass' => 'active',
								'internalPageCssClass' => '',
								'htmlOptions' => array('class'=>''),
					            'pages' => $pager,    
					            'maxButtonCount'=>8    
								)
							);
							?>
							</div>
				  		</td>
				  	</tr>
				  	<?php }else{?>
				  		<tbody>
					  		<tr><td colspan="8">没有配置的数据</td></tr>
				  		</tbody>
				  	<?php }?>
				  </tbody>
			  </table>
		</div>
	</div><!--/span-->
</div>

<script>
$(function(){
	//注册开始时间
	$( '#ingift_start_time' ).datepicker(
		{ 
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//注册结束时间
	$( '#ingift_end_time' ).datepicker(
		{ 
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
})
</script>