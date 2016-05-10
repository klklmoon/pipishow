<?php

class DefaultController extends PipiController
{
	public $breadcrumbs;
	
	public function actionIndex()
	{
		$this->render('index');
	}
	
	
}