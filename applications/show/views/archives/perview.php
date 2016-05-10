<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script>
var is_dotey=false;
</script>
</head>
<?php if($coverName):?>
<script>
<?php 
	if($width/$height>1.7){
		$x1=intval(($width-(($height-10)*1.7))/2);
		$y1=5;
		$x2=intval(($width-(($height-10)*1.7))/2+($height-10)*1.7);
		$y2=$height-5;
	}else {
		$x1=5;
		$y1=intval(($height-($width-10)/1.7)/2);
		$x2=$width-5;
		$y2=intval(($height-(($width-10)/1.7))/2+($width-10)/1.7);
	}
?>
$(function(){
	$('#editorpic img').imgAreaSelect({
		 aspectRatio:'1.7:1',
		 autoHide:false,
		 handles:false,
		 persistent:true,
		 show:false,	
         x1: <?php echo $x1;?>,
         y1: <?php echo $y1;?>,
         x2: <?php echo $x2;?>,
         y2: <?php echo $y2;?>,
         onSelectEnd:function (img, selection) {
             $('#cover_x').val(selection.x1);
             $('#cover_y').val(selection.y1);
             $('#cover_w').val(selection.x2);
             $('#cover_h').val(selection.y2);
	 	}
	});
})
function confirmCover(){
	var cover_x=$("#cover_x").val();
	var cover_y=$("#cover_y").val();
	var cover_w=$("#cover_w").val();
	var cover_h=$("#cover_h").val();
	var newCoverImg=$("#newCoverImg").val();
	//if(cover_x==''||cover_y==''||cover_w==''||cover_h==''||newCoverImg==''){
		//$("#SucMove .popcon",parent.document).empty().html('<p class="oneline">您还没有节目封面照，请上传</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>');
		//$("#SucMove",parent.document).show();
		//setTimeout('$("#SucMove",parent.document).hide()',3000);
		//$("#mask",parent.document).hide();
	//}
	$.ajax({
		type:'POST',
		url:'index.php?r=dotey/confirmCover',
		dataType:'json',
		data:{newCoverImg:newCoverImg,cover_x:cover_x,cover_y:cover_y,cover_w:cover_w,cover_h:cover_h},
		success:function(data){
			if(data){
				window.parent.document.getElementById('Covers').style.display='none';
				if(data.flag==1){
					window.parent.document.getElementById('coverTips').style.display='none';
				}
				window.parent.document.getElementById('popcon').innerHTML='';
				window.parent.document.getElementById('popcon').innerHTML='<p class="oneline">'+data.message+'</p><p class="oneline"><input class="shiftbtn" type="button" onClick="$.mask.hide(\'SucMove\');" value="确&nbsp;&nbsp;定"></p>'
				window.parent.document.getElementById('SucMove').style.display='block';
				setTimeout('window.parent.document.getElementById("SucMove").style.display="none"',3000);
				window.parent.document.getElementById('mask').style.display='none';
			}
		}
	})
}
</script>
<?php endif;?>
<body>
<style>
body{background:#fff;}
li{list-style:none;}
</style>
<input type="hidden" name="cover_x" id="cover_x" <?php if($coverName):?>value="<?php echo $x1;?>"<?php endif;?>/>
<input type="hidden" name="cover_y" id="cover_y" <?php if($coverName):?>value="<?php echo $y1;?>"<?php endif;?>/>
<input type="hidden" name="cover_w" id="cover_w" <?php if($coverName):?>value="<?php echo $x2;?>"<?php endif;?>/>
<input type="hidden" name="cover_h" id="cover_h" <?php if($coverName):?>value="<?php echo $y2;?>"<?php endif;?>/>
<input type="hidden" name="newCoverImg" id="newCoverImg" <?php if($coverName):?>value="<?php echo $coverName;?>"<?php endif;?>/>
<li><label>编辑裁剪</label><div class="editorpic" id="editorpic"><?php if($coverImg):?><img class="editorimg" id="editImg" src="<?php echo $coverImg;?>"/><?php endif;?></div></li>
<li><input class="surebtn" type="button" onclick="confirmCover()" value="确&nbsp;&nbsp;&nbsp;&nbsp;定"></li>

</body>
</html>