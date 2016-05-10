<?php
$this->breadcrumbs = array('礼物之星','礼物规则设定');
?>
<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:140px;margin-left:10px;padding:3px 5px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!isset($isAjax) || !$isAjax){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>礼物规则设置</h2>
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
			<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('activities/giftstarrule');?>" enctype="multipart/form-data">
				<fieldset>
				<div class="control-group">
					<label class="control-label" for="RuleForm[week_id]">周编号</label>
					<div class="controls">
					<?php echo $updateRuleInfo['week_id'];?>
					</div>
				  </div>
				  
				<div class="control-group">
					<label class="control-label" for="RuleForm[monday_date]">周一日期</label>
					<div class="controls">
					<?php echo $updateRuleInfo['monday_date'];?>
					</div>
				  </div>				  
				
				  <div class="control-group">
					<label class="control-label" for="RuleForm[gift_id]">礼物</label>
					<div class="controls">
						<?php 
							echo CHtml::dropDownList('RuleForm[gift_id]', isset($updateRuleInfo['gift_id'])?$updateRuleInfo['gift_id']:'', $giftList,array('empty'=>'--选择礼物--'));
						?>
					</div>
				  </div>
				  
				  <div class="control-group">
					<label class="control-label">主播等级限制</label>
					<div class="controls">
						<?php 
							echo CHtml::checkBoxList(
								'RuleForm[contention_rule]', 
								isset($updateRuleInfo['contention_rule'])?explode(",",$updateRuleInfo['contention_rule']):'', 
								$allDoteyRank,
								array(
									'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
									'labelOptions'=>array('class'=>'checkbox inline'),
									)
								);
						?>
					</div>
				   </div>
				   <?php if(isset($updateRuleInfo['rule_id'])){?>
				  <input name="RuleForm[rule_id]" type="hidden" id="RuleForm[rule_id]" value="<?php echo $updateRuleInfo['rule_id'];?>">
				  <?php }?>
				  
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>
