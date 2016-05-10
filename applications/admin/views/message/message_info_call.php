<?php
$this->breadcrumbs = array('运营管理','消息提醒');
$types = $this->getCategory();
$stypes = $this->getSCategory();
$readStatus = $this->getReadStatus();
$extras = $this->getExtraFlag();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-user"></i> 消息提醒记录</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" title="新增消息提醒 "><i class="icon-plus-sign"></i></a>
			</div>
		</div>
		
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('message/infoCall');?>" method="post">
				<fieldset>
					<div class="control-group">
					<span>UID:</span> 
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
					<span>消息时间:</span>
					<?php echo CHtml::textField('form[create_time_on]',isset($condition['create_time_on'])?$condition['create_time_on']:'',array('class'=>'date_ui input-small'));?>&nbsp;至&nbsp;
					<?php echo CHtml::textField('form[create_time_end]',isset($condition['create_time_end'])?$condition['create_time_end']:'',array('class'=>'date_ui input-small'));?>
					<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
				</fieldset>
			</form>
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>ID</th>
						<th>类型</br>子类型</th>
						<th>创建者(UID)</th>
						<th style="overflow: hidden;white-space: nowrap;width:120px;">接收对象</th>
						<th style="overflow: hidden;white-space: nowrap;width:120px;">标题</th>
						<th style="overflow: hidden;white-space: nowrap;width:200px;">内容</th>
						<th>扩展信息</th>
						<th>创建时间</br>更新时间</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
				  	<?php if(!empty($data['list'])){?>
				  	<?php 
				  		foreach($data['list'] as $uinfo){
				  			if(isset($stypes[$uinfo['category']]) && isset($stypes[$uinfo['category']]['child'][$uinfo['sub_category']])){	
				  	?>
				  	<tr>
				  		<td><?php echo $uinfo['message_id']?></td>
				  		<td><?php echo $stypes[$uinfo['category']]['name'];?></br><?php echo $stypes[$uinfo['category']]['child'][$uinfo['sub_category']];?></td>
				  		<td><?php echo isset($uinfos[$uinfo['uid']])?$uinfos[$uinfo['uid']]['username']:'';?>(<?php echo $uinfo['uid'];?>)</td>
				  		<td><p style="border:0px;overflow-y: auto;overflow-x:auto;width:120px;height:50px;size:10px;"><?php echo $uinfo['receive_uid'];?></p></td>
				  		<td><p style="border:0px;overflow-y: auto;overflow-x:auto;width:120px;height:50px;size:10px;"><?php echo $uinfo['title']?></p></td>
				  		<td style="margin:0px;padding:0px;"><p style="border:0px;overflow-y: auto;overflow-x:auto;width:200px;height:50px;size:10px;"><?php echo $uinfo['content']?></p></td>
						<td>
							<?php 
							 foreach($uinfo['extra'] as $k=>$v){
							 	if($v && isset($extras[$k])){
							 		echo $extras[$k]['name'].':'.$v.'</br>';
							 	}
							 }
							?>
						</td>
						<td><?php echo date('Y-m-d H:i:s',$uinfo['create_time']);?></br><?php echo date('Y-m-d H:i:s',$uinfo['update_time']);?></td>
						<td>
							<span class="icon icon-color icon-close" mid="<?php echo $uinfo['message_id'];?>"></span>
						</td>
					</tr>
				  	<?php 
				  			}
				  		}
				  	?>
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
					<tr>
						<td colspan="9">没有配置的数据</td>
					</tr>
				</tbody>
				  	<?php }?>
				  </tbody>
			</table>
		</div>
	</div>
	<!--/span-->
</div>

<div class="modal hide fade" id="dotey_award_manage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>新增消息提醒</h3>
	</div>
	<div class="modal-body" id="dotey_award_manage_body"></div>
</div>

<script>
$(function() {
	$( '#form_create_time_on' ).datepicker(
		{ 
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	$( '#form_create_time_end' ).datepicker(
		{ 
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
			if(realname.length <= 1){
				alert("搜索姓名的关键字太少");
				return false;
			}
		}
		if(username){
			if(username.length <= 2){
				alert("搜索账号的关键字太少");
				return false;
			}
		}
		return true;
	});
	//新增赠品
	$('.icon-plus-sign').click(function(e){
		$.ajax({
			url:"<?php echo $this->createUrl('message/infoCall');?>",
			type:'post',
			dataType:'html',
			data:{'op':'addMessage'},
			success:function(msg){
				e.preventDefault();
				$('#dotey_award_manage_body').html(msg);
				$('#dotey_award_manage').modal('show');
			}
		});
	});
	//删除
	$('.icon-close').live('click',function(){
		var mid = $(this).attr('mid');
		var obj = this;
		if(mid){
			$.ajax({
				url:"<?php echo $this->createUrl('message/infoCall');?>",
				type:'post',
				dataType:'html',
				data:{'mid':mid,'op':'delMessage'},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						alert(msg);
					}		
				}
			});
		}
	});
});
</script>