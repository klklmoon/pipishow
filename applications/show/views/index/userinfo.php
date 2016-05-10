<?php if($this->isLogin):?>

				<!--用户登录-->

					<dl class="anchorname clearfix">
						<dt><img class="headerpic" src="<?php echo $this->viewer['avatar_s'];?>"></dt>
						<dd>
							<p class="name clearfix"><em>hi，</em><em class="pink ellipsis"><?php echo $this->viewer['user_attribute']['nk'];?></em></p>
							<div class="editbtn">
                            	<div class="editbox">
                            		<p>当前昵称：<em class="pink"  title="<?php echo $this->viewer['user_attribute']['nk'];?>"><?php echo $this->viewer['user_attribute']['nk'];?></em></p>
                            		<p><input type="text" value="ID或昵称" id="indexNickAame"></p>
                            		<p class="sureEdit">
                            			<a id="indexModifyNickname" href="javascript:void(0);" title="确定修改" class="surebtn">确定修改</a>
                            			<a href="javascript:void(0);" title="不改了" class="noedit">不改了</a>
                            		</p>
                            		<p class="remarkEdit"><em>注：</em>昵称不能超过10个字<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;修改后，原昵称可能会被抢注</p>
                            		<em title="关闭" class="topclosedBtn"></em>
                            	</div>
                            </div><!--.editbtn-->
                            <a class="fright" href="index.php?r=user/logout">[退出]</a>
						</dd>
                        <dd id="headinfo">
                        	<?php 
                        		$user_vip=$this->viewer['user_attribute']['vip'];
                        		if($user_vip['vt']>time()&&$user_vip['t']>0):
                        	?>
                            	<em class="ver ver-<?php echo $user_vip['t'];?>"></em>
                            <?php endif;?>
                            
                            <?php if(isset($this->viewer['user_attribute']['num'])):?>
	                            <?php if(strlen($this->viewer['user_attribute']['num']['n'])==4):?>
	                            <span class="jpnumb"><em>靓</em><?php echo $this->viewer['user_attribute']['num']['n'];?></span>
	                            <?php else:?>
	                            <span class="fivenumb"><em>靓</em><?php echo $this->viewer['user_attribute']['num']['n'];?></span>
	                            <?php endif;?>
                            <?php else:?>
                            	<span>ID:<?php echo $this->viewer['user_attribute']['uid'];?></span>
                            <?php endif;?>
                        </dd>
					</dl>
					<p class="mt10">
					<?php if($this->isDotey):?>
						魅力等级<em class="lvlo lvlo-<?php echo isset($this->viewer['user_attribute']['dk'])?$this->viewer['user_attribute']['dk']:0;?>"></em>&nbsp;&nbsp;&nbsp;
						富豪等级<em class="lvlr lvlr-<?php echo isset($this->viewer['user_attribute']['rk'])?$this->viewer['user_attribute']['rk']:0;?>"></em>
					<?php else:?>
						富豪等级<em class="lvlr lvlr-<?php echo isset($this->viewer['user_attribute']['rk'])?$this->viewer['user_attribute']['rk']:0;?>"></em>&nbsp;&nbsp;&nbsp;
					<?php endif;?>
					</p>
					<p class="mangebtn mt10 clearfix">
						<a href="<?php $this->getTargetHref($this->createUrl('family/index'));?>">我的家族</a>
                        <a href="<?php $this->getTargetHref($this->createUrl('account/items'));?>">我的物品</a>
                        <a href="<?php $this->getTargetHref($this->createUrl('account/main'));?>">个人中心</a>
					</p>
                    <div class="mybank mt5">
                        <dl>
                            <dd><strong class="pink"><?php echo intval($this->viewer['user_attribute']['pe']);?></strong>
                            <?php if($this->isPipiDomain):?>
                            <a href="javascript:goExchange();" class="col2">皮蛋</a>
                            <?php elseif($this->domain_type == 'pptv'):?>
			                <a href="javascript:void(0);" class="col2 J_pptv_pay">充值</a>
			                <?php elseif($this->domain_type == 'tuli'):?>
			                <a href="javascript:void(0);" class="col2 J_tuli_pay" target="_self">充值</a>
			                <?php endif;?>
                            </dd>
                            <dd><strong class="pink"><?php  echo intval($this->viewer['user_attribute']['ep']);?></strong>
                            <a href="<?php $this->getTargetHref($this->createUrl('account/exchange'));?>" class="col2"  target="<?php echo $this->target;?>">皮点</a>
                            </dd>
                            <?php if($this->isDotey):?>
                            <dd><strong class="pink"><?php  echo intval($this->viewer['user_attribute']['cp']);?></strong>
                            <a href="<?php $this->getTargetHref($this->createUrl('account/cash'));?>" class="col2" target="<?php echo $this->target;?>">魅力点</a>
                            </dd>
                            <?php endif;?>
                        </dl>
                    </div><!--.mybank-->

<?php else:?>                    
                    
				<!-- 用户未登录 -->
					<p class="welcome"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/index/right/autoHeader.jpg"><span>你好，欢迎光临！</span></p>
					<p class="logBtn clearfix">
						<a href="javascript:$.User.loginController('login');">登&nbsp;&nbsp;&nbsp;&nbsp;录</a>
						<a href="javascript:$.User.loginController('register');">免费注册</a>
					</p>                    
<?php endif;?>