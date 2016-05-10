<div class="kong">
  <div class="w1230 relative">
    <div class="intro">
     活动介绍：本次活动分“女神排位赛”与“女神进击战”两个阶段进行。<br/>
     活动时间：<?php echo date('Y年n月d日', strtotime($top_start));?>——<?php echo date('Y年n月d日', strtotime($battle_end));?><br/><br/>
     活动专属礼物：女神，定价10皮蛋 ∕ 个
    </div>
    <div class="giftimg"><img src="/statics/fontimg/activities/battle/nvshen.png" /></div>
  </div>
</div><!--.kong-->


<div class="w1230 main">
  <a id="t"></a>
  <div class="w1010 clearfix">
  
     <div class="round-bj">
       <a href="<?php $this->getTargetHref($this->createUrl('activities/battleTop'));?>#t"><div class="round-1 fleft"><span>活动时间：<?php echo date('n.d H:i', strtotime($top_start));?>---<?php echo date('n.d H:i', strtotime($top_end));?></span></div></a>
       <?php if(time() > strtotime($battle_start)){?>
       <a href="<?php $this->getTargetHref($this->createUrl('activities/battle'));?>#t"><div class="round-3 fleft ml10"><span>活动时间：<?php echo date('n.d H:i', strtotime($battle_start));?>---<?php echo date('n.d H:i', strtotime($battle_end));?></span></div></a>
       <?php }else{?>
       <div class="round-2 fleft ml10"><span>活动时间：<?php echo date('n.d H:i', strtotime($battle_start));?>---<?php echo date('n.d H:i', strtotime($battle_end));?></span></div>
       <?php }?>
     </div><!--.round-bj-->
     
     <div class="rules mt20">用户给主播送“女神”专属礼物，让主播登上女神榜单。<br/>
                  排行榜前16位主播晋级下一轮的女神奖金争夺战，即”女神进击战“。晋级名次按<br/>
                  “先到先得”原则，无并列排行。<br/>
                  直播间全体主播。<br/>
                  16强主播每位主播奖励10万魅力值 
     </div><!--.rules-->
     
     <div class="titbj">“女神站位赛”排名：按收到礼物数量由高到低依次排列。前16名晋级。</div>
     <a id="top" ></a>
     <div class="bigtit"><a href="<?php $this->getTargetHref($this->createUrl('activities/battleTop', array('pos' => 'top')));?>"><img src="/statics/fontimg/activities/battle/shuaxin.png" /></a></div>
     
     <div class="tabbj">
        <div class="anchor1-con">
            <ul class="anchor1-list">
            	<?php foreach ($top as $uid=>$userRow){?>
				<li class="clearfix <?php echo $userRow['rank_order'] <= 3 ? 'no'.$userRow['rank_order'] : '';?>">
					<em class="order"><?php echo $userRow['rank_order'];?></em> <span title="<?php echo $userRow['nk'];?>"><em class="lvlo lvlo-<?php echo $userRow['dk'];?>"></em><?php echo mb_substr((empty($userRow['nk'])?"求昵称":$userRow['nk']),0,4,'UTF-8');?></span>
					<i><?php echo $userRow['gift_num'];?></i>		
                    <code>
                    	<?php foreach($userRow['users'] as $user){?>
                    	<label title="<?php echo $user['nk'];?>"><em class="lvlr lvlr-<?php echo $user['rk'];?>"></em> <?php echo mb_substr((empty($user['nk'])?"求昵称":$user['nk']),0,4,'UTF-8');?>（<?php echo $user['gift_num'].'个';?>）</lable>
                    	<?php }?> 
                    </code>
					<span style="text-align:center;"><?php echo $userRow['update_time'];?></span>
				</li>
				<?php }?>      
              </ul>
        </div>
     </div>
     
  </div><!--.w1000-->
</div><!--.w1230-->

<script type="text/javascript">
var position = '<?php echo $position;?>';
$(function(){

    if(position){
		window.location.href="#"+position;
    }
})
</script>