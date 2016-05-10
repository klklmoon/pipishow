<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
    
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/buy');?>">道具</a></li>
            <li><a href="<?php echo $this->createUrl('account/buyGift');?>">商城礼物</a></li>
            <li><a href="<?php echo $this->createUrl('account/send');?>">送礼</a></li>
            <li><a href="<?php echo $this->createUrl('account/vod');?>">点歌</a></li>
            <!--<li><a href="<?php echo $this->createUrl('account/prize');?>">中奖记录</a></li>
            <li class="menuvisted"><a href="#">其他</a></li>-->
             <li><a href="<?php echo $this->createUrl('account/numberBuy');?>">靓号</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">  
           
           <div class="cooper-list">
            <table width="750" border="1" bordercolor="#DDDDDD" class="none">
				  <tr bgcolor="#F5F5F5" class="biaot">
					<td width="170" height="40">时间</td>
					<td width="150" height="40">赠送对象</td>
					<td width="90" height="40">礼物名</td>
					<td width="60" height="40">数量</td>
					<td width="90" height="40">贡献值</td>
					<td width="90" height="40">皮蛋变化</td>
					<td width="100" height="40">来源</td>
				  </tr>		  
				</table>
           </div><!-- .cooper-list 送礼 -->
            
      </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->

