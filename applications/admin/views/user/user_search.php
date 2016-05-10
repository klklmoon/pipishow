<?php
$this->breadcrumbs = array('用户管理','用户查询');
$userRks = $this->getUserRk();
$doteyRks = $this->getDoteyRk();
?>
			
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('user/usersearch');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
			  		<span>用户名:</span> 
			  		<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-mini'));?>
			  		<span>昵称:</span> 
			  		<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-mini'));?>
			  		<span>UID:</span> 
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-mini'));?>
					<span>注册IP:</span> 
					<?php echo CHtml::textField('form[reg_ip]',isset($condition['reg_ip'])?$condition['reg_ip']:'',array('class'=>'input-mini'));?>
					<span>IP次数:</span> 
					<?php echo CHtml::textField('form[reg_ip_count]',isset($condition['reg_ip_count'])?$condition['reg_ip_count']:'不限',array('class'=>'input-mini'));?>
					<?php echo CHtml::listBox('form[user_type]', isset($condition['user_type'])?$condition['user_type']:'', $userType,array('size'=>1,'class'=>'input-small','empty'=>'-用户类型-'));?>
					<?php echo Chtml::listBox('form[user_status]', isset($condition['user_status'])?$condition['user_status']:'', $userStatus,array('size'=>1,'class'=>'input-small','empty'=>'-状态-'));?>
				</div>
				<div class="control-group">
				  	<span>注册时间:</span>
				  	<?php echo CHtml::textField('form[start_time]',isset($condition['start_time'])?$condition['start_time']:'',array('class'=>'date_ui input-small'));?>至
				  	<?php echo CHtml::textField('form[end_time]',isset($condition['end_time'])?$condition['end_time']:'',array('class'=>'date_ui input-small'));?>
				  	<span>绑定手机:</span>
				  	<?php echo CHtml::textField('form[bind_tel]',isset($condition['bind_tel'])?$condition['bind_tel']:'',array('class'=>'input-small'));?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				  	<?php echo CHtml::button('user_count',array('class'=>'btn','value'=>'合计：'.$pager->getItemCount()));?>
				</div>
			  </fieldset>
			</form>   
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
					  	  <th><?php echo CHtml::checkBox('check_uid')?></th>
						  <th>UID</th>
						  <th>用户名</br>昵称</th>
						  <th>真实姓名 </th>
						  <th>用户类型</th>
						  <th>家族</th>
						  <th>总消费</br>总充值</th>
						  <th>富豪等级</br>魅力等级</th>
						  <th>贡献值</br>魅力值</th>
						  <th>是否禁用</th>
						  <th>注册时间</br>注册IP</th>
						  <th>绑定手机</br>绑定邮件</th>
						  <th>操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($userList)){?>
				  	<?php foreach($userList as $uinfo){?>
				  	<tr>
				  		<td><?php echo CHtml::checkBox('uids[]',false,array('value'=>$uinfo['uid']));?></td>
				  		<td><?php echo $uinfo['uid'];?></td>
				  		<td><?php echo $uinfo['username'];?></br><?php echo $uinfo['nickname'];?></td>
				  		<td><?php echo $uinfo['realname'];?></td>
				  		<td><?php echo implode('</br>',$this->userSer->checkUserType($uinfo['user_type'],true));?> </td>
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
				  			<?php echo isset($comsumeList[$uinfo['uid']])?$comsumeList[$uinfo['uid']]['consume_pipiegg']:'0';?> </br>
				  			<?php echo isset($comsumeList[$uinfo['uid']])?$comsumeList[$uinfo['uid']]['consume_pipiegg']+$comsumeList[$uinfo['uid']]['pipiegg']+$comsumeList[$uinfo['uid']]['freeze_pipiegg']:'0';?>
				  		</td>
				  		<td>
				  			<?php echo isset($comsumeList[$uinfo['uid']])?$userRks[$comsumeList[$uinfo['uid']]['rank']]['name']:'平民';?> </br>
				  			<?php echo isset($comsumeList[$uinfo['uid']])?$doteyRks[$comsumeList[$uinfo['uid']]['dotey_rank']]['name']:'新人';?>
				  		</td>
				  		<td>
				  			<?php echo isset($comsumeList[$uinfo['uid']])?$comsumeList[$uinfo['uid']]['dedication']:0;?> </br>
				  			<?php echo isset($comsumeList[$uinfo['uid']])?$comsumeList[$uinfo['uid']]['charm']:0;?>
				  		</td>
				  		<td><?php echo isset($userStatus[$uinfo['user_status']])?$userStatus[$uinfo['user_status']]:'';?></td>
				  		<td><?php echo date('Y-m-d H:i:s',$uinfo['create_time']);?></br><?php echo $uinfo['reg_ip'];?></td>
				  		<td><?php echo $uinfo['reg_mobile'];?><br/><?php echo $uinfo['reg_email'];?></td>
				  		<td>
				  			<a class="btn btn-mini" href="#" uid="<?php echo $uinfo['uid'];?>" title="编辑"> <i class="icon-edit"></i></a>
				  			<a class="btn btn-mini" href="<?php echo $this->createUrl('user/ugiftrecords',array('uid'=>$uinfo['uid']));?>" title="送礼明细记录"> <i class="icon-gift"></i> </a>
				  			<a class="btn btn-mini" href="#" uid="<?php echo $uinfo['uid'];?>" title="重置密码"> <i class="icon-lock"></i> </a>
				  			<a class="btn btn-mini family" href="#" uid="<?php echo $uinfo['uid'];?>" title="家族修订"><span class="icon icon-color icon-home"></span></a>
				  			<a class="btn btn-mini bind_user" href="javascript:void(1);" title="手机绑定操作" uid="<?php echo $uinfo['uid'];?>"><span class="icon icon-color icon-locked"></span></a>
				  			<?php if(isset($extends[$uinfo['uid']]) && $extends[$uinfo['uid']]['login_verify']){?>
				  			<a class="btn btn-mini login_verify" href="javascript:void(1);" title="关闭登陆验证" uid="<?php echo $uinfo['uid'];?>"><span class="icon icon-color icon-cancel"></span></a>
				  			<?php }else{?>
				  			<a class="btn btn-mini login_verify" href="javascript:void(1);" title="开启登陆验证" uid="<?php echo $uinfo['uid'];?>"><span class="icon icon-color icon-comment-text"></span></a>
				  			<?php }?>
				  		</td>
				  	</tr>
				  	<?php }?>
				  	<tr>
				  		<td colspan="13">
				  			<a class="btn" href="javascript:void(0);" id="batch_us">批量封号</a>
				  		</td>
				  	</tr>
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
					  		<tr><td colspan="13">没有配置的数据</td></tr>
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
		<h3>用户管理</h3>
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
				data:{'uid':uid,'new_pwd':$.md5(new_pwd),'confirm_new_pwd':$.md5(confirm_new_pwd)},
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
	$( '#form_start_time' ).datepicker(
		{ 
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//注册结束时间
	$( '#form_end_time' ).datepicker(
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
				url:"<?php echo $this->createUrl('user/uinfoedit');?>",
				dataType:'html',
				data:{'uid':uid,'op':'showUinfo'},
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
			if(nickname.length < 2){
				alert("搜索昵称的关键字太少");
				return false;
			}
		}
		if(username){
			if(username.length < 2){
				alert("搜索用户名的关键字太少");
				return false;
			}
		}
		return true;
	});
	//全选，反选
	$('#check_uid').click(function(){
		$('input[name="uids[]"]').each(function(i){
			var isChecked = $(this).attr('checked');
			if(isChecked){
				$(this).removeAttr('checked');
			}else{
				$(this).attr('checked','checked');
			}
		});
	});
	//批量封号
	$('#batch_us').click(function(e){
		var uids = new Array();
		$('input[name="uids[]"]:checked').each(function(i){
			uids[i] = $(this).val();
		});
		if(uids.length > 0){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('user/uinfoedit');?>",
				dataType:'text',
				data:{'uid':uids,'op':'batchUpdateUS'},
				success:function(msg){
					if(msg == 1){
						$('#user_list_manage_body').html('批量封号成功');
					}else{
						$('#user_list_manage_body').html(msg);
					}
					e.preventDefault();
					$('#user_list_manage').modal('show');
				}
			});
		}else{
			$('#user_list_manage_body').html('请选择批量封号的用户');
			e.preventDefault();
			$('#user_list_manage').modal('show');
		}
	});
	$(':submit').click(function(){
		var ipCount = $('#form_reg_ip_count').attr('value');
		var ip = $('#form_reg_ip').attr('value');
		if(!isNaN(ipCount) && !ip){
			alert('ip地址不能为空');
			return false;
		}
		return true;
	});
	//家族操作
	$(".family").click(function(e){
		var uid = $(this).attr('uid');
		if(uid){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('dotey/editFamily');?>",
				dataType:'html',
				data:{'uid':uid,'type':'user'},
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
	//手机绑定解绑操作
	$(".bind_user").click(function(e){
		var uid = $(this).attr('uid');
		if(uid){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('user/usersearch');?>",
				dataType:'html',
				data:{'uid':uid,'op':'bind'},
				success:function(msg){
					$('#user_list_manage_body').html(msg);
					e.preventDefault();
					$('#user_list_manage').modal('show');
				}
			});
		}
	});
	//登陆验证开关
	$(".login_verify").click(function(e){
		var uid = $(this).attr('uid');
		if(uid && confirm('确认开启/关闭用户的登陆验证功能么？')){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('user/usersearch');?>",
				dataType:'html',
				data:{'uid':uid,'op':'loginVerify'},
				success:function(msg){
					if(msg != 1){
						alert(msg);
					}else{
						window.location.href="<?php echo $this->createUrl('user/usersearch',array('uid'=>''));?>"+uid;
					}
				}
			});
		}
	});
});
</script>