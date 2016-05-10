<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
    
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/buy');?>">道具</a></li>
            <li><a href="<?php echo $this->createUrl('account/buyGift');?>">商城礼物</a></li>
            <li><a href="<?php echo $this->createUrl('account/send');?>">送礼</a></li>
            <li class="menuvisted"><a href="#">点歌</a></li>
            <li><a href="<?php echo $this->createUrl('account/game');?>">游戏</a></li>
            <!--<li><a href="<?php echo $this->createUrl('account/prize');?>">中奖记录</a></li>
            <li><a href="<?php echo $this->createUrl('account/myother');?>">其他</a></li>-->
             <li><a href="<?php echo $this->createUrl('account/numberBuy');?>">靓号</a></li>
        </ul><!-- .main-menu -->
        
		<div id="MainCon">  
           
			<div class="cooper-list">
			<?php if($list): ?>
            <table width="750" border="1" bordercolor="#DDDDDD" >
				<tr bgcolor="#F5F5F5" class="biaot">
					<td width="180" height="40">时间</td>
					<td width="200" height="40">主播</td>
					<td width="150" height="40">点歌</td>
					<td width="120" height="40">状态</td>
				</tr>
				<?php foreach($list as $k=>$v):?>
				<tr>
					<td height="30"><?php echo date('Y-m-d H:i', $v['create_time']);?></td>
					<td height="30"><?php echo $v['dotey_name'];?>(<?php echo $v['target_id'];?>)</td>
					<td height="30"><?php echo $v['name'];?></td>
					<td height="30"><?php echo $v['is_handle']==0 ? '未处理' : ($v['is_handle']==1 ? '已处理' : '已取消');?></td>
				</tr>
				<?php endforeach;?>
			</table>
			<?php
				$counts = $songs['count'];
				$page = $songs['page'];
				$page_num = $songs['page_num'];
				echo '<p>'.$counts.' 条记录 '.$page.' / '.$page_num.' 页</p>';
				echo '<ol class="page">
						<li><a href="?r=account/vod'.$page_url.'">首页</a></li>';
				$_page = $page > 1 ? ( ($page_num-$page < 2) ? (($page_num - 2 > 0) ? $page_num -2 : 1) : $page - 1) : 1;
				for($_p = $_page; $_p <= $page_num; $_p++){
					echo '<li><a href="?r=account/vod'.$page_url.'&page='.$_p.'" '.($_p==$page ? 'style="background:#ffb6e2"' : '').'>'.$_p.'</a></li>';
					if(($_p - $_page) == 2) {
						break;
					}
				}
				echo	'<li><a href="?r=account/vod'.$page_url.'&page='.$page_num.'">尾页</a></li>
					 </ol>';
			else:?>
				没有记录, 赶紧去给喜欢的主播点歌吧
			<?php endif;?>
			</div><!-- .cooper-list 送礼 -->
            
		</div><!--#MainCon-->
	</div><!-- .main -->        
</div><!-- .w1000 -->

