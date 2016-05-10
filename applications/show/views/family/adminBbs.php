<?php echo $this->renderPartial('home_top', compact(array_keys(get_defined_vars())));?>
<div class="w1000 memberMesg-con clearfix">
    <div class="fleft memberMesgBd-l">
        <div class="control-box">
            <?php echo $this->renderPartial('admin_menu', array('family' => $family,'action' => $this->getAction()->id, 'menu' => $menu, 'manager' => $manager));?>
            <form name="myform" id="myform" method="post" action="<?php echo $this->createUrl('family/adminBbs', array('family_id' => $family['id']));?>">
                <ul class="condition-list">
                    <li><strong>发帖设置</strong></li>
                    <?php foreach($post_conditions as $k => $v){?>
                    <li>
                    	<input type="radio" id="PostSeting<?php echo $k;?>" name="post_rank" value="<?php echo $k;?>" <?php if($config['post_rank'] == $k) echo "checked='checked'";?>>
                    	<label for="PostSeting<?php echo $k;?>" hidefocus="true"><?php echo $v;?></label>
                    </li>
                    <?php }?>
                </ul>
                <ul class="condition-list">
                    <li><strong>回应设置</strong></li>
                    <?php foreach($reply_conditions as $k => $v){?>
                    <li>
                    	<input type="radio" id="ResSeting<?php echo $k;?>" name="reply_rank" value="<?php echo $k;?>" <?php if($config['reply_rank'] == $k) echo "checked='checked'";?>>
                    	<label for="ResSeting<?php echo $k;?>" hidefocus="true"><?php echo $v;?></label>
                    </li>
                    <?php }?>
                    <li class="applybtn"><input class="shiftbtn" type="submit" value="确&nbsp;&nbsp;定"></li>
                    <li>*发帖和回应条件不限制家族长、长老、管理和主播。</li>
                    <li>*发帖和回应验默认需要输入验证码，只有家族长、长老、主播和绅士04以上用户不受限制。</li>
                </ul>
            </form>
        </div>
        <p class="anchor-btm"></p>
    </div>
    <?php echo $this->renderPartial('home_right', compact(array_keys(get_defined_vars())));?>
</div>