                        <dl>
						<?php 
						$attentionType = isset($attentionType) ? $attentionType : 'common';
						$i = 1;
						foreach($rank as $key=>$_rank):
							$attentTionClass = $_rank['is_attention'] ? 'cancelatt' : '';
							$attentIionText = $isAttention ? '取消关注' : '关注';
							$jsMethod = $_rank['is_attention'] ? 'cacnelAttentionUser' : 'attentionUser';
							$archivesHref = '/'.$_rank['d_uid'];
						?>
						<?php if(1==$i):?>
                            <dt>
                                <a class="headpic" href="<?php $this->getTargetHref($archivesHref,true,false)?>" target="<?php echo $this->target?>">
                                <?php if($isLazyLoad):?>	
                                	<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png" data-original="<?php echo $_rank['d_avatar'];?>">
	            				<?php else:?>
	            					<img src="<?php echo $_rank['d_avatar'];?>" >
	            				<?php endif;?>	                                	
                                </a>
                                <p class="name"><span><a href="<?php $this->getTargetHref($archivesHref,true,false)?>" title="<?php echo $_rank['d_nickname'];?>"><?php echo $_rank['d_nickname']?></a>
                                </span><em class="lvlo lvlo-<?php echo $_rank['d_rank']?>"></em></p>
                                <p class="actext">护花使者：<span class="accon" title="<?php echo $_rank['nickname'];?>"><?php echo $_rank['nickname']?></span></p>
                                <a class="attent <?php echo $attentTionClass?>" href="javascript:void(0);" title="关注" onclick="$.User.<?php echo $jsMethod?>('<?php echo $_rank['d_uid']?>',this,'single');"></a>
                            </dt>
                         <?php elseif ($i>1 && $i<4):?>
                            <dd>
                                <em class="numcon"><?php echo $i;?></em>
                                <a class="headpic" href="<?php $this->getTargetHref($archivesHref,true,false)?>" target="<?php echo $this->target?>">
                                <?php if($isLazyLoad):?>	
                                	<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png" data-original="<?php echo $_rank['d_avatar'];?>">
	            				<?php else:?>
	            					<img src="<?php echo $_rank['d_avatar'];?>" >
	            				<?php endif;?>	
                                </a>
                                <p class="name"><span><a href="<?php $this->getTargetHref($archivesHref,true,false)?>" title="<?php echo $_rank['d_nickname'];?>"><?php echo $_rank['d_nickname']?></a>
                                </span><em class="lvlo lvlo-<?php echo $_rank['d_rank']?>"></em></p>
                                <p class="actext"><span class="accon" title="<?php echo $_rank['nickname'];?>"><?php echo $_rank['nickname']?></span></p>
                                <a class="attent <?php echo $attentTionClass?>" href="javascript:void(0);" title="关注" onclick="$.User.<?php echo $jsMethod?>('<?php echo $_rank['d_uid']?>',this,'single');"></a>
                            </dd>
                          <?php elseif($i>3):?>
                            <dd>
                                <em class="numcon blue"><?php echo $i;?></em>
                                <a class="headpic" href="<?php $this->getTargetHref($archivesHref,true,false)?>" target="<?php echo $this->target?>">
                                <?php if($isLazyLoad):?>	
                                	<img src="<?php echo Yii::app()->params->images_server['url'];?>/default/avatar/avatar_default_small.png" data-original="<?php echo $_rank['d_avatar'];?>">
	            				<?php else:?>
	            					<img src="<?php echo $_rank['d_avatar'];?>" >
	            				<?php endif;?>	
                                </a>
                                <p class="name"><span><a href="<?php $this->getTargetHref($archivesHref,true,false)?>" title="<?php echo $_rank['d_nickname'];?>"><?php echo $_rank['d_nickname']?></a>
                                </span><em class="lvlo lvlo-<?php echo $_rank['d_rank']?>"></em></p>
                                <p class="actext"><span class="accon" title="<?php echo $_rank['nickname'];?>"><?php echo $_rank['nickname']?></span></p>
                                <a class="attent <?php echo $attentTionClass?>" href="javascript:void(0);" title="关注" onclick="$.User.<?php echo $jsMethod?>('<?php echo $_rank['d_uid']?>',this,'single');"></a>
                            </dd>
                            <?php endif;?>

                        <?php
                        	$i++;
                        	if($i>8)
                        		break;
                         endforeach;
                         ?>
                        </dl>

