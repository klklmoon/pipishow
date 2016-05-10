<?php $userSer = new UserService();?>
<script type="text/javascript">
$(function(){
	var url = "<?php echo $this->createUrl('activities/guardangel');?>";
	//查看守护列表
	$('.watch').click(function(){
		$.ajax({
			url:url,
			dataType:'json',
			data:{'op':'lookGuardList'},
			type:'post',
			success:function(jsonMsg){
				$('.tips > .tips_con > a').hide();
				$('.tips > .tips_con > p').html(jsonMsg.message);
				$('.tips').show();
				setTimeout("$('.tips').hide();",3000);
			}
		});
	});
	//切换推荐主播
	$('.change').click(function(){
		var p = $(this).attr('p');
		var obj = this;
		if(p){
			p = parseInt(p)+1;
			$.ajax({
				url:url,
				dataType:'json',
				data:{'op':'changeLuckDotey','p':p},
				type:'post',
				success:function(jsonMsg){
					$(obj).attr('p',jsonMsg.currPage);
					var chtml = '';
					for(var key in jsonMsg.data){
						chtml += '<li>';
						chtml += '<div class="imgmsg"><a href="'+jsonMsg.data[key].href+'" target="_blank"><img src="'+jsonMsg.data[key].avatar+'" width="157" height="133"/></a></div>';
				        chtml += '<p><a href="'+jsonMsg.data[key].href+'" target="_blank">'+jsonMsg.data[key].nickname+'</a></p> <p>'+jsonMsg.data[key].title+'</p>';
				        chtml +=  '<p><a class="vote" href="javascript:void(1);" dotey_uid="'+jsonMsg.data[key].uid+'">守护TA</a></p>';
				        chtml += '</li>';
					}
					$('#luckDoteyList').html(chtml);
				}
			});
		}
	});
	//点击守护
	$('.vote').live('click',function(){
		var dotey_uid = $(this).attr('dotey_uid');
		if(dotey_uid){
			$.ajax({
				url:url,
				dataType:'json',
				data:{'op':'startGuard','dotey_uid':dotey_uid},
				type:'post',
				success:function(jsonMsg){
					$('.tips > .tips_con > a').hide();
					$('.tips > .tips_con > p').html(jsonMsg.message.info);
					$('.tips').show();
					setTimeout("$('.tips').hide();",3000);
				}
			});
		}else{
			$('.tips > .tips_con > a').hide();
			$('.tips > .tips_con > p').html('</br>参数有误');
			$('.tips').show();
			setTimeout("$('.tips').hide();",1500);
		}
	});
});
</script>
<div class="kong">
 <div class="w1000 relative">
 </div> 
</div>

<div class="w1000">
	<img src="<?php echo $this->pipiFrontPath;?>/fontimg/activities/guardangel/img1.jpg" />
</div>

<div class="w1020 relative rule">
   <div class="rule_con">
1、从“幸运主播”板块选择你要守护的主播（幸运主播为最近30天开播过的主播）<br/>
2、直播间视频左下角有守护图标，若用户在活动页已经守护主播，则进入该主播直播间后，每停留5分钟，便能累计1颗守护星（点击图标能看到守护星数量），累计到100颗之后，<br/>即可成为该主播的守护天使，并得到守护天使勋章；若用户还未守护主播，则点击守护图标能够选择守护主播<br/>
3、粉丝只有在主播直播的时候才能累计守护星数量，未直播时不累计<br/>
4、守护天使勋章只在你守护的主播直播间有效，去其他直播间则不会显示哦<br/>
5、守护天使活动以两个自然周为一个周期，即从第一周的周一开始粉丝可以选择主播进行守护并开始累计守护星，达到100颗之后就会自动出现守护天使勋章，勋章显示将一直持续到<br/>第二周的周日。第三周开始粉丝需要重新选择守护的主播，并重新累计守护星数量。<br/>
</div>
</div><!--.rule-->



