<?php
$this->breadcrumbs=array(
		'系统角色管理',
	);
?>
<div class="row-fluid">
	<div class="box span12">
		<div class="box-header well" style="cursor: auto;" data-original-title>
			<h2> <i class="icon-user"></i> 系统角色管理 </h2>
			<div class="box-icon">
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
				<a href="#" class="btn btn-round" title="新增角色"><i class="icon-plus-sign"></i></a>
			</div>
		</div>
		
		<?php $this->widget('zii.widgets.grid.CGridView', array(
 				'id'=>'roleList_grid',
				'dataProvider'=>$dataProvider->roleSearch($data),  //数据结果集
				'filter'=>$model,
				'tagName' => 'div',
				'summaryText' => '',
				'summaryCssClass' => '',
				'itemsCssClass' => 'table table-striped table-bordered bootstrap-datatable',
				'htmlOptions' => array('class'=>'box-content'),
				'pagerCssClass' => 'pagination pagination-centered',
				//'cssFile' => $this->themeCssFile,
				//'baseScriptUrl' => $this->cssAssetsPath,
				//'filterCssClass' => "",
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
 						'name'=>'role_id',
 						'type'=>'raw',
 						'value'=>'$data->role_id',
 						'filter'=>'',
 					),
			       array(
			              'name'=>'role_name',
			              'type'=>'raw',
			              'value'=>'$data->role_name',
			       		  'filter' => '',
			         ),
			       array(
			              'name'=>'role_type',
			              'type'=>'raw',
			              'value'=>array($this,'transRoleType'),
			       		  'filter'=>$this->purSer->getPurviewRange(),
			         ),
 					array(
 						'name'=>'is_use',
 						'type'=>'raw',
 						'value'=>array($this,'transStatus'),
 						'filter'=>array(0=>'已删除',1=>'可用'),
 					),
			  		array(
			  			'header'=>'操作',
			  			'headerHtmlOptions'=>array('width'=>'140'),
			   			'class'=>'CButtonColumn',
			  			'template' => '{_update} {_delete}',
			  			'buttons' => array(
			  				'_update' => array(
			  						'label' => '<span class="_roleUpdate"><i class="icon-edit icon-white"></i>修改</span>',
			  						//'url' => 'Yii::app()->createUrl("purview/roleadd",array("role_id"=>$data->primaryKey))',
			  						'options' => array(
			  								'class' =>'btn btn-info',
			  								'title' => '修改',
			  							),
			  					),
			  				'_delete' => array(
				  					'label' => '<span class="_roleDelete"><i class="icon-edit icon-white"></i>删除</span>',
				  					//'url' => 'Yii::app()->createUrl("purview/roleadd",array("role_id"=>$data->primaryKey))',
				  					'options' => array(
				  								'class' =>'btn btn-danger',
				  								'title' => '删除',
			  								),
			  					),	
			  			 ),
			  			//'deleteButtonLabel' => '删除',
			  			//'deleteButtonOptions' => array('class' => 'btn btn-info'),
			  			//'deleteButtonUrl' => 'Yii::app()->controller->createUrl("roleAdd",array("role_id"=>$data->primaryKey,"op"=>"roleDel"))',
			  			//'updateButtonLabel' => '修改',
			  			//'updateButtonOptions' => array('class' => 'btn btn-info'),
			  			//'updateButtonUrl' => 'Yii::app()->controller->createUrl("roleAdd",array("role_id"=>$data->primaryKey,"op"=>"roleUpdate"))',
			  			'htmlOptions' => array('class' => 'center'),
			  		),
 				),
			)); 
			?>
		
	</div>
</div>

<!-- 角色权限浮层 -->
<div class="modal hide fade span10" id="prview_role_items" style="left:5%">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>系统角色操作</h3>
	</div>
	<div class="modal-body" id="prview_role_items_body">
	
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//编辑角色权限 停用
	$('.box-content ._roleUpdate').live('click',function(e){
		var roleId = $(this).parents('tr').children().first().html();
		var obj = this;
		if(roleId){
			$.ajax({
				  type: "POST",
				  url: "<?php echo $this->createUrl('purview/roleadd');?>",
				  dataType: "html",
				  data:{'role_id':roleId},
				  success:function(msg){
					  if(msg){
						  $('#prview_role_items_body').html(msg);
					  }
					  e.preventDefault();
					  $('#prview_role_items').modal('show');
				}
			});
		}
	});
	//删除
	$('.box-content ._roleDelete').live('click',function(e){
		var roleId = $(this).parents('tr').children().first().html();
		var obj = $(this);
		if(roleId){
			$.ajax({
				  type: "POST",
				  url: "<?php echo $this->createUrl('purview/roleadd');?>",
				  dataType: "html",
				  data:{'role_id':roleId,'op':'roleDel'},
				  success:function(msg){
					  if(msg  == 1){
						  obj.parents("tr").detach();
					  }else{
						  $('#prview_role_items_body').html(msg);
						  e.preventDefault();
						  $('#prview_role_items').modal('show');
					  }
					  
				}
			});
		}
	});
	
	//新增系统角色
	$(".icon-plus-sign").click(function(e){
		$.ajax({
			url:"<?php echo $this->createUrl('purview/roleadd');?>",
			type:'post',
			dataType:'html',
			success:function(msg){
				if(msg){
					$('#prview_role_items_body').html(msg);
				}else{
					$('#prview_role_items_body').html('加载失败');
				}
				e.preventDefault();
				$('#prview_role_items').modal('show');
			}
		});
	});
	
})
</script>