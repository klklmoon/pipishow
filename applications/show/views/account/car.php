<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
     
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/bag');?>">礼物背包</a></li>
            <li><a href="<?php echo $this->createUrl('account/props');?>">道具</a></li>
            <li class="menuvisted"><a href="<?php echo $this->createUrl('account/car');?>">座驾</a></li>
            <li><a href="<?php echo $this->createUrl('account/moon');?>">月卡</a></li>
            <li><a href="<?php echo $this->createUrl('account/vip');?>">vip</a></li>
            <!--<li><a href="index.php?r=account/guard">家族守护</a></li>-->
            <li><a href="<?php echo $this->createUrl('account/number');?>">靓号</a></li>
        </ul><!-- .main-menu -->
		
		<div id="MainCon">
			<div class="cooper-list">
			座驾，是您特显尊贵身份的象征。<a  target="_blank" href="<?php $this->getTargetHref($this->createUrl('shop/car'));?>" class="undo">购买新座驾</a>
			<ul class="car-list mt20 clearfix">
			<?php 
// 				if($getCar){
// 					foreach($getCar as $k=>$v){
// 						echo '<li>
// 		                	  <div class="carlt-mid"> <img src="'.$account_imgurl.'props/'.$v['image'].'" alt="'.$v['name'].'" />
// 		                	  <p>等级达到'.$ranks[$v['rank']]['name'].'以上免费领取</p>
// 		                	  <span class="soldpic">赠品</span> </div>
// 		                	  <div class="carlt-foot clearfix"><span>期限：永久</span> 
// 							  <button onclick="account.rank_get_car('.$v['prop_id'].')" value="点击免费领取">点击免费领取</button></div>
// 		                    </li>';
// 					}
// 				}
				if($bagInfo) {
					foreach($bagInfo as $k => $v) {
						$props = $propsInfo[$v['prop_id']];
						echo '<li>
						  <div class="carlt-mid"> 
							  <img src="'.$account_imgurl.$props['image'].'" alt="'.$props['name'].'" />
						';
						if($props['rank']){
							//print_r($ranks[$props['rank']]);
						}
						echo '<p>限购级别：', ($props['rank'] > 0 ? $ranks[$props['rank']]['name'].'以上</p>' : '无限制</p>');
						if($props['attribute']['car_is_limit']['value']>0){
							echo '<span class="soldpic">'.(($props['attribute']['car_is_limit']['value']==1) ? '限量' : '限购').$props['attribute']['car_limit']['value'].'</span>';
						}
						echo '</div>
						  <div class="carlt-foot clearfix">
							  <span>期限：'.$v['time_desc'].'</span> ';
						if($propUsed['car']==$v['prop_id']){	  
							echo '<img src="'.$this->pipiFrontPath.'/fontimg/account/use.jpg" class="fright" />
							  <button value="停用" onclick="account.change_car(0)">停用</button>';
						}else{
							echo '<img src="'.$this->pipiFrontPath.'/fontimg/account/cheku.jpg" class="fright" />
							<button value="启用" onclick="account.change_car(\''.$v['prop_id'].'\')">启用</button>';
						}
						echo '</div>
						</li>';
					}
				}
			?>
             </ul>  
			</div>
		</div>
		
	</div><!--#MainCon-->
</div><!-- .main -->        
</div><!-- .w1000 -->


