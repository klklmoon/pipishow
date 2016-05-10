<div class="w1000 clanNav-con clearfix">
    <div class="fleft clanLR-btn">
        <a href="<?php echo $this->createUrl('family/index');?>" class="clanL-btn">家族首页</a>
        <a href="<?php echo $this->createUrl('family/myFamily');?>" class="clanR-btn">我的家族</a>
    </div><!--.clanLR-btn-->
    <a class="fright gray-btn" href="<?php echo $this->createUrl('family/apply');?>">创建家族<em class="beta"></em></a></a>
</div><!--.clanNav-con-->
<?php /*
<div class="w1000 pattern-box clearfix">
    <div class="fleft" id="dedication">
        <div class="pattern-list">
            <h4>
                    <i class="banericon"></i>
                    <span class="fleft pink">家族贡献榜</span>
                    <p class="tip-text relative">
                        <em>?</em>
                        <span class="tipcon" style="display: none;">根据家族所有成员的累计贡献值排序</span>
                    </p>
                    <p id="charmrank" class="fright mr5 start-tab clearfix">
                        <a title="今日" href="javascript:void(0);" class="starttabover">今日</a><em>|</em>
                        <a title="本周" href="javascript:void(0);">本周</a><em>|</em>
                        <a title="本月" href="javascript:void(0);">本月</a><em>|</em>
                        <a title="超级" href="javascript:void(0);">超级</a>
                    </p>
            </h4>
            <ul class="pattern-con a0">
            <?php $this->renderPartial('application.views.family.top', array('top' => $top_dedication)); ?>
            </ul>
            <ul class="pattern-con a1" style="display:none"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>
            <ul class="pattern-con a2" style="display:none"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>
            <ul class="pattern-con a3" style="display:none"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>
        </div><!--.pattern-list-->
        <p class="pattern-btm"></p>
    </div>
    <div class="fleft mr10 ml10" id="recharge">
        <div class="pattern-list">
            <h4>
                    <i class="banericon"></i>
                    <span class="fleft pink">家族财富榜</span>
                    <p class="tip-text relative">
                        <em>?</em>
                        <span class="tipcon" style="display: none;">按家族成员的累计充值排序</span>
                    </p>
                    <p id="charmrank" class="fright mr5 start-tab clearfix">
                        <a title="今日" href="javascript:void(0);" class="starttabover">今日</a><em>|</em>
                        <a title="本周" href="javascript:void(0);">本周</a><em>|</em>
                        <a title="本月" href="javascript:void(0);">本月</a><em>|</em>
                        <a title="超级" href="javascript:void(0);">超级</a>
                    </p>
            </h4>
            <ul class="pattern-con a0">
            <?php $this->renderPartial('application.views.family.top', array('top' => $top_recharge)); ?>
            </ul>
            <ul class="pattern-con a1" style="display:none"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>
            <ul class="pattern-con a2" style="display:none"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>
            <ul class="pattern-con a3" style="display:none"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>
        </div><!--.pattern-list-->
        <p class="pattern-btm"></p>
    </div>
    <div class="fleft" id="medal">
        <div class="pattern-list">
            <h4>
                    <i class="banericon"></i>
                    <span class="fleft pink">家族徽章榜</span>
                    <p class="tip-text relative">
                        <em>?</em>
                        <span class="tipcon" style="display: none;">按家族族徽数量排序</span>
                    </p>
                    <p id="charmrank" class="fright mr5 start-tab clearfix">
                        <a title="今日" href="javascript:void(0);" class="starttabover">今日</a><em>|</em>
                        <a title="本周" href="javascript:void(0);">本周</a><em>|</em>
                        <a title="本月" href="javascript:void(0);">本月</a><em>|</em>
                        <a title="超级" href="javascript:void(0);">超级</a>
                    </p>
            </h4>
            <ul class="pattern-con a0">
            <?php $this->renderPartial('application.views.family.top', array('top' => $top_medal)); ?>
            </ul>
            <ul class="pattern-con a1" style="display:none"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>
            <ul class="pattern-con a2" style="display:none"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>
            <ul class="pattern-con a3" style="display:none"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>
        </div><!--.pattern-list-->
        <p class="pattern-btm"></p>
    </div>
</div><!--.pattern-box-->
*/ ?>
<div class="satebox mt10">
    <div class="satetab clearfix">
        <dl class="fleft">
            <dt>排序:</dt>
            <dd<?php if($type == 'member'){?> class="on"<?php }?>><a href="<?php echo $this->createUrl('family/index', array('type' => 'member'));?>" title="族员人数">族员人数</a></dd>
            <dd<?php if($type == 'dotey'){?> class="on"<?php }?>><a href="<?php echo $this->createUrl('family/index', array('type' => 'dotey'));?>" title="主播人数">主播人数</a></dd>
            <dd<?php if($type == 'time'){?> class="on"<?php }?>><a href="<?php echo $this->createUrl('family/index', array('type' => 'time'));?>" title="创建时间">创建时间</a></dd>
        </dl>
    </div>
    <ul class="anchor-con mt10 clearfix">
    	<?php foreach($list['list'] as $f){ ?>
        <li>
            <div class="anchor-head">
                <a href="<?php echo $this->createHomeUrl($f['id']);?>" title="<?php echo $f['name'];?>"><img src="/images/family/<?php echo $f['id'];?>/<?php echo $f['cover'];?>"></a>
            </div>
            <p class="chorname clearfix">
                <span class="fleft pattern-lvr">
                    <img src="/images/family/<?php echo $f['id'];?>/medal_<?php echo $f['sign'] == 1 ? '0' : $f['level'];?>3.jpg" />
                </span>
                <?php if($f['sign'] == 1){?><img class="fleft" style="margin:7px 5px 0 0 ;" src="<?php echo $this->pipiFrontPath;?>/fontimg/family/qianyue-btn.jpg"><?php }?>
                <a href="<?php echo $this->createHomeUrl($f['id']);?>" class="fleft nambtm pink"><?php echo $f['name'];?></a>
            </p>
            <p class="patternBtm-text"><em>族长：</em><?php echo $f['nickname'];?></p>
            <p class="patternBtm-text"><em>主播：</em><?php echo $f['doteys'];?></p>
            <p class="patternBtm-text"><em>成员：</em><?php echo $f['members'];?></p>
        </li>
        <?php } ?>
    </ul>
    <?php $this->widget('lib.widgets.family.LinkPager', array('pages'=>$list['pages']));?>
