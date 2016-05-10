$(function(){
	$("#MianList li").click(function(){
		$(this).addClass("menuvisted").siblings().removeClass("menuvisted");
		var index=$(this).index();
		$("#MainCon").children().eq(index).show().siblings().hide();
	});	
	
	$('#startData,#startData1').focus(function(){
        WdatePicker({
            maxDate:'%y-%M-{%d}',
            dateFmt:'yyyy-MM-dd',
            qsEnabled:false,
            readOnly:true
        });
    });
	
	$('#endData,#endData1').focus(function(){
		WdatePicker({
			minDate:'#F{$dp.$D(\'endData\');}',
			maxDate:'%y-%M-{%d}',
			dateFmt:'yyyy-MM-dd',
			qsEnabled:false,
			readOnly:true
		});
	});
	
	$('#liveListDate').focus(function(){
		WdatePicker({
			maxDate:'%y-%M',
			dateFmt:'yyyy-MM',
			qsEnabled:false,
			readOnly:true
		});
	});

	$('.notice-h').find('a').click(function(){
		$(this).parent().siblings('.notice-con').stop(true,true).toggle('fast');
	});	
	
	$('#exchange').bind('change',function(){
		var val = $(this).val();
		if(val >= 100){
			var _v = parseInt(val/100)*100;
			$(this).val(_v);
			$('#exchange_egg').text(_v + ' 个');
		}else{
			$(this).val(100);
			$('#exchange_egg').text('100 个');
		}
	});
	
	$('#change_pswd').bind('click',function(){
		var pswd = $('#password').val();
		var newpswd = $('#newpswd').val();
		var repswd = $('#renewpswd').val();
		var uid = $('#uid').val();
		if(pswd.length == 0){
			$('.otline').html('请输入原始密码');
			$.mask.show('FryFail',3000);
			return false;
		} 
		if(newpswd.length == 0 || newpswd.length < 4 || newpswd.length > 20){
			$('.otline').html('请输入4到20位长度新密码');
			$.mask.show('FryFail',3000);
			return false;
		}
		/* if(! /(?:\d+.*[a-zA-Z]+)|(?:[a-zA-Z]+.*\d+)/.test(newpswd)){
			$('.otline').html('密码必须同时包括数字和字母');
			$.mask.show('FryFail',3000);
			return false;
		} */
		if(repswd.length == 0){
			$('.otline').html('请确认新密码');
			$.mask.show('FryFail',3000);
			return false;
		}
		if(newpswd != repswd){
			$('.otline').html('新密码和确认密码不一致');
			$.mask.show('FryFail',3000);
			return false;
		}
		$.ajax({
			url : 'index.php?r=account/password',
			data:{'password':pswd,'newpswd':newpswd,'repswd':repswd,'uid':uid},
			type:'post',
			success:function(e){
				$('.otline').html(e);
				$.mask.show('FryFail',3000);
				$('input[type=password]').val('');
			}
		});
	});
	
	$('#edit_user_info').click(function(){
		var patrn=/^\S{2,15}$/;
		var patrn2=/\s+|^c:\\con\\con|(&\d{2})|(%\d{2})|[%,\*\"\s\<\>\|\&\\\[\]\/\?\^\+`~]/;
		var uid = $('#uid').val();
		var nickname = $('#acc_nick_name').val();
		var gender = $('#gender').val();
		var birthday = $('#startData1').val();
		var province = $('#channelarea_province').val();
		var city = $('#channelarea_city').val();
		if(patrn.test(nickname)) {
			
			if(patrn2.test(nickname)) {
				$('.otline').html('昵称中含有不允许的字符');
				$.mask.show('FryFail',3000);
				return 0;
			}
			
			$.ajax({
				type: "POST",
				url: "index.php?r=account/edit",
				data: {'uid':uid,"nickname":nickname,'gender':gender,'birthday':birthday,'province':province,'city':city},
				dataType: "json",
				async: false,
				success: function(e){
					if(e.result){
						$('#account_nickname').text(nickname);
					}
					$('.otline').html(e.msg);
					$.mask.show('FryFail',3000);
				}
			});
		}else{
			$('.otline').html('请输入正确的昵称,2-15个英文字符或汉字');
			$.mask.show('FryFail',3000);
		}
	});
	
	$('#edit_dotey_info').click(function(){
		var patrn=/^\S{2,15}$/;
		var patrn2=/\s+|^c:\\con\\con|(&\d{2})|(%\d{2})|[%,\*\"\s\<\>\|\&\\\[\]\/\?\^\+`~]/;
		
		var realname = $('#realname').val();
		var archives_id = $('#archives_id').val();
		var dotey_title = $('#dotey_title').val();
		var birthday = $('#startData1').val();
		var province = $('#channelarea_province').val();
		var city = $('#channelarea_city').val();
		var profession = $('#profession').val();
		var sub_id = $('input[name=sub_id]').val();
//		var sub_title = $('#sub_title').val();
		var description = $('#description').val();
		
		var data_str = '';
		if($('#realname').length > 0){
			if(!patrn.test(realname)){
				$('.otline').html('真实姓名长度为2到15位');
				$.mask.show('FryFail',2000);
				return;
			}
			if(patrn2.test(realname)){
				$('.otline').html('真实姓名不能含有特殊符号');
				$.mask.show('FryFail',2000);
				return;
			}
			data_str += '&realname=' + realname;
		}
		if($('#dotey_title').length > 0){
			if(patrn2.test(dotey_title)){
				$('.otline').html('直播间名称不能含有特殊符号');
				$.mask.show('FryFail',2000);
				return;
			}
			data_str += '&dotey_title=' + dotey_title;
		}
		if(birthday.length==0){
			$('.otline').html('请选择生日');
			$.mask.show('FryFail',2000);
			return;
		}
		if(province.length==0 || city.length==0 || city=='选择城市'){
			$('.otline').html('请选择省市地区');
			$.mask.show('FryFail',2000);
			return;
		}
		if((profession.replace(/(^\s*)|(\s*$)/g, "")).length <=0 ){
			$('.otline').html('职业不能为空');
			$.mask.show('FryFail',2000);
			return ;
		}
		if(description.length > 1000){
			$('.otline').html('个人介绍长度不能超过1000');
			$.mask.show('FryFail',2000);
			return ;
		}
		$.ajax({
			type: "POST",
			url: "index.php?r=account/editdotey",
			data: 'archives_id='+archives_id+'&birthday='+birthday+'&province='+province+'&city='+city+'&profession='+profession+'&sub_id='+sub_id+'&description='+description+data_str,
			dataType: "json",
			async: false,
			success: function(e){
				$('.otline').html(e.msg);
				$.mask.show('FryFail',3000);
			}
		});
	});
	$('#account_song_search img').click(function(){
		$('#account_song_search').submit();
	});
	$('#edit_dotey_income').click(function(){
		$('#account_edit_dotey_form').submit();
	});
	
});
var account = {
	// 取消房管
	undo_maneger : function(uid,aid){
		$.ajax({
			type: "POST",
			url: "index.php?r=account/undoManager",
			data: {'uid':uid,'aid':aid},
			dataType: "json",
			async: false,
			success: function(e){
				$('.otline').html(e.msg);
				$.mask.show('FryFail',3000);
				if(e.result){
					setTimeout(function(){window.location.reload()},3000);
				}
			}
		});
	},
	// 取消管理
	cancelPurview : function(uid,arid){
		$.ajax({
			url:"index.php?r=account/undoManage",
			data:{'uid':uid,'arid':arid},
			type: "POST",
			dataType: "json",
			success:function(c){
				$('.otline').html(c.msg);
				$.mask.show('FryFail',3000);
				if(c.result){
					setTimeout(function(){window.location.reload()},3000);
				}
			}
		});
	},
	// 上传头像
	upload_avatar:function(obj){
		$('#account_avatar').hide('',function(){
			$('#account_avatar_upload').show('slow');
		});
	},
	// 座驾管理
	change_car:function(prop_id){
		$.ajax({
			type: "POST",
			url: "index.php?r=account/undoCar",
			data: {'prop_id':prop_id},
			dataType: "json",
			async: false,
			success: function(e){
				$('.otline').html(e.msg);
				$.mask.show('FryFail',3000);
				if(e.result){
					setTimeout(function(){window.location.reload()},3000);
				}
			}
		});
	},
	// 皮点/魅力点兑换皮蛋
	exchangeEgg:function() {
		var exchange = $('#exchange').val();
		var points = parseInt($('em[data_point=points]').text());
		if(exchange==0){
			$('.otline').html('请输入兑换的点数');
			$.mask.show('FryFail',3000);
			return 0;
		}
		if(exchange > points){
			$('.otline').html('您的皮点不足，无法兑换成功!');
			$.mask.show('FryFail',3000);
			return 0;
		}
		$.ajax({
			type: "POST",
			url: "index.php?r=account/doExchange",
			data: {'exchange':exchange},
			dataType: "json",
			async: false,
			success: function(e){
				$('.otline').html(e.msg);
				$.mask.show('FryFail',3000);
				if(e.result){
					setTimeout(function(){window.location.reload()},3000);
				}
			}
		});
	},
	// 魅力兑现
	exchange:function() {
		var charmpoints = parseInt($('em[datas=charmpoints]').text());
		var meili = $('input[name=meili]').val();
		var exchange_charmpoints=$('em[datas=exchange_charmpoints]').text();
		if(exchange_charmpoints > charmpoints){
			$('.otline').html('魅力点不足');
			$.mask.show('FryFail',3000);
			return 0;
		}
		$.ajax({
			type: "POST",
			url: "index.php?r=account/docash",
			data: {'meili':meili},
			dataType: "json",
			async: false,
			success: function(e){
				$('.otline').html(e.msg);
				$.mask.show('FryFail',3000);
				if(e.result){
					setTimeout(function(){window.location.reload()},3000);
				}
			}
		});
	},
	exchangeval:function(){
		var val = $('input[name=meili]').val();
		var exchange_value = $('input[name=exchangeval]').val();
		if(val >= 100){
			var _v = parseInt(val/100)*100;
			$('input[name=meili]').val(_v);
			$('em[datas=exchange_charmpoints]').text(Math.ceil(_v  / exchange_value));
		}else{
			$('input[name=meili]').val(100);
			$('em[datas=exchange_charmpoints]').text(Math.ceil(100  / exchange_value));
		}
	},
	liveListSearch:function(e){
		var time = $('#liveListDate').val();
		if(time.length<=0){
			$('.otline').html('请选择查询月份');
			$.mask.show('FryFail',3000);
			return ;
		}
		$('#account_search_live_time').submit();
	},
	checkin:function(e){
		$.ajax({
			type: "POST",
			url: "index.php?r=account/checkin",
			data: {'checkinAll':e},
			dataType: "json",
			async: false,
			success: function(e){
				if(e.is_month && e.result==false){
					$('#SignFram .surebtn').hide();
					$('#SignFram .shiftbtn').show();
				}
				$('#SignFram .sucinfo').html(e.msg);
				$.mask.show('SignFram');
				if(e.result){
					setTimeout(function(){window.location.reload()},3000);
				}
			}
		});
	},
	vip_hidden:function(status){
		$.ajax({
			type: "POST",
			url: "index.php?r=account/VipHide",
			data: {'is_hidden':status},
			dataType: "json",
			async: false,
			success: function(e){
				$('.otline').html(e.msg);
				$.mask.show('FryFail',3000);
				if(e.result){
					$('#account_vip_show').toggle();
					$('#account_vip_hidden').toggle();
				}
			}
		});
	},
	rank_get_car:function(car){
		$.ajax({
			type: "POST",
			url: "index.php?r=account/getCar",
			data: {'car':car},
			dataType: "json",
			async: false,
			success: function(e){
				$('.otline').html(e.msg);
				$.mask.show('FryFail',3000);
				if(e.result){
					setTimeout(function(){window.location.reload()},3000);
				}
			}
		});
	},
	msgDel:function(e,msgId,action){
		$.ajax({
			type: "POST",
			url: "index.php?r=account/delMsg",
			dataType: "json",
			data:{'id':msgId,'action':action},
			success: function(d){
				if(d.result){
					if(action=='del'){
						$(e).parents('li').hide();
					}else{
						$(e).css({'color':'#999'});
						var num = parseInt($(this).html()) - 1 > 0 ? parseInt($(this).html()) - 1 : '';
						$('#unread').html(num);
					}
				}
			}
		});
	}
};
$(function(){
	$('#account_avatar a').click(function(){
		account.upload_avatar($(this).parent);
	});
	$('#account_avatar').hover(function(){
		$('.imghead2').css('display','block');
	},function(){
		$('.imghead2').css('display','none');
	});	
	$('#account_avatar_upload a').click(function(){
		$('#account_avatar_upload').hide(function(){
			$('#account_avatar').show('slow');
		});
	});
	
});
function updateavatar(){
	$.ajax({
		type: "POST",
		url: "index.php?r=account/main",
		dataType: "json",
		success: function(e){
			$('.otline').html(e.msg);
			$.mask.show('FryFail',3000);
			if(e.result){
				setTimeout(function(){window.location.reload();},2000);
			}
		}
	});
}