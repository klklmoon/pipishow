<?php
$this->breadcrumbs = array('运营工具','客服管理');
$kefuType = $operateSer->getKefuType();
$contactType = $operateSer->getKefuContactType();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2>客服列表</h2>
			<div class="box-icon">
				<a class="btn btn-round" href="#" title="添加客服管理"><i class="icon-plus"></i></a>
			</div>
		</div>
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('operators/kefu');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
					<span>客服类型：</span>
					<?php $select1 = isset($condition['kefu_type'])?$condition['kefu_type']:'';?>
					<?php echo CHtml::listBox('kefu[kefu_type]', $select1, $kefuType,array('size'=>1,'class'=>'input-small','empty'=>' '));?>
					<span>联系方式：</span>
					<?php $select2 = isset($condition['contact_type'])?$condition['contact_type']:'';?>
					<?php echo CHtml::listBox('kefu[contact_type]', $select2, $contactType,array('size'=>1,'class'=>'input-small','empty'=>' '));?>
					<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索'));?>
				</div>
			  </fieldset>
			
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>客服类型</th>
						  <th>联系方式</th>
						  <th>联系人</th>
						  <th>号码</th>
						  <th>创建时间</th>
						  <th>操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $v){?>
				  	<tr>
				  		<td><?php echo isset($kefuType[$v['kefu_type']])?$kefuType[$v['kefu_type']]:'不详';?></td>
				  		<td><?php echo isset($contactType[$v['contact_type']])?$contactType[$v['contact_type']]:'不详';?></td>
				  		<td><?php echo $v['contact_name'];?></td>
				  		<td><?php echo $v['contact_account'];?></td>
				  		<td><?php echo date('Y-m-d H:i:s',$v['create_time']);?></td>
				  		<td>
				  			<a class="btn" href="#" kefuId="<?php echo $v['id'];?>" title="编辑"> <i class="icon-edit"></i></a>
				  			<a class="btn" href="#" kefuId="<?php echo $v['id'];?>" title="删除"> <i class="icon-remove"></i></a>
				  		</td>
				  	</tr>
				  	
				  	<?php }?>
				  	<tr>
				  		<td colspan="6">
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
					  		<tr><td colspan="6">没有配置的数据</td></tr>
				  		</tbody>
				  	<?php }?>
				  </tbody>
			  </table>
			  </form>
		</div>
	</div><!--/span-->
</div>

<!-- 浮层 -->
<div class="modal hide fade" id="kefu_list_manage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>客服管理</h3>
	</div>
	<div class="modal-body" id="kefu_list_manage_body">
	</div>
</div>

<script>
$(function() {
	//添加用户帮助
	$(".icon-plus").click(function(e){
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('operators/addkefu');?>",
			dataType:'html',
			success:function(msg){
				if(msg){
					$('#kefu_list_manage_body').html(msg);
				}else{
					$('#kefu_list_manage_body').html('信息获取失败');
				}
				e.preventDefault();
				$('#kefu_list_manage').modal('show');
			}
		});	
	});
	
	//编辑操作
	$(".box-content .icon-edit").click(function(e){
		var kefuId = $(this).parents('a').attr('kefuId');
		if(kefuId){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('operators/addkefu');?>",
				dataType:'html',
				data:{'kefuId':kefuId,'op':'getKefu'},
				success:function(msg){
					if(msg){
						$('#kefu_list_manage_body').html(msg);
					}else{
						$('#kefu_list_manage_body').html('信息获取失败');
					}
					e.preventDefault();
					$('#kefu_list_manage').modal('show');
				}
			});
		}
	});
	//删除
	$(".box-content .icon-remove").click(function(e){
		var kefuId = $(this).parents('a').attr('kefuId');
		var obj = this;
		if(kefuId){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('operators/addkefu');?>",
				dataType:'html',
				data:{'kefuId':kefuId,'op':'delKefu'},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						$('#kefu_list_manage_body').html(msg);
						e.preventDefault();
						$('#kefu_list_manage').modal('show');
					}
				}
			});
		}
	});
});
</script>