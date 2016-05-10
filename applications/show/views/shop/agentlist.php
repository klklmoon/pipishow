<?php 
$userAgents = $this->agentsService->getAgentList();
?>
<div class="agentbtn">
	<span>通过代理购买</span>
	<div class="buyagent-box">
		<ul class="buyagent-list">
			<?php 
				foreach($userAgents as $userAgent): 
			?>
			<li><a href="javascript:selectAgent(<?php echo $userAgent['uid'];?>,'<?php echo mb_substr($userAgent['agent_nickname'],0,8,'UTF-8');?>');"><span class="ellipsis"><?php echo $userAgent['agent_nickname']?></span><em>(<?php echo $userAgent['uid']?>)</em></a></li>
			<?php endforeach;?>
		</ul>
	</div>
	<!--.buyagent-box-->
</div>
<!--.agentbtn-->
<p class="agentname">
	<span>您选择的代理：</span> 
	<span><em class="pink"></em><em
		class="close"></em></span>
</p>
<script type="text/javascript">
$('.agentbtn').hover(function(){
    $(this).addClass('onagent');
    $('.buyagent-box').css('display','block');
},function(){
    $(this).removeClass('onagent');
    $('.buyagent-box').css('display','none');
});
$('.agentname .close').live('click',function(){
    $(this).parent('span').html('无');
    selectAgent(0,'');
});
$('.agentbtn').click(function(){
    $(this).removeClass('onagent');
    $('.buyagent-box').css('display','none');
});
function showAgent(n,id)
{
	var htmlstr='';
	htmlstr=n+'<br>'+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;('+id+')';
	return htmlstr;
}
</script>
<!--.agentname-->
