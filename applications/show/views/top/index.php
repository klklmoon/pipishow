<div id="top_rank">
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
</div>
<script type="text/javascript">
$(function(){
function RankTab(hdid,bdclass){
		$(hdid).live('click',function(){
		var index=$(this).index();
		$(bdclass).eq(index).css('display','block').siblings(bdclass).css('display','none');
		$(this).find('a').addClass('curr').parent().siblings().find('a').removeClass('curr');
	});	
}
RankTab('#Charm li','.hm-xsboard');
RankTab('#HoterHd li','.tabcon-bd ul');

setInterval(function(){
	$.ajax({
		type : 'post',
		url : 'index.php?r=top/ajax/',
		data : {target:hrefTarget},
		success:function(data){
			$('#top_rank').html(data);
		}
	});	
	
},1000*60*1);

});
</script>

