<?php 
$types = $this->getAwardTypes();
$giftInfos = $this->getGiftListOption(false,false);
$propInfos = $this->getPropList();
$sources = $this->getSources();
$subSources = $this->getSubSources();
?>
<div class="row-fluid sortable ui-sortable">
		<div class="box-content">
		<form class="form-horizontal" id="broadsearch" action="" method="post">
		  <fieldset>
		  	<div class="control-group">
		  		<?php echo CHtml::listBox('search[type]', isset($condition['type'])?$condition['type']:'', $types,array('empty'=>'-类型-','size'=>1,'class'=>'input-small'))?>
		  		<?php echo CHtml::listBox('search[source]', isset($condition['source'])?$condition['source']:'', $sources,array('empty'=>'-来源-','size'=>1,'class'=>'input-small'))?>
		  		<?php echo CHtml::listBox('search[sub_source]', isset($condition['sub_source'])?$condition['sub_source']:'', $subSources,array('empty'=>'-子来源-','size'=>1,'class'=>'input-small'))?>
		  		<span>UID</span> 
				<?php echo CHtml::textField('search[uid]',isset($condition['uid'])?$condition['uid']:'',array('class'=>'input-small'));?>
			  	<span>奖励时间</span>
			  	<?php echo CHtml::textField('search[stime]',isset($condition['stime'])?$condition['stime']:'',array('class'=>'input-small'));?>至
			  	<?php echo CHtml::textField('search[etime]',isset($condition['etime'])?$condition['etime']:'',array('class'=>'input-small'));?>
			  	<?php echo CHtml::submitButton('search',array('class'=>'btn','value'=>'搜索','id'=>'award_record_search'));?>
			</div>
		  </fieldset>
		</form> 
		</div>
		<div class="box-content">
			<table class="table table-bordered" id="luckygifts_award_list">
			  <thead>
				  <tr>
					  <th>ID</br>分类</th>
					  <th>赠送者</br>UID</th>
					  <th>礼物记录</th>
					  <th>目标</br>Target ID</th>
					  <th>奖励目标</br>Target ID</th>
					  <th>数量/倍数</br>皮蛋数</th>
					  <th>来源</br>子来源</th>
					  <th>描述</th>
					  <th>奖励时间</th>
				  </tr>
			  </thead>   
			  <tbody id="luckygifts_award_record_list">
			  	<?php if(!empty($clist['list'])){?>
			  	<?php foreach($clist['list'] as $info){?>
			  	<tr id="__<?php echo $info['record_id'];?>">
			  		<td><?php echo $info['record_id'];?></br><?php echo $types[$info['type']];?></td>
			  		<td><?php echo isset($uinfos[$info['uid']])?$uinfos[$info['uid']]['nickname']:'空';?></br><?php echo $info['uid'];?></td>
			  		<td><?php echo $info['record_sid'];?></td>
			  		<td>
			  			<?php 
			  				if(isset($giftInfos[$info['target_id']])){
			  					$_info = $giftInfos[$info['target_id']];
			  					echo "礼物({$_info})</br>";
			  				}
			  				
			  				if(isset($propInfos[$info['target_id']])){
			  					$_info = $propInfos[$info['target_id']]['name'];
			  					echo "道具({$_info})</br>";
			  				}
			  				echo $info['target_id'];
			  			?>
			  		</td>
			  		<td>
			  			<?php 
			  				if(isset($giftInfos[$info['to_target_id']])){
			  					$_info = $giftInfos[$info['to_target_id']];
			  					echo "礼物({$_info})</br>";
			  				}
			  				
			  				if(isset($propInfos[$info['to_target_id']])){
			  					$_info = $propInfos[$info['to_target_id']]['name'];
			  					echo "道具({$_info})</br>";
			  				}
			  				echo $info['to_target_id'];
			  			?>
			  		</td>
			  		<td><?php echo $info['num'];?></br><?php echo $info['pipiegg'];?></td>
			  		<td><?php echo isset($sources[$info['source']])?$sources[$info['source']]:'不详';?></br><?php echo isset($subSources[$info['sub_source']])?$subSources[$info['sub_source']]:'不详';?></td>
			  		<td><?php echo $info['info'];?></td>
			  		<td><?php echo date('Y-m-d H:i:s',$info['create_time']);?></td>
			  	</tr>
			  	<?php }?>
			  	</tbody>
			  	<tfoot>
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
			  	</tfoot>
			  	<?php }else{?>
			  		<tbody>
				  		<tr><td colspan="9">没有配置的数据</td></tr>
			  		</tbody>
			  	<?php }?>
		  </table>
		</div>
</div>

<script type="text/javascript">
$(function(){
	//分页不刷新页面
	$('.pagination > ul > li > a').click(function(){
		var href = $(this).attr('href');
		$.ajax({
			url:href,
			type:'post',
			dataType:'html',
			success:function(html){
				$('#awardRecord').html(html);
			}
		});
		$(this).attr('href','javascript:void(0);');
	});

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
		var type = $('#search_type option:selected').val();
		var source = $('#search_source option:selected').val();
		var sub_source = $('#search_sub_source option:selected').val();
		$.ajax({
			url:'<?php echo $this->createUrl('luckyGifts/index');?>',
			type:'post',
			dataType:'html',
			data:{'tab':'awardRecord','uid':uid,'stime':stime,'etime':etime,'type':type,'source':source,'sub_source':sub_source},
			success:function(html){
				$('#awardRecord').html(html);
			}
		});
		return false;
	});
	
});
</script>