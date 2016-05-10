<div id="HaveRec" class="outframe">
	<p id="HaveRecTxt"></p>
	<p class="btn"><a href="javascript:void(0);" onclick="$.mask.hide('HaveRec');"></a></p>
</div>
<div class="twoyear-top">
	<dl>
		<dt>活动时间</dt>
		<dd class="time"><?php echo date('Y.m.d H:i:s', strtotime($startTime));?>-<?php echo date('Y.m.d H:i:s', strtotime($endTime));?></dd>
		<dd>活动说明：</dd>
		<dd class="content">不知不觉皮皮乐天已来到两周年之际，周年庆已准备就绪，伴着千万玩家的祝福道贺，皮皮乐天幸福满满。为感谢一直守护着皮皮乐天的广大用户，特进行周年庆大回馈，限量版道具以及加速升级的机会就摆在这，小伙伴们赶紧行动起来吧！</dd>
	</dl>
</div><!--.twoyear-top-->
<div class="twoyear-con">
	<div class="w980">
		<div class="reward-box clearfix">
			<dl>
				<dt>
					<p>单日充值达到</p>
					<p><?php echo $packs[1]['need'];?>元</p>
				</dt>
				<dd class="con">可获得黄色VIP30天<br/>座驾（大众甲壳虫）15天<br/>额外获得<?php echo $packs[1]['reward'];?>贡献值</dd>
				<dd><a href="javascript:void(0);" onclick="receive(1);"></a></dd>
			</dl>
			<dl>
				<dt>
					<p>单日充值达到</p>
					<p><?php echo $packs[2]['need'];?>元</p>
				</dt>
				<dd class="con">可获得紫色VIP7天<br/>座驾（ 地狱战车 ）7天<br/>额外获得<?php echo $packs[2]['reward'];?>贡献值</dd>
				<dd><a href="javascript:void(0);" onclick="receive(2);"></a></dd>
			</dl>
			<dl>
				<dt>
					<p>单日充值达到</p>
					<p><?php echo $packs[3]['need'];?>元</p>
				</dt>
				<dd class="con">可获得紫色VIP7天 + 6位靓号（888ABC）<br/>座驾（ 地狱战车 ）15天<br/>额外获得<?php echo $packs[3]['reward'];?>贡献值</dd>
				<dd><a href="javascript:void(0);" onclick="receive(3);"></a></dd>
			</dl>
			<dl>
				<dt>
					<p>单日充值达到</p>
					<p><?php echo $packs[4]['need'];?>元</p>
				</dt>
				<dd class="con">可获得紫色VIP15天 + 6位靓号（888AAB）<br/>座驾（ 地狱战车 ）30天<br/>额外获得<?php echo $packs[4]['reward'];?>贡献值</dd>
				<dd><a href="javascript:void(0);" onclick="receive(4);"></a></dd>
			</dl>
			<dl>
				<dt>
					<p>单日充值达到</p>
					<p><?php echo $packs[5]['need'];?>元</p>
				</dt>
				<dd class="con">可获得紫色VIP30天 + 6位靓号（8888AA）<br/>新品恶搞座驾（公交车）30天<br/>额外获得<?php echo $packs[5]['reward'];?>贡献值</dd>
				<dd><a href="javascript:void(0);" onclick="receive(5);"></a></dd>
			</dl>
			<dl>
				<dt>
					<p>单日充值达到</p>
					<p><?php echo $packs[6]['need'];?>元</p>
				</dt>
				<dd class="con">可获得紫色VIP60天 + 5位靓号（888AA）<br/>新品恶搞座驾（公交车）30天<br/>额外获得<?php echo $packs[6]['reward'];?>贡献值</dd>
				<dd><a href="javascript:void(0);" onclick="receive(6);"></a></dd>
			</dl>
			<dl>
				<dt>
					<p>单日充值达到</p>
					<p><?php echo $packs[7]['need'];?>元</p>
				</dt>
				<dd class="con">可获得紫色VIP一年 + 5位靓号（8888A）<br/>新品恶搞座驾（公交车）30天<br/>额外获得<?php echo $packs[7]['reward'];?>贡献值</dd>
				<dd><a href="javascript:void(0);" onclick="receive(7);"></a></dd>
			</dl>
		</div><!--.reward-box-->
		<dl class="explain">
			<dt>特别说明：</dt>
			<dd>1.以单日实际充值成功的人民币数为准，不计算折扣，当天的00：00:01--23:59：59计算</dd>
			<dd>2.活动期间每个账号只可领取一次，不可赠送给他人，奖励只发放给充值的账号，其他账号不予发放。</dd>
			<dd>3.达到领取礼包3至礼包7条件的用户，请及时联系客服领取靓号奖励。</dd>
			<dd>4.本活动最终解释权归皮皮乐天所有</dd>
		</dl>
	</div>
	<div class="w980 ancher">
		<div class="reward-box clearfix">
			<dl>
				<dt>
					<p>魅力点达到</p>
					<p><?php echo $packs[8]['need'];?>以及</p>
					<p><?php echo $packs[8]['need'];?>以上</p>
				</dt>
				<dd class="con">可额外获得<?php echo $packs[8]['reward'];?>魅力值</dd>
				<dd><a href="javascript:void(0);" onclick="receive(8);"></a></dd>
			</dl>
			<dl>
				<dt>
					<p>魅力点达到</p>
					<p><?php echo $packs[9]['need'];?>以及</p>
					<p><?php echo $packs[9]['need'];?>以上</p>
				</dt>
				<dd class="con">可额外获得<?php echo $packs[9]['reward'];?>魅力值</dd>
				<dd><a href="javascript:void(0);" onclick="receive(9);"></a></dd>
			</dl>
			<dl>
				<dt>
					<p>魅力点达到</p>
					<p><?php echo $packs[10]['need'];?>以及</p>
					<p><?php echo $packs[10]['need'];?>以上</p>
				</dt>
				<dd class="con">可额外获得<?php echo $packs[10]['reward'];?>魅力值</dd>
				<dd><a href="javascript:void(0);" onclick="receive(10);"></a></dd>
			</dl>
			<dl>
				<dt>
					<p>魅力点达到</p>
					<p><?php echo $packs[11]['need'];?>以及</p>
					<p><?php echo $packs[11]['need'];?>以上</p>
				</dt>
				<dd class="con">可额外获得<?php echo $packs[11]['reward'];?>魅力值</dd>
				<dd><a href="javascript:void(0);" onclick="receive(11);"></a></dd>
			</dl>
			<dl>
				<dt>
					<p>魅力点达到</p>
					<p><?php echo $packs[12]['need'];?>以及</p>
					<p><?php echo $packs[12]['need'];?>以上</p>
				</dt>
				<dd class="con">可额外获得<?php echo $packs[12]['reward'];?>魅力值</dd>
				<dd><a href="javascript:void(0);" onclick="receive(12);"></a></dd>
			</dl>
			<dl>
				<dt>
					<p>魅力点达到</p>
					<p><?php echo $packs[13]['need'];?>以及</p>
					<p><?php echo $packs[13]['need'];?>以上</p>
				</dt>
				<dd class="con">可额外获得<?php echo $packs[13]['reward'];?>魅力值</dd>
				<dd><a href="javascript:void(0);" onclick="receive(13);"></a></dd>
			</dl>
		</div><!--.reward-box-->
		<dl class="explain">
			<dt>特别说明：</dt>
			<dd>1.主播的奖励是计算从活动开始至结束的累积魅力点，非计算单日的，领取的魅力值不能兑换现金。</dd>
			<dd>2.请主播在活动期间及时领取对应的魅力值奖励，每个账号只可领取一次，超过活动时间则不予领取，请主播谨慎选择领取时间。 </dd>
			<dd>3.本活动最终解释权归皮皮乐天所有</dd>
		</dl>
	</div>
<script type="text/javascript">
var lastClickTime = 0;
function receive(id){
	var timestamp = (new Date()).valueOf();
	if(lastClickTime==0 || (timestamp-lastClickTime)>3000){
		lastClickTime=(new Date()).valueOf();
		$.ajax({
			type:'POST',
			url:'<?php echo $this->createUrl('activities/2years');?>',
			data:{op:'receive',id:id},
			dataType:'json',
			async:false,
			success:function(data){
				if(data.status == 1){
					$('#HaveRecTxt').html(data.message);
					$.mask.show('HaveRec');
				}else if(data.status == -1){
					curLoginController = 'login';
					$("#form_login").resetForm();
					$.User.loginController('login');
				}else if(data.status == -2){
					$('#HaveRecTxt').html('该礼包不存在！');
					$.mask.show('HaveRec');
				}else if(data.status == 0 && data.message){
					$('#HaveRecTxt').html(data.message);
					$.mask.show('HaveRec');
				}else{
					$('#HaveRecTxt').html('系统出错，请与客服联系！');
					$.mask.show('HaveRec');
				}
			}
		});
	}else{
		$('#HaveRecTxt').html('请不要频繁点击领取按钮！');
		$.mask.show('HaveRec');
	}
}
</script>