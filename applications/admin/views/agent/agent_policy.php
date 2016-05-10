<?php
$this->breadcrumbs = array('代理管理','代理政策');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2>代理政策列表</h2>
			<div class="box-icon">
				<a class="btn btn-round" href="#" title="添加代理政策"><i class="icon-plus"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th width="150px;">文章标题</th>
						  <th>内容简介</th>
						  <th width="120px;">创建时间</th>
						  <th width="80px;">操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($threadList['list'])){?>
				  	<?php foreach($threadList['list'] as $thread){?>
				  	<tr>
				  		<td><?php echo $thread['title'];?></td>
				  		<td><?php echo strip_tags(mb_substr($thread['content'],0,16,'UTF-8'))."......";?></td>
				  		<td><?php echo date('Y-m-d H:i:s',$thread['create_time']);?></td>
				  		<td>
				  			<a class="btn" href="#" threadId="<?php echo $thread['thread_id'];?>" title="编辑"> <i class="icon-edit"></i></a>
				  			<a class="btn" href="#" threadId="<?php echo $thread['thread_id'];?>" title="删除"> <i class="icon-remove"></i></a>
				  		</td>
				  	</tr>
				  	<?php }?>
				  	<tr>
				  		<td colspan="4">
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
					  		<tr><td colspan="4">没有数据</td></tr>
				  		</tbody>
				  	<?php }?>
				  </tbody>
			  </table>
		</div>
	</div><!--/span-->
</div>

<!-- 浮层 -->
<div class="modal hide fade span10" id="agentPolicy_list_manage" style="left:5%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>代理政策</h3>
	</div>
	<div class="modal-body" id="agentPolicy_list_manage_body">
	</div>
</div>

<script>
$(function() {
	//添加主播政策操作
	$(".icon-plus").click(function(e){
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('agent/agentpolicy');?>",
			dataType:'html',
			data:{'op':'addAgentPolicy'},
			success:function(msg){
				if(msg){
					$('#agentPolicy_list_manage_body').html(msg);
				}else{
					$('#agentPolicy_list_manage_body').html('信息获取失败');
				}
				e.preventDefault();
				$('#agentPolicy_list_manage').modal('show');
			}
		});	
	});
	
	//编辑操作
	$(".box-content .icon-edit").click(function(e){
		var threadId = $(this).parents('a').attr('threadId');
		if(threadId){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('agent/agentpolicy');?>",
				dataType:'html',
				data:{'threadId':threadId,'op':'getThreadInfo'},
				success:function(msg){
					if(msg){
						$('#agentPolicy_list_manage_body').html(msg);
					}else{
						$('#agentPolicy_list_manage_body').html('信息获取失败');
					}
					e.preventDefault();
					$('#agentPolicy_list_manage').modal('show');
				}
			});
		}
	});
	//删除
	$(".box-content .icon-remove").click(function(e){
		var threadId = $(this).parents('a').attr('threadId');
		var obj = this;
		if(threadId){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('agent/agentpolicy');?>",
				dataType:'html',
				data:{'threadId':threadId,'op':'delAgentPolicy'},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						$('#agentPolicy_list_manage_body').html(msg);
						e.preventDefault();
						$('#agentPolicy_list_manage').modal('show');
					}
				}
			});
		}
	});
	
});
</script>