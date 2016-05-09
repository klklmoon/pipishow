<div class="reward novice" id="tasktip">
    <a href="javascript:$.task.task();" id="NewReward" class="new-reward"><span>新手奖励</span></a>
    <div class="tasktip" id="NewReward_tasktip"></div>
</div>
<dl id="RewardShow" class="reward-show"></dl>
<script type="text/javascript">
$(function(){
	var uid=$.User.getSingleAttribute('uid',true);
	if(uid && $.cookie('novice') == uid){
		$('.novice').hide();
		$('#RewardShow').hide();
	}
<?php if($is_archive){?>
	$('.novice').css('bottom', '58px');
	$('#RewardShow').css('bottom', '95px');
<?php }?>
	$('#tasktip').mouseover(function(){
		$('#NewReward_tasktip').show();
	}).mouseout(function(){
		$('#NewReward_tasktip').hide();
	}).click(function(){
		$('#NewReward_tasktip').hide();
	});
});
</script>