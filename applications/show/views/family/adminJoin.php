<?php echo $this->renderPartial('home_top', compact(array_keys(get_defined_vars())));?>
<div class="w1000 memberMesg-con clearfix">
    <div class="fleft memberMesgBd-l">
        <div class="control-box">
            <?php echo $this->renderPartial('admin_menu', array('family' => $family,'action' => $this->getAction()->id, 'menu' => $menu, 'manager' => $manager));?>
            <form name="myform" method="post" action="<?php echo $this->createUrl('family/adminJoin', array('family_id' => $family['id']));?>">
                <ul class="condition-list">
                	<?php foreach($conditions as $k => $v){?>
                    <li>
                    	<input type="radio" id="condition<?php echo $k?>" name="join_rank" value="<?php echo $k?>" <?php if($config['join_rank'] == $k) echo "checked='checked'";?>>
                    	<label for="condition<?php echo $k?>" hidefocus="true"><?php echo $v?></label>
                    </li>
                    <?php }?>
                    <li class="applybtn"><input class="shiftbtn" type="submit" value="确&nbsp;&nbsp;定"></li>
                    <li>*申请加入的等级条件不限制主播。</li>
                </ul>
            </form>
        </div>
        <p class="anchor-btm"></p>
    </div>
    <?php echo $this->renderPartial('home_right', compact(array_keys(get_defined_vars())));?>
</div>