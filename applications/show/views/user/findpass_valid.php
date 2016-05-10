<div class="clearfix w1000 mt30">
    
    <div class="main" style="width:1000px;">
        <ul class="main-menu clearfix" id="MianList" style="width:960px;">
            <li class="menuvisted"><a href="javascript:void(0)">找回密码</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
              <div class="sendbj" style="height:auto; width:520px;">
              		<?php if($type == 'mail'):?>
            		  抱歉，您的找回密码验证邮件已失效，
            		<?php else:?>
            		抱歉，您的找回密码的短信验证码已失效，或者每天手机找回密码不能超过三次
            		<?php endif;?>
            		<a href="<?php echo $this->createUrl('user/findPass')?>" class="modify_1"> 请重新找回密码</a>
              </div>
              <div class="safetips mt30"><strong style=" color:#000;">温馨提示：</strong><br/>
            	  <?php if($type == 'mail'):?>
			               验证邮件7天内有效，请尽快完成验证。<br/>
			               邮件到达时间可能长达2-3分钟，请耐心等待。<br/>
			      <?php else:?>
			   	  	短信验证码 验证码30分钟内有效<br/>
			                     短信验证码发送时间可能长达2-3分钟，请耐心等待。<br/> 
			     <?php endif;?>                    
			  </div>  
           </div> 
           <div class="cooper-list onhide"></div>
      </div>
     </div>       
</div>
