<div class="w1000 mt30 mb20 topbox clearfix">
	<div class="pointbox point1">
        <a href="javascript:void(0);" title="关闭" class="close"></a>
        <a href="javascript:void(0);" title="去感受一下" class="pointBtn"></a>
    </div><!--.point1-->
    <!--<div class="pointbox point2">
        <a href="javascript:void(0);" title="关闭" class="close"></a>
        <a href="javascript:void(0);" title="进入她的直播间" class="pointBtn"></a>
    </div>-->
	<div class="w1000 topbox-con">
		<div class="fleft topbox-l">
			<h1>互动综艺第一平台</h1>
			<ul class="area-banner">
				<li>
					<a class="jukebtn" href="<?php $this->getTargetHref('index.php?r=index/category&type=songs')?>" target="<?php echo $this->target?>">点唱专区</a>
				</li>
			</ul>
			<dl class="area-con">
				<dt>等级</dt>
				<dd>
					<a href="<?php $this->getTargetHref('index.php?r=index/category&type=rank&id=1')?>" target="<?php echo $this->target?>">皇冠</a>
				</dd>
				<dd>
					<a href="<?php $this->getTargetHref('index.php?r=index/category&type=rank&id=2')?>" target="<?php echo $this->target?>">钻石</a>
				</dd>
				<dd>
					<a href="<?php $this->getTargetHref('index.php?r=index/category&type=rank&id=3')?>" target="<?php echo $this->target?>">红心</a>
				</dd>
			</dl>
			<dl class="area-con">
				<dt>主播印象</dt>
				<dd>
					<a href="<?php $this->getTargetHref('index.php?r=index/category&type=tag&id=1')?>" target="<?php echo $this->target?>">美女</a>
				</dd>
				<dd>
					<a href="<?php $this->getTargetHref('index.php?r=index/category&type=tag&id=2')?>" target="<?php echo $this->target?>">小清新</a>
				</dd>
				<dd>
					<a href="<?php $this->getTargetHref('index.php?r=index/category&type=tag&id=3')?>" target="<?php echo $this->target?>">女神</a>
				</dd>
			</dl>
			<dl class="area-con">
				<dt>活动</dt>
				<dd>
					<a href="<?php $this->getTargetHref('index.php?r=activities/firstchargegifts')?>" target="<?php echo $this->target?>">首充送礼</a>
				</dd>
				<dd>
					<a href="<?php $this->getTargetHref('index.php?r=activities/happySaturday')?>" target="<?php echo $this->target?>">快乐周六</a>
				</dd>
				<dd>
					<a href="<?php $this->getTargetHref('index.php?r=activities/giftstar')?>" target="<?php echo $this->target?>">礼物之星</a>
				</dd>
				<dd>
					<a href="<?php $this->getTargetHref('index.php?r=activities/guardangel')?>" target="<?php echo $this->target?>">守护天使</a>
				</dd>
				<dd>
					<a href="<?php $this->getTargetHref('index.php?r=activities/happybirthday')?>" target="<?php echo $this->target?>">生日快乐</a>
				</dd>				
			</dl>

			
            <?php if($this->isPipiDomain) :?>
            <dl class="area-con">
            	<!-- 
				<dt>游戏</dt>
				<dd>
					<a href="<?php $this->getTargetHref( Yii::app()->params['letian_game']['host'] . '/gp/html/LuckSofa.html')?>" title="幸运沙发" target="<?php echo $this->target?>">幸运沙发</a>
				</dd>
				<dd>
					<a href="<?php $this->getTargetHref( Yii::app()->params['letian_game']['host'] . '/gp/html/BreakEggs.html')?>" title="砸金蛋" target="<?php echo $this->target?>">砸金蛋</a>
				</dd>
				<dd>
					<a href="<?php $this->getTargetHref(Yii::app()->params['letian_game']['host'] . '/gp/html/TreasureBox.html');?>" title="开心宝箱" target="<?php echo $this->target?>">开心宝箱</a>
				</dd>
				<dd>
					<a href="<?php $this->getTargetHref('index.php?r=index/gameRedirect&game=jiangshenglu&uid='.$this->getUserJsonAttribute('uid',false,true));?>" title="将神怒" target="<?php echo $this->target?>">将神怒</a>
				</dd>
				<!-- 
				<dd>
					<a href="index.php?r=index/gameRedirect&game=zuixiyou&uid=<?php $this->getUserJsonAttribute('uid',false,true) ?>" title="醉西游记" target="<?php echo $this->target?>" title="醉西游记" target="<?php echo $this->target?>">醉西游记</a>
				</dd>
				<dd>
					<a href="index.php?r=index/gameRedirect&game=shengxiandao&uid=<?php $this->getUserJsonAttribute('uid',false,true) ?>" title="醉西游记" target="<?php echo $this->target?>" title="神仙道" target="<?php echo $this->target?>">神仙道</a>
				</dd>
				<dd>
					<a href="index.php?r=index/gameRedirect&game=xiaobaoshengzhi&uid=<?php $this->getUserJsonAttribute('uid',false,true) ?>" title="小宝升职" target="<?php echo $this->target?>">小宝升职</a>
				</dd>
				<dd>
					<a href="index.php?r=index/gameRedirect&game=longjiang&uid=<?php $this->getUserJsonAttribute('uid',false,true) ?>" title="龙将" target="<?php echo $this->target?>">龙将</a>
				</dd>
				-->
			</dl>
            <?php endif;?>
             
		</div>
		<div class="fleft topbox-m">
			<div id="Mflash" class="mflash clearfix">
				<div class="fright mflash-hd">
					<ul>
                		<?php
						$i = 0;
						foreach ($siteStars as $siteStar) :
							if ($i >= 3) break;
						?>
                    	<li><img src="<?php echo $siteStar['small_avatar']?>"></li>
                        <?php
							$i++;
						endforeach;
						?>
                    </ul>
					<span id="starts_next" class="end next"><img
						src="<?php echo $this->pipiFrontPath?>/fontimg/common/changebtn.png"></span>
				</div>
				<div class="fleft mflash-bd">
					<div class="mflash-box">
						<ul>
	                    	<?php
								$i = 0;
								foreach ($siteStars as $s) :
									if ($i >= 3) break;
									$sliveText = $s['status'] == 1 ? '直播中' : '待直播';
									$sliveClass = $s['status'] == 1 ? 'playing' : 'readying';
									$archivesHref = '/' . $s['uid'];
							?>
                    		<li><a
								href="<?php echo $this->getTargetHref($archivesHref,true,false)?>"
								title="<?php echo $s['nickname']?>"
								target="<?php echo $this->target?>"> <img
									src="<?php echo $s['display_big']?>"></a>
								<p class="<?php echo $sliveClass?>">
									<em><?php echo $sliveText?></em>
								</p></li>
                    	  	<?php
									$i++;
								endforeach;
							?>
                    	</ul>
					</div>
				</div>
			</div>
			<!--.mflash-->
			<div class="mbox-con">
            	<?php
					if ($topSendGift) :
				?>
	            <ul>
	            	<?php
						foreach ($topSendGift as $t) :
							$archiveHref = '/' . $t['d_uid'];
							$archiveGiftInfo = $t['gift_num'] . '个' . $t['gift_name'] . '&nbsp;<img width="20px" height="20px" src="' . $this->userService->getUploadUrl() . 'gift/' . $t['picture'] . '" />';
					?>
	            	 <li><span><?php echo $t['time']?> </span> <a
						href="<?php $this->getTargetHref($archiveHref,true,false)?>"
						title="<?php echo $t['nickname']?>"
						class="givename pink" target="<?php echo $this->target?>"><?php echo $t['nickname']?></a>送给
						<a href="<?php $this->getTargetHref($archiveHref,true,false)?>"
						class="givename pink" title="<?php $t['d_nickname']?>"
						target="<?php echo $this->target?>"><?php echo $t['d_nickname']?></a>
						<span><?php echo $archiveGiftInfo ?></span></li>
	              	 <?php endforeach;?>
	            </ul>
                <?php endif; ?>
            </div>

		</div>
		<!--.topbox-m-->
		<div class="fleft topbox-r">
			<div id="Rflash" class="rflash">
				<div class="rflash-bd">
					<ul>
                	<?php
						foreach ($showcase as $s) :
					?>
                    	<li><a
							href="<?php $this->getTargetHref($s['textlink'])?>"
							title="<?php echo $s['subject']?>"
							target="<?php echo $this->target?>"><img
							src="<?php echo $s['piclink']?>"></a></li>
                    <?php
						endforeach;									
					?>
                    </ul>
				</div>
				<div class="rflash-hd clearfix">
					<span class="fleft prev"></span>
					<ul class="fleft">
                		<?php
							foreach ($showcase as $key => $s) :
						?>
	                    	<li><?php ++$key?></li>
                        <?php
							endforeach;										
						?>
                    </ul>
					<span class="fright next"></span>
				</div>
			</div>
			<!--.rflash-->
			<div class="notice">
				<h2 class="clearfix">
					<span class="fleft">公告</span><a class="fright more" href="#"
						title=""></a>
				</h2>
				<ul class="notice-list">
                <?php
					foreach ($notice as $n) :											
				?>  
                	<li><a
						href="<?php $this->getTargetHref($n['textlink'])?>"
						title="<?php echo $n['subject']?>"
						target="<?php echo $this->target?>"><?php echo $n['subject']?></a></li>
	            <?php
					endforeach;
				?>
                </ul>
			</div>
			<!--.notice-->
		</div>
		<!--.topbox-r-->
	</div>
