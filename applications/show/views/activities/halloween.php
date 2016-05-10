<div class="kong">
  <div class="w1000 relative">
  </div>
</div><!--.kong-->


<div class="w1000 mt20">
  <div class="title"><strong>玩家兑换套餐一</strong>
  <?php if(isset($userInfo['totalPumpkinNum']) && $userInfo['totalPumpkinNum']>0):?> 
  <span>您当前已送出<em><?php echo $userInfo['totalPumpkinNum'];?></em>个万圣南瓜，已兑换<em><?php echo $userInfo['exchangedPumpkinNum'];?></em>个</span> 
  <?php endif;?>
  </div>
  
  <div class="package_box clearfix">
      <!--
      <div class="tips">恭喜，兑换成功<br/><a href="#"><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/confire.jpg" /></a></div>
      <div class="tips">抱歉，您不能兑换<br/><a href="#"><img src="images/confire.jpg" /></a></div>
      <div class="tips">活动已结束<br/><a href="#"><img src="images/confire.jpg" /></a></div>-->
  
      <div class="package_con fleft">
        <dl class="tc_box">
            <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/tc.jpg" /> <a>万圣南瓜</a></dt>
            <dd>3000个</dd>
        </dl><!-- .tc_box -->
        <div class="dengyu mt20">=</div>
        <dl class="tc_box ml10">
            <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/vip_yellow.jpg" /> <a>黄色VIP</a></dt>
            <dd class="blue">15天</dd>
        </dl><!-- .tc_box -->
      <a class="dh mt20" href="javascript:;" onclick="halloweenOp.userExchange(1);"><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/duihuan.jpg" width="101" height="31" /></a></div><!-- .package_con -->
      
      <div class="package_con fleft ml20">
        <dl class="tc_box">
            <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/tc.jpg" /> <a>万圣南瓜</a></dt>
            <dd>5000个</dd>
        </dl><!-- .tc_box -->
        <div class="dengyu mt20">=</div>
        <dl class="tc_box ml10">
            <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/vip_purple.jpg" /> <a>紫色VIP</a></dt>
            <dd class="blue">15天</dd>
        </dl><!-- .tc_box -->
        <a class="dh mt20" href="javascript:;" onclick="halloweenOp.userExchange(2);"><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/duihuan.jpg" width="101" height="31" /></a>
      </div><!-- .package_con -->
      
      <div class="package_con fleft mt20">
        <dl class="tc_box">
            <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/tc.jpg" /> <a>万圣南瓜</a></dt>
            <dd>10000个</dd>
        </dl><!-- .tc_box -->
        <div class="dengyu mt20">=</div>
        <dl class="tc_box ml10">
            <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/nanguamache.png" /> <a>南瓜马车</a></dt>
            <dd class="blue">30天</dd>
        </dl><!-- .tc_box -->
        <a class="dh mt20" href="javascript:;" onclick="halloweenOp.userExchange(3);"><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/duihuan.jpg" width="101" height="31" /></a>
      </div><!-- .package_con -->
      
      <div class="intro mt20 ml20 fleft">
        <strong>兑换说明：</strong>
        <p>1、玩家每送出3000个万圣南瓜，即能兑换15天的黄色VIP；送出5000个万圣南瓜，即能兑换15天的紫色VIP，送出1万个万圣南瓜，即能兑换30天的南瓜马车</p>
        <p>2、玩家能够多次兑换套餐中的奖品，但需送出相应数量的万圣南瓜。(例子：玩家A送出5000个万圣南瓜，只能选择兑换黄色VIP或紫色VIP，若想两个都能兑换，则需另外再送3000个万圣南瓜，以此类推。)</p>
        <p>3、若想兑换更优惠的套餐，请看套餐二 ，兑换规则与套餐一相同。</p>
        <p>4、如若同时兑换了含有黄色vip与紫色vip的套餐，则两种vip同时生效，优先显示紫色vip。重复兑换同一礼包礼物叠加计算有效时间。</p>
      </div>
     
  </div><!-- .package_box -->
</div><!--.w1000-->


