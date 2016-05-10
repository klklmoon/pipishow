<?php
$this->breadcrumbs = array('主播管理','主播代理');
?>
<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:9%;margin-left:10px;padding:3px 3px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!isset($isAjax) || !$isAjax){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>主播代理列表</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" title="新增主播代理"><i class="icon-plus-sign"></i></a>
			</div>
		</div>
		<?php }?>
		<div class="box-content">
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>帐号名</th>
						<th>昵称</th>
						<th>代理名</th>
						<th>负责人</th>
						<th>手机联系</th>
						<th>QQ</th>
						<th>代理主播</th>
						<th>代理查询</th>
						<th>前台显示</th>
						<th>申请状态</th>
						<th>管理操作</th>
					</tr>
				</thead>
				<tbody>
				  	<?php if(!empty($proxyList)){?>
				  	<?php foreach($proxyList as $uinfo){?>
				  	<tr>
						<td><?php echo $uinfo['username'];?></td>
						<td><?php echo $uinfo['nickname'];?> </td>
						<td><?php echo $uinfo['agency'];?> </td>
						<td><?php echo $uinfo['realname'];?> </td>
						<td><?php echo $uinfo['mobile'];?> </td>
						<td><?php echo $uinfo['qq'];?> </td>
						<td><?php echo $uinfo['total_dotey'];?> </td>
						<td><?php echo $uinfo['query_allow'];?> </td>
						<td><?php echo $uinfo['is_display'];?> </td>
						<td><?php echo ($uinfo['status'] == 1)?'待审核':(($uinfo['status'] == 2)?'通过':'未通过');?> </td>
						<td>
							<span class="btn"><i class="icon-edit" uid=<?php echo $uinfo['uid'];?>></i></span>
							<?php if($uinfo['status'] == 1){?>
								<span class='btn auth_stauts' uid="<?php echo $uinfo['uid'];?>">授权</span>
							<?php }?>
						</td>
					</tr>
				  	<?php }?>
				  	<?php }else{?>
				<tbody>
					<tr>
						<td colspan="11">没有配置的数据</td>
					</tr>
				</tbody>
				  	<?php }?>
				  </tbody>
			</table>
		</div>
	</div>
		
</div>

<!-- 浮层 -->
<div class="modal hide fade" id="user_list_manage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>代理主播管理</h3>
	</div>
	<div class="modal-body" id="user_list_manage_body"></div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//新增主播代理
	$('.icon-plus-sign').click(function(e){
		var obj = this;
		$.ajax({
			url:"<?php echo $this->createUrl('dotey/addproxy');?>",
			type:'post',
			dataType:'html',
			success:function(msg){
				e.preventDefault()
				$('#user_list_manage_body').html(msg);
				$('#user_list_manage').modal('show');
			}
		});
	});

	//编辑信息
	$('.icon-edit').click(function(e){
		var uid = $(this).attr('uid');
		var obj = this;
		if(uid){
			$.ajax({
				url:"<?php echo $this->createUrl('dotey/addproxy');?>",
				type:'post',
				dataType:'html',
				data:{"uid":uid},
				success:function(msg){
					e.preventDefault()
					$('#user_list_manage_body').html(msg);
					$('#user_list_manage').modal('show');
				}
			});
		}
	});
	//授权
	$('.auth_stauts').click(function(e){
		var uid = $(this).attr('uid');
		var obj = this;
		if(uid){
			$.ajax({
				url:"<?php echo $this->createUrl('dotey/proxy'); ?>",
				dataType:'html',
				type:'post',
				data:{'uid':uid,'op':'proxyChange'},
				success:function(msg){
					if(msg == 1){
						$(obj).detach();
					}else{
						e.preventDefault();
						$('#user_list_manage_body').html(msg);
						$('#user_list_manage').modal('show');
					}
				}
			});
		}
	});
})
</script>