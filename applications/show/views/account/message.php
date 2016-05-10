<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>

	<div class="main fright">
		<ul class="main-menu clearfix" id="MianList">
			<li <?php echo $this->userService->array_get($this->viewer['curSelect'],'system')?>><a href="<?php echo $this->createUrl('account/message',array('type'=>'system'));?>">系统通知<em><?php echo $system_unread > 0 ? $system_unread : '';?></em></a></li>
			<?php if(FamilyService::familyEnable()){?>
			<li <?php echo $this->userService->array_get($this->viewer['curSelect'],'family')?>><a href="<?php echo $this->createUrl('account/message',array('type'=>'family'));?>">家族消息<em><?php echo $family_unread > 0 ? $family_unread : '';?></em></a></li>
            <?php }?>
            <li <?php echo $this->userService->array_get($this->viewer['curSelect'],'site')?>><a href="<?php echo $this->createUrl('account/message',array('type'=>'site'));?>">全站消息<em><?php echo $site_unread > 0 ? $site_unread : '';?></em></a></li>
		</ul>
		
		<div id="MainCon">
			<?php if($msgList):?>
			<div class="cooper-list">
				<div class="msgcon grey">消息内容</div> 
				<div class="msgfrom grey">来自</div>  
				<div class="msgtime grey">时间</div> 
				<div class="msgopera grey">操作</div>

				<ul class="msgbox">
					<?php foreach($msgList as $k=>$v) : ?>
					<li id="message_<?php echo $v['message_id'];?>" style="<?php if($v['is_read']) : echo "color:#999;"; endif; ?> <?php if($v['title']) :  echo ' margin-top:20px;'; endif; ?>" <?php if($v['is_read']==0){ echo 'onclick="markMessage('.$v['message_id'].')"';}?> >
						<?php if($v['title']) : ?>
						<div>
							<strong><?php echo $v['title'];?></strong>
						</div>
						<?php endif;?>
						<div class="msgcon mt5"><?php echo $v['content'];?>&nbsp;&nbsp;<?php echo isset($v['extra']['href']) && $v['extra']['href'] ? '<a href="'.$v['extra']['href'].'" target="'.$this->target.'" class="pink">去看看</a>' : ''?></div> 
						<div class="msgfrom pink mt5"><?php echo isset($v['extra']['from']) ? $v['extra']['from'] : '系统发送'?></div>  
						<div class="msgtime mt5"><?php echo date('Y-m-d H:i',$v['create_time']);?></div> 
						<div class="msgopera mt5">
						<a href="javascript:void(0);" onclick="delMessage('<?php echo $v['message_id'];?>')"><img src="<?php echo $this->pipiFrontPath.'/fontimg/account';?>/delete.jpg" /></a>
						</div>
					</li>
					<?php endforeach;?>
				</ul>

				<?php
					echo '<p>'.$count.' 条记录 '.$page.' / '.$page_num.' 页</p>';
					echo '<ol class="page">
							<li><a href="?r=account/message'.$page_url.'">首页</a></li>';
					$_page = $page > 1 ? ( ($page_num-$page < 2) ? (($page_num - 2 > 0) ? $page_num -2 : 1) : $page - 1) : 1;
					for($_p = $_page; $_p <= $page_num; $_p++){
						echo '<li><a href="?r=account/message'.$page_url.'&page='.$_p.'" '.($_p==$page ? 'style="background:#ffb6e2"' : '').'>'.$_p.'</a></li>';
						if(($_p - $_page) == 2) {
							break;
						}
					}
					echo	'<li><a href="?r=account/message'.$page_url.'&page='.$page_num.'">尾页</a></li>
						 </ol>';
				?>
				
			</div>  
			<?php else: ?>
			<div class="cooper-list">
				没有消息
			</div>
			<?php endif;?>
		</div>
	</div> 
</div>
<script type="text/javascript">
var messageType = "<?php echo $msgType?>";
function delMessage(messageId){
	$.ajax({
		type: "POST",
		data:{message_id:messageId,type:messageType},
		url: "index.php?r=message/delMessage",
		dataType:'json',
		success: function (response) {
			if(response.status == true){
				$('#message_'+messageId).remove();
			}else{
				//alert(response.message);
			}
			
		}
	});
};

function markMessage(messageId){
	$.ajax({
		type: "POST",
		data:{message_id:messageId,type:messageType},
		url: "index.php?r=message/markReadMessage",
		dataType:'json',
		success: function (response) {
			if(response.status == true){
				$('#message_'+messageId).attr('style',"color:#999;");
				$('#message_'+messageId).removeAttr('onclick');
				$('#message_'+messageId).unbind('click');
			}else{
				//alert(response.message);
			}
			
		}
	});
}
</script>