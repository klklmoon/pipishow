<div class="kong"></div><!--.kong-->
<?php $userService=$this->userService;?>
<?php foreach ($monthHonorRankData as $month=>$honorRank):?>
<div class="w1000 mt20">
  <div class="round-top"></div>
  <div class="bjwhite relative">
     <div class="tit"><?php echo $month."月    ";?>荣誉榜</div>      
      <div class="birth">
        <div class="birthgirl fleft">
        <?php $princessRank=$honorRank['doteyRank'];?>
          <div class="tit">生日公主</div>
          <div class="cont">
             <dl class="conbox-list">
             <?php $princess1=$userService->getUserFrontsAttributeByCondition($princessRank[1]['dotey_id'],true,true);?> 
                <dt>
                   <a href="<?php echo $this->getTargetHref("/".$princessRank[1]['dotey_id'],true,false)?>" title="<?php echo mb_substr((empty($princess1['nk'])?"求昵称":$princess1['nk']),0,8,'UTF-8');?>" target="<?php echo $this->target?>">
                   <img src="<?php echo $userService->getUserAvatar($princessRank[1]['dotey_id'],"small");?>"></a>
                </dt>
                <dd class="ml20">
                   <a href="<?php echo $this->getTargetHref("/".$princessRank[1]['dotey_id'],true,false)?>" title="<?php echo mb_substr((empty($princess1['nk'])?"求昵称":$princess1['nk']),0,8,'UTF-8');?>" target="<?php echo $this->target?>">NO.1 <?php echo mb_substr((empty($princess1['nk'])?"求昵称":$princess1['nk']),0,6,'UTF-8');?></a>
                   <p>收到生日礼物：<?php echo $princessRank[1]['gift_num'];?></p>
                   <p>生日魅力值：<?php echo $princessRank[1]['sum_charm'];?></p> 
               </dd>
             </dl>
             
            <div class="guard mt10 ml15">守护者：
            <?php foreach ($princessRank[1]['guardian'] as $guardian):?>
            <?php $user=$userService->getUserFrontsAttributeByCondition($guardian['uid'],true,true);?> 
            <?php if($guardian['rank']==1):?>
            <a class="numone"><?php echo $guardian['rank'].".".mb_substr((empty($user['nk'])?"求昵称":$user['nk']),0,6,'UTF-8');?></a>
            <em class="lvlr lvlr-<?php echo $user['rk'];?>"></em> <br/>
            <?php else:?> 
            <a class="ml48"><?php echo $guardian['rank'].".".mb_substr((empty($user['nk'])?"求昵称":$user['nk']),0,6,'UTF-8');?></a>
            <em class="lvlr lvlr-<?php echo $user['rk'];?>"></em> 
            	<?php if($guardian['rank']<3):?>
            		<br/>
            	<?php endif;?>
            <?php endif;?>
            <?php endforeach;?>
            </div>
             
             <dl class="conbox-list-small fleft">
             <?php $princess2=$userService->getUserFrontsAttributeByCondition($princessRank[2]['dotey_id'],true,true);?> 
                <dt>
                   <a href="<?php echo $this->getTargetHref("/".$princessRank[2]['dotey_id'],true,false)?>" title="<?php echo mb_substr((empty($princess2['nk'])?"求昵称":$princess2['nk']),0,8,'UTF-8');?>" target="<?php echo $this->target?>">
                   <img src="<?php echo $userService->getUserAvatar($princessRank[2]['dotey_id'],"small");?>"></a>
                </dt>
                <dd class="ml20">
                   <a href="<?php echo $this->getTargetHref("/".$princessRank[2]['dotey_id'],true,false)?>" title="<?php echo mb_substr((empty($princess2['nk'])?"求昵称":$princess2['nk']),0,8,'UTF-8');?>" style="font-size:12px;" target="<?php echo $this->target?>">NO.2 <?php echo mb_substr((empty($princess2['nk'])?"求昵称":$princess2['nk']),0,6,'UTF-8');?></a>
                   <p>收到生日礼物：<?php echo $princessRank[2]['gift_num']?></p>
                   <p>生日魅力值：<?php echo $princessRank[2]['sum_charm']?></p> 
               </dd>
             </dl>
             
             <dl class="conbox-list-small fleft">
             <?php $princess3=$userService->getUserFrontsAttributeByCondition($princessRank[3]['dotey_id'],true,true);?>
                <dt>
                   <a href="<?php echo $this->getTargetHref("/".$princessRank[3]['dotey_id'],true,false)?>" title="<?php echo mb_substr((empty($princess3['nk'])?"求昵称":$princess3['nk']),0,8,'UTF-8');?>" target="<?php echo $this->target?>"><img src="<?php echo $userService->getUserAvatar($princessRank[3]['dotey_id'],"small");?>"></a>
               </dt>
                <dd class="ml20">
                   <a href="<?php echo $this->getTargetHref("/".$princessRank[3]['dotey_id'],true,false)?>" title="<?php echo mb_substr((empty($princess3['nk'])?"求昵称":$princess3['nk']),0,8,'UTF-8');?>" style="font-size:12px;" target="<?php echo $this->target?>">NO.3 <?php echo mb_substr((empty($princess3['nk'])?"求昵称":$princess3['nk']),0,6,'UTF-8');?></a>
                   <p>收到生日礼物：<?php echo $princessRank[3]['gift_num']?></p>
                   <p>生日魅力值：<?php echo $princessRank[3]['sum_charm']?></p> 
               </dd>
             </dl>
             <?php $user2=$userService->getUserFrontsAttributeByCondition($princessRank[2]['guardian']['uid'],true,true);?>
             <div class="guard mt10 fleft" style="width:210px;"><a>守护者：<?php echo mb_substr((empty($user2['nk'])?"求昵称":$user2['nk']),0,6,'UTF-8');?></a>
             <em class="lvlr lvlr-<?php echo $user2['rk'];?>"></em> </div>
             <?php $user3=$userService->getUserFrontsAttributeByCondition($princessRank[3]['guardian']['uid'],true,true);?>
             <div class="guard mt10 fleft" style="width:210px;"><a>守护者：<?php echo mb_substr((empty($user2['nk'])?"求昵称":$user3['nk']),0,6,'UTF-8');?></a>
             <em class="lvlr lvlr-<?php echo $user3['rk'];?>"></em> </div>
          </div><!-- cont -->
        </div><!-- birthgirl -->
        
        <div class="birthboy fright">
        	<?php $princeRank=$honorRank['userRank'];?>
          <div class="tit">生日王子</div>
          <div class="cont">
             <dl class="conbox-list">
             <?php $prince1=$userService->getUserFrontsAttributeByCondition($princeRank[1]['uid'],true,true);?>
                <dt>
                   <a href="javascript:void(0);" title="<?php echo mb_substr((empty($prince1['nk'])?"求昵称":$prince1['nk']),0,8,'UTF-8');?>"><img src="<?php echo $userService->getUserAvatar($princeRank[1]['uid'],"small");?>"></a>
                </dt>
                <dd class="ml20">
                   <a href="javascript:void(0);" title="<?php echo mb_substr((empty($prince1['nk'])?"求昵称":$prince1['nk']),0,8,'UTF-8');?>">NO.1<?php echo mb_substr((empty($prince1['nk'])?"求昵称":$prince1['nk']),0,6,'UTF-8');?></a>
                   <p>送出生日礼物：<?php echo $princeRank[1]['gift_num'];?></p>
                   <p>生日贡献值：<?php echo $princeRank[1]['sum_dedication'];?></p> 
               </dd>
             </dl>
            <div class="guard mt10 ml15">守护主播：
            <?php $guardDotey1=$userService->getUserFrontsAttributeByCondition($princeRank[1]['guardDotey']['uid'],true,true);?>
            <a class="numone"><?php echo mb_substr((empty($guardDotey1['nk'])?"求昵称":$guardDotey1['nk']),0,6,'UTF-8');?></a> <em class="lvlo lvlo-<?php echo $guardDotey1['dk'];?>"></em><br/>
            </div>
             
             <dl class="conbox-list-small fleft">
             <?php $prince2=$userService->getUserFrontsAttributeByCondition($princeRank[2]['uid'],true,true);?>
                <dt>
                   <a href="javascript:void(0);" title="<?php echo mb_substr((empty($prince2['nk'])?"求昵称":$prince2['nk']),0,8,'UTF-8');?>"><img src="<?php echo $userService->getUserAvatar($princeRank[2]['uid'],"small");?>"></a>
                </dt>
                <dd class="ml20">
                   <a href="javascript:void(0);" title="<?php echo mb_substr((empty($prince2['nk'])?"求昵称":$prince2['nk']),0,8,'UTF-8');?>" style="font-size:12px;">NO.2 <?php echo mb_substr((empty($prince2['nk'])?"求昵称":$prince2['nk']),0,6,'UTF-8');?></a>
                   <p>送出生日礼物：<?php echo $princeRank[2]['gift_num'];?></p>
                   <p>生日魅力值：<?php echo $princeRank[2]['sum_dedication'];?></p> 
               </dd>
             </dl>
             
             <dl class="conbox-list-small fleft">
             <?php $prince3=$userService->getUserFrontsAttributeByCondition($princeRank[3]['uid'],true,true);?>
                <dt>
                   <a href="javascript:void(0);" title="<?php echo mb_substr((empty($prince3['nk'])?"求昵称":$prince3['nk']),0,8,'UTF-8');?>"><img src="<?php echo $userService->getUserAvatar($princeRank[3]['uid'],"small");?>"></a>
               </dt>
                <dd class="ml20">
                   <a href="javascript:void(0);" title="<?php echo mb_substr((empty($prince3['nk'])?"求昵称":$prince3['nk']),0,8,'UTF-8');?>" style="font-size:12px;">NO.3 <?php echo mb_substr((empty($prince3['nk'])?"求昵称":$prince3['nk']),0,6,'UTF-8');?></a>
                   <p>送出生日礼物：<?php echo $princeRank[3]['gift_num'];?></p>
                   <p>生日魅力值：<?php echo $princeRank[3]['sum_dedication'];?></p> 
               </dd>
             </dl>
             <?php $guardDotey2=$userService->getUserFrontsAttributeByCondition($princeRank[2]['guardDotey']['uid'],true,true);?>
             <div class="guard mt10 fleft" style="width:210px;"><a>守护主播：<?php echo mb_substr((empty($guardDotey2['nk'])?"求昵称":$guardDotey2['nk']),0,6,'UTF-8');?></a>
             <em class="lvlo lvlo-<?php echo $guardDotey2['dk'];?>"></em> </div>
             <?php $guardDotey3=$userService->getUserFrontsAttributeByCondition($princeRank[3]['guardDotey']['uid'],true,true);?>
             <div class="guard mt10 fleft" style="width:210px;"><a>守护主播：<?php echo mb_substr((empty($guardDotey3['nk'])?"求昵称":$guardDotey3['nk']),0,6,'UTF-8');?></a>
             <em class="lvlo lvlo-<?php echo $guardDotey3['dk'];?>"></em> </div>
             
          </div><!-- cont -->
        </div><!-- birthboy -->
      </div><!-- birth -->        
  </div><!-- bjwhite -->
  
  <div class="round-bottom"></div>
</div><!-- w1000 生日荣誉榜-->
<?php endforeach;?>
