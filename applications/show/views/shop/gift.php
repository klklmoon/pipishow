<div class="w1000 mt20 shop">
    <div class="shop-con clearfix " style="margin-top:30px;">
    	 <?php $this->renderPartial('application.views.shop.left'); ?>
        <div class="fright">
            <div class="shop-main">
                <h1>
                	<strong class="open-header">商城专卖</strong>
                    <a title="我的背包" class="mygoods" href="<?php echo $this->getTargetHref($this->createUrl('account/bag'))?>" target="<?php echo $this->target?>">我的背包</a>
                </h1>
                <ul class="gif-list mt20 clearfix">
                <?php foreach($giftList as $row):?>
                    <li class="newpro">
                        <div class="giflt-top">
                            <img src="<?php echo $row['image'];?>">
                            <p><?php echo $row['zh_name'];?></p>
                            <p>价格：<?php echo intval($row['pipiegg']);?>皮蛋</p>
                            <span><?php if($row['buy_limit']==0):?>不限<?php else:?>当前库存：<?php echo $row['sell_nums'];?><?php endif;?></span>
                        </div>
                        <div class="giflt-bottom">
                   			<p><?php if($row['grade']>0):?>限<?php echo $row['sell_grade'];?>以上购买<?php else:?>无<?php endif;?></p>
                            <a class="buy-btn" href="javascript:void(0);" onclick="Shop.buyGift('GifNumBox',<?php echo $row['gift_id'];?>,'<?php echo $row['zh_name'];?>','<?php echo $row['pipiegg'];?>','<?php echo $row['sell_nums']?>',<?php echo $row['grade'];?>,<?php echo isset($row['buy_limit'])?$row['buy_limit']:0;?>)" title="购买">购买</a>
                        </div>
                        <div class="newpro-pic">
                        	<?php if(in_array('新品',$row['shop_type'])):?>
                        	<img src="<?php echo Yii::app()->getController()->pipiFrontPath;?>/fontimg/common/new_pro.jpg">
                        	<?php elseif(in_array('热卖',$row['shop_type'])):?>
                        	<img src="<?php echo Yii::app()->getController()->pipiFrontPath;?>/fontimg/common/hot_buy.jpg">
                        	<?php endif;?>
                        </div>
                    </li>
                   <?php endforeach;?>
                 </ul>
            </div>
        </div>
    </div>
</div>
<div id="GifNumBox" class="buy-box"></div>
<div id="GuardSucBox2" class="buy-box buylast"></div>
<script>

var Shop={
		prop_id:0,//购买物品id	
		quantity:0,//当前库存	
		buyNum:0,//购买数量
		price:0,//礼物单价
		grade:0,//购买等级限制
		isSucc:false,
		//购买礼物
		buyGift:function(obj,gift_id,name,price,quantity,grade,buy_limit){
			this.price=price;
			if($.User.getSingleAttribute('uid',true)<=0){
				$.User.loginController('login');
				return;
			}
			if(gift_id==null||!gift_id) return false;
			if(quantity<=0&&buy_limit==1){
				var text='<div class="last-con"><p>当前库存数量不足!</p><input class="btn sure" onClick="$.mask.hide(\'LowStock\');" type="button" value="确认"><input onClick="$.mask.hide(\'LowStock\');" class="btn cancel" type="button" value="取消"></div>';
				$("#LowStock").html(text);
				$.mask.show('LowStock');
				return;
			}
			if($.User.getSingleAttribute('rk',true)<grade){
				var text='<div class="last-con"><p>限'+this.rankList[grade]+'以上购买</p><input class="btn sure" onClick="$.mask.hide(\'LowStock\');" type="button" value="确认"><input onClick="$.mask.hide(\'LowStock\');" class="btn cancel" type="button" value="取消"></div>';
				$("#LowStock").html(text);
				$.mask.show('LowStock');
				return;
			}
			var text='<h2 class="clearfix"><em onClick="$.mask.hide(\''+obj+'\');" class="fright">&Chi;</em>'+name+'</h2><div class="buy-con clearfix"><p class="buynum clearfix"><label>购买数量：</label><input type="text" id="quantity" value="1" onblur="Shop.changeGiftPrice()" onkeyup="Shop.changeGiftPrice()"></p><p class="buyprice">购买价格：<strong>'+price+'皮蛋</strong></p><input class="btn sure" type="button" onClick="Shop.confirmBuyGift(\''+obj+'\','+gift_id+')" value="确认"><input onClick="$.mask.hide(\''+obj+'\');" class="btn cancel" type="button" value="取消"></div>';
			$("#"+obj).html(text);
			$.mask.show(obj);
			
		},
		//改变礼物总价
		changeGiftPrice:function(){
			var quantity=$("#quantity").val();
			quantity=quantity.replace(/[^\d]/g,'');
			quantity=quantity>9999?9999:quantity;
			if(this.buy_limit==1){
				quantity=quantity>this.quantity?this.quantity:quantity;
			}
			$("#quantity").val(quantity);
			var totalPrice=this.price*10000*quantity/10000;
			$(".buyprice").find('strong').text(totalPrice+'皮蛋');
		},
		
		confirmBuyGift:function(obj,gift_id){
			var buyNum=parseInt($("#quantity").val());
			var o=this;
			$.ajax({
				type:'POST',
				url:'index.php?r=/shop/buyGift',
				data:{gift_id:gift_id,buyNum:buyNum},
				dataType:'json',
				async:false,
				success:function(data){
					$.mask.hide(obj);
					if(data.flag==1){
						$.User.refershWebLoginHeader();
						var text='<div class="last-con"><p class="suc">购买成功，礼物已存入背包!</p><input class="btn sure" onClick="$.mask.hide(\'GuardSucBox2\');" type="button" value="确认"><input  class="btn cancel" onClick="$.mask.hide(\'GuardSucBox2\');" type="button" value="取消"> </div>';
						$("#GuardSucBox2").html(text);
						$.mask.show('GuardSucBox2');
					}else{
						var text='<div class="last-con"><p>'+data.message+'</p><input class="btn sure" onClick="$.mask.hide(\'LowStock\');" type="button" value="确认"><input onClick="$.mask.hide(\'LowStock\');" class="btn cancel" type="button" value="取消"></div>';
						$("#LowStock").html(text);
						$.mask.show('LowStock');
						return;
					}
				}
			});
		}
	}
Shop.rankList=<?php echo json_encode($rankList)?>;		
</script>