	<div class="fright memberMesgBd-r">
        <!--家族长start-->
        <div class="rightcon">
            <h4 class="clearfix">
                <i class="banericon"></i>
                <span class="fleft pink">家族长</span>
            </h4>
            <div class="conbox">
                <div class="today">
                    <ul class="rank">
                       <li class="clearfix">
                            <a class="rank-pic fleft" href="javascript:void(0);"><img src="<?php echo $family_owner['pic'];?>"></a>
                            <p class="richname fleft">
                                <a class="ellipsis pink" href="javascript:void(0);"><?php echo $family_owner['nk'];?><em>(<?php echo $family_owner['uid'];?>)</em></a>
                                <em class="lvlo <?php echo $family_owner['ut'] & 2 ? 'lvlo-'.intval($family_owner['dk']) : 'lvlr-'.intval($family_owner['rk']);?>"></em>
                                <?php if(!empty($family_owner['fp']['medal'])){ ?><img style="margin-bottom:-6px;" src="<?php echo $family_owner['fp']['medal']; ?>"><?php } ?>
                            </p>
                        </li>
                    </ul>
                </div><!--.today-->
            </div>
        </div>
        <p class="rightcon-btm"></p>
        <!--家族长end-->

        <!--家族长老、管理 start-->
        <div class="rightcon" id="admin">
            <h4 class="clearfix">
                <p class="fright mr5 start-tab clearfix">
                    <a class="starttabover" href="javascript:void(0);">家族长老(<?php echo count($family_elder);?>)</a>
                    <em>&#124;</em>
                    <a href="javascript:void(0);">家族管理(<?php echo count($family_admin);?>)</a>
                </p>
            </h4>
            <div class="conbox">
                <div class="today">
                    <ul class="rank">
                    	<?php foreach($family_elder as $fe){?>
						<li class="clearfix" style="width:50%;float:left;">
                            <a class="rank-pic fleft" href="javascript:void(0);"><img src="<?php echo $fe['pic'];?>"></a>
                            <p class="richname fleft" style="width:76px;">
                                <a class="ellipsis pink" href="javascript:void(0);"><?php echo $fe['nk'];?></a>
                                <em class="lvlo <?php echo $fe['ut'] & 2 ? 'lvlo-'.intval($fe['dk']) : 'lvlr-'.intval($fe['rk']);?>"></em>
                                <?php if(!empty($fe['medal'])){ ?><img src="<?php echo $fe['medal']; ?>"><?php } ?>
                            </p>
                        </li>
                        <?php }?>
                        <div style="clear:both;"></div>
                    </ul>
                </div>
            </div>
            <div class="conbox" style="display:none;">
                <div class="today">
                    <ul class="rank">
                    	<?php foreach($family_admin as $fa){?>
						<li class="clearfix" style="width:50%;float:left;">
                            <a class="rank-pic fleft" href="javascript:void(0);"><img src="<?php echo $fa['pic'];?>"></a>
                            <p class="richname fleft" style="width:76px;">
                                <a class="ellipsis pink" href="javascript:void(0);"><?php echo $fa['nk'];?></a>
                                <em class="lvlo <?php echo $fa['ut'] & 2 ? 'lvlo-'.intval($fa['dk']) : 'lvlr-'.intval($fa['rk']);?>"></em>
                                <?php if(!empty($fa['medal'])){ ?><img src="<?php echo $fa['medal']; ?>"><?php } ?>
                            </p>
                        </li>
                        <?php }?>
                        <div style="clear:both;"></div>
                    </ul>
                </div>
            </div>
        </div>
        <p class="rightcon-btm"></p>
        <!--家族长老、管理 end-->

        <!--家族主播右侧大图片 start-->
        <div class="rightcon" id="user-list">
        	<?php if($family['sign']){ ?>
            <h4 class="clearfix">
                <p class="fright mr5 start-tab clearfix">
                    <a class="starttabover" href="javascript:void(0);" data-tag='showLiving'>正在直播(<?php echo $living['count'];?>)</a>
                    <em>&#124;</em>
                    <a href="javascript:void(0);" data-tag='showDoteys'>家族主播(<?php echo count($family_dotey);?>)</a>
                    <em>&#124;</em>
                    <a href="javascript:void(0);" data-tag='showMembers'>家族富豪(<?php echo $family_members['count'];?>)</a>
                </p>
            </h4>
            <div class="conbox" id="box_living">
                <ul class="richlist clearfix"></ul>
                <a class="moreDynamic" href="javascript:void(0);" onclick="showLiving();">查看更多</a>
            </div>
            <div class="conbox" id="box_doteys" style="display:none;">
                <ul class="richlist clearfix"></ul>
                <a class="moreDynamic" href="javascript:void(0);" onclick="showDoteys();">查看更多</a>
            </div>
            <div class="conbox" id="box_members" style="display:none;">
                <ul class="richlist clearfix" ></ul>
                <a class="moreDynamic" href="javascript:void(0);" onclick="showMembers();">查看更多</a>
            </div>
            <?php }else{ ?>
            <h4 class="clearfix">
                <p class="fright mr5 start-tab clearfix">
                    <a class="starttabover" href="javascript:void(0);" data-tag='showMembers'>家族富豪(<?php echo $family_members['count'];?>)</a>
                </p>
            </h4>
            <div class="conbox" id="box_members">
                <ul class="richlist clearfix" ></ul>
                <a class="moreDynamic" href="javascript:void(0);" onclick="showMembers();">查看更多</a>
            </div>
            <?php } ?>
        </div>
        <p class="rightcon-btm"></p>
		<!--家族主播右侧大图片 end-->
		
        <!--家族荣誉start-->
        <div class="rightcon">
            <h4 class="clearfix">
                <i class="banericon"></i>
                <span class="fleft pink">家族荣誉</span>
            </h4>
            <div class="conbox">
                <div class="today">
                	<input type="hidden" name="last_id" id="last_id" value="<?php echo count($honor) > 0 ? $honor[count($honor) - 1]['id'] : 0;?>" />
                	<?php echo $this->renderPartial('honor', array('family' => $family,'honor' => $honor, 'family_owner' => $family_owner));?>
                    <a href="javascript:void(0);" id="honor" class="moreDynamic">查看更多</a>
                </div><!--.today-->
            </div>
        </div>
        <p class="rightcon-btm"></p>
        <!--家族荣誉end-->
    </div>
