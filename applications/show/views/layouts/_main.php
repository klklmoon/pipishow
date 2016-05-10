<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $this->getPageTitle()?></title>
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
</script>
</head>
<body>
<?php if($this->getId()=='archives'):?>
<div class="gotopbox">
  <ul>
    <li class="service">
      <div class="servicebox">
       <div class="chargebox">
       <?php if($this->viewer['qqKeFu']):
            	foreach($this->viewer['qqKeFu'] as $key=>$kefuList):
            		if($kefuList):?>
       <p> <em class="fleft"><?php echo $key;?>：</em> 
	       <?php foreach ($kefuList as $kefu): ?>
	       <a class="fleft"  target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo $kefu['contact_account']?>&amp;site=qq&amp;menu=yes"> <img border="0" src="http://wpa.qq.com/pa?p=3:<?php echo $kefu['contact_account']?>:45" alt="<?php echo $kefu['contact_name']?>" title="<?php echo $kefu['contact_name']?>" style="vertical-align:middle;"></a>
	       <?php endforeach;?>
        </p>
        <?php endif;?>
        <?php endforeach;?>
        <?php endif;?>
      </div>
      <!--<div class="qq-qun">官方-玩家体验交流群 <a target="_blank" href="http://shang.qq.com/wpa/qunwpa?idkey=5333c250e200841ea4e1edff8ffe849f51d26e7ae1ad58c6105486660fac7b14"><img border="0" src="http://pub.idqqimg.com/wpa/images/group.png" alt="官方-玩家体验交流群" title="官方-玩家体验交流群"></a></div>  -->
    </div>
    </li>
    <li class="feedback"><a href="<?php echo $this->getTargetHref($this->createUrl('public/suggest'));?>" target="<?php echo $this->target?>"></a></li> 
     <!--<li class="gotop"><a id="gotop" href="javascript:void(0)"></a></li>-->
  </ul>
</div>
<?php endif;?>
<?php if($this->viewer['topHeadBanner']):?>
<a <?php if($this->viewer['topHeadBanner']['textlink']){echo 'href="'.$this->viewer['topHeadBanner']['textlink'].'"';}?> target="<?php echo $this->viewer['topHeadBanner']['target'];?>" id="TopAd" class="topad" title="<?php echo $this->viewer['topHeadBanner']['subject']; ?>" style="background: url(<?php echo $this->viewer['topHeadBanner']['piclink']; ?>) no-repeat center top">
    <span class="topclosed clearfix"><em title="关闭" class="topclosedBtn"></em></span>
</a><!--.topad-->
<?php endif;?>

