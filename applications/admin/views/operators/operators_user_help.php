<?php
$this->breadcrumbs = array('运营工具','用户帮助');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2>用户帮助列表</h2>
			<div class="box-icon">
				<a class="btn btn-round" href="#" title="添加用户帮助"><i class="icon-plus"></i></a>
			</div>
		</div>
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('operators/userhelp');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
					<span>所属目录：</span>
					<?php echo CHtml::listBox('sub_forum', $subForum, $allSubForum,array('size'=>1,'class'=>'input-small','empty'=>'-请选择-'));?>
					<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索'));?>
				</div>
			  </fieldset>
			
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
				  	<?php if(!empty($threadList)){?>
				  	<?php foreach($threadList as $thread){?>
				  	<tr>
				  		<td><?php echo $thread['title'];?></td>
				  		<td><?php echo strip_tags($thread['content']);?></td>
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
					  		<tr><td colspan="4">没有配置的数据</td></tr>
				  		</tbody>
				  	<?php }?>
				  </tbody>
			  </table>
			  </form>
		</div>
	</div><!--/span-->
</div>

<!-- 浮层 -->
<div class="modal hide fade span10" id="newsNotice_list_manage" style="left:5%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>用户帮助管理</h3>
	</div>
	<div class="modal-body" id="newsNotice_list_manage_body">
	</div>
</div>

<script>
$(function() {
	var subForum = "<?php echo $subForum;?>";
	//添加用户帮助
	$(".icon-plus").click(function(e){
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('operators/adduserhelp');?>",
			dataType:'html',
			success:function(msg){
				if(msg){
					$('#newsNotice_list_manage_body').html(msg);
				}else{
					$('#newsNotice_list_manage_body').html('信息获取失败');
				}
				e.preventDefault();
				$('#newsNotice_list_manage').modal('show');
			}
		});	
	});
	
	//编辑操作
	$(".box-content .icon-edit").click(function(e){
		var threadId = $(this).parents('a').attr('threadId');
		if(threadId){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('operators/adduserhelp');?>",
				dataType:'html',
				data:{'threadId':threadId,'sub_forum':subForum,'op':'getThreadInfo'},
				success:function(msg){
					if(msg){
						$('#newsNotice_list_manage_body').html(msg);
					}else{
						$('#newsNotice_list_manage_body').html('信息获取失败');
					}
					e.preventDefault();
					$('#newsNotice_list_manage').modal('show');
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
				url:"<?php echo $this->createUrl('operators/adduserhelp');?>",
				dataType:'html',
				data:{'threadId':threadId,'sub_forum':subForum,'op':'delUserHelp'},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						$('#newsNotice_list_manage_body').html(msg);
						e.preventDefault();
						$('#newsNotice_list_manage').modal('show');
					}
				}
			});
		}
	});
	//推荐操作
	$(".icon-hand-up").click(function(e){
		var threadId = $(this).parents('a').attr('threadId');
		if(threadId){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('index/newsnoticermd');?>",
				dataType:'html',
				data:{'threadId':threadId},
				success:function(msg){
					$('#newsNotice_list_manage_body').html(msg);
					e.preventDefault();
					$('#newsNotice_list_manage').modal('show');
				}
			});
		}
	});
	//查看与管理所有的公告推荐
	$(".icon-list").click(function(e){
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('index/newsnoticermd');?>",
			dataType:'html',
			success:function(msg){
				$('#newsNotice_list_manage_body').html(msg);
				e.preventDefault();
				$('#newsNotice_list_manage').modal('show');
			}
		});
	});
});
</script>