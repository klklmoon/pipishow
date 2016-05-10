<?php
$this->breadcrumbs = array('主播管理','收入查询');
$days = date('t',strtotime($condition['live_time_on']));
$sources = $this->getProxyAndTutorListOption();
?>
<style type="text/css">
	.table td, .table th {padding:2px;}
</style>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('dotey/incomesearch');?>" method="post">
				<fieldset>
					<div class="control-group">
					<?php if(!$isFilterSource){?>
					<?php $select1 = isset($condition['sources'])?$condition['sources']:''?>
					<?php echo CHtml::listBox('form[sources]', $select1, $sources,array('class'=>'input-small','empty'=>'-来源-','size'=>1));?>
					<?php }?>
					<span>UID:</span> 
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
					<span>昵称:</span> 
					<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
					<span>姓名:</span> 
					<?php echo CHtml::textField('form[realname]',isset($condition['realname'])?$condition['realname']:'',array('class'=>'input-small'));?>
					<span>账号:</span> 
					<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
					<span>直播时间(按月):</span>
					<?php echo CHtml::textField('form[live_time_on]',isset($condition['live_time_on'])?$condition['live_time_on']:'',array('class'=>'date_ui input-small'));?>
					<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
					<?php echo CHtml::submitButton('dl_excel',array('class'=>'btn','value'=>'下载Excel','id'=>'dl_excel'));?>
				</div>
				</fieldset>
			</form>
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>节目名</th>
						<th>昵称</th>
						<th>总皮蛋</th>
						<?php for ($i=1;$i<=$days;$i++){?>
						<th><?php echo $i;?></th>
						<?php }?>
					</tr>
				</thead>
				<tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $uinfo){?>
				  	<tr>
						<td><?php echo $uinfo['title'];?></td>
						<td><?php echo $uinfo['nickname'];?> </td>
						<td><?php echo $uinfo['total_charm_point'];?> </td>
						<?php for ($i=1;$i<=$days;$i++){?>
						<td>
							<?php echo isset($uinfo['detail'][$i])?$uinfo['detail'][$i]:0;?>
						</td>
						<?php }?>
					</tr>
				  	<?php }?>
				  	<tr>
						<td colspan="<?php echo $days+5;?>">
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
						<td colspan="<?php echo $days+5;?>">没有配置的数据</td>
					</tr>
				</tbody>
				  	<?php }?>
				  </tbody>
			</table>
		</div>
	</div>
	<!--/span-->
</div>

<script>
$(function() {
	//注册开始时间
	$( '#form_live_time_on' ).datepicker(
		{ 
			//showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm'
		}
	);
	//download excel
	$('#dl_excel').click(function(e){
		var action = $(this).parents('form').attr('action');
		$(this).parents('form').attr('action',action+'&op=dlIncomeSearchExcel');
		return true;
	});
	//搜索提交
	$('#user_search_submit').click(function(){
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
});
</script>