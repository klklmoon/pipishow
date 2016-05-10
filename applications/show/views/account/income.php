<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
   
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">汇款设置</a></li>
            <li><a href="<?php echo $this->createUrl('account/livetime');?>">直播时长</a></li>
            <li><a href="<?php echo $this->createUrl('account/cash');?>">魅力提现</a></li>
            <li><a href="<?php echo $this->createUrl('account/billings');?>">收入账单</a></li>
        </ul><!-- .main-menu -->
        
        <div id="MainCon">
            <div class="cooper-list">
			<?php echo $edit_result;?>
			<form action="" method="post" id="account_edit_dotey_form">
            <p>真实姓名： &nbsp;
				<input name="realname" id="realname" value="<?php echo $userBasic['realname'];?>" type="text"/>
				<em>*</em>&nbsp;&nbsp;
			</p>
            <p>身份证号： &nbsp;
				<input name="id_card" id="id_card" value="<?php echo $userExtend['id_card'];?>" type="text"/> 
				<em>*</em>
			</p>
			<p>开户姓名： &nbsp;
				<input name="bank_user" id="bank_user" value="<?php echo $userExtend['bank_user'];?>" type="text"/> 
				<em>*</em>&nbsp;&nbsp;</p>
			<p>开户银行： &nbsp;
				<input name="bank" id="bank" value="<?php echo $userExtend['bank'];?>" type="text"/> 
				<em>*</em>&nbsp;&nbsp;</p>
            <p>银行卡号： &nbsp;
				<input name="bank_account" id="bank_account" value="<?php echo $userExtend['bank_account'];?>" type="text"/> 
				<em>*</em>&nbsp;&nbsp;</p>
            <p>手机号码： &nbsp;
				<input name="mobile" id="mobile" value="<?php echo $userExtend['mobile'];?>" type="text"/> 
				<em>*</em> </p>
            <p>QQ号码：   &nbsp;&nbsp; 
				<input name="qq" id="qq" value="<?php echo $userExtend['qq'];?>" type="text"/> 
				<em>*</em></p>
            <p>
				<a style="cursor:pointer;" id="edit_dotey_income">
				<img src="<?php echo $this->pipiFrontPath;?>/fontimg/account/conf.jpg" style="margin-left:65px;" /></a>
			</p>
			</form>
            
            <div class="illustrate">
            <strong>汇款设置说明：</strong><br/>
1、请填写完整正确的银行卡号、开户行名称、个人姓名、联系方式等信息；信息有误导致汇款失败，该月结款会失败。如果对填写内容有不解的，请及时联系自己的官方导师，请勿胡乱填写！<br/>
2、每月10-15日期间，平台将你的当月收入汇款到银行卡。（遇节假日顺延）<br/>
3、有任何疑问或问题，可咨询自己的官方导师详解。<br/>
            </div>
          </div><!-- .cooper-list 汇款设置 -->
    
            
        </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->




