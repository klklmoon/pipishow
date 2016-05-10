<div class="fleft w470">
		<ul  id="Charm"  class="tab clearfix w470 fleft">
			<li><a class="curr" href="#3">今日魅力</a></li>
			 <!--
		 	 <li><a class="" href="#3">主播人气</a></li>
		 	 -->
            <li><a class="" href="#3">超级魅力</a></li>
		</ul>
		<div class="hm-xsboard tabcon-bd fleft ">
			<table class="xsboard">			
			<colgroup class="col-w35"></colgroup>
			<colgroup class="col-w42"></colgroup>
			<colgroup class="col-w140"></colgroup>
			<colgroup class="col-w100"></colgroup>
			<colgroup class="col-w140"></colgroup>
			<tbody>
<?php 
foreach($rank['today'] as $key=>$_rank):
$key++;
$no = $key <= 3 ? 'no'.$key : '';
$archivesHref = 'index.php?r=archives/index/uid/'.$_rank['d_uid'];
?>			
			<tr>
			<td>
				<div class="list-num <?php echo $no?>">
				<?php echo $key?>
				</div>
			</td>
			<td><a  href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><img class="xscover" src="<?php echo $_rank['d_avatar']?>" width=48 height=48 alt="<?php echo $_rank['d_nickname']?>" /></a></td>
			<td>
				<div class="col3-zhubo">
				<p><a class="name ellipsis" href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><?php echo $_rank['d_nickname']?></a></p>
				<em class="lvlo lvlo-<?php echo $_rank['d_rank']?>"></em>
				</div>
			</td>			
			<td>
				<p><em class="xs-num">+<?php echo $_rank['charm']?></em></p>
				<p>新增魅力</p>
			</td>			
			<td>
				<span class="fans-nm"><a href="#3"><?php echo $_rank['nickname']?></a></span> <em class="lvlr lvlr-<?php echo $_rank['rank']?>"></em>
				<p>今日护花达人</p>
			</td>
			</tr>	
<?php endforeach;?> 
		</tbody>
	</table>
</div>
        
        
<!--     
<div class="hm-xsboard tabcon-bd fleft disp">
			<table class="xsboard">			
			<colgroup class="col-w35"></colgroup>
			<colgroup class="col-w42"></colgroup>
			<colgroup class="col-w140"></colgroup>
			<colgroup class="col-w100"></colgroup>
			<colgroup class="col-w140"></colgroup>
			<tbody>
			<tr>
<?php 
if(isset($rank['dotey_gift_super'])):
foreach($rank['dotey_gift_super'] as $key=>$_rank):
$key++;
$no = $key <= 3 ? 'no'.$key : '';
$archivesHref = 'index.php?r=archives/index/uid/'.$_rank['d_uid'];
?>			
			<tr>
			<td>
				<div class="list-num <?php echo $no?>">
				<?php echo $key?>
				</div>
			</td>
			<td><a href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><img class="xscover" src="<?php echo $_rank['d_avatar']?>" width=48 height=48 alt="<?php echo $_rank['d_nickname']?>" /></a></td>
			<td>
				<div class="col3-zhubo">
				<p><a  class="name ellipsis"  href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><?php echo $_rank['d_nickname']?></a></p>
				<em class="lvlo lvlo-<?php echo $_rank['d_rank']?>"></em>
				</div>
			</td>			
			<td>
				<p><em class="xs-num">+<?php echo $_rank['num']?></em></p>
				<p>粉丝数</p>
			</td>			
			<td>
				<span class="fans-nm"><a href="#3"><?php echo $_rank['nickname']?></a></span> <em class="lvlr lvlr-<?php echo $_rank['rank']?>"></em>
				<p>护花大使</p>
			</td>
			</tr>	
<?php 
endforeach;
endif;
?> 
			</tbody>
			</table>
</div>
-->           
        
<div class="hm-xsboard tabcon-bd fleft disp">
			<table class="xsboard">			

			<colgroup class="col-w35"></colgroup>
			<colgroup class="col-w42"></colgroup>
			<colgroup class="col-w140"></colgroup>
			
			<colgroup class="col-w100"></colgroup>
			<colgroup class="col-w140"></colgroup>


			<tbody>
<?php 
foreach($rank['super'] as $key=>$_rank):
$key++;
$no = $key <= 3 ? 'no'.$key : '';
$archivesHref = 'index.php?r=archives/index/uid/'.$_rank['d_uid'];
?>			
			<tr>
			<td>
				<div class="list-num <?php echo $no?>">
				<?php echo $key?>
				</div>
			</td>
			<td><a href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><img class="xscover" src="<?php echo $_rank['d_avatar']?>" width=48 height=48 alt="<?php echo $_rank['d_nickname']?>" /></a></td>
			<td>
				<div class="col3-zhubo">
				<p><a  class="name ellipsis"  href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><?php echo $_rank['d_nickname']?></a></p>
				<em class="lvlo lvlo-<?php echo $_rank['d_rank']?>"></em>
				</div>
			</td>			
			<td>
				<p><em class="xs-num"></em></p>
				<p></p>
			</td>			
			<td>
				<span class="fans-nm"><a href="#3"><?php echo $_rank['nickname']?></a></span> <em class="lvlr lvlr-<?php echo $_rank['rank']?>"></em>
				<p>护花大使</p>
			</td>
			</tr>	
<?php endforeach;?> 
			</tbody>
			</table>
</div>
</div>
<div class="fleft w470 ml20">
		<ul class="tab clearfix w470">
			<li><a class="curr" href="#3">本周魅力</a></li>
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
foreach($rank['week'] as $key=>$_rank):
$key++;
$no = $key <= 3 ? 'no'.$key : '';
$archivesHref = 'index.php?r=archives/index/uid/'.$_rank['d_uid'];
?>			
			<tr>
			<td>
				<div class="list-num <?php echo $no?>">
				<?php echo $key?>
				</div>
			</td>
			<td><a  href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><img class="xscover" src="<?php echo $_rank['d_avatar']?>" width=48 height=48 alt="<?php echo $_rank['d_nickname']?>" /></a></td>
			<td>
				<div class="col3-zhubo">
				<p><a  class="name ellipsis"  href="<?php $this->getTargetHref($archivesHref)?>" target="<?php echo $this->target?>"><?php echo $_rank['d_nickname']?></a></p>
				<em class="lvlo lvlo-<?php echo $_rank['d_rank']?>"></em>
				</div>
			</td>			
			<td>
				<p><em class="xs-num">+<?php echo $_rank['charm']?></em></p>
				<p>新增魅力</p>
			</td>			
			<td>
				<span class="fans-nm"><a href="#3"><?php echo $_rank['nickname']?></a></span> <em class="lvlr lvlr-<?php echo $_rank['rank']?>"></em>
				<p>本周护花达人</p>
			</td>
			</tr>	
<?php endforeach;?>    
			</tbody>
			</table>
		</div>
</div>

