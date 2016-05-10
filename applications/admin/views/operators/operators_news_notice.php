<?php
$this->breadcrumbs = array('运营工具','新闻公告管理');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2>新闻公告列表</h2>
			<div class="box-icon">
				<a class="btn btn-round" href="#" title="添加新闻公告"><i class="icon-plus"></i></a>
				<a class="btn btn-round" href="#" title="查看与编辑首页公告推荐"><i class="icon-list"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>文章ID</th>
						  <th width="150px;">文章标题</th>
						  <th>内容简介</th>
						  <th width="120px;">创建时间</th>
						  <th width="120px;">操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($threadList)){?>
				  	<?php foreach($threadList as $thread){?>
				  	<tr>
				  		<td><?php echo $thread['thread_id'];?></td>
				  		<td><?php echo $thread['title'];?></td>
				  		<td><?php echo strip_tags($thread['content']);?></td>
				  		<td><?php echo date('Y-m-d H:i:s',$thread['create_time']);?></td>
				  		<td>
				  			<a class="btn" href="#" threadId="<?php echo $thread['thread_id'];?>" title="编辑"> <i class="icon-edit"></i></a>
				  			<a class="btn" href="#" threadId="<?php echo $thread['thread_id'];?>" title="删除"> <i class="icon-remove"></i></a>
				  			<a class="btn" href="#" threadId="<?php echo $thread['thread_id'];?>" title="公告推荐"> <i class="icon-hand-up"></i></a>
				  		</td>
				  	</tr>
				  	
				  	<?php }?>
				  	<tr>
				  		<td colspan="5">
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
					  		<tr><td colspan="5">没有配置的数据</td></tr>
				  		</tbody>
				  	<?php }?>
				  </tbody>
			  </table>
		</div>
	</div><!--/span-->
</div>

<!-- 浮层 -->
<div class="modal hide fade span10" id="newsNotice_list_manage" style="left:5%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>新闻公告管理</h3>
	</div>
	<div class="modal-body" id="newsNotice_list_manage_body">
	</div>
</div>

<!-- 公告推荐项 -->
<div class="box-content" id="news_notice_rcmd" style="display:none">
	<div id="news_notice_rcmd_options">
		<span class="btn" sign="1">唱区公告</span>
		<span class="btn" sign="2">首页公告</span>
		<script>
		//公告推荐
		$("#news_notice_rcmd_options > .btn").click(function(e){
			var threadId = $(this).parent().attr('threadId');
			var sign = $(this).attr('sign');
			threadId = threadId?threadId:'';
			if(sign){
				if(sign == 1){
					var url = "<?php echo $this->createUrl('index/songnoticermd');?>";
				}else{
					var url = "<?php echo $this->createUrl('index/newsnoticermd');?>";
				}
				$.ajax({
					type:'post',
					url:url,
					dataType:'html',
					data:{'threadId':threadId},
					success:function(msg){
						$('#newsNotice_list_manage_body').html(msg);
						e.preventDefault();
						$('#newsNotice_list_manage').addClass('span10').css({"left":"5%"});
						$('#newsNotice_list_manage').modal('show');
					}
				});
			}
		});
		</script>
	</div>
</div>
		
<script>
$(function() {
	//添加新闻公告操作
	$(".icon-plus").click(function(e){
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('operators/addnewsnotice');?>",
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
				url:"<?php echo $this->createUrl('operators/addnewsnotice');?>",
				dataType:'html',
				data:{'threadId':threadId,'op':'getThreadInfo'},
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
				url:"<?php echo $this->createUrl('operators/addnewsnotice');?>",
				dataType:'html',
				data:{'threadId':threadId,'op':'delNewsNotice'},
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
		$("#news_notice_rcmd").children('div').attr('threadId',threadId);
		//唱区公告推荐或者是首页公告推荐
		$('#newsNotice_list_manage_body').html($("#news_notice_rcmd").html());
		e.preventDefault();
		$('#newsNotice_list_manage').removeClass("span10").removeAttr('style').modal('show');
	});

	//查看与管理所有的公告推荐
	$(".icon-list").click(function(e){
		//唱区公告推荐或者是首页公告推荐
		$('#newsNotice_list_manage_body').html($("#news_notice_rcmd").html());
		e.preventDefault();
		$('#newsNotice_list_manage').removeClass("span10").removeAttr('style').modal('show');
	});
});

</script>