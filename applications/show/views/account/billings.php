<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
   
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/income');?>">汇款设置</a></li>
            <li><a href="<?php echo $this->createUrl('account/livetime');?>">直播时长</a></li>
            <li><a href="<?php echo $this->createUrl('account/cash');?>">魅力提现</a></li>
            <li class="menuvisted"><a href="#">收入账单</a></li>
        </ul><!-- .main-menu -->
        
        <div id="MainCon">
            <div class="cooper-list">
			
              <p><em>平台将在每月10至15日间， 将收入现金汇款到您的银行卡，遇节假日顺延。</em></p>
              
				 <table width="770" border="1" bordercolor="#DDDDDD">
				  <tr bgcolor="#F5F5F5" class="biaot">
					<td width="90" height="40">月 </td>
					<td width="100" height="40">魅力提现</td>
					<td width="80" height="40">平台奖励</td>
					<td width="70" height="40">才艺补贴</td>
					<td width="130" height="40">底薪 / 奖金</td>
					<!-- 
					<td width="80" height="40">总额</td>
					 -->
				  </tr>
				  <?php
					if($month){
						foreach($month as $k=>$v){
				  ?>
				  <tr>
					<td height="42"><?php echo $v['month'];?></td>
					
					<td height="42"><?php echo $v['exchange_money'];?></td>
					<td height="42"><?php echo $v['exchange_admin'];?></td>
					<td height="42"><?php echo $v['exchange_art']>0?$v['exchange_art']:0;?></td>
					<td height="42"><a href="<?php echo $this->createUrl('account/doteyNotice');?>">查看主播公告了解</a></td>
					<!-- 
					<td height="42"><?php echo $v['pay']['salary'];?> + <?php echo $v['pay']['bonus']?></td>
					<td height="42"><a><?php echo ($v['pay']['salary'] + $v['pay']['bonus'] + $v['exchange_money'] + $v['exchange_admin'] + $v['exchange_art']);?></a></td>
					 -->
				  </tr>
				  <?php
						}
					}
				  ?>
				</table>    
            </div><!-- .cooper-list 收入账单-->
            
            
        </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->




