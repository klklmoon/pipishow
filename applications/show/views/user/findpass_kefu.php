<div class="clearfix w1000 mt30">
    
    <div class="main" style="width:1000px;">
        <ul class="main-menu clearfix" id="MianList" style="width:960px;">
            <li class="menuvisted"><a href="#">找回密码</a></li>
        </ul><!-- .main-menu -->
        
       <div id="MainCon">
           <div class="cooper-list">
               <div class="sendbj" style=" width:580px;">您的账号没有绑定手机和邮箱，或者忘记了绑定手机和邮箱，存在很大的安全隐患</div>
               <p>
                 <span class="fleft">客服申诉QQ &nbsp;</span>
				 <a class="fleft" href="http://wpa.qq.com/msgrd?v=3&uin=800070126&site=qq&menu=yes" target="_blank">
					<img border="0" style="vertical-align:middle;" title="艾乐" alt="艾乐" src="http://wpa.qq.com/pa?p=3:800070126:45">
				 </a>
               </p>
               <br/>
           </div>
           <div class="cooper-list onhide">
           </div>
      </div>
     </div>   
</div>
<script type="text/javascript">
function findPass(type){
	$.ajax({
		type:"POST",
		url:"index.php?r=user/find",
		data:{type:type},
		dataType:"json",
		async: false, 
		success:function(response){
			if(response.status == 'fail'){
				alert(response.message);
			}else{
				window.location = "<?php echo $this->createUrl('user/findPass&step=mail') ?>";
			}
		}
	});
	return false;
}

</script>