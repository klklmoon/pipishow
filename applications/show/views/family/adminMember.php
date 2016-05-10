<?php echo $this->renderPartial('home_top', compact(array_keys(get_defined_vars())));?>
<div class="w1000 memberMesg-con clearfix">
    <div class="fleft memberMesgBd-l">
        <div class="control-box">
            <?php echo $this->renderPartial('admin_menu', array('family' => $family,'action' => $this->getAction()->id, 'menu' => $menu, 'manager' => $manager));?>
            <p class="sub-controlMenu">
                <a<?php if($type == 'all'){?> class="on"<?php }?> href="<?php echo $this->createUrl('family/adminMember', array('family_id' => $family['id'], 'type' => 'all'));?>">全部成员</a>
                <a<?php if($type == 'elder'){?> class="on"<?php }?> href="<?php echo $this->createUrl('family/adminMember', array('family_id' => $family['id'], 'type' => 'elder'));?>">家族长老</a>
                <a<?php if($type == 'admin'){?> class="on"<?php }?> href="<?php echo $this->createUrl('family/adminMember', array('family_id' => $family['id'], 'type' => 'admin'));?>">家族管理</a>
                <?php if($family['sign'] == 1){ ?>
                <a<?php if($type == 'dotey_medal'){?> class="on"<?php }?> href="<?php echo $this->createUrl('family/adminMember', array('family_id' => $family['id'], 'type' => 'dotey_medal'));?>">族徽主播</a>
                <a<?php if($type == 'dotey'){?> class="on"<?php }?> href="<?php echo $this->createUrl('family/adminMember', array('family_id' => $family['id'], 'type' => 'dotey'));?>">家族主播</a>
                <?php } ?>
                <a<?php if($type == 'member_medal'){?> class="on"<?php }?> href="<?php echo $this->createUrl('family/adminMember', array('family_id' => $family['id'], 'type' => 'member_medal'));?>">族徽成员</a>
                <a<?php if($type == 'member'){?> class="on"<?php }?> class="end" href="<?php echo $this->createUrl('family/adminMember', array('family_id' => $family['id'], 'type' => 'member'));?>">普通成员</a>
            </p>
            <form name="myform" id="myform" method="post" action="<?php echo $this->createUrl('family/adminMember', array('family_id' => $family['id']));?>">
            	<input type="hidden" name="type" value="<?php echo $type;?>" />
            	<input type="hidden" name="status" id="status" value="<?php echo empty($status) ? 'set' : $status;?>" />
                <div class="checkall-box clearfix">
                    <span class="fleft mt5 mr20 checkall-text"><input type="checkbox" id="all"><label>全选/反选</label></span>
                    <?php if($member['uid'] == $family['uid'] || $manager){?>
                    <a class="fleft mr10 gray-btn" href="javascript:void(0);" id="setElder">设为长老</a>
                    <?php }?>
                    <?php if(($member['role_id'] > 0 && $member['role_id'] < FAMILY_ROLE_ADMINISTRATOR) || $manager){?>
                    <a class="fleft mr10 gray-btn" href="javascript:void(0);" id="setAdmin">设为管理</a>
                    <?php }?>
                    <a class="fleft gray-btn" href="javascript:void(0);" id="kick">踢出家族</a>
                    <p class="fright search">
                        <input type="text" value="按ID" name="uid" id="input_uid" class="fleft">
                        <a title="搜索" class="searchbtn fleft" href="javascript:void(0);" id="submit"></a>
                    </p>
                </div>
                <dl class="control-list">
                    <dt>
                        <span class="name">昵称</span>
                        <span class="member_level">等级</span>
                        <span class="familyer">家族身份</span>
                        <span class="passControl">审批操作</span>
                    </dt>
                    <?php foreach($members['list'] as $m){?>
                    <?php if($m['is_dotey']) $rank = 'lvlo'; else $rank = 'lvlr';?>
                    <dd>
                        <span class="name">
                        	<input type="checkbox" name="uids[]" id="uid_<?php echo $m['uid'];?>" value="<?php echo $m['uid'];?>" <?php if($m['uid'] == $family['uid'] || $m['role_id'] > 0 && $member['role_id'] > $m['role_id'] || $member['uid'] == $m['uid']){?> disabled="disabled"<?php }?>>
                        	<label><?php echo $m['nickname'];?>(<?php echo $m['uid'];?>)</label>
                        	<?php if($m['medal']){?><img src="<?php echo $m['medal'];?>" style="margin-bottom:-5px;" /><?php }?>	
                        </span>
                        <span class="member_level"><em class="<?php echo $rank;?> <?php echo $rank;?>-<?php echo $m['rank'];?>"></em></span>
                        <span class="familyer"><?php echo $m['role'];?></span>
                        <span class="passControl">
                        	<?php if($member['uid'] != $m['uid'] && $m['role_id'] > 0 && $m['uid'] != $family['uid'] && $m['role_id'] != FAMILY_ROLE_DOTEY && $member['role_id'] < $m['role_id']){?>
                        	<a class="gray-btn remove<?php if($m['role_id'] == FAMILY_ROLE_ELDER) echo "Elder"; elseif($m['role_id'] == FAMILY_ROLE_ADMINISTRATOR) echo "Admin";?>" href="javascript:void(0);" data="<?php echo $m['uid'];?>">解除身份</a>
                        	<?php }?>
                        </span>
                    </dd>
                    <?php }?>
                </dl>
            </form>
            <?php $this->widget('lib.widgets.family.LinkPager', array('pages'=>$members['pages']));?>
        </div>
        <p class="anchor-btm"></p>
    </div>
    <?php echo $this->renderPartial('home_right', compact(array_keys(get_defined_vars())));?>
