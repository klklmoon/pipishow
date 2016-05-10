<?php 
if($rank):
$top = array_shift($rank);
$attentTionClass = $top['is_attention'] ? 'cancelatt' : '';
$jsMethod = $top['is_attention'] ? 'cacnelAttentionUser' : 'attentionUser';
$archivesHref = '/'.$top['d_uid'];
?>
<dl class="conbox-list">
    <dt>
        <a href="<?php $this->getTargetHref($archivesHref,true,false)?>" title="<?php echo $top['d_nickname']?>" target="<?php echo $this->target?>"><img src="<?php echo $top['d_avatar']?>"></a>
         <a class="attent <?php echo $attentTionClass?>" href="javascript:void(0);" title="关注" onclick="$.User.<?php echo $jsMethod?>('<?php echo $top['uid']?>',this,'single');"></a>
    </dt>
<dd>
    <a href="<?php $this->getTargetHref($archivesHref,true,false)?>" title="<?php echo $top['d_nickname']?>" target="<?php echo $this->target?>"><?php echo $top['d_nickname']?></a>
    <p><?php echo $desc?></p>
    <p class="toper clearfix">
    <span class="fleft"><?php echo $top['nickname']?></span><em class="fright lvlo lvlo-<?php echo $top['d_rank']?>"></em>
    </p> 
</dd>
</dl>
<?php 
endif;
?>
<ul class="rank">
<?php 
$i = 1;
foreach($rank as $key=>$_rank):
	if($key >= 9):
		break;
	endif;
	$archivesHref = '/'.$_rank['d_uid'];
?>
     <li class="clearfix">
        <em class="fleft order"><?php echo ++$i?></em>
        <a class="fleft rank-pic" href="<?php $this->getTargetHref($archivesHref,true,false)?>" title="<?php echo $_rank['d_nickname']?>" target="<?php echo $this->target?>"><img width="38px" height="40px" src="<?php echo $_rank['d_avatar']?>"></a>
        <p class="fleft rank-text">
            <a class="ellipsis pink" href="<?php $this->getTargetHref($archivesHref,true,false)?>" title="<?php echo $_rank['d_nickname']?>" target="<?php echo $this->target?>"><?php echo $_rank['d_nickname']?></a>
            <span><?php echo $_rank['nickname']?></span>
        </p>
        <em class="fright mt20 lvlo lvlo-<?php echo $_rank['d_rank']?>"></em>
    </li>  
<?php 
endforeach;
?>                
</ul>