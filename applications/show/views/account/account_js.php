<script type="text/javascript">
$(function(){
	$("#MianList li").click(function(){
		$(this).addClass("menuvisted").siblings().removeClass("menuvisted");
		var index=$(this).index();
		$("#MainCon").children().eq(index).show().siblings().hide();
	});	
	
	$('#startData').focus(function(){
		WdatePicker({
			maxDate:'%y-%M-{%d}',
			dateFmt:'yyyy-MM-dd',
			qsEnabled:false,
			readOnly:true
		});
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

	$('.notice-h').find('a').toggle(function(){
		$(this).parent().siblings('.notice-con').stop(true,true).slideDown('fast');	
	},function(){
		$(this).parent().siblings('.notice-con').stop(true,true).slideUp('fast');
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
			alert('请输入原始密码');
			return false;
		} 
		if(newpswd.length == 0 || newpswd.length < 6){
			alert('请输入至少6六位长度新密码');
			return false;
		}
		if(repswd.length == 0){
			alert('请确认新密码');
			return false;
		}
		if(newpswd != repswd){
			alert('新密码和确认密码不一致');
			return false;
		}
		$.ajax({
			url : 'index.php?r=account/password',
			data:{'password':$.md5(pswd),'newpswd':$.md5(newpswd),'repswd':$.md5(repswd),'uid':uid},
			type:'post',
			success:function(e){
				alert(e);
				$('input[type=password]').val('');
			},
		});
	});
	
	$('#edit_user_info').click(function(){
		var patrn=/^\S{3,15}$/;
		var patrn2=/\s+|^c:\\con\\con|(&\d{2})|(%\d{2})|[%,\*\"\s\<\>\|\&\\\[\]\/\?\^\+`~]/;
		var uid = $('#uid').val();
		var nickname = $('#nickname').val();
		var gender = $('#gender').val();
		var birthday = $('#startData1').val();
		var province = $('#channelarea_province').val();
		var city = $('#channelarea_city').val();
		if(patrn.test(nickname)) {
			
			if(patrn2.test(nickname)) {
				alert("昵称中含有不允许的字符");
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
					alert(e.msg);
					// console.log(e);
				}
			});
		}else{
			alert("请输入正确的昵称,3-15个英文字符或汉字");
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
		var sub_title = $('#sub_title').val();
		var description = $('#description').val();
		
		var data_str = '';
		if(realname){
			if(patrn.test(realname)){
				data_str += '&realname=' + realname;
			}
		}
		if(dotey_title){
			data_str += '&dotey_title=' + dotey_title;
		}
		$.ajax({
			type: "POST",
			url: "index.php?r=account/editdotey",
			data: 'archives_id='+archives_id+'&birthday='+birthday+'&province='+province+'&city='+city+'&profession='+profession+'&sub_title='+sub_title+'&description='+description+data_str,
			dataType: "json",
			async: false,
			success: function(e){
				alert(e.msg);
			}
		});
	});
	
});
	var account = {
		undo_maneger : function(id){
			$.ajax({
				type: "POST",
				url: "index.php?r=account/undoManager",
				data: {'plid':id},
				dataType: "json",
				async: false,
				success: function(e){
					alert(e.msg);
				}
			});
		},
	};
</script>