<?php
$this->breadcrumbs = array('运营管理','魅力值发放记录');
$types = $consumeSer->getGiveawayType();
$clients = $consumeSer->getClients();
$sources = $this->getConsumeSourceList();
$sourcelist = $consumeSer->getSourceList();
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
					<?php $select2 = isset($condition['client'])?$condition['client']:''?>
					<?php echo CHtml::listBox('form[client]', $select2, $clients,array('class'=>'input-small','empty'=>'--类型--','size'=>1));?>
					<?php $select2 = isset($condition['source'])?$condition['source']:''?>
					<?php echo CHtml::listBox('form[source]', $select2, $sources,array('class'=>'input-small','empty'=>'--来源--','size'=>1));?>
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
						<th>昵称(ID)</th>
						<th>姓名</th>
						<th>魅力值</th>
						<th>数量</th>
						<th>来源</th>
						<th>子来源</th>
						<th>类型</th>
						<th>说明</th>
					</tr>
				</thead>
				<tbody>
				  	<?php if(!empty($list)){?>
				  	<?php foreach($list as $uinfo){?>
				  	<tr>
						<td><?php echo date('Y-m-d H:i:s',$uinfo['create_time']);?></td>
						<td><?php echo $doteyInfo[$uinfo['uid']]['nickname'];?>(<?php echo $uinfo['uid'];?>) </td>
						<td><?php echo $doteyInfo[$uinfo['uid']]['username'];?> </td>
						<td><?php echo $uinfo['charm']?></td>
						<td><?php echo $uinfo['num']?></td>
						<td><?php echo $sourcelist[$uinfo['source']]['name'];?></td>
						<td><?php 
								if (isset($sourcelist[$uinfo['source']]['subsource'][$uinfo['sub_source']])){
									echo $sourcelist[$uinfo['source']]['subsource'][$uinfo['sub_source']];	
								}
							?>
						</td>
						<td><?php echo $clients[$uinfo['client']];?></td>
						<td><?php echo $uinfo['info'];?></td>
					</tr>
				  	<?php }?>
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

<?php $this->renderPartial('_modal_add_giveaway');?>