</div>
<!--.topbox-->
<div class="w1000 clearfix">
	<div class="fleft content-l">
		<!--正在直播start-->
		<div class="anchor">
			<h3 class="seting-h clearfix">
				<p class="fleft seting-menu clearfix">
					<a class="onlive" href="javascript:void(0);" title="正在直播">
						<i class="banericon"></i>
						<span class="fleft pink">正在直播</span>
					</a>
					<a class="attented setover" href="javascript:void(0);" title="我关注的">我关注的</a>
					<a class="managed" href="javascript:void(0);" title="管理的">管理的</a>
					<a class="looked" href="javascript:void(0);" title="看过的">看过的</a>
				</p>

				<a id="allmanaged" 
					href="<?php $this->getTargetHref($this->createUrl('account/manage'),true,false);?>"
					class="fright more" title="全部管理"
					target="<?php echo $this->target?>">全部管理</a>
				<a id="allattented" 
					href="<?php $this->getTargetHref($this->createUrl('account/follow'),true,false);?>"
					class="fright more" title="全部关注"
					target="<?php echo $this->target;?>">全部关注</a>
			</h3>
			<div class="anchor-box">
			<?php
				$this->renderPartial('application.views.index2.liveArchivesTemplate2',$living);
			?>
			</div>
			<!--.anchor-box-->
		</div>
		<p class="anchor-btm"></p>
		<!--正在直播end-->

		<div class="anchor">
			<h3 class="clearfix">
				<i class="banericon"></i><span class="fleft pink">即将开播</span>
				<p class="fleft"></p>
				<a href="<?php $this->getTargetHref('index.php?r=channel/category&status=2#doteyData')?>"
					class="fright more" title="更多" target="<?php echo $this->target?>">更多</a>
			</h3>
            <?php
				$this->renderPartial('application.views.index2.liveArchivesTemplate2', $willLive);
			?>
        </div>
		<p class="anchor-btm"></p>
	</div>
	<!--.content-l-->

	<div class="fright content-r">
		<?php 
