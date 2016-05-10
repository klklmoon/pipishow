<div class="row-fluid sortable ui-sortable">
		<div id='award_form_content'>
			<?php $this->renderPartial('_record_form')?>
		</div>
		<div class="box-content" style="overflow-y: auto;height:450px;">
			<table class="table table-bordered" id="luckygifts_award_list">
			  <thead>
				  <tr>
					  <th>ID</th>
					  <th>奖池金额</th>
					  <th>奖池A值</th>
					  <th>变化时间</th>
					 <th>编辑</th>
				  </tr>
			  </thead>   
			  <tbody id="luckygifts_record_list" >
			  	<?php if(!empty($clist['list'])){?>
			  	<?php foreach($clist['list'] as $info){?>
			  	<tr id="__<?php echo $info['id'];?>">
			  		<td><?php echo $info['id'];?></td>
			  		<td><?php echo $info['value'];?></td>
			  		<td><?php echo $info['chance'];?></td>
			  		<td><?php echo date('Y-m-d H:i:s',$info['create_time']);?></td>
			  		<td>
			  		<span class="icon icon-color icon-edit" rid="<?php echo $info['id'];?>"></span>
			  		</td>
			  	</tr>
			  	<?php }?>
			  	</tbody>
			  	<tfoot>
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
			  	</tfoot>
			  	<?php }else{?>
			  		<tbody>
				  		<tr><td colspan="5">没有配置的数据</td></tr>
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
				$('#poolRecord').html(html);
			}
		});
		$(this).attr('href','javascript:void(0);');
	});

	//编辑
	$('.icon-edit').live('click',function(){
		var rid = $(this).attr('rid');
		var obj = this;
		if(rid){
			var value = $('#luckygifts_record_list #__'+rid+' > td').eq(1).html();
			var chance = $('#luckygifts_record_list #__'+rid+' > td').eq(2).html();
			$('#rsetup_value').attr('value',value);
			$('#rsetup_chance').attr('value',chance);
			$('#rsetup_id').attr('value',rid);
 		}
	});
	//重置
	$('#_record_reset').live('click',function(){
		$(':input').each(function(i){
			$(this).attr('value','');
		});
	});
	//提交
	$(':submit').click(function(){
		var id=$('#rsetup_id').val();
		var value=$('#rsetup_value').val();
		var chance=$('#rsetup_chance').val();
		
		if(id < 1 || isNaN(id)){
			$('#info_rsetup_value').html('编辑对象有误').show();
			return false;
		}else{
			$('#info_rsetup_value').html('').hide();
		}

		if(value <= 0 || isNaN(value)){
			$('#info_rsetup_value').html('奖池金额有误').show();
			return false;
		}else{
			$('#info_rsetup_value').html('').hide();
		}

		if(chance < 0 || isNaN(chance)){
			$('#info_rsetup_chance').html('奖池A值有误').show();
			return false;
		}else{
			$('#info_rsetup_chance').html('').hide();
		}
		
		$.ajax({
			url:"<?php echo $this->createUrl('luckyGifts/index');?>",
			type:'post',
			dataType:'text',
			data:{'id':id,'value':value,'chance':chance,'op':'editPoolRecord','tab':'poolRecord'},
			success:function(msg){
				if(msg == 1){
					$('#luckygifts_record_list #__'+id+' > td').eq(1).html(value);
					$('#luckygifts_record_list #__'+id+' > td').eq(2).html(chance);
					$(':input').each(function(i){
						$(this).attr('value','');
					});
				}else{
					alert(msg);
				}
			}
		});
		return false;
	});
});
</script>