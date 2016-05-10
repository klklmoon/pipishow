<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
    
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="<?php echo $this->createUrl('account/bag');?>">礼物背包</a></li>
            <li><a href="<?php echo $this->createUrl('account/props');?>">道具</a></li>
            <li><a href="<?php echo $this->createUrl('account/car');?>">座驾</a></li>
            <li><a href="<?php echo $this->createUrl('account/moon');?>">月卡</a></li>
            <li><a href="<?php echo $this->createUrl('account/vip');?>">vip</a></li>
            <!--<li><a href="index.php?r=account/guard">家族守护</a></li>-->
            <li><a href="<?php echo $this->createUrl('account/number');?>">靓号</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
			<?php 
				if($account_bags){
			?>
			<ul class="gift-list">
			<?php
				$nums = 0;
				foreach($account_bags as $k=>$v) {
					if($v['num'] > 0){
					echo '<li>
						<a href="#" class="gift-box">
							<img title="'.$v['info']['zh_name'].'" src="'.Yii::app()->params->images_server['url'].'/gift/'.$v['info']['image'].'" />
							<span>'.$v['num'].'</span>
						</a>
						<a href="#" title="'.$v['info']['zh_name'].'" class="gif-title">'.$v['info']['zh_name'].'</a>
					</li>';
					$nums ++;
					}
				}
			?>
            </ul>
			<?php if($nums==0){ ?>
				您的背包中，还没有任何礼物道具，请到<a target="_blank" href="<?php $this->getTargetHref($this->createUrl('shop/gift'));?>" class="undo">商城</a>购买。
			<?php }}else{?>
				您的背包中，还没有任何礼物道具，请到<a target="_blank" href="<?php $this->getTargetHref($this->createUrl('shop/gift'));?>" class="undo">商城</a>购买。
			<?php 
				}
			?>
           </div><!-- .cooper-list 礼物背包 -->
         
	</div><!--#MainCon-->
</div><!-- .main -->        
</div><!-- .w1000 -->
