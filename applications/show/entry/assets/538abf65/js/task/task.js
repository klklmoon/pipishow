(function($){
	$.task={
		click : new Array(),
		list_time : 0,
		
		init:function(){

		},
		//任务奖励页列表
		task : function(force){
			if(!force) force = false;
			if(force || !$("#RewardShow").html()){
				$.ajax({
					url : "index.php?r=task/taskList",
					type : "GET",
					dataType : "html",
					success : function(html){
						$("#RewardShow").empty().html(html);
						$("#NewReward").addClass('rewardover');
						$("#RewardShow").show();
				 	}
				});
				this.list_time = new Date().getTime();
			}else if(user_attribute.uid && this.list_time < (new Date().getTime() - 600*1000)){
				$.ajax({
					url : "index.php?r=task/taskList",
					type : "GET",
					dataType : "html",
					success : function(html){
						$("#RewardShow").empty().html(html);
						$("#NewReward").addClass('rewardover');
						$("#RewardShow").show();
				 	}
				});
				this.list_time = new Date().getTime();
			}else{
				if($('#RewardShow').css('display') == 'none'){
					$("#NewReward").addClass('rewardover');
					$("#RewardShow").show();
				}else{
					$.task.close();
				}
			}
		},
		close : function(){
			$("#RewardShow").hide();
			$("#NewReward").removeClass('rewardover'); 
		},
		//任务奖励页面
		dotask : function(tid){
			//领取奖励每一个任务三秒内只能点击一次，避免重复点击
			if(!this.click[tid] || this.click[tid] < new Date().getTime() - 3000){
				if(user_attribute.uid){
					$.ajax({
						url : "index.php?r=task/doTask",
						type : "GET",
						data:{'tid':tid},
						dataType : "json",
						success : function(json){
							if(json.status==1){
								$.task.task(true);
								alert("任务完成!");
							}else if(json.status==2){
								alert("任务暂停!");
							}else if(json.status==3){
								alert("任务没有完成!");                   
							}else if(json.status==4){
								alert("对不起,任务奖励不能重复领取!");
							}else if(json.status==5){
								alert("请先完成邮箱绑定或手机绑定任务，才能领取其他任务奖励!");
							}else if(json.status==-1){
								$.task.task();
								$.task.login();
							}else{
								alert("系统出错，请与管理员联系!");
							}
					 	 }
					});
				}else{
					$.task.task();
					$.task.login();
				}
				this.click[tid] = new Date().getTime();
			}
		},
		login : function(){
			if(!user_attribute.uid){
				curLoginController = 'login';
				$("#form_login").resetForm();
				$.User.loginController('login');
			}
		},
		register : function(){
			if(!user_attribute.uid){
				curLoginController = 'register';
				$("#form_register").resetForm();
				$.User.loginController('register');
			}
		}
	}
})(jQuery);