<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
   
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/income');?>">汇款设置</a></li>
            <li class="menuvisted"><a href="#">直播时长</a></li>
            <li><a href="<?php echo $this->createUrl('account/cash');?>">魅力提现</a></li>
            <li><a href="<?php echo $this->createUrl('account/billings');?>">收入账单</a></li>
        </ul><!-- .main-menu -->
        
        <div id="MainCon">
            
            <div class="cooper-list">
				<table width="580" border="1" bordercolor="#DDDDDD">
				  <tr bgcolor="#F5F5F5" class="biaot">
					<td width="115" height="40">月份 </td>
					<td width="200" height="40">直播总时长</td>
					<td width="135" height="40">有效天数</td>
					<td width="130" height="40">详情</td>
				  </tr>
				  <?php
					if($month){
					foreach($month as $k=>$v){
				  ?>
					<tr>
						<td height="42"><?php echo $v['month'];?></td>
						<td height="42"><?php echo $v['dedi'];?></td>
						<td height="42"><?php echo $v['day'];?>天</td>
						<td height="42"><a href="<?php echo $this->createUrl('account/livelist');?>&month=<?php echo $v['month'];?>">查看</a></td>
					</tr>
				  <?php
					}
					}
				  ?>
				</table>
               <div class="illustrate">
                <strong>有效时长的系统计时说明：</strong><br/>
					1、未签约入驻他人小站，且在自己直播间内直播，才计算有效直播时间。<br/>
					2、主播点击开播后，直播间开始自动记录主播视频信号传输时长，每1分钟纪录和更新一次时长，未够1分钟，信号断开，不记录时长。主播直播自动断开，只要够1分钟，每日直播时长自动记录，主播不会再因为档期丢失，而丢失时长。<br/>
					3、主播每日直播时长达到2小时以上为一个有效天。
					<p style="color:#060;">据统计,每一位收入稳定的主播平均每月直播时长都在60个小时以上！ 想成为一位高收入主播,至少要先保证基本的直播时间！</p>
                </div>
            </div><!-- .cooper-list 时长结算--> 
            
        </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->




