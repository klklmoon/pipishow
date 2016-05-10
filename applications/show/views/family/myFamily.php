<?php
function getHtml($family, &$obj, $roles, $manager){
?>
		<div class="family_inf mt10">
             <div class="anchor-head fleft">
             	<a title="<?php echo $family['name'];?>" href="<?php echo $obj->createHomeUrl($family['id']);?>" style="display:block;"><img src="/images/family/<?php echo $family['id'];?>/<?php echo $family['cover'];?>"></a></div>
             
             <div class="famliy_mid fleft ml10">
             	<div class="clan_LR clan_pos">
					<?php if($family['member']['have_medal'] == 1 && $family['member']['medal_enable'] == 1){?>
                	<a class="clan_L" href="javascript:void(0);" title="已佩戴族徽"><img src="<?php echo $obj->pipiFrontPath;?>/fontimg/family/pd.gif" class="vett_mid">已佩戴</a>
                	<a class="clan_R" href="javascript:void(0);" title="卸下佩戴的族徽" onClick="unload(<?php echo $family['id'];?>, '<?php echo $family['name'];?>', <?php echo $family['level'];?>, <?php echo $family['status'];?>);">卸下</a>
                	<?php }elseif($family['member']['have_medal'] == 1 && $family['member']['medal_enable'] == 0){?>
                	<a class="clan_L" href="javascript:void(0);">已购买</a>
                	<a class="clan_R" href="javascript:void(0);" onClick="equit(<?php echo $family['id'];?>, '<?php echo $family['name'];?>', <?php echo $family['level'];?>, <?php echo $family['status'];?>);">佩戴族徽</a>
                	<?php }elseif($family['member']['have_medal'] == 0){?>
                	<a class="clan_L" href="javascript:void(0);" title="购买族徽" onClick="buyMedal(<?php echo $family['id'];?>, <?php echo $family['level'];?>, <?php echo $family['status'];?>);">购买</a>
                	<?php }?>
             	</div>
             	<p>家族名称：<strong class="pink"><?php echo $family['name'];?></strong><?php if($family['sign'] == 1){?><img class="ml5" src="<?php echo $obj->pipiFrontPath;?>/fontimg/family/qianyue-btn.jpg"><?php }?></p>
             	<p>族徽：<img src="/images/family/<?php echo $family['id'];?>/medal_<?php echo $family['sign'] == 1 ? '0' : $family['level'];?>3.jpg" class="vett_mid ml10 mr10">有<?php echo $family['medal_total'];?>人佩戴</p>
             	<p>族长：<a class="pink"><?php echo $family['owner']['nk'];?></a> (<?php echo $family['owner']['uid'];?>)</p>
             	<p><span>家族长老：<?php echo $family['elder_total'];?></span> <span class="ml50">家族管理：<?php echo $family['admin_total'];?></span><?php if($family['sign'] == 1){?> <span class="ml50">家族主播：<?php echo $family['dotey_total'];?></span><?php }?></p>
             	<p><span>族徽族员：<?php echo $family['medal_total'];?></span> <span class="ml50">家族成员：<?php echo $family['member_total'];?></span></p>
             	<?php if($family['sign'] != 1){?><p>家族等级：<img src="<?php echo $obj->pipiFrontPath?>/fontimg/family/lv<?php echo $family['level'];?>.png" alt="LV<?php echo $family['level'];?>" title="LV<?php echo $family['level'];?>" />  <span class="ml20 gray">(可设<span class="pink"><?php echo $family['level_info']['elder'];?></span>名长老，<a class="pink"><?php echo $family['level_info']['admin'];?></a>名管理<!-- 接纳<a class="pink">，<?php echo $family['level_info']['dotey'];?></a>名主播， --><?php if($family['level_info']['members'] > 0){ ?>，家族成员数上限<a class="pink"><?php echo $family['level_info']['members'];?></a>人<?php }else echo "，家族成员人数不限";?>)</span></p><?php }?>
             </div>
             
             <div class="family_right fright">
                <p>家族身份：<?php echo $family['member']['role_id'] == 0 && $family['member']['family_dotey'] ? '家族主播' : $roles[$family['member']['role_id']];?></p>
                <p><a class="fright gray-btn" href="<?php echo $obj->createHomeUrl($family['id']);?>">家族主页</a></p>
                <?php if($family['member']['role_id'] > 0 || $manager){?>
                <p><a class="fright gray-btn" href="<?php echo $obj->createUrl('family/admin', array('family_id' => $family['id']));?>">家族管理</a></p>
                <?php }?>
                <?php if($family['uid'] != Yii::app()->user->id){?>
                <p><a class="fright gray-btn" href="javascript:void(0);" onClick="quit(<?php echo $family['id'];?>, '<?php echo $family['name'];?>', <?php echo $family['sign'];?>, <?php echo $family['member']['family_dotey'];?>);">退出家族</a></p>
                <?php }?>
             </div>
        </div>
<?php
}
?>

<div class="w1000 clanNav-con clearfix">
    <div class="fleft clanLR-btn myClan">
        <a href="<?php echo $this->createUrl('family/index');?>" class="clanL-btn">家族首页</a>
        <a href="<?php echo $this->createUrl('family/myFamily');?>" class="clanR-btn">我的家族</a>
    </div>
    <a class="fright gray-btn" href="<?php echo $this->createUrl('family/apply');?>">创建家族<em class="beta"></em></a></a>
</div>

<div class="w1000 mb20">您已创建<a class="pink"><?php echo empty($myFamily['create']) ? 0 : 1;?></a>个家族，加入<a class="pink"><?php echo count($myFamily['join']);?></a>个家族。<span class="gray">（每人最多可同时加入3个家族，玩家达到富豪10或主播达到蓝钻5可以创建1个家族。<a class="pink" href="<?php echo $this->createUrl('family/help');?>">家族功能说明</a>）</span></div>

<div class="satebox mt10">
    <div class="familyBox clearfix">
        <p><strong>我创建的家族：</strong></p>
        <?php if(!empty($myFamily['create'])){?>
		<?php $family = $myFamily['create'];?>
		<?php getHtml($family, $this, $roles, $manager);?>
        <?php }?>
        <?php if(empty($myFamily['create'])){?>
	    <p style="text-align: center;">尚未创建家族</p>
	    <?php }?>
    </div>
    
    <div class="familyBox clearfix">
        <p><strong>我加入的家族：</strong></p>
        <?php foreach($myFamily['join'] as $family){?>
        <?php getHtml($family, $this, $roles, $manager);?>
        <?php }?>
        <?php if(empty($myFamily['join'])){?>
	    <p style="text-align: center;">尚未加入家族</p>
	    <?php }?>
    </div>
</div>
<p class="patternOrder-btm"></p>
<?php echo $this->renderPartial('dialog', array('medal_price' => $medal_price));?>