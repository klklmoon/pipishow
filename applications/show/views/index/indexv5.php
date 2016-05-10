<div id="GoTop" class="gotop-box">
    <a href="#">
        <span class="showuptop"></span>
        <span class="gotop-con">回到<br>顶部</span>
    </a>
</div><!--.gotop-box-->
<?php $attentionType = isset($attentionType) ? $attentionType : 'common';?>
<div class="main-box">
	<div class="w1000 clearfix">
		<!-- 首页左侧导航 -->
		<?php $this->renderPartial('application.views.index.index_left'); ?>
		<div class="midWrap">
			<div id="showbatch" class="showbatch clearfix">
				<?php $this->renderPartial('application.views.index.showbatch',array('dynamic'=>$dynamic));?>
			</div><!--.showbatch-->
			<!-- 
			<div class="cnnerGovem">
            	<div class="govem-hd">
            		<ul id="myAttentions" class="fleft">
            			<li>关注的主播&nbsp;&nbsp;<strong id="attentions_num"></strong></li>
            			<li>管理的主播&nbsp;&nbsp;<strong id="manages_num"></strong></li>
            			<li id="seeDotey">看过的主播&nbsp;&nbsp;<strong id="seeArchives_num"></strong></li>
            		</ul>
            		<p class="fright lgovm-con">
                		<a href="index.php?r=account/follow"  target="">我的关注</a>
                		<em>&#124</em>
                		<a href="index.php?r=account/manage"  target="">管理</a>
            		</p>
            	</div>
            	<div class="govem-bd">
            		<div class="govembd-list">
            			<dl id="attentions"></dl>
            			<dl id="manages"></dl>
            			<dl id="seeArchives"></dl>
            		</div>--.govembd-list--
            		<p class="govemNum"></p>
            	</div>--.govem-bd--
            </div>--.cnnerGovem--
             -->
            <!--今日推荐start-->
            <?php if(isset($todayRecommand['living']) && count($todayRecommand['living'])>0):?>
            <div class="todayRecommend">
            	<h2 class="mainTit"><em></em><span>今日推荐</span></h2>
            	<ul class="actorlist clearfix">
				<?php $this->renderPartial('application.views.index.doteylist',$todayRecommand);?>
				</ul><!--.actorlist-->
            </div><!--.todayRecommend-->
            <?php endif;?>
            <!--今日推荐end-->
            <!--最新开播start-->
            <?php if(isset($newLiving['living']) && count($newLiving['living'])>0):?>
            <div class="newplay">
            	<h2 class="mainTit"><em></em><span>最新开播</span>
            	<a href="index.php?r=index/categoryv5&type=normal&sort=time&by=desc"  target="<?php echo $this->target?>">更多</a>
            	</h2>
            	<ul class="actorlist clearfix">
            	<?php $this->renderPartial('application.views.index.doteylist',$newLiving);?>
            	</ul><!--.actorlist-->
            </div><!--.newplay-->
            <?php endif;?>
            <!--最新开播end-->
            <!--热门主播strat-->
            <?php if(isset($hotArchives['living']) && count($hotArchives['living'])>0):?>
            <div class="hotactors">
            	<h2 class="mainTit"><em></em><span>热门主播</span>
            	<a href="index.php?r=index/categoryv5&type=normal&sort=online&by=desc"  target="<?php echo $this->target?>">更多</a></h2>
            	<ul class="actorlist clearfix">
				<?php $this->renderPartial('application.views.index.doteylist',$hotArchives);?>
				</ul><!--.actorlist-->
            	<!-- <p class="morehotBtn"><a href="#">点击看更多直播中的美女</a></p> -->
            </div><!--.hotactors-->
            <?php endif;?>
            <!--热门主播end-->
            <!--即将开播strat-->
            <div class="willplay">
            	<h2 class="mainTit"><em></em><span>即将开播</span></h2>
            	<ul class="actorlist clearfix">
            	<?php $this->renderPartial('application.views.index.doteylist',$willLive);?>
				</ul><!--.actorlist-->
            </div><!--.willplay-->
            <!--即将开播end-->
		</div><!--.midWrap-->

		<div class="rightWrap">
			<div class="rtBtnbox">
			</div><!--.rtBtn-->
			<div class="rtopbox">
				<div class="Rflash">
					<div class="rflash-bd">
						<ul>
						<?php foreach ($showcase as $s) :?>
							<li><a href="<?php $this->getTargetHref($s['textlink'])?>"  target="<?php echo $this->target?>"><img src="<?php echo $s['piclink']?>"></a></li>
						<?php endforeach;	?>
						</ul>
					</div>
					<div class="rflash-hd clearfix">
						<span class="fleft prev"></span>
						<ul class="fleft">
						<?php foreach ($showcase as $key => $s) :?>
							<li><?php ++$key?></li>
						<?php endforeach;?>
						</ul>
						<span class="fleft next"></span>
					</div>
				</div><!--.Rflash-->
				<!-- 用户登录 -->
				<div  id="userinfo">
				</div><!--.userEnter-->
				
				<dl class="solidot">
					<dt><i class="fleft icont"></i>公告</dt>
					<?php foreach ($notice as $n) :?> 
					<dd><a href="<?php $this->getTargetHref($n['textlink'])?>"  target="<?php echo $this->target?>"><?php echo $n['subject']?></a></dd>
					<?php endforeach;?>
				</dl><!--.solidot-->
			</div><!--.rtopbox-->
			<div class="showActor">
				<!--生日主播start-->
				<?php if((isset($todayBirthdayArchives) && count($todayBirthdayArchives)>0) || (isset($willBirthdayArchives) && count($willBirthdayArchives)>0)):?>
				<div class="showac birthday">
					<h3 class="showac-h">
						<i class="icont"></i>
						<span>生日<em>主播</em></span>
						<a title="即将过生日的主播"><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/right/questionMark.jpg"></a>
						<p class="showacHBtn"><span title="上一页" class="up"></span><span title="下一页" class="down"></span></p>
					</h3>
					<div class="showac-bd">

						<ul class="showac-list">
							<?php
								if(count($todayBirthdayArchives)>0):
									foreach ($todayBirthdayArchives as $doteyRow):
										$isAttention = isset($doteyRow['is_attention']) ? $doteyRow['is_attention'] : 0;
										$attentTionClass = $isAttention ? 'cancelatt' : '';
										$attentIionText = $isAttention ? '取消关注' : '关注';
										$jsMethod = $isAttention ? 'cacnelAttentionUser' : 'attentionUser';
							?>
							<li>
								<a href="<?php echo $this->getTargetHref("/".$doteyRow['uid']);?>" target="<?php echo $this->target?>">
									<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png" data-original="<?php echo $doteyRow['display_small'];?>">
								<span title="<?php echo $doteyRow['doteyInfo']['nk'];?>"><?php echo $doteyRow['doteyInfo']['nk'];?></span>
								</a>
								<p>今日生日</p>
								<a href="javascript:void(0);" title="<?php echo $attentIionText?>"
								  onclick="$.User.<?php echo $jsMethod?>('<?php echo $doteyRow['uid']?>',this,'<?php echo $attentionType?>');"
								  class="attent <?php echo $attentTionClass?>"></a>
							</li>
							<?php 
									endforeach;
								endif;
							?>
							<?php
								if(count($willBirthdayArchives)>0):
									foreach ($willBirthdayArchives as $doteyRow):
										$isAttention = isset($doteyRow['is_attention']) ? $doteyRow['is_attention'] : 0;
										$attentTionClass = $isAttention ? 'cancelatt' : '';
										$attentIionText = $isAttention ? '取消关注' : '关注';
										$jsMethod = $isAttention ? 'cacnelAttentionUser' : 'attentionUser';
							?>
							<li>
								<a href="<?php echo $this->getTargetHref("/".$doteyRow['uid']);?>" target="<?php echo $this->target?>">
									<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png" data-original="<?php echo $doteyRow['display_small'];?>">
								<span title="<?php echo $doteyRow['doteyInfo']['nk'];?>"><?php echo $doteyRow['doteyInfo']['nk'];?></span>
								</a>
								<p><?php echo $doteyRow['sbirthday'];?></p>
								<a href="javascript:void(0);" title="<?php echo $attentIionText?>"
								  onclick="$.User.<?php echo $jsMethod?>('<?php echo $doteyRow['uid']?>',this,'<?php echo $attentionType?>');"
								  class="attent <?php echo $attentTionClass?>"></a>
							</li>
							<?php 
									endforeach;
								endif;
							?>
						</ul>
					</div>
				</div><!--.showac-->
				<?php endif;?>
				<!--生日主播end-->
				<!--明星主播start-->
				<?php if (isset($finalStarDotey) && count($finalStarDotey)>0) : ?>
				<div class="showac stars">
					<h3 class="showac-h">
						<i class="icont"></i>
						<span>明星<em>主播</em></span>
						<a title="<?php echo $finalStarDoteyDesc;?>"><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/right/questionMark.jpg"></a>
					</h3>
					<div class="showac-bd">
						<ul class="showac-list">
						<?php foreach ($finalStarDotey as $star):
									$isAttention = isset($star['is_attention']) ? $star['is_attention'] : 0;
									$attentTionClass = $isAttention ? 'cancelatt' : '';
									$attentIionText = $isAttention ? '取消关注' : '关注';
									$jsMethod = $isAttention ? 'cacnelAttentionUser' : 'attentionUser';
						?>
							<li>
								<a href="<?php $this->getTargetHref('/' . $star['d_uid'],true,false)?>" target="<?php echo $this->target?>">
									<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png" data-original="<?php echo $star['d_avatar'];?>">
								<span title="<?php echo $star['d_nickname'];?>"><?php echo $star['d_nickname']?></span></a>
								<a href="javascript:void(0);" title="<?php echo $attentIionText?>"
								  onclick="$.User.<?php echo $jsMethod?>('<?php echo $star['d_uid']?>',this,'<?php echo $attentionType?>');"
								  class="attent <?php echo $attentTionClass?>"></a>
							</li>
						<?php endforeach;?>
						</ul>
					
					</div>
				</div><!--.showac-->
				<?php endif;?>
				<!--明星主播end-->
				<!--新秀主播start-->
				<?php if (isset($finalRookieDotey) && count($finalRookieDotey)>0) : ?>
				<div class="showac newshow">
					<h3 class="showac-h">
						<i class="icont"></i>
						<span>新秀<em>主播</em></span>
						<a title="<?php echo $finalRookieDoteyDesc;?>"><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/right/questionMark.jpg"></a>
					</h3>
					<div class="showac-bd">
					
						<ul class="showac-list">
						<?php foreach ($finalRookieDotey as $new) :
								$isAttention = isset($new['is_attention']) ? $new['is_attention'] : 0;
								$attentTionClass = $isAttention ? 'cancelatt' : '';
								$attentIionText = $isAttention ? '取消关注' : '关注';
								$jsMethod = $isAttention ? 'cacnelAttentionUser' : 'attentionUser';
						?>   
							<li>
								<a href="<?php $this->getTargetHref('/' . $new['d_uid'],true,false)?>" target="<?php echo $this->target?>">
									<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png" data-original="<?php echo $new['d_avatar'];?>">
								<span title="<?php echo $new['d_nickname'];?>"><?php echo $new['d_nickname']?></span></a>
								<a href="javascript:void(0);" title="<?php echo $attentIionText?>"
								  onclick="$.User.<?php echo $jsMethod?>('<?php echo $new['d_uid']?>',this,'<?php echo $attentionType?>');"
								  class="attent <?php echo $attentTionClass?>"></a>
							</li>
						<?php endforeach;?>
						</ul>
					</div>
				</div><!--.showac-->
				<?php endif;?>
				<!--新秀主播end-->
				<!--最新加入start-->
				<?php if (isset($newJoinDotey) && count($newJoinDotey)>0) : ?>
				<div class="showac newjoin">
					<h3 class="showac-h">
						<i class="icont"></i>
						<span>最新<em>加入</em></span>
						<a title="<?php echo $newJoinDoteyDesc;?>"><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/right/questionMark.jpg"></a>
					</h3>
					<div class="showac-bd">
						<ul class="showac-list">
						<?php foreach ($newJoinDotey as $join) :
									$isAttention = isset($join['is_attention']) ? $join['is_attention'] : 0;
									$attentTionClass = $isAttention ? 'cancelatt' : '';
									$attentIionText = $isAttention ? '取消关注' : '关注';
									$jsMethod = $isAttention ? 'cacnelAttentionUser' : 'attentionUser';
						?>   
							<li>
								<a href="<?php $this->getTargetHref('/' . $join['d_uid'],true,false)?>" target="<?php echo $this->target?>">
									<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png" data-original="<?php echo $join['d_avatar'];?>">
								<span title="<?php echo $join['d_nickname'];?>"><?php echo $join['d_nickname']?></span></a>
								<a href="javascript:void(0);" title="<?php echo $attentIionText?>"
								  onclick="$.User.<?php echo $jsMethod?>('<?php echo $join['d_uid']?>',this,'<?php echo $attentionType?>');"
								  class="attent <?php echo $attentTionClass?>"></a>
							</li>
						<?php endforeach;?>
						</ul>
					</div>
				</div><!--.showac-->
				<?php endif;?>
				<!--最新加入end-->
			</div><!--.showActor-->
            <div class="kuso">
                <h4><span>明星魅力榜</span><a title="根据主题获得的魅力值排序"><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/right/questionMark.jpg"></a></h4>
                <div class="kusobox">
                    <div class="kuso-hd">
                        <ul class="clearfix" id="charmrank">
                            <li class="on first"><a href="javascript:void(0);">今日</a></li>
                            <li><a href="javascript:void(0);">本周</a></li>
                            <li><a href="javascript:void(0);">本月</a></li>
                            <li><a href="javascript:void(0);">超级</a></li>
                        </ul>
                    </div><!--.kuso-hd-->
                    <div class="kuso-bd" id="charmrank_append">
						<?php $this->renderPartial('application.views.index.charmrank',array('rank'=>$todayCharmRank,'isLazyLoad'=>true));?>
						<?php $this->renderPartial('application.views.index.charmrank',array('rank'=>$weekCharmRank,'isLazyLoad'=>true));?>
						<?php $this->renderPartial('application.views.index.charmrank',array('rank'=>$monthCharmRank,'isLazyLoad'=>true));?>
						<?php $this->renderPartial('application.views.index.charmrank',array('rank'=>$superCharmRank,'isLazyLoad'=>true));?>
                    </div><!--.kuso-bd-->
                </div><!--.kusobox-->
                <h4 class="mt10"><span>富豪贡献榜</span><a title="根据主题获得的贡献值排序"><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/right/questionMark.jpg"></a></h4>
                <div class="kusobox">
                    <div class="kuso-hd">
                        <ul class="clearfix" id="richrank">
                            <li class="on first"><a href="javascript:void(0);">今日</a></li>
                            <li><a href="javascript:void(0);">本周</a></li>
                            <li><a href="javascript:void(0);">本月</a></li>
                            <li><a href="javascript:void(0);">超级</a></li>
                        </ul>
                    </div><!--.kuso-hd-->
                    <div class="kuso-bd" id="richrank_append">
						<?php $this->renderPartial('application.views.index.richrank',array('rank'=>$todayRichRank,'isLazyLoad'=>true));?>
						<?php $this->renderPartial('application.views.index.richrank',array('rank'=>$weekRichRank,'isLazyLoad'=>true));?>
						<?php $this->renderPartial('application.views.index.richrank',array('rank'=>$monthRichRank,'isLazyLoad'=>true));?>
						<?php $this->renderPartial('application.views.index.richrank',array('rank'=>$superRichRank,'isLazyLoad'=>true));?>
                    </div><!--.kuso-bd-->
                </div><!--.kusobox-->
            </div><!--.kuso-->
		</div><!--.rightWrap-->
	</div>
