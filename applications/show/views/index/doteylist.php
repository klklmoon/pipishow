<?php $tagClass=$this->getTagClass();
$attentionType = isset($attentionType) ? $attentionType : 'common';
?>
				<?php 
            	if ($living) :
					 	foreach ($living as $_live) :
						 	$isAttention = isset($_live['is_attention']) ? $_live['is_attention'] : 0;
						 	$attentTionClass = $isAttention ? 'cancelatt' : '';
						 	$attentIionText = $isAttention ? '取消关注' : '关注';
						 	$jsMethod = $isAttention ? 'cacnelAttentionUser' : 'attentionUser';
            				$archivesHref = '/' . $_live['uid'];
            	?>
            		<li>
            			<div class="actorbox">
	            			<div class="actor-hd">
	            				<?php if($isLazyLoad):?>
	            					<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/dotey/dotey_display_default_small.png" data-original="<?php echo $_live['display_small'];?>">
	            				<?php else:?>
	            					<img src="<?php echo $_live['display_small'];?>" >
	            				<?php endif;?>
	            				<em class="pysate playsate">直播</em>
	            				<?php if($_live['today_recommand']==true):?>
	            				<em class="todayRec"></em>
	            				<?php endif;?>
	            				<em class="viewnum"><?php echo number_format($_live['user_total']);?></em>
	            			</div>
	            			<div class="actor-bd">
	            				<p class="actor-name"><em class="lvlo lvlo-<?php echo empty($_live['dotey_rank'])?0:intval($_live['dotey_rank']);?>"></em><span class="name"><?php echo $_live['nickname']?></span></p>
	            				<p class="actor-sign"><?php echo $_live['sub_title']?></p>
	            			</div>
            			</div><!--.actorbox-->
            			
						<dl class="actor-big">
            				<dt>
            					<a  class="actor-hd" href="<?php $this->getTargetHref($archivesHref,true,false)?>" target="<?php echo $this->target?>">
            							            				
			            			<em class="pysate playsate">直播</em>
			            			<?php if($_live['today_recommand']==true):?>
			            				<em class="todayRec"></em>
			            			<?php endif;?>
			            			<em class="viewnum"><?php echo number_format($_live['user_total']);?></em>
			            			<em class="playmask"></em>
			            		</a>
			            		<div class="actor-bd">
			            			<p class="actor-name">
			            				<em class="lvlo lvlo-<?php echo empty($_live['dotey_rank'])?0:intval($_live['dotey_rank']);?>"></em>
			            				<a target="<?php echo $this->target?>" class="name" href="<?php $this->getTargetHref($archivesHref,true,false)?>" title="<?php echo $_live['nickname'];?>"><?php echo $_live['nickname']?></a>
			            			</p>
			            			<p class="actor-sign"><?php echo $_live['sub_title']?></p>
			            			<a onclick="$.User.<?php echo $jsMethod?>('<?php echo $_live['uid']?>',this,'<?php echo $attentionType?>');"
			            			 class="attent <?php echo $attentTionClass?>"
			            			 title="<?php echo $attentIionText?>" href="javascript:void(0);" > 
			            			 <span class="attent-text"><?php echo $attentIionText?></span>
			            			 </a>
			            		</div>
            				</dt>
            				<dd class="kide">
            				<?php foreach ($_live['tags'] as $tagRow):?>
            					<a href="<?php echo 'index.php?'.http_build_query(array('r'=>'index/categoryv5','type'=>'tag','id'=>$tagRow['tag_id']));?>" target="<?php echo $this->target?>">
            					<em class="<?php echo isset($tagClass[$tagRow['tag_id']])?$tagClass[$tagRow['tag_id']]:$tagClass[5];?>"><?php echo $tagRow['tag_name']; ?></em></a>
							<?php endforeach;?>
            				</dd>
                            <dd class="hwhite"></dd>
            			</dl><!--.actor-big-->
            		</li>
				<?php 
						endforeach;
				 	endif;
				 ?>
				<?php if ($wait) :
						foreach ($wait as $_live) :
						$isAttention = isset($_live['is_attention']) ? $_live['is_attention'] : 0;
						$attentTionClass = $isAttention ? 'cancelatt' : '';
						$attentIionText = $isAttention ? '取消关注' : '关注';
						$jsMethod = $isAttention ? 'cacnelAttentionUser' : 'attentionUser';
						$archivesHref = '/' . $_live['uid'];
				?>
            		<li>
            			<div class="actorbox">
	            			<div class="actor-hd">
	            				<?php if($isLazyLoad):?>	
	            					<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/dotey/dotey_display_default_small.png" data-original="<?php echo $_live['display_small'];?>">
	            				<?php else:?>
	            					<img src="<?php echo $_live['display_small'];?>" >
	            				<?php endif;?>		            					
	            				<p class="playtime"><?php echo $_live['start_desc'][0];?> 开播</p>
	            				<em class="pysate readysate">预告</em>
	            			</div>
	            			<div class="actor-bd">
	            				<p class="actor-name"><em class="lvlo lvlo-<?php echo empty($_live['dotey_rank'])?0:intval($_live['dotey_rank']);?>"></em><span class="name"><?php echo $_live['nickname'];?></span></p>
	            				<p class="actor-sign"><?php echo $_live['sub_title'];?></p>
	            			</div>
            			</div><!--.actorbox-->
						<dl class="actor-big">
            				<dt>
            					<a  class="actor-hd" href="<?php $this->getTargetHref($archivesHref,true,false)?>" target="<?php echo $this->target?>">
			            				
			            			<p class="playtime"><?php echo $_live['start_desc'][0];?> 开播</p>
			            			<em class="pysate readysate">预告</em>
			            			<em class="playmask"></em>
			            		</a>
			            		<div class="actor-bd">
			            			<p class="actor-name">
			            				<em class="lvlo lvlo-<?php echo empty($_live['dotey_rank'])?0:intval($_live['dotey_rank']);?>"></em>
			            				<a target="<?php echo $this->target?>" class="name" href="<?php $this->getTargetHref($archivesHref,true,false)?>" title="<?php echo $_live['nickname'];?>"><?php echo $_live['nickname'];?></a>
			            			</p>
			            			<p class="actor-sign"><?php echo $_live['sub_title'];?></p>
			            			<a onclick="$.User.<?php echo $jsMethod?>('<?php echo $_live['uid']?>',this,'<?php echo $attentionType?>');"
			            			 class="attent <?php echo $attentTionClass?>"
			            			 title="<?php echo $attentIionText?>" href="javascript:void(0);" > 
			            			 <span class="attent-text"><?php echo $attentIionText?></span>
			            			 </a>
			            		</div>
            				</dt>
            				<dd class="kide">
            				<?php foreach ($_live['tags'] as $tagRow):?>
            					<a href="<?php echo 'index.php?'.http_build_query(array('r'=>'index/categoryv5','type'=>'tag','id'=>$tagRow['tag_id']));?>" target="<?php echo $this->target?>">
            					<em class="<?php echo isset($tagClass[$tagRow['tag_id']])?$tagClass[$tagRow['tag_id']]:$tagClass[5];?>"><?php echo $tagRow['tag_name']; ?></em></a>
							<?php endforeach;?>
            				</dd>
            				<dd class="hwhite"></dd>
            			</dl><!--.actor-big-->
            		</li>			
				
				<?php 
						endforeach;
					endif;
				?>
