var Gift={
		numError : '数值有误,不允许随意更改',
		doteyError : '请重新选择发送对象',
		giftError : '礼物选择有误',
		maxSendNum:100000,                       //最大送礼数量
		giftNum:100,                            //礼物最大显示量
		giftPath:'',      //礼物路径
		giftEffectSet:true,                     //礼物效果是否显示
		giftId:null,
		pipieggNum:0,
		doteyId:null,
		doteyNickname : null,
		giftNum:1,
		giftType:'common',
		_qFun : [],                             //礼物处理函数队列
		_q : [],                                //礼物数据队列
		_lFun:[],                               //幸运礼物处理函数队列
		_l:[],                                   //幸运礼物数据队列
		_tFun:[],                               //跑道礼物处理函数队列
		_t:[],                                  //跑道礼物数据队列
		init:function(){
			Gift.giftPath=archives.imgSite+'/gift';
			$(".gift-box .gift-con").slide({mainCell:"ul",effect:"leftLoop",autoPlay:false,vis:6,delayTime:300,scroll:6,prevCell:".prev",nextCell:".next",effect:"leftLoop",pnLoop:"true"});
			$('.gift-menu li').live('click',function(){
				var index=$(this).index();
				$(this).find('a').addClass('giftover').parent('li').siblings('li').find('a').removeClass('giftover');
				$('.gift-list li img').removeClass('overbd');
				$('.gift-hd .gift-con').eq(index).show().siblings().hide();
			});
			$('.gift-list li').live('click',function(){
				$(this).find('img').addClass('overbd').parents('li').siblings().find('img').removeClass('overbd');
			});
			$('#GiveNameText').click(function(){
				$('.giftnamefram').show();
			});

		    $('#userBag').click(function(){
		    	Gift.getUserBag();
		    })
		    $("#giftList").click(function(){
		    	Gift.getGiftList();
		    })
		    $('#CharmBox ul li').each(function(){
				var index=$(this).index();
				if(index>4){
					$(this).hide();
				}
			});
		    if(Chat.show_uid>0){
		    	$(".rechangebtn").attr('href',exchangeUrl);
		    	if(domain_type=='pptv'){
		    		hrefTarget = '_blank';
		    	}

		    	$(".rechangebtn").attr('target',hrefTarget);
		    }
		    
			var i=4;
			$('#DownBtn').bind('click',function(){
				i++;
				$('#CharmBox ul li').eq(i).stop(true,true).slideDown('slow');
				//document.getElementById("Livebg").style.height=getPageSize()[1]+220+64+"px";
				if(i>9){
					$('#CharmBox').find('.page').stop(true,true).slideDown('fast');
					return;
				}
			});
			$("#giftEffectSet").bind('click',function(){
				if($(this).attr('checked')=='checked'){
					$("#SucMove .popcon").empty().html('<p class="oneline">礼物显示特效已开启</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				}else{
					$("#SucMove .popcon").empty().html('<p class="oneline">礼物显示特效已关闭</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				}
				$.mask.show('SucMove',3000);
			})
			if(Chat.show_uid>0){
				setInterval("$.User.refershWebLoginHeader()",300000);
			}
			if($('#truck-content').has('li')){
				ScrollTruckText('truck-list','truck-content',1,12);
			}
			$("#truck-gift-num").bind('keyup mousedown',function(){
				Gift.changeTruckGift();
			})
			$("#truck_remark").bind('keyup mousedown',function(){
				if(Chat.strLen($(this).val())>36){
					$("#truck-confimGift").attr("disabled",true);
					$('#truck-tips').css('color','red');
					$('#truck-tips').empty().html('提醒：寄语超过18字限制，请修改');
				}else{
					$("#truck-confimGift").removeAttr("disabled");
					Gift.changeTruckGift();
				}
			})
		},

		//获取直播间礼物列表
		getGiftList:function(){
			if(archives.archives_id){
				$.ajax({
					type:'POST',
					url:'index.php?r=/archives/getGiftList',
					data:{archives_id:archives.archives_id},
					dataType:'json',
					success:function(data){
						if(data){
							if(data.nickname){
								var text='<p class="geilitext"><em class="black ellipsis">'+data.nickname+'</em><span>送给</span><em class="black ellipsis">'+data.d_nickname+'</em><em class="pink">'+data.gift_num+'</em><span>个 '+data.gift_name+'</span><img src="'+Gift.giftPath+'/'+data.picture+'"></p>'
								$(".gifts-box .geili em").next().empty();
								$(".gifts-box .geili em").after(text);
							}

						}
					}
				})

			}
		},
		addGiftNum:function(id){
			var sendNum=$("#"+id).val();
			sendNum=parseInt(sendNum)+1;
			if(sendNum>this.maxSendNum){
				sendNum=this.maxSendNum;
			}
			$("#gift_num").val(sendNum);
			$("#send_num").val(sendNum);
			this.giftNum=sendNum;
			var pipiegg=parseInt(this.pipieggNum*this.giftNum*10000)/10000;
			$("#pipiegg").html(pipiegg+'个皮蛋');
			this.giftNum=sendNum;
		},
		reduceGiftNum:function(id){
			var sendNum=$("#"+id).val();
			sendNum=parseInt(sendNum)-1;
			if(sendNum<1){
				sendNum=1;
			}
			$("#gift_num").val(sendNum);
			$("#send_num").val(sendNum);
			this.giftNum=sendNum;
			var pipiegg=parseInt(this.pipieggNum*this.giftNum*10000)/10000;
			$("#pipiegg").html(pipiegg+'个皮蛋');
			this.giftNum=sendNum;
		},
		changeEgg:function(){
			var giftNum=$("#gift_num").val();
			var pipiegg=parseInt(this.pipieggNum*giftNum*10000)/10000;
			$("#pipiegg").html(pipiegg+'个皮蛋');
		},
		selectGift:function(obj,giftId,price,type){
			$(".numname").hide();
			var effect=$(obj).attr('effects');
			if(effect!='undefined'&&effect!=null){
				var effectList=effect.split("|");
				var effectHtml='';
				if(effectList.length>0){
					for(i=0;i<effectList.length;i++){
						if(effectList[i].indexOf('(')){
							var effectNum=effectList[i].split("(");
							effectHtml+='<li><a href="javascript:void(0)" onclick="Gift.selectCount(this)" rel="'+effectNum[0]+'">'+effectList[i]+'</a></li>';
						}else{
							effectHtml+='<li><a href="javascript:void(0)" onclick="Gift.selectCount(this)" rel="'+effectList[i]+'">'+effectList[i]+'</a></li>';
						}
						
					}
					$(".numname ul").empty().html(effectHtml);
					$(".numname").show()
				}
			}else{
				$(".numname ul").empty();
				$(".numname").hide()
			}
			if(!giftId){
				return;
			}
			this.giftId=giftId;
			this.pipieggNum=price;
			this.giftType=type;
			var pipiegg=parseInt(this.pipieggNum*this.giftNum*10000)/10000;
			$("#send_num").val(1);
			$("#gift_num").val(1);
			$("#pipiegg").html(pipiegg+'个皮蛋');
			
			if($("#GiveNameText").val()==null||$("#GiveNameText").val()==undefined){
				if($(".giftnamefram li").length===1){
					this.doteyId=$(".giftnamefram li:first").find("a").attr("rel");
					this.doteyNickname=$(".giftnamefram li:first").text();
					$("#GiveNameText").val(this.doteyNickname);
				}else{
					if($("#GiveNameText").val()==null||$("#GiveNameText").val()==undefined){
						$(".giftnamefram").show();
					}
					
				}
			}
			
		},
		selectTruckGift:function(obj,giftId,price){
			$("#truck-gift-list").find('.on').removeClass('on');
			$(obj).addClass('on');
			this.giftId=giftId;
			this.pipieggNum=price;
			this.giftNum=$("#truck-gift-num").val();
			var pipiegg=parseInt(this.pipieggNum*this.giftNum*10000)/10000;
			$('#truck_pipiegg').empty().html('礼物总价：'+pipiegg+'皮蛋');
			$.ajax({
				type:'POST',
				url:'index.php?r=archives/checkTruckGift',
				data:{gift_id:this.giftId,'num':this.giftNum},
				dataType:'json',
				success:function(data){
					if(data){
						$('#truck-tips').empty().html(data.message);
					}
				}
			})
		},
		changeTruckGift:function(){
			this.giftNum=$("#truck-gift-num").val();
			var pipiegg=parseInt(this.pipieggNum*this.giftNum*10000)/10000;
			$('#truck_pipiegg').empty().html('礼物总价：'+pipiegg+'皮蛋');
			$.ajax({
				type:'POST',
				url:'index.php?r=archives/checkTruckGift',
				data:{gift_id:this.giftId,'num':this.giftNum},
				dataType:'json',
				success:function(data){
					if(data){
						$('#truck-tips').empty().html(data.message);
					}
				}
			})
		},
		selectCount:function(obj){
			var effetct=$(obj).attr('rel');
			$("#send_num").val(effetct);
			$(".numname").hide();
		},
		selectDotey :  function(doteyId,nick){
			if(!doteyId || !nick){
				return;
			}
			this.doteyId=doteyId;
			this.doteyNickname=nick;
			$("#GiveNameText").val(nick);
			$('.giftnamefram').hide();
		},
		selectUser:function(){
			var c;
			$(".giftnamefram ul").find("li >a").each(function(i){
				if($(this).attr('rel')==Purview.arr.uid){
					c=true;
				}
			})
			if(c!=true){
				$('.giftnamefram ul').append('<li><a href="javascript:Gift.selectDotey('+Purview.arr.uid+',\''+Purview.arr.nickname+'\')" rel="'+Purview.arr.uid+'">'+Purview.arr.nickname+'</a></li>');
			}
			$("#purviewList").hide();
			$(".cuspopmenu").hide();
			this.doteyId=Purview.arr.uid;
			this.doteyNickname=Purview.arr.nickname;
			$("#GiveNameText").val(Purview.arr.nickname);
			$("#truck_dotey_id").append('<option value="'+Purview.arr.uid+'" selected>'+Purview.arr.nickname+'</option>');
		},
		randomNum:function(n,max){
			 var myNumber=new Array();
			  n=(n>=max)?max:n;	
			  for(var i=0;i<n;i++){
			  do{
				  var a = Math.round(Math.random()*max);
			  }while(Gift.check(a,n,myNumber)){  //调用check函数,检验是否重复
				  myNumber[i] = a;
			  }
			 }
			 return myNumber;
		},
		 check:function(x,y,z){
			 for (var j=0;j<z.length;j++){
			  if(z[j]== x){ //遍历数组中的每一个值,看是否与新生成的数相等
			    return true; //如果相等,继续循环,重新生成一个随机数
			  }
			 }
			 return false;//如果不等,跳出循环,给数组内添加一个新的随机数
		},
		sendGift:function(){
			$("#confimGift").removeAttr("disabled");
			if(this.giftId<=0){
				$("#SucMove .popcon").empty().html('<p class="oneline">请选择礼物!</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			var num=$("#send_num").val();

			num = parseInt(num);

			if (num > 100000) {
				$("#SucMove .popcon").empty().html('<p class="oneline">礼物数量不能大于100000个!</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			var re = /^[1-9]+[0-9]*]*$/;
			if(!re.test(num)){
				$("#SucMove .popcon").empty().html('<p class="oneline">'+this.numError+'</p>');
				$.mask.show('SucMove',3000);
				return;
			}
			if(!this.doteyId || this.doteyId=='undefined'){
				$("#SucMove .popcon").empty().html('<p class="oneline">'+this.doteyError+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			if(Chat.islogin()){
				$("#gift_num").val(num);
				if(this.giftType=='common'){
					var pipiegg=(parseInt(this.pipieggNum*10000)*num)/10000;
					$("#giftTip").empty().html('赠送礼物需要<em class="pink" id="pipiegg">'+pipiegg+'个皮蛋</em>');
				}else{
					$("#giftTip").empty().html('本次赠送，将消耗背包中礼物。');
				}
				$.mask.show('GiftCount');
			}else{
				$.User.loginController('login');
				return false;
			}
		},
		confirmSendGift:function(){
			if(Chat.islogin()){
				$("#confimGift").attr("disabled",true);
				var giftNum=$("#gift_num").val();
				if (giftNum > 100000) {
					$("#SucMove .popcon").empty().html('<p class="oneline">礼物数量不能大于100000个!</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
					$.mask.show('SucMove',3000);
					return;
				}
				var consume_many=(parseInt(this.pipieggNum*10000)*giftNum)/10000;
				var re = /^[1-9]+[0-9]*]*$/;
				if(!re.test(giftNum)){
					$("#SucMove .popcon").empty().html('<p class="oneline">'+this.doteyError+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
					$.mask.show('SucMove',3000);
					return;
				}
				var doteyId=this.doteyId;
				var giftId=this.giftId;
				var giftType=this.giftType;
				$.ajax({
					type : "POST",
					url: "index.php?r=/archives/confirmSendGift",
					dataType: "json",
					data :{uid:Chat.show_uid,to_uid:doteyId,archivesId:archives.archives_id,giftId:giftId,giftNum:giftNum,giftType:giftType},
					success:function(data){
						if(data){
							if(data.flag==0){
								$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
								$.mask.show('SucMove',3000);
							}else if(data.flag==-1){
								if(domain_type == 'tuli'){
									$.get(
											tuli_uinfo_url,
											{'token':tuli_token},
											function(e){
												if(e.data.user_type==1){
													var text='<ul class="paysong"><li><p style="text-align:center">您的帐户余额不足</p></li><li class="clearfix"><input class="cancelbtn" type="button" onclick="window.Tuli.pay()" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>';
													$("#SucMove .popcon").empty().html(text);
												    $.mask.show('SucMove',3000);
												}else{
													var text='<ul class="paysong"><li><p style="text-align:center">您的帐户余额不足</p></li><li class="clearfix"><input class="cancelbtn" type="button" onclick="goExchange(\'_self\')" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>';
													$("#SucMove .popcon").empty().html(text);
												    $.mask.show('SucMove',3000);
												}
											},
											'json'
										);
								}else{
									$("#SucMove .popcon").empty().html('<ul class="paysong"><li><p style="text-align:center">您的帐户余额不足</p></li><li class="clearfix"><input class="cancelbtn" type="button" onclick="goExchange()" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>');
								    $.mask.show('SucMove',3000);
								}
							}else if(data.flag==-2){
								$.User.loginController('login');
							}else if(data.flag==2){
								Gift.getDoteyCharm();
								Gift.getUserBag();
								$.User.refershWebLoginHeader();
							}else if(data.flag==1){
								Gift.getDoteyCharm();
								$.User.refershWebLoginHeader();
							}
							$("#confimGift").removeAttr("disabled");
							$.mask.hide('GiftCount');
						}
					}
				})
			}
		},
		showTruckGift:function(){
			if(Chat.show_uid>0){
				$("#truck-confimGift").removeAttr("disabled");
				$.mask.show('truckGift');
			}else{
				$.User.loginController('login');
			}
		},
		confirmSendTruckGift:function(){
			$("#truck-confimGift").attr("disabled",true);
			var giftNum=$("#truck-gift-num").val();
			if (giftNum > 100000) {
				$.mask.hide('truckGift');
				$("#SucMove .popcon").empty().html('<p class="oneline">礼物数量不能大于100000个!</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			var consume_many=(parseInt(this.pipieggNum*10000)*giftNum)/10000;
			var re = /^[1-9]+[0-9]*]*$/;
			if(!re.test(giftNum)){
				$.mask.hide('truckGift');
				$("#SucMove .popcon").empty().html('<p class="oneline">'+this.doteyError+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			var doteyId=$("#truck_dotey_id").val();
			var giftId=this.giftId;
			if(giftId==null||giftId==undefined){
				$.mask.hide('truckGift');
				$("#SucMove .popcon").empty().html('<p class="oneline">请选择跑道礼物</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			var remark=$("#truck_remark").val();
			if(Chat.strLen(remark)>36){
				$.mask.hide('truckGift');
				$("#SucMove .popcon").empty().html('<p class="oneline" style="color:red;">提醒：寄语超过18字限制，请修改</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
				$.mask.show('SucMove',3000);
				return;
			}
			$.ajax({
				type : "POST",
				url: "index.php?r=/archives/confirmSendGift",
				dataType: "json",
				data :{uid:Chat.show_uid,to_uid:doteyId,archivesId:archives.archives_id,giftId:giftId,giftNum:giftNum,giftType:'common',remark:remark},
				success:function(data){
					if(data){
						if(data.flag==0){
							$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
							$.mask.show('SucMove',3000);
						}else if(data.flag==-1){
							if(domain_type == 'tuli'){
								$.get(
										tuli_uinfo_url,
										{'token':tuli_token},
										function(e){
											if(e.data.user_type==1){
												$("#SucMove .popcon").empty().html('<ul class="paysong"><li><p style="text-align:center">您的帐户余额不足</p></li><li class="clearfix"><input class="cancelbtn" type="button" onclick="window.Tuli.pay()" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>');
											    $.mask.show('SucMove',3000);
											}else{
												$("#SucMove .popcon").empty().html('<ul class="paysong"><li><p style="text-align:center">您的帐户余额不足</p></li><li class="clearfix"><input class="cancelbtn" type="button" onclick="goExchange(\'_self\')" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>');
											    $.mask.show('SucMove',3000);
											}
										},
										'json'
									);
							}else{
								$("#SucMove .popcon").empty().html('<ul class="paysong"><li><p style="text-align:center">您的帐户余额不足</p></li><li class="clearfix"><input class="cancelbtn" type="button" onclick="goExchange()" value="马&nbsp;上&nbsp;充&nbsp;值"></li></ul>');
							    $.mask.show('SucMove',3000);
							}
						}else if(data.flag==-2){
							$.User.loginController('login');
						}else if(data.flag==2){
							Gift.getDoteyCharm();
							Gift.getUserBag();
							$.User.refershWebLoginHeader();
						}else if(data.flag==1){
							Gift.getDoteyCharm();
							$.User.refershWebLoginHeader();
						}
						$("#truck-confimGift").removeAttr("disabled");
						$.mask.hide('truckGift');
					}
				}
			})
			
		},
		getDoteyCharm:function(){
			$.ajax({
				type:'POST',
				url:'index.php?r=archives/getDoteyCharm',
				data:{doteyId:archives.dotey.uid},
				dataType:'json',
				success:function(data){
					if(data){
						$("#doteyLevel").removeAttr('class');
						$("#doteyLevel").attr('class','lvlo lvlo-'+data.rank);
						$("#doteyProcess .now-rate").text(data.nowRank);
						$("#doteyProcess .total-rate").text(data.nextRank);
						$.User.rankProgress('doteyProcess',data.nxch,data.charm,data.cuch);
					}
				}
			})
		},
		//获取用户背包礼物
		getUserBag:function(){
			if(Chat.show_uid>0){
				$.ajax({
					type:'POST',
					url:'index.php?r=archives/getUserBag',
					dataType:'json',
					success:function(data){
						if(data.length>0){
							var text='<ul class="clearfix pack-list">';
							var image;
							for(i=0;i<data.length;i++){
								image=Gift.giftPath+'/'+data[i].image;
								var effects='';
								if(data[i].effects!=null&&typeof(data[i].effects)!='undefined'){
									effects='effects="'+data[i].effects+'"';
								}
								text+='<li><a href="javascript:void(0)" onclick="Gift.selectGift(this,'+data[i].gift_id+','+data[i].pipiegg+',\'bag\')" title="'+data[i].zh_name+'" '+effects+'><img src="'+image+'"></a><span>'+data[i].zh_name+'</span><i>'+data[i].num+'</i></li>';
							}
							text+='</ul>';
						}else{
							var text='<p class="gift-none">您的背包空空如也，马上参加活动赚礼物，或<a href="'+giftShopUrl+'" class="pink" target="_blank">商城</a>购买</p>';
						}
						$("#bagGiftList").empty().html(text);
						var page = 1;
						var j = 6; //每版放6张图片
						var $v_show = $(".giftList"); 
						var $v_content = $("#giftBagList");
						var len = $v_show.find("li").length;
						var v_width ='426px';
						var page_count = Math.ceil(len / j) ;
						$("span#Aright").live('click',function(){
							if( !$v_show.is(":animated") ){
								if( page == page_count ){
									$v_show.animate({ left : '0px'}, "slow");
									page = 1;
								}else{
									$v_show.animate({ left : '-='+v_width }, "slow"); 
									page++;
								}
							}
						});
						$("span#Aleft").live('click',function(){
							if( !$v_show.is(":animated") ){
								if( page == 1 ){
									$v_show.animate({ left : '-='+v_width*(page_count-1) }, "slow");
								}else{
									$v_show.animate({ left : '+='+v_width }, "slow");
									page--;
								}
							}
						});
					}
				})
				
				$("#userBag").show();
			}else{
				$("#userBag").hide();
			}
		},
		luckGiftMsg:function(aid,u,n,t_n,zh,pic,num,data){
			if(aid==archives.archives_id){
				var obj=this;
				if(data!=null&&data!=undefined&&data.length>0){
					var award=new Array();
					for(j=0;j<data.length;j++){
						award[j]=new Array(); 
						award[j][0]=data[j].id;
						award[j][1]=data[j].type;
						award[j][2]=data[j].zh_name;
						award[j][3]=data[j].award;
					}
					this._l.push(award);
					var lk=function(){ playMsg();};
					this._lFun.push(lk);
					$("#luckGift").queue('playlk',this._lFun);
					if(this._l.length==1){
						_takeOneMsg();
					}
				}
				
				var text='<p class="luckGif-text">'+t_n+' 收到了 '+n+' 送来的“'+zh+'”';
				var sn=(num>500)?500:num;
				for(i=0;i<sn;i++){
					text+='<img src="'+obj.giftPath+'/'+pic+'"/>';
				}
				text+='(共'+num+'个)';
				$("#"+Chat.commonChatArea).append(text);
				Chat.ScrollOn();
				if(data!=null&&data!=undefined&&data.length>0){
					for(k=0;k<data.length;k++){
						if(data[k].type==1||data[k].type==2){
							var adText='<p><span style="color:red">【中奖】恭喜 '+n+' 送出 '+zh+' 后喜中大奖 '+data[k].zh_name+data[k].award+'个</span></p>';
						}else if(data[k].type==3){
							var adText='<p><span style="color:red">【中奖】恭喜 '+n+' 送出 '+zh+' 后喜中 '+data[k].award+'倍大奖共'+data[k].zh_name+'个皮蛋</span></p>';
						}
						$("#"+Chat.commonChatArea).append(adText);  
						Chat.ScrollOn();
						if(u==Chat.show_uid){
							$("#"+Chat.privateChatArea).append(adText);
							Chat.ScrollOnp();
						}
					}
				}
			}
			
			
			function _takeOneMsg(){
				$("#luckGift").dequeue('playlk');
			};
			function playMsg(){
				var text='<div class="fleft bd"><ul>';
				var d=obj._l[0];
				for(i=0;i<d.length;i++){
					text+='<li><img width="28" heigth="24" class="mt5 mr5" src="'+obj.giftPath+'/'+pic+'"><span>恭喜</span> <em class="pink ellipsis">"'+n+'"</em><span>送出礼物喜中</span>';
					if(d[i][1]==1||d[i][1]==2){
						text+='大奖 '+d[i][2]+d[i][3]+'个';
					}else if(d[i][1]==3){
						text+='<img  class="mt10" src="'+archives.staticPath+'/fontimg/common/'+d[i][3]+'.png"> <span>大奖'+d[i][2]+'皮蛋</span>';
					}
					text+='</li>';
				}
				text+='</ul></div>';
				if(d.length>1){
					text+='<div class="fright hd"> <a href="javascript:void(0);" class="upBtn"></a><a href="javascript:void(0);" class="downBtn"></a> </div>';
					
				}
				$("#luckGift").empty().html(text).show();
				$("#luckGift").slide({mainCell:".bd ul",prevCell:".upBtn",nextCell:".downBtn",autoPage:true,effect:"top",autoPlay:true,vis:1});
				setTimeout(hideMsg,10000);
				
			};
			function hideMsg(){
				$("#luckGift").empty().hide();
				obj._l.shift();//播放完一个 移除一个
				if(obj._l.length>0){
					_takeOneMsg();
				}else{
					//数据播放完后 清空函数队列
					$("#luckGift").clearQueue('playlk');
				}
			}
		},
		truckGift:function(data){
			if(data){
				var obj=this;
				obj._t.push(data);
				var truck=function(){ playMsg();};
				obj._tFun.push(truck);
				$(".runway-list").queue('playTruck',obj._tFun);
				if(obj._t.length==1){
					_takeOneMsg();
				}
			}
			function _takeOneMsg(){
				$(".runway-list").dequeue('playTruck');
			};
			function playMsg(){
				var d=obj._t[0];
				$('#truck-content li').each(function(i){
					if($(this).attr('rel')!='0'&&d.replace!='0'){
						$(this).attr('rel','0');
					}
				})
				var text='<li rel="'+d.replace+'"><a target="_blank" href="/'+d.to_uid+'">';
				text+='<img src="'+obj.giftPath+'/'+d.picture+'"><em class="pink">'+d.nickname+'</em>   送给    <em class="pink">'+d.to_nickname+'</em>'+d.gift_num+'个'+d.zh_description+' ：<em class="pink">'+d.remark+'</em>';
				
				var num=d.gift_num>10?10:d.gift_num;
				for(i=0;i<num;i++){
					text+='<img src="'+obj.giftPath+'\/'+d.picture+'">';
				}
				text+='</a></li>';
				$("#truck-content").append(text);
				$(".runway-con").show();
				$('.runway-btn').addClass('crun').find('a').animate({top:'13px'},'fast');
				obj._t.shift();//播放完一个 移除一个
				if(obj._t.length>0){
					_takeOneMsg();
				}else{
					//数据播放完后 清空函数队列
					$(".runway-list").clearQueue('playTruck');
				}
				//跑道消息
				var PosInit,showWidth;
				showWidth=$('#truck-list').width();
				var ContainerWidth=($('#truck-content li:last').index()+1)*showWidth;
				$('#truck-content').css('width',ContainerWidth);
			}
		},
		showTruckMsg:function(u,n,t_u,t_n,o_n,sn,zh){
			var text=cText='';
			text="<p><span style='color:red'>【跑道消息】"+n+" 携  "+t_n;
			if(o_n!=null&&o_n!=undefined){
				text+=" 取代 "+o_n;
			}
			text+=" 登上了跑道！</span></p>";
			
			$("#"+Chat.commonChatArea).append(text);
			if(u==Chat.show_uid){
				cText="<p><span style='color:red'>【跑道消息】您携  "+t_n;
				if(o_n!=null&&o_n!=undefined){
					cText+=" 取代 "+o_n;
				}
				cText+=" 登上了跑道！</span></p>";
				$("#"+Chat.privateChatArea).append(cText);
			}
			Chat.ScrollOn();
			if(t_u==Chat.show_uid){
				var pText="<p><span style='color:red'>【跑道消息】"+n+" 用 "+sn+"个"+zh +"将您推上了跑道！";
				$("#"+Chat.privateChatArea).append(pText);
				Chat.ScrollOnp();
			}
			
		},
		getGiftMess : function(data){
			var gift=$.parseJSON(data[2]);
			var u=gift.uid,n=gift.nickname,t_u=gift.to_uid,t_n=gift.to_nickname,d_u=gift.dotey_uid,avatar=gift.avatar,zh=gift.zh_description,charm=gift.charm,p=gift.position,sn=gift.gift_num,pic=gift.picture,flash=gift.flash,type=gift.type,recevier_type=gift.recevier_type,timeout=gift.timeout,time=gift.time,aid=data[0];
			if(gift.to_uid==null||gift.to_uid==undefined){
				t_u=gift.dotey_uid;
			}
			if(gift.to_nickname==null||gift.to_nickname==undefined){
				t_n=gift.dotey_nickname;
			}
			if(gift.recevier_type==null||gift.recevier_type==undefined){
				recevier_type='0';
			}
			
			if(gift.gift_type!=null&&gift.gift_type!=undefined&&gift.gift_type=='luckGifts'){
				if(gift.pipiegg>=archives.gift_message.global_message){
					var cg="<p><span style='color:red'>【超礼】"+t_n+" 收到了 "+n+" 送来的 "+sn+" 个 "+zh+"</span></p>"
					$("#"+Chat.commonChatArea).append(cg);
					Chat.ScrollOn();
				}
				Gift.luckGiftMsg(aid,u,n,t_n,zh,pic,sn,gift.gift_award);
			}else if(gift.pipiegg>=archives.gift_message.global_message){
				var cg="<p><span style='color:red'>【超礼】"+t_n+" 收到了 "+n+" 送来的 "+sn+" 个 "+zh+"</span></p>"
				$("#"+Chat.commonChatArea).append(cg);
				Chat.ScrollOn();
				
			}else if(aid==archives.archives_id&&gift.pipiegg>=archives.gift_message.private_message){
				var cg="<p><span style='color:red'>"+t_n+" 收到了 "+n+" 送来的 "+sn+" 个 "+zh+"</span></p>"
				$("#"+Chat.commonChatArea).append(cg);
				Chat.ScrollOn();
			}
			
			//公聊窗口显示本场送礼消息
			if(gift.gift_type=='truckGifts'){
				Gift.truckGift(gift);
				if(aid==archives.archives_id){
					var o_n=gift.org_nickname;
					Gift.showTruckMsg(u,n,t_u,t_n,o_n,sn,zh);
				}	
			}
			
			//判断是不是高级礼物
			var charm=charm*sn;
			if(gift.pipiegg>=archives.gift_message.global_message){
				var c='<p><a href="/'+d_u+'" target="'+hrefTarget+'">'+time+'<em class="name" title="'+n+'">'+cutstr(n,16)+'</em>送给<em class="name" title="'+t_n+'">'+cutstr(t_n,16)+'</em><em class="pink">'+sn+'</em>个'+zh+'</a><img src="'+this.giftPath+'/'+pic+'" width="25" height="25" /></p>';
				if($("#giftNotice").find('p').length>2){
					$("#giftNotice").find('p:last').remove();
				}
				if($("#giftNotice").find('p').length>0){
					$("#giftNotice").find('p:first').before(c);
				}else{
					$("#giftNotice").html(c);
				}
				
				if(recevier_type=='0'){
					if(aid==archives.archives_id){
						if(gift.new_time!=null&&gift.new_time!=undefined){
							var date=new Date();
							var ndate=new Date(gift.new_time*1000);
							if(date.getDate()-ndate.getDate()==0){
								time=ndate.getHours()+':'+ndate.getMinutes();
							}else if(date.getDate()-ndate.getDate()==1){
								time='昨日'+ndate.getHours()+':'+ndate.getMinutes();
							}else if(date.getDate()-ndate.getDate()>1){
								time=ndate.getFullYear()+'年'+(ndate.getMonth()+1)+'月'+ndate.getDate()+'日 '+ndate.getHours()+':'+ndate.getMinutes();
							}
						}
						var text='<li class="clearfix"><div class="small-head"><img src="'+avatar+'"></div><div class="charm-con"><p class="charm-text">'+t_n+'&nbsp;收到了&nbsp;'+n+'&nbsp;送来的&nbsp;<em class="pink">'+sn+'</em>&nbsp;个 '+zh+'<img src="'+this.giftPath+'/'+pic+'"></p><p class="time"><span>'+time+'</span></p></div>';
						if($("#CharmBox").find('li').length>25){
							$("#CharmBox").find('li:last').remove();
						}
						if($("#CharmBox").find('li').length>0){
							$("#CharmBox").find('li:first').before(text);
						}else{
							$("#CharmBox ul").html(text);
						}
						
					}
					
				}
			}
			if(aid==archives.archives_id){
				Gift.getDoteyCharm();
			}
			//获取主播等级进度条
			
			var obj=this;
			//用队列处理礼物播放 本档期的礼物才有效果
			if(data && (aid==archives.archives_id)){
				var f=function(){ playFlash();};
					this._qFun.push(f);
					this._q.push(data);

				$("#playFlash").queue('playf',this._qFun);
					if(this._q.length==1){
						_takeOne();
					}

			}

			function _takeOne(){
				$("#playFlash").dequeue('playf');
			};
			function playFlash(){
				var d=obj._q[0];//播放第一个
				var gift=$.parseJSON(d[2]);
				var u=gift.uid,n=gift.nickname,t_u=gift.dotey_uid,t_n=gift.dotey_nickname,zh=gift.zh_description,charm=gift.charm,p=gift.position,sn=gift.gift_num,pic=gift.picture,flash=gift.flash,type=gift.type,recevier_type=gift.receiver,timeout=gift.timeout,time=gift.time,aid=d[0];
				var swf=obj.giftPath+'/effect/'+flash;
				if($("#giftEffectSet").attr('checked')){
					if(type==1){
						if(p==1){
							var embed='<div style="width:925px;height:350px;"><embed width="925" height="350" src='+swf+' wmode="transparent" bgcolor="#fff" quality="high" type="application/x-shockwave-flash"></div>';
						}else if(p==2){
							var embed='<div style="width:450px;height:350px;float:right;margin-right:20px;"><embed width="450"  height="350" src='+swf+' wmode="transparent" bgcolor="#fff" quality="high" type="application/x-shockwave-flash"></div>';
						}else{
							var embed='<div style="width:450px;height:350px;float:left;"><embed width="450"  height="350" src='+swf+' wmode="transparent" bgcolor="#fff" quality="high" type="application/x-shockwave-flash"></div>';
						}
						$("#playFlash").html(embed);
						$("#playFlash").css({'margin':'0 auto','width':'970px'}).show();
					}else{
						var embed="<ul style=\"width:925px;height:350px;\">";
						var arrNum=Gift.randomNum(sn,73);
						for(l=0;l<73;l++){
							embed+='<li style="width:72px;heigth:72px;float:left">&nbsp;</li>';
						}
						embed+="</ul>";
						$("#playFlash").html(embed);
						
						for(k=0;k<arrNum.length;k++){
							$("#playFlash ul li").eq(arrNum[k]).html("<img src=\""+swf+"\" width=\"72\" height=\"72\"/>")
						}
						$("#playFlash").css({'margin':'0 auto','width':'925px'}).show();
					}
				}

				//礼物列表
				if(gift.recevier_type==null||gift.recevier_type==undefined||gift.recevier_type=='0'){
					var v=$(".gifts-box .gift-msg ul");
					var giftText='<li><span class="fansname ellipsis">'+n+'</span><span class="giftname ellipsis"><img src="'+obj.giftPath+'/'+pic+'">'+zh+'</span><span class="giftnum pink">'+sn+'</span></li>';
					v.find("li:first").after(giftText);
					if(v.find("li").length>50){
						v.find("li:last").remove();
					}
				}
				


				setTimeout(hideFlash,timeout*1000 );
			}

			function hideFlash(){
				$("#playFlash").empty().hide();
				obj._q.shift();//播放完一个 移除一个
				if(obj._q.length>0){
					_takeOne();
				}else{
					//数据播放完后 清空函数队列
					$("#playFlash").clearQueue('playf');
				}
			}
		}
};
$(function(){
	Gift.init();
})