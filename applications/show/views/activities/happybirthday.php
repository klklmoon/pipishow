
<div class="kong"></div>
<!--.kong-->
<?php $userService=new UserService();?>
<?php if(isset($todayBirthdayDoteys) && count($todayBirthdayDoteys)>0):?>
<div class="w1000 mt20">
	<div class="round-top"></div>

	<div class="bjwhite">
		<div class="tit">
			<img
				src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/cake.gif" />今日寿星
		</div>
     <?php
	
	foreach ($todayBirthdayDoteys as $doteyInfo) :
		$sliveText = isset($doteyInfo['status']) && $doteyInfo['status'] == 1 ? '直播中' : '待直播';
		$sliveClass = isset($doteyInfo['status']) && $doteyInfo['status'] == 1 ? 'playing' : 'readying';
		?>
     <div class="ovhide line mt10">
			<ul class="anchor-con clearfix ovhide mt10 fleft">
				<li>
					<div class="anchor-head">
						<a
							href="<?php echo $this->getTargetHref("/".$doteyInfo['dotey_id'],true,false)?>"
							title="美女主播" target="<?php echo $this->target?>"> <img
							src="<?php echo $doteyInfo['pic'];?>"></a>
						<p class="<?php echo $sliveClass?>"><?php echo $sliveText?></p>
					</div>
					<p class="chorname clearfix">
                    <?php if($doteyInfo['show_cake']):?>	
                  	<img
							style="display: block; float: left; height: 22px;"
							src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/cake.gif">
                	<?php endif;?>          
              <a
							href="<?php echo $this->getTargetHref("/".$doteyInfo['dotey_id'],true,false)?>"
							title="<?php echo $doteyInfo['title'];?>"
							target="<?php echo $this->target?>" class="fleft nambtm pink">
              <?php echo $doteyInfo['title'];?></a>

					</p>
					<p class="time"><?php echo  isset($doteyInfo['sub_title'])?$doteyInfo['sub_title']:"";?></p>
					<p class="totbless mt10">
						TA已经收到<a><?php echo $doteyInfo['giftTotalNum'];?></a>份祝福啦~~~
					</p>
				</li>
			</ul>  
     <?php $dotey=$userService->getUserFrontsAttributeByCondition($doteyInfo['dotey_id'],true,true);?>
     <div class="inform fleft">昵称：<?php echo $dotey['nk'];?> <br /> 生日：<?php echo date("m月d日");?></div>
			<div class="acceptgift fleft" style=' background: url("<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/accept_bj-1.jpg")'>
				<ul>
       	<?php foreach ($doteyInfo['giftDetail'] as $gift_id=>$giftInfo):?>
          <li><img
						src="<?php echo $activityGiftList[$gift_id]['url'];?>" /><br /><?php echo $giftInfo['gift_num'];?></li>
		<?php endforeach;?>
       </ul>
			</div>
			<!-- acceptgift -->
		</div> 
     <?php endforeach;?>
  </div>
	<!-- bjwhite -->

	<div class="round-bottom"></div>
</div>
<!-- w1000 今日生日-->
<?php endif;?>

<div class="w1000 mt20">
	<div class="round-top"></div>

	<div class="bjwhite relative">


		<div class="tit">生日套礼</div>
		<div class="taoli">
			<ul>
       <?php foreach ($activityGiftList as $gift_id=>$giftInfo):?>
          <li><img src="<?php echo $giftInfo['url'];?>" /><br /><?php echo $giftInfo['zh_name'] ?> <br />
					<a href="javascript:void(0);"
					onclick="Shop.buyGift('GifNumBox',<?php echo $giftInfo['gift_id'];?>,'<?php echo $giftInfo['zh_name'];?>','<?php echo $giftInfo['pipiegg'];?>');"><img
						src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/buy.jpg" /></a></li>
		<?php endforeach;?>
       </ul>
		</div>
		<!-- taoli -->

		<a href="javascript:void(0);"
			onclick="HappyBirthdayShop.buyGift('GifNumBox','生日套礼',<?php echo $batchPrice;?>);"
			class="absolute buy_btn"><img
			src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/buy-btn.jpg" /></a>

	</div>
	<!-- bjwhite -->

	<div class="round-bottom"></div>
</div>
<!-- w1000 生日套礼-->


