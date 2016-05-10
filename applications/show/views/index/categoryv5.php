<ul id="CateFixed" class="catefixed">
    <li class="forrank anchorRank" style="display:<?php echo $type=='song'?'block':'none';?>">
        <a class="fixedlink" href="#">主播排行</a>
        <div class="catefixed-box">
            <p class="catefixed-h">点唱专区&nbsp;<em>主播排行</em></p>
            <div class="kusobox">
                <div class="kuso-hd">
                    <ul class="clearfix">
                        <li class="first on"><a href="javascript:void(0);">今日</a></li>
                        <li><a href="javascript:void(0);">本周</a></li>
                        <li><a href="javascript:void(0);">本月</a></li>
                    </ul>
                </div><!--.kuso-hd-->
                <div class="kuso-bd">
                    <?php $this->renderPartial('application.views.index.charmrank',array('rank'=>$top['dotey_songs_rank']['today'],'isLazyLoad'=>false));?>
                    <?php $this->renderPartial('application.views.index.charmrank',array('rank'=>$top['dotey_songs_rank']['week'],'isLazyLoad'=>false));?>
                    <?php $this->renderPartial('application.views.index.charmrank',array('rank'=>$top['dotey_songs_rank']['month'],'isLazyLoad'=>false));?>
                </div><!--.kuso-bd-->
            </div>
        </div>
    </li>
    <li class="forrank richRank" style="display:<?php echo $type=='song'?'block':'none';?>">
        <a class="fixedlink" href="#">富豪排行</a>
        <div class="catefixed-box">
            <p class="catefixed-h">点唱专区&nbsp;<em>富豪排行</em></p>
            <div class="kusobox">
                <div class="kuso-hd">
                    <ul class="clearfix">
                        <li class="first on"><a href="javascript:void(0);">今日</a></li>
                        <li><a href="javascript:void(0);">本周</a></li>
                        <li><a href="javascript:void(0);">本月</a></li>
                    </ul>
                </div><!--.kuso-hd-->
                <div class="kuso-bd">
                    <?php $this->renderPartial('application.views.index.richrank',array('rank'=>$top['user_songs_rank']['today'],'isLazyLoad'=>false));?>
                    <?php $this->renderPartial('application.views.index.richrank',array('rank'=>$top['user_songs_rank']['week'],'isLazyLoad'=>false));?>
                    <?php $this->renderPartial('application.views.index.richrank',array('rank'=>$top['user_songs_rank']['month'],'isLazyLoad'=>false));?>
                </div><!--.kuso-bd-->                                
            </div>
        </div>
    </li>
    <li class="forrank autoruns"><a class="fixedlink DD_belapng" href="#">返回顶部</a></li>
</ul><!--.catefixed-->
<div class="main-box">
    <div class="w1000 clearfix">
		<!-- 分类页左侧导航 -->
		<?php $this->renderPartial('application.views.index.index_left'); ?>
        <div class="rightCateWrap">
            <h3><em></em><span id="categorytitle"><?php echo $title;?></span></h3>
            <div class="category-box">
                <ul class="category-hd">
                    <li class="title on"><a href="javascript:void(0);" onclick="freshenCategory('sort','online')">观众人数<span class="trend"></span></a></li>
                    <li class="title"><a href="javascript:void(0);" onclick="freshenCategory('sort','rank')">主播等级<span class="trend"></span></a></li>
                    <li class="title"><a href="javascript:void(0);" onclick="freshenCategory('sort','time')">开播时间<span class="trend"></span></a></li>
                    <li class="title"><a href="javascript:void(0);" onclick="freshenCategory('sort','days')">连播天数<span class="trend"></span></a></li>
                    <li class="view bigview"><a title="大图浏览" href="javascript:void(0);"></a></li>
                    <li class="view smlview"><a title="小图浏览" class="onover" href="javascript:void(0);"></a></li>
                </ul><!--.category-hd-->
                <div class="category-bd">
                <ul id="freshenDoteyList" class="actorlist smallactor clearfix">
					<?php if($lastsort=='online'){
						$this->renderPartial('application.views.index.doteylist',$livingArchives);
					}?>
				</ul>
<!--                     <p class="morehotBtn"><a href="#">点击看更多直播中的美女</a></p> -->
                </div><!--.category-bd-->
                            <div class="category-bd">
            <h3><em></em><span>即将开播</span></h3>
            <ul class="actorlist smallactor clearfix">
            <?php 
            	$this->renderPartial('application.views.index.doteylist',$waitArchives);
            ?>
            </ul>
<!--             <p class="morehotBtn"><a href="#">点击看更多直播中的美女</a></p> -->
            </div>
            </div><!--.category-box-->
        </div><!--.rightCateWrap-->

    </div>
