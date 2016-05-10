<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
    
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/bag');?>">礼物背包</a></li>
            <li><a href="<?php echo $this->createUrl('account/props');?>">道具</a></li>
            <li><a href="<?php echo $this->createUrl('account/car');?>">座驾</a></li>
            <li><a href="<?php echo $this->createUrl('account/moon');?>">月卡</a></li>
            <li><a href="<?php echo $this->createUrl('account/vip');?>">vip</a></li>
            <li class="menuvisted"><a href="#">家族守护</a></li>
            <li><a href="<?php echo $this->createUrl('account/number');?>">靓号</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
		   <?php
			if($bagInfo){
			?>
              <table width="750" border="1" bordercolor="#DDDDDD">
			    <tr bgcolor="#F5F5F5" class="biaot">
			  	<td width="150" height="40">种类</td>
			  	<td width="300" height="40">主播信息</td>
			  	<td width="120" height="40">有效期</td>
			  	<!--<td width="180" height="40">操作</td>-->
			    </tr>
				<?php
					foreach($bagInfo as $k=>$v) {
				?>
			    <tr>
			  	<td height="42"><strong><?php echo $propsInfo[$v['prop_id']]['name'];?></strong></td>
			  	<td height="42">主播：<a><?php echo $dotey[$v['target_id']]['nickname'];?></a><span>（ID：<?php echo $v['target_id'];?>）</span></td>
			  	<td height="42"><?php echo $v['time_desc'];?></td>
			  	<!--<td height="42"><button value="守护升级">守护升级</button>&nbsp;&nbsp;&nbsp; <button value="续买">续买</button></td>-->
			    </tr>
				<?php
					}
				?>
			    <tr>
			  	<td height="42"><strong>初级守护</strong></td>
			  	<td height="42">主播：<a>柠檬</a><span>（ID：102238）</span></td>
			  	<td height="42">还剩23天</td>
			  	<!--<td height="42"><button value="守护升级">守护升级</button></td>-->
			    </tr>
			  </table>
			<?php
			}else{
				echo '您还没有守护的主播，赶快去<a href="#" class="undo">商城</a>看看。';
			}
			?>
			  <p><span>*守护到期后，将不再生效；过期5天后自动从列表中消失。</span></p>
           </div><!-- .cooper-list 家族守护 -->
         
	</div><!--#MainCon-->
</div><!-- .main -->        
</div><!-- .w1000 -->


