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
			<li class="menuvisted"><a href="#">收入概况</a></li>
			<li><a href="<?php echo $this->createUrl('account/salerecords');?>">销售记录</a></li>
		</ul>
		<!-- .main-menu -->

		<div id="MainCon">
			<div class="cooper-list">
				<div class="cooper-hd clearfix">
					<p class="fleft">
						<em class="fleft ver vsale<?php echo $userAgent['agent_type'];?>"></em>代理人：<em class="pink"><?php echo mb_substr((empty($userAgent['agent_nickname'])?"求昵称":$userAgent['agent_nickname']),0,8,'UTF-8');?></em>(<?php echo $userAgent['uid'];?>)
					</p>
					<p class="fright">
						我的道具代理购买链接：<input id="agent_link" type="text" value="<?php echo $this->createAbsoluteUrl('shop/vip');?>&agent_id=<?php echo $userAgent['uid'];?>"
							style="width: 300px;"><a class="pinklenbtn" href="javascript:setCopyLink();">复制链接</a>
					</p>
				</div>
				<p>*玩家可以点击“售”字标识，或点击道具代理链接，打开商城页面购买商品，代理人即可获得相应的销售提成。</p>
				<p class="line"></p>
				<div class="roynum clearfix">
				<p class="fleft">
					<select id="selectYear">
					<?php foreach($yearList as $yearRow):?>
						<option value="<?php echo $yearRow['value'];?>" <?php if($seletedYear==$yearRow['value']){echo ' selected="selected"';}?>><?php echo $yearRow['text'];?></option>
					<?php endforeach;?>
					</select>
				</p>
				<p class="fleft moonyroy">
					本月提成金额：<em><?php echo isset($userAgent['thisMonthIncome'])?$userAgent['thisMonthIncome']:0;?></em>元
				</p>
				</div>
				<?php
					if($salestat_list):
				?>
				<table width="620" border="1" bordercolor="#DDDDDD">
					<tr bgcolor="#F5F5F5" class="biaot">
						<td width="100" height="40">月份</td>
						<td width="150" height="40">销售笔数</td>
						<td width="120" height="40">提成收入（元）</td>
					</tr>
				<?php foreach ($salestat_list as $statRow):?>
					<tr>
						<td height="30"><?php echo $statRow['sale_month']."月"; ?></td>
						<td height="30"><?php echo $statRow['counts']; ?></td>
						<td height="30"><?php echo $statRow['sum_income']; ?></td>
					</tr>
				<?php endforeach;?>
				</table>
				<?php else:?>
					暂无记录
				<?php endif;?>
			</div>
			<!-- .cooper-list -->
		</div>
		<!--#MainCon-->
	</div>
	<!-- .main -->
</div>
<!-- .w1000 -->
<script type="text/javascript">
function setCopyLink() {
	//$.cookie('agent_id', <?php echo $userAgent['uid'];?>);
    copyToClipboard($("#agent_link").val());
}

$("#selectYear").live("change",function(){
	window.location="<?php echo $this->createUrl('account/salestat');?>&sale_year="+$(this).val();
})
</script>

