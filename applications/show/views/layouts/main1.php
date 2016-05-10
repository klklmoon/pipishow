<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $this->getPageTitle()?>123</title>
<meta name="keywords" content="<?php echo $this->getPageKeyWords()?>" />
<meta name="description" content="<?php echo $this->getPageDescription()?>" />
<link rel="shortcut icon" href="<?php echo $this->pipiFrontPath?>/fontimg/common/favicon.ico" type="image/x-icon"> 
<link rel="icon" href="<?php echo $this->pipiFrontPath?>/fontimg/common/favicon.ico" type="image/x-icon">
<script type="text/javascript">
var is_ajax_user_attribute = true;
var runEnvironment = '<?php echo DEV_ENVIRONMENT?>';
var runFontPath = '<?php echo $this->pipiFrontPath?>';
var UploadHttpUrl = '<?php echo $this->userService->getUploadUrl()?>';
var is_dotey = <?php echo $this->isDotey ? 1 : 0?>;
var hrefTarget = '<?php echo $this->target?>';
var SERVER_TIME = '<?php echo time()?>';
var domain_type = '<?php echo $this->domain_type?>';
var domain_pipi = '<?php echo $this->isPipiDomain?>';
var cookie_domain  = '<?php echo DOMAIN?>';
var page_controller = '<?php echo $this->getId();?>';
var giftShopUrl = '<?php echo $this->getTargetHref($this->createUrl('shop/gift'));?>';
var login_pop_type=false;
var pb;
</script>
</head>
<body>
<?php 
if($this->viewer['topHeadBanner']):
$topHeadBannel = $this->viewer['topHeadBanner'];
?>
<a <?php echo $topHeadBanner['href']?> target="<?php echo $topHeadBannel['target']?>" id="TopAd" class="topad" title="<?php echo $topHeadBannel['subject'] ?>" style="background: url(<?php echo $topHeadBannel['piclink'] ?>) no-repeat center top">
	<span class="topclosed clearfix">
	<em title="关闭" class="topclosedBtn"></em>
	</span>
</a>
<?php endif;?>

