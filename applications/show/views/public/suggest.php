<style>
.feedback #raido_type input{width:auto;height:15px; color:#808080;}
.feedback p span{ display:inline-block; margin-left:0px; width:300px; color:gray; }
</style>
<div class="w1000 mt20 boxshadow clearfix">
	<form action='index.php?r=public/doSuggest' method='post' enctype="multipart/form-data" name="suggest_add" id="suggest_add" />
	<h1 class="faqhd">意见反馈</h1>
    <div class="feedback">
    	<p id="raido_type">
	  		<?php echo CHtml::radioButtonList('type', $type, $types,array('separator'=>'&nbsp;&nbsp;','container'=>''));?>
        </p>
       <p>
       <textarea name="content" onfocus="if(value=='亲爱的皮皮用户：描述您遇到的问题或者建议（500字以内）') {value=' '}"
        onBlur="if(value==' ') {value='亲爱的皮皮用户：描述您遇到的问题或者建议（500字以内）'}">亲爱的皮皮用户：描述您遇到的问题或者建议（500字以内）</textarea>
        </p>
       <p>
       		<input name="contact" id="name" type="text" value='填写手机/QQ联系方式' onFocus="if(this.value=='填写手机/QQ联系方式'){this.value=''; this.style.color='#000000';}" onBlur="if(this.value==''){this.value='填写手机/QQ联系方式'; this.style.color='#808080';}"> 
       </p>
       <p>
       		<a href="javascript:void(0)" class="cutpic">+添加截图</a>
       		<span>你可以用QQ截图保存，再添加到意见建议里。</span>
       		 <input id="fileBtn" class="file-btn" type="file" name="attach" style="display:none;"/>
       </p>
       <p>
	       <input type="submit" value="确认提交" title="确认提交" class="submit-pro"/> 
       </p>
    </div><!-- feedback -->
    </form>
</div>

<script type="text/javascript">

var options = {
   target: '',       //把服务器返回的内容放入id为output的元素中    
   beforeSubmit: showRequest,  //提交前的回调函数
   success: showResponse,      //提交后的回调函数
   //url: url,                 //默认是form的action， 如果申明，则会覆盖
   //type: type,               //默认是form的method（get or post），如果申明，则会覆盖
   dataType: 'json',         
   clearForm: false,            //成功提交后，清除所有表单元素的值
   resetForm: true,          //成功提交后，重置所有表单元素的值
   timeout: 3000               //限制请求的时间，当请求大于3秒后，跳出请求
}

$(function() {
	//添加截图功能
	$(".cutpic").click(function(){
		var a=$(this).siblings("span").remove();
		$("#fileBtn").show();
		$("#fileBtn").attr("value","");
	});
});
 
$("#suggest_add").submit(function(){
	if($.User.getSingleAttribute('uid',true) <= 0){
		$.User.loginController('login');
		return false;
	}
	$(this).ajaxSubmit(options);
	return false;  
			
});

/**
 * @param json formData: 数组对象，提交表单时，Form插件会以Ajax方式自动提交这些数据，格式如：[{name:user,value:val },{name:pwd,value:pwd}]
 * @param jquery 表单
 * @param options 选项
 */
function showRequest(formData, jqForm, options){
	var domForm = jqForm[0];

	if($.User.getSingleAttribute('uid',true) <= 0){
		$.User.loginController('login');
		return false;
	}
	
	if($.trim(domForm.content.value) == '' || domForm.content.value == '亲爱的皮皮用户：描述您遇到的问题或者建议（500字以内）'){
		$('#SetSuc').html('<p class="oneline">内容不能为空</p>');
		$.mask.show('SetSuc',1000);
		return false;
	}

	if($(domForm.content).val().length > 500){
		$('#SetSuc').html('<p class="oneline">您输入的内容不能超过500字符哦</p>');
		$.mask.show('SetSuc',1000);
		return false;
	}
	if($.trim(domForm.contact.value) == '' || domForm.contact.value == '填写手机/QQ联系方式'){
		$('#SetSuc').html('<p class="oneline">联系方式不能为空</p>');
		$.mask.show('SetSuc',1500);
		return false;
	}
};

function showResponse(responseText, statusText){
	if(statusText == 'success'){
		$('#SetSuc').html('<p class="oneline">'+responseText.message+'</p>');
		$.mask.show('SetSuc',1000);
		//location.reload();
		return false;
	}else{
		$('#SetSuc').html('<p class="oneline">'+statusText+'</p>');
		$.mask.show('SetSuc',1000);
		return false;
	}
};

</script>