<?php foreach($top as $n => $t){ ?>
<li>
	<i class="mt10 pattern-icon"><?php echo $n+1;?></i>
	<img class="mt10" style="display:inline-block;float:left;margin:10px;" src="/images/family/<?php echo $t['family_id'];?>/medal_<?php echo $t['sign'] == 1 ? '0' : $t['level'];?>3.jpg" />
	<span class="pattern-text ellipsis"><a href="<?php echo $this->createHomeUrl($t['family_id']);?>"><?php echo $t['name'];?></a></span>
</li>
<?php } ?>
<?php
if($n < 10){
	for($i = $n+1; $i < 10; $i++) echo "<li></li>\n";
}
?>