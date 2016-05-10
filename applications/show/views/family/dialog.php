<div id="AddBadge" class="popbox">
    <div class="poph">
        <span>佩戴徽章</span>
        <a title="关闭" class="closed" onClick="$.mask.hide('AddBadge');"></a>
    </div>
    <div class="popcon">
        <ul class="paysong pybadge">
            <li>
                <p class="pyfor"><img class="vett_mid ml10 mr10" id="a_medal" src="#">您确定佩戴<em class="pink" id="a_name"></em>的族徽</p>
            </li>
            <li>
            	<p>注意：同一时间只可佩戴一枚族徽，您当前佩戴的族徽将被替换。</p>
            </li>
            <li><input id="a_btn" class="shiftbtn" type="button" value="确&nbsp;定&nbsp;佩&nbsp;戴" onclick="window.location.href='<?php echo $this->createUrl('family/equipMedal');?>&family_id='+$(this).attr('data-id');" data-id="0"><input onClick="$.mask.hide('AddBadge');" class="shiftbtn" type="button" value="取&nbsp;&nbsp;消"></li>
        </ul>
    </div>
</div>

<div id="UnloadBadge" class="popbox">
    <div class="poph">
        <span>卸下徽章</span>
        <a title="关闭" class="closed" onClick="$.mask.hide('UnloadBadge');"></a>
    </div>
    <div class="popcon">
        <ul class="paysong pybadge">
            <li>
                <p class="pyfor"><img class="vett_mid ml10 mr10" id="u_medal" src="#">您确定卸下<em class="pink" id="u_name"></em>的族徽</p>
            </li>
            <li><input id="u_btn" class="shiftbtn" type="button" value="确&nbsp;定&nbsp;卸&nbsp;下" onclick="window.location.href='<?php echo $this->createUrl('family/unloadMedal');?>&family_id='+$(this).attr('data-id');" data-id="0"><input onClick="$.mask.hide('UnloadBadge');" class="shiftbtn" type="button" value="取&nbsp;&nbsp;消"></li>
        </ul>
    </div>
</div>

<div id="PayBadge" class="popbox">
    <div class="poph">
        <span>购买家族徽章</span>
        <a title="关闭" class="closed" onClick="$.mask.hide('PayBadge');"></a>
    </div>
    <div class="popcon">
        <ul class="paysong pybadge">
            <li>
                <p class="pyfor"><img class="vett_mid ml10 mr10" id="p_medal" src="#">徽章购买需花费 <em class="pink"><?php echo $medal_price;?></em> 皮蛋</p>
            </li>
            <li>
                <p>同一时间只可佩戴1个族徽，退出家族后族徽消失。</p>
            </li>
            <li><input id="p_btn" class="shiftbtn" type="button" value="确&nbsp;定&nbsp;购&nbsp;买" onclick="window.location.href='<?php echo $this->createUrl('family/buyMedal');?>&family_id='+$(this).attr('data-id');" data-id="0"><input onClick="$.mask.hide('PayBadge');" class="shiftbtn" type="button" value="取&nbsp;&nbsp;消"></li>
        </ul>
    </div>
</div>

<div id="OutBadge" class="popbox">
    <div class="poph">
        <span>退出家族</span>
        <a title="关闭" class="closed" onClick="$.mask.hide('OutBadge');"></a>
    </div>
    <div class="popcon">
        <ul class="paysong pybadge">
            <li>
                <p class="pyfor">您确定要退出<em class="pink" id="o_name"></em>吗？</p>
            </li>
            <li>
                <p>注意：退出家族后购买的族徽将随之消失。</p>
            </li>
            <li><input id="o_btn" class="shiftbtn" type="button" value="确&nbsp;定&nbsp;退&nbsp;出" onclick="window.location.href='<?php echo $this->createUrl('family/quit');?>&family_id='+$(this).attr('data-id');" data-id="0"><input onClick="$.mask.hide('OutBadge');" class="shiftbtn" type="button" value="取&nbsp;&nbsp;消"></li>
        </ul>
    </div>
