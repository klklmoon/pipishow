<?php if($type == NUMBER_TYPE_FOUR):
 foreach($list as $item):
      $price = isset($item['confirm_price']) && $item['confirm_price'] ? $item['confirm_price'] : $item['buffer_price'];
                 	

?>
<li id="number_<?php echo $item['number']?>">
     <p class="relative">
     	<span class="jpnumb"><em>靓</em><?php echo $item['number'];?></span>
     	<span class="tipcon" style="display: none;">价格：<?php echo $price;?>皮蛋</span>
     </p>
     <p>
     	<span class="caishen"><?php echo $item['short_desc'];?></span>
     </p>
     <p>
      <a class="numb-btn mt3" title="购买" href="javascript:void(0);" onclick="buyNumber('<?php echo $item['number'];?>','',1)">购买</a> 
      <a class="numb-btn mt3" title="赠送" href="javascript:void(0)" onclick="_sendNumber('<?php echo $item['number'];?>');">赠送</a>
     </p>
</li>
<?php 
endforeach;
else:
 foreach($list as $item):
      $price = isset($item['confirm_price']) && $item['confirm_price'] ? $item['confirm_price'] : $item['buffer_price'];
     
?>

 <li id="number_<?php echo $item['number']?>">
      <p><span class="sixnumb"><em>靓</em> <?php echo $item['number']?></span></p>
      <p>价格:<?php echo $price;?></p>
      <p>
     	 <a class="numb-btn mt3" title="购买" href="javascript:void(0);" onclick="buyNumber('<?php echo $item['number'];?>','',1)">购买</a> 
      	 <a class="numb-btn mt3" title="赠送" href="javascript:void(0)" onclick="_sendNumber('<?php echo $item['number'];?>');">赠送</a>
     </p>
 </li>
 <?php 
 endforeach;
 endif;
 ?>