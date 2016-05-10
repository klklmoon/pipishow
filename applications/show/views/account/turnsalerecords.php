	
				<?php if($salerecordsType=="ByMonth"):?>
					<div class="roynum clearfix">
						<p class="fleft">
							<select id="selectYearMonth">
								<?php foreach($monthList as $monthRow):?>
								<option value="<?php echo $monthRow['value'];?>" <?php if($seletedMonth==$monthRow['value']){echo ' selected="selected"';}?>>
								<?php echo $monthRow['text'];?>
								</option>
								<?php endforeach;?>
							</select>
						</p>
						<p class="fleft moonyroy">
							提成金额：<em><?php echo isset($statData['sum_income'])?$statData['sum_income']:0;?></em>元
						</p>
						<p class="fleft moonyroy">
							销售额：<em><?php echo intval(isset($statData['sale_pipieggs'])?$statData['sale_pipieggs']:0);?></em>皮蛋
						</p>
					</div>
					<?php if($salerecords['list']):	?>
					<table width="620" border="1" bordercolor="#DDDDDD">
						<tr bgcolor="#F5F5F5" class="biaot">
							<td width="200" height="40">购买时间</td>
							<td width="150" height="40">玩家</td>
							<td width="120" height="40">购买道具</td>
							<td width="150" height="40">购买数量</td>
							<td width="100" height="40">花费皮蛋数</td>
							<td width="100" height="40">提成金额</td>
						</tr>
						<?php foreach($salerecords['list'] as $v):?>
						<tr>
							<td height="30"><?php echo date("Y-m-d H:i:s",$v['create_time']);?></td>
							<td height="30"><?php echo isset($v['user_nickname'])?$v['user_nickname']:"";?>(<?php echo $v['uid'];?>)</td>
							<td height="30"><?php echo isset($v['goods_name'])?$v['goods_name']:"";?></td>
							<td height="30"><?php echo $v['goods_num'];?></td>
							<td height="30"><?php echo intval($v['pipieggs']);?></td>
							<td height="30"><?php echo $v['agent_income'];?></td>
						</tr>
						<?php endforeach;?>
					</table>
					<!--翻页-->
					<?php
						$count = $salerecords['count'];
						$page = $salerecords['page'];
						$page_num = $salerecords['page_num'];
						echo '<p>'.$count.' 条记录 '.$page.' / '.$page_num.' 页</p>';
					?>
					<ol class="page">
						<li><a href="javascript:salerecordsByMonth(1);">首页</a></li>
						<?php
							$_page = $page > 1 ? ( ($page_num-$page < 2) ? (($page_num - 2 > 0) ? $page_num -2 : 1) : $page - 1) : 1;
							for($_p = $_page; $_p <= $page_num; $_p++){
								echo "<li><a href='javascript:salerecordsByMonth(".$_p.");'".($_p==$page ? ' style="background:#ffb6e2"' : '').'>'.$_p.'</a></li>';
								if(($_p - $_page) == 2) {
									break;
								}
							}
						?>
						<li><a href="javascript:salerecordsByMonth(<?php echo $page_num;?>);">尾页</a></li>
					</ol>
					<!--翻页-->
					<?php else:?>
					无销售记录
					<?php endif;?>
				<?php endif;?>
				
				
				<?php if($salerecordsType=="ByUserId"):?>
					<div class="roynum clearfix">
						<p class="fleft">
							<input type="text" id="user_id" > <a class="samllpinkbtn"
								href="javascript:salerecordsByUserId(1);">搜索</a>
						</p>
						<p class="fleft moonyroy">
							提成金额：<em><?php echo $statData['sum_income'];?></em>元
						</p>
						<p class="fleft moonyroy">
							销售金额：<em><?php echo $statData['sale_pipieggs'];?></em>元
						</p>
					</div>
					<?php if($salerecords['list']):	?>
					<table width="620" border="1" bordercolor="#DDDDDD">
						<tr bgcolor="#F5F5F5" class="biaot">
							<td width="150" height="40">购买时间</td>
							<td width="150" height="40">玩家</td>
							<td width="120" height="40">购买道具</td>
							<td width="150" height="40">购买数量</td>
							<td width="100" height="40">购买价格</td>
							<td width="100" height="40">提成金额</td>
						</tr>
						<?php foreach($salerecords['list'] as $v):?>
						<tr>
							<td height="30"><?php echo date("Y-m-d H:i:s",$v['create_time']);?></td>
							<td height="30"><?php echo isset($v['user_nickname'])?$v['user_nickname']:"";?>(<?php echo $v['uid'];?>)</td>
							<td height="30"><?php echo isset($v['goods_name'])?$v['goods_name']:"";?></td>
							<td height="30"><?php echo $v['goods_num'];?></td>
							<td height="30"><?php echo $v['pipieggs'];?></td>
							<td height="30"><?php echo $v['agent_income'];?></td>
						</tr>
						<?php endforeach;?>
					</table>
					<!--翻页-->
					<?php
						$count = $salerecords['count'];
						$page = $salerecords['page'];
						$page_num = $salerecords['page_num'];
						echo '<p>'.$count.' 条记录 '.$page.' / '.$page_num.' 页</p>';
					?>
					<ol class="page">
						<li><a href="javascript:salerecordsByUserId(1);">首页</a></li>
						<?php
							$_page = $page > 1 ? ( ($page_num-$page < 2) ? (($page_num - 2 > 0) ? $page_num -2 : 1) : $page - 1) : 1;
							for($_p = $_page; $_p <= $page_num; $_p++){
								echo "<li><a href='javascript:salerecordsByUserId(".$_p.");'".($_p==$page ? ' style="background:#ffb6e2"' : '').'>'.$_p.'</a></li>';
								if(($_p - $_page) == 2) {
									break;
								}
							}
						?>
						<li><a href="javascript:salerecordsByUserId(<?php echo $page_num;?>);">尾页</a></li>
					</ol>
					<!--翻页-->
					<?php else:?>
					无销售记录
					<?php endif;?>
				<?php endif;?>
	