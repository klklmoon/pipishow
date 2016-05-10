<div id="Star" class="popbox" style="display: none; margin-left: -150px; margin-top: -103.5px; top: 172px; left: 720px;">
	<div class="poph">
  	    <span>消费星级</span>
    	<a title="关闭" class="closed" onclick="$.mask.hide('Star');"></a>
    </div>
    <div class="popcon">
    	<ul class="paysong overhide">
            <li></li>
        </ul>
        <a style="margin-left:140px;" href="index.php?r=public/help#a16" class="pink" id="starInfo" target="<?php echo $this->target?>">消费星说明？</a>
        
    </div>
</div>


<div id="StarIntro" class="popbox starbox" style="display: none; margin-left: -150px; margin-top: -103.5px; top: 172px; left: 720px;">
	<div class="poph">
  	    <span>消费星说明</span>
    	<a title="关闭" class="closed" onclick="$.mask.hide('StarIntro');"></a>
    </div>
    <div class="popcon starcon">
    	 <p>1、什么是计算周期？<br>
每两个星期为一个自然周（第一周周一至第二周周日），计算周期14天中的消费皮蛋数。计算周结束后，消费清空，进入下一个计算周。</p>
			<p>2、什么是延长显示周期？<br>
为了避免和解决用户在计算周最后一天才达到星级条件，星级显示周期过短的问题，故计算周期结束后的一周（即下一个周期的第一周）为星级延长显示周期，即新的消费开始计算了，但显示的依旧是前两周的星级。</p>
            <p>3、什么是立即升级规则？<br>
用户在计算周期内，消费超过200个皮蛋以上，就将立即并且自动升级到1星。（刷新后就可显示）根据您的消费额度，您在计算周期内升级，就会立刻获得新的星级（刷新后就可显示）。</p>
            <p>小窍门：因此，如果您在计算周第一天就点亮消费星，您的星星最长显示是21天。同理，如果您在计算周期最后最后一天升到星级，您的星星也可以最低显示8天。</p>
            <p>4、为什么星星掉了，或者消失了？<br>
当一个周期结束了，下一个周期的第一周为延长显示期，又是新的计算周期。因为计算周期是清零计算的，所以，当您在这一周中消费没有超过200皮蛋，您的星星，将在这之后消失。或者您的消费额度没有达到或者超过之前的星级消费，您的星星就会显示为掉星。</p>       
    </div>
</div>

<script type="text/javascript">
$('#starInfo').bind('click',function(){
	//$('#Star').hide();
	//$('#StarIntro').show();
})
</script>