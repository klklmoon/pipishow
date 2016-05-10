<?php echo $this->renderPartial('adminIncomeHeader',compact(array_keys(get_defined_vars())));?>
<div class="controlInfo">
                  <select name="month" onchange="changeMonth(this)">
                	<?php foreach($monthList as $key=>$value):
                		if($month == $key){
                			$selected = 'selected';
                		}else{
                			$selected = '';
                		}
                	?>
                		
                    	<option value="<?php echo $key?>" <?php echo $selected?>><?php echo $value?></option>
                    <?php endforeach;?>
                </select>
                <p class="controlinfo-text">家族主播本月收入<em class="pink"><?php echo $header['family_points']?></em>魅力点，家族提成收入<em class="pink"><?php echo $header['family_rmb']?></em>元</p>
            </div>
            
 <form name="myform" method="post">
                <dl class="control-list">
                    <dt>
                        <span class="zbname ellipsis">家族主播</span>
                        <span class="zblevel">主播等级</span>
                        <span class="effect-day">本月收入魅力点</span>
                        <span class="fam-income">家族收入</span>
                        <span class="detail">详细记录</span>
                    </dt>
                     <?php foreach($statices['list'] as $statice):?>
                    <dd>
                        <span class="zbname ellipsis"><?php echo $statice['nickname']?></span>
                        <span class="zblevel"><em class="lvlo lvlo-<?php echo $statice['dk']?>"></em>（ID<?php echo $statice['uid']?>）</span>
                        <span class="effect-day"><?php echo $statice['points']?></span>
                        <span class="fam-income"><?php echo $statice['family_rmb']?></span>
                        <span class="detail"><a href="<?php $this->getTargetHref($this->createUrl('family/adminIncome&type=income_info',array('month'=>$month,'uid'=>$statice['uid'],'family_id'=>$family['id'])))?>">查看</a></span>
                    </dd>
                     <?php endforeach;?>
                </dl>
            </form>
  <?php $this->widget('lib.widgets.family.LinkPager', array('pages'=>$statices['pages']));?>
<script type="text/javascript">
var qcondition = {
		month:/&month\s*=\s*\S*/i
	};


function changeMonth(obj){
	var selectedValue = obj.options[obj.selectedIndex].value;
	var href = location.href;
	searchCondition(href,'month',selectedValue);
 }

function searchCondition(href,key,value){
	href = href.replace(qcondition[key],'');
	if(value !='' && value != null)
	href += '&'+key+'='+value;
	location.href = href;
	return false;
}
	 
</script>        
         
 <?php echo $this->renderPartial('adminIncomeFooter',compact(array_keys(get_defined_vars())));?>