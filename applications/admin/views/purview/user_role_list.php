<?php
$this->breadcrumbs=array(
		'用户权限管理',
	);
?>

<div class="row-fluid sortable">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> 用户权限管理</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round"><i class="icon-plus-sign"></i></a>
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('purview/userrole');?>" method="post">	
				<div class="row-fluid">
					<div class="span12">
						<div class="dataTables_filter">
						<label>
							<span>用户名/ID:</span> 
							<input type="text" name="search[user_info]" id="user_info" value="">
							<input type="submit" name="search[search_user_role]" id="search_user_role" value="搜索" class="btn">
							<span class="label label-important" id="check_user_info" style="display:none;margin-left:10px;"></span>
						</label>
						</div>
					</div>
				</div>
			</form>
			
			<table class="table table-bordered" id="user_role_relateion">
			  <thead>
				  <tr>
					  <th style="width:140px;">用户</th>
					  <th>角色</th>
					  <th style="width:70px;">操作</th>
				  </tr>
			  </thead>   
			  <tbody>
			  	<?php if(isset($userRoles['roleInfos'])){?>
			  	<?php foreach($userRoles['roleInfos'] as $uid => $roles){?>
			  	<tr uid="<?php echo $uid;?>">
			  		<td><?php echo isset($userRoles['uInfos'][$uid])?$userRoles['uInfos'][$uid]:'';?>(<?php echo $uid;?>)</td>
			  		<td class="userRoleIds">
			  			<?php foreach($roles as $roleId => $role){?>
			  			<span class="label label-success"><?php echo $role['role_name'];?></span><i roleId="<?php echo $roleId;?>"></i>
			  			<?php }?>
			  		</td>
			  		<td>
			  			<a class="btn btn-info" href="#"> 编辑 </a>
			  		</td>
			  	</tr>
			  	<?php }?>
			  	<?php }else{?>
			  		<tbody>
				  		<tr><td colspan="3">没有配置的数据</td></tr>
			  		</tbody>
			  	<?php }?>
				
			  </tbody>
		  </table>            
		</div>
	</div><!--/span-->

</div>

<!-- 添加修改权限浮层 -->
<div class="modal hide span10 fade" id="prview_user_role" style="left:5%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>用户权限管理</h3>
	</div>
	<div class="modal-body" id="prview_user_role_body">
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//hover事件
	$("#user_role_relateion tr .userRoleIds").hover(
		function(){
			$(this).css("background-color",'#cf0');
			$(this).children('i').each(function(){
				$(this).last().addClass('icon-remove');
			});
		},
		function(){
			$(this).css("background-color",'');
			$(this).children('i').each(function(){
				$(this).last().removeClass('icon-remove');
			});
		}
			
	);
	//删除事件
	$(".userRoleIds i").click(function(e){
		var uid = $(this).parents('tr').attr('uid');
		var roleId = $(this).attr('roleId');
		var obj = this;
		if(uid && roleId){
			$.ajax({
				type:'post',
				dataType:'text',
				data:{'uid':uid,'role_id':roleId,'op':'userRoleDel'},
				url:"<?php echo $this->createUrl('purview/userroleadd');?>",
				success:function(msg){
					if(msg == 1){
						var c =$(obj).parents('td').children("span").length;
						if(c <= 1) {
							$(obj).parents('tr').detach();
						}else{
							$(obj).prev("span").detach();
							$(obj).detach();
						}
					}else{
						$('#prview_user_role_body').html(msg);
						e.preventDefault();
						$('#prview_user_role').modal('show');
					}
				}
				
			});
		}
	});

	//添加用户角色权限
	$(".icon-plus-sign").click(function(e){
		var url = "<?php echo $this->createUrl("purview/userroleadd")?>";
		$.ajax({
			type:'post',
			dataType:'html',
			url:url,
			success:function(msg){
				if(msg){
					$('#prview_user_role_body').html(msg);
				}else{
					$('#prview_user_role_body').html("加载失败");
				}
				e.preventDefault();
				$('#prview_user_role').modal('show');
			}
			
		});
	});
	//修改角色权限
	$("#user_role_relateion tr .btn-info").click(function(e){
		var url = "<?php echo $this->createUrl("purview/userroleadd")?>";
		var uid = $(this).parents('tr').attr('uid');
		$.ajax({
			type:'post',
			dataType:'html',
			url:url,
			data:{"uid":uid},
			success:function(msg){
				if(msg){
					$('#prview_user_role_body').html(msg);
				}else{
					$('#prview_user_role_body').html("加载失败");
				}
				e.preventDefault();
				$('#prview_user_role').modal('show');
			}
		});
	});
	//搜索 过虑
	$("#search_user_role").click(function(e){
		var userInfo = $("input[name='search[user_info]']").attr('value');
		if(userInfo){
			$("#check_user_info").html('').hide();
			$(this).submit();
		}else{
			$("#check_user_info").html("用户名或用户ID 不能为空").show();
			return false;
		}
	});
})
</script>