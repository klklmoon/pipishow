<ul class="anchor-con clearfix">
<?php
if(empty($this->pipiFrontPath))
{
	$assetManager = Yii::app()->getAssetManager();
	$assetManager->excludeFiles = array('.svn','.gitignore','images','admin');
	$pipiFrontPath = $assetManager->publish(Yii::getPathOfAlias('root.statics'));
	$this->pipiFrontPath = $pipiFrontPath;
}
$attentionType = isset($attentionType) ? $attentionType : 'common';
$happyBirthdayService = new HappyBirthdayService();
$sdate = date("Y-m-d");
$webConfigSer = new WebConfigService();
$keyInfo = $webConfigSer->getChannelSymbol();
$keyInfo = $keyInfo['c_value'];
$keyInfo['sing_general']['pic'] = Yii::app()->params->images_server['url']."/".$keyInfo['sing_general']['pic'];
$keyInfo['sing_area']['pic'] = Yii::app()->params->images_server['url']."/".$keyInfo['sing_area']['pic'];

if(isset($type) && empty($living) && empty($wait)):
	$archivesService = new ArchivesService();
	$todayRecommand = $archivesService->getAllTodayRecommand(isset(Yii::app()->user->id) ? Yii::app()->user->id : 0,true,$this->isLogin);
	$archivesService->addStarSingerForArchives($todayRecommand);
	if (isset($todayRecommand['living'])){
		$todayRecommandLiving=array();
		if(count($todayRecommand['living'])>=3)
		{
			$todayRecommandKeys=array_rand($todayRecommand['living'],3);
			foreach ($todayRecommandKeys as $key)
			{
				$todayRecommandLiving[]=$todayRecommand['living'][$key];
			}
		}
		else
			$todayRecommandLiving=$todayRecommand['living'];
	}

?>
	<li class="nocon">您还没<?php echo $type;?>的主播，以下为今日推荐主播</li>
<?php 
	if (isset($todayRecommandLiving)) :
		$userListService=new UserListService();
		foreach ($todayRecommandLiving as $_live) :
			$isAttention = isset($_live['is_attention']) ? $_live['is_attention'] : 0;
			$attentTionClass = $isAttention ? 'cancelatt' : '';
			$attentIionText = $isAttention ? '取消关注' : '关注';
			$jsMethod = $isAttention ? 'cacnelAttentionUser' : 'attentionUser';
			$archivesHref = '/' . $_live['uid'];
			$_live['UserList']=$userListService->getUserList($_live['archives_id']);
			?>				
						<li>
			<div class="anchor-head">
				<a href="<?php $this->getTargetHref($archivesHref,true,false)?>"
					title="<?php echo $_live['title']?>"
					target="<?php echo $this->target?>"> <img
					src="<?php echo Yii::app()->params->images_server['url'];?>/default/dotey/dotey_display_default_small.png"
					data-original="<?php echo $_live['display_small']?>">
				</a>
				<p class="playing">
				<?php if(isset($_live['UserList']['total']) && $_live['UserList']['total']>0):?>
					<span><?php echo $_live['UserList']['total'];?>人在观看</span>
				<?php endif;?>
					<em>直播中</em>
				</p>
				
				<?php if(isset($_live['star_singer']) && $_live['star_singer'] == true):?>
				<span class="juke-icon">
				<img src="<?php echo $keyInfo['sing_general']['pic']; ?>">
				 <em><?php echo empty($keyInfo['sing_general']['desc'])?"唱将":$keyInfo['sing_general']['desc']; ?></em></span>
				<?php elseif(isset($_live['sing_area']) && $_live['sing_area'] == true):?>
				<span class="juke-icon">
				<img src="<?php echo $keyInfo['sing_area']['pic']; ?>">
				 <em><?php echo empty($keyInfo['sing_area']['desc'])?"唱区主播":$keyInfo['sing_area']['desc']; ?></em></span>
				<?php endif;?>
				
				<?php if($_live['today_recommand']):?>
				 <em class="todayRec"></em>
				 <?php endif;?>
			</div>
			<p class="chorname clearfix">
							          <?php
			$birthdayDotey = $happyBirthdayService->getBirthdayDoteyInfoById($sdate, $_live['uid']);
			if (isset($birthdayDotey['birthday'])){
				$thisYearBirthDay=$birthdayDotey['year'] . "-" . $birthdayDotey['month'] . "-" . $birthdayDotey['sday'];
				$_live['show_cake'] = PipiDate::checkTimeRange($thisYearBirthDay,date("Y-m-d H:i:s"),false,3,false);
			}
			if (isset($_live['show_cake']) && $_live['show_cake']) :
				?>	
								<span class="fleft cakeIcon"> <img
					src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/cake-icon.jpg">
					<em><?php echo $birthdayDotey['month']."月".$birthdayDotey['sday']."日";?>&nbsp;&nbsp;生日快乐</em>
				</span>
	          <?php endif;?>    							
								 <a href="<?php $this->getTargetHref($archivesHref,true,false)?>"
					title="<?php echo $_live['title']?>" class="fleft nambtm pink"
					target="<?php echo $this->target?>"><?php echo $_live['title']?></a>
				<a
					onclick="$.User.<?php echo $jsMethod?>('<?php echo $_live['uid']?>',this,'<?php echo $attentionType?>');"
					class="attent <?php echo $attentTionClass?>"
					href="javascript:void(0)" title="<?php echo $attentIionText?>"> <span
					class="attent-text"><?php echo $attentIionText?></span>
				</a>
			</p>
			<p class="time"><?php echo $_live['sub_title']?></p>
		</li>
						
	<?php
		endforeach
		;
	endif;

 endif;?>

