<?php echo $this->renderPartial('home_top', compact(array_keys(get_defined_vars())));?>
<div class="w1000 memberMesg-con clearfix">
    <div class="fleft memberMesgBd-l">
        <div class="control-box familySubject">
            <h4 class="clearfix">
                <i class="banericon"></i>
                <span class="fleft pink">
                	<?php echo PipiCommon::truncate_utf8_string($thread['title'], 35);?>
                	<?php if($thread['top']){?><img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/topicon.gif" /><?php }?>
                    <?php if($thread['flag_hot']){?><img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/hoticon.gif" /><?php }?>
                    <?php if($thread['flag_image']){?><img src="<?php echo $this->pipiFrontPath;?>/fontimg/family/picicon.gif" /><?php }?>
                </span>
            </h4>
            <div class="famSubj-t">
            	<?php if($admin || $manager){?>
                <a class="gray-btn" href="<?php echo $this->createUrl('family/bbsTop', array('family_id' => $family['id'], 'ids' => $thread['thread_id']))?>">置顶/取消</a>
                <a class="gray-btn" href="<?php echo $this->createUrl('family/editPost', array('family_id' => $family['id'], 'tid' => $thread['thread_id'], 'pid' => $posts['list'][0]['post_id']))?>">编辑帖子</a>
                <a class="gray-btn" href="<?php echo $this->createUrl('family/bbsDelete', array('family_id' => $family['id'], 'ids' => $thread['thread_id']))?>">删除帖子</a>
                <?php }?>
                <?php if(Yii::app()->user->id > 0){?>
                <input class="fright shiftbtn" type="button" value="回&nbsp;&nbsp;应" onclick="window.location.href='#reply';">
                <!--灰色按钮-->
                <!--<input class="fright gry-shiftbtn shiftbtn" type="button" value="发&nbsp;&nbsp;布">-->
                <?php }?>
            </div><!--.famSubj-t-->
            <ul class="famSubj-list">
            	<?php foreach($posts['list'] as $p){?>
                <li>
                    <div class="small-head"><img src="<?php echo $p['pic'];?>"></div>
                    <dl class="famSubj-con">
                        <dt><span><?php echo $p['nickname'];?></span><em class="lvlr lvlr-<?php echo $p['rank']?>"></em><?php if($p['medal']){?><img src="<?php echo $p['medal'];?>" /><?php }?></dt>
                        <?php if(isset($p['reply'])){?>
                        <dt><strong>回应 <?php echo $p['reply']['floor'];?># <?php echo $p['reply']['reply_post_nickname'];?>:</strong><?php echo nl2br(PipiCommon::truncate_utf8_string($p['reply']['content'], 30));?></dt>
                        <?php }?>
                        <dd class="content" style="clear:both;">
                            <p><?php echo nl2br($p['content']);?></p>
                            <?php if(!empty($p['op_uid'])) echo "<p style='color:#CCC'>该贴于 ".date('Y-m-d H:i', $p['update_time'])." 被 ".$p['op_user']." 编辑过</p>"?>
                        </dd>
                        <dd class="icon disicon">
                            <em class="time"><?php echo date('Y-m-d H:i', $p['create_time']);?></em>
                            <em style="float:right"><?php echo $p['floor'];?> 楼</em>
                            <?php if(Yii::app()->user->id > 0){?>
                            <em class="disnum"><a class="reply" href="javascript:void(0);" <?php echo ($p['floor'] > 1 ? 'data-pid="'.$p['post_id'].'" data-nk="'.$p['nickname'].'"' : '');?>>回复</a></em>
                            <em class="report"><a href="javascript:void(0);" onclick="report(<?php echo $p['post_id'];?>);">举报</a></em>
                            <?php if(($admin || $manager) && $p['floor'] > 1){?>
                            	<em><a href="<?php echo $this->createUrl('family/editPost', array('family_id' => $family['id'], 'tid' => $thread['thread_id'], 'pid' => $p['post_id']))?>">编辑</a></em>
                            	<em><a href="<?php echo $this->createUrl('family/deletePost', array('family_id' => $family['id'], 'tid' => $thread['thread_id'], 'pid' => $p['post_id']))?>" onclick="return confirm('确定要删除么？');">删除</a></em>
                            <?php }?>
                            <?php }?>
                        </dd>
                    </dl>
                </li>
                <?php }?>
            </ul>
            <?php $this->widget('lib.widgets.family.LinkPager', array('pages'=>$posts['pages']));?>
            <?php if(Yii::app()->user->id > 0){?>
            <div class="note-box">
            	<form action="<?php echo $this->createUrl('family/sendPost', array('family_id' => $family['id']));?>" method="post">
            	<input type="hidden" name="thread_id" value="<?php echo $thread['thread_id'];?>" />
            	<input type="hidden" name="reply_post_id" id="reply_post_id" value="0" />
            	<a id="reply" />
                <p class="title">回复<em id="reply_post_nk">楼主</em></p>
                <textarea class="familyNotice-text" name="content"></textarea>
                <div class="note-btn clearfix">
                	<?php if($needValidate){?>
                    <span class="noteTest">
                    <?php 
		            $this->widget('CCaptcha', array(
						'imageOptions'=>array('width'=>100,'height'=>40,'style'=>'margin-bottom:-17px'),
						'clickableImage'=>true,
						'showRefreshButton'=>true,
						'buttonLabel'=> '看不清，换一个',
						'buttonOptions'=> array('style'=>'color:#B2B2B2; display:inline-block; margin-left:5px;'),
						'captchaAction'=>'family/captcha'
					)); 
		            ?>
                    </span>
                    <span><input class="notext" type="text" name="code"></span>
                    <?php }?>
                    <input class="fright shiftbtn" style="margin-right:2px;float:right;display:inline;" type="submit" value="发&nbsp;&nbsp;布">
                    <!--灰色按钮-->
                    <!--<input class="fright gry-shiftbtn shiftbtn" type="button" value="发&nbsp;&nbsp;布">-->
                </div>
                </form>
            </div><!--.note-box-->
            <?php }?>
        </div>
        <p class="anchor-btm"></p><!--.familySubject-->
    </div>
    <?php echo $this->renderPartial('home_right', compact(array_keys(get_defined_vars())));?>
</div>
<script type="text/javascript">
$(function(){
	$('.reply').click(function(){
		var pid = $(this).attr('data-pid');
		var nickname = $(this).attr('data-nk');
		if(pid){
			$('#reply_post_id').val(pid);
			$('#reply_post_nk').html(nickname);
		}else{
			$('#reply_post_id').val(0);
			$('#reply_post_nk').html('楼主');
		}
		window.location.href='#reply';
	});
});
function report(pid){
	$.ajax({
		url : "index.php?r=family/bbsReport",
		type : "GET",
		data:{'family_id':<?php echo $family['id'];?>, 'post_id':pid},
		dataType : "json",
		success : function(json){
			if(json.status){
				alert('举报成功');
			}else{
				var str = "";
				for(var i in json.message){
					str += json.message[i]+"\n";
				}
				alert(str);
			}
	 	}
	});
}
</script>
<?php $this->widget('lib.widgets.family.EditorWidget', array('name' => 'content')); ?>