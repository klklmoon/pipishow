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
			<li><a href="<?php echo $this->createUrl('account/salestat');?>">收入概况</a></li>
			<li class="menuvisted"><a href="#">销售记录</a></li>
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
				<ul class="selectbox-hd clearfix">
					<li class='on'>按时间</li>
					<li>按玩家id</li>
				</ul>
				<!--.selectbox-hd-->
				<div class="selectbox">
				<?php $this->renderPartial('turnsalerecords',
						array(
							'monthList'=>$monthList,
							'userAgent'=>$userAgent,
							'salerecords'=>$salerecords,
							'statData'=>$statData,
							'salerecordsType'=>$salerecordsType,
							'seletedMonth'=>$seletedMonth)
					);
				?>
				</div>
				<!--.selectbox-->
			</div>
			<!-- .cooper-list -->
		</div>
		<!--#MainCon-->
	</div>
	<!-- .main -->
</div>
<!-- .w1000 -->
<script type="text/javascript">
$(".selectbox-hd li").live("click",function(){
    $(this).addClass("on").siblings().removeClass("on");
    var index=$(this).index();
	if(index==0)
		salerecordsByMonth(1);
	else
		salerecordsByUserId(1);
  }); 

$("#selectYearMonth").live("change",function(){
	salerecordsByMonth(1);
});

function salerecordsByMonth(page)
{
	var year_month=$("#selectYearMonth").val();
	var salerecords_type="ByMonth";
	$.ajax({
		type:"POST",
		url:"index.php?r=account/turnsalerecords",
		data:{'salerecords_type':salerecords_type,'year_month':year_month,'page':page},
		dataType:"html",
		success:function(resonseData){
			$(".selectbox").html(resonseData);
		}
	});
}

function salerecordsByUserId(page)
{
	var user_id=$("#user_id").val();
	var salerecords_type="ByUserId";
	$.ajax({
		type:"POST",
		url:"index.php?r=account/turnsalerecords",
		data:{'salerecords_type':salerecords_type,'user_id':user_id,'page':page},
		dataType:"html",
		success:function(resonseData){
			$(".selectbox").html(resonseData);
		}
	});
	
}

function setCopyLink() {
	//$.cookie('agent_id', <?php echo $userAgent['uid'];?>);
	//copy_code($("#agent_link").val());
    copyToClipboard($("#agent_link").val());
}
</script>
