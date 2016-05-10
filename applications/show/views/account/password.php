<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>


<div class="clearfix w1000 mt30">
	
	<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
	
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/main');?>">个人资料</a></li>
            <li class="menuvisted"><a href="#">密码修改</a></li>
        </ul><!-- .main-menu -->
        
        <div id="MainCon">
           <div class="cooper-list">
              <p>用户名：<?php echo $this->viewer['login_name'];?></p>
              <p>用户ID：<?php $this->getUserJsonAttribute('uid',false,true);?></p>
			  <input type="hidden" id="uid" name="uid" value="<?php echo $this->viewer['login_uid']?>" />
			  <p>原密码&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="password" id="password" name="password"></p>
              <p>新密码&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="password" id="newpswd" name="newpswd"></p>
              <p>确认新密码&nbsp;&nbsp;<input type="password" id="renewpswd" name="renewpswd"></p>
              <p><a><img id="change_pswd"  src="<?php echo $this->pipiFrontPath;?>/fontimg/account/conf.jpg" style="display:block; margin-left:65px; cursor:pointer;" /></a></p>
           </div>
        </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->
