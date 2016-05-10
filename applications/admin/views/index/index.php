<?php 
	$this->breadcrumbs = array('用户欢迎页');
?>

<?php 
//print_r($this->menuTree);exit;
$rawStartLabel = '<div class="row-fluid sortable ui-sortable">';
$rawEndLabel = '</div>';
$node = 3;

if(isset($this->menuTree)){ 
	foreach ($this->menuTree as $key => $group) {
		if($node%3==0){
			echo $rawStartLabel;
		}
?>
	<div class="box span4">
		<div class="box-header well" data-original-title="">
			<h2><?php echo $key;?></h2>
		</div>
		<div class="box-content">
			<ul class="dashboard-list" style ="overflow-y: auto;height:200px;">
				<?php if (is_array($group)){?>
					<?php foreach ($group as $op=>$item){?>
						<li>
							<span>操作项:</span><a href="<?php echo $this->createUrl($item);?>" target="_blank"><?php echo $op;?></a>
						</li>
					<?php }?>
				<?php }?>
			</ul>
		</div>
	</div>
<?php 
		if($node%3==1){
			echo $rawEndLabel;
		}
		
		if ($node == 0){
			$node = 3;
		}
		--$node;
	}
?>
<?php }?>
