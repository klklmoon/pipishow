<style type="text/css">
/*.playing*/
.playing,.readying{position:absolute; bottom:46px; right:0px; height:24px; line-height:22px; text-indent:24px; color:#fff;}
.playing{width:70px; background-position:0 -566px; }
.readying{padding-right:8px;background-position:0 -600px; }

/*.errorbox*/
.errorbox{ padding-top:170px;}
.errorbox-bd{width:400px; margin:0 auto;}
.errorbox-bd dt{ margin-right:20px;}
.errorbox-bd dt,.errorbox-bd dd{float:left;}
.errorbox-bd dd{width:280px;}
.errortext{font-size:30px; color:#ff008a; font-family:"Microsoft YaHei","\5FAE\8F6F\96C5\9ED1";}
.back{font-size:20px;font-family:"Microsoft YaHei","\5FAE\8F6F\96C5\9ED1";}

/*.live-error*/
.live-error{width:430px; height:250px; margin:0 auto;}
.live-t{font-size:20px; color:#ff008a;font-family:"Microsoft YaHei","\5FAE\8F6F\96C5\9ED1"; height:50px; line-height:50px;}
.live-list{width:420px;}
.live-list li{ float:left; width:190px; height:158px; margin-right:20px; position:relative;}
.anchor-pic{ display:block; position:relative;}
.anchor-pic img{width:190px; height:112px;}
.anchor-name{ display:block; height:24px; line-height:30px; text-align:center;}
.time{text-align:center;}
.playstate{width:54px; height:54px; position:absolute; top:28px; left:68px; background:url(<?php echo $this->pipiFrontPath?>/fontimg/common/playstate.png) no-repeat 0 0;}
.pinkstate{background:url(<?php echo $this->pipiFrontPath?>/fontimg/common/playstate.png) no-repeat 0 -64px;}

/*.live-hd*/
.live-hd{width:430px; height:20px; margin-top:20px;}
.liveHd-list{float:left; +width:90px; position:relative; left:50%; padding-top:6px;}
.liveHd-list li{float:left; text-indent:-9999px; width:7px; height:7px; cursor:pointer; margin-right:10px; background:url(<?php echo $this->pipiFrontPath?>/fontimg/common/sprite.png) no-repeat 0 -424px; text-align:center; position:relative; left:-50%;}
.liveHd-list li.on{background-position:0 -441px;}
</style>
<div class="w1000 errorbox">
	<dl class="errorbox-bd clearfix">
    	<dt><Img src="<?php echo $this->pipiFrontPath?>/fontimg/common/404error2-bg.jpg"></dt>
        <dd class="errortext">这个真没有</dd>
        <dd><a class="back" href="#" title="404错误，文件或目录未找到。">404错误，文件或目录未找到。</a></dd>
    </dl>
</div><!--.errorbox-->

<div class="live-error">
	<p class="live-t">这个可以有~~~看看美女主播</p>
    <div class="live-bd">
    	<ul class="live-list clearfix">
<?php 
$archiveService = new ArchivesService();
$living = $archiveService->getLivingArchives(Yii::app()->user->id,true,false);
if(isset($living['living'])):
foreach($living['living'] as $_live):
$archivesHref = 'index.php?r=archives/index/uid/'.$_live['uid'];
?>    	
        	<li>
        	 <a  class="anchor-pic" href="<?php $this->getTargetHref($archivesHref)?>"  target="<?php echo $this->target?>">
        		 <img src="<?php echo $_live['display_small']?>"/>
        		 <span class="playstate"></span>
        	 </a>
                <a class="anchor-name pink"  href="<?php $this->getTargetHref($archivesHref)?>"  target="<?php echo $this->target?>"><?php echo $_live['title']?></a>
               <p class="time"><?php echo $_live['live_desc'][0]?> </p>
                <p class="playing">直播中</p>
                
            </li>
 <?php 
endforeach;
endif;
?>          
        </ul>
    </div><!--.live-bd-->
    <div class="live-hd">
    	<ul class="liveHd-list clearfix">
<?php 
if(isset($living['living'])):
foreach($living['living'] as $key=>$_live):
?>  	
        	<li class="on"><?php ++$key?></li>
 <?php 
endforeach;
endif;
?>  
        </ul>
    </div><!--.live-hd-->
</div><!--.live-error-->

<script type="text/javascript">
$(function(){
	$('.anchor-pic').hover(function(){
		$(this).find('.playstate').addClass('pinkstate');
	},function(){
		$(this).find('.playstate').removeClass('pinkstate');
	});	
	$(".live-error").slide({titCell:".live-hd ul",mainCell:".live-bd ul",autoPage:true,effect:"left",autoPlay:true,interTime:2000,vis:2});
});
</script>