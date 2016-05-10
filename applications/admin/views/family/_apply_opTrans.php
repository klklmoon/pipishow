<div>
	<div class="box-content form-horizontal">
		<div class="control-group">
		  <?php echo CHtml::label('家族名称','family_name',array('class'=>'control-label'));?>
		  <div class="controls">
		  	<?php echo $info['name'];?>
		  	<?php echo CHtml::hiddenField('familyId',$info['id']);?>
		  </div>
		</div>
		
		<div class="control-group">
			<label class="control-label">用户名/UID</label>
			<div class="controls">
			  <input class="input-small focused" id="indexForm_dotey_name" name="dotey_info" type="text" value="">
			  <input class="btn" type="button" value="验证" name="valid_dotey_info" id="valid_dotey_info" isDotey="0">
			  <span class="label label-important"  id="valid_dotey_info_noty" style="margin-left:10px; display:none;" isSubmit="1"></span>
			</div>
		  </div>
		  
		  <!-- 用户结果集合 -->
		  <div id="dotey_info_uids" class="box" style="padding:5px;display:none;"> 
		  	
		  </div>
				  
		
		<div class="control-group">
		  <div class="controls" id="uidflag">
		  	<?php echo CHtml::button('button',array('class'=>'btn','value'=>'确认','id'=>'confirm_button_submit'));?>
		  </div>
		</div>
	</div>
</div>

<script style="text/javascript">
$(function() {
	//验证用户
	$("#valid_dotey_info").click(function(){
		var doteyName = $("#indexForm_dotey_name").attr('value');
		
		if(doteyName){
			$("#valid_dotey_info_noty").html("").hide();
			$.ajax({
				url:"<?php echo $this->createUrl("family/apply");?>",
				type:"post",
				dataType:"text",
				data:{"op":"checkUserInfo","doteyName":doteyName},
				success:function(msg){
					var data = msg.split('#xx#');
					if(data[0] == 1){
						var doteyUid 		= data[1];
						var doteyUsername 	= data[2];
						var doteyNickname 	= data[3];
						var isReturn = false;

						$('#valid_dotey_info_noty').html('').hide();
						
						var html = '<div class="control-group">';
						html += '<label class="control-label">'+doteyUsername+'</label>';
						html += '<div class="controls">';
						html += '<input class="input-small focused" id="to_uid" name="to_uid" type="text" value="'+doteyUid+'" readonly="readonly">';
						html += '<i class="icon-remove" style="margin-left:20px;" onclick="'+"$(this).parents('.control-group').detach()"+'"></i></div></div>';  
						$('#dotey_info_uids').html(html).show();
					}else{
						$("#valid_dotey_info_noty").html(msg).show();
					}
				}
			});
		}else{
			$("#valid_dotey_info_noty").html("请输入用户名称或UID").show();
		}
	});
	
	$('#confirm_button_submit').click(function(e){
		var uid = $('#to_uid').attr('value');
		var familyId = $('#familyId').attr('value');
		if(!uid || isNaN(uid)){
			alert('转让对象不能为不合法');
			return false;
		}

		if(!familyId || isNaN(familyId)){
			alert('家族信息不合法');
			return false;
		}

		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('family/apply',array('op'=>'transFamily'));?>",
			dataType:'html',
			data:{'familyId':familyId,'to_uid':uid},
			success:function(msg){
				e.preventDefault();
				$('#user_list_manage').modal('hide');
				if(msg != 1){
					alert(msg);
				}else{
					var url='<?php echo $this->createUrl('family/apply');?>&uid='+uid;
					window.location.href=url;
				}
			}
		});
	});
});
</script>