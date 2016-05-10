<?php echo $this->renderPartial('adminIncomeHeader',compact(array_keys(get_defined_vars())));?>
<form name="myform" method="post">
                <dl class="control-list">
                    <dt>
                        <span class="zbname ellipsis">家族主播</span>
                        <span class="zblevel">主播等级</span>
                        <span class="intime">加入家族时间</span>
                        <span class="outtime">离开家族时间</span>
                        <span class="outway">离开方式</span>
                    </dt>
                    <?php foreach($records as $r):?>
                    <dd>
                        <span class="zbname ellipsis"><?php echo $r['nk']?></span>
                      	<span class="zblevel"><em class="lvlo lvlo-<?php echo $r['rk']?>"></em>（ID<?php echo $r['uid']?>）</span>
                        <span class="intime"><?php echo $r['join_time']?></span>
                        <span class="outtime"><?php echo $r['quit_time']?></span>
                        <span class="outway"><?php echo $r['leave']?></span>
                    </dd>
                    <?php endforeach;?>
        
                </dl>
            </form>
  <?php $this->widget('lib.widgets.family.LinkPager', array('pages'=>$pages));?>  
                
 <?php echo $this->renderPartial('adminIncomeFooter',compact(array_keys(get_defined_vars())));?>