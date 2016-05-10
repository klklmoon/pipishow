<dt class="clearfix"><span>完成新手任务，领取奖励</span><em>&minus;</em></dt>
<?php if(!$this->isLogin){ ?>
<dd class="btn"><input class="surebtn" type="button" value="请  先  登  录" onclick="$.task.login();"></dd>
<?php } ?>
<?php
	if(!empty($tasklist)){
	$i=0;
	foreach($tasklist as $task){
		$i++;
?>
<dd class="<?php if($i == count($tasklist)) echo 'end ';?>clearfix">
	<a href="<?php if($i == 1){ ?>javascript:$.task.register();<?php }else{ ?>javascript:void(0);<?php } ?>"><?php echo $task['name'];?>，奖励<?php echo round($task['pipiegg'], 1);?>皮蛋</a>
	<div class="reward-l"><?php if($task['done'] == 0){?><em>未完成</em><?php }elseif($task['done'] == 1 && $task['reward'] == 0){ ?><em class="green">已完成</em><?php }else{ ?><em class="greensuc">领取成功</em><?php } ?></div>
	<div class="reward-r">
	<?php if($this->isLogin && $task['reward']==0){?>
	<a href="javascript:$.task.dotask(<?php echo $task['tid'];?>);">点击领取</a>
	<?php }?>
	<?php if($task['done']==0 && !empty($task['url'])){?>
		<?php if($this->isLogin){?>
		<a href="<?php echo $task['url'];?>" target="<?php echo $this->target?>">立即设置</a>
		<?php }else{?>
		<a href="javascript:$.task.login();">立即设置</a>
		<?php } ?>
	<?php } ?>
	</div>
	<div class="reward-pic"><?php if($task['pic'] != ''){ ?><img src="<?php echo $task['pic'];?>"><?php } ?><p><?php echo $task['content'];?></p></div>
</dd>
<?php
	}
	}else{
?>
<dd class="end clearfix">
	<p><a href="javascript:void(0);" style="color:#FF0099;">您的所有任务都已完成，感谢您的参与！</a></p>
</dd>
<script type="text/javascript">
$(function(){
	setInterval(function(){
		$.cookie('novice', <?php echo Yii::app()->user->id;?>);
		$('.novice').hide();
		$('#RewardShow').hide();
	},3000);
});
</script>
<?php
	}
?>
<script type="text/javascript">
$(function(){
	$('#RewardShow dt em').bind('click', function(){
		$.task.close()
	});
	$('#RewardShow dd').hover(function(){
		var w=$(this).find(".reward-pic").width();
		var h=$(this).find(".reward-pic").height();
		var i=$('#RewardShow dd').length - $('#RewardShow dd').index(this) -1;
	    $(this).find(".reward-pic").css({
	        display:"block",
	        top:"-"+(h-43-i*20)+"px",
	        left:"-"+(w+30)+"px",
	        width:w+"px"
	    });
	},function(){
	    $(this).find(".reward-pic").css('display','none');
	});
});
</script>