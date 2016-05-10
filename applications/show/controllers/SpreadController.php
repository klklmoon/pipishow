<?php
class SpreadController extends PipiController{
	public function actionIndex(){
		$this->renderPartial('register');
	}
	
	public function actionPrograme(){
		$sign=Yii::app()->request->getParam('sign');
		$operateService=new OperateService();
		$url=$operateService->getSpreadPrograme();
		if($sign){
			$url.='?sign='.$sign;
		}
		$this->redirect($url);
	}
}
?>