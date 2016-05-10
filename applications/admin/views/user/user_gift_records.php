<?php
$this->breadcrumbs = array('用户管理','获取用户送礼明细记录');
?>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="" method="post">
			  <fieldset>
				<div class="control-group">
				  	<span>UID:</span>
				  	<?php $uid = Yii::app()->request->getParam('uid',null);?>
				  	<?php echo CHtml::hiddenField('uid',$uid);?>
				  	<?php echo CHtml::textField('sendgift[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
				  	<span>昵称:</span> 
					<?php echo CHtml::textField('sendgift[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
					<span>姓名:</span> 
					<?php echo CHtml::textField('sendgift[realname]',isset($condition['realname'])?$condition['realname']:'',array('class'=>'input-small'));?>
					<span>账号:</span> 
					<?php echo CHtml::textField('sendgift[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
				  	<span>送礼时间:</span>
				  	<?php echo CHtml::textField('sendgift[start_time]',isset($condition['start_time'])?$condition['start_time']:'',array('class'=>'input-small'));?>至
				  	<?php echo CHtml::textField('sendgift[end_time]',isset($condition['end_time'])?$condition['end_time']:'',array('class'=>'input-small'));?>
				  	<?php echo CHtml::submitButton('sendgift_search',array('class'=>'btn','value'=>'搜索','id'=>'submit_search'));?>
				  	<?php echo CHtml::submitButton('sendgift_search',array('class'=>'btn','value'=>'下载EXCEL','id'=>'submit_dlExcel'));?>
					<?php echo CHtml::button('user_gift_count',array('class'=>'btn','value'=>'礼物合计：'.$pager->getItemCount()));?>
				  	<?php echo CHtml::button('user_gift_duplicate_count',array('class'=>'btn','value'=>'用户合计：'.$records['remDuplicateCount']));?>
				  	<?php echo CHtml::button('user_gift_pipieggs_sum',array('class'=>'btn','value'=>'消费合计：'.$records['pipieggSum']));?>
				</div>
			  </fieldset>
			</form>   
			<table class="table table-bordered" id="gift_list_table">
				  <thead>
					  <tr>
						  <th>发送者(UID)</th>
						  <th>接收者(UID) </th>
						  <th>礼物名称 </th>
						  <th>礼物数量</th>
						  <th>消费皮蛋</th>
						  <th>送礼时间</th>
					  </tr>
				  </thead>   
				  <tbody>
				  	<?php if(!empty($records['list'])){?>
				  	<?php 
				  		foreach($records['list'] as $r){
				  			$info = unserialize($r['info']);
				  	?>
				  	
				  	<tr>
				  		<td><?php echo $info['sender'];?>(<?php echo $r['uid'];?>)</td>
				  		<td><?php echo $info['receiver'];?>(<?php echo $r['to_uid'];?>)</td>
				  		<td><?php echo isset($info['gift_zh_name'])?$info['gift_zh_name']:'';?></td>
				  		<td><?php echo $r['num'];?></td>
				  		<td><?php echo $r['pipiegg'];?></td>
				  		<td><?php echo date('Y-m-d H:i:s',$r['create_time']);?></td>
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

<script>
var actionUrl = "<?php echo $this->createUrl('user/ugiftrecords');?>";							
$(function(){
	//注册开始时间
	$( '#sendgift_start_time' ).datepicker(
		{ 
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//注册结束时间
	$( '#sendgift_end_time' ).datepicker(
		{ 
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	$('#submit_search').click(function(){
		if(!$('#sendgift_uid').attr('value') && (!$('#sendgift_start_time').attr('value') || !$('#sendgift_start_time').attr('value'))){
			alert('送礼时间或UID不能为空');
			return false;
		}
		
		$(this).parents('form').attr('action',actionUrl);

		var nickname = $("#sendgift_nickname").attr('value');
		var username = $("#sendgift_username").attr('value');
		var realname = $("#sendgift_realname").attr('value');
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
		if(!$('#sendgift_uid').attr('value') && (!$('#sendgift_start_time').attr('value') || !$('#sendgift_start_time').attr('value'))){
			alert('送礼时间或UID不能为空');
			return false;
		}
		
		$(this).parents('form').attr('action',actionUrl+'&op=dlSendGiftExcel');

		var nickname = $("#sendgift_nickname").attr('value');
		var username = $("#sendgift_username").attr('value');
		var realname = $("#sendgift_realname").attr('value');
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