<?php
$this->breadcrumbs = array('活动管理','幸运礼物');
?>
<div class="row-fluid sortable ui-sortable">
  	<div class="box span12">
		<div class="box-content">
			<ul class="nav nav-tabs" id="myTab">
				<li>
					<a href="#poolSet">奖池设置</a>
				</li>
				<li>
					<a href="#giftAward">中奖设置</a>
				</li>
				<li>
					<a href="#poolRecord">奖池变化记录</a>
				</li>
				<li>
					<a href="#awardRecord">中奖记录</a>
				</li>
			</ul>
			 
			<div id="myTabContent" class="tab-content" style="overflow-x:hidden;overflow-y:hidden;">
				<div class="tab-pane" id="poolSet"><?php echo $data;?> </div>
				<div class="tab-pane" id="giftAward"> </div>
				<div class="tab-pane" id="poolRecord"> </div>
				<div class="tab-pane" id="awardRecord"> </div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

$(function(){
	var URL = "<?php echo $this->createUrl('luckyGifts/index');?>";
	var ALLTABS = ['poolSet','poolRecord','giftAward','awardRecord'];
	var isClick = true;
	$('#myTab > li').live('mouseover',function(){
		if($(this).attr('class')  == 'active'){
			isClick = false;
		}else{
			isClick = true;
		}
	});

	$('#myTab > li').live('mouseout',function(){
		if($(this).attr('class')  == 'active'){
			isClick = false;
		}else{
			isClick = true;
		}
	});
	$('#myTab > li').live('click',function(){
		var _ID = $(this).children('a').attr('href');
			_ID = _ID.substring(1);
		if(_ID && isClick){
			$('#myTabContent #'+_ID).html('<div id="loading" class="center">努力加载中...<div class="center"></div></div>');
			$.ajax({
				url:URL+'&tab='+_ID,
				type:'post',
				dataType:'html',
				success:function(data){
					var arrLen = ALLTABS.length;
					for(var i=0;i<arrLen;i++){
						var _v = arrLen[i];
						if(_v != _ID){
							$('#myTabContent #'+_v).html('');
						}
					}
					$('#myTabContent #'+_ID).html(data);
				}
			});
		}
	});
});
</script>