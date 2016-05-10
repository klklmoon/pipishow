<script type="text/javascript" >
var WEB_GID='pipi';
function check_pptv(data){
	var uid = $.User.getSingleAttribute('uid',true);
	var pptv_msg= eval(data);
	if(pptv_msg.state==1 && uid <= 0){
		var userobj= eval(pptv_msg.result);
		var referrer = encodeURIComponent(document.referrer);
		//alert("{&DOMAIN_HREF}/pptvapi/pptv_login?username="+userobj.username+"&tm="+pptv_msg.tm+"&token="+pptv_msg.token);
		window.location.href="<?php echo Yii::app()->params['main_url']?>/index.php?r=pptv/pptvLogin&username="+userobj.username+"&tm="+pptv_msg.tm+"&token="+pptv_msg.token+"&goto="+referrer;
	}else{ 
		if(pptv_msg.state==0 && uid > 0){
			window.location.href="<?php echo $this->createUrl('pptv/logout');?>";
		}
	}
}
</script>
<script type="text/javascript" src="<?php echo Yii::app()->params['pptv']['auth_url']?>?gid=pipi&action=login&cb=check_pptv"></script>