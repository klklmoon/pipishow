<?php
$this->breadcrumbs = array('标签管理','主播印象标签');
$url = $this->createUrl('tag/doteyList');
?>
<div class="row-fluid sortable ui-sortable">
  	<div class="box span12">
  		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>主播印象标签列表</h2>
			<div class="box-icon">
				<a href="javascript:void(0);" class="btn btn-setting btn-round" title="给主播添加标签"><i class="icon-plus"></i></a>
				<a href="javascript:void(0);" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $url;?>" method="post">
			  <fieldset>
			  	<div class="control-group">
				  	<span>UID</span> 
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
					<span>标签名称</span> 
					<?php echo CHtml::textField('form[tag_name]',isset($condition['tag_name'])?$condition['tag_name']:'',array('class'=>'input-small'));?>
					<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
			  </fieldset>
			</form> 
		
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width:200px">主播(uid)</th>
						<th style="width:50px">等级</th>
						<th>标签(鼠标移动点击删除)</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if($list['count']):?>
					<?php foreach($list['list'] as $k=>$v):?>
					<tr>
						<td><?php echo $v['nickname'].'('.$v['uid'].')';?></td>
						<td><?php echo $v['rank_name'];?></td>
						<td class='tags'>
							<?php foreach($v['tags'] as $tag){?>
							<span class="btn btn-<?php echo isset($condition['tag_name']) && $condition['tag_name'] == $tag['tag_name'] ? 'inverse' : ($tag['is_display'] == 1 ? 'success' : 'info');?>" data_uid="<?php echo $v['uid'];?>" data_tag_id="<?php echo $tag['tag_id'];?>"><?php echo $tag['tag_name'];?></span>
							<?php }?>
						</td>
						<td>
							<a class="btn" title="编辑"><i class="icon-edit" operateId="<?php echo $v['uid'];?>"></i></a>
							<em class="hide" 
								data_tag_ids="<?php echo $v['tag_ids'];?>" 
								data_nickname="<?php echo $v['nickname']?>"
							></em>
						</td>
					</tr>
					<?php endforeach;?>
					<tr>
				  		<td colspan="10">
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
					            'pages' => $list['pages'],    
					            'maxButtonCount'=>8    
								)
							);
							?>
							</div>
				  		</td>
				  	</tr>
					<?php endif;?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal hide fade span4" id="global_video_edit" style="left:33%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>分配主播印象标签</h3>
	</div>
	<div class="modal-body" id="global_chatword_edit_body">
		<form class="form-horizontal" method="post" action="<?php echo $url;?>" enctype="multipart/form-data" id="myForm">
		<input type="hidden" name="op" value="doteyAdd" />
		<table>
			<tr id='selectUser'>
				<td>用户名/UID</td>
				<td>
					<input class="input-small focused" id="indexForm_dotey_name" name="dotey_info" type="text" value="">
			  		<input class="btn" type="button" value="验证" name="valid_dotey_info" id="valid_dotey_info" isDotey="0">
			  		<span class="label label-important"  id="valid_dotey_info_noty" style="margin-left:10px; display:none;" isSubmit="1"></span>
				</td>
			</tr>
			<tr id='user' style='display:none;'>
				<td>主播</td>
				<td></td>
			</tr>
			<tr id='tags' style='display:none;'>
				<td>印象标签</td>
				<td></td>
			</tr>
		</table>
		</form>
		<button class="btn btn-large btn-danger">确认</button>
		<button class="btn btn-large btn-success">取消</button>
	</div>
</div>

<div class="modal hide fade span3" id="loading" style="left:40%;top:50%;width:152px;z-index: -1;">
	<div class="box span12">
		<div class="box-content">
			<div class="tab-content" style="overflow-x:hidden;overflow-y:hidden;">
				<div id="loading" style="text-align:center">努力加载中...<div class="center"></div></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var tags=<?php echo json_encode($tags);?>;
