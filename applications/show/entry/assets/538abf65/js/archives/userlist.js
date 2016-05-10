
var UserList={
		staticPath:archives.staticPath+'/fontimg/common',                  //静态图片路径
		arr : {rank:0,purviewrank:0,nickname:'',uid:0},
		_position : {top:0,left:0},                      //点击后所在的定位
		_myPurviewRank : 0,                              //自己所在房间拥有的权限等级
		_myRank : 0,                                     //自己的用户等级
		UserNum:0,                                       //用户数量
		_UserNum:0,                                      //真实用户数量
		ManageNum:0,                                     //管理数量
		_Lnum:50,
		growth:50,                                       //用户增长量
		kickOut:false,                                   //是否被提出
		refreshTime:15000,                               //用户列表默认刷新时间
		userList:{},
		loadmore : false,
		userLoad:false,
		medalList:[],
		labelList:{},                                   //本房间被贴条的用户列表
		manageList:{},									//本房间房管列表
		init : function(){
			$(".chat-r").slide({titCell:".custab li",mainCell:".cus-con",trigger:"click",titOnClassName:"custabover",delayTime:0})
			//获取用户列表
			if(Chat.show_uid>0){
				this.addUserList();
			}else{
				this.WuserList(archives.userList);
			}
			setInterval("UserList.getUser()",this.refreshTime);
			$(".cus-con ul li").bind({
				hover:function(){
					$(this).addClass('cusover').siblings().removeClass('cusover');
				}
				
			})
			$('#prettyNum').live({
					hover:function(){
						 $(this).find('span').css('display','block');
					},
					blur:function(){
						$(this).find('span').css('display','none');
					}
			})
			

		},
		_splitRank : function(id){
			if(id && id.search(/\|\*\|/i)>0){
				var id_arr=id.split("|*|");
				this.arr.nickname=id_arr[1];
				this.arr.uid=id_arr[0];//用户id
				this.arr.purviewrank=id_arr[3];
				this.arr.rank=id_arr[2];
				this.arr.monthcard=id_arr[5];
				this.arr.label=id_arr[4];
				if(id_arr.length>6){
					var medal=new Array();
					for(i=0;i<(id_arr.length-6);i++){
						medal[i]=id_arr[i+6];
					}
					this.medal=medal;
				}else{
					this.medal='';
				}
			}
		},
		//给他贴条
		showLabel:function(){
			if(Purview.arr.uid>0){
				if(Purview.arr.uid==Chat.show_uid){
					$("#SucMove .popcon").empty().html('<p class="oneline">不能给自己贴条</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
					$.mask.show('SucMove',3000);
				}else{
					$.ajax({
						type:"POST",
						url:'index.php?r=/archives/getLabelList',
						dataType:"text",
						success:function(data){
							if(data){
								$('#StickBox').empty().html(data);
							}
						}
					})
					$("#Stick").show();
				}
			}else{
				$.User.loginController('login');
				return false;
			}

		},
		stickLabel:function(prop_id){
			$.ajax({
				type:"POST",
				url:'index.php?r=/archives/stickLabel',
				dataType:"json",
				data:{prop_id:prop_id,to_uid:Purview.arr.uid,archives_id:archives.archives_id},
				success:function(data){
					if(data){
						$.mask.hide('Stick');
						if(data.flag==2){
							var text='<ul class="paysong"><li><p class="otline">'+data.message+'</p></li><li class="clearfix"><input class="fleft shiftbtn" type="button" onclick="UserList.confirmStickLabel('+prop_id+')" value="确&nbsp;&nbsp;定"><input onClick="$.mask.hide(\'SucMove\');" class="shiftbtn" type="button" value="取&nbsp;&nbsp;消"></li></ul>';
							$("#SucMove .popcon").empty().html(text);
							$("#SucMove").show();
						}else{
							$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
							$.mask.show('SucMove',3000);
						}
					}
				}
			})
		},
		confirmStickLabel:function(prop_id){
			$.mask.hide("SucMove");
			var obj=this;
			$.ajax({
				type:"POST",
				url:'index.php?r=/shop/buyLabel',
				data:{prop_id:prop_id,to_uid:Purview.arr.uid,archive_id:archives.archives_id},
				dataType:"json",
				success:function(data){
					if(data){
						$.mask.hide('Stick');
						if(data.flag==1){
							$.User.refershWebLoginHeader();
						}
						$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
					}
				}
			})
		},
		//移除贴条
		removeLabel:function(){
			if(Chat.show_uid!=Purview.arr.uid){
				$("#SucMove .popcon").empty().html('<p class="oneline">只能揭除本人贴条</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
			}else{
				$.ajax({
					type:"POST",
					url:'index.php?r=/archives/removeLabel',
					data:{to_uid:Purview.arr.uid,archives_id:archives.archives_id},
					dataType:"json",
					success:function(data){
						if(data){
							if(data.flag==1){
								$.User.refershWebLoginHeader();
								$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
								$.mask.show('SucMove',3000);
							}else if(data.flag==-1){
								if(domain_type == 'tuli'){
									$.get(
											tuli_uinfo_url,
											{'token':tuli_token},
											function(e){
												if(e.data.user_type==1){
													$.mask.show('SucMove');
													window.Tuli.pay();
												}else{
													$("#SucMove .popcon").empty().html('<ul class="paysong"><li><p style="text-align:center">您的帐户余额不足</p></li><li class="clearfix"><input class="cancelbtn" type="button" onclick="goExchange(\'_self\')" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>');
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
								$.mask.show('SucMove',3000);
							}
							
						}
					}
				})
			}
		},
		//贴条添加到用户列表中显示
		stickUserLabel:function(uid,img){
			var labelList=archives.labelList;
			alert(labelList.length);
			
		},
		//移除用户列表中用户的贴条
		removeUserLabel:function(uid){
			var obj=this;
			$.ajax({
					type:"POST",
					url:"index.php?r=archives/removeUserLabel",
					data:{archives_id:archives.archives_id,uid:uid},
					dataType:"json",
					success:function(data){
						if(data){
							obj.WuserList(data);
						}
					}
				})
		},
		getUser:function(){
			this.loadmore=false;
			this._Lnum=50;
			this.getUserList();
		},
		getUserList : function(){
			var obj=this;
			var num;
			if(obj.loadmore==true){
				num=obj._Lnum;
			}else{
				num=50;
			}
			if(obj.kickOut==false){
				$.ajax({
					type:"POST",
					url:"index.php?r=archives/getUserList",
					data:{archives_id:archives.archives_id,num:num},
					dataType:"json",
					success:function(data){
						if(data){
							obj.WuserList(data);
						}
					}
				})
			}


		},
		WuserList :  function(data){
			if(data){
				this.userList=data;
				$("#user").empty();
				$("#manage").empty();
				var text=text_manage=text_guardian='';
				var obj=this;
				var manage_count=dotey_count=0;
				if(data.dotey!=null&&typeof(data.dotey)!=undefined){
					dotey_count=data.dotey.length;
					for(j=0;j<data.dotey.length;j++){
						var doteylabel='0';
						if(data.dotey[j].lb!=null&&typeof(data.dotey[j].lb)!=undefined){
							doteylabel=1;
						}
						var family_medal = '';
						if(data.dotey[j].fp!=null && data.dotey[j].fp.medal != ''){
							family_medal = '<img class="fleft huipic" src="'+data.dotey[j].fp.medal+'" />';
						}
						var dNum=dTip=''
						if(data.dotey[j].num!=null&& data.dotey[j].num!=undefined){
							dNum=data.dotey[j].num.n;
							if(data.dotey[j].num.s!=null&& data.dotey[j].num.s!=undefined){
								dTip=data.dotey[j].num.s;
							}
						}
						var dMedal=_dMedal='';
						if(data.dotey[j].md!=null&&typeof(data.dotey[j].md)!=undefined){
							for(p=0;p<data.dotey[j].md.length;p++){
								dMedal+='|*|'+data.dotey[j].md[p];
							}
							_dMedal='<img class="fleft medal" src="'+archives.imgSite+data.dotey[j].md[0]+'">';
						}
						text+='<li id="'+data.dotey[j].uid+'|*|'+data.dotey[j].nk+'|*|'+data.dotey[j].rk+'|*|'+data.dotey[j].pk+'|*|'+doteylabel+'|*|'+data.dotey[j].mc+'|*|'+dNum+'|*|'+dTip+dMedal+'"><div class="cus-first clearfix">'+obj._showListRank(data.dotey[j].rk,data.dotey[j].pk,data.dotey[j].uid,0)+obj._showVip(data.dotey[j].vip)+obj._showGoodNum(data.dotey[j].num)+'</div><div class="cus-secd clearfix">'+family_medal+_dMedal+'<p class="fleft">'+data.dotey[j].nk+'</p></div>'+obj.showUserLabel(data.dotey[j].lb)+'</li>';
						text_manage+='<li id="'+data.dotey[j].uid+'|*|'+data.dotey[j].nk+'|*|'+data.dotey[j].rk+'|*|'+data.dotey[j].pk+'|*|'+doteylabel+'|*|'+data.dotey[j].mc+'|*|'+dNum+'|*|'+dTip+dMedal+'"><div class="cus-first clearfix">'+obj._showListRank(data.dotey[j].rk,data.dotey[j].pk,data.dotey[j].uid,0)+obj._showVip(data.dotey[j].vip)+obj._showGoodNum(data.dotey[j].num)+'</div><div class="cus-secd clearfix">'+family_medal+_dMedal+'<p class="fleft">'+data.dotey[j].nk+'</p></div></li>';
					}
				}
				if(data.manage!=null&&typeof(data.manage)!=undefined){
					manage_count=data.manage.length;
					for(k=0;k<data.manage.length;k++){
						var family_medal = '';
						if(data.manage[k].fp!=null && data.manage[k].fp.medal != ''){
							family_medal = '<img class="fleft huipic" src="'+data.manage[k].fp.medal+'"  />';
						}
						var managelabel='0';
						if(data.manage[k].lb!=null&&typeof(data.manage[k].lb)!=undefined){
							managelabel=1;
						}
						var mNum=mTip=''
						if(data.manage[k].num!=null&& data.manage[k].num!=undefined){
							mNum=data.manage[k].num.n;
							if(data.manage[k].num.s!=null&& data.manage[k].num.s!=undefined){
								mtip=data.manage[k].num.s;
							}
						}
						var mMedal=_mMedal='';
						if(data.manage[k].md!=null&&typeof(data.manage[k].md)!=undefined){
							for(p=0;p<data.manage[k].md.length;p++){
								mMedal+='|*|'+data.manage[k].md[p];
							}
							_mMedal='<img class="fleft medal" src="'+archives.imgSite+data.manage[k].md[0]+'">';
						}
						text_manage+='<li id="'+data.manage[k].uid+'|*|'+data.manage[k].nk+'|*|'+data.manage[k].rk+'|*|'+data.manage[k].pk+'|*|'+data.manage[k].mc+mMedal+'"><div class="cus-first clearfix">'+obj._showListRank(data.manage[k].rk,data.manage[k].pk,data.manage[k].uid,0)+obj._showVip(data.manage[k].vip)+obj._showGoodNum(data.manage[k].num)+'</div><div class="cus-secd clearfix">'+family_medal+_mMedal+'<p class="fleft">'+data.manage[k].nk+'</p></div></li>';
					}
				}
				if(data.user){
					for(l=0;l<data.user.length;l++){
						var medal='';
						var _medal='';
						var label='0';
						if(data.user[l].lb!=null&&typeof(data.user[l].lb)!=undefined){
							label=1;
						}
						var family_medal = '';
						if(data.user[l].fp!=null && data.user[l].fp.medal != ''){
							family_medal = '<img class="fleft huipic" src="'+data.user[l].fp.medal+'" style="margin-bottom:-4px;" />';
						}
						var gNum=gTip='';
						if(data.user[l].num!=null&& data.user[l].num!=undefined){
							gNum=data.user[l].num.n;
							if(data.user[l].num.s!=null&& data.user[l].num.s!=undefined){
								gTip=data.user[l].num.s;
							}
						}
						if(data.user[l].md!=null&&typeof(data.user[l].md)!=undefined){
							for(p=0;p<data.user[l].md.length;p++){
								 medal+='|*|'+data.user[l].md[p];
							}
							_medal='<img class="fleft medal" src="'+archives.imgSite+data.user[l].md[0]+'">';
						}
						text+='<li id="'+data.user[l].uid+'|*|'+data.user[l].nk+'|*|'+data.user[l].rk+'|*|'+data.user[l].pk+'|*|'+label+'|*|'+data.user[l].mc+'|*|'+gNum+'|*|'+gTip+'|*|'+medal+'"><div class="cus-first clearfix">'+obj.showUserRank(data.user[l].rk,data.user[l].pk,data.user[l].uid,data.user[l].st)+obj._showVip(data.user[l].vip)+obj._showGoodNum(data.user[l].num)+'</div><div class="cus-secd clearfix">'+family_medal+_medal+'<p class="fleft">'+data.user[l].nk+'</p></div>'+obj.showUserLabel(data.user[l].lb)+'</li>';

					}
				}
				obj.UserNum=data.total;
				if(data.total>500&&obj._Lnum>500){
					text+="<li>还有"+(obj.UserNum-500)+"位游客</li>"
				}

				manage_count=dotey_count+manage_count;
				var text_more='<a class="lodemore" href="javascript:UserList.LoadUser()">显示更多</a>';
				$("#user_count").html(data.total);//更新房间用户数
				$("#manage_count").html(manage_count);//更新房间管理数
				if(data.total>obj._Lnum){
					$("#user").html(text+text_more);
				}else{
					$("#user").html(text);
				}
				$("#manageList").html(text_manage);
			}else{
				$("#user").html("<li><img style=\"margin-left:30px;\" src=\""+staticPath+"/loading.gif\" /></li>");
			}
		},

		//载入剩下的用户
		LoadUser :function(){
			this.loadmore=true;
			if(this._Lnum>=this.UserNum){
				this._Lnum=this.UserNum;
			}else{
				this._Lnum=this._Lnum+50;
			}
			this.getUserList();
		},
		//登陆用户写入自己的用户列表中
		addUserList:function(){
			if(Chat.show_uid>0){
				var obj=this;
				$.ajax({
					type:"POST",
					url:"index.php?r=archives/addUserToList",
					data:{uid:Chat.show_uid,archives_id:archives.archives_id},
					dataType:"json",
					success:function(data){
						if(data){
							if(obj.kickOut==false){
								obj.WuserList(data);
							}
						}
					}
				})

			}

		},
		//判断是否为数字
		checkInt : function (num){
		   var re = /^[1-9]+[0-9]*]*$/
		   return re.test(num)
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
		//列表中显示用户等级
		_showListRank:function(r,pr,u,s){
			if(u==0){
				return '';
			}
			var rank_css='',star_css='';
			if(s>0){
				star_css='<img class="snownum" src="'+this.staticPath+'/s'+s+'.gif"/>';
			}
			if(pr==2){
				rank_css='<em class="fleft lvlr lvlr-'+r+'">'+star_css+'</em><em class="fleft ver ver-5"></em>';
			}else if(pr==3){
				rank_css='<em class="fleft lvlo lvlo-'+r+'">'+star_css+'</em><em class="fleft ver ver-4"></em>';
			}else if(pr==4){
				rank_css='<em class="fleft lvlr lvlr-'+r+'">'+star_css+'</em><em class="fleft ver ver-3"></em>';
			}else{
				rank_css='<em class="fleft lvlr lvlr-'+r+'">'+star_css+'</em>';
				
			}
			return rank_css;
		},
		showUserRank:function(r,pr,u,s){
			if(u==0){
				return '';
			}
			var rank_css='',star_css='';
			if(s>0){
				star_css='<img class="snownum" src="'+this.staticPath+'/s'+s+'.gif"/>';
			}
			rank_css='<em class="fleft lvlr lvlr-'+r+'">'+star_css+'</em>';
			return rank_css;
		},

		//显示用户贴条
		showUserLabel:function(data){
			var label='';
			if(data){
				label+='<span class="sticker"><img src="'+archives.imgSite+data+'"/></span>';
			}
			return label;
		},
		showMonthCard:function(data){
			var month=0;
			if(data){
				month=1;
			}
			return month;
		},
		
		//显示守护等级
		_showGuardianRank:function(data){
			var rank_arr = new Array("","初级守护","高级守护","超级守护");
			if(!this.checkInt(data)){
				data=0;
			}
			var label='';
			if(data){
				label='<p>'+rank_arr[data]+'</p>';
			}
			return label;
		},

		//显示vip用户图标
		_showVip:function(data){
			var vip='';
			if(data!=null&&typeof(data)!=undefined){
				vip='<em class="fleft ver ver-'+data+'"></em>';

			}
			return vip;
		},
		//用户列表中显示靓号
		_showGoodNum:function(data){
			var text='';
			if(data!=null&&typeof(data)!=undefined){
				if(data.n!=null &&typeof(data.n)!=undefined){
					text='<span class="fleft '+$.User.getNumCss(data.n)+'"><em>靓</em>'+data.n+'</span>';
				}
			}
			return text;
		},

		//显示家族守护
		_showGuardianTip:function(data){
			var guardian='';
			if(data){
				if(data.vt>timestamp){
					guardian='<em class="ver ver-9"></em>';
				}
			}
			return guardian;
		}


};
$(function(){
	UserList.init();

})