</div>

<div id="OutSignBadge" class="popbox">
    <div class="poph">
        <span>退出家族</span>
        <a title="关闭" class="closed" onClick="$.mask.hide('OutSignBadge');"></a>
    </div>
    <div class="popcon">
        <ul class="paysong pybadge">
            <li>
                <p>若要退出本签约家族，建议你先与家族长沟通，请家族长将你离出家族。</p>
            </li>
            <li>
                <p>若选择强行退出签约家族，则再加入其他签约家族会受到制约。且强退后60天内不能新建家族或将已有家族转为签约家族。</p>
            </li>
            <li><input id="so_btn" class="shiftbtn" type="button" value="确&nbsp;定&nbsp;退&nbsp;出" onclick="window.location.href='<?php echo $this->createUrl('family/quit');?>&family_id='+$(this).attr('data-id');" data-id="0"><input onClick="$.mask.hide('OutSignBadge');" class="shiftbtn" type="button" value="取&nbsp;&nbsp;消"></li>
        </ul>
    </div>
</div>

<div id="SignJoin" class="popbox">
    <div class="poph">
        <span>重要提醒</span>
        <a title="关闭" class="closed" onClick="$.mask.hide('SignJoin');"></a>
    </div>
    <div class="popcon">
        <ul class="paysong pybadge">
            <li>
                <p>成功加入本签约家族后，您将正式成为该家族的家族主播。同时，不能再加入其它签约家族，若要退出家族，必须获得家族长同意。</p>
            </li>
            <li><input id="j_btn" class="shiftbtn" type="button" value="确定申请加入" onclick="window.location.href='<?php echo $this->createUrl('family/join');?>&family_id='+$(this).attr('data-id');" data-id="0"><input onClick="$.mask.hide('SignJoin');" class="shiftbtn" type="button" value="取&nbsp;&nbsp;消"></li>
        </ul>
    </div>
</div>

<div id="SignNoJoin" class="popbox">
    <div class="poph">
        <span>重要提醒</span>
        <a title="关闭" class="closed" onClick="$.mask.hide('SignNoJoin');"></a>
    </div>
    <div class="popcon">
        <ul class="paysong pybadge">
            <li>
                <p>您已是其他签约家族的家族主播，如果要加入本签约家族，请先退出当前所在的签约家族。</p>
            </li>
            <li><input onClick="$.mask.hide('SignNoJoin');" class="shiftbtn" type="button" value="确&nbsp;&nbsp;定"></li>
        </ul>
    </div>
</div>