</div><!--.main-box-->
<script type="text/javascript">
$(function(){						
	//中间“换一批”轮播图
	$('.batchlist li').live({
	mouseover:function(){
			$(this).addClass('on').find('.batchmask').css('display','block');
			$(this).find('.batchFram').css('display','block');
			$(this).mousemove(function(e){
			    var ev=e || event;
			    var t=(ev.clientY+$(window).scrollTop())-$(this).offset().top+12;
			    var l=ev.clientX-$(this).offset().left+12;
			    $(this).find('.batchFram').css({
			        'top':t+'px',
			        'left':l+'px'
			    });
			});
	},mouseleave:function(){
		$(this).removeClass('on').find('.batchmask').css('display','none');
		$(this).find('.batchFram').css('display','none');
	}
	});
	
	
	//我的资产鼠标悬停
	$('#userinfo .mybank dd').live({
		mouseover:function(){
		    var index=$(this).index(),
		    atext=$(this).find('a');
		    switch(index){
		        case 0:
		        atext.text('充值皮蛋');
		        break;
		        case 1:
		        atext.text('兑换皮蛋');
		        break;
		        case 2:
		        if(atext.html()=='魅力点'){
		        	atext.text('魅力提现');
		        }
		        break;
		    }
		},
		mouseleave:function(){
		    for(var i=0;i<3;i++){
		    	var ctext=$('#userinfo .mybank dd').eq(i).find('a');
		        if(i==0)
			    {
		            ctext.text('皮蛋');
		        }
		        else if(i==1)
			    {
		        	ctext.text('皮点');
		        }
		        else if(i==2)
			    {
		        	if(ctext.html()=='魅力提现')
			        {
		            	ctext.text('魅力点');
		            }
		        }
		    }
		}
		
	});
	
	
	$('#indexModifyNickname').live('click',function(){
		$.User.updateUserNickName($('#indexNickAame').val(),this);
		$('.editbox').css('display','none');
		userlogin();
	})
	
	//首页中间部分关注、管理主播切换
	$('.cnnerGovem').slide({titCell:".govem-hd li",mainCell:".govembd-list",trigger:"click"});
	
	//首页右侧焦点图切换
	$(".Rflash").slide({titCell:".rflash-hd ul",mainCell:".rflash-bd ul",autoPage:true,delayTime:0,autoPlay:true});
	
	//首页右侧“主播生日”选项卡
	$(".birthday").slide({mainCell:".showac-bd ul",autoPage:true,effect:"left",prevCell:".up",nextCell:".down",scroll:3,vis:3});
	
	//返回顶部
	var  elem=document.getElementById('GoTop');
	$(window).scroll(function(){
	    if($(window).scrollTop()>400)
	    {
	        $("#GoTop").css('display','block');
	        elem.style.right = '10px';
	        elem.style.top = '50%';
	        position.fixed(elem);
	    }else{
	        $("#GoTop").css('display','none');
	    }                         
	});
	$('#GoTop a').hover(function(){
	    $(this).find('.gotop-con').css('display','block').animate({left: '0px'},'fast');
	},function(){
	    $(this).find('.gotop-con').css('display','none').animate({left: '50px'},'fast');
	})
	
	$("#refreshBtn").live("click",function(){
		$.ajax({
			type : 'post',
			url : 'index.php?r=index/batchlist',
			async : false,
			dataType: "html",
			success:function(data){
				$('#showbatch').html(data);
			}
		});
	});
	
	$('#charmrank li').bind('click',function(){
		$(this).addClass('on');
		var _this = this;
		var _i = 0;
		$('#charmrank li').each(function (i,index){
			if(_this != this){
				$(this).removeClass('on');
			}else{
				_i = i;
			}
		});

	});
	
	$('#richrank li').bind('click',function(){
		$(this).addClass('on');
		var _this = this;
		var _i = 0;
		$('#richrank li').each(function (i,index){
			if(_this != this){
				$(this).removeClass('on');
			}else{
				_i = i;
			}
		});

	});
	
	function myAttentionDotey()
	{
		$.ajax({
			type : 'post',
			url : 'index.php?r=public/MyAttentionDotey',
			async : false,
			dataType: "json",
			success:function(data){
				$("#attentions").html("<dd class='nodata'>当前被关注的主播都没有在直播，去看看其他主播吧！</dd>");
				$("#manages").html("<dd class='nodata'>当前被管理的主播都没有在直播，去看看其他主播吧！</dd>");	
				$("#seeArchives").html("<dd class='nodata'>还没有看过谁，去<a class='pink' href='index.php?r=index/categoryv5&type=normal'  target='<?php echo $this->target?>'>感受一下美女主播的欢乐吧！</a></dd>");
				
				if(data!=null && data!=undefined && data!='')
				{
					var attentionLivingArchivesNum=(data.attentionLivingArchivesNum==undefined||data.attentionLivingArchivesNum==null||data.attentionLivingArchivesNum==''?0:data.attentionLivingArchivesNum);
					var attentionArchivesNum=(data.attentionArchivesNum==undefined||data.attentionArchivesNum==null||data.attentionArchivesNum==''?0:data.attentionArchivesNum);
					$("#attentions_num").text(attentionLivingArchivesNum+"/"+attentionArchivesNum);
		
					var manageLivingArchivesNum=(data.manageLivingArchivesNum==undefined||data.manageLivingArchivesNum==null||data.manageLivingArchivesNum==''?0:data.manageLivingArchivesNum);
					var manageArchivesNum=(data.manageArchivesNum==undefined||data.manageArchivesNum==null||data.manageArchivesNum==''?0:data.manageArchivesNum);
					$("#manages_num").text(manageLivingArchivesNum+"/"+manageArchivesNum);
					
					var seeArchivesNum=(data.seeArchivesNum==undefined||data.seeArchivesNum==null||data.seeArchivesNum==''?0:data.seeArchivesNum);
					$("#seeArchives_num").text(data.seeArchivesNum);
					
					if(attentionLivingArchivesNum>0)
						$("#attentions").html(data.attentionArchives);				
	
					$(".govemNum").html("当前有 <em class=\"pink\">"+attentionLivingArchivesNum+"</em> 位关注的主播正在直播，<a class=\"pink\" href=\"#\">点击显示更多</a>");
					
					if(manageLivingArchivesNum>0)
						$("#manages").html(data.manageArchives);
	
					if(seeArchivesNum>0)
						$('#seeArchives').html(data.seeArchives);
				}
				if($.User.getSingleAttribute('uid',true) <= 0){
					$('#myAttentions li').eq(2).click();
				}
			}
		});
	}
	
	function userlogin()
	{
		$.ajax({
			type : 'post',
			url : 'index.php?r=index/userinfo',
			async : false,
			dataType: "html",
			success:function(data){
				if($.User.getSingleAttribute('uid',true) > 0){
					$("#userinfo").removeClass('userNoLog');
					$("#userinfo").addClass('userEnter');
				}
				else
				{
					$("#userinfo").removeClass('userEnter');
					$("#userinfo").addClass('userNoLog');
				}
				$("#userinfo").html(data);
			}
		});
	}
	function startLiving()
	{
		if($.User.getSingleAttribute('uid',true) > 0 && $.User.isDotey(true)){
			$(".rtBtnbox").html('<a class="openlonBtn" href="index.php?r=dotey/apply"><span class="DD_belapng">开启直播</span></a>');
		}
		else
		{
			$(".rtBtnbox").html('<a class="openliveBtn" href="index.php?r=dotey/apply">开启直播</a><a class="signedBtn" href="index.php?r=dotey/apply">主播签约</a>');
		}
	}
	
	startLiving();
	userlogin();
	$(".rightWrap img").lazyload({
		effect : "fadeIn",
		failurelimit : 5,
		skip_invisible : false
	});
});
</script>