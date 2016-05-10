<div class="kong"></div><!--.kong-->

<div class="w1000 zhongqiu clearfix relative"> 
  <div class="tit"><strong>活动规则</strong></div>
  
  <div class="receive clearfix">
    <dl>
      <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/moonfestival/pic1.jpg" /></dt>
      <dd>1、月饼为本活动指定礼物之一<br />2、只有在活动期间送礼才能计入榜单<br />3、主播可争夺月饼榜</dd>
    </dl>
    
    <dl style="margin-left:85px;">
      <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/moonfestival/pic2.jpg" /></dt>
      <dd>1、中秋快乐为本活动指定礼物之一<br />2、只有在活动期间送礼才能计入榜单<br />3、主播可争夺中秋快乐榜</dd>
    </dl>
  </div><!-- receive -->
  
</div><!-- W1000 -->

<div class="w1000 relative clearfix mt30"> 
  <div class="tit"><strong>活动奖励</strong></div>
  <img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/moonfestival/mid.jpg" />
  
</div>


<div class="w1000 relative clearfix mt40"> 
  <div class="fleft anchor1">
    	<h6>主播榜（月饼榜）</h6>
        <div class="anchor1-con">
        	<ul class="anchor1-h clearfix">
            	<li>主播昵称</li>
                <li>主播ID</li>
                <li>收到月饼</li>
            </ul>
            <ul class="anchor1-list">
            <?php foreach ($moonfestival['DoteyMoonCakeRank'] as $uid=>$doteyRow):?>
				<li class="clearfix no<?php echo $doteyRow['rank_order'];?>">
					<em class="order"><?php echo $doteyRow['rank_order'];?></em>
					<span><em class="lvlo lvlo-<?php echo $doteyRow['dk'];?>"></em>
					<a href="<?php $this->getTargetHref("/{$uid}",true,false)?>"
					title="<?php echo $doteyRow['nk'];?>"
					target="<?php echo $this->target?>">
					<?php echo mb_substr((empty($doteyRow['nk'])?"求昵称":$doteyRow['nk']),0,5,'UTF-8');?>
					</a>
					</span>
					<span><?php echo $uid;?></span>
					<span><?php echo $doteyRow['gift_num'];?></span>
				</li>
			<?php endforeach;?>	           
            </ul>
        </div>
    </div><!-- .anchor -->
    
  <div class="fright anchor1">
    	<h6>主播榜（中秋快乐榜）</h6>
        <div class="anchor1-con">
        	<ul class="anchor1-h clearfix">
            	<li>主播昵称</li>
                <li>主播ID</li>
                <li>收到中秋快乐</li>
            </ul>
            <ul class="anchor1-list">
            <?php foreach ($moonfestival['DoteyHappyMoonFestivalRank'] as $uid=>$doteyRow): ?>
				<li class="clearfix no<?php echo $doteyRow['rank_order'];?>">
					<em class="order"><?php echo $doteyRow['rank_order'];?></em>
					<span><em class="lvlo lvlo-<?php echo $doteyRow['dk'];?>"></em>
					<a href="<?php $this->getTargetHref("/{$uid}",true,false)?>"
					title="<?php echo $doteyRow['nk'];?>"
					target="<?php echo $this->target?>">
					<?php echo mb_substr((empty($doteyRow['nk'])?"求昵称":$doteyRow['nk']),0,5,'UTF-8');?>
					</a>
					</span>
					<span><?php echo $uid;?></span>
					<span><?php echo $doteyRow['gift_num'];?></span>
				</li>
			<?php endforeach;?>	  	         
            </ul>
        </div>
    </div><!-- .anchor -->  
</div><!-- W1000 -->


<div class="w1000 relative clearfix mt40"> 
  <div class="fleft anchor1 fuhao">
    	<h6>富豪榜（月饼榜）</h6>
        <div class="anchor1-con">
        	<ul class="anchor1-h clearfix">
            	<li>富豪昵称</li>
                <li>富豪ID</li>
                <li>送出月饼</li>
            </ul>
            <ul class="anchor1-list">
            <?php foreach ($moonfestival['UserMoonCakeRank'] as $uid=>$userRow): ?>
				<li class="clearfix no<?php echo $userRow['rank_order'];?>">
					<em class="order"><?php echo $userRow['rank_order'];?></em>
					<span><em class="lvlr lvlr-<?php echo $userRow['rk'];?>"></em><?php echo mb_substr((empty($userRow['nk'])?"求昵称":$userRow['nk']),0,5,'UTF-8');?></span>
					<span><?php echo $uid;?></span>
					<span><?php echo $userRow['gift_num'];?></span>
				</li>
			<?php endforeach;?>		
        
            </ul>
        </div>
    </div><!-- .anchor -->
    
  <div class="fright anchor1 fuhao">
    	<h6>富豪榜（中秋快乐榜）</h6>
        <div class="anchor1-con">
        	<ul class="anchor1-h clearfix">
            	<li>富豪昵称</li>
                <li>富豪ID</li>
                <li>送出中秋快乐</li>
            </ul>
            <ul class="anchor1-list">
            <?php foreach ($moonfestival['UserHappyMoonFestivalRank'] as $uid=>$userRow): ?>
				<li class="clearfix no<?php echo $userRow['rank_order'];?>">
					<em class="order"><?php echo $userRow['rank_order'];?></em>
					<span><em class="lvlr lvlr-<?php echo $userRow['rk'];?>"></em><?php echo mb_substr((empty($userRow['nk'])?"求昵称":$userRow['nk']),0,5,'UTF-8');?></span>
					<span><?php echo $uid;?></span>
					<span><?php echo $userRow['gift_num'];?></span>
				</li>
			<?php endforeach;?>		
            
              </ul>
        </div>
    </div><!-- .anchor -->  
</div><!-- W1000 -->
<script type="text/javascript">
var end_time_s = "<?php echo $end_time;?>"; 
var c_time_s="<?php echo $c_time;?>"; 
var end_time = new Date(Date.parse(end_time_s.replace(/-/g, "/")));
var c_time=new Date(Date.parse(c_time_s.replace(/-/g, "/")));
if(c_time>end_time)
{
	alert("活动已结束");
}
</script>