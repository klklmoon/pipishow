<?php
$this->breadcrumbs = array('礼物列表管理');
$buyLimit = $this->giftSer->getBuyLimitOption();
?>
<div class="row-fluid sortable">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> 礼物列表管理</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" title="添加礼物"><i class="icon-plus-sign"></i></a>
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('gift/giftlist');?>" method="post">	
				<div class="row-fluid">
					<div class="span12">
						<div class="dataTables_filter">
							<span>礼物名称:</span>
							<?php echo CHtml::textField('form[zh_name]',isset($condition['zh_name'])?$condition['zh_name']:'',array('class'=>'input-small'));?>
							<span>礼物分类：</span>
							<?php $select1 = isset($condition['cat_id'])?$condition['cat_id']:'';?>
							<select id="form_cat_id" name="form[cat_id]" class="input-small">
								<option value="">--请选择--</option>
								<?php foreach($allGiftCat as $cate){?>
								<option value="<?php echo $cate['category_id'];?>" <?php if($select1 == $cate['category_id']) echo 'selected="selected"'?>><?php echo $cate['cat_name'];?></option>
								<?php }?>
							  </select>&nbsp;&nbsp;
							 <span>状态：</span>
							 <?php $select2 = isset($condition['is_display'])?$condition['is_display']:'';?>
							 <?php echo CHtml::listBox('form[is_display]', $select2, $allStatus,array('class'=>'input-small','empty'=>'-请选择-','size'=>1))?>
							  <span>商品类型：</span>
							 <?php $select3 = isset($condition['shop_type'])?$condition['shop_type']:'';?>
							 <?php echo CHtml::listBox('form[shop_type]', $select3, $allShopType,array('class'=>'input-small','empty'=>'-请选择-','size'=>1))?>
							  <span>礼物类型：</span>
							 <?php $select4 = isset($condition['gift_type'])?$condition['gift_type']:'';?>
							 <?php echo CHtml::listBox('form[gift_type]', $select3, $allGiftType,array('class'=>'input-small','empty'=>'-请选择-','size'=>1))?>
							<input type="submit" name="form_search_gift_list_botton" id="form_search_gift_list_botton" value="搜索" class="btn">
							<span class="label label-important" id="check_user_info" style="display:none;margin-left:10px;"></span>
						</div>
					</div>
				</div>
			</form>
			
			<table class="table table-bordered" id="gift_list_table">
			  <thead>
				  <tr>
					  <th>名称</th>
					  <th>分类</th>
					  <th>礼物类型</th>
					  <th>商品类型</th>
					  <th>状态</th>
					  <th>排序</th>
					  <th>礼物价格</th>
					  <th>魅力值</th>
					  <th>魅力点</th>
					  <th>贡献值</th>
					  <th>皮点</th>
					  <th>出售数量/限购</th>
					  <th>出售等级</th>
					  <th>操作</th>
				  </tr>
			  </thead>   
			  <tbody>
			  	<?php if(!empty($giftList)){?>
			  	<?php foreach($giftList as $gid => $ginfo){?>
			  	<tr>
			  		<td><?php echo $ginfo['zh_name'];?></td>
			  		<td><?php echo isset($allGiftCat[$ginfo['cat_id']])?$allGiftCat[$ginfo['cat_id']]['cat_name']:'';?></td>
			  		<td>
			  			<?php 
			  				echo implode(",",$this->giftSer->getGiftType(intval($ginfo['gift_type'])));
			  			?>
			  		</td>
			  		<td>
			  			<?php 
			  				echo implode(",",$this->giftSer->getShopType(intval($ginfo['shop_type'])));
			  			?>
			  		</td>
			  		<td><?php echo $allStatus[$ginfo['is_display']];?></td>
			  		<td><?php echo $ginfo['sort'];?></td>
			  		<td><?php echo $ginfo['pipiegg'];?></td>
			  		<td><?php echo $ginfo['charm'];?></td>
			  		<td><?php echo $ginfo['charm_points'];?></td>
			  		<td><?php echo $ginfo['dedication'];?></td>
			  		<td><?php echo $ginfo['egg_points'];?></td>
			  		<td>
			  			<?php echo $ginfo['sell_nums'];?>/<?php echo $buyLimit[$ginfo['buy_limit']];?></td>
			  		<td><?php echo isset($allGrade[$ginfo['sell_grade']])?$allGrade[$ginfo['sell_grade']]['name']:'无';?></td>
			  		<td>
			  			<a class="" href="#" giftId="<?php echo $gid;?>"> <i class="icon-edit"></i></a>
			  			<a class="" href="#" giftId="<?php echo $gid;?>"> <i class="icon-remove"></i> </a>
			  		</td>
			  	</tr>
			  	
			  	<?php }?>
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
			  	<?php }else{?>
			  		<tbody>
				  		<tr><td colspan="15">没有配置的数据</td></tr>
			  		</tbody>
			  	<?php }?>
			  </tbody>
		  </table>            
		</div>
	</div><!--/span-->
</div>

<!-- 添加礼物分类浮层 -->
<div class="modal hide fade span10" id="gift_list" style="left:5%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>礼物分类管理</h3>
	</div>
	<div class="modal-body" id="gift_list_body">
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//新增礼物
	$(".box-icon .icon-plus-sign").click(function(e){
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('gift/addgift');?>",
			dataType:'html',
			success:function(msg){
				if(msg){
					$('#gift_list_body').html(msg);
				}else{
					$('#gift_list_body').html('信息获取失败');
				}
				e.preventDefault();
				$('#gift_list').modal('show');
			}
		});
	});
	//修改礼物
	$("#gift_list_table .icon-edit").click(function(e){
		var gift_id = $(this).parent('a').attr('giftId');
		if(gift_id){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('gift/addgift');?>",
				dataType:'html',
				data:{'op':'updateGift','gift_id':gift_id},
				success:function(msg){
					if(msg){
						$('#gift_list_body').html(msg);
					}else{
						$('#gift_list_body').html('信息获取失败');
					}
					e.preventDefault();
					$('#gift_list').modal('show');
				}
			});
		}
	});
	//删除礼物
	$("#gift_list_table .icon-remove").click(function(e){
		var gift_id = $(this).parent('a').attr('giftId');
		var obj = this;
		if(gift_id){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('gift/addgift');?>",
				dataType:'html',
				data:{'op':'delGift','gift_id':gift_id},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						$('#gift_list_body').html('删除失败');
						e.preventDefault();
						$('#gift_list').modal('show');
					}
				}
			});
		}
	});

})
</script>
