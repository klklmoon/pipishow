<?php echo $this->renderPartial('home_top', compact(array_keys(get_defined_vars())));?>
<div class="w1000 memberMesg-con clearfix">
    <div class="fleft memberMesgBd-l">
        <div class="control-box">
        	<?php echo $this->renderPartial('admin_menu', array('family' => $family,'action' => $this->getAction()->id, 'menu' => $menu, 'manager' => $manager));?>
            <p class="sub-controlMenu">
                <a href="javascript:void(0);" class="SubMenu on">管理</a>
                <a href="javascript:void(0);" class="SubMenu end">公告</a>
            </p>
            <form name="myform" method="post" action="<?php echo $this->createUrl('family/admin', array('family_id' => $family['id']));?>" enctype="multipart/form-data">
            <div class="notice-mange admin-notice">
                <p><span>家族名称：</span><em><?php echo $family['name'];?></em></p>
                <p class="clearfix">
                	<span class="fleft">家族封面：</span>
                	<input type="file" class="fleft gray-btn" value="上传图片" name="cover" id="cover" style="width:200px;background:none;" />
                	<span class="fleft">支持jpg格式，大小不超过2Mb</span>
                </p>
                <!--
                <p>
                	<span>家族活动房：</span>
                	<input id="openfor" type="checkbox" name="activity_room" value="1" <?php if(isset($config['activity_room']) && $config['activity_room'] == 1) echo "checked";?>>
                	<label for="openfor">开放</label>
                </p>
                -->
                <p><input class="gray-btn" type="submit" value="确定修改" style="border:0;" /></p>
            </div>
            <div style="display:none;" class="admin-notice">
                <textarea class="familyNotice-text" name="announcement"><?php echo $extend['announcement'];?></textarea>
                <p class="noticeBtn clearfix">
                    <input class="fright shiftbtn" type="submit" value="发  布">
                    <!--灰色按钮-->
                    <!--<input class="fright gry-shiftbtn shiftbtn" type="button" value="发  布">-->
                </p>
            </div>
            </form>
        </div>
        <p class="anchor-btm"></p>
    </div>
    <?php echo $this->renderPartial('home_right', compact(array_keys(get_defined_vars())));?>
</div>
<script type="text/javascript">
$(function(){
	$('.SubMenu').click(function(){
        $(this).addClass('on').siblings('a').removeClass('on');
		$('.admin-notice').toggle();
    });
});
</script>