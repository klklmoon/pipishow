<?php
$this->breadcrumbs = array('用户管理','封号查询');
$userRank = $this->formatUserRank();
$doteyRank = $this->formatDoteyRank();
?>
			
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('user/violation');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
			  		<span>账号:</span> 
			  		<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
			  		<span>昵称:</span> 
			  		<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
			  		<span>ID:</span> 
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
					<!-- <span>禁用时间:</span> -->
				  	<?php //echo CHtml::textField('form[start_time]',isset($condition['start_time'])?$condition['start_time']:'',array('class'=>'input-small'));?>至
				  	<?php //echo CHtml::textField('form[end_time]',isset($condition['end_time'])?$condition['end_time']:'',array('class'=>'input-small'));?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				  	<a class="btn" href="<?php echo $this->createUrl('user/violation',array('condition'=>json_encode($condition),'op'=>'dlViolationExcel'));?>" title="导出Excel">导出Excel</a>
				</div>
			  </fieldset>
			</form>   
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>UID </th>
						  <th>账号</br>昵称</th>
						  <th>操作者UID</br>账号</th>
						  <th>禁用时间</th>
						  <th>禁用原因</th>
						  <th>用户类型</th>
						  <th>用户总消费</th>
						  <th>15天消费</th>
						  <th>富豪等级</th>
						  <th>主播等级</th>
						  <th>操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($userList)){?>
				  	<?php foreach($userList as $uinfo){?>
				  	<tr>
				  		<td><?php echo $uinfo['uid'];?></td>
				  		<td><?php echo $uinfo['username'];?></br><?php echo $uinfo['nickname'];?></td>
				  		<td>
				  			<?php 
				  				if(isset($uinfo['op_uinfo'])){
				  					echo $uinfo['op_uinfo']['uid'].'</br>'.$uinfo['op_uinfo']['username'];
				  				}
				  			?>
				  		</td>
				  		<td>
				  			<?php 
				  			if(isset($uinfo['reason'])){
				  				echo date('Y-m-d H:i:s',$uinfo['reason']['op_time']);
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
				  		<td><?php echo implode('</br>',$this->userSer->checkUserType($uinfo['user_type'],true));?> </td>
				  		<td>
				  			<?php  echo $uinfo['allConsume']; ?>
				  		</td>
				  		<td>
				  			<?php  echo $uinfo['halfMonthConsume']; ?>
				  		</td>
				  		<td>
				  			<?php 
					  			if(isset($uinfo['consumeList'])){
					  				$_rank = $uinfo['consumeList']['rank'];
					  				echo $userRank[$_rank];
					  			}
				  			?>
				  		</td>
				  		<td>
				  			<?php 
					  			if(isset($uinfo['consumeList'])){
					  				$_rank = $uinfo['consumeList']['dotey_rank'];
					  				echo $doteyRank[$_rank];
					  			}
				  			?>
				  		</td>
				  		<td>
				  			<a class="btn" title="恢复账号" uid="<?php echo $uinfo['uid'];?>"><span class="icon icon-color icon-undo"></span></a>
				  		</td>
				  	</tr>
				  	<?php }?>
				  	<tr>
				  		<td colspan="11">
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
					  		<tr><td colspan="11">没有配置的数据</td></tr>
				  		</tbody>
				  	<?php }?>
				  </tbody>
			  </table>
		</div>
	</div><!--/span-->
</div>

<!-- 浮层 -->
<div class="modal hide fade" id="user_list_manage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
	</div>
	<div class="modal-body" id="user_list_manage_body">
	</div>
</div>

<script>
$(function() {
	//注册开始时间
	$( '#form_start_time' ).click(function(){
			WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm:ss'});
		}
	);
	//注册结束时间
	$( '#form_end_time' ).click(function(){
			WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm:ss'});
		}
	);

	//恢复账号
	$('.btn').click(function(e){
		var uid = $(this).attr('uid');
		var obj = this;
		if(uid){
			$.ajax({
				url:"/index.php?r=user/violation",
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
	});
	
	//搜索提交
	$('#user_search_submit').click(function(){
		var nickname = $("#form_nickname").attr('value');
		var username = $("#form_username").attr('value');
		if(nickname){
			if(nickname.length <= 2){
				alert("搜索昵称的关键字太少");
				return false;
			}
		}
		if(username){
			if(username.length <= 2){
				alert("搜索用户名的关键字太少");
				return false;
			}
		}
		return true;
	});
});
</script>