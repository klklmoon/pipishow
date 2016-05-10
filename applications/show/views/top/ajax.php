<div class="w1000 mt20">
	<div class="boxshadow p15 clearfix">
		<div class="boxhd clearfix">		
			<h3>排行榜</h3>			
		</div>
		<div class="main-2 fleft ovhide">
		<?php $this->renderPartial('application.views.top.doteyrank',array('rank'=>$dotey_rank)); ?>
		</div>
	</div>
</div>
<?php $this->renderPartial('application.views.top.songsrank',array('songs'=>$songs)); ?>
<?php $this->renderPartial('application.views.top.giftrank',array('gift'=>$gift)); ?>
<?php $this->renderPartial('application.views.top.userrank',array('rank'=>$rank)); ?>