<?php
$this->breadcrumbs = array('添加礼物');
?>
<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:120px;margin-left:10px;padding:3px 5px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!isset($isAjax) || !$isAjax){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>添加礼物</h2>
		</div>
		<?php }?>
		<div class="box-content">
			<?php if(isset($notices) && count($notices)>0){?>
			<div class="alert alert-block" style="margin-left:60px;margin-right:200px;clear:both;">
				<button type="button" class="close" data-dismiss="alert">×</button>
			<?php foreach($notices as $notice){?>
				<p><?php echo isset($notice[0])?$notice[0]:$notice;?></p>
			<?php }?>
			</div>
			<?php }?>
			<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('gift/addgift');?>" enctype="multipart/form-data">
				<fieldset>
				  <div class="control-group">
					<label class="control-label" for="GiftForm[cat_id]">礼物分类</label>
					<div class="controls">
						<?php 
							$selectCat = isset($upGiftInfo['cat_id'])?array($upGiftInfo['cat_id']):'';
							echo CHtml::dropDownList('GiftForm[cat_id]', $selectCat, $allGiftCat,array('empty'=>'--选择礼物分类--'));
						?>
					</div>
				  </div>
				 
				 <div class="control-group">
					<label class="control-label" for="GiftForm[zh_name]">礼物名称</label>
					<div class="controls">
					  <input class="input-small focused" id="GiftForm[zh_name]" name="GiftForm[zh_name]" type="text" value="<?php echo isset($upGiftInfo['zh_name'])?$upGiftInfo['zh_name']:'';?>">
					  <span class="label label-important" style="margin-left:10px;">填写中文名称</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="GiftForm[en_name]">礼物标识</label>
					<div class="controls">
					  <input class="input-small focused" id="GiftForm[en_name]" name="GiftForm[en_name]" type="text" value="<?php echo isset($upGiftInfo['en_name'])?$upGiftInfo['en_name']:'';?>">
						<span class="label label-important" style="margin-left:10px;">填写英文名称</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label">商品类型</label>
					<div class="controls">
						<?php 
						 	$_shopType = isset($upGiftInfo['shop_type'])?$upGiftInfo['shop_type']:'';
						 	$selectShopType = '';
						 	if ($_shopType){
						 		$selectShopType = array_keys($this->giftSer->getShopType($_shopType));
						 	}
						 ?>
						<?php 
							echo CHtml::checkBoxList(
								'GiftForm[shop_type]', $selectShopType, 
								$allShopType,
								array(
									'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
									'labelOptions'=>array('class'=>'checkbox inline'),
									)
								);
						?>
					</div>
				   </div>
				   
				   <div class="control-group">
					<label class="control-label">礼物类型</label>
					<div class="controls">
						<?php 
						 	$_giftType = isset($upGiftInfo['gift_type'])?$upGiftInfo['gift_type']:'';
						 	$selectGiftType = '';
						 	if ($_giftType){
						 		$selectGiftType = array_keys($this->giftSer->getGiftType($_giftType));
						 	}
						 ?>
						<?php 
							echo CHtml::checkBoxList(
								'GiftForm[gift_type]', $selectGiftType, 
								$allGiftType,
								array(
									'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
									'labelOptions'=>array('class'=>'checkbox inline'),
									)
								);
						?>
					</div>
				   </div>
				   
				  <div class="control-group">
					<label class="control-label" for="GiftForm[sort]">排序</label>
					<div class="controls">
					  <input class="input-small focused" id="GiftForm[sort]" name="GiftForm[sort]" type="text" value="<?php echo isset($upGiftInfo['sort'])?$upGiftInfo['sort']:'';?>">
					</div>
				  </div>
				  
				  <div class="control-group">
					  <label class="control-label" for="GiftForm[image]">上传礼物图片</label>
					  <div class="controls">
					  	<?php if(isset($upGiftInfo['image'])){?>
						  	<img alt="" src="<?php echo $this->giftSer->getShowAdminGiftUrl($upGiftInfo['image']);?>">
					  	<?php }?>
						<input class="input-small focused" id="GiftForm[image]" name="GiftForm[image]" type="file" size="19" >
					  </div>
				  </div>
							
				  <div class="control-group">
					<label class="control-label" for="GiftForm[pipiegg]">皮蛋价格</label>
					<div class="controls">
					  <input class="input-small focused" id="GiftForm[pipiegg]" name="GiftForm[pipiegg]" type="text" value="<?php echo isset($upGiftInfo['pipiegg'])?$upGiftInfo['pipiegg']:'';?>">
					</div>
				  </div>
				  
				   <div class="control-group">
					<label class="control-label" for="GiftForm[charm]">魅力值</label>
					<div class="controls">
					  <input class="input-small focused" id="GiftForm[charm]" name="GiftForm[charm]" type="text" value="<?php echo isset($upGiftInfo['charm'])?$upGiftInfo['charm']:'';?>">
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="GiftForm[charm_points]">魅力点</label>
					<div class="controls">
					  <input class="input-small focused" id="GiftForm[charm_points]" name="GiftForm[charm_points]" type="text" value="<?php echo isset($upGiftInfo['charm_points'])?$upGiftInfo['charm_points']:'';?>">
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="GiftForm[dedication]">贡献值</label>
					<div class="controls">
					  <input class="input-small focused" id="GiftForm[dedication]" name="GiftForm[dedication]" type="text" value="<?php echo isset($upGiftInfo['dedication'])?$upGiftInfo['dedication']:'';?>">
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="GiftForm[egg_points]">皮点</label>
					<div class="controls">
					  <input class="input-small focused" id="GiftForm[egg_points]" name="GiftForm[egg_points]" type="text" value="<?php echo isset($upGiftInfo['egg_points'])?$upGiftInfo['egg_points']:'';?>">
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="GiftForm[sell_nums]">出售数量</label>
					<div class="controls">
					  <input class="input-small focused" id="GiftForm[sell_nums]" name="GiftForm[sell_nums]" type="text" value="<?php echo isset($upGiftInfo['sell_nums'])?$upGiftInfo['sell_nums']:'';?>">
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="GiftForm[remark]">文字描述</label>
					<div class="controls">
					  <input class="input-small focused" id="GiftForm[remark]" name="GiftForm[remark]" type="text" value="<?php echo isset($upGiftInfo['remark'])?$upGiftInfo['remark']:'';?>">
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="GiftForm[is_display]">是否限购</label>
					<div class="controls">
						<?php 
						   $buy_limit = isset($upGiftInfo['buy_limit'])?array($upGiftInfo['buy_limit']):'';
						?>
						<?php 
							echo CHtml::dropDownList('GiftForm[buy_limit]', $buy_limit, $this->giftSer->getBuyLimitOption(),array('class'=>'input-small'));
						?>
					</div>
				  </div>
				  
				 <div class="control-group">
					<label class="control-label" for="GiftForm[is_display]">显示状态</label>
					<div class="controls">
						<?php 
						   $selectStatus = isset($upGiftInfo['is_display'])?array($upGiftInfo['is_display']):'';
						?>
						<?php 
							echo CHtml::dropDownList('GiftForm[is_display]', $selectStatus, $allStatus,array('class'=>'input-small'));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="GiftForm[sell_grade]">出售等级</label>
					<div class="controls">
						<?php 
							$selectGrade = isset($upGiftInfo['sell_grade'])?array($upGiftInfo['sell_grade']):'';
						?>
						<?php 
							echo CHtml::dropDownList('GiftForm[sell_grade]', $selectGrade, $allGrade,array('class'=>'input-small'));
						?>
					</div>
				  </div>
				  <dl style="border-top:1px #999999 dotted"></dl>
				  	动画特效(送礼达到数量后，代替默认动画效果显示)：
				  	<div class="control-group">
				  	<dl style="margin-top:5px;">
						<dt>达到数量</dt>
						<dt>播放时长(秒)</dt>
						<dt>效果类型</dt>
						<dt>显示位置</dt>
						<dt>文字描述</dt>
						<dt>动画上传</dt>
					</dl>
					<dl class="effect"> </dl>
					<?php if(isset($upGiftInfo['effects'])){?>
					<?php foreach($upGiftInfo['effects'] as $k=>$effect){?>
					<dl class="effect">
						<dd>≥<input name="GiftForm[num][<?php echo $k?>]" type="text" id="GiftForm[num][<?php echo $effect['effect_id']?>]" class="input-small focused" size="10" value="<?php echo $effect['num'];?>"></dd>
						<dt>
							<input name="GiftForm[effect_id][<?php echo $k?>]" type="hidden" id="GiftForm[effect_id][<?php echo $effect['effect_id']?>]" value="<?php echo $effect['effect_id'];?>">
							<input name="GiftForm[timeout][<?php echo $k?>]" type="text" id="GiftForm[timeout][<?php echo $effect['effect_id']?>]" class="input-small focused" size="10" value="<?php echo $effect['timeout'];?>">
						</dt>
						<dt>
							<select name="GiftForm[effect_type][<?php echo $k?>]" class="input-small focused" id="GiftForm[effect_type][<?php echo $effect['effect_id']?>]">
								<option value="1" <?php if($effect['effect_type'] == 1){?>selected<?php }?>>Flash效果</option>
								<option value="2" <?php if($effect['effect_type'] == 2){?>selected<?php }?>>图片效果</option>
							</select>
						</dt>
						<dt>
							<select name="GiftForm[position][<?php echo $k?>]" class="input-small focused" id="effect[position][<?php echo $effect['effect_id']?>]">
								<option value="1" <?php if($effect['position'] == 1){?>selected<?php }?>>全屏居中</option>
								<option value="2" <?php if($effect['position'] == 2){?>selected<?php }?>>聊天区域</option>
								<option value="3" <?php if($effect['position'] == 3){?>selected<?php }?>>视频区域</option>
							</select>
						</dt>
						<dt>
							<input name="GiftForm[e_remark][<?php echo $k?>]" class="input-small focused" id="effect[e_remark][<?php echo $effect['effect_id']?>]" type="text" value="<?php echo isset($effect['remark'])?$effect['remark']:'';?>"/>
						</dt>
						<dt>
							<?php if($effect['effect']){?>
								<a href="<?php echo $this->giftSer->getShowAdminGiftEffectUrl($effect['effect']);?>" target="_blank"><span class="icon icon-color icon-link"></span></a>
							<?php }?>
							<input name="GiftForm[effect][<?php echo $k?>]" type="hidden" id="GiftForm[effect][<?php echo $effect['effect']?>]" value="<?php echo $effect['effect'];?>">
							<input name="GiftForm[effect][<?php echo $k?>]" id="GiftForm[effect][<?php echo $effect['effect_id']?>]" type="file" class="input-small focused" size="10" value="<?php echo $effect['effect'];?>">
						</dt>
						<dt><i class="icon-remove" effectId=<?php echo $effect['effect_id'];?>></i></dt>
					</dl>
					<?php }?>
					<?php }?>
				  </div>
				   <div class="control-group">
					  <a href="javascript:;" id="addEffect">+添加特效</a>
				   </div>
				  
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				  <?php if(isset($upGiftInfo['gift_id'])){?>
				  <input name="GiftForm[gift_id]" type="hidden" id="GiftForm[gift_id]" value="<?php echo $upGiftInfo['gift_id'];?>">
				  <?php }?>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script type="text/javascript">