<!--.header-->
<div class="header clearfix">
    <div class="w1000">
    	<p class="fleft logo">
     	<?php if($this->isPipiDomain):?>
        <a name="window_top" id="window_top" href="<?php $this->getTargetHref($this->createUrl('index/index'))?>" target="_self"><img src="<?php echo $this->pipiFrontPath?>/fontimg/common/logo.png"></a>
        <?php elseif($this->domain_type == 'pptv'):?>
        <a name="window_top" id="window_top" href="<?php $this->getTargetHref($this->createUrl('index/index'))?>" target="<?php echo $this->target?>"><img src="<?php echo $this->pipiFrontPath?>/fontimg/common/logo.png"></a>
        <?php else: ?>
        <a name="window_top" id="window_top" href="<?php $this->getTargetHref($this->createUrl('index/index'))?>" target="<?php echo $this->target?>"><img src="<?php echo $this->pipiFrontPath?>/fontimg/common/logo.png"></a>
        <?php endif;?>
        <?php if($this->isPipiDomain):?>
        <a class="biao" href="javascript:void(0)" onclick="javascript:desktop();return false;"><span>收藏到桌面</span></a>
        <?php endif;?>
        <span class="biaotip">
        	<a href="#" title="关闭" class="close"></a>
        	<a href="#" title="点击收藏" class="ressbtn" onclick="javascript:desktop();return false;"></a>
        </span>
        </p>
        <ul id="Nav" class="fleft nav">
            <li>
             <?php if($this->isPipiDomain):?>
		    		<a href="<?php $this->getTargetHref('/')?>" title="首页"  target="_self">首页</a></li>
		     <?php elseif($this->domain_type == 'pptv'):?>
		        	<a href="<?php $this->getTargetHref('/')?>" title="首页"  target="<?php echo $this->target?>">首页</a></li>
		     <?php elseif ($this->domain_type == 'tuli') : ?>
		     		<a href="<?php $this->getTargetHref('index.php')?>" title="首页"  target="_blank">首页</a></li>
		     <?php endif;?>
		    <!-- 
            <li><a href="<?php $this->getTargetHref('index.php?r=channel/category')?>" title="节目分类"  target="<?php echo $this->target?>">节目分类</a></li>
            -->
            <li><a href="<?php $this->getTargetHref('index.php?r=top/index')?>" title="排行榜"  target="<?php echo $this->target?>">排行榜</a></li>
            <?php if(FamilyService::familyEnable()){?>
            <li><a href="<?php $this->getTargetHref('index.php?r=family/index')?>" title="家族"  target="<?php echo $this->target?>">家族</a></li>
            <?php }?>
            <li><a href="<?php $this->getTargetHref('index.php?r=shop/gift')?>" title="商城" target="<?php echo $this->target?>">商城</a></li>
      		<?php foreach($this->viewer['topNavigate'] as $tNavigate):?>
      		<?php if($this->domain_type != 'pptv' || !preg_match('/apply/', $tNavigate['link'])) : ?>
      		<li><a href="<?php $this->getTargetHref($tNavigate['link'])?>" title="<?php echo $tNavigate['name']?>" target="<?php echo $this->target?>"><?php echo $tNavigate['name']?></a></li>
      		<?php endif;?>
      		<?php endforeach;?>
        </ul>
        <div class="fleft search clearfix" id="SearchBox">
            <input id="SearchText" class="fleft" value="ID或昵称" type="text">
            <a href="javascript:void(0);" class="searchbtn fleft" title="搜索"></a>
            <div class="searchList" style="display:none;">  
            <ul>
            </ul>
            </div>
        </div>
       
        <div class="fright login" style="display:<?php echo ($this->isLogin ? '' : 'none')?>;" id="login_header">
            <ul id="LoginList" class="login-list">
                <li id="Portrait" class="portrait">
                	<a class="portlink" href="<?php $this->getTargetHref('index.php?r=account/main')?>" title="<?php $this->getUserJsonAttribute('nk',false,true);?>" target="<?php echo $this->target?>"><img class="portimg" src="<?php echo $this->viewer['avatar_s']?>" id="header_avatar"/></a>
                	
                	<!-- 用户信息浮层开始 -->
                	<div id="PortraitInfo" class="portrait-info clearfix">
                    	<dl id="PortMenu" class="fleft portrait-menu">
                        	<dd><a class="portover" href="<?php $this->getTargetHref('index.php?r=account/main')?>" title="个人资料" target="<?php echo $this->target?>">个人资料</a></dd>
                            <dd><a href="<?php $this->getTargetHref('index.php?r=account/follow')?>" title="关注管理" target="<?php echo $this->target?>">关注管理</a></dd>
                            <dd><a href="<?php $this->getTargetHref('index.php?r=account/items')?>" title="我的物品" target="<?php echo $this->target?>">我的物品</a></dd>
                            <?php if($this->domain_type != 'pptv') :?>
                            <dd><a href="<?php $this->getTargetHref('index.php?r=account/security')?>" title="账户安全" target="<?php echo $this->target?>">账户安全</a></dd>
                            <dd><a href="index.php?r=user/logout" title="退出账号" id="logout">退出账号</a></dd>
                            <?php else: ?>
                            <style>
								#PortraitInfo{height:113px;} 
								.portrait-r{height:113px;}
								.portrait-r p{line-height:28px; height:28px;}
								.makename{line-height:28px; height:28px;}
        					</style>
                            <?php endif;?>
                        </dl>
                        <div class="fleft portrait-r">
                        	<p class="clearfix">
                        	<span class="fleft">账号ID：</span>
                        	<span class="fleft jpnumb" style="display:none;" id="jpnumb"><em>靓</em>
                                <i style="display: none;" class="tipcon"></i></span>
                        	<em class="fleft" id="header_uid"><?php $this->getUserJsonAttribute('uid',false,true);?></em>
                            <a href="javascript:void(0)" class="fleft orange" id="viewStar">查看消费星级</a>
	                        	<?php /*
	                        	<a href="javascript:void(0)" class="fright mt3 mr5 pinkbtn" title="签到领礼" onclick="$.User.checkin(0);">
	                        		<span>签到领礼</span>
	                        	</a>
	                        	*/ ?>
	                        	<?php if(FamilyService::familyEnable()){?>
	                        	<a href="<?php $this->getTargetHref('index.php?r=family/myFamily')?>" class="fright mt3 mr5 pinkbtn" title="我的家族" target="<?php echo $this->target?>">
	                        		<span>我的家族</span>
	                        	</a>
	                        	<?php }?>
                        	</p>
                            <p class="clearfix">
                            	<span class="fleft">昵称：</span>
                            	<span id="MakeName" class="fleft">
                                	<em class="petname ellipsis" id="header_nk"><?php $this->getUserJsonAttribute('nk',false,true);?></em>
                                	<input class="updatebtn" type="button" value="修改">
                                </span>
                                 <span id="MakeText" class="fleft makename">
                                	<input class="fleft name" type="text" name="nickname" id="nickname">
                                	<input class="fleft topsurebtn updateNickName" type="button" value="确定">
                                </span>
                            	 <?php 
        	 		 				if($this->isPipiDomain):
        						 ?>
                            	<a  id="make_pipieggs" href="<?php $this->getTargetHref('http://app.offer99.com/index.php?pid=d37c6b09a49ed5d6ca1a52569a39073e&userid='.$this->getUserJsonAttribute('uid',true,true))?>" class="fright mr5 pinkbtn" title="免费皮蛋" target="<?php echo $this->target?>"><span>免费皮蛋</span></a>
                           		 <?php 
                           		 	endif;
                           		 ?>
                            </p>
                            <p class="clearfix">
                            	<em id="header_rk" class="fleft mt2 lvlr lvlr-<?php  $this->getUserJsonAttribute('rk',false,true);?>"></em>
                           		<a class="fleft mt5 process-box process-blue" id="RichLevel">
                                	<span class="process"></span>
                                	<span class="rate-con clearfix">
                                	<em class="now-rate"><?php  $this->getUserJsonAttribute('de',false,true);?></em>
                                	<em>/</em>
                                	<em class="total-rate"><?php  $this->getUserJsonAttribute('nxde',false,true);?></em>
                                	</span>
                            	</a>
                                <em id="header_rk2" class="fleft mt2 lvlr lvlr-<?php echo (intval($this->getUserJsonAttribute('rk',true,true)) + 1);?>"></em>
                            </p>
                            
                            <p class="clearfix" style="display:<?php echo ($this->isDotey ? '' : 'none')?>;" id="login_dotey_rank">
                            	<span  class="fleft"></span><em id="header_dk" class="fleft lvlo lvlo-<?php  $this->getUserJsonAttribute('dk',false,true);?>"></em>
                           		<a class="fleft mt4 process-box" id="CharmLevel">
                                    <span class="process"></span>
                                    <span class="rate-con clearfix">
                                    <em class="now-rate">0</em>
                                    <em>/</em>
                                    <em class="total-rate">0</em>
                                    </span>
                                </a>
                                <span  class="fleft"></span><em id="header_dk2" class="fleft lvlo lvlo-<?php echo (intval($this->getUserJsonAttribute('dk',true,true)) + 1);?>"></em>
                            </p>
                            <p class="egg">
                            	<span>皮蛋：</span><em id="header_pipiegg"><?php $this->getUserPipieggs(false)?></em><a class="changelink" href="javascript:goExchange();">充值</a>
                            </p>

                            <p id="login_eggpoints" style="display:block;">
                            	<span>皮点：</span><em id="header_eggpoints" class="pink"><?php  $this->getUserJsonAttribute('ep',false,true);?></em><a class="changelink" href="<?php $this->getTargetHref('index.php?r=account/exchange')?>" target="<?php echo $this->target;?>">兑换</a>
                            </p>

                            <p style="display:block;" id="login_dotey_charmpoints">
                            	<span>魅力点：</span><em id="header_charmpoints"  class="pink"><?php  $this->getUserJsonAttribute('cp',false,true);?></em><a class="changelink" href="<?php $this->getTargetHref('index.php?r=account/exchange')?>" target="<?php echo $this->target;?>">兑换</a><a class="changelink" href="<?php $this->getTargetHref('index.php?r=account/cash')?>" target="<?php echo $this->target;?>">提现</a>
                            </p>

                        </div>
                        <div class="clr"></div>
                    </div>
                    <!-- 用户信息浮层结束 -->
                    
                    <!-- 绑定信息浮层开始 -->
                    <div class="toptip" style="display:none;">
                        <em>关闭</em>
                        <p>您尚未绑定手机密保，为了账号安全，请<a class="pink" href="<?php $this->getTargetHref('index.php?r=account/security')?>" target="<?php echo $this->target?>">立即绑定</a></p>
                    </div>
                    <!-- 绑定信息浮层结束 -->
                </li>
                
                <li><a class="signbtn" href="javascript:void(0)" title="签到" onclick="$.User.checkin(0);">签到</a></li>
                
                <li class="infoList">
                	<a class="infobtn" href="<?php echo $this->getTargetHref($this->createUrl('account/message')); ?>" title="消息" target="<?php echo $this->target?>">消息</a>
                	<em class="red-dot none"></em>
                	
                	<!-- 消息浮层开始 -->
                    <div class="newInfo none">
                        <p class="newApply">你有新的消息!</p>
                        <em class="closed"></em>
                    </div>
                    <!-- 消息浮层结束 -->
                </li>
                                
                <?php if($this->isPipiDomain): ?>
                <li><a class="chargebtn" href="javascript:goExchange();" title="充值">充值</a></li>
                <?php elseif($this->domain_type == 'pptv'):?>
                	<li><a class="chargebtn J_pptv_pay" href="javascript:void(0);" title="充值" >充值</a></li>
                <?php elseif($this->domain_type == 'tuli'):?>
                	<li><a class="chargebtn J_tuli_pay" href="javascript:void(0);" target="_self" title="充值" >充值</a></li>
                <?php endif;?>
                
                <!--  
                <li><a class="trendbtn" href="#" title="动态" target="<?php echo $this->target?>">动态</a></li>
				-->

                <?php if($this->isPipiDomain) :?>
                <li style="display:<?php echo ($this->isDotey ? '' : 'none')?>;" id="login_dotey_archives"><a class="livebtn" href="/<?php echo $this->viewer['login_uid']?>" title="直播" target="<?php echo $this->target?>">直播</a></li>
                <li style="display:<?php echo ($this->isDotey ? 'none' : '')?>;" id="login_dotey_apply"><a class="livebtn" href="index.php?r=dotey/apply" title="直播" target="<?php echo $this->target?>">直播</a></li>
                <?php endif;?>
            </ul>
        </div>
        <?php if($this->isPipiDomain):?>
	        <div class="fright login" style="display:<?php echo ($this->isLogin ? 'none' : '')?>;" id="logout_header">
	            <a class="fleft regbtn" href="javascript:void(0)" title="注册"  id="regbtn">注册</a>
	            <a class="fleft logbtn" href="javascript:void(0)" title="登录" id="logbtn">登录</a>
	            <a class="fleft qqlogin" href="<?php $this->getTargetHref('index.php?r=user/openLogin&type=qq')?>" target="_self" title="QQ登录">QQ登录</a>
	        </div>
	        <?php elseif($this->domain_type == 'pptv'):?>
	        
	         <div class="fright login" style="display:<?php echo ($this->isLogin ? 'none' : '')?>;" id="logout_header">
	            <a class="fleft regbtn J_pptv_reg" href="javascript:void(0)" title="注册" >注册</a>
	            <a class="fleft logbtn J_pptv_login" href="javascript:void(0)" title="登录" >登录</a>
	        </div>
	        
	        <?php elseif($this->domain_type == 'tuli'):?>
	        <div class="fright login" style="display:<?php echo ($this->isLogin ? 'none' : '')?>;" id="logout_header">
	        	<a class="fleft regbtn J_tuli_reg" href="javascript:void(0)" title="注册" >注册</a>
	            <a class="fleft logbtn J_tuli_login" href="javascript:void(0)" title="登录" >登录</a>
	        </div>
        <?php endif; ?>
    </div>
