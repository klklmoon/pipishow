<?php
$this->breadcrumbs = array('主题管理');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div data-original-title="" class="box-header well">
			<h2><i class="icon-edit"></i> 主题频道管理</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting-addpur btn-round"><i class="icon-plus"></i></a>
			</div>
		</div>
	</div>
</div>

<?php 
$rawStartLabel = '<div class="row-fluid sortable ui-sortable">';
$rawEndLabel = '</div>';
$node = 4;

if(isset($allSubTheme)){ 
	foreach ($allSubTheme as $id => $group) {
		if($node%4==0){
			echo $rawStartLabel;
		}
?>
	<div class="box span3">
		<div class="box-header well" data-original-title="">
			<h2><?php echo $allParentTheme[$id]['channel_name'];?></h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" channelId="<?php echo $id;?>"><i class="icon-plus"></i></a>
				<a href="#" class="btn btn-setting btn-round" channelId="<?php echo $id;?>"><i class="icon-remove-sign"></i></a>
				<a href="#" class="btn btn-setting btn-round" channelId="<?php echo $id;?>"><i class="icon-edit"></i></a>
			</div>
		</div>
		<div class="box-content">
			<ul class="dashboard-list" style ="overflow-y: auto;height:200px;">
				<?php if (is_array($group)){?>
					<?php foreach ($group as $sub_id=>$item){?>
						<li>
							<span>子频道:</span> <?php echo $item['sub_name'];?>
							<i class="icon-remove-sign" style="margin-left:10px;display:none;" channelId="<?php echo $id;?>" subChannelId="<?php echo $item['sub_channel_id'];?>"></i>
							<i class='icon-edit' style='margin-left:10px;display:none;' channelId="<?php echo $id;?>" subChannelId="<?php echo $item['sub_channel_id'];?>"></i>
						</li>
					<?php }?>
				<?php }?>
			</ul>
		</div>
	</div>
<?php 
		if($node%4==1){
			echo $rawEndLabel;
		}
		
		if ($node == 0){
			$node = 4;
		}
		--$node;
	}
?>
<?php }?>

<!-- 添加修改权限浮层 -->
<div class="modal hide fade" id="channel_update_items" style="height:auto">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>主题频道管理</h3>
	</div>
	<div class="modal-body" id="channel_update_items_body">
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//添加频道;
	$('.btn-setting-addpur').click(function(e){
		$.ajax({
			  type: "POST",
			  url: "<?php echo $this->createUrl('channel/createtheme');?>",
			  dataType: "html",
			  success:function(msg){
				  if(msg){
					  $('#channel_update_items_body').html(msg);
				  }
				  e.preventDefault();
				  $('#channel_update_items').modal('show');
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
			  url: "<?php echo $this->createUrl('channel/createtheme');?>",
			  dataType: "html",
			  data:{"subChannelId":$(this).attr('subChannelId'),"op":"editSubChannel","channelId":$(this).attr('channelId')},
			  success:function(msg){
				  if(msg){
					  $('#channel_update_items_body').html(msg);
				  }
				  e.preventDefault();
				  $('#channel_update_items').modal('show');
			}
		});
	});
	//新增父频道组下的子频道
	$(".box  .box-icon .icon-plus").click(function(e){
		$.ajax({
			  type: "POST",
			  url: "<?php echo $this->createUrl('channel/createtheme');?>",
			  dataType: "html",
			  data:{"channelId":$(this).parent('a').attr('channelId')},
			  success:function(msg){
				  if(msg){
					  $('#channel_update_items_body').html(msg);
				  }
				  e.preventDefault();
				  $('#channel_update_items').modal('show');
			}
		});
	});
	//删除子频道 
	$(".dashboard-list > li .icon-remove-sign").click(function(e){
		var subChannelId = $(this).attr('subChannelId');
		var channelId = $(this).attr('channelId');
		var obj = $(this);
		var objLis = obj.parents('.dashboard-list');
		$.ajax({
			  type: "POST",
			  url: "<?php echo $this->createUrl('channel/createtheme');?>",
			  dataType: "html",
			  data:{"subChannelId":subChannelId,"op":"delSubChannelTheme",'channelId':channelId},
			  success:function(msg){
				  if(msg == 1){
					  if(objLis.children('li').length <=1){
						  objLis.parents(".box").detach();
					  }else{
						  obj.parent().detach();
					  }
				  }else{
					  $('#channel_update_items_body').html(msg);
					  e.preventDefault();
					  $('#channel_update_items').modal('show');
				  }
			}
		});
	});
	//删除父频道 
	$(".box-icon > a > .icon-remove-sign").click(function(e){
		var channelId = $(this).parent('a').attr('channelId');
		var obj = $(this);
		var objLis = obj.parents('.dashboard-list');
		$.ajax({
			  type: "POST",
			  url: "<?php echo $this->createUrl('channel/createtheme');?>",
			  dataType: "html",
			  data:{"op":"delChannelTheme",'channelId':channelId},
			  success:function(msg){
				  if(msg == 1){
					  obj.parents(".box").detach();
				  }else{
					  $('#channel_update_items_body').html(msg);
					  e.preventDefault();
					  $('#channel_update_items').modal('show');
				  }
			}
		});
	});
	//修改父频道 
	$(".box-icon > a > .icon-edit").click(function(e){
		var channelId = $(this).parent('a').attr('channelId');
		var obj = $(this);
		var objLis = obj.parents('.dashboard-list');
		$.ajax({
			  type: "POST",
			  url: "<?php echo $this->createUrl('channel/createtheme');?>",
			  dataType: "html",
			  data:{"op":"editChannel",'channelId':channelId},
			  success:function(msg){
				  if(msg == 1){
					  obj.parents(".box").detach();
				  }else{
					  $('#channel_update_items_body').html(msg);
					  e.preventDefault();
					  $('#channel_update_items').modal('show');
				  }
			}
		});
	});
})
</script>