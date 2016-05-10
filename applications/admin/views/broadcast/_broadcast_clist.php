<div class="row-fluid sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="broadsearch" action="" method="post">
			  <fieldset>
			  	<div class="control-group">
			  		<span>UID</span> 
					<?php echo CHtml::textField('search[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
			  		<span>档期ID</span> 
					<?php echo CHtml::textField('search[aid]',isset($condition['aid'])?$condition['aid']:'',array('class'=>'input-small'));?>
			  		<span>主播UID</span> 
					<?php echo CHtml::textField('search[dotey_uid]',isset($condition['dotey_uid'])?$condition['dotey_uid']:'',array('class'=>'input-small'));?>
				  	<span>禁用时间</span>
				  	<?php echo CHtml::textField('search[stime]',isset($condition['stime'])?$condition['stime']:'',array('class'=>'input-small'));?>至
				  	<?php echo CHtml::textField('search[etime]',isset($condition['etime'])?$condition['etime']:'',array('class'=>'input-small'));?>
				  	<?php echo CHtml::submitButton('search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
			  </fieldset>
			</form>   
			
			<table class="table table-bordered" id="broadcast_dlist">
			  <thead>
				  <tr>
					  <th>uid</th>
					  <th>昵称</br>用户名</th>
					  <th>来源直播间</br>来源主播</th>
					  <th>消费</th>
					  <th>广播内容</th>
					  <th>广播时间</th>
					  <th>操作</th>
				  </tr>
			  </thead>   
			  <tbody>
			  	<?php if(!empty($clist['list'])){?>
			  	<?php foreach($clist['list'] as $uid => $info){?>
			  	<tr>
			  		<td><?php echo $info['uid'];?></td>
			  		<td><?php echo $clist['uinfo'][$info['uid']]['nickname'];?></br><?php echo $clist['uinfo'][$info['uid']]['username'];?></td>
			  		<td><?php echo $clist['ainfo'][$info['aid']]['title'];?></br><?php echo isset($clist['doteyInfo'][$info['dotey_uid']])? $clist['doteyInfo'][$info['dotey_uid']]['nickname']:'NULL';?></td>
			  		<td><?php echo $info['price'];?></td>
			  		<td><?php echo $info['content'];?></td>
			  		<td><?php echo date('Y-m-d H:i:s',$info['ctime']);?></td>
			  		<td>
			  			<?php if(isset($clist['disableInfo'][$info['uid']])){?>
			  			<span class="icon icon-color icon-cancel"></span>
			  			<?php }else{?>
			  			<span class="icon icon-color icon-volume-on" title="禁止广播" uid='<?php echo $info['uid'];?>'></span>
			  			<?php }?>
			  			
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
<script type="text/javascript">
$(document).ready(function(){
	$('#search_stime').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm:ss'});
	});
	$('#search_etime').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm:ss'});
	});
	//搜索不刷新页面
	$(':submit').click(function(){
		var uid = $('input[name="search[uid]"]').val();
		var stime = $('input[name="search[stime]"]').val();
		var etime = $('input[name="search[etime]"]').val();
		var aid = $('input[name="search[aid]"]').val();
		var dotey_uid = $('input[name="search[dotey_uid]"]').val();
		$.ajax({
			url:'<?php echo $this->createUrl('Broadcast/siteBroadcast');?>',
			type:'post',
			dataType:'html',
			data:{'tab':'clist','uid':uid,'stime':stime,'etime':etime,'aid':aid,'dotey_uid':dotey_uid},
			success:function(html){
				$('#clist').html(html);
			}
		});
		return false;
	});
	//分页不刷新页面
	$('.pagination > ul > li > a').click(function(){
		var href = $(this).attr('href');
		$.ajax({
			url:href,
			type:'post',
			dataType:'html',
			success:function(html){
				$('#clist').html(html);
			}
		});
		$(this).attr('href','javascript:void(0);');
	});
	//禁播
	$('.icon-volume-on').click(function(){
		var uid = $(this).attr('uid');
		var obj = this;
		if(uid){
			$.ajax({
				url:'<?php echo $this->createUrl('Broadcast/siteBroadcast');?>',
				type:'post',
				dataType:'text',
				data:{'uid':uid,'tab':'clist','op':'doDisable'},
				success:function(text){
					if(text == 1){
						$(obj).removeClass('icon icon-color icon-volume-on');
						$(obj).addClass('icon icon-color icon-cancel');
					}else{
						alert(text);
					}
				}
			});
		}
	});
})
</script>