// 			if(count($todayBirthdayArchives)>0 || count($willBirthdayArchives)>0):
		?>
        <!--生日专栏start-->
		<div class="rightcon relative">
			<h4 class="clearfix">
				<i class="banericon"></i> <span class="fleft pink">生日专栏</span>
				<p class="tip-text relative">
					<em>?</em> <span class="tipcon">即将过生日的主播</span>
				</p>
			</h4>
			<div class="conbox">
				<?php
					foreach ($todayBirthdayArchives as $doteyRow):
				?>
				<dl class="conbox-list birthList">
					<dt>
						<a href="<?php echo $this->getTargetHref("/".$doteyRow['uid']);?>"
							title="<?php echo $doteyRow['doteyInfo']['nk'];?>"
							target="<?php echo $this->target?>"> <img 
							src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png"
							data-original="<?php echo $doteyRow['display_small'];?>"
							>
						</a> <a class="attent <?php echo $doteyRow['is_attention'] ? 'cancelatt' : '';?>"
							href="javascript:void(0);" title="关注"
							onclick="$.User.<?php echo $doteyRow['is_attention'] ? 'cacnelAttentionUser' : 'attentionUser';?>('<?php echo $doteyRow['uid'];?>',this,'single');"></a>
					</dt>
					<dd>
						<a href="<?php echo $this->getTargetHref("/".$doteyRow['uid']);?>"
							title="<?php echo $doteyRow['doteyInfo']['nk'];?>"
							target="<?php echo $this->target?>"><?php echo $doteyRow['doteyInfo']['nk'];?></a>
						<p class="ellipsis"><?php echo $doteyRow['title'];?></p>
						<p class="ellipsis clearfix">
							<img
								src="<?php echo $this->pipiFrontPath?>/fontimg/index/clock.png">
							<span class="startTime"><?php echo $doteyRow['start_desc'][0];?> <em>开播</em></span>
							<span class="todayBirth">[<em class="pink">今日生日</em>]
							</span>
						</p>
					</dd>
					<a href="<?php echo $this->getTargetHref("/".$doteyRow['uid']);?>"
						class="blessBtn" target="<?php echo $this->target?>">去送祝福</a>
				</dl>
				<?php
					endforeach;
					
					if(count($willBirthdayArchives)>0):
				?>
				<ul class="conbox-btm birth-btm">
					<?php 
						foreach ($willBirthdayArchives as $doteyRow):
					?>
					<li><a
						href="<?php echo $this->getTargetHref("/".$doteyRow['uid']);?>"
						title="<?php echo $doteyRow['doteyInfo']['nk'];?>"
						target="<?php echo $this->target?>"> 
						<span>
						<img 
							src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png"
							data-original="<?php echo $doteyRow['display_small'];?>">
						</span>
						 <span
							class="btmtext"><?php echo $doteyRow['doteyInfo']['nk'];?></span> <span
							class="btmtext btmdate"><?php echo $doteyRow['sbirthday'];?></span>
					</a> <a class="attent <?php echo $doteyRow['is_attention'] ? 'cancelatt' : '';?>"
						href="javascript:void(0);" title="关注"
						onclick="$.User.<?php echo $doteyRow['is_attention'] ? 'cacnelAttentionUser' : 'attentionUser';?>('<?php echo $doteyRow['uid'];?>',this,'single');"></a></li>
					<?php
						endforeach;
					?>
				</ul>
				<?php endif;?>
			</div>
			<i class="starttip birthtip"></i>
		</div>
		<p class="rightcon-btm"></p>
		<!--生日专栏end-->	
		<?php //endif;?>
	
   		<?php if (!empty($recommand)) : ?>
    	<div class="rightcon">
			<h4>
				<i class="banericon"></i><span class="fleft pink">专栏</span>
			</h4>
        	<?php
				$i = 0;
				foreach ($recommand as $activity) :
					$activityClass = ($i == 0 ? 'activead1' : 'activead2');
			?>
           	<a class="<?php echo $activityClass?>"
				href="<?php $this->getTargetHref($activity['textlink'])?>"
				title="<?php echo $activity['subject']?>"
				target="<?php echo $this->target?>"><img
				src="<?php echo Yii::app()->params->images_server['url'];?>/default/dotey/dotey_display_default_small.png"
				data-original="<?php echo $activity['piclink']?>"></a>
            <?php
					$i++;
				endforeach;
			?>
        </div>
		<p class="rightcon-btm"></p>		
		<?php  endif; ?>
		
		<?php if ($finalStarDotey) : ?>
		<div class="rightcon relative">
			<h4>
				<i class="banericon"></i><span class="fleft pink">明星主播</span>
				<p class="tip-text relative">
					<em>?</em>
					<span class="tipcon"><?php echo $finalStarDoteyDesc;?></span>
				</p>
			</h4>
			<div class="conbox">
            	<?php
				$stars = $finalStarDotey;
				if ($stars) :
					$topStar = array_shift($stars);
				?>
            	<dl class="conbox-list">
					<dt>
						<a href="<?php $this->getTargetHref('/' . $topStar['uid'],true,false)?>"
						title="<?php echo $topStar['nickname']?>"
						target="<?php echo $this->target?>">
							<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png"
								data-original="<?php echo $topStar['small_avatar']?>">
						</a>
						<a class="attent  <?php echo $topStar['is_attention'] ? 'cancelatt' : '';?>"
						href="javascript:void(0)" title="关注"
						onclick="$.User.<?php echo $topStar['is_attention'] ? 'cacnelAttentionUser' : 'attentionUser';?>('<?php echo $topStar['uid']?>',this,'single');"></a>
					</dt>
					<dd>
						<a href="<?php $this->getTargetHref('/' . $topStar['uid'],true,false)?>"
							title="<?php echo $topStar['nickname']?>"
							target="<?php echo $this->target?>"><?php echo $topStar['nickname']?></a>
						<p><?php echo $topStar['subject']?></p>
					</dd>
				</dl>
            	<?php endif;?>
                <ul class="conbox-btm">
             	<?php
				if ($stars) :
					foreach ($stars as $star) :
				?>
                	<li>
                		<a href="<?php $this->getTargetHref('/' . $topStar['uid'],true,false)?>"
							title="<?php echo $star['nickname']?>"
							target="<?php echo $this->target?>">
							<span>
							<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png"
								data-original="<?php echo $star['small_avatar']?>">
							</span>
							<span class="btmtext"><?php echo $star['nickname']?></span>
						</a>
						<a class="attent  <?php echo $topStar['is_attention'] ? 'cancelatt' : '';?>"
							href="javascript:void(0)" title="关注"
							onclick="$.User.<?php echo $topStar['is_attention'] ? 'cacnelAttentionUser' : 'attentionUser';?>('<?php echo $star['uid']?>',this,'single');"></a>
					</li>
    			<?php
					endforeach;	
            	endif;
				?>
                </ul>
			</div>
			<i class="starttip"></i>
		</div>
		<p class="rightcon-btm"></p>
		<?php endif; ?>
		
		<?php if ($finalRookieDotey) : ?>
        <div class="rightcon relative">
			<h4>
				<i class="banericon"></i><span class="fleft pink">新秀主播</span>
				<!--<a href="#" class="fright mr10 more" title="更多">更多</a>-->
				<p class="tip-text relative">
					<em>?</em>
					<span class="tipcon"><?php echo finalRookieDoteyDesc;?></span>
				</p>
			</h4>
			<div class="conbox">
         	<?php
			foreach ($finalRookieDotey as $new) :
			?>   
            	<dl class="conbox-list">
					<dt>
						<a href="<?php $this->getTargetHref('/' . $topStar['uid'],true,false)?>"
							title="<?php echo $new['nickname']?>"
							target="<?php echo $this->target?>">
							<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png"
								data-original="<?php echo $new['small_avatar']?>">
						</a>
						<a class="attent  <?php echo $topStar['is_attention'] ? 'cancelatt' : '';?>"
							href="javascript:void(0)" title="关注"
							onclick="$.User.<?php echo $topStar['is_attention'] ? 'cacnelAttentionUser' : 'attentionUser';?>('<?php echo $new['uid']?>',this,'single');"></a>
					</dt>
					<dd>
						<a href="<?php $this->getTargetHref('/' . $topStar['uid'],true,false)?>"
							title="<?php echo $new['nickname']?>"
							target="<?php echo $this->target?>"><?php echo $new['nickname']?></a>
						<p><?php echo $new['subject']?></p>
					</dd>
				</dl>
            <?php
			endforeach;
			?>  
            </div>
			<i class="starttip newstip"></i>
		</div>
		<p class="rightcon-btm"></p>
        <?php endif; ?>
        
		<?php if ($newJoinDotey) : ?>
         <div class="rightcon relative">
			<h4>
				<i class="banericon"></i><span class="fleft pink">最新加入</span>
				<!--<a href="#" class="fright mr10 more" title="更多">更多</a>-->
				<p class="tip-text relative">
					<em>?</em>
					<span class="tipcon"><?php echo newJoinDoteyDesc;?></span>
				</p>
			</h4>
			<div class="conbox">
         	<?php
			foreach ($this->viewer['newJoinDotey'] as $join) :
			?>   
            	<dl class="conbox-list">
					<dt>
						<a href="<?php $this->getTargetHref($archivesHref,true,false)?>"
							title="<?php echo $join['nickname']?>"
							target="<?php echo $this->target?>">
							<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png"
								data-original="<?php echo $join['small_avatar']?>">
						</a>
						<a class="attent  <?php echo $topStar['is_attention'] ? 'cancelatt' : '';?>"
							href="javascript:void(0)" title="关注"
							onclick="$.User.<?php echo $topStar['is_attention'] ? 'cacnelAttentionUser' : 'attentionUser';?>('<?php echo $join['uid']?>',this,'single');"></a>
					</dt>
					<dd>
						<a href="<?php $this->getTargetHref($archivesHref,true,false)?>"
							title="<?php echo $join['nickname']?>"
							target="<?php echo $this->target?>"><?php echo $join['nickname']?></a>
						<p><?php echo $join['subject']?></p>
					</dd>
				</dl>
			<?php
			endforeach;
			?>  
            </div>
			<i class="starttip intip"></i>
		</div>
		<p class="rightcon-btm"></p>
        <?php endif; ?>
       
        <div class="rightcon relative">
			<h4>
				<i class="banericon"></i> <span class="fleft pink">明星榜</span>
				<p class="tip-text relative">
					<em>?</em> <span class="tipcon"> 根据主播获得的魅力值排序 </span>
				</p>
				<p class="fright mr5 start-tab clearfix" id="charmrank">
					<a class="starttabover" href="javascript:void(0);" title="今日">今日</a><em>&#124;</em>
					<a href="javascript:void(0);" title="本周">本周</a><em>&#124;</em> <a
						href="javascript:void(0);" title="本月">本月</a><em>&#124;</em> <a
						href="javascript:void(0);" title="超级">超级</a>
				</p>
			</h4>
			<div class="conbox">
				<div class="datecon" id="charmrank_append">
            		<?php $this->renderPartial('application.views.user.charmrank',array('rank'=>$charmRank));?>
                </div>
			</div>
		</div>
		<p class="rightcon-btm"></p>
		<div class="rightcon relative">
			<h4>
				<i class="banericon"></i> <span class="fleft pink">富豪榜</span>
				<p class="tip-text relative">
					<em>?</em> <span class="tipcon">根据玩家贡献值排序</span>
				</p>
				<p class="fright mr5 start-tab clearfix" id="richrank">
					<a class="starttabover" href="javascript:void(0);" title="今日">今日</a><em>&#124;</em>
					<a href="javascript:void(0);" title="本周">本周</a><em>&#124;</em> <a
						href="javascript:void(0);" title="本月">本月</a><em>&#124;</em> <a
						href="javascript:void(0);" title="超级">超级</a>
				</p>
			</h4>
			<div class="conbox">
				<div class="today" id="richrank_append">
                    <?php $this->renderPartial('application.views.user.richrank',array('rank'=>$richRank));?>
                </div>
			</div>
		</div>
		<p class="rightcon-btm"></p>
		<div class="rightcon relative">
			<h4>
				<i class="banericon"></i> <span class="fleft pink">情谊榜</span>
				<p class="tip-text relative">
					<em>?</em> <span class="tipcon">根据玩家间互赠礼物的贡献值排序</span>
				</p>
				<p class="fright mr5 start-tab clearfix" id="friendlyrank">
					<a class="starttabover" href="javascript:void(0);" title="今日">今日</a><em>&#124;</em>
					<a href="javascript:void(0);" title="本周">本周</a><em>&#124;</em> <a
						href="javascript:void(0);" title="本月">本月</a><em>&#124;</em> <a
						href="javascript:void(0);" title="超级">超级</a>
				</p>
			</h4>
			<div class="conbox">
				<div class="today" id="friendlyrank_append">
                    <?php $this->renderPartial('application.views.user.friendlyrank',array('rank'=>$friendlyRank));?>
                </div>
			</div>
		</div>
		<p class="rightcon-btm"></p>
	</div>
