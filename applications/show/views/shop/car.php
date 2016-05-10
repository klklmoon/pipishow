 <div class="w1000 mt20 shop">
    <div class="shop-con clearfix " style="margin-top:30px;">
    	 <?php $this->renderPartial('application.views.shop.left'); ?>
    	 
 		<div class="fright">         
            <div class="shop-main">
            	<h1>
                	<strong class="open-header">按级别排序</strong>
                    <a title="我的座驾" class="mygoods" href="<?php echo $this->getTargetHref($this->createUrl('account/car'))?>" target="<?php echo $this->target?>">我的座驾</a>
                    <?php $this->renderPartial('application.views.shop.agentlist'); ?>
                </h1>
                <ul class="car-list mt20 clearfix">
                	<?php 
                		foreach($props as $v):
                	?>
                	<li>
                        <div class="carlt-mid">
                            <dl>
                                <dt>
                                <?php if(!empty($v['car_logo'])):?>
                                <img src="<?php echo $v['car_logo']?>">
                                <?php endif;?>
                                <em><?php echo $v['name']?></em></dt>
                                <dd><img src="<?php echo $v['image']?>" alt="<?php echo $v['name']?>"></dd>
                            </dl>
                            <p>限购级别：<?php echo $v['rank_desc']?></p>
                            
                            <?php if($v['limit_type'] == 0):?>
                            	<span></span>
                            <?php elseif ($v['limit_type'] == 1):?>
                            	<?php if($v['limit_num']> 0):?>
                            	 	<span class="soldpic" id='car_num_<?php echo $v['prop_id']?>'>限量<?php echo $v['limit_num']?></span>
                            	<?php else:?>
                            	    <span class="soldpic">售完</span>
                            	 <?php 
                             		endif;
                             elseif ($v['limit_type'] == 2):
                             ?>
                            	<?php if($v['limit_num']> 0):?>
                            	 	<span class="soldout" id='car_num_<?php echo $v['prop_id']?>'>可售<?php echo $v['limit_num']?>辆</span>
                            	 <?php else:?>
                            	    <span class="soldout">售完</span>
                            	 
                            <?php 
                            	 endif;
                            endif;?>
                        </div>
                        <div class="carlt--foot clearfix">
                        	<select name="price">
                        		<?php 
                					foreach($v['priceList'] as $vv):
                				?>
                            		<option value="<?php echo $vv['id']?>"><?php echo $vv['value']?></option>
                                <?php 
                                	endforeach;
                                ?>
                               
                            </select>
                            <a class="buy-btn car-btn" href="javascript:void(0);"  onclick="confirmBuyCar('<?php echo $v['prop_id']?>','<?php echo $v['name']?>',this)">购买</a>
                        </div>
                    </li>
                    <?php 
                       	endforeach;
                    ?>   
                </ul>
                <div class="car-bottom">
                	<strong class="carbt-head">说明:</strong>
                    <p>1、座驾处于"使用中"状态时，进入直播间将显示进场特效动画。<a href="<?php $this->getTargetHref('index.php?r=account/car')?>" title="立即启用座驾" target="<?php echo $this->target?>">立即启用座驾</a></p>
                    <p>2、可拥有多辆座驾，在"我的座驾"页面可以查看所有座驾。<a href="<?php $this->getTargetHref('index.php?r=account/car')?>" title="查看我的座驾" target="<?php echo $this->target?>">查看我的座驾</a></p>
                </div>
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

function confirmBuyCar(propId,name,obj){
	if($.User.getSingleAttribute('uid',true)<=0){
		$.User.loginController('login');
		return;
	}
	if(propId <= 0 || name == ''){
		$('#LowStock div p').html('<center style="color:red;">购买失败</center>');
		$.mask.show('LowStock');
		return false;
	}
	prop_id = propId;
	prop_name = name;
	price_atrr_id = $(obj).prev().val();

	if(price_atrr_id <= 0 || prop_id <= 0){
		$('#LowStock div p').html('<center style="color:red;">购买失败</center>');
		$.mask.show('LowStock');
		return false;
	}
	var price_list =$(obj).prev().parent();
	var price_attr_text = $('select option:selected',price_list).text();
	price_attr_text = price_attr_text.split('/');
	var reg = /(\d+)/;
	var match = reg.exec(price_attr_text[2]);

	$('#CarBox h2 span').html(prop_name);
	$('#CarBox div ul li strong').eq(0).html(price_attr_text[0]);
	$('#CarBox div ul li strong').eq(1).html(price_attr_text[1]);
	$('#CarBox div ul li strong').eq(2).html(match[1]);
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
					$('#CarBox div ul li').eq(4).show();
					$('#CarBox div ul li strong').eq(3).html(showAgent(agent_nickname,agent_id));
				}
				else
				{
					$('#CarBox div ul li').eq(4).hide();
				}
			}
		});
	}
	else
	{
		$('#CarBox div ul li').eq(4).hide();
	}
	$.mask.show('CarBox');
	
}

function buyCar(){
	if(price_atrr_id <= 0 || prop_id <= 0){
		$('#LowStock div p').html('<center style="color:red;">购买失败</center>');
		$.mask.show('LowStock');
		return false;
	}
	$.mask.hide('CarBox');
	$.ajax({
		type:"POST",
		url:"index.php?r=shop/buyCar",
		data:{'prop_id':prop_id,'price_attr_id':price_atrr_id,'agent_id':agent_id},
		dataType:"json",
		success:function(data){
			if(data.flag == 1){
				var numObj = $('#car_num_'+prop_id);
				var num = numObj.text();
				var newText = num.replace(/(\d+)/,function($1){return $1-1;});
				numObj.text(newText);
				$.User.refershWebLoginHeader();
			}
		    $('#LowStock div p').html('<center style="color:red;">'+data.message+'</center>');
			price_atrr_id = prop_id = 0;
			prop_name = '';
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
<div id="CarBox" class="buy-box">
	<h2 class="clearfix"><em onclick="$.mask.hide('CarBox');" class="fright">Χ</em>购买<span></span></h2>
    <div class="buy-con">
    	<ul class="buy-info">
        	<li class="first"></li>
            <li>
                <span>售价：</span>
                <strong>200皮蛋</strong>
            </li>
            <li>
                <span>时限：</span>
                <strong>1个月</strong>
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
                <input class="btn sure" type="button" value="确认" onclick="buyCar()"/>
                <input onclick="$.mask.hide('CarBox');" class="btn cancel" type="button" value="取消"/>
            </li>
        </ul>
    </div>
</div>
