<?php
$this->breadcrumbs = array('家族管理','操作记录');
$opTypes = FamilyService::getOPTypes();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('family/oprecords');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
				  	<?php echo CHtml::listBox('form[opType]',isset($condition['opType'])?$condition['opType']:'',$opTypes,array('class'=>'input-small','size'=>1,'empty'=>'-操作类型-'));?>
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
				  	<span>操作时间</span>
				  	<?php echo CHtml::textField('form[create_time_start]',isset($condition['create_time_start'])?$condition['create_time_start']:'',array('class'=>'input-small'));?>至
				  	<?php echo CHtml::textField('form[create_time_end]',isset($condition['create_time_end'])?$condition['create_time_end']:'',array('class'=>'input-small'));?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
			  </fieldset>
			</form>   
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>家族ID</th>
						  <th>家族名称</th>
						  <th>家族等级</th>
						  <th>家族拥有者(UID)</br>昵称</th>
						  <th>操作者(UID)</br>昵称</th>
						  <th>操作类型</th>
						  <th>操作理由</th>
						  <th>操作时间</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php 
				  		if(!empty($list['list'])){
				  		foreach($list['list'] as $v){
				  			$uinfo = isset($uinfos[$v['uid']])?$uinfos[$v['uid']]:array();
				  			$opUinfo = isset($uinfos[$v['op_uid']])?$uinfos[$v['op_uid']]:array();
				  	?>
				  	<tr>
				  		<td><?php echo $v['family_id'];?></td>
				  		<td><?php echo $v['name'];?></td>
				  		<td><?php echo $v['level'];?></td>
				  		<td>
				  			<?php 
				  				if($uinfo){
				  					echo $uinfo['username'].'('.$uinfo['uid'].')</br>'.$uinfo['nickname'];
				  				}
				  			?>
				  		</td>
				  		<td>
				  			<?php 
				  				if($opUinfo){
				  					echo $opUinfo['username'].'('.$opUinfo['uid'].')</br>'.$opUinfo['nickname'];
				  				}
				  			?>
				  		</td>
				  		<td><?php echo isset($opTypes[$v['type']])?$opTypes[$v['type']]:'不详';?> </td>
				  		<td><?php echo $v['reason'];?></td>
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