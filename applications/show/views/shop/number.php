 <div class="w1000 mt20 shop">
    <div class="shop-con clearfix " style="margin-top:30px;">
    	 <?php $this->renderPartial('application.views.shop.left'); ?>
    	 
 		<div class="fright">  
            <div class="numb-main">
            	 <div><img src="<?php echo $this->pipiFrontPath?>/fontimg/props//lh.jpg" /></div>
              <div class="numbtit mynumbt mt10"> 
                    <span class="fleft"><strong>我的靓号</strong>&nbsp;&nbsp;&nbsp;
                    <?php $num = $this->getUserJsonAttribute('num',true,true);
                    	  if($num){
                    	  	$userNum = $num['n'];
                    	  	$strLen = strlen((string)$userNum);
                    	  	if($strLen  == 4){
								$numStyle = 'jpnumb';
							}else{
								$numStyle = 'sixnumb';
							}
                    	  	echo '<span class="'.$numStyle.'" style="display:inline-block; vertical-align:middle;" id="mynumber"><em>靓</em>'.$num['n'].'<span style="display: none;" class="tipcon">'.$num['s'].'</span></span>';
                    	  }else{
                    	  	 echo '未购买靓号';
                    	  }
                    ?> </span> 
                    <span class="fleft ml10">自助选号&nbsp;&nbsp;<input type="text" id="input_number" />  
                   		 <a class="buy-btn" title="查询" href="javascript:void(0);" onclick="return queryNumber();">查询</a>
                    </span>
                    <?php $this->renderPartial('application.views.shop.agentlist'); ?>
              </div>
                 
              <div class="numbtit mt10"><span class="fleft"><strong>极品靓号</strong></span> <span class="fright"><a href="javascript:void(0)" class="pink" onclick="switchFourNumber(<?php echo NUMBER_TYPE_FOUR?>);">换一批</a></span></div>
              <div class="numbbox">
                                
                 <?php if($this->viewer['four']):?>
                 <ul id="four_number">
                 	  <?php foreach($this->viewer['four'] as $four):
                 	  			$fourPrice = isset($four['confirm_price']) && $four['confirm_price'] ? $four['confirm_price'] : $four['buffer_price'];
                 	  ?>
                      <li id="number_<?php echo $four['number']?>">
                        <p class="relative">
                        	<span class="jpnumb"><em>靓</em><?php echo $four['number'];?></span>
                        	<span class="tipcon" style="display: none;">价格：<?php echo $fourPrice;?>皮蛋</span>
                        </p>
                        <p><span class="caishen"><?php echo $four['short_desc'];?></span></p>
                        <p><a class="numb-btn mt3" title="购买" href="javascript:void(0);" onclick="_buyNumber('<?php echo $four['number'];?>',<?php echo $fourPrice;?>)">购买</a> <a class="numb-btn mt3" title="赠送" href="javascript:void(0)" onclick="_sendNumber('<?php echo $four['number'];?>',<?php echo $fourPrice;?>);">赠送</a></p>
                      </li>
                      <?php endforeach;?>
                    </ul>
                    <?php endif;?>
              </div><!-- numbbox -->
              
              <div class="numbtit mt30"><span class="fleft"><strong>五位靓号</strong></span> <span class="fright"><a href="javascript:void(0)" class="pink" onclick="switchFiveNumber(<?php echo NUMBER_TYPE_FIVE?>);">换一批</a></span></div>
              <div class="numbbox">
                 <ul id="five_number">
                  	<?php foreach($this->viewer['five'] as $five):
                	  $fivePrice = isset($five['confirm_price']) && $five['confirm_price'] ? $five['confirm_price'] : $five['buffer_price'];
                  	?>
                      <li id="number_<?php echo $five['number']?>">
                        <p><span class="fivenumb"><em>靓</em> <?php echo $five['number']?></span></p>
                        <p>价格:<?php echo $fivePrice;?></p>
                        <p><a class="numb-btn mt3 ml2" title="购买" href="javascript:void(0);" onclick="_buyNumber('<?php echo $five['number'];?>',<?php echo $fivePrice;?>)">购买</a> <a class="numb-btn mt3" title="赠送" href="javascript:void(0)"  onclick="_sendNumber('<?php echo $five['number'];?>',<?php echo $fivePrice;?>);">赠送</a></p>
                      </li>
                  	<?php endforeach;?>
                    </ul>
              </div><!-- numbbox -->
                                  
              <div class="numbtit mt30"><span class="fleft"><strong>六位靓号</strong></span> <span class="fright"><a href="javascript:void(0)" class="pink" onclick="switchSixNumber(<?php echo NUMBER_TYPE_SIX?>);">换一批</a></span></div>
              <div class="numbbox">
                 <ul id="six_number">
                  	<?php foreach($this->viewer['six'] as $six):
                	  $sixPrice = isset($six['confirm_price']) && $six['confirm_price'] ? $six['confirm_price'] : $six['buffer_price'];
                  	?>
                      <li id="number_<?php echo $six['number']?>">
                        <p><span class="sixnumb"><em>靓</em> <?php echo $six['number']?></span></p>
                        <p>价格:<?php echo $sixPrice;?></p>
                        <p><a class="numb-btn mt3 ml2" title="购买" href="javascript:void(0);" onclick="_buyNumber('<?php echo $six['number'];?>',<?php echo $sixPrice;?>)">购买</a> <a class="numb-btn mt3" title="赠送" href="javascript:void(0)"  onclick="_sendNumber('<?php echo $six['number'];?>',<?php echo $sixPrice;?>);">赠送</a></p>
                      </li>
                  	<?php endforeach;?>
                    </ul>
              </div><!-- numbbox -->
              
              
              <div class="numbtit mt30"><span class="fleft"><strong>七位靓号</strong></span> <span class="fright"><a href="javascript:void(0);" class="pink" onclick="switchSevenNumber(<?php echo NUMBER_TYPE_SEVEN?>);">换一批</a></span></div>
              <div class="numbbox">
                 <ul id="seven_number">
                  	<?php 
                  		foreach($this->viewer['seven'] as $seven):
                  			$sevenPrice = isset($seven['confirm_price']) && $seven['confirm_price'] ? $seven['confirm_price'] : $seven['buffer_price'];
                  	?>
                      <li id="number_<?php echo $seven['number']?>">
                        <p><span class="sixnumb"><em>靓</em> <?php echo $seven['number']?></span></p>
                        <p>价格:<?php echo $sevenPrice?></p>
                        <p><a class="numb-btn mt3 ml2" title="购买" href="javascript:void(0);" onclick="_buyNumber('<?php echo $seven['number'];?>',<?php echo $sevenPrice?>)">购买</a> <a class="numb-btn mt3" title="赠送" href="javascript:void(0);" onclick="_sendNumber('<?php echo $seven['number'];?>',<?php echo $sevenPrice?>);">赠送</a></p>
                      </li> 
                    <?php endforeach;?>
                    </ul>
              </div><!-- numbbox -->
              
              <div class="numbtit mt30"><span class="fleft"><strong>靓号FAQ</strong></span></div>
              <div class="numbbox">
                <p class="p15"><a class="faq-tit">靓号是什么</a><br/>
                靓号指众多ID号中的珍稀号码，显示出众不凡的身份，拥有靓号的您也更易被人牢记。</p>
                
                <p class="p15"><a class="faq-tit">靓号的好处</a><br/>
                1、可以直接用靓号登录。<br/>
                2、靓号自带最长12个汉字的寄语，可由您自编含义。<br/>
                3、特殊含义的靓号，如我爱你520、发发发888等，蕴含吉祥，珍惜，幸运、通达之意。<br/>
                4、带有规律节奏的靓号，美观又易记。<br/>
                5、靓号的特殊效果，将您从人群中凸现，彰显不凡。</p>
                
                
                <p class="p15"><a class="faq-tit">自助选号技巧</a><br/>
                情感寓意：特殊表达如5201314（我爱你一生一世），纪念意义如属于我们的纪念日130725，每次输入号码，都记起认识的甜蜜。
                生意兴隆如666888，一个好帐号，运气伴一生。兄弟情谊如三个连续号，虽然我们不经常在一起，但就像你在身边默默支持着。
                无连号，不兄弟！手机号如取后4-6位短号简单易记。</p>
                
                <p class="p15"><a class="faq-tit">其他</a><br/>
                为使靓号资源充分使用，以下情况官方有权自动收回靓号：<br/>
                1.普通用户超过2个月无充值记录。<br/>
                2.主播超过2个月无开播记录。</p>
              </div><!-- numbbox -->
                 
          </div><!--靓号-->
       </div>
    </div>