$(document).ready(function(){
	//添加礼物特效输入
	$("#addEffect").click(function(){
		var effectNum = $(".effect").length;
		if(effectNum>5){
			alert('最多只能添加5个动画效果');
		}else{
			var effectHtml = "<dl class='effect'><dd>≥<input name='GiftForm[num][]' type='text' id='GiftForm[num]["+effectNum+"]' class='input-small focused' size='10' value=''/></dd>";
			effectHtml += "<dt><input name='GiftForm[timeout][]' type='text' id='GiftForm[timeout]["+effectNum+"]' class='input-small focused' size='10' value=''/></dt>";
			effectHtml += "<dt><select name='GiftForm[effect_type][]' class='input-small focused' id='GiftForm[effect_type]["+effectNum+"]'><option value='1'>Flash效果</option><option value='2'>图片效果</option></select></dt>";
			effectHtml += "<dt><select name='GiftForm[position][]' class='input-small focused' id='effect[position]["+effectNum+"]'><option value='1'>全屏居中</option><option value='2'>聊天区域</option><option value='3'>视频区域</option></select></dt>";
			effectHtml += "<dt><input name=\"GiftForm[e_remark][]\" class=\"input-small focused\" id=\"effect[e_remark]["+effectNum+"]\" type=\"text\" value=\"\"></dt>";
			effectHtml += "<dt><input name='GiftForm[effect][]' id='GiftForm[effect]["+effectNum+"]' type='file' class='input-small focused' size='10' /></dt>";
			effectHtml += "<dt><i class='icon-remove' onclick=\"$(this).parents('dl').detach()\"></i></dl>";
			$(".effect:last").after(effectHtml);
			effectNum++;
		}
	});
	
	//删除动画效果
	$("dt .icon-remove").click(function(){
		var effectId = $(this).attr('effectId');
		var obj = this;
		if(effectId){
			$.ajax({
				type:'post',
				url:'<?php echo $this->createUrl('gift/addgift');?>',
				dataType:'text',
				data:{'op':'delGiftEffect','effect_id':effectId},
				success:function(msg){
					if(msg == 1){
						$(obj).parents("dl").detach();
					}else{
						alert(msg);
					}
				}
			});
		}			
	});

	//选择文件时触发
	$("dl :file").change(function(){
		var prev = $(this).prev(':input');
		if(prev){
			prev.detach();
		}
		
	});
})
</script>