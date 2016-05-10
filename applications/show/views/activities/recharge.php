<style>
.footer{padding-bottom:0px;}
.mb20{margin-bottom:-6px;}
</style>
<div class="rechargebox">
<div class="w1000 rechargecon">
	<a href="javascript:exchange();" class="rethbtn">我要充值</a>
	<div class="rech-btm">
		<p>*当日充值数可累计，累计不超过12小时。活动期间奖励不可重复领取。</p>
		<p>*奖励在活动结束后3天内发放。 靓号随机发放。</p>
	</div>
	<dl class="rech-contact">
		<dt><a href="http://wpa.qq.com/msgrd?v=3&amp;uin=121294340&amp;site=qq&amp;menu=yes" target="_blank"><img border="0" title="丁小希" alt="丁小希" src="http://wpa.qq.com/pa?p=2:121294340:41"></a></dt>
		<dd>QQ:121294340</dd>
        <dd>*本活动最终解释权归皮皮乐天所有。</dd>
	</dl>
</div>
<script type="text/javascript">
function exchange(t){
	if(exchangeUrl!='#'){
		if(!t || t.length == 0){
			t = '_blank';
		}
		window.open(exchangeUrl,t);
	}else{
		$.User.loginController('login');
	}
}
</script>