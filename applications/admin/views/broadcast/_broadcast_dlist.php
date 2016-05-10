<div class="row-fluid sortable">		
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" id="broadsearch" action="" method="post">
			  <fieldset>
			  	<div class="control-group">
			  		<span>UID</span> 
					<?php echo CHtml::textField('search2[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
				  	<span>禁用时间</span>
				  	<?php echo CHtml::textField('search2[stime]',isset($condition['stime'])?$condition['stime']:'',array('class'=>'input-small'));?>至
				  	<?php echo CHtml::textField('search2[etime]',isset($condition['etime'])?$condition['etime']:'',array('class'=>'input-small'));?>
				  	<?php echo CHtml::submitButton('search2',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
			  </fieldset>
			</form>   
			
			<table class="table table-bordered" id="broadcast_dlist">
			  <thead>
				  <tr>
					  <th>uid</th>
					  <th>昵称</th>
					  <th>用户名</th>
					  <th>禁用时间</th>
					  <th>操作</th>
				  </tr>
			  </thead>   
			  <tbody>
			  	<?php if(!empty($dlist['list'])){?>
			  	<?php foreach($dlist['list'] as $uid => $info){?>
			  	<tr>
			  		<td><?php echo $info['uid'];?></td>
			  		<td><?php echo $dlist['uinfo'][$info['uid']]['nickname'];?></td>
			  		<td><?php echo $dlist['uinfo'][$info['uid']]['username'];?></td>
			  		<td><?php echo date('Y-m-d H:i:s',$info['utime']);?></td>
			  		<td>
			  			<span title="取消禁播" class="icon icon-color icon-undo" uid='<?php echo $info['uid'];?>'></span>
			  		</td>
			  	</tr>
			  	
			  	<?php }?>
			  	<tr>
			  		<td colspan="5">
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
				  		<tr><td colspan="5">没有配置的数据</td></tr>
			  		</tbody>
			  	<?php }?>
			  </tbody>
		  </table>            
		</div>
	</div><!--/span-->
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('#search2_stime').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm:ss'});
	});
	$('#search2_etime').click(function(){
		WdatePicker({'dateFmt':'yyyy-MM-dd HH:mm:ss'});
	});
	//搜索不刷新页面
	$(':submit').click(function(){
		var uid = $("input[name='search2[uid]']").val();
		var stime = $('input[name="search2[stime]"]').val();
		var etime = $('input[name="search2[etime]"]').val();
		$.ajax({
			url:'<?php echo $this->createUrl('Broadcast/siteBroadcast');?>',
			type:'post',
			dataType:'html',
			data:{'tab':'dlist','uid':uid,'stime':stime,'etime':etime},
			success:function(html){
				$('#dlist').html(html);
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
				$('#dlist').html(html);
			}
		});
		$(this).attr('href','javascript:void(0);');
	});
	//取消禁播
	$('.icon-undo').click(function(){
		var uid = $(this).attr('uid');
		var obj = this;
		if(uid){
			$.ajax({
				url:'<?php echo $this->createUrl('Broadcast/siteBroadcast');?>',
				type:'post',
				dataType:'text',
				data:{'uid':uid,'tab':'dlist','op':'undoDisable'},
				success:function(text){
					if(text == 1){
						$(obj).parents('tr').detach();
					}else{
						alert(text);
					}
				}
			});
		}
	});
})
</script>
