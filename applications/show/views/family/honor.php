				<?php foreach($honor as $h){?>
                	<?php if($h['type'] == 'create'){?>
                	<dl class="family-honor">
                        <dt><i class="banericon"></i><span><?php echo date('Y', $family['create_time']);?> <em><?php echo date('m', $family['create_time']);?></em>月<?php echo date('d', $family['create_time']);?>日</span></dt>
                        <dd>
                        	今天，在家族长<em class="pink"><?php echo $family_owner['nk'];?></em>(<?php echo $family_owner['uid'];?>)，初创成员
                        	<?php
                        	$array = json_decode($h['honor'], true);
                        	$str = '';
                        	foreach($array as $u){
								$str .= '<em class="pink">'.$u['nk'].'</em> ('.$u['uid'].')、';
							}
							echo rtrim($str, '、');
							?>
                        	的倾力组织下，<strong class="pink"><?php echo $family['name'];?></strong> 隆重诞生啦！
                        </dd>
                    </dl>
                	<?php }elseif($h['type'] == 'top'){?>
                    <dl class="family-honor">
                        <dt><i class="banericon"></i><span><?php echo date('Y', $family['create_time']);?> <em><?php echo date('m', $family['create_time']);?></em>月</span></dt>
                        <?php $top = json_decode($h['honor'], true);?>
                        <dd>荣获第<?php echo $top['num'];?>周 家族贡献榜 <em class="pink">第<?php echo $top['dedication'];?>名</em>！</dd>
                        <dd>荣获第<?php echo $top['num'];?>周 家族财富榜 <em class="pink">第<?php echo $top['recharge'];?>名</em>！</dd>
                        <dd>荣获第<?php echo $top['num'];?>周 家族徽章榜 <em class="pink">第<?php echo $top['medal'];?>名</em>！</dd>
                        <?php if($top['dedication'] == 1){?>
                        <dd>荣获第<?php echo $top['num'];?>周 家族贡献榜 <em class="pink">第1名宝座</em>此时此刻沉醉小荷家族 力压群雄，风光无限！</dd>
                        <?php }?>
                        <?php if($top['recharge'] == 1){?>
                        <dd>荣获第<?php echo $top['num'];?>周 家族财富榜 <em class="pink">第1名宝座</em>此时此刻沉醉小荷家族 力压群雄，风光无限！</dd>
                        <?php }?>
                        <?php if($top['medal'] == 1){?>
                        <dd>荣获第<?php echo $top['num'];?>周 家族徽章榜 <em class="pink">第1名宝座</em>此时此刻沉醉小荷家族 力压群雄，风光无限！</dd>
                        <?php }?>
                    </dl>
                    <?php }?>
                <?php }?>