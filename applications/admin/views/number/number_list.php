<?php
$this->breadcrumbs = array('靓号管理','靓号列表');
$type = $this->numService->getNumberType();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i>靓号列表管理</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round number_edit" title="添加靓号"><i class="icon-plus-sign"></i></a>
			</div>
		</div>
		
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('number/list');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
				  	<?php echo CHtml::listBox('form[type]',isset($condition['type'])?$condition['type']:'',$type,array('class'=>'input-small','size'=>1,'empty'=>'-靓号类型-'));?>
			  		<span>靓号</span> 
					<?php echo CHtml::textField('form[number]',isset($condition['number'])?$condition['number']:'',array('class'=>'input-small'));?>
				  	<span>创建时间</span>
				  	<?php echo CHtml::textField('form[create_time_start]',isset($condition['create_time_start'])?$condition['create_time_start']:'',array('class'=>'input-small'));?>至
				  	<?php echo CHtml::textField('form[create_time_end]',isset($condition['create_time_end'])?$condition['create_time_end']:'',array('class'=>'input-small'));?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
			  </fieldset>
			</form>   
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>靓号</th>
						  <th>靓号类型</th>
						  <th>自助价</th>
						  <th>确认价</th>
						  <th>寄语</th>
						  <th>创建时间</th>
						  <th>操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php 
				  		if(!empty($list['list'])){
				  		foreach($list['list'] as $v){
				  	?>
				  	<tr id="number_list_<?php echo $v['number'];?>">
				  		<td><?php echo $v['number'];?></td>
				  		<td><?php echo $type[$v['number_type']];?> </td>
				  		<td><?php echo $v['buffer_price'];?></td>
				  		<td><?php echo $v['confirm_price'];?></td>
				  		<td><?php echo $v['short_desc'];?></td>
				  		<td><?php echo date('Y-m-d H:i:s',$v['create_time']);?></td>
				  		<td>
				  			<a  href="javascript:void(1);" numberId="<?php echo $v['number'];?>" title="编辑"  class="number_edit"> <i class="icon-edit"></i></a>
				  			<a  href="javascript:void(1);" title="删除" numberId="<?php echo $v['number'];?>" class="number_del"><span class="icon icon-color icon-remove"></span></a>
				  			<a  href="javascript:void(1);" numberId="<?php echo $v['number'];?>" title="赠送"  class="number_share"> <i class="icon-share"></i></a>
				  		</td>
				  	</tr>
				  	<?php }?>
				  	<tr>
				  		<td colspan="7">
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
					  		<tr><td colspan="7">没有配置的数据</td></tr>
				  		</tbody>
				  	<?php }?>
				  </tbody>
			  </table>
		</div>
	</div><!--/span-->
</div>

<!-- 浮层 -->
<div class="modal hide fade" id="user_list_manage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
	</div>
	<div class="modal-body" id="user_list_manage_body">
	</div>
</div>

<script>
$(function() {
	$('#form_create_time_start').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm:ss'});
	});
	$('#form_create_time_end').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm:ss'});
	});

	$('.number_edit').live('click',function(e){
		var number = $(this).attr('numberId');
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('number/addNumber');?>",
			dataType:'html',
			data:{'number':number},
			success:function(msg){
				if(msg){
					$('#user_list_manage_body').html(msg);
				}else{
					$('#user_list_manage_body').html('失败');
				}
				e.preventDefault();
				$('#user_list_manage').modal('show');
			}
		});
	});

	$('.number_del').live('click',function(e){
		if(!confirm('确定要删除此靓号吗，此操作是不可逆的')){
			return false;
		}
		
		var number = $(this).attr('numberId');
		if(isNaN(number)){
			alert('参数有误，删除失败');
			return false;
		}
		
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('number/list');?>",
			dataType:'html',
			data:{'number':number,'op':'delNumber'},
			success:function(msg){
				if(msg == 1){
					$('#number_list_'+number).detach();
				}else{
					alert(msg);
				}
			}
		});
	});

	$('.number_share').live('click',function(e){
		if(!confirm('确定要将此靓号赠送给别人吗，此操作是不可逆的')){
			return false;
		}

		var number = $(this).attr('numberId');
		if(isNaN(number)){
			alert('参数有误，删除失败');
			return false;
		}

		//此靓号是否被使用
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('number/addUserNumber');?>",
			dataType:'html',
			data:{'number':number,'op':'checkNumberUsed'},
			success:function(msg){
				if(msg == 1){
					$.ajax({
						type:'post',
						url:"<?php echo $this->createUrl('number/addUserNumber');?>",
						dataType:'html',
						data:{'number':number},
						success:function(msg){
							if(msg){
								$('#user_list_manage_body').html(msg);
							}else{
								$('#user_list_manage_body').html('失败');
							}
							e.preventDefault();
							$('#user_list_manage').modal('show');
						}
					});
				}else{
					alert(msg);
				}
			}
		});
	});
});
</script>