<?php $indexLeft=$this->getIndexLeft();?>
		<div class="leftWrap">
			<div class="viewcount">
				<p>当前在线玩家<br></p>
				<p class="fornum">
				<em class="ml5">位</em>
				<?php for($i=strlen($indexLeft['onlineCount'])-1;$i>=0;$i--):?>
				<em class="num no-<?php echo $indexLeft['onlineCount'][$i]; ?>"></em>
				<?php endfor;?>
				</p>
			</div>
			<p class="count-btm DD_belapng"></p>
			<!--.viewcount-->
			<div class="liveHall">
				<div class="liveHall-hd DD_belapng">直播大厅</div>
				<div class="liveHall-bd">
					<dl class="hallLevel  clickHall">
						<dt><a href="javascript:void(0);"><i class="fleft DD_belapng"></i><span class="fleft">主播等级</span></a></dt>
						<dd>
						<?php 
							$doteyRankId=0;
							foreach ($indexLeft['doteyRank'] as $key=>$value):
								$doteyRankId++;
							?>
							<a href="<?php echo 'index.php?'.http_build_query(array('r'=>'index/categoryv5','type'=>'rank','id'=>$doteyRankId));?>" target="<?php echo $this->target?>">
							<span><?php echo $key;?></span><em><?php echo number_format($value);?></em></a>
						<?php endforeach;?>
						</dd>
					</dl>
					<dl class="hallImp  clickHall">
						<dt><a href="javascript:void(0);"><i class="fleft DD_belapng"></i><span class="fleft">主播印象</span></a></dt>
						<dd>
						<?php foreach ($indexLeft['tags'] as $tagRow):?>
							<a href="<?php echo 'index.php?'.http_build_query(array('r'=>'index/categoryv5','type'=>'tag','id'=>$tagRow['tag_id']));?>" target="<?php echo $this->target?>">
							<span><?php echo $tagRow['tag_name'];?></span><em><?php echo number_format($tagRow['user_count']);?></em></a>
						<?php endforeach;?>
						</dd>
					</dl>
					<dl class="hallSong">
						<dt><a href="<?php echo 'index.php?'.http_build_query(array('r'=>'index/categoryv5','type'=>'song'));?>" target="<?php echo $this->target?>">
						<i class="fleft DD_belapng"></i><span class="fleft">点唱专区</span></a>
						</dt>
					</dl>
				</div>
			</div><!--.liveHall-->
			<div class="arder">
				<dl class="hotActive">
					<dt><i class="fleft icont"></i><span class="fleft">热门活动</span></dt>
					<dd><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/left/hotactive1.jpg">
					<a href="<?php $this->getTargetHref('index.php?r=activities/firstchargegifts')?>" target="<?php echo $this->target?>">首冲送好礼</a>
					</dd>
					<dd><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/left/hotactive2.jpg">
					<a href="<?php $this->getTargetHref('index.php?r=activities/guardangel')?>" target="<?php echo $this->target?>">守护天使</a>
					</dd>
					<dd><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/left/hotactive3.jpg">
					<a href="<?php $this->getTargetHref('index.php?r=activities/happySaturday')?>" target="<?php echo $this->target?>">快乐星期六</a>
					</dd>
					<dd><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/left/hotactive4.jpg">
					<a href="<?php $this->getTargetHref('index.php?r=activities/giftstar')?>" target="<?php echo $this->target?>">礼物周星</a>
					</dd>
					<dd><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/left/birthday.png">
					<a href="<?php $this->getTargetHref('index.php?r=activities/happybirthday')?>" target="<?php echo $this->target?>">生日快乐</a>
					</dd>
				</dl>
				<!-- 
				<dl class="games">
					<dt><i class="fleft icont"></i><span class="fleft">游戏娱乐</span></dt>
					<dd><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/left/hotactive5.jpg">
					<a href="<?php $this->getTargetHref( Yii::app()->params['letian_game']['host'] . '/gp/html/BreakEggs.html')?>"
						title="砸金蛋" target="<?php echo $this->target?>">砸金蛋</a></dd>
					<dd><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/left/hotactive6.jpg">
					<a href="<?php $this->getTargetHref( Yii::app()->params['letian_game']['host'] . '/gp/html/LuckSofa.html')?>"
						title="幸运沙发" target="<?php echo $this->target?>">幸运沙发</a></dd>
					<dd><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/left/hotactive7.jpg"><a href="#">棋牌中心</a></dd>
					<dd><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/left/hotactive8.jpg"><a href="#">幸福游乐园</a></dd>
				</dl>
				 -->
			</div><!--.arder-->
			<div class="userhelp">
				<dl class="serviceQQ clearfix">
					<dt><i class="fleft icont"></i><span>24小时客服</span></dt>
					<?php foreach ($this->viewer['qqKeFu'] as $kefuKey=>$kefuValue):?>
					<dd>
						<a class="paret" class="pink" href="#"><?php echo $kefuKey;?></a>
						<?php if(count($kefuValue)>0):?>
						<div class="serviceCon">
							<?php foreach ($kefuValue as $kefuRow):?>
								<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo $kefuRow['contact_account']?>&amp;site=qq&amp;menu=yes">
	            		 		<img border="0" src="http://wpa.qq.com/pa?p=3:<?php echo $kefuRow['contact_account']?>:45" alt="<?php echo $kefuRow['contact_name']?>" title="<?php echo $kefuRow['contact_name']?>" style="vertical-align:middle;">
	            	  			</a> 
							<?php endforeach;?>
						</div>
						<?php endif;?>
					</dd>
					<?php endforeach;?>
				</dl>
				<dl class="webhelp clearfix">
					<dt><i class="fleft icont"></i><span>网站帮助</span></dt>
					<dd><a  target="<?php echo $this->target?>" href="index.php?r=public/doteyHelp">主播帮助</a></dd>
					<dd><a target="<?php echo $this->target?>"  href="index.php?r=public/annouce&thread_id=29">用户服务条例</a></dd>
					<dd><a  target="<?php echo $this->target?>" href="index.php?r=public/help">玩家帮助</a></dd>
				</dl>
			</div><!--.userhelp-->
		</div><!--.leftWrap-->
		<script>
		function goCategory(type,id){
			href = 'index.php?r=index/categoryv5&type='+type;
			if(id !='' && id != null)
				href+='&id='+id;
			location.href = href;
			return false;
		}
		</script>