(function($){$.fn.extend({insertAtCaret:function(myValue){var $t=$(this)[0];if(document.selection){this.focus();sel=document.selection.createRange();sel.text=myValue;this.focus();}else if($t.selectionStart||$t.selectionStart=='0'){var startPos=$t.selectionStart;var endPos=$t.selectionEnd;var scrollTop=$t.scrollTop;$t.value=$t.value.substring(0,startPos)+myValue+$t.value.substring(endPos,$t.value.length);this.focus();$t.selectionStart=startPos+myValue.length;$t.selectionEnd=startPos+myValue.length;$t.scrollTop=scrollTop;}else{this.value+=myValue;this.focus();}}})})(jQuery);

var Ubb={
	facePath:archives.staticPath+'/fontimg/express/',	
	//普通表情
	faceCommon : [],
	//vip表情
	faceVip : [],
	faceContainer : 'FaceBox',
	commonFaceContainer : 'common',
	vipFaceContainer : 'vip',
	insertPosition:'msg_input', //表情插入位置
	facePosition:'FaceGood',
	initFace : function(){
		var i=j=k=0;
		var faceCommon={};
		if(faceList!=null&&faceList!=''){
			for(i=0;i<faceList.length;i++){
				if(faceList[i].type=='common'){
					var faceCommon=new Object();
					faceCommon['code']=faceList[i].code;
					faceCommon['image']=faceList[i].image;
					this.faceCommon.push(faceCommon);
					j++;
				}else if(faceList[i].type=='vip'){
					var faceVip=new Object();
					faceVip['code']=faceList[i].code;
					faceVip['image']=faceList[i].image;
					this.faceVip.push(faceVip);
					k++;
				}
			}
		}
		$("#"+this.faceContainer).hide();
	},
	showFace:function(id,position){
		this.insertPosition=position?position:'msg_input';
		var text = $('#'+id).next("#"+this.faceContainer).find("#"+this.commonFaceContainer).html();
		$('#'+id).next("#"+this.faceContainer).slide({titCell:".face-hd li",targetCell:".face-con",trigger:"click",delayTime:0});
		this.facePosition=id;
		if(!text || $.trim(text) == ''){
			this.commonFace();
			var vip=$.User.getSingleAttribute('vip',true);
			var timestamp=new Date().getTime()/1000;
			if(vip.vt>timestamp||vip.vt==0){
				this.vipFace();
			}else{
				$('#'+id).next("#"+this.faceContainer).find("#"+this.vipFaceContainer).html('<p>您还不是尊贵的VIP用户，马上去<br/><a href="index.php?r=shop/vip" class="pink" target="_blank">商城</a>购买吧</p>');
			}
			
		}
		$('#'+id).next("#"+this.faceContainer).show();
	},
	commonFace : function(){
		var obj=this;
		var faceDiv='';
		$.each(this.faceCommon,function(i,val){
			faceDiv+="<img rel='"+val.code+"' title='"+val.code+"' class='face_img' src='"+obj.facePath+"common/"+val.image+"' />";
		})
		if(Ubb.facePosition=='FaceGood'){
			faceDiv+="<img class='face_img' onclick='Dice.sendDice(\"common_dice\")' src="+archives.staticPath+"/fontimg/dice/common_dice_5.png>";
		}
		$('#'+this.facePosition).next("#"+this.faceContainer).find("#"+Ubb.facePosition+'_'+obj.commonFaceContainer).html(faceDiv);
		obj.faceFunction(obj.commonFaceContainer);
	},
	vipFace: function(){
		var obj=this;
		var faceDiv='';
		$.each(this.faceVip,function(i,val){
			faceDiv+="<img rel='"+val.code+"' title='"+val.code+"' class='face_img' src='"+obj.facePath+"vip/"+val.image+"' />";
		})
		$('#'+this.facePosition).next("#"+this.faceContainer).find("#"+Ubb.facePosition+'_'+this.vipFaceContainer).html(faceDiv);
		this.faceFunction(this.vipFaceContainer);
	},
	faceFunction : function(id){
		$("#"+Ubb.facePosition+'_'+id+" img").bind('click',function(){
			var face=$(this).attr('rel');
			if(face!=null||face!=undefined){
				var text= face;
				$("#"+Ubb.insertPosition).insertAtCaret(text);
			}
			$('#'+Ubb.facePosition).next("#"+Ubb.faceContainer).hide();
		})
	},
	faceHtml : function(string,isVip,isTransmit){
		if(faceList!=null&&faceList!=''){
			for(i=0;i<faceList.length;i++){
				if(string.indexOf(faceList[i].code)>=0){
					var reg=new RegExp('\\['+faceList[i].name+'\\]',"g");
					if(faceList[i].type=='vip'){
						if(isVip==1){
							string=string.replace(reg,'<img src="'+Ubb.facePath+faceList[i].type+'/'+faceList[i].image+'" />');
						}
					}else if(faceList[i].type=='common'){
						string=string.replace(reg,'<img src="'+Ubb.facePath+faceList[i].type+'/'+faceList[i].image+'" />');
					}
				}
			}
			
		}
		return string;
	}
		
};
$(function(){
	Ubb.initFace();
})