<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
    
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">礼物</a></li>
            <li><a href="#">道具</a></li>
            <li><a href="#">贡献值</a></li>
            <li><a href="#">魅力值</a></li>
            <li><a href="#">皮蛋</a></li>
        </ul><!-- .main-menu -->
        
<div id="MainCon">
           <div class="cooper-list">
			<?php 
			if($getGifts){
			?>
             <table width="700" border="1" bordercolor="#DDDDDD">
			  <tr bgcolor="#F5F5F5" class="biaot">
				<td width="180" height="40">时间</td>
				<td width="200" height="40">来源</td>
				<td width="150" height="40">奖励类型</td>
				<td width="60" height="40">数量</td>
				<td width="110" height="40">备注</td>
			  </tr>
			  <?php
				foreach($getGifts['list'] as $k=>$v){
					$_info = unserialize($v['info']);
			  ?>
			  <tr>
				<td height="30"><?php echo date('Y-m-d H:i:s',$v['create_time']);?></td>
				<td height="30">后台赠送</td>
				<td height="30"><?php echo $_info['gift_name'];?></td>
				<td height="30"><?php echo $v['num'];?></td>
				<td height="30"><?php echo $_info['remark'];?></td>
			  </tr>
			  <?php
				}
			  ?>
			</table>
					 <p>5条记录 1/1页</p>
					 <!--翻页-->
			 <ol class="page">                 
				<li><a href="###">1</a></li>
				<li><a href="###">2</a></li>
				<li><a href="###">3</a></li>
				<li><a href="###" rel="next">下一页</a></li>
				<li><a href="###">尾页</a></li>
			 </ol><!--翻页-->
			<?php
			}
			?>
           </div><!-- .cooper-list 礼物 -->   
           
           <div class="cooper-list onhide">
            <table width="700" border="1" bordercolor="#DDDDDD">
				<tr bgcolor="#F5F5F5" class="biaot">
				<td width="170" height="40">赠送时间</td>
				<td width="150" height="40">道具名称</td>
				<td width="100" height="40">来源</td>
				<td width="80" height="40">失效时间</td>
				<td width="200" height="40">详情</td>
				</tr>
				<?php
				foreach($getProps['list'] as $k=>$v){
				?>
				<tr>
					<td height="30"><?php echo date('Y-m-d H:i:s',$v['ctime']);?></td>
					<td height="30"><?php echo $getProps['prop_info'][$v['prop_id']]['name'];?></td>
					<td height="30">后台赠送</td>
					<td height="30"><?php echo $v['time_desc'];?></td>
					<td height="30"><?php echo $v['info'];?></td>
				</tr>
				<?php
				}
				?>
			</table>
             <p><?php echo $getProps['count'];?>条记录 <?php echo $getProps['page'];?> / <?php echo $getProps['page_num'];?>页</p>
           </div><!-- .cooper-list 道具 -->
           
           <div class="cooper-list onhide">
			<?php
			if($getExper){
			?>
            <table width="700" border="1" bordercolor="#DDDDDD">
			  <tr bgcolor="#F5F5F5" class="biaot">
				<td width="160" height="40">时间</td>
				<td width="140" height="40">来源</td>
				<td width="120" height="40">奖励类型</td>
				<td width="120" height="40">数量</td>
				<td width="150" height="40">备注</td>
			  </tr>
			  <?php
				foreach($getExper['list'] as $k=>$v){
					$_info = unserialize($v['info']);
			  ?>
				  <tr>
					<td height="42"><?php echo date('Y-m-d H:i:s',$v['create_time']);?></td>
					<td height="42">系统赠送</td>
					<td height="42">贡献值</td>
					<td height="42"><?php echo $v['dedication'];?></td>
					<td height="42">测试</td>
				  </tr>
			  <?php
				}
			  ?>
			  <tr>
				<td height="42">2012-7-16 &nbsp;22:20:05</td>
				<td height="42">系统赠送</td>
				<td height="42">贡献值</td>
				<td height="42">10000</td>
				<td height="42">测试</td>
			  </tr>
			</table>
            <p>2条记录 1/1页</p>
			<?php
			}
			?>
           </div><!-- .cooper-list 经验 -->                    
            
            
      </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->



