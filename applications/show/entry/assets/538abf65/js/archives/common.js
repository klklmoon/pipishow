
$(function(){
	RemoveBroadContent();
	//直播间横幅广告
	$(".rmid-ad").slide({mainCell:".bd ul",effect:"topLoop",autoPlay:true});
	//本场粉丝榜
	$("#dedication").slide({titCell:".fanchar-menu li",mainCell:".fans-con",trigger:"click",titOnClassName:"fansover",delayTime:0})
	$("#archives_dedication ul li").click(function(){
		var type;
		var n=$(this).index()?$(this).index():0;
		if(n==0){
			type="archives_dedication";
		}else{
			type="week_dedication";
		}
		$.ajax({
			type:"POST",
			url:"index.php?r=/archives/getArchivesRankList",
			data:{archives_id:archives.archives_id,type:type},
			dataType:"json",
			success:function(e){
				if(e){
					var text='';
					var j=1;
					for(i=0;i<e.length;i++){
						if(j<=2){
							var cs='top'+j;
						}else if(j==3){
							var cs='top2';
						}else{
							var cs='';
						}
						text+='<li class="clearfix"><em class="fleft order '+cs+'">'+j+'</em><p class="name"><em class="lvlr lvlr-'+e[i].rank+'"></em><span>'+e[i].nickname+'</span></p><div class="convalue"><em class="pink">'+e[i].dedication+'</em><p>贡献值</p></div></li>';
						j++;
					}
					$("#dedication_list ul").eq(n).empty().html(text);
				}
			}
		})
	})
	//本场情谊榜
	$("#friendly").slide({titCell:".fanchar-menu li",mainCell:".fans-con",trigger:"click",titOnClassName:"fansover",delayTime:0})
	$("#archives_friendly ul li").click(function(){
		var type;
		var n=$(this).index()?$(this).index():0;
		if(n==0){
			type="archives_friendly";
		}else{
			type="week_archives_friendly";
		}
		$.ajax({
			type:"POST",
			url:"index.php?r=/archives/getArchivesFriendly",
			data:{archives_id:archives.archives_id,type:type},
			dataType:"json",
			success:function(e){
				if(e){
					var text='';
					var j=1;
					for(i=0;i<e.length;i++){
						if(j<=2){
							var cs='top'+j;
						}else if(j==3){
							var cs='top2';
						}else{
							var cs='';
						}
						text+='<li class="clearfix"><em class="fleft order '+cs+'">'+j+'</em><p class="name"><em class="lvlr lvlr-'+e[i].rank+'"></em><span>'+e[i].nickname+'</span></p><div class="convalue"><em class="pink">'+e[i].dedication+'</em><p>贡献值</p></div></li>';
						j++;
					}
					$("#friendly_list ul").eq(n).empty().html(text);
				}
			}
		})
	})
	
	
	$('#SendAir').bind('click',function(){
		if($.User.getSingleAttribute('uid',true)<=0){
			$.User.loginController('login');
			return;
		}
		$("#broadCastContent").val('');
		if($('#AirBox').css('display')=='none'){
			$(this).addClass('rewardover');
			$('#AirBox').css("display","block");
			$("#NewReward").hide();
		}else{
			$('#AirBox').css("display","none");
			$(this).removeClass('rewardover');
			$("#NewReward").show();
		}
		 
	});
	$('#AirClose').bind('click',function(){
		 $('#AirBox').css("display","none");
		 $(this).removeClass('rewardover');
		 $("#NewReward").show();
	});
	
	$("#broadCastContent").keyup(check);
	$("#broadCastContent").mousedown(check);

});

function trim(str) {
	 return (str + '').replace(/(\s+)$/g, '').replace(/^\s+/g, '');
}

function strlen(str) {
	var str = trim(str);
	var len = 0;
	for (var i = 0; i < str.length; i++) {
		len += 1;
	}
	return len;
}
function check() {
	var str = $("#broadCastContent").val();
	var len = strlen(str);
	var info = 50 - len;
	info = info + "";
	if (info.indexOf('.') > 0)
		info = info.substring(0, info.indexOf('.'));
	if(len==0){
		$(".faceBox-btn .shiftbtn").addClass('shifted');
		$(".faceBox-btn .shiftbtn").removeAttr('onclick');
		$("#broadCastInfo").html('还能输入<em class="pink">'+info+'</em>个字');
	}else{
		if (len > 50) {
			$("#broadCastContent").val(str.substr(0, 50));
			$("#broadCastInfo").html('还能输入<em class="pink">0</em>个字');
		}else{
			$(".faceBox-btn .shiftbtn").removeClass('shifted');
			$(".faceBox-btn .shiftbtn").attr('onclick','Chat.sendBroadcast()');
			$("#broadCastInfo").html('还能输入<em class="pink">'+info+'</em>个字');
		}
	}
}

