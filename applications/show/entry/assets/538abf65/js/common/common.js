(function($){
	$.fn.slide=function(options){
		$.fn.slide.defaults={
		effect:"fade", 
		autoPlay:false, 
		delayTime:500, 
		interTime:2500,
		triggerTime:150,
		defaultIndex:0,
		titCell:".hd li",
		mainCell:".bd",
		targetCell:null,
		trigger:"mouseover",
		scroll:1,
		vis:1,
		titOnClassName:"on",
		autoPage:false,
		prevCell:".prev",
		nextCell:".next",
		pageStateCell:".pageState",
		opp: false, 
		pnLoop:true, 
		easing:"linear",
		startFun:null,
		endFun:null,
		switchLoad:null
		};

		return this.each(function() {
			var opts = $.extend({},$.fn.slide.defaults,options);
			var effect = opts.effect;
			var prevBtn = $(opts.prevCell, $(this));
			var nextBtn = $(opts.nextCell, $(this));
			var pageState = $(opts.pageStateCell, $(this));
			var navObj = $(opts.titCell, $(this));//导航子元素结合
			var navObjSize = navObj.size();
			var conBox = $(opts.mainCell , $(this));//内容元素父层对象
			var conBoxSize=conBox.children().size();
			var sLoad=opts.switchLoad;
			if(opts.targetCell!=null){ var tarObj = $(opts.targetCell, $(this)) };

			/*字符串转换为数字*/
			var index=parseInt(opts.defaultIndex);
			var delayTime=parseInt(opts.delayTime);
			var interTime=parseInt(opts.interTime);
			var triggerTime=parseInt(opts.triggerTime);
			var scroll=parseInt(opts.scroll);
			var vis=parseInt(opts.vis);
			var autoPlay = (opts.autoPlay=="false"||opts.autoPlay==false)?false:true;
			var opp = (opts.opp=="false"||opts.opp==false)?false:true;
			var autoPage = (opts.autoPage=="false"||opts.autoPage==false)?false:true;
			var loop = (opts.pnLoop=="false"||opts.pnLoop==false)?false:true;

			var slideH=0;
			var slideW=0;
			var selfW=0;
			var selfH=0;
			var easing=opts.easing;
			var inter=null;//setInterval名称 
			var oldIndex = index;
			
			//处理分页
			if( navObjSize==0 )navObjSize=conBoxSize;
			if( autoPage ){
				var tempS = conBoxSize-vis;
				navObjSize=1+parseInt(tempS%scroll!=0?(tempS/scroll+1):(tempS/scroll)); 
				if(navObjSize<=0)navObjSize=1;
				navObj.html(""); 
				for( var i=0; i<navObjSize; i++ ){ navObj.append("<li>"+(i+1)+"</li>") }
				var navObj = $("li", navObj);//重置导航子元素对象
			}

			conBox.children().each(function(){ //取最大值
				if( $(this).width()>selfW ){ selfW=$(this).width(); slideW=$(this).outerWidth(true);  }
				if( $(this).height()>selfH ){ selfH=$(this).height(); slideH=$(this).outerHeight(true);  }
			});

			if(conBoxSize>=vis){ //当内容个数少于可视个数，不执行效果。
				switch(effect)
				{
					case "fold": conBox.css({"position":"relative","width":slideW,"height":slideH}).children().css( {"position":"absolute","width":selfW,"left":0,"top":0,"display":"none"} ); break;
					case "top": conBox.wrap('<div class="tempWrap" style="overflow:hidden; position:relative; height:'+vis*slideH+'px"></div>').css( { "position":"relative","padding":"0","margin":"0"}).children().css( {"height":selfH} ); break;
					case "left": conBox.wrap('<div class="tempWrap" style="overflow:hidden; position:relative; width:'+vis*slideW+'px"></div>').css( { "width":conBoxSize*slideW,"position":"relative","overflow":"hidden","padding":"0","margin":"0"}).children().css( {"float":"left","width":selfW} ); break;
					case "leftLoop":
					case "leftMarquee":
						conBox.children().clone().appendTo(conBox).clone().prependTo(conBox); 
						conBox.wrap('<div class="tempWrap" style="overflow:hidden; position:relative; width:'+vis*slideW+'px"></div>').css( { "width":conBoxSize*slideW*3,"position":"relative","overflow":"hidden","padding":"0","margin":"0","left":-conBoxSize*slideW}).children().css( {"float":"left","width":selfW}  ); break;
					case "topLoop":
					case "topMarquee":
						conBox.children().clone().appendTo(conBox).clone().prependTo(conBox); 
						conBox.wrap('<div class="tempWrap" style="overflow:hidden; position:relative; height:'+vis*slideH+'px"></div>').css( { "height":conBoxSize*slideH*3,"position":"relative","padding":"0","margin":"0","top":-conBoxSize*slideH}).children().css( {"height":selfH} ); break;
				}
			}

			var doStartFun=function(){ if ( $.isFunction( opts.startFun) ){ opts.startFun( index,navObjSize ); } };
			var doEndFun=function(){ if ( $.isFunction( opts.endFun ) ){ opts.endFun( index,navObjSize ); } };
			var doSwitchLoad=function(objs){ 
						objs.eq(index).find("img").each(function(){ 
						if ( typeof($(this).attr(sLoad))!="undefined"){ $(this).attr("src",$(this).attr(sLoad)).removeAttr(sLoad) }
					})
				}

			//效果函数
			var doPlay=function(isFirst){
				
				if( oldIndex==index && !isFirst && effect!="leftMarquee" && effect!="topMarquee"  ) return; // 当前页状态不触发效果

				switch(effect)
				{
					case "fade": case "fold": case "top": case "left": if ( index >= navObjSize) { index = 0; } else if( index < 0) { index = navObjSize-1; } break;
					case "leftMarquee":case "topMarquee": if ( index>= 1) { index=1; } else if( index<=0) { index = 0; } break;
					case "leftLoop": case "topLoop":
						var tempNum = index - oldIndex; 
						if( navObjSize>2 && tempNum==-(navObjSize-1) ) tempNum=1;
						if( navObjSize>2 && tempNum==(navObjSize-1) ) tempNum=-1;
						var scrollNum = Math.abs( tempNum*scroll );
						if ( index >= navObjSize) { index = 0; } else if( index < 0) { index = navObjSize-1; }
					break;
				}

				doStartFun();

				//处理切换加载
				if( sLoad!=null ){ doSwitchLoad( conBox.children() ) }

				//处理targetCell
				if(tarObj){ 
					if( sLoad!=null ){ doSwitchLoad( tarObj ) }
					tarObj.hide().eq(index).animate({opacity:"show"},delayTime,function(){ if(!conBox[0])doEndFun() }); 
				}
				
				if(conBoxSize>=vis){ //当内容个数少于可视个数，不执行效果。
					switch (effect)
					{
						case "fade":conBox.children().stop(true,true).eq(index).animate({opacity:"show"},delayTime,easing,function(){doEndFun()}).siblings().hide(); break;
						case "fold":conBox.children().stop(true,true).eq(index).animate({opacity:"show"},delayTime,easing,function(){doEndFun()}).siblings().animate({opacity:"hide"},delayTime,easing);break;
						case "top":conBox.stop(true,false).animate({"top":-index*scroll*slideH},delayTime,easing,function(){doEndFun()});break;
						case "left":conBox.stop(true,false).animate({"left":-index*scroll*slideW},delayTime,easing,function(){doEndFun()});break;
						case "leftLoop":
							if(tempNum<0 ){
									conBox.stop(true,true).animate({"left":-(conBoxSize-scrollNum )*slideW},delayTime,easing,function(){
									for(var i=0;i<scrollNum;i++){ conBox.children().last().prependTo(conBox); }
									conBox.css("left",-conBoxSize*slideW);
									doEndFun();
								});
							}
							else{
								conBox.stop(true,true).animate({"left":-( conBoxSize + scrollNum)*slideW},delayTime,easing,function(){
									for(var i=0;i<scrollNum;i++){ conBox.children().first().appendTo(conBox); }
									conBox.css("left",-conBoxSize*slideW);
									doEndFun();
								});
							}break;// leftLoop end

						case "topLoop":
							if(tempNum<0 ){
									conBox.stop(true,true).animate({"top":-(conBoxSize-scrollNum )*slideH},delayTime,easing,function(){
									for(var i=0;i<scrollNum;i++){ conBox.children().last().prependTo(conBox); }
									conBox.css("top",-conBoxSize*slideH);
									doEndFun();
								});
							}
							else{
								conBox.stop(true,true).animate({"top":-( conBoxSize + scrollNum)*slideH},delayTime,easing,function(){
									for(var i=0;i<scrollNum;i++){ conBox.children().first().appendTo(conBox); }
									conBox.css("top",-conBoxSize*slideH);
									doEndFun();
								});
							}break;//topLoop end

						case "leftMarquee":
							var tempLeft = conBox.css("left").replace("px",""); 

							if(index==0 ){
									conBox.animate({"left":++tempLeft},0,function(){
										if( conBox.css("left").replace("px","")>= 0){ for(var i=0;i<conBoxSize;i++){ conBox.children().last().prependTo(conBox); }conBox.css("left",-conBoxSize*slideW);}
									});
							}
							else{
									conBox.animate({"left":--tempLeft},0,function(){
										if(  conBox.css("left").replace("px","")<= -conBoxSize*slideW*2){ for(var i=0;i<conBoxSize;i++){ conBox.children().first().appendTo(conBox); }conBox.css("left",-conBoxSize*slideW);}
									});
							}break;// leftMarquee end

							case "topMarquee":
							var tempTop = conBox.css("top").replace("px",""); 
								if(index==0 ){
										conBox.animate({"top":++tempTop},0,function(){
											if( conBox.css("top").replace("px","") >= 0){ for(var i=0;i<conBoxSize;i++){ conBox.children().last().prependTo(conBox); }conBox.css("top",-conBoxSize*slideH);}
										});
								}
								else{
										conBox.animate({"top":--tempTop},0,function(){
											if( conBox.css("top").replace("px","")<= -conBoxSize*slideH*2){ for(var i=0;i<conBoxSize;i++){ conBox.children().first().appendTo(conBox); }conBox.css("top",-conBoxSize*slideH);}
										});
								}break;// topMarquee end


					}//switch end
				}


					navObj.removeClass(opts.titOnClassName).eq(index).addClass(opts.titOnClassName);
					oldIndex=index;
					if( loop==false ){ //loop控制是否继续循环
						nextBtn.removeClass("nextStop"); prevBtn.removeClass("prevStop");
						if (index==0 ){ prevBtn.addClass("prevStop"); }
						else if (index==navObjSize-1 ){ nextBtn.addClass("nextStop");  }
					}
					pageState.html( "<span>"+(index+1)+"</span>/"+navObjSize);
			};
			//初始化执行
			doPlay(true);

			//自动播放
			if (autoPlay) {
					if( effect=="leftMarquee" || effect=="topMarquee"  ){
						if(opp){ index-- }else{ index++ } inter = setInterval(doPlay, interTime);
						conBox.hover(function(){if(autoPlay){clearInterval(inter); }},function(){if(autoPlay){clearInterval(inter);inter = setInterval(doPlay, interTime);}});
					}else{
						 inter=setInterval(function(){  if(opp){ index-- }else{ index++ } ; doPlay() }, interTime); 
						$(this).hover(function(){if(autoPlay){clearInterval(inter); }},function(){if(autoPlay){clearInterval(inter); inter=setInterval(function(){if(opp){ index-- }else{ index++ }; doPlay() }, interTime); }});
					}
			}

			//鼠标事件
			var mst;
			if(opts.trigger=="mouseover"){
				navObj.hover(function(){ index=navObj.index(this); mst = window.setTimeout(doPlay,opts.triggerTime); }, function(){ clearTimeout(mst); });
			}else{ navObj.click(function(){index=navObj.index(this);  doPlay(); })  }

			 nextBtn.click(function(){ if ( loop==true || index!=navObjSize-1 ){ index++; doPlay(); }  });  
			 prevBtn.click(function(){ if ( loop==true || index!=0 ){ index--; doPlay(); } }); 


    	});//each End

	};//slide End
	
	$.mask = {
			  title:null,
			  show:function(id,time){
				  $("#mask").remove();
				  $("body").append("<div id='mask'></div>");
				  this.resizeMask();
				  //var iframeDiv = "<iframe id=\"alertIframe\" ></iframe>";
				  setTimeout(function(){
					  $("#mask").show();
					  //$(iframeDiv).appendTo("#mask");
					  $("#"+id).show();
					  $.mask.moveit(id);
				  },0);
				  if(time!=null){
					setTimeout(function(){$.mask.hide(id);},time);  
					}
				  $('html').css('overflow','hidden');
			  },
			  hide:function(id){
				  $("#mask").hide();
				  $("#"+id).hide();
				  //$(".popWin").hide();
				  //$("#alertIframe").remove();
				  $("#mask").remove();
				  $('html').css('overflow','auto');
			  },
			  moveit :  function (id){
				  var marginleft = -($("#"+id).width()/2);
				  var marginheight = -($("#"+id).height()/2);
				  var top =  (window == window.top) ? ($(window).height())/2 + $(window).scrollTop() : 450;
				  var left = ($(window).width())/2 + $(window).scrollLeft();
				  $("#"+id).css({
					  'margin-left': marginleft,
					  'margin-top': marginheight,
					  'top': top,
					  'left': left
				  });
			  },
			  resizeMask : function(){
				  var maskHeight=this.getPageSize()[0];
				  if (maskHeight<document.documentElement.clientHeight)
				  {
					  maskHeight = document.documentElement.clientHeight;
					  
				  }else if(maskHeight==document.documentElement.clientHeight){
					  maskHeight=maskHeight*2+1000;
				  }
				  $("#mask").height(maskHeight);
			  },
			  getPageSize:function (){ 
			  	var body=document.documentElement;
			  	var bodyOffsetWidth = 0; 
			  	var bodyOffsetHeight = 0; 
			  	var bodyScrollWidth = 0; 
			  	var bodyScrollHeight = 0; 
			  	var pageDimensions = [0,0]; 
			  	pageDimensions[0]=body.clientHeight; 
			  	pageDimensions[1]=body.clientWidth; 
			  	bodyOffsetWidth=body.offsetWidth; 
			  	bodyOffsetHeight=body.offsetHeight; 
			  	bodyScrollWidth=body.scrollWidth; 
			  	bodyScrollHeight=body.scrollHeight; 
			  	if(bodyOffsetHeight > pageDimensions[0]) 
			  	{ 
			  		pageDimensions[0]=bodyOffsetHeight; 
			  	} 
			  	if(bodyOffsetWidth > pageDimensions[1]) 
			  	{ 
			  		pageDimensions[1]=bodyOffsetWidth; 
			  	} 
			  	if(bodyScrollHeight > pageDimensions[0]) 
			  	{ 
			  		pageDimensions[0]=bodyScrollHeight; 
			  	} 
			  	if(bodyScrollWidth > pageDimensions[1]) 
			  	{ 
			  		pageDimensions[1]=bodyScrollWidth; 
			  	} 
			  	return pageDimensions; 
			  } 
		};//mask end
	
})(jQuery);

