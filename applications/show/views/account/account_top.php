<link rel="stylesheet" href="<?php echo $this->pipiFrontPath;?>/css/account/zhanghu.css" type="text/css" />

<div class="personal w1000 clearfix mt20">
  <div class="fleft ml20">
    <h1><span id="account_nickname"><?php echo isset($info['user_attribute']['nk']) ? $info['user_attribute']['nk'] : '';?></span>(<em><?php echo isset($info['login_uid']) ? $info['login_uid'] : 0;?></em>)的帐户中心</h1>
    <br/>帐户资产： 皮蛋 <?php echo $this->getUserJsonAttribute('pe',false,true);?> &nbsp;&nbsp;
    <?php if($this->domain_type == 'tuli'):?>
    <a href="javascript:void();" target="_blank" class="recharge J_tuli_pay"></a>
    <?php else:?>
    <a href="<?php echo $this->goExchange();?>" target="_blank" class="recharge"></a>
    <?php endif;?>
  </div>
</div>

<div class="popbox" id="FryFail">
	<div class="poph noline">
    	<a onclick="$.mask.hide('FryFail');" class="closed" title="关闭"></a>
    </div>
    <div class="popcon">
    	<ul class="paysong">
            <li>
            	<p class="otline">操作失败, 请重新操作</p>
				<p class="oneline"></p>
                <p class="oneline"><input type="button" onclick="$.mask.hide('FryFail');" class="shiftbtn" value="确&nbsp;&nbsp;定"></p>
            </li>
        </ul>
    </div>
</div>