$(function(){
	<?php if(!empty($message)){?>
		noty({"text":"<?php echo $message;?>","layout":"top","type":"information"});
	<?php }?>
	
	$('.box-icon .icon-plus').parent('a').click(function(e){
		$('#global_video_edit #selectUser').show();
		$('#global_video_edit #user').hide();
		$('#global_video_edit #tags').hide();
		$('#global_video_edit').modal('show');
	});

	//验证用户
	$("#valid_dotey_info").click(function(){
		var doteyName = $("#indexForm_dotey_name").attr('value');
		
		if(doteyName){
			$("#valid_dotey_info_noty").html("").hide();
			$.ajax({
				url:"<?php echo $url;?>",
				type:"post",
				dataType:"text",
				data:{"op":"valid", "doteyName":doteyName},
				success:function(msg){
					var data = msg.split('#xx#');
					if(data[0] == 1){
						var doteyUid 		= data[1];
						var doteyUsername 	= data[2];
						var doteyNickname 	= data[3];
						var tagIds			= data[4];
						var isReturn = false;

						$('#valid_dotey_info_noty').html('').hide();
						
						var html = '';
						html += '<label class="control-label" style="width:100px;text-align:left;">'+doteyUsername+'</label>';
						html += '<div class="controls" style="margin-left:100px;">';
						html += '<input class="input-small focused" name="form[uid]" type="text" value="'+doteyUid+'" readonly="readonly">';
						html += '</div>';  
						$('#user td').eq(1).html(html);
						$('#user').show();

						var html = '';
						var tagIdsArr = tagIds.split(',');
						for(var i in tags){
							checked = false;
							for(j=0;j<tagIdsArr.length;j++){
						        if(tagIdsArr[j] == tags[i].tag_id){
						        	checked = true;
						        	break;
						        }
						    }
							html += '<input name="form[tag_id][]" type="checkbox" id="tag_'+tags[i].tag_id+'" value="'+tags[i].tag_id+'"'+(checked ? 'checked="checked" disabled="disabled"' : '')+'><lable class="checkbox inline" for="tag_'+tags[i].tag_id+'" style="padding-left:5px; padding-right:10px;">'+tags[i].tag_name+'</lable>';
						}
						$('#tags td').eq(1).html(html);
						$('#tags').show();
					}else{
						$("#valid_dotey_info_noty").html(msg).show();
					}
				}
			});
		}else{
			$("#valid_dotey_info_noty").html("请输入用户名称或UID").show();
		}
	});
	
	// 添加
	$('#global_video_edit .btn-danger').click(function(){
		$('#global_video_edit form').submit();
	});
	
	// 编辑
	$('div.box-content i.icon-edit').parent('a').click(function(){
		var operateId = $(this).children('i.icon-edit').attr('operateId');
		var _em = $(this).parents('tr').children('td').find('em');
		
		$('#global_video_edit input[name="op"]').val('doteyEdit');
		$('#global_video_edit #selectUser').hide();
		
		var html = '';
		html += '<label class="control-label" style="width:100px;text-align:left;">'+_em.attr('data_nickname')+'</label>';
		html += '<div class="controls" style="margin-left:100px;">';
		html += '<input class="input-small focused" name="form[uid]" type="text" value="'+operateId+'" readonly="readonly">';
		html += '</div>';  
		$('#user td').eq(1).html(html);
		$('#global_video_edit #user').show();
		
		var html = '';
		var tagIdsArr = _em.attr('data_tag_ids').split(',');
		for(var i in tags){
			checked = false;
			for(j=0;j<tagIdsArr.length;j++){
		        if(tagIdsArr[j] == tags[i].tag_id){
		        	checked = true;
		        	break;
		        }
		    }
			html += '<input name="form[tag_id][]" type="checkbox" id="tag_'+tags[i].tag_id+'" value="'+tags[i].tag_id+'"'+(checked ? 'checked="checked" disabled="disabled"' : '')+'><lable class="checkbox inline" for="tag_'+tags[i].tag_id+'" style="padding-left:5px; padding-right:10px;">'+tags[i].tag_name+'</lable>';
		}
		$('#tags td').eq(1).html(html);
		$('#global_video_edit #tags').show();
		
		$('#global_video_edit').modal('show');
	});
	
	//hover事件
	$(".tags span").hover(
		function(){
			$(this).removeClass('btn-success');
			$(this).removeClass('btn-inverse');
			$(this).addClass('btn-danger');
		},
		function(){
			$(this).removeClass('btn-danger');
			if($(this).attr('data_tag_id') == '<?php echo isset($condition['tag_id']) ? $condition['tag_id'] : 0;?>'){
				$(this).addClass('btn-inverse');
			}else{
				var tag;
				for(var i in tags){
					if(tags[i] == $(this).attr('data_tag_id'))
						tag = tags[i];
				}
				if(!tag || tag.is_display == 1) $(this).addClass('btn-success');
				else $(this).addClass('btn-info');
			}
		}	
	);
	
	//删除事件
	$(".tags span").click(function(e){
		if(confirm('确定删除么？')){
			var uid = $(this).attr('data_uid');
			var tagId = $(this).attr('data_tag_id');
			var obj = this;

			if(uid && tagId){
				$('#loading').css('z-index', 1100);
				$('#loading').modal('show');
				$.ajax({
					type:'post',
					dataType:'text',
					data:{'uid':uid,'tag_id':tagId,'op':'doteyDelete'},
					url:"<?php echo $url;?>",
					success:function(msg){
						$("#loading").modal('hide');
						$('#loading').css('z-index', -1);
						if(msg == 1){
							var c =$(obj).parent().children("span").length;
							if(c <= 1) {
								$(obj).parent().parent().detach();
							}else{
								$(obj).detach();
							}
						}else{
							alert(msg);
						}
					}
					
				});
			}
		}
	});
	
	$('#global_video_edit .btn-success').click(function(){
		$("#global_video_edit").modal('hide');
	});
});
</script>