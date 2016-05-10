<?php
	$this->breadcrumbs = array('道具列表管理');
?>
<div class="row-fluid sortable">		
	<div class="box span12">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> 道具列表管理</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" title="添加道具"><i class="icon-plus-sign"></i></a>
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<div class="box-content">
			<?php $this->widget('zii.widgets.grid.CGridView', array(
 				'id'=>'roleList_grid',
				'dataProvider'=>$dataProvider->searchProps($condition),
				'filter'=>$model,
				'tagName' => 'div',
				'summaryText' => '',
				'summaryCssClass' => '',
				'ajaxUpdate' => false,
				'itemsCssClass' => 'table table-striped table-bordered bootstrap-datatable',
				'htmlOptions' => array('class'=>'box-content'),
				'pagerCssClass' => 'pagination pagination-centered',
				'pager' => array(
					'class'=>'CLinkPager',
					'selectedPageCssClass' => 'active',
					'hiddenPageCssClass' => '',
					'previousPageCssClass' => 'prev',
					'nextPageCssClass' => 'Next',
					'htmlOptions' => array('class' => ''),
					'maxButtonCount' => 8,
					'header' => '',
					'footer' => '',
					'internalPageCssClass' => '',
				),
 				'columns'=>array(
 					array(
 						'name'=>'prop_id',
 						'type'=>'raw',
 						'value'=>'$data->prop_id',
 						'filter'=>'',
 					),
			       array(
			              'name'=>'name',
			              'type'=>'raw',
			              'value'=>'$data->name',
					      'filter'=>'',
			       		  'sortable' => false,
			         ),
			       array(
			              'name'=>'en_name',
			              'type'=>'raw',
			              'value'=>'$data->en_name',
			       		  'filter'=>'',
			       	      'sortable' => false,
			         ),
 					array(
 						'name'=>'cat_id',
 						'type'=>'html',
 						'value'=>array($this,'transPropsCat'),
 						'filter'=> $this->getPropsCat(2),
 						'sortable' => false,
 					),
			       array(
			              'name'=>'pipiegg',
			              'type'=>'raw',
			              'value'=>'$data->pipiegg',
			       		  'filter'=>'',
			       	      'sortable' => false,
			         ),
			       array(
			              'name'=>'charm',
			              'type'=>'raw',
			              'value'=>'$data->charm',
			       		  'filter'=>'',
			       	      'sortable' => false,
			         ),
			        array(
			              'name'=>'rank',
			              'type'=>'html',
			              'value'=>array($this,'transRank'),
			        	'filter' =>$this->getAllUserRank(2),
			        	'sortable' => false,
			         ),
			        array(
			              'name'=>'dedication',
			              'type'=>'html',
			              'value'=>'$data->dedication',
			        	  'filter'=>'',
			        	 'sortable' => false,
			         ),
			        array(
			              'name'=>'sort',
			              'type'=>'html',
			              'value'=>'$data->sort',
			        	  'filter'=>'',
			        	 'sortable' => false,
			         ),
 					array(
 						'name'=>'create_time',
 						'type'=>'html',
 						'value'=>'date("Y-m-d H:i:s","$data->create_time")',
 						'filter'=>'',
 						'sortable' => false,
 					),
			  		array(
			   			'class'=>'CButtonColumn',
			  			'template' => '{_update}{_delete}',
			  			'buttons' => array(
			  				'_update'=>array(
			  					'label'=>'<i class="icon-edit"></i>',
			  					'url' =>'',
			  					'options'=>array('class'=>'btn','title'=>'修改'),
			  				),
			  				'_delete'=>array(
			  					'label'=>'<i class="icon-remove"></i>',
			  					'url' =>'',
			  					'options'=>array('class'=>'btn','title'=>'删除'),
			  				),
			  			),
			  		),
 				),
			)); 
			?>
		</div>
	</div><!--/span-->
</div>

<!-- 添加道具分类属性浮层 -->
<div class="modal hide fade span6" id="props_list_manage" style="left:20%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>道具管理</h3>
	</div>
	<div class="modal-body" id="props_list_manage_body">
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//新增道具
	$(".box-icon .icon-plus-sign").click(function(e){
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('props/addProps');?>",
			dataType:'html',
			success:function(msg){
				if(msg){
					$('#props_list_manage_body').html(msg);
				}else{
					$('#props_list_manage_body').html('信息获取失败');
				}
				e.preventDefault();
				$('#props_list_manage').modal('show');
			}
		});
	});
	//修改道具
	$(".box-content .icon-edit").click(function(e){
		var prop_id = $(this).parents('tr').children().first().html();
		if(prop_id){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('props/addProps');?>",
				dataType:'html',
				data:{'prop_id':prop_id},
				success:function(msg){
					if(msg){
						$('#props_list_manage_body').html(msg);
					}else{
						$('#props_list_manage_body').html('信息获取失败');
					}
					e.preventDefault();
					$('#props_list_manage').modal('show');
				}
			});
		}
	});
	//删除道具
	$(".box-content .icon-remove").click(function(e){
		var prop_id = $(this).parents('tr').children().first().html();
		var obj = this;
		if(prop_id){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('props/addProps');?>",
				dataType:'html',
				data:{'op':'delProps','prop_id':prop_id},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						$('#props_list_manage_body').html(msg);
						e.preventDefault();
						$('#props_list_manage').modal('show');
					}
				}
			});
		}
	});

})
</script>