</div>
<div id="BuyNumb" class="popbox" >
	<div id="ChangeRun" class="poph">
        <span>购买靓号</span>
        <a title="关闭" class="closed" onclick="$.mask.hide('BuyNumb');"></a>
    </div>
    <div class="popcon">
    	<ul class="paysong">
            <li>
            	<p>恭喜您，靓号<em class="pink" ></em>可以购买与赠送！</p><br>
                <p>靓号寄语：<em class="pink"></em></p>
                <p>靓号价格：<em class="pink"></em>皮蛋</p>
                <p class="end" style="display:none;">
            		<span>代理渠道：</span>
                	<strong></strong>
            	</p>
                <p class="gray">*您可以去<a href="<?php $this->getTargetHref($this->createUrl('account/number'))?>" target="<?php echo $this->target?>"><strong>我的物品&gt;靓号</strong></a> 修改靓号寄语文字。</p>
                <p class="runwayok clearfix"> <input type="button" value="购&nbsp;&nbsp;买" class="fleft shiftbtn" />
            	<input type="button" value="赠&nbsp;&nbsp;送" class="shiftbtn"></p>
            </li>
        </ul>
    </div>
</div>

<div id="BuyNumbConfirm" class="popbox">
	<div id="ChangeRun" class="poph">
        <span>确认购买</span>
        <a title="关闭" class="closed" onclick="$.mask.hide('BuyNumbConfirm');"></a>
    </div>
    <div class="popcon">
    	<ul class="paysong">
            <li>
            	<p>靓号：<em class="pink"></em></p>
                <p>价格：<em class="pink"></em>皮蛋</p>
                <p class="end" style="display:none;">
            		<span>代理渠道：</span>
                	<strong></strong>
            	</p>
                <p class="gray">*您可以去<a href="<?php $this->getTargetHref($this->createUrl('account/number'))?>" target="<?php echo $this->target?>"><strong>我的物品&gt;靓号</strong></a> 修改靓号寄语文字。</p><br>
                <p class="gradline"><input class="shiftbtn" type="button" value="确  定"><input onclick="$.mask.hide('BuyNumbConfirm');" class="shiftbtn" type="button" value="取&nbsp;&nbsp;消"></p>
            </li>
        </ul>
    </div>