</div>
<!--.header-->

<?php echo $content; ?>

<!--.footer-->
<div class="w1000 mt50 mb20 footer">
	<div class="footer-t clearfix">
    	<dl>
            <dt>关于我们</dt>
            <dd><a  href="<?php $this->getTargetHref('index.php?r=public/aboutus&type=introduce')?>"  title="公司介绍" target="<?php echo $this->target?>">公司介绍</a></dd>
            <dd><a  href="<?php $this->getTargetHref('index.php?r=public/aboutus&type=contact')?>"  title="联系我们" target="<?php echo $this->target?>">联系我们</a></dd>
            <dd><a  href="<?php $this->getTargetHref('index.php?r=public/aboutus&type=cooperation')?>"  title="市场合作" target="<?php echo $this->target?>">市场合作</a></dd>
            <dd><a  href="<?php $this->getTargetHref('index.php?r=public/aboutus&type=join')?>"  title="人才招募" target="<?php echo $this->target?>">人才招募</a></dd>
        </dl>
        <dl>
            <dt>关注我们</dt>
            <dd><a href="<?php $this->getTargetHref('http://weibo.com/pipiletian')?>" title="新浪微博" target="<?php echo $this->target?>">新浪微博</a></dd>
            <dd><a href="<?php $this->getTargetHref('http://t.qq.com/pipixiuchang')?>" title="腾讯微博" target="<?php echo $this->target?>">腾讯微博</a></dd>
        </dl>
        <a class="logobtm" title="皮皮乐天"><img src="<?php echo $this->pipiFrontPath?>/fontimg/common/logobtm.png"></a>
        <dl>
            <dt>网站帮助</dt>
            <dd><a  href="<?php $this->getTargetHref($this->createUrl('public/help'))?>"  title="问题帮助" target="<?php echo $this->target?>">用户帮助</a></dd>
            <!--  
            <dd><a  href="<?php $this->getTargetHref('index.php?r=public/doteyHelp')?>"  title="主播帮助" target="<?php echo $this->target?>">主播帮助</a></dd>
            -->
        </dl>
        <dl>
            <dt>在线客服</dt>
            <dd>在线时段：10:30-2：00</dd>
            <?php 
            if($this->viewer['qqKeFu']):
            	foreach($this->viewer['qqKeFu'] as $key=>$kefuList):
            ?>
	            <dd class="clearfix"><span class="fleft"><?php echo $key?>:&nbsp;</span>
	            	 <?php foreach ($kefuList as $kefu): ?>
	            	 <a class="fleft pipionline" target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo $kefu['contact_account']?>&amp;site=qq&amp;menu=yes">
	            		 <img border="0" src="http://wpa.qq.com/pa?p=3:<?php echo $kefu['contact_account']?>:45" alt="<?php echo $kefu['contact_name']?>" title="<?php echo $kefu['contact_name']?>" style="vertical-align:middle;">
	            	  </a>  
	           		 <?php endforeach;?>
	            </dd>
    		<?php 
    			endforeach;
    		endif;?>
        </dl>
    </div>
    <p class="copyright"><span>信息网络传播视听节目许可证1109373号</span><span>增值电信业务经营许可证：浙B2-20070030</span><span>广播电视节目制作经营许可证（浙）字第548号</span><span>网络文化经营许可证文网文2010-191号</span></p>
	<p class="copyright"><span>互联网视听节目服务自律公约</span><span>浙江浩影网络有限公司</span><span>Copyright © 2006-2011 皮皮网 All Rights Reserved</span></p>
	<!-- 站点统计  -->
	<p style="display:none;"><?php echo $this->webSiteCount();?></p>
	<!-- 站点统计 -->
