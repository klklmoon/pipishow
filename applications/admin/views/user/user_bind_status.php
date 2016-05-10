<?php
$this->breadcrumbs = array('用户管理','用户绑定状态记录');
$bindTelStatus = $this->bindStatus('tel');
$bindEmailStatus = $this->bindStatus('email');
$allBindStatus = $this->bindStatus('all');
?>		
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="" method="post">
			  <fieldset>
			  	<div class="control-group">
			  		<span>姓名:</span> 
			  		<?php echo CHtml::textField('form[realname]',isset($condition['realname'])?$condition['realname']:'',array('class'=>'input-small'));?>
			  		<span>账号:</span> 
			  		<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
			  		<span>昵称:</span> 
			  		<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
			  		<span>UID:</span> 
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
					<?php echo CHtml::listBox('form[bind_tel]', isset($condition['bind_tel'])?$condition['bind_tel']:'',$bindTelStatus,array('size'=>1,'empty'=>'-手机绑定-','class'=>'input-small'));?>
					<?php echo CHtml::listBox('form[bind_email]', isset($condition['bind_email'])?$condition['bind_email']:'',$bindEmailStatus,array('size'=>1,'empty'=>'-邮箱绑定-','class'=>'input-small'));?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
			  </fieldset>
			</form>   
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>记录ID </th>
						  <th>UID </th>
						  <th>账号</th>
						  <th>昵称</th>
						  <th>用户类型</th>
						  <th>绑定状态</th>
						  <th>绑定值</th>
						  <th>操作时间</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $v){
				  			if(!isset($uinfos[$v['uid']]))
				  				continue;
				  		?>
				  	<tr>
				  		<td><?php echo $v['bind_id'];?></td>
				  		<td><?php echo $v['uid'];?></td>
				  		<td><?php echo $uinfos[$v['uid']]['username'];?></td>
				  		<td><?php echo $uinfos[$v['uid']]['nickname'];?></td>
				  		<td><?php echo implode('</br>',$this->userSer->checkUserType($uinfos[$v['uid']]['user_type'],true));?> </td>
				  		<td><?php echo isset($allBindStatus[$v['method']])?$allBindStatus[$v['method']]:$v['method'];?></td>
				  		<td><?php echo $v['method_content'];?></td>
				  		<td><?php echo date('Y-m-d H:i:s',$v['create_time']);?></td>
				  	</tr>
				  	<?php }?>
				  	<tr>
				  		<td colspan="8">
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
					  		<tr><td colspan="8">没有配置的数据</td></tr>
				  		</tbody>
				  	<?php }?>
				  </tbody>
			  </table>
		</div>
	</div><!--/span-->
</div>

<script>
var actionUrl = "<?php echo $this->createUrl('user/UserBind');?>";							
$(function() {
	$(':submit').click(function(){
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
		
		$(this).submit();
	});
});
</script>