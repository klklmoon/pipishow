<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
    
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">收礼记录</a></li>
        </ul><!-- .main-menu -->
        
<div id="MainCon">
           <div class="cooper-list">
			  <form action="<?php echo $this->createUrl('account/doteygifts');?>" method="post" id="account_gifts">
              <p class="search2" style="width:300px;">
			  <input class="date Wdate" id="startData" type="text" name="stime" />
			  至
			  <input class="date Wdate" id="endData" type="text" name="etime" />
             <a href="#" style="display:inline-block; float:right;" onclick="javascript:document.getElementById('account_gifts').submit();">
			 <img src="<?php echo $this->pipiFrontPath;?>/fontimg/account/search.jpg" /></a>
			 </p> 
			  </form>
			  
				<?php
				if($gifts) {
				?>
				<p>合计：礼物 <?php echo $count['num'];?> &nbsp;&nbsp; 魅力点 <?php echo $count['points'];?></p>
				<table width="760" border="1" bordercolor="#DDDDDD">
					<tr bgcolor="#F5F5F5" class="biaot">
						<td width="180" height="40">时间</td>
						<td width="200" height="40">送礼人</td>
						<td width="150" height="40">收礼地点</td>
						<td width="120" height="40">礼物</td>
						<td width="110" height="40">数量</td>
					</tr>
					<?php
					foreach($gifts as $k=>$v) {
						$_info = unserialize($v['info']);
					?>
					<tr>
						<td height="30"><?php echo date('Y-m-d H:i:s',$v['create_time']);?></td>
						<td height="30"><?php echo $_info['sender'];?> (<?php echo $v['uid'];?>)</td>
						<td height="30"><a href="#" class="undo"><?php echo $_info['receiver'],'(',$v['to_uid'],')';?></a></td>
						<td height="30"><?php echo isset($_info['zh_name']) ? $_info['zh_name'] : $_info['gift_zh_name'];?></td>
						<td height="30"><?php echo $v['num'];?></td>
					</tr>
					<?php
					}
					?>
				</table>
				<!--翻页-->
				<?php
					$counts = $count['count'];
					$page = $count['page'];
					$page_num = $count['page_num'];
					echo '</table>
					 <p>'.$counts.' 条记录 '.$page.' / '.$page_num.' 页</p>';
					echo '<ol class="page">
							<li><a href="'.($this->createUrl('account/doteygifts'.$page_url)).'">首页</a></li>';
						$_page = $page > 1 ? ( ($page_num-$page < 2) ? (($page_num - 2 > 0) ? $page_num -2 : 1) : $page - 1) : 1;
						for($_p = $_page; $_p <= $page_num; $_p++){
							echo '<li><a href="'.($this->createUrl('account/doteygifts'.$page_url,array('page'=>$_p))).'" '.($_p==$page ? 'style="background:#ffb6e2"' : '').'>'.$_p.'</a></li>';
							if(($_p - $_page) == 2) {
								break;
							}
						}
					echo	'<li><a href="'.($this->createUrl('account/doteygifts'.$page_url,array('page'=>$page_num))).'">尾页</a></li>
						 </ol>';
				?>
				<?php
				}else{
					echo '暂无记录';
				}
				?>
           </div><!-- .cooper-list 礼物 -->   
                     
      </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->