</div>

<div id="GoTop" class="gotop-box">
	<a href="#">返<br>回<br>顶<br>部</a>
</div>
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
	$(this).find('.attent').show();              
}, mouseleave: function () {
	$(this).find('.anchor-head').removeClass('chorover');
	$(this).find('.attent').hide();    
}
});

/*左侧主播管理模块选卡*/
$(".anchor").slide({
	trigger:"click",
	titOnClassName:"setover",
	titCell:".seting-menu a",
	mainCell:".anchor-box"
});


//正在直播
$(".onlive").bind('click',function(){
	$.ajax({
		type:"GET",
		url:"index.php?r=index/onliveArchives",
		data:{target:hrefTarget},
		dataType:"html",
		success:function(live_html){
			$('.anchor-box').html(live_html);
			$(".anchor-box img").each(function(){
				if($(this).attr('data-original')){
					$(this).attr('src', $(this).attr('data-original'));
				}
			});
		}
	});
});


$(".managed").bind('click',function(){
	if($.User.getSingleAttribute('uid',true)<=0){
		$.User.loginController('login');
		return;
	}	
	$.ajax({
		type:"GET",
		url:"index.php?r=index/managerArchives",
		data:{target:hrefTarget},
		dataType:"html",
		success:function(live_html){
			$('.anchor-box').html(live_html);
			$(".anchor-box img").each(function(){
				if($(this).attr('data-original')){
					$(this).attr('src', $(this).attr('data-original'));
				}
			});
		}
	});
});

