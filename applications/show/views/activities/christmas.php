<div class="w1000">
    <div class="rule">                   
        <p>1、活动专属礼物：“圣诞帽”  定价10皮蛋/个</p>
        <p>2、活动规则：用户为主播赠送圣诞专属礼物——圣诞帽，让女主播戴上圣诞帽登上排行榜，</p>
        <p style="text-indent:90px;">度过一个快乐又难忘的圣诞节。</p>
    </div><!--.rule-->
    <dl class="rewardcrm ten">
        <dt>活动结束时，收到以上专属礼物总数达到以下条件可获得奖励：</dt>
        <dd>第一名：蓝夜 Duke(公爵）高级大振膜电容麦原价（800元）+ “圣诞女王”徽章30天 + 魅力值30万，基数10万个</dd>
        <dd>第二名：蓝夜LY-R8火花大振膜电容麦克风原价（600元）+ “圣诞女王”徽章30天+魅力值20万，基数5万个</dd>
        <dd>第三名：蓝夜Ly800炫彩（黑、红、蓝）+ “圣诞女王”徽章30天  +魅力值10万，基数3万个</dd>
        <dd>第四名------第十名：高清红外视频一个，基数1万个</dd>
    </dl><!--.reward-->
    <dl class="rewardcrm three">
        <dt>活动结束时，送出礼物总数排行前3者，可获如下奖励：</dt>
        <dd>第一名：圣诞雪橇（专属座驾）+“圣诞巨星”徽章30天+50万贡献值 ,8888888靓号使用60天，基数10万个 </dd>
        <dd>第二名：圣诞雪橇（专属座驾）+“圣诞巨星”徽章30天+30万贡献值 ,6666666靓号使用60天，基数5万个</dd>
        <dd>第三名：圣诞雪橇（专属座驾）+“圣诞巨星”徽章30天+10万贡献值 ,3333333靓号使用60天，基数3万个</dd>
        <dd><p>注：1.以上奖励，用户只有满足相关基数要求方可获得。奖励按“先到先得”原则发放，无并列排行，一人一份。</p></dd>
        <dd><p style="text-indent:24px;">2.主播获得的魅力值用于升级非提现魅力点。</p></dd>
    </dl><!--.reward-->
    <p class="explain clearfix">
        <span>官方客服：<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=121294340&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:121294340:41 &r=0.41319712803607783"></a></span>
        <span>活动说明：奖励在活动结束后3天内发放。</span>
        <span class="red">*本活动最终解释权归皮皮乐天所有</span>
    </p><!--.explain-->
    <div class="kuso">
        <a href="<?php $this->getTargetHref($this->createUrl('activities/christmas', array('pos' => 'top')));?>" title="点击刷新榜单"></a>
    </div><!--.kuso-->
    
    <a id="top"></a>
    <div class="kuso-list clearfix">
        <div class="anchor-new-con">
            <ul class="anchor-new-h clearfix ovhide">
                <li class="name">主播昵称</li>
                <li>收礼总数</li>
                <li class="time">刷新时间<span>最近一次收到帽子的时间</span></li>
            </ul>
            <div class="startbox<?php echo $time_start ? ' none' : '';?>"></div>
            <ul class="anchor-new-list<?php echo $time_start ? '' : ' none';?>">
                <?php foreach ($top['DoteyRiceBallRank'] as $uid=>$doteyRow):?>
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
            <div class="startbox<?php echo $time_start ? ' none' : '';?>"></div>
            <ul class="anchor-new-list<?php echo $time_start ? '' : ' none';?>">
            <?php foreach ($top['UserRiceBallRank'] as $uid=>$userRow): ?>
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
var position = '<?php echo $position;?>';
var time_end = "<?php echo $time_end;?>";
if(time_end!=1)
{
	alert("活动已结束");
}

$(function(){
    $('.anchor-new-h li.time').hover(function(){
        $(this).find('span').css('display','block');
    },function(){
         $(this).find('span').css('display','none');
    });

    if(position){
		window.location.href="#"+position;
    }
})
</script>