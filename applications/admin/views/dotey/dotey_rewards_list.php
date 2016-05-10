<?php
$this->breadcrumbs = array('主播管理','主播报酬管理');
$sources = $this->getProxyAndTutorListOption();
$types = $this->doteySer->getDoteyBaseStatus(true);
$whether = $this->doteySer->getWhetherDotey();
$doteyType = $this->doteySer->getDoteyType();
$userStatus = $this->userSer->getUserStatus();
$userStatus[USER_STATUS_OFF] = '停播';
$userStatus[USER_STATUS_ON] = '开播';
?>
<style type="text/css">
	.table th, .table tr, .table td, .table .btn{padding:1px;}
</style>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-user"></i> 主播报酬列表</h2>
			<div class="box-icon">
				<a href="<?php echo $this->createUrl('dotey/rewardpolicy');?>" class="btn btn-setting btn-round"><i class="icon-plus-sign" title="添加主播报酬政策"></i></a>
			</div>
		</div>
		
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('dotey/rewards');?>" method="post" enctype="multipart/form-data">
				<fieldset>
					<div class="control-group">
					<?php $select1 = isset($condition['dotey_type'])?$condition['dotey_type']:''?>
					<?php echo CHtml::listBox('form[dotey_type]', $select1, $doteyType,array('class'=>'input-small','empty'=>'主播类型','size'=>1));?>
					<?php if(!$isFilterSource){?>
					<?php $select2 = isset($condition['sources'])?$condition['sources']:''?>
					<?php echo CHtml::listBox('form[sources]', $select2, $sources,array('class'=>'input-small','empty'=>'主播来源','size'=>1));?>
					<?php }?>
					<?php $select3 = isset($condition['status'])?$condition['status']:''?>
					<?php echo CHtml::listBox('form[status]', $select3, $types ,array('class'=>'input-small','empty'=>'签约状态','size'=>1));?>
					<span>UID:</span>
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-mini'));?>
					<span>昵称:</span> 
					<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-mini'));?>
					<span>姓名:</span> 
					<?php echo CHtml::textField('form[realname]',isset($condition['realname'])?$condition['realname']:'',array('class'=>'input-mini'));?>
					<span>账号:</span> 
					<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-mini'));?>
					<span>导入UID:</span>
					<?php echo CHtml::fileField('filter_uids','',array('class'=>'input-small'));?>
				</div>
				<div class="control-group">
					<span>结算时间:</span> 
					从<?php echo CHtml::textField('form[pay_time_start]',isset($condition['pay_time_start'])?$condition['pay_time_start']:'',array('class'=>'input-small'));?>
					到<?php echo CHtml::textField('form[pay_time_end]',isset($condition['pay_time_end'])?$condition['pay_time_end']:'',array('class'=>'input-small'));?>
					<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
					<?php echo CHtml::submitButton('user_search_dlexcel',array('class'=>'btn','value'=>'导出Excel','id'=>'user_search_dlexcel'));?>
				 </div>
				 <div class="alert alert-error">
							<button data-dismiss="alert" class="close" type="button">×</button>
							<strong>提示：</strong> 导入的UID必须为txt后缀的文件，且一行只放一个uid，多个uid分为多行。
						</div>
				</fieldset>
			</form>
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>ID</br>账号</th>
						<th>昵称</br>姓名</th>
						<th>开户银行+卡号</th>
						<th>来源</th>
						<th>签约</br>状态</th>
						<th>开播</br>状态</th>
						<th>有效天</th>
						<th>小时数</th>
						<th>原始魅力点</br>有效魅力点</th>
						<th>无效魅力点</br>无效提现</th>
						<th>底薪</br>收入</th>
						<th>奖金</th>
						<th>已兑换</br>金额</th>
						<th>平台奖励</br>才艺补贴</th>
						<th>原始合计</br>有效合计</th>
					</tr>
				</thead>
				<tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $uinfo){?>
				  	<tr>
						<td><?php echo $uinfo['uid'];?></br><?php echo $uinfo['username']?></td>
						<td><?php echo $uinfo['nickname'];?></br><?php echo $uinfo['realname']?></td>
						<td><?php echo $uinfo['bank'];?></br><?php echo $uinfo['bank_account']?></td>
						<td>
							<?php 
								if ($uinfo['proxy_uid'] > 0){
									$k = DOTEY_MANAGER_PROXY.'#XX#'.$uinfo['proxy_uid'];
									echo isset($sources[$k])?$sources[$k]:'无';
									if($uinfo['tutor_uid'] > 0){
										echo "</br>";
									}
								}
								
								if ($uinfo['tutor_uid'] > 0){
									$k = DOTEY_MANAGER_TUTOR.'#XX#'.$uinfo['tutor_uid'];
									echo isset($sources[$k])?$sources[$k]:'无';
								}
							?>
						</td>
						<td><?php echo $types[$uinfo['status']];?> </td>
						<td><?php echo $userStatus[$uinfo['user_status']];?> </td>
						<td><?php echo isset($uinfo['archives']['have_days'])?$uinfo['archives']['have_days']:'0';?> </td>
						<td><?php echo isset($uinfo['archives']['have_hours'])?number_format($uinfo['archives']['have_hours']/3600,2):'0';?> </td>
						<td><?php echo isset($uinfo['archives']['old_charm_points'])?$uinfo['archives']['old_charm_points']:'0';?></br><?php echo isset($uinfo['archives']['charm_points'])?$uinfo['archives']['charm_points']:'0';?></td>
						<td><?php echo isset($uinfo['archives']['invalid_charm_points'])?$uinfo['archives']['invalid_charm_points']:'0';?></br><?php echo isset($uinfo['archives']['invalid_money'])?$uinfo['archives']['invalid_money']:'0';?></td>
						<td><?php echo isset($uinfo['archives']['basic_salary'])?$uinfo['archives']['basic_salary']:'0';?> </td>
						<td><?php echo isset($uinfo['archives']['bonus'])?$uinfo['archives']['bonus']:'0';?> </td>
						<td><?php echo isset($uinfo['transRs'])?$uinfo['transRs']:'0';?> </td>
						<td><?php echo isset($uinfo['awardRs'])?$uinfo['awardRs']:'0';?></br><?php echo isset($uinfo['artRs'])?$uinfo['artRs']:'0';?> </td>
						<td>
							<?php 
								$_t = 0;
								$_t += isset($uinfo['transRs'])?$uinfo['transRs']:'0';
								$_t += isset($uinfo['artRs'])?$uinfo['artRs']:'0';
								$_t += isset($uinfo['awardRs'])?$uinfo['awardRs']:'0';
								$_t += isset($uinfo['archives']['bonus'])?$uinfo['archives']['bonus']:'0';
								$_t += isset($uinfo['archives']['basic_salary'])?$uinfo['archives']['basic_salary']:'0';
							?> 
							<?php echo $_t;?></br>
							<?php echo $_t-$uinfo['archives']['invalid_money'];?>
						</td>
					</tr>
				  	<?php }?>
				  	<tr>
						<td colspan="16">
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
						<td colspan="16">没有配置的数据</td>
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
		<h3>主播报酬管理</h3>
	</div>
	<div class="modal-body" id="dotey_award_manage_body"></div>
</div>

<script>
$(function() {
	//报酬结算时间
	$( '#form_pay_time_start' ).datepicker(
		{
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	$( '#form_pay_time_end' ).datepicker(
			{
				showButtonPanel: true,
				changeMonth: true,
				changeYear: true,
				dateFormat:'yy-mm-dd'
			}
		);
	//下载Excel
	$('#user_search_dlexcel').click(function(){
		var action = $(this).parents('form').attr('action');
		$(this).parents('form').attr('action',action+"&op=dlRewardsExcel");
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
	//查询
	$('#user_search_submit').click(function(){
		$(this).parents('form').attr('action','<?php echo $this->createUrl('dotey/rewards');?>');
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