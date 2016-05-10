<div class="kong">
  <div class="w1000 relative">
   <a href="javascript:void(0)" onclick="getAward()"><img src="<?php echo  Yii::app()->getController()->pipiFrontPath;?>/fontimg/activities/luckstar/lq.gif" /></a>
  </div>
</div><!--.kong-->


<div class="w1000 relative main">

  <div class="vote">
    <div class="cont"></div>
    
  </div>
  
  <div class="intro">
     <img src="<?php echo  Yii::app()->getController()->pipiFrontPath;?>/fontimg/activities/luckstar/intro.jpg" />
     <span>1、中奖用户请务必在次日22:00前领取奖励（过期无效），奖励货币将直接充入中奖账号内。<br/>
     2、每天晚上从22：00开始第一位送出特别礼物“幸运星”时获得500倍大奖的用户，将赢得额外的货币奖励。<br/><br/>
     
     *本活动最终解释权归皮皮乐天所有</span>
  </div><!-- intro -->
  
  <div class="luckstar mt20">
     <div class="fleft">
        <div class="tit w225">每日幸运星</div>
        <div class="conbox">
        <?php if(isset($luckStar['tStar'])):?>
        	<?php if(!empty($luckStar['tStar'])):?>
           <p><span>昵称：<?php echo $luckStar['tStar']['nickname'];?></span> <em class="lvlr lvlr-<?php echo $luckStar['tStar']['rank'];?>"></em></p>
           <p>时间：<?php echo date('H:i:s',$luckStar['tStar']['create_time']);?></p>
           <p>奖励：<?php echo $luckStar['tStar']['award'];?>皮蛋</p>
           <?php endif;?> 
        <?php endif;?> 
        </div>
     </div><!-- fleft -->
     
   <div class="fleft ml15">
        <div class="tit w225">昨日幸运星</div>
        <div class="conbox">
        <?php if(isset($luckStar['yStar'])):?>
        	<?php if(!empty($luckStar['yStar'])):?>
           <p><span>昵称：<?php echo $luckStar['yStar']['nickname'];?></span> <em class="lvlr lvlr-<?php echo $luckStar['yStar']['rank'];?>"></em></p>
           <p>时间：<?php echo date('H:i:s',$luckStar['yStar']['create_time']);?></p>
           <p>奖励：<?php echo $luckStar['yStar']['award'];?>皮蛋</p>
           <?php endif;?> 
        <?php endif;?> 
        </div>
     </div><!-- fleft -->
     
     <div class="fleft ml15">
        <div class="tit w437">超级幸运星</div>
        <div class="conbox1">
           <table>
           		<tr style="color:#ff0000; font-size:14px;">					
					<td width="40">排名</td>
                    <td width="120">用户昵称</td>					
					<td>等级</td>
                    <td width="80">得奖次数</td>
				</tr>
				<?php if(isset($luckStar['sStar'])):?>
				<?php foreach($luckStar['sStar'] as $key=>$row):?>	
                <tr>					
					<td width="40"><?php echo $key+1;?></td>
                    <td width="120"><?php echo $row['nickname'];?></td>
					<td><em class="lvlr lvlr-<?php echo $row['rank'];?>"></em></td>
					<td width="80"><?php echo $row['num'];?></td>
				</tr>
				<?php endforeach;?>
				<?php endif;?>	
           </table>
        </div>
     </div><!-- fleft -->
  </div><!-- luckstar -->

</div><!--.w1000-->
<script type="text/javascript">
function getAward(){
	if($.User.getSingleAttribute('uid',true)<=0){
		$.User.loginController('login');
		return;
	}
	$.ajax({
		type:'POST',
		url:'index.php?r=activities/getAward',
		data:{uid:$.User.getSingleAttribute('uid',true)},
		dataType:'json',
		async: false, 
		success:function(data){
			if(data){
				text=data.message+'<br/><a href="javascript:hideAward()"><img src="<?php echo  Yii::app()->getController()->pipiFrontPath;?>/fontimg/activities/luckstar/conf.jpg" /></a>';
				$(".cont").empty().html(text);
				$(".vote").show();
			}
			
		}
	});
	$(".vote").show();
}
function hideAward(){
	$(".vote").hide();
}
</script>