<div class="w1000 mt20">
  <div class="title"><strong>玩家兑换套餐二（推荐）</strong></div>
  
  <div class="package_box clearfix">
    <ul class="tuijian_con">
      <li>
       	<dl class="tc_box">
          <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/tc.jpg" /> <a>万圣南瓜</a></dt>
          <dd>12000个</dd>
        </dl><!-- .tc_box -->
        <div class="dengyu mt20">=</div>
        <dl class="tc_box ml10">
          <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/vip_yellow.jpg" /> <a>黄色VIP</a></dt>
          <dd class="blue">15天</dd>
        </dl><!-- .tc_box -->
        <div class="dengyu mt20">+</div>
        <dl class="tc_box ml10">
          <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/nanguamache.png" /> <a>南瓜马车</a></dt>
          <dd class="blue">30天</dd>
        </dl><!-- .tc_box -->
        <a class="dh mt20" href="javascript:;" onclick="halloweenOp.userExchange(4);"><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/duihuan.jpg" width="101" height="31" /></a>        
      </li>
      
      <li class="mt20">
       	<dl class="tc_box">
          <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/tc.jpg" /> <a>万圣南瓜</a></dt>
          <dd>13000个</dd>
        </dl><!-- .tc_box -->
        <div class="dengyu mt20">=</div>
        <dl class="tc_box ml10">
          <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/vip_purple.jpg" /> <a>紫色VIP</a></dt>
          <dd class="blue">15天</dd>
        </dl><!-- .tc_box -->
        <div class="dengyu mt20">+</div>
        <dl class="tc_box ml10">
          <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/nanguamache.png" /> <a>南瓜马车</a></dt>
          <dd class="blue">30天</dd>
        </dl><!-- .tc_box -->
        <a class="dh mt20" href="javascript:;" onclick="halloweenOp.userExchange(5);"><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/duihuan.jpg" width="101" height="31" /></a>        
      </li>
      
      <li class="mt20">
       	<dl class="tc_box">
          <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/tc.jpg" /> <a>万圣南瓜</a></dt>
          <dd>15000个</dd>
        </dl><!-- .tc_box -->
        <div class="dengyu mt20">=</div>
        <dl class="tc_box ml10">
          <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/vip_purple.jpg" /> <a>紫色VIP</a></dt>
          <dd class="blue">15天</dd>
        </dl><!-- .tc_box -->
        <div class="dengyu mt20">+</div>
        <dl class="tc_box ml10">
          <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/nanguamache.png" /> <a>南瓜马车</a></dt>
          <dd class="blue">30天</dd>
        </dl><!-- .tc_box -->
        <div class="dengyu mt20">+</div>
        <dl class="tc_box ml10">
          <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/pipieggs.jpg" /> <a>皮蛋</a></dt>
          <dd class="orange">10000个</dd>
        </dl><!-- .tc_box -->
        <a class="dh mt20 ml20" href="javascript:;" onclick="halloweenOp.userExchange(6);"><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/duihuan.jpg" width="101" height="31" /></a>        
      </li>
    </ul>
  </div><!-- .package_box -->
</div><!--.w1000-->



