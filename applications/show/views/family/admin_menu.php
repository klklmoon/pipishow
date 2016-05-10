<ul class="control-menu clearfix">
	<li><a href="<?php echo $this->createHomeUrl($family['id']);?>">家族主页</a></li>
	<li class="<?php if($action == 'admin') echo 'on';?>"><a href="<?php echo $this->createUrl('family/admin', array('family_id' => $family['id']));?>">家族管理</a></li>
	<?php
	$show = false;
	if($manager) $show = true;
	foreach($menu as $m){
		if($m['action'] == 'AdminJoin'){
			$show = true;
			break;
		}
	}
	if($show){
	?>
	<li class="<?php if($action == 'adminJoin') echo 'on';?>"><a href="<?php echo $this->createUrl('family/adminJoin', array('family_id' => $family['id']));?>">入族条件</a></li>
	<?php } ?>
	<li class="<?php if($action == 'adminCheck') echo 'on';?>"><a href="<?php echo $this->createUrl('family/adminCheck', array('family_id' => $family['id']));?>">申请审批</a></li>
	<li class="<?php if($action == 'adminMember') echo 'on';?>"><a href="<?php echo $this->createUrl('family/adminMember', array('family_id' => $family['id']));?>">成员管理</a></li>
	<li class="<?php if($action == 'adminTop') echo 'on';?>"><a href="<?php echo $this->createUrl('family/adminTop', array('family_id' => $family['id']));?>">族员排行</a></li>
	<?php if($family['uid'] == Yii::app()->user->id || $manager){?>
	<li class="<?php if($action == 'adminMedal') echo 'on';?>"><a href="<?php echo $this->createUrl('family/adminMedal', array('family_id' => $family['id']));?>">族徽管理</a></li>
	<?php }?>
	<li class="<?php if($action == 'adminBbs') echo 'on';if(!($family['uid'] == Yii::app()->user->id || $manager)) echo ' end';?>"><a href="<?php echo $this->createUrl('family/adminBbs', array('family_id' => $family['id']));?>">发帖管理</a></li>
	<?php if($family['sign'] && ($family['uid'] == Yii::app()->user->id || $manager)){?>
	<li class="<?php if($action == 'adminIncome') echo 'on';?> end"><a href="<?php echo $this->createUrl('family/adminIncome', array('family_id' => $family['id']));?>">收益管理</a></li>
	<?php }?>
</ul>