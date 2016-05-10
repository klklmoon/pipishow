<?php
$this->breadcrumbs = array('主播管理','用户勋章管理');
$types = $userMedal->getGrantTypeList();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2>用户勋章管理</h2>
			<div class="box-icon">
				<a class="btn btn-round" href="#" title="勋章授权"><i class="icon-plus"></i></a>
			</div>
		</div>
		
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('operators/usermedal');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
			  		<span>用户名:</span> 
			  		<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
			  		<span>用户ID:</span> 
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
					<span>昵称：</span> 
					<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
					<span>勋章：</span>
					<?php echo Chtml::listBox('form[mid]', isset($condition['mid'])?$condition['mid']:'', $mids,array('size'=>1,'class'=>'input-small','empty'=>' '));?>
					<span>发送类型：</span>
					<?php echo Chtml::listBox('form[type]', isset($condition['type'])?$condition['type']:'', $types,array('size'=>1,'class'=>'input-small','empty'=>' '));?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
			  </fieldset>
			</form>
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>用户名(id)</th>
						  <th>昵称</th>
						  <th>勋章名称</th>
						  <th>发送类型</th>
						  <th>有效时间</th>
						  <th>颁发时间</th>
						  <th>操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $v){?>
				  	<tr>
				  		<td><?php echo $uinfo[$v['uid']]['username'];?>(<?php echo $v['uid'];?>)</td>
				  		<td><?php echo $uinfo[$v['uid']]['nickname'];?></td>
				  		<td> <?php echo $mids[$v['mid']]; ?>  </td>
				  		<td><?php echo $types[$v['type']];?> </td>
				  		<td><?php echo date('Y-m-d H:i:s',$v['vtime']);?> </td>
				  		<td><?php echo date('Y-m-d H:i:s',$v['ctime']);?> </td>
				  		<td>
				  			<a class="btn" href="#" rid="<?php echo $v['rid'];?>" title="删除"> <i class="icon-remove"></i></a>
				  		</td>
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
$(function() {
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
	//删除操作
	$('.icon-remove').click(function(e){
		var rid = $(this).parents('a').attr('rid');
		var obj = this;
		if(rid){
			$.ajax({
				url:"<?php echo $this->createUrl('operators/addusermedal');?>",
				dataType:'text',
				type:'post',
				data:{'rid':rid,'op':'delUserMedal'},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						e.preventDefault();
						$('#user_list_manage_body').html(msg);
						$('#user_list_manage').modal('show');
					}
				}
			});
		}
	});
	//添加操作
	$('.icon-plus').click(function(e){
		$.ajax({
			url:"<?php echo $this->createUrl('operators/addusermedal');?>",
			dataType:'text',
			type:'post',
			success:function(msg){
				e.preventDefault();
				$('#user_list_manage_body').html(msg);
				$('#user_list_manage').modal('show');
			}
		});
	});
});
</script>