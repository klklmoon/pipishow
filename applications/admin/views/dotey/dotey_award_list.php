<?php
$this->breadcrumbs = array('主播管理','平台奖励');
$types = $this->getAwardType();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-user"></i> 平台奖励列表</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" title="新增平台奖励"><i class="icon-plus-sign"></i></a>
			</div>
		</div>
		
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('dotey/award');?>" method="post">
				<fieldset>
					<div class="control-group">
					<span>奖励类型:</span> 
					<?php $select1 = isset($condition['type'])?$condition['type']:''?>
					<?php echo CHtml::listBox('form[type]', $select1, $types,array('class'=>'input-small','empty'=>' ','size'=>1));?>
					<span>状态:</span> 
					<?php $select1 = isset($condition['status'])?$condition['status']:''?>
					<?php echo CHtml::listBox('form[status]', $select1, array('1'=>'已处理','2'=>'已撤销'),array('class'=>'input-small','empty'=>' ','size'=>1));?>
					<span>UID:</span>
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-mini'));?>
					<span>昵称:</span> 
					<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-mini'));?>
					<span>姓名:</span> 
					<?php echo CHtml::textField('form[realname]',isset($condition['realname'])?$condition['realname']:'',array('class'=>'input-mini'));?>
					<span>账号:</span> 
					<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-mini'));?>
					<span>奖励时间:</span>
					<?php echo CHtml::textField('form[create_time_on]',isset($condition['create_time_on'])?$condition['create_time_on']:'',array('class'=>'date_ui input-small'));?>&nbsp;至&nbsp;
					<?php echo CHtml::textField('form[create_time_end]',isset($condition['create_time_end'])?$condition['create_time_end']:'',array('class'=>'date_ui input-small'));?>
					<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
				</fieldset>
			</form>
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>奖励时间</th>
						<th>账号(ID)</th>
						<th>姓名</th>
						<th>奖励</th>
						<th>奖励理由</th>
						<th>状态</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $uinfo){?>
				  	<tr>
						<td><?php echo $uinfo['create_time'];?></td>
						<td><?php echo $doteyInfo[$uinfo['uid']]['username'];?>(<?php echo $uinfo['uid'];?>) </td>
						<td><?php echo $doteyInfo[$uinfo['uid']]['realname'];?> </td>
						<td><?php echo $uinfo['quantity'];?></td>
						<td><?php echo $uinfo['reason'];?> </td>
						<td><?php echo $uinfo['status'];?> </td>
						<td><?php if($uinfo['isclick']){?><span class="icon icon-color icon-redo" title="撤销奖励" type="<?php echo $uinfo['type'];?>" recordId="<?php echo $uinfo['record_id'];?>"></span><?php }?></td>
					</tr>
				  	<?php }?>
				  	<tr>
						<td colspan="7">
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
						<td colspan="7">没有配置的数据</td>
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
<div class="modal hide fade" id="dotey_award_manage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>新增平台奖励</h3>
	</div>
	<div class="modal-body" id="dotey_award_manage_body"></div>
</div>

<script>
$(function() {
	//注册开始时间
	$( '#form_create_time_on' ).datepicker(
		{ 
			//showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//注册开始时间
	$( '#form_create_time_end' ).datepicker(
		{ 
			//showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//搜索提交
	$('#user_search_submit').click(function(){
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
	//新增平台奖励
	$('.icon-plus-sign').click(function(e){
		$.ajax({
			url:"<?php echo $this->createUrl('dotey/addaward');?>",
			type:'post',
			dataType:'html',
			success:function(msg){
				e.preventDefault();
				$('#dotey_award_manage_body').html(msg);
				$('#dotey_award_manage').modal('show');
			}
		});
	});
	//撤消操作
	$('.icon-redo').click(function(e){
		if(confirm('是否确定撤消该平台奖励！')){
			var type = $(this).attr('type');
			var recordId = $(this).attr('recordId');
			var obj = this;
			$(obj).hide();
			if(type >= 0 && recordId){
				$.ajax({
					url:"<?php echo $this->createUrl('dotey/addaward');?>",
					type:'post',
					dataType:'text',
					data:{'type':type,'recordId':recordId,'op':'unAward'},
					success:function(msg){
						if(msg == 1){
							$(obj).parents('tr').children('td:eq(-2)').html('已撤销');
							$(obj).detach();
						}else{
							$(obj).show();
							e.preventDefault();
							$('#dotey_award_manage_body').html(msg);
							$('#dotey_award_manage').modal('show');
						}
					}
				});
			}
		}
	});
});
</script>