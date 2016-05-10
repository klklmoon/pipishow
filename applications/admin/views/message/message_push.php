<?php
$this->breadcrumbs = array('运营管理','消息推送');
$types = $this->getPushType();
$sendStatus = $this->getIsSendStatus();
$extras = $this->getExtraFlag();
$isSend = $this->getIsSendStatus();
$windows = $this->getWindow();
$service = new ConsumeService();
$userRanks = $service->getUserRankFromRedis();
$doteyRanks = $service->getDoteyRankFromRedis();
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-user"></i> 消息推送记录</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" title="新增推送消息 "><i class="icon-plus-sign"></i></a>
			</div>
		</div>
		
		<div class="box-content">
			<form class="form-horizontal" id="ursearch" action="<?php echo $this->createUrl('message/push');?>" method="post">
				<fieldset>
					<div class="control-group">
					<?php echo CHtml::listBox('form[is_send]', isset($condition['is_send'])?$condition['is_send']:'', $sendStatus,array('empty'=>'-是否已推送-','class'=>'input-small','size'=>1));?>
					<?php echo CHtml::listBox('form[type]', isset($condition['type'])?$condition['type']:'', $types,array('empty'=>'-推送类型-','class'=>'input-small','size'=>1));?>
					<span>创建时间:</span>
					<?php echo CHtml::textField('form[create_time_on]',isset($condition['create_time_on'])?$condition['create_time_on']:'',array('class'=>'date_ui input-small'));?>&nbsp;至&nbsp;
					<?php echo CHtml::textField('form[create_time_end]',isset($condition['create_time_end'])?$condition['create_time_end']:'',array('class'=>'date_ui input-small'));?>
					<?php echo CHtml::submitButton('user_search',array('class'=>'btn','value'=>'搜索','id'=>'user_search_submit'));?>
				</div>
				</fieldset>
			</form>
			<?php echo $this->renderPartial('message_push_'.$t,array('data'=>$data,'pager'=>$pager));?>
		</div>
	</div>
	<!--/span-->
</div>

<div class="modal hide fade" id="dotey_award_manage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>新增推送</h3>
	</div>
	<div class="modal-body" id="dotey_award_manage_body"></div>
</div>

<script>
$(function() {
	$( '#form_create_time_on' ).datepicker(
		{ 
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	$( '#form_create_time_end' ).datepicker(
		{ 
			changeMonth: true,
			changeYear: true,
			dateFormat:'yy-mm-dd'
		}
	);
	//新增赠品
	$('.icon-plus-sign').click(function(e){
		$.ajax({
			url:"<?php echo $this->createUrl('message/push');?>",
			type:'post',
			dataType:'html',
			data:{'op':'addPush'},
			success:function(msg){
				e.preventDefault();
				$('#dotey_award_manage_body').html(msg);
				$('#dotey_award_manage').modal('show');
			}
		});
	});
	//删除
	$('.icon-remove').live('click',function(){
		var pushId = $(this).attr('pushId');
		var obj = this;
		if(pushId){
			$.ajax({
				url:"<?php echo $this->createUrl('message/push');?>",
				type:'post',
				dataType:'html',
				data:{'pushId':pushId,'op':'delPush'},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						alert(msg);
					}		
				}
			});
		}
	});
});
</script>