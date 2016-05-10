<div class="w1000 mt20">
	<div class="wlimit boxshadow ovhide p15">
	<div class="ranklist clearfix">	
		<div class="rankboard fleft">
			<h4>今日富豪榜</h4>			
			<table>
				<colgroup class="col1"></colgroup>
				<colgroup class="col2"></colgroup>				
<?php 
foreach($rank['today'] as $key=>$_rank):
$_rank['rank'] = $_rank['rank'] == 0 ? 1 : $_rank['rank'];
?>				
				<tr>					
					<td><span class="col1"><a href="#3"><?php echo $_rank['nickname']?></a></span></td>
					<td><?php ?></td>
					<td><em class="lvlr lvlr-<?php echo $_rank['rank']?>"></em></td>
				</tr>
<?php endforeach;?> 				
             
			</table>
		</div>

		<div class="rankboard fleft">
			<h4>本周富豪榜</h4>			
			<table>
				<colgroup class="col1"></colgroup>
				<colgroup class="col2"></colgroup>
				<colgroup class="col3"></colgroup>
<?php 
foreach($rank['week'] as $key=>$_rank):
$_rank['rank'] = $_rank['rank'] == 0 ? 1 : $_rank['rank'];
?>				
				<tr>					
					<td><span class="col1"><a href="#3"><?php echo $_rank['nickname']?></a></span></td>
					<td><?php ?></td>
					<td><em class="lvlr lvlr-<?php echo $_rank['rank']?>"></em></td>
				</tr>
<?php endforeach;?> 
	
			</table>
		</div><!-- .rankboard -->
		<div class="rankboard fleft">
			<h4>本月富豪榜</h4>
			<table>
				<colgroup class="col1"></colgroup>
				<colgroup class="col2"></colgroup>
				<colgroup class="col3"></colgroup>		
<?php 
foreach($rank['month'] as $key=>$_rank):
$_rank['rank'] = $_rank['rank'] == 0 ? 1 : $_rank['rank'];
?>				
				<tr>					
					<td><span class="col1"><a href="#3"><?php echo $_rank['nickname']?></a></span></td>
					<td><?php ?></td>
					<td><em class="lvlr lvlr-<?php echo $_rank['rank']?>"></em></td>
				</tr>
<?php endforeach;?> 						
			</table>
		</div><!-- .rankboard -->
		<div class="rankboard fleft">
			<h4>超级富豪榜</h4>
			<table>
				<colgroup class="col1"></colgroup>
				<colgroup class="col2"></colgroup>
				<colgroup class="col3"></colgroup>				
	<?php 
foreach($rank['super'] as $key=>$_rank):
$_rank['rank'] = $_rank['rank'] == 0 ? 1 : $_rank['rank'];
?>				
				<tr>					
					<td><span class="col1"><a href="#3"><?php echo $_rank['nickname']?></a></span></td>
					<td><?php ?></td>
					<td><em class="lvlr lvlr-<?php echo $_rank['rank']?>"></em></td>
				</tr>
<?php endforeach;?> 
			</table>
		</div>

	</div>
	</div>
</div>