<div class="w1020 actcon clearfix"> 
  <div class="player">
     <a href="javascript:void(1);" class="change fright" p="<?php echo $luckList['currPage'];?>"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/activities/guardangel/change.jpg" /></a>
     <a href="javascript:void(1);" class="watch fleft"><img src="<?php echo $this->pipiFrontPath;?>/fontimg/activities/guardangel/watch.jpg" /></a>
     <ul class="playerlist">
      <!--  
       <div class="tips none">
         <div class="tips_con">
           <p>守护成功！为TA累计满<span class="col-red">100</span>颗守<br/>护星就可获得守护天使勋章哟</p>
           <a href="#">确定</a>
         </div>
       </div>
       -->
       <div class="tips none">
         <div class="tips_con">
           <p></p>
           <a href="javascript:void(1);">开始守护</a>
         </div>
       </div>
       <div id='luckDoteyList'>
       <?php if($luckList['data']){?>
      <?php foreach($luckList['data'] as $uid=>$v){?>
      <li>
           <div class="imgmsg"><a href="<?php echo $v['href']?>" target="_blank"><img src="<?php echo $v['avatar']?>" width="157" height="133"/></a></div>
           <p><a href="<?php echo $v['href']?>" target="_blank"><?php echo $v['nickname'];?></a></p> <p><?php echo $v['title']?></p> 
           <p><a class="vote" href="javascript:void(1);" dotey_uid="<?php echo $v['uid'];?>">守护TA</a></p>
        </li>
      <?php }?>
      <?php }?>
       </div>
    </ul><!-- playerlist -->
  </div><!-- player -->
  
  <div class="intro">1、“本期最幸福新人主播”榜单显示等级在皇冠1（含）以下的新人主播，若主播在某一周期内从皇冠1以下升级到皇冠2及以上，则本周期依然能显示在该榜单中，到下一周期则只显<br/>
示在“本期最幸福主播”榜单上<br/>
2、“本期最幸福主播”榜单显示全部主播，按照守护星数量排列<br/>
3、“本期最佳新人守护天使”显示等级在绅士7（含）以下的用户，若用户在某周期内升级，则依然能显示在该榜单中，到下一周期再移出<br/>
4、“本期最佳守护天使”榜单只按照粉丝的守护星数量来排，而不限定粉丝的等级</div>
</div><!-- actcon -->


<div class="w1000 mt20">
<div class="clearfix">
    
  <div class="main-2 fleft">
      <div class="fleft w470 bang_bj p10">
        <a class="bang-tit">本期最幸福新人主播</a>
		<div class="hm-xsboard tabcon-bd fleft ">        
			<table class="xsboard">			
			<colgroup class="col-w35"></colgroup>
			<colgroup class="col-w45"></colgroup>
			<colgroup class="col-w125"></colgroup>			
			<colgroup class="col-w85"></colgroup>
			<colgroup class="col-w170"></colgroup>
			<tbody>
            
            <?php if($newDoteyRank){?>
            <?php foreach($newDoteyRank as $r=>$v){?>
            <tr>
				<td> <div class="list-num"><?php echo $r;?></div> </td>
				<td><a href="<?php echo $this->getTargetHref('/'.$v['duid'],true,false)?>" target="_blank"><img class="xscover" src="<?php echo $userSer->getUserAvatar($v['duid'],'small');?>" width=40 height=53 alt="" /></a></td>
				<td>
					<div class="col3-zhubo bor-r">
						<p class="xs-num"><?php echo $v['dnickname'];?></p>
						<em class="lvlo lvlo-<?php echo $v['drank'];?>"></em>
					</div>
				</td>			
				<td>
					<p class="bor-r"><em class="xs-num"><?php echo $v['star']?></em></p>
					<p class="col-green bor-r">守护星</p>
				</td>			
				<td>
					<span class="fans-nm"><a href="#3"><?php echo $v['nickname']?></a></span> <em class="lvlr lvlr-<?php echo $v['urank']?>"></em>
					<p class="col-green">守护天使</p>
				</td>
			</tr>
            <?php }?>
            <?php }?>
			</tbody>
			</table>
		</div>       
