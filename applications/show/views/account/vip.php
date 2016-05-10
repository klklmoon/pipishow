<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
     
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/bag');?>">礼物背包</a></li>
            <li><a href="<?php echo $this->createUrl('account/props');?>">道具</a></li>
            <li><a href="<?php echo $this->createUrl('account/car');?>">座驾</a></li>
            <li><a href="<?php echo $this->createUrl('account/moon');?>">月卡</a></li>
            <li class="menuvisted"><a href="<?php echo $this->createUrl('account/vip');?>">vip</a></li>
            <!--<li><a href="<?php echo $this->createUrl('account/guard');?>">家族守护</a></li>-->
            <li><a href="<?php echo $this->createUrl('account/number');?>">靓号</a></li>
        </ul><!-- .main-menu -->
		
		<div id="MainCon">
			<div class="cooper-list">
			<?php if($bagInfo): ?>
				<?php 
					$vip_info = $this->viewer['user_attribute'];
					if($vip_info['vip']['t']==2){
				?>
					<p id="account_vip_show" <?php if($vip_info['vip']['h'] == 1 ){?> style="display:none;" <?php }?> >状态：在进入直播间时<em>显示</em>身份 <button onclick="account.vip_hidden(1)" value="切换为隐身">切换为隐身</button></p>
					<p id="account_vip_hidden" <?php if($vip_info['vip']['h'] == 0 ){?> style="display:none;" <?php }?>>状态：在进入直播间时<em>隐藏</em>身份 <button onclick="account.vip_hidden(0)" value="切换为显示">切换为显示</button></p>
					<P><span>*隐身时进入直播间，不会被主播或用户发现，可以悄悄发送私聊；但有公聊发言、送礼、禁言、踢人等行为时，将暴露身份。</span></P>
				<?php } ?>
              <table class="open">
                    <tr class="colum">
                        <td width="100">名称</td>
                        <td width="200">权利</td>
                        <td width="100">期限</td>
                        <td width="100">状态</td>
                        <td width="120">管理</td>
                    </tr>
					<?php
					foreach($bagInfo as $k=>$v) {
					?>
                    <tr>
                        <td>
                            <em class="prot"><img src="<?php echo $account_imgurl. $propsInfo[$v['prop_id']]['image'];?>"></em>
                        </td>
                        <td class="power" width="520">
							<?php echo strtr($propsInfo[$v['prop_id']]['attribute']['vip_right']['value'],array("\n\r"=>'<br/>',"\n"=>'<br/>','\r'=>'<br/>'));?>
                        </td>
                        <td><?php echo $v['time_desc'];?></td>
                        <td><?php echo $v['use_status']?"停用":"启用";?></td>
                        <td>
                        <?php if($v['expired']!=1):?>
	                        <input style="width:100px;" type="button" value="<?php echo $v['use_status']?"启用":"停用";?>" 
	                        onclick="javascript:openOrStopVip(<?php echo $v['uid'];?>,<?php echo $v['prop_id'];?>,<?php echo $v['use_status']?"0":"1";?>);" />
                        <?php else:?>
                            <input style="width:100px;" type="button" value="<?php echo $v['time_desc'];?>" />
                        <?php endif;?>
                        </td>
                    </tr>
					<?php
					}
					?>
                </table>
			<?php else: ?>
				您还没有购买过VIP特权，赶快去<a target="_blank" href="<?php $this->getTargetHref($this->createUrl('shop/vip'));?>" class="undo">商城</a>看看。
			
			<?php endif;?>
           </div><!-- .cooper-list vip -->
		</div>
		
	</div><!--#MainCon-->
</div><!-- .main -->        
</div><!-- .w1000 -->
<script type="text/javascript">
function openOrStopVip(uid,prop_id,use_status)
{
	$.ajax({
		type:"POST",
		url:"index.php?r=Account/OpenOrStopVip",
		data:{'uid':uid,'prop_id':prop_id,'use_status':use_status},
		dataType:"json",
		success:function(resonseData){
			alert(resonseData.msg);
			location.reload();
		}
	});
}
</script>

