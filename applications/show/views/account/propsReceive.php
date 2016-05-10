<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
    
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/giftreceive');?>">礼物</a></li>
            <li class="menuvisted"><a href="#">道具</a></li>
            <li><a href="<?php echo $this->createUrl('account/experReceive');?>">贡献值</a></li>
            <li><a href="<?php echo $this->createUrl('account/charmReceive');?>">魅力值</a></li>
            <li><a href="<?php echo $this->createUrl('account/eggReceive');?>">皮蛋</a></li>
            <li><a href="<?php echo $this->createUrl('account/numberReceive');?>">靓号</a></li>
        </ul><!-- .main-menu -->
        
		<div id="MainCon">
          <div class="cooper-list">
			<?php
			if($getProps['list']){
			?>
            <table width="700" border="1" bordercolor="#DDDDDD">
				<tr bgcolor="#F5F5F5" class="biaot">
				<td width="170" height="40">赠送时间</td>
				<td width="150" height="40">道具名称</td>
				<td width="100" height="40">来源</td>
				<td width="80" height="40">数量</td>
				<td width="80" height="40">失效时间</td>
				<td width="200" height="40">详情</td>
				</tr>
				<?php
				foreach($getProps['list'] as $k=>$v){
				?>
				<tr>
					<td height="30"><?php echo date('Y-m-d H:i:s',$v['ctime']);?></td>
					<td height="30"><?php echo $getProps['prop_info'][$v['prop_id']]['name'];?></td>
					<td height="30"><?php echo $source[$v['source']];?></td>
					<td height="30"><?php echo $v['amount'];?></td>
					<td height="30"><?php echo $v['time_desc'];?></td>
					<td height="30"><?php echo $v['info'];?></td>
				</tr>
				<?php
				}
				?>
			</table>
			 <?php
					$counts = $count['count'];
					$page = $count['page'];
					$page_num = $count['page_num'];
					echo '<p>'.$counts.' 条记录 '.$page.' / '.$page_num.' 页</p>';
					echo '<ol class="page">
							<li><a href="index.php?r=account/propsreceive'.$page_url.'">首页</a></li>';
						$_page = $page > 1 ? ( ($page_num-$page < 2) ? (($page_num - 2 > 0) ? $page_num -2 : 1) : $page - 1) : 1;
						for($_p = $_page; $_p <= $page_num; $_p++){
							echo '<li><a href="index.php?r=account/propsreceive'.$page_url.'&page='.$_p.'" '.($_p==$page ? 'style="background:#ffb6e2"' : '').'>'.$_p.'</a></li>';
							if(($_p - $_page) == 2) {
								break;
							}
						}
					echo	'<li><a href="index.php?r=account/propsreceive'.$page_url.'&page='.$page_num.'">尾页</a></li>
						 </ol>';
			}else{
				echo '没有记录';
			}
			?>
           </div><!-- .cooper-list 道具 -->
           
      </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->



