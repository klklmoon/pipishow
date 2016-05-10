<div class="apply_box clearfix">

<div class="w1000">
<div class="apply_content clearfix">
    
	<div class="info_table">
        <h1>申请成为主播</h1>
    	<h3>恭喜您，您的申请已提交成功！</h3>
        <div class="audition">
          <h3>请在24小时内不要加入任何家族，尽快联系下方官方导师进行面试！</h3>
          <p>
          	在线面试：（请选择一位官方导师预约面试，通过面试后，即可成为主播。）<br/>
          	导师QQ：
          	<?php foreach($tutors as $t){?>
            <a style="display:inline-block; width:160px;" target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo $t['extend']['qq'];?>&amp;site=qq&amp;menu=yes">
            <img border="0" src="http://wpa.qq.com/pa?p=3:<?php echo $t['extend']['qq'];?>:45" alt="<?php echo $t['user']['nickname'];?>" title="<?php echo $t['user']['nickname'];?>" style="vertical-align:middle;">
           	<?php echo $t['user']['nickname'];?>(QQ<?php echo $t['extend']['qq'];?>)
           	</a>
           	<?php }?>
	      </p><br/>
          <?php if($applyInfo['status'] == APPLY_STATUS_WAITING){ ?><p><a class="modify" href="<?php echo $this->createUrl('dotey/apply', array('edit' => 1))?>">修改申请资料</a></p><?php } ?>
        </div>
    </div>
    <div class="info_ad">
    	
        <div class="audition_tips">
        	<p><strong>视频面试建议</strong></p> 
            <p>1、着装干净整洁</p>
            <p>2、女生着淡妆</p>
            <p>3、有演唱、播音主持或舞蹈特长可准备一首歌曲</p>
            <p>4、准备好直播用的电脑设备，如麦克风、摄像头。</p>
        </div>
    </div>
    
</div>
</div><!-- .w1000 -->