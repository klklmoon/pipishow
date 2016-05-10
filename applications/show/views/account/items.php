<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
    
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">礼物背包</a></li>
			<?php
				/* if($cat_list) {
					foreach($cat_list as $k=>$v) {
						if($v['is_display']=='1'){
							echo '<li><a href="#">'.$v['name'].'</a></li>';
						}
					}
				} */
			?>
            <li><a href="#">道具</a></li>
            <li><a href="#">座驾</a></li>
            <li><a href="#">月卡</a></li>
            <li><a href="#">vip</a></li>
            <li><a href="#">家族守护</a></li>
            <li><a href="#">靓号</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
			<?php 
				if(count($account_bags)){
			?>
			<ul class="gift-list">
			<?php
				foreach($account_bags as $k=>$v) {
					echo '<li>
						<a href="#" class="gift-box">
							<img title="'.$v['info']['zh_name'].'" src="'.Yii::app()->params->images_server['url'].'/'.$v['info']['image'].'" />
							<span>'.$v['num'].'</span>
						</a>
						<a href="#" title="'.$v['info']['zh_name'].'" class="gif-title">'.$v['info']['zh_name'].'</a>
					</li>';
				}
			?>
            </ul>
			<?php
				}else{
			?>
				  您的背包中，还没有任何礼物道具，请到<a href="#" class="undo">商城</a>购买。
			<?php 
				}
			?>
           </div><!-- .cooper-list 礼物背包 -->
           
           <div class="cooper-list onhide">
              <table width="700" border="1" bordercolor="#DDDDDD">
			    
			<?php 
			if($account_props){
				echo '<tr bgcolor="#F5F5F5" class="biaot">
			  	<td width="200" height="40">名称</td>
			  	<td width="300" height="40">威力</td>
			  	<td width="100" height="40">数量</td>
			  	<td width="100" height="40">有效期</td>
			    </tr>';
				foreach($account_props as $k=>$v) {
					echo '<tr>
						<td height="42">'.$v['info'].'</td>
						<td height="42">给用户贴上普通趣味标签，显示15分钟后，自动消失</td>
						<td height="42">10</td>
						<td height="42">无限期</td>
						</tr>';
				}
			}else{
				echo '您的背包中，还没有任何礼物道具，请到<a href="#" class="undo">商城</a>购买。';
			}
			?>
			  </table>
           </div><!-- .cooper-list 道具 -->                         
            
           <div class="cooper-list onhide">
              <ul class="car-list mt20 clearfix">
					<?php 
						if($bagInfo) {
							foreach($bagInfo as $k => $v) {
								$props = $propsInfo[$v['prop_id']];
								echo '<li>
								  <div class="carlt-mid"> 
									  <img src="'.$account_imgurl.'/'.$props['image'].'" alt="'.$props['name'].'" />
								';
								if($props['rank']){
									//print_r($ranks[$props['rank']]);
								}
								echo '<p>限购级别：'.$ranks[$props['rank']]['name'].'以上</p>';
								if($props['attribute']['car_is_limit']['value']>0){
									echo '<span class="soldpic">'.(($props['attribute']['car_is_limit']['value']==1) ? '限量' : '限购').$props['attribute']['car_limit']['value'].'</span>';
								}
								echo '</div>
								  <div class="carlt-foot clearfix">
									  <span>期限：'.$v['time_desc'].'</span> ';
								if($propUsed['car']==$v['prop_id']){	  
									echo '<img src="'.$this->pipiFrontPath.'/fontimg/account/use.jpg" class="fright" />
									  <button value="停用">停用</button>';
								}else{
									echo '<img src="'.$this->pipiFrontPath.'/fontimg/account/cheku.jpg" class="fright" />
									<button value="启用">启用</button>';
								}
								echo '</div>
								</li>';
							}
						}
					?>
             </ul>    
           </div><!-- .cooper-list 座驾 --> 
           
           <div class="cooper-list onhide">
           <!--您还没有办理月卡，赶快去<a href="#" class="undo">商城</a>看看。-->
              <table class="open">
                    <tr class="colum">
                        <td>种类</td>
                        <td>威力</td>
                        <td>购买条件</td>
                        <td>价格</td>
                    </tr>
                    <tr>
                        <td class="kind">
                        	<img src="pic/mouth-big.png" /><br>
                            <strong>月卡</strong>
                        </td>
                        <td class="power">
                            <P>特殊礼物红玫瑰，每朵价值0.1皮蛋。<em>每天可免费领取3朵红玫瑰，累计30天共90朵。</em></P>
                            <P>您可以在首页和直播间的"每日签到"领取。也可以在"我的物品-月卡"里一次性全部提取。</P>
                            <P>办月卡后将获得"月卡"标识，彰显您对主播的支持和鼓励。</P>
                        </td>
                        <td class="term">
                            <p>无限制</p>
                        </td>
                        <td class="price">
                            <p>已领取：<em>90/90朵玫瑰</em></p>
                            <p>您的月卡超级礼物配额已经用完，19天以后可以继续办理</p>
                            <a class="buy-btn" href="javascript:void(0);" title="每日签到">每日签到</a><span>（按天数领取配额）</span><br/><br/>
                            <a class="buy-btn" href="javascript:void(0);" title="领取全部">领取全部</a><span>（全部提取到背包）</span>
                        </td>
                    </tr>
                </table>
           </div><!-- .cooper-list 月卡 --> 
           
           <div class="cooper-list onhide">
              <table class="open">
                    <tr class="colum">
                        <td width="150">名称</td>
                        <td width="280">权利</td>
                        <td width="180">购买限制</td>
                        <td>价格</td>
                    </tr>
                    <tr>
                        <td>
                            <em class="prot"><img src="images/VIP_02.jpg"></em>
                        </td>
                        <td class="power">
                            <p>显示紫色VIP标志</p>
                            <p>可以隐身进入直播间</p>
                            <p>防止被房管、低等级主播和家族守护禁言</p>
                            <p>防止被房管、低等级主播和家族守护踢</p>
                            <p>排位在同级用户中优先</p>
                            <p>每天免费使用25次贴条</p>
                            <p>飞屏8折特惠</p>
                            <p>贵族VIP表情</p>
                        </td>
                        <td>
                            <em class="lvlr lvlr-14"></em>
                            <p>（富豪8）以上</p>
                        </td>
                        <td>永久</td>
                    </tr>
                </table>
           </div><!-- .cooper-list vip --> 
            
      </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->


