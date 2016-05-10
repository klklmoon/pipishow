<?php
$this->breadcrumbs = array('礼物之星规则说明管理');
?>
<div class="row-fluid sortable">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> 礼物之星规则说明管理</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('Activities/GiftStarSetList');?>" method="post">	
				<div class="row-fluid">
					<div class="span12">
						<div class="dataTables_filter">
							<span>周编号:</span>
							<?php echo CHtml::textField('form[week_id]',isset($condition['week_id'])?$condition['week_id']:'',
								array('class'=>'input-small'));?>
							<span>周一日期：</span>
							<?php echo CHtml::textField('form[monday_date]',isset($condition['monday_date'])?$condition['monday_date']:'',
								array('class'=>'input-small'));?>
							 
							<input type="submit" name="form_search_gift_list_botton" id="form_search_gift_list_botton" value="搜索" class="btn">
							<span class="label label-important" id="check_user_info" style="display:none;margin-left:10px;"></span>
						</div>
					</div>
				</div>
			</form>
			
			<table class="table table-bordered" id="set_list_table">
			  <thead>
				  <tr>
					  <th>说明id</th>
					  <th>周编号</th>
					  <th>周一日期</th>
					  <th>说明类型</th>
					  <th>操作</th>
				  </tr>
			  </thead>   
			  <tbody>
			  	<?php foreach($setList as $setRow):?>
			  	<tr>
			  		<td><?php echo $setRow['set_id'];?></td>
			  		<td><?php echo $setRow['week_id'];?></td>
			  		<td><?php echo $setRow['monday_date'];?></td>
			  		<td><?php echo $setRow['set_type']==1?"常规":"通用";?></td>
			  		<td>
			  		<?php if(date("Y-m-d")<$setRow['monday_date']):?>
			  			<a class="" href="#" set_id="<?php echo $setRow['set_id'];?>"> <i class="icon-edit"></i></a>
			  		<?php else:?>
			  			<span>无</span>
			  		<?php endif;?>
			  		</td>
			  	</tr>
			  	<?php endforeach;?>
			  	<tr>
			  		<td colspan="15">
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
			  </tbody>
		  </table>            
		</div>
	</div><!--/span-->
</div>

<!-- 添加礼物分类浮层 -->
<div class="modal hide fade span10" id="set_list" style="left:5%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>规则说明</h3>
	</div>
	<div class="modal-body" id="set_list_body">
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//修改礼物
	$("#set_list_table .icon-edit").click(function(e){
		var set_id = $(this).parent('a').attr('set_id');
		if(set_id){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('Activities/GiftStarSet');?>",
				dataType:'html',
				data:{'op':'updateSetInfo','set_id':set_id},
				success:function(msg){
					if(msg){
						$('#set_list_body').html(msg);
					}else{
						$('#set_list_body').html('信息获取失败');
					}
					e.preventDefault();
					$('#set_list').modal('show');
				}
			});
		}
	});

	$('#form_monday_date').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd'});
	});
})

</script>
