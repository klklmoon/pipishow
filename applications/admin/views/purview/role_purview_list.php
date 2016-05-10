<?php
$this->breadcrumbs=array(
		'角色权限关联',
	);
?>
<div class="row-fluid">
	<div class="box span12">
		<div class="box-header well" style="cursor: auto;" data-original-title>
			<h2> <i class="icon-user"></i> 角色权限关联列表 </h2>
			<h2 style="float:right;"><a href="<?php echo $this->createUrl('purview/roleAdd');?>"><span class="label label-info" style="font-size:13px;">新增角色权限</span></a></h2>
		</div>
		<!-- <div class="center" id="loading">Loading...<div class="center"></div></div> -->
		
		<?php $this->widget('zii.widgets.grid.CGridView', array(
 				'id'=>'roleList_grid',
				'dataProvider'=>$dataProvider->rolesPurviewSearch($data),  //数据结果集
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
      				//'relation_id',
 					/* array(
 						'name'=>'purview_id',
 						'type'=>'html',
 						'value'=>'$data->purview_id',
 					), */
 					array(
 						'name'=>'role_name',
 						'type'=>'html',
 						'value'=>'$data->role_name',
 					),
			       array(
			              'name'=>'purview_name',
			              'type'=>'raw',
			              'value'=>'$data->purview_name',
					       	'filterHtmlOptions' => array(
					       		'class' => 'center',
					       	),
			       			//'headerHtmlOptions'=>array('class'=>''),
			       			//'filter'=>'<input type="text" name="PurviewItemsForm[purview_name]" style="width:50px;">',
			         ),
			       array(
			              'name'=>'group',
			              'type'=>'raw',
			              'value'=>'$data->group',
			       		  'filter'=>$this->getAllGroups(),
			         ),
			        array(
			              'name'=>'is_tree_display',
			              'type'=>'html',
			              'value'=>'$data->is_tree_display',
			        	  'filter' => array(0=>'隐藏',1=>'显示'),
			         ),
			       /*  array(
			              'name'=>'role_id',
			              'type'=>'html',
			              'value'=>'$data->role_id',
			         ), */
			       
			        array(
			              'name'=>'is_use',
			              'type'=>'html',
			              'value'=>'$data->is_use',
			        	  'filter' => array(0=>'禁用',1=>'启用'),
			         ),
 					array(
 						'name'=>'sub_id',
 						'type'=>'html',
 						'value'=>'$data->sub_id',
 					),
			  		
 				),
			)); 
			?>
		
	</div>
</div>
<!--/span-->
</div>
<!--/row-->