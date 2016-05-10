<?php $userService=new UserService();?>
  <div class="round-top"></div>
  
  <div class="bjwhite">
     <div class="tit">本月寿星</div>
     	<?php foreach($thisMonthBirthdayDoteys as $doteyInfo): 
     		$sliveText = isset($doteyInfo['status'] ) && $doteyInfo['status'] == 1 ? '直播中' : '待直播';
     		$sliveClass = isset($doteyInfo['status'] ) && $doteyInfo['status'] == 1 ? 'playing' : 'readying';
     	?>
       <div class="ovhide line mt10">
         <ul class="anchor-con clearfix ovhide mt10 fleft">
            <li>
              <div class="anchor-head">
                  <a href="<?php echo $this->getTargetHref("/".$doteyInfo['dotey_id'],true,false)?>" title="美女主播" target="<?php echo $this->target?>">
                  <img src="<?php echo $doteyInfo['pic'];?>"></a><p class="<?php echo $sliveClass?>"><?php echo $sliveText?></p>
              </div>
              <p class="chorname clearfix">
                    <?php if($doteyInfo['show_cake']):?>	
                  	<img style="display: block; float:left; height:22px;" src="<?php echo $this->pipiFrontPath?>/fontimg/activities/happybirthday/cake.gif">
                	<?php endif;?>
                  <a href="<?php echo $this->getTargetHref("/".$doteyInfo['dotey_id'],true,false)?>" title="<?php echo $doteyInfo['title'];?>" target="<?php echo $this->target?>" class="fleft nambtm pink">
                  <?php echo $doteyInfo['title'];?></a>
              </p>
              <p class="time"><?php echo isset($doteyInfo['sub_title'])?$doteyInfo['sub_title']:"";?></p>
              <p class="totbless mt10">TA已经收到<a><?php echo $doteyInfo['giftTotalNum'];?></a>份祝福啦~~~</p>
            </li>
         </ul> 
         <?php $dotey=$userService->getUserFrontsAttributeByCondition($doteyInfo['dotey_id'],true,true);?> 
         <div class="inform fleft">昵称：<?php echo $dotey['nk'];?><br/> 生日：<?php echo date("m月d日",strtotime($doteyInfo['birthday']));?></div> 
         <div class="acceptgift fleft">
           <ul>
           <?php foreach ($doteyInfo['giftDetail'] as $gift_id=>$giftInfo):?>
              <li><img src="<?php echo $activityGiftList[$gift_id]['url'];?>" /><br/><?php echo $giftInfo['gift_num'];?></li>
			<?php endforeach;?>
           </ul>
         </div><!-- acceptgift -->
       </div> 
       <?php endforeach;?>
       
  </div><!-- bjwhite -->
  
  <div class="round-bottom"></div>
 <style>
.page{ list-style-type:none; display:block; width:560px; height:40px;}
.page li{ float:left; display:inline; margin:0 0.3em;}
.page li a{ display:block; border:1px solid #D8D8D8; padding:0.2em 0.5em; color:#666666; background:#F6F4F5;}
.page li a:hover,.page li a:active{ background:#ffb6e2; color:#454545;}
.page .selected a{  display:block; border:1px solid #D8D8D8; padding:0.2em 0.5em; color:#666666; background:#ffb6e2;}
</style>
  <div id="pager">    
  		<?php    
		$this->widget('CLinkPager',array(
            'header'=>'',  
			'firstPageCssClass' => '',  
            'firstPageLabel' => '首页',    
            'lastPageLabel' => '末页',  
            'lastPageCssClass' => '',  
			'previousPageCssClass' =>'prev disabled',  
            'prevPageLabel' => '上一页',    
            'nextPageLabel' => '下一页', 
			'nextPageCssClass' => '', 
			'internalPageCssClass' => '',
			'htmlOptions' => array('class'=>'page'),
            'pages' => $pager,    
            'maxButtonCount'=>8    
			)
		);
		?>  
    </div>