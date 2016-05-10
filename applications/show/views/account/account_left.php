<div class="nav_left fleft">
	<ul class="nav-list">
		<div class="nav-tit">个人中心</div>
		<?php foreach($account_left as $k=>$v) :?>
				<li><a target="_self" href="<?php echo $this->createUrl('account/'.$v['action']);?>" class="<?php echo $v['is_check'] ? "navvisted" : "";?>"><?php echo $v['name'];?></a></li>
		<?php endforeach;?>                                                          
	</ul>
	<?php if($dotey_left) { ?>
	<ul class="nav-list">
		<div class="nav-tit">我是主播</div>
		<?php foreach($dotey_left as $k=>$v): ?>
				<li><a href="<?php echo $this->createUrl('account/'.$v['action']);?>" class="<?php echo $v['is_check'] ? "navvisted" : "";?>"><?php echo $v['name'];?></a></li>
		<?php endforeach;?>
	</ul>
	<?php } ?>
	<?php if($agent_left) { ?>
	<ul class="nav-list">
		<div class="nav-tit">我是代理</div>
		<?php foreach($agent_left as $k=>$v): ?>
				<li><a href="<?php echo $this->createUrl('account/'.$v['action']);?>" class="<?php echo $v['is_check'] ? "navvisted" : "";?>"><?php echo $v['name'];?></a></li>
		<?php endforeach;?>
	</ul>
	<?php } ?>	
</div>