<div id="thisMonthBirthdayDoteys" class="w1000 mt20"></div>
<!-- w1000 本月寿星-->


<script type="text/javascript">

$(function(){
	$.ajax({
		type: "POST",
		url: "<?php echo $this->createUrl('Activities/PageMonthBirthdayDoteys');?>",
		dataType:'html',
		success: function (resonseData) {
			$("#thisMonthBirthdayDoteys").html(resonseData);
		}
	});

	$('#pager > ul > li > a').live('click',function(){
		var href = $(this).attr('href');
		$.ajax({
			url:href,
			type:'POST',
			dataType:'html',
			success:function(resonseData){
				$("#thisMonthBirthdayDoteys").html(resonseData);
			}
		});
		$(this).attr('href','javascript:void(0);');
	});
});

</script>


<div class="w1000 mt20">
	<div class="round-top"></div>

	<div class="bjwhite relative">
		<div class="tit">生日荣誉榜</div>
		<p class="rule">
			入驻规则说明：<br /> 1、生日公主榜当月魅力值最高的前三名主播，将作为当月生日公主，入驻生日荣誉榜，长期陈列展示<br /> <a>温馨提示：生日公主榜只显示当月生日的主播，生日魅力值是指当月用户给主播赠送的生日礼物的总魅力值</a>
		</p>
		<p class="rule">
			2、生日王子榜当月贡献值最高的前三名用户，将作为当月生日王子，入驻生日荣誉榜，长期陈列展示<br /> <a>温馨提示：生日王子榜显示为当月生日的主播送过生日礼物的用户（只统计生日礼物的贡献值）</a>
		</p>
		<p class="rule">PS：生日公主中的守护者为当月为该主播送出生日礼物总魅力值最高的用户</p>
      <?php
						if ($honorRank['yearMonth'] >= date("Y-m", strtotime(HappyBirthdayService::ACTIVITY_START_DATE))) :
							$yearMonth = explode('-', $honorRank['yearMonth']);
							$month = $yearMonth[1];
							?>  
      <div class="birth">
			<div class="birthgirl fleft">
        <?php $princessRank=$honorRank['honorRank']['doteyRank'];?>
          <div class="tit">生日公主— —<?php echo $month;?>月</div>
				<div class="cont">
					<dl class="conbox-list">
             <?php $princess1=$userService->getUserFrontsAttributeByCondition($princessRank[1]['dotey_id'],true,true);?> 
                <dt>
							<a
								href="<?php echo $this->getTargetHref("/".$princessRank[1]['dotey_id'],true,false)?>"
								title="<?php echo mb_substr((empty($princess1['nk'])?"求昵称":$princess1['nk']),0,8,'UTF-8');?>"
								target="<?php echo $this->target?>"> <img
								src="<?php echo $userService->getUserAvatar($princessRank[1]['dotey_id'],"small");?>"></a>
						</dt>
						<dd class="ml20">
							<a
								href="<?php echo $this->getTargetHref("/".$princessRank[1]['dotey_id'],true,false)?>"
								title="<?php echo mb_substr((empty($princess1['nk'])?"求昵称":$princess1['nk']),0,6,'UTF-8');?>"
								target="<?php echo $this->target?>">NO.1 <?php echo $princess1['nk'];?></a>
							<p>收到生日礼物：<?php echo $princessRank[1]['gift_num'];?></p>
							<p>生日魅力值：<?php echo $princessRank[1]['sum_charm'];?></p>
						</dd>
					</dl>

					<div class="guard mt10 ml15">守护者：
            <?php foreach ($princessRank[1]['guardian'] as $guardian):?>
            <?php $user=$userService->getUserFrontsAttributeByCondition($guardian['uid'],true,true);?> 
            <?php if($guardian['rank']==1):?>
            <a class="numone"><?php echo $guardian['rank'].".".mb_substr((empty($user['nk'])?"求昵称":$user['nk']),0,6,'UTF-8');?></a>
						<em class="lvlr lvlr-<?php echo $user['rk'];?>"></em> <br />
            <?php else:?> 
            <a class="ml48"><?php echo $guardian['rank'].".".mb_substr((empty($user['nk'])?"求昵称":$user['nk']),0,6,'UTF-8');?></a>
						<em class="lvlr lvlr-<?php echo $user['rk'];?>"></em> 
            	<?php if($guardian['rank']<3):?>
            		<br />
            	<?php endif;?>
            <?php endif;?>
            <?php endforeach;?>
            </div>

					<dl class="conbox-list-small fleft">
             <?php $princess2=$userService->getUserFrontsAttributeByCondition($princessRank[2]['dotey_id'],true,true);?> 
                <dt>
							<a
								href="<?php echo $this->getTargetHref("/".$princessRank[2]['dotey_id'],true,false)?>"
								title="<?php echo mb_substr((empty($princess2['nk'])?"求昵称":$princess2['nk']),0,8,'UTF-8');?>"
								target="<?php echo $this->target?>"> <img
								src="<?php echo $userService->getUserAvatar($princessRank[2]['dotey_id'],"small");?>"></a>
						</dt>
						<dd class="ml20">
							<a
								href="<?php echo $this->getTargetHref("/".$princessRank[2]['dotey_id'],true,false)?>"
								title="<?php echo mb_substr((empty($princess2['nk'])?"求昵称":$princess2['nk']),0,8,'UTF-8');?>"
								style="font-size: 12px;" target="<?php echo $this->target?>">NO.2 <?php echo mb_substr((empty($princess2['nk'])?"求昵称":$princess2['nk']),0,8,'UTF-8');?></a>
							<p>收到生日礼物：<?php echo $princessRank[2]['gift_num']?></p>
							<p>生日魅力值：<?php echo $princessRank[2]['sum_charm']?></p>
						</dd>
					</dl>

					<dl class="conbox-list-small fleft">
             <?php $princess3=$userService->getUserFrontsAttributeByCondition($princessRank[3]['dotey_id'],true,true);?>
                <dt>
							<a
								href="<?php echo $this->getTargetHref("/".$princessRank[3]['dotey_id'],true,false)?>"
								title="<?php echo mb_substr((empty($princess3['nk'])?"求昵称":$princess3['nk']),0,6,'UTF-8');?>"><img
								src="<?php echo $userService->getUserAvatar($princessRank[3]['dotey_id'],"small");?>"
								target="<?php echo $this->target?>"></a>
						</dt>
						<dd class="ml20">
							<a
								href="<?php echo $this->getTargetHref("/".$princessRank[3]['dotey_id'],true,false)?>"
								title="<?php echo mb_substr((empty($princess3['nk'])?"求昵称":$princess3['nk']),0,6,'UTF-8');?>"
								style="font-size: 12px;" target="<?php echo $this->target?>">NO.3 <?php echo mb_substr((empty($princess3['nk'])?"求昵称":$princess3['nk']),0,8,'UTF-8');?></a>
							<p>收到生日礼物：<?php echo $princessRank[3]['gift_num']?></p>
							<p>生日魅力值：<?php echo $princessRank[3]['sum_charm']?></p>
						</dd>
					</dl>
             <?php $user2=$userService->getUserFrontsAttributeByCondition($princessRank[2]['guardian']['uid'],true,true);?>
             <div class="guard mt10 fleft" style="width: 210px;">
						<a>守护者：<?php echo mb_substr((empty($user2['nk'])?"求昵称":$user2['nk']),0,6,'UTF-8');?></a>
						<em class="lvlr lvlr-<?php echo $user2['rk'];?>"></em>
					</div>
             <?php $user3=$userService->getUserFrontsAttributeByCondition($princessRank[3]['guardian']['uid'],true,true);?>
             <div class="guard mt10 fleft" style="width: 210px;">
						<a>守护者：<?php echo mb_substr((empty($user2['nk'])?"求昵称":$user3['nk']),0,6,'UTF-8');?></a>
						<em class="lvlr lvlr-<?php echo $user3['rk'];?>"></em>
					</div>

					<div class="more fright">
						<a
							href="<?php echo $this->getTargetHref($this->createUrl("Activities/MonthHonorRank"),true,false)?>"
							target="<?php echo $this->target?>">查看更多>></a>
					</div>
				</div>
				<!-- cont -->
			</div>
			<!-- birthgirl -->

			<div class="birthboy fright">
        	<?php $princeRank=$honorRank['honorRank']['userRank'];?>
          <div class="tit">生日王子— —<?php echo $month;?>月</div>
				<div class="cont">
					<dl class="conbox-list">
             <?php $prince1=$userService->getUserFrontsAttributeByCondition($princeRank[1]['uid'],true,true);?>
                <dt>
							<a href="javascript:void(0);"
								title="<?php echo mb_substr((empty($prince1['nk'])?"求昵称":$prince1['nk']),0,8,'UTF-8');?>"><img
								src="<?php echo $userService->getUserAvatar($princeRank[1]['uid'],"small");?>"></a>
						</dt>
						<dd class="ml20">
							<a href="javascript:void(0);"
								title="<?php echo mb_substr((empty($prince1['nk'])?"求昵称":$prince1['nk']),0,8,'UTF-8');?>">NO.1<?php echo mb_substr((empty($prince1['nk'])?"求昵称":$prince1['nk']),0,6,'UTF-8');?></a>
							<p>送出生日礼物：<?php echo $princeRank[1]['gift_num'];?></p>
							<p>生日贡献值：<?php echo $princeRank[1]['sum_dedication'];?></p>
						</dd>
					</dl>
					<div class="guard mt10 ml15">守护主播：
            <?php $guardDotey1=$userService->getUserFrontsAttributeByCondition($princeRank[1]['guardDotey']['uid'],true,true);?>
            <a class="numone"><?php echo mb_substr((empty($guardDotey1['nk'])?"求昵称":$guardDotey1['nk']),0,6,'UTF-8');?></a>
						<em class="lvlo lvlo-<?php echo $guardDotey1['dk'];?>"></em><br />
					</div>

					<dl class="conbox-list-small fleft">
             <?php $prince2=$userService->getUserFrontsAttributeByCondition($princeRank[2]['uid'],true,true);?>
                <dt>
							<a href="javascript:void(0);"
								title="<?php echo mb_substr((empty($prince2['nk'])?"求昵称":$prince2['nk']),0,8,'UTF-8');?>"><img
								src="<?php echo $userService->getUserAvatar($princeRank[2]['uid'],"small");?>"></a>
						</dt>
						<dd class="ml20">
							<a href="javascript:void(0);"
								title="<?php echo mb_substr((empty($prince2['nk'])?"求昵称":$prince2['nk']),0,8,'UTF-8');?>"
								style="font-size: 12px;">NO.2 <?php echo mb_substr((empty($prince2['nk'])?"求昵称":$prince2['nk']),0,6,'UTF-8');?></a>
							<p>送出生日礼物：<?php echo $princeRank[2]['gift_num'];?></p>
							<p>生日魅力值：<?php echo $princeRank[2]['sum_dedication'];?></p>
						</dd>
					</dl>

					<dl class="conbox-list-small fleft">
             <?php $prince3=$userService->getUserFrontsAttributeByCondition($princeRank[3]['uid'],true,true);?>
                <dt>
							<a href="javascript:void(0);"
								title="<?php echo mb_substr((empty($prince3['nk'])?"求昵称":$prince3['nk']),0,8,'UTF-8');?>"><img
								src="<?php echo $userService->getUserAvatar($princeRank[3]['uid'],"small");?>"></a>
						</dt>
						<dd class="ml20">
							<a href="javascript:void(0);"
								title="<?php echo mb_substr((empty($prince3['nk'])?"求昵称":$prince3['nk']),0,8,'UTF-8');?>"
								style="font-size: 12px;">NO.3 <?php echo mb_substr((empty($prince3['nk'])?"求昵称":$prince3['nk']),0,6,'UTF-8');?></a>
							<p>送出生日礼物：<?php echo $princeRank[3]['gift_num'];?></p>
							<p>生日魅力值：<?php echo $princeRank[3]['sum_dedication'];?></p>
						</dd>
					</dl>
             <?php $guardDotey2=$userService->getUserFrontsAttributeByCondition($princeRank[2]['guardDotey']['uid'],true,true);?>
             <div class="guard mt10 fleft" style="width: 210px;">
						<a>守护主播：<?php echo mb_substr((empty($guardDotey2['nk'])?"求昵称":$guardDotey2['nk']),0,6,'UTF-8');?></a>
						<em class="lvlo lvlo-<?php echo $guardDotey2['dk'];?>"></em>
					</div>
             <?php $guardDotey3=$userService->getUserFrontsAttributeByCondition($princeRank[3]['guardDotey']['uid'],true,true);?>
             <div class="guard mt10 fleft" style="width: 210px;">
						<a>守护主播：<?php echo mb_substr((empty($guardDotey3['nk'])?"求昵称":$guardDotey3['nk']),0,6,'UTF-8');?></a>
						<em class="lvlo lvlo-<?php echo $guardDotey3['dk'];?>"></em>
					</div>

					<div class="more fright">
						<a
							href="<?php echo $this->getTargetHref($this->createUrl("Activities/MonthHonorRank"),true,false)?>"
							target="<?php echo $this->target?>">查看更多>></a>
					</div>
				</div>
				<!-- cont -->
			</div>
			<!-- birthboy -->
		</div>
		<!-- birth --> 
      <?php endif;?>       
  </div>
	<!-- bjwhite -->

	<div class="round-bottom"></div>
