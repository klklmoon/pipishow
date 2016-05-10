<?php echo $this->renderPartial('home_top', compact(array_keys(get_defined_vars())));?>
<div class="w1000 memberMesg-con clearfix">
    <div class="fleft memberMesgBd-l">
        <div class="control-box familyHome">
            <h4 class="clearfix">
                <i class="banericon"></i>
                <span class="fleft pink"><?php echo $edit ? '编辑帖子' : '发布新帖';?></span>
            </h4>
        <div class="posting_box clearfix">
        	<form id="myform" action="<?php echo $this->createUrl($edit ? 'family/editPost' : 'family/sendThread', array('family_id' => $family['id']));?>" method="post" onsubmit="return checkSubmit();">
            <?php if($edit){?>
            <input type="hidden" name="tid" value="<?php echo $thread['thread_id'];?>" />
            <input type="hidden" name="pid" value="<?php echo $post['post_id'];?>" />
            <span>标题：<?php echo $thread['title'];?></span>
            <?php }else{?>
            <input class="title" name="title" id="title" type="text" value="填写发帖标题（限35字以内）" style="padding: 0 10px; width:630px; color:gray;">
            <?php }?>
            <textarea name="content" cols="" rows="" id="content"><?php echo $edit ? $post['content'] : '';?></textarea>
            <div class="note-btn clearfix">
            	<?php if(!$edit && $needValidate){?>
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
        </div><!--.posting_box-->
        </div><!--.control-box-->
        <p class="anchor-btm"></p>
    </div>
    <?php echo $this->renderPartial('home_right', compact(array_keys(get_defined_vars())));?>
</div>
<script type="text/javascript">
$(function(){
	$('#title').focus(function(){
		if($(this).val() == this.defaultValue){  
			$(this).val("");           
		} 
	}).blur(function(){
		if ($(this).val() == '') {
			$(this).val(this.defaultValue);
		}
	});
});
function checkSubmit(){
	if($('#title').val() == '填写发帖标题（限35字以内）'){
		alert('请先填写标题');
		return false;
	}else return true;
}
</script>
<?php $this->widget('lib.widgets.family.EditorWidget', array('name' => 'content')); ?>