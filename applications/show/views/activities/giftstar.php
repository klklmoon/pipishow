<div class="kong"></div>
<div class="w975 gift ovhide mt20">
<div class="gift-top clearfix">
<h1>本周指定礼物</h1>
<ul>
<?php foreach($this->viewer['giftList'] as $gift): ?>
<li><img src="<?php echo $gift['url'] ?>" width="60" height="60" /><span><?php echo $gift['zh_name'] ?></span></li>
<?php endforeach;?>
</ul>
</div><!-- .gift-top -->

<div class="gift-middle clearfix">
	<div class="m-lft fleft">
		<h1>礼物之星显示规则： </h1>
		<span>1、获得每周礼物之星的主播将会在下周一到周日在直播窗口左上角展示礼物之星勋章及所获得第一名礼物；每周一至周日如果主播当周所获指定礼物在前5名的话， 则也会在直播窗口左上角显示该项礼物。</span><br/>
		<span>2、对于指定礼物，如果主播是目前排第一的话，则显示真实色彩；如果仅排第2到5位，则显示为半透明礼物。所有礼物的右下角都会显示相应排名的数字。</span>
		<br /><br /><br/>
		<h1>礼物之星奖励： </h1>
		<span>1、礼物之星勋章一个（悬挂在直播窗口左上角），显示时间为一周（周一00:01——周日23:30）</span><br/>
		<span>2、10000魅力值+10000魅力点（下周一上午由系统自动发放）</span>
		<br /><br /><br/>

		<h2>特别说明：</h2>
		<?php echo $this->viewer['thisIllustration'];?>
		</div><!-- .m-left -->

		<div class="m-right fleft">
		<h2>温馨提示：</h2>
      	1、主播需以每周一00:01的等级为标准来争夺相应榜单（周一以后升级的只能下周再换争夺榜单）<br/>
      	2、主播可以将自己想要夺取的单项礼物写在房间公告处，让粉丝帮你夺取！<br/>
      	3、每周指定礼物可能会有所不同，请主播每周一都查看一下活动页面哦<br/>
		<span>PS：要多关注活动页的礼物排行榜哦</span>
		</div><!-- .m-right -->
		</div><!-- .gift-middle -->
		 
		</div><!-- gift -->
		<div class="w950 star ovhide mt20">
		<h1>上周礼物之星</h1>
		<img style="display:block;" src="<?php echo $this->pipiFrontPath?>/fontimg/activities/giftstar/line.jpg" />

  <ul class="fleft star-left">
	  <?php 
	  $i=1;
	  foreach($this->viewer['lastGiftStarList'] as $lastGiftStar):
	  	if(($i%2)==1):
	   ?>
		  <li><img src="<?php echo $lastGiftStar['gift_url'] ?>" width="60" height="60" />
		  <span><?php echo $lastGiftStar['gift_name']."x".$lastGiftStar['gift_num'] ?></span>
		  <code><?php echo mb_substr((empty($lastGiftStar['dotey_nickname'])?"求昵称":$lastGiftStar['dotey_nickname']),0,8,'UTF-8') ?><em class="lvlo lvlo-<?php echo $lastGiftStar['dotey_rank'] ?>">
		  </em></code></li>
		<?php else: ?> 
		 <li style="margin-left:110px;"><img src="<?php echo $lastGiftStar['gift_url'] ?>" width="60" height="60" />
		  <span><?php echo $lastGiftStar['gift_name']."x".$lastGiftStar['gift_num'] ?></span>
		  <code><?php echo mb_substr((empty($lastGiftStar['dotey_nickname'])?"求昵称":$lastGiftStar['dotey_nickname']),0,8,'UTF-8') ?><em class="lvlo lvlo-<?php echo $lastGiftStar['dotey_rank'] ?>">
		  </em></code></li>
	  <?php
	  endif;
	  $i++; 
	  endforeach;
	  ?>
    		</ul><!-- star-left -->

    		</div><!-- star -->
    		<div class="w950 bang ovhide mt20">
    		<h1>本周单项礼物排行榜</h1>
    		<img style="display:block;" src="<?php echo $this->pipiFrontPath?>/fontimg/activities/giftstar/line.jpg" />
<?php 
$userService=new UserService();
$giftService=new GiftService();
$i=1;
foreach ($this->viewer['thisWeekRankList'] as $giftId=>$rankList):
?>
<?php if(($i%3)==0):?>    		
  <div class="fleft paihb" style="margin-right:0px;">
<?php else:?>    		
    		<div class="fleft paihb">
<?php endif;?>
    <div class="ph-top">
    <?php $giftInfo=$giftService->getGiftByIds($giftId);
    	echo $giftInfo[$giftId]['zh_name'];
    ?></div>
    	<div class="bcon">
    	<table>
    	<tr style="color: #e05738;">
    	<td width="20"></td>
    	<td width="135">主播昵称</td>
          <td width="50">等级</td>
          <td width="95">收到礼物个数</td>
          	</tr>
          	<?php 
	          	$counts=0;
	          	foreach ($rankList['data'] as $rankRow):
	          		$doteyInfo=$userService->getUserFrontsAttributeByCondition($rankRow['uid'],true,true);
	          		$counts++;
          	?>
          	<tr>
          	<td width="20"><a class="col1"><?php echo $rankRow['rank']?></a></td>
          	<td width="135"><?php echo mb_substr(empty($doteyInfo['nk'])?"求昵称":$doteyInfo['nk'],0,8,'UTF-8');?></td>
          	<td width="50"><em class="lvlo lvlo-<?php echo $doteyInfo['dk']?>"></em></td>
          	<td width="95"><?php echo $rankRow['gift_num']?></td>
          	</tr>
			<?php 
					if($counts>=5) break;
				endforeach;
			?>
			</table>
			</div><!-- bcon -->
			</div><!-- paihb -->
<?php 
$i++;
endforeach; ?>
</div><!-- bang -->
<div class="w1000"></div>
