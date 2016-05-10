<div class="kong"></div><!--.kong-->


<div class="w1000 clearfix relative mt20 h240"> 
  <div class="tit"><strong>活动规则</strong></div>
  
  <div class="rule">
    <img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/beer/pic1.jpg" />
    <div class="absolute rulecon">
1、活动时间：7月18日09：00——7月21日23：30<br/>
2、小啤酒为本次活动指定礼物，送其他礼物不能计入榜单哦<br/>
3、参赛主播为系统随机挑选<br/>
4、活动奖励：<br/>
（1）主播榜前15名的主播能够免费游玩苏州乐园（包食宿），若主播因个人原因不能到现场，则工作人员可以将门票寄给主播，<br/>
     有效期到10月为止。<br/>
（2）富豪榜前15名的玩家将获得苏州乐园游玩体验券！<br/>
（请上榜玩家在7月25日之前将收件地址、个人姓名、手机号、邮编发至邮箱2698918839@qq.com，过期无效）<br/>
<span>*本活动最终解释权归皮皮乐天所有</span>
    </div>
  </div><!-- rule -->
  
</div><!-- W1000 -->



<div class="w1000 clearfix relative mt20"> 
  <div class="tit"><strong>参赛主播</strong></div> 
  
  <div class="anchor">
                <ul id="MianList" class="anchor-nav">
                  <li><a class="on" href="javascript:void(0);">正在直播</a></li>
                  <li style="margin-left:2px;"><a href="javascript:void(0);">待直播</a></li>
                </ul>
                
                <div id="MainCon1" class="anchor-box">
                    <?php $this->renderPartial('application.views.archives.liveArchivesTemplate',$living); ?>           
                </div>
                <div id="MainCon2" class="anchor-box" style="display:none;"> 
                    <?php $this->renderPartial('application.views.archives.liveArchivesTemplate',$wait); ?>         
                </div><!--.anchor-box-->
  </div><!--.anchor-->
   
</div><!-- W1000 -->


<div class="w1000 clearfix ellipsis mt20 relative"> 
  <div class="fleft anchor1">
    	<h6>主播榜</h6>
        <div class="anchor1-con">
        	<ul class="anchor1-h clearfix ovhide">
            	<li>主播昵称</li>
                <li>等级</li>
                <li>收到小啤酒</li>
            </ul>
            <ul class="anchor1-list">
            	<?php
            	$i = 0;
            	foreach($dotey_rank as $d){
					$i++;
				?>
				<li class="clearfix<?php if($i<=3) echo " no".$i;?>">
					<em class="order"><?php echo $i;?></em>
					<span><?php echo $d['nickname'];?>&nbsp;</span>
					<span><em class="lvlo lvlo-<?php echo $d['rank'];?>"></em></span>
					<span><?php echo $d['num'];?></span>
				</li>
				<?php }?>          
              </ul>
        </div>
    </div><!-- .anchor1 -->
    
  <div class="fright anchor1">
    	<h6>富豪榜</h6>
        <div class="anchor1-con">
        	<ul class="anchor1-h clearfix ovhide">
            	<li>富豪昵称</li>
                <li>等级</li>
                <li>送出小啤酒</li>
            </ul>
            <ul class="anchor1-list">
            	<?php
            	$i = 0;
            	foreach($user_rank as $d){
					$i++;
				?>
				<li class="clearfix<?php if($i<=3) echo " no".$i;?>">
					<em class="order"><?php echo $i;?></em>
					<span><?php echo $d['nickname'];?>&nbsp;</span>
					<span><em class="lvlr lvlr-<?php echo $d['rank'];?>"></em></span>
					<span><?php echo $d['num'];?></span>
				</li>
				<?php }?>
             </ul>
        </div>
    </div><!-- .anchor1 -->  
</div><!-- W1000 -->

<script type="text/javascript">
$(function(){
	/*互动综艺第一平台*/
	$('.area-con').each(function(){
		$(this).find('dd:first').addClass('tip');
	});
	$('.area-con').hover(function(){
		$(this).find('dd:first').addClass('tipover');
	},function(){
		$(this).find('dd:first').removeClass('tipover');
	});

	/*左边美女主播模块*/
	$(".anchor-con li").live({mouseenter: function () {
		$(this).find('.anchor-head').addClass('chorover');
		$(this).find('.attent').show().hover(function(){
			$(this).find('.attent-text').show();
		},function(){
			$(this).find('.attent-text').hide();	
		});              
	}, mouseleave: function () {
		$(this).find('.anchor-head').removeClass('chorover');
		$(this).find('.attent').hide();    
	}
	});

	$("#MianList a").click(function(){
		$("#MianList a").removeClass('on');
		$(this).addClass("on");
		$(".anchor-box").css('display','none');
		var n = $("#MianList a").index(this)+1;
		$("#MainCon"+n).css('display','block');
	});
});
</script>