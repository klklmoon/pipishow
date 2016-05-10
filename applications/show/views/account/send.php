<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
    
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/buy');?>">道具</a></li>
            <li><a href="<?php echo $this->createUrl('account/buyGift');?>">商城礼物</a></li>
            <li class="menuvisted"><a href="#">送礼</a></li>
            <li><a href="<?php echo $this->createUrl('account/vod');?>">点歌</a></li>
            <li><a href="<?php echo $this->createUrl('account/game');?>">游戏</a></li>
            <!--<li><a href="<?php echo $this->createUrl('account/prize');?>">中奖记录</a></li>
            <li><a href="<?php echo $this->createUrl('account/myother');?>">其他</a></li>-->
             <li><a href="<?php echo $this->createUrl('account/numberBuy');?>">靓号</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">  
           
           <div class="cooper-list">
             <table width="750" border="1" bordercolor="#DDDDDD">
				  <tr bgcolor="#F5F5F5" class="biaot">
					<td width="170" height="40">时间</td>
					<td width="150" height="40">赠送对象</td>
					<td width="90" height="40">礼物名</td>
					<td width="60" height="40">数量</td>
					<td width="90" height="40">贡献值</td>
					<td width="90" height="40">皮蛋变化</td>
					<td width="100" height="40">来源</td>
				  </tr>
				  <?php
				  if($send_record['list']) {
					foreach($send_record['list'] as $k=>$v) {
						$_info = unserialize($v['info']);
				  ?>
					  <tr>
						<td height="30"><?php echo date('Y-m-d H:i:s',$v['create_time']);?></td>
						<td height="30"><?php echo $_info['receiver'];?></td>
						<td height="30"><?php echo isset($_info['gift_zh_name']) ? $_info['gift_zh_name'] : $_info['zh_name'];?></td>
						<td height="30"><?php echo $v['num'];?></td>
						<td height="30"><?php echo $v['dedication'] > 0 ? ('+'.$v['dedication']) : '';?></td>
						<td height="30"><?php echo $v['gift_type']==1 ? '--' : '-'.$v['pipiegg'];?></td>
						<td height="30"><?php echo $v['gift_type']==1 ? '背包库存' : '正常购买';?></td>
					  </tr>
				  <?php
					}
				  }
				  ?>				  
				</table>
            <?php
				$count = $send_record['count'];
				$page = $send_record['page'];
				$page_num = $send_record['page_num'];
				echo '</table>
				 <p>'.$count.' 条记录 '.$page.' / '.$page_num.' 页</p>';
				echo '<ol class="page">
						<li><a href="?r=account/send">首页</a></li>';
					$_page = $page > 1 ? ( ($page_num-$page < 2) ? (($page_num - 2 > 0) ? $page_num -2 : 1) : $page - 1) : 1;
					for($_p = $_page; $_p <= $page_num; $_p++){
						echo '<li><a href="?r=account/send&page='.$_p.'" '.($_p==$page ? 'style="background:#ffb6e2"' : '').'>'.$_p.'</a></li>';
						if(($_p - $_page) == 2) {
							break;
						}
					}
				echo	'<li><a href="?r=account/send&page='.$page_num.'">尾页</a></li>
					 </ol>';
			?>
           </div><!-- .cooper-list 送礼 -->
            
      </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->

