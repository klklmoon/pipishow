<?php
$this->breadcrumbs = array('主播管理','主播申请');
$sources = $this->getProxyAndTutorListOption($isFilterSource);
$types = $this->doteySer->getDoteyBaseStatus();
$whether = $this->doteySer->getWhetherDotey();
$genders = $this->doteySer->getDoteyGender();
unset($genders[0]);
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-user"></i> 主播申请列表</h2>
		</div>
		
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('dotey/doteyapply');?>" method="post">
				<fieldset>
					<div class="control-group">
					<?php $select1 = isset($condition['sources'])?$condition['sources']:''?>
					<?php echo CHtml::listBox('form[sources]', $select1, $sources,array('class'=>'input-small','empty'=>'-来源-','size'=>1));?>
					<?php $select2 = isset($condition['status'])?$condition['status']:''?>
					<?php echo CHtml::listBox('form[status]', $select2, $types ,array('class'=>'input-small','empty'=>'-签约状态-','size'=>1));?>
					<?php $select3 = isset($condition['has_experience'])?$condition['has_experience']:''?>
					<?php echo CHtml::listBox('form[has_experience]', $select3, $whether,array('class'=>'input-small','empty'=>'-直播经验-','size'=>1));?>
					<?php $select4 = isset($condition['gender'])?$condition['gender']:''?>
					<?php echo CHtml::listBox('form[gender]', $select4,$genders,array('class'=>'input-small','empty'=>'-性别-','size'=>1));?>
					<span>账号:</span> 
					<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
					<span>姓名:</span> 
					<?php echo CHtml::textField('form[realname]',isset($condition['realname'])?$condition['realname']:'',array('class'=>'input-small'));?>
					<span>杭州地区:</span> 
					<?php echo CHtml::checkBox('form[city]',!empty($condition['city'])?true:false,array('value'=>'杭州市'));?>
					</div>
					<div class="control-group">
					<span>申请时间:</span> 
					<?php echo CHtml::textField('form[create_time_start]',isset($condition['create_time_start'])?$condition['create_time_start']:'',array('class'=>'input-small'));?>至
					<?php echo CHtml::textField('form[create_time_end]',isset($condition['create_time_end'])?$condition['create_time_end']:'',array('class'=>'input-small'));?>
					<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
					</div>
				</fieldset>
			</form>
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>申请时间</th>
						<th>来源</th>
						<th>账号</th>
						<th>姓名</th>
						<th>性别</th>
						<th>主播经验</th>
						<th>手机/QQ</th>
						<th>所在城市</th>
						<th>签约状态</th>
						<th>操作管理</th>
					</tr>
				</thead>
				<tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $uinfo){?>
				  	<tr>
						<td>
							<?php echo date('Y-m-d',$uinfo['create_time']); ?>
						</td>
						<td>
							<?php 
								if ($uinfo['proxy_uid'] > 0){
									$k = DOTEY_MANAGER_PROXY.'#XX#'.$uinfo['proxy_uid'];
									echo isset($sources[$k])?$sources[$k]:'无';
								}
								if ($uinfo['proxy_uid'] && $uinfo['tutor_uid']){
									echo "</br>";
								}
								if ($uinfo['tutor_uid'] > 0){
									$k = DOTEY_MANAGER_TUTOR.'#XX#'.$uinfo['tutor_uid'];
									echo isset($sources[$k])?$sources[$k]:'无';
								}
							?>
						</td>
						<td id="username_<?php echo $uinfo['uid'];?>"><?php echo $uinfo['username'];?> </td>
						<td><?php echo $uinfo['realname'];?></td>
						<td><?php echo isset($genders[$uinfo['gender']])?$genders[$uinfo['gender']]:'不详';?> </td>
						<td>
							<?php 
								echo $whether[$uinfo['has_experience']];
							?>
						</td>
						<td>
							<?php 
								$mobile = !empty($uinfo['mobile'])?$uinfo['mobile']:'null';
								$qq = !empty($uinfo['qq'])?$uinfo['qq']:'null';
								echo $mobile.'/'.$qq;
							?> 
						</td>
						<td><?php echo $uinfo['city'];?></td>
						<td><?php echo $types[$uinfo['status']];?> </td>
						<td>
							<span class='btn look_apply_info' uid="<?php echo $uinfo['uid'];?>">查看</span>
							<?php 
								//授权和拒绝	
								if($uinfo['status'] == APPLY_STATUS_WAITING){
							?>
								<span class='btn auth_stauts' uid="<?php echo $uinfo['uid'];?>">授权</span>
								<span class='btn refuse_status' uid="<?php echo $uinfo['uid'];?>">拒绝</span>
							<?php 
								}
							?>
							<?php 
								//已授权未签约	
								if($uinfo['status'] == APPLY_STATUS_FACE){
							?>
								<span class='btn contract_status' uid="<?php echo $uinfo['uid'];?>">签约</span>
							<?php 
								}
							?>
							<?php 
								//已拒绝	
								if($uinfo['status'] == APPLY_STATUS_REFUES){
							?>
								<!-- <span class='btn revoked_status' uid="<?php echo $uinfo['uid'];?>">撤销拒绝</span> -->
								<span class='btn del_status' uid="<?php echo $uinfo['uid'];?>">删除申请</span>
							<?php 
								}
							?>
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
					<tr>
						<td colspan="10">没有配置的数据</td>
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
<div class="modal hide span10 fade" id="dotey_award_manage" style="left:5%;">

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
	</div>
	<div class="modal-body" id="dotey_award_manage_body">
		
	</div>
</div>

<div style="display:none" id="_refuse_status_reason">
	<div class="box-content">
			<fieldset class="form-horizontal">
			  <div class="control-group">
				<label class="control-label" for="focusedInput">理由</label>
				<div class="controls">
				  <input class="input-large focused" type="text" value="" name="op_reason" id="op_reason">
				  <span id="op_reason_info"></span>					
				</div>
			  </div>
			  <div class="form-actions">
			  	<input type="hidden" value="" name="op_uid" id="op_uid">
				<button type="submit" class="btn btn-primary">提交</button>
			  </div>
			</fieldset>
		</div>
	<script>
	$(':submit').click(function(e){
		var uid =$("#op_uid").attr('value');
		var reason = $('#op_reason').attr('value');
		if(uid && reason){
			$.ajax({
				url:"<?php echo $this->createUrl('dotey/doteyapply'); ?>",
				dataType:'html',
				type:'post',
				data:{'uid':uid,'op':'refuseDoteyApply','reason':reason},
				success:function(msg){
					if(msg == 1){
						location.href=applyUrl+"&username="+$('#username_'+uid).html();
					}else{
						e.preventDefault();
						$('#dotey_award_manage_body').html(msg);
						$('#dotey_award_manage').modal('show');
					}
				}
			});
		}else{
			$('#op_reason_info').html('理由不能为空');
		}
	});
	</script>
</div>

<script>
var applyUrl = "<?php echo $this->createUrl('dotey/doteyapply');?>";
$(function() {
	$('#form_create_time_start').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm'});
	});
	$('#form_create_time_end').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm'});
	});
	
	//查看与编辑
	$('.look_apply_info').click(function(e){
		var uid = $(this).attr('uid');
		if(uid){
			$.ajax({
				url:"<?php echo $this->createUrl('dotey/doteyapply'); ?>",
				dataType:'html',
				type:'post',
				data:{'uid':uid,'op':'getApplyInfo'},
				success:function(msg){
					e.preventDefault();
					$('#dotey_award_manage_body').html(msg);
					$('#dotey_award_manage').modal('show');
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
				url:"<?php echo $this->createUrl('dotey/doteyapply'); ?>",
				dataType:'html',
				type:'post',
				data:{'uid':uid,'op':'authDoteyApply'},
				success:function(msg){
					if(msg == 1){
						location.href=applyUrl+"&username="+$('#username_'+uid).html();
						$(obj).removeClass("auth_stauts").addClass("contract_status").html("签约");
						$(obj).next().detach();
					}else{
						e.preventDefault();
						$('#dotey_award_manage_body').html(msg);
						$('#dotey_award_manage').modal('show');
					}
				}
			});
		}
	});
	//签约
	$('.contract_status').click(function(e){
		var uid = $(this).attr('uid');
		var obj = this;
		if(uid){
			$.ajax({
				url:"<?php echo $this->createUrl('dotey/doteyapply'); ?>",
				dataType:'html',
				type:'post',
				data:{'uid':uid,'op':'contractDoteyApply'},
				success:function(msg){
					if(msg == 1){
						location.href=applyUrl+"&username="+$('#username_'+uid).html();
						$(obj).detach();
					}else{
						e.preventDefault();
						$('#dotey_award_manage_body').html(msg);
						$('#dotey_award_manage').modal('show');
					}
				}
			});
		}
	});
	//拒绝
	$('.refuse_status').click(function(e){
		var uid = $(this).attr('uid');
		e.preventDefault();
		$("#op_uid").attr('value',uid);
		$('#dotey_award_manage_body').html($('#_refuse_status_reason').html());
		$('#dotey_award_manage').modal('show');
	});
	
	//撤销拒绝
	$('.revoked_status').click(function(e){
		var uid = $(this).attr('uid');
		var obj = this;
		if(uid){
			$.ajax({
				url:"<?php echo $this->createUrl('dotey/doteyapply'); ?>",
				dataType:'html',
				type:'post',
				data:{'uid':uid,'op':'revokedDoteyApply'},
				success:function(msg){
					if(msg == 1){
						location.href=applyUrl+"&username="+$('#username_'+uid).html();
						$(obj).removeClass("revoked_status").addClass("refuse_status").html('拒绝');
						$(obj).next().removeClass('del_status').addClass('revoked_status').html("撤销拒绝");
					}else{
						e.preventDefault();
						$('#dotey_award_manage_body').html(msg);
						$('#dotey_award_manage').modal('show');
					}
				}
			});
		}
	});
	//删除申请
	$('.del_status').click(function(e){
		var uid = $(this).attr('uid');
		var obj = this;
		if(uid){
			$.ajax({
				url:"<?php echo $this->createUrl('dotey/doteyapply'); ?>",
				dataType:'html',
				type:'post',
				data:{'uid':uid,'op':'delDoteyApply'},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						e.preventDefault();
						$('#dotey_award_manage_body').html(msg);
						$('#dotey_award_manage').modal('show');
					}
				}
			});
		}
	});
	
});
</script>