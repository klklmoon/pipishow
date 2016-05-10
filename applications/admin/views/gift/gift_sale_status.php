<?php
$this->breadcrumbs = array('销售统计');
?>
<div class="row-fluid sortable">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> 销售统计</h2>
		</div>
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('gift/salestatus');?>" method="post">	
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
					  <th>ID</th>
					  <th>名称</th>
					  <th>价格</th>
					  <th>分类</th>
					  <th>礼物类型</th>
					  <th>商品类型</th>
					  <th>状态</th>
					  <th>累计销量</th>
					  <th>上月销售</th>
					  <th>本月销售</th>
					  <th>上周销量</th>
					  <th>本周销量</th>
					  <th>昨日销量</th>
				  </tr>
			  </thead>   
			  <tbody>
			  	<?php if(!empty($giftList)){?>
			  	<?php foreach($giftList as $gid => $ginfo){?>
			  	<tr>
			  		<td><?php echo $ginfo['gift_id'];?></td>
			  		<td><?php echo $ginfo['zh_name'];?></td>
			  		<td><?php echo $ginfo['pipiegg'];?></td>
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
			  		<td><?php if (isset($ginfo['allSum'])) echo $ginfo['allSum'];else echo 0;?> </td>
			  		<td><?php if (isset($ginfo['lastMonth'])) echo $ginfo['lastMonth'];else echo 0;?></td>
			  		<td><?php if (isset($ginfo['theMonth'])) echo $ginfo['theMonth'];else echo 0;?></td>
			  		<td><?php if (isset($ginfo['lastWeek'])) echo $ginfo['lastWeek']; else echo 0;?></td>
			  		<td><?php if (isset($ginfo['theWeek'])) echo $ginfo['theWeek']; else echo 0;?></td>
			  		<td><?php if (isset($ginfo['lastDay'])) echo $ginfo['lastDay']; else echo 0;?></td>
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