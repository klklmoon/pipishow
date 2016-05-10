<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:140px;margin-left:10px;padding:3px 5px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-content">
			<form class="form-horizontal" method="post" action="<?php echo $this->createUrl('dotey/editFamily',array('op'=>'addSiteStar'));?>" enctype="multipart/form-data">
				<fieldset>
				  	<div class="control-group">
				  	<dl style="margin-top:10px;">
						<dt>家族名称</dt>
						<dt>族长(族长UID)</dt>
					</dl>
					<dl class="effect"> </dl>
					<?php if(!empty($myFamilyInfos['create'])){?>
					<dl class="effect" id="<?php echo $myFamilyInfos['create']['uid'];?>">
						<dd>
						≥ <input name="family[<?php echo $myFamilyInfos['create']['id'];?>]['uid']" type="text" readonly="true" id="family[<?php echo $myFamilyInfos['create']['id'];?>]['uid']" class="input-small focused" size="10" value="<?php echo $myFamilyInfos['create']['name'];?>">
						</dd>
						<dt>
							<span class="label label-success">
								<?php echo $userInfos[$myFamilyInfos['create']['uid']]['username'].'('.$myFamilyInfos['create']['uid'].')';?>
							</span>
						</dt>
					</dl>
					<?php }?>
					
					<?php if(!empty($myFamilyInfos['join'])){?>
					<?php foreach($myFamilyInfos['join'] as $v){?>
					<dl class="effect" id="<?php echo $v['uid'];?>">
						<dd>
						≥ <input name="family[<?php echo $v['id'];?>]['uid']" type="text" readonly="true" id="family[<?php echo $v['id'];?>]['uid']" class="input-small focused" size="10" value="<?php echo $v['name'];?>">
						</dd>
						<dt>
							<span class="label label-success">
								<?php echo $userInfos[$v['uid']]['username'].'('.$uid.')';?>
							</span>
						</dt>
						<dt>
							<i class="icon-remove" operateId=<?php echo $v['id'].'_'.$uid;?>></i>
						</dt>
					</dl>
					<?php }?>
					<?php }?>
				  </div>
				  
				  <dl style="border-top:2px #999999 solid"></dl>
				  <div class="control-group">
					<label class="control-label" for="forum_desc">新增家族：</label>
					<div class="controls">
					  <input name="family_uid" type='hidden' value="<?php echo $uid?>">
					  <input name="family_id" type="text" id="family_id" class="input-small focused" size="10" value="">
					  <span class="label label-info">请输入家族号</span>
					</div>
				  </div>
				  <div class="form-actions">
					<button type="submit" class="btn btn-primary" id='family_submit'>提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
		
</div>

<script type="text/javascript">
var type = "<?php echo $type;?>";
$(document).ready(function(){
	
	//删除
	$("dt .icon-remove").click(function(){
		var operateId = $(this).attr('operateId');
		var obj = this;
		if(operateId){
			$.ajax({
				type:'post',
				url:'<?php echo $this->createUrl('dotey/editFamily');?>',
				dataType:'html',
				data:{'op':'delOperate','operateId':operateId},
				success:function(msg){
					if(msg == 1){
						$(obj).parents("dl").detach();
					}else{
						alert(msg);
					}
				}
			});
		}			
	});

	
	$('#family_submit').click(function(){
		var uid='<?php echo $uid;?>';
		var family_id = $('#family_id').attr('value');
		if(isNaN(family_id)){
			alert('家族号只能是数字');
			return false;
		}else{
			$.ajax({
				type:'post',
				url:'<?php echo $this->createUrl('dotey/editFamily');?>',
				dataType:'html',
				data:{'op':'addFamily','familyId':family_id,'uid':uid},
				success:function(msg){
					if(msg != 1){
						alert(msg);
					}else{
						if(type == 'dotey'){
							window.location.href="<?php echo $this->createUrl('dotey/doteylist',array('uid'=>$uid));?>";
						}else{
							window.location.href="<?php echo $this->createUrl('user/usersearch',array('uid'=>$uid));?>";
						}
					}
				}
			});
		}
	});
	
	//提交 前动作 确认所有 file都需要上传文件
	$(":submit").click(function(){
		return false;
	});
	
})
</script>