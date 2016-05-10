<?php $this->renderPartial('account_top',array('info'=>$this->viewer));?>

<div class="clearfix w1000 mt30">

<?php $this->renderPartial('account_left',array('account_left'=>$account_left,'dotey_left'=>$dotey_left,'agent_left'=>$agent_left));?>
   
    <div class="main fright">
        <ul class="main-menu clearfix" id="MianList">
            <li class="menuvisted"><a href="#">关注</a></li>
            <li><a href="<?php echo $this->createUrl('account/manage');?>">管理</a></li>
        </ul><!-- .main-menu -->
        
		<div id="MainCon">
           <div class="cooper-list">
              <p><code style="display:block; float:left; line-height:22px;">排序方式:</code>
                <ul class="order">
                   <!--<li><a href="#">开播顺序</a></li>-->
                   <li><a href="javascript:;" onclick="account_sort.rank_sort(this)" class="on">主播等级</a></li>
                   <li><a href="javascript:;" onclick="account_sort.dedi_sort(this);">我的贡献</a></li>
                </ul>
              </p>
              
              <ul class="focus">
				<?php
				if($dotey_info){
				foreach($dotey_info as $k=>$v){
				?>
				<li data_deti="<?php echo $v['deti'];?>" data_rank="<?php echo $v['dk'];?>">
                   <div class="thumb">
                     <a href="/<?php echo $k;?>" <?php ($this->isPipiDomain) ? 'target="_blank"' : '';?>><img src="<?php echo $v['av'];?>" width="89" height="107" /></a>
                     <?php if($v['live_record']): ?>
						 <?php if($v['live_record']['status']=='1') : ?>
							<span class="vstatus living">直播中</span>
						 <?php elseif($v['live_record']['status']=='0'): ?>
							<span class="vstatus waiting"><?php echo date('n-j G:i',$v['live_record']['start_time']); ?></span>
						 <?php else: ?>
							<span class="vstatus trailer">暂无预告</span>
						 <?php endif;?>
					 <?php else: ?>
						<span class="vstatus trailer">暂无预告</span>
					 <?php endif; ?>
                   </div>
                   <p><a href="/<?php echo $k;?>" <?php ($this->isPipiDomain) ? 'target="_blank"' : '';?>><em class="tit"><?php echo $v['nk'];?></em>&nbsp;&nbsp;&nbsp;&nbsp;<em class="lvlo lvlo-<?php echo $v['dk'];?>"></em></a></p>
                   <p>关注：<?php echo $v['fans_nums'];?></p>
                   <p>我的贡献：<?php echo $v['deti'];?></p>
                   <p><a href="javascript:;" onclick="$.User.cacnelAttentionUser(<?php echo $k;?>,this,'attention')">取消关注</a></p>
                </li>
				<?php
				}
				}
				?>
              </ul>
			 <p></p>         
			 <!--翻页
			 <ol class="page">                 
				<li><a href="###">1</a></li>
				<li><a href="###">2</a></li>
				<li><a href="###">3</a></li>
				<li><a href="###" rel="next">下一页</a></li>
				<li><a href="###">尾页</a></li>
			 </ol>翻页-->
           </div><!-- .cooper-list 汇款设置 -->                       
            
            
        </div><!--#MainCon-->
     </div><!-- .main -->        
</div><!-- .w1000 -->

<script type="text/javascript">
var account_sort = {
	dedi_sort:function(obj){
		var items = $('.focus li').get();
		items.sort(function(a,b){
			var one = parseInt($(a).attr('data_deti'));
			var two = parseInt($(b).attr('data_deti'));
			if(one < two) return 1;
			if(two < one) return -1;
			return 0;
		});
		
		var ul = $(".focus");
		$.each(items,function(i,li){
			ul.append(li);
		});
		$('.order li a').removeClass('on');
		$(obj).addClass('on');
	},
	rank_sort:function(obj){
		var items = $('.focus li').get();
		items.sort(function(a,b){
			var one = parseInt($(a).attr('data_rank'));
			var two = parseInt($(b).attr('data_rank'));
			if(one < two) return 1;
			if(two < one) return -1;
			return 0;
		});
		
		var ul = $(".focus");
		$.each(items,function(i,li){
			ul.append(li);
		});
		if(obj){
			$('.order li a').removeClass('on');
			$(obj).addClass('on');
		}
	}
}
$(function(){
	account_sort.rank_sort();
})
</script>