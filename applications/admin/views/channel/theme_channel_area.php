<?php
$this->breadcrumbs = array('地区频道','地区频道管理');
?>
<div class="row-fluid sortable">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> 地区频道管理</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" title="添加频道地区关联 " ><i class="icon-plus-sign"></i></a>
			</div>
		</div>
		
		<div class="box-content">
			<table class="table table-bordered" id="user_role_relateion">
			  <thead>
				  <tr>
					  <th style="width:140px;">父频道(子频道)</th>
					  <th>地区列表</th>
					  <th style="width:40px;">操作</th>
				  </tr>
			  </thead>
			  <tbody>
			  	<?php if(isset($areaChannel)){?>
			  	<?php foreach($areaChannel as $subid => $info){?>
			  	<tr>
			  		<td><?php echo $info['channel_name'];?>(<?php echo $info['sub_name'];?>)</td>
			  		<td>
			  			<div class="row-fluid sortable ui-sortable">
			  			<div class="box span12">
							<div class="box-content">
								<ul class="nav nav-tabs" id="myTab">
								<?php 
									$i = 0;
									foreach($info['area'] as $province => $citys){?>
									<li class="<?php echo ($i==0)?'active':'';?>">
										<a href="#<?php echo $subid.'_'.$province;?>"><?php echo $province;?>
											<i provinceName="<?php echo $province;?>" subChannelId="<?php echo $subid;?>"></i>
										</a>
									</li>
								<?php 
									++$i;
									}
								?>
								</ul>
								 
								<div id="myTabContent" class="tab-content" style="overflow-x:hidden;overflow-y:hidden;">
								<?php
									$i = 0; 
									foreach($info['area'] as $province => $citys){
								?>
									<div class="tab-pane <?php echo ($i==0)?'active':'';?>" id="<?php echo $subid.'_'.$province;?>" style="margin:5px 0px;">
										<div class="box-icon" provinceName="<?php echo $province;?>" subChannelId="<?php echo $subid;?>" style="margin-bottom:5px;" >
											<a href="#" class="btn btn-round" title="编辑"><i class="icon-edit"></i></a>
										</div>
										<div style="width:88%;">
										<?php foreach($citys as $city){?>
										<span class="label label-success" style="margin:15px 5px 0px 0px;">
							  				<?php echo $city;?>
											<i subChannelId="<?php echo $subid;?>" provinceName="<?php echo $province;?>" cityName="<?php echo $city;?>"></i>
							  			</span>
										<?php }?>
										</div>
									</div>
								<?php 
									++$i;
									}
								?>
								</div>
							</div>
						</div>
			  			</div>
			  		</td>
			  		<td>
			  			<a class="btn" href="#" title="删除"> <i class="icon-remove-sign" subChannelId="<?php echo $subid;?>"></i> </a>
			  		</td>
			  	</tr>
			  	<?php }?>
			  	<?php }else{?>
			  		<tbody>
				  		<tr><td colspan="3">没有配置的数据</td></tr>
			  		</tbody>
			  	<?php }?>
			  </tbody>
		  </table>            
		</div>
	</div><!--/span-->
</div>

<!-- 添加地区频道-->
<div class="modal hide fade" id="channel_area_relation">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>地区频道管理</h3>
	</div>
	<div class="modal-body" id="channel_area_relation_body">
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//城市hover事件
	$("#user_role_relateion tr .tab-pane span").hover(
		function(){
			$(this).children('i').addClass('icon-remove');
		},
		function(){
			$(this).children('i').removeClass('icon-remove');
		}
	);
	//省份
	$(".box-content ul li a").hover(
		function(){
			$(this).children('i').eq(0).addClass('icon-remove');
		},
		function(){
			$(this).children('i').eq(0).removeClass('icon-remove');
		}
	);
	//删除省份
	$(".box-content ul li a i").each(function(){
		$(this).click(function(e){
			var provinceName = $(this).attr('provinceName');
			var subChannelId = $(this).attr('subChannelId');
			var tabHref = $(this).parent('a').attr('href');
			var obj = this;
			if(provinceName && subChannelId){
				$.ajax({
					url:"<?php echo $this->createUrl('channel/createchannelarea');?>",
					type:'post',
					dataType:'html',
					data:{'subChannelId':subChannelId,'provinceName':provinceName,'op':'delChannelAreaProvince'},
					success:function(msg){
						if(msg == 1){
							if($(obj).parents('ul').children('li').length == 1){
								$(obj).parents('tr').detach();
							}else{
								$(tabHref).detach();
								$(obj).parents('li').detach();
							}
						}else{
							$('#channel_area_relation_body').html(msg);
							e.preventDefault();
							$('#channel_area_relation').modal('show');
						}
					}
				});
			}
		});
	});
	
	//针对地区进行编辑操作
	$(".icon-edit").click(function(e){
		var subChannelId = $(this).parents('.box-icon').attr('subChannelId');
		var provinceName = $(this).parents('.box-icon').attr('provinceName');
		if(subChannelId && provinceName){
			$.ajax({
				url:"<?php echo $this->createUrl('channel/createchannelarea');?>",
				type:'post',
				dataType:'html',
				data:{'subChannelId':subChannelId,'provinceName':provinceName,'op':'getChannelArea'},
				success:function(msg){
					$('#channel_area_relation_body').html(msg);
					e.preventDefault();
					$('#channel_area_relation').modal('show');
				}
			});
		}
	});

	//删除频道地区关联
	$('.label i').click(function(e){
		var provinceName =  $(this).attr('provinceName');
		var subChannelId =  $(this).attr('subChannelId');
		var cityName =  $(this).attr('cityName');
		var obj = this;
		if(cityName && subChannelId && provinceName){
			$.ajax({
				url:"<?php echo $this->createUrl('channel/createchannelarea');?>",
				type:'post',
				dataType:'html',
				data:{'subChannelId':subChannelId,'provinceName':provinceName,'cityName':cityName,'op':'delChannelAreaCity'},
				success:function(msg){
					if(msg == 1){
						$(obj).parent('span').detach();
					}else{
						$('#channel_area_relation_body').html(msg);
						e.preventDefault();
						$('#channel_area_relation').modal('show');
					}
				}
			});
		}
	});
	
	//删除事件
	$(".icon-remove-sign").click(function(e){
		var subChannelId = $(this).attr('subChannelId');
		var obj = this;
		if(subChannelId){
			$.ajax({
				url:"<?php echo $this->createUrl('channel/createchannelarea');?>",
				type:'post',
				dataType:'html',
				data:{'subChannelId':subChannelId,'op':'delChannelArea'},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						$('#channel_area_relation_body').html(msg);
						e.preventDefault();
						$('#channel_area_relation').modal('show');
					}
				}
			});
		}
	});

	//添加地区频道
	$(".icon-plus-sign").click(function(e){
		var url = "<?php echo $this->createUrl("channel/createchannelarea")?>";
		$.ajax({
			type:'post',
			dataType:'html',
			url:url,
			success:function(msg){
				if(msg){
					$('#channel_area_relation_body').html(msg);
				}else{
					$('#channel_area_relation_body').html("加载失败");
				}
				e.preventDefault();
				$('#channel_area_relation').modal('show');
			}
			
		});
	});
	
})
</script>