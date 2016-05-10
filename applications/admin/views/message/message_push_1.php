<?php
$extras = $this->getExtraFlag();
$isSend = $this->getIsSendStatus();
$service = new ConsumeService();
$userRanks = $service->getUserRankFromRedis();
?>
<table class="table table-bordered" id="gift_list_table">
	<thead>
		<tr>
			<th>推送ID</th>
			<th>范围</th>
			<th style="overflow: hidden;white-space: nowrap;width:100px;">目标对象</th>
			<th style="overflow: hidden;white-space: nowrap;width:100px;">消息标题</th>
			<th>发送</th>
			<th style="overflow: hidden;white-space: nowrap;width:150px;">内容</th>
			<th style="overflow: hidden;white-space: nowrap;width:70px;">tips</th>
			<th>扩展信息</th>
			<th>创建时间</br>预发送时间</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>
	  	<?php if(!empty($data['list'])){?>
	  	<?php 
	  		foreach($data['list'] as $uinfo){
	  	?>
	  	<tr>
	  		<td><?php echo $uinfo['push_id']?></td>
	  		<td>
	  			<?php 
	  				if (isset($userRanks[$uinfo['rank']])){
	  					echo $userRanks[$uinfo['rank']]['name'];
	  				}else{
	  					echo '无';	
	  				}
	  			?>
	  		</td>
	  		<td style="margin:0px;padding:0px;"><p style="border:0px;overflow-y: auto;overflow-x: auto;width:100px;height:50px;size:10px;"><?php echo $uinfo['target_id']?></p></td>
	  		<td style="margin:0px;padding:0px;"><p style="border:0px;overflow-y: auto;overflow-x: auto;width:100px;height:50px;size:10px;"><?php echo ($uinfo['title'])?$uinfo['title']:'空'?></p></td>
	  		<td><?php echo $isSend[$uinfo['is_send']];?></td>
	  		<td style="margin:0px;padding:0px;"><p style="border:0px;overflow-y: auto;overflow-x: auto;width:150px;height:50px;size:10px;"><?php echo $uinfo['content']?></p></td>
	  		<td style="margin:0px;padding:0px;"><p style="border:0px;overflow-y: auto;overflow-x: auto;width:70px;height:50px;size:10px;"><?php echo $uinfo['tips']?></p></td>
			<td>
				<?php 
				 $uinfo['extra'] = json_decode($uinfo['extra'],true);
				 foreach($uinfo['extra'] as $k=>$v){
				 	if($v && isset($extras[$k])){
				 		echo $extras[$k]['name'].':'.$v.'</br>';
				 	}
				 }
				?>
			</td>
			<td><?php echo date('Y-m-d H:i:s',$uinfo['create_time']);?></br><?php echo ($uinfo['send_time'])?date('Y-m-d H:i:s',$uinfo['send_time']):'';?></td>
			<td>
				<i class="icon-remove" pushId="<?php echo $uinfo['push_id'];?>"></i>
			</td>
		</tr>
	  	<?php 
	  		}
	  	?>
	  	<tr>
			<td colspan="10">
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
		            'pages' => $pager,    
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
			<td colspan="10">没有配置的数据</td>
		</tr>
	</tbody>
	  	<?php }?>
	  </tbody>
</table>