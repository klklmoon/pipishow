<?php
$this->breadcrumbs = array('主播管理','主播经理');
?>
<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:9%;margin-left:10px;padding:3px 3px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!isset($isAjax) || !$isAjax){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>主播经理列表</h2>
		</div>
		<?php }?>
		<div class="box-content">
			<?php if(isset($notices) && count($notices)>0){?>
			<div class="alert alert-block" style="margin-left:60px;margin-right:200px;clear:both;">
				<button type="button" class="close" data-dismiss="alert">×</button>
			<?php foreach($notices as $notice){?>
				<p><?php echo isset($notice[0])?$notice[0]:$notice;?></p>
			<?php }?>
			</div>
			<?php }?>
			<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('dotey/manager',array('op'=>'editManager'));?>">
				<fieldset>
				  	<div class="control-group">
				  	<dl style="margin-top:10px;">
						<dt>账号名</dt>
						<dt>昵称</dt>
						<dt>员工姓名</dt>
						<dt>官方QQ</dt>
						<dt>手机号码</dt>
						<dt>管辖主播</dt>
						<dt>前台显示</dt>
						<dt>管理操作</dt>
					</dl>
					<?php if(isset($managerList)){?>
					<?php foreach($managerList as $uid=>$data){?>
					<dl class="effect" id="list_<?php echo $uid;?>">
						<dd>
							<?php echo $data['username'];?>
						</dd>
						<dt>
							<input name="user[nickname][<?php echo $uid?>]" type="text" id="user_nickname_<?php echo $uid;?>" class="input-small focused" size="10" value="<?php echo $data['nickname'];?>">
						</dt>
						<dt>
							<input name="user[realname][<?php echo $uid?>]" type="text" id="user_realname_<?php echo $uid;?>" class="input-small focused" size="10" value="<?php echo $data['realname'];?>">
						</dt>
						<dt>
							<input name="user[qq][<?php echo $uid?>]" type="text" id="user_qq_<?php echo $uid;?>" class="input-small focused" size="10" value="<?php echo $data['qq'];?>">
						</dt>
						<dt>
							<input name="user[mobile][<?php echo $uid?>]" type="text" id="user_mobile_<?php echo $uid;?>" class="input-small focused" size="10" value="<?php echo $data['mobile'];?>">
						</dt>
						<dt>
							<?php echo $data['total_dotey'];?>
						</dt>
						<dt>
							<?php echo CHtml::listBox("user[is_display][{$uid}]", $data['is_display'], array('0'=>'隐藏','1'=>'显示'),array('size'=>1,'class'=>'input-small'));?>
						</dt>
						<dt style="width:14%;">
							<?php if($data['is_new']) {?>
								<span class="btn"><i class="icon-circle-arrow-up" uid="<?php echo $uid;?>" title="导入新导师（主播经理）"></i></span>
							<?php }else{?>
								<span class="btn"><i class="icon-pencil" uid=<?php echo $data['uid'];?> title="确认修改"></i></span>
							<?php }?>
							<span class="label label-important" id="valid_dotey_info_noty" style="margin-left:5px;display:none;"></span>
						</dt>
					</dl>
					<?php }?>
					<?php }?>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script type="text/javascript">
$(document).ready(function(){
	//导入信息
	$('.icon-circle-arrow-up').click(function(){
		var uid = $(this).attr('uid');
		var obj = this;
		if(uid){
			$.ajax({
				url:"<?php echo $this->createUrl('dotey/manager');?>",
				type:'post',
				dataType:'text',
				data:{"user[uid]":uid,'op':'addManager'},
				success:function(msg){
					if(msg == 1){
						$(obj).parent('span').empty().html("<i class='icon-pencil' uid='"+uid+"' title='编辑'></i>");
						msg = '成功添加为导师';
					}
					$(obj).parent('span').next('#valid_dotey_info_noty').html(msg).show();
					setTimeout(function(){ $(obj).parent('span').next('#valid_dotey_info_noty').html('').hide(1000);},2000);
				}
			});
		}
	});

	//导入信息
	$('.icon-pencil').click(function(){
		var uid = $(this).attr('uid');
		var obj = this;
		if(uid){
			var nickname = $('#user_nickname_'+uid).attr('value');
			var realname = $('#user_realname_'+uid).attr('value');
			var qq = $('#user_qq_'+uid).attr('value');
			var mobile = $('#user_mobile_'+uid).attr('value');
			var is_display = $('#user_is_display_'+uid).attr('value');
			if(!nickname || !realname || !qq || !mobile || is_display<0){
				alert('昵称,姓名,QQ,手机 信息,前台显示状态不能为空 ');
				return false;
			}

			$.ajax({
				url:"<?php echo $this->createUrl('dotey/manager');?>",
				type:'post',
				dataType:'text',
				data:{"user[uid]":uid,"user[nickname]":nickname,"user[qq]":qq,"user[mobile]":mobile,'user[realname]':realname,'user[is_display]':is_display,'op':'editManager'},
				success:function(msg){
					if(msg == 1){
						msg = '成功修改导师信息';
					}
					$(obj).parent('span').next('#valid_dotey_info_noty').html(msg).show(500);
					setTimeout(function(){ $(obj).parent('span').next('#valid_dotey_info_noty').html('').hide(500);},2000);
				}
			});
		}
	});
})
</script>