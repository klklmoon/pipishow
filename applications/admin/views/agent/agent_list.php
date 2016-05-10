<?php
$this->breadcrumbs = array('代理管理','代理配置');
?>
<style type="text/css">
	dl{clear:left;width:auto;}
	dt,dd{float:left;width:9%;margin-left:10px;padding:3px 3px;}
</style>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>授权代理</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<form id="form_grant" class="form-horizontal" style="margin: 0px;" method="post" action="<?php echo $this->createUrl('agent/list',array('op'=>'grant'));?>" enctype="multipart/form-data">
				<fieldset>
				 <div class="control-group">
					<label class="control-label" for="agnet_uid">授权对象ID</label>
					<div class="controls">
					  <input class="input-small focused" id="agent_uid" name="uid" type="text" value="">
					  <input class="btn" type="button" value="验证" name="valid_agent_uid" id="valid_agent_uid">
					  <span class="label label-important"  id="valid_agent_uid_noty" style="margin-left:10px; display:none;" isSubmit="1"></span>
					</div>
				  </div>
				  
				  <div class="control-group" style="display: none;" id="show_addAgent">
				      <label class="control-label" id="text_addAgent" style="margin-left: 76px;margin-right: 10px;"></label>
				      <div class="controls">
						  <a href="javascript:;" id="addAgent" class="btn">授权成为代理</a>
				      </div>
				   </div>
				</fieldset>
			</form>
		</div>
	</div>
</div>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>代理配置</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<form class="form-horizontal" style="margin: 0px;" method="post" action="<?php echo $this->createUrl('agent/list',array('op'=>'config'));?>" enctype="multipart/form-data">
				<fieldset>
				<div class="control-group">
					<label class="control-label" for="config[global_enable]" style="padding-right: 20px;width: 320px;">是否启用全局配置</label>
					<div class="controls">
						<?php echo CHtml::radioButtonList('config[global_enable]' ,isset($config['global_enable'])?$config['global_enable']:'', array(1=>'开启',0=>'关闭'),
								array(
									'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;',
									'labelOptions'=>array('class'=>'checkbox inline'),
								)
							);
						?>
					</div>
				  </div>
				 <div class="control-group">
					<label class="control-label" for="config[global_lightup_condition]" style="padding-right: 20px;width: 320px;">代理人点亮售字标志的所需累计销售皮蛋数</label>
					<div class="controls">
					  <input class="input-small focused" id="indexForm_global_lightup_condition" name="config[global_lightup_condition]" type="text" value="<?php echo $config['global_lightup_condition'];?>">
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="config[global_rate]" style="padding-right: 20px;width: 320px;">默认商品代理提成比例</label>
					<div class="controls">
					  <input class="input-small focused" id="indexForm_global_rate" name="config[global_rate]" type="text" value="<?php echo $config['global_rate'];?>">
					</div>
				  </div>
				  <div style="margin-left: 160px;">
					<button type="submit" class="btn btn-primary">提交</button>
				  </div>
				</fieldset>
			</form>
		</div>
	</div>
</div>