</div>
<!-- w1000 生日荣誉榜-->


<div class="w1000 mt20">
	<div class="round-top"></div>

	<div class="bjwhite relative">

		<div class="vote none">
			<em><img
				src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/x.jpg" /></em>
			<div id="rewardMsg" class="cont"></div>
			<div class="conf">
				<a href="javascript:void(0);"><img
					src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/conf.jpg" /></a>
			</div>
		</div>

		<div class="tit">生日奖励</div>

		<div class="award">
			<div class="award-con fleft">
				<div class="tit">主播奖励</div>
				<div class="conbox">
					<div class="awardbox">
						<div class="fleft pink" style="text-align: center;">
							<img
								src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/charmpoint.jpg" />
							<br /> 10000魅力点
						</div>
						<div class="fright" style="width: 200px;">
							<a class="pink">主播生日当天</a>，收到1套生日套礼， 即可领取1份奖励（最多能领3份） <a
								id="DoteyCharmPoints" href="javascript:happyBirthdayReward(1);"
								class="ml20 disblock mt30"> <img
								src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/lq-btn.jpg" /></a>
						</div>
					</div>
					<!-- awardbox -->
					<div class="awardbox mt30">
						<div class="fleft pink" style="text-align: center;">
							<img
								src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/princess.jpg" />
							<br />生日公主
						</div>
						<div class="fright" style="width: 200px;">
							当月收到生日魅力值前三名的主播， 均可在次月1日领取1个生日公主勋 章（有效期15天，挂在主播直播间）<a
								id="DoteyMedal" href="javascript:happyBirthdayReward(2);"
								class="ml20 disblock mt20"><img
								src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/lq-btn.jpg" /></a>
						</div>
					</div>
					<!-- awardbox -->
				</div>
				<!-- conbox -->
			</div>
			<!-- award-con -->

			<div class="award-con fright">
				<div class="tit">用户奖励</div>
				<div class="conbox">
					<div class="awardbox">
						<div class="fleft pink" style="text-align: center;">
							<img
								src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/charmpoint.jpg" />
							<br /> 20000贡献值
						</div>
						<div class="fright" style="width: 200px;">
							<a class="pink">主播生日当天</a>，用户每送出一套生日<br /> 套礼，即可领取1份奖励（不限领取<br />
							次数） <a id="UserDedication"
								href="javascript:happyBirthdayReward(3);"
								class="ml20 disblock mt20"><img
								src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/lq-btn.jpg" /></a>
						</div>
					</div>
					<!-- awardbox -->
					<div class="awardbox mt30">
						<div class="fleft pink" style="text-align: center;">
							<img
								src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/prince.jpg" />
							<br />生日王子
						</div>
						<div class="fright" style="width: 200px;">
							当月生日贡献值前三名的用户，均可<br />在次月1日领取1个生日王子勋章（有<br />效期15天<a
								id="UserMedal" href="javascript:happyBirthdayReward(4);"
								class="ml20 disblock mt20"><img
								src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/lq-btn.jpg" /></a>
						</div>
					</div>
					<!-- awardbox -->
				</div>
				<!-- conbox -->
			</div>
			<!-- award-con -->
		</div>
		<!-- award -->
		<p class="rule mb20">
			特别说明：<br />
			1、魅力点及贡献值奖励只能在主播生日当天23:59之前领取，其他时间不能领取，只有在主播生日当天收到或者送出的套礼才有效<br />
			2、生日公主和生日王子勋章，只能在次月1日这一天领取，过期无效
		</p>
	</div>
	<!-- bjwhite -->

	<div class="round-bottom"></div>
