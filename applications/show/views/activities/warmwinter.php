<div class="w1000">
    <div class="rule">
        <p>1、活动专属礼物：“汤圆”  定价50皮蛋/个</p>
        <p>2、活动形式：用户为最爱的女主播刷礼，让主播排上魅力榜。</p>
    </div><!--.rule-->
    <dl class="rewardwarm ten">
        <dt>活动结束时，主播收到礼物总数排行前10者，可获如下奖励：</dt>
        <dd>第一名：奖励现金999元+50万魅力值。基数20000个</dd>
        <dd>第二名：奖励现金500元+30万魅力值。基数10000个</dd>
        <dd>第三名：奖励现金300元+10万魅力值。基数5000个</dd>
        <dd>第四---第十名各奖励10万魅力值。无基数要求。</dd>
        <dd>以上奖励，主播只有满足相关基数要求才可获得。若礼物总数相同的主播，则按收到礼物的时间先后排行。</dd>
        <dd><p>注：主播获得的魅力值用于升级非提现魅力点</p></dd>
    </dl><!--.reward-->
    <dl class="rewardwarm three">
        <dt>活动结束时，用户送出礼物总数排行前3者，可获如下奖励：</dt>
        <dd>第一名：奖励2013 四位靓号一个 +20万皮蛋</dd>
        <dd>第二名：奖励18888五位靓号一个 +10万皮蛋</dd>
        <dd>第三名：奖励181818六位靓号一个+5万皮蛋</dd>
    </dl><!--.reward-->
    <p class="explain clearfix">
        <span>官方客服：<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=121294340&site=qq&menu=yes">
		<img border="0" src="http://wpa.qq.com/pa?p=2:121294340:41 &r=0.41319712803607783" >
		</a>
		</span>
        <span>活动说明：奖励活动结束后3天内发放。</span>
        <span class="red">*本活动最终解释权归皮皮乐天所有</span>
    </p><!--.explain-->
    <div class="kuso">
        <a href="<?php $this->getTargetHref($this->createUrl('activities/warmwinter'))?>" title="点击刷新榜单"></a>
    </div><!--.kuso-->
    <div class="kuso-list clearfix">
        <div class="anchor-new-con">
            <ul class="anchor-new-h clearfix ovhide">
                <li class="name">主播昵称</li>
                <li>收礼总数</li>
                <li class="time">刷新时间<span>最近一次收到汤圆的时间</span></li>
            </ul>
            <ul class="anchor-new-list">
                <?php foreach ($warmwinter['DoteyRiceBallRank'] as $uid=>$doteyRow):?>
                <li class="clearfix no<?php echo $doteyRow['rank_order'];?>">
                    <em class="order"><?php echo $doteyRow['rank_order'];?></em>
                    <a href="<?php $this->getTargetHref("/{$uid}",true,false)?>" title="<?php echo $doteyRow['nk'];?>" target="<?php echo $this->target?>">
                    <em class="lvlo lvlo-<?php echo $doteyRow['dk'];?>"></em><?php echo mb_substr((empty($doteyRow['nk'])?"求昵称":$doteyRow['nk']),0,6,'UTF-8');?></a>
                    <span><?php echo $doteyRow['gift_num'];?></span>
                    <span><?php echo $doteyRow['update_time'];?></span>
                </li>
                <?php endforeach;?>	   
              </ul>
        </div><!--.anchor-new-con-->
        <div class="anchor-new-con plute">
            <ul class="anchor-new-h clearfix ovhide">
                <li class="name">富豪昵称</li>
                <li>送礼总数</li>
                <li class="time">刷新时间<span>最近一次送出汤圆的时间</span></li>
            </ul>
            <ul class="anchor-new-list">
            <?php foreach ($warmwinter['UserRiceBallRank'] as $uid=>$userRow): ?>
                <li class="clearfix no<?php echo $userRow['rank_order'];?>">
                    <em class="order"><?php echo $userRow['rank_order'];?></em>
                    <a href="#" title="<?php echo $userRow['nk'];?>"><em class="lvlr lvlr-<?php echo $userRow['rk'];?>"></em><?php echo mb_substr((empty($userRow['nk'])?"求昵称":$userRow['nk']),0,6,'UTF-8');?></a>
                    <span><?php echo $userRow['gift_num'];?></span>
                    <span><?php echo $userRow['update_time'];?></span>
                </li>
            <?php endforeach;?> 
              </ul>
        </div><!--.anchor-new-con-->
    </div><!--.kuso-list-->
</div>
<script type="text/javascript">
var time_test = "<?php echo $time_test;?>"; 
if(time_test!=1)
{
	alert("活动已结束");
}

$(function(){
    $('.anchor-new-h li.time').hover(function(){
        $(this).find('span').css('display','block');
    },function(){
         $(this).find('span').css('display','none');
    });
})
</script>