</div><!--.main-box-->
<script type="text/javascript">
          
	/*分类浏览页主播选项卡*/
	var cateboola='a',cateboolb='a',strcate='';
	$('.category-hd .title').bind('click',function(){
	    if($(this).hasClass('on')){
	        cateboola='b';
	    }
	    if($(this).find('span').hasClass('top')){
	        cateboolb='b';
	    }
	    strcate=cateboola+cateboolb;
	    if(cateboola=='a'){
	        $(this).addClass('on').siblings().removeClass('on');
	        $(this).siblings('.title').find('span').removeClass('top');
	    }
	    
	})
	$('.category-hd .title').toggle(function(){
	    changeArrow($(this),strcate);
	},function(){
	    changeArrow($(this),strcate);
	});
	function changeArrow(catedom,strcate){
	    if(strcate=="ba"){
	        catedom.find('.trend').addClass('top');
	        cateboola='a',cateboolb='a';
	    }else if(strcate=="bb"){
	        catedom.find('.trend').removeClass('top');
	        cateboola='a',cateboolb='a';
	    }  
	}
	/*大小图浏览按钮*/
	var viewbool=true;
	$('.category-hd li.view').hover(function(){
	    $(this).find('a').addClass('over');
	},function(){
	    $(this).find('a').removeClass('over');
	})
	$('.category-hd li.smlview').bind('click',function(){
	    $(this).find('a').addClass('onover');
	     $(this).siblings('.view').find('a').removeClass('onover');
	     $('.category-bd .actorlist').removeClass('bigactor').addClass('smallactor');
	});
	$('.category-hd li.bigview').bind('click',function(){
	    $(this).find('a').addClass('onover');
	     $(this).siblings('.view').find('a').removeClass('onover');
	     $('.category-bd .actorlist').removeClass('smallactor').addClass('bigactor');
	});
	
	/*主播排行、富豪排行悬浮*/
	$('.catefixed li.forrank').hover(function(){
	    $(this).find('a').first().addClass('on');
	    $(this).find('.catefixed-box').css('display','block');
	},function(){
	    $(this).find('a').first().removeClass('on');
	    $(this).find('.catefixed-box').css('display','none');
	});
	
	var cateElem=document.getElementById('CateFixed');
	cateElem.style.right = '20px';
	cateElem.style.top = '15%';
	position.fixed(cateElem);
	$(window).scroll(function(){
	    if($(window).scrollTop()>400)
	    {
	        $(".catefixed li.autoruns").css('display','block');
	    }else{
	        $(".catefixed li.autoruns").css('display','none');
	    }
	}); 
	
	var qcondition = {
			type:/&type\s*=\s*\w*/i,
			sort:/&sort\s*=\s*\w*/i,
			id:/&id\s*=\s*\d*/i,
			by:/&by\s*=\s*\w*/i
	};
	
	var onlinecount=0;
	var rankcount=0;
	var timecount=0;
	var dayscount=0;
	var lastsort="<?php echo $lastsort;?>";
	var href="<?php echo $searchUrl;?>";
	
	function freshenCategory(key,value){
		href=href.replace('categoryv5','categorymodule');
		href = href.replace(qcondition[key],'');
		href = href.replace(/&by\s*=\s*\w*/i,'');
		
		if(value !='' && value != null)
			href += '&'+key+'='+value;
	
		if(value=='online')
		{
			onlinecount++;
			if(value!=lastsort )
				onlinecount=0;
			if(onlinecount%2==1)
				href += '&by=asc';
			else
				href += '&by=desc';
		}
		if(value=='rank')
		{
			rankcount++;
			if(value!=lastsort )
				rankcount=0;
	
			if(rankcount%2==1)
				href += '&by=asc';
			else
				href += '&by=desc';
		}
		if(value=='time')
		{
			timecount++;
			if(value!=lastsort )
				timecount=0;
	
			if(timecount%2==1)
				href += '&by=asc';
			else
				href += '&by=desc';
		}
		if(value=='days')
		{
			dayscount++;
			if(value!=lastsort )
				dayscount=0;
	
			if(dayscount%2==1)
				href += '&by=asc';
			else
				href += '&by=desc';
		}

		$.ajax({
			type : 'post',
			url : href,
			async : false,
			dataType: "json",
			success:function(data){
				$("#freshenDoteyList").html(data.html);
				$("#categorytitle").text(data.title);
				href=data.searchUrl;
				lastsort=data.lastsort;
				if(data.type=='song')
				{
					$("#CateFixed li").eq(0).show();
					$("#CateFixed li").eq(1).show();
				}
				else
				{
					$("#CateFixed li").eq(0).hide();
					$("#CateFixed li").eq(1).hide();
				}
			}
		});
		
	}
	
	
	if(lastsort=='rank')
	{
		$(".category-hd li a").eq(1).click();
	}
	if(lastsort=='time')
	{
		$(".category-hd li a").eq(2).click();
	}
	if(lastsort=='days')
	{
		$(".category-hd li a").eq(3).click();
	}

</script>