$(".attented").bind('click',function(){
	if($.User.getSingleAttribute('uid',true)<=0){
		$.User.loginController('login');
		return;
	}
	$.ajax({
		type:"GET",
		url:"index.php?r=index/attentionArchives",
		data:{target:hrefTarget},
		dataType:"html",
		success:function(live_html){
			$('.anchor-box').html(live_html);
			$(".anchor-box img").each(function(){
				if($(this).attr('data-original')){
					$(this).attr('src', $(this).attr('data-original'));
				}
			});
		}
	});
});

$(".looked").bind('click',function(){
	if($.User.getSingleAttribute('uid',true)<=0){
		$.User.loginController('login');
		return;
	}	
	$.ajax({
		type:"GET",
		url:"index.php?r=index/latestSeeArchives",
		data:{target:hrefTarget},
		dataType:"html",
		success:function(live_html){
			$('.anchor-box').html(live_html);
			$(".anchor-box img").each(function(){
				if($(this).attr('data-original')){
					$(this).attr('src', $(this).attr('data-original'));
				}
			});
		}
	});
});

if($.User.getSingleAttribute('uid',true) > 0){
	$('#login_user_manager').css('display','');
	$('#login_user_manager_sep').css('display','');
	//$(".attented").click();
	$("#allmanaged").show();
	$("#allattented").show();
}else{
	$('#login_user_manager').css('display','none');
	$('#login_user_manager_sep').css('display','none');
	$("#allmanaged").hide();
	$("#allattented").hide();
}

