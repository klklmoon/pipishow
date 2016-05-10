<?php
$this->breadcrumbs = array('靓号管理','用户靓号列表');
$type = $this->numService->getUserNumber();
$status = $this->numService->getUNumberStatus();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i>靓号列表管理</h2>
			<div class="box-icon">
				<a href="<?php echo $this->createUrl('number/list');?>" target="_blank" class="btn btn-setting btn-round" title="赠送靓号"><i class="icon-plus-sign"></i></a>
			</div>
		</div>
		
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('number/userNumber');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
				  	<?php echo CHtml::listBox('form[type]',isset($condition['type'])?$condition['type']:'',$type,array('class'=>'input-small','size'=>1,'empty'=>'-靓号来源-'));?>
				  	<?php echo CHtml::listBox('form[status]',isset($condition['status'])?$condition['status']:'',$status,array('class'=>'input-small','size'=>1,'empty'=>'-回收状态-'));?>
			  		<span>靓号</span> 
					<?php echo CHtml::textField('form[number]',isset($condition['number'])?$condition['number']:'',array('class'=>'input-small'));?>
			  		<span>UID</span> 
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
			  		<span>用户名</span> 
					<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
			  		<span>昵称</span> 
					<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
				  	<span>获取时间</span>
				  	<?php echo CHtml::textField('form[create_time_start]',isset($condition['create_time_start'])?$condition['create_time_start']:'',array('class'=>'input-small'));?>至
				  	<?php echo CHtml::textField('form[create_time_end]',isset($condition['create_time_end'])?$condition['create_time_end']:'',array('class'=>'input-small'));?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
			  </fieldset>
			</form>   
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>UID</br>昵称</th>
						  <th>用户名 </th>
						  <th>靓号</th>
						  <th>获取方式</th>
						  <th>回收状态</th>
						  <th>购买记录ID</th>
						  <th>寄语</th>
						  <th>创建时间</th>
						  <th>操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php 
				  		if(!empty($list['list'])){
				  		foreach($list['list'] as $v){
				  	?>
				  	<tr id="number_list_<?php echo $v['uid'].'_'.$v['number'];?>">
				  		<td><?php echo $v['uid'];?></br><?php echo isset($uinfos[$v['uid']])?$uinfos[$v['uid']]['nickname']:'';?></td>
				  		<td><?php echo isset($uinfos[$v['uid']])?$uinfos[$v['uid']]['username']:'';?></td>
				  		<td><?php echo $v['number'];?></td>
				  		<td><?php echo $type[$v['reward_type']];?></td>
				  		<td><?php echo $status[$v['status']];?> </td>
				  		<td><?php echo $v['record_id'];?></td>
				  		<td><?php echo $v['short_desc'];?></td>
				  		<td><?php echo date('Y-m-d H:i:s',$v['create_time']);?></td>
				  		<td>
				  			<?php if(!$v['status']){?>
				  			<a  href="javascript:void(1);" title="回收" rid="<?php echo $v['uid'].'_'.$v['number'];?>"class="number_del"><span class="icon icon-color icon-remove"></span></a>
				  			<?php }?>
				  		</td>
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
					  		<tr><td colspan="9">没有配置的数据</td></tr>
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

	$('.number_del').live('click',function(e){
		if(!confirm('确定要回收此靓号吗，此操作是不可逆的')){
			return false;
		}
		
		var rid = $(this).attr('rid');
		if(rid){
			var html = '<div class="control-group"><label class="control-label" for="focusedInput">回收理由</label><div class="controls">'+
					'<input type="hidden" value="'+rid+'" name="recover_flag" id="recover_flag"><input class="input-large focused" type="text" value="" name="recover_reason" id="recover_reason"></div></div>';
			html += '<div class="form-actions"><button type="submit" class="btn btn-primary recover_submit">确定</button></div>';
			e.preventDefault();
			$('#user_list_manage_body').html(html);
			$('#user_list_manage').modal('show');
		}else{
			alert('参数有误，回收失败');
			return false;
		}
		
	});

	$('.recover_submit').live('click',function(e){
		var reason = $('#recover_reason').attr('value');
		var rid =  $('#recover_flag').attr('value');

		if(rid && reason){
			e.preventDefault();
			$('#user_list_manage_body').html('');
			$('#user_list_manage').modal('hide');
	
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('number/userNumber');?>",
				dataType:'html',
				data:{'rid':rid,'reason':reason,'op':'recoverUserNumber'},
				success:function(msg){
					if(msg == 1){
						$('#number_list_'+rid+' > td').eq(4).html('回收');
					}else{
						alert(msg);
					}
				}
			});
		}
	});
	
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
		$(this).submit();
	});
});
</script>