</div>
<!--.footer-->

<?php if($this->getId()=='archives'):?>
</div>
<div id="Livebg" class="livingbg filt"></div><!--.livingbg-->
<?php endif; ?>

<?php if($this->isPipiDomain): ?>
<div id="LoginMask">
    <div id="LoginBox">
        <div class="loginbox" id="loginController">
        	<div class="login-hd">
            	<ul class="clearfix">
                	<li>登录</li>
                    <li class="logincur">注册</li>
                </ul>
                <a title="关闭" class="closed"></a>
            </div>
            <div class="login-bd"> 
               <?php $this->renderPartial('application.views.user.login'); ?>
               <?php $this->renderPartial('application.views.user.register'); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
var curLoginController = 'register';
$(function(){
	//关闭登录弹出框
	$('.loginbox .closed').live('click',function(){
		 curLoginController = $('.login-hd ul li').first().hasClass('logincur') ? 'login' : 'register';
		 $.loginmask.hide('loginController');
		 //直播间注册弹出控制
		 if(page_controller == 'archives'){
			 doteyPrivateBox();
		 }
	});
	//弹出注册框
	$('.regbtn').live('click',function(){
		curLoginController = 'register';
		$("#form_register").resetForm();
		$.User.loginController('register');
	});
	//弹出登录框
	$('.logbtn').live('click',function(){
		curLoginController = 'login';
		$("#form_login").resetForm();
		$.User.loginController('login');
		
	});
	//切换登录与注册
	$('.login-hd ul li').live('click',function(){
		$(this).attr('class','logincur');
		var _this = this;
		$('.login-hd ul li').each(function(index,value){
			if(_this != this){
				$(this).attr('class','');
			}else{
				if(index == 0){
					curLoginController = 'login';
					$.User.loginController('login');
				}else{
					curLoginController = 'register';
					$.User.loginController('register');
				}
			}
		});
	});
	
	if($.User.getSingleAttribute('uid',true) <= 0){
		$.UserLogin.bindEvent();
		$.UserRegister.bindEvent();
	};
});
</script>
<?php 
	elseif($this->domain_type == 'pptv'):
	$this->renderPartial('application.views.user.pptv_sync_login');	 	
	endif; 
