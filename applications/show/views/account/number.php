<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
     
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li><a href="<?php echo $this->createUrl('account/bag');?>">礼物背包</a></li>
            <li><a href="<?php echo $this->createUrl('account/props');?>">道具</a></li>
            <li><a href="<?php echo $this->createUrl('account/car');?>">座驾</a></li>
            <li><a href="<?php echo $this->createUrl('account/moon');?>">月卡</a></li>
            <li><a href="<?php echo $this->createUrl('account/vip');?>">vip</a></li>
            <!--<li><a href="<?php echo $this->createUrl('account/guard');?>">家族守护</a></li>-->
            <li class="menuvisted"><a href="<?php echo $this->createUrl('account/number');?>">靓号</a></li>
        </ul><!-- .main-menu -->
		<div id="MainCon">
		<div class="cooper-list onhide relative" style="display: block;">
           
              <div id="Wordbox" class="wordbox">请输入靓号寄语 <input type="text">
              <a class="buy-btn mt20" title="确认" href="#">确认</a> <a class="buy-btn ml45 mt20" style="background:#86b213;" title="取消" href="#">取消</a>
              </div>
              
              <?php if(!$userNumbers):?>
        		   您还没有靓号<!-- ，赶快去<a  target="_blank" href="<?php $this->getTargetHref($this->createUrl('shop/number'));?>" class="undo">商城</a>看看 -->。
    		  <?php else:?>
              <table width="800" border="1" bordercolor="#DDDDDD">
                  <tbody><tr bgcolor="#F5F5F5" class="biaot">
                    <td width="130" height="40">获得时间</td>
                    <td width="100" height="40">我的靓号</td>
                    <td width="120" height="40">获得方式</td>
                    <td width="350" height="40">寄语文字</td>
                    <td width="100" height="40">状态</td>
                    <td width="100" height="40">管理</td>
                  </tr>
                  <?php
						foreach($userNumbers as $k=>$v):
						if($lastChangeTime){
							if($v['last_recharge_time']==0){
								$tips = '您的靓号永久有效';
							}elseif($lastChangeTime >= $v['last_recharge_time']){
								$days = ceil (($lastChangeTime - $v['last_recharge_time']) / (3600*24));
								$diffDays = 60 - $days;
								if($diffDays > 0){
									$tips = "最近{$days}天没有充值，根据系统规则将在{$diffDays}天后自动收回靓号";
								}else{
									$tips = "您超过60天没有充值过，根据系统规则将自动收回靓号";
								}
							}else{
								$tips = "您超过60天没有充值过，根据系统规则将自动收回靓号";
							}
						}else{
							$lastChangeTime = time();
							if($v['last_recharge_time']==0){
								$tips = '您的靓号永久有效';
							}elseif($lastChangeTime < $v['last_recharge_time']){
								$days = ceil (($v['last_recharge_time'] - $lastChangeTime ) / (3600*24));
								$tips = "根据系统规则将在{$days}天过期，过期后自动收回靓号";
							}else{
								$tips = '您靓号已过期，根据系统规则将自动收回靓号';
							}
						}
						
						 $click = '';
                    	 if(!$v['status']):
                    	  		$click = "<a class='buy-btn ml5' title='修改' href='javascript:void(0)' onclick=showModifyDesc('".$v['number']."')>修改</a>";
                    	 else:
                    	  		$click = '';
                    	 endif;
				  ?>
                  <tr>
                    <td height="42"><?php echo $v['time_desc']?></td>
                    <td height="42"><?php echo $v['number']?></td>
                    <td height="42"><?php echo $v['type_desc']?></td>
                    <td height="42"><?php echo $v['short_desc']?><?php echo $click?></td>
                    <td height="42"><p class="tc"><em><a class="pink">
                    	<?php if($userProps && $v['number'] == $userProps['number']):?>
                    	显示中
                    	<?php else:?>
                    	隐藏
                    	<?php endif;?>
                    	</a></em>
                    	<span class="tipcon"><?php echo $tips;?></span></p>
                    </td>
                    <td height="42">
                    <?php if(!$v['status']):?>
                    	<?php if($userProps && $v['number'] == $userProps['number']):?>
                    	<a class="buy-btn ml5 tc" href="javascript:void(0)" onclick="unUseNumber('<?php echo  $v['number'];?>')"><em>隐藏此号</em></a>
                    	<?php else:?>
                    	<a class="buy-btn ml5 tc" href="javascript:void(0)" onclick="useNumber('<?php echo  $v['number'];?>')"><em>显示此号</em></a>
                    	<?php endif;?>
				  	<?php else:?>
                    	<?php echo $v['status_desc']?>
                    	<a class="buy-btn ml5" title="删除靓号" href="javascript:void(0)" onclick="recyleNumber('<?php echo  $v['number'];?>')">确定</a>
                    <?php endif;?>
                    </td>
                  </tr>
                  <?php endforeach;?>
                 
              </tbody>
              </table>
              <p>
              	说明：<br/>
              	寄语文字限制，七和六位靓号限7字，四位靓号最长不超过12字。<br/>
              	为使靓号资源充分使用，以下情况官方有权自动收回靓号：<br/>
				&nbsp; 1.普通用户超过2个月无充值记录。<br/>
				&nbsp; 2.主播超过2个月无开播记录。<br/>
				</p>
              <?php endif;?>
           </div>
	</div>
		
	</div><!--#MainCon-->