</div>
<!-- w1000 生日奖励-->


<div class="w1000 mt20">
	<div class="round-top"></div>

	<div class="bjwhite relative">
		<div class="award">
			<div class="award-con fleft">
				<div class="tit">本月生日公主榜</div>
				<div class="bcon">
					<table>
						<tr style="color: #fe2476;">
							<td width="40">&nbsp;</td>
							<td width="120">主播昵称</td>
							<td>等级</td>
							<td width="90">收到生日礼物</td>
							<td width="90">生日魅力值</td>
						</tr>
				<?php foreach ($thisMonthRank['doteyRank'] as $doteyRow):?>
				<?php $monthDotey=$userService->getUserFrontsAttributeByCondition($doteyRow['uid'],true,true);?>
                <tr>
							<td width="40"><a><?php echo $doteyRow['month_rank'];?></a></td>
							<td width="120"><a href="#3"><?php echo mb_substr((empty($monthDotey['nk'])?"求昵称":$monthDotey['nk']),0,8,'UTF-8');?></a></td>
							<td><em class="lvlo lvlo-<?php echo $monthDotey['dk'];?>"></em></td>
							<td width="90"><a><?php echo $doteyRow['gift_num'];?></a></td>
							<td width="90"><a><?php echo $doteyRow['sum_charm'];?></a></td>
						</tr>
				<?php endforeach;?>
                 
          </table>
				</div>
				<!-- bcon -->
			</div>
			<!-- award-con -->

			<div class="award-con fright">
				<div class="tit">本月生日王子榜</div>
				<div class="bcon">
					<table>
						<tr style="color: #fe2476;">
							<td width="40">&nbsp;</td>
							<td width="120">富豪昵称</td>
							<td>等级</td>
							<td width="90">送出生日礼物</td>
							<td width="90">生日贡献值</td>
						</tr>
				<?php foreach ($thisMonthRank['userRank'] as $userRow):?>
				<?php $monthUser=$userService->getUserFrontsAttributeByCondition($userRow['uid'],true,true);?>
                <tr>
							<td width="40"><a><?php echo $userRow['month_rank'];?></a></td>
							<td width="120"><a href="#3"><?php echo mb_substr((empty($monthUser['nk'])?"求昵称":$monthUser['nk']),0,8,'UTF-8');?> </a></td>
							<td><em class="lvlr lvlr-<?php echo $monthUser['rk'];?>"></em></td>
							<td width="90"><a><?php echo $userRow['gift_num'];?></a></td>
							<td width="90"><a><?php echo $userRow['sum_dedication'];?></a></td>
						</tr>
				<?php endforeach;?>
                 
          </table>
				</div>
				<!-- bcon -->
			</div>
			<!-- award-con -->
		</div>
		<!-- award -->
	</div>
	<!-- bjwhite -->

	<div class="round-bottom"></div>