<div class="header">
    <div class="w1000 header-con clearfix">
       <?php if($this->isPipiDomain):?>
        <a href="<?php $this->getTargetHref($this->createUrl('index/index'))?>" target="_self" class="fleft logo" title="小V秀场"><img src="<?php echo $this->pipiFrontPath?>/fontimg/common/logo.png"></a>
        <ul class="fleft menu">
            <li><a <?php if($this->getId()=='index'):?>class="on"<?php endif;?> href="<?php $this->getTargetHref($this->createUrl('index/index'))?>" target="_self">首页</a></li>
            <li>
                <a <?php if($this->getId()=='top'):?>class="on"<?php endif;?> href="<?php $this->getTargetHref('index.php?r=top/index')?>" target="_self">排行榜<!--<i class="ico"></i>--></a>
                <!--<dl class="submenu">
                    <dd><a href="#">个人排行</a></dd>
                    <dd><a href="#">家族排行</a></dd>
                </dl>-->
            </li>
            <?php if(FamilyService::familyEnable()):?>
            <li><a <?php if($this->getId()=='family'):?>class="on"<?php endif;?> href="<?php $this->getTargetHref('index.php?r=family/index')?>" target="_self">家族<!--<i class="ico"></i>--></a></li>
            <?php endif;?>
            <li><a <?php if($this->getId()=='shop'):?>class="on"<?php endif;?> href="<?php $this->getTargetHref('index.php?r=shop/gift')?>" target="_self">商城</a></li>
       		<?php foreach($this->viewer['topNavigate'] as $tNavigate):?>
      		<?php if($this->domain_type != 'pptv' || !preg_match('/apply/', $tNavigate['link'])) : ?>
      		<li><a <?php if(strpos($tNavigate['link'],$this->getId())==true):?>class="on"<?php endif;?> href="<?php $this->getTargetHref($tNavigate['link'])?>" title="<?php echo $tNavigate['name']?>" target="<?php echo $this->target?>"><?php echo $tNavigate['name']?></a></li>
      		<?php endif;?>
      		<?php endforeach;?>
        </ul><!--.menu-->
        <?php elseif($this->domain_type == 'pptv'):?>
        <a href="<?php $this->getTargetHref($this->createUrl('index/index'))?>" target="<?php echo $this->target?>" class="fleft logo" title="小V秀场"><img src="<?php echo $this->pipiFrontPath?>/fontimg/common/logo.png"></a>
        <ul class="fleft menu">
            <li><a <?php if($this->getId()=='index'):?>class="on"<?php endif;?> href="<?php $this->getTargetHref($this->createUrl('index/index'))?>" target="<?php echo $this->target?>">首页</a></li>
            <li>
                <a <?php if($this->getId()=='top'):?>class="on"<?php endif;?> href="<?php $this->getTargetHref('index.php?r=top/index')?>" target="<?php echo $this->target?>">排行榜<!--<i class="ico"></i>--></a>
                <!--<dl class="submenu">
                    <dd><a href="#" target="<?php echo $this->target?>">个人排行</a></dd>
                    <dd><a href="#" target="<?php echo $this->target?>">家族排行</a></dd>
                </dl>-->
            </li>
            <?php if(FamilyService::familyEnable()):?>
            <li><a <?php if($this->getId()=='family'):?>class="on"<?php endif;?> href="<?php $this->getTargetHref('index.php?r=family/index')?>" target="<?php echo $this->target?>">家族<!--<i class="ico"></i>--></a></li>
            <?php endif;?>
            <li><a <?php if($this->getId()=='shop'):?>class="on"<?php endif;?> href="<?php $this->getTargetHref('index.php?r=shop/gift')?>" target="<?php echo $this->target?>">商城</a></li>
        	<?php foreach($this->viewer['topNavigate'] as $tNavigate):?>
      		<?php if($this->domain_type != 'pptv' || !preg_match('/apply/', $tNavigate['link'])) : ?>
      		<li><a <?php if(strpos($tNavigate['link'],$this->getId())==true):?>class="on"<?php endif;?> href="<?php $this->getTargetHref($tNavigate['link'])?>" title="<?php echo $tNavigate['name']?>" target="<?php echo $this->target?>"><?php echo $tNavigate['name']?></a></li>
      		<?php endif;?>
      		<?php endforeach;?>
        </ul><!--.menu-->
        <?php else: ?>
        <a href="<?php $this->getTargetHref($this->createUrl('index/index'))?>" target="_blank" class="fleft logo" title="小V秀场"><img src="<?php echo $this->pipiFrontPath?>/fontimg/common/logo.png"></a>
        <ul class="fleft menu">
            <li><a <?php if($this->getId()=='index'):?>class="on"<?php endif;?> href="<?php $this->getTargetHref($this->createUrl('index/index'))?>" target="_blank">首页</a></li>
            <li>
                <a <?php if($this->getId()=='top'):?>class="on"<?php endif;?> href="<?php $this->getTargetHref('index.php?r=top/index')?>" target="_blank">排行榜<!--<i class="ico"></i>--></a>
               <!-- <dl class="submenu">
                    <dd><a href="#" target="_blank">个人排行</a></dd>
                    <dd><a href="#" target="_blank">家族排行</a></dd>
                </dl>-->
            </li>
            <?php if(FamilyService::familyEnable()):?>
            <li><a <?php if($this->getId()=='family'):?>class="on"<?php endif;?> href="<?php $this->getTargetHref('index.php?r=family/index')?>" target="_blank">家族<!--<i class="ico"></i>--></a></li>
            <?php endif;?>
            <li><a <?php if($this->getId()=='shop'):?>class="on"<?php endif;?> href="<?php $this->getTargetHref('index.php?r=shop/gift')?>" target="_blank">商城</a></li>
        	<?php foreach($this->viewer['topNavigate'] as $tNavigate):?>
      		<?php if($this->domain_type != 'pptv' || !preg_match('/apply/', $tNavigate['link'])) : ?>
      		<li><a <?php if(strpos($tNavigate['link'],$this->getId())==true):?>class="on"<?php endif;?> href="<?php $this->getTargetHref($tNavigate['link'])?>" title="<?php echo $tNavigate['name']?>" target="<?php echo $this->target?>"><?php echo $tNavigate['name']?></a></li>
      		<?php endif;?>
      		<?php endforeach;?>
        </ul><!--.menu-->
        <?php endif;?>
       	<!--用户未登录-->
        <div class="fright notlog"  <?php if($this->isLogin):?>style="display:none;"<?php endif;?>>
            <p>hi,欢迎光临！</p>
            <?php if($this->isPipiDomain):?>
            <div class="inlet"><a href="javascript:void(0);" onclick="$.User.loginController('register');return false" title="注册">注册</a>&nbsp;&nbsp;<i>&#124</i>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="$.User.loginController('login');return false" title="登录">登录</a></div>
            <a href="<?php $this->getTargetHref('index.php?r=user/openLogin&type=qq')?>" target="<?php echo $this->target?>" class="qq-inlet">&nbsp;登&nbsp;录</a>
        	<?php elseif($this->domain_type == 'pptv'):?>
        	<div class="inlet"><a href="javascript:void(0)" class="J_pptv_reg" title="注册">注册</a>&nbsp;&nbsp;<i>&#124</i>&nbsp;&nbsp;<a href="javascript:void(0)" class="J_pptv_login" title="登录">登录</a></div>
            <?php elseif($this->domain_type == 'tuli'):?>
            <div class="inlet"><a href="javascript:void(0)" class="J_tuli_reg" title="注册">注册</a>&nbsp;&nbsp;<i>&#124</i>&nbsp;&nbsp;<a href="javascript:void(0)" class="J_tuli_login" title="登录">登录</a></div>
       		<?php endif;?>
        </div>
        <?php $this->renderPartial('application.views.public.login'); ?>
        <?php $this->renderPartial('application.views.public.login_head'); ?>
        <div class="fright search">
            <input type="text" value="主播昵称或ID" id="SearchText">
            <a class="searchbtn" href="javascript:void(0);" title="搜索"></a>
            <ul class="search-con"></ul>
        </div><!--.search-->
    </div>
