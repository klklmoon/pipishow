<?php
$this->breadcrumbs = array('频道管理','地区频道主播管理');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2>地区频道主播列表</h2>
			<div class="box-icon">
				<a class="btn btn-round" href="#" title="添加地区频道主播"><i class="icon-plus"></i></a>
			</div>
		</div>
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('channel/areadotey');?>" method="post">
			  <fieldset>
			  	<div class="control-group">
			  		<span>主播用户：</span>
					<?php $select1 = isset($condition['username'])?$condition['username']:'';?>
					<?php echo CHtml::textField('search[username]',$select1,array('class'=>'input-small'));?>
					<span>ID：</span>
					<?php $select2 = isset($condition['uid'])?$condition['uid']:'';?>
					<?php echo CHtml::textField('search[uid]',$select2,array('class'=>'input-small'));?>
			  		<span>地区分类</span>
					<?php $select3 = isset($condition['sub_name'])?$condition['sub_name']:'';?>
			  		<?php echo CHtml::listBox('search[sub_name]', $select3, $this->channelSer->getChannelCateConfig(CHANNEL_AREA),array('size'=>1,'class'=>'input-small','empty'=>' '));?>
					<?php echo CHtml::submitButton('dotey_song_search',array('class'=>'btn','value'=>'搜索'));?>
				</div>
			  </fieldset>
			
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>用户名</th>
						  <th>真实姓名</th>
						  <th>昵称</th>
						  <th>直播等级</th>
						  <th>地区频道</th>
						  <th>操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($areaList)){?>
				  	<?php foreach($areaList as $area){?>
				  	<?php if($area['uid'] >0 ){?>
				  	<tr>
				  		<td><?php echo isset($doteyInfos[$area['uid']])?$doteyInfos[$area['uid']]['username']:'';?></td>
				  		<td><?php echo isset($doteyInfos[$area['uid']])?$doteyInfos[$area['uid']]['realname']:'';?></td>
				  		<td><?php echo isset($doteyInfos[$area['uid']])?$doteyInfos[$area['uid']]['nickname']:'';?></td>
				  		<td>
				  			<?php echo $allDoteyRanks[$doteyConsumes[$area['uid']]['dotey_rank']];?>
				  		</td>
				  		<td><?php echo $area['sub_name'];?></td>
				  		<td>
				  			<a class="btn" href="#" relinfos="<?php echo $area['uid'].'_'.$area['channel_id'].'_'.$area['sub_channel_id'].'_'.$area['target_relation_id'];?>" title="删除"> <i class="icon-remove"></i></a>
				  		</td>
				  	</tr>
				  	<?php }?>
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
			  </form>
		</div>
	</div><!--/span-->
</div>

<!-- 浮层 -->
<div class="modal hide fade span10" id="doteysong_list_manage" style="left:5%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>地区频道主播管理</h3>
	</div>
	<div class="modal-body" id="doteysong_list_manage_body">
	</div>
</div>

<script>
$(function() {
	//添加地区频道主播
	$(".icon-plus").click(function(e){
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('channel/createareadotey');?>",
			dataType:'html',
			success:function(msg){
				if(msg){
					$('#doteysong_list_manage_body').html(msg);
				}else{
					$('#doteysong_list_manage_body').html('信息获取失败');
				}
				e.preventDefault();
				$('#doteysong_list_manage').modal('show');
			}
		});	
	});
	
	//删除
	$(".box-content .icon-remove").click(function(e){
		var refInfos = $(this).parent('a').attr('relinfos');
		var obj = this;
		if(refInfos){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('channel/createareadotey');?>",
				dataType:'html',
				data:{'refInfos':refInfos,'op':'delDoteyChannel'},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						$('#newsNotice_list_manage_body').html(msg);
						e.preventDefault();
						$('#newsNotice_list_manage').modal('show');
					}
				}
			});
		}
	});
});
</script>