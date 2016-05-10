<?php 
	$giftList = $this->getGiftListOption(false);
	$giftListEgg = $this->getGiftListOption(false,true);
	$propList = $this->getPropList();
	$types = $this->getAwardTypes();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<div id='award_form_content'>
				<?php $this->renderPartial('_award_form',array('types'=>$types))?>
			</div>
			<table class="table table-bordered" id="luckygifts_award_list">
			  <thead>
				  <tr>
					  <th>ID</th>
					  <th>类型</th>
					  <th>幸运礼物</th>
					  <th>礼物ID</th>
					  <th>目标对象</th>
					  <th>对象ID</th>
					  <th>奖品倍数</th>
					  <th>中奖概率</th>
					  <th>单价</th>
					  <th>操作</th>
				  </tr>
			  </thead>   
			  <tbody>
			  	<?php if(!empty($clist['list'])){?>
			  	<?php foreach($clist['list'] as $info){?>
			  	<tr id="__<?php echo $info['id'];?>">
			  		<td><?php echo $info['id'];?></td>
			  		<td><?php echo $types[$info['type']];?></td>
			  		<td><?php echo $giftList[$info['gift_id']];?></td>
			  		<td><?php echo $info['gift_id'];?></td>
			  		<td>
			  			<?php 
			  				if ($info['type'] == 1){
								echo $giftList[$info['target_id']];
			  				}elseif ($info['type'] == 2){
			  					echo $propList[$info['target_id']]['name'];
			  				}else{
			  					echo "NULL";
			  				}
			  			?>
			  		</td>
			  		<td>
			  			<?php 
			  				echo $info['target_id'];
			  			?>
			  		</td>
			  		<td><?php echo $info['award'];?></td>
			  		<td><?php echo $info['chance'];?></td>
			  		<td>
			  			<?php 
			  				if ($info['type'] == 1){
								echo $giftListEgg[$info['target_id']];
			  				}elseif ($info['type'] == 2){
			  					echo $propList[$info['target_id']]['pipiegg'];
			  				}else{
			  					echo "0";
			  				}
			  			?>
			  		</td>
			  		<td>
			  			<?php if($info['type'] == 2){?>
			  			<span class="icon icon-color icon-edit" awardId="<?php echo $info['id'];?>" type="<?php echo $info['type'];?>" catId="<?php echo $propList[$info['target_id']]['cat_id']?>"></span>
			  			<span class="icon icon-color icon-close" awardId="<?php echo $info['id'];?>" type="<?php echo $info['type'];?>" catId="<?php echo $propList[$info['target_id']]['cat_id']?>"></span>
			  			<?php }else{?>
			  			<span class="icon icon-color icon-edit" awardId="<?php echo $info['id'];?>" type="<?php echo $info['type'];?>"></span>
			  			<span class="icon icon-color icon-close" awardId="<?php echo $info['id'];?>" type="<?php echo $info['type'];?>"></span>
			  			<?php }?>
			  		</td>
			  	</tr>
			  	<?php }?>
			  	</tbody>
			  	<tfoot>
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
				  		<tr><td colspan="10">没有配置的数据</td></tr>
			  		</tbody>
			  	<?php }?>
		  </table>
		</div>
	</div>
</div>