</div>

      <div class="fright w470 bang_bj p10">
        <a class="bang-tit">本期最佳新人守护天使</a>
		<div class="bcon">
             <table>			
                <tr style="color:#ff0000; font-size:14px;">					
					<td width="40">&nbsp;</td>
                    <td width="110">富豪昵称</td>					
					<td>等级</td>
                    <td width="110">守护主播 </td>
                    <td width="90">守护星</td>
				</tr>
				<?php if($newUserRank){?>
				<?php foreach($newUserRank as $r=>$v){?>
				<?php $colClass = $r<=2?'col1':'col4';?>
				<tr>					
					<td width="40"><a class="<?php echo $colClass;?>"><?php echo $r;?></a></td>
                    <td width="110" ><a href="#3" class="col2 xs-num dispblock"><?php echo $v['nickname']?> </a></td>
					<td><em class="lvlr lvlr-<?php echo $v['urank'];?>"></em></td>
                    <td width="110"><a href="#3" class="col2 xs-num dispblock"><?php echo $v['dnickname'];?></a></td>
					<td width="90"  class="col2"><?php echo $v['star'];?></td>
				</tr>
				<?php }?>
				<?php }?>
          </table>     
        </div><!-- bcon -->       
      </div> 
  </div><!-- .main-2 -->
        
  <div style="clear:both;"></div>      
</div>
</div><!-- w1000 -->


<div class="w1000 mt20">
<div class="clearfix">
    
  <div class="main-2 fleft">
      <div class="fleft w470 bang_bj2 p10">
        <a class="bang-tit">本期最幸福主播</a>
		<div class="hm-xsboard tabcon-bd fleft ">        
			<table class="xsboard">			
			<colgroup class="col-w35"></colgroup>
			<colgroup class="col-w45"></colgroup>
			<colgroup class="col-w125"></colgroup>			
			<colgroup class="col-w85"></colgroup>
			<colgroup class="col-w170"></colgroup>
			<tbody>
            
            <?php if($allDoteyRank){?>
            <?php foreach($allDoteyRank as $r=>$v){?>
            <tr>
				<td> <div class="list-num"><?php echo $r;?></div> </td>
				<td><a href="<?php echo $this->getTargetHref('/'.$v['duid'],true,false)?>" target="_blank"><img class="xscover" src="<?php echo $userSer->getUserAvatar($v['duid'],'small');?>" width=40 height=53 alt="" /></a></td>
				<td>
					<div class="col3-zhubo bor-r">
						<p class="xs-num"><?php echo $v['dnickname'];?></p>
						<em class="lvlo lvlo-<?php echo $v['drank'];?>"></em>
					</div>
				</td>			
				<td>
					<p class="bor-r"><em class="xs-num"><?php echo $v['star']?></em></p>
					<p class="col-green bor-r">守护星</p>
				</td>			
				<td>
					<span class="fans-nm"><a href="#3"><?php echo $v['nickname']?></a></span> <em class="lvlr lvlr-<?php echo $v['urank']?>"></em>
					<p class="col-green">守护天使</p>
				</td>
			</tr>
            <?php }?>
            <?php }?>
			</tbody>
			</table>
		</div>       
</div>

      <div class="fright w470 bang_bj2 p10">
        <a class="bang-tit">本期最佳守护天使</a>
		<div class="bcon">
            <table>			
                <tr style="color:#ff0000; font-size:14px;">					
					<td width="40">&nbsp;</td>
                    <td width="110">富豪昵称</td>					
					<td>等级</td>
                    <td width="110">守护主播 </td>
                    <td width="90">守护星</td>
				</tr>
				<?php if($allUserRank){?>
				<?php foreach($allUserRank as $r=>$v){?>
				<?php $colClass = $r<=2?'col1':'col4';?>
				<tr>					
					<td width="40"><a class="<?php echo $colClass;?>"><?php echo $r;?></a></td>
                    <td width="110"><a href="#3" class="col2 xs-num dispblock"><?php echo $v['nickname']?> </a></td>
					<td><em class="lvlr lvlr-<?php echo $v['urank'];?>"></em></td>
                    <td width="110"><a href="#3" class="col2 xs-num dispblock"><?php echo $v['dnickname'];?></a></td>
					<td width="90"  class="col2"><?php echo $v['star'];?></td>
				</tr>
				<?php }?>
				<?php }?>
          </table>     
        </div><!-- bcon -->       
      </div> 
  </div><!-- .main-2 -->
        
  <div style="clear:both;"></div>      
</div>
</div><!-- w1000 -->
