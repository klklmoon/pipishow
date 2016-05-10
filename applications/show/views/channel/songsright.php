 <div class="rankbox">
        	<h4>
            	<i class="banericon"></i>
            	<span class="pink">点唱达人</span>
            </h4>
            <p id="RichTab" class="start-tab clearfix">
                    <a title="今日" href="#" class="starttabover">今日</a><em>|</em>
                    <a title="本周" href="#">本周</a><em>|</em>
                    <a title="本月" href="#">本月</a><em>|</em>
                    <a title="超级" href="#">超级</a>
            </p>
            <div class="conbox">
            	<div class="rankcon">
                    <ul class="rankcon-list">
<?php 
foreach($rightData['user_songs_rank']['today'] as $key=>$_rank):
$key++;
	if($key > 8)
	break
//$avatar = $this->userService->getUserAvatar($_rank['uid'],'small');
?>	                  
                       <li class="clearfix">
                            <em class="fleft top2 order"><?php echo $key?></em>
                             <p class="richname fleft"><a title="<?php echo $_rank['nickname']?>" href="javascript:void(0)" class="ellipsis pink"><?php echo $_rank['nickname']?></a></p>
                             <em class="fright mt5 lvlr lvlr-<?php echo $_rank['rank']?>"></em>
                        </li>
<?php 
endforeach;
?>              
                    </ul>
                </div><!--.rankcon-->
                <div class="rankcon none">
                    <ul class="rankcon-list">
<?php 
foreach($rightData['user_songs_rank']['week'] as $key=>$_rank):
	$key++;
	if($key > 8)
	break;
//$avatar = $this->userService->getUserAvatar($_rank['uid'],'small');
?>	                   
                       <li class="clearfix">
                            <em class="fleft top2 order"><?php echo $key?></em>
                             <p class="richname fleft"><a title="<?php echo $_rank['nickname']?>" href="javascript:void(0)" class="ellipsis pink"><?php echo $_rank['nickname']?></a></p>
                             <em class="fright mt5 lvlr lvlr-<?php echo $_rank['rank']?>"></em>
                        </li>
<?php 
endforeach;
?> 
                    </ul>
                </div><!--.rankcon-->
                <div class="rankcon none">
                    <ul class="rankcon-list">
 <?php 
foreach($rightData['user_songs_rank']['month'] as $key=>$_rank):
	$key++;
	if($key > 8)
		break;
//$avatar = $this->userService->getUserAvatar($_rank['uid'],'small');
?>                   
                       <li class="clearfix">
                            <em class="fleft top2 order"><?php echo $key?></em>
                             <p class="richname fleft"><a title="<?php echo $_rank['nickname']?>" href="javascript:void(0)" class="ellipsis pink"><?php echo $_rank['nickname']?></a></p>
                             <em class="fright mt5 lvlr lvlr-<?php echo $_rank['rank']?>"></em>
                        </li>
<?php 
endforeach;
?>
                    </ul>
                </div><!--.rankcon-->
                <div class="rankcon none">
                    <ul class="rankcon-list">
  <?php 
foreach($rightData['user_songs_rank']['super'] as $key=>$_rank):
	$key++;
	if($key > 8)
		break;
//$avatar = $this->userService->getUserAvatar($_rank['uid'],'small');
?>                    
                        <li class="clearfix">
                            <em class="fleft top2 order"><?php echo $key?></em>
                             <p class="richname fleft"><a title="<?php echo $_rank['nickname']?>" href="javascript:void(0)" class="ellipsis pink"><?php echo $_rank['nickname']?></a></p>
                             <em class="fright mt5 lvlr lvlr-<?php echo $_rank['rank']?>"></em>
                        </li>
