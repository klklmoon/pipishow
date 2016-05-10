<?php
$this->breadcrumbs = array('主播管理','直播间管理');
$doteyStatus = $this->doteySer->getDoteyBaseStatus();
$doteySignType = $this->doteySer->getDoteySignType();
$doteyRegSource = $this->userSer->getUserRegSource();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('dotey/archiveslist');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
			  		<span>用户ID:</span> 
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
					<span>昵称：</span> 
					<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
					<?php echo Chtml::listBox('form[cat_id]', isset($condition['cat_id'])?$condition['cat_id']:'', $cat_ids,array('size'=>1,'class'=>'input-small','empty'=>'节目类型'));?>
					<?php echo Chtml::listBox('form[recommond]', isset($condition['recommond'])?$condition['recommond']:'', $recommond,array('size'=>1,'class'=>'input-small','empty'=>'是否推荐'));?>
					<?php echo Chtml::listBox('form[is_hide]', isset($condition['is_hide'])?$condition['is_hide']:'', $is_hide,array('size'=>1,'class'=>'input-small','empty'=>'是否隐藏'));?>
				  	<span>创建时间:</span>
				  	<?php echo CHtml::textField('form[create_time_start]',isset($condition['create_time_start'])?$condition['create_time_start']:'',array('class'=>'date_ui input-small'));?>至
				  	<?php echo CHtml::textField('form[create_time_end]',isset($condition['create_time_end'])?$condition['create_time_end']:'',array('class'=>'date_ui input-small'));?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
			  </fieldset>
			</form>   
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>昵称-id</th>
						  <th>创建时间 </th>
						  <th>节目类型</th>
						  <th>标题</th>
						  <th>是否推荐</th>
						  <th>是否隐藏</th>
						  <th>操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $uinfo){?>
				  	<tr>
				  		<td><?php echo $doteyInfo[$uinfo['uid']]['nickname'];?>-<?php echo $uinfo['uid'];?></td>
				  		<td><?php echo date('Y-m-d H:i:s',$uinfo['create_time']);?></td>
				  		<td> <?php echo $uinfo['name'];?> </td>
				  		<td><?php echo $uinfo['title'];?> </td>
				  		<td><?php echo $recommond[$uinfo['recommond']];?> </td>
				  		<td><?php echo $is_hide[$uinfo['is_hide']];?> </td>
				  		<td>
				  			<a class="btn" href="#" archivesid="<?php echo $uinfo['archives_id'];?>" title="编辑"> <i class="icon-edit"></i></a>
				  			<a class="btn" href="<?php echo $this->createUrl('dotey/onlive',array('uid'=>$uinfo['uid']));?>" title="查看开播记录"> <i class="icon-th-list"></i> </a>
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
		<h3>用户管理</h3>
	</div>
	<div class="modal-body" id="user_list_manage_body">
	</div>
</div>

<script>
$(function() {
	//创建开始时间
	$( '#form_create_time_start' ).datepicker(
		{ 
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//创建结束时间
	$( '#form_create_time_end' ).datepicker(
		{ 
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//编辑操作
	$(".box-content .icon-edit").click(function(e){
		var archivesid = $(this).parents('a').attr('archivesid');
		if(archivesid){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('dotey/editarchives');?>",
				dataType:'html',
				data:{'archives_id':archivesid},
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
});
</script>