<div class="w1000 mt20">
	<div class="boxshadow p15 clearfix">

		<div  id="GiftHoter"  class="main-2 fleft ovhide">
			<ul id="HoterHd" class="tab clearfix">
				<li><a class="curr" href="#3">本周礼物红人</a></li>
				<li><a href="#3">上周礼物之星</a></li>
			</ul>
			<div class="tabcon-bd">
			<ul class="gftboard clearfix">
<?php 
foreach($gift['week'] as $key=>$_gift):
$picImages = $this->userService->getUploadUrl().'gift/'.$_gift['picture'];
$archivesHref = 'index.php?r=archives/index/uid/'.$_gift['d_uid'];
?>				
				<li>
					<span class="gft-num">
					<img src="<?php echo  $picImages?>" width="42" height="42" alt="" />
					<?php echo $_gift['gift_name']?>×<?php echo $_gift['num']?>个
					</span>
					<span class="gft-zhubo">
					<a href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><?php echo $_gift['d_nickname']?></a> 
					<em class="lvlo lvlo-<?php echo $_gift['d_rank']?>"></em>
					</span>
				</li>
<?php endforeach;?> 			
			</ul>
			<ul class="gftboard disp clearfix" style="display: none;">
<?php 
foreach($gift['lastweek'] as $key=>$_gift):
$picImages = $this->userService->getUploadUrl().'gift/'.$_gift['picture'];
$archivesHref = 'index.php?r=archives/index/uid/'.$_gift['d_uid'];
?>				
				<li>
					<span class="gft-num">
					<img src="<?php echo  $picImages?>" width="42" height="42" alt="" />
					<?php echo $_gift['gift_name']?>×<?php echo $_gift['num']?>个
					</span>
					<span class="gft-zhubo">
					<a  href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><?php echo $_gift['d_nickname']?></a> 
					<em class="lvlo lvlo-<?php echo $_gift['d_rank']?>"></em>
					</span>
				</li>
<?php endforeach;?> 
			</ul>
			
			</div>
		</div>
	</div>
</div>