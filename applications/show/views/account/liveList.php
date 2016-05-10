<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
   
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/income');?>">汇款设置</a></li>
            <li class="menuvisted"><a href="<?php echo $this->createUrl('account/livetime');?>">直播时长</a></li>
            <li><a href="<?php echo $this->createUrl('account/cash');?>">魅力提现</a></li>
            <li><a href="<?php echo $this->createUrl('account/billings');?>">收入账单</a></li>
        </ul><!-- .main-menu -->
        
        <div id="MainCon">
            
            <div class="cooper-list">
			<form action="<?php echo $this->createUrl('account/livelist');?>" method="post" id="account_search_live_time">
              <p>查询月份：
				<input class="date Wdate" name="month" id="liveListDate" type="text">
				<img onclick="account.liveListSearch(this)" src="<?php echo $this->pipiFrontPath;?>/fontimg/account/search.jpg" />
			  </p> 
			  </form>
              <p><em><?php echo $countRecord['moon'];?>月直播总时长：<?php echo $countRecord['dedi'];?>，<?php echo $countRecord['day'];?>个有效天</em></p>
				<table width="685" border="1" bordercolor="#DDDDDD">
				  <tr bgcolor="#F5F5F5" class="biaot">
					<td width="150" height="40">日期 </td>
					<td width="150" height="40">开播开始 </td>
					<td width="135" height="40">直播时长</td>
					<td width="250" height="40">节目预告</td>
				  </tr>
				  <?php if($liveList):
					foreach($liveList as $k=>$v){
				  ?>
				  <tr>
					<td height="42"><?php echo date('Y-m-d',$v['live_time']);?></td>
					<td height="42"><?php echo date('H:i',$v['live_time']);?></td>
					<td height="42"><?php echo $v['duration'];?></td>
					<td height="42"><?php echo $v['sub_title'];?></td>
				  </tr>
				  <?php
					}
				  ?>
				</table>
				<?php
					$counts = $liveCount['count'];
					$page = $liveCount['page'];
					$page_num = $liveCount['page_num'];
					echo '<p>'.$counts.' 条记录 '.$page.' / '.$page_num.' 页</p>';
					echo '<ol class="page">
							<li><a href="?r=account/livelist'.$page_url.'">首页</a></li>';
					$_page = $page > 1 ? ( ($page_num-$page < 2) ? (($page_num - 2 > 0) ? $page_num -2 : 1) : $page - 1) : 1;
					for($_p = $_page; $_p <= $page_num; $_p++){
						echo '<li><a href="?r=account/livelist'.$page_url.'&page='.$_p.'" '.($_p==$page ? 'style="background:#ffb6e2"' : '').'>'.$_p.'</a></li>';
						if(($_p - $_page) == 2) {
							break;
						}
					}
					echo	'<li><a href="?r=account/livelist'.$page_url.'&page='.$page_num.'">尾页</a></li>
						 </ol>';
						 
				else: ?>
				</table>
				<?php endif;?>
				 <br/><br/><br/>
				 
						   <div class="illustrate">
							<strong>有效时长的系统计时说明：</strong><br/>
			1、未签约入驻他人小站，且在自己直播间内直播，才计算有效直播时间。<br/>
			2、主播点击开播后，直播间开始自动记录主播视频信号传输时长，每5分钟纪录和更新一次时长，未够5分钟，信号断开，不记录时长。主播直播自动断开，只要够5分钟，每日直播时长自动记录，主播不会再因为档期丢失，而丢失时长。<br/>
			3、主播每日直播时长达到2小时以上为一个有效天。
			<p style="color:#060;">据统计,每一位收入稳定的主播平均每月直播时长都在60个小时以上！ 想成为一位高收入主播,至少要先保证基本的直播时间！</p>
                </div>
            </div><!-- .cooper-list 时长结算--> 
            
        </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->

