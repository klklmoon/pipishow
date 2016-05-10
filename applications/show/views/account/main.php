<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>


<div class="clearfix w1000 mt30">
	
	<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
	
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">个人资料</a></li>
            <?php if($this->isPipiDomain): ?>
			<li style="display:none;"><a href="<?php echo $this->createUrl('account/password');?>">密码修改</a></li>
			<?php endif; ?>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
			<div class="cooper-list">
				<p>
					<span id="account_avatar" class="avatar">
					<img src="<?php echo $this->viewer['avatar'];?>" />
					<span class="account_cursor"><a class="imghead2">修改头像</a></span></span>
					<span id="account_avatar_upload" style="display:none;">
						<?php echo $upload_avatar;?><br/>
						<a href="javascript:void(0);"><em>取消</em></a>
					</span>
				</p>
				<p>用户名：<?php echo $this->viewer['login_name'];?></p>
				<p>用户ID：<?php echo $this->viewer['login_uid'];?></p>
				<input type="hidden" id="uid" name="uid" value="<?php echo $this->viewer['login_uid']?>" />
				<p>昵称&nbsp;&nbsp;<input type="text" id="acc_nick_name" name="nickname" value="<?php echo $account_user_info['basic'][$account_user_info['uid']]['nickname'];?>">&nbsp;&nbsp;每个人的昵称都将是唯一的，修改昵称后，您原来的昵称将有可能被占用。</p>
				<p>性别&nbsp;&nbsp;
					<select name="gender" id="gender" class="sex">
					<option value="0" <?php echo $acount_extend_info['gender']=='0' ? 'selected="selected"' : '';?>>保密</option>
					<option value="1" <?php echo $acount_extend_info['gender']=='1' ? 'selected="selected"' : '';?>>男</option>
					<option value="2" <?php echo $acount_extend_info['gender']=='2' ? 'selected="selected"' : '';?>>女</option>
					</select>
				</p>
				<p>生日&nbsp;&nbsp;
					<input class="date Wdate" id="startData1" type="text" name="stime" value="<?php echo $acount_extend_info['birthday'] > 0 ? date('Y-m-d',$acount_extend_info['birthday']) : '';?>" />
				<!--<span>25岁 金牛座</span>-->
				</p>
				<p>来自&nbsp;&nbsp;
					<select id="channelarea_province" name="channelarea[province]" class='input-large'> </select>
					<select id="channelarea_city" name="channelarea[city][]" class="input-large"></select>
				</p>
				<p><a style="cursor:pointer;" id="edit_user_info"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/account/conf.jpg" style="margin-left:65px;" /></a></p>

				<!--<div class="modify_headimg">修改头像</div>
				<img src="<?php echo $this->pipiFrontPath;?>/fontimg/account/modify_headimg.jpg" border="0" usemap="#Map" />
				<map name="Map" id="Map">
					<area shape="rect" coords="147,69,320,116" href="#" />
					<area shape="rect" coords="147,142,321,187" href="#" />
				</map>-->
			</div>		   
            
            
        </div><!--#MainCon-->
		
     </div><!-- .main -->        
</div><!-- .w1000 -->


<script type="text/javascript">
	<?php
		if($acount_extend_info['province']){
	?>
	$(function(){
		init("channelarea_province","<?php echo $acount_extend_info['province'];?>","channelarea_city","<?php echo $acount_extend_info['city'];?>");
	});
	<?php
	}else{
		echo '$(function(){ init("channelarea_province","请选择省份","channelarea_city","选择城市") });';
	}
	?>
</script>