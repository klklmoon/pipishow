<?php
$this->breadcrumbs = array('礼物之星','礼物图片设置');
?>
<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:140px;margin-left:10px;padding:3px 5px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!isset($isAjax) || !$isAjax){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>礼物图片设置</h2>
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
			<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('activities/giftstarimg');?>" enctype="multipart/form-data">
				<fieldset>
				  <div class="control-group">
					<label class="control-label" for="GiftStarImgForm[gift_id]">礼物</label>
					<div class="controls">
						<?php 
							echo CHtml::dropDownList('GiftStarImgForm[gift_id]',isset($updateGiftInfo['gift_id'])?$updateGiftInfo['gift_id']:'', $giftList,array('empty'=>'--选择礼物--'));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					  <label class="control-label" for="GiftStarImgForm[image]">上传礼物图片</label>
					  <div class="controls">
					  	<?php if(isset($updateGiftInfo['image'])){?>
						  	<img alt="" src="<?php echo $this->giftService->getShowAdminGiftUrl($updateGiftInfo['image']);?>">
					  	<?php }?>
						<input class="input-small focused" id="GiftStarImgForm[image]" name="GiftStarImgForm[image]" type="file" size="19" >
					  </div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label" for="GiftStarImgForm[order_number]">图片序号</label>
					<div class="controls">
						<?php 
							echo CHtml::dropDownList('GiftStarImgForm[order_number]',isset($updateGiftInfo['order_number'])?$updateGiftInfo['order_number']:'', 
								array(1=>1,2=>2,3=>3,4=>4,5=>5),array('empty'=>'--选择序号--'));
						?>
					</div>
				  </div>				  
				  
				 <div class="control-group">
					<label class="control-label" for="GiftForm[zh_name]">描述</label>
					<div class="controls">
					  <input class="input-small focused" id="GiftStarImgForm[summary]" name="GiftStarImgForm[summary]" type="text" value="<?php echo isset($updateGiftInfo['summary'])?$updateGiftInfo['summary']:'';?>">
					  <span class="label label-important" style="margin-left:10px;">填写中文描述</span>
					</div>
				  </div>				  
				  
				   <?php if(isset($updateGiftInfo['img_id'])){?>
				  <input name="GiftStarImgForm[img_id]" type="hidden" id="GiftStarImgForm[img_id]" value="<?php echo $updateGiftInfo['img_id'];?>">
				  <?php }?>
				  
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>
