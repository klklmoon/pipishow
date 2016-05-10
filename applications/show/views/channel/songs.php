<?php 
$operateUrl = $this->viewer['operateUrl'];
?>
<h1 class="w1000 mt20 clearfix">
	<span class="songh fleft">点唱专区</span>
	<!-- 
    <a title="申请加入" class="fleft pinkbtn" href="#"><span>申请加入</span></a>
     -->
</h1>

<div class="w1000 clearfix">
	<div class="fleft song-lbox">
        <div class="topflash clearfix">
        
            <div class="fleft topflash-con">
            	<div class="topflash-hd">
                	<span class="fleft prev"></span>
                    <ul class="fleft">
<?php 
	        	$hasShowRecommand = isset($this->viewer['operate'][CATEGORY_CHANNEL_SONG_CAROUSEL]);
	        	if($hasShowRecommand):
	        		$recommands = $this->viewer['operate'][CATEGORY_CHANNEL_SONG_CAROUSEL];
	        		$i = 0;
	        		foreach($recommands as $recommand):
	        			$on =  $i== 0 ?	'on' : '';
	        			$i++;
?>                   
                    	<li class="<?php echo $on?>"><?php echo $i?></li>
<?php 
           			 endforeach;
           		endif;
?>
                    </ul>
                    <span class="fleft next"></span>
                </div>
                <div class="topflash-bd">
                	<ul>
                	<?php 
			        	if($hasShowRecommand):
			        		foreach($recommands as $recommand):
			        		$recommand['piclink'] = $operateUrl.$recommand['piclink'];
			        ?>
                    	<li>
                    		<a href="<?php echo $this->getTargetHref($recommand['textlink'],false,false);?>" title="<?php echo $recommand['subject']?>" target="<?php echo $this->target?>">
                    		<img src="<?php echo $recommand['piclink']?>">
                    		</a>	
                    	</li>
                    	
                    <?php 
           				 endforeach;
           			 endif;
           			?>
                    </ul>
                </div>
            </div>
           	
            <div class="fleft channel-nte">
            	<h2 class="clearfix"><span class="fleft">频道公告</span></h2>
            	 <?php 
	        	$hasNotice = isset($this->viewer['operate'][CATEGORY_CHANNEL_SONG_NOTICE]);
	        	$notices =  array();
	        	if($hasNotice):
	        		$notices = $this->viewer['operate'][CATEGORY_CHANNEL_SONG_NOTICE];
	        		$topNotice = array_shift($notices);	
	        	?>
                <p class="nte-des"><a  href="<?php $this->getTargetHref($topNotice['textlink']);?>" target="<?php echo $this->target?>"><?php echo $topNotice['subject'];?></a></p>
                <?php endif;?>
                <ul class="nte-list">
                <?php 
	        	if($notices):
	        		foreach($notices as $notice):
        		?>
                	<li><a href="<?php $this->getTargetHref($notice['textlink']);?>" title="<?php echo $notice['subject']?>" target="<?php echo $this->target?>"><?php echo $notice['subject']?></a></li>
               <?php 
           		 	endforeach;
            	endif;
            	?>
                </ul>
            </div><!--.channel-nte-->
        </div><!--.topflash-->
        
        <div class="satebox mt10" id="doteyData">
            <div class="satetab clearfix">
            	<dl class="fleft">
                	<dt>状态:</dt>
                	<dd class="<?php echo $this->viewer['typeSelect'][0]?>"><a  href="javascript:void(0)" title="不限" onclick="return searchCondition('<?php echo $searchUrl?>','type',0)">不限</a></dd>
                    <dd class="<?php echo $this->viewer['typeSelect'][1]?>"><a  href="javascript:void(0)" title="正在直播" onclick="return searchCondition('<?php echo $searchUrl?>','type',1)">正在直播</a></dd>
                    <dd class="<?php echo $this->viewer['typeSelect'][2]?>"><a  href="javascript:void(0)" title="即将开播" onclick="return searchCondition('<?php echo $searchUrl?>','type',2)">即将开播</a></dd>
                   
                </dl>
                <dl class="fleft">
                	<dt>排序:</dt>
                    <dd class="<?php echo $this->viewer['orderSelect'][6]?>"><a href="javascript:void(0)" title="默认" onclick="return searchCondition('<?php echo $searchUrl?>','order',6)">默认</a></dd>
                    <dd class="<?php echo $this->viewer['orderSelect'][4]?>"><a href="javascript:void(0)" title="点唱数" onclick="return searchCondition('<?php echo $searchUrl?>','order',4)">点唱数</a></dd>
                    <dd class="<?php echo $this->viewer['orderSelect'][2]?>"><a href="javascript:void(0)" title="魅力等级" onclick="return searchCondition('<?php echo $searchUrl?>','order',2)">魅力等级</a></dd>
                    <dd class="<?php echo $this->viewer['orderSelect'][3]?>"><a href="javascript:void(0)" title="开播时间" onclick="return searchCondition('<?php echo $searchUrl?>','order',3)">开播时间</a></dd>
                </dl>
            </div>
            <?php 
            	$this->renderPartial('application.views.index2.liveArchivesTemplate2',$songArchives);
            ?>
        </div><!--.satebox-->
        <p class="satebox-btm"></p>
        <!--  
        <p class="page none" style="display: block;">
                    <a class="prev" title="上一页" href="#">上一页</a>
                    <a class="pagenum pageon" title="1" href="#">1</a>
                    <a class="pagenum" title="1" href="#">2</a>
                    <a class="pagenum" title="1" href="#">3</a>
                    <a class="pagenum" title="1" href="#">5</a>
                    <a class="pagenum" title="1" href="#">6</a>
                    <a class="pagenum" title="1" href="#">7</a>
                    <a class="pagenum" title="1" href="#">8</a>
                    <a class="pagenum" title="1" href="#">9</a>
                    <a class="pagenum" title="1" href="#">10</a>
                    <a class="next" title="下一页" href="#">下一页</a>
        </p>
        -->
    </div><!--.song-lbox-->
    
    <div class="fright song-rbox" id="top_rank">
        <?php 
    		$this->renderPartial('application.views.channel.songsright',array('rightData'=>$rightData)); 
   		?>
    </div><!--.song-rbox-->
    