<div class="row-fluid sortable ui-sortable">
	<div class="box span12">
		<?php if(!isset($isAjax) || !$isAjax){?>
		<div class="box-header well" data-original-title="">
			<h2><i class="icon-edit"></i>代理列表</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<?php }?>
		<div class="box-content">
			<form class="form-horizontal" id="search" action="<?php echo $this->createUrl('agent/list');?>" method="post">
				<fieldset>
				<?php echo CHtml::listBox('agent_status',isset($conditions['agent_status'])?$conditions['agent_status']:'',array(0=>'正常代理', 1=>'停用代理'),array('class'=>'input-small','size'=>1,'empty'=>'-代理状态-'));?>
				<?php echo CHtml::submitButton('search',array('class'=>'btn','value'=>'搜索','id'=>'search_submit'));?>
				</fieldset>
			</form>
			<table class="table table-bordered" id="gift_list_table">
				<thead>
					<tr>
						<th>代理人</th>
						<th>姓名</th>
						<th>手机号/QQ号</th>
						<th>授权时间</th>
						<th>授权状态</th>
						<th>管理操作</th>
					</tr>
				</thead>
				<tbody>
					<?php if($list['count']>0){?>
				  	<?php foreach($list['list'] as $agent){?>
				  	<tr>
						<td><?php echo $agent['agent_nickname'];?>(<?php echo $agent['uid']?>) </td>
						<td><?php echo $agent['agent_name'];?> </td>
						<td><?php echo $agent['agent_mobile'];?>/<?php echo $agent['agent_qq'];?> </td>
						<td><?php echo date('Y-m-d', $agent['update_time']);?> </td>
						<td><?php echo ($agent['agent_status'] == 0?'正常':'停用');?> </td>
						<td>
							<?php if($agent['agent_status'] == 0){?>
							<span class="btn" title="停用授权"><i class="icon-pause" uid="<?php echo $agent['uid'];?>"></i></span>
							<?php }else{?>
							<span class="btn" title="恢复授权"><i class="icon-play" uid="<?php echo $agent['uid'];?>"></i></span>
							<?php }?>
							<span class="btn" title="编辑资料"><i class="icon-edit" uid="<?php echo $agent['uid'];?>"></i></span>
						</td>
					</tr>
				  	<?php }?>
				  	<tr>
				  		<td colspan="6">
				  			<div class="pagination pagination-centered">
				  			<?php    
							$this->widget('CLinkPager',array(
					            'header'=>'',  
								'firstPageCssClass' => '',  
					            'firstPageLabel' => '首页',    
					            'lastPageLabel' => '末页',  
					            'lastPageCssClass' => '',  
								'previousPageCssClass' =>'prev disabled',  
					            'prevPageLabel' => '上一页',    
					            'nextPageLabel' => '下一页', 
								'nextPageCssClass' => 'next', 
								'selectedPageCssClass' => 'active',
								'internalPageCssClass' => '',
								'htmlOptions' => array('class'=>''),
					            'pages' => $list['pager'],    
					            'maxButtonCount'=>8    
								)
							);
							?>
							</div>
				  		</td>
				  	</tr>
				  	<?php }else{?>
					<tbody>
					<tr>
						<td colspan="11">没有配置的数据</td>
					</tr>
					</tbody>
				  	<?php }?>
				  </tbody>
			</table>
		</div>
	</div>
		
</div>

<!-- 浮层 -->
<div class="modal hide fade" id="user_list_manage">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>代理主播管理</h3>
	</div>
	<div class="modal-body" id="user_list_manage_body"></div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//验证用户
	$("#valid_agent_uid").click(function(){
		var agent_uid = $("#agent_uid").attr('value');
		if(agent_uid){
			$("#valid_agent_uid_noty").html("").hide();
			$.ajax({
				url:"<?php echo $this->createUrl("agent/list");?>",
				type:"post",
				dataType:"text",
				data:{"op":"checkAgent","uid":agent_uid},
				success:function(msg){
					var data = msg.split('#xx#');
					if(data[0] == 1){
						$("#valid_agent_uid_noty").html(data[3]+'&nbsp;&nbsp; 验证通过').show();
						$('#text_addAgent').html('昵称：'+data[3]+' ('+data[1]+')');
						$("#show_addAgent").show();
						
					}else{
						$("#valid_agent_uid_noty").html(msg).show();
						$("#show_addAgent").hide();
					}
				}
			});
		}else{
			$("#valid_agent_uid_noty").html("请输入用户ID").show();
			$("#show_addAgent").hide();
		}
	});

	//添加代理 
	$("#addAgent").click(function(){
		$('#form_grant').submit();
	});

	//停用授权
	$('.icon-pause').click(function(e){
		var uid = $(this).attr('uid');
		var obj = this;
		if(uid &&　confirm('确定停用授权')){
			window.location.href='<?php echo $this->createUrl('agent/list',array('op'=>'ungrant'));?>&uid='+uid;
		}
	});

	//恢复授权
	$('.icon-play').click(function(e){
		var uid = $(this).attr('uid');
		var obj = this;
		if(uid &&　confirm('确定恢复授权')){
			window.location.href='<?php echo $this->createUrl('agent/list',array('op'=>'grant'));?>&uid='+uid;
		}
	});

	//编辑信息
	$('.icon-edit').click(function(e){
		var uid = $(this).attr('uid');
		var obj = this;
		if(uid){
			$.ajax({
				url:"<?php echo $this->createUrl('agent/list', array('op'=>'edit'));?>",
				type:'post',
				dataType:'html',
				data:{"uid":uid},
				success:function(msg){
					e.preventDefault()
					$('#user_list_manage_body').html(msg);
					$('#user_list_manage').modal('show');
				}
			});
		}
	});
	
})
</script>