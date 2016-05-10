var game_room_id = 1;

function getSocketSdk(){
	return getSwf("swfframe").movie();
}
function socketInit(){
	getSocketSdk().addSocketServer(archives.chatServer.serverId,archives.chatServer.socketIp,archives.chatServer.port,archives.chatServer.policyPort);
}
function removeSocketServer(id){
	return id;
}
function socketErrorMessage(id,mess){
		alert(id+':'+mess);

}


function addSocketServerComplete(id){
	var rank=0;
	socketServer=true;
	$("#sendChatBoton,#sendMsg").removeAttr("disabled");
	if($.User.getSingleAttribute('uid',true)>0){
		var uid=$.User.getSingleAttribute('uid',true);
		var token=archives.token
		//Chat.addUserList();
	}else{
		var uid=0;//为空时重置为0
		var token=$.cookie("temp_nickname");
	}
	var domain=window.location.host;
	//管理账号与用户权限账号一起 百位 为标示位 十位为管理位 各位为用户位 100 则代表不是房管 为普通用户 游客为0
	var data_arr=[archives.archives_id,domain,uid,token];
	var serverId=archives.chatServer.serverId;

	getSocketSdk().sendSocketData(101,data_arr,serverId);


}
function socketDisconnect(id){
	socketServer=false;
	if(id===archives.chatServer.serverId){
		$("#sendChatBoton,#sendMsg,#sendfagift").attr("disabled","disabled","disabled");
		$("#sendfagift").attr("title","与聊天室断开,请刷新页面");
		$("#sendfagift").attr("onclick","#");
	}
	$("#commonChat").append("<p>与服务器断开，您可能已经在另外的窗口打开了本直播间;或者您的网络有问题请刷新重试</p>");
}
function receiveSocketData(id,code,data_arr){
    /*
     * 1102 聊天，1108 发送礼物 ，1618 用户列表   1107 禁言响应
     * 1103 禁言状态 1616 欢迎消息
     */
	switch(code){
		case 1102 : Chat.getChatMess(data_arr);break;
		case 1108 : Gift.getGiftMess(data_arr);break;
		case 1618 : /*Chat.getUserList(data_arr);*/break;
		case 1107 : Chat.insertForbidMess(data_arr);break;
		case 1103 : Purview.getForbidStatus(data_arr);break;
		case 1616 : Chat.insetWelMess(data_arr);break;
		default : return;
	}
}



var sendTime=3;
var countdown;
function SetRemainTime() {
	Chat._isSendMsg=false;
	$("#sendChatBotton").removeClass("submitbtn");
	//$("#sendChatBotton").attr("onclick","#");
	$("#sendChatBotton").addClass("count");
	$("#sendChatBotton").text(sendTime+"秒");
	if(sendTime==0){
		clearInterval(countdown);
		//$("#sendChatBotton").attr("onclick","Chat.sendChat()");
		$("#sendChatBotton").removeClass("count");
		$("#sendChatBotton").addClass("submitbtn");
		$("#sendChatBotton").text("发言");
		Chat._isSendMsg=true;
		sendTime=3;
	}
	sendTime--;
}