</div><!--.satebox-->
<p class="patternOrder-btm"></p>
<script type="text/javascript">
$(function(){
    //提示跳框
    $('.tip-text').hover(function(){
        $(this).find('.tipcon').css('display','block');
    },function(){
        $(this).find('.tipcon').css('display','none');
    });

    //左侧主播列表提示框
    $('.juke-icon,.cakeIcon').bind({
        mouseover:function(){
            $(this).find('em').css('display','block');
            $(this).parent('.anchor-head').addClass('z30');
        },
        mouseout:function(){
            $(this).find('em').css('display','none');
            $(this).parent('.anchor-head').removeClass('z30');
        }
    });

    $('#dedication .start-tab a').click(function(i){
		var n = $('#dedication .start-tab a').index($(this));
		$('#dedication .start-tab a').removeClass('starttabover');
		$(this).addClass('starttabover');
		getTop('dedication', n);
    });

    $('#recharge .start-tab a').click(function(i){
		var n = $('#recharge .start-tab a').index($(this));
		$('#recharge .start-tab a').removeClass('starttabover');
		$(this).addClass('starttabover');
		getTop('recharge', n);
    });

    $('#medal .start-tab a').click(function(i){
		var n = $('#medal .start-tab a').index($(this));
		$('#medal .start-tab a').removeClass('starttabover');
		$(this).addClass('starttabover');
		getTop('medal', n);
    });

});

var rank = {'dedication': new Array(), 'recharge': new Array(), 'medal': new Array()};
var interval = 5000;
for(k in rank){
	rank[k][0] = new Date().getTime();
}
function getTop(type, date_type){;
	if(!rank[type][date_type] || rank[type][date_type] < new Date().getTime() - interval){
		var dtype = 'day';
		if(date_type == 1) dtype = 'week';
		else if(date_type == 2) dtype = 'month';
		else if(date_type == 3) dtype = 'super';
		else dtype = 'day';
		
		$.ajax({
			url : "index.php?r=family/top",
			type : "GET",
			data:{'type':type, 'date':dtype},
			dataType : "html",
			success : function(html){
				$('#'+type+' .a'+date_type).html(html);
		 	}
		});
		rank[type][date_type] = new Date().getTime();
	}
	$('#'+type+' .pattern-con').css('display', 'none');
	$('#'+type+' .a'+date_type).css('display', 'block');
}
</script>