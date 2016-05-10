<?php
$this->breadcrumbs = array('靓号管理','用户购买靓号记录');
$type = $this->numService->getUserNumber();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i>用户购买靓号记录</h2>
		</div>
		
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('number/buyRecords');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
				  	<?php echo CHtml::listBox('form[type]',isset($condition['type'])?$condition['type']:'',$type,array('class'=>'input-small','size'=>1,'empty'=>'-购买来源-'));?>
			  		<span>靓号</span> 
					<?php echo CHtml::textField('form[number]',isset($condition['number'])?$condition['number']:'',array('class'=>'input-small'));?>
			  		<span>赠送UID</span> 
					<?php echo CHtml::textField('form[sender_uid]',isset($condition['sender_uid'])?$condition['sender_uid']:'',array('class'=>'input-small'));?>
			  		<span>代理UID</span> 
					<?php echo CHtml::textField('form[proxy_uid]',isset($condition['proxy_uid'])?$condition['proxy_uid']:'',array('class'=>'input-small'));?>
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
						  <th>靓号</th>
						  <th>购买人UID</br>昵称</th>
						  <th>购买来源</th>
						  <th>自助价</th>
						  <th>实际价</th>
						  <th>描述</th>
						  <th>代理人UID</br>昵称</th>
						  <th>销售代理UID</br>昵称</th>
						  <th>创建时间</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php 
				  		if(!empty($list['list'])){
				  		foreach($list['list'] as $v){
				  	?>
				  	<tr>
				  		<td><?php echo $v['number'];?></td>
				  		<td><?php echo $v['uid'];?></br><?php echo isset($uinfos[$v['uid']])?$uinfos[$v['uid']]['nickname']:'';?></td>
				  		<td><?php echo $type[$v['source']];?></td>
				  		<td><?php echo $v['buffer_price'];?></td>
				  		<td><?php echo $v['confirm_price'];?></td>
				  		<td><?php echo $v['desc'];?></td>
				  		<td><?php echo $v['proxy_uid'];?></br><?php echo isset($uinfos[$v['proxy_uid']])?$uinfos[$v['proxy_uid']]['nickname']:'';?></td>
				  		<td><?php echo $v['sender_uid'];?></br><?php echo isset($uinfos[$v['sender_uid']])?$uinfos[$v['sender_uid']]['nickname']:'';?></td>
				  		<td><?php echo date('Y-m-d H:i:s',$v['create_time']);?></td>
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