<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
  
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">主播资料</a></li>
        </ul><!-- .main-menu -->
<?php
	$uid = $account_user_info['uid'];
	$account = $account_user_info['basic'][$uid];
?>
		<div id="MainCon">
           <div class="cooper-list">
				<input type="hidden" value="<?php echo $dotey_info['archives_id'];?>" id="archives_id" />
              <p>直播间短号：<a href="#"><em><?php echo $uid;?></em></a></p>
              <p>直播间名称：&nbsp;&nbsp;<?php echo $dotey_info['title'];?></p>
              <p>真实姓名：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php 
					if($account['realname']){
						echo $account['realname'];
					}else{
				?>
					<input type="text" id="realname" name="realname" value="<?php echo $account['realname'];?>">
					<em>*&nbsp;&nbsp;一经填写，不可修改。</em>
				<?php
					}
				?>
				</p>
               <p>主播生日：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input class="date Wdate" id="startData1" type="text" name="stime" value="<?php echo $dotey_info['birthday'] > 0 ? date('Y-m-d',$dotey_info['birthday']) : '';?>" />
				<em>*</em>
				<!--<input name="" type="checkbox" value="" class="birth" />不显示</p>-->
              <p>来自：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<select id="channelarea_province" name="channelarea[province]" class='input-large'> </select>
					<select id="channelarea_city" name="channelarea[city][]" class="input-large"></select>
				<em>*</em>
              </p>    
              <p>职业：&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="profession" name="profession" value="<?php echo $dotey_info['profession'];?>"><em>*</em></p>
              <input type="hidden" name="sub_id" value="<?php echo $dotey_info['live_record']['record_id'];?>" />   
              <!--<p>节目介绍：&nbsp;&nbsp;&nbsp;&nbsp;<textarea id="sub_title" cols="" rows="" class="intro"><?php echo $dotey_info['live_record']['sub_title'];?></textarea><em>*&nbsp;&nbsp;当您的节目被首页推荐时，将为您显示该条节目介绍。</em></p>  -->
              <p>个人介绍：&nbsp;&nbsp;&nbsp;&nbsp;<textarea id="description" cols="" rows="" class="intro"><?php echo $dotey_info['description'];?></textarea><em>*&nbsp;&nbsp;显示在直播间里的主播个人介绍。</em></p> 
              <p><a style="cursor:pointer;" id="edit_dotey_info"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/account/conf.jpg" style="margin-left:65px;" /></a></p>
              
         </div><!-- .cooper-list 汇款设置 -->                       
            
            
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