$("#Mflash").slide({mainCell:".mflash-bd ul",titCell:'.mflash-hd ul li',autoPlay:true,delayTime:0,triggerTime:0 });
$("#Rflash").slide({titCell:".rflash-hd ul",mainCell:".rflash-bd ul",autoPage:true,delayTime:0,autoPlay:true});

$("#starts_next").unbind( "click" );
$("#starts_next").bind("click",function(){
	$.ajax({
		type:"GET",
		url:"index.php?r=index/UpdateSiteStars",
		data:{target:hrefTarget},
		dataType:"html",
		success:function(starts_html){
			$('#Mflash').html(starts_html);
		}
	});
});

$('#richrank a').bind('click',function(){
	$(this).addClass('starttabover');
		var _this = this;
		var _i = 0;
		$('#richrank a').each(function (i,index){
			if(_this != this){
				$(this).removeClass('starttabover');
			}else{
				_i = i;
			}
		});
		var type = 'today';
		if(_i == 0){
			type = 'today';
		}else if(_i == 1){
			type = 'week';
		}else if(_i == 2){
			type = 'month';
		}else if(_i == 3){
			type = 'super';
		};
		$.User.userRichRank(type,'#richrank_append');
});

$('#charmrank a').bind('click',function(){
	$(this).addClass('starttabover');
		var _this = this;
		var _i = 0;
		$('#charmrank a').each(function (i,index){
			if(_this != this){
				$(this).removeClass('starttabover');
			}else{
				_i = i;
			}
		});
		var type = 'today';
		if(_i == 0){
			type = 'today';
		}else if(_i == 1){
			type = 'week';
		}else if(_i == 2){
			type = 'month';
		}else if(_i == 3){
			type = 'super';
		};
		$.User.userCharmRank(type,'#charmrank_append');
});

