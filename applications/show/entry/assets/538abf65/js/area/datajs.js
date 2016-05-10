var _citys = [];//默认城市集合
function init(obj_1,val_1,obj_2,val_2,obj_3,val_3){
	
	//定义默认数据
	var ar = ["选择省份","选择城市","选择区县"];
	var pindex=0;
	var cindex=0;
	var ccindex=0;
	//初始化
	$("<option value=''>"+ar[0]+"</option>").appendTo($("#"+obj_1));
	$("<option value=''>"+ar[1]+"</option>").appendTo($("#"+obj_2));
	if(obj_3){
		$("<option value=''>"+ar[2]+"</option>").appendTo($("#"+obj_3));
	}
	//初始化obj_1
	for (i=0;i<mp.length;i++){
			var tmp = mp[i];
			if (mp[i]==val_1 || tmp.indexOf(val_1)> -1){
				pindex = i;
				$("<option selected>"+mp[i]+"</option>").appendTo($("#"+obj_1));
			}else{
				$("<option>"+mp[i]+"</option>").appendTo($("#"+obj_1));
				}
		}
	if (pindex >= 0 && mc[pindex]){
			for (n=0;n<mc[pindex].length;n++){
					var tmp = mc[pindex][n];
					if (mc[pindex][n]==val_2 || tmp.indexOf(val_2) > -1){
						cindex = n;
						$("<option selected>"+mc[pindex][n]+"</option>").appendTo($("#"+obj_2));
					}else{	
						//数组多选
						if(val_2 instanceof Array){
							var _isExists = $.inArray(mc[pindex][n],val_2);
							if(_isExists >= 0){
								$("<option selected>"+mc[pindex][n]+"</option>").appendTo($("#"+obj_2));
							}else{
								$("<option>"+mc[pindex][n]+"</option>").appendTo($("#"+obj_2));
							}
						}else{
							$("<option>"+mc[pindex][n]+"</option>").appendTo($("#"+obj_2));
						}
					}			
			}
			if(obj_3 && mh[pindex][cindex] && cindex != 0){
				for (m=0;m<mh[pindex][cindex].length;m++){
					var tmp = mh[pindex][cindex][m].replace(/\s+/g,"");
					var tmp_1 = val_3.replace(/\s+/g,"");
					if (tmp==tmp_1 || tmp.indexOf(tmp_1) > -1){
					//if (mh[pindex][cindex][m].replace(/\s+/g,"")==val_3.replace(/\s+/g,"")){
							$("<option selected>"+mh[pindex][cindex][m]+"</option>").appendTo($("#"+obj_3));
						}else{						
							$("<option>"+mh[pindex][cindex][m]+"</option>").appendTo($("#"+obj_3));
						}	
				}
			}
		}
	/*
	if (cindex!=0){
			for (m=0;m<mh[pindex][cindex].length;m++){
					if (mh[pindex][cindex][m]==val_3){
							$("<option selected>"+mh[pindex][cindex][m]+"</option>").appendTo($("#"+obj_3));
						}else{						
							$("<option>"+mh[pindex][cindex][m]+"</option>").appendTo($("#"+obj_3));
						}	
				}
		}
	*/	
		
	//响应obj_1的change事件
	$("#"+obj_1).change(function(){
		//获取索引
		pindex = $("#"+obj_1).get(0).selectedIndex;
		//清空c和h
		$("#"+obj_2).empty();
		//重新给c填充内容
		$("<option>"+ar[1]+"</option>").appendTo($("#"+obj_2));
			if (pindex!=0 && mc[pindex-1]){
				for (k=0;k<mc[pindex-1].length;k++){
					if(_citys.length > 0){
						var _isExists = $.inArray(mc[pindex-1][k],_citys);
						if(_isExists >= 0){
							$("<option selected>"+mc[pindex-1][k]+"</option>").appendTo($("#"+obj_2));
						}else{
							$("<option>"+mc[pindex-1][k]+"</option>").appendTo($("#"+obj_2));
						}
					}else{
						$("<option>"+mc[pindex-1][k]+"</option>").appendTo($("#"+obj_2));
					}
				}
			}	
		//清空h
		if(obj_3){
			$("#"+obj_3).empty();
			$("<option>"+ar[2]+"</option>").appendTo($("#"+obj_3));
		}
	});
	
	//响应obj_2的change事件	
	$("#"+obj_2).change(function(){
		//获取省索引
		pindex = $("#"+obj_1).get(0).selectedIndex;
		//获取市索引
		cindex = $("#"+obj_2).get(0).selectedIndex;
		//清空h
		if(obj_3){
			$("#"+obj_3).empty();
			//重新给h填充内容
			$("<option>"+ar[2]+"</option>").appendTo($("#"+obj_3));
			if (cindex!=0 && mh[pindex-1][cindex-1]){
				for (j=0;j<mh[pindex-1][cindex-1].length;j++){
					$("<option>"+mh[pindex-1][cindex-1][j]+"</option>").appendTo($("#"+obj_3));
				}
			}
		}
	});
	
}