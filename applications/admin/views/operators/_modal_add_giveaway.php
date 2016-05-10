<div class="modal hide fade" id="dotey_award_manage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>新增赠品</h3>
	</div>
	<div class="modal-body" id="dotey_award_manage_body"></div>
</div>

<script>
$(function() {
	//注册开始时间
	$( '#form_create_time_on' ).datepicker(
		{ 
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//注册开始时间
	$( '#form_create_time_end' ).datepicker(
		{ 
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//搜索提交
	$('#user_search_submit').click(function(){
		var realname = $("#form_realname").attr('value');
		var nickname = $("#form_nickname").attr('value');
		var username = $("#form_username").attr('value');
		if(nickname){
			if(realname.length < 2){
				alert("搜索姓名的关键字太少");
				return false;
			}
		}
		if(username){
			if(username.length < 2){
				alert("搜索账号的关键字太少");
				return false;
			}
		}
		if(realname){
			if(realname.length < 2){
				alert("搜索姓名的关键字太少");
				return false;
			}
		}
		return true;
	});
	//新增赠品
	$('.icon-plus-sign').click(function(e){
		$.ajax({
			url:"<?php echo $this->createUrl('operators/addgiveaway');?>",
			type:'post',
			dataType:'html',
			success:function(msg){
				e.preventDefault();
				$('#dotey_award_manage_body').html(msg);
				$('#dotey_award_manage').modal('show');
			}
		});
	});
});
</script>