</div><!-- .main -->        
</div><!-- .w1000 -->
<div id="GivNumb" class="popbox">
    <div class="poph">
        <span>修改靓号寄语</span>
        <a title="关闭" class="closed" onclick="$.mask.hide('GivNumb');"></a>
    </div>
    <div class="popcon">
        <div class="bgset-bd">
            <ul class="paysong">                
                <li><p>请输入靓号寄语</p></li>
                <li><input class="intext" type="text" id="short_desc"/></li>                
                <li><input class="shiftbtn" type="button" value="确&nbsp;&nbsp;定">
                
                </li>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">

	function showModifyDesc(number){
		$.mask.show('GivNumb');
		$('#GivNumb input').eq(1).unbind('click');
		$('#GivNumb input').eq(1).bind('click',function(){
			modifyDesc(number);
		});
	}

	function modifyDesc(number){
		var short_desc = $('#short_desc').val();

		if(!$.isInt(number)){
			alert('靓号必须是数字');
			return false;
		}
		if($.isEmpty(short_desc)){
			alert('请输入靓号寄语');
			return false;
		}
		var len = short_desc.length;
		var nLen = number.length;
		if(nLen == 4 && len > 12){
			alert('4位靓号长度不超过12个字');
			return false;
		}

		if((nLen == 6 || nLen == 7) && len > 7){
			alert('6、7位靓号长度不超过7个字');
			return false;
		}

		$.ajax({
			type : 'POST',
			url : 'index.php?r=account/modifyDesc',
			data : {number:number,short_desc:short_desc},
			dataType:"json",
			success:function(response){
				if(response.flag == 1){
					alert('修改寄语成功');
					location.reload();
				}else{
					alert(response.message);
				}
				
			}
		});
		
	}

	function useNumber(number){
		if(!$.isInt(number)){
			alert('靓号必须是数字');
			return false;
		}

		$.ajax({
			type : 'POST',
			url : 'index.php?r=account/useNumber',
			data : {number:number},
			dataType:"json",
			success:function(response){
				if(response.flag == 1){
					alert('切换靓号成功');
					location.reload();
				}else{
					alert(response.message);
				}
				
			}
		});
	}

	function unUseNumber(number){
		if(!$.isInt(number)){
			alert('靓号必须是数字');
			return false;
		}

		$.ajax({
			type : 'POST',
			url : 'index.php?r=account/unUseNumber',
			data : {number:number},
			dataType:"json",
			success:function(response){
				if(response.flag == 1){
					alert('隐藏靓号成功');
					location.reload();
				}else{
					alert(response.message);
				}
				
			}
		});
	}

	function recyleNumber(number){
		if(!$.isInt(number)){
			alert('靓号必须是数字');
			return false;
		}

		$.ajax({
			type : 'POST',
			url : 'index.php?r=account/recycleNumber',
			data : {number:number},
			dataType:"json",
			success:function(response){
				if(response.flag == 1){
					alert('删除回收靓号成功');
					location.reload();
				}else{
					alert(response.message);
				}
				
			}
		});
	}

	$('.tc').hover(function(){
	    $(this).find('.tipcon').css('display','block');
	},function(){
	    $(this).find('.tipcon').css('display','none');
	});	
		
</script>