?>
<script type="text/javascript">
$(function(){
	//搜索隐藏
	$('#SearchBox').mouseleave(function(){
		$(this).find('.searchList').css('display','none');
	});	
	
	
	$('.searchList li').live({
    	mouseover:function(){
    	 	$(this).addClass('outon');
    	},
    	mouseout:function(){
      		$(this).removeClass('outon');
    	}
	});
	
});
</script>
<?php if($this->domain_type == 'tuli'):?>
<script type="text/javascript">
var exchangeUrl = "<?php echo $this->goExchange();?>";
var curLoginController = 'login';
var tuli_uinfo_url = "<?php echo $this->createUrl('tuli/getUserInfoFromTuli')?>";
var tuli_token = '<?php echo $this->token;?>';
$(function(){
	//页面高度自适应
	setTimeout("window.Tuli.send('setifm',{height:($(document).height())})",1000);
	setInterval("window.Tuli.send('setifm',{height:($(document).height())})",60000);
	
	$('.J_tuli_login').click(function(){
		var paramsObj = {'url':'<?php echo Yii::app()->request->hostInfo . Yii::app()->request->getUrl(); ?>'};
		window.Tuli.login(paramsObj);
	});
	$('.J_tuli_reg').click(function(){
		window.Tuli.register();
	});
	$('#logout').click(function(event){
		$.get("<?php echo $this->createUrl('tuli/logout')?>",'',function(e){
			if(e.result){
				window.Tuli.logout();
			}
		},'json');
		return false;
	});
	
	$('.J_tuli_pay').bind('click',function(){
		$.get(
				tuli_uinfo_url,
				{'token':tuli_token},
				function(e){
					if(e.data.user_type==1){
						window.Tuli.pay();
					}else{
						window.open(exchangeUrl,'_self');
					}
				},
				'json'
			);
	});
});
</script>
<?php endif;?>

