<ul class="rank">
<?php 
foreach($rank as $key=>$_rank):
$key++;
if($key>10){
	break;
}
?>
     <li class="clearfix">
            <em class="fleft order"><?php echo $key?></em>
              <a class="rank-pic fleft" href="javascript:void(0);" title="<?php echo $_rank['nickname']?>"><img width="38px" height="40px" src="<?php echo $_rank['avatar']?>"></a>
              <p class="richname fleft"><a class="ellipsis pink" href="javascript:void(0);" title="<?php echo $_rank['nickname']?>"><?php echo $_rank['nickname']?></a></p>
              <em class="fright mt10 lvlr lvlr-<?php echo $_rank['rank']?>"></em>
     </li> 
<?php endforeach;?>               
 </ul>