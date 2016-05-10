<?php
class PublicController extends PipiAdminController {
	public $layout ='errorMain';
	public $errorbg ;
	public $cs;
	
	public function init(){
		parent::init();
		$this->cs = Yii::app()->getClientScript();
		$this->cs->reset();
		$assetManager = Yii::app()->getAssetManager();
		$assetManager->excludeFiles = array('.svn','.gitignore');
		
		$static = Yii::getPathOfAlias('root.statics');
		$this->errorbg = $assetManager->publish($static.'/css/admin/img/error_bg.png');
	}
	
	public function actions(){
		return array(
			'ckeditorupload'=>'application.controllers.actions.CKEditorUploadAction',
		);
	}
	
	public function actionError()
	{
	    if(Yii::app()->errorHandler->error){
	    	$error=Yii::app()->errorHandler->error;
	    	if(Yii::app()->request->isAjaxRequest){
	    		echo $error['message'];
	    		Yii::app()->end();
	    	}else{
	    		if(isset($error['code'])){
					$op = "error".$error['code'];
					$this->$op ($error);    			
	    		}else{
		        	$this->renderPartial('error');
	    		}
	    	}
	    }
	}
	
	public function error405($error){
		$this->redirect($this->createUrl('user/login'));
		$this->render('error_405');
	}
	
	public function error404($error){
		$this->render('error_404');
	}
	
	public function error500($error){
		$this->render('error_500',array('errorMsg'=>$error['message']));
	}
}