</div>

<div id="BuyNumbSus" class="popbox">
	<div id="ChangeRun" class="poph">
        <span>购买成功</span>
        <a title="关闭" class="closed" onclick="$.mask.hide('BuyNumbSus');"></a>
    </div>
    <div class="popcon">
    	<ul class="paysong">
            <li>
            	<p>购买成功，您已拥有靓号<em class="pink"></em>！</p>
                <p class="gray">*您可以去<a href="<?php $this->getTargetHref($this->createUrl('account/number'))?>" target="<?php echo $this->target?>"><strong>我的物品&gt;靓号</strong></a> 修改靓号寄语文字。</p><br>
                <p class="gradline"><input class="shiftbtn" type="button" value="确  定" onclick="$.mask.hide('BuyNumbSus');"></p>
            </li>
        </ul>
    </div>
</div>

<div id="GivNumb" class="popbox">
    <div class="poph">
        <span>赠送靓号</span>
        <a title="关闭" class="closed" onclick="$.mask.hide('GivNumb');"></a>
    </div>
    <div class="popcon">
        <div class="bgset-bd">
            <ul class="paysong">
            	<li>
            	<p>靓号：<em class="pink"></em></p>
                <p>价格：<em class="pink"></em>皮蛋</p>
                <p class="end" style="display:none;">
            		<span>代理渠道：</span>
                <strong></strong>
            	</p>
                </li>          
                <li><p>请输入对方ID号</p></li>
                <li><input class="intext" type="text" id="to_uid" style="width:90px;margin-right:10px;"/><input type="button" class="shiftbtn" value="验&nbsp;&nbsp;证" /></li>
                <li><p>受赠者：<em class="pink">无</em></p></li>               
                <li style="display:none;"><input class="shiftbtn" type="button" value="确&nbsp;&nbsp;定"><input onclick="$.mask.hide('GivNumb');" class="shiftbtn" type="button" value="取&nbsp;&nbsp;消"></li>
            </ul>
        </div>
    </div>
