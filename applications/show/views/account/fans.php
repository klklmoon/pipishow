<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
   
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">粉丝排行</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           
           <div class="cooper-list">
			<?php
				if($fans_list){
			?>
			 <table width="600" border="1" bordercolor="#DDDDDD">
			  <tr bgcolor="#F5F5F5" class="biaot">
				<td width="60" height="40">排名</td>
				<td width="260" height="40">用户昵称</td>
				<td width="120" height="40">等级</td>
				<td width="160" height="40">贡献值</td>
			  </tr>
			  <?php
				foreach($fans_list as $k=>$v){
			  ?>
			  <tr>
				<td height="42"><?php echo $k  + 1 + ($count['page'] -1)*10 ;?></td>
				<td height="42"><?php echo $v['userInfo']['nk'];?> (<?php echo $v['sender_uid'];?>)</td>
				<td height="42"><em class="lvlr <?php echo $v['userInfo']['rk'] > 0 ? 'lvlr-' . $v['userInfo']['rk'] : 'lvlr-0'?>"></em></td>
				<td height="42"><?php echo $v['points'];?></td>
			  </tr>
			  <?php
				}
			  ?>
			</table>
			<?php
				$counts = $count['count'];
				$page = $count['page'];
				$page_num = $count['page_num'];
				echo '</table>
				 <p>'.$counts.' 条记录 '.$page.' / '.$page_num.' 页</p>';
				echo '<ol class="page">
						<li><a href="?r=account/fans'.$page_url.'">首页</a></li>';
					$_page = $page > 1 ? ( ($page_num-$page < 2) ? (($page_num - 2 > 0) ? $page_num -2 : 1) : $page - 1) : 1;
					for($_p = $_page; $_p <= $page_num; $_p++){
						echo '<li><a href="?r=account/fans'.$page_url.'&page='.$_p.'" '.($_p==$page ? 'style="background:#ffb6e2"' : '').'>'.$_p.'</a></li>';
						if(($_p - $_page) == 2) {
							break;
						}
					}
				echo	'<li><a href="?r=account/fans'.$page_url.'&page='.$page_num.'">尾页</a></li>
					 </ol>';
					 
				}
			?>
           </div><!-- .cooper-list 粉丝排行 -->                     
            
        </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->