<!-- 签到浮层开始 -->
<div id="SignFram" class="popbox signbox">
    <div class="poph">
        <span id="checkin_title">签到成功</span>
        <a title="关闭" class="closed" onClick="$.mask.hide('SignFram');"></a>
    </div>
    <div class="popcon clearfix">
        <p class="sucinfo sign-con"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/common/cao.jpg">三叶草已存放到背包中</p>
        <input class="surebtn" type="button" onClick="$.mask.hide('SignFram');" value="确&nbsp;&nbsp;&nbsp;&nbsp;定">
        <input class="fleft shiftbtn" style="display:none;" onClick="javascript:window.open('<?php $this->getTargetHref($this->createUrl('account/moon'))?>')" type="button" value="提前领取">
        <input class="fleft shiftbtn" style="display:none;" onClick="$.mask.hide('SignFram');" type="button" value="确定">
    </div>
    <?php 
	$monthCardSign = $this->getUserJsonAttribute('mc',true,true);
	if(empty($monthCardSign) || $monthCardSign['vt'] < time()):
	?>
    <div class="sign-btm clearfix">
        <p class="fleft signbtm-l">
            <img src="<?php echo $this->pipiFrontPath?>/fontimg/common/mouth-small.png">
            <a target="_blank" href="<?php $this->getTargetHref($this->createUrl('shop/monthcard'))?>">惠，签到礼物加倍拿！</a>
        </p>
        <?php if($this->isPipiDomain):?>
        <p href="#" class="fleft signbtm-r">
            <img src="<?php echo $this->pipiFrontPath?>/fontimg/common/eggicon.jpg">
            <a target="_blank" href="<?php $this->getTargetHref('http://app.offer99.com/index.php?pid=d37c6b09a49ed5d6ca1a52569a39073e&userid='.$this->getUserJsonAttribute('uid',true,true))?>">免费赚皮蛋</a>
        </p>
        <?php endif;?>
    </div>
    <?php 
    else:
    ?>
    <div class="sign-btm clearfix">
        <p class="fleft signbtm-l">
            <img src="<?php echo $this->pipiFrontPath?>/fontimg/common/mouth-small.png">
            <a target="_blank" href="<?php $this->getTargetHref($this->createUrl('account/moon'))?>">已持有特惠月卡</a>
        </p>
        <?php if($this->isPipiDomain):?>
        <p href="#" class="fleft signbtm-r">
            <img src="<?php echo $this->pipiFrontPath?>/fontimg/common/eggicon.jpg">
            <a target="_blank" href="<?php $this->getTargetHref('http://app.offer99.com/index.php?pid=d37c6b09a49ed5d6ca1a52569a39073e&userid='.$this->getUserJsonAttribute('uid',true,true))?>">免费赚皮蛋</a>
        </p>
        <?php endif;?>
    </div>
    <?php 
    endif;
    ?>
</div>
<!-- 签到浮层结束 -->

<div id="SetSuc" class="popbox">
	<div class="popcon noline"><a title="关闭" class="closed" onclick="$.mask.hide('SetSuc');"></a></div>
	<p class="oneline" id="oneline"></p>
	<p class="oneline"><input class="shiftbtn" type="button" onclick="$.mask.hide('SetSuc');" value="确&nbsp;&nbsp;定"></p>
</div>
<?php $this->renderPartial('application.views.user.star'); ?>

<?php $this->widget('lib.widgets.task.TaskWidget'); ?>


