<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
    
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">兑换皮蛋</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
              <p>*说明：100皮点或魅力点可以兑换100皮蛋 (兑换时系统默认先兑皮点再兑魅力点)</p>
              <p><strong>现在可兑换皮点和魅力点共 <em data_point="points"><?php echo $consume['egg_points'] + $consume['charm_points'];?></em></strong>  ( 皮点 <em><?php echo $consume['egg_points'];?></em> + 魅力点 <em><?php echo $consume['charm_points'];?></em> )</p>
             <div class="charm-box" style="background:#FFFFCC; width:370px; border:1px solid #FFDDA8; padding-left:20px;"> 
              <p>要兑换的点数&nbsp;&nbsp;&nbsp;<input name="name" id="exchange" type="text" value='0' onFocus="if(this.value=='0'){this.value=''; this.style.color='#000000';}" onBlur="if(this.value==''){this.value='0'; this.style.color='#CCCCCC';}" style="color:#CCCCCC; width:150px;";/> <em>*请输入100的倍数</em> </p>
              <p>兑换皮蛋: &nbsp;&nbsp;&nbsp;<em id="exchange_egg">0个</em></p>
              <p><a style="cursor:pointer;" onclick="account.exchangeEgg()"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/account/conf.jpg" style="margin-left:65px;" /></a></p>
             </div> 
             
            <div class="mt20" style="line-height:25px;">
				<strong>最近三次兑换记录如下：</strong><br/>
				<?php foreach($exchange_list as $k=>$v): ?>
					<?php echo date('Y-m-d  H:i:s',$v['create_time']);?>  &nbsp;&nbsp;&nbsp;兑换数量：<?php echo $v['oamount'];?> &nbsp;&nbsp;&nbsp;&nbsp;获得皮蛋：<?php echo $v['damount'];?><br />
				<?php endforeach; ?>
            </div>
           </div><!-- .cooper-list 汇款设置 -->                       
            
            
        </div><!--#MainCon-->
     </div><!-- .main -->  
</div><!-- .w1000 -->


