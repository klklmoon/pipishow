<?php
$this->breadcrumbs = array('道具分类管理');
?>
<div class="row-fluid sortable">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> 道具分类管理</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" title="添加属性分类"><i class="icon-plus-sign"></i></a>
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<table class="table table-bordered" id="props_cat_table">
			  <thead>
				  <tr>
					  <th style="width:140px;">分类ID</th>
					  <th>分类名称</th>
					  <th>分类标识</th>
					  <th>是否显示</th>
					  <th style="width:140px;">操作</th>
				  </tr>
			  </thead>   
			  <tbody>
			  	<?php if(isset($cateList)){?>
			  	<?php foreach($cateList as $cid => $cinfo){?>
			  	<tr>
			  		<td><?php echo $cid;?></td>
			  		<td><?php echo $cinfo['name'];?></td>
			  		<td><?php echo $cinfo['en_name'];?></td>
			  		<td><?php echo $isDisplay[$cinfo['is_display']];?></td>
			  		<td>
			  			<a class="btn" href="#" cateId="<?php echo $cid;?>" title="编辑"> <i class="icon-edit"></i> </a>
			  			<a class="btn" href="#" cateId="<?php echo $cid;?>" title="查看分类属性"> <i class="icon-list"></i> </a>
			  			<a class="btn" href="#" cateId="<?php echo $cid;?>" title="删除"> <i class="icon-remove"></i></a>
			  		</td>
			  	</tr>
			  	<?php }?>
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
<!-- 添加道具分类浮层 -->
<div class="modal hide fade" id="props_category">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>道具分类管理</h3>
	</div>
	<div class="modal-body" id="props_category_body">
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//添加礼物分类
	$(".icon-plus-sign").click(function(e){
		var url = "<?php echo $this->createUrl("props/addpropscat")?>";
		$.ajax({
			type:'post',
			dataType:'html',
			url:url,
			success:function(msg){
				if(msg){
					$('#props_category_body').html(msg);
				}else{
					$('#props_category_body').html("加载失败");
				}
				e.preventDefault();
				$('#props_category').modal('show');
			}
			
		});
	});
	//编辑
	$("#props_cat_table .icon-edit").click(function(e){
		var cid = $(this).parents('a').attr("cateId");
		if(cid){
			var url = "<?php echo $this->createUrl("props/addpropscat")?>";
			$.ajax({
				type:'post',
				dataType:'html',
				url:url,
				data:{"cat_id":cid},
				success:function(msg){
					if(msg){
						$('#props_category_body').html(msg);
					}else{
						$('#props_category_body').html("加载失败");
					}
					e.preventDefault();
					$('#props_category').modal('show');
				}
			});
		}else{
			$('#props_category_body').html("缺少参数 无法进行编辑操作");
			e.preventDefault();
			$('#props_category').modal('show');
		}
	});
	//查看分类属性
	$("#props_cat_table .icon-list").click(function(e){
		var cid = $(this).parents('a').attr("cateId");
		if(cid){
			var url = "<?php echo $this->createUrl("props/CatAttr")?>";
			$.ajax({
				type:'post',
				dataType:'html',
				url:url,
				data:{"cat_id":cid},
				success:function(msg){
					if(msg){
						$('#props_category').addClass("modal hide fade span10");
						$('#props_category').css({"left":"5%"});
						$('#props_category_body').html(msg);
					}else{
						$('#props_category_body').html("加载失败");
					}
					e.preventDefault();
					$('#props_category').modal('show');
				}
			});
		}else{
			$('#props_category_body').html("缺少参数 无法进行编辑操作");
			e.preventDefault();
			$('#props_category').modal('show');
		}
	});
	//删除
	$("#props_cat_table .icon-remove").click(function(e){
		var cid = $(this).parents('a').attr("cateId");
		var obj = this;
		if(cid){
			var url = "<?php echo $this->createUrl("props/addpropscat")?>";
			$.ajax({
				type:'post',
				dataType:'html',
				url:url,
				data:{'op':'delPropsCat',"cid":cid},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						$('#props_category_body').html(msg);
						e.preventDefault();
						$('#props_category').modal('show');
					}
				}
			});
		}else{
			$('#props_category_body').html("请求失败");
			e.preventDefault();
			$('#props_category').modal('show');
		}
	});
})
</script>