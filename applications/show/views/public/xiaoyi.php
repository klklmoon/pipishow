

<div class="recomn-box">
    <a href="javascript:;" id="attention_xiaoyi" class="rec-btn attent-btn" title="关注她">关注她</a>
    <a href="http://show.pipi.cn/10874265" title="进入直播间" target="_blank" class="rec-btn in-btn">进入直播间</a>
    <div class="recomn-con oneyear">
        <p class="oneyear-f"><em>一年前的7月，</em>她走进皮皮乐天</p>
        <p>一年后的7月，她成为了我们的明星主播</p>
        <p>从红心到皇冠8，并不是一个轻松的旅程</p>
        <p>她用半年的时间，才升到了皇冠1</p>
        <p>但她同样也是用了半年的时间，便从皇冠1升到了皇冠8</p>
        <p>这是粉丝为她创造的奇迹</p>
        <p>现在，在她的一周年即将来临之际，</p>
        <p>粉丝又为她创造了一个奇迹——最佳女歌手！</p>
    </div><!--.oneyear-->

    <div class="recomn-con fans">
        <p class="fans-f"><em>她作为主播，</em>成长之路并非一帆风顺</p>
        <p>她说，如果没有“一个人还在”、没有执着、龙虾、华筝、霸道、</p>
        <p>熊牧野，她撑不到今天</p>
        <p>没有粉丝，就没有今天的她</p>
        <p>她说要感谢很多人，她有很长的一串名单，每个人都要好好感谢，</p>
        <p>那些人不仅是她的粉丝，更是她的朋友</p>
        <div class="recomn-btm">
            <p>小编有话说：</p>
            <p>小编去过小艺的直播间，气氛超好的说，一群人欢乐地聊天，</p>
            <p>连送礼都送得心甘情愿有木有？难怪人气这么高啊！</p>
        </div>
    </div><!--.recomn-con-->

    <div class="recomn-con moanchor">
        <p class="moanchor-f"><em>如果有人问：</em>新人如何做到皇冠主播？</p>
        <p>答案一定是这样的：坚持直播，坚持长时间直播。</p>
        <p>小艺就是其中的典型</p>
        <p>她几乎每天直播，很少连续好几天不上线</p>
        <p>她从未中断过与粉丝的交流</p>
        <p>成功没有秘诀，唯有坚持不懈</p>
        <div class="recomn-btm">
            <p>小编有话说：</p>
            <p>问小艺要照片真的是一个很艰难的过程~~~</p>
            <p>小艺的照片好少，写真？木有！生活照？木有！</p>
            <p>自拍照？嗯……倒是有一些……可惜放到页面上太模糊了有木有？</p>
            <p>小艺的回答是这样的：在皮皮之后就很少出门了……</p>
            <p>小编捂脸，小艺真的是超勤奋的主播，小编经常能看到她在直播，直播时间还超长的有木有？</p>
            <p>嗯，这点必须要赞一个！</p>
            <p>最后小编终于成功逼着小艺去拍照啦，嘿嘿嘿~~~~</p>
        </div>
    </div><!--.recomn-con-->

</div><!--.recomn-box-->


<div class="popbox" id="FryFail">
	<div class="poph noline">
    	<a onclick="$.mask.hide('FryFail');" class="closed" title="关闭"></a>
    </div>
    <p class="otline">设置成功</p>
	<p class="oneline"></p>
    <p class="oneline"><input type="button" value="确&nbsp;&nbsp;定" onclick="$.mask.hide('FryFail');" class="shiftbtn"></p>
</div>

<script type="text/javascript">
$(function(){
	$('#attention_xiaoyi').click(function(){
		if($.User.getSingleAttribute('uid',true) <= 0){
			$.User.loginController('login');
			return false;
		}
		var uid = 10874265;
		$.ajax({
			type : 'post',
			url : 'index.php?r=user/attention',
			data : {uid:uid},
			success:function(data){
				if(data == 0){
					$('.otline').html('关注成功');
					$.mask.show('FryFail',3000);
				}
				if(data == -1){
					$.User.loginController('login');
				}
				if(data == -2){
					$('.otline').html('非法操作');
					$.mask.show('FryFail',3000);
				}
				if(data == -3){
					$('.otline').html('自己不能关注自己');
					$.mask.show('FryFail',3000);
				}
			}
		});
	});
});
</script>