$('#friendlyrank a').bind('click',function(){
	$(this).addClass('starttabover');
		var _this = this;
		var _i = 0;
		$('#friendlyrank a').each(function (i,index){
			if(_this != this){
				$(this).removeClass('starttabover');
			}else{
				_i = i;
			}
		});
		var type = 'today';
		if(_i == 0){
			type = 'today';
		}else if(_i == 1){
			type = 'week';
		}else if(_i == 2){
			type = 'month';
		}else if(_i == 3){
			type = 'super';
		};
		$.User.userFriendlyRank(type,'#friendlyrank_append');
});

/*小头像，鼠标悬停显示关注按钮*/
showattent('.conbox-list dt');
showattent('.conbox-btm li');

//返回顶部
function floatPlug(){
	var floatY=$(this).scrollTop();
	var floatH=$(this).height()/2-100;
	var parentT=floatY+floatH;
	if(parentT<0){
		parentT=Math.ceil(parentT);	
	}else{
		parentT=Math.floor(parentT);	
	}
	var parentV=parentT+"px";
	$("#GoTop").stop(true,true).animate({top:parentV},"fast");
}
floatPlug();
$(window).scroll(function(){
	if($(window).scrollTop()>200)
	{
		$("#GoTop").css('display','block');
	}else{
		$("#GoTop").css('display','none');
	}						  
	floatPlug();
});	

