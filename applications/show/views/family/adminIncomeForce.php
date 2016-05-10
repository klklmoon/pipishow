<?php echo $this->renderPartial('adminIncomeHeader',compact(array_keys(get_defined_vars())));?>
<div class="controlInfo">
             <p class="controlinfo-text">强退主播家族累积收入<em class="pink"><?php echo $header['family_points']?></em>魅力点，家族提成收入<em class="pink"><?php echo $header['family_rmb']?></em>元</p>
</div>
            
 <form name="myform" method="post">
                <dl class="control-list">
                    <dt>
                        <span class="zbname ellipsis">家族主播</span>
                          <span class="zblevel">主播等级</span>
                        <span class="effect-day">强退期间魅力点</span>
                        <!--  <span class="effect-hour">本月兑现金额</span> -->
                        <span class="fam-income">家族收入</span>
                        <span class="detail"></span>
                    </dt>
                     <?php foreach($statices['list'] as $statice):?>
                    <dd>
                        <span class="zbname ellipsis"><?php echo $statice['nickname']?></span>
                        <span class="zblevel"><em class="lvlo lvlo-<?php echo $statice['dk']?>"></em>（ID<?php echo $statice['uid']?>）</span>
                        <span class="effect-day"><?php echo $statice['points']?></span>
                          <!-- <span class="effect-hour"></span>-->
                        <span class="fam-income"><?php echo $statice['family_rmb']?></span>
                        <span class="detail"></span>
                    </dd>
                     <?php endforeach;?>
                </dl>
            </form>
  <?php $this->widget('lib.widgets.family.LinkPager', array('pages'=>$statices['pages']));?> 
                 
 <?php echo $this->renderPartial('adminIncomeFooter',compact(array_keys(get_defined_vars())));?>