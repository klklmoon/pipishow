<?php
$this->breadcrumbs = array('家族管理','家族申请');
$status = FamilyService::getFamilyStatus();
$sign = FamilyService::getFamilySign();
$forbidden = FamilyService::getFamilyForbidden();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('family/apply');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
				  	<?php echo CHtml::listBox('form[status]',isset($condition['status'])?$condition['status']:'',$status,array('class'=>'input-small','size'=>1,'empty'=>'-申请状态-'));?>
				  	<?php echo CHtml::listBox('form[sign]',isset($condition['sign'])?$condition['sign']:'',$sign,array('class'=>'input-small','size'=>1,'empty'=>'-是否签约-'));?>
				  	<?php echo CHtml::listBox('form[forbidden]',isset($condition['forbidden'])?$condition['forbidden']:'',$forbidden,array('class'=>'input-small','size'=>1,'empty'=>'-启用状态-'));?>
			  		<span>用户名</span> 
			  		<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
			  		<span>UID</span> 
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
					<span>昵称</span> 
					<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
					<span>姓名</span> 
					<?php echo CHtml::textField('form[realname]',isset($condition['realname'])?$condition['realname']:'',array('class'=>'input-small'));?>
				</div>
				<div class="control-group">
					<span>家族名称</span>
					<?php echo CHtml::textField('form[name]',isset($condition['name'])?$condition['name']:'',array('class'=>'input-small'));?>
				  	<span>申请时间</span>
				  	<?php echo CHtml::textField('form[create_time_start]',isset($condition['create_time_start'])?$condition['create_time_start']:'',array('class'=>'input-small'));?>至
				  	<?php echo CHtml::textField('form[create_time_end]',isset($condition['create_time_end'])?$condition['create_time_end']:'',array('class'=>'input-small'));?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
			  </fieldset>
			</form>   
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>用户名(UID)</br>昵称(真实姓名)</th>
						  <th>用户类型</th>
						  <th width="120px;">家族名称</th>
						  <th>徽章名称</th>
						  <th>家族等级</th>
						  <th>是否签约</th>
						  <th>是否禁封</th>
						  <th>审核状态</th>
						  <th>申请时间</br>审核时间</th>
						  <th>操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php 
				  		if(!empty($list['list'])){
				  		foreach($list['list'] as $v){
				  			if(!isset($uinfos[$v['uid']]))
				  				continue;
				  			$uinfo = $uinfos[$v['uid']];
				  	?>
				  	<tr id="family_list_<?php echo $v['id'];?>">
				  		<td><?php echo $uinfo['realname'];?>(<?php echo $uinfo['uid'];?>)</br><?php echo $uinfo['nickname'];?>(<?php echo $uinfo['realname'];?>)</td>
				  		<td><?php echo implode('</br>',$this->userService->checkUserType($uinfo['user_type'],true));?> </td>
				  		<td><?php echo $v['name'];?></td>
				  		<td><?php echo $v['medal'];?></td>
				  		<td>
				  			<span style="cursor:pointer;" class="label label-warning opList" familyId="<?php echo $v['id'];?>" type="opLevel">
				  			等级<?php echo $v['level'];?>
				  			</span>
				  		</td>
				  		<td>
				  			<span style="cursor:pointer;" class="label label-success">
				  				<?php echo $sign[$v['sign']];?>
				  			</span>
				  		</td>
				  		<td>
				  			<span style="cursor:pointer;" class="label label-warning opList" familyId="<?php echo $v['id'];?>" type="opForbidden">
				  			<?php echo $forbidden[$v['forbidden']];?>
				  			</span>
				  		</td>
				  		<td>
				  			<span style="cursor:pointer;" class="label label-important opList" familyId="<?php echo $v['id'];?>"  type="opStatus">
				  			<?php echo $status[$v['status']];?>
				  			</span>
				  		</td>
				  		<td><?php echo date('Y-m-d H:i:s',$v['create_time']);?></br><?php echo empty($v['update_time'])?'':date('Y-m-d H:i:s',$v['update_time']);?></td>
				  		<td>
				  			<a  href="javascript:void(1);" familyId="<?php echo $v['id'];?>" title="编辑"  class="family_edit"> <i class="icon-edit"></i></a>
				  			<a  href="javascript:void(1);" title="解散家族" familyId="<?php echo $v['id'];?>" class="opList" type="opDisband"><span class="icon icon-color icon-unlocked"></span></a>
				  			<a  href="javascript:void(1);" title="家族转让" familyId="<?php echo $v['id'];?>" class="opList" type="opTrans"><span class="icon icon-color icon-transfer-ew"></span></a>
				  			<a  href="<?php echo $this->createUrl('family/live',array('familyId'=>$v['id']));?>" title="家族主播开播记录" target="_blank"><i class="icon-th-list"></i></a>
				  			<a  href="<?php echo $this->createUrl('family/income',array('familyId'=>$v['id']));?>" title="家族主播收入" target="_blank"><i class="icon-list-alt"></i></a>
				  		</td>
				  	</tr>
				  	<?php }?>
				  	<tr>
				  		<td colspan="10">
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
					  		<tr><td colspan="10">没有配置的数据</td></tr>
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
	$('#form_create_time_start').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm:ss'});
	});
	$('#form_create_time_end').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm:ss'});
	});

	$('.family_edit').live('click',function(e){
		var familyId = $(this).attr('familyId');
		if(familyId){
			if(!confirm('你确定要这样做吗')){
				e.preventDefault();
				$('#user_list_manage').modal('hide');
				return false;
			}

			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('family/apply',array('op'=>'editApply'));?>",
				dataType:'html',
				data:{'familyId':familyId},
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
	//绑定行级点击事件
	$(".opList").live('click',function(e){
		var familyId = $(this).attr('familyId');
		var type = $(this).attr('type');

		if(!confirm('你确定要这样做吗')){
			e.preventDefault();
			$('#user_list_manage').modal('hide');
			return false;
		}
		
		if(type && familyId){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('family/apply',array('op'=>'updateList'));?>",
				dataType:'html',
				data:{'familyId':familyId,'type':type},
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

	//家族一般状态切换 包括显示状态，隐藏状态 ，审核状态
	$('#confirm_button_submit').live('click',function(e){
		if(!$('#ops_reason').attr('value')){
			$('#ops_reason_tip').show();
			return false;
		}else{
			$('#ops_reason_tip').hide();
		}
		
		var family_id = $('#ops_family_id').attr('value');
		var type = $('#ops_type').attr('value');
		var reason = $('#ops_reason').attr('value');

		if(type == ''){
			e.preventDefault();
			$('#user_list_manage').modal('hide');
			return false;
		}
		
		if(type == 0 || type == 1){
			var value = $("#ops_status :selected").attr('value');
		}else if(type == 2 || type == 3){
			var value = $("#ops_hidden :selected").attr('value');
		}else if(type == 4 || type == 5){
			var value = $("#ops_forbidden :selected").attr('value');
		}else if(type == 6){
			var value = $("#ops_hidden :selected").attr('value');
		}else{
			e.preventDefault();
			$('#user_list_manage').modal('hide');
			return false;
		}

		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('family/apply',array('op'=>'updateListDo'));?>",
			dataType:'html',
			data:{'familyId':family_id,'type':type,'value':value,'reason':reason},
			success:function(msg){
				if(msg == 1){
					switch(type){
						case '0':
							var text = '<span style="cursor:pointer;" class="label label-important opList" familyId="'+family_id+'" type="opStatus">审核通过</span>';
							$('#family_list_'+family_id+' > td').eq(6).html(text);
							break;
						case '1':
							var text = '<span style="cursor:pointer;" class="label label-important opList" familyId="'+family_id+'" type="opStatus">拒绝审核</span>';
							$('#family_list_'+family_id+' > td').eq(6).html(text);
							break;
							break;
						case '2':
							var text = '<span style="cursor:pointer;" class="label label-success opList" familyId="'+family_id+'" type="opHidden">隐藏</span>';
							$('#family_list_'+family_id+' > td').eq(4).html(text);
							break;
						case '3':
							var text = '<span style="cursor:pointer;" class="label label-success opList" familyId="'+family_id+'" type="opHidden">显示</span>';
							$('#family_list_'+family_id+' > td').eq(4).html(text);
							break;
						case '4':
							var text = '<span style="cursor:pointer;" class="label label-warning opList" familyId="'+family_id+'" type="opForbidden">停封</span>';
							$('#family_list_'+family_id+' > td').eq(5).html(text);
							break;
						case '5':
							var text = '<span style="cursor:pointer;" class="label label-warning opList" familyId="'+family_id+'" type="opForbidden">启用</span>';
							$('#family_list_'+family_id+' > td').eq(5).html(text);
							break;
						case '6':
							break;
						default:
							return false;
					}
				}else{
					alert(msg);
				}
				e.preventDefault();
				$('#user_list_manage').modal('hide');
			}
		});
		return false;
	});

	//解散家族
	$('#confirm_button_disband').live('click',function(e){
		if(!$('#ops_reason').attr('value')){
			$('#ops_reason_tip').show();
			return false;
		}else{
			$('#ops_reason_tip').hide();
		}
		
		var family_id = $('#ops_family_id').attr('value');
		var reason = $('#ops_reason').attr('value');

		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('family/apply',array('op'=>'disbandDo'));?>",
			dataType:'html',
			data:{'familyId':family_id,'reason':reason},
			success:function(msg){
				e.preventDefault();
				$('#user_list_manage').modal('hide');
				if(msg == 1){
					$('#family_list_'+family_id).detach();
				}else{
					alert(msg);
				}
			}
		});
		return false;
	});
		
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
				alert("搜索真实姓名的关键字太少");
				return false;
			}
		}
		return true;
	});
});
</script>