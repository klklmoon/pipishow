<style type="text/css">
.errorbox{ padding-top:170px; padding-bottom:170px;}
.errorbox-bd{width:700px; margin:0 auto;}
.errorbox-bd dt{ margin-right:20px;}
.errorbox-bd dt,.errorbox-bd dd{float:left;}
.errortext{font-size:30px; color:#ff008a; font-family:"Microsoft YaHei","\5FAE\8F6F\96C5\9ED1";width:80%}
.back{font-size:20px;font-family:"Microsoft YaHei","\5FAE\8F6F\96C5\9ED1";}
</style>
<div class="w1000 errorbox">
	<dl class="errorbox-bd clearfix">
    	<dt><Img src="<?php echo $this->pipiFrontPath?>/fontimg/common/404bg.jpg"></dt>
        <dd class="errortext"><?php echo $errorMsg?></dd>
        <dd class="errortext"><a class="back" href="javascript:void(0)" title="返回前页" onclick="history.go(-2);">返回前页</a></dd>
    </dl>
</div>