<?php echo $this->renderPartial('home_top', compact(array_keys(get_defined_vars())));?>
<div class="w1000 memberMesg-con clearfix">
    <div class="fleft memberMesgBd-l">
        <div class="control-box">
            <?php echo $this->renderPartial('admin_menu', array('family' => $family, 'action' => $this->getAction()->id, 'menu' => $menu, 'manager' => $manager));?>
            <p class="sub-controlMenu">
                <a class="<?php if($type == 'normal') echo 'on';?>" href="<?php echo $this->createUrl('family/adminTop', array('family_id' => $family['id'], 'type' => 'normal'));?>">普通族员</a>
                <?php if($family['sign'] == 1){ ?>
                <a class="<?php if($type == 'dotey') echo 'on';?> end" href="<?php echo $this->createUrl('family/adminTop', array('family_id' => $family['id'], 'type' => 'dotey'));?>">主播族员</a>
            	<?php } ?>
            </p>
            <form name="myform" method="post">
                <dl class="control-list">
                    <dt>
                        <span class="ranking">排名</span>
                        <span class="petname">昵称</span>
                        <span class="rank-level">等级</span>
                        <span class="rank-familyer">家族身份</span>
                        <span class="clan-badge">族徽</span>
                    </dt>
                    <?php foreach($members as $k => $m){?>
                    <?php if($m['is_dotey']) $rank = 'lvlo'; else $rank = 'lvlr';?>
                    <dd>
                        <span class="ranking"><?php echo $k+1;?></span>
                        <span class="petname"><label><?php echo $m['nickname'];?>(<?php echo $m['uid'];?>)</label></span>
                        <span class="rank-level"><em class="<?php echo $rank;?> <?php echo $rank;?>-<?php echo intval($m['rank']);?>"></em></span>
                        <span class="rank-familyer"><?php echo $m['role'];?></span>
                        <span class="clan-badge"><?php echo $m['medal_enable'] ? '已佩戴' : '--'?></span>
                    </dd>
                    <?php }?>
                </dl>
            </form>
            <?php $this->widget('lib.widgets.family.LinkPager', array('pages'=>$pages));?>
        </div>
        <p class="anchor-btm"></p>
    </div>
    <?php echo $this->renderPartial('home_right', compact(array_keys(get_defined_vars())));?>
</div>