<?php 
if ($living) :
	$userListService=new UserListService();

	foreach ($living as $_live) :
		$isAttention = isset($_live['is_attention']) ? $_live['is_attention'] : 0;
		$attentTionClass = $isAttention ? 'cancelatt' : '';
		$attentIionText = $isAttention ? '取消关注' : '关注';
		$jsMethod = $isAttention ? 'cacnelAttentionUser' : 'attentionUser';
		$archivesHref = '/' . $_live['uid'];
		$_live['UserList']=$userListService->getUserList($_live['archives_id']);
		?>				
					<li>
		<div class="anchor-head">
			<a href="<?php $this->getTargetHref($archivesHref,true,false)?>"
				title="<?php echo $_live['title']?>"
				target="<?php echo $this->target?>"> <img
				src="<?php echo Yii::app()->params->images_server['url'];?>/default/dotey/dotey_display_default_small.png"
				data-original="<?php echo $_live['display_small']?>">
			</a>
			<p class="playing">
				<?php if(isset($_live['UserList']['total']) && $_live['UserList']['total']>0):?>
				<span><?php echo $_live['UserList']['total'];?>人在观看</span>
				<?php endif;?>
				<em>直播中</em>
			</p>
			
				<?php if(isset($_live['star_singer']) && $_live['star_singer'] == true):?>
				<span class="juke-icon">
				<img src="<?php echo $keyInfo['sing_general']['pic']; ?>">
				 <em><?php echo empty($keyInfo['sing_general']['desc'])?"唱将":$keyInfo['sing_general']['desc']; ?></em></span>
				<?php elseif(isset($_live['sing_area']) && $_live['sing_area'] == true):?>
				<span class="juke-icon">
				<img src="<?php echo $keyInfo['sing_area']['pic']; ?>">
				 <em><?php echo empty($keyInfo['sing_area']['desc'])?"唱区主播":$keyInfo['sing_area']['desc']; ?></em></span>
				<?php endif;?>
			
			<?php if($_live['today_recommand']):?>
			 <em class="todayRec"></em>
			 <?php endif;?>
		</div>
		<p class="chorname clearfix">
						          <?php
		$birthdayDotey = $happyBirthdayService->getBirthdayDoteyInfoById($sdate, $_live['uid']);
		if (isset($birthdayDotey['birthday'])) {
			$thisYearBirthDay=$birthdayDotey['year'] . "-" . $birthdayDotey['month'] . "-" . $birthdayDotey['sday'];
			$_live['show_cake'] = PipiDate::checkTimeRange($thisYearBirthDay,date("Y-m-d H:i:s"),false,3,false);
		}
		if (isset($_live['show_cake']) && $_live['show_cake']) :
			?>	
							<span class="fleft cakeIcon"> <img
				src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/cake-icon.jpg">
				<em><?php echo $birthdayDotey['month']."月".$birthdayDotey['sday']."日";?>&nbsp;&nbsp;生日快乐</em>
			</span>
          <?php endif;?>    							
							 <a href="<?php $this->getTargetHref($archivesHref,true,false)?>"
				title="<?php echo $_live['title']?>" class="fleft nambtm pink"
				target="<?php echo $this->target?>"><?php echo $_live['title']?></a>
			<a
				onclick="$.User.<?php echo $jsMethod?>('<?php echo $_live['uid']?>',this,'<?php echo $attentionType?>');"
				class="attent <?php echo $attentTionClass?>"
				href="javascript:void(0)" title="<?php echo $attentIionText?>"> <span
				class="attent-text"><?php echo $attentIionText?></span>
			</a>
		</p>
		<p class="time"><?php echo $_live['sub_title']?></p>
	</li>
					
