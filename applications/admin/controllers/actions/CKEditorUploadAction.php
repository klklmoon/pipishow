<?php
class CKEditorUploadAction extends PipiAction{
	public function run(){
		$this->getController()->renderPartial('public/ckeditor_upload');
	}
}