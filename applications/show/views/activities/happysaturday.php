
<div class="kong"></div>

<div class="w1020 bags ovhide clearfix">
	<div class="lq_1">
		<a href="javascript:void(0);"><img id="OrdinaryGiftBag"
			src="<?php echo $this->pipiFrontPath;?>/fontimg/activities/happysaturday/lq.jpg" /></a>
	</div>
</div>
<!-- sell -->

<div class="w1020 bang ovhide">
	<div class="intro">
		1、快乐大礼包每人每周六只能领取1次 <br /> 2、勋章有效期为1天(24h)，若用户已领取小财神勋章，则小财神勋章即时升级成大财神勋章
		<br />3、领取礼包之后，玉白菜和大财神勋章会即时生效 <br /> 4、本活动最终解释权归皮皮乐天所有
	</div>
	<div class="lq_2">
		<a href="javascript:void(0);"><img id="AdvancedGiftBag"
			src="<?php echo $this->pipiFrontPath;?>/fontimg/activities/happysaturday/lq.jpg" /></a>
	</div>
</div>
<!-- bang -->


<div class="w1000"></div>
	<script type="text/javascript">
$(function(){
	$("#OrdinaryGiftBag").click(function(){
		$.ajax({
			type: "POST",
			url: "<?php echo $this->createUrl('Activities/HappSatuReceOrdiGiftBag');?>",
			dataType: "json",
			success: function (resonseData) {
				alert(resonseData.message);
			}
		})
	});

	$("#AdvancedGiftBag").click(function(){
		$.ajax({
			type: "POST",
			url: "<?php echo $this->createUrl('Activities/HappSatuReceAdvaGiftBag');?>",
			dataType: "json",
			success: function (resonseData) {
				alert(resonseData.message);
			}
		});
	});		
});
</script>