<script type="text/javascript">
$(function(){
	$.User.loginHtmlHeader();
	if($.User.getSingleAttribute('uid',true) > 0 && is_ajax_user_attribute == true){
		setInterval('$.User.refershWebLoginHeader();',1000*60*4);
	};
	if($.User.getSingleAttribute('uid',true) > 0){
		$('#make_pipieggs').attr('href','http://app.offer99.com/index.php?pid=d37c6b09a49ed5d6ca1a52569a39073e&userid='+$.User.getSingleAttribute('uid',true));
	}
	$("img").lazyload({
		effect : "fadeIn",
		failurelimit : 5
	});
	if($.User.getSingleAttribute('uid',true) <= 0){
		if(page_controller == 'archives' && $.cookie('archiveGuide') >= 1){
			setTimeout(function(){
				login_pop_type=true;
				$.User.loginController();
				},20000);
		}
	}
	if(account_bind == 1 && $.cookie('account_bind_close') == null){
		$('.toptip').css('display','');
	}

	$('.biao').hover(function(){
	    $(this).find('span').css('display','block');
	},function(){
	    $(this).find('span').css('display','none');
	});
	/**
   if($.User.getSingleAttribute('st',true) > 0){
   		$('#viewStar').css({'display':''});
   }else{
   		$('#viewStar').css({'display':'none'});
   }*/

   $('.newInfo .closed').click(function(){
		$('div .newInfo').hide();
	});

   //点击收藏的弹框关闭
   $('.biaotip .close').bind('click',function(){
       $(this).parent('.biaotip').css('display','none');
       $.cookie('indexGuide2',1,{expires: 365,path: '/',domain:cookie_domain});
       $('.sharetip').show();
   })
   var uid=$.User.getSingleAttribute('uid',true);
   if(uid > 0 && $.cookie('indexGuide2') < 1){
	   $('.biaotip').show();
   }
});

$('#viewStar').bind('click',function(){
		$.ajax({
			type: "POST",
			url: "index.php?r=user/viewStar",
			dataType: "json",
			async: false,
			success: function(response){
				if(response.status == 'success'){
					var timestamp=new Date().getTime()/1000;
					var startTime=new Date(response.message.startTime*1000);
					var endTime=new Date(response.message.endTime*1000);  
					var text='<p class="htline">你的星级是 <em class="pink">'+response.message.cst+'星</em>';
					if((timestamp>response.message.startTime&&timestamp<(parseInt(response.message.startTime)+7*86400))&&response.message.rst<response.message.cst){
						text+='，显示的是上周期星级</p>';
					}else{
						text+='，显示的是本周期星级</p>';
					}
					text+='<p class="htline">本周从<em class="pink">'+startTime.getFullYear()+'-'+(startTime.getMonth()+1)+'-'+startTime.getDate()+'</em>开始至<em class="pink">'+endTime.getFullYear()+'-'+(endTime.getMonth()+1)+'-'+endTime.getDate()+'</em>结束</p>';
					if(response.message.rst<10){
						text+='<p class="htline">本周期星级<em class="pink">'+response.message.rst+'星</em>，继续消费<em class="pink">'+response.message.npipiegg+'</em>可达<em class="pink">'+response.message.nst+'星</em></p>';
					}else{
						text+='<p class="htline">本周期星级已达到最高星级</p>';
					}
					if((timestamp>response.message.startTime&&timestamp<(parseInt(response.message.startTime)+7*86400))&&response.message.rst<response.message.cst){
						var showTime=new Date((parseInt(response.message.startTime)+7*86400)*1000);
						text+='<p class="htline"><em class="pink">'+showTime.getFullYear()+'-'+(showTime.getMonth()+1)+'-'+showTime.getDate()+'</em>开始你将显示为<em class="pink">'+response.message.rst+'星</em></p>';
					}else{
						var showTime=new Date((parseInt(response.message.endTime)+7*86400)*1000);
						text+='<p class="htline">你的<em class="pink">'+response.message.rst+'星</em>将显示至<em class="pink">'+showTime.getFullYear()+'-'+(showTime.getMonth()+1)+'-'+showTime.getDate()+'</em></p>';
					}
					$('#Star ul li').empty().html(text);	
					$('#Star').show();
				}else{
					alert(response.message);
					$('#Star').hide();
				}
			}
		}
		);
	

});
//收藏桌面
function desktop(){
	window.location.href="/index.php?r=index/desktop";
}

//推广注册
var sign=$.Global.getParam('sign');
var oper=$.Global.getParam('oper');
var tuliSign=$.Global.getParam('from');
sign=tuliSign?tuliSign:sign;

if(sign!='' && !$.cookie('reg_referer')){
	if(oper != ''){
		sign = sign+"_"+oper;
	}
	var reg_referer=encodeURIComponent(document.referrer);
	$.cookie('reg_sign',sign,{path: '/',domain:cookie_domain});
	$.cookie('reg_referer',reg_referer,{path: '/',domain:cookie_domain});
}

//头部广告
$('.topclosedBtn').bind('click',function(){
	$('#TopAd').css('display','none');
	return false;
});

var controll = /<?php echo Yii::app()->controller->id;?>\//gi;
var _controll = /<?php echo Yii::app()->controller->id;?>\%2\F/gi;

$('#Nav li').each(function(key,value){
	var liHref = $(this).find('a').attr('href');

	if('<?php echo Yii::app()->controller->id;?>'=='index' && '<?php echo $this->getAction()->getId();?>'=='index' ){
		if(key==0){
			$(this).addClass('navselect');
		}else{
			$(this).removeClass('navselect');
		}
	}else if(controll.test(liHref) || _controll.test(liHref)){
		$(this).addClass('navselect');
	}else{
		$(this).removeClass('navselect');
	}
	
});

