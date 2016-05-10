<?php echo $this->renderPartial('home_top', compact(array_keys(get_defined_vars())));?>
<div class="w1000 memberMesg-con clearfix">
    <div class="fleft memberMesgBd-l">
    	<div class="family-honour">
            <ul class="clearfix">
                <li>
                    <p>家族消费</p>
                    <span>富甲天下榜：第 <em><?php echo isset($top['dedication']) ? $top['dedication'] : '--';?></em> 名</span>
                </li>
                <li>
                    <p>家族人气</p>
                    <span>情谊无限榜：第 <em><?php echo isset($top['members']) ? $top['members'] : '--';?></em> 名</span>
                </li>
                <li>
                    <p>徽章成员</p>
                    <span>江湖至尊榜：第 <em><?php echo isset($top['medal']) ? $top['medal'] : '--';?></em> 名</span>
                </li>
                <?php if($family['sign']){?>
                <li>
                    <p>皇冠主播</p>
                    <span>巨星辉煌榜：第 <em><?php echo isset($top['rank1']) ? $top['rank3'] : '--';?></em> 名</span>
                </li>
                <li>
                    <p>钻石主播</p>
                    <span>明星闪耀榜：第 <em><?php echo isset($top['rank2']) ? $top['rank2'] : '--';?></em> 名</span>
                </li>
                <li>
                    <p>红心主播</p>
                    <span>新人人气榜：第 <em><?php echo isset($top['rank3']) ? $top['rank1'] : '--';?></em> 名</span>
                </li>
                <?php }?>
            </ul>
        </div>
        
        <div class="control-box familySubject">
            <h4 class="clearfix">
                <i class="banericon"></i>
                <span class="fleft pink">家族话题</span>
            </h4>
            <div class="famSubj-t">
            	<?php if($admin || $manager){?>
                <a class="gray-btn" href="javascript:void(0);" id="all">全选/反选</a>
                <a class="gray-btn" href="javascript:void(0);" id="threadTop">置顶/取消</a>
                <a class="gray-btn" href="javascript:void(0);" id="threadDelete">删除帖子</a>
                <a class="gray-btn" href="<?php echo $this->createUrl('family/adminBbs', array('family_id' => $family['id']));?>">发帖设置</a>
                <?php }?>
                <input class="fright shiftbtn" type="button" onclick="window.location.href='<?php echo $this->createUrl('family/sendThread',array('family_id' => $family['id']));?>';" value="我&nbsp;要&nbsp;发&nbsp;帖">
                <!--灰色按钮-->
                <!--<input class="fright gry-shiftbtn shiftbtn" type="button" value="发&nbsp;&nbsp;布">-->
            </div>
            <form id="myform" action="" method="post">
            <ul class="famSubj-list">
            	<?php foreach($threads['list'] as $t){?>
                <li>
                    <div class="small-head"><img src="<?php echo $t['pic'];?>"></div>
                    <dl class="famSubj-con">
                        <dt>
                        	<span><?php echo $t['nickname'];?></span>
                        	<em class="lvlr lvlr-<?php echo $t['rank'];?>"></em>
                        	<?php if(!empty($t['medal'])){?><img src="<?php echo $t['medal'];?>"><?php }?>
                        </dt>
                        <dd class="title">
                        <?php if($admin || $manager){?>
                        	<input type="checkbox" name="ids[]" value="<?php echo $t['thread_id'];?>" />
                        <?php }?>
                        <label><a href="<?php echo $this->createUrl('family/thread', array('family_id' => $family['id'], 'thread_id' => $t['thread_id']));?>"><?php echo PipiCommon::truncate_utf8_string($t['title'], 35);?></a></label>
                        <?php if($t['top']){?><img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/topicon.gif" /><?php }?>
                        <?php if($t['flag_hot']){?><img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/hoticon.gif" /><?php }?>
                        <?php if($t['flag_image']){?><img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/picicon.gif" /><?php }?>
                        </dd>
                        <dd class="time">
                        	<div class="fleft timeold"><span>发布于</span><p><?php echo date('n-d H:i', $t['create_time']);?></p></div>
                            <div class="fright timenew"><p title="回应数"><?php echo $t['posts'];?></p><p title="最后回复"><?php echo date('H:i', $t['last_reply_time']);?></p></div>
                        </dd>
                    </dl>
                </li>
                <?php }?>
            </ul>
           	</form>
            <?php $this->widget('lib.widgets.family.LinkPager', array('pages'=>$threads['pages']));?>
        </div>
        <p class="anchor-btm"></p><!--.familySubject-->
    </div>
    <?php echo $this->renderPartial('home_right', compact(array_keys(get_defined_vars())));?>
</div>
<?php if($admin || $manager){?>
<script type="text/javascript">
var checked = false;
$(function(){
	$('#all').click(function(){
		if(!checked) checked = true;
		else checked = false;
		$('#myform input[type="checkbox"]').attr('checked', checked);
	});
	$('#threadTop').click(function(){
		if($('#myform input[type="checkbox"]:checked').length < 1){
			alert('请先选择贴子');
		}else{
			$('#myform').attr('action', '<?php echo $this->createUrl('family/bbsTop', array('family_id' => $family['id']));?>');
			$('#myform').submit();
		}
	});
	$('#threadDelete').click(function(){
		if($('#myform input[type="checkbox"]:checked').length < 1){
			alert('请先选择贴子');
		}else{
			$('#myform').attr('action', '<?php echo $this->createUrl('family/bbsDelete', array('family_id' => $family['id']));?>');
			$('#myform').submit();
		}
	});
});
</script>
<?php }?>