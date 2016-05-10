<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
    
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/bag');?>">礼物背包</a></li>
            <li class="menuvisted"><a href="<?php echo $this->createUrl('account/props');?>">道具</a></li>
            <li><a href="<?php echo $this->createUrl('account/car');?>">座驾</a></li>
            <li><a href="<?php echo $this->createUrl('account/moon');?>">月卡</a></li>
            <li><a href="<?php echo $this->createUrl('account/vip');?>">vip</a></li>
            <!--<li><a href="<?php echo $this->createUrl('account/guard');?>">家族守护</a></li>-->
            <li><a href="<?php echo $this->createUrl('account/number');?>">靓号</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
		  <div class="cooper-list">
            <table width="700" border="1" bordercolor="#DDDDDD">
				<?php if($bagInfo or $fly['bagInfo'] or $broadcast['bagInfo']): ?>
					<tr bgcolor="#F5F5F5" class="biaot">
					<td width="200" height="40">名称</td>
					<td width="300" height="40">威力</td>
					<td width="100" height="40">数量</td>
					<td width="100" height="40">有效期</td>
					</tr>
				<?php if($bagInfo) : foreach($bagInfo as $k=>$v): ?>
						<tr>
							<td height="42"><?php echo $propsInfo[$v['prop_id']]['name'];?></td>
							<td height="42"><?php echo $propsInfo[$v['prop_id']]['attribute']['prop_power']['value'];?></td>
							<td height="42"><?php echo $v['num'];?></td>
							<td height="42"><?php echo $v['time_desc'];?></td>
						</tr>
				<?php endforeach; endif; ?>
				<?php if($fly['bagInfo']) : foreach($fly['bagInfo'] as $k=>$v): ?>
						<tr>
							<td height="42"><?php echo $fly['propsInfo'][$v['prop_id']]['name'];?></td>
							<td height="42"><?php echo $fly['propsInfo'][$v['prop_id']]['attribute']['flyscreen_power']['value'];?></td>
							<td height="42"><?php echo $v['num'];?></td>
							<td height="42"><?php echo $v['time_desc'];?></td>
						</tr>
				<?php endforeach;endif; ?>
				<?php if($broadcast['bagInfo']) : foreach($broadcast['bagInfo'] as $k=>$v): ?>
						<?php if($v['num']>0):?>
						<tr>
							<td height="42"><?php echo $broadcast['propsInfo'][$v['prop_id']]['name'];?></td>
							<td height="42"><?php echo $broadcast['propsInfo'][$v['prop_id']]['attribute']['broadcast_power']['value'];?></td>
							<td height="42"><?php echo $v['num'];?></td>
							<td height="42"><?php echo $v['time_desc'];?></td>
						</tr>
						<?php endif;?>
				<?php endforeach;endif; ?>
				<?php else: ?>	
					您的背包中，还没有任何礼物道具，请到<a target="_blank" href="<?php $this->getTargetHref($this->createUrl('shop/gift'));?>" class="undo">商城</a>购买。
				<?php endif; ?>
			</table>
           </div><!-- .cooper-list 道具 -->
         
		</div><!--#MainCon-->
</div><!-- .main -->        
</div><!-- .w1000 -->


