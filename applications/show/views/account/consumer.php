<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
    
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">购买</a></li>
            <li><a href="#">送礼</a></li>
            <li><a href="#">中奖纪录</a></li>
            <li><a href="#">其他</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
		   <?php
			if($buy_record['list']){
				echo '<table width="650" border="1" bordercolor="#DDDDDD">
				  <tr bgcolor="#F5F5F5" class="biaot">
					<td width="200" height="40">购买时间</td>
					<td width="300" height="40">详情</td>
					<td width="150" height="40">皮蛋变化</td>
				  </tr>';
				foreach($buy_record['list'] as $k=>$v)
				{
					echo '<tr>
						<td height="30">'.(date('Y-m-d H:i:s',$v['ctime'])).'</td>
						<td height="30">'.$v['info'].'</td>
						<td height="30">'.$v['pipiegg'].'</td>
					</tr>';
				}
				echo '</table>
				 <p>'.$buy_record['count'].' 条记录 '.$buy_record['page'].' / '.$buy_record['page_num'].' 页</p>';
				echo '<ol class="page">            
						<li><a href="?r=account/consumer&page=1">1</a></li>
						<li><a href="?r=account/consumer&page=2">2</a></li>
						<li><a href="?r=account/consumer&page=3">3</a></li>
						<li><a href="?r=account/consumer&page=2" rel="next">下一页</a></li>
						<li><a href="?r=account/consumer&page=3">尾页</a></li>
					 </ol>';
			}else{
				echo '暂无记录';
			}
		   ?>
           </div><!-- .cooper-list 购买 -->   
           
           <div class="cooper-list onhide">
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
						<td height="30"><?php echo $_info['gift_zh_name'];?></td>
						<td height="30"><?php echo $v['num'];?></td>
						<td height="30"><?php echo $v['dedication'] > 0 ? ('+'.$v['dedication']) : '';?></td>
						<td height="30"><?php echo $v['dedication'];?></td>
						<td height="30"><?php echo $v['gift_type']==1 ? '背包库存' : '正常购买';?></td>
					  </tr>
				  <?php
					}
				  }
				  ?>				  
				</table>
             <p><?php echo $send_record['count'];?>条记录 <?php echo $send_record['page'];?> / <?php echo $send_record['page_num'];?> 页</p>
			 <ol class="page">            
				<li><a href="?r=account/consumer&page=1">1</a></li>
				<li><a href="###">2</a></li>
				<li><a href="###">3</a></li>
				<li><a href="###" rel="next">下一页</a></li>
				<li><a href="###">尾页</a></li>
			 </ol>
           </div><!-- .cooper-list 送礼 -->
            
      </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->

