<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
   
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/income');?>">汇款设置</a></li>
            <li><a href="<?php echo $this->createUrl('account/livetime');?>">直播时长</a></li>
            <li class="menuvisted"><a href="#">魅力提现</a></li>
            <li><a href="<?php echo $this->createUrl('account/billings');?>">收入账单</a></li>
        </ul><!-- .main-menu -->
        
        <div id="MainCon">
		
                   
            <div class="cooper-list">
               <p>现存魅力点：<em datas="charmpoints"><?php echo $exchange_count['now_cash'];?></em>
				＝ (本月收获<em><?php echo $exchange_count['now_income'];?></em> ＋  
				  上月结余<em><?php echo $exchange_count['now_cash'] + $exchange_count['now_exchange'] - $exchange_count['now_income'];?></em> －  
				  本月已兑换<em><?php echo $exchange_count['now_exchange'];?></em>)</p>
               <div class="charm-box">
					<input type="hidden" name="exchangeval" value="<?php echo $exchange_value;?>" />
                   <p>要兑换的金额：<input name="meili" type="text" value='0' onchange="account.exchangeval()" onFocus="if(this.value=='0'){this.value=''; this.style.color='#000000';}" onBlur="if(this.value==''){this.value='0'; this.style.color='#8e8e8e';}" style="color:#8e8e8e;";/>
                   <em style="color:red; margin-left:10px;">元（请输入100的倍数）</em>
                   </p>
                   <p>所需魅力点：&nbsp;&nbsp;<em datas="exchange_charmpoints">0</em></p>
                   <p><a style="cursor:pointer;" onclick="account.exchange()"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/account/conf.jpg" style="margin-left:65px;" /></a></p>
               </div>
               <div class="record">
               		<p>兑换记录：<a>本月累积<?php echo $month_count['nums'];?>次，共计兑换魅力点<?php echo $month_count['amounts'];?>，共计兑换金额<?php echo $month_count['money'];?>元</a></p>
                    <ul class="record-list mb10">
						<?php 
						if($cash_list){
							foreach($cash_list as $k=>$v){
						?>
                    	<li>
							<span><?php echo date('Y-m-d H:i:s',$v['create_time']);?></span>
							<span>兑换金额：<?php echo $v['dst_amount'];?></span>
							<span>兑换魅力点：<?php echo $v['org_amount'];?></span>
						</li>
						<?php 
							}
						} 
						?>
                    </ul>
               </div>
               <div class="illustrate">
                <strong>魅力点兑换提现说明：</strong><br/>
1、每月8日至月底（31日）期间为兑换期，可以自由兑现。<br/>
2、每月1至7日为出账冻结期，不可兑换。正常工作日里，平台将及时汇款到您的银行卡。（遇节假日顺延）<br/>
3、有任何疑问或问题，可咨询官方工作人员详解。
                </div>
            </div><!-- .cooper-list 魅力提现-->
			
            
            
        </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->




