<div class="kong">
  <div class="w1000 relative">
  </div>
</div><!--.kong-->


<div class="w1000 relative main">

  <div class="vote none">
    <div class="cont"><strong>20000皮蛋</strong><br/>恭喜，奖励领取成功<br/><a href="#"><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/tuesday/conf.jpg" /></a></div>
  </div>
  
  <div class="main1">
    1、给皇冠以下的单个主播送礼满10000皮蛋，即可获得一个真爱勋章，有效期1天；给不同的皇冠以下的主播各送满10000皮蛋，<br/>能够分别增加1天有效期，最高有效期5天（PS：给同一个主播继续送礼，不能增加有效天）<br/>
    2、给皇冠以上的单个主播送礼满20000皮蛋，即可获得一个钻石勋章，有效期1天；给不同的皇冠以上的主播各送满20000皮蛋，<br/>能够分别增加1天有效期，最高有效期5天（PS：给同一个主播继续送礼，不能增加有效天）<br/>
    3、当天累计送礼满50000皮蛋的用户，能够获得一个价值30000皮蛋的福利礼包
  </div>

  <div class="main2">
    <a id="tuesday" class="lq" href="javascript:void(0)"><img src="<?php echo $this->pipiFrontPath?>/fontimg/activities/tuesday/lq.gif" width="209" height="67" /></a>
    <span>1、以上奖励只有在每周二送礼才有效，且奖励能够重复获得，福利礼包每人只能领取1次<br/>
        2、若用户在送礼过程中主播升级了，则当天依然按照主播升级前的等级获取真爱勋章，到下一周二再按照升级后的等级来获取钻石勋章<br/>
        3、用户消费后，达到奖励要求，不会立即生效，15分钟后才生效<br/>
        *本活动最终解释权归皮皮乐天所有</span>
  </div>
  
</div>

<script type="text/javascript">
$('#tuesday').bind('click',function(){

	if($.User.getSingleAttribute('uid',true) <= 0){
		curLoginController = 'login';
		$.User.loginController('login');
		return false;
	}

	$.ajax({
		type: "POST",
		url: "index.php?r=activities/doTuesDay",
		dataType: "json",
		success: function(response){
			if(response.status == 'success'){
				$('#SetSuc').html('<p class="oneline">'+response.message+'</p>');
				$.mask.show('SetSuc',2000);
			}else{
				$('#SetSuc').html('<p class="oneline">'+response.message+'</p>');
				$.mask.show('SetSuc',2000);
			}
		}
	}
	);
});
</script>
