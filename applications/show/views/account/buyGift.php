<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
    
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/buy');?>">道具</a></li>
            <li class="menuvisted"><a href="#">商城礼物</a></li>
            <li><a href="<?php echo $this->createUrl('account/send');?>">送礼</a></li>
            <li><a href="<?php echo $this->createUrl('account/vod');?>">点歌</a></li>
            <li><a href="<?php echo $this->createUrl('account/game');?>">游戏</a></li>
            <!--<li><a href="<?php echo $this->createUrl('account/prize');?>">中奖记录</a></li>
            <li><a href="<?php echo $this->createUrl('account/myother');?>">其他</a></li>-->
             <li><a href="<?php echo $this->createUrl('account/numberBuy');?>">靓号</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
		   <?php
			if($buy_record['list']){
			?>
				<table width="650" border="1" bordercolor="#DDDDDD">
				  <tr bgcolor="#F5F5F5" class="biaot">
					<td width="200" height="40">购买时间</td>
					<td width="300" height="40">礼物名称</td>
					<td width="300" height="40">数量</td>
					<td width="150" height="40">皮蛋变化</td>
				  </tr>
			<?php
				foreach($buy_record['list'] as $k=>$v)
				{
					$_info = unserialize($v['info']);
					echo '<tr>
						<td height="30">'.(date('Y-m-d H:i:s',$v['create_time'])).'</td>
						<td height="30">'.$giftInfo[$v['gift_id']]['zh_name'].'</td>
						<td height="30">'.$_info['num'].'</td>
						<td height="30"> - '.($giftInfo[$v['gift_id']]['pipiegg'] * $_info['num']).'</td>
					</tr>';
				}
			?>
				</table>
			<?php
				$count = $buy_record['count'];
				$page = $buy_record['page'];
				$page_num = $buy_record['page_num'];
				echo '<p>'.$count.' 条记录 '.$page.' / '.$page_num.' 页</p>';
			?>
				<ol class="page">
					<li><a href="?r=account/buyGift">首页</a></li>
			<?php
				$_page = $page > 1 ? ( ($page_num-$page < 2) ? (($page_num - 2 > 0) ? $page_num -2 : 1) : $page - 1) : 1;
				for($_p = $_page; $_p <= $page_num; $_p++){
					echo '<li><a href="?r=account/buyGift&page='.$_p.'" '.($_p==$page ? 'style="background:#ffb6e2"' : '').'>'.$_p.'</a></li>';
					if(($_p - $_page) == 2) {
						break;
					}
				}
			?>
				<li><a href="?r=account/buyGift&page='<?php echo $page_num?>">尾页</a></li>
				</ol>
			<?php
			}else{
				echo '暂无记录';
			}
		   ?>
           </div><!-- .cooper-list 购买 -->
            
      </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->