</div><!--.header-->
<?php echo $content;?>
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
<div class="footer">
	<div class="w1000">
		<div class="footer-hd">
			<a class="fleft logo-btm" href="#"><img src="<?php echo $this->pipiFrontPath?>/fontimg/common/logobtm.png"></a>
			<dl class="fleft aboutus">
				<dt>关于我们</dt>
				<dd>
					<a href="<?php $this->getTargetHref($this->createUrl('public/aboutus&type=introduce'))?>"  title="公司介绍" target="<?php echo $this->target?>">公司介绍</a>
					<a href="<?php $this->getTargetHref($this->createUrl('public/aboutus&type=cooperation'))?>"  title="市场合作" target="<?php echo $this->target?>">市场合作</a>
					<a href="<?php $this->getTargetHref($this->createUrl('public/aboutus&type=contact'))?>"  title="联系我们" target="<?php echo $this->target?>">联系我们</a>
					<a href="<?php $this->getTargetHref($this->createUrl('public/aboutus&type=join'))?>"  title="人才招募" target="<?php echo $this->target?>">人才招聘</a>
					<a href="<?php $this->getTargetHref('http://weibo.com/pipiletian')?>" title="新浪微博" target="<?php echo $this->target?>">新浪微博</a>
					<a href="<?php $this->getTargetHref('http://t.qq.com/pipixiuchang')?>" title="腾讯微博" target="<?php echo $this->target?>">腾讯微博</a>
				</dd>
			</dl>
			<dl class="fleft cus-service">
				<dt>客户服务</dt>
				<dd class="cusbtn">
					<a class="online" href="<?php $this->getTargetHref('http://wpa.qq.com/msgrd?v=3&uin=800070126&site=qq&menu=yes')?>" target="<?php echo $this->target?>">在线客服帮忙</a>
					<a class="suge" href="<?php $this->getTargetHref($this->createUrl('public/suggest'))?>"  title="公司介绍" target="<?php echo $this->target?>">提建议/投诉</a>
				</dd>
				<dd class="helplink">
					<a href="<?php $this->getTargetHref($this->createUrl('public/help'))?>"  title="用户帮助" target="<?php echo $this->target?>">用户帮助</a>
					<a href="<?php $this->getTargetHref($this->createUrl('public/doteyHelp'))?>"  title="主播帮助" target="<?php echo $this->target?>">主播帮助</a>
				</dd>
			</dl>
		</div>
		<div class="footer-bd">
			<p>信息网络传播视听节目许可证1109373号  增值电信业务经营许可证：浙B2-20070030  广播电视节目制作经营许可证（浙）字第548号   网络文化经营许可证文网文2010-191号</p>
			<p>互联网视听节目服务自律公约    浙江浩影网络有限公司   Copyright © 2006-2011 皮皮网 All Rights Reserved</p>
		</div>
	</div>
