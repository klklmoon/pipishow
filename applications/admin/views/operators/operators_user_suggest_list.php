<?php
$this->breadcrumbs = array('运营工具','意见反馈');
$types = $operateSer->getSuggestType();
$handler = $operateSer->getSuggestHandler();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('operators/usersuggest');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
			  		<span>用户名</span> 
			  		<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
			  		<span>昵称</span> 
			  		<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
			  		<span>ID</span> 
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
					<?php echo Chtml::listBox('form[type]', isset($condition['type'])?$condition['type']:'', $types,array('size'=>1,'class'=>'input-small','empty'=>'-类型-'));?>
					<?php echo Chtml::listBox('form[is_handle]', isset($condition['is_handle'])?$condition['is_handle']:'', $handler,array('size'=>1,'class'=>'input-small','empty'=>'-是否处理-'));?>
				  	<span>反馈时间</span> 
				  	<?php echo CHtml::textField('form[create_time_on]',isset($condition['create_time_on'])?$condition['create_time_on']:'',array('class'=>'date_ui input-small'));?>&nbsp;至&nbsp;
					<?php echo CHtml::textField('form[create_time_end]',isset($condition['create_time_end'])?$condition['create_time_end']:'',array('class'=>'date_ui input-small'));?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
			  </fieldset>
			</form>
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>用户(id)</th>
						  <th>内容</th>
						  <th>类型</th>
						  <th>是否处理</th>
						  <th>反馈时间</th>
						  <th>操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $v){?>
				  	<tr>
				  		<td>
				  			<?php if(isset($uinfo[$v['uid']])){?>
				  				<?php echo $uinfo[$v['uid']]['username'];?>(<?php echo $v['uid'];?>)
				  			<?php }?>
				  			</td>
				  		<td><?php echo $v['content'];?></td>
				  		<td> <?php echo $v['type'] >= count($types) ? $types[count($types) - 2] : $types[$v['type']]; ?>  </td>
				  		<td><?php echo $handler[$v['is_handle']];?> </td>
				  		<td><?php echo date('Y-m-d H:i:s',$v['create_time']);?> </td>
				  		<td>
				  			<?php if($v['is_handle'] == SUGGEST_HANDLER_NO){?>
				  			<a class="btn" href="#" suggestId="<?php echo $v['suggest_id'];?>" title="标记为处理"> <i class="icon-flag"></i></a>
				  			<?php }?>
				  			<a class="btn" href="#" suggestId="<?php echo $v['suggest_id'];?>" title="删除"> <i class="icon-remove"></i></a>
				  			<a class="btn" href="#" suggestId="<?php echo $v['suggest_id'];?>" title="查看"> <i class="icon-book"></i></a>
				  		</td>
				  	</tr>
				  	<?php }?>
				  	<tr>
				  		<td colspan="6">
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
					  		<tr><td colspan="6">没有配置的数据</td></tr>
				  		</tbody>
				  	<?php }?>
				  </tbody>
			  </table>
		</div>
	</div><!--/span-->
</div>

<!-- 浮层 -->
<div class="modal hide fade span10" id="user_list_manage" style="left:5%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
	</div>
	<div class="modal-body" id="user_list_manage_body">
	</div>
</div>
<script>
$(function() {
	//注册开始时间
	$( '#form_create_time_on' ).datepicker(
		{ 
			//showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//注册开始时间
	$( '#form_create_time_end' ).datepicker(
		{ 
			//showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
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
	//标记为处理
	$('.icon-flag').click(function(e){
		var suggestId = $(this).parents('a').attr('suggestId');
		var obj = this;
		if(suggestId){
			$.ajax({
				url:"<?php echo $this->createUrl('operators/usersuggest');?>",
				dataType:'text',
				type:'post',
				data:{'suggestId':suggestId,'op':'flagSuggest'},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').children('td:eq(3)').html('已处理');
						$(obj).parents('a').detach();
					}else{
						e.preventDefault();
						$('#user_list_manage_body').html(msg);
						$('#user_list_manage').modal('show');
					}
				}
			});
		}
	});
	//删除操作
	$('.icon-remove').click(function(e){
		var suggestId = $(this).parents('a').attr('suggestId');
		var obj = this;
		if(suggestId){
			$.ajax({
				url:"<?php echo $this->createUrl('operators/usersuggest');?>",
				dataType:'text',
				type:'post',
				data:{'suggestId':suggestId,'op':'delSuggest'},
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
	//查看
	$('.icon-book').click(function(e){
		var suggestId = $(this).parents('a').attr('suggestId');
		if(suggestId){
			$.ajax({
				url:"<?php echo $this->createUrl('operators/usersuggest');?>",
				dataType:'text',
				type:'post',
				data:{'suggestId':suggestId,'op':'lookSuggest'},
				success:function(msg){
					e.preventDefault();
					$('#user_list_manage_body').html(msg);
					$('#user_list_manage').modal('show');
				}
			});
		}
	});
});
</script>