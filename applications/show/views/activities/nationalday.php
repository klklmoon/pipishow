<div class="kong">
  <div class="w1000 relative">
  </div>
</div><!--.kong-->


<div class="w1000">
  <div class="rule">
     <img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/nationalday/pic1.png" />
     <span>1、“喜迎国庆”为本活动指定礼物，送其他礼物不计入榜单<br/>2、只有在活动期间送礼才能计入榜单</span>
  </div>
</div><!--.w1000-->


<div class="w1000">
  <img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/nationalday/main.jpg" />
</div><!--.w1000-->


<div class="w1000 relative clearfix ovhide mt10"> 
  <div class="fleft anchor-new">
    	<h6>主播榜</h6>
        <div class="anchor-new-con">
        	<ul class="anchor-new-h clearfix ovhide">
            	<li>主播昵称</li>
                <li>主播ID</li>
                <li>收到喜迎国庆</li>
            </ul>
            <ul class="anchor-new-list">
            <?php foreach ($nationalday['DoteyWelcomeNationalDayRank'] as $uid=>$doteyRow):?>
				<li class="clearfix no<?php echo $doteyRow['rank_order'];?>">
					<em class="order"><?php echo $doteyRow['rank_order'];?></em>
					<span style="text-align: left;"><em class="lvlo lvlo-<?php echo $doteyRow['dk'];?>"></em>
					<a href="<?php $this->getTargetHref("/{$uid}",true,false)?>"
					title="<?php echo $doteyRow['nk'];?>"
					target="<?php echo $this->target?>">
					<?php echo mb_substr((empty($doteyRow['nk'])?"求昵称":$doteyRow['nk']),0,6,'UTF-8');?>
					</a>
					</span>
					<span><?php echo $uid;?></span>
					<span><?php echo $doteyRow['gift_num'];?></span>
				</li>
			<?php endforeach;?>	            
              </ul>
        </div>
    </div><!-- .anchor-new -->
    
  <div class="fright anchor-new">
    	<h6>富豪榜</h6>
        <div class="anchor-new-con">
        	<ul class="anchor-new-h clearfix ovhide">
            	<li>富豪昵称</li>
                <li>富豪ID</li>
                <li>送出喜迎国庆</li>
            </ul>
            <ul class="anchor-new-list">
            <?php foreach ($nationalday['UserWelcomeNationalDayRank'] as $uid=>$userRow): ?>
				<li class="clearfix no<?php echo $userRow['rank_order'];?>">
					<em class="order"><?php echo $userRow['rank_order'];?></em>
					<span style="text-align: left;"><em class="lvlr lvlr-<?php echo $userRow['rk'];?>"></em><?php echo mb_substr((empty($userRow['nk'])?"求昵称":$userRow['nk']),0,6,'UTF-8');?></span>
					<span><?php echo $uid;?></span>
					<span><?php echo $userRow['gift_num'];?></span>
				</li>
			<?php endforeach;?>	 	           
            </ul>
        </div>
    </div><!-- .anchor-new -->  
</div><!-- W1000 -->
<script type="text/javascript">
var time_test = "<?php echo $time_test;?>"; 
if(time_test!=1)
{
	alert("活动已结束");
}
</script>