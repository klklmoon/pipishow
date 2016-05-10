<?php
$this->breadcrumbs = array('添加道具');
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!($this->isAjax)){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i> 添加道具分类属性</h2>
		</div>
		<?php }?>
		<div class="box-content">
			<?php if(!empty($notices)){?>
			<div class="alert alert-block">
			<?php foreach($notices as $notice){?>
				<p><?php echo $notice[0];?></p>
			<?php }?>
			</div>
			<?php }?>
			<form class="form-horizontal" action="<?php echo $this->createUrl('props/addprops');?>" method="post" enctype="multipart/form-data">
				<fieldset>
				
				  <div class="control-group">
					<label class="control-label" for="focusedInput">道具名称</label>
					<div class="controls">
					  <input class="input-large focused" id="props[name]" name="props[name]" type="text" value="<?php echo isset($cinfo['name'])?$cinfo['name']:'';?>">
					  <span class="label label-important" style="margin-left:10px;">填写中文名称</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">道具标识</label>
					<div class="controls">
					  <input class="input-large focused" id="props[en_name]" name="props[en_name]" type="text" value="<?php echo isset($cinfo['en_name'])?$cinfo['en_name']:'';?>">
						<span class="label label-important" style="margin-left:10px;">填写英文名称</span>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">属性分类</label>
					<div class="controls">
						<?php 
							$select1 = isset($cinfo['cat_id'])?$cinfo['cat_id']:'';
							echo CHtml::listBox('props[cat_id]', $select1, $this->getPropsCat(2),array('size'=>1,'class'=>'input-small','empty'=>'-请选择-'));
						?>
					</div>
				  </div>
				  <!-- 分类属性明细 start -->
				  <div id="attribute_search_list" style="display:none;" class="box">
				  </div>
				  <!-- 分类属性明细 end-->
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">花费皮蛋</label>
					<div class="controls">
					  <input class="input-small focused" id="props[pipiegg]" name="props[pipiegg]" type="text" value="<?php echo isset($cinfo['pipiegg'])?$cinfo['pipiegg']:'';?>">
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">魅力值变化</label>
					<div class="controls">
					  <input class="input-small focused" id="props[charm]" name="props[charm]" type="text" value="<?php echo isset($cinfo['charm'])?$cinfo['charm']:'';?>">
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">贡献值变化</label>
					<div class="controls">
					  <input class="input-small focused" id="props[dedication]" name="props[dedication]" type="text" value="<?php echo isset($cinfo['dedication'])?$cinfo['dedication']:'';?>">
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">道具状态</label>
					<div class="controls">
						<?php 
							$select3 = isset($cinfo['status'])?$cinfo['status']:'';
							echo CHtml::radioButtonList('props[status]', $select3, $this->getPropsStatus(),array('separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;','labelOptions'=>array('class'=>'checkbox inline')));
						?>
					</div>
				  </div>
				  				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">等级限制</label>
					<div class="controls">
						<?php 
							$select2 = isset($cinfo['rank'])?$cinfo['rank']:'';
							echo CHtml::listBox('props[rank]', $select2, $this->getAllUserRank(2),array('class'=>'input-small','size'=>1));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">排序</label>
					<div class="controls">
					  <input class="input-small focused" id="props[sort]" name="props[sort]" type="text" value="<?php echo isset($cinfo['sort'])?$cinfo['sort']:'';?>">
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">展示图标</label>
					<div class="controls">
						<?php if(isset($cinfo['image'])){?>
						<img alt="" src="<?php echo Yii::app()->params['images_server']['url'].$cinfo['image'];?>">
						<?php }?>
					  <input class="input-small focused" id="props[image]" name="props[image]" type="file" value="">
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="focusedInput">游戏展示图标</label>
					<div class="controls">
						<?php if(isset($cinfo['game_image'])){?>
						<img alt="" src="<?php echo Yii::app()->params['images_server']['url'].$cinfo['game_image'];?>">
						<?php }?>
					  <input class="input-small focused" id="gameimg" name="gameimg" type="file" value="">
					</div>
				  </div>
				  
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				  <?php if(isset($cinfo['prop_id'])){?>
				  <input id="props_old_cat_id" name="props[old_cat_id]" type="hidden" value="<?php echo $cinfo['cat_id'];?>">
				  <input id="props_prop_id" name="props[prop_id]" type="hidden" value="<?php echo $cinfo['prop_id'];?>">
				  <?php }?>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script type="text/javascript">
var global = {
		loadCatAttr:function(cat_id,prop_id){
			$.ajax({
				url:"<?php echo $this->createUrl('props/AddProps');?>",
				dataType:'html',
				type:'post',
				data:{'op':'getCatAttr','cat_id':cat_id,'prop_id':prop_id},
				success:function(msg){
					var _css = {"margin-top":"0px","margin-bottom":"0px","padding":"0px"};
					if(msg == 1){
						$("#attribute_search_list").css(_css).hide().html('');
						alert('缺少参数');
					}else if(msg == 2){
						$("#attribute_search_list").css(_css).hide().html('');
						alert('获取分类属性失败');
					}else{
						var _css = {"margin-top":"5px","margin-bottom":"5px","padding":"5px"};
						$("#attribute_search_list").css(_css).show().html(msg);
					}
				}
			});
		}
	};
	
$(document).ready(function(){
	//初始化道具分类
	var prop_id = $('#props_prop_id').attr('value');
	var old_cat_id = $('#props_old_cat_id').attr('value');
	if(prop_id){
		global.loadCatAttr(old_cat_id,prop_id);
		$('#props_old_cat_id').detach();
	}

	//改变道具分类
	$("#props_cat_id").change(function(){
		var cat_id = $(this).attr('value');
		var prop_id = $('#props_prop_id').attr('value');
		if(cat_id){
			global.loadCatAttr(cat_id,prop_id);
		}
	});
	
});
</script>