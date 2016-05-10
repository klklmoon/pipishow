<div class="w1000 memberMesg-hd">
    <p class="memberMesg-title">
    	<a href="<?php echo $this->createHomeUrl($family['id']);?>" class="pink"><?php echo $family['name'];?></a>
    	<img style="margin-bottom:-3px;" src="/images/family/<?php echo $family['id'];?>/medal_<?php echo $family['sign'] == 1 ? '0' : $family['level'];?>3.jpg">
    	<?php if($family['sign'] == 1){?><img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/qianyue-btn.jpg"><?php }?>
        <!-- <a class="inbtn" href="#">直播中</a> -->
    </p>
    <div class="memberMesg-info clearfix">
        <div class="fleft headpic">
            <img src="/images/family/<?php echo $family['id']."/".$family['cover'];?>">
        </div>
        <div class="fleft notice">
            <p><?php echo $extend['announcement'];?></p>
            <div class="notice-icon"></div>
        </div>
        <dl class="fleft control-detail">
            <dt class="clearfix">
            	<?php if(empty($member)){?>
            		<a class="fleft gray-btn" href="javascript:void(0);" onClick="join(<?php echo $family['id'];?>, <?php echo $user['ut'] & USER_TYPE_DOTEY ? $family['sign'] : 0;?>, <?php echo $isFamilyDotey;?>);">申请加入</a>
            		<?php if($manager){?>
	            	<a class="fleft gray-btn" href="<?php echo $this->createUrl('family/admin', array('family_id' => $family['id']));?>">家族管理</a>
	            	<?php }?>
            	<?php }else{?>
	            	<?php if($member['have_medal'] == 0){?>
	            	<a class="fleft gray-btn" href="javascript:void(0);" onClick="buyMedal(<?php echo $family['id'];?>, <?php echo $family['level'];?>, <?php echo $family['status'];?>);">购买族徽</a>
	            	<?php }elseif($member['medal_enable'] == 1){?>
	            	<a class="fleft gray-btn" href="javascript:void(0);" onClick="unload(<?php echo $family['id'];?>, '<?php echo $family['name'];?>', <?php echo $family['level'];?>, <?php echo $family['status'];?>);">卸下族徽</a>
	            	<?php }else{?>
	            	<a class="fleft gray-btn" href="javascript:void(0);" onClick="equit(<?php echo $family['id'];?>, '<?php echo $family['name'];?>', <?php echo $family['level'];?>, <?php echo $family['status'];?>);">佩戴族徽</a>
	            	<?php }?>
	            	<?php if($admin || $manager){?>
	            	<a class="fleft gray-btn" href="<?php echo $this->createUrl('family/admin', array('family_id' => $family['id']));?>">家族管理</a>
	            	<?php }?>
	            	<?php if($family['uid'] != Yii::app()->user->id){?>
	            	<a class="fleft gray-btn" href="javascript:void(0);" onClick="quit(<?php echo $family['id'];?>, '<?php echo $family['name'];?>', <?php echo $family['sign'];?>, <?php echo $member['family_dotey'];?>);">退出家族</a>
	            	<?php }?>
            	<?php }?>
            </dt>
            <dd>
            	<span><em>家族成立：</em><?php echo date('Y-m-d', $family['create_time']);?></span>
                <span><em>族徽族员：</em><?php echo $family['medal_total'];?></span>
            </dd>
            <dd>
                <span><em>长老：</em><?php echo $family['elder_total'];?></span>
                <span><em>管理：</em><?php echo $family['admin_total'];?></span>
                <?php if($family['sign'] == 1){?><span><em>主播：</em><?php echo $family['dotey_total'];?></span><?php }?>
                <span><em>成员：</em><?php echo $family['member_total'];?></span>
            </dd>
            <?php if($family['sign'] != 1){?>
            <dd class="gradecon clearfix">
                <span class="fleft"><em>家族等级：</em></span>
                <div class="fleft gradebox">
                    <em class="lvlf lvlf-<?php echo $family['level'];?>"></em>
                    <div id="FamLevel" class="fleft process-box">
                        <span class="process" style="width: <?php echo $level['percent'];?>;"></span>
                        <span class="rate-con clearfix"><em class="now-rate"><?php echo $level['process'];?></em><em>/</em><em class="total-rate"><?php echo $level['end'] - $level['start'];?></em></span>
                        <span class="tipcon" style="display: none;">升级还需充<?php echo $level['need'];?>皮蛋</span>
                    </div>
                    <em class="lvlf lvlf-<?php echo $family['level'] == 6 ? 6 : $family['level']+1;?>"></em>
                    <?php /*?>
                    <div class="baograde">
                        <img src="images/bao.jpg">
                        <dl class="baofram">
                            <dd><span>当前家族等级：</span><em class="lvlf lvlf-1"></em></dd>
                            <dd><span>实际家族权限：</span><em class="lvlf lvlf-2"></em></dd>
                            <dt>本月保级需充<em class="pink">20000</em>皮蛋</dt>
                        </dl>
                    </div>
                    <? */?>
                </div><!--.gradebox-->
            </dd>
            <?php }?>
        </dl>
    </div>
</div>
<script type="text/javascript">
//经验值进度条
function EmpValue(Id){
	var total=$('#'+Id).find('.total-rate').text(),
	nownum=$('#'+Id).find('.now-rate').text();
	total=parseInt(total);
	nownum=parseInt(nownum);
	if(nownum>total){
		nownum=total;
	}
	var wdper=Math.round((nownum/total)*100)+'%';
	$('#'+Id).find('.process').width(wdper);
};
$(function(){
	//等级事件
    EmpValue('FamLevel');
    //等级条鼠标悬停事件
    $('#FamLevel').hover(function(){
        $(this).find('.tipcon').css('display','block');
    },function(){
        $(this).find('.tipcon').css('display','none');
    });
    //'保'图标
    $('.baograde').hover(function(){
        $(this).find('.baofram').css('display','block');
    },function(){
        $(this).find('.baofram').css('display','none');
    });
});
</script>
<?php echo $this->renderPartial('dialog', array('medal_price' => $medal_price));?>