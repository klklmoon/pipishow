<?php
$this->breadcrumbs = array('主播管理','直播数据');
?>
<style type="text/css">
	.table td, .table th {padding:2px;}
</style>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('dotey/liveonline');?>" method="post">
				<fieldset>
					<div class="control-group">
					<span>UID:</span>
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
					<span>昵称:</span> 
					<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
					<span>姓名:</span> 
					<?php echo CHtml::textField('form[realname]',isset($condition['realname'])?$condition['realname']:'',array('class'=>'input-small'));?>
					<span>账号:</span> 
					<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
					<span>直播时间:</span>
				  	<?php echo CHtml::textField('form[live_time_on]',isset($condition['live_time_on'])?$condition['live_time_on']:'',array('class'=>'date_ui input-small'));?>至
				  	<?php echo CHtml::textField('form[live_time_end]',isset($condition['live_time_end'])?$condition['live_time_end']:'',array('class'=>'date_ui input-small'));?>
				  	<span>去重:</span> 
				  	<?php echo CHtml::checkBox('form[remDuplicate]',isset($condition['remDuplicate'])?true:false);?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索'));?>
				  	<?php if(isset($condition['remDuplicate'])){?>
					  	<?php echo CHtml::button('user_search',array('class'=>'btn','value'=>'主播合计：'.$pager->getItemCount()));?>
				  	<?php }else{?>
					  	<?php echo CHtml::button('user_search',array('class'=>'btn','value'=>'直播合计：'.$pager->getItemCount()));?>
				  	<?php }?>
				</div>
				</fieldset>
			</form>
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>档期</br>ID</th>
						<th>主播昵称</th>
						<th>节目名</th>
						<th>送礼</br>人数</th>
						<th>皮蛋</br>收入</th>
						<th>人均</br>单价</th>
						<th>直播时</br>长/h</th>
						<th>直播总次数</th>
					</tr>
				</thead>
				<tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $uinfo){?>
				  	<tr>
						<td><?php echo $uinfo['archives_id'];?></td>
						<td>
							<?php 
								$nickname = isset($sessStatInfo[$uinfo['archives_id']]['nickname'])?$sessStatInfo[$uinfo['archives_id']]['nickname']:'';
								echo $nickname;	
							?>
						</td>
						<td><?php echo $uinfo['title'];?></td>
						<td>
							<?php 
								$sendTotal = isset($sessStatInfo[$uinfo['archives_id']]['send_total'])?$sessStatInfo[$uinfo['archives_id']]['send_total']:'';
								echo $sendTotal;
							?>
						</td>
						<td>
							<?php 
								$consume_many = isset($sessStatInfo[$uinfo['archives_id']]['consume_many'])?$sessStatInfo[$uinfo['archives_id']]['consume_many']:'';
								echo $consume_many;
							?>
						</td>
						<td>
							<?php 
								$send_avg = isset($sessStatInfo[$uinfo['archives_id']]['send_avg'])?$sessStatInfo[$uinfo['archives_id']]['send_avg']:'';
								echo $send_avg;
							?>
						</td>
						<td>
							<?php 
								echo number_format($uinfo['duration']/3600,2);
							?>
						</td>
						<td><?php echo $uinfo['recordCount'];?></td>
					</tr>
				  	<?php }?>
				  	<tr>
						<td colspan="9">
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
						<td colspan="9">没有配置的数据</td>
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
		<h3>用户管理</h3>
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
	//结束直播
	$(".icon-ban-circle").click(function(e){
		var recordId = $(this).parent('a').attr('recordId');
		if(recordId){
			$.ajax({
				url:"<?php echo $this->createUrl('dotey/onlive');?>",
				dataType:'text',
				type:'post',
				data:{'record_id':recordId,'op':'changeLiveStatus'},
				success:function(msg){
					if(msg == 1){
						var statusId = "#status_"+recordId;
						$(statusId).html('直播结束');
					}else{
						$('#user_list_manage_body').html(msg);
						e.preventDefault();
						$('#user_list_manage').modal('show');
					}
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
	$(':submit').click(function(){
		var nickname = $("#form_nickname").attr('value');
		var username = $("#form_username").attr('value');
		var realname = $("#form_realname").attr('value');
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

		if(realname){
			if(realname.length <= 1){
				alert("搜索姓名的关键字太少");
				return false;
			}
		}
		return true;
	});
});
</script>