<div class="kong">
  <div class="w1230 relative">
    <div class="intro">
     活动介绍：本次活动分“女神排位赛”与“女神进击战”两个阶段进行。<br/>
     活动时间：<?php echo date('Y年n月d日', strtotime($top_start));?>——<?php echo date('Y年n月d日', strtotime($battle_2_end));?><br/><br/>
     活动专属礼物：女神，定价10皮蛋 ∕ 个
    </div>
    <div class="giftimg"><img src="/statics/fontimg/activities/battle/nvshen.png" /></div>
  </div>
</div><!--.kong-->

<?php
$background = '';
if($battle == 8) $background = 'background:url(/statics/fontimg/activities/battle/main-bj-2.jpg) no-repeat; height:2031px;';
elseif($battle == 4 || $battle == 2) $background = 'background:url(/statics/fontimg/activities/battle/main-bj-3.jpg) no-repeat; height:1650px;';
?>
<div class="w1230 main" style="<?php echo $background;?>">
  <a id="t"></a>
  <div class="w1010 clearfix">
  
     <div class="round-bj">
       <a href="<?php $this->getTargetHref($this->createUrl('activities/battleTop'));?>#t"><div class="round-1 fleft"><span>活动时间：<?php echo date('n.d H:i', strtotime($top_start));?>---<?php echo date('n.d H:i', strtotime($top_end));?></span></div></a>
       <a href="<?php $this->getTargetHref($this->createUrl('activities/battle'));?>#t"><div class="round-3 fleft ml10"><span> 活动时间：<?php echo date('n.d H:i', strtotime($battle_16_start));?>---<?php echo date('n.d H:i', strtotime($battle_2_end));?></span></div></a>
     </div><!--.round-bj-->
     
    <div class="w1000 mt20 rule-tab clearfix">
        <h1>活动规则：</h1>
        <table width="1000" border="1">
          <tr style=" font-size:28px; font-weight:bold;">
            <td class="w150">&nbsp;</td>
            <td class="w210">16进8</td>
            <td class="w210">8进4</td>
            <td class="w210">4进2</td>
            <td class="w210">巅峰对决</td>
          </tr>
          <tr>
            <td>参与人数</td>
            <td>16</td>
            <td>8</td>
            <td>4</td>
            <td>2</td>
          </tr>
          <tr>
            <td>晋级人数</td>
            <td>8</td>
            <td>4</td>
            <td>2</td>
            <td>1</td>
          </tr>
          <tr>
            <td>主播奖励</td>
            <td>淘汰的每名主播奖励<br />
              20W魅力值<br />
              晋级者继续前进争夺<br />
              总冠军</td>
            <td>淘汰的每名主播奖励<br />
              300元<br />
              晋级者继续前进争夺<br />
              总冠军</td>
            <td>淘汰的每名主播奖励<br />
              500元<br />
              晋级者继续前进争夺<br />
              总冠军</td>
            <td>冠军主播奖励1000元<br />
              +魅力值50W<br />
              亚军主播奖励800元</td>
          </tr>
          <tr>
            <td>晋级主播的粉<br />
              丝前三名奖励</td>
            <td>晋级主播的粉<br />
              丝前三名奖励</td>
            <td>刷的贡献值×2倍</td>
            <td>刷的贡献值×3倍</td>
            <td>刷的贡献值×4倍</td>
          </tr>
        </table>
        <p>1. 用户给主播送礼，主播以收到的礼物总数进行两两PK,胜者晋级。<br/>2. 2V2分组：1&16,2&15,3&14,4&13,5&12,6&11,7&10,8&9</p>
        <h1 class="fleft">活动对象：</h1><p class="fleft">“女神站位赛”中排行榜上的前16位主播</p>
    </div><!--.rule-tab-->  
    
    <?php
    $param = array('pos' => 'top');
    if(!empty($now)) $param = array_merge($param, array('time' => $now));
    ?>
    <a id="top" ></a>
    <div class="promotion clearfix">
    	<a href="<?php echo $time > strtotime($battle_16_end) ? $this->getTargetHref($this->createUrl('activities/battle', array_merge($param,array('res' => '16'))), false, true) : ($battle <= 16 ? $this->getTargetHref($this->createUrl('activities/battle', $param), false, true) : 'javascript:void(0);');?>">
        <div class="promotion-bj fleft">
          <p><?php echo date('n月d日<br/>H:i', strtotime($battle_16_start));?>--<?php echo date('H:i', strtotime($battle_16_end));?></p>
        </div>
        </a>
        
        <a href="<?php echo $time > strtotime($battle_8_end) ? $this->getTargetHref($this->createUrl('activities/battle', array_merge($param,array('res' => '8'))), false, true) : ($battle <= 8 ? $this->getTargetHref($this->createUrl('activities/battle', $param), false, true) : 'javascript:void(0);');?>">
        <div class="promotion-bj-1<?php echo $time > strtotime($battle_8_start) ? '-1' : '';?> fleft">
          <p><?php echo date('n月d日<br/>H:i', strtotime($battle_8_start));?>--<?php echo date('H:i', strtotime($battle_8_end));?></p>
        </div>
        </a>
        
        <a href="<?php echo $time > strtotime($battle_4_end) ? $this->getTargetHref($this->createUrl('activities/battle', array_merge($param,array('res' => '4'))), false, true) : ($battle <= 4 ? $this->getTargetHref($this->createUrl('activities/battle', $param), false, true) : 'javascript:void(0);');?>">
        <div class="promotion-bj-2<?php echo $time > strtotime($battle_4_start) ? '-2' : '';?> fleft">
          <p><?php echo date('n月d日<br/>H:i', strtotime($battle_4_start));?>--<?php echo date('H:i', strtotime($battle_4_end));?></p>
        </div>
        </a>
        
        <a href="<?php echo $time > strtotime($battle_2_end) ? $this->getTargetHref($this->createUrl('activities/battle', array_merge($param,array('res' => '2'))), false, true) : ($battle <= 2 ? $this->getTargetHref($this->createUrl('activities/battle', $param), false, true) : 'javascript:void(0);');?>">
        <div class="promotion-bj-3<?php echo $time > strtotime($battle_2_start) ? '-3' : '';?> fleft">
          <p><?php echo date('n月d日<br/>H:i', strtotime($battle_2_start));?>--<?php echo date('H:i', strtotime($battle_2_end));?></p>
        </div>
        </a>
    </div><!--.promotion--> 
     
    <div class="titbj clearfix">“女神进击战”排名：两两PK，胜者晋级。前<?php echo $battle/2;?>名晋级。</div>
    
    <div class="refresh"><a href="<?php $this->getTargetHref($this->createUrl('activities/battle', array('pos' => 'top')));?>"><img src="/statics/fontimg/activities/battle/shuaxin-big.png" /></a></div>
    
    <?php
    	$count = count($top);
    	for($i=0; $i<$count/2; $i++){
    ?>
    <div class="pk <?php echo $i%2 == 0 ? 'fleft' : 'fright';?> mt40" style="<?php echo $battle == 2 ? 'margin-left:260px; _margin-left:130px;' : '';?>">
      <?php if($battle == 2){?>
      <div class="wing-left"><img src="/statics/fontimg/activities/battle/wing-left.png" /></div>
      <div class="wing-right"><img src="/statics/fontimg/activities/battle/wing-right.png" /></div>
      <?php }?>
      <div class="vs"><img src="/statics/fontimg/activities/battle/vs.png" /></div>
      <?php if($top[$i]['result']){?>
      <div class="ward"><img src="/statics/fontimg/activities/battle/up<?php echo $battle==2 ? '-1' : '';?>.png" /></div>
      <div class="up"><img src="/statics/fontimg/activities/battle/ward<?php echo $battle==8 ? '-1' : ($battle==4 ? '-2' : ($battle==2 ? '-3': ''));?>.png" /></div>
      <?php }elseif($top[$count-1-$i]['result']){?>
      <div class="up"><img src="/statics/fontimg/activities/battle/up<?php echo $battle==2 ? '-1' : '';?>.png" /></div>
      <div class="ward"><img src="/statics/fontimg/activities/battle/ward<?php echo $battle==8 ? '-1' : ($battle==4 ? '-2' : ($battle==2 ? '-3': ''));?>.png" /></div>
      <?php }?>
    
      <div class="pkbox fleft">
        <h3><?php echo $top[$i]['number'] > 0 ? '靓号：'.$top[$i]['number'] : '&nbsp;';?></h3>
         <img src="<?php echo $top[$i]['pic'];?>" />
         <p title="<?php echo $top[$i]['nk'];?>"><em class="lvlo lvlo-<?php echo $top[$i]['dk'];?>"></em> <?php echo mb_substr((empty($top[$i]['nk'])?"求昵称":$top[$i]['nk']),0,6,'UTF-8');?></p>
         <code><?php echo intval($top[$i]['num']);?></code>
         <table width="190" border="0">
         	<?php foreach($top[$i]['users'] as $user){?>
              <tr>
                <td><em class="lvlr lvlr-<?php echo $user['rk'];?>"></em></td>
                <td><span title="<?php echo $user['nk'];?>"><?php echo mb_substr((empty($user['nk'])?"求昵称":$user['nk']),0,5,'UTF-8')?></span></td>
                <td><span><?php echo $user['gift_num'];?></span></td>
              </tr>
            <?php }?>
         </table>
      </div>
      
      <div class="pkbox fright">
         <h3><?php echo $top[$count-1-$i]['number'] > 0 ? '靓号：'.$top[$count-1-$i]['number'] : '&nbsp;';?></h3>
         <img src="<?php echo $top[$count-1-$i]['pic'];?>" />
         <p title="<?php echo $top[$count-1-$i]['nk'];?>"><em class="lvlo lvlo-<?php echo $top[$count-1-$i]['dk'];?>"></em> <?php echo mb_substr((empty($top[$count-1-$i]['nk'])?"求昵称":$top[$count-1-$i]['nk']),0,6,'UTF-8');?></p>
         <code><?php echo intval($top[$count-1-$i]['num']);?></code>
         <table width="190" border="0">
              <?php foreach($top[$count-1-$i]['users'] as $user){?>
              <tr>
                <td><em class="lvlr lvlr-<?php echo $user['rk'];?>"></em></td>
                <td><span title="<?php echo $user['nk'];?>"><?php echo mb_substr((empty($user['nk'])?"求昵称":$user['nk']),0,5,'UTF-8')?></span></td>
                <td><span><?php echo $user['gift_num'];?></span></td>
              </tr>
            <?php }?>
         </table>
      </div>
    </div>
    <?php }?>
    
  </div><!--.w1010-->
</div><!--.w1230-->

<script type="text/javascript">
var position = '<?php echo $position;?>';
$(function(){

    if(position){
		window.location.href="#"+position;
    }
})
</script>