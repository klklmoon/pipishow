<?php
$this->breadcrumbs = array('主播管理','主播查询');
$doteyStatus = $this->doteySer->getDoteyBaseStatus();
$doteySignType = $this->doteySer->getDoteySignType();
$doteyRegSource = $this->userSer->getUserRegSource();
$doteyTypes = $this->getProxyAndTutorListOption();
$doteySource = $this->doteySer->getDoteyType();
$isHide = $this->getArchivesIsHide();
$userStatus = $this->userSer->getUserStatus();
$userStatus[USER_STATUS_OFF] = '停播';
$userStatus[USER_STATUS_ON] = '开播';
?>
<style type="text/css">
	.table td, .table th {padding:1px;}
</style>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('dotey/doteylist');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
			  		<?php 
			  		if(!$isFilterSource){
				  		echo CHtml::listBox('form[dotey_type]',$condition['dotey_type'],$doteyTypes,array('class'=>'input-small','size'=>1,'empty'=>'-主播类型-'));
			  		} 
			  		?>
				  	<?php echo CHtml::listBox('form[user_status]',isset($condition['user_status'])?$condition['user_status']:'',$userStatus,array('class'=>'input-small','size'=>1,'empty'=>'-开播/停播-'));?>
				  	<?php echo CHtml::listBox('form[is_hide]',isset($condition['is_hide'])?$condition['is_hide']:'',$isHide,array('class'=>'input-small','size'=>1,'empty'=>'-是否显示-'));?>
				  	<?php echo CHtml::listBox('form[sources]',isset($condition['sources'])?$condition['sources']:'',$doteyRegSource,array('class'=>'input-small','size'=>1,'empty'=>'-推广来源-'));?>
			  		<span>用户名</span> 
			  		<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
			  		<span>UID</span> 
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
					<span>昵称</span> 
					<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
					<span>姓名</span> 
					<?php echo CHtml::textField('form[realname]',isset($condition['realname'])?$condition['realname']:'',array('class'=>'input-small'));?>
					<a class="btn" href="<?php echo $this->createUrl('dotey/doteylist',array('condition'=>json_encode($condition),'op'=>'dlDoteyListExcel'));?>" title="导出Excel">导出Excel</a>
				</div>
				<div class="control-group">
					<span>节目名称</span>
					<?php echo CHtml::textField('form[archives_title]',isset($condition['archives_title'])?$condition['archives_title']:'',array('class'=>'input-small'));?>
				  	<span>注册时间</span>
				  	<?php echo CHtml::textField('form[create_time_start]',isset($condition['create_time_start'])?$condition['create_time_start']:'',array('class'=>'date_ui input-small'));?>至
				  	<?php echo CHtml::textField('form[create_time_end]',isset($condition['create_time_end'])?$condition['create_time_end']:'',array('class'=>'date_ui input-small'));?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
			  </fieldset>
			</form>   
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>用户名-UID</br>昵称-真实姓名</th>
						  <th>注册时间 </th>
						  <th>用户类型</th>
						  <th>节目名</br>来源类型</th>
						  <th>导师来源</th>
						  <th>家族</th>
						  <th>魅力值</br>魅力点</th>
						  <th>隐藏</th>
						  <th>签约</br>状态</th>
						  <th>主播</br>等级</th>
						  <th>开播</br>状态</th>
						  <th>推广</br>来源</th>
						  <th>开播</br>总时长</th>
						  <th>开播<br>总数</th>
						  <th>初次开播</br>最近开播</th>
						  <th>操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $uinfo){?>
				  	<tr>
				  		<td><?php echo $uinfo['realname']?$uinfo['realname']:'null';?>-<?php echo $uinfo['uid'];?></br><?php echo $uinfo['nickname'];?>-<?php echo $uinfo['realname'];?></td>
				  		<td><?php echo date('Y-m-d',$uinfo['create_time']);?></td>
				  		<td>
				  			<?php 
				  				echo implode('</br>', $this->userSer->checkUserType($uinfo['user_type'],true)) ;
				  			?> 
				  		</td>
				  		<td>
				  			<?php
				  				if(isset($uinfo['archivesInfo'])){
				  					echo $uinfo['archivesInfo']['title'];
				  				}
				  				//echo implode('</br>', $this->doteySer->checkSignType($uinfo['sign_type'],true)) ;
				  			?>
				  			<?php echo '</br>'.$doteySource[$uinfo['dotey_type']];?>
				  		</td>
				  		<td>
				  			<?php 
				  				if($uinfo['proxy_uid']){
				  					$_dk = DOTEY_MANAGER_PROXY.'#XX#'.$uinfo['proxy_uid'];
				  					echo isset($doteyTypes[$_dk])?$doteyTypes[$_dk]:'';
				  				}
				  				if($uinfo['proxy_uid'] && $uinfo['tutor_uid']){
				  					echo "</br>";
				  				}
				  				if ($uinfo['tutor_uid']){
				  					$_dk = DOTEY_MANAGER_TUTOR.'#XX#'.$uinfo['tutor_uid'];
				  					echo isset($doteyTypes[$_dk])?$doteyTypes[$_dk]:'';
				  				}
				  			?>
				  		</td>
				  		<td>
				  			<?php 
				  				if(!empty($uinfo['family'])){
				  					$finfo = '';
				  					foreach($uinfo['family'] as $family){
										$finfo .= $family['family_name'].'</br>';				  						
				  					}
				  					$finfo = trim($finfo,'</br>');
				  					echo $finfo;
				  				}
				  			?>
				  		</td>
				  		<td>
				  			<?php 
				  				if(isset($uinfo['consumeInfo'])){
				  					echo $uinfo['consumeInfo']['charm']?$uinfo['consumeInfo']['charm']:'null';
				  					echo "</br>";
				  					echo $uinfo['consumeInfo']['charm_points']?$uinfo['consumeInfo']['charm_points']:'null';
				  				}
				  			?>
				  		</td>
				  		<td>
				  			<?php
				  				if(isset($uinfo['archivesInfo'])){
				  					echo $isHide[$uinfo['archivesInfo']['is_hide']];
				  				}
				  			?>
				  		</td>
				  		<td><?php echo $doteyStatus[$uinfo['status']];?> </td>
				  		<td>
				  			<?php 
				  				if(isset($uinfo['consumeInfo'])){
				  					echo $uinfo['consumeInfo']['dotey_rank'];
				  				}
				  			?>
				  		</td>
				  		<td><?php echo $userStatus[$uinfo['user_status']];?> </td>
				  		<td><?php echo $doteyRegSource[$uinfo['reg_source']];?> </td>
				  		<td>
				  			<?php 
				  				if(isset($uinfo['liveStatitics'])){
				  					echo $uinfo['liveStatitics']['sum_duration'];
				  				}
				  			?>
				  		</td>
				  		<td>
				  			<?php 
				  				if(isset($uinfo['liveStatitics'])){
				  					echo $uinfo['liveStatitics']['count_lives'];
				  				}
				  			?>
				  		</td>
				  		<td>
				  			<?php 
				  				if(isset($uinfo['liveStatitics'])){
				  					echo date('Y-m-d',$uinfo['liveStatitics']['first_live_time']);
				  					echo "</br>";
				  					echo date('Y-m-d',$uinfo['liveStatitics']['last_live_time']);
				  				}
				  			?>
				  		</td>
				  		<td>
				  			<a  href="#" uid="<?php echo $uinfo['uid'];?>" title="编辑"> <i class="icon-edit"></i></a>
				  			<a  href="<?php echo $this->createUrl('dotey/doteyingift',array('uid'=>$uinfo['uid']));?>" title="收礼明细记录"> <i class="icon-gift"></i> </a>
				  			<a  href="#" uid="<?php echo $uinfo['uid'];?>" title="重置密码"> <i class="icon-lock"></i></a>
				  			<a  href="#" uid="<?php echo $uinfo['uid'];?>" title="家族修订" class="btn btn-mini family"> F</a>
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
					  		<tr><td colspan="16">没有配置的数据</td></tr>
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

<!-- 重置密码 -->
<div class="hide" id="resetPwd">
	<div class="box-content form-horizontal">
		<div class="control-group">
		  <?php echo CHtml::label('新密码','new_pwd',array('class'=>'control-label'));?>
		  <div class="controls">
		  	<?php echo CHtml::passwordField('new_pwd','',array('class'=>'input-small'));?>
		  	<span class="label label-important" style="margin-left:10px;display:none;">密码不能为空</span>
		  </div>
		</div>
		<div class="control-group">
		  <?php echo CHtml::label('确认密码','confirm_new_pwd',array('class'=>'control-label'));?>
		  <div class="controls">
		  	<?php echo CHtml::passwordField('confirm_new_pwd','',array('class'=>'input-small'));?>
		  	<span class="label label-important" style="margin-left:10px;display:none;">请确认密码</span>
		  </div>
		</div>
		<div class="control-group">
		  <div class="controls" id="uidflag">
		  	<?php echo CHtml::button('button',array('class'=>'btn','value'=>'确认','id'=>'confirm_button_reset'));?>
		  	<span class="label label-important" style="margin-left:10px;display:none;">两次密码不一样，请重新输入</span>
		  </div>
		</div>
	</div>
	<script>
	//重置密码
	$('#confirm_button_reset').click(function(e){
		var new_pwd = $('#new_pwd').attr('value');
		var confirm_new_pwd = $('#confirm_new_pwd').attr('value');
		var uid = $(this).attr('uid');

		if(uid){
			if(!new_pwd){
				$('#new_pwd').next('span').show();
				return false;
			}else{
				$('#new_pwd').next('span').hide();
			}
			if(!confirm_new_pwd){
				$('#confirm_new_pwd').next('span').show();
				return false;
			}else{
				$('#confirm_new_pwd').next('span').hide();
			}
			if(new_pwd != confirm_new_pwd ){
				$(this).next('span').show();
				return false;
			}else{
				$(this).next('span').hide();
			}

			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('user/resetpwd');?>",
				dataType:'html',
				data:{'uid':uid,'new_pwd':new_pwd,'confirm_new_pwd':confirm_new_pwd},
				success:function(msg){
					$('#user_list_manage_body').html(msg);
					e.preventDefault();
					$('#user_list_manage').modal('show');
				}
			});
		}
	});
	</script>
</div>

<script>
$(function() {
	//注册开始时间
	$( '#form_create_time_start' ).datepicker(
		{ 
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//注册结束时间
	$( '#form_create_time_end' ).datepicker(
		{ 
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//编辑操作
	$(".box-content .icon-edit").click(function(e){
		var uid = $(this).parents('a').attr('uid');
		if(uid){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('dotey/editdotey');?>",
				dataType:'html',
				data:{'uid':uid},
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
	//家族操作
	$(".family").click(function(e){
		var uid = $(this).attr('uid');
		if(uid){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('dotey/editFamily');?>",
				dataType:'html',
				data:{'uid':uid,'type':'dotey'},
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
	//重置密码输入框
	$(".icon-lock").click(function(e){
		var uid = $(this).parents('a').attr('uid');
		if(uid){
			$('#confirm_button_reset').attr('uid',uid);
			$('#user_list_manage_body').html($('#resetPwd').html());
			e.preventDefault();
			$('#user_list_manage').modal('show');
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
});
</script>