</div><!--.w1000-->

<script type="text/javascript">
$(function(){
	/*幻灯片调用*/
	$(".topflash-con").slide({titCell:".topflash-hd ul",mainCell:".topflash-bd ul",autoPage:true,autoPlay:true,delayTime:0});	
	
	/*右侧选卡效果*/
	RankTab('#RichTab a');
	RankTab('#JukeTab a');
	function RankTab(tabId){
		$(tabId).live('click',function(){
			var index=$(this).index(tabId);
			$(this).addClass('starttabover').siblings().removeClass('starttabover');
			$(this).parent().siblings('.conbox').find('.rankcon').eq(index).show().siblings('.rankcon').hide();	
		});		
	}
	
	/*鼠标放头像显示关注*/
	showattent('.conbox-list dt');
	
	$('.anchor-con li').hover(function(){
	$(this).find('.anchor-head').addClass('chorover');
	$(this).find('.attent').show().hover(function(){
		$(this).find('.attent-text').show();
	},function(){
		$(this).find('.attent-text').hide();	
	});
},function(){
	$(this).find('.anchor-head').removeClass('chorover');
	$(this).find('.attent').hide();
	
});

setInterval(function(){
		$.ajax({
			type : 'post',
			url : 'index.php?r=channel/ajaxRight/',
			success:function(data){
				$('#top_rank').html(data);
			}
		});	
		
},1000*60*3);

});


var qcondition = {
	type:/&type\s*=\s*\d*/i,
	order:/&order\s*=\s*\d*/i,
	strings:/&strings\s*=\s*[a-z]*/i
};


function searchCondition(href,key,value){
	href = href.replace(qcondition[key],'');
	if(value !='' && value != null)
	href += '&'+key+'='+value;
	location.href = href+'#doteyData';
	return false;
}
</script>