</div>

<div id="GivNumbSus" class="popbox">
	<div id="ChangeRun" class="poph">
        <span>赠送成功</span>
        <a title="关闭" class="closed" onclick="$.mask.hide('GivNumbSus');"></a>
    </div>
    <div class="popcon">
    	<ul class="paysong">
            <li>
            	<p></p><br>
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript">
var fourP = 2;
var fiveP = 2;
var sixP = 2;
var sevenP = 2;
var self_buyumber = 0;
var agent_id=<?php echo $this->agent_id;?>;	//选中的代理id
var agent_nickname='<?php echo mb_substr($this->agent_nickname,0,8,'UTF-8');?>';	//选中的代理昵称
function switchFourNumber(type){
	$.ajax({
		type : 'GET',
		url : 'index.php?r=shop/NumberPage&type='+type+'&p='+fourP,
		success:function(data){
			if(data != 'no_page'){
				$('#four_number').html(data);
			}
			if($('#four_renumber').html() == 1){
				fourP = 1;
			}else{
				fourP++;
			}
		}
	});	
}
function switchFiveNumber(type){
	$.ajax({
		type : 'GET',
		url : 'index.php?r=shop/NumberPage&type='+type+'&p='+fiveP,
		success:function(data){
			if(data != 'no_page'){
				$('#five_number').html(data);
			}
			if($('#five_renumber').html() == 1){
				sixP = 1;
			}else{
				sixP++;
			}
		}
	});	
}
function switchSixNumber(type){
	$.ajax({
		type : 'GET',
		url : 'index.php?r=shop/NumberPage&type='+type+'&p='+sixP,
		success:function(data){
			if(data != 'no_page'){
				$('#six_number').html(data);
			}
			if($('#six_renumber').html() == 1){
				sixP = 1;
			}else{
				sixP++;
			}
		}
	});	
}

function switchSevenNumber(type){
	$.ajax({
		type : 'GET',
		url : 'index.php?r=shop/NumberPage&type='+type+'&p='+sevenP,
		success:function(data){
			if(data != 'no_page'){
				$('#seven_number').html(data);
			}
			if($('#seven_renumber').html() == 1){
				sevenP = 1;
			}else{
				sevenP++;
			}
		}
	});	
}

