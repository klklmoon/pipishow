<?php
$this->breadcrumbs=array(
		'统计代码设置',
	);
?>
<div class="row-fluid sortable ui-sortable">
	<div class="box span6">
		<div class="box-header well" data-original-title>
			<h2><i class="icon-signal"></i>统计代码设置</h2>
		</div>
		<div class="box-content">
			<form action="" method="post" id="web_site_config">
			<table>
				<tr>
					<td>统计代码:</td>
					<td><textarea class="autogrow" name="content" style="width:450px"><?php echo isset($web_config['content']) ? $web_config['content'] : '';?></textarea></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<a class="btn btn-large btn-danger">确认</a>
						<a class="btn btn-large btn-success">取消</a>
					</td>
				</tr>
			</table>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('a.btn-danger').click(function(){
		$('#web_site_config').submit();
	});
</script>