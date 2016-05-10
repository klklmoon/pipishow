<div class="w1000 mt20">
	<div class="boxshadow p15 clearfix">

		<div class="main-2 fleft ovhide">

<div class="fleft w470">
		<ul class="tab clearfix w470">
			<li><a class="curr" href="javascript:void(0)">本周点唱榜</a></li>
		</ul>
		<div class="hm-xsboard tabcon-bd">
			<table class="xsboard">
		<colgroup class="col-w35"></colgroup>
		<colgroup class="col-w42"></colgroup>
		<colgroup class="col-w140"></colgroup>
		<colgroup class="col-w100"></colgroup>
		<colgroup class="col-w140"></colgroup>
	<tbody>
<?php 
	foreach($songs['week'] as $key=>$_song):
	$key++;
	$no = $key <= 3 ? 'no'.$key : '';
	$archivesHref = 'index.php?r=archives/index/uid/'.$_song['d_uid'];
?>
		<tr>
			<td>
				<div class="list-num no<?php echo $no?>"><?php echo $key?></div>
			</td>
			<td><a href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><img src="<?php echo  $_song['d_avatar']?>" width="48" height="48" alt="<?php echo $_song['d_nickname']?>"></a></td>
			<td>
				<div class="col3-zhubo">
					<p>
						<a href="javascript:void(0)"><?php echo $_song['d_nickname']?></a>
					</p>
					<em class="lvlo lvlo-<?php echo $_song['d_rank']?>"></em>
				</div>
			</td>
			<td>
			</td>
			<td>
				<p>
					<em class="xs-num"> +<?php echo $_song['num']?></em>
				</p>
				<p>点唱数</p>
			</td>
		</tr>	
<?php endforeach;?>		
		</tbody>
</table>
	</div>                             
</div>

<div class="fleft w470 ml20">
		<ul class="tab clearfix w470">
			<li><a class="curr" href="javascript:void(0)">本月点唱榜</a></li>
		</ul>
		<div class="hm-xsboard tabcon-bd">
			<table class="xsboard">
			<colgroup class="col-w35"></colgroup>
			<colgroup class="col-w42"></colgroup>
			<colgroup class="col-w140"></colgroup>
			<colgroup class="col-w100"></colgroup>
			<colgroup class="col-w140"></colgroup>
			<tbody>
<?php 
	foreach($songs['month'] as $key=>$_song):
	$key++;
	$no = $key <= 3 ? 'no'.$key : '';
	$archivesHref = 'index.php?r=archives/index/uid/'.$_song['d_uid'];
?>
		<tr>
			<td>
				<div class="list-num no<?php echo $no?>"><?php echo $key?></div>
			</td>
			<td><a href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><img src="<?php echo $_song['d_avatar']?>" width="48" height="48" alt="<?php echo $_song['d_nickname']?>"></a></td>
			<td>
				<div class="col3-zhubo">
					<p>
						<a href="javascript:void(0)"><?php echo $_song['d_nickname']?></a>
					</p>
					<em class="lvlo lvlo-<?php echo $_song['d_rank']?>"></em>
				</div>
			</td>
			<td>
			</td>
			<td>
				<p>
					<em class="xs-num"> +<?php echo $_song['num']?></em>
				</p>
				<p>点唱数</p>
			</td>
		</tr>	
<?php endforeach;?>		
	</tbody>
</table>
		</div>
		</div>		
		</div>
	</div>
</div>