<?php $tagClass=$this->getTagClass();?>
			<ul class="batchlist listl">
				<?php 
					$leftDynamic=array_slice($dynamic, 0,4);
					foreach ($leftDynamic as $leftkey=>$leftRow):
				?>
					<li>
						<a class="batchpic" href="<?php $this->getTargetHref("/".$leftRow['uid'],true,false)?>" target="<?php echo $this->target?>">
							<img src="<?php echo $leftRow['dynamic_small'];?>" >
							<em class="batchmask DD_belapng"></em>
						</a>
						<div class="batchFram">
							<dl>
								<dt>
									<img src="<?php echo $leftRow['dynamic_big'];?>" >
								</dt>
								<dd class="name"><em class="lvlo lvlo-<?php echo empty($leftRow['dotey_rank'])?0:intval($leftRow['dotey_rank']);?>"></em><span><?php echo $leftRow['nickname'];?></span></dd>
								<?php if(intval($leftRow['status'])==1 && intval($leftRow['live_time'])>0):?>
									<dd class="time"><span>开播：<?php echo $leftRow['live_desc'][0];?></span><span class="pink"><?php echo $leftRow['user_total'];?>人在看</span></dd>
								<?php endif;?>
								<dd class="trait">
									<?php foreach ($leftRow['tags'] as $tagRow):?>
	            					<a href="<?php echo 'index.php?'.http_build_query(array('r'=>'index/categoryv5','type'=>'tag','id'=>$tagRow['tag_id']));?>" target="<?php echo $this->target?>">
	            					<em class="<?php echo isset($tagClass[$tagRow['tag_id']])?$tagClass[$tagRow['tag_id']]:$tagClass[5];?>"><?php echo $tagRow['tag_name']; ?></em>
	            					</a>
									<?php endforeach;?>
								</dd>
							</dl>
						</div><!--.batchFram-->
					</li>
				
				<?php
					endforeach;
				?>
				</ul>
				<ul class="batchlist listr">
					<?php $fifthDotey=$dynamic[4];?>
					<li class="batch-big">
						<a class="batchpic" href="<?php $this->getTargetHref("/".$fifthDotey['uid'],true,false)?>" target="<?php echo $this->target?>">
							<img src="<?php echo $fifthDotey['dynamic_middle'];?>" >
							<em class="batchmask DD_belapng"></em>
						</a>
						<div class="batchFram">
							<dl>
								<dt>
									<img src="<?php echo $fifthDotey['dynamic_big'];?>" >
								</dt>
								<dd class="name"><em class="lvlo lvlo-<?php echo empty($fifthDotey['dotey_rank'])?0:intval($fifthDotey['dotey_rank']);?>"></em><span><?php echo $fifthDotey['nickname'];?></span></dd>
								<?php if(intval($fifthDotey['status'])==1 && intval($fifthDotey['live_time'])>0):?>
									<dd class="time"><span>开播：<?php echo $fifthDotey['live_desc'][0];?></span><span class="pink"><?php echo $fifthDotey['user_total'];?>人在看</span></dd>
								<?php endif;?>
								<dd class="trait">
									<?php foreach ($fifthDotey['tags'] as $tagRow):?>
	            					<a href="<?php echo 'index.php?'.http_build_query(array('r'=>'index/categoryv5','type'=>'tag','id'=>$tagRow['tag_id']));?>" target="<?php echo $this->target?>">
	            					<em class="<?php echo isset($tagClass[$tagRow['tag_id']])?$tagClass[$tagRow['tag_id']]:$tagClass[5];?>"><?php echo $tagRow['tag_name']; ?></em>
	            					</a>
									<?php endforeach;?>
								</dd>
							</dl>
						</div><!--.batchFram-->
					</li>
					<?php $sixthDotey=$dynamic[5];?>
					<li>
						<a class="batchpic" href="<?php $this->getTargetHref("/".$sixthDotey['uid'],true,false)?>" target="<?php echo $this->target?>">
							<img src="<?php echo $sixthDotey['dynamic_small'];?>" >
							<em class="batchmask DD_belapng"></em>
						</a>
						<div class="batchFram">
							<dl>
								<dt>
									<img src="<?php echo $sixthDotey['dynamic_big'];?>" ">
								</dt>
								<dd class="name"><em class="lvlo lvlo-<?php echo empty($sixthDotey['dotey_rank'])?0:intval($sixthDotey['dotey_rank']);?>"></em><span><?php echo $sixthDotey['nickname'];?></span></dd>
								<?php if(intval($sixthDotey['status'])==1 && intval($sixthDotey['live_time'])>0):?>
									<dd class="time"><span>开播：<?php echo $sixthDotey['live_desc'][0];?></span><span class="pink"><?php echo $sixthDotey['user_total'];?>人在看</span></dd>
								<?php endif;?>
								<dd class="trait">
									<?php foreach ($sixthDotey['tags'] as $tagRow):?>
	            					<a href="<?php echo 'index.php?'.http_build_query(array('r'=>'index/categoryv5','type'=>'tag','id'=>$tagRow['tag_id']));?>" target="<?php echo $this->target?>">
	            					<em class="<?php echo isset($tagClass[$tagRow['tag_id']])?$tagClass[$tagRow['tag_id']]:$tagClass[5];?>"><?php echo $tagRow['tag_name']; ?></em>
	            					</a>
									<?php endforeach;?>
								</dd>
							</dl>
						</div><!--.batchFram-->
					</li>
				
					<li id="refreshBtn">
						<a  title="换一批" href="javascript:void(0);"><img src="<?php echo $this->pipiFrontPath?>/fontimg/index/refreshBtn-bg.jpg"></a>
					</li>
				</ul>