<?php
/* 转让家族功能有问题暂时屏蔽
<div id="TranBadgeError" class="popbox">
    <div class="poph">
        <span>转让家族</span>
        <a title="关闭" class="closed" onClick="$.mask.hide('TranBadgeError');"></a>
    </div>
    <div class="popcon">
        <ul class="paysong pybadge">
            <li>
                <p class="pyfor" id="e_user"></p>
            </li>
            <li>
                <p id="e_msg"></p>
            </li>
            <li><input id="e_btn" type="button" value="确&nbsp;&nbsp;&nbsp;&nbsp;定 " class="surebtn" onClick="$.mask.hide('TranBadgeError');"></li>
        </ul>
    </div>
</div>
*/?>
<script type="text/javascript">
$(function(){
	$('#input_uid').focus(function(){
		if($(this).val() == this.defaultValue){  
			$(this).val("");           
		} 
	}).blur(function(){
		if ($(this).val() == '') {
			$(this).val(this.defaultValue);
		}
	});
	$('#select_uid').click(function(){
		var uid = $('#input_uid').val();
		if(uid != '' && uid != '填写ID号'){
			$.ajax({
				url : "<?php echo $this->createUrl('family/checkUid');?>",
				type : "GET",
				data:{'uid':uid},
				dataType : "json",
				success : function(json){
					if(json.status > 0){
						$('#t_uid').val(json.data.uid);
						$('#transfer_user').html('受让方：<em class="mr5 lvlr lvlr-'+json.data.rk+'"></em><em class="pink">'+json.data.nk+'</em> ('+json.data.uid+')');
					}else{
						alert(json.message);
					}
			 	}
			});
		}else{
			alert('填写ID号');
		}
	});
	<?php
	/* 转让家族功能有问题暂时屏蔽
	$('#tranSubmit').click(function(){
		var family_id = $('#t_family').val();
		var to_uid = $('#t_uid').val();
		if(family_id < 1 || to_uid < 1){
			alert('请先填选受让方');
		}else{
			$.ajax({
				url : "<?php echo $this->createUrl('family/transferFamily');?>",
				type : "POST",
				data:{'family_id':family_id, 'to_uid':to_uid, 'password': $('#password').val()},
				dataType : "json",
				success : function(json){
					if(json.status > 0){
						$('#e_user').html('');
						$('#e_msg').html('转让成功，您已卸任家族长。');
						$('#e_btn').click(function(){
							window.location.reload();
						});
					}else{
						var str = "";
						for(var i in json.message){
							if(i == 1 || i == 2){
								str += "未达到创建家族的等级条件，无法接受转让。<br/>";
							}else if(i == 4 || i == 5){
								str += "已有自己的家族，无法接受转让。<br/>";
							}else{
								str += json.message[i]+"<br/>";
							}
						}
						$('#e_msg').html(str);
						if(json.data.nk){
							$('#e_user').html('受让方：<em class="mr5 lvlr lvlr-'+json.data.rk+'"></em><em class="pink">'+json.data.nk+'</em> ('+json.data.uid+')');
						}else{
							$('#e_user').html('');
						}
					}
					$.mask.show('TranBadgeError');
					$.mask.hide('TranBadge');
			 	}
			});
		}
	});
	*/ ?>
});
function equit(id, name, level, status){
	if(status == 1){
		$('#a_btn').attr('data-id', id);
		$('#a_medal').attr('src', '/images/family/'+id+'/medal_'+level+'3.jpg');
		$('#a_name').html(name);
		$.mask.show('AddBadge');
	}else{
		alert('家族筹备中，无法佩戴族徽！');
	}
}
function unload(id, name, level, status){
	if(status == 1){
		$('#u_btn').attr('data-id', id);
		$('#u_medal').attr('src', '/images/family/'+id+'/medal_'+level+'3.jpg');
		$('#u_name').html(name);
		$.mask.show('UnloadBadge');
	}else{
		alert('家族筹备中，无法卸下族徽！');
	}
}
function buyMedal(id, level, status){
	if(status == 1){
		$('#p_btn').attr('data-id', id);
		$('#p_medal').attr('src', '/images/family/'+id+'/medal_'+level+'3.jpg');
		$.mask.show('PayBadge');
	}else{
		alert('家族筹备中，无法购买族徽！');
	}
}
function quit(id, name, sign, dotey){
	if(sign == 1 && dotey == 1){
		$('#so_btn').attr('data-id', id);
		$.mask.show('OutSignBadge');
	}else{
		$('#o_btn').attr('data-id', id);
		$('#o_name').html(name);
		$.mask.show('OutBadge');
	}
}
<?php
/* 转让家族功能有问题暂时屏蔽
function trans(id, name){
	$('#t_family').val(id);
	$('#t_name').html(name);
	$.mask.show('TranBadge');
}
*/ ?>
function join(id, sign, flag){
	if(sign == 0){
		window.location.href='<?php echo $this->createUrl('family/join');?>&family_id='+id;
	}else if(flag == 0){
		$('#j_btn').attr('data-id', id);
		$.mask.show('SignJoin');
	}else{
		$.mask.show('SignNoJoin');
	}
}
</script>