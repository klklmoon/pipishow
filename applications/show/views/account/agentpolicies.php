<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">
	<?php
		$this->renderPartial('account_left',
			array('account_left'=>$account_left,
			'dotey_left'=>$dotey_left,
			'agent_left'=>$agent_left)
		);
	?>

	<div class="main fright">
	
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="<?php echo $this->createUrl('account/agentpolicies');?>">代理政策</a></li>
        </ul><!-- .main-menu -->
        
        <div id="MainCon">
            <div class="cooper-list agentyer">
            	<?php if($threadList['list']):?>
                <ul class="notice-list">
                <?php foreach ($threadList['list'] as $thread):?>
                	<li>
                    	<p class="notice-h clearfix">
                        	<a href="javascript:void(0);" title="<?php echo $thread['title'];?>" class="fleft"><?php echo $thread['title'];?></a>
                        	<span class="fright"><?php echo date('Y-m-d',$thread['create_time']);?></span>
                        </p>
                    	<div class="notice-con"><?php echo strip_tags($thread['content']);?></div>
                    </li>
					<?php endforeach;?>
                </ul>
                <?php else:?>
                没有代理政策
                <?php endif;?>
            </div><!-- .cooper-list 收入账单-->

                <!--翻页-->
		  			<div class="pagination pagination-centered">
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
						'nextPageCssClass' => 'next', 
						'selectedPageCssClass' => 'active',
						'internalPageCssClass' => '',
						'htmlOptions' => array('class'=>'page'),
			            'pages' => $pager,    
			            'maxButtonCount'=>8    
						)
					);
					?>
					</div>
     		<!--翻页--> 
            
        </div><!--#MainCon-->
     </div><!-- .main --> 	
</div>
<!-- .w1000 -->
<script type="text/javascript">
$(function(){
	$('.notice-h').find('a').toggle(function(){
		$(this).parent().siblings('.notice-con').stop(true,true).slideUp('fast');
	},function(){
		$(this).parent().siblings('.notice-con').stop(true,true).slideDown('fast');	
	});	
});
</script>
