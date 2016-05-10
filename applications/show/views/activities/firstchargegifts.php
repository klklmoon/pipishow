<div class="kong">
  <div class="w1000 relative">
  <?php if ($this->domain_type == 'tuli'):?>
   <a target="_self" class="J_tuli_pay" href="javascript:void(0);"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/activities/firstchargegifts/charge.gif" /></a>
  <?php else :?>
   <a target="_self" href="<?php echo $this->goExchange();?>"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/activities/firstchargegifts/charge.gif" /></a>
  <?php endif;?>
  </div>
</div><!--.kong-->

<div class="w1000 relative main">
  <!-- 
  <div class="vote none">
    <em><img src="images/x.jpg" /></em>
    <div class="cont">抱歉，您不能领取礼包</div>
  </div>
  
  <div class="vote">
    <em><img src="<?php echo $this->pipiFrontPath;?>/fontimg/activities/firstchargegifts/x.jpg" /></em>
    <div class="cont">领取成功！请去个人中心查看</div>
  </div>
   -->
  
  <a class="pos1" href="javascript:void(0)"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/activities/firstchargegifts/lq.gif" /></a>
  <a class="pos2" href="javascript:void(0)"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/activities/firstchargegifts/lq.gif" /></a>
</div><!--.w1000-->

<script type='text/javascript'>
$(function(){
	//礼包一
	$('.pos1').click(function(){
		$.ajax({
			url:"<?php echo $this->createUrl('activities/firstchargegifts');?>",
			type:'post',
			dataType: "json",
			data:{'op':'collectGiftsOne'},
			success:function(resonseData){
				alert(resonseData.message);
			}
		});
	});
	//礼包二
	$('.pos2').click(function(){
		$.ajax({
			url:"<?php echo $this->createUrl('activities/firstchargegifts');?>",
			type:'post',
			dataType: "json",
			data:{'op':'collectGiftsTwo'},
			success:function(resonseData){
				alert(resonseData.message);
			}
		});
	});
});
</script>