jQuery.easing['jswing'] = jQuery.easing['swing'];
jQuery.extend( jQuery.easing,
{
	def: 'easeOutQuad',
	swing: function (x, t, b, c, d) { return jQuery.easing[jQuery.easing.def](x, t, b, c, d); },
	easeInQuad: function (x, t, b, c, d) {return c*(t/=d)*t + b;},
	easeOutQuad: function (x, t, b, c, d) {return -c *(t/=d)*(t-2) + b},
	easeInOutQuad: function (x, t, b, c, d) {if ((t/=d/2) < 1) return c/2*t*t + b;return -c/2 * ((--t)*(t-2) - 1) + b},
	easeInCubic: function (x, t, b, c, d) {return c*(t/=d)*t*t + b},
	easeOutCubic: function (x, t, b, c, d) {return c*((t=t/d-1)*t*t + 1) + b},
	easeInOutCubic: function (x, t, b, c, d) {if ((t/=d/2) < 1) return c/2*t*t*t + b;return c/2*((t-=2)*t*t + 2) + b},
	easeInQuart: function (x, t, b, c, d) {return c*(t/=d)*t*t*t + b},
	easeOutQuart: function (x, t, b, c, d) {return -c * ((t=t/d-1)*t*t*t - 1) + b},
	easeInOutQuart: function (x, t, b, c, d) {if ((t/=d/2) < 1) return c/2*t*t*t*t + b;return -c/2 * ((t-=2)*t*t*t - 2) + b},
	easeInQuint: function (x, t, b, c, d) {return c*(t/=d)*t*t*t*t + b},
	easeOutQuint: function (x, t, b, c, d) {return c*((t=t/d-1)*t*t*t*t + 1) + b},
	easeInOutQuint: function (x, t, b, c, d) {if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;return c/2*((t-=2)*t*t*t*t + 2) + b},
	easeInSine: function (x, t, b, c, d) {return -c * Math.cos(t/d * (Math.PI/2)) + c + b},
	easeOutSine: function (x, t, b, c, d) {return c * Math.sin(t/d * (Math.PI/2)) + b},
	easeInOutSine: function (x, t, b, c, d) {return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b},
	easeInExpo: function (x, t, b, c, d) {return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b},
	easeOutExpo: function (x, t, b, c, d) {return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b},
	easeInOutExpo: function (x, t, b, c, d) {if (t==0) return b;if (t==d) return b+c;if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;return c/2 * (-Math.pow(2, -10 * --t) + 2) + b},
	easeInCirc: function (x, t, b, c, d) {return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b},
	easeOutCirc: function (x, t, b, c, d) {return c * Math.sqrt(1 - (t=t/d-1)*t) + b},
	easeInOutCirc: function (x, t, b, c, d) {if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b},
	easeInElastic: function (x, t, b, c, d) {var s=1.70158;var p=0;var a=c;if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b},
	easeOutElastic: function (x, t, b, c, d) {var s=1.70158;var p=0;var a=c;if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b},
	easeInOutElastic: function (x, t, b, c, d) {var s=1.70158;var p=0;var a=c;if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b},
	easeInBack: function (x, t, b, c, d, s) {if (s == undefined) s = 1.70158;return c*(t/=d)*t*((s+1)*t - s) + b},
	easeOutBack: function (x, t, b, c, d, s) {if (s == undefined) s = 1.70158;return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b},
	easeInOutBack: function (x, t, b, c, d, s) {if (s == undefined) s = 1.70158; 
		if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b},
	easeInBounce: function (x, t, b, c, d) {return c - jQuery.easing.easeOutBounce (x, d-t, 0, c, d) + b},
	easeOutBounce: function (x, t, b, c, d) {if ((t/=d) < (1/2.75)) {	return c*(7.5625*t*t) + b;} else if (t < (2/2.75)) {	return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;} else if (t < (2.5/2.75)) {	return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;} else {	return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;}},
	easeInOutBounce: function (x, t, b, c, d) {if (t < d/2) return jQuery.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;return jQuery.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;}
});