</div>
<script type="text/javascript">
var str = '';
$(function(){
	$('#all').click(function(){
		var checked = false;
		if($(this).attr('checked')){
			checked = true;
		}
		$('#myform input[type="checkbox"]').attr('checked', checked);
	});
	$('#input_uid').focus(function(){
		if($(this).val() == this.defaultValue){  
			$(this).val("");           
		} 
	}).blur(function(){
		if ($(this).val() == '') {
			$(this).val(this.defaultValue);
		}
	});
	$('#submit').click(function(){
		$('#myform').submit();
	});
	<?php if($member['role_id'] < FAMILY_ROLE_ADMINISTRATOR){?>
	str = "如要踢管理请先卸任\n";
	$('#setAdmin').click(function(){
		if($('#myform input[type="checkbox"]:checked').length < 1){
			alert('请先选择成员');
		}else if(confirm('确定要设为管理么')){
			$('#status').val('set');
			$('#myform').attr('action', '<?php echo $this->createUrl('family/setAdmin', array('family_id' => $family['id']));?>');
			$('#myform').submit();
		}
	});
	$('.removeAdmin').click(function(){
		if(confirm('确定要解除管理么')){
			$('#status').val('unset');
			$('#myform input[type="checkbox"]').attr('checked', false);
			$('#uid_'+$(this).attr('data')).attr('checked', true);
			$('#myform').attr('action', '<?php echo $this->createUrl('family/setAdmin', array('family_id' => $family['id']));?>');
			$('#myform').submit();
		}
	});
	<?php }?>
	<?php if($member['uid'] == $family['uid']){?>
	str = "如要踢长老、管理请先卸任\n";
	$('#setElder').click(function(){
		if($('#myform input[type="checkbox"]:checked').length < 1){
			alert('请先选择成员');
		}else if(confirm('确定要设为长老么')){
			$('#status').val('set');
			$('#myform').attr('action', '<?php echo $this->createUrl('family/setElder', array('family_id' => $family['id']));?>');
			$('#myform').submit();
		}
	});
	$('.removeElder').click(function(){
		if(confirm('确定要解除长老么')){
			$('#status').val('unset');
			$('#myform input[type="checkbox"]').attr('checked', false);
			$('#uid_'+$(this).attr('data')).attr('checked', true);
			$('#myform').attr('action', '<?php echo $this->createUrl('family/setElder', array('family_id' => $family['id']));?>');
			$('#myform').submit();
		}
	});
	<?php }?>
	$('#kick').click(function(){
		if($('#myform input[type="checkbox"]:checked').length < 1){
			alert('请先选择成员');
		}else if(confirm(str+'确定要踢出家族么')){
			$('#myform').attr('action', '<?php echo $this->createUrl('family/kick', array('family_id' => $family['id']));?>');
			$('#myform').submit();
		}
	});
});
</script>