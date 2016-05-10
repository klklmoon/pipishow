var Dice={
	_dFun:[],
	_d:[],
	num:1,
	init:function(){
		$("#diceGame").live('click',function(){
			$.ajax({
				type:'POST',
				url:'index.php?r=archives/getDiceRecord',
				data:{archives_id:archives.archives_id},
				dataType:'json',
				success:function(data){
					if(data){
						var timestamp=new Date().getTime()/1000;
						var text='';
						for(i=0;i<data.length;i++){
							text+='<li>';
							if(data[i].send_type==1){
								if(data[i].valid_time>timestamp){
									text+='<img src="'+archives.staticPath+'/fontimg/common/chart-green.png">';
								}else{
									text+='<img src="'+archives.staticPath+'/fontimg/common/chart-gray.png">';
								}
							}else{
								text+='<img src="'+archives.staticPath+'/fontimg/common/right-icon.png">';
							}
							if(data[i].send_type==3){
								if(data[i].result==2){
									text+='<a class="ellipsis">'+data[i].to_nk+'</a>向<a class="ellipsis">'+data[i].nk+'</a>扔出骰子';
								}else{
									text+='<a class="ellipsis">'+data[i].nk+'</a>向<a class="ellipsis">'+data[i].to_nk+'</a>扔出骰子';
								}
							}else{
								if(data[i].to_nk!=null&&data[i].to_nk!=undefined){
									text+='<a class="ellipsis">'+data[i].nk+'</a>向<a class="ellipsis">'+data[i].to_nk+'</a>扔出骰子';
								}else{
									text+='<a class="ellipsis">'+data[i].nk+'</a>扔出骰子';
								}
								
							}
							
							if(data[i].send_type==3){
								if(data[i].result==3){
									text+='<img src="'+archives.staticPath+'/fontimg/common/eqpic.png">';
								}else{
									text+='<img src="'+archives.staticPath+'/fontimg/common/winpic.png">';
								}
							}else{
								text+='<img src="'+archives.staticPath+'/fontimg/dice/'+data[i].dice_type+'_23.png">';
							}
							
							text+='</li>';
						}
						$("#diceRecord").empty().html(text);
					}
				}
			})
		});
	},
	sendDice:function(type){
		if(Chat.show_uid<=0){
			$.User.loginController('login');
		}
		if($("#"+Chat.privateSet).attr('checked')=='checked'){
			$("#SucMove .popcon").empty().html('<p class="oneline">不能在私聊中发送骰子</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
			$.mask.show('SucMove',3000);
			return false;
		}
		$.ajax({
			type:'POST',
			url:'index.php?r=archives/sendDice',
			data:{archives_id:archives.archives_id,to_uid:Chat.chatObj.uid,type:type},
			dataType:'json',
			success:function(data){
				if(data){
					if(data.flag!=1){
						$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
					}
					
				}
			}
		})
	},
	receiveDice:function(record_id,uid){
		if(Chat.show_uid<=0){
			$.User.loginController('login');
		}
		$.ajax({
			type:'POST',
			url:'index.php?r=archives/receiveDice',
			data:{archives_id:archives.archives_id,uid:uid,record_id:record_id},
			dataType:'json',
			success:function(data){
				if(data){
					if(data.flag!=1){
						$("#SucMove .popcon").empty().html('<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
						$.mask.show('SucMove',3000);
					}
					
				}
			}
		})
	},
	diceResult:function(data){
		var text=ptext='';
		if(data.result==1){
			text='<p style="color:red;">【骰子游戏】'+data.nk+'（'+data.points.join("和")+'点） 战胜了 '+data.to_nk+'（'+data.to_points.join("和")+'点）';
		}else if(data.result==2){
			text='<p style="color:red;">【骰子游戏】'+data.to_nk+'（'+data.to_points.join("和")+'点）战胜了 '+data.nk+'（'+data.points.join("和")+'点）';
		}else if(data.result==3){
			text='<p style="color:red;">【骰子游戏】'+data.nk+'（'+data.points.join("和")+'点） 和 '+data.to_nk+'（'+data.to_points.join("和")+'点） 平局不分胜负';
		}
		if(data.zh_name!=null&&data.zh_name!=undefined){
			text+='赢得'+data.gift_num+'个'+data.zh_name
		}
		text+='</p>';
		$("#"+Chat.commonChatArea).append(text);
		Chat.ScrollOn();
		if(Chat.show_uid==data.uid){
			if(data.result==1){
				ptext='<p style="color:red;">【骰子游戏】'+data.to_nk+' 接受您的挑战（'+data.points.join("和")+'点），回掷骰子（'+data.to_points.join("和")+'点），您赢得本局';
			}else if(data.result==2){
				ptext='<p style="color:red;">【骰子游戏】'+data.to_nk+' 接受您的挑战（'+data.points.join("和")+'点），回掷骰子（'+data.to_points.join("和")+'点），'+data.to_nk+' 赢得本局';
			}else if(data.result==3){
				ptext='<p style="color:red;">【骰子游戏】'+data.to_nk+' 接受您的挑战（'+data.points.join("和")+'点），回掷骰子（'+data.to_points.join("和")+'点），平局不分胜负';
			}
			if(data.zh_name!=null&&data.zh_name!=undefined){
				ptext+='赢得'+data.gift_num+'个'+data.zh_name
			}
			ptext+='</p>';
			$("#"+Chat.privateChatArea).append(ptext);
			Chat.ScrollOnp();
		}
		if(Chat.show_uid==data.to_uid){
			if(data.result==1){
				ptext='<p style="color:red;">【骰子游戏】您接受 '+data.nk+' 的挑战（'+data.points.join("和")+'点），回掷骰子（'+data.to_points.join("和")+'点），'+data.nk+' 赢得本局';
			}else if(data.result==2){
				ptext='<p style="color:red;">【骰子游戏】您接受 '+data.nk+' 的挑战（'+data.points.join("和")+'点），回掷骰子（'+data.to_points.join("和")+'点），您赢得本局';
			}else if(data.result==3){
				ptext='<p style="color:red;">【骰子游戏】您接受 '+data.nk+' 的挑战（'+data.points.join("和")+'点），回掷骰子（'+data.to_points.join("和")+'点），平局不分胜负';
			}
			if(data.zh_name!=null&&data.zh_name!=undefined){
				ptext+='赢得'+data.gift_num+'个'+data.zh_name
			}
			ptext+='</p>';
			$("#"+Chat.privateChatArea).append(ptext);
			Chat.ScrollOnp();
		}
	},
	dice:function(data){
		var f=function(){ playDice();};
		this._dFun.push(f);
		var obj=this;
		obj._d.push(data);
		$(".chat-msg").queue('dicef',obj._dFun);
		if(obj._d.length==1){
			_takeOne();
		}
		
		function _takeOne(){
			$(".chat-msg").dequeue('dicef');
		};
		function stopDice(send_type,uid,type,data){
			if(send_type==1){
				if(type=='common_dice'){
					for(i=0;i<data.length;i++){
						$("#"+data[i][0]).attr('src',archives.staticPath+'/fontimg/dice/'+type+'_'+data[i][1]+'.png');
					}
				}else{
					if(Chat.show_uid==uid){
						for(i=0;i<data.length;i++){
							$("#"+data[i][0]).attr('src',archives.staticPath+'/fontimg/dice/'+type+'_'+data[i][1]+'.png');
						}
					}else{
						for(i=0;i<data.length;i++){
							$("#"+data[i][0]).attr('src',archives.staticPath+'/fontimg/dice/'+type+'.png');
						}
					}
				}
				
			}else{
				for(i=0;i<data.length;i++){
					$("#"+data[i][0]).attr('src',archives.staticPath+'/fontimg/dice/'+type+'_'+data[i][1]+'.png');
				}
			}
			
			obj._d.shift();
			if(obj._d.length>0){
				_takeOne();
			}else{
				//数据播放完后 清空函数队列
				$(".chat-msg").clearQueue('dicef');
			}
		}

		function playDice(){
			var c=obj._d[0];
			var u=c.uid,n=c.nk,r=c.rk,p=c.pk,lb=c.lb,mc=c.mc,md=c.md,num=c.num,points=c.points,dice_type=c.dice_type,send_type=c.send_type,sid=c.record_id;
			var point=new Array();
			var diceId=new Array();
			for(i=0;i<points.length;i++){
				point[i]=new Array();
				point[i][0]='dice_'+Dice.num;
				point[i][1]=points[i];
				diceId[i]='dice_'+Dice.num;
				Dice.num+=1;	
			}
			if(c.to_uid!=null&&c.to_uid!=undefined&&c.to_uid>0){
				var t_u=c.to_uid,t_n=c.to_nk,t_r=c.to_rk,t_p=c.to_pk,t_lb=c.to_lb,t_mc=c.to_mc,t_md=c.to_md;
				var text='<p>'+Chat._showRank(r,p,u)+'<a href="javascript:void(0);" id="'+u+'|*|'+n+'|*|'+r+'|*|'+p+'|*|'+lb+'|*|'+mc+'|*|'+md+'">'+n+'</a>对'+Chat._showRank(t_r,t_p,t_u)+'<a href="javascript:void(0);" id="'+t_u+'|*|'+t_n+'|*|'+t_r+'|*|'+t_p+'|*|'+t_lb+'|*|'+t_mc+'|*|'+t_md+'">'+t_n+'</a>扔出骰子:';
				for(k=0;k<diceId.length;k++){
					text+='<img   id="'+diceId[k]+'" src="'+archives.staticPath+'/fontimg/dice/'+dice_type+'.gif"/>';
				}
				if(Chat.show_uid==c.to_uid&&send_type==1){
					text+='<span class="pk-link"  onclick="Dice.receiveDice('+sid+','+u+')">回掷骰子</span>';
				}
				text+='</p>';
			}else{
				var text='<p>'+Chat._showRank(r,p,u)+'<a href="javascript:void(0);" id="'+u+'|*|'+n+'|*|'+r+'|*|'+p+'|*|'+lb+'|*|'+mc+'|*|'+md+'">'+n+'</a>扔出骰子:';
				for(k=0;k<diceId.length;k++){
					text+='<img   id="'+diceId[k]+'" src="'+archives.staticPath+'/fontimg/dice/'+dice_type+'.gif"/>';
				}
				text+='</p>';
			}
			
			$("#"+Chat.commonChatArea).append(text);
			Chat.ScrollOn();
			if(c.to_uid!=null&&c.to_uid!=undefined&&c.to_uid>0){
				if(Chat.show_uid==c.to_uid&&send_type==1){
					$("#"+Chat.privateChatArea).append('<p style="color:red;">【骰子游戏】'+n+' 向您扔出骰子<span class="pk-link"  onclick="Dice.receiveDice('+sid+','+u+')">回掷骰子</span></p>');
					Chat.ScrollOnp();
				}
			}
			setTimeout(function(){
				stopDice(send_type,u,dice_type,point)
			},2000);
		}
		
	}
	
}
$(function(){
	Dice.init();

})