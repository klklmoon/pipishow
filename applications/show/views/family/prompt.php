<?php
if($type == 'apply'){
	$title = '申请创建家族';
	$sub_title = '创建家族的条件：';
	$suffix = '';
}elseif($type == 'success'){
	$title = '祝贺，'.$family['name'].' 家族正式成立！';
	$sub_title = '';
	$suffix = '';
}elseif($type == 'refuse'){
	$title = '创建家族被拒绝';
	$sub_title = '拒绝原因：';
	$suffix = "*有疑问或投诉请您联系官方在线客服，三天后可重新发起创建家族申请。<br/>";
	if(!empty($qq)){
		$suffix .= '客服：<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin='.$qq.'&amp;site=qq&amp;menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=3:'.$qq.':45" style="vertical-align:middle;"></a><br/>';
	}
}elseif($type == 'hidden'){
	$title = '家族被管理员隐藏';
	$sub_title = '隐藏原因：';
	if(!empty($qq)){
		$suffix .= '客服：<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin='.$qq.'&amp;site=qq&amp;menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=3:'.$qq.':45" style="vertical-align:middle;"></a><br/>';
	}
}elseif($type == 'forbidden'){
	$title = '家族被封禁';
	$sub_title = '封禁原因：';
	if(!empty($qq)){
		$suffix .= '客服：<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin='.$qq.'&amp;site=qq&amp;menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=3:'.$qq.':45" style="vertical-align:middle;"></a><br/>';
	}
}else{
	$title = $sub_title = $suffix = '';
}
?>
<div class="w1000 bord mt40">
	<h1><?php echo empty($title) ? '温馨提示' : $title;?></h1>
	<?php if($type == 'success'){ ?>
	<div class="info_table" style="width:400px; margin-bottom:0;">
        <ul class="info_content clearfix">
            <li>
            	<label>家族长：</label>
            	<div class="long_filed">
            		<em class="lvlr lvlr-<?php echo $user['rk'];?> mr10"></em>
            		<span class="pink"><?php echo $user['nk'];?></span>
            		（<?php echo $user['uid'];?>）
            	</div>
            </li>
            <li>
            	<label>徽族：</label>
            	<div class="short_filed"><img src="/images/family/<?php echo $family['id'];?>/medal_<?php echo $family['level'];?>3.jpg" /></div>
            </li>
        </ul>
	</div>
	<div class="conditions" style="margin:0 auto">
        <center>
			<a class="cancel" href="<?php echo $this->createUrl('family/help');?>">家族帮助</a>
			&nbsp;
			<a class="cancel" href="<?php echo $this->createHomeUrl($family['id']);?>">进入家族主页</a>
		</center>
	</div>
	<?php }else{ ?>
	<div class="conditions">
		<strong><?php echo $sub_title?></strong><br/>
		<?php
	     	$i = 1;
	     	foreach($error as $e){
				if(is_array($e)){
					foreach($e as $msg){
						echo $i++."、".$msg."<br/>\n";
					}
				}else{
					echo $i++."、".$e."<br/>\n";
				}
			}
			echo $suffix;
		?>
		<center>
			<a class="cancel" href="<?php if(isset($url) && !empty($url)) echo $url; else{?>javascript:history.go(-1);<?php }?>">后　退</a>
			&nbsp;
			<a class="cancel" href="/">首　页</a>
		</center>
	</div>
	<?php } ?>
</div>