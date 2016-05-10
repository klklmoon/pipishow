				<div class="fright mflash-hd">
					<ul>
                	<?php
																	$i = 0;
																	foreach ($siteStars as $siteStar) :
																		if ($i >= 3) break;
																		?>
                    	<li><img
							src="<?php echo $siteStar['small_avatar']?>"></li>
                         <?php
																		$i++;
																	endforeach
																	;
																	
																	?>
                    </ul>
					<span id="starts_next" class="end next"><img
						src="<?php echo $this->pipiFrontPath?>/fontimg/common/changebtn.png"></span>
				</div>
				<div class="fleft mflash-bd">
					<div class="mflash-box">
						<ul>
                    	<?php
							$i = 0;
							foreach ($siteStars as $siteStar) :
								if ($i >= 3) break;
								$sliveText = $siteStar['status'] == 1 ? '直播中' : '待直播';
								$sliveClass = $siteStar['status'] == 1 ? 'playing' : 'readying';
								$archivesHref = '/' . $siteStar['uid'];
						?>
                    		<li><a
								href="<?php echo $this->getTargetHref($archivesHref,true,false)?>"
								title="<?php echo $siteStar['nickname']?>"
								target="<?php echo $this->target?>"> <img
									src="<?php echo $siteStar['display_big']?>"></a>
								<p class="<?php echo $sliveClass?>"><em><?php echo $sliveText?></em></p></li>
                    	  <?php
									$i++;
								endforeach;
							?>
                    	</ul>
					</div>
				</div>

							
<script type="text/javascript">
$("#Mflash").slide({mainCell:".mflash-bd ul",titCell:'.mflash-hd ul li',autoPlay:true,delayTime:0,triggerTime:0 });
$("#starts_next").unbind( "click" );
$("#starts_next").bind("click",function(){
	$.ajax({
		type:"GET",
		url:"index.php?r=index/UpdateSiteStars",
		data:{target:hrefTarget},
		dataType:"html",
		success:function(starts_html){
			$('#Mflash').html(starts_html);
		}
	});
});
</script>			
