<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>登录</title>
<style type="text/css">
body, form, div, span, li, ul, p { margin:0; padding:0; }
body { background-image:url(<?php echo Yii::app()->request->getBaseUrl();?>/statics/images/login/ehr_03.gif); font-size:12px; color:#FFF; margin:0 auto; }
ul { list-style-type:none }
.center { margin:17px auto 0; background:url(<?php echo Yii::app()->request->getBaseUrl();?>/statics/images/login/ehr_04.gif) scroll no-repeat 1px 0; width:620px; height:550px; }
.form { padding-top:271px; padding-left:336px; line-height:24px; }
.inputTxt { background-color:#d9d8d8; border: #798fac 1px solid; height:23px; border:0; width:180px; padding:0 0 0 2px }
.form ul li { line-height:23px; margin-bottom:12px; }
.but { padding-left:64px; padding-top:2px; padding-right:15px }
.butt { background:url(<?php echo Yii::app()->request->getBaseUrl();?>/statics/images/login/ehr_05.gif); width:47px; height:27px; border:0; margin:0 20px 0 0; cursor:pointer }
#checking { width:80px;}
</style>
</head>

<body>
<form id="login-form" method="post">
  <div class="center">
  <div class="form">
  <ul>
  <li><span style="padding-right:16px;">用 户：</span>
    <input type="text" name="LoginForm[username]" id="username"  class="inputTxt" maxlength="20" />
</li>
<li><span style="padding-right:16px;">密 码：</span>
  <input type="password" name="LoginForm[password]" id="password" class="inputTxt" maxlength="20" />
</li><span style="padding-right:7px;">验证码：</span>
  <div style="float:right; margin-right:60px"><?php $this->widget('CCaptcha',array('showRefreshButton'=>false,'clickableImage'=>true,'imageOptions'=>array('alt'=>'点击换图','title'=>'点击换图','style'=>'cursor:pointer;height:25px;'))); ?></div><input type="text" name="LoginForm[validatecode]" id="checking" class="inputTxt" />
  
<li>
</li>
</ul>
<div class="but">
  <input class="butt" type="submit" value="登录" />
  <input class="butt" type="button" value="退出" />
</div>
</div>
</div>
</form>
</body>
</html>