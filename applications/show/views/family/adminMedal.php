<?php echo $this->renderPartial('home_top', compact(array_keys(get_defined_vars())));?>
<div class="w1000 memberMesg-con clearfix">
    <div class="fleft memberMesgBd-l">
        <div class="control-box">
            <?php echo $this->renderPartial('admin_menu', array('family' => $family,'action' => $this->getAction()->id, 'menu' => $menu, 'manager' => $manager));?>
            <p class="clanBadge-top">目前共有<em class="pink"><?php echo $records['count'];?></em>位家族成员购买了族徽；您累计获得<em class="pink"><?php echo $records['count'] * $medal_eggpoint;?></em>皮点</p>
            <form name="myform" id="myform" method="post" action="<?php echo $this->createUrl('family/adminMedal', array('family_id' => $family['id']));?>">
            <div class="clanMange-box">
                <p>族徽简文字</p>
                <p class="change-clan clearfix">
                    <input type="text" name="medal" id="medal" value="<?php echo $family['medal'];?>">
                    <a class="gray-btn" href="javascript:void(0);" id="make_medal">预览</a>
                    <label>2个汉字或2-3个英文字母</label>
                </p>
                <p class="create-clan"><span>生成族徽</span><img id="show_medal" src="images/family/<?php echo $family['id'];?>/medal_<?php echo $family['level'];?>3.jpg"><em>修改花费<i class="pink"><?php echo $update_medal_price;?></i>皮蛋</em></p>
            </div><!--.clanMange-box-->
            <p class="pt10 pl20"><a class="gray-btn" href="javascript:void(0);" id="submit">确定修改</a></p>
            </form>
            <dl class="control-list">
				<dt>
					<span class="applyTime">购买时间</span>
                    <span class="applyPerson">购买者</span>
				</dt>
                <?php foreach($records['list'] as $m){?>
                <?php if($m['is_dotey']) $rank = 'lvlo'; else $rank = 'lvlr';?>
                <dd>
                    <span class="applyTime"><?php echo date('Y-m-d H:i', $m['consume_time']);?></span>
                    <span class="applyPerson">
                    	<?php if($m['medal']){?><img src="<?php echo $m['medal'];?>" style="margin-bottom:-5px;" /><?php }?>
                    	<em class="<?php echo $rank;?> <?php echo $rank;?>-<?php echo $m['rank'];?>"></em>
                    	<?php echo $m['nickname'];?>(<?php echo $m['uid'];?>)
                    </span>
                </dd>
                <?php }?>
			</dl>
			<?php $this->widget('lib.widgets.family.LinkPager', array('pages'=>$records['pages']));?>
        </div>
        <p class="anchor-btm"></p>
    </div>
    <?php echo $this->renderPartial('home_right', compact(array_keys(get_defined_vars())));?>
</div>
<script type="text/javascript">
$(function(){
	$('#make_medal').click(function(){
		var medal = $('#medal').val();
		if(medal == ''){
			alert('请输入族徽简字');
		}else{
			var length = 0, len = $.trim(medal).length;
			for (var i = 0; i < len; i++) {
		        charCode = $.trim(medal).charCodeAt(i);
		        if (charCode >= 0 && charCode <= 128) length += 1;
		        else length += 1.5;
		    }
		    if(!(length >= 2 && length <= 3)){
				alert("2个汉字或2-3个英文字母");
		    }else{
		    	$.ajax({
					url : "index.php?r=family/makeMedal",
					type : "GET",
					data:{'medal':medal},
					dataType : "text",
					success : function(text){
						$('#show_medal').attr('src', '/images/'+text+'?'+(new Date().getTime()));
				 	}
				});
		    }
		}
	});
	$('#submit').click(function(){
		$('#myform').submit();
	});
});
</script>