var Chat={
		staticPath:archives.staticPath,
		sendBotton:'sendChatBotton',     //发送聊天按钮id
		commonChatArea:'commonChat',     //公聊区域id
		privateChatArea:'privateChat',   //私聊区域id
		sendChatArea:'msg_input',        //发送聊天内容区域Id
		privateSet:'privateSet',         //私聊勾选按钮Id
		randomLength : '6',
		show_uid:0,
		show_nickname:'',
		show_rank:0,
		show_purviewrank:0,
		temp_nickname:'',
		chatObj : {uid:0,nickname:'',rank:0,purviewrank:0},
		arr : {rank:0,purviewrank:0,nickname:'',uid:0},
		_sheildRank:11,                //屏蔽他人发言等级要求
		isScrollOn : true,             //公聊滚动条
		isScrollOnp : true,		       //私聊滚动条
		tourist_set:false,//游客禁言
		global_set:false,//全局发言
		forbidTimeOut : 0,//禁言取消时间
		forbidFullTimeOut : 0,//全局禁言时间
		chatL_0:8,  //游客
		chatL_1:20, //0=<rank<2
		chatL_2 :50,//2=<rank<7
		chatL_3:100,//7=<rank<15
		chatL_4:250,//15=<rank<23
		chatL_5:500,//rank>=23
		_Lnum : 50,//用户列表首次载入人数
		_maxNum : 500,//最多加载人数
		UserNum:0,//用户数量
		_UserNum:0,//真实用户数量
		ManageNum:0,  //管理数量
		growth:50, //用户增长量
		kickOut:false,//是否被踢
		crownUser:{},//皇冠用户
		loadmore : false,
		userLoad:false,
		clickNum:0,
		_isSendMsg:true, //是否发送聊天信息
		pageSize:10,
		_qFun : [],//处理函数队列
		_q : [],//礼物数据队列
		_qCar : [],
		_qCarFun : [],
		_qFly : [],//飞屏数据队列
		_qFlyFun : [],//飞屏函数队列
		_bFun:[],  //广播消息函数队列
		_b:[],    //广播消息队列
		msgNum:1,
		countdown:null,
		init : function(){
			addSwf('swfframe',this.staticPath+'/swf/archives/socketc.swf?version=10',true,false,'1','1','9','#000000','allowScriptAccess=always');
			var obj=this;
			obj.show_uid=$.User.getSingleAttribute('uid',true);
			
			if((!obj.show_uid && !obj.temp_nickname)){
				obj.temp_nickname='游客'+this.randomChar(this.randomLength);
				$.cookie("temp_nickname",obj.temp_nickname);
				obj.show_nickname=obj.temp_nickname;
			}else{
				if(obj.kickout){
					Purview.onlock();
					return;
				}
				obj.show_nickname=$.User.getSingleAttribute('nk',true);
				obj.show_rank=$.User.getSingleAttribute('rk',true);
				obj.show_purviewrank=obj.getUserPurviewRank($.User.getSingleAttribute('pk',true));
			}
			this.tourist_set=archives.chatSet.tourist_set;
			this.global_set=archives.chatSet.global_set;
			this.crownUser=archives.crown.uid;
			if(obj.show_uid>0){
				$("#exchange").attr('href',exchangeUrl);
			}
			obj.initFunction();
		},
		islogin : function (){
			e = this.show_uid;
			if(!e || e=='undefined'){
				return false;
			}else{
				return true;
			}
		},
		randomChar:function (n){
			 var x="0123456789";
			 var tmp="";
			 for(var i=0;i< n;i++) {
			 tmp += x.charAt(Math.ceil(Math.random()*100000000)%x.length);
			 }
			 return tmp;
		},
		showLoginDiv:function(){
			$.mask.hide('SucMove');
			$.User.loginController('login');
		},
		sendChat:function(){
			if(this._isSendMsg==false) return false;
			if(this.kickOut==true){
				$("#SucMove .popcon").empty().html('<p class="oneline">您已被踢出房间</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			if(this.global_set==true&&this.show_purviewrank<=1&&$("#"+this.privateSet).attr('checked')!='checked'){
				$("#SucMove .popcon").empty().html('<p class="oneline">主播已关闭公聊频道发言，请使用私聊发言</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return false;
			}
			if(this.tourist_set==true&&this.show_uid<=0&&$("#"+this.privateSet).attr('checked')!='checked'){
				$("#SucMove .popcon").empty().html('<p class="oneline">直播间暂不允许游客发言，与主播交流，请注册登录</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove');
				setTimeout('Chat.showLoginDiv()',3000);
				return false;
			}
			//是否被禁言
			this.forbidTimeOut=$.cookie("forbidchat_"+archives.archives_id+'_'+this.show_uid);
			this.forbidFullTimeOut=$.cookie("forbidFullchat");
			if(new Date().getTime()<this.forbidFullTimeOut*1000 && this.forbidFullTimeOut){
				$("#SucMove .popcon").empty().html('<p class="ontline">您已被全局禁言，120分钟内无法在任何直播间内发言。</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return false;
			}else if(this.forbidFullTimeOut){
				$.cookie("forbidFullchat", null);
			}
			if(this.forbidTimeOut&&this.forbidTimeOut==this.show_uid){
				$("#SucMove .popcon").empty().html('<p class="oneline">您已经被禁言</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return false;
			}else if(this.forbidTimeOut){
				$.cookie("forbidchat_"+archives.archives_id, null);
			}
			var content=$("#"+this.sendChatArea).val();
			if(!$.trim(content)){
				$("#SucMove .popcon").empty().html('<p class="oneline">请填写内容</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return false;
			}
			if(this.show_uid<=0 && this.strLen(content)>this.chatL_0*2){
				$("#SucMove .popcon").empty().html('<p class="oneline">游客发言限'+this.chatL_0+'字以内，请注册后再发</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return false;
			}
			//各个等级对应的字符数
			if(this.show_purviewrank!=3){
				if(this.show_rank>=0&&this.show_rank<2){
					if(this.strLen(content)>this.chatL_1*2){
						$("#SucMove .popcon").empty().html('<p class="oneline">不能超过'+this.chatL_1+'字</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
						return false;
					}
				}else if(this.show_rank>=2&&this.show_rank<7){
					if(this.strLen(content)>this.chatL_2*2){
						$("#SucMove .popcon").empty().html('<p class="oneline">不能超过'+this.chatL_2+'字</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
						return false;
					}
				}else if(this.show_rank>=7&&this.show_rank<15){
					if(this.strLen(content)>this.chatL_3*2){
						$("#SucMove .popcon").empty().html('<p class="oneline">不能超过'+this.chatL_3+'字</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
						return false;
					}
				}else if(this.show_rank>=15&&this.show_rank<23){
					if(this.strLen(content)>this.chatL_4*2){
						$("#SucMove .popcon").empty().html('<p class="oneline">不能超过'+this.chatL_4+'字</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
						return false;
					}
				}else{
					if(this.strLen(content)>this.chatL_5*2){
						$("#SucMove .popcon").empty().html('<p class="oneline">不能超过'+this.chatL_5+'字</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
						return false;
					}
				}
			}else{
				if(this.strLen(content)>this.chatL_5*2){
					$("#SucMove .popcon").empty().html('<p class="oneline">不能超过'+this.chatL_5+'字符</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
					$.mask.show('SucMove',3000);
					return false;
				}
			}
			if($("#"+this.privateSet).attr('checked')=='checked'){
				if(Chat.show_uid<=0||(Chat.show_rank<8&&Chat.show_purviewrank<3)){
					$("#SucMove .popcon").empty().html('<p class="oneline">富豪等级玩家（累计贡献值达到10000）才能发送私聊</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
					$.mask.show('SucMove',3000);
					return false;
				}
				if(Chat.show_uid>0&&this.chatObj.uid>0){
					var type='private';
				}else{
					var type='common';
				}

			}else{
				var type='common';
			}
			var char_arr=new Array();
			char_arr[0]=archives.archives_id;
			char_arr[1]=window.location.host;
			char_arr[2]=this.show_uid;
			char_arr[3]=this.show_nickname;
			char_arr[4]=this.chatObj.uid;
			char_arr[5]=this.chatObj.nickname;
			char_arr[6]=content;
			char_arr[7]=type;
			var serverId=archives.chatServer.serverId;
			getSocketSdk().sendSocketData(102,char_arr,serverId);
			$("#"+this.sendChatArea).val('').focus();
			this._isSendMsg=false;
			//3秒倒计时
			countdown = setInterval("SetRemainTime()", 1000);
			//$("#"+this.sendBotton).removeAttr("onclick");
			//setTimeout(this.SetRemainTime, 3000); //间隔函数，1秒执行
		},
		commonChat:function(arr){
			this.chatObj=arr;
			$("#ChatObj").val(arr.nickname);
			$("#msg_input").focus();
			var c=0;
			$(".chatname ul").find("li >a").each(function(i){
				if($(this).attr('rel')==arr.uid){
					c=1;
				}
			})
			if(c!=1){
				$(".chatname ul").append("<li><a href='javascript:void(0)' rel="+arr.uid+">"+arr.nickname+"</a></li>");
			}
			this.ScrollOn();
		},

		privateChat : function(arr,flag){
			if(this.show_rank<1&&this.show_purviewrank<1){
				$("#SucMove .popcon").empty().html('<p class="oneline">普通等级用户才能发送私聊</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return false;
			}
			//初始化私聊对象
			this.chatObj=arr;
			$("#ChatObj").val(arr.nickname);
			$("#msg_input").focus();
			var c;
			$(".chatname ul").find("li >a").each(function(i){
				if($(this).attr('rel')==arr.uid){
					c=1;
				}
			})
			if(c!=1){
				$(".chatname ul").append("<li><a href='javascript:void(0)' rel="+arr.uid+">"+arr.nickname+"</a></li>");
			}
			this.ScrollOnp();
		},
		//计算字符串长度
		strLen:function(str) {
			var l = 0;
			var a = str.split("");
			for (var i=0;i<a.length;i++) {
				if (a[i].charCodeAt(0)<299) {
					l++;
				} else {
					l+=2;
				}
			}
			return l;
		},
		chatover:function(overclass){
			$(overclass).find('.setingbox').show().hover(function(){
					$('.seting').css('border-bottom','none');
					$('.seting-con').stop(true,true).slideDown('fast');
				},function(){
					$('.seting-con').stop(true,true).slideUp('fast');
					$('.seting').css('border-bottom','1px solid #c9ccd0');
				});
		},
		//清除聊天区域内容
		cleanScreen:function(obj){
			$("#"+obj).children('p').remove();
		},
		getChatMess : function(data){
			this.insertChatMess(data,false);
		},
		//将聊天信息写入页面
		insertChatMess : function(data,flag){
			if(data.length){
				var ty=data[2];
				var timestamp=new Date().getTime()/1000;
				//普通聊天消息
				if(ty=='common'){
					var c=$.parseJSON(data[3]);
					var u=c.from_uid,n=c.from_nickname,t_u=c.to_uid,t_n=c.to_nickname,content=c.content;
					var r=t_r=0;
					var pr=t_pr=1;
					var label=to_label=mc=to_mc=0;
					var medals=to_medals='';
					if(c.from_json!=null&&c.from_json!=undefined){
						if(c.from_json.pk!=null&&c.from_json.pk!=undefined){
							pr=this.getUserPurviewRank(c.from_json.pk);
						}
						if(pr==3){
							r=c.from_json.dk?c.from_json.dk:0;
						}else{
							r=c.from_json.rk;
						}
						if(c.from_json.lb!=null&&typeof(c.from_json.lb)!=undefined){
							if(c.from_json.lb.vt>timestamp){
								label=1;
							}
						}
						if(c.from_json.mc!=null&&typeof(c.from_json.mc)!=undefined){
							if(c.from_json.mc.vt>timestamp){
								mc=1;
							}
						}
						if(c.from_json.md!=null&&c.from_json.md!=undefined){
							for(i=0;i<c.from_json.md.length;i++){
								if(c.from_json.md[i].aid!=null&&c.from_json.md[i].aid!=undefined){
									if(this.in_array(archives.archives_id,c.from_json.md[i].aid)==true){
										if(c.from_json.md[i].vt>timestamp||c.from_json.md[i].vt=='0'){
											medals+='|*|'+c.from_json.md[i].img;
										}
									}
								}else{
									if(c.from_json.md[i].vt>timestamp||c.from_json.md[i].vt=='0'){
										medals+='|*|'+c.from_json.md[i].img;
									}
								}
								
							}
						}
					}
					var sheildList=$.parseJSON($.cookie('sheildList'));
					if(sheildList){
						if(this.in_array(u,sheildList)==true){
							return false;
						}
					}
					var isVip
					if(c.from_json.vip!=null&&c.from_json.vip!=''){
						if(c.from_json.vip.t!=null&&c.from_json.vip.t!=''&&c.from_json.vip.t>0){
							isVip=true;
						}
					}
					content=this.cSensWord(content,isVip);
					var crown=this.crownUser;
					var crownStyle='';
					var medal='';
					if(this.crownUser){
						if(this.crownUser==u){
							crownStyle='style="color:#0099FF;"';
						}
					}
					
					var fNum=fTip=tNum=tTip=''
					if(c.from_json.num!=null&& c.from_json.num!=undefined){
						fNum=c.from_json.num.n;
						if(c.from_json.num.s!=null&& c.from_json.num.s!=undefined){
							fTip=c.from_json.num.s;
						}
					}
					
					if(t_n!=null&&t_n!=''){
						if(c.to_json.num!=null&& c.to_json.num!=undefined){
							tNum=c.to_json.num.n;
							if(c.to_json.num.s!=null&& c.to_json.num.s!=undefined){
								tTip=c.to_json.num.s;
							}
						}
						if(c.to_json!=undefined&&c.to_json!=null){
							if(typeof(c.to_json.pk)!=undefined&&c.to_json.pk!=null){
								t_pr=this.getUserPurviewRank(c.to_json.pk);
							}
							if(t_pr==3){
								t_r=c.to_json.dk?c.to_json.dk:0;
							}else{
								t_r=c.to_json.rk;
							}
						}
						if(c.to_json.lb!=null&&typeof(c.to_json.lb)){
							if(c.to_json.lb.vt>timestamp){
								to_label=1;
							}
						}
						if(c.to_json.mc!=null&&typeof(c.to_json.mc)!=undefined){
							if(c.to_json.mc.vt>timestamp){
								to_mc=1;
							}
						}
						if(c.to_json.md!=null&&c.to_json.md!=undefined){
							for(i=0;i<c.to_json.md.length;i++){
								if(c.to_json.md[i].aid!=null&&c.to_json.md[i].aid!=undefined){
									if(this.in_array(archives.archives_id,c.to_json.md[i].aid)==true){
										if(c.to_json.md[i].vt>timestamp||c.to_json.md[i].vt=='0'){
											to_medals+='|*|'+c.to_json.md[i].img;
										}
									}
								}else{
									if(c.to_json.md[i].vt>timestamp||c.to_json.md[i].vt=='0'){
										to_medals+='|*|'+c.to_json.md[i].img;
									}
								}
								
							}
						}
						medal=c.from_json.md;
						if(medal!=null&&typeof(medal)!=undefined){
							var text='<p '+crownStyle+'>'+this.showUserMedal(medal)+this._showRank(r, pr, u)+'<a href="javascript:void(0);" id="'+u+'|*|'+n+'|*|'+r+'|*|'+pr+'|*|'+label+'|*|'+mc+'|*|'+fNum+'|*|'+fTip+medals+'">'+n+'</a> 对 <a href="javascript:void(0);" id="'+t_u+'|*|'+t_n+'|*|'+t_r+'|*|'+t_pr+'|*|'+to_label+'|*|'+to_mc+'|*|'+tNum+'|*|'+tTip+to_medals+'">'+t_n+'</a> 说: '+content+'</p>';
							if(t_u>0&&t_u==Chat.show_uid){
								var ptext='<p '+crownStyle+'>'+this.showUserMedal(medal)+this._showRank(r, pr, u)+'<a href="javascript:void(0);" id="'+u+'|*|'+n+'|*|'+r+'|*|'+pr+'|*|'+label+'|*|'+mc+'|*|'+fNum+'|*|'+fTip+medals+'">'+n+'</a> 对 我 说: '+content+'</p>';
							}
						}else{
							var text='<p '+crownStyle+'>'+this._showRank(r, pr, u)+'<a href="javascript:void(0);" id="'+u+'|*|'+n+'|*|'+r+'|*|'+pr+'|*|'+label+'|*|'+mc+'|*|'+fNum+'|*|'+fTip+medals+'">'+n+'</a> 对 <a href="javascript:void(0);" id="'+t_u+'|*|'+t_n+'|*|'+t_r+'|*|'+t_pr+'|*|'+to_label+'|*|'+to_mc+'|*|'+tNum+'|*|'+tTip+to_medals+'">'+t_n+'</a> 说: '+content+'</p>';
							if(t_u>0&&t_u==Chat.show_uid){
								var ptext='<p '+crownStyle+'>'+this._showRank(r, pr, u)+'<a href="javascript:void(0);" id="'+u+'|*|'+n+'|*|'+r+'|*|'+pr+'|*|'+label+'|*|'+mc+'|*|'+fNum+'|*|'+fTip+medals+'">'+n+'</a> 对 我 说: '+content+'</p>';
							}
						}

					}else{
						medal=c.from_json.md;
						if(medal!=null&&typeof(medal)!=undefined){
							var text='<p '+crownStyle+'>'+this.showUserMedal(medal)+this._showRank(r, pr, u)+'<a href="javascript:void(0);" id="'+u+'|*|'+n+'|*|'+r+'|*|'+pr+'|*|'+label+'|*|'+mc+'|*|'+fNum+'|*|'+fTip+medals+'">'+n+'</a> 说: '+content+'</p>';
						}else{
							var text='<p '+crownStyle+'>'+this._showRank(r, pr, u)+'<a href="javascript:void(0);" id="'+u+'|*|'+n+'|*|'+r+'|*|'+pr+'|*|'+label+'|*|'+mc+'|*|'+fNum+'|*|'+fTip+medals+'">'+n+'</a> 说: '+content+'</p>';
						}
					}

					$("#"+this.commonChatArea).append(text);
					this.ScrollOn();
					if(t_u>0&&t_u==Chat.show_uid){
						$("#"+this.privateChatArea).append(ptext);
						this.ScrollOnp();
					}
					return;
				}
				if(ty=='private'){
					
					var c=$.parseJSON(data[3]);
					var u=c.from_uid,n=c.from_nickname,t_u=c.to_uid,t_n=c.to_nickname,content=c.content;
					var r=pr=t_r=t_pr=0;
					var label=to_label=mc=to_mc=0;
					var medals=to_medals='';
					if(c.from_json.lb!=null&&typeof(c.from_json.lb)){
						if(c.from_json.lb.vt>timestamp){
							label=1;
						}
					}
					if(c.to_json.lb!=null&&typeof(c.to_json.lb)){
						if(c.to_json.lb.vt>timestamp){
							to_label=1;
						}
					}
					if(c.from_json!=null&&typeof(c.from_json)!=undefined){
						pr=this.getUserPurviewRank(c.from_json.pk);
						if(pr==3){
							r=c.from_json.dk?c.from_json.dk:0;
						}else{
							r=c.from_json.rk;
						}
					}
					if(c.from_json.mc!=null&&typeof(c.from_json.mc)!=undefined){
						if(c.from_json.mc.vt>timestamp){
							mc=1;
						}
					}
					if(c.from_json.md!=null&&c.from_json.md!=undefined){
						for(i=0;i<c.from_json.md.length;i++){
							if(c.from_json.md[i].aid!=null&&c.from_json.md[i].aid!=undefined){
								if(this.in_array(archives.archives_id,c.from_json.md[i].aid)==true){
									if(c.from_json.md[i].vt>timestamp||c.from_json.md[i].vt=='0'){
										medals+='|*|'+c.from_json.md[i].img;
									}
								}
							}else{
								if(c.from_json.md[i].vt>timestamp||c.from_json.md[i].vt=='0'){
									medals+='|*|'+c.from_json.md[i].img;
								}
							}
						}
					}
					if(c.to_json!=null&&typeof(c.to_json)!=undefined){
						t_pr=this.getUserPurviewRank(c.to_json.pk);
						if(t_pr==3){
							t_r=c.to_json.dk?c.to_json.dk:0;
						}else{
							t_r=c.to_json.rk;
						}
					}
					if(c.to_json.mc!=null&&typeof(c.to_json.mc)!=undefined){
						if(c.to_json.mc.vt>timestamp){
							mc=1;
						}
					}
					if(c.to_json.md!=null&&c.to_json.md!=undefined){
						for(i=0;i<c.to_json.md.length;i++){
							if(c.to_json.md[i].aid!=null&&c.to_json.md[i].aid!=undefined){
								if(this.in_array(archives.archives_id,c.to_json.md[i].aid)==true){
									if(c.to_json.md[i].vt>timestamp||c.to_json.md[i].vt=='0'){
										to_medals+='|*|'+c.to_json.md[i].img;
									}
								}
							}else{
								if(c.to_json.md[i].vt>timestamp||c.to_json.md[i].vt=='0'){
									to_medals+='|*|'+c.to_json.md[i].img;
								}
							}
						}
					}		
					var fNum=fTip=tNum=tTip=''
					if(c.from_json.num!=null&& c.from_json.num!=undefined){
						fNum=c.from_json.num.n;
						if(c.from_json.num.s!=null&& c.from_json.num.s!=undefined){
							fTip=c.from_json.num.s;
						}
					}

					if(c.to_json.num!=null&& c.to_json.num!=undefined){
						tNum=c.to_json.num.n;
						if(c.to_json.num.s!=null&& c.to_json.num.s!=undefined){
							tTip=c.to_json.num.s;
						}
					}
					var sheildList=$.parseJSON($.cookie('sheildList'));
					if(sheildList){
						if(this.in_array(u,sheildList)==true){
							return false;
						}
					}
					var isVip
					if(c.from_json.vip!=null&&c.from_json.vip!=''){
						if(c.from_json.vip.t!=null&&c.from_json.vip.t!=''&&c.from_json.vip.t>0){
							isVip=true;
						}
					}
					content=this.cSensWord(content,isVip);
					
					var crown=this.crownUser;
					var crownStyle='';
					if(crown!=null){
						if(crown.uid==u && crown.archives_id==archives_id){
							crownStyle='style="color:#0099FF;"';
						}
					}
					if(u==this.show_uid){
						var medal=c.to_json.md;
						if(medal==null){
							var text='<p '+crownStyle+'>我对'+this._showRank(t_r, t_pr, t_u)+'<a href="javascript:void(0);" id="'+t_u+'|*|'+t_n+'|*|'+t_r+'|*|'+t_pr+'|*|'+to_label+'|*|'+to_mc+'|*|'+tNum+'|*|'+tTip+to_medals+'">'+t_n+'</a> 悄悄说：'+content+'</p>';
						}else{
							var text='<p '+crownStyle+'>我对'+this.showUserMedal(medal)+this._showRank(t_r, t_pr, t_u)+'<a href="javascript:void(0);" id="'+t_u+'|*|'+t_n+'|*|'+t_r+'|*|'+t_pr+'|*|'+to_label+'|*|'+to_mc+'|*|'+tNum+'|*|'+tTip+to_medals+'">'+t_n+'</a> 悄悄说：'+content+'</p>';
						}

					}else{
						if(this.show_uid>0){
							var medal=c.from_json.md;
							if(medal==null){
								var text='<p '+crownStyle+'>'+this._showRank(r, pr,u)+'<a href="javascript:void(0);" id="'+u+'|*|'+n+'|*|'+r+'|*|'+pr+'|*|'+label+'|*|'+mc+'|*|'+fNum+'|*|'+fTip+medals+'">'+n+'</a> 对我悄悄说：'+content+'</p>';
							}else{
								var text='<p '+crownStyle+'>'+this.showUserMedal(medal)+this._showRank(r, pr, u)+'<a href="javascript:void(0);" id="'+u+'|*|'+n+'|*|'+r+'|*|'+pr+'|*|'+label+'|*|'+mc+'|*|'+fNum+'|*|'+fTip+medals+'">'+n+'</a> 对我悄悄说：'+content+'</p>';
							}

						}
					}
					
					$("#"+this.privateChatArea).append(text);
					this.ScrollOnp();
					return;
				}
				//localroom消息
				if(ty=='localroom'){
					var c=$.parseJSON(data[3]);
					if(c.type=='crown'){
						var char_arr=new Array();
						this.crownUser=c.uid;
						var text='<p style="color:#0099FF;">【快报】恭喜“'+c.nickname+'” 成为本场皇冠粉丝</p>';
						$("#"+this.commonChatArea).append(text);
						$.show.ScrollOn();
						return;
					}
					if(c.type=='demandSong'){
						var text='<p>【点歌】 <a>'+c.nickname+'</a> 刚刚点唱了一首<a>《'+c.name+'》 </a>，等待 <a>主播</a> 处理</p>'
						$("#"+this.commonChatArea).append(text);
						this.ScrollOn();
						return;
					}
					if(c.type=='cancelsong'){
						var text='<p><a>主播</a> 取消了 <a>'+c.nickname+'</a> 点播的 <a>《'+c.name+'》</a> </p>'
						$("#"+this.commonChatArea).append(text);
						this.ScrollOn();
						return;
					}
					if(c.type=='actsong'){
						var text='<p><a>主播</a> 已演唱<a>'+c.nickname+'</a> 点播的 <a>《'+c.name+'》</a> </p>'
						$("#"+this.commonChatArea).append(text);
						this.ScrollOn();
						return;
					}
					if(c.type=='stickLabel'){
						if(c.to_uid==Chat.show_uid){
							$("#"+this.privateChatArea).append('<p><a style="color:#000;">'+c.nickname+' 给 您 贴条['+c.name+']</a></p>');
							this.ScrollOnp();
						}
						$("#"+this.commonChatArea).append('<p><a style="color:#000;">'+c.nickname+' 给 '+c.to_nickname+' 贴条['+c.name+']</a></p>');
						this.ScrollOn();
						UserList.getUserList();
						return;
					}
					if(c.type=='removeLabel'){
						UserList.getUserList();
						return;
					}

					if (c.type == 'vipdefend') {
						var n=c.from_nickname,t_n=c.to_nickname;
						var text='<li style="color:#0099FF;">“'+t_n+'”大展神威，成功防住了'+n+'的'+c.content+'</li>';
						$("#"+this.commonChatArea).append(text);
						this.ScrollOn();
						return;
					}
					if(c.type=='flyscreen'){
						var n=c.from_nickname,t_n=c.to_nickname,cn=c.content,t=c.time_out;
						this.insertFlyscreenMess(n,t_n,cn,t);
						return;
					}
					//主播升级
					if(c.type=='upgrade'){
						var text='<li class="clearfix"><div class="small-head"><img src="'+archives.doteyAvatar+'"/></div><div class="charm-con"><p class="charm-text">魅力等级升至<em class="lvlo lvlo-'+c.rank+'"></em></p><p class="time"><span>'+c.time+'</span></p></div></li>';
						if($("#CharmBox").find('li').length>25){
							$("#CharmBox").find('li:last').remove();
						}
						$("#"+Chat.commonChatArea).append('<p><span style="color:red">恭喜 '+c.nickname+' 升级到达 '+c.name+'</span></p>'); 
						Chat.ScrollOn();
						$("#CharmBox").find('li:first').before(text);
						if(c.uid==Chat.show_uid){
							var img=rtext='';
							if(c.rank<=5){
								img='<img src="'+archives.staticPath+'/fontimg/common/red_heart.png"';
							}else if(c.rank>5&&c.rank<=14){
								img='<img src="'+archives.staticPath+'/fontimg/common/diamond.png"';
							}else{
								img='<img src="'+archives.staticPath+'/fontimg/common/crown.png"';
							}
							var dtext='<div class="poph"><span>升级啦！</span><a title="关闭" class="closed" onClick="$.mask.hide(\'GradBox\');"></a></div><div class="popcon"><ul><li><p>'+img+'</p><p class="gradline">恭喜您升级达到<em class="pink">'+c.name+'</em>！继续加油哦！</p><p class="gradline"><input type="button" onClick="$.mask.hide(\'GradBox\');" value="确&nbsp;&nbsp;定" class="shiftbtn"></p></li></ul></div>';
							$("#GradBox").empty().html(dtext);
							$.mask.show('GradBox');
						}
						return;
					}
					//用户升级
					if(c.type=='upgrade_user'){
						$("#"+Chat.commonChatArea).append('<p><span style="color:red">恭喜 '+c.nickname+' 升级到达 '+c.name+'</span></p>'); 
						Chat.ScrollOn();
						if(c.uid==Chat.show_uid){
							var img=rtext='';
							Chat.show_rank=c.rank;
							if(c.rank<=6){
								img='<img src="'+archives.staticPath+'/fontimg/common/shenshi.png"';
							}else if(c.rank>6&&c.rank<=14){
								img='<img src="'+archives.staticPath+'/fontimg/common/fuhao.png"';
							}else{
								img='<img src="'+archives.staticPath+'/fontimg/common/jiaowei.png"';
							}
							var dtext='<div class="poph"><span>升级啦！</span><a title="关闭" class="closed" onClick="$.mask.hide(\'GradBox\');"></a></div><div class="popcon"><ul><li><p>'+img+'</p><p class="gradline">恭喜您升级达到<em class="pink">'+c.name+'</em>！继续加油哦！</p><p class="gradline"><input type="button" onClick="$.mask.hide(\'GradBox\');" value="确&nbsp;&nbsp;定" class="shiftbtn"></p></li></ul></div>';
							$("#GradBox").empty().html(dtext);
							$.mask.show('GradBox');
						}
						return;
					}
					//发言设置
					if(c.type=='chatSet'){
						this.tourist_set=c.tourist_set;
						this.global_set=c.global_set;
						return;
					}
					//点歌状态
					if(c.type=='allowSong'){
						if(c.status==2){
							$("#allowSong").addClass('nosong');
						}
						if(c.status==1){
							$("#allowSong").removeClass('nosong');
						}
						return;
					}

					if(c.type=='modifyNotice'){
						if(this.show_uid>0){
							if(c.notice!=null||typeof(c.notice)!=undefined){
								if(c.url!=null||typeof(c.url)!=undefined){
									var common_text='<a href="'+c.url+'" title="'+c.notice+'">'+c.notice+'</a>';
								}else{
									var common_text=c.notice;
								}
								$("#common_notice").empty().html(common_text);

							}
							if(c.private_notice!=null||typeof(c.private_notice)!=undefined){
								if(c.private_url!=null||typeof(c.private_url)!=undefined){
									var private_text='<a href="#" class="pink" id="private_notice">'+c.nickname+'</a>对您说：<a href="'+data.data.private_url+'" title="'+data.data.private_notice+'">'+data.data.private_notice+'</a>';
								}else{
									var private_text='<a href="#" class="pink" id="private_notice">'+c.nickname+'</a>对您说：'+data.data.private_notice;
								}
								$("#private_notice").empty().html(private_text);
							}
						}else{
							if(c.notice!=null||typeof(c.notice)!=undefined){
								if(c.url!=null||typeof(c.url)!=undefined){
									var common_text='<a href="'+c.url+'" title="'+c.notice+'">'+c.notice+'</a>';
								}else{
									var common_text=c.notice;
								}
								$("#common_notice").empty().html(common_text);

							}
						}
						return;
					}

					if(c.type=='live_server_drop'){
						if(this.show_uid==archives.dotey.uid){
							$("#ControlBtn .time span").removeClass('didnot');
							$("#ControlBtn .time span").removeClass('didgo');
							$("#ControlBtn .time span").addClass('didend');
							$("#ControlBtn .time span").empty().text('自动结束：');
							$("#ControlBtn .time i").empty().text('信号中断15分钟');
							$("#SucMove .popcon").empty().html('<ul class="roomclose"><li><label>信号中断15分钟，直播间已自动关闭</label></li> <li><input class="shiftbtn" type="button" onclick="Show.reStartLive()" value="重&nbsp;新&nbsp;开&nbsp;播"><input class="shiftbtn" type="button" onclick="$.mask.hide(\'SetSuc\')" value="关&nbsp;&nbsp;闭"></li></ul>');
							$.mask.show("SucMove");
						}
						return;

					}

					//转移观众
					if(c.type=='moveViewer'){
						if(this.show_uid!=c.send_uid){
							self.location='http://'+window.location.host+'/'+c.target_uid;
						}
						return;
					}
					if(c.type=='total_guard_stars'){
						if(archives.archives_id==c.aid){
							$("#dotey_guard").text(c.total_stars);
						}
					}
					if(c.type=='dice'){
						Dice.dice(c);
					}
					if(c.type=='dice_result'){
						Dice.diceResult(c);
					}

					if(c.type=='message'){
						Chat.showArchivesMsg(c);
					}

					// 主播开播提醒
					if(c.type=='user_notice'){
						var _uid = this.show_uid;
						$.each(c.uid_list, function(i, n){
							if(parseInt(n)==_uid){
								$('#bg_msg_img').attr('src',c.avatar);
								$('#bg_msg_dnk').html(c.d_nk);
								$('#bg_msg_cancel').unbind('click');
								$('#bg_msg_cancel').bind('click',function(){
									$.ajax({
										type : 'post',
										url : 'index.php?r=user/cancelAttention',
										data : {uid:c.d_uid},
										success:function(data){
											$('#BroadCast').hide();
										}
									});
								});
								$('#BroadCast a.lookBtn').attr({'href':'/' + c.d_uid,'target':'_blank'});
								$('#BroadCast').show();
							}
						});
					}
					//用户新消息提配
					if(c.type == 'user_message'){
						if(c.uid == Chat.show_uid){
							var messageContent = $.isEmpty(c.s_title) ? $.isEmpty(c.title) ? c.content : c.title : c.s_title;
							var messageHref = 'index.php?r=account/message&type=';
							if(c.category == 0){
								if(c.s_category == 2){
									messageHref += 'site';
								}else{
									messageHref += 'system';
								}
							}else if(c.category == 1){
								messageHref += 'family';
							}
							$('.newApply').html('<a href="'+messageHref+'" target="_blank">'+messageContent+'</a>');
							$('.red-dot').show();
							$('div .newInfo').show();
						}
					}

				}
				if(ty=='broadcast'){
					var c=$.parseJSON(data[3]);
					if(c.type=='FullSite'){
						Chat.fullBroadcast(c);
					}
					if(c.type=='message'){
						Chat.showArchivesMsg(c);
					}
				}
			}

		},
		showArchivesMsg:function(data){
			if(data!=null&&data!=undefined){
				var text='<p><span style="color:red">'+data.content+'</span></p>';
				if(data.window==0){
					$("#"+Chat.commonChatArea).append(text);  
					Chat.ScrollOn();
					$("#"+Chat.privateChatArea).append(text);
					Chat.ScrollOnp();
				}else if(data.window==1){
					$("#"+Chat.commonChatArea).append(text); 
					Chat.ScrollOn();
				}else if(data.window==2){
					$("#"+Chat.privateChatArea).append(text);
					Chat.ScrollOnp();
				}
			}
			
		},
		sendBroadcast:function(){
			if($.User.getSingleAttribute('uid',true)<=0){
				$.User.loginController('login');
				return;
			}
			var content=$("#broadCastContent").val();
			if(content==null||content==undefined){
				return;
			}
			$.ajax({
				type:'POST',
				url:'index.php?r=archives/sendBroadcast',
				data:{archives_id:archives.archives_id,content:content},
				dataType:'json',
				async: false, 
				success:function(data){
					if(data){
						if(data.flag==1){
							$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
							$("#AirBox").hide();
							$("#broadCastContent").val('');
							$.User.refershWebLoginHeader();
							$.mask.show("SucMove",3000);
						}else if(data.flag==2){
							if(domain_type == 'tuli'){
								$.get(
										tuli_uinfo_url,
										{'token':tuli_token},
										function(e){
											if(e.data.user_type==1){
												var text='<ul class="paysong"><li><p style="text-align:center">您的帐户余额不足</p></li><li class="clearfix"><input class="cancelbtn" type="button" onclick="window.Tuli.pay()" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>';
												$.mask.show('SucMove');
											}else{
												var text='<ul class="paysong"><li><p style="text-align:center">您的帐户余额不足</p></li><li class="clearfix"><input class="cancelbtn" type="button" onclick="goExchange(\'_self\')" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>';
												$.mask.show('SucMove');
											}
										},
										'json'
									);
							}else{
								$("#SucMove .popcon").empty().html('<ul class="paysong"><li><p style="text-align:center">您的帐户余额不足</p></li><li class="clearfix"><input class="cancelbtn" type="button" onclick="goExchange()" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>');
							    $.mask.show('SucMove');
							}
						}else{
							$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
							$.mask.show("SucMove",3000);
						}
					}
					
				}
			});
		},
		fullBroadcast:function(data){
			if(data){
				var obj=this;
				$(".broadContent").show();
				var msgNum=$(".broadList ul li").size();
				obj.msgNum=msgNum+1;
				var isVip;
				if(data.user_json.vip!=null&&data.user_json.vip!=''){
					if(data.user_json.vip.t!=null&&data.user_json.vip.t!=''&&data.user_json.vip.t>0){
						isVip=true;
					}
				}
				var text='<li id="broadcast_'+obj.msgNum+'" ><a target="_blank" href="/'+data.dotey_uid+'"><em class="pink">'+data.user_json.nk+'（'+data.user_json.uid+'）：</em>'+Chat.cSensWord(data.content,isVip)+'（'+data.time+'来自<em class="pink">'+data.title+'</em>）</a></li>';
				$(".broadList ul").append(text);
				var arr=new Array();
				arr[0]=obj.msgNum;
				obj.msgNum++;
				var f=function(){ playBroadcast();};
				obj._bFun.push(f);
				obj._b.push(arr);
				$(".broadList").queue('broadf',obj._bFun);
				if(Chat._b.length==1){
					_takeOne();
				}
				if($('.broadList ul li:last').index()>=2){
					setTimeout(function(){
						$('.broadList ul li:first').remove();
					},5000);
				}
			}
			
			function _takeOne(){
				$(".broadList").dequeue('broadf');
			};
			function playBroadcast(){
				var c=obj._b[0];
				var textId='broadcast_'+c[0];
				setTimeout(function(){hideBroadcast(textId)},600000);
				
			};
			function hideBroadcast(id){
				$("#"+id).remove();
				obj._b.shift();//播放完一个 移除一个
				if(obj._b.length>0){
					_takeOne();
				}else{
					//数据播放完后 清空函数队列
					$(".broadContent").hide();
					$(".broadList").clearQueue('broadf');
				}
			};
			
		},
		//判断在数组中是否含有给定的一个变量值
		in_array:function(target,arr){
			var type=typeof target;
			if(type=='string'||type=='number'){
				for(j=0;j<arr.length;j++){
					if(arr[j]==target){
						return true;
					}
				}
			}else{
				return false;
			}
		},
		chatSrollSet:function(){
			if(this.isScrollOn){
				$("#chatSrollSet").addClass('onroll');
				this.isScrollOn=false;
			}else{
				$("#chatSrollSet").removeClass('onroll');
				this.isScrollOn=true;
			}
		},
		privateSrollSet:function(){
			if(this.isScrollOnp){
				$("#privateSrollSet").addClass('onroll');
				this.isScrollOnp=false;
			}else{
				$("#privateSrollSet").removeClass('onroll');
				this.isScrollOnp=true;
			}
		},

		//滚屏
		ScrollOn : function(){
			if(this.isScrollOn){
				//公聊框
				var obj=$("#"+this.commonChatArea).children("p");

				var Divh=$("#"+this.commonChatArea).height();
				var t= row=0;
				$.each(obj,function(i){
					t+=parseInt($(this).height())+10;//5是像素差距
					row++;
					if(row==100){
						obj.eq(0).remove();
					}
				})
				if(t>Divh){
					$("#"+this.commonChatArea).animate({scrollTop: 2*t}, 500);
					return false;
					//$("#commonChat").scrollTop(24);
				}
			}

		},
		 ScrollOnp : function(){
			if(this.isScrollOnp){
				//私聊框
				var obj_p=$("#"+this.privateChatArea).children("p");
				var Divh_p=$("#"+this.privateChatArea).height();
				var t_p=row_p=0;
				$.each(obj_p,function(i){
					t_p+=parseInt($(this).height())+12;
					row_p++;
					if(row_p==30){
						obj_p.eq(0).remove();
					}
				})

				if(t_p>Divh_p){
					$("#"+this.privateChatArea).animate({scrollTop: t_p}, 500);
					return false;
					//$("#private_div").scrollTop(t_p);
				}
			}
		},
		//禁ip页面操作
		insertForbidMess : function(data){
			var u=data[2],n=data[3],t_u=data[4],t_n=data[5],type=parseInt(data[6]),p=data[7],s=data[8],aid=data[0];
			var c='';
			var forbidTourist;
			if(s!=1 && u==Chat.show_uid){
				alert('操作失败。');return false;
			}else if(s==1){
				switch (type){
					case 0:
						c="<p><span style='color:red'>"+n+"解禁了"+t_n+"的ip。</span></p>";
						break;
					case 1:
						c="<p><span style='color:red'>"+n+"禁言了"+t_n+"的ip。</span></p>";
						break;
					case 2:
						c="<p><span style='color:red'>"+n+"允许了游客发言。</span></p>";
						this.forbidTourist=false;
						break;
					case 3:
						c="<p><span style='color:red'>"+n+"禁止了游客发言。</span></p>";
						this.forbidTourist=true;
						break;
					case 4:
						c="<p><span style='color:red'>"+n+"解除了"+t_n+"的禁言。</span></p>";
						if(t_n==this.show_nickname){
							$.cookie("forbidchat_"+archives.archives_id+'_'+t_u,null);
						}
						break;
					case 5:
						c="<p><span style='color:red'>"+n+"禁止"+t_n+"在本房间内发言，（持续"+p+"分钟）。</span></p>";
						if(t_u==this.show_uid && t_n==this.show_nickname){
							var date = new Date();
							date.setTime(date.getTime() + (p * 60 * 1000));
							$.cookie("forbidchat_"+archives.archives_id+'_'+t_u,t_u,{ path: '/' ,expires:date});
						}
						break;
					case 6:
						c="<p><span style='color:red'>"+n+"</span>解除了<span style='color:red'>"+t_n+"</span>的房间管理员身份.</p>";
						UserList.getUserList();
						if(t_u==this.show_uid && t_n==this.show_nickname){
							this.show_purviewrank='0';
							//addSocketServerComplete(archives.chatServer.serverId);
						}
						break;
					case 7:
						c="<p><span style='color:red'>"+n+"</span>将<span style='color:red'>"+t_n+"</span>设为房间管理员.</p>";
						UserList.getUserList();
						if(t_u==this.show_uid && t_n==this.show_nickname){
							this.show_purviewrank='2';
							//addSocketServerComplete(archives.chatServer.serverId);
						}
						break;
					case 8:
						c="<p><span style='color:red'>"+n+"</span>将<span style='color:red'>"+t_n+"</span>踢出该直播间,"+p+"分钟内不准进入.</p>";
						if((t_u!='0'&&t_u==this.show_uid)||(t_u=='0'&&t_n==this.show_nickname)){
							this.kickOut=true;
							$("#user_count").empty();
							$("#manage_count").empty();
							$("#manage ul").empty();
							$("#user ul").empty();
							Purview.onlock();
							alert('您已经被踢出该直播间，'+p+'分钟内不准进入');
						}
						break;
					case 10:
						if(aid==archives.archives_id){
							if(n!='system'){
								c="<p><span style='color:red'>"+n+"</span>禁止<span style='color:red'>"+t_n+"</span>在所有直播间内发言(持续"+p+"分钟)</p>";
								if(t_u==this.show_uid && t_n==this.show_nickname){
									$.cookie('forbidFullchat', new Date().getTime()/1000 + (p * 60 ));
								}
							}
						}
						break;
					case 11:
						if(aid==archives.archives_id){
							if(n!='system'){
								c="<p><span style='color:red'>"+n+"</span>将<span style='color:red'>"+t_n+"</span>解除全局禁言.</p>";
								$.cookie('forbidFullchat', null);
							}
						}
						break;

				}
				$("#"+this.commonChatArea).append(c);
				this.ScrollOn();
			}
		},
		//1107 显示欢迎信息
		insetWelMess :  function(data){
			if(data){
				var u=data[0],c=data[1],status=data[2];
				c=$.parseJSON(c);
				if(status==0){
					var pk=this.getUserPurviewRank(c.pk);
					if(pk==3){
						var rk=c.dk;
					}else{
						var rk=c.rk;
					}
					if(c.vip!=null||typeof(c.vip)!='undefined'){
						if(c.vip.h){
							$("#"+this.commonChatArea).append('<p>有位大侠悄悄的进入了房间</p>');
						}else if(c.car!=null||typeof(c.car)!='undefined'){
							if(c.car.f!=null||typeof(c.car.f)=='undefined'){
								this.showCar(data);
							}
						}else{
							if(rk<8&&rk>=0){
								var text='<p style="color:#000;">欢迎&nbsp;'+this._showVip(c.vip)+c.nk+'&nbsp;进入直播间</p>';
								$("#"+this.commonChatArea).append(text);
							}
							if(rk>=8){
								var text='<p style="color:#FF0099;">欢迎&nbsp;'+this._showVip(c.vip)+this._showRank(rk,pk,c.uid)+c.nk+'&nbsp; 进入直播间</p>';
								$("#"+this.commonChatArea).append(text);
							}

						}
					}else{
						if(c.car!=null||typeof(c.car)!='undefined'){
							if(c.car.f!=null||typeof(c.car.f)=='undefined'){
								this.showCar(data);
							}
						}else{
							if(rk<8&&rk>=0){
								var text='<p style="color:#000;">欢迎&nbsp;'+this._showVip(c.vip)+c.nk+'&nbsp;进入直播间</p>';
								$("#"+this.commonChatArea).append(text);
							}
							if(rk>=8){
								var text='<p style="color:#FF0099;">欢迎&nbsp;'+this._showVip(c.vip)+this._showRank(rk,pk,c.uid)+c.nk+'&nbsp; 进入直播间</p>';
								$("#"+this.commonChatArea).append(text);
							}

						}
					}
				}
				this.ScrollOn();
			}
		},
		showCar : function(data){
			if(!data)
        		return false;
			var uid=data[0],c=data[1];
			c=$.parseJSON(c);
			var car=c.car;
			car.nickname=c.nk;
			car.rank=c.rk;
			var obj=this;
			var f=function(){playFlashCar();}
			obj._qCarFun.push(f);
			obj._qCar.push(car);
			$("#playFlashCar").queue('playCar',obj._qCarFun);

			if(obj._qCar.length==1){
				_takeOneCar();
			}
			function _takeOneCar(){
				$("#playFlashCar").dequeue('playCar');
			};

			function playFlashCar(){
				var _car =obj._qCar[0];//播放第一个
				if($("#giftEffectSet").attr("checked")=='checked'){
					if(_car.f!=null&&typeof(_car.f)!='undefined'){
						var msg = '欢迎 <span>'+Chat._showVip(c.vip)+'<em class="lvlr lvlr-'+_car.rank+'"></em>'+_car.nickname+'</span> 驾驶他的座驾 <span style="color:red;">'+_car.n+'</span> 进入房间。';
			    		$("#commonChat").append('<p  style="color:red;">'+msg+'</p>');

						var embed='<div style="width:530px;height:350px;float:left;"><embed width="450"  height="350" src='+archives.imgSite+_car.f+' wmode="transparent" bgcolor="#fff" quality="high" type="application/x-shockwave-flash"></div>';
						$("#playFlashCar").html(embed);
						$("#playFlashCar").css({'margin':'0 auto'}).show();
					}
				}else{
					var msg = '欢迎 <span style="color:red;"><em class="lvlr lvlr-'+_car.rank+'"></em>'+_car.nickname+'</span>进入房间。';
					$("#commonChat").append('<p>'+msg+'</p>');
				}
				setTimeout(hideFlashCar, _car.to*1000);
			}

			function hideFlashCar(){
				$("#playFlashCar").empty().hide();
				obj._qCar.shift();//播放完一个 移除一个
				if(obj._qCar.length>0){
					_takeOneCar();
				}else{
					//数据播放完后 清空函数队列
					$("#playFlashCar").clearQueue('playCar');
				}
			}
			return true;
		},

		//获取用户的操作等级
		getUserPurviewRank:function(data){
			var purviewrank=1;
			if(data!=null&&typeof(data)!='undefined'){
				 if(typeof(data[3])!='undefined'&&data[3]!=null){
					for(i=0;i<data[3].length;i++){
						if(data[3][i]==archives.archives_id){
							return purviewrank=3;
						}
					}
				 }
				 if(typeof(data[4])!='undefined'&&data[4]!=null){
					 return purviewrank=4;
				}
				if(typeof(data[2])!='undefined'&&data[2]!=null){
					for(i=0;i<data[2].length;i++){
						if(data[2][i]==archives.archives_id){
							return purviewrank=2;
						}
					}
				}

			}
			return purviewrank;
		},
		//显示用户的等级
		_showRank:function(r,pr,u){
			if(u==0){
				return '';
			}
			var rank_css='';
			if(pr==2){
				rank_css='<em class="lvlr lvlr-'+r+'"></em><em class="ver ver-5"></em>';
			}else if(pr==3){
				rank_css='<em class="lvlo lvlo-'+r+'"></em><em class="ver ver-4"></em>';
			}else if(pr==4){
				rank_css='<em class="lvlr lvlr-'+r+'"></em><em class="ver ver-3"></em>';
			}else{
				if(r>1){
					rank_css='<em class="lvlr lvlr-'+r+'"></em>';
				}
			}
			return rank_css;
		},
		//显示vip用户图标
		_showVip:function(data){
			var vip='';
			if(data!=null&&typeof(data)!='undefined'){
				var timestamp=new Date().getTime()/1000;
				if(data.vt==0||data.vt>timestamp){
					vip='<em class="ver ver-'+data.t+'"></em>';
				}
			}
			return vip;
		},
		//显示用户勋章
		showUserMedal:function(data){
			var medal='';
			if(data!=null&&typeof(data)!='undefined'){
				var timestamp=new Date().getTime()/1000;
				for(i=0;i<data.length;i++){
					if(data[i].aid!=null&&data[i].aid!=undefined){
						if(this.in_array(archives.archives_id,data[i].aid)==true){
							if(data[i].vt>timestamp||data[i].vt==0){
								medal+='<img class="medal" src="'+archives.imgSite+data[i].img+'"/>';
							}
						}
					}else{
						if(data[i].vt>timestamp||data[i].vt==0){
							medal+='<img class="medal" src="'+archives.imgSite+data[i].img+'"/>';
						}
					}
					
				}
			}
			return medal;
		},

		//判断是否为数字
		checkInt : function (num){
		   var re = /^[1-9]+[0-9]*]*$/
		   return re.test(num)
		},
		cSensWord : function(str,isVip,isTransmit){
			if(word.length>0){
				for(j=0;j<word.length;j++){
					var reg=new RegExp(word[j].name,"g");
					if(word[j].type=='0'){
						str=str.replace(reg,word[j].replace);
					}else if(word[j].type=='1'){
						if(reg.test(str)==true){
							str=word[j].replace;
						}
					}
					
				}
			}
			str=str.replace(/<img[^>]*>/gim,"");
			str=str.replace(/<script[^>]*>/gim,"");
			var RexStr = /\<|\>|\"|\'|\&/g ;
			    str = str.replace(RexStr,
			        function(MatchStr){
			            switch(MatchStr){
			                case "<":
			                    return "&lt;";
			                    break;
			                case ">":
			                    return "&gt;";
			                    break;
			                case "\"":
			                    return "&quot;";
			                    break;
			                case "'":
			                    return "&#39;";
			                    break;
			                case "&":
			                    return "&amp;";
			                    break;
			                default :
			                    break;
			            }
			        }
			    );
				
			return  Ubb.faceHtml(str,isVip,isTransmit);
		},

		//主播点歌记录
		doteySongRecord:function(){
			$.ajax({
				type:"POST",
				url:"index.php?r=/doteySong/doteySongRecord",
				data:{uid:archives.dotey.uid,archives_id:archives.archives_id},
				dataType:"json",
				success:function(data){
					if(data.flag==1){
						var text='';
						if(data.data){
							var song=data.data;
							for(i=0;i<song.length;i++){
								if(archives.dotey.uid==Chat.show_uid){
									text+='<dd class="clearfix"><span class="time">'+song[i].create_time+'</span><span class="name ellipsis" title="'+song[i].name+'">'+song[i].name+'</span><span class="faner ellipsis" title="'+song[i].singer+'">'+song[i].singer+'</span><span class="control"><a href="javascript:Chat.actSong('+song[i].record_id+')" title="演唱">演唱</a>&#124;<a href="javascript:Chat.cancelSong('+song[i].record_id+')" title="取消">取消</a></span></dd>';
								}else{
									text+='<dd class="clearfix"><span class="time">'+song[i].create_time+'</span><span class="name ellipsis" title="'+song[i].name+'">'+song[i].name+'</span><span class="faner ellipsis" title="'+song[i].singer+'">'+song[i].singer+'</span><span class="control">待处理</span></dd>'

								}
							}
						}
						$(".song-list dd").remove();
						$(".song-list dl").after(text);
					}

				}
			})
		},
		//获取主播歌单
		DoteySongList:function(page){
			var uid=archives.dotey.uid;
			if(this.show_uid<=0||this.show_uid==''||this.show_uid==null){
				$.User.loginController('login');
				return;
			}else if(uid<=0||uid==''||uid==null){
				$("#SucMove .popcon").empty().html('<p class="oneline">主播歌单获取异常</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}else{
				$.ajax({
					type:"POST",
					url:"index.php?r=/doteySong/doteySongList",
					data:{uid:uid,page:page,archives_id:archives.archives_id},
					dataType:'json',
					success:function(data){
						if(data.flag==1){
							var text='';
							$("#MyMusic .mymusic").find('li:gt(0)').remove();
							var list=data.data.list;
							for(i=0;i<list.length;i++){
								var singer=list[i].singer?list[i].singer:'&nbsp';
								if(Chat.show_uid!=uid){
									text+='<li class="clearfix"><span class="time">'+list[i].create_time+'</span><span class="musicname">'+list[i].name+'</span><span class="songname">'+singer+'</span><span class="control"><a href="javascript:Chat.demandSong(\''+list[i].name+'\','+list[i].song_id+')" class="pink" title="点唱">点唱</a></span></li>';
								}else{
									text+='<li class="clearfix"><span class="time">'+list[i].create_time+'</span><span class="musicname">'+list[i].name+'</span><span class="songname">'+singer+'</span><span class="control"><a href="javascript:Chat.delSong('+list[i].song_id+')" class="pink" title="删除">删除</a></span></li>';
								}

							}
							var pageText=Chat.songPage(data.data.count,page);
							$("#MyMusic .songpage").empty();
							$("#MyMusic .songpage").html(pageText);
							$("#MyMusic .mymusic li").after(text);
							$('#MyMusic').show();
						}else{
							$("#SucMove .popcon").html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
							$.mask.show('SucMove',3000);
							return;
						}
					}
				})
			}

		},
		//添加歌曲
		addSong:function(){
			var song_name=$("#song_name").val();
			var song_singer=$("#song_singer").val();
			if(song_name==null||song_singer==''||song_name==null||song_singer==''){
				$("#SucMove .popcon").empty().html('<p class="oneline">歌曲名和原唱不能为空</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			if(this.show_uid!=archives.dotey.uid){
				$("#SucMove .popcon").empty().html('<p class="oneline">只有主播才能添加歌曲</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			var obj=this;
			$("#song_name").val("");
			$("#song_singer").val("");
			$.ajax({
				type:"POST",
				url:"index.php?r=/doteySong/addSong",
				data:{archives_id:archives.archives_id,song_name:song_name,song_singer:song_singer},
				dataType:"json",
				success:function(data){
					if(data.flag==1){
						Chat.DoteySongList(1);
					}
					$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>')
					$.mask.show('SucMove',3000);
				}
			})

		},
		batchSong:function(){
			if(this.show_uid<=0){
				$.User.loginController('login');
				return;
			}
			if(this.show_uid!=archives.dotey.uid){
				$("#SucMove .popcon").empty().html('<p class="oneline">主播才添加歌曲</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			$("#LotAddBox").css('z-index',300);
			$("#LotAddBox").show();
		},
		//批量添加歌曲
		confirmBatchSong:function(){
			var song_name=new Array();
			var song_singer=new Array();
			var j=0;
			$("#LotAddBox ul li:gt(0)").each(function(i){
				if($(this).find(':input[name=song_name]').val()!=''&&$(this).find(':input[name=song_singer]').val()!=''){
					song_name[j]=$(this).find(':input[name=song_name]').val();
					song_singer[j]=$(this).find(':input[name=song_singer]').val();
					j++;
				}

			})
			var obj=this;
			$.ajax({
				type:"POST",
				url:"index.php?r=/doteySong/batchAddSong",
				data:{archives_id:archives.archives_id,song_name:song_name,song_singer:song_singer},
				dataType:"json",
				success:function(data){
					if(data.flag==1){
						Chat.DoteySongList(1);
					}
					$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
					$.mask.hide('LotAddBox');
					$.mask.show('SucMove',3000);
				}

			})
		},
		//演唱歌曲
		actSong:function(recordId){
			if(this.show_uid<=0){
				$.User.loginController('login');
				return;
			}
			if(recordId<=0){
				return false;
			}
			$.ajax({
				type:"POST",
				url:"index.php?r=/doteySong/actSong",
				data:{record_id:recordId,archives_id:archives.archives_id},
				dataType:"json",
				success:function(data){
					if(data.flag==1){
						Chat.doteySongRecord(1);
						$.User.refershWebLoginHeader();
					}
					$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
					$.mask.show('SucMove',3000);
				}
			})
		},
		//演唱歌曲
		cancelSong:function(recordId){
			if(this.show_uid<=0){
				$.User.loginController('login');
				return;
			}
			if(recordId<=0){
				return false;
			}
			$.ajax({
				type:"POST",
				url:"index.php?r=/doteySong/cancelSong",
				data:{record_id:recordId,archives_id:archives.archives_id},
				dataType:"json",
				success:function(data){
					if(data.flag==1){
						Chat.doteySongRecord(1);
					}
					$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
					$.mask.show('SucMove',3000);
				}
			})
		},
		//主播删除歌曲
		delSong:function(songId){
			if(this.show_uid<=0){
				$.User.loginController('login');
				return;
			}
			var obj=this;
			$.ajax({
				type:"POST",
				url:"index.php?r=/doteySong/delSong",
				data:{song_id:songId},
				dataType:"json",
				success:function(data){
					if(data.flag==1){
						Chat.DoteySongList(1);
					}
					$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
					$.mask.show('SucMove',3000);
				}
			})
		},
		//用户点歌
		demandSong:function(name,songId){
			if(archives.uid<=0){
				$.User.loginController('login');
				return;
			}
			var text='<ul class="paysong"><li><p>您点唱这首<em class="pink">《'+name+'》</em>需要支付<em class="pink">1000</em>个皮蛋。主播取消点唱返还皮蛋。</p></li><li><input class="shiftbtn" type="button" onclick="Chat.ConfirmdemandSong(\''+name+'\','+songId+')" value="确&nbsp;&nbsp;定"><input onClick="$.mask.hide(\'SucMove\');" class="shiftbtn" type="button" value="取&nbsp;&nbsp;消"></li></ul>';
			$("#SucMove .popcon").empty().html(text);
			$.mask.show('SucMove');
		},
		//用户自定义点歌
		userDefinedSong:function(){
			var song_name=$("#song_name").val();
			var song_singer=$("#song_singer").val();
			if(song_name==''||song_singer==''){
				$("#SucMove .popcon").empty().html('<p class="oneline">歌曲名和原唱不能为空</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			var text='<ul class="paysong"><li><p>您点唱这首<em class="pink">《'+song_name+'》</em>需要支付<em class="pink">1000</em>个皮蛋。主播取消点唱返还皮蛋。</p></li><li><input class="shiftbtn" type="button" onclick="Chat.ConfirmdemandSong(\''+song_name+'\')" value="确&nbsp;&nbsp;定"><input onClick="$.mask.hide(\'SucMove\');" class="shiftbtn" type="button" value="取&nbsp;&nbsp;消"></li></ul>';
			$("#SucMove .popcon").empty().html(text);
			$.mask.show('SucMove');
		},
		ConfirmdemandSong:function(name,songId){
			if(archives.uid<=0){
				$.User.loginController('login');
				return;
			}
			var obj=this;
			if(songId>0){
				$.ajax({
					type:"POST",
					url:"index.php?r=/doteySong/demandSong",
					data:{song_id:songId,archives_id:archives.archives_id,dotey_id:archives.dotey.uid},
					dataType:"json",
					success:function(data){
						if(data){
							if(data.flag==1){
								obj.doteySongRecord();
								$.User.refershWebLoginHeader();
								var text='<ul class="paysong"><li><p>您已成功点唱首<em class="pink">《'+data.data.song+'》</em>，目前排在第<em class="pink">'+data.data.count+'</em>首。请耐心等待主播的处理哦。</p></li></ul>';
								$("#SucMove .popcon").empty().html(text);
								$.mask.show('SucMove',3000);
							}else if(data.flag==-1){
								if(domain_type == 'tuli'){
									$.get(
											tuli_uinfo_url,
											{'token':tuli_token},
											function(e){
												if(e.data.user_type==1){
													var text='<ul class="paysong"><li><p style="text-align:center">您的帐户余额不足</p></li><li class="clearfix"><input class="cancelbtn" type="button" onclick="window.Tuli.pay();" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>';
													$("#SucMove .popcon").empty().html(text);
													$.mask.show('SucMove');
												}else{
													var text='<ul class="paysong"><li><p style="text-align:center">您的帐户余额不足</p></li><li class="clearfix"><input class="cancelbtn" type="button" onclick="goExchange(\'_self\')" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>';
													$("#SucMove .popcon").empty().html(text);
													$.mask.show('SucMove');
												}
											},
											'json'
										);
								}else{
									var text='<ul class="paysong"><li><p style="text-align:center">您的帐户余额不足</p></li><li class="clearfix"><input class="cancelbtn" type="button" onclick="goExchange()" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>';
									$("#SucMove .popcon").empty().html(text);
									$.mask.show('SucMove');
								}
							}else{
								$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');;
								$.mask.show('SucMove',3000);
							}
							
						}
					}
				})
			}else{
				var song_singer=$("#song_singer").val();
				$.ajax({
					type:"POST",
					url:"index.php?r=/doteySong/demandSong",
					data:{song_name:name,song_singer:song_singer,archives_id:archives.archives_id,dotey_id:archives.dotey.uid},
					dataType:"json",
					success:function(data){
						if(data){
							if(data.flag==1){
								obj.doteySongRecord();
								$.User.refershWebLoginHeader();
								var text='<ul class="paysong"><li><p>您已成功点唱首<em class="pink">《'+data.data.song+'》</em>，目前排在第<em class="pink">'+data.data.count+'</em>首。请耐心等待主播的处理哦。</p></li></ul>';
								$("#SucMove .popcon").empty().html(text);
							}else if(data.flag==-1){
								if(domain_type == 'tuli'){
									$.get(
											tuli_uinfo_url,
											{'token':tuli_token},
											function(e){
												if(e.data.user_type==1){
													var text='<ul class="paysong"><li><p>皮蛋不足，请先<a href="javascript:void();" onclick="window.Tuli.pay()" target="_self" class="pink">充值</a></p></li><li class="clearfix"><input class="fleft shiftbtn" type="button" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>';
													$("#SucMove .popcon").empty().html(text);
												}else{
													var text='<ul class="paysong"><li><p>皮蛋不足，请先<a href="'+exchangeUrl+'" target="_self" class="pink">充值</a></p></li><li class="clearfix"><input class="fleft shiftbtn" type="button" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>';
													$("#SucMove .popcon").empty().html(text);
												}
											},
											'json'
										);
								}else{
									var text='<ul class="paysong"><li><p>皮蛋不足，请先<a href="'+exchangeUrl+'" target="'+hrefTarget+'" class="pink">充值</a></p></li><li class="clearfix"><input class="fleft shiftbtn" type="button" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>';
									$("#SucMove .popcon").empty().html(text);
								}
							}else{
								$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');;
							}
							$.mask.show('SucMove',3000);
						}
					}
				})
			}
		},
		alreadySong:function(){
			$("#AlreadySong").show();
			$.ajax({
				type:'POST',
				url:'index.php?r=doteySong/alreadySong',
				data:{archives_id:archives.archives_id,doteyId:archives.dotey.uid},
				dataType:'json',
				success:function(data){
					if(data){
						var text='';
						for(i=0;i<data.length;i++){
							text+='<li class="clearfix"><span class="time">'+(i+1)+'</span><span class="musicname ellipsis">'+data[i].name+'</span><span class="songfans ellipsis">'+data[i].nickname+'</span></li>';
						}
						$("#AlreadySong .mymusic li:gt(0)").remove();
						$("#AlreadySong .mymusic").append(text);
					}
				}
			})
		},
		songPage:function(count,page){
			var pageText='<li class="first">共'+Math.ceil(count/this.pageSize)+'页</li>';
			var offset=5;
			var page=page?page:1;
			var nowPage=Math.ceil(page/offset);
			if(nowPage>1){
				pageText+='<li><a href="javascript:Chat.DoteySongList('+(nowPage-1)*offset+')">&lt;</a></li>';
			}
			for(j=((nowPage-1)*offset+1);j<=nowPage*offset;j++){
				if(j<=Math.ceil(count/this.pageSize)){
					if(page==j){
						pageText+='<li><a class="overed" href="javascript:void(0)" title="'+j+'">'+j+'</a></li>';
					}else{
						pageText+='<li><a href="javascript:Chat.DoteySongList('+j+')" title="'+j+'">'+j+'</a></li>';
					}
				}
			}
			if(nowPage<Math.ceil(count/(this.pageSize*offset))){
				pageText+='<li><a href="javascript:Chat.DoteySongList('+(nowPage+1)*offset+')">&gt;</a></li>';
			}
			return pageText;
		},
		//发送飞屏
		sendFlyscreen:function(){
			if(this.show_uid<=0){
				$.User.loginController('login');
				return false;
			}
			if(this.chatObj.uid!=0&&this.show_uid&&$("#"+this.privateSet).attr('checked')=='checked'){
				$("#SucMove .popcon").empty().html('<p class="oneline">悄悄话模式，不能发送飞屏</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return false;
			}
			var content=$("#msg_input").val();
			if(!$.trim(content)){
				$("#SucMove .popcon").empty().html('<p class="oneline">请输入飞屏内容</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return false;
			}
			if(Chat.show_uid>0){
				$.ajax({
					type:'POST',
					url:'index.php?r=/archives/sendFlyscreen',
					data:{archives_id:archives.archives_id},
					dataType:'json',
					success:function(data){
						if(data){
							if(data.flag==1){
								var text='<ul class="paysong"><li><p class="otline">'+data.message+'</p></li><li class="clearfix"><input class="fleft shiftbtn" onclick="Chat.confirmFlyscreen('+data.data+')" type="button" value="确&nbsp;&nbsp;定"><input onClick="$.mask.hide(\'SucMove\');" class="shiftbtn" type="button" value="取&nbsp;&nbsp;消"></li></ul>';
							}else{
								var text='<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>'
							}
							$("#SucMove .popcon").empty().html(text);
							$("#SucMove").show();
						}
					}
				})
			}else{
				$.User.loginController('login');
				return false;
			}
		},
		confirmFlyscreen:function(){
			var content=$("#msg_input").val();
			if(!$.trim(content)){
				$("#SucMove .popcon").empty().html('<p class="oneline">请输入飞屏内容</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return false;
			}
			$.ajax({
				type:'POST',
				url:'index.php?r=/shop/buyFlyscreen',
				data:{to_uid:this.chatObj.uid,content:content,archives_id:archives.archives_id},
				dataType:'json',
				success:function(data){
					if(data){
						if(data.flag!=1){
							$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
							$.mask.show('SucMove',3000);
						}else{
							$.User.refershWebLoginHeader();
							$("#SucMove").hide();
						}
					}

				}
			})
		},
		insertFlyscreenMess:function(from_nick,to_nick,data,time){
			var fly_data = Array(from_nick,to_nick,data,time);
			var obj=this;
			//用队列处理飞屏播放
			var f=function(){ playflyscreen();};
			obj._qFlyFun.push(f);
			obj._qFly.push(fly_data);
			$("#flyscreen").queue('playflyscreen',obj._qFlyFun);
			if(obj._qFly.length==1){
				flyscreen_takeOne();
			}
			function flyscreen_takeOne(){
				$("#flyscreen").dequeue('playflyscreen');
			};

			function playflyscreen(){
				var d=obj._qFly[0];//播放第一个
				$("#flyscreen").width(1000).height(150);
				setTimeout(function(){
		        			document.getElementById("flyMovice").showFlyscreen(d[0],d[1],d[2]);
		        		},100);
				var timeOut=d[3]*1000;
	        	setTimeout(function(){
		        			$("#flyscreen").width(0).height(0);
		        		},timeOut);
				setTimeout(hideflyscreen, timeOut);
			}

			function hideflyscreen(){
				obj._qFly.shift();//播放完一个 移除一个
				if(obj._qFly.length>0){
					flyscreen_takeOne();
				}else{
					//数据播放完后 清空函数队列
					$("#flyscreen").clearQueue('playflyscreen');
				}
			}
		},
		getGuard:function(){
			if(Chat.show_uid<=0){
				$.User.loginController('login');
				return false;
			}
			$.ajax({
				type:'POST',
				url:'index.php?r=archives/getGuard',
				data:{dotey_uid:archives.dotey.uid,archives_id:archives.archives_id},
				dataType:'json',
				success:function(data){
					if(data){
						$("#SucMove .popcon").empty().html(data.message);
						$.mask.show('SucMove');
					}
				}
			})
		},
		starGuard:function(){
			if(Chat.show_uid<=0){
				$.User.loginController('login');
				return false;
			}
			$.ajax({
				type:'POST',
				url:'index.php?r=archives/startGuard',
				data:{dotey_uid:archives.dotey.uid},
				dataType:'json',
				success:function(data){
					if(data){
						$("#SucMove .popcon").empty().html(data.message);
						$.mask.show('SucMove');
					}
				}
			})
		},

		initFunction:function(){
			var obj=this;
			$('#GurdNum').hover(function(){
				$('.endcon').addClass('endover');
				$(this).find('.gurd-box').stop(true,true).slideDown('fast');
			},function(){
				$('.endcon').removeClass('endover');
				$(this).find('.gurd-box').stop(true,true).slideUp('fast');
			});
			$('.chat-msg').hover(function(){
				obj.chatover('.chat-msg');
			},function(){
				$('.setingbox').hide();
			});
			$('.chat-msgpre').hover(function(){
				obj.chatover('.chat-msgpre');
			},function(){
				$('.setingbox').hide();
			});
			$(".chat-l").slide({
				titCell:".chat-menu li a",
				targetCell:".chat-con",
				trigger:"click",
				titOnClassName:"giftover",delayTime:0
			});
			$('#ChatObj').click(function(){
				$(".chat-con").css({'z-index':40});
				$('.chatname').show();

			});
			if($('#ChatObj').val()=='所有人'){
				$(".topub").hide();
			}else{
				$(".topub").show();
			}
			$('.chatname ul li a').live('click',function(){
				var uid=$(this).attr('rel');
				var nickname=$(this).text();
				obj.chatObj.uid=uid;
				obj.chatObj.nickname=(nickname=='所有人')?'':nickname;
				$('#ChatObj').val(nickname);
				$('.chatname').hide();
				if($('#ChatObj').val()=='所有人'){
					$(".topub").hide();
				}else{
					$(".topub").show();
				}
			})


			$(".topub").click(function(){
				obj.chatObj.uid=0;
				obj.chatObj.nickname='';
				$('#ChatObj').val('所有人');
				$(".topub").hide();
			})
			$("#"+obj.commonChatArea).TextAreaResizer();

			$("#songRecord").bind('click',function(){
				Chat.doteySongRecord();
			})
			$("#"+obj.commonChatArea).showPurview();
			$("#"+obj.privateChatArea).showPurview();

			$("#FaceGood").bind('click',function(e){
				$(".chat-con").css({'z-index':40});
				Ubb.showFace('FaceGood');
				e.stopPropagation();
			});
			if($.browser.msie&&($.browser.version == "6.0")){
				var bindK='keypress';
			}else{
				var bindK='keydown';
			}
			$("#FaceBox").live('mouseleave',function(e){
				$(this).hide();
			})
			$('#MateClosed').bind('click',function(){
				$('#MateCon').css('display','none');
			});
			
			$("#chatTab,#giftTab").click(function(){
				$("#MyMusic").hide();
			})

			$("#"+obj.sendChatArea).bind(bindK,function(e){

				var theEvent = window.event || e;
				var code = theEvent.keyCode || theEvent.which;
				if (obj._isSendMsg && code == 13) {
					obj.sendChat();
					return false;
				}
				return;
			});
			$("#con-close").live('click',function(){
				$(".chat-con").css({'z-index':40});
			})
			
			$(".game-box").slide({trigger:"click",delayTime:0});
		}
};

$(function(){
	Chat.init();

})
jQuery.fn.showPurview = function(settings) {
	settings = $.extend({
		purviewrank : 0
	}, settings);
	if(settings.rank==0){
		return false;
	}
	return this.each(function(){
		var $pTip = $(this).find("p >a");
		$pTip.live("click",function(){
			var id=$(this).attr("id");
			Purview._splitRank(id);
			var purviewrank=Chat.show_purviewrank;
			var rank=Chat.show_rank;
			Purview._myPurviewRank=purviewrank;//获取本身的操作等级
			Purview._myRank=rank;//获取本身的等级
			$(".chat-con").css({'z-index':51});
			showTable($(this));
			$("#purviewList").bind({
				mouseleave:function(){
					$(".chat-con").css({'z-index':40});
					$(this).empty().hide();
				}
				//click:function(){
					//$(this).empty().hide();
				//}
			})
		});
		$(".cus-con ul li").live("click",function(){
			var id=$(this).attr("id");
			Purview._splitRank(id);
			var purviewrank=Chat.show_purviewrank;
			var rank=Chat.show_rank;
			Purview._myPurviewRank=purviewrank;//获取本身的操作等级
			Purview._myRank=rank;//获取本身的等级
			showUserMenu($(this));
			$("#cuspopmenu").bind("mouseleave",function(){
				$(this).hide();
			})
		});
		function showTable(obj){
			Purview._position= obj.position();
			Purview.getPurHtml();
		}
		function showUserMenu(obj){
			Purview._position.top= obj.offset().top-65;
			Purview._position.left= obj.offset().left-132;
			Purview.getUserHtml();
		}
	});
};
