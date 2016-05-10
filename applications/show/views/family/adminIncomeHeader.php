<?php echo $this->renderPartial('home_top', compact(array_keys(get_defined_vars())));?>
<div class="w1000 memberMesg-con clearfix">
    <div class="fleft memberMesgBd-l">
        <div class="control-box">
            <?php echo $this->renderPartial('admin_menu', array('family' => $family, 'action' => $this->getAction()->id, 'menu' => $menu, 'manager' => $manager));?>
			<p class="sub-controlMenu">
				 <a class="<?php if($type == 'live' || $type== 'live_info') echo 'on';?>" href="<?php echo $this->createUrl('family/adminIncome', array('family_id' => $family['id'],'type'=>'live'));?>">家族开播统计</a>
				 <a class="<?php if($type == 'income' || $type == 'income_info') echo 'on';?>" href="<?php echo $this->createUrl('family/adminIncome', array('family_id' => $family['id'],'type'=>'income'));?>">家族收益统计</a>
				 <a class="<?php if($type == 'forceIncome') echo 'on';?>" href="<?php echo $this->createUrl('family/adminIncome', array('family_id' => $family['id'],'type'=>'forceIncome'));?>">强退主播记录</a>
				 <a class="<?php if($type == 'join') echo 'on';?> end" href="<?php echo $this->createUrl('family/adminIncome', array('family_id' => $family['id'],'type'=>'join'));?>">家族主播加入与退出记录</a>
			</p>
			
			