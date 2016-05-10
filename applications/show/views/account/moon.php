<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
     
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/bag');?>">礼物背包</a></li>
            <li><a href="<?php echo $this->createUrl('account/props');?>">道具</a></li>
            <li><a href="<?php echo $this->createUrl('account/car');?>">座驾</a></li>
            <li class="menuvisted"><a href="<?php echo $this->createUrl('account/moon');?>">月卡</a></li>
            <li><a href="<?php echo $this->createUrl('account/vip');?>">vip</a></li>
            <!--<li><a href="<?php echo $this->createUrl('account/guard');?>">家族守护</a></li>-->
            <li><a href="<?php echo $this->createUrl('account/number');?>">靓号</a></li>
        </ul><!-- .main-menu -->
		
		<div id="MainCon">
			<div class="cooper-list">
			<?php if($bagInfo) : ?>
              <table class="open">
                    <tr class="colum">
                        <td>种类</td>
                        <td>威力</td>
                        <td>有效期</td>
                        <td>操作</td>
                    </tr>
				<?php
				foreach($bagInfo as $k=>$v){
				?>
                    <tr>
                        <td class="kind">
                        	<img src="<?php echo $this->pipiFrontPath;?>/fontimg/account/mouth-big.png" /><br>
                            <strong><?php echo $propsInfo[$v['prop_id']]['name'] ;?></strong>
                        </td>
                        <td class="power">
						<?php 
							echo $propsInfo[$v['prop_id']]['attribute']['monthcard_power']['value'];
						?>
                        </td>
                        <td class="term">
                            <p><?php echo $v['time_desc'];?></p>
                        </td>
                        <td class="price">
							<?php 
								if($v['valid_time'] > time()){
							?>
								<p>已领取：<em><?php echo ($monthgift['all_num'] - $monthgift['num']);?>/<?php echo $monthgift['all_num'];?>朵玫瑰</em></p>
								<?php if($monthgift['num'] == 0) {?>
								<p>您的月卡超级礼物配额已经用完，<?php echo ceil(($v['valid_time']-time())/86400);?>天以后可以继续办理</p>
								<?php } ?>
								<?php if($monthgift['num'] > 0) {?>
								<a class="buy-btn" href="javascript:void(0);" onclick="account.checkin()" title="每日签到">每日签到</a><span>（按天数领取配额）</span><br/><br/>
								<a class="buy-btn" href="javascript:void(0);" onclick="account.checkin(1)" title="领取全部">领取全部</a><span>（全部提取到背包）</span>
								<?php } ?>
							<?php
							}else{
								echo '您的月卡已经过期, 请到<a  target="_blank" href="'.($this->getTargetHref($this->createUrl('shop/monthcard'),true,true)).'" class="undo"><em>商城</em></a>续办;';
							}
							?>
                        </td>
                    </tr>
				<?php
				}
				?>
                </table>
			<?php else: ?>
				您还没有办理月卡，赶快去<a target="_blank" href="<?php $this->getTargetHref($this->createUrl('shop/monthcard'))?>" class="undo">商城</a>看看。
			<?php endif;?>
           </div><!-- .cooper-list 月卡 --> 
		</div>
		
	</div><!--#MainCon-->
</div><!-- .main -->        
</div><!-- .w1000 -->


