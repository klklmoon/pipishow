<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
   
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">主播公告</a></li>
        </ul><!-- .main-menu -->
        
        <div id="MainCon">
            <div class="cooper-list">
				<?php if ($list) :?>
                <ul class="notice-list">
					<?php foreach($list as $k=>$v) :?>
						<li>
							<p class="notice-h clearfix">
								<a href="javascript:void(0);" title="<?php echo $v['title'];?>" class="fleft"><?php echo $v['title'];?></a>
								<span class="fright"><?php echo date('Y-m-d',$v['create_time']);?></span>
							</p>
							
							<div class="notice-con" ><?php echo $v['text'];?></div>
						</li>
					<?php endforeach;?>
                </ul>
				<?php endif; ?>
            </div><!-- .cooper-list 收入账单-->
            
            
        </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->


<script>
$(function(){
	$('.notice-con').first().show();
});
</script>