$(function(){
	//导航效果
	$('#Nav li').hover(function(){
		$(this).addClass('navover').siblings().removeClass('navover');	
	},function(){
		$(this).removeClass('navover');	
	});
	
	//文本框焦点效果
	$('#SearchText').focus(function(){
		$(this).attr('value','');
		$(this).addClass('focustext');
	}).focusout(function(){
		$(this).attr('value','ID或昵称');	
		$(this).removeClass('focustext');
	}); 
	
	//登陆后状态header效果
	$('#LoginList li').hover(function(){
		if($.User.getSingleAttribute('is_redis') == false){
			$.User.refershWebLoginHeader();
		}
		$(this).addClass('loginover').siblings().removeClass('loginover');	
	},function(){
		$(this).removeClass('loginover');
	});	
	$('#SearchText').keyup(function(){
		var data = $(this).attr('value');
		$.User.searchUser(data);
		
	});
	
	$('#searchbtn').click(
		function(){
			var data = $('#SearchText').attr('value');
			$.User.searchUser(data);
		}
	);
	/*头部头像弹出框*/
	$('#Portrait').hover(function(){
		$('#PortraitInfo').stop(true,true).css('display','block');
		$('.updatebtn').bind('click',function(){
			$('#MakeName').hide();
			$('#MakeText').show();
			$('#nickname').val($.User.getSingleAttribute('nk',false));
		});
	},function(){
		$('#PortraitInfo').stop(true,true).css('display','none');
	});
	//头部修改昵称绑定
	$('.topsurebtn').bind('click',function(){
		$.User.updateUserNickName($('#nickname').val(),this);
		$('#MakeName').show();
		$('#MakeText').hide();
	
	});
	
	/*导航条hover事件*/
	$('#PortMenu dd').hover(function(){
		$(this).find('a').addClass('portover').parents().siblings().children('a').removeClass('portover');	
	});
	
	
	$.User.rankUserProgress();
	if($.User.isDotey(true)){
		$.User.rankDoteyProgress();
	}
	/*主播头像提示*/
	$('#TopHeader').hover(function(){
		$(this).find('.changehead').css('display','block');	
	},function(){
		$(this).find('.changehead').css('display','none');	
	});
});



