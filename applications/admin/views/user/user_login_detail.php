<?php
$this->breadcrumbs = array('用户管理','登录明细');
$userRank = $this->formatUserRank();
$doteyRank = $this->formatDoteyRank();
?>
<style type="text/css">
	.table td, .table th {padding:1px;}
</style>			
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="" method="post">
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
				  	<span>登陆IP:</span>
					<?php echo CHtml::textField('form[login_ip]',isset($condition['login_ip'])?$condition['login_ip']:'',array('class'=>'input-small'));?>
					<br/>
				  	登录时间：<?php echo CHtml::textField('form[start_time]',isset($condition['start_time'])?$condition['start_time']:'',array('class'=>'input-small'));?>至
				  	<?php echo CHtml::textField('form[end_time]',isset($condition['end_time'])?$condition['end_time']:'',array('class'=>'input-small'));?>
				  	<span>去重:</span> 
				  	<?php echo CHtml::checkBox('form[remDuplicate]',isset($condition['remDuplicate'])?true:false);?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				  	<?php echo CHtml::button('user_search',array('class'=>'btn','value'=>'合计：'.$pager->getItemCount()));?>
				</div>
			  </fieldset>
			</form>   
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>UID </th>
						  <th>账号</th>
						  <th>昵称</th>
						  <th>用户类型</th>
						  <th>登录时间</th>
						  <th>登录IP</th>
						  <?php if(isset($condition['remDuplicate'])){?>
						  <th>登录总次数</th>
						  <?php }?>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $uinfo){?>
				  	<tr>
				  		<td><?php echo $uinfo['uid'];?></td>
				  		<td>
				  			<?php 
				  				if(isset($userInfo[$uinfo['uid']])){
				  					echo $userInfo[$uinfo['uid']]['username'];
				  				}
				  			?>
				  		</td>
				  		<td>
				  			<?php 
				  				if(isset($userInfo[$uinfo['uid']])){
				  					echo $userInfo[$uinfo['uid']]['nickname'];
				  				}
				  			?>
				  		</td>
				  		<td>
				  			<?php 
				  				if(isset($userInfo[$uinfo['uid']])){
				  					echo implode('</br>',$this->userSer->checkUserType($userInfo[$uinfo['uid']]['user_type'],true));
				  				}
				  			?>
				  		</td>
				  		<td><?php echo date('Y-m-d H:i:s',$uinfo['login_time']);?></td>
				  		<td><?php echo $uinfo['login_ip'];?></td>
				  		<?php if(isset($condition['remDuplicate'])){?>
				  		<td><?php echo $uinfo['loginCount'];?></td>
				  		<?php }?>
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
					  		<tr><td colspan="7">没有配置的数据</td></tr>
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
var actionUrl = "<?php echo $this->createUrl('user/logindetail');?>";							
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
		$(this).parents('form').attr('action',actionUrl);
		var nickname = $("#form_nickname").attr('value');
		var username = $("#form_username").attr('value');
		var realname = $("#form_realname").attr('value');
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
		if(realname){
			if(realname.length < 2){
				alert("搜索姓名的关键字太少");
				return false;
			}
		}
		return true;
	});
	
	$('#dlBriskExcel').click(function(){
		var uid = $('#form_uid').attr('value');
		var start_time = $('#form_start_time').attr('value');
		var end_time = $('#form_end_time').attr('value');

		if(!uid && !start_time && !end_time){
			alert('搜索条件不能为空');
			return false;
		}

		if(end_time && !start_time){
			alert('登录起始时间不能为空');
			return false;
		}

		if(start_time && !end_time){
			alert('登录结束时间不能为空');
			return false;
		}

		if(start_time && end_time){
			var f_start_time = Date.parse(start_time);
			var f_end_time = Date.parse(end_time);
			var days = ((f_end_time - f_start_time)/86400/1000);
			if(days > 7){
				alert('导出EXCEL的时间范围不能超过一周');
				return false;
			}
		}

		$(this).parents('form').attr('action',actionUrl+'&op=dlBriskExcel');
		return true;
	});
});
</script>