</div>
<!-- w1000 榜单-->


<div class="w1000 mt20">
	<div class="round-top"></div>

	<div class="bjwhite">
		<p class="rule mb10" style="color: #fe2476;">
			温馨提示： 本活动的每日数据统计时间为每日的00：00——23:59；排行榜统计时间为每月1日00:00——每月末日23:59<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;*本活动最终解释权归皮皮乐天所有
		</p>
	</div>
	<!-- bjwhite -->

	<div class="round-bottom"></div>
</div>
<!-- w1000 温馨提示-->
<div id="GifNumBox" class="buy-box"></div>
<div id="LowStock" class="buy-box buylast"></div>
<div id="GuardSucBox2" class="buy-box buylast"></div>
<script type="text/javascript">

function happyBirthdayReward(rewardType)
{
	var url = "<?php echo $this->createUrl('Activities/HappyBirthday');?>";
	if(rewardType==1)
	{
		$.ajax({
			url:url,
			dataType:'json',
			data:{'op':'DoteyCharmPoints'},
			type:'post',
			success:function(data){
				$(".vote").show();
				if(data==1)
				{
					$("#rewardMsg").html("成功领取1份奖励");
				}
				else if(data==-2)
				{
					$("#rewardMsg").html("您还没有登录");
				}
				else if(data==-3)
				{
					$("#rewardMsg").html("您不满足奖励条件");
				}
				else if(data==-4)
				{
					$("#rewardMsg").html("您已经领取了3份奖励");
				}
				else
					$("#rewardMsg").html("领取失败");
			}
		});
	}
	else if(rewardType==2)
	{
		$.ajax({
			url:url,
			dataType:'json',
			data:{'op':'DoteyMedal'},
			type:'post',
			success:function(data){
				$(".vote").show();
				if(data==1)
				{
					$("#rewardMsg").html("成功领取公主勋章");
				}
				else if(data==-2)
				{
					$("#rewardMsg").html("您还没有登录");
				}
				else if(data==-3)
				{
					$("#rewardMsg").html("您不满足奖励条件");
				}
				else if(data==-4)
				{
					$("#rewardMsg").html("您已经领过了");
				}					
				else
					$("#rewardMsg").html("领取失败");
			}
		});
	}
	else if(rewardType==3)
	{
		$.ajax({
			url:url,
			dataType:'json',
			data:{'op':'UserDedication'},
			success:function(data){
				$(".vote").show();
				if(data==1)
				{
					$("#rewardMsg").html("成功领取1份奖励");
				}
				else if(data==-2)
				{
					$("#rewardMsg").html("您还没有登录");
				}
				else if(data==-3)
				{
					$("#rewardMsg").html("您不满足奖励条件");
				}			
				else
					$("#rewardMsg").html("领取失败");
			}
		});
	}
	else if(rewardType==4)
	{
		$.ajax({
			url:url,
			dataType:'json',
			data:{'op':'UserMedal'},
			type:'post',
			success:function(data){
				$(".vote").show();
				if(data==1)
				{
					$("#rewardMsg").html("成功领取王子勋章");
				}
				else if(data==-2)
				{
					$("#rewardMsg").html("您还没有登录");
				}
				else if(data==-3)
				{
					$("#rewardMsg").html("您不满足奖励条件");
				}
				else if(data==-4)
				{
					$("#rewardMsg").html("您已经领过了");
				}
				else
					$("#rewardMsg").html("领取失败");
			}
		});
	}
		
}

