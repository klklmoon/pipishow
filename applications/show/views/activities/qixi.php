<div class="kong"></div><!--.kong-->


<div class="w1000 clearfix relative mt30"> 
  <div class="tit"><strong>活动规则</strong></div>
  
  <div class="rule">
    <img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/qixi/pic1.png" /><em class="absolute">仙女剑</em>
    <div class="absolute rulecon">
    1、“仙女剑”为本活动指定礼物，送其他礼物不计入榜单<br/>
	2、只有在活动期间送出的“仙女剑”才能计入榜单哦<br/>
	3、活动期间的主播请一律古装亮相，若屡次发现活动期间未穿古装直播，则取消获奖资格<br/>
	4、只有报名过的参赛主播才能计入主播榜，只有给参赛主播送出仙女剑的用户才能计入富豪榜
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

<div class="w1000 relative clearfix mt30"> 
  <div class="tit"><strong>活动奖励</strong></div>
  <img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/qixi/mid.jpg" />
  <div class="ex-rule">
  	<strong>特别说明：</strong>
    <p>活动结束后5个工作日内，请获奖的主播和玩家请将自己的皮皮用户名、收件地址、真实姓名、邮编和手机号发至邮箱553216564@qq.com，
也可联系此QQ号，过期作废，敬请谅解。</p>
	<span>* 本活动最终解释权归皮皮乐天所有</span>
  </div>
</div>


<div class="w1000 clearfix ellipsis mt20 relative"> 
  <div class="fleft anchor1">
    	<h6>主播榜</h6>
        <div class="anchor1-con">
        	<ul class="anchor1-h clearfix ovhide">
            	<li>主播昵称</li>
                <li>等级</li>
                <li>收到仙女剑</li>
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
                <li>送出仙女剑</li>
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