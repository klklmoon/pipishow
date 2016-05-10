var imgIas;
var Show={
		doteyId:0,  		//主播uid
		doteyRank:0, 		//主播等级
		live_status:0,  	//直播状态
		text_limit:80,      //公告文本限制
		refreshTime:15000,  //主播直播时间更新间隔
		init:function(){
			this.doteyId=$.User.getSingleAttribute('uid',true);
			this.doteyRank=$.User.getSingleAttribute('rk',true);
			this.live_status=archives.live_status;
			$('#ControlBtn').toggle(function(){
				$('#ControlBox').css('height','21px');
				$('#ControlCon').hide();
				$('#ControlBtn').animate({top:'-22px'},'fast').removeClass('posbtn');
				$('.sate').text('展开控制栏');
				$('#Livebg').removeClass('bgdown');
			},function(){
				$('#ControlBox').css('height','67px');
				$('#ControlCon').show();
				$('#ControlBtn').animate({top:'47px'},'fast').addClass('posbtn');
				$('.sate').text('收起控制栏');
				$('#Livebg').addClass('bgdown');
			});
			$('#BgSetTab').slide({titCell:".bgset-hd label",mainCell:".bgset-bd",trigger:"click",delayTime:0});


			$("#GiveNameText").click(function(){
				$(".giftnamefram").show();
			})
			$('.editbtn').live('click',function(){
				$(this).siblings('.cast-time').hide().siblings('.cast-input').show();
			});
			if(!$.browser.msie){
				$("#RModel").hide();
			}
			$('#RModel').live('click',function(){
				if(confirm("注意!在直播中切换输出控件会导致画面中断，请在更改前先告知观众一声.")){
					$("#actx-player").show();
					$("#flash-player").hide();
					$('#LModel').css('background','none');
					$(this).css('background','url('+archives.staticPath+'/fontimg/common/rover.png) no-repeat');
					$("#ifflash").empty();
					$("#ifflash").attr("src",'');
					$("#ifactx").attr("src",archives.staticPath+'/swf/actxRecordPlayer.html?'+Math.random());
				}
			});
			$('#LModel').live('click',function(){
				if(confirm("注意!在直播中切换输出控件会导致画面中断，请在更改前先告知观众一声.")){
					$("#flash-player").show();
					$("#actx-player").hide();
					$('#RModel').css('background','none');
					$(this).css('background','url('+archives.staticPath+'/fontimg/common/lover.png) no-repeat');
					$("#ifactx").empty();
					$("#ifactx").attr("src",'');
					$("#ifflash").attr("src",archives.staticPath+'/swf/flashRecordPlayer.html?'+Math.random());
				}
			});
			setInterval("Show.getArchivesLiveTime()",this.refreshTime);

		},
		//检查是否是房间拥有者
		doteyCheck:function(){
			if(!this.doteyId||this.doteyId=='undefined'){
				$.User.loginController('login');
				return false;
			}
			if(archives.dotey.uid!=this.doteyId){
				$("#SucMove .popcon").empty().html('<p class="oneline">无主播权限</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return false;
			}
		},
		actxRecord:function(){
			if(confirm("注意!在直播中切换输出控件会导致画面中断，请在更改前先告知观众一声.")){
				$("#actx-player").show();
				$("#flash-player").hide();
				$('#LModel').css('background','none');
				$(this).css('background','url('+archives.staticPath+'/fontimg/common/rover.png) no-repeat');
				$("#ifactx").attr(archives.staticPath+'/swf/actxRecordPlayer.html?'+Math.random());
			}
		},
		showLiveNotice:function(type){
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/getLiveNotice',
				data:{archives_id:archives.archives_id,type:type},
				dataType:"json",
				success:function(data){
					if(data){
						$.mask.show('LiveNotice');
						$("#start_time").val(data.start_time);
						$("#liveSubject").val(data.sub_title);
						$("#startType").val(data.type);
					}else{
						$("#SucMove .popcon").empty().html('<p class="oneline">档期异常</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
					}
				}
			})
		},
		//确认直播预告
		liveNotice:function(){
			var start_time=$("#start_time").val();
			var liveSubject=$("#liveSubject").val();
			var type=$("#startType").val();
			if(start_time&&liveSubject){
				$.ajax({
					type:'POST',
					url:'index.php?r=dotey/addLiveNotice',
					data:{archives_id:archives.archives_id,start_time:start_time,liveSubject:liveSubject,type:type},
					dataType:"json",
					success:function(data){
						if(data){
							$("#liveNoticeTips").hide();
							$.mask.hide('LiveNotice');
							$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
							$.mask.show('SucMove',3000);
							if(data.flag==1){
								if(type=='true'){
									$("#modifyLive").val('结 束 直 播');
									$("#modifyLive").attr('onclick','Show.stopLive(true)');
									$("#ControlBtn .time span").removeClass('didnot');
									$("#ControlBtn .time span").removeClass('didend');
									$("#ControlBtn .time span").addClass('didgo');
									$("#ControlBtn .time span").empty().text('已播时间：');
									$("#ControlBtn .time i").empty().text('00时00分');
									$("#head_subject_title").text('['+data.data.start_time+'开播] '+data.data.sub_title);
									Show.setRecord();
									if(data.data.display==1){
										$("#coverTips").show();
									}
								}else{
									$("#ControlBtn .time span").removeClass('didend');
									$("#ControlBtn .time span").removeClass('didgo');
									$("#ControlBtn .time span").addClass('didnot');
									$("#ControlBtn .time span").empty().text('未开播：');
									$("#ControlBtn .time i").empty().text('00时00分');
									$("#head_subject_title").text('['+data.data.start_time+'开播] '+data.data.sub_title);
									if(data.data.display==1){
										$("#coverTips").show();
									}
								}
								
							}
						}

					}

				})
			}
		},
		getArchivesLiveTime:function(){
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/getArchivesLiveTime',
				data:{archives_id:archives.archives_id},
				dataType:"json",
				success:function(data){
					if(data){
						if(data.status==1){
							$("#ControlBtn .time span").removeClass('didnot');
							$("#ControlBtn .time span").removeClass('didend');
							$("#ControlBtn .time span").addClass('didgo');
							$("#ControlBtn .time span").empty().text('已播时间：');
							$("#ControlBtn .time i").empty().text(data.time);
						}else{
							$("#ControlBtn .time span").removeClass('didgo');
							$("#ControlBtn .time span").removeClass('didend');
							$("#ControlBtn .time span").addClass('didnot');
							$("#ControlBtn .time span").empty().text('未开播：');
							$("#ControlBtn .time i").empty().text('00时00分');
						}
					}
				}
			})
		},
		//重新开始直播
		reStartLive:function(){
			if(Chat.show_uid<=0){
				$.User.loginController('login');
				return false;
			}
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/reStartLive',
				data:{archives_id:archives.archives_id},
				dataType:'json',
				success:function(data){
					$.mask.hide('SetSuc');
					if(data.flag>0){
						var text='';
						if(data.flag==2){
							text+='<p>设定节目预告开播</p><p class="clearfix"><span class="fleft">时间：</span> <em class="fleft pink cast-time">立即开始</em></p><p class="clearfix"><span class="fleft">标题：</span><input class="fleft cast-input" style="display:block;" type="text" id="sub_title"></p><p class="cast-explain">填写节目预告</p>';
						}
						if(data.flag==1){
							text+='<p>使用节目预告开播</p><p class="clearfix"><span class="fleft">时间：</span><em class="fleft pink cast-time">'+data.data.time+'</em><input class="fleft cast-input" id="restart_time" onClick="javascript:ShowCalendar(this.id,1)" type="text" value="'+data.data.time+'"><a href="javascript:void(0);" class="fleft editbtn"><img src="'+archives.staticPath+'/fontimg/common/editicon.jpg"></a></p><p class="clearfix"><span class="fleft">标题：</span><em class="fleft pink cast-time">'+data.data.sub_title+'</em><input class="fleft cast-input" id="sub_title" value="'+data.data.sub_title+'" type="text"><a href="javascript:void(0);" class="fleft editbtn"><img src="'+archives.staticPath+'/fontimg/common/editicon.jpg"></a></p>';
						}
						text+='<p><input class="surebtn" onclick="Show.confirmStartLive()" type="button" value="开始直播 "></p>';
						$("#CastIng .popcon").empty().html(text);
						$.mask.show('CastIng');
					}else{
						$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
					}
				}
			})
		},
		confirmStartLive:function(){
			if(Chat.show_uid<=0){
				$.User.loginController('login');
				return false;
			}
			var restart_time=$("#restart_time").val();
			var sub_title=$("#sub_title").val();
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/confirmStartLive',
				data:{archives_id:archives.archives_id,restart_time:restart_time,sub_title:sub_title},
				dataType:'json',
				success:function(data){
					$.mask.hide('CastIng');
					if(data.flag==1){
						$("#modifyLive").val('结 束 直 播');
						$("#modifyLive").attr('onclick','Show.stopLive()');
						$("#ControlBtn .time span").removeClass('didnot');
						$("#ControlBtn .time span").removeClass('didend');
						$("#ControlBtn .time span").addClass('didgo');
						$("#ControlBtn .time span").empty().text('已播时间：');
						$("#ControlBtn .time i").empty().text('00时00分');
					}
					$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
					$.mask.show('SucMove',3000);
				}
			})
		},
		//结束直播提示
		stopLive:function(){
			this.doteyCheck();
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/stopLive',
				data:{archives_id:archives.archives_id},
				dataType:'json',
				success:function(data){
					if(data){
						Show.setRecord();
						var text='<p>开播中</p><p class="clearfix"><span class="fleft">开播时间：</span> <em class="fleft pink cast-time">'+data.data.live_time+'</em></p><p class="clearfix"><span class="fleft">已播：</span><em class="fleft pink cast-time">'+data.data.duration+'</em></p><p class="clearfix"><span class="fleft">标题：</span><em class="fleft pink cast-time">'+data.data.sub_title+'</em></p> <p><input class="surebtn" type="button" onclick="Show.confirmStopLive()" value="结束直播 "></p>'
						$("#CastIng .popcon").empty().html(text);
						$.mask.show('CastIng');
					}
				}
			})
		},
		//开始直播
		startLive:function(status){
			Show.doteyCheck();
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/startLive',
				data:{archives_id:archives.archives_id},
				dataType:"json",
				success:function(data){
					if(data){
						if(data.flag==1){
							$("#modifyLive").val('结 束 直 播');
							$("#modifyLive").attr('onclick','Show.stopLive()');
							$("#ControlBtn .time span").removeClass('didnot');
							$("#ControlBtn .time span").removeClass('didend');
							$("#ControlBtn .time span").addClass('didgo');
							$("#ControlBtn .time span").empty().text('已播时间：');
							$("#ControlBtn .time i").empty().text('00时00分');
							Show.setRecord();
							if(data.data.display==1){
								$("#coverTips").show();
							}
							$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
							$.mask.show('SucMove',3000);
						}else if(data.flag==-1){
							Show.showLiveNotice(true);
						}else{
							$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
							$.mask.show('SucMove',3000);
						}
					}
				}
			})
		},
		confirmStopLive:function(){
			this.doteyCheck();
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/confirmStopLive',
				data:{archives_id:archives.archives_id},
				dataType:"json",
				success:function(data){
					if(data){
						$.mask.hide('CastIng');
						if(data.flag==1){
							$("#liveNoticeTips").show();
							$("#modifyLive").val('开 始 直 播');
							$("#modifyLive").attr('onclick','Show.startLive()');
						}
						$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
					}
				}
			})
		},

		showCover:function(){
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/showCover',
				dataType:'text',
				success:function(data){
					if(data){
						$("#Covers .popcon").empty().html(data);
						$.mask.show('Covers');
					}
				}
			})
			//$('#editorpic').empty();
			//var url=$("#cover_from").attr('action');
			//url+='&s='+Math.random();
			//$("#cover_from").attr('action',url);
			//$("#perviewCover").attr('src','index.php?r=dotey/perviewCover&s'+Math.random());
			//$.mask.show('Covers');
		},
		//房间通告
		roomNotice:function(){
			$('#TalkNotice').show();
		},
		checkUrl:function (url){
			var arr=url.split('/');
			var msg=false;
			for(i=0;i<arr.length;i++){
				if(arr[i]==window.location.host){
					msg=true;
				}
			}
			return msg;
		},
		modifyNotice:function(){
			var commonNotice=$("#commonNotice").val();
			var privateNotice=$("#privateNotice").val();
			var commonUrl=$("#commonUrl").val();
			var privateUrl=$("#privateUrl").val();
			if(!commonNotice&&!privateNotice){
				$.mask.hide("TalkNotice");
				$("#SucMove .popcon").empty().html('<p class="oneline">公聊通告和私聊通告不能都为空</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			if(commonUrl!=''&&commonUrl!=null&&typeof(commonUrl)!='undefined'&&commonUrl!='http://'){
				if(!this.checkUrl(commonUrl)){
					$.mask.hide("TalkNotice");
					$("#SucMove .popcon").empty().html('<p class="oneline">公聊通告链接不合法</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
					$.mask.show('SucMove',3000);
					return;
				}
			}
			if(privateUrl!=''&&privateUrl!=null&&typeof(privateUrl)!='undefined'&&privateUrl!='http://'){
				if(!this.checkUrl(privateUrl)){
					$.mask.hide("TalkNotice");
					$("#SucMove .popcon").empty().html('<p class="oneline">私聊通告链接不合法</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
					$.mask.show('SucMove',3000);
					return;
				}
			}
			
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/modifyNotice',
				data:{archives_id:archives.archives_id,commonNotice:commonNotice,commonUrl:commonUrl,privateNotice:privateNotice,privateUrl:privateUrl},
				dataType:"json",
				success:function(data){
					if(data){
						$.mask.hide("TalkNotice");
						$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
						if(data.flag==1){
							if(data.data.notice!=null||typeof(data.data.notice)!='undefined'){
								if(data.data.url!=null||typeof(data.data.url)!='undefined'){
									var common_text='<a href="'+data.data.url+'" title="'+data.data.notice+'">'+data.data.notice+'</a>';
								}else{
									var common_text=data.data.notice;
								}
								$("#common_notice").empty().html(common_text);

							}
							if(data.data.private_notice!=null||typeof(data.data.private_notice)!='undefined'){
								if(data.data.private_url!=null||typeof(data.data.private_url)!='undefined'){
									var private_text='<a class="pink" href="javascript:void(0)" id="'+data.data.uid+'|*|'+data.data.nickname+'|*|'+data.data.rank+'|*|3">'+data.data.nickname+'</a>对您说：<a href="'+data.data.private_url+'" title="'+data.data.private_notice+'">'+data.data.private_notice+'</a>';
								}else{
									var private_text='<a class="pink" href="javascript:void(0)" id="'+data.data.uid+'|*|'+data.data.nickname+'|*|'+data.data.rank+'|*|3">'+data.data.nickname+'</a>对您说：'+data.data.private_notice;
								}
								$("#private_notice").empty().html(private_text);
							}

						}
					}
				}
			})
		},
		textup:function (id){
			var word = $("#"+id).val();
			//判断ID为text的文本区域字数是否超过180个
			if(word.length > Show.text_limit){
				$("#"+id).val(wor.substring(0,Show.text_limit));
			}
		},
		textdown:function (e,id){
			textevent = e ;
			if(textevent.keyCode == 8){
				return;
			}
			if($("#"+id).val().length >=Show.text_limit) {
				alert("不超过"+Show.text_limit+"个汉字")
				if(!document.all){
					textevent.preventDefault();
				}else{
					textevent.returnValue = false;
				}
			}
		},
		//发言设置
		chatSet:function(){
			var tourist_set=$('input:checkbox[name="tourist_set"]:checked').val();
			var global_set=$('input:checkbox[name="global_set"]:checked').val();
			tourist_set=tourist_set?0:1;
			global_set=global_set?0:1;
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/chatSet',
				data:{archives_id:archives.archives_id,tourist_set:tourist_set,global_set:global_set},
				dataType:"json",
				success:function(data){
					if(data){
						$('#SaySet').hide();
						$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
						if(data.flag==1){
							Chat.tourist_set=tourist_set;
							Chat.global_set=global_set;
						}
					}
				}
			})
		},
		//转移观众
		moveViewer:function(){
			var target_uid=$("#target_uid").val();
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/moveViewer',
				data:{archives_id:archives.archives_id,target_uid:target_uid},
				dataType:"json",
				success:function(data){
					if(data){
						$('#MoveViewer').hide();
						$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
					}
				}
			})
		},
		showBgSet:function(){
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/showBgSet',
				data:{archives_id:archives.archives_id},
				dataType:'text',
				success:function(data){
					if(data){
						$.mask.show('BgSet');
						$("#BgSet .bgpic-set").html(data);
					}else{
						$("#SucMove .popcon").empty().html('<p class="oneline">获取系统背景异常</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
					}
				}
			})
		},
		BgSet:function(){
			var paddtop=$("#paddtop").val();
			var bgImg=$("#bgImg").val();
			if(paddtop<0||paddtop>50){
				$('#BgSet').hide();
				$("#SucMove .popcon").empty().html('<p class="oneline">高度限制0-50</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
			}
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/bgSet',
				data:{archives_id:archives.archives_id,paddtop:paddtop,bgImg:bgImg},
				dataType:'json',
				success:function(data){
					if(data){
						$('#BgSet').hide();
						$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
						if(data.flag==1){
							ChangeBg(data.data.bgurl);
						}
					}

				}


			})

		},
		DefaultBgSet:function(bgImg_id){
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/defaultBgSet',
				data:{archives_id:archives.archives_id,bgImg_id:bgImg_id},
				dataType:'json',
				success:function(data){
					if(data){
						$('#BgSet').hide();
						$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
						if(data.flag==1){
							ChangeBg(data.data.bgurl,data.data.bgcolor);
						}
					}

				}
			})
		},
		copyUrl: function(title,url){	
			$.mask.hide('ToFriend');
			var u=url?url:location.href;
		    var txt="推荐你看“"+title+"”的视频互动直播节目直播间地址:"+u;
		    if(window.clipboardData) {
		    	$("#SucMove .popcon").empty().html('<p class="oneline">复制成功，快转发给好友吧~</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
		    	window.clipboardData.clearData();
		    	clipboardData.setData("Text", txt);
		    	
		    }
		    else if(navigator.userAgent.indexOf("Opera") != -1){
		    	window.location = txt;
		    }
		    else if (window.netscape){
		    	try {
		    		netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
		    	}catch (e){
		    		alert("被浏览器拒绝！\n请在浏览器地址栏输入’about:config’并回车\n然后将 ‘signed.applets.codebase_principal_support’设置为’true’");
		    	}
		    	var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
		    	if (!clip) return;
		    		var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
		    	if (!trans) return;
		    		trans.addDataFlavor('text/unicode');
		    	var str = new Object();
		    	var len = new Object();
		    	var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
		    	var copytext = txt; str.data = copytext; trans.setTransferData('text/unicode',str,copytext.length*2);
		    	var clipid = Components.interfaces.nsIClipboard;
		    	if (!clip) return false;
		    	$("#SucMove .popcon").empty().html('<p class="oneline">复制成功，快转发给好友吧~</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
		    	clip.setData(trans,null,clipid.kGlobalClipboard); 
		    		
		    }
	    },
		allowSong:function(){
			if(Chat.show_uid<=0){
				$.User.loginController('login');
				return false;
			}
			if(Chat.show_uid!=archives.dotey.uid){
				$("#SucMove .popcon").empty().html('<p class="oneline">没有操作权限</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return false;
			}
			$.ajax({
				type:'POST',
				url:'index.php?r=dotey/allowSong',
				data:{archives_id:archives.archives_id},
				dataType:'json',
				success:function(data){
					if(data){
						if(data.flag==1){
							$("#allowSong").addClass('nosong');
						}
						if(data.flag==2){
							$("#allowSong").removeClass('nosong');
						}
						$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
					}
				}
			})
		},
		setRecord:function(){
			if(archives.live_status!=1){
				archives.live_status=1;
			}else{
				archives.live_status=2;
			}
			window.frames['flashiframe'].setModel();
		},
}
$(function(){
	Show.init();
})

$("#backImg").change(function(){
	if($("#backImg").val() != ''){
		$("#backUpload").show();
		$("#back_form").submit();
	}
});
$("#tarframe").load(function(){
	var data = $(window.frames['tarframe'].document.body).find("#msg").html();
	if(data != null){
		$("#backUpload").empty().html(data);
	}
});

$("#coverImg").live('change',function(){
	if($("#coverImg").val() != ''){
		$(this).find("#backUpload").show();
		var url=$("#cover_from").attr('action');
		url+='&s='+Math.random();
		$("#cover_from").attr('action',url);
		$("#cover_from").submit();
	}
})

$("#coverframe").load(function(){
	var obj = $(window.frames['coverframe'].document.body);
	if(obj.find("#msg").html() != null){
		var data=obj.find("#msg").html();
		$("#coverloading").empty().hide();
		$("#coverImg").val('');
		if(obj.find('#errorMsg').html()!='undefined'&&obj.find('#errorMsg').html()!=null){
			$("#coverloading").empty().html(data).show();
		}else{
			if(data){
				$('#perviewCover').attr('src','index.php?r=dotey/perviewCover/coverName/'+data);
            }
		}
	}
});