function queryNumber(){
	var number = parseInt($('#input_number').val());
	if($.User.getSingleAttribute('uid',true)<=0){
		$.User.loginController('login');
		return false;
	}
	if($.isEmpty($('#input_number').val())){
		$('#LowStock div p').html('<center style="color:red;">请输入您要购买的靓号</center>');
		$.mask.show('LowStock');
		return false;
	}
	if(!$.isInt(number) || number < 1){
		$('#LowStock div p').html('<center style="color:red;">靓号必须是大于0的数字</center>');
		$.mask.show('LowStock');
		return false;
	}
	if(number.toString().length == 4){
		$('#LowStock div p').html('<center style="color:red;">4位靓号暂未开放购买。</center>');
		$.mask.show('LowStock');
		return false;
	}
	if(number.toString().length < 4 || number.toString().length > 7){
		$('#LowStock div p').html('<center style="color:red;">请输入5位或6位或7位数字的号码</center>');
		$.mask.show('LowStock');
		return false;
	}
	$.ajax({
		type : 'POST',
		url : 'index.php?r=shop/queryNumber',
		data : {number:number},
		dataType:"json",
		success:function(response){
			if(response.flag == 1){
				$('#BuyNumb div ul li p em').eq(0).html(response.message.number);
				$('#BuyNumb div ul li p em').eq(1).html(response.message.short_desc);
				$('#BuyNumb div ul li p em').eq(2).html(response.message.price);
				$('#BuyNumb input').eq(0).unbind('click');
				$('#BuyNumb input').eq(1).unbind('click');
				$('#BuyNumb input').eq(0).bind('click',function(){
					$.mask.hide('BuyNumb');
					buyNumber(response.message.number,'',0);
				})
				$('#BuyNumb input').eq(1).bind('click',function(){
					$.mask.hide('BuyNumb');
					$('#GivNumb div ul li p em').eq(0).html(response.message.number);
					$('#GivNumb div ul li p em').eq(1).html(response.message.price);
					$.mask.show('GivNumb');
					$('#GivNumb input').eq(1).unbind('click');
					$('#GivNumb input').eq(1).bind('click',function(){
						var to_uid = $('#to_uid').val();
						if($.isEmpty(to_uid)){
							alert('请输入您要赠送给用户的UID');
							return false;
						}
						if(!$.isInt(to_uid)){
							alert('用户ID必须是整数');
							return false;
						}
						$.ajax({
							type : 'POST',
							url : 'index.php?r=shop/checkUid',
							data : {uid:$('#GivNumb input').eq(0).val()},
							dataType:"json",
							success:function(res){
								if(res.flag == 1){
									$('#GivNumb div ul li p em').eq(2).html(res.message);
									$('#GivNumb li').eq(4).show();
									$('#GivNumb input').eq(2).unbind('click');
									$('#GivNumb input').eq(2).bind('click',function(){
										sendNumber(response.message.number,0);
									});
								}else{
									$('#GivNumb div ul li p em').eq(2).html(res.message);
									$('#GivNumb li').eq(4).hide();
								}
							}
						});
					});
				});
				addAgent('#BuyNumb div ul li p', 3);
				addAgent('#GivNumb div ul li p', 2);
				$.mask.show('BuyNumb');
			}else{
				$('#LowStock div p').html('<center style="color:red;">'+response.message+'</center>');
				$.mask.show('LowStock');
			}
			
		}
	});
}

function _buyNumber(number,price){
	$('#BuyNumbConfirm div ul li p em').eq(0).html(number);
	$('#BuyNumbConfirm div ul li p em').eq(1).html(price);
	$.mask.show('BuyNumbConfirm');
	$('#BuyNumbConfirm input').eq(0).unbind('click');
	$('#BuyNumbConfirm input').eq(0).bind('click',function(){
		$.mask.hide('BuyNumbConfirm');
		buyNumber(number,'',1);
	});
	addAgent('#BuyNumbConfirm div ul li p', 2);
}

//type ==0 是自助购买行为，type == 1列表购买行为
function buyNumber(number,to_uid,type){
	if(number == null || number == 'undefined'){
		number = self_buyumber;
	}
	if($.isEmpty(number)){
		if(to_uid > 0){
			alert('请输入您要购买的靓号');
		}else{
			$('#LowStock div p').html('<center style="color:red;">请输入您要购买的靓号</center>');
			$.mask.show('LowStock');
		}
		return false;
	}

	if(!$.isInt(number)){
		if(to_uid > 0){
			alert('靓号必须是数字');
		}else{
			$('#LowStock div p').html('<center style="color:red;">靓号必须是数字</center>');
			$.mask.show('LowStock');
		}
		return false;
	}
	if(number.length == 4){
		if(type == 0){
			if(to_uid > 0){
				alert('4位靓号暂未开放购买。');
			}else{
				$('#LowStock div p').html('<center style="color:red;">4位靓号暂未开放购买。</center>');
				$.mask.show('LowStock');
			}
			return false;
		}
	}
	if(number.length < 4 || number.length > 7){
		if(to_uid > 0){
			alert('请输入5位或6位或7位数字的号码');
		}else{
			$('#LowStock div p').html('<center style="color:red;">请输入5位或6位或7位数字的号码</center>');
			$.mask.show('LowStock');
		}
		return false;
	}

	$.ajax({
		type : 'POST',
		url : 'index.php?r=shop/buyNumber',
		data : {number:number,to_uid:to_uid,type:type,agent_id:agent_id},
		dataType:"json",
		success:function(response){
			if(response.flag == 1){
				if(to_uid > 0){
					$.mask.hide('GivNumb');
					$('#GivNumbSus div ul li p').html(response.message);
					$.mask.show('GivNumbSus');
				}else{
					$('#BuyNumbSus div ul li p em').eq(0).html(number);
					$.mask.show('BuyNumbSus');
				}

				if(type == 1){
					$('#number_'+number).remove();
				}
				$.User.refershWebLoginHeader();
			}else{
				//用户赠送时有两个遮照层
				if(to_uid > 0){
					alert(response.message);
				}else{
					$('#LowStock div p').html('<center style="color:red;">'+response.message+'</center>');
					$.mask.show('LowStock');
				}
			}
			
		}
	});
}

