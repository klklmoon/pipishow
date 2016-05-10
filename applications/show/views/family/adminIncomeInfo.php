<?php echo $this->renderPartial('adminIncomeHeader',compact(array_keys(get_defined_vars())));?>
<div class="controlInfo">
       <p class="controlinfo-text"><?php echo $info['nk']?><em class="lvlo lvlo-<?php echo $info['dk']?>"></em>（ID<?php echo $info['uid']?>）的直播详情：</p>
       <p class="controlinfo-text"><em class="pink"><?php echo date('n',strtotime($month))?>月收入共<?php echo $info['points']?>魅力点</em></p>
</div>
<form name="myform" method="post">
           <dl class="control-list liveinfo-det">
                    <dt>
                        <span class="date">日期</span>
                        <span class="meili-num">魅力收入点</span>
                        <span class="live-hour">直播时长</span>
                        <span class="parade">节目预告</span>
                    </dt>
                     <?php foreach($statices as $statice):?>
                    <dd>
                        <span class="date"><?php echo $statice['date']?></span>
                        <span class="meili-num"><?php echo $statice['points']?></span>
                        <span class="live-hour"><?php echo $statice['live_hour']?></span>
                        <span class="parade"><?php echo $statice['program']?></span>
                    </dd>
                   
                 	<?php endforeach;?>

                </dl>
</form>

<?php echo $this->renderPartial('adminIncomeFooter',compact(array_keys(get_defined_vars())));?>