<?php 
endforeach;
?>
       
                    </ul>
                </div><!--.rankcon-->
            </div><!--.conbox-->
            <div class="rlist-btm clearfix">
                <h2 class="fleft">点歌达人勋章</h2>
                <span class="fleft mt10 rbtm-pic"><img src="<?php echo $this->pipiFrontPath?>/fontimg/channel/rightconpic1.jpg"></span>
                <p class="fleft mt10 rbtm-con">周榜中获得排名前五的玩家可在下周一获得勋章7天</p>
           	</div><!--.rlist-btm-->
        </div><!--.rankbox-->
        
        <div class="rankbox mt10">
        	<h4 class="clearfix">
            	<i class="fleft banericon"></i>
            	<span class="fleft pink">点唱榜</span>
                <a href="<?php $this->getTargetHref('index.php?r=top/index')?>" class="fleft more" target="<?php echo $this->target?>">更多</a>
            </h4>
            <p id="JukeTab" class="start-tab clearfix">
                    <a title="今日" href="javascript:void(0);" class="starttabover">今日</a><em>|</em>
                    <a title="本周" href="javascript:void(0);">本周</a><em>|</em>
                    <a title="本月" href="javascript:void(0);">本月</a><em>|</em>
                    <a title="超级" href="javascript:void(0);">超级</a>
            </p>
            <div class="conbox">
            	<div class="rankcon">
<?php 
if($rightData['dotey_songs_rank']['today']):
	$topTodayData = array_shift($rightData['dotey_songs_rank']['today']);
	$avatar = $topTodayData['d_avatar'];
	$archivesHref = 'index.php?r=archives/index/uid/'.$topTodayData['d_uid'];
	if($this->isLogin){
		$weiboService = new WeiboService();
		$isAttentTion = $weiboService->isAttentionDotey($topTodayData['d_uid'],Yii::app()->user->id);
		$attentTionClass = $isAttentTion ? 'cancelatt' : '';
		$jsMethod = $isAttentTion ? 'cacnelAttentionUser' : 'attentionUser';
	}else{
		$attentTionClass = '';
		$jsMethod = 'attentionUser';
	}
?>	             	
                	<dl class="conbox-list">
                        <dt>
                            <a title="<?php echo $topTodayData['d_nickname']?>" href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><img src="<?php echo $avatar?>"></a>
                            <a title="关注" href="javascript:void(0)" class="attent <?php echo $attentTionClass?>" style="display: none;" onclick="$.User.<?php echo $jsMethod?>('<?php echo $topTodayData['d_uid']?>',this,'single');"></a>
                        </dt>
                        <dd>
                            <a title="<?php echo $topTodayData['d_nickname']?>" class="pink name ellipsis" href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><?php echo $topTodayData['d_nickname']?></a>
                            <p class="toper clearfix"><span class="fleft">点歌数+<?php echo $topTodayData['num']?></span></p> 
                        </dd>
                    </dl>
 <?php 
endif;
?>                   
                    <ul class="rankconpic-list">
<?php 
$i = 1;
foreach($rightData['dotey_songs_rank']['today'] as $key=>$_rank):
$i++;
$avatar = $_rank['d_avatar'];
$archivesHref = 'index.php?r=archives/index/uid/'.$_rank['d_uid'];
?>                   
                        <li class="clearfix">
                            <em class="fleft top2 order"><?php echo $i?></em>
                            <a class="fleft rank-pic" href="<?php $this->getTargetHref($archivesHref)?>" title="<?php echo $_rank['d_nickname']?>" target="<?php echo $this->target?>"><img src="<?php echo $avatar?>"></a>
                            <p class="fleft rank-text">
                                <a class="ellipsis pink" href="<?php $this->getTargetHref($archivesHref)?>" title="<?php echo $_rank['d_nickname']?>" target="<?php echo $this->target?>"><?php echo $_rank['d_nickname']?></a>
                                <span>点歌数+<?php echo $_rank['num']?></span>
                            </p>
                        </li>
 <?php 
endforeach;
?>                          
                    
                    </ul>
                </div><!--.rankcon-->
                <div class="rankcon none">
<?php 
if($rightData['dotey_songs_rank']['week']):
	$topWeekData = array_shift($rightData['dotey_songs_rank']['week']);
	$avatar = $topWeekData['d_avatar'];
	$archivesHref = 'index.php?r=archives/index/uid/'.$topWeekData['d_uid'];
	if($this->isLogin){
		$weiboService = new WeiboService();
		$isAttentTion = $weiboService->isAttentionDotey($topWeekData['d_uid'],Yii::app()->user->id);
		$attentTionClass = $isAttentTion ? 'cancelatt' : '';
		$jsMethod = $isAttentTion ? 'cacnelAttentionUser' : 'attentionUser';
	}else{
		$attentTionClass = '';
		$jsMethod = 'attentionUser';
	}
