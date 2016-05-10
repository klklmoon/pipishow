<div class="w1000 mt20 boxshadow clearfix">
	<h1 class="faqhd">家族帮助</h1><!-- .faqlist -->
    <div class="intro">
       <strong>官方客服 </strong>
       <div class="asw">
            <p class="kefu clearfix">
                <em>在线时段10:30-2:00</em>
                <?php foreach($kefu as $key=>$kf){ ?>
                <div>
				<span style="float:none;"><?php echo $key;?>:</span>
				<?php foreach($kf as $k){ ?>
                <a title="<?php echo $k['contact_name'];?>" target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo $k['contact_account']?>&amp;site=qq&amp;menu=yes">
	            	<img border="0" src="http://wpa.qq.com/pa?p=3:<?php echo $k['contact_account']?>:45" alt="<?php echo $k['contact_name']?>" title="<?php echo $k['contact_name']?>" style="vertical-align:middle;">
	            </a>
	            <span><?php echo $k['contact_name'];?></span>
                <?php } ?>
                </div>
				<?php } ?>
            </p>
            <p class="clearfix">
                <input type="button" value="申请成为签约家族" class="fleft cancelbtn" id='apply'>
                <label class="fleft ml10 mt10">*申请签约家族前请先联系家族招募QQ</label>
            </p>
       </div>

       <strong>家族成员权限 </strong>
       <div class="asw">
        <p><img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/inr-tab.jpg" /></p>
       </div>
    
       <strong>家族族徽</strong>
       <div class="asw">
        <p>1、家族成员可以购买族徽，彰显家族身份。</p>            
        <p>2、玩家可同时加入多个家族，但同一时间只可佩戴一枚族徽。</p>            
        <p>3、族徽价格200皮蛋，族员购买后永久有效，退出家族则族徽消失；需要重新购买才可佩戴。</p>
       </div>
       
       <strong>家族等级</strong>
       <div class="asw">
        <p>家族达到指定条件后，可以晋升家族等级，扩大等级权限。</p>
        <p>
        	<table width="600" border="1">
			  <tr height="30">
			    <td>家族等级</td>
			    <td>升级条件</td>
			    <td>等级权限</td>
			  </tr>
			  <tr height="90">
			    <td>Lv1 <br/> <img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/13.png" /></td>
			    <td>家族长达到富豪4，或蓝钻5；<br/>可以创建Lv1等级的家族。</td>
			    <td>可设1名长老<br/>可设3名管理<br/>家族人数上限20人<br/>拥有1级族徽</td>
			  </tr>
			  <tr height="90">
			    <td>Lv2 <br/> <img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/23.png" /></td>
			    <td>家族族徽成员的充值累计达<br/>到1000000皮蛋（佩戴族徽<br/>时的充值算为有效）。</td>
			    <td>可设2名长老<br/>可设3名管理<br/>家族人数上限100人<br/>拥有2级族徽</td>
			  </tr>
			  <tr height="90">
			    <td>Lv3 <br/> <img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/33.png" /></td>
			    <td>家族族徽成员的充值累计达<br/>到8000000皮蛋（佩戴族徽<br/>时的充值算为有效）。</td>
			    <td>可设5名长老<br/>可设15名管理<br/>家族人数上限500人<br/>拥有3级族徽</td>
			  </tr>
			  <tr height="90">
			    <td>Lv4<br/> <img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/43.png" /></td>
			    <td>家族族徽成员的充值累计达<br/>到20000000皮蛋（佩戴族徽<br/>时的充值算为有效）。</td>
			    <td>可设8名长老<br/>可设25名管理<br/>家族人数上限1500人<br/>拥有4级族徽</td>
			  </tr>
			  <tr height="90">
			    <td>Lv5<br/> <img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/53.png" /></td>
			    <td>家族族徽成员的充值累计达<br/>到50000000皮蛋（佩戴族徽<br/>时的充值算为有效）。</td>
			    <td>可设12名长老<br/>可设30名管理<br/>家族人数上限3000人<br/>拥有5级族徽</td>
			  </tr>
			  <tr height="90">
			    <td>Lv6 <br/> <img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/63.png" /></td>
			    <td>家族族徽成员的充值累计达<br/>到100000000皮蛋（佩戴族徽<br/>时的充值算为有效）。</td>
			    <td>可设15名长老<br/>可设50名管理<br/>家族成员数不限<br/>拥有6级族徽</td>
			  </tr>
			</table>
		</p>
       </div>
    </div>

</div>
<div id="SignApply" class="popbox">
    <div class="poph">
        <span>签约家族申请</span>
        <a title="关闭" class="closed" onClick="$.mask.hide('SignApply');"></a>
    </div>
    <div class="popcon">
        <ul class="paysong pybadge">
            <li>
                <p class="pyfor" id="message"></p>
            </li>
            <li style="text-align:center;"><input id="o_btn" class="shiftbtn" type="button" value="确&nbsp;定" onClick="$.mask.hide('SignApply');" /></li>
        </ul>
    </div>
</div>
<script type="text/javascript">
$(function(){
	$('#apply').click(function(){
		$.ajax({
			url : "index.php?r=family/signApply",
			type : "GET",
			dataType : "json",
			success : function(json){
				var str = "";
				for(var i in json.message){
					str += json.message[i]+"\n";
				}
				$("#message").html(str);
				$.mask.show('SignApply');
		 	}
		});
	});
});
</script>