<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
   
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">我的房管</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
             <p><em>当前主播等级可设置<?php echo $managers_nums;?>位房管，当前房管数量<?php echo count($managers);?>人 ；升级到下一级房管人数可达到<?php echo $next_managers_nums;?>人。</em></p>
				<table width="710" border="1" bordercolor="#DDDDDD">
				  <tr bgcolor="#F5F5F5" class="biaot">
					<td width="200" height="40">昵称</td>
					<td width="135" height="40">等级</td>
					<!--<td width="115" height="40">是否守护</td>
					<td width="130" height="40">最近登录</td>-->
					<td width="130" height="40">操作</td>
				  </tr>
				<?php
					foreach($managers as $k=>$v){
				?>
					<tr>
						<td height="42"><?php echo $v['nk'];?></td>
						<td height="42"><em class="lvlr lvlr-<?php echo $v['rk'];?>"></em></td>
						<!--<td height="42">初级</td>
						<td height="42">今日</td>-->
						<td height="42"><a style="cursor:pointer;" class="undo" onclick="account.undo_maneger(<?php echo $v['uid'],',',$v['archives_id']?>)">撤销</a></td>
					</tr>
				<?php
					}
				?>
				</table>
           </div><!-- .cooper-list 我的房管 -->
           
           
        </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->

