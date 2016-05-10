<?php 
$channels = $this->viewer['channels'];
$themeChannel = $channels[CHANNEL_THEME];
$categoryChannel = $channels[CHANNEL_AREA];
?>
<div class="w1000 clearfix">
    <div class="fleft sort-l">
    	<div class="topbox-l">
        	<h1>互动综艺第一平台</h1>
            <ul class="area-banner">
				<li><a class="classbtn"
					href="<?php $this->getTargetHref('index.php?r=channel/category');?>"
					title="节目分类" target="<?php echo $this->target;?>">节目分类</a></li>            
            	<li><a class="jukebtn" href="<?php $this->getTargetHref('index.php?r=channel/songs')?>"  target="<?php echo $this->target?>" ><?php echo $themeChannel[CHANNEL_THEME_SONG]['sub_name']?></a></li>
            	<!--  
                <li><a href="#" title="情感故事">情感故事</a></li>
                -->
            </ul>
            <!-- 
            <dl class="area-con">
            	<dt>游戏</dt>
                <dd><a href="#" title="幸运沙发">幸运沙发</a></dd>
                <dd><a href="#" title="砸金蛋">砸金蛋</a></dd>
            </dl>
             -->
        </div><!--.topbox-l-->
        
        <div class="rankbox mt10" id="leftFansTop">
     	<?php 
    		$this->renderPartial('application.views.channel.categoryleft',array('leftData'=>$leftData)); 
   		?>
        </div><!--.rankbox-->
        
    </div><!--.sort-l-->
    
    <div class="fright sort-r" id='doteyData'>
    	<div class="satetab clearfix">
        	 <div class="satetab-t">
                 <dl class="fleft">
                      <dt>地区:</dt>
                      <dd class="<?php echo $this->viewer['q']['area'][0]?>"><a title="不限" href="javascript:void(0)" onclick="return searchCondition('<?php echo $searchUrl?>','area',0)">不限</a></dd>
                      <?php foreach ($categoryChannel as $_channel):?>
                      <dd class="<?php echo $this->viewer['q']['area'][$_channel['sub_channel_id']]?>"><a title="<?php echo $_channel['sub_name']?>" href="javascript:void(0)" onclick="return searchCondition('<?php echo $searchUrl?>','area',<?php echo $_channel['sub_channel_id']?>)"><?php echo $_channel['sub_name']?></a></dd>
                      <?php endforeach;?>
                  </dl>
                  <dl class="fleft">
                      <dt>等级:</dt>
                      <dd class="<?php echo $this->viewer['q']['rank'][0]?>"><a title="不限" href="javascript:void(0)" onclick="return searchCondition('<?php echo $searchUrl?>','rank',0)">不限</a></dd>
                      <dd class="<?php echo $this->viewer['q']['rank'][1]?>"><a title="皇冠" href="javascript:void(0)" onclick="return searchCondition('<?php echo $searchUrl?>','rank',1)">皇冠</a></dd>
                      <dd class="<?php echo $this->viewer['q']['rank'][2]?>"><a title="钻石" href="javascript:void(0)" onclick="return searchCondition('<?php echo $searchUrl?>','rank',2)">钻石</a></dd>
                      <dd class="<?php echo $this->viewer['q']['rank'][3]?>"><a title="红心" href="javascript:void(0)" onclick="return searchCondition('<?php echo $searchUrl?>','rank',3)">红心</a></dd>
                  </dl>
             </div><!--.satetab-t-->
             <p class="mline"></p>
              <dl class="fleft">
                  <dt>状态:</dt>
                  <dd class="<?php echo $this->viewer['q']['status'][0]?>"><a title="不限" href="javascript:void(0)"     onclick="return searchCondition('<?php echo $searchUrl?>','status',0)">不限</a></dd>
                  <dd class="<?php echo $this->viewer['q']['status'][1]?>"><a title="正在直播" href="javascript:void(0)"  onclick="return searchCondition('<?php echo $searchUrl?>','status',1)">正在直播</a></dd>
                  <dd class="<?php echo $this->viewer['q']['status'][2]?>"><a title="即将开播" href="javascript:void(0)"  onclick="return searchCondition('<?php echo $searchUrl?>','status',2)">即将开播</a></dd>
                 
              </dl>
              <dl class="fleft">
                  <dt>排序:</dt>
                  <dd class="<?php echo $this->viewer['q']['order'][6]?>"><a title="默认" href="javascript:void(0)" onclick="return searchCondition('<?php echo $searchUrl?>','order',6)">默认</a></dd>
                  <dd class="<?php echo $this->viewer['q']['order'][2]?>"><a title="主播等级" href="javascript:void(0)" onclick="return searchCondition('<?php echo $searchUrl?>','order',2)">主播等级</a></dd>
                  <dd class="<?php echo $this->viewer['q']['order'][3]?>"><a title="开播时间" href="javascript:void(0)" onclick="return searchCondition('<?php echo $searchUrl?>','order',3)">开播时间</a></dd>
                  <dd class="<?php echo $this->viewer['q']['order'][1]?>"><a title="连播天数" href="javascript:void(0)" onclick="return searchCondition('<?php echo $searchUrl?>','order',1)">连播天数</a></dd>
              </dl>
         </div><!--.satetab-->
         <div class="satebox mt10">
        	 <?php 
            	$this->renderPartial('application.views.index2.liveArchivesTemplate2',$songArchives);
            ?>
        </div>
        <p class="satebox-btm"></p>
        <!--.
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
    </div><!--.sort-r-->
    
</div><!--.w1000-->

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
	
	/*左侧选卡效果*/
	RankTab('#FansTab a');
	function RankTab(tabId){
		$(tabId).bind('click',function(){
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
});

var qcondition = {
		area:/&type\s*=\s*\d*/i,
		rank:/&rank\s*=\s*\d*/i,
		status:/&status\s*=\s*\d*/i,
		order:/&order\s*=\s*\d*/i
	};


	function searchCondition(href,key,value){
		href = href.replace(qcondition[key],'');
		if(value !='' && value != null)
		href += '&'+key+'='+value;
		location.href = href+'#doteyData';
		return false;
	}
</script>