<?php
$this->breadcrumbs = array('道具购买记录');
?>
<div class="row-fluid sortable">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> 道具购买记录</h2>
		</div>
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('props/buyrecords');?>" method="post">	
				<div class="row-fluid">
					<div class="span12">
						<div class="dataTables_filter">
							<?php $select1 = isset($condition['cat_id'])?$condition['cat_id']:'';?>
							<?php echo CHtml::listBox('form[cat_id]', $select1, $allCat,array('empty'=>'-道具分类-','class'=>'input-small','size'=>1));?>
							<?php $select2 = isset($condition['source'])?$condition['source']:'';?>
							<?php echo CHtml::listBox('form[source]', $select2, $sources,array('empty'=>'-来源-','class'=>'input-small','size'=>1));?>
							<?php $select3 = isset($condition['prop_id'])?$condition['prop_id']:'';?>
							<?php echo CHtml::listBox('form[prop_id]', $select3, $allProps,array('empty'=>'-道具名称-','class'=>'input-small','size'=>1));?>
							<span>用户名:</span>
							<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-small'));?>
							<span>昵称:</span>
							<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-small'));?>
							<span>UID:</span>
							<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
							<input type="submit" name="form_search_gift_list_botton" id="form_search_gift_list_botton" value="搜索" class="btn">
							<span class="label label-important" id="check_user_info" style="display:none;margin-left:10px;"></span>
						</div>
					</div>
				</div>
			</form>
			
			<table class="table table-bordered" id="gift_list_table">
			  <thead>
				  <tr>
					  <th>道具ID</br>UID</th>
					  <th>道具名称</br>道具分类</th>
					  <th>用户名</br>昵称</th>
					  <th>购买时间</br>有效时间</th>
					  <th>来源</th>
					  <th>价格</th>
					  <th>数量</th>
					  <th>贡献值</th>
					  <th>魅力值</th>
					  <th>皮点</th>
					  <th>贡献点</th>
					  <th>购买信息</th>
				  </tr>
			  </thead>   
			  <tbody>
			  	<?php if(!empty($list)){?>
			  	<?php foreach($list as $ginfo){?>
			  	<tr>
			  		<td><?php echo $ginfo['prop_id'];?></br><?php echo $ginfo['uid'];?></td>
			  		<td><?php echo $ginfo['prop_info']['name'];?></br><?php echo $allCat[$ginfo['cat_id']];?></td>
			  		<td><?php echo $ginfo['user_info']['username'];?></br><?php echo $ginfo['user_info']['nickname'];?></td>
			  		<td>
			  			<?php echo date('Y-m-d H:i:s',$ginfo['ctime']);?></br>
			  			<?php echo ($ginfo['vtime'] == 0)?'永久':date('Y-m-d H:i:s',$ginfo['vtime']);?>
			  		</td>
			  		<td><?php echo isset($sources[$ginfo['source']])?$sources[$ginfo['source']]:'';?></td>
			  		<td><?php echo $ginfo['pipiegg'];?></td>
			  		<td><?php echo $ginfo['amount'];?></td>
			  		<td><?php echo $ginfo['dedication'];?></td>
			  		<td><?php echo $ginfo['charm'];?></td>
			  		<td><?php echo $ginfo['egg_points'];?></td>
			  		<td><?php echo $ginfo['charm_points'];?></td>
			  		<td><?php echo $ginfo['info'];?></td>
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