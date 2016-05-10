<?php
$this->breadcrumbs = array('主播管理','直播管理');
$liveStatus = $archivesSer->getLiveStatus();
unset($liveStatus[INVALID_LIVE]);
$doteySignType = $this->doteySer->getDoteySignType();
$doteyRegSource = $this->userSer->getUserRegSource();
$is_hide = $archivesSer->getArchivesIsHide();
$archivesUrl = $this->getArchivesUrl();
$sources = $this->getProxyAndTutorListOption();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch"
				action="<?php echo $this->createUrl('dotey/onlive');?>"
				method="post">
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
					<span>账号:</span> 
					<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
					<?php echo Chtml::listBox('form[status]', isset($condition['status'])?$condition['status']:'', $liveStatus,array('size'=>1,'class'=>'input-small','empty'=>'-开播状态-'));?>
					<?php echo Chtml::listBox('form[is_hide]', isset($condition['is_hide'])?$condition['is_hide']:'', $is_hide,array('size'=>1,'class'=>'input-small','empty'=>'-是否隐藏-'));?>
					<?php //echo Chtml::listBox('form[cat_id]', isset($condition['cat_id'])?$condition['cat_id']:'', $cates,array('size'=>1,'class'=>'input-small','empty'=>'-节目分类-'));?>
				</div>
					<div class="control-group">
						<span>直播时间:</span>
				  	<?php echo CHtml::textField('form[live_time_on]',isset($condition['live_time_on'])?$condition['live_time_on']:'',array('class'=>'date_ui input-small'));?>至
				  	<?php echo CHtml::textField('form[live_time_end]',isset($condition['live_time_end'])?$condition['live_time_end']:'',array('class'=>'date_ui input-small'));?>
				  	<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
				</fieldset>
			</form>
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>节目名称</th>
						<th>姓名(昵称)</th>
						<th>节目分类</th>
						<th>标题</th>
						<th>直播状态</th>
						<th>隐藏</th>
						<th>时间</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $uinfo){?>
				  	<tr>
						<td><?php echo $uinfo['title'];?></td>
						<td><?php echo $doteyInfo[$uinfo['uid']]['realname'];?>(<?php echo $doteyInfo[$uinfo['uid']]['nickname'];?>) </td>
						<td><?php echo $cates[$uinfo['cat_id']];?> </td>
						<td><?php echo $uinfo['sub_title'];?> </td>
						<td id="status_<?php echo $uinfo['record_id']?>"><?php echo $liveStatus[$uinfo['status']];?> </td>
						<td><?php echo $is_hide[$uinfo['is_hide']];?> </td>
						<td>
							<?php 
								echo '创建：'.date('Y-m-d H:i:s',$uinfo['create_time']);
								if($uinfo['status'] == START_LIVE){
									echo '</br>开播：'.date('Y-m-d H:i:s',$uinfo['live_time']);
								}
								if($uinfo['status'] == END_LIVE){
									echo '</br>开播：'.date('Y-m-d H:i:s',$uinfo['live_time']);
									echo '</br>结束：'.date('Y-m-d H:i:s',$uinfo['end_time']);
								}
							?>
							
						</td>
						<td>
							<?php if($uinfo['status'] == WIIL_START_LIVE){?>
								<a class="btn" href="#" recordId="<?php echo $uinfo['record_id'];?>" title="编辑"> <i class="icon-edit"></i></a> 
								<a class="btn" href="#" recordId="<?php echo $uinfo['record_id'];?>" title="开始直播"> <i class=" icon-play"></i></a> 
							<?php }?>
							<?php if($uinfo['status'] == START_LIVE){?>
							<a class="btn" href="#" recordId="<?php echo $uinfo['record_id'];?>" title="结束直播"> <i class=" icon-stop"></i></a> 
							<?php }?>
							<a class="btn" href="<?php echo $archivesUrl.$uinfo['uid'];?>" target="_blank"><span title="直播间链接" class="icon icon-color icon-link"></span></a>
							<!-- <a class="btn" archivesId="<?php echo $uinfo['archives_id'];?>" title="查看直播在线统计"><span class="icon-th-list"></span></a> -->
						</td>
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
					<tr>
						<td colspan="8">没有配置的数据</td>
					</tr>
				</tbody>
				  	<?php }?>
				  </tbody>
			</table>
		</div>
	</div>
	<!--/span-->
</div>

<!-- 浮层 -->
<div class="modal hide fade" id="user_list_manage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>用户管理</h3>
	</div>
	<div class="modal-body" id="user_list_manage_body"></div>
</div>


<script>
$(function() {
	//注册开始时间
	$( '#form_live_time_on' ).click(function(){
			WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm'});
		}
	);
	//注册结束时间
	$( '#form_live_time_end' ).click(function(){
			WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm'});
		}
	);
	
	//编辑操作
	$(".box-content .icon-edit").click(function(e){
		var recordId = $(this).parents('a').attr('recordId');
		if(recordId){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('dotey/onlive');?>",
				dataType:'html',
				data:{'record_id':recordId,'op':'editLiveRecords','condition':'<?php echo json_encode($condition);?>'},
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
	//结束直播
	$(".icon-stop").click(function(e){
		var recordId = $(this).parent('a').attr('recordId');
		var obj = this;
		if(recordId){
			$.ajax({
				url:"<?php echo $this->createUrl('dotey/onlive');?>",
				dataType:'text',
				type:'post',
				data:{'record_id':recordId,'op':'changeLiveStatus','type':'off'},
				success:function(msg){
					if(msg == 1){
						var statusId = "#status_"+recordId;
						$(statusId).html('直播结束');
						$(obj).parents('a').detach();
					}else{
						$('#user_list_manage_body').html(msg);
						e.preventDefault();
						$('#user_list_manage').modal('show');
					}
				}
			});
		}
	});
	//开始直播
	$(".icon-play").click(function(e){
		var recordId = $(this).parent('a').attr('recordId');
		var obj = this;
		if(recordId){
			$.ajax({
				url:"<?php echo $this->createUrl('dotey/onlive');?>",
				dataType:'text',
				type:'post',
				data:{'record_id':recordId,'op':'changeLiveStatus','type':'on'},
				success:function(msg){
					if(msg == 1){
						var statusId = "#status_"+recordId;
						$(statusId).html('正在直播');
						$(obj).removeClass("icon-play").addClass('icon-stop');
					}else{
						$('#user_list_manage_body').html(msg);
						e.preventDefault();
						$('#user_list_manage').modal('show');
					}
				}
			});
		}
	});
	//查看直播在线统计
	$('.icon-th-list').click(function(e){
		var archivesId = $(this).parent('a').attr('archivesId');
		if(archivesId){
			$.ajax({
				url:"<?php echo $this->createUrl('dotey/onlive');?>",
				dataType:'html',
				type:'post',
				data:{'archives_id':archivesId,'op':'searchLiveOnline'},
				success:function(msg){
					$('#user_list_manage_body').html(msg);
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
			if(nickname.length <= 2){
				alert("搜索昵称的关键字太少");
				return false;
			}
		}
		if(username){
			if(username.length <= 2){
				alert("搜索用户名的关键字太少");
				return false;
			}
		}
		return true;
	});
	
});
</script>