<?php
$this->breadcrumbs = array('礼物分类管理');
?>
<div class="row-fluid sortable">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> 礼物分类管理</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" title="添加礼物分类"><i class="icon-plus-sign"></i></a>
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table class="table table-bordered" id="gift_cat_table">
			  <thead>
				  <tr>
					  <th style="width:140px;">分类ID</th>
					  <th>分类名称</th>
					  <th>分类标识</th>
					  <th style="width:140px;">操作</th>
				  </tr>
			  </thead>   
			  <tbody>
			  	<?php if(isset($cateInfo)){?>
			  	<?php foreach($cateInfo as $cid => $cinfo){?>
			  	<tr>
			  		<td><?php echo $cid;?></td>
			  		<td><?php echo $cinfo['cat_name'];?></td>
			  		<td><?php echo $cinfo['cat_enname'];?></td>
			  		<td>
			  			<a class="btn btn-info" href="#" cateId="<?php echo $cid;?>"> 编辑 </a>
			  			<a class="btn btn-danger" href="#" cateId="<?php echo $cid;?>"> 删除 </a>
			  		</td>
			  	</tr>
			  	<?php }?>
			  	<?php }else{?>
			  		<tbody>
				  		<tr><td colspan="4">没有配置的数据</td></tr>
			  		</tbody>
			  	<?php }?>
			  </tbody>
		  </table>            
		</div>
	</div><!--/span-->
</div>

<!-- 添加礼物分类浮层 -->
<div class="modal hide fade" id="gift_category">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>礼物分类管理</h3>
	</div>
	<div class="modal-body" id="gift_category_body">
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//添加礼物分类
	$(".icon-plus-sign").click(function(e){
		var url = "<?php echo $this->createUrl("gift/addgiftcat")?>";
		$.ajax({
			type:'post',
			dataType:'html',
			url:url,
			data:{'op':'addGiftCat'},
			success:function(msg){
				if(msg){
					$('#gift_category_body').html(msg);
				}else{
					$('#gift_category_body').html("加载失败");
				}
				e.preventDefault();
				$('#gift_category').modal('show');
			}
			
		});
	});
	//编辑
	$("#gift_cat_table .btn-info").click(function(e){
		var cid = $(this).attr("cateId");
		if(cid){
			var url = "<?php echo $this->createUrl("gift/addgiftcat")?>";
			$.ajax({
				type:'post',
				dataType:'html',
				url:url,
				data:{'op':'addGiftCat',"cid":cid},
				success:function(msg){
					if(msg){
						$('#gift_category_body').html(msg);
					}else{
						$('#gift_category_body').html("加载失败");
					}
					e.preventDefault();
					$('#gift_category').modal('show');
				}
			});
		}else{
			$('#gift_category_body').html("加载失败");
			e.preventDefault();
			$('#gift_category').modal('show');
		}
	});
	//删除
	$("#gift_cat_table .btn-danger").click(function(e){
		var cid = $(this).attr("cateId");
		var obj = this;
		if(cid){
			var url = "<?php echo $this->createUrl("gift/addgiftcat")?>";
			$.ajax({
				type:'post',
				dataType:'html',
				url:url,
				data:{'op':'delGiftCat',"cid":cid},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						$('#gift_category_body').html("删除失败");
						e.preventDefault();
						$('#gift_category').modal('show');
					}
				}
			});
		}else{
			$('#gift_category_body').html("请求失败");
			e.preventDefault();
			$('#gift_category').modal('show');
		}
	});
})
</script>