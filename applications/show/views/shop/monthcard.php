<div class="w1000 mt20 shop">
    <div class="shop-con clearfix " style=" margin-top:30px;">
    	 <?php $this->renderPartial('application.views.shop.left'); ?>
        <div class="fright">
            <div class="shop-main">
            	 <table class="open">
                    <tr>
                        <th colspan="4">
                            <strong class="open-header">开通月卡</strong>
                            <a title="我的月卡" class="mygoods" href="<?php echo $this->getTargetHref($this->createUrl('account/moon'))?>" target="<?php echo $this->target?>">我的月卡</a>
                            <?php $this->renderPartial('application.views.shop.agentlist'); ?>
                        </th>
                    </tr>
                    <tr class="colum">
                        <td>种类</td>
                        <td>威力</td>
                        <td>购买条件</td>
                        <td>价格</td>
                    </tr>
                    <?php 
                		foreach($props as $v):
                	?>
                    <tr>
                        <td class="kind">
                            <img src="<?php echo $this->pipiFrontPath?>/fontimg/props/mouth-big.png"><br>
                            <strong><?php echo $v['name']?></strong>
                        </td>
                        <td class="power">
                        	<?php echo $v['attribute']['monthcard_power']['value']?>
                            
                        </td>
                        <td class="term">
                            <p><?php echo $v['rank']?></p>
                        </td>
                        <td class="price">
                            <span><?php echo (int)$v['pipiegg']?>皮蛋/30天</span>
                            <a class="buy-btn" href="javascript:void(0);" onclick="confirmBuyMonthCard(<?php echo $v['prop_id']?>,<?php echo (int)$v['pipiegg']?>)" title="购买">购买</a>
                        </td>
                    </tr>
                   <?php 
                       	endforeach;
                    ?>  
                </table>
            </div>

        </div>
    </div><!-- .shop-con -->
</div><!-- .shop -->

<script type="text/javascript">
var prop_id = 0;
var agent_id=<?php echo $this->agent_id;?>;	//选中的代理id
var agent_nickname='<?php echo $this->agent_nickname;?>';	//选中的代理昵称

function confirmBuyMonthCard(propId,price){
	if($.User.getSingleAttribute('uid',true)<=0){
		$.User.loginController('login');
		return;
	}
	if(propId <= 0 || price <=0){
		$('#LowStock div p').html('<center style="color:red;">购买失败</center>');
		$.mask.show('LowStock');
		return false;
	}
	prop_id = propId;
	$('#MouthCardBox em').eq(0).html('购买价格：'+price+'皮蛋');
	if(agent_id>0)
	{
		$.ajax({
			type:"POST",
			url:"index.php?r=shop/CheckAgentId",
			data:{'agent_id':agent_id},
			dataType:"json",
			success:function(data){
				if(data.flag==1)
				{
					$('#MouthCardBox em').eq(1).show();
					$('#MouthCardBox em').eq(1).html('代理渠道：'+agent_nickname+'('+agent_id+')');
				}
				else
				{
					$('#MouthCardBox em').eq(1).hide();
				}
			}
		});
	}
	else
	{
		$('#MouthCardBox em').eq(1).hide();
	}
	$.mask.show('MouthCardBox');
}

function buyMonthord(){
	if($.User.getSingleAttribute('uid',true)<=0){
		$.User.loginController('login');
		return;
	}
	if(prop_id <= 0){
		$('#LowStock div p').html('<center style="color:red;">购买失败</center>');
		$.mask.show('LowStock');
		return false;
	}
	$.mask.hide('MouthCardBox');
	$.ajax({
		type:"POST",
		url:"index.php?r=shop/buyMonthCard",
		data:{'prop_id':prop_id,'agent_id':agent_id},
		dataType:"json",
		success:function(data){
			if(data.flag == 1){
				$('#LowStock div p').html('<center"><span style="color:red;>'+data.message+'</span>&nbsp;<a href="<?php $this->getTargetHref('index.php?r=account/moon')?>" target="<?php echo $this->target?>">查看买到的道具</a>'+'</center>');
				$.User.refershWebLoginHeader();
			}
		    $('#LowStock div p').html('<center style="color:red;">'+data.message+'</center>');
			prop_id = 0;
			$.mask.show('LowStock');
		}
	});
}

function  selectAgent(uid,nickname)
{
	agent_id=uid;
	agent_nickname=nickname;
	if(uid>0)
	{
		$.cookie('agent_id', uid);
		$(".agentname").html('<span>您选择的代理：</span><span><em class="pink">'+nickname+'</em>('+uid+')<em class="close"></em></span>' );
	}
	else
	{
		$.cookie('agent_id', null);
		$(".agentname").html('<span>您选择的代理：</span><span>无</span>' );
	}
}

selectAgent(agent_id,agent_nickname);
</script>

<div id="MouthCardBox" class="buy-box buylast">
<div class="last-con">
	<p>购买月卡（有效期30天）<br>
	<em></em><br>
	<em style="display: none;"></em>
	</p>
	<input class="btn sure" onclick="buyMonthord();" type="button" value="确认">
	<input onclick="$.mask.hide('MouthCardBox');" class="btn cancel" type="button" value="取消">
</div>
</div>

<div id="MouthCardSuc" class="buy-box buylast" style="display:none; margin-left: -125px; margin-top: -103px; top: 224.5px; left: 711.5px;">
	<div class="last-con">
	<p>购买成功</p>
	<input onclick="$.mask.hide('MouthCardSuc');" class="btn sure" type="button" value="确认">
	<a href="index.php?r=account/moon">查看买到的道具</a>
	</div>
</div>