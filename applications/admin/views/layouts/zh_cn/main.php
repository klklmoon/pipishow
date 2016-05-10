<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>乐天后台管理</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="乐天后台管理">
	<meta name="author" content="Muhammad Usman">
	<!-- The styles -->
	<style type="text/css">
	  body {
		padding-bottom: 40px;
	  }
	  .sidebar-nav {
		padding: 9px 0;
	  }
	</style>
	<!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script type="text/javascript">
		var assertCssPath = "<?php echo $this->cssAssetsPath;?>";
	</script>
</head>

<body>
	<!-- topbar starts -->
	<div class="navbar">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".top-nav.nav-collapse,.sidebar-nav.nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="#"> <img alt="乐天" src="<?php echo $this->cssAssetsPath;?>/img/logo20.png" /> <span>乐天后台管理</span></a>
				
				<!-- theme selector starts -->
				<div class="btn-group pull-right theme-container" >
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="icon-tint"></i><span class="hidden-phone">修改皮肤</span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu" id="themes">
						<li><a data-value="classic" href="#"><i class="icon-blank"></i> Classic</a></li>
						<li><a data-value="cerulean" href="#"><i class="icon-blank"></i> Cerulean</a></li>
						<li><a data-value="cyborg" href="#"><i class="icon-blank"></i> Cyborg</a></li>
						<li><a data-value="redy" href="#"><i class="icon-blank"></i> Redy</a></li>
						<li><a data-value="journal" href="#"><i class="icon-blank"></i> Journal</a></li>
						<li><a data-value="simplex" href="#"><i class="icon-blank"></i> Simplex</a></li>
						<li><a data-value="slate" href="#"><i class="icon-blank"></i> Slate</a></li>
						<li><a data-value="spacelab" href="#"><i class="icon-blank"></i> Spacelab</a></li>
						<li><a data-value="united" href="#"><i class="icon-blank"></i> United</a></li>
					</ul>
				</div>
				<!-- theme selector ends -->
				
				<!-- user dropdown starts -->
				<div class="btn-group pull-right" >
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="icon-user"></i><span class="hidden-phone"> <?php echo Yii::app()->user->getName();?></span>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="#">Profile</a></li>
						<li class="divider"></li>
						<li><a href="<?php echo $this->createUrl('User/logout');?>">Logout</a></li>
					</ul>
				</div>
				<!-- user dropdown ends -->
				
				<div class="top-nav nav-collapse">
					<ul class="nav">
						<li><a href="#">Visit Site</a></li>
						<li>
							<form class="navbar-search pull-left">
								<input placeholder="Search" class="search-query span2" name="query" type="text">
							</form>
						</li>
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</div>
	</div>
	<!-- topbar ends -->
	<div class="container-fluid">
		<div class="row-fluid">
			<!-- left menu starts -->
			<div class="span2 main-menu-span">
				<div class="well nav-collapse sidebar-nav">
					<ul class="nav nav-tabs nav-stacked main-menu">
						<li class="nav-header hidden-tablet">日常操作管理</li>
						<?php if (isset($this->menuTree )){?>
						<?php foreach ($this->menuTree as $label => $subMenu){?>
							<li>
								<a class="ajax-link" href="javascript:;">
									<span class="hidden-tablet"><?php echo $label;?></span> <i class="icon-chevron-down" style="float:right;"></i>
								</a>
							</li>
							<?php if(is_array($subMenu)){?>
							<li class="tree" style="display:none;">
							<ul class="nav nav-tabs nav-stacked main-menu" style="margin-bottom:1px">
							<?php foreach($subMenu as $sublabel=>$menu){?>
								<li style="width:80%;margin-left:10%">
									<a class="ajax-link" href="<?php echo $this->createUrl($menu);?>">
										<i class="icon-chevron-right"></i><?php echo $sublabel;?>
									</a>
								</li>
							<?php }?>
							</ul>
							</li>
							<?php }?>
						<?php }?>
						<?php }?>
					</ul>
					<!-- <label id="for-is-ajax" class="hidden-tablet" for="is-ajax" style="display:block;"><input id="is-ajax" type="checkbox" checked="checked" selected="selected"> Ajax on menu</label> -->
				</div><!--/.well -->
			</div><!--/span-->
			<!-- left menu ends -->
			
			<div id="content" class="span10">
			<!-- content starts -->
				<div>
				<!-- 面包屑 -->
				<?php 
					if (isset($this->breadcrumbs)){
						$this->widget('zii.widgets.CBreadcrumbs',array(
								'links' => $this->breadcrumbs,
								'homeLink'=>CHtml::link('<i class="icon-home"></i>首页',Yii::app()->homeUrl),
								'activeLinkTemplate' =>"<li><a href='{url}'>{label}</a></li>",
								'inactiveLinkTemplate' => "<li><span>{label}</span></li>",
								'separator' => '<span class="divider">/</span>',
								'tagName' => 'ul',
								'htmlOptions' => array('class' => 'breadcrumb'),
							));
					} 
					?>
				</div>
				
				<?php echo $content;?>
				<!-- content ends -->
			</div><!--/#content.span10-->
		</div><!--/fluid-row-->
		
		<!-- 页脚 -->
		<hr>
		<footer>
			<p class="pull-left">&copy; <a href="http://show.pipi.cn" target="_blank">皮皮乐天</a> 2013</p>
			<p class="pull-right">Powered by: <a href="http://show.pipi.cn">皮皮乐天</a></p>
		</footer>
	</div><!--/.fluid-container-->
</body>
</html>