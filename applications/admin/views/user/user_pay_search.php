<?php
$this->breadcrumbs = array('用户管理','违规查询');
$userRank = $this->formatUserRank();
$doteyRank = $this->formatDoteyRank();
?>		
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="" method="post">
			  <fieldset>
			  	<div class="control-group">
			  		<span>账号:</span> 
			  		<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
			  		<span>昵称:</span> 
			  		<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
			  		<span>UID:</span> 
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
					<?php echo CHtml::listBox('form[user_rank]', isset($condition['user_rank'])?$condition['user_rank']:'',$userRank,array('size'=>1,'empty'=>'用户等级','class'=>'input-small'));?>
					<?php echo CHtml::listBox('form[dotey_rank]', isset($condition['dotey_rank'])?$condition['dotey_rank']:'',$doteyRank,array('size'=>1,'empty'=>'主播等级','class'=>'input-small'));?>
				  	<?php echo CHtml::submitButton('dl_excel',array('class'=>'btn','value'=>'导出Excel','id'=>'dl_excel'));?>
				</div>
				<div class="control-group">
				  	<span>付费时间:</span>
				  	<?php echo CHtml::textField('form[start_time]',isset($condition['start_time'])?$condition['start_time']:'',array('class'=>'input-medium'));?>至
				  	<?php echo CHtml::textField('form[end_time]',isset($condition['end_time'])?$condition['end_time']:'',array('class'=>'input-medium'));?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
			  </fieldset>
			</form>   
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>UID </th>
						  <th>账号</br>昵称</th>
						  <th>注册时间</th>
						  <th>富豪</br>等级</th>
						  <th>主播</br>等级</th>
						  <th>用户类型</th>
						  <th>累计充值</th>
						  <th>累计消费</th>
						  <th>15天内</br>充值数</th>
						  <th>15天内</br>充消费</th>
						  <th>有效范围</br>内的充值</th>
						  <th>有效范围</br>内总消费</th>
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
				  			if(isset($uinfo['create_time'])){
				  				echo date('Y-m-d H:i:s',$uinfo['create_time']);
				  			}
				  			?>
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
				  		<td><?php echo implode('</br>',$this->userSer->checkUserType($uinfo['user_type'],true));?> </td>
				  		<td><?php echo $uinfo['allCash'];?></td>
				  		<td><?php echo $uinfo['allConsume'];?></td>
				  		<td><?php echo $uinfo['halfMonthCash'];?></td>
				  		<td><?php echo $uinfo['halfMonthConsume'];?></td>
				  		<td><?php echo $uinfo['rangeCash'];?></td>
				  		<td><?php echo $uinfo['rangeConsume'];?></td>
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
var actionUrl = "<?php echo $this->createUrl('user/paysearch');?>";							
$(function() {
	//注册开始时间
	$( '#form_start_time' ).click(function(){
			WdatePicker();
		}
	);
	//注册结束时间
	$( '#form_end_time' ).click(function(){
			WdatePicker();
		}
	);

	$(':submit').click(function(){
		$(this).parents('form').attr('action',actionUrl);

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
		
		$(this).submit();
	});
	
	$('#dl_excel').click(function(){
		$(this).parents('form').attr('action',actionUrl+'&op=dlPaySearchExcel');
		this.submit();
	});
});
</script>