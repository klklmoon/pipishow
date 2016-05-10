<?php
$this->breadcrumbs = array('主播管理','停播管理');
$sources = $this->getProxyAndTutorListOption();
?>
<style type="text/css">
	.table th, .table tr, .table .btn{padding:1px;}
</style>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('dotey/stoplive');?>" method="post">
				<fieldset>
					<div class="control-group">
					<?php if(!$isFilterSource){?>
					<?php $select1 = isset($condition['sources'])?$condition['sources']:''?>
					<?php echo CHtml::listBox('form[sources]', $select1, $sources,array('class'=>'input-small','empty'=>'-来源-','size'=>1));?>
					<?php }?>
					<span>昵称:</span> 
			  		<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
					<span>用户名:</span> 
			  		<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
			  		<span>UID:</span> 
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
					<span>停播时间:</span>
				  	<?php echo CHtml::textField('form[live_time_on]',isset($condition['live_time_on'])?$condition['live_time_on']:'',array('class'=>'date_ui input-small'));?>至
				  	<?php echo CHtml::textField('form[live_time_end]',isset($condition['live_time_end'])?$condition['live_time_end']:'',array('class'=>'date_ui input-small'));?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				  	<?php echo CHtml::submitButton('user_search_excel',array('class'=>'btn','value'=>'下载Excel','id'=>'dl_excel_submit'));?>
				</div>
				</fieldset>
			</form>
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>用户名</br>昵称</th>
						<th>真实姓名</th>
						<th>操作UID</br>账号</th>
						<th>操作理由</th>
						<th>注册时间</br>停播时间</th>
						<th>最近一次</br>开播时间</th>
						<th>富豪等级</br>主播等级</th>
						<th>总皮蛋</th>
						<th>消费</br>皮蛋</th>
						<th>上月</br>魅力点</th>
						<th>近15天</br>魅力点</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $uinfo){?>
				  	<tr>
						<td> <?php echo $uinfo['username'];?></br><?php echo $uinfo['nickname'];?> </td>
						<td> <?php echo $uinfo['realname'];?> </td>
						<td> <?php 
				  				if(isset($uinfo['op_uinfo'])){
				  					echo $uinfo['op_uinfo']['uid'].'</br>'.$uinfo['op_uinfo']['username'];
				  				}
				  			?>
				  		</td>
						<td>
							<?php 
				  				if(isset($uinfo['reason'])){
				  					echo $uinfo['reason']['op_desc'];
				  				}
				  			?>
						</td>
						<td><?php echo date('Y-m-d',$uinfo['reg_time']);?></br><?php echo date('Y-m-d',$uinfo['stop_time']);?></td>
						<td><?php echo !empty($uinfo['last_live_time'])?date('Y-m-d',$uinfo['last_live_time']):'';?></td>
						<td> <?php echo $uinfo['user_rank'];?></br><?php echo $uinfo['dotey_rank'];?> </td>
						<td> <?php echo $uinfo['total_pipieggs'];?> </td>
						<td> <?php echo $uinfo['consume_pipiegg'];?> </td>
						<td> <?php echo $uinfo['prev_charm_points'];?> </td>
						<td> <?php echo $uinfo['15days_charm_points'];?> </td>
						<td> <a class="btn" title="恢复直播" uid="<?php echo $uinfo['uid'];?>"><span class="icon icon-color icon-undo"></span></a> </td>
					</tr>
				  	<?php }?>
				  	<tr>
						<td colspan="13">
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
					<tr>
						<td colspan="13">没有配置的数据</td>
					</tr>
				</tbody>
				  	<?php }?>
				  </tbody>
			</table>
		</div>
	</div>
	<!--/span-->
</div>

<!-- 浮层 -->
<div class="modal hide fade" id="user_list_manage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>直播管理</h3>
	</div>
	<div class="modal-body" id="user_list_manage_body"></div>
</div>


<script>
$(function() {
	//注册开始时间
	$( '#form_live_time_on' ).datepicker(
		{ 
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//注册结束时间
	$( '#form_live_time_end' ).datepicker(
		{ 
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//编辑操作
	$(".box-content .icon-edit").click(function(e){
		var recordId = $(this).parents('a').attr('recordId');
		if(recordId){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('dotey/onlive');?>",
				dataType:'html',
				data:{'record_id':recordId,'op':'editLiveRecords','condition':'<?php echo json_encode($condition);?>'},
				success:function(msg){
					if(msg){
						$('#user_list_manage_body').html(msg);
					}else{
						$('#user_list_manage_body').html('信息获取失败');
					}
					e.preventDefault();
					$('#user_list_manage').modal('show');
				}
			});
		}
	});
	
	//查看直播在线统计
	$('.icon-th-list').click(function(e){
		var archivesId = $(this).parent('a').attr('archivesId');
		if(archivesId){
			$.ajax({
				url:"<?php echo $this->createUrl('dotey/onlive');?>",
				dataType:'html',
				type:'post',
				data:{'archives_id':archivesId,'op':'searchLiveOnline'},
				success:function(msg){
					$('#user_list_manage_body').html(msg);
					e.preventDefault();
					$('#user_list_manage').modal('show');
				}
			});
		}
	});
	//搜索提交
	$('#user_search_submit').click(function(){
		var nickname = $("#form_nickname").attr('value');
		var username = $("#form_username").attr('value');
		if(nickname){
			if(nickname.length <= 1){
				alert("搜索昵称的关键字太少");
				return false;
			}
		}
		if(username){
			if(username.length <= 1){
				alert("搜索用户名的关键字太少");
				return false;
			}
		}
		return true;
	});
	//excel下载
	$('#dl_excel_submit').click(function(){
		var nickname = $("#form_nickname").attr('value');
		var username = $("#form_username").attr('value');
		var action = $(this).parents('form').attr('action');
		$(this).parents('form').attr('action',action+'&op=dlStopLiveExcel');
		
		if(nickname){
			if(nickname.length <= 1){
				alert("搜索昵称的关键字太少");
				return false;
			}
		}
		if(username){
			if(username.length <= 1){
				alert("搜索用户名的关键字太少");
				return false;
			}
		}
		return true;
	});
	
	//恢复账号
	$('table .btn').click(function(e){
		if(confirm('确定恢复该停播账号')){
			var uid = $(this).attr('uid');
			var obj = this;
			if(uid){
				$.ajax({
					url:"<?php echo $this->createUrl('dotey/stoplive');?>",
					dataType:'text',
					type:'post',
					data:{'uid':uid,'op':'restoreStopLive'},
					success:function(msg){
						if(msg == 1){
							$(obj).parents('tr').detach();
						}else{
							$('#user_list_manage_body').html(msg);
							e.preventDefault();
							$('#user_list_manage').modal('show');
						}
					}
				});
			}
		}
	});
});
</script>