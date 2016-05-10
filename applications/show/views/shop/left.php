<?php 
$this->cs->registerCssFile($this->pipiFrontPath.'/css/shop/shop.css?token='.$this->hash,'all');
$this->cs->registerCssFile($this->pipiFrontPath.'/css/common/propbox.css?token='.$this->hash,'all');
$propsCategory = $this->propService->getPropsCatList();
$class = $this->getAction()->getId() == 'gift' ? 'barvisited' : '';
$numberClass = $this->getAction()->getId() == 'number' ? 'barvisited' : '';
?>
<ul id="shopBar" class="fleft shop-sidebar">
	<?php 
	if ($this->domain_type =='tuli'):
	$_target = '_blank';
	elseif ($this->domain_type =='pptv'):
	$_target = '_blank';
	else:
	$_target = '_self';
	endif;
	?>
	<li class="<?php echo $class?>"><a href="<?php echo $this->getTargetHref($this->createUrl('shop/gift'));?>" target="<?php echo $_target;?>">礼物</a></li>
	<!-- <li class="<?php echo $numberClass?>" ><a href="<?php echo $this->createUrl('shop/number')?>" >靓号</a></li> -->
	<?php 
	foreach($propsCategory as $category): 
	if(!$category['is_display']):
		continue;
	endif;
	$class = $category['en_name'] == $this->getAction()->getId() ? 'barvisited' : '';
	$href = 'shop/'.$category['en_name'];
	?>
 
    <li class="<?php echo $class?>"><a href="<?php echo $this->getTargetHref($this->createUrl($href));?> " target="<?php echo $_target;?>"><?php echo $category['name']?></a></li>
    <?php endforeach;?>
</ul>

<div id="LowStock" class="buy-box buylast">
	<div class="last-con">
    	<p></p>
        <input onclick="$.mask.hide('LowStock');" class="btn cancel" type="button" value="关闭">
    </div>
</div>