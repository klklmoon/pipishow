<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
    
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">点歌记录</a></li>
        </ul><!-- .main-menu -->
        
		<div id="MainCon">           
			<div class="cooper-list">
				<form action="<?php echo $this->createUrl('account/song');?>" method="post" id="account_song_search">
				<p class="search2" style="width:300px;">
					<input class="date Wdate" id="startData" type="text" name="stime" />
					&nbsp;&nbsp;至&nbsp;&nbsp;
					<input class="date Wdate" id="endData" type="text" name="etime" />
					<a href="javascript:;" style="display:inline-block; float:right;">
					<img src="<?php echo $this->pipiFrontPath;?>/fontimg/account/search.jpg" /></a>
				</p> 
				</form>
				<p>本月共接受点歌<em><?php echo $song_count['nums'];?></em>次, 获得魅力点<em><?php echo $song_count['charm_points'];?></em></p>
				<table width="678" border="1" bordercolor="#DDDDDD">
					<tr bgcolor="#F5F5F5" class="biaot">
						<td width="180" height="40">时间</td>
						<td width="200" height="40">赠送人</td>
						<td width="150" height="40">点歌</td>
						<td width="120" height="40">状态</td>
					</tr>
					<?php
						foreach($songs['list'] as $k=>$v){
					?>
					  <tr>
						<td height="30"><?php echo date('Y-m-d H:i', $v['create_time']);?></td>
						<td height="30"><?php echo $v['userName'];?>(<?php echo $v['uid'];?>)</td>
						<td height="30"><?php echo $v['name'];?></td>
						<td height="30"><?php echo $v['is_handle']==0 ? '未处理' : ($v['is_handle']==1 ? '已处理' : '已取消');?></td>
					  </tr>
					<?php
						}
					?>
				</table>
				<?php
					$counts = $songs['count'];
					$page = $songs['page'];
					$page_num = $songs['page_num'];
					echo '<p>'.$counts.' 条记录 '.$page.' / '.$page_num.' 页</p>';
					echo '<ol class="page">
							<li><a href="?r=account/song'.$page_url.'">首页</a></li>';
					$_page = $page > 1 ? ( ($page_num-$page < 2) ? (($page_num - 2 > 0) ? $page_num -2 : 1) : $page - 1) : 1;
					for($_p = $_page; $_p <= $page_num; $_p++){
						echo '<li><a href="?r=account/song'.$page_url.'&page='.$_p.'" '.($_p==$page ? 'style="background:#ffb6e2"' : '').'>'.$_p.'</a></li>';
						if(($_p - $_page) == 2) {
							break;
						}
					}
					echo	'<li><a href="?r=account/song'.$page_url.'&page='.$page_num.'">尾页</a></li>
						 </ol>';
						 
				?>
			</div><!-- .cooper-list 点歌 -->                          
            
		</div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->