function showFace(){
	$('#FaceBox').css({'top':obj.offset().top,'left':obj.offset().left}).show();
}
var ScrollTruckTime;
function ScrollTruckMsg(AppendId,contID,textwidth,steper){
	var PosInit,currPos,showWidth;
	showWidth=$('#'+contID+' li:last').index()*textwidth+$('#'+contID+' a:last').width();
	with($('#'+contID)){
		currPos = parseInt(css('margin-left'));
		if(currPos<0 && Math.abs(currPos)>showWidth){
			css('margin-left',textwidth);
		}else{
			css('margin-left',currPos-steper);
		}
	}
	RemovePlay(AppendId,contID,textwidth);
}

function ScrollTruckText(AppendId,contentId,Steper,Interval){
	var TextWidth,PosInit,PosSteper,showWidth,showText;
	if(ScrollTruckTime){
		clearInterval(ScrollTruckTime);
	}
	showText=$('#'+contentId).html();
	if($('#'+contentId).has('li')){
		showWidth=$('#'+AppendId).width();
		PosInit =showWidth;
		PosSteper = Steper;
		if(Steper<1 || Steper>PosInit){Steper = 1}//每次移动间距超出限制(单位:px)
		if(Interval<1){Interval = 10}//每次移动的时间间隔（单位：毫秒）
		var Container = $('#'+contentId);
		with(Container){
		  html($('#'+contentId).html());
		  if(isNaN(PosInit)){PosInit = 0 -showWidth;}
		   css('margin-left',PosInit);
		  
		}
		var ContainerWidth=($('#'+contentId+' li:last').index()+1)*showWidth;
		$('#'+contentId).css('width',ContainerWidth);
		ScrollTruckTime = setInterval(function(){
			ScrollTruckMsg(AppendId,contentId,showWidth,Steper);
		},Interval);
		
	}
	
}
function RemovePlay(AppendId,contID,textwidth){
	var marginLeft=parseInt($("#"+contID).css('margin-left'));
	var showWidth=$('#'+contID+' li:last').index()*textwidth+$('#'+contID+' a:last').width();;
	if(marginLeft<0&&showWidth==Math.abs(marginLeft)){
		$('#'+contID+' li').each(function(i){
			if($(this).attr('rel')=='0'){
				$(this).remove();
			}
		})
		var ContainerWidth=($('#'+contID+' li:last').index()+1)*textwidth;
		$('#'+contID).css('width',ContainerWidth);
	}
}

function RemoveBroadContent(){
	var time;
	if($(".broadCastList li").size()>0){
		$(".broadContent").show();
	}
	var broadcastTime=setInterval(function(){
		$(".broadCastList li").each(function(i){
			if($(this).attr('rel')<new Date().getTime()/1000){
				$(this).remove();
			}
			time++;
		})
	},1000);
	//移除定时器
	if(time>1800){
		clearInterval(broadcastTime);
	}
}

//中文截字
function cutstr(str,len){
	var str_length = 0;
	var str_len = 0;
	str_cut = new String();
	str_len = str.length;
	for(var i = 0;i<str_len;i++){
		a = str.charAt(i);
		str_length++;
		if(escape(a).length > 4){
			//中文字符的长度经编码之后大于4
			str_length++;
		}
		str_cut = str_cut.concat(a);
		if(str_length>=len){
			return str_cut;
		}
	}
	//如果给定字符串小于指定长度，则返回源字符串；
	if(str_length<len){
		return str;
	}
}

function getNewUserAward(){
	setTimeout(function(){
		$.ajax({
			type:'POST',
			url:'index.php?r=user/checkUserAward',
			dataType:'json',
			success:function(data){
				if(data.flag==1){
					$.mask.show('newUserAward');
				}
			}
		})
	},180000);
}

function hideNewUserAward(){
	$.mask.hide('newUserAward');
	var url=$('.surebtn').attr('rel');
	window.open(url,'_blank');
}
