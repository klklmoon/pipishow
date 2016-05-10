<?php
$this->breadcrumbs = array('家族管理','签约家庭');
$status = FamilyService::getFamilySignStatus();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('family/contracted');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
				  	<?php echo CHtml::listBox('form[status]',isset($condition['status'])?$condition['status']:'',$status,array('class'=>'input-small','size'=>1,'empty'=>'-签约状态-'));?>
					<span>家族ID</span>
					<?php echo CHtml::textField('form[family_id]',isset($condition['family_id'])?$condition['family_id']:'',array('class'=>'input-small'));?>
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
						  <th>家族拥有者(UID)</br>昵称</th>
						  <th>签约状态</th>
						  <th>操作理由</th>
						  <th>操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php 
				  		if(!empty($list['list'])){
				  		foreach($list['list'] as $v){
				  			if (!isset($famInfos[$v['family_id']]))
				  				continue;
				  			$famInfo = $famInfos[$v['family_id']];
				  			if (!isset($uinfos[$famInfo['uid']]))
				  				continue;
				  			$uinfo = $uinfos[$famInfo['uid']];
				  	?>
				  	<tr>
				  		<td><?php echo $v['family_id'];?></td>
				  		<td><?php echo $famInfo['name'];?></td>
				  		<td>
				  			<?php 
				  				if($uinfo){
				  					echo $uinfo['username'].'('.$uinfo['uid'].')</br>'.$uinfo['nickname'];
				  				}
				  			?>
				  		</td>
				  		<td><?php echo isset($status[$v['status']])?$status[$v['status']]:'不详';?> </td>
				  		<td><?php echo date('Y-m-d H:i:s',$v['create_time']);?></td>
				  		<td>
				  			<a  href="javascript:void(1);" title="更改签约状态" signId="<?php echo $v['id'];?>" class="opList"><span class="icon icon-color icon-transfer-ew"></span></a>
				  			<a  href="javascript:void(1);" title="签约家族转普通家族" familyId="<?php echo $v['family_id'];?>" class="opToNormal"><span class="icon icon-color icon-shuffle"></span></a>
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

	//绑定行级点击事件
	$(".opList").live('click',function(e){
		var signId = $(this).attr('signId');

		if(!confirm('你确定要这样做吗')){
			e.preventDefault();
			$('#user_list_manage').modal('hide');
			return false;
		}
		
		if(signId){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('family/contracted',array('op'=>'changeSign'));?>",
				dataType:'html',
				data:{'signId':signId},
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

	$(".opToNormal").live('click',function(e){
		var familyId = $(this).attr('familyId');

		if(!confirm('你确定要这样做吗')){
			e.preventDefault();
			$('#user_list_manage').modal('hide');
			return false;
		}
		
		if(familyId){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('family/contracted',array('op'=>'toNormal'));?>",
				dataType:'html',
				data:{'familyId':familyId},
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
});
</script>