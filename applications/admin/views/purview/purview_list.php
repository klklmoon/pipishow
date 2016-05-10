<?php
$this->breadcrumbs=array(
		'系统权限列表',
	);
?>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div data-original-title="" class="box-header well">
			<h2><i class="icon-edit"></i> 创建系统权限</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting-addpur btn-round"><i class="icon-plus"></i></a>
				<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
	</div><!--/span-->
	
</div>

<?php 
$rawStartLabel = '<div class="row-fluid sortable ui-sortable">';
$rawEndLabel = '</div>';
$node = 3;

if(isset($sysGroups)){ 
	foreach ($sysGroups as $key => $group) {
		if($node%3==0){
			echo $rawStartLabel;
		}
?>
	<div class="box span4">
		<div class="box-header well" data-original-title="">
			<h2><?php echo $key;?></h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" groupName="<?php echo $key;?>"><i class="icon-plus"></i></a>
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
				<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
			</div>
		</div>
		<div class="box-content">
			<ul class="dashboard-list" style ="overflow-y: auto;height:200px;">
				<?php if (is_array($group)){?>
					<?php foreach ($group as $op=>$item){?>
						<li>
							<span>操作名称:</span> <?php echo $op;?> <br>
							<span>状态:</span>
							<?php if ($item['is_use'] == 1){?>
									<span class="label label-success">可用</span>
							<?php }else{?>
								<span class="label label-important">删除</span>
							<?php }?>
							<i class="icon-remove-sign" style="margin-left:10px;display:none;" purviewid="<?php echo $item['purview_id'];?>"></i>
							<i class='icon-edit' style='margin-left:10px;display:none;' purviewid="<?php echo $item['purview_id'];?>"></i>
						</li>
					<?php }?>
				<?php }?>
			</ul>
		</div>
	</div>
<?php 
		if($node%3==1){
			echo $rawEndLabel;
		}
		
		if ($node == 0){
			$node = 3;
		}
		--$node;
	}
?>
<?php }?>

<!-- 添加修改权限浮层 -->
<div class="modal hide fade" id="prview_update_items" style="height:auto">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>系统权限操作</h3>
	</div>
	<div class="modal-body" id="prview_update_items_body">
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//添加权限和分组;
	$('.btn-setting-addpur').click(function(e){
		$.ajax({
			  type: "POST",
			  url: "<?php echo $this->createUrl('purview/puradd');?>",
			  dataType: "html",
			  success:function(msg){
				  if(msg){
					  $('#prview_update_items_body').html(msg);
				  }
				  e.preventDefault();
				  $('#prview_update_items').modal('show');
			}
		});
	});
	
	$(".dashboard-list > li").hover(
		function () {
			$(this).css("background-color","#a04060");
			$(this).children().last().show();
			$(this).children().eq(-2).show();
		},
		function () {
    		$(this).css("background-color",'');
    		$(this).children().last().hide();
    		$(this).children().eq(-2).hide();
  		}
	);

	//编辑权限和分组
	$(".dashboard-list > li .icon-edit").click(function(e){
		$.ajax({
			  type: "POST",
			  url: "<?php echo $this->createUrl('purview/puradd');?>",
			  dataType: "html",
			  data:{"purview_id":$(this).attr('purviewid')},
			  success:function(msg){
				  if(msg){
					  $('#prview_update_items_body').html(msg);
				  }
				  e.preventDefault();
				  $('#prview_update_items').modal('show');
			}
		});
	});
	//新增权限组下的权限
	$(".box  .box-icon > .btn-setting").click(function(e){
		$.ajax({
			  type: "POST",
			  url: "<?php echo $this->createUrl('purview/puradd');?>",
			  dataType: "html",
			  data:{"groupName":$(this).attr('groupName')},
			  success:function(msg){
				  if(msg){
					  $('#prview_update_items_body').html(msg);
				  }
				  e.preventDefault();
				  $('#prview_update_items').modal('show');
			}
		});
	});
	//删除权限
	//编辑权限和分组
	$(".dashboard-list > li .icon-remove-sign").click(function(e){
		var purviewId = $(this).attr('purviewid');
		var obj = $(this);
		var objLis = obj.parents('.dashboard-list');
		$.ajax({
			  type: "POST",
			  url: "<?php echo $this->createUrl('purview/puradd');?>",
			  dataType: "html",
			  data:{"purview_id":purviewId,"op":"purDel"},
			  success:function(msg){
				  if(msg == 1){
					  if(objLis.children('li').length <=1){
						  objLis.parents(".box").detach();
					  }else{
						  obj.parent().detach();
					  }
				  }else{
					  $('#prview_update_items_body').html(msg);
					  e.preventDefault();
					  $('#prview_update_items').modal('show');
				  }
			}
		});
	});
})
</script>