<?php echo $this->renderPartial('home_top', compact(array_keys(get_defined_vars())));?>
<div class="w1000 memberMesg-con clearfix">
    <div class="fleft memberMesgBd-l">
        <div class="control-box">
            <?php echo $this->renderPartial('admin_menu', array('family' => $family,'action' => $this->getAction()->id, 'menu' => $menu, 'manager' => $manager));?>
            <p class="sub-controlMenu">
                <a class="<?php if($type == 'normal'){?>on<?php }?>" href="<?php echo $this->createUrl('family/adminCheck', array('family_id' => $family['id'], 'type' => 'normal'));?>">玩家(<?php echo $members['count'];?>)</a>
                <?php if($family['sign'] == 1){?>
                <a class="end <?php if($type == 'dotey'){?>on<?php }?>" href="<?php echo $this->createUrl('family/adminCheck', array('family_id' => $family['id'], 'type' => 'dotey'));?>">主播(<?php echo $doteys['count'];?>)</a>
            	<?php }?>
            </p>
            <form name="myform" id="myform" method="post" action="<?php echo $this->createUrl('family/adminCheck', array('family_id' => $family['id']));?>">
            	<input type="hidden" name="status" id="status" value="agree" />
                <div class="checkall-box clearfix">
                    <span class="fleft mt5 mr20 checkall-text"><input type="checkbox" id='all'><label>全选/反选</label></span>
                    <a class="fleft mr10 gray-btn" href="javascript:void(0);" id="agrees">批量同意</a>
                    <a class="fleft gray-btn" href="javascript:void(0);" id="refuses">批量拒绝</a>
                    <p class="fright applyNum">共有<?php echo $members['count']+$doteys['count'];?>个申请等待处理</p>
                </div>
                <dl class="control-list">
                    <dt>
                        <span class="applyTime">申请时间</span>
                        <span class="applyPerson">申请人</span>
                        <span class="applyControl">审批操作</span>
                    </dt>
                    <?php
                    if($type == 'dotey'){
						$list = $doteys['list'];
						$pages = $doteys['pages'];
                    }else{
						$list = $members['list'];
						$pages = $members['pages'];
					}
                    foreach($list as $m){
                    ?>
                    <?php if($m['apply_type']) $rank = 'lvlo'; else $rank = 'lvlr';?>
                    <dd>
                        <span class="applyTime"><input type="checkbox" name="uids[]" id="uid_<?php echo $m['uid'];?>" value="<?php echo $m['uid'];?>"><label><?php echo date('Y-m-d H:i', $m['create_time']);?></label></span>
                        <span class="applyPerson">
                        	<?php if($m['medal']){?><img src="<?php echo $m['medal'];?>" style="margin-bottom:-5px;" /><?php }?>
                            <em class="<?php echo $rank;?> <?php echo $rank;?>-<?php echo intval($m['rank']);?>"></em>
                            <em><i class="pink"><?php echo $m['nickname'];?></i> (<?php echo $m['uid'];?>)</em>
                        </span>
                        <span class="applyControl">
                            <a class="gray-btn mr5 agree" href="javascript:void(0);" data="<?php echo $m['uid'];?>">同意加入</a>
                            <a class="gray-btn refuse" href="javascript:void(0);" data="<?php echo $m['uid'];?>">拒绝</a>
                        </span>
                    </dd>
                    <?php } ?>
                </dl>
            </form>
            <?php $this->widget('lib.widgets.family.LinkPager', array('pages'=>$pages));?>
        </div>
        <p class="anchor-btm"></p>
    </div>
    <?php echo $this->renderPartial('home_right', compact(array_keys(get_defined_vars())));?>
</div>
</div>
<script type="text/javascript">
$(function(){
	$('#all').click(function(){
		var checked = false;
		if($(this).attr('checked')){
			checked = true;
		}
		$('#myform input[type="checkbox"]').attr('checked', checked);
	});
	$('#agrees').click(function(){
		$('#status').val('agree');
		$('#myform').submit();
	});
	$('#refuses').click(function(){
		$('#status').val('refuse');
		$('#myform').submit();
	});
	$('.agree').click(function(){
		$('#status').val('agree');
		$('#myform input[type="checkbox"]').attr('checked', false);
		$('#uid_'+$(this).attr('data')).attr('checked', true);
		$('#myform').submit();
	});
	$('.refuse').click(function(){
		$('#status').val('refuse');
		$('#myform input[type="checkbox"]').attr('checked', false);
		$('#uid_'+$(this).attr('data')).attr('checked', true);
		$('#myform').submit();
	});
});
</script>