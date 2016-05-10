<?php
$this->breadcrumbs = array('道具分类属性管理');
?>
<div class="row-fluid sortable">		
	<div class="box span12">
		<?php if(!$this->isAjax){?>
		<div class="box-header well" data-original-title>
			<h2><i class="icon-user"></i> 道具分类属性管理</h2>
			<div class="box-icon">
				<a href="#" class="btn btn-setting btn-round" title="添加道具分类属性"><i class="icon-plus-sign"></i></a>
				<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
			</div>
		</div>
		<?php }?>
		<div class="box-content">
			<?php 
			$cloumns = array(
				array( 'name'=>'attr_id', 'type'=>'raw', 'value'=>'$data->attr_id', 'filter'=>'', ),
				array( 'name'=>'attr_name', 'type'=>'raw', 'value'=>'$data->attr_name', 'filter'=>'', 'sortable' => false, ),
				array( 'name'=>'attr_enname', 'type'=>'raw', 'value'=>'$data->attr_enname', 'filter'=>'', 'sortable' => false,),
				array( 'name'=>'cat_id', 'type'=>'html', 'value'=>array($this,'transPropsCat'), 'filter'=> $this->getPropsCat(2), 'sortable' => false,),
				array( 'name'=>'is_display', 'type'=>'html', 'value'=>array($this,'transStatus'), 'filter'=> array(0=>'隐藏',1=>'显示'), 'sortable' => false, ),
				array( 'name'=>'attr_value', 'type'=>'html', 'value'=>'$data->attr_value', 'filter' =>'', 'sortable' => false, 'htmlOptions'=>array('style'=>'width:140px;') ),
				array( 'name'=>'attr_type', 'type'=>'html', 'value'=>array($this,'transCatAttrTypes'), 'filter'=>$this->getCatAttrTypes(), 'sortable' => false, ),
				array( 'name'=>'is_multi', 'type'=>'html', 'value'=>array($this,'transIsMulti'), 'filter'=> '', 'sortable' => false, ),
				array( 'name'=>'create_time', 'type'=>'html', 'value'=>'date("Y-m-d","$data->create_time")', 'filter'=>'', 'sortable' => false, )
			);
			
			if (!$this->isAjax){
				$botton = array(
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
				);
				array_push($cloumns, $botton);
			}
			
			$pager = array(
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
				);
			
			$common = array(
 				'id'=>'roleList_grid',
				'dataProvider'=>$dataProvider->searchPropsCatAttr($condition),
				'filter'=>$model,
				'tagName' => 'div',
				'summaryText' => '',
				'summaryCssClass' => '',
				'ajaxUpdate' => false,
				'itemsCssClass' => 'table table-striped table-bordered bootstrap-datatable',
				'htmlOptions' => array('class'=>'box-content'),
				'pagerCssClass' => 'pagination pagination-centered',
				'pager' => $pager,
 				'columns'=> $cloumns,
			);
			
			if($this->isAjax){
				unset($common['filter']);
			}
			
			$this->widget('zii.widgets.grid.CGridView',$common );
			?>
		</div>
	</div><!--/span-->
</div>

<!-- 添加道具分类属性浮层 -->
<div class="modal hide fade span6" id="props_cat_attr" style="left:20%;">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3>道具分类属性管理</h3>
	</div>
	<div class="modal-body" id="props_cat_attr_body">
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//新增道具分类属性
	$(".box-icon .icon-plus-sign").click(function(e){
		$.ajax({
			type:'post',
			url:"<?php echo $this->createUrl('props/addcatattr');?>",
			dataType:'html',
			success:function(msg){
				if(msg){
					$('#props_cat_attr_body').html(msg);
				}else{
					$('#props_cat_attr_body').html('信息获取失败');
				}
				e.preventDefault();
				$('#props_cat_attr').modal('show');
			}
		});
	});
	//修改道具分类属性
	$(".box-content .icon-edit").click(function(e){
		var attr_id = $(this).parents('tr').children().first().html();
		if(attr_id){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('props/addcatattr');?>",
				dataType:'html',
				data:{'attr_id':attr_id},
				success:function(msg){
					if(msg){
						$('#props_cat_attr_body').html(msg);
					}else{
						$('#props_cat_attr_body').html('信息获取失败');
					}
					e.preventDefault();
					$('#props_cat_attr').modal('show');
				}
			});
		}
	});
	//删除道具分类属性
	$(".box-content .icon-remove").click(function(e){
		var attr_id = $(this).parents('tr').children().first().html();
		var obj = this;
		if(attr_id){
			$.ajax({
				type:'post',
				url:"<?php echo $this->createUrl('props/addcatattr');?>",
				dataType:'html',
				data:{'op':'delCatAttr','attr_id':attr_id},
				success:function(msg){
					if(msg == 1){
						$(obj).parents('tr').detach();
					}else{
						$('#props_cat_attr_body').html(msg);
						e.preventDefault();
						$('#props_cat_attr').modal('show');
					}
				}
			});
		}
	});

})
</script>
