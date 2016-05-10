<div class="w1000 mt20 shop">
    <div class="shop-con clearfix " style="margin-top:30px;">
    	<?php $this->renderPartial('application.views.shop.left'); ?>
        <div class="fright">
           	<div class="shop-main">
            	 <table class="open">
                    <tr>
                        <th colspan="4">
                            <strong class="open-header">VIP权限展示</strong>
                            <a class="mygoods" title="我的vip" href="<?php echo $this->getTargetHref($this->createUrl('account/vip'))?>" target="<?php echo $this->target?>">我的VIP</a>
							<?php $this->renderPartial('application.views.shop.agentlist'); ?>
                        </th>
                    </tr>
                    <tr class="colum">
                        <td>名称</td>
                        <td>权利</td>
                        <td>价格</td>
                    </tr>
                    <?php 
                		foreach($props as $v):
                	?>
                    <tr>
                        <td class="kind">
                            <em class="prot"><img src="<?php echo $v['image']?>" alt="<?php echo $v['name']?>"></em>
                        </td>
                        <td class="power">
							<?php echo $v['right']?>
                        </td>
                        <td class="price">
                        	<?php 
                					foreach($v['priceList'] as $vv):
                			?>
                        	<p>
                                <span> <?php echo $vv['value']?></span>
                                <a class="buy-btn" href="javascript:void(0);" title="购买" onclick="confirmBuyVip('<?php echo $v['prop_id']?>','<?php echo $v['name']?>','<?php echo $vv['id']?>','<?php echo $vv['data']?>')">购买</a>
                            </p>
                           <?php 
                                endforeach;
                            ?>
                        </td>
                    </tr>
                    <?php 
                       	endforeach;
                    ?> 
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var prop_id = 0;
var price_atrr_id = 0;
var prop_name = '';
var agent_id=<?php echo $this->agent_id;?>;	//选中的代理id
var agent_nickname='<?php echo mb_substr($this->agent_nickname,0,8,'UTF-8');?>';	//选中的代理昵称


function confirmBuyVip(propId,name,priceAttrId,desc){
	if($.User.getSingleAttribute('uid',true)<=0){
		$.User.loginController('login');
		return;
	}
	if(propId <= 0 || name == '' || priceAttrId <= 0){
		$('#LowStock div p').html('<center style="color:red;">购买失败</center>');
		$.mask.show('LowStock');
		return false;
	}
	prop_id = propId;
	prop_name = name;
	price_atrr_id = priceAttrId;

	if(price_atrr_id <= 0 || prop_id <= 0){
		$('#LowStock div p').html('<center style="color:red;">购买失败</center>');
		$.mask.show('LowStock');
		return false;
	}
	price_attr_text = desc.split('/');
	$('#VipSucBox h2 span').html(prop_name);
	$('#VipSucBox div ul li strong').eq(0).html(price_attr_text[1]+'皮蛋');
	$('#VipSucBox div ul li strong').eq(1).html(price_attr_text[0]+'个月');
	$('#VipSucBox div ul li strong').eq(2).html(price_attr_text[1]);
	
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
					$('#VipSucBox div ul li').eq(4).show();
					$('#VipSucBox div ul li strong').eq(3).html(showAgent(agent_nickname,agent_id));	
				}
				else
				{
					$('#VipSucBox div ul li').eq(4).hide();
				}
			}
		});
	}
	else
	{
		$('#VipSucBox div ul li').eq(4).hide();
	}
	
	$.mask.show('VipSucBox');
	
}

function buyVip(){
	if(price_atrr_id <= 0 || prop_id <= 0){
		$('#LowStock div p').html('<center style="color:red;">购买失败</center>');
		$.mask.show('LowStock');
		return false;
	}
	$.mask.hide('VipSucBox');
	$.ajax({
		type:"POST",
		url:"index.php?r=shop/buyVip",
		data:{'prop_id':prop_id,'price_attr_id':price_atrr_id,'agent_id':agent_id},
		dataType:"json",
		success:function(data){
		    $('#LowStock div p').html('<center style="color:red;">'+data.message+'</center>');
		    $.User.refershWebLoginHeader();
			price_atrr_id = prop_id = 0;
			prop_name = '';
			$.mask.hide('VipSucBox');
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

<div id="VipSucBox" class="buy-box">
	<h2 class="clearfix"><em onclick="$.mask.hide('VipSucBox');" class="fright">Χ</em>购买<span></span></h2>
    <div class="buy-con">
    	<ul class="buy-info">
        	<li class="first"></li>
            <li>
                <span>售价：</span>
                <strong></strong>
            </li>
            <li>
                <span>时限：</span>
                <strong></strong>
            </li>
            <li >
            	<span>贡献值：</span>
                <strong></strong>
            </li>
            <li class="end" style="display:none;">
            	<span>代理渠道：</span>
                <strong></strong>
            </li>
            <li>
                <input class="btn sure" type="button" value="确认" onclick="buyVip()"/>
                <input onclick="$.mask.hide('VipSucBox');" class="btn cancel" type="button" value="取消"/>
            </li>
        </ul>
    </div>
</div>