<script style="text/javascript">
$(function() {
	var url = "<?php echo $this->createUrl('luckyGifts/index');?>";
	var TYPE_NULL = 0;
	var TYPE_GIFT = 1;
	var TYPE_PROP = 2;
	var TYPE_EGGS = 3;

	function getAwardView(type,isContinue,obj){
		if(type >= 0 ){
			$.ajax({
				url:url,
				type:'post',
				dataType:'html',
				data:{'type':type,'op':'getAwardView','tab':'giftAward'},
				success:function(msg){
					$('#setup_info_text').html(msg).show();
					if(isContinue){
						var id = $(obj).attr('awardId');
						var target_id = $('#luckygifts_award_list #__'+id+' > td').eq(5).html();
						var award = $('#luckygifts_award_list #__'+id+' > td').eq(6).html();
						if(type == TYPE_PROP){
							var catId = $(obj).attr('catId');
							$('#setup_cat_id option').each(function(i){
								if($(this).attr('value') == catId){
									$(this).attr('selected','selected');
									checkPropList(catId,true,target_id,award);
								}
							});
						}else{
							$('#setup_award').attr('value',award);
							$('#setup_target_id option').each(function(i){
								if(parseInt($(this).attr('value')) == parseInt(target_id)){
									$(this).attr('selected','selected');
								}
							});
						}
					}
				}
			});
		}else{
			$('#setup_info_text').html('').hide();
		}
	}

	function submitAward(chance,id,target_id,target_text,award,gift_id,gift_text,type,type_text,pipiegg){
		$.ajax({
			url:url,
			type:'post',
			dataType:'html',
			data:{'chance':chance,'id':id,'target_id':target_id,'award':award,'gift_id':gift_id,'type':type,'op':'addAward','tab':'giftAward'},
			success:function(msg){
				if(msg == 'fail'){
					alert('更新失败');
				}else if(msg == 'success'){
					$('#luckygifts_award_list #__'+id+' > td').eq(1).html(type_text);
					$('#luckygifts_award_list #__'+id+' > td').eq(2).html(gift_text);
					$('#luckygifts_award_list #__'+id+' > td').eq(3).html(gift_id);
					$('#luckygifts_award_list #__'+id+' > td').eq(4).html(target_text);
					$('#luckygifts_award_list #__'+id+' > td').eq(5).html(target_id);
					$('#luckygifts_award_list #__'+id+' > td').eq(6).html(award);
					$('#luckygifts_award_list #__'+id+' > td').eq(7).html(chance);
					$('#luckygifts_award_list #__'+id+' > td').eq(8).html(pipiegg);
				}else{
					var _html = '<tr id="__'+msg+'">'+
				  		'<td>'+msg+'</td>'+
				  		'<td>'+type_text+'</td>'+
				  		'<td>'+gift_text+'</td>'+
				  		'<td>'+gift_id+'</td>'+
				  		'<td>'+target_text+'</td>'+
				  		'<td>'+target_id+'</td>'+
				  		'<td>'+award+'</td>'+
				  		'<td>'+chance+'</td>'+
				  		'<td>'+pipiegg+'</td>'+
				  		'<td>'+
				  			'<span class="icon icon-color icon-edit" awardId="'+msg+'" type="'+type+'"></span>'+
				  			'<span class="icon icon-color icon-close" awardId="'+msg+'" type="'+type+'"></span>'+
				  		'</td>'+
				  	'</tr>';
				  	$('#luckygifts_award_list > tbody > tr').last().after(_html);
					if(type == TYPE_PROP){
						$('#luckygifts_award_list #__'+msg+' >td').last().children('span').each(function(i){
							$(this).attr('catId',$('#setup_cat_id').attr('value'));
						});
					}
				}
			}
		});
	}
	//改变状态动作 初始化数据
	$('#setup_type').change(function(e){
		var type = $(this).attr('value');
		getAwardView(type,false,this);
	});

	//提交
	$(':submit').click(function(){
		var chance=$('#setup_chances').val();
		var id=$('#setup_award_id').val();
		var gift_text = $('#setup_gift_id option:selected').text();
		var gift_id=$('#setup_gift_id').val();
		var type=$('#setup_type').val();
		var type_text=$('#setup_type option:selected').text();
		
		if(gift_id <=1){
			$('#info_setup_gift_id').html('请选择幸运礼物').show();
			return false;
		}else{
			$('#info_setup_gift_id').html('').hide();
		}

		if(type == ''){
			$('#info_setup_type').html('请选择类型').show();
			return false;
		}else{
			$('#info_setup_type').html('').hide();
		}
		
		var isRequest = checkAwardInfo();
		if(isRequest){
			var target_text = $('#setup_target_id option:selected').text();
			var target_id=$('#setup_target_id').val();
				target_id = target_id?target_id:0;
			var award=$('#setup_award').val();
			var pipiegg = $('#setup_pipiegg').val();
				pipiegg = pipiegg?pipiegg:0;
			//验证奖品概率
			$.ajax({
				url:url,
				type:'post',
				dataType:'text',
				data:{'chance':chance,'id':id,'target_id':target_id,'award':award,'type':type,'gift_id':gift_id,'op':'checkChances','tab':'giftAward'},
				success:function(msg){
					if(msg == 1){
						if(parseInt(id) <=0){
							//该记录是否已经存在
							$.ajax({
								url:url,
								type:'post',
								dataType:'text',
								data:{'target_id':target_id,'award':award,'type':type,'gift_id':gift_id,'op':'checkExists','tab':'giftAward'},
								success:function(msg){
									if(msg == 1){
										submitAward(chance,id,target_id,target_text,award,gift_id,gift_text,type,type_text,pipiegg);
									}else{
										alert(msg);
									}
								}
							});
						}else{
							submitAward(chance,id,target_id,target_text,award,gift_id,gift_text,type,type_text,pipiegg);
						}
					}else{
						alert(msg);
					}
				}
			});
		}
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
				$('#giftAward').html(html);
			}
		});
		$(this).attr('href','javascript:void(0);');
	});
	
	//删除
	$('.icon-close').live('click',function(){
		var id = $(this).attr('awardId');
		var obj = this;
		if(id){
			$.ajax({
				url:url,
				type:'post',
				dataType:'html',
				data:{'id':id,'op':'delAward','tab':'giftAward'},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						alert(msg);
					}		
				}
			});
		}
	});
	//编辑
	$('.icon-edit').live('click',function(){
		var id = $(this).attr('awardId');
		var type = $(this).attr('type');
		var obj = this;
		if(id && type>=0){
			var gift_id = $('#luckygifts_award_list #__'+id+' > td').eq(3).html();
			var target_id = $('#luckygifts_award_list #__'+id+' > td').eq(5).html();
			var award = $('#luckygifts_award_list #__'+id+' > td').eq(6).html();
			var chance = $('#luckygifts_award_list #__'+id+' > td').eq(7).html();
			$('#setup_gift_id option').each(function(i){
				if($(this).attr('value') == gift_id){
					$(this).attr('selected','selected');
				}
			});
			
			$('#setup_type option').each(function(i){
				if($(this).attr('value') == type){
					$(this).attr('selected','selected');
					getAwardView(type,true,obj);
				}
			});

			$('#setup_chances').attr('value',chance);
			$('#setup_award_id').attr('value',id);
		}
	});
			
	//重置
	$('#_poolset_reset').live('click',function(){
		$(':input').each(function(i){
			$(this).attr('value','');
		});
	});
});
</script>