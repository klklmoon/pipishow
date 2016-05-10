 		<!--用户登录后-->
        <ul id="LogMenu" class="fright logmenu-box" <?php if(!$this->isLogin):?>style="display:none;"<?php endif;?>>
            <li class="userheader" id="userheader">
                <a href="<?php echo $this->getTargetHref($this->createUrl('account/main'));?>" target="<?php echo $this->target;?>" class="sidebg"><img class="headerpic" src="<?php echo $this->viewer['avatar_s']?>"></a>
                <div class="showlogbox userheader-con">
                    <dl class="anchorname clearfix">
                        <dt><a href="<?php echo $this->getTargetHref($this->createUrl('account/main'));?>" target="<?php echo $this->target;?>"><img class="headerpic" src="<?php echo $this->viewer['avatar_s']?>"></a></dt>
                        <dd id="headnickname">
                            <p>hi，<em class="pink" title="<?php echo isset($this->viewer['user_attribute']['nk'])?$this->viewer['user_attribute']['nk']:'';?>"><?php echo isset($this->viewer['user_attribute']['nk'])?$this->viewer['user_attribute']['nk']:'';?></em>！</p>
                            <div class="editbtn">
                            	<div class="editbox">
                            		<p>当前昵称：<em class="pink updateNickName" title="<?php echo isset($this->viewer['user_attribute']['nk'])?$this->viewer['user_attribute']['nk']:'';?>"><?php echo isset($this->viewer['user_attribute']['nk'])?$this->viewer['user_attribute']['nk']:'';?></em></p>
                            		<p><input type="text" value="ID或昵称" id="nickname"></p>
                            		<p class="sureEdit">
                            			<a class="surebtn updateNickName" id="modifyNickname" title="确定修改" href="javascript:void(0);">确定修改</a>
                            			<a class="noedit" title="不改了" href="javascript:void(0);">不改了</a>
                            		</p>
                            		<p class="remarkEdit"><em>注：</em>昵称不能超过10个字<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;修改后，原昵称可能会被抢注</p>
                            		 <em class="topclosedBtn" title="关闭"></em>
                            	</div>
                            </div><!--.editbtn-->
                            <a class="fright" href="index.php?r=user/logout">[退出]</a>
                        </dd>
                        <dd id="headinfo">
                        	<?php 
                        		if(isset($this->viewer['user_attribute']['vip'])):
	                        		$user_vip=$this->viewer['user_attribute']['vip'];
	                        		if($user_vip['vt']>time()&&$user_vip['t']>0):
                        	?>
                            <em class="ver ver-<?php echo $user_vip['t'];?>"></em>
                            <?php 
                            		endif;
                            	endif;
                            ?>
                            <?php if(isset($this->viewer['user_attribute']['num'])):?>
                            <?php if(strlen($this->viewer['user_attribute']['num']['n'])==4):?>
                            <span class="jpnumb"><em>靓</em><?php echo $this->viewer['user_attribute']['num']['n'];?></span>
                            <?php else:?>
                            <span class="fivenumb"><em>靓</em><?php echo $this->viewer['user_attribute']['num']['n'];?></span>
                            <?php endif;?>
                            <?php else:?>
                            <span>ID:<?php echo isset($this->viewer['user_attribute']['uid'])?$this->viewer['user_attribute']['uid']:0;?></span>
                            <?php endif;?>
                            <span>消费星级：</span>
                            <?php if(isset($this->viewer['user_attribute']['st'])&&$this->viewer['user_attribute']['st']>0):?>
                            <em class="lvls lvls-<?php echo $this->viewer['user_attribute']['st'];?>"></em>
                        	<?php else:?>
                        	<?php echo "无";?>
                        	<?php endif;?>
                        </dd>
                    </dl><!--.anchorname-->
                    <ul class="anchorlvl clearfix" id="anchorlvl">
                   		<li <?php if(!$this->isDotey):?>style="display:none"<?php endif;?>>
                            <em class="fleft lvlo lvlo-<?php echo isset($this->viewer['user_attribute']['dk'])?$this->viewer['user_attribute']['dk']:0;?>"></em>
                            <a class="fleft process-box" id="Usercp">
                                <span class="process"></span>
                                <span class="rate-con clearfix">
                                <em class="now-rate"><?php  echo isset($this->viewer['user_attribute']['ch'])?$this->viewer['user_attribute']['ch']:0;?></em>
                                <em>/</em>
                                <em class="total-rate"><?php echo (isset($this->viewer['user_attribute']['nxch'])&&$this->viewer['user_attribute']['nxch']>0)?$this->viewer['user_attribute']['nxch']:99999999;?></em>
                                </span>
                            </a>
                            <em class="fleft lvlo lvlo-<?php echo isset($this->viewer['user_attribute']['dk'])?($this->viewer['user_attribute']['dk']+1):1;?>"></em>
                            <span class="fright">升级还需<em class="pink"><?php echo intval(((isset($this->viewer['user_attribute']['nxch'])&&$this->viewer['user_attribute']['nxch']>0)?$this->viewer['user_attribute']['nxch']:99999999)-(isset($this->viewer['user_attribute']['ch'])?$this->viewer['user_attribute']['ch']:0));?></em>魅力</span>
                        </li>
                      	<li> 
                            <em class="fleft lvlr lvlr-<?php echo isset($this->viewer['user_attribute']['rk'])?$this->viewer['user_attribute']['rk']:0;?>"></em>
                            <a class="fleft process-box process-blue" id="Devote">
                                <span class="process"></span>
                                <span class="rate-con clearfix">
                                <em class="now-rate"><?php echo isset($this->viewer['user_attribute']['de'])?$this->viewer['user_attribute']['de']:0;?></em>
                                <em>/</em>
                                <em class="total-rate"><?php echo (isset($this->viewer['user_attribute']['nxde'])&&$this->viewer['user_attribute']['nxde']>0)?$this->viewer['user_attribute']['nxde']:99999999;?></em>
                                </span>
                            </a>
                            <em class="fleft lvlr lvlr-<?php echo isset($this->viewer['user_attribute']['rk'])?(intval($this->viewer['user_attribute']['rk']) + 1):1;?>"></em>
                            <span class="fright">升级还需<em class="pink"><?php  echo intval((isset($this->viewer['user_attribute']['nxde'])&&$this->viewer['user_attribute']['nxde']>0)?$this->viewer['user_attribute']['nxde']:99999999)-(isset($this->viewer['user_attribute']['de'])?$this->viewer['user_attribute']['de']:0);?></em>贡献</span>
                        </li>
                    </ul><!--.anchorlvl-->
                    <ul class="anchorlvl clearfix">
                    	<li class="mt10 mangebtn">
                            <a href="<?php $this->getTargetHref($this->createUrl('account/follow'));?>" target="<?php echo $this->target;?>">关注管理</a>
                            <a href="<?php $this->getTargetHref($this->createUrl('family/index'));?>" target="<?php echo $this->target;?>">我的家族</a>
                            <a href="<?php $this->getTargetHref($this->createUrl('account/main'));?>" target="<?php echo $this->target;?>">个人中心</a>
                            <a href="<?php $this->getTargetHref($this->createUrl('account/security'));?>" target="<?php echo $this->target;?>">账户安全</a>
                        </li>
                    </ul>
                    <div id="MyBank" class="mybank">
                        <dl>
                            <dt><a href="javascript:void(0);">我的资产</a></dt>
                            <dd><strong class="pink"><?php echo isset($this->viewer['user_attribute']['pe'])?intval($this->viewer['user_attribute']['pe']):0;?></strong>
                            <?php if($this->isPipiDomain):?>
			                <a href="javascript:goExchange();" class="col2">皮蛋</a>
			                <?php elseif($this->domain_type == 'pptv'):?>
			                <a href="javascript:void(0);" class="col2 J_pptv_pay">充值</a>
			                <?php elseif($this->domain_type == 'tuli'):?>
			                <a href="javascript:void(0);" class="col2 J_tuli_pay" target="_self">充值</a>
			                <?php endif;?></dd>
                            <dd><strong class="pink"><?php  echo isset($this->viewer['user_attribute']['ep'])?intval($this->viewer['user_attribute']['ep']):0;?></strong><a class="col2" href="<?php $this->getTargetHref($this->createUrl('account/exchange'));?>" target="<?php echo $this->target;?>">皮点</a></dd>
                            <dd <?php if(!$this->isDotey):?>style="display:none"<?php endif;?>><strong class="pink"><?php  echo isset($this->viewer['user_attribute']['cp'])?intval($this->viewer['user_attribute']['cp']):0;?></strong><a class="col2" href="<?php $this->getTargetHref($this->createUrl('account/cash'));?>" target="<?php echo $this->target;?>">魅力点</a></dd>
                            <dd><a href="#" id="make_pipieggs" target="<?php echo $this->target;?>">免费皮蛋</a></dd>
                            <dd><a href="<?php $this->getTargetHref($this->createUrl('account/bag'));?>" target="<?php echo $this->target;?>">礼物</a></dd>
                            <dd><a href="<?php $this->getTargetHref($this->createUrl('account/car'));?>" target="<?php echo $this->target;?>">座驾</a></dd>
                            <dd><a href="<?php $this->getTargetHref($this->createUrl('account/props'));?>" target="<?php echo $this->target;?>">道具</a></dd>
                            <dd><a href="<?php $this->getTargetHref($this->createUrl('account/number'));?>" target="<?php echo $this->target;?>">靓号</a></dd>
                        </dl>
                    </div><!--.mybank-->
                </div><!--.userheader-con-->
            </li><!--.userheader-->
            <li class="cnner" id="attentionDotey">
                <a <?php if($this->isDotey):?> href="<?php $this->getTargetHref('/'.$this->viewer['login_uid']);?>"<?php endif;?> class="sidebg">主播</a>
                <div class="showlogbox cnner-con">
                	<div class="cnner-hd">
                		<ul class="fleft">
                			<li class="on">关注的主播&nbsp;&nbsp;<strong id="attentionDoteyNum">0/0</strong></li>
                			<li>管理的主播&nbsp;&nbsp;<strong id="manageDoteyNum">0/0</strong></li>
                			<li>看过的主播&nbsp;&nbsp;<strong id="viewDoteyNum">0</strong></li>
                		</ul>
                		<p class="fright lhd-con">
	                		<a href="<?php $this->getTargetHref($this->createUrl('account/follow'));?>">全部关注</a>
	                		<em>&#124</em>
	                		<a href="<?php $this->getTargetHref($this->createUrl('account/manage'));?>">管理</a>
                		</p>
                	</div>
                	<div class="conner-pbd">
	                	<div class="conner-bd" id="attentionDoteyList"></div><!--.conner-bd-->
                		<div class="conner-bd" id="manageDoteyList"></div><!--.conner-bd-->
                		<div class="conner-bd" id="viewDoteyList"></div><!--.conner-bd-->
                	</div><!--.conner-phd-->
                </div><!--.cnner-con-->
            </li><!--.cnner-->
            <li class="qmdk" id="checkInList">
                <a class="sidebg">签到</a>
                <div class="showlogbox qmdk-con">
                	<p class="qmdk-hd">签到有礼</p>
                	<div id="showqmdlist"></div>
                </div><!--.qmdk-con-->
            </li>
            <li class="infos" id="messageList">
                <a href="<?php echo $this->getTargetHref($this->createUrl('account/message'));?>" class="sidebg">消息</a>
            	<ul class="showlogbox infos-con">
            		<li><a href="<?php $this->getTargetHref('index.php?r=account/message&type=system')?>" target="<?php echo $this->target;?>"><span class="fleft">系统通知</span><em class="fright pink" id="systemMessage">0</em></a></li>
            		<li><a href="<?php $this->getTargetHref('index.php?r=account/message&type=family')?>" target="<?php echo $this->target;?>"><span class="fleft">家族通知</span><em class="fright pink" id="familyMessage">0</em></a></li>
            		<li><a href="<?php $this->getTargetHref('index.php?r=account/message&type=site')?>" target="<?php echo $this->target;?>"><span class="fleft">全站通知</span><em class="fright pink" id="siteMessage">0</em></a></li>
            	</ul>
            	<!--.infos-con-->
            </li>
            <li class="recharge">
            	<?php if($this->isPipiDomain):?>
                <a href="javascript:goExchange();" class="sidebg">充值</a>
                <?php elseif($this->domain_type == 'pptv'):?>
                <a href="javascript:void(0);" class="sidebg J_pptv_pay">充值</a>
                <?php elseif($this->domain_type == 'tuli'):?>
                <a href="javascript:void(0);" class="sidebg J_tuli_pay" target="_self">充值</a>
                <?php endif;?>
            </li>
        </ul><!--.loginbox-->
        <?php if(isset($this->viewer['user_basic']['reg_mobile'])&&$this->viewer['user_basic']['reg_mobile']==''&&$this->viewer['user_basic']['reg_email']==''):?>
      	<div class="bindinfo">
        	您尚未绑定邮箱或手机，为了账号安全，<a class="pink" href="<?php echo $this->getTargetHref($this->createUrl('account/security'))?>" target="<?php echo $this->target;?>">请立即绑定(至少一种)</a>
        	<em title="关闭" class="closed"></em>
        </div><!--.bindinfo-->
        <?php endif;?>