<?php
/**
 * @author He xin <hexin@pipi.cn> 2013-9-6
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class LinkPager extends CLinkPager{
	public $nextPageLabel = '下一页';
	public $prevPageLabel = '上一页';
	public $firstPageLabel = '首页';
	public $lastPageLabel = '末页';
	public $htmlOptions=array(
		'class'	=> 'page',
	);
	public $selectedPageCssClass = 'pageon';
	
	public function run()
	{
		$this->registerClientScript();
		$buttons=$this->createPageButtons();
		if(empty($buttons))
			return;
		echo CHtml::tag('p',$this->htmlOptions,implode("\n",$buttons));
	}
	
	protected function createPageButton($label,$page,$class,$hidden,$selected)
	{
		if($class == $this->firstPageCssClass || $class == $this->previousPageCssClass){
			$class = 'prev';
		}elseif($class == $this->nextPageCssClass || $class == $this->lastPageCssClass){
			$class = 'next';
		}else{
			$class = 'pagenum';
		}
		if($selected) $class.=' '.$this->selectedPageCssClass;
		return '<a class="'.$class.'" href="'.$this->createPageUrl($page).'">'.$label.'</a>';
	}
}