//文字滚动
$(".topbox-m").slide({mainCell:".mbox-con ul",effect:"topLoop",autoPlay:true,scroll:3,vis:3,delayTime:0,triggerTime:120});

$('.tip-text').hover(function(){
    $(this).find('.tipcon').css('display','block');
},function(){
    $(this).find('.tipcon').css('display','none');
});


$('.content-r img').lazyload({
	effect : "fadeIn",
	failurelimit : 5
});



$('.biao').hover(function(){
    $(this).find('span').css('display','block');
},function(){
    $(this).find('span').css('display','none');
});

if($.User.getSingleAttribute('uid',true) <= 0){
	var timestamp=new Date().getTime()/1000;
	var indexGuide=$.cookie('indexGuide');
	if(indexGuide==null||indexGuide==undefined){
		$('.point1').css('display','block');
		 $.ajax({
			type:'POST',
			url:'index.php?r=index/guide',
			dataType:'JSON',
			success:function(data){
				if(data){
					$('.point1 a:last').attr('target','_blank');
					$('.point1 a:last').attr("href",data.url);
				}
			}
		})
	}else{
		$('.point1').css('display','none');
	}
	$('.point1 .pointBtn,.point1 .close').bind('click',function(){
		$.cookie('indexGuide',1,{expires: 365,path: '/',domain:cookie_domain});
	    $('.point1').css('display','none');
	   
	});
}

});




</script>

<!-- Google Code for &#28508;&#22312;&#23458;&#25143; Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 980954121;
var google_conversion_language = "en";
var google_conversion_format = "2";
var google_conversion_color = "ffffff";
var google_conversion_label = "WKhRCMeb_QYQidjg0wM";
var google_conversion_value = 0;
/* ]]> */
</script>
<script type="text/javascript"
	src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
	<div style="display: inline;">
		<img height="1" width="1" style="border-style: none;" alt=""
			src="//www.googleadservices.com/pagead/conversion/980954121/?value=0&amp;label=WKhRCMeb_QYQidjg0wM&amp;guid=ON&amp;script=0" />
	</div>
</noscript>