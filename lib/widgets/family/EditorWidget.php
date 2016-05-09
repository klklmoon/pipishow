<?php
/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2013-9-15 上午9:49:45 hexin $ 
 * @package
 */
class EditorWidget extends PipiWidget {
	public $name; //需要富文本编辑器的textarea输入框的name
	public function init(){
		/* @var $contrller PipiController */
		$contrller = Yii::app()->getController();
		/* @var $clientScript CClientScript */
		$clientScript = Yii::app()->getClientScript();
// 		$dir = CHtml::asset(Yii::getPathOfAlias('lib.widgets.family.views.assets')); //表情图等图片不能正常发布出来
		$dir = '/statics/utils/kindeditor/';
		$clientScript->registerCssFile($dir.'themes/default/default.css?token='.$contrller->hash);
		$clientScript->registerScriptFile($dir.'kindeditor-min.js?token='.$contrller->hash);
		$clientScript->registerScriptFile($dir.'lang/zh_CN.js?token='.$contrller->hash);
	}
	
	
	public function run(){
		$this->render('editor', array('name' => $this->name));
	}
}