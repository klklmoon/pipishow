<?php
$this->breadcrumbs = array('礼物之星图片管理');
?>
<div class="row-fluid sortable">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> 礼物之星图片管理</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" title="添加礼物"><i class="icon-plus-sign"></i></a>
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('Activities/GiftStarImgList');?>" method="post">	
				<div class="row-fluid">
					<div class="span12">
						<div class="dataTables_filter">
							<span>图片id:</span>
							<?php echo CHtml::textField('form[img_id]',isset($condition['img_id'])?$condition['img_id']:'',
								array('class'=>'input-small'));?>
							<span>礼物id：</span>
							<?php echo CHtml::textField('form[gift_id]',isset($condition['gift_id'])?$condition['gift_id']:'',
								array('class'=>'input-small'));?>
							 
							<input type="submit" name="form_search_gift_list_botton" id="form_search_gift_list_botton" value="搜索" class="btn">
							<span class="label label-important" id="check_user_info" style="display:none;margin-left:10px;"></span>
						</div>
					</div>
				</div>
			</form>
			
			<table class="table table-bordered" id="img_list_table">
			  <thead>
				  <tr>
					  <th>图片id</th>
					  <th>礼物名称</th>
					  <th>图片文件</th>
					  <th>图片序号</th>
					  <th>图片描述</th>
					  <th>操作</th>
				  </tr>
			  </thead>   
			  <tbody>
			  	<?php foreach($giftImgList as $img_id => $giftImgRow):?>
			  	<tr>
			  		<td><?php echo $giftImgRow['img_id'];?></td>
			  		<td><?php 
				  		$giftInfos=$this->giftService->getGiftByIds(array($giftImgRow['gift_id']));
				  		echo $giftInfos[$giftImgRow['gift_id']]['zh_name'];
			  		?></td>
			  		<td><img src="<?php echo $this->giftService->getShowAdminGiftUrl($giftImgRow['image']);?>" /></td>
			  		<td><?php echo $giftImgRow['order_number'];	?></td>
			  		<td><?php echo $giftImgRow['summary'];	?></td>
			  		<td>
			  			<a class="" href="#" img_id="<?php echo $giftImgRow['img_id'];?>"> <i class="icon-edit"></i></a>
			  		</td>
			  	</tr>
			  	<?php endforeach;?>
			  	<tr>
			  		<td colspan="15">
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
			  </tbody>
		  </table>            
		</div>
	</div><!--/span-->
</div>

<!-- 添加礼物分类浮层 -->
<div class="modal hide fade span10" id="img_list" style="left:5%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>礼物分类管理</h3>
	</div>
	<div class="modal-body" id="img_list_body">
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//新增礼物
	$(".box-icon .icon-plus-sign").click(function(e){
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('Activities/GiftStarImg');?>",
			dataType:'html',
			success:function(msg){
				if(msg){
					$('#img_list_body').html(msg);
				}else{
					$('#img_list_body').html('信息获取失败');
				}
				e.preventDefault();
				$('#img_list').modal('show');
			}
		});
	});
	
	//修改礼物
	$("#img_list_table .icon-edit").click(function(e){
		var img_id = $(this).parent('a').attr('img_id');
		if(img_id){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('Activities/GiftStarImg');?>",
				dataType:'html',
				data:{'op':'updateGiftInfo','img_id':img_id},
				success:function(msg){
					if(msg){
						$('#img_list_body').html(msg);
					}else{
						$('#img_list_body').html('信息获取失败');
					}
					e.preventDefault();
					$('#img_list').modal('show');
				}
			});
		}
	});

})
</script>