$('.toptip').hover(function(){return false;});
$('.toptip em').bind('click',function(){
	var PipiDateObj = new Date();
	var clientTopTimeStamp = Math.ceil(PipiDateObj.getTime() / 1000);
	$.cookie('account_bind_close',1,{expires:0.5,path: '/',domain:cookie_domain});
    $('.toptip').css('display','none');
});

//左侧主播列表提示框
$('.juke-icon,.cakeIcon').live({
    mouseover:function(){
        $(this).find('em').css('display','block');
        $(this).parent('.anchor-head').addClass('z30');
    },
    mouseout:function(){
        $(this).find('em').css('display','none');
        $(this).parent('.anchor-head').removeClass('z30');
    }
});

//靓号悬停事件
$('#jpnumb').live({
    mouseover:function(){
      $(this).find('i').css('display','block');
      $(this).parents('li').addClass('z30');
    },
    mouseout:function(){
      $(this).find('i').css('display','none');
      $(this).parents('li').removeClass('z30');
    }
});

$('#jpnumb').live({
    mouseover:function(){
      $(this).find('span').css('display','block');
      $(this).parents('li').addClass('z30');
    },
    mouseout:function(){
      $(this).find('span').css('display','none');
      $(this).parents('li').removeClass('z30');
    }
});
var privateBox=false;
function doteyPrivateBox(){
	if($.User.getSingleAttribute('uid',true) <= 0){
		if(privateBox==false){
			pb=setInterval(function(){
				privateBox=true;
				$(".yourinfo").show();
			},20000);
		}else{
			clearInterval(pb);
		}
		
	}
}
function hidePrivateBox(){
	if(pb!=null&&pb!=undefined){
		clearInterval(pb);
	}
	pb=setInterval(function(){
		$(".yourinfo").show();
	},20000);
	$(".yourinfo").hide();
}
</script>
<!--[if IE 6]>
<style type="text/css">
html, html body{background-image:url(about:blank);background-attachment:fixed}
.reward,.broadContent{position:absolute;left:expression(eval(document.documentElement.scrollLeft+document.documentElement.clientWidth-this.offsetWidth)-(parseInt(this.currentStyle.marginLeft,10)||5)-(parseInt(this.currentStyle.marginRight,10)||5));top:expression(eval(document.documentElement.scrollTop+document.documentElement.clientHeight-this.offsetHeight-(parseInt(this.currentStyle.marginTop,10)||5)-(parseInt(this.currentStyle.marginBottom,10)||5)))}
#RewardShow,#AirBox{position:absolute;left:expression(eval(document.documentElement.scrollLeft+document.documentElement.clientWidth-this.offsetWidth)-(parseInt(this.currentStyle.marginLeft,10)||10)-(parseInt(this.currentStyle.marginRight,10)||0));top:expression(eval(document.documentElement.scrollTop+document.documentElement.clientHeight-this.offsetHeight-(parseInt(this.currentStyle.marginTop,10)||10)-(parseInt(this.currentStyle.marginBottom,10)||37)))}
#RewardShow{position:absolute;left:expression(eval(document.documentElement.scrollLeft+document.documentElement.clientWidth-this.offsetWidth)-(parseInt(this.currentStyle.marginLeft,10)||10)-(parseInt(this.currentStyle.marginRight,10)||0));top:expression(eval(document.documentElement.scrollTop+document.documentElement.clientHeight-this.offsetHeight-(parseInt(this.currentStyle.marginTop,10)||10)-(parseInt(this.currentStyle.marginBottom,10)||83)))}
</style>
<script type="text/javascript" src="<?php echo $this->pipiFrontPath?>/js/common/DD_belatedPNG.js"></script>
<script type="text/javascript">
DD_belatedPNG.fix(".login-list li a,.area-con dd.tip,.area-con dd.tipover,.seting-menu a,a.subbtn,.playing,.readying,.chaticon,.gifticon,.songicon,.gameicon,span.prev,span.next,.topflash-hd ul li,.topflash-hd ul li.on,.starttip,.tipcon,.juke-icon em,.cakeIcon em,.playing em,.readying em,.red-dot,.vocieicon,em.order,.face-good,.gemstone,.gemnum,.gem-text,.broadList li,.gift-menu a.close,.paternlistbox,.paternlist,.qipao,.firstcalltip,.calltipcon,.sharetip,.biaotip,.tasktip");
</script>
<![endif]-->

<?php /* 跟踪网民搜索行为，引导再次销售 */ ?>
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 980954121;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/980954121/?value=0&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

</body>
</html>