$.extend({
	regExpContainer : {
		tel : /^\+?[0\s]*[\d]{0,4}[\-\s]?\d{0,6}[\-\s]?\d{4,12}$/,
		phone : /^\d{11}$/,
		qq : /^[1-9]\d{4,14}$/,
		email : /^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/,
		idCard : /^(?:\d{17}[\d|X]|\d{15})$/,
		url : /^(?:http(?:s)?:\/\/(?:[\w-]+\.)+[\w-]+(?:\:\d+)*(?:\/[\w- .\/?%&=]*)?)$/,
		html : /^<(.*)>.*|<(.*)\/>$/,
		ipfour : /(?:(?:25[0-5]|2[0-4]\d|1\d{2}|0?[1-9]\d|0?0?\d)\.){3}(?:25[0-5]|2[0-4]\d|1\d{2}|0?[1-9]\d|0?0?\d)/,
		ipsix : /^(?:(?:(?:[a-f0-9]{1,4}:){6}|::(?:[a-f0-9]{1,4}:){5}|(?:[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){4}|(?:(?:[a-f0-9]{1,4}:){0,1}[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){3}|(?:(?:[a-f0-9]{1,4}:){0,2}[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){2}|(?:(?:[a-f0-9]{1,4}:){0,3}[a-f0-9]{1,4})?::[a-f0-9]{1,4}:|(?:(?:[a-f0-9]{1,4}:){0,4}[a-f0-9]{1,4})?::)(?:[a-f0-9]{1,4}:[a-f0-9]{1,4}|(?:(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.){3}(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5]))|(?:(?:(?:[a-f0-9]{1,4}:){0,5}[a-f0-9]{1,4})?::[a-f0-9]{1,4}|(?:(?:[a-f0-9]{1,4}:){0,6}[a-f0-9]{1,4})?::))$/i,
		script : /<script(?:.*?)>(?:[^\x00]*?)<\/script>/,
		float : /^[\-\+]?[0-9]+\.[0-9]+$/,
		int : /^[\-\+]?[0-9]+$/,
		illegal : /([\\<'">\s\{\}\[\]])/
	},
	isInt : function(param){
		return this.regExpContainer.int.test(param);
	},
	isEmpty : function(param){
		return '' == $.trim(param);
	},
	isFloat : function(param){
		return this.regExpContainer.float.test(param);
	},
	isLength : function(length,start,end){
		return (start && length >= start) && (end && length <= end);
	},
	isTel : function(param){
		return this.regExpContainer.tel.test(param);
	},
	isPhone : function (param){
		return this.regExpContainer.phone.test(param);
	},
	isEmail : function(param){
		return this.regExpContainer.email.test(param);
	},
	isQQ : function (param){
		return this.regExpContainer.qq.test(param);
	},
	isIdCard : function(param){
		return this.regExpContainer.idCard.test(param);
	},
	isUrl : function(param){
		return this.regExpContainer.url.test(param);
	},
	isScript : function(param){
		return this.regExpContainer.script.test(param);
	},
	isHtml : function(param){
		return this.regExpContainer.html.test(param);
	},
	isIpv4 : function(param){
		return this.regExpContainer.ipfour.test(param);
	},
	isIpv6 : function(param){
		return this.regExpContainer.ipsix.test(param);
	},
	isEqual : function(value,value1){
		return $.trim(value) == $.trim(value1);
	},
	isReverseEqual : function(value,value1){
		return !($.trim(value) == $.trim(value1));
	},
	isPipiUserName : function(username){
		return /^[0-9A-Za-z_]{4,15}$/.test(username);
	},
	isPipiPassWord : function(password){
		return /(?:\d+.*[a-zA-Z]+)|(?:[a-zA-Z]+.*\d+)/.test(password);
	},
	isIllegal : function(param){
		return this.regExpContainer.illegal.test(param);
	}
});

$.extend({
	yesCss : '',
	yesNo  : 'error',
	selected : function(id,tipId,errorTips){
		return this.validate(-1 != $(id).val(),tipId,errorTips);
	},
	empty : function(id,tipId,errorTips){
		return this.validate(!$.isEmpty($(id).val()),tipId,errorTips);
	},
	phone : function(id,tipId,errorTips){
		return this.validate($.isPhone($(id).val()),tipId,errorTips);
	},
	money : function(id,tipId,errorTips){
		var bool = ($.isFloat($(id).val()) || $.isInt($(id).val()));
		return this.validate(bool,tipId,errorTips);
	},
	checked : function(id,tipId,errorTips){
		return this.validate($(id).attr('checked'),tipId,errorTips);
	},
	url : function(id,tipId,errorTips){
		return this.validate($.isUrl($(id).val()),tipId,errorTips);
	},
	email : function(id,tipId,errorTips){
		return this.validate($.isEmail($(id).val()),tipId,errorTips);
	},
	idCard : function(id,tipId,errorTips){
		return this.validate($.isIdCard($(id).val()),tipId,errorTips);
	},
	tel : function(id,tipId,errorTips){
		return this.validate($.isTel($(id).val()),tipId,errorTips);
	},
	int : function(id,tipId,errorTips){
		return this.validate($.isInt($(id).val()),tipId,errorTips);
	},
	float : function(id,tipId,errorTips){
		return this.validate($.isFloat($(id).val()),tipId,errorTips);
	},
	equal : function(value,value1,tipId,errorTips){
		return this.validate($.isEqual(value,value1),tipId,errorTips);
	},
	reverseEqual : function(value,value1,tipId,errorTips){
		return this.validate(!$.isEqual(value,value1),tipId,errorTips);
	},
	pipiUserName : function(id,tipId,errorTips){
		return this.validate($.isPipiUserName($(id).val()),tipId,errorTips);
	},
	pipiPassWord :  function(id,tipId,errorTips){
		return this.validate($.isPipiPassWord($(id).val()),tipId,errorTips);
	},
	script : function(id,tipId,errorTips){
		return this.validate($.isScript($(id).val()),tipId,errorTips);
	},
	html : function(id,tipId,errorTips){
		return this.validate($.isHtml($(id).val()),tipId,errorTips);
	},
	illegal :  function(id,tipId,errorTips){
		return this.validate(!$.isIllegal($(id).val()),tipId,errorTips+"' <strong>"+RegExp.$1+" <strong>'");
	},
	len :  function(length,start,end,tipId,errorTips){
		return this.validate($.isLength(length,start,end),tipId,errorTips);
		
	},
	controllCss : function (id,remove,add,tips){
		var obj = $(id);
		obj.attr('class',remove);
		obj.attr('class',add);
		if(null != tips)
			obj.html(tips);
		return this.yesCss == add ? true : false;
	},
	validate : function (bool,tipId,errorTips){
		if(bool)
			return this.controllCss(tipId,this.yesNo,this.yesCss,'');
		return this.controllCss(tipId,this.yesCss,this.yesNo,errorTips);
	}
});

jQuery.User={
	//用户属性
	UserAttribute : {},
	//获取服务端的属性状态
	getUserAttribute : function (){
		var _this = this;
		$.ajax({
			type:"GET",
			url:"index.php?r=user/attribute",
			data:{isScriptFile:1},
			dataType:"json",
			async: false, 
			success:function(data){
				_this.UserAttribute = data;
				user_attribute = data;
			}
		});
		return _this.UserAttribute;	
	},
	searchUser : function(data){
		if(data.length >= 3 && data.length <= 20){
			var baseUrl = '/select/';
			$.ajax({
				type:"GET",
				url:baseUrl+"?sort=int_number asc,score desc&df=all&wt=json&indent=true&rows=200",
				data:{q:data},
				dataType:"json",
				async: false, 
				success:function(response){
					if(response.response){
						var li = '';
						var docs = response.response.docs;
						for(var i=0;i<docs.length;i++){
							var rows = docs[i];
							var href = '';
							if(rows.dotey != 'undefined' && rows.dotey != null){
								href = 'http://'+location.host+'/'+rows.uid;
							}else{
								href ='javascript:void(0)';
							}
							li += "<li><a class='fleft' href='"+href+"' target='"+hrefTarget+"'>"+rows.nickname+"</a>";
							if(rows.use_number != 'undefined' && rows.use_number != null && rows.use_number != ''){
								var numCss = $.User.getNumCss(rows.use_number);
								if(rows.dotey != 'undefined' && rows.dotey != null){
									li += "<a href='"+href+"' target='"+hrefTarget+"'><span class='fleft "+numCss+"'><em>靓</em>"+rows.use_number+"</span></a>";
								}else{
									li += "<span class='fleft "+numCss+"'><em>靓</em>"+rows.use_number+"</span>";
								}
								
							}else{
								if(rows.dotey != 'undefined' && rows.dotey != null)
									li += "<a href='"+href+"' target='"+hrefTarget+"'>("+rows.uid+")</a>";
								else
									li += "("+rows.uid+")";
							}
							li += "</li>";
							
						}
						if(li != ''){
							$('.searchList').show();
							$('.searchList ul').html(li);
						}else{
							$('#SetSuc #oneline').html('搜索不到结果');
							$.mask.show('SetSuc',1500);
							$('.searchList').hide();
						}
					}
				}
			});
		}
	},
	//取得这户端的属性状态
	getClientUserAttribute : function(){
		if(!this.UserAttribute.nk && user_attribute){
			this.UserAttribute = user_attribute;
		}
		return this.UserAttribute;
	},
	
	//重新刷新用户登录的头信息
	refershWebLoginHeader : function(){
		this.getUserAttribute();
		this.loginHtmlHeader();
	},
	loginHtmlHeader : function(){
		if($.User.getSingleAttribute('uid',true) > 0 ){
			$('#login_header').css('display','');
			$('#logout_header').css('display','none');
			if(this.getSingleAttribute('num',true) == 0){
				$('#header_uid').html(this.getSingleAttribute('uid',false));
			}else{
				var num = this.getSingleAttribute('num',true);
				if(num.n > 0){
					var numCss = this.getNumCss(num.n);
					var numCss = this.getNumCss(num.n);
					$('#jpnumb').attr('style','display:block;');
					$('#jpnumb').removeAttr('class');
					$('#jpnumb').addClass('fleft '+numCss);
					var jpnumb = $('#jpnumb');
					if(jpnumb[0].childNodes[1].nodeType == 3){
						jpnumb[0].removeChild(jpnumb[0].childNodes[1]);
					}
					$('#jpnumb em').after(num.n);
					$('#jpnumb span').html(num.s);
					$('#header_uid').html('');
				}else{
					$('#header_uid').html(this.getSingleAttribute('uid',false));
				}
			}
			
			$('#header_nk').html(this.getSingleAttribute('nk',false));
			$('#header_rk').removeAttr('class');
			$('#header_rk').addClass('fleft mt2 mr10 lvlr lvlr-'+this.getSingleAttribute('rk',true));
			$('#header_dk').removeAttr('class');
			$('#header_dk').addClass('fleft mr10 lvlo lvlo-'+this.getSingleAttribute('dk',true));
			$('#header_pipiegg').html(this.getPipiEggs());
			$('#header_eggpoints').html(this.getSingleAttribute('ep',true));
			$('#header_charmpoints').html(this.getSingleAttribute('cp',true));
			$('#header_avatar').attr('src',this.getSingleAttribute('avatar',true));
			if(this.getSingleAttribute('u_rk',true) > 0){
				$('#RichLevel').attr('title','当前排在第'+this.getSingleAttribute('u_rk',true)+'名');
			}
			if(this.isDotey(true)){
				$('#login_dotey_rank').css('display','');
				$('#login_dotey_charmpoints').css('display','');
				$('#login_dotey_archives').css('display','');
				$('#login_dotey_archives a').attr('href','/'+this.getSingleAttribute('uid',false));
				$('#login_dotey_apply').css('display','none');
			}else{
				$('#login_dotey_rank').css('display','none');
				$('#login_dotey_charmpoints').css('display','none');
				$('#login_dotey_archives').css('display','none');
				$('#login_dotey_apply').css('display','');
			}
			this.rankUserProgress();
			if(this.isDotey(true)){
				this.rankDoteyProgress();
			}
			
			/*
		   if(this.getSingleAttribute('st',true) > 0){
		   		$('#viewStar').css({'display':''});
		    }else{
		   		$('#viewStar').css({'display':'none'});
		    }*/
			if(!$.isEmptyObject(newMessage)){
				var messageContent = $.isEmpty(newMessage.s_title) ? $.isEmpty(newMessage.title) ? newMessage.content : newMessage.title : newMessage.s_title;
				var messageHref = 'index.php?r=account/message&type=';
				if(newMessage.category == 0){
					if(newMessage.s_category == 2){
						messageHref += 'site';
					}else{
						messageHref += 'system';
					}
				}else if(newMessage.category == 1){
					messageHref += 'family';
				}
				$('.newApply').html('<a href="'+messageHref+'" target="_blank">'+messageContent+'</a>');
				$('.red-dot').show();
				$('div .newInfo').show();
			}
		}else{
			$('#login_header').css('display','none');
			$('#logout_header').css('display','');
		}
	},
	//取得用户属性
	getSingleAttribute : function(attribute,isZero){
		if(user_attribute[attribute] == null || user_attribute[attribute] == 'undefined' || !user_attribute[attribute]){
			return isZero == true ? 0 : '';
		}
		return user_attribute[attribute];
	},
	//取得用户正确的皮蛋余额
	getPipiEggs : function(){
		var pe = this.getSingleAttribute('pe',true);
		var fe = this.getSingleAttribute('fe', true);
		return pe - fe;
	},
	//是否是主播
	isDotey : function (staticPage){
		if(staticPage == false){
			return is_dotey;
		}
		var user_type = this.getSingleAttribute('ut',true);
		if(user_type <= 1){
			return false;
		}
		return (user_type & 2) == 2;
	},
	//控制注册登录弹出层
	loginController : function (scenario){
		
		if(scenario == null || scenario == 'undefined'){
			scenario = curLoginController;
		}
		if(domain_type == 'pptv'){
			
			if(scenario == 'register'){
				showPPTVReg();
			}else{
				showPPTVLogin();
			}
			return true;
		}else if(domain_type == 'tuli'){
			if(scenario == 'register'){
				window.Tuli.register();
			}else{
				var paramsObj = {'url':document.URL};
				window.Tuli.login(paramsObj);
			}
			return true;
		}
		
		var loginStyle = $('#loginController').attr('style');
		if( loginStyle.indexOf('none') > 0 ){
			$.mask.show('loginController');
		}
		
		if(scenario == 'register'){
			$('#register_code_img').click();
			$('.login-hd ul li').first().attr('class','');
			$('.login-hd ul li').last().attr('class','logincur');
			$('.register').attr('style','display:');
			$('.loginin').attr('style','display:none;');
		}else{
			$('#login_code_img').click();
			$('.login-hd ul li').last().attr('class','');
			$('.login-hd ul li').first().attr('class','logincur');
			$('.register').attr('style','display:none;');
			$('.loginin').attr('style','display:;');
		}
	},
	//关注用户
	attentionUser : function(uid,target,type){
		if($.User.getSingleAttribute('uid',true) <= 0){
			$.User.loginController('login');
			return false;
		}
		if(uid <= 0){
			$('#SetSuc #oneline').html('非法操作');
			$.mask.show('SetSuc',1000);
			return false;
		}
		$.ajax({
			type : 'post',
			url : 'index.php?r=user/attention',
			data : {uid:uid},
			success:function(data){
				if(data == 0){
					if(type == 'single'){
						//todo 排行榜单个关注
						
					}else if(type == 'live'){
						//todo 直播间关注
					}else{
						$(target).html('<span class="attent-text" style="display: none;">取消关注</span>');
					}
					if(type == 'live'){
						//直播间关注
						$("#attentionUser a").html('已关注');
						var num=parseInt($("#attentionUser span").html());
						$("#attentionUser span").html(num+1);
					}else{
						$(target).addClass('cancelatt');
					}
					$(target).attr('title','取消关注');
					$(target).attr('onclick','$.User.cacnelAttentionUser("'+uid+'",this,"'+type+'")');
					$('#SetSuc #oneline').html('关注成功');
					$.mask.show('SetSuc',1000);
				}
				if(data == -1){
					$.User.loginController('login');
				}
				if(data == -2){
					$('#SetSuc .oneline').html('非法操作');
					$.mask.show('SetSuc',1000);
				}
				if(data == -3){
					$('#SetSuc #oneline').html('自己不能关注自己');
					$.mask.show('SetSuc',1000);
				}
				
			}
		});
		//图利关注
		if(domain_type == 'tuli'){
			$.ajax({
				type : 'post',
				url : 'index.php?r=tuli/GetUInfo',
				data : {uid:uid},
				success:function(data){
					var data=eval('('+data+')');
					if(data.res == true){
						window.Tuli.follow(data.data);
					}
				}
			});
		}
	},
	//取消关注用户
	cacnelAttentionUser : function(uid,target,type){
		if($.User.getSingleAttribute('uid',true) <= 0){
			$.User.loginController('login');
			return false;
		}
		if(uid <= 0){
			$('#SetSuc #oneline').html('非法操作');
			$.mask.show('SetSuc',1000);
			return false;
		}
		$.ajax({
			type : 'post',
			url : 'index.php?r=user/cancelAttention',
			data : {uid:uid},
			success:function(data){
				if(data == 0){
					if(type == 'single'){
						//todo 排行榜单个关注
						
					}else if(type == 'live'){
						//todo 直播间关注
					}else{
						$(target).html("<span class='attent-text' style='display: none;'>关注</span>");
					}
					$(target).attr('title','关注');
					$(target).attr('onclick','$.User.attentionUser("'+uid+'",this,"'+type+'")');
					if(type=='live'){
						$("#attentionUser a").html('+关注');
						var num=parseInt($("#attentionUser span").html());
						$("#attentionUser span").html(num-1);
					}else{
						$(target).removeClass('cancelatt');
					}
					if(type == 'attention'){
						$(target).parent().parent().remove();
					}
					$('#SetSuc #oneline').html('取消关注成功');
					$.mask.show('SetSuc',1000);
				}
				if(data == -1){
					$.User.loginController('login');
				}
				if(data == -2){
					$('#SetSuc #oneline').html('非法操作');
					$.mask.show('SetSuc',1000);
				}
				
			}
		});	
	},
	updateUserNickName : function(nickName,target){
		var uid = $.User.getSingleAttribute('uid',true);
		if(uid <= 0){
			$.User.loginController('login');
			return false;
		}
		if($.trim(nickName) == ''){
			$('#SetSuc #oneline').html('昵称不能为空');
			$.mask.show('SetSuc',1000);
			return false;
		}
		var flag = false;
		$.ajax({
			type : 'post',
			url : 'index.php?r=account/edit',
			data : {'nickname':nickName,uid:uid},
			success:function(response){
				var response = $.parseJSON(response);
				if(response.result == true){
					if($(target).hasClass('updateNickName') == true){
						$('#MakeName .petname').get(0).innerHTML = nickName;
					}
				}
				$('#SetSuc #oneline').html(response.msg);
				$.mask.show('SetSuc',1000);
				
			}
		});	
		return flag;
	},
	userRichRank : function(type,appendHtml){
		target = appendHtml == null ? "#rank_rich_target" : appendHtml;
		$.ajax({
			type : 'post',
			url : 'index.php?r=user/richRank/type/'+type,
			data : {type:type,target:hrefTarget},
			success:function(data){
				$(target).html(data);
			}
		});	
	},
	userFriendlyRank : function(type,appendHtml){
		target = appendHtml == null ? "#rank_friendly_target" : appendHtml;
		$.ajax({
			type : 'post',
			url : 'index.php?r=user/friendlyRank/type/'+type,
			data : {type:type,target:hrefTarget},
			success:function(data){
				$(target).html(data);
			}
		});	
	},
	userCharmRank : function (type,appendHtml){
		target = appendHtml == null ? "#rank_charm_target" : appendHtml;
		$.ajax({
			type : 'post',
			url : 'index.php?r=user/doteyRank/type/'+type,
			data : {type:type,target:hrefTarget},
			async : false,
			success:function(data){
				$(target).html(data);
			}
		});	
	},
	rankUserProgress : function(){
		var nownum = this.getSingleAttribute('de',true);
		var curnum = this.getSingleAttribute('cude',true);
		var total = this.getSingleAttribute('nxde',true);
		this.rankProgress('RichLevel', total, nownum, curnum);
		
	},
	rankDoteyProgress:function(){
		var nownum = this.getSingleAttribute('ch',true);
		var curnum = this.getSingleAttribute('cuch',true);
		var total = this.getSingleAttribute('nxch',true);
		this.rankProgress('CharmLevel', total, nownum, curnum);
	},
	rankProgress : function(id,total,nownum,curnum){
		total = parseInt(total);
		nownum = parseInt(nownum);
		curnum = parseInt(curnum);
		if(nownum>total){
			nownum=total;
		}
		if(curnum > nownum){
			curnum = nownum;
		}
		var wdper = 0;
		if(total == 0){
			wdper = 0;
		}else{
			var dividend = total-curnum;
			var divisor = nownum-curnum;
			$('#'+id+' .now-rate').html(divisor);
			$('#'+id+' .total-rate').html(dividend);
			wdper=Math.round(( divisor/dividend )*100)+'%';
		}
		$('#'+id+' .process').width(wdper);
	},
	checkin:function(type){
		$.ajax({
			type: "POST",
			url: "index.php?r=account/checkin",
			data: {'checkinAll':type},
			dataType: "json",
			async: false,
			success: function(response){
				if(response.is_month && response.result==false){
					$('#SignFram .surebtn').hide();
					$('#SignFram .shiftbtn').show();
				}
				if(response.result === true){
					$('#checkin_title').html('签到成功');
					//var meigui = '<img src="'+$.Global.getFontPath()+'/fontimg/common/meigui.jpg"/>';
					//var cao = '<img src="'+$.Global.getFontPath()+'/fontimg/common/cao.jpg"/>';
					if(response.is_month == true){
						$('#SignFram .sucinfo').html(response.msg);
					}else{
						$('#SignFram .sucinfo').html(response.msg);
					}
				}else{
					$('#checkin_title').html('签到失败');
					$('#SignFram .sucinfo').html(response.msg);
					$('#SignFram .surebtn').unbind('click');
					if(response.href){
						$('#SignFram .surebtn').val('马上设置');
						$('#SignFram .surebtn').one('click',function(){
							window.location.href = response.href;
						});
					}else{
						$('#SignFram .surebtn').val('确    定');
					}
				}
				$.mask.show('SignFram');
			}
		});
	},
	getNumCss:function(num){
		if($.isInt(num)){
			num = new String(num);
		}
		var sLen = num.length;
		if(sLen == 4){
			return 'jpnumb';
		}else{
			return 'sixnumb';
		}
	}
};

jQuery.Global = {
	getRunEnvironment : function(){
		return runEnvironment;
	},
	
	getUploadUrl : function(){
		return UploadHttpUrl;
	},
	getFontPath : function(folder,subFolder){
		if(folder == null || folder == 'undefined'){
			return runFontPath;
		}
		runFontPath += '/'+folder;
		if(subFolder == null || subFolder == 'undefined'){
			return runFontPath;
		}
		return runFontPath += '/'+subFolder;
	},
	getParam : function(paramName){
	     paramValue = "";
	     isFound = false;
	     if (location.search.indexOf("?") == 0 && location.search.indexOf("=")>1){
	        arrSource = unescape(location.search).substring(1,location.search.length).split("&");
	        i = 0;
	        while (i < arrSource.length && !isFound){
	              if (arrSource[i].indexOf("=") > 0){
	                   if (arrSource[i].split("=")[0].toLowerCase()==paramName.toLowerCase()){
	                            paramValue = arrSource[i].split("=")[1];
	                            isFound = true;
	                         }
	                    }
	                    i++;
	               }
	       }
	       return paramValue;
	}
	
};

//直播间背景
function ChangeBg(bgurl,bgcolor){ 
	if(bgcolor==null||bgcolor==''){
		$('#Livebg').removeClass('livingbg');
		$('#Livebg').css({
			"background":"url("+bgurl+")"
		});	
	}else{
		$('#Livebg').css({
		"background":bgcolor+" url("+bgurl+")"+" no-repeat center top"
		});	
	}
}

//鼠标放至头像显示关注
function showattent(atclass){
	$(atclass).bind({
		mouseover:function(){$(this).find('.attent').css('display','block');},
		mouseout:function(){$(this).find('.attent').css('display','none');}
	});
}

function goExchange(t){
	if(exchangeUrl!='#'){
		if(!t || t.length == 0){
			t = '_blank';
		}
		window.open(exchangeUrl,t);
	}
}

