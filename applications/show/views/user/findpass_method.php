<div class="clearfix w1000 mt30">
    
    <div class="main" style="width:1000px;">
        <ul class="main-menu clearfix" id="MianList" style="width:960px;">
            <li class="menuvisted"><a href="#">找回密码</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
           		<?php if($reg_email || $reg_mobile):?>
	               <p>为了您的帐号安全，我们为您提供了以下方式找回密码：</p>
	               <p>
	               <?php if($reg_email):?>
	               <a href="<?php echo $this->createUrl('user/findPass&step=mail') ?>" class="modify_1" style="width:120px;" >通过邮箱找回密码</a>  
	               <?php 
	               		endif;
	               		if($reg_mobile):
	               ?>
	               <a href="<?php echo $this->createUrl('user/findPass&step=mobile') ?>" class="modify_1 ml20" style="width:120px;">通过手机找回密码</a>
	               <?php 
	               		endif;
	               ?>
              	 </p>
              	<?php else:?>
              	   <div class="sendbj" style=" width:580px;">您的账号没有绑定手机或邮箱，存在很大的安全隐患，皮皮提醒您通过客服申诉找回密码后立即进行绑定。</div>
	               <p>
	                 <span class="fleft">客服申诉QQ &nbsp;</span>
					 <a class="fleft" href="http://wpa.qq.com/msgrd?v=3&uin=800070126&site=qq&menu=yes" target="_blank">
						<img border="0" style="vertical-align:middle;" title="艾乐" alt="艾乐" src="http://wpa.qq.com/pa?p=3:800070126:45">
					 </a>
	               </p>
	               <br/>
              	<?php endif;?>
           </div>
           <div class="cooper-list onhide">
           </div>
      </div>
     </div>   
</div>
