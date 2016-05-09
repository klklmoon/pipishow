        <?php 
        $giftService=new GiftService();
        ?>
        <div class="gifts">
        	<div class="gift-box">
            	<div class="gift-menu">
                	<ul class="clearfix">
                		<?php 
                		$i=1;
                		foreach($gift['giftList'] as $row):?>
                		<li <?php if($i==1):?>class="first"<?php endif;?><?php if($i==count($gift['giftList'])):?>class="lastwo"<?php endif;?> ><a <?php if($i==1):?>class="giftover"<?php endif;?> href="javascript:void(0);" title="<?php echo $row['cat_name'];?>"><?php echo $row['cat_name'];?></a></li>
                        <?php
                        $i++;
                        endforeach;?>
                        <?php if(Yii::app()->user->id):?><li class="last" id="userBag"><a href="javascript:void(0);" title="背包">背包</a></li><?php endif;?>
                    </ul>
                </div><!--.gift-menu-->
                <div class="gift-hd">
                <?php foreach($gift['giftList'] as $key=>$row):?>
                    <div class="gift-con clearfix <?php if($key>0):?>none<?php endif;?>">
                        <span class="prev"></span>
                        <div class="gift-list">
                            <ul class="clearfix">
                            <?php foreach($row['child'] as $val):?>	
                                <li><a onclick="Gift.selectGift(this,<?php echo $val['gift_id']?>,'<?php echo $val['pipiegg']?>','common')" href="javascript:void(0)" title="<?php if(!empty($val['remark'])){ echo '价格：'.intval($val['pipiegg']).'皮蛋，'.$val['remark']; }else{ echo '价格：'.intval($val['pipiegg']).'皮蛋';}?>"  <?php $effects=array(); if($val['effects']){ foreach($val['effects'] as $_effects){if($_effects['num']>1){if($_effects['remark']){$effects[]=$_effects['num'].'('.$_effects['remark'].')';}else{$effects[]=$_effects['num'];}}} if(!empty($effects)){echo "effects=\"".implode('|',$effects)."\"";}}?>><img src="<?php echo $giftService->getGiftUrl($val['image']);?>" alt="<?php if(!empty($val['remark'])){ echo '价格：'.intval($val['pipiegg']).'皮蛋，'.$val['remark']; }else{ echo '价格：'.intval($val['pipiegg']).'皮蛋';}?>"></a><span><?php echo $val['zh_name'];?></span></li>
                            <?php endforeach;?>  
                            </ul>
                        </div>
                        <span class="next"></span>
                    </div><!--.gift-con-->
                  <?php endforeach;?> 
                   <div class="gift-con clearfix none" id="bagGiftCon">
                   		<span id="Aleft" class="prev"></span>
                   		<div class="gift-list" id="giftBagList"><div class="giftList" id="bagGiftList"></div></div>	 
                   		<span id="Aright" class="next"></span>
                   </div>
                </div><!--.gift-hd-->
            </div><!--.gift-box-->
            <div class="gift-control clearfix">
                 <span class="fleft giftext">赠送给</span>
                 <span class="text"><input id="GiveNameText" type="text" readonly></span>
                 <span class="fleft giftext">数量</span>
                 <div class="fleft changenum"><input type="text" id="send_num" value="1" onblur="value=value.replace(/[^\d]/g,'');value=value>Gift.maxSendNum?Gift.maxSendNum:value;value=value<=0?1:value;" onkeyup="value=value.replace(/[^\d]/g,'');value=value>Gift.maxSendNum?Gift.maxSendNum:value;value=value<=0?1:value;"><span class="add" onclick="Gift.addGiftNum('send_num')" title="+1"></span><span class="reduce" onclick="Gift.reduceGiftNum('send_num')" title="-1"></span></div>
                 <a href="javascript:void(0);" onclick="Gift.sendGift()" class="subbtn givebtn" title="赠送">赠送</a>
				 <?php if($this->getController()->domain_type == 'tuli'){?>
	                <a href="javascript:void(0);" target="_self" class="subbtn rechangebtn J_tuli_pay" title="充值">充值</a>
                 <?php }else{?>
                 	<a href="javascript:void(0);" <?php if(Yii::app()->user->id<=0){ echo "onclick=\"$.User.loginController('login')\"";}else{echo ' target="_blank" ';}?> id="exchange" class="subbtn rechangebtn" title="充值">充值</a>
                 <?php }?>
				                
                 <div class="giftnamefram ellipsis">
                    <ul>
                    	<?php if(isset($gift['dotey']['list'])):?>
	                    	<?php foreach($gift['dotey']['list'] as $row):?>
	                    	<li><a href="javascript:Gift.selectDotey(<?php echo $row['uid'];?>,'<?php echo $row['nickname'];?>')"  rel="<?php echo $row['uid'];?>"><?php echo $row['nickname'];?></a></li>
	                    	<?php endforeach;?>
                    	<?php else:?>
                    		<li><a href="javascript:Gift.selectDotey(<?php echo $gift['dotey']['uid'];?>,'<?php echo $gift['dotey']['nickname'];?>')"  rel="<?php echo $gift['dotey']['uid'];?>"><?php echo $gift['dotey']['nickname'];?></a></li>
                    	<?php endif;?>
                    </ul>
                  </div><!--.giftname-->
                  <div class="numname">
                   	<ul></ul>
                  </div><!--.numname-->
             </div>
        </div><!--.gifts-->
      <!--礼物数量-->
	