$(".vote > em > img").click(function(){
	$(".vote").hide();
});

$(".vote .conf a").click(function(){
	$(".vote").hide();
});
</script>
<script>
var Shop={
		prop_id:0,//购买物品id	
		quantity:0,//当前库存	
		buyNum:0,//购买数量
		price:0,//礼物单价
		isSucc:false,
		//购买礼物
		buyGift:function(obj,gift_id,name,price){
			
			this.price=price;
			if($.User.getSingleAttribute('uid',true)<=0){
				$.User.loginController('login');
				return;
			}
			if(gift_id==null||!gift_id) return false;
			
			var text='<h2 class="clearfix"><em onClick="$.mask.hide(\''+obj+'\');" class="fright">&Chi;</em>'+name+'</h2><div class="buy-con clearfix"><p class="buynum clearfix"><label>购买数量：</label><input type="text" id="quantity" value="1" onblur="Shop.changeGiftPrice()" onkeyup="Shop.changeGiftPrice()"></p><p class="buyprice">购买价格：<strong>'+price+'皮蛋</strong></p><input class="btn sure" type="button" onClick="Shop.confirmBuyGift(\''+obj+'\','+gift_id+')" value="确认"><input onClick="$.mask.hide(\''+obj+'\');" class="btn cancel" type="button" value="取消"></div>';
			//alert(text);
			$("#"+obj).html(text);
			$.mask.show(obj);
			
		},
		//改变礼物总价
		changeGiftPrice:function(){
			var quantity=$("#quantity").val();
			quantity=quantity.replace(/[^\d]/g,'');
			quantity=quantity>9999?9999:quantity;
			if(this.buy_limit==1){
				quantity=quantity>this.quantity?this.quantity:quantity;
			}
			$("#quantity").val(quantity);
			var totalPrice=this.price*10000*quantity/10000;
			$(".buyprice").find('strong').text(totalPrice+'皮蛋');
		},
		
		confirmBuyGift:function(obj,gift_id){
			var buyNum=parseInt($("#quantity").val());
			var o=this;
			$.ajax({
				type:'POST',
				url:'index.php?r=/Activities/buyGift',
				data:{gift_id:gift_id,buyNum:buyNum},
				dataType:'json',
				async:false,
				success:function(data){
					$.mask.hide(obj);
					if(data.flag==1){
						$.User.refershWebLoginHeader();
						var text='<div class="last-con"><p class="suc">购买成功，礼物已存入背包!</p><input class="btn sure" onClick="$.mask.hide(\'GuardSucBox2\');" type="button" value="确认"><input  class="btn cancel" onClick="$.mask.hide(\'GuardSucBox2\');" type="button" value="取消"> </div>';
						$("#GuardSucBox2").html(text);
						$.mask.show('GuardSucBox2');
					}else{
						var text='<div class="last-con"><p>'+data.message+'</p><input class="btn sure" onClick="$.mask.hide(\'LowStock\');" type="button" value="确认"><input onClick="$.mask.hide(\'LowStock\');" class="btn cancel" type="button" value="取消"></div>';
						$("#LowStock").html(text);
						$.mask.show('LowStock');
						return;
					}
				}
			});
		}
	}	
