   	<h4 class="clearfix">
            	<i class="fleft banericon"></i>
            	<span class="fleft pink">粉丝榜</span>
                <a  href="<?php $this->getTargetHref('index.php?r=top/index')?>" class="fleft more" target="<?php echo $this->target?>">更多</a>
            </h4>
            <p id="FansTab" class="start-tab clearfix">
                    <a title="新人主播" href="javascript:void(0);" class="starttabover">新人主播</a><em>|</em>
                    <a title="所有主播" href="javascript:void(0);">所有主播</a>
            </p>
            <div class="conbox">
            	<div class="rankcon">
<?php 
if($leftData['dotey_fans_rank']['new']):
	$topSuperData = array_shift($leftData['dotey_fans_rank']['new']);
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
                            <a title="<?php echo $topSuperData['d_nickname']?>" class="pink name" href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><?php echo $topSuperData['d_nickname']?></a>
                            <p class="toper clearfix"><span class="fleft">粉丝数 <?php echo $topSuperData['num']?></span></p> 
                        </dd>
                    </dl>
 <?php 
endif;
?>                   
                    <ul class="rankconpic-list">
<?php 
$i = 1;
foreach($leftData['dotey_fans_rank']['new'] as $key=>$_rank):
$i++;
if($i>10)
break;
$avatar = $_rank['d_avatar'];
$archivesHref = 'index.php?r=archives/index/uid/'.$_rank['d_uid'];
?>                   
                        <li class="clearfix">
                            <em class="fleft top2 order"><?php echo $i?></em>
                            <a class="fleft rank-pic" href="<?php $this->getTargetHref($archivesHref)?>" title="<?php echo $_rank['d_nickname']?>" target="<?php echo $this->target?>"><img src="<?php echo $avatar?>"></a>
                            <p class="fleft rank-text">
                                <a class="ellipsis pink" href="<?php $this->getTargetHref($archivesHref)?>" title="<?php echo $_rank['d_nickname']?>" target="<?php echo $this->target?>"><?php echo $_rank['d_nickname']?></a>
                                <span>粉丝数 <?php echo $_rank['num']?></span>
                            </p>
                        </li>
 <?php 
endforeach;
?>                          
                    
                    </ul>
                </div><!--.rankcon-->
                <div class="rankcon none">
<?php 
if($leftData['dotey_fans_rank']['super']):
	$topSuperData = array_shift($leftData['dotey_fans_rank']['super']);
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
                            <a title="<?php echo $topSuperData['d_nickname']?>" class="pink name" href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><?php echo $topSuperData['d_nickname']?></a>
                            <p class="toper clearfix"><span class="fleft">粉丝数 <?php echo $topSuperData['num']?></span></p> 
                        </dd>
                    </dl>
 <?php 
endif;
?>                   
                    <ul class="rankconpic-list">
<?php 
$i = 1;
foreach($leftData['dotey_fans_rank']['super'] as $key=>$_rank):
$i++;
if($i>10)
break;
$avatar = $_rank['d_avatar'];
$archivesHref = 'index.php?r=archives/index/uid/'.$_rank['d_uid'];
?>                   
                        <li class="clearfix">
                            <em class="fleft top2 order"><?php echo $i?></em>
                            <a class="fleft rank-pic" href="<?php $this->getTargetHref($archivesHref)?>" title="<?php echo $_rank['d_nickname']?>" target="<?php echo $this->target?>"><img src="<?php echo $avatar?>"></a>
                            <p class="fleft rank-text">
                                <a class="ellipsis pink" href="<?php $this->getTargetHref($archivesHref)?>" title="<?php echo $_rank['d_nickname']?>" target="<?php echo $this->target?>"><?php echo $_rank['d_nickname']?></a>
                                <span>粉丝数 <?php echo $_rank['num']?></span>
                            </p>
                        </li>
 <?php 
endforeach;
?>  
         </div>
</div>