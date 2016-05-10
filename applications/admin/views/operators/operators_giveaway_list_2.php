<?php
$this->breadcrumbs = array('运营管理','道具发放记录');
$types = $consumeSer->getGiveawayType();
$sources = $_commonSer->getSourceTypeList();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-user"></i> <?php echo $types[$condition['type']];?>发放记录</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" title="新增赠品"><i class="icon-plus-sign"></i></a>
			</div>
		</div>
		
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('operators/giveaway');?>" method="post">
				<fieldset>
					<div class="control-group">
					<?php $select1 = isset($condition['type'])?$condition['type']:''?>
					<?php echo CHtml::listBox('form[type]', $select1, $types,array('class'=>'input-small','empty'=>'-赠送类型-','size'=>1));?>
					<?php $select2 = isset($condition['source'])?$condition['source']:''?>
					<?php echo CHtml::listBox('form[source]', $select2, $sources,array('class'=>'input-small','empty'=>'-来源-','size'=>1));?>
					<span>UID:</span>
					<?php echo CHtml::textField('form[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-mini'));?>
					<span>姓名:</span>
					<?php echo CHtml::textField('form[realname]',isset($condition['realname'])?$condition['realname']:'',array('class'=>'input-mini'));?>
					<span>用户名:</span>
					<?php echo CHtml::textField('form[username]',isset($condition['username'])?$condition['username']:'',array('class'=>'input-mini'));?>
					<span>昵称:</span> 
					<?php echo CHtml::textField('form[nickname]',isset($condition['nickname'])?$condition['nickname']:'',array('class'=>'input-mini'));?>
					<span>赠送时间:</span>
					<?php echo CHtml::textField('form[create_time_on]',isset($condition['create_time_on'])?$condition['create_time_on']:'',array('class'=>'date_ui input-small'));?>&nbsp;至&nbsp;
					<?php echo CHtml::textField('form[create_time_end]',isset($condition['create_time_end'])?$condition['create_time_end']:'',array('class'=>'date_ui input-small'));?>
					<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
				</fieldset>
			</form>
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>赠送时间</th>
						<th>昵称</th>
						<th>姓名</th>
						<th>道具名称</th>
						<th>魅力值</th>
						<th>魅力点</th>
						<th>皮蛋</th>
						<th>贡献值</th>
						<th>数量</th>
						<th>失效时间</th>
						<th>说明</th>
						<th>来源</th>
					</tr>
				</thead>
				<tbody>
				  	<?php if(!empty($list)){?>
				  	<?php 
				  		foreach($list as $uinfo){
				  			if(!empty($doteyInfo[$uinfo['uid']])){
				  	?>
				  	<tr>
						<td><?php echo date('Y-m-d H:i:s',$uinfo['ctime']);?></td>
						<td><?php echo $doteyInfo[$uinfo['uid']]['nickname'];?></td>
						<td><?php echo $doteyInfo[$uinfo['uid']]['username'];?> </td>
						<td><?php echo $uinfo['prop_id']['name']?></td>
						<td><?php echo $uinfo['charm']?></td>
						<td><?php echo $uinfo['charm_points']?></td>
						<td><?php echo $uinfo['pipiegg']?></td>
						<td><?php echo $uinfo['dedication']?></td>
						<td><?php echo $uinfo['amount']?></td>
						<td><?php echo $uinfo['time_desc']?></td>
						<td><?php echo $uinfo['info'];?></td>
						<td><?php echo $sources[$uinfo['source']];?></td>
					</tr>
				  	<?php 
				  				}
				  		}	
				  		?>
				  	<tr>
						<td colspan="12">
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
						<td colspan="12">没有配置的数据</td>
					</tr>
				</tbody>
				  	<?php }?>
				  </tbody>
			</table>
		</div>
	</div>
	<!--/span-->
</div>

<?php $this->renderPartial('_modal_add_giveaway');?>