<div class="w1000 mt20">
  <div class="title"><strong>主播兑换套餐</strong> 
  <?php if(isset($doteyInfo['dotey_id']) && $doteyInfo['user_type']==1 && $doteyInfo['totalPumpkinNum']>0):?> 
  <span>您当前已收到<em><?php echo $doteyInfo['totalPumpkinNum'];?></em>个万圣南瓜，<em><?php echo isset($doteyInfo['totalPumpkinNum']) && $doteyInfo['exchangedPumpkinNum']>0?"已兑换":"未兑换";?></em></span>
  <?php endif;?>
   </div>
  
  <div class="package_box clearfix">
  
      <div class="package_con fleft">
        <dl class="tc_box">
            <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/tc.jpg" /> <a>万圣南瓜</a></dt>
            <dd>50000个</dd>
        </dl><!-- .tc_box -->
        <div class="dengyu mt20">=</div>
        <dl class="tc_box ml10">
            <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/halloweenqueen.jpg" /> <a>万圣女王</a></dt>
            <dd class="blue">1个月</dd>
        </dl><!-- .tc_box -->
        <a class="dh mt20" href="javascript:;" onclick="halloweenOp.doteyExchange(13);"><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween//duihuan.jpg" width="101" height="31" /></a>
    </div><!-- .package_con -->
      
      <div class="package_con fleft ml20">
        <dl class="tc_box">
            <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/tc.jpg" /> <a>万圣南瓜</a></dt>
            <dd>30000个</dd>
        </dl><!-- .tc_box -->
        <div class="dengyu mt20">=</div>
        <dl class="tc_box ml10">
            <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/nanguanvlang.jpg" /> <a>南瓜女郎</a></dt>
            <dd class="blue">1个月</dd>
        </dl><!-- .tc_box -->
        <a class="dh mt20" href="javascript:;" onclick="halloweenOp.doteyExchange(12);"><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/duihuan.jpg" width="101" height="31" /></a>
    </div><!-- .package_con -->
      
      <div class="package_con fleft mt20">
        <dl class="tc_box">
            <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/tc.jpg" /> <a>万圣南瓜</a></dt>
            <dd>10000个</dd>
        </dl><!-- .tc_box -->
        <div class="dengyu mt20">=</div>
        <dl class="tc_box ml10">
            <dt><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/tangguojingling.jpg" /> <a>糖果精灵</a></dt>
            <dd class="blue">1个月</dd>
        </dl><!-- .tc_box -->
        <a class="dh mt20" href="javascript:;" onclick="halloweenOp.doteyExchange(11);"><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/halloween/duihuan.jpg" width="101" height="31" /></a>
    </div><!-- .package_con -->
      
      <div class="intro mt20 ml20 fleft">
        <strong>兑换说明：</strong>
        <p>1、主播收到万圣南瓜5万个及以上，就能兑换1个万圣女王；收到万圣南瓜3万个及以上，就能兑换1个南瓜女郎；收到万圣南瓜1万个级以上，就能兑换1个糖果精灵</p>
        <p>2、万圣女王、南瓜女郎和糖果精灵为动态人物形象，显示在视频框右下角，显示时间为一个月（从主播兑换成功之时开始计算）</p>
        <p>3、以上三个人物形象不能重复兑换，每位主播只能兑换一次，兑换过后不得更改，请主播谨慎选择，以免后悔。</p>
      </div>
     
  </div><!-- .package_box -->
</div><!--.w1000-->
<script>
var time_test = "<?php echo $time_test;?>"; 
if(time_test!=1)
{
	alert("当前不是活动时间");
}

var halloweenOp={
	lastClickTime:0,
	timeInterval:3000,
	userExchange:function(setmeal_id){
		var timestamp = (new Date()).valueOf();
		if(this.lastClickTime==0 || (timestamp-this.lastClickTime)>this.timeInterval)
		{
			this.lastClickTime=(new Date()).valueOf();
			$.ajax({
				type:'POST',
				url:'index.php?r=/Activities/Halloween',
				data:{op:'userExchange',setmeal_id:setmeal_id},
				dataType:'json',
				async:false,
				success:function(data){
					
					if(data==1)
						alert("兑换成功");
					else if(data==-1)
						alert("还没有登录");
					else if(data==-2)
						alert("没有送出足够的南瓜");	
					else if(data==-3)
						alert("兑换失败");	
					else if(data==-9)
						alert("只有活动期间才能兑换");					
					else
						alert("兑换失败");	
				}
			});
		}
		else
			alert("请不要频繁点击兑换按钮");
	},
	doteyExchange:function(setmeal_id){
		var timestamp = (new Date()).valueOf();
		if(this.lastClickTime==0 || (timestamp-this.lastClickTime)>this.timeInterval)
		{
			this.lastClickTime=(new Date()).valueOf();
			$.ajax({
				type:'POST',
				url:'index.php?r=/Activities/Halloween',
				data:{op:'doteyExchange',setmeal_id:setmeal_id},
				dataType:'json',
				async:false,
				success:function(data){
					if(data==1)
						alert("兑换成功");
					else if(data==-1)
						alert("还没有登录");
					else if(data==-2)
						alert("您不是主播");	
					else if(data==-3)
						alert("主播只能兑换一次");	
					else if(data==-4)
						alert("没有收到足够的南瓜");
					else if(data==-9)
						alert("只有活动期间才能兑换");
					else
						alert("兑换失败");
				}
			});
		}
		else
			alert("请不要频繁点击兑换按钮");	
	}
}
</script>