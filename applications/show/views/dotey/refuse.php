<div class="apply_box clearfix">

<div class="w1000">
<div class="apply_content clearfix">
    
	<div class="info_table">
		<?php if(isset($forbid) && $forbid == 1){ ?>
		<h1>禁止申请成为主播</h1>
		<div class="audition">
			<center>你的主播申请被拒绝并被禁止申请为主播。</center>
		</div>
		<?php }else{ ?>
        <h1>申请成为主播</h1>
    	<h3>你的主播申请被拒绝</h3>
        <div class="audition">
          <?php if(!empty($applyInfo['reason'])){?><p>导师回复：<?php echo $applyInfo['reason'];?></p><?php }?>
          <p><a class="modify" href="/">返回首页</a></p>
        </div>
        <?php } ?>
    </div>
</div>
</div><!-- .w1000 -->