</script>
<script>
var HappyBirthdayShop={
		prop_id:0,//购买物品id	
		quantity:0,//当前库存	
		buyNum:0,//购买数量
		batchprice:0,//礼物单价
		isSucc:false,
		//购买礼物
		buyGift:function(obj,name,batchprice){
			
			this.batchprice=batchprice;
			if($.User.getSingleAttribute('uid',true)<=0){
				$.User.loginController('login');
				return;
			}
			
			var text='<h2 class="clearfix"><em onClick="$.mask.hide(\''+obj+'\');" class="fright">&Chi;\
			</em>'+name+'</h2><div class="buy-con clearfix"><p class="buynum clearfix">\
			<label>购买数量：</label><input type="text" id="quantity" value="1" onblur="HappyBirthdayShop.changeGiftPrice()"\
			 onkeyup="HappyBirthdayShop.changeGiftPrice()"></p><p class="buyprice">购买价格：<strong>'+batchprice+'皮蛋</strong>\
			 </p><input class="btn sure" type="button" onClick="HappyBirthdayShop.confirmBuyGift(\''+obj+'\')" value="确认">\
			 <input onClick="$.mask.hide(\''+obj+'\');" class="btn cancel" type="button" value="取消"></div>';
			$("#"+obj).html(text);
			$.mask.show(obj);
			
		},
		//改变礼物总价
		changeGiftPrice:function(){
			var quantity=$("#quantity").val();
			quantity=quantity.replace(/[^\d]/g,'');
			quantity=quantity>9999?9999:quantity;

			$("#quantity").val(quantity);
			var totalPrice=this.batchprice*10000*quantity/10000;
			$(".buyprice").find('strong').text(totalPrice+'皮蛋');
		},
		
		confirmBuyGift:function(obj){
			var buyNum=parseInt($("#quantity").val());
			var o=this;
			$.ajax({
				type:'POST',
				url:'index.php?r=/Activities/BuyBatchGift',
				data:{buyNum:buyNum},
				dataType:'json',
				async:false,
				success:function(data){
					$.mask.hide(obj);
					if(data.flag==1){
						$.User.refershWebLoginHeader();
						var text='<div class="last-con"><p class="suc">购买成功，礼物已存入背包!</p>\
							<input class="btn sure" onClick="$.mask.hide(\'GuardSucBox2\');" type="button" value="确认">\
							<input  class="btn cancel" onClick="$.mask.hide(\'GuardSucBox2\');" type="button" value="取消"> </div>';
						$("#GuardSucBox2").html(text);
						$.mask.show('GuardSucBox2');
					}else{
						var text='<div class="last-con"><p>'+data.message+'</p>\
						<input class="btn sure" onClick="$.mask.hide(\'LowStock\');" type="button" value="确认">\
						<input onClick="$.mask.hide(\'LowStock\');" class="btn cancel" type="button" value="取消"></div>';
						$("#LowStock").html(text);
						$.mask.show('LowStock');
						return;
					}
				}
			});
		}
	}	
</script>