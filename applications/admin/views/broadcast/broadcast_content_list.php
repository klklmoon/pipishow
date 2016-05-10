<?php
$this->breadcrumbs = array('运营工具','全站广播');
?>
<div class="row-fluid sortable ui-sortable">
  	<div class="box span12">
		<div class="box-content">
			<ul class="nav nav-tabs" id="myTab">
				<li>
					<a href="#clist">广播内容 </a>
				</li>
				<li>
					<a href="#setup">设置 </a>
				</li>
				<li>
					<a href="#dlist">禁播用户 </a>
				</li>
			</ul>
			 
			<div id="myTabContent" class="tab-content" style="overflow-x:hidden;overflow-y:hidden;">
				<div class="tab-pane" id="clist"> <?php echo $data;?></div>
				<div class="tab-pane" id="setup"> </div>
				<div class="tab-pane" id="dlist"> </div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

$(function(){
	var URL = "<?php echo $this->createUrl('Broadcast/siteBroadcast');?>";
	var ALLTABS = ['dlist','clist','setup'];
	var isClick = true;
	$('#myTab > li').live('mouseover',function(){
		if($(this).attr('class')  == 'active'){
			isClick = false;
		}else{
			isClick = true;
		}
	});

	$('#myTab > li').live('mouseout',function(){
		if($(this).attr('class')  == 'active'){
			isClick = false;
		}else{
			isClick = true;
		}
	});
	$('#myTab > li').live('click',function(){
		var _ID = $(this).children('a').attr('href');
			_ID = _ID.substring(1);
		if(_ID && isClick){
			$('#myTabContent #'+_ID).html('<div id="loading" class="center">努力加载中...<div class="center"></div></div>');
			$.ajax({
				url:URL+'&tab='+_ID,
				type:'post',
				dataType:'html',
				success:function(data){
					var arrLen = ALLTABS.length;
					for(var i=0;i<arrLen;i++){
						var _v = arrLen[i];
						if(_v != _ID){
							$('#myTabContent #'+_v).html('');
						}
					}
					$('#myTabContent #'+_ID).html(data);
				}
			});
		}
	});
});
</script>