?>	             	
                	<dl class="conbox-list">
                        <dt>
                            <a title="<?php echo $topWeekData['d_nickname']?>" href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><img src="<?php echo $avatar?>"></a>
                            <a title="关注" href="javascript:void(0)" class="attent <?php echo $attentTionClass?>" style="display: none;" onclick="$.User.<?php echo $jsMethod?>('<?php echo $topWeekData['d_uid']?>',this,'single');"></a>
                        </dt>
                        <dd>
                            <a title="<?php echo $topWeekData['d_nickname']?>" class="pink name ellipsis" href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><?php echo $topWeekData['d_nickname']?></a>
                            <p class="toper clearfix"><span class="fleft">点歌数+<?php echo $topWeekData['num']?></span></p> 
                        </dd>
                    </dl>
 <?php 
endif;
?>                   
                    <ul class="rankconpic-list">
<?php 
$i = 1;
foreach($rightData['dotey_songs_rank']['week'] as $key=>$_rank):
$i++;
$avatar = $_rank['d_avatar'];
$archivesHref = 'index.php?r=archives/index/uid/'.$_rank['d_uid'];

?>                   
                        <li class="clearfix">
                            <em class="fleft top2 order"><?php echo $i?></em>
                            <a class="fleft rank-pic" href="<?php $this->getTargetHref($archivesHref)?>" title="<?php echo $_rank['d_nickname']?>" target="<?php echo $this->target?>"><img src="<?php echo $avatar?>"></a>
                            <p class="fleft rank-text">
                                <a class="ellipsis pink" href="<?php $this->getTargetHref($archivesHref)?>" title="<?php echo $_rank['d_nickname']?>" target="<?php echo $this->target?>"><?php echo $_rank['d_nickname']?></a>
                                <span>点歌数+<?php echo $_rank['num']?></span>
                            </p>
                        </li>
 <?php 
endforeach;
?>                          
                    
                    </ul>
                </div><!--.rankcon-->
                <div class="rankcon none">
 <?php 
if($rightData['dotey_songs_rank']['month']):
	$topMonthData = array_shift($rightData['dotey_songs_rank']['month']);
	$avatar = $topMonthData['d_avatar'];
	$archivesHref = 'index.php?r=archives/index/uid/'.$topMonthData['d_uid'];
	if($this->isLogin){
		$weiboService = new WeiboService();
		$isAttentTion = $weiboService->isAttentionDotey($topMonthData['d_uid'],Yii::app()->user->id);
		$attentTionClass = $isAttentTion ? 'cancelatt' : '';
		$jsMethod = $isAttentTion ? 'cacnelAttentionUser' : 'attentionUser';
	}else{
		$attentTionClass = '';
		$jsMethod = 'attentionUser';
	}
?>	             	
                	<dl class="conbox-list">
                        <dt>
                            <a title="<?php echo $topMonthData['d_nickname']?>" href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><img src="<?php echo $avatar?>"></a>
                           <a title="关注" href="javascript:void(0)" class="attent <?php echo $attentTionClass?>" style="display: none;" onclick="$.User.<?php echo $jsMethod?>('<?php echo $topMonthData['d_uid']?>',this,'single');"></a>
                        </dt>
                        <dd>
                            <a title="<?php echo $topMonthData['d_nickname']?>" class="pink name ellipsis" href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><?php echo $topMonthData['d_nickname']?></a>
                            <p class="toper clearfix"><span class="fleft">点歌数+<?php echo $topMonthData['num']?></span></p> 
                        </dd>
                    </dl>
 <?php 
endif;
?>                   
                    <ul class="rankconpic-list">
<?php 
$i = 1;
foreach($rightData['dotey_songs_rank']['month'] as $key=>$_rank):
$i++;
$avatar = $_rank['d_avatar'];
$archivesHref = 'index.php?r=archives/index/uid/'.$_rank['d_uid'];
?>                   
                        <li class="clearfix">
                            <em class="fleft top2 order"><?php echo $i?></em>
                            <a class="fleft rank-pic" href="<?php $this->getTargetHref($archivesHref)?>" title="<?php echo $_rank['d_nickname']?>" target="<?php echo $this->target?>"><img src="<?php echo $avatar?>"></a>
                            <p class="fleft rank-text">
                                <a class="ellipsis pink" href="<?php $this->getTargetHref($archivesHref)?>" title="<?php echo $_rank['d_nickname']?>" target="<?php echo $this->target?>"><?php echo $_rank['d_nickname']?></a>
                                <span>点歌数+<?php echo $_rank['num']?></span>
                            </p>
                        </li>
 <?php 