function _sendNumber(number,price){
	$('#GivNumb div ul li p em').eq(0).html(number);
	$('#GivNumb div ul li p em').eq(1).html(price);
	$.mask.show('GivNumb');
	$('#GivNumb input').eq(1).unbind('click');
	$('#GivNumb input').eq(1).bind('click',function(){
		var to_uid = $('#to_uid').val();
		if($.isEmpty(to_uid)){
			alert('请输入您要赠送给用户的UID');
			return false;
		}
		if(!$.isInt(to_uid)){
			alert('用户ID必须是整数');
			return false;
		}
		$.ajax({
			type : 'POST',
			url : 'index.php?r=shop/checkUid',
			data : {uid:$('#GivNumb input').eq(0).val()},
			dataType:"json",
			success:function(res){
				if(res.flag == 1){
					$('#GivNumb div ul li p em').eq(2).html(res.message);
					$('#GivNumb li').eq(4).show();
					$('#GivNumb input').eq(2).unbind('click');
					$('#GivNumb input').eq(2).bind('click',function(){
						sendNumber(number,1);
					});
				}else{
					$('#GivNumb div ul li p em').eq(2).html(res.message);
					$('#GivNumb li').eq(4).hide();
				}
			}
		});
	});
	addAgent('#GivNumb div ul li p', 2);
}

function addAgent(obj, position){
	if(agent_id>0)
	{
		$.ajax({
			type:"POST",
			url:"index.php?r=shop/CheckAgentId",
			data:{'agent_id':agent_id},
			dataType:"json",
			success:function(data){
				if(data.flag==1)
				{
					$(obj).eq(position).show();
					$(obj + ' strong').eq(0).html(showAgent(agent_nickname,agent_id));
				}
				else
				{
					$(obj).eq(position).hide();
				}
			}
		});
	}
	else
	{
		$(obj).eq(position).hide();
	}
}

function sendNumber(number,type){
	var to_uid = $('#to_uid').val();
	if($.isEmpty(to_uid)){
		alert('请输入您要赠送给用户的UID');
		return false;
	}

	if(!$.isInt(to_uid)){
		alert('用户ID必须是整数');
		return false;
	}
	buyNumber(number,to_uid,type);
}


$('.jpnumb').live('mouseover mouseout', function(event) {
	  if (event.type == 'mouseover') {
		  $(this).siblings('.tipcon').css('display','block');
	  } else {
		  $(this).siblings('.tipcon').css('display','none');
	  }
});

$('#mynumber').live('mouseover mouseout', function(event) {
	  if (event.type == 'mouseover') {
		  $('span',this).css('display','block');
	  } else {
		  $('span',this).css('display','none');
	  }
});

function selectAgent(uid,nickname)
{
	agent_id=uid;
	agent_nickname=nickname;
	if(uid>0)
	{
		$.cookie('agent_id', uid);
		$(".agentname").html('<span>您选择的代理：</span><span><em class="pink">'+nickname+'</em>('+uid+')<em class="close"></em></span>' );
	}
	else
	{
		$.cookie('agent_id', null);
		$(".agentname").html('<span>您选择的代理：</span><span>无</span>' );
	}
}

selectAgent(agent_id,agent_nickname);
</script>
      
                 