<?php
	endforeach
	;
endif;

if ($wait) :
	foreach ($wait as $_live) :
		$isAttention = isset($_live['is_attention']) ? $_live['is_attention'] : 0;
		$attentTionClass = $isAttention ? 'cancelatt' : '';
		$attentIionText = $isAttention ? '取消关注' : '关注';
		$jsMethod = $isAttention ? 'cacnelAttentionUser' : 'attentionUser';
		$archivesHref = '/' . $_live['uid'];
		?>					
					
					<li>
		<div class="anchor-head">
			<a href="<?php $this->getTargetHref($archivesHref,true,false)?>"
				title="<?php echo $_live['title'];?>"
				target="<?php echo $this->target?>"> <img
				src="<?php echo Yii::app()->params->images_server['url'];?>/default/dotey/dotey_display_default_small.png"
				data-original="<?php echo $_live['display_small'];?>">
			</a>
			<p class="readying">
				<em><?php echo $_live['start_desc'][0];?> 开播</em>
			</p>
			
				<?php if(isset($_live['star_singer']) && $_live['star_singer'] == true):?>
				<span class="juke-icon">
				<img src="<?php echo $keyInfo['sing_general']['pic']; ?>">
				 <em><?php echo empty($keyInfo['sing_general']['desc'])?"唱将":$keyInfo['sing_general']['desc']; ?></em></span>
				<?php elseif(isset($_live['sing_area']) && $_live['sing_area'] == true):?>
				<span class="juke-icon">
				<img src="<?php echo $keyInfo['sing_area']['pic']; ?>">
				 <em><?php echo empty($keyInfo['sing_area']['desc'])?"唱区主播":$keyInfo['sing_area']['desc']; ?></em></span>
				<?php endif;?>
			
		</div>
		<p class="chorname clearfix">
          <?php
		$birthdayDotey = $happyBirthdayService->getBirthdayDoteyInfoById($sdate, $_live['uid']);
		if (isset($birthdayDotey['birthday'])) {
			$thisYearBirthDay=$birthdayDotey['year'] . "-" . $birthdayDotey['month'] . "-" . $birthdayDotey['sday'];
			$_live['show_cake'] = PipiDate::checkTimeRange($thisYearBirthDay,date("Y-m-d H:i:s"),false,3,false);
		}
		if (isset($_live['show_cake']) && $_live['show_cake']) :
			?>	
							<span class="fleft cakeIcon"> <img
				src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/cake-icon.jpg">
				<em><?php echo $birthdayDotey['month']."月".$birthdayDotey['sday']."日";?>&nbsp;&nbsp;生日快乐</em>
			</span>
          <?php endif;?>              						
							<a href="<?php $this->getTargetHref($archivesHref,true,false)?>"
				title="<?php echo $_live['title'];?>" class="fleft nambtm pink"
				target="<?php echo $this->target?>"><?php echo $_live['title'];?></a>
			<a
				onclick="$.User.<?php echo $jsMethod?>('<?php echo $_live['uid']?>',this,'<?php echo $attentionType?>');"
				class="attent <?php echo $attentTionClass?>"
				href="javascript:void(0)" title="<?php echo $attentIionText?>"> <span
				class="attent-text"><?php echo $attentIionText?></span>
			</a>
		</p>
		<p class="time"><?php echo $_live['sub_title'];?></p>
	</li>
<?php
	endforeach
	;

endif;
?>
					
</ul>