var Purview={
		 arr : {rank:0,purviewrank:0,nickname:'',uid:0,monthcard:0,label:0,num:'',tip:'',medal:''},
		 medal:{},
		 medalPath : '/uploadimg/medal/',
		_forip : 1,//是否被禁ip
		_forcommon : 1,//是否被禁言用户名及昵称
		_forkick:1,   //是否被踢
		_forall:1,    //是否被全局禁言
		_formanage:1,  //是否能设房管
		_forbid_user :[],//被禁言的用户
		_forbidIp_user :[],//被禁ip的用户
		_forbid_uid :[],//被禁uid的用户
		_myPurviewRank : 0,//自己所在房间拥有的权限等级
		_myRank : 0,//自己的用户等级
		_period: 30,//禁言时间
		_position : {top:0,left:0},//点击后所在的定位
		_showPosition:1,
		_sheildRank:7, //屏蔽他人发言等级要求
		_sheildList:[], //被屏蔽的人
		_splitRank : function(id){
			if(id && id.search(/\|\*\|/i)>0){
				var id_arr=id.split("|*|");
				this.arr.nickname=id_arr[1];
				this.arr.uid=(id_arr[0]==null||id_arr[0]=='')?'0':id_arr[0];//用户id
				this.arr.purviewrank=id_arr[3];
				this.arr.rank=id_arr[2];
				this.arr.label=id_arr[4];
				this.arr.monthcard=id_arr[5];
				this.arr.num=id_arr[6];
				this.arr.tip=id_arr[7]
				if(id_arr.length>7){
					var medal=new Array();
					for(i=0;i<(id_arr.length-8);i++){
						medal[i]=id_arr[i+8];
					}
					this.arr.medal=medal;
				}else{
					this.arr.medal='';
				}
				
				if(this.arr.uid>0){
					Chat.chatObj=this.arr;
					$("#ChatObj").val(this.arr.nickname);
					//Gift.doteyId=this.arr.uid;
					//Gift.doteyNickname=this.arr.nickname;
					//$("#GiveNameText").val(this.arr.nickname);
				}
			}
		},
		getPurHtml:function(){
			var text='<span class="cus-name clearfix">';
			text+='<em class="pink ellipsis">'+this.arr.nickname+'</em>';
			text+=' <a href="javascript:void(0);" id="con-close" onclick=\'$.mask.hide("purviewList")\'>关闭</a></span>';
			if(this.arr.num!=null&&this.arr.num!=''){
				text+='<p class="cus-id clearfix"><span id="prettyNum" class="fleft  '+$.User.getNumCss(this.arr.num)+'"><em>靓</em>'+this.arr.num;
				if(this.arr.tip!=null&&this.arr.tip!=undefined){
					text+='<span class="tipcon" style="display: none;">'+this.arr.tip+'</span>';
				}
				text+='</span>';
			}else{
				text+='<p class="cus-id clearfix"><em>ID:'+this.arr.uid+'</em>';
			}
			if(this.arr.monthcard!=null&&typeof(this.arr.monthcard)&&this.arr.monthcard==1){
				text+='<img src="'+Chat.staticPath+'/fontimg/common/mouth-small.png">';
			}
			text+='</p>';
			if(this.arr.medal.length>0){
				text+='<p class="cus-honor">';
				for(i=0;i<this.arr.medal.length;i++){
					if(this.arr.medal[i]!=null&&typeof(this.arr.medal[i])&&this.arr.medal[i]!=''){
						text+='<img class="medal" src="'+archives.imgSite+this.arr.medal[i]+'"/>';
					}
				}
				text+='</p>';
			}
			text+='<ul class="cus-control clearfix">';
			var myhtml=new Array(6);
			myhtml[0]='<li class="cusion give-gift"><a href="javascript:Gift.selectUser();">赠送礼物</a></li>';
			myhtml[1]='<li class="cusion pub-chat"><a href="javascript:Purview.Chat();">对TA公聊</a></li>';
			myhtml[2]='<li><a href="javascript:UserList.showLabel();">给TA贴条</a></li>';
			myhtml[3]='<li><a href="javascript:UserList.removeLabel();">揭除贴条</a></li>';
			myhtml[4]='<li><a href="javascript:Purview.privateChat();">对TA私聊</a></li>';
			myhtml[5]='<li class="cusion shield-speak"><a href="javascript:Purview.shieldChat();">屏蔽发言</a></li>';
			myhtml[6]='<li class="cusion shield-speak"><a href="javascript:Purview.recoverChat()">解除屏蔽</a></li>';
			myhtml[7]='<li class="famlink"><a href="javascript:Purview.showFamily(\'famlink\');">家族主页</a></li>';
			//陈列的权限列表
			var listhtml='';
			if(this.arr.uid>0){
				listhtml+=myhtml[0];
			}
			listhtml+=myhtml[1];
			if(this.arr.uid>0){
				listhtml+=myhtml[2]
				if(this.arr.label!=null&&typeof(this.arr.label)&&this.arr.label==1){
					listhtml+=myhtml[3];
				}
				listhtml+=myhtml[4];
			}
			
			
			if(this.arr.uid>0&&(Chat.show_rank>=this._sheildRank||Chat.show_purviewrank>1)){
				var sheildList=$.parseJSON($.cookie('sheildList'));
				if(sheildList){
					if(this.in_array(this.arr.uid,sheildList)){
						listhtml+=myhtml[6];
					}else{
						listhtml+=myhtml[5];
					}
				}else{
					listhtml+=myhtml[6];
				}

			}
			if(this.arr.uid>0){
				listhtml+=myhtml[7];
			}
			listhtml+='</ul>';
			this._showPosition=1;
			if(this._myPurviewRank>=1&&Chat.show_uid!=this.arr.uid){
				Purview.sendCode(9);
			}else{
				$("#purviewList").css({'top' : this._position.top, 'left' : this._position.left,'z-index':9999}).html(text+listhtml).show();
			}
//			switch (this._myPurviewRank){
//				case 2:
//					//首先判断两者的等级
//					//if(this.checkRank(3)){//禁言对应的判断操作
//						//同步获取 不然取不到值
//
//						Purview.sendCode(9);//获取服务端用户状态 type=9
//					//}else{
//						//$("#cuspopmenu").css({'top' : this._position.top, 'left' : this._position.left,'z-index':9999}).html(listhtml).show();
//					//}
//					break;
//				case 3:
//					//在自己房间才有一下管理项
//					if(Chat.show_uid == archives.dotey.uid){
//						Purview.sendCode(9);//获取服务端用户状态 type=9
//					}else{
//						$("#purviewList").css({'top' : this._position.top, 'left' : this._position.left,'z-index':9999}).html(listhtml).show();
//					}
//					break;
//				case 4:
//					Purview.sendCode(9);
//					break;
//				default:
//					$("#purviewList").css({'top' : this._position.top, 'left' : this._position.left,'z-index':9999}).html(listhtml).show();
//				    break;
//			}
		},
		//判断等级及操作等级 然后再显示操作选项
		checkRank : function(type){

			switch (type){
				case 2,3://禁止游客发言

					if(parseInt(this._myRank)>parseInt(this.arr.rank-2) && this._myPurviewRank>1 && this._myPurviewRank > this.arr.purviewrank){
						return true;
					}else{
						return false;
					}
					break;
				case 8://将用户踢出房间
					if(parseInt(this._myRank)>parseInt(this.arr.rank-3) && this._myPurviewRank>1 && this._myPurviewRank > this.arr.purviewrank){
						return true;
					}else{
						return false;
					}
				case 6:
					if(this._myPurviewRank>1 && this._myPurviewRank > this.arr.purviewrank){
						return true;
					}else{
						return false;
					}
				default :
					break;
			}
		},
		Chat : function(){
			$("#purviewList").hide();
			$("#cuspopmenu").hide();
			$("#privateSet").removeAttr('checked');
			Chat.commonChat(this.arr);
		},
		privateChat:function(){
			$("#purviewList").hide();
			$("#cuspopmenu").hide();
			if(Chat.show_rank<1&&Chat.show_purviewrank<1){
				$("#SucMove .popcon").empty().html('<p class="oneline">普通等级用户才能发送私聊</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}else{
				$("#purviewList").hide();
				$("#privateSet").attr('checked',true);
				Chat.privateChat(this.arr);
			}
			
		},
		chatRepair:function(){
			if($.common.show_uid != uid){
				alert("对不起，您在此房间没有该权限");return;
			}
			if(archives_id==''){
				alert("对不起，获取房间错误");return;
			}
			$.ajax({
				type:"GET",
				url:"/dotey/modifyArchive",
				data:{archives_id:archives_id},
				dataTye:"json",
				success:function(data){
					data=eval("("+data+")");
					if(data.flag==1){
						var serverId=GlobalSet.serverId;
						getSocket();
						//alert(socketIp+port+policyPort);
						getSocketSdk().removeSocketServer(serverId);
						getSocketSdk().addSocketServer(serverId,socketIp,port,policyPort);
						alert(data.message);
						location.href=data.url;
					}else{
						alert(data.message);
					}
				}
			})

		},
		setPurview : function(){
			if(Chat.show_uid != archives.dotey.uid){
				$("#SucMove .popcon").empty().html('<p class="oneline">对不起，您在此房间没有该权限</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
//			if(this._myPurviewRank <= this.arr.purviewrank){
//				$("#SetSuc .popcon").empty().html('<p class="oneline">对不起，您不能对该用户进行此操作</p>');
//				$.mask.show('SetSuc',3000);
//				return;
//			}
//			if(this.arr.purviewrank==2){
//				$("#SetSuc .popcon").empty().html('<p class="oneline">该用户已经是房间管理员!</p>');
//				$.mask.show('SetSuc',3000);
//				return;
//			}
			//成功返回后再进行操作。移到后端
			$.ajax({
				url:"index.php?r=purview/setPurview",
				data:{uid:this.arr.uid,nickname:this.arr.nickname,archives_id:archives.archives_id},
				type: "POST",
				dataType: "json",
				success:function(c){
					if(c){
						$("#SucMove .popcon").empty().html('<p class="oneline">'+c.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
					}
				}
			});
		},
		cancelPurview : function(){
//			if(this._myPurviewRank >=3){
//				$("#SetSuc .popcon").empty().html('<p class="oneline">对不起，您不能对该用户进行此操作!</p>');
//				$.mask.show('SetSuc',3000);
//				return;
//			}
//			if(this.arr.purviewrank!=2){
//				$("#SetSuc .popcon").empty().html('<p class="oneline">该用户房间管理员身份已解除!</p>');
//				$.mask.show('SetSuc',3000);
//				return;
//			}
			$.ajax({
				url:"index.php?r=purview/removePurview",
				data:{uid:this.arr.uid,nickname:this.arr.nickname,archives_id:archives.archives_id},
				type: "POST",
				dataType: "json",
				success:function(c){
					if(c){
						$("#SucMove .popcon").empty().html('<p class="oneline">'+c.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
					}
				}
			});
		},
		//对相应ip进行禁言
		ForbidIp : function(){
			if(!Chat.show_uid || !Chat.show_nickname){
				$("#SucMove .popcon").empty().html('<p class="oneline">对不起，您没有禁言权限！</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			if(this._myPurviewRank <= this.arr.purviewrank){
				$("#SucMove .popcon").empty().html('<p class="oneline">对不起，您没有禁言权限！</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			this.sendCode(1);//发送消息给该用户
			$("#purviewList").empty();

		},
		cancelForbidIp : function(){
			if(!Chat.show_uid || !Chat.show_nickname){
				$("#SucMove .popcon").empty().html('<p class="oneline">对不起，您没有禁言权限！</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			if(this._myPurviewRank <= this.arr.purviewrank){
				$("#SucMove .popcon").empty().html('<p class="oneline">对不起，您不能对该用户进行此操作</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			this.sendCode(0);//发送消息给该用户
			$("#purviewList").empty();
		},

		//禁言游客
		Forbidyk :  function(){
			if(this.arr.uid!=0){
				alert("对不起，请对游客用户进行操作！");return;
			}
			if(!Chat.show_uid || !Chat.show_nickname){
				alert("对不起，您没有禁言权限！");return;
			}
			if(this._myPurviewRank <= this.arr.purviewrank){
				alert("对不起，您不能对该用户进行此操作");return;
			}
			$("#purviewList").empty();
			this.sendCode(3);

		},
		cancelForbidyk :  function(){
			$("#purviewList").empty();
			this.sendCode(2);
		},

		//禁言uid
		ForbidUid :  function(){
			if(Chat.show_uid == this.arr.uid){
				$("#SucMove .popcon").empty().html('<p class="oneline">对不起，不能对自己进行本操作！</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}

			if(this._myPurviewRank < 4 && this.arr.uid==archives.dotey.uid){
				$("#SucMove .popcon").empty().html('<p class="oneline">对不起，您不能对主播禁言</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}

			this.sendCode(5);
			$("#purviewList").empty();
		},
		cancelForbidUid :  function(){
			this.sendCode(4);
			$("#purviewList").empty();
		},
		//全局禁言
		ForbidFull : function(){
			if(Chat.show_uid == this.arr.uid){
				$("#SucMove .popcon").empty().html('<p class="oneline">对不起，不能对自己进行本操作！</p>');
				$.mask.show('SucMove',3000);
				return;
			}
			this.sendCode(10);
			$("#purviewList").empty();
		},
		//取消全局禁言
		cancelForbidFull : function(){
			this.sendCode(11);
			$("#purviewList").empty();
		},
		//踢出房间
		kickOut : function(){
			if(confirm('是否将'+this.arr.nickname+'踢出房间')){
				this.sendCode(8);
				$("#purviewList").empty();
			}
			return;

		},
		//屏蔽他人发言
		shieldChat:function(){
			var obj=this;
			$.ajax({
				url:"index.php?r=purview/shieldChat",
				type:"POST",
				data:{uid:this.arr.uid,archives_id:archives.archives_id},
				dataType:"json",
				success:function(data){
					if(data){
						$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
					}
				}
			})

		},
		recoverChat:function(){
			var obj=this;
			$.ajax({
				url:"index.php?r=purview/recoverChat",
				type:"POST",
				data:{uid:this.arr.uid,archives_id:archives.archives_id},
				dataType:"json",
				success:function(data){
					if(data.flag==1){
						$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
					}
				}
			})

		},
		//发送用户监测信令
		sendCode : function(type){
			var char_arr=new Array();
			char_arr[0]=Chat.show_uid;
			char_arr[1]=Chat.show_nickname;
			char_arr[2]=this.arr.uid;
			char_arr[3]=this.arr.nickname;
			char_arr[4]=type;
			if(type==1||type==8){
				char_arr[5]=120;
			}else{
				char_arr[5]=this._period;
			}
			//将内容写入页面
			if(type==9) {
				getSocketSdk().sendSocketData(103,char_arr,archives.chatServer.serverId);
			} else {
				$.ajax({
					type:"POST",
					url:"index.php?r=/purview/forbiden",
					data:{archives_id:archives.archives_id,to_uid:this.arr.uid,to_nickname:this.arr.nickname,type:type,period:char_arr[5]},
					dataType:"json",
					success:function(data){
						var flag = data.flag;
						if(data){
							if(data.flag==1){
								if(type==8){
									if(this.arr.uid==Chat.show_uid&&this.arr.nickname==Chat.show_nickname){
										this.onlock();
									}
								}
							}
							$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
							$.mask.show('SucMove',3000);
						}

					}
				});
			}
		},
		
		onlock:function(){
			document.getElementById("CPlayer").lockRecord();
		},
		unlock:function(){
			document.getElementById("CPlayer").unlockRecord();
		},
		getForbidStatus : function(data){
			if(data.length){
				this._forip=data[0];
				this._forcommon=data[1];
				this._forkick=data[2];
				this._forall=data[3];
				this._formanage=data[4]
			}
			var myhtml=new Array(15);
			myhtml[0]='<li class="cusion give-gift"><a href="javascript:Gift.selectUser();">赠送礼物</a></li>';
			myhtml[1]='<li class="cusion pub-chat"><a href="javascript:Purview.Chat();">对TA公聊</a></li>';
			myhtml[2]='<li><a href="javascript:UserList.showLabel();">给TA贴条</a></li>';
			myhtml[3]='<li><a href="javascript:UserList.removeLabel();">揭除贴条</a></li>';
			myhtml[4]='<li><a href="javascript:Purview.privateChat();">对TA私聊</a></li>';
			myhtml[5]='<li class="cusion ban-speak"><a href="javascript:Purview.ForbidUid()">禁止发言</a></li>';
			myhtml[6]='<li class="cusion ban-speak"><a href="javascript:Purview.cancelForbidUid()">解除禁止</a></li>';
			myhtml[7]='<li class="cusion shield-speak"><a href="javascript:Purview.shieldChat()">屏蔽发言</a></li>';
			myhtml[8]='<li class="cusion shield-speak"><a href="javascript:Purview.recoverChat()">解除屏蔽</a></li>';
			myhtml[9]='<li class="cusion ip-speak"><a href="javascript:Purview.ForbidIp()">将IP禁言</a></li>';
			myhtml[10]='<li class="cusion ip-speak"><a href="javascript:Purview.cancelForbidIp()">解除IP</a></li>';
			myhtml[11]='<li class="cusion t-room"><a href="javascript:Purview.kickOut()">踢出房间</a></li>';
			myhtml[12]='<li><a href="javascript:Purview.ForbidFull(120)">全局禁言</a></li>';
			myhtml[13]='<li><a href="javascript:Purview.cancelForbidFull()">全局解禁</a></li>';
			myhtml[14]='<li class="cusion admin-room"><a href="javascript:Purview.setPurview()">升为房管</a></li>';
			myhtml[15]='<li class="cusion admin-room"><a href="javascript:Purview.cancelPurview()">解除房管</a></li>';
			myhtml[16]='<li class="famlink"><a href="javascript:Purview.showFamily(\'famlink\');">家族主页</a></li>';
			
			//陈列的权限列表
			var listhtml='';
			if(this.arr.uid>0){
				listhtml+=myhtml[0]
			}
			listhtml+=myhtml[1];
			if(this.arr.uid>0){
				listhtml+=myhtml[2];
				if(this.arr.label!=null&&typeof(this.arr.label)&&this.arr.label==1){
					listhtml+=myhtml[3];
				}
				listhtml+=myhtml[4];
			}
			
			
			if(this._forcommon>0){
				if(this._forcommon==1){
					listhtml+=myhtml[5];
				}
				if(this._forcommon==2){
					listhtml+=myhtml[6];
				}

			}
			if(this.arr.uid>0&&(Chat.show_rank>=this._sheildRank||Chat.show_purviewrank>1)){
				var sheildList=$.parseJSON($.cookie('sheildList'));
				if(sheildList){
					if(this.in_array(this.arr.uid,sheildList)){
						listhtml+=myhtml[8];
					}else{
						listhtml+=myhtml[7];
					}
				}else{
					listhtml+=myhtml[7];
				}

			}
			if(this._forip>0){
				if(this._forip==1){
					listhtml+=myhtml[9];
				}
				if(this._forip==2){
					listhtml+=myhtml[10];
				}
			}
			if(this._forkick>0){
				if(this._forkick==1){
					listhtml+=myhtml[11];
				}
			}
			if(this._forall>0){
				if(this._forall==1){
					listhtml+=myhtml[12];
				}
				if(this._forall==2){
					listhtml+=myhtml[13];
				}
			}
			if(this._formanage>0){
				if(this._formanage==1){
					listhtml+=myhtml[14];
				}
				if(this._formanage==2){
					listhtml+=myhtml[15];
				}
			}
			if(this.arr.uid>0){
				listhtml += myhtml[16];
			}
			var text='<span class="cus-name clearfix">';
			text+='<em class="pink ellipsis">'+this.arr.nickname+'</em>';
			if(this._forall==2||this._forip==2||this._forcommon==2){
				text+='<img src="'+Chat.staticPath+'/fontimg/common/feng.png">';
			}
			if(this._showPosition==1){
				text+='<a href="javascript:void(0);" id="con-close" onclick=\'$.mask.hide("purviewList")\'>关闭</a></span>';
			}else{
				text+='<a href="javascript:void(0);" id="con-close" onclick=\'$.mask.hide("cuspopmenu")\'>关闭</a></span>';
			}
			
			if(this.arr.num!=null&&this.arr.num!=''){
				text+='<p class="cus-id clearfix"><span  id="prettyNum" class="fleft  '+$.User.getNumCss(this.arr.num)+'"><em>靓</em>'+this.arr.num;
				if(this.arr.tip!=null&&this.arr.tip!=undefined){
					text+='<span class="tipcon" style="display: none;">'+this.arr.tip+'</span>';
				}
				text+='</span>';
			}else{
				text+='<p class="cus-id clearfix"><em>ID:'+this.arr.uid+'</em>';
			}
			if(this.arr.monthcard!=null&&typeof(this.arr.monthcard)&&this.arr.monthcard==1){
				text+='<img src="'+Chat.staticPath+'/fontimg/common/mouth-small.png">';
			}
			text+='</p>';
			if(this.arr.medal.length>0){
				text+='<p class="cus-honor">';
				for(i=0;i<this.arr.medal.length;i++){
					if(this.arr.medal[i]!=null&&typeof(this.arr.medal[i])&&this.arr.medal[i]!=''){
						text+='<img class="medal" src="'+archives.imgSite+this.arr.medal[i]+'"/>';
					}
				}
				text+='</p>';
			}
			text+='<ul class="cus-control clearfix">';
			text+=listhtml+'</ul>';
			
			if(this._showPosition==1){
				$("#purviewList").css({'top' : this._position.top,'left':this._position.left}).empty().html(text).show();
			}
			
			if(this._showPosition==2){
				$("#cuspopmenu").css({'top' : this._position.top,'left':this._position.left-60}).empty().html(text).show();
			}

			//return listhtml;
		},
		//判断在数组中是否含有给定的一个变量值
		in_array:function(target,arr){
			var type=typeof target;
			if(type=='string'||type=='number'){
				for(i=0;i<arr.length;i++){
					if(arr[i]==target){
						return true;
					}
				}
			}else{
				return false;
			}
		},
		//好友列表tab
		getUserHtml : function(){
			this._showPosition=2;
			var text='<span class="cus-name clearfix">';
			text+='<em class="pink ellipsis">'+this.arr.nickname+'</em>';
			text+=' <a href="javascript:void(0);" id="con-close" onclick=\'$.mask.hide("cuspopmenu")\'>关闭</a></span>';
			if(this.arr.num!=null&&this.arr.num!=''){
				text+='<p class="cus-id clearfix"><span  id="prettyNum" class="fleft  '+$.User.getNumCss(this.arr.num)+'"><em>靓</em>'+this.arr.num;
				if(this.arr.tip!=null&&this.arr.tip!=undefined){
					text+='<span class="tipcon" style="display: none;">'+this.arr.tip+'</span>';
				}
				text+='</span>';
			}else{
				text+='<p class="cus-id clearfix"><em>ID:'+this.arr.uid+'</em>';
			}
			if(this.arr.monthcard!=null&&typeof(this.arr.monthcard)&&this.arr.monthcard==1){
				text+='<img src="'+Chat.staticPath+'/fontimg/common/mouth-small.png">';
			}
			text+='</p>';
			if(this.arr.medal.length>0){
				text+='<p class="cus-honor">';
				for(i=0;i<this.arr.medal.length;i++){
					if(this.arr.medal[i]!=null&&typeof(this.arr.medal[i])&&this.arr.medal[i]!=''){
						text+='<img class="medal" src="'+archives.imgSite+this.arr.medal[i]+'"/>';
					}
				}
				text+='</p>';
			}
			text+='<ul class="cus-control clearfix">';
			var myhtml=new Array(6);
			myhtml[0]='<li class="cusion give-gift"><a href="javascript:Gift.selectUser();">赠送礼物</a></li>';
			myhtml[1]='<li class="cusion pub-chat"><a href="javascript:Purview.Chat();">对TA公聊</a></li>';
			myhtml[2]='<li><a href="javascript:UserList.showLabel();">给TA贴条</a></li>';
			myhtml[3]='<li><a href="javascript:UserList.removeLabel();">揭除贴条</a></li>';
			myhtml[4]='<li><a href="javascript:Purview.privateChat();">对TA私聊</a></li>';
			myhtml[5]='<li class="cusion shield-speak"><a href="javascript:Purview.shieldChat();">屏蔽发言</a></li>';
			myhtml[6]='<li class="cusion shield-speak"><a href="javascript:Purview.recoverChat()">解除屏蔽</a></li>';
			myhtml[7]='<li class="famlink"><a href="javascript:Purview.showFamily(\'famlink\')">家族主页</a></li>';
			
			//陈列的权限列表
			var listhtml='';
			if(this.arr.uid>0){
				listhtml+=myhtml[0];
			}
			listhtml+=myhtml[1];
			if(this.arr.uid>0){
				listhtml+=myhtml[2];
				if(this.arr.label!=null&&typeof(this.arr.label)&&this.arr.label==1){
					listhtml+=myhtml[3];
				}
				listhtml+=myhtml[4];
			}
			
			
			if(this.arr.uid>0&&(Chat.show_rank>=this._sheildRank||Chat.show_purviewrank>1)){
				var sheildList=$.parseJSON($.cookie('sheildList'));
				if(sheildList){
					if(this.in_array(this.arr.uid,sheildList)){
						listhtml+=myhtml[6];
					}else{
						listhtml+=myhtml[5];
					}
				}else{
					listhtml+=myhtml[6];
				}

			}
			if(this.arr.uid>0){
				listhtml+=myhtml[7];
			}
			listhtml+='</ul>';
			if(this._myPurviewRank>=1&&Chat.show_uid!=this.arr.uid&&this.arr.uid>0){
				Purview.sendCode(9);
			}else{
				$("#cuspopmenu").css({'top' : this._position.top,'left':this._position.left-60}).empty().html(text+listhtml).show();
			}
		},
		showFamily : function(obj){
			if(this.arr.uid > 0 ){
				$.ajax({
					type:"POST",
					url:"index.php?r=/family/getMyFamily",
					data:{uid:this.arr.uid},
					dataType:"json",
					success:function(data){
						var str = '<dl class="famhome clearfix">';
						if(data.status){
							if(data.data.length > 0){
								for(var i in data.data){
									str += '<dd><a class="ellipsis" href="'+data.data[i].url+'" target="_black">'+data.data[i].name+'</a>'+(data.data[i].medal == '' ? '' : '<span><img style="margin-bottom:-4px;" src="'+data.data[i].medal+'" /></span>')+'</dd>';
								}
							}else{
								str += '<dd>暂无家族</dd>';
							}
						}else{
							str =+ '<dd>'+data.message[0]+'</dd>';
						}
						str += '</dl>';
						$('.cuspopmenu dl').remove();
						$('.cuspopmenu ul').after(str);
					}
				});
			}
		}
}