endforeach;
?>                          
                    
                    </ul>               	
                </div><!--.rankcon-->
                <div class="rankcon none">
<?php 
if($rightData['dotey_songs_rank']['super']):
	$topSuperData = array_shift($rightData['dotey_songs_rank']['super']);
	$avatar = $topSuperData['d_avatar'];
	$archivesHref = 'index.php?r=archives/index/uid/'.$topSuperData['d_uid'];
	if($this->isLogin){
		$weiboService = new WeiboService();
		$isAttentTion = $weiboService->isAttentionDotey($topSuperData['d_uid'],Yii::app()->user->id);
		$attentTionClass = $isAttentTion ? 'cancelatt' : '';
		$jsMethod = $isAttentTion ? 'cacnelAttentionUser' : 'attentionUser';
	}else{
		$attentTionClass = '';
		$jsMethod = 'attentionUser';
	}
?>	             	
                	<dl class="conbox-list">
                        <dt>
                            <a title="<?php echo $topSuperData['d_nickname']?>" href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><img src="<?php echo $avatar?>"></a>
                           <a title="关注" href="javascript:void(0)" class="attent <?php echo $attentTionClass?>" style="display: none;" onclick="$.User.<?php echo $jsMethod?>('<?php echo $topSuperData['d_uid']?>',this,'single');"></a>
                        </dt>
                        <dd>
                            <a title="<?php echo $topSuperData['d_nickname']?>" class="pink name ellipsis" href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><?php echo $topSuperData['d_nickname']?></a>
                            <p class="toper clearfix"><span class="fleft">点歌数 <?php echo $topSuperData['num']?></span></p> 
                        </dd>
                    </dl>
 <?php 
endif;
?>                   
                    <ul class="rankconpic-list">
<?php 
$i = 1;
foreach($rightData['dotey_songs_rank']['super'] as $key=>$_rank):
$i++;
$avatar = $_rank['d_avatar'];
$archivesHref = 'index.php?r=archives/index/uid/'.$_rank['d_uid'];
?>                   
                        <li class="clearfix">
                            <em class="fleft top2 order"><?php echo $i?></em>
                            <a class="fleft rank-pic" href="<?php $this->getTargetHref($archivesHref)?>" title="<?php echo $_rank['d_nickname']?>" target="<?php echo $this->target?>"><img src="<?php echo $avatar?>"></a>
                            <p class="fleft rank-text">
                                <a class="ellipsis pink" href="<?php $this->getTargetHref($archivesHref)?>" title="<?php echo $_rank['d_nickname']?>" target="<?php echo $this->target?>"><?php echo $_rank['d_nickname']?></a>
                                <span>点歌数 <?php echo $_rank['num']?></span>
                            </p>
                        </li>
 <?php 
endforeach;
?>                          
                    
                    </ul>
                </div><!--.rankcon-->
            </div><!--.conbox-->
<?php 
$webConfigSer = new WebConfigService();
$keyInfo = $webConfigSer->getChannelSymbol();
$keyInfo = $keyInfo['c_value'];
$keyInfo['sing_general']['pic'] = Yii::app()->params->images_server['url']."/".$keyInfo['sing_general']['pic'];
$keyInfo['sing_area']['pic'] = Yii::app()->params->images_server['url']."/".$keyInfo['sing_area']['pic'];
?>
            <div class="rlist-btm clearfix">
                <h2 class="fleft">唱将勋章</h2>
                <span class="fleft mt10 rbtm-pic"><Img src="<?php echo $keyInfo['sing_general']['pic'];?>"></span>
                <p class="fleft mt10 rbtm-con">周榜中获得排名前十的主播家可在下周一获得唱将图标7天</p>
           	</div><!--.rlist-btm-->
        </div><!--.rankbox-->