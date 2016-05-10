                        <dl>
                        <?php 
							foreach($rank as $key=>$_rank):
						?>
						<?php if(1==($key+1)):?>
                            <dt>
                                <a class="headpic" href="#">
                                <?php if($isLazyLoad):?>	
                                	<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png" data-original="<?php echo $_rank['avatar'];?>">
	            				<?php else:?>
	            					<img src="<?php echo $_rank['avatar'];?>" >
	            				<?php endif;?>	                                	
                                </a>
                                <p class="name"><span><a href="#" title="<?php echo $_rank['nickname'];?>"><?php echo $_rank['nickname']?></a></span><em class="lvlr lvlr-<?php echo $_rank['rank']?>"></em></p>
                                <?php if(isset($_rank['number'])):?>
                                <p class="actext"><span class="fivenumb"><em>靓</em> <?php echo $_rank['number']?></span></p>
                                <?php endif;?>
                            </dt>
                        <?php elseif (($key+1)>1 && ($key+1)<4):?>    
                            <dd>
                                <em class="numcon"><?php echo ($key+1);?></em>
                                <a class="headpic" href="#">
	                               	<?php if($isLazyLoad):?>	
	                                	<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png" data-original="<?php echo $_rank['avatar'];?>">
		            				<?php else:?>
		            					<img src="<?php echo $_rank['avatar'];?>" >
		            				<?php endif;?>	
                                </a>
                                <p class="name"><span><a href="#" title="<?php echo $_rank['nickname'];?>"><?php echo $_rank['nickname']?></a></span><em class="lvlr lvlr-<?php echo $_rank['rank']?>"></em></p>
                                <?php if(isset($_rank['number'])):?>
                                <p class="actext"><span class="fivenumb"><em>靓</em> <?php echo $_rank['number']?></span></p>
                                <?php endif;?>
                            </dd>

                            <?php elseif(($key+1)>3):?>
                            <dd>
                                <em class="numcon blue"><?php echo ($key+1);?></em>
                                <a class="headpic" href="#">
	                               	<?php if($isLazyLoad):?>	
	                                	<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png" data-original="<?php echo $_rank['avatar'];?>">
		            				<?php else:?>
		            					<img src="<?php echo $_rank['avatar'];?>" >
		            				<?php endif;?>	
                                </a>
                                <p class="name"><span><a href="#" title="<?php echo $_rank['nickname'];?>"><?php echo $_rank['nickname']?></a></span><em class="lvlr lvlr-<?php echo $_rank['rank']?>"></em></p>
                                <?php if(isset($_rank['number'])):?>
                                <p class="actext"><span class="fivenumb"><em>靓</em> <?php echo $_rank['number']?></span></p>
                                <?php endif;?>
                            </dd>
							<?php endif;?>
                        <?php
                        	if($key==7)
                        		break;
                         endforeach;
                         ?>
                        </dl>
              