</div><!--.footer-->
<?php if($this->getId()=='archives'):?>
</div>
<div id="Livebg" class="livingbg filt"></div><!--.livingbg-->
<?php endif; ?>
<!-- 签到浮层开始 -->
<div id="SignFram" class="popbox signbox">
    <div class="poph">
        <a title="关闭" class="closed" onClick="$.mask.hide('SignFram');"></a>
    </div>
    <div class="popcon clearfix"></div>
    <div class="sign-btm clearfix">
        <p class="fleft signbtm-l">
            <img src="<?php echo $this->pipiFrontPath?>/fontimg/common/mouth-small.png">
            <a target="_blank" href="<?php $this->getTargetHref($this->createUrl('shop/monthcard'))?>">惠，签到礼物加倍拿！</a>
        </p>
        <?php if($this->isPipiDomain):?>
        <p class="fleft signbtm-r">
            <img src="<?php echo $this->pipiFrontPath?>/fontimg/common/eggicon.jpg">
            <a target="_blank" href="<?php $this->getTargetHref('http://app.offer99.com/index.php?pid=d37c6b09a49ed5d6ca1a52569a39073e&userid='.$this->getUserJsonAttribute('uid',true,true))?>">免费赚皮蛋</a>
        </p>
        <?php endif;?>
    </div>
</div>
<!-- 签到浮层结束 -->

<div id="SetSuc" class="popbox">
	<div class="popcon noline"><a title="关闭" class="closed" onclick="$.mask.hide('SetSuc');"></a></div>
	<p class="oneline" id="oneline"></p>
	<p class="oneline"><input class="shiftbtn" type="button" onclick="$.mask.hide('SetSuc');" value="确&nbsp;&nbsp;定"></p>
</div>
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
<script>
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
var pb;
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
<script type="text/javascript" src="<?php echo $this->pipiFrontPath?>/js/common/DD_belatedPNG.js"></script>
<script type="text/javascript">
DD_belatedPNG.fix(".DD_belapng,a.subbtn,.chaticon,.gifticon,.songicon,.gameicon,span.prev,span.next,.topflash-hd ul li,.topflash-hd ul li.on,.vocieicon,em.order,.face-good,.gemstone,.gemnum,.gem-text,.broadList li,.gift-menu a.close,.paternlistbox,.paternlist,.qipao,.firstcalltip,.calltipcon,.sharetip,.tasktip,.logo img,.menu li a .ico,.notlog .inlet,.notlog .qq-inlet,.search,.submenu,.logmenu-box li a,.editbtn,.lvls,.editbox,.num,.icont,.playmask,.viewnum");
</script>
<![endif]-->
</body>
</html>