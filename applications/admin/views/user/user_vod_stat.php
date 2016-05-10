<?php
$this->breadcrumbs = array('用户管理','用户点歌统计');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="" method="post">
			  <fieldset>
				<div class="control-group">
				  	<span>用户UID:</span>
				  	<?php echo CHtml::textField('vod[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
				  	<span>昵称:</span> 
					<?php echo CHtml::textField('vod[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
					<span>姓名:</span> 
					<?php echo CHtml::textField('vod[realname]',isset($condition['realname'])?$condition['realname']:'',array('class'=>'input-small'));?>
					<span>账号:</span> 
					<?php echo CHtml::textField('vod[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
				  	<span>主播UID:</span>
				  	<?php echo CHtml::textField('vod[to_uid]',isset($condition['to_uid'])?$condition['to_uid']:'',array('class'=>'input-small'));?>
				  	<?php echo CHtml::listBox('vod[dotey_cat]', isset($condition['dotey_cat'])?$condition['dotey_cat']:'', array(1=>'全部主播',2=>'唱区主播'),array('empty'=>'选择主播类型','size'=>1,'class'=>'input-small'))?>
				  	<span>点歌时间:</span>
				  	<?php echo CHtml::textField('vod[start_time]',isset($condition['start_time'])?$condition['start_time']:'',array('class'=>'input-small'));?>至
				  	<?php echo CHtml::textField('vod[end_time]',isset($condition['end_time'])?$condition['end_time']:'',array('class'=>'input-small'));?>
				  	<?php echo CHtml::submitButton('vod_search',array('class'=>'btn','value'=>'搜索','id'=>'submit_search'));?>
				  	<?php echo CHtml::submitButton('vod_search',array('class'=>'btn','value'=>'下载EXCEL','id'=>'submit_dlExcel'));?>
					<?php echo CHtml::button('user_song_count',array('class'=>'btn','value'=>'合计：'.$pager->getItemCount()));?>
				</div>
			  </fieldset>
			</form>   
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>用户名(UID) </th>
						  <th>魅力值</th>
						  <th>魅力点</th>
						  <th>皮蛋消费</th>
						  <th>贡献值</th>
						  <th>皮点</th>
						  <th>总点歌数</th>
						  <th>操作</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($records['list'])){?>
				  	<?php 
				  		foreach($records['list'] as $r){
				  	?>
				  	<tr>
				  		<td><?php echo $records['userInfos'][$r['uid']]['nickname'];?>(<?php echo $r['uid'];?>)</td>
				  		<td><?php echo $r['sum_charm'];?></td>
				  		<td><?php echo $r['sum_charm_points'];?></td>
				  		<td><?php echo $r['sum_pipiegg'];?></td>
				  		<td><?php echo $r['sum_dedication'];?></td>
				  		<td><?php echo $r['sum_egg_points'];?></td>
				  		<td><?php echo $r['count'];?></td>
				  		<td><a class="btn" title="查看明细" href="<?php echo $this->createUrl('user/vodquery',array('uid'=>$r['uid']));?>"><li class="icon-list"></li></a></td>
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
var actionUrl = "<?php echo $this->createUrl('user/vodstat');?>";							
$(function(){
	$('#vod_start_time').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm:ss'});
	});
	$('#vod_end_time').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm:ss'});
	});
	$('#submit_search').click(function(){
		$(this).parents('form').attr('action',actionUrl);
		var nickname = $("#vod_nickname").attr('value');
		var username = $("#vod_username").attr('value');
		var realname = $("#vod_realname").attr('value');
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
	$('#submit_dlExcel').click(function(){
		$(this).parents('form').attr('action',actionUrl+'&op=dlVODStatExcel');
		var nickname = $("#vod_nickname").attr('value');
		var username = $("#vod_username").attr('value');
		var realname = $("#vod_realname").attr('value');
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
})
</script>