<script type="text/javascript">
var living = [];
var doteys = [];
var members = [];
var num_living = num_doteys = num_members = 0;
var num = 9;
<?php
if($family['sign'] == 1){
	$temp = array();
	$i = 0;
	foreach($living['list'] as $fd){
		$temp = array(
			'uid'	=> $fd['uid'],
			'src'	=> $fd['pic'],
			'nk'	=> $fd['nk'],
			'rank'	=> 'lvlo-'.intval($fd['dk']),
		);
		echo "living[".$i++."] = ".json_encode($temp)."\n";
	}
	$i = 0;
	foreach($family_dotey as $fd){
		$temp = array(
			'uid'	=> $fd['uid'],
			'src'	=> $fd['pic'],
			'nk'	=> $fd['nk'],
			'rank'	=> 'lvlo-'.intval($fd['dk']),
		);
		echo "doteys[".$i++."] = ".json_encode($temp)."\n";
	}
}
$i = 0;
foreach($family_members['list'] as $fd){
	$temp = array(
		'uid'	=> $fd['uid'],
		'src'	=> $fd['pic'],
		'nk'	=> $fd['nk'],
		'rank'	=> $fd['ut'] & 2 ? 'lvlo-'.intval($fd['dk']) : 'lvlr-'.intval($fd['rk']),
	);
	echo "members[".$i++."] = ".json_encode($temp)."\n";
}
?>
$(function(){
	$('#admin .start-tab a').click(function(){
		var n = $('#admin .start-tab a').index($(this));
		$('#admin .start-tab a').removeClass('starttabover');
		$(this).addClass('starttabover');
		$('#admin .conbox').css('display', 'none');
		$($('#admin .conbox').get(n)).css('display', 'block');
	});

	$('#user-list .start-tab a').click(function(){
		var n = $('#user-list .start-tab a').index($(this));
		$('#user-list .start-tab a').removeClass('starttabover');
		$(this).addClass('starttabover');
		$('#user-list .conbox').css('display', 'none');
		num_living = num_doteys = num_members = 0;
		$('#box_living ul').html('');
		$('#box_doteys ul').html('');
		$('#box_members ul').html('');
		var tag = $(this).attr('data-tag');
		eval(tag+'();');
		$($('#user-list .conbox').get(n)).css('display', 'block');
	});

	$('#honor').click(function(){
		var last_id = $('#last_id').val();
		$.ajax({
			url : "index.php?r=family/honor",
			type : "GET",
			data:{'family_id':<?php echo $family['id'];?>, 'last_id':last_id},
			dataType : "html",
			success : function(html){
				$('#honor').before(html);
				$('#last_id').val(last_id - 1);
		 	}
		});
	});

	<?php if($family['sign'] == 1){ ?>showLiving();<?php }else{ ?>showMembers();<?php } ?>
});
function showLiving(){
	if(num_living < living.length){
		var html = '';
		var max = num_living + num;
		for(num_living; num_living < max && num_living < living.length; num_living++){
			html += showHtml(living[num_living], 'living');
		}
		$('#box_living ul').append(html);
	}
}
function showDoteys(){
	if(num_doteys < doteys.length){
		var html = '';
		var max = num_doteys + num;
		for(num_doteys; num_doteys < max && num_doteys < doteys.length; num_doteys++){
			html += showHtml(doteys[num_doteys], 'dotey');
		}
		$('#box_doteys ul').append(html);
	}
}
function showMembers(){
	if(num_members < members.length){
		var html = '';
		var max = num_members + num;
		for(num_members; num_members < max && num_members < members.length; num_members++){
			html += showHtml(members[num_members], 'member');
		}
		$('#box_members ul').append(html);
	}
}
function showHtml(val, type){
	var url = 'javascript:void(0);';
	var target = '';
	if(type == 'living' || type == 'dotey'){
		url = '/'+val.uid;
		target = 'target="<?php echo $this->target?>"';
	}
	var html = '<li><a href="'+url+'" '+target+'>'+
    	'<img src="'+val.src+'">'+
		'<span class="ellipsis" style="display: inline-block; width:100%; height:18px;">'+val.nk+'</span>'+
		'<em class="lvlo '+val.rank+'"></em>'+
		'</a></li>';
	return html;
}
</script>