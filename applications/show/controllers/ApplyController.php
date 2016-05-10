<?php

class ApplyController extends PipiController{
	private $_allowT = array(1,2);
	protected static $upload;
	
	public function actionIndex(){
		$type = Yii::app()->request->getParam('t',1);
		if(!in_array($type,$this->_allowT)){
			throw new CHttpException(404);
		}
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/apply/star.css?token='.$this->hash,'all');
		$this->render('index_'.$type);
	}

	/**
	 * 主播申请
	 * @author hexin
	 */
	public function actionApply(){
		if(!$this->isLogin) $this->redirect('/');
		$type = Yii::app()->request->getParam('t',1);
		$t = $type+1;
		if(!in_array($type,$this->_allowT)){
			throw new CHttpException(404);
		}
		
		$contrller = Yii::app()->getController();
		$uid = Yii::app()->user->id;
		$edit = intval(Yii::app()->request->getParam('edit', 0));
		$doteyService = new DoteyService();
		if($this->isLogin){
			if($this->isDotey){
				$this->redirect('/'.$uid);
				Yii::app()->end();
			}
			$applyInfo = $doteyService->getApplyInfo($uid,$t);
			
			if(!empty($applyInfo) && $applyInfo['status'] == 2){
				$this->renderApplyResult($uid, $applyInfo,$type);
			}
		}

		$error = '';
		if(Yii::app()->request->getIsPostRequest()){
			$doteyApplyForm = new DoteyApplyOtherForm();
			foreach($_POST as $k => $v){
				$doteyApplyForm -> $k = $v;
			}
			if($doteyApplyForm->validate()){
				$realname = Yii::app()->request->getPost('realname');
				$gender = Yii::app()->request->getPost('gender');
				$birth_province = Yii::app()->request->getPost('birth_province');
				$birth_city = Yii::app()->request->getPost('birth_city');
				$province = Yii::app()->request->getPost('province');
				$city = Yii::app()->request->getPost('city');
				$profession = Yii::app()->request->getPost('profession');
				$profession = $profession == '学生' ? $profession : Yii::app()->request->getPost('profession_text');
				$mobile = Yii::app()->request->getPost('mobile');
				$qq = Yii::app()->request->getPost('qq');
				$id_card = Yii::app()->request->getPost('id_card');
				$id_card_front = Yii::app()->request->getPost('id_card_front');
				$id_card_back = Yii::app()->request->getPost('id_card_back');
				$personal_image = Yii::app()->request->getPost('personal_image');
				$agree = Yii::app()->request->getPost('agree');
				$bank = Yii::app()->request->getPost('bank');
				$bank_account = Yii::app()->request->getPost('bank_account');
				
				$data = array(
					'realname'	=> $realname,
					'gender'	=> $gender,
					'province'	=> $province,
					'city'		=> $city,
					'mobile'	=> $mobile,
					'qq'		=> $qq,
					'id_card'	=> $id_card,
					'bank'		=> $bank,
					'bank_account' => $bank_account,
					'type' => $t,
					'profession'=> $profession,
					'birth_province'	=> $birth_province,
					'birth_city'		=> $birth_city,
					'id_card_front'		=> $id_card_front,
					'id_card_back'		=> $id_card_back,
					'personal_image'	=> $personal_image,
					'status' => 1,
				);
				if($doteyService -> doteyApply($uid, $data)){
					$add['type'] = ($t==2)?DOTEY_MANAGER_PROXY:DOTEY_MANAGER_STAR;
					$add['is_display'] = 0;
					$add['query_allow'] = 0;
					$add['agency'] = 'empty';
					$add['company'] = 'empty';
					$doteyService->updateProxy($uid,$add);
					$this->renderApplyResult($uid, $data,$type);
				}else{
					$error['system'][] = Yii::t('common','System error');
					$error['system']=$doteyService->getNotice();
				}
			}else{
				$error = $doteyApplyForm->getErrors();
			}
		}

		if($this->isLogin){
			$userService = new UserService();
			$user = $userService->getUserBasicByUids(array($uid));
			$user = $user[$uid];
		}

		$data = array(
			'isLogin' => $this->isLogin,
			'user'	=> $this->isLogin ? $user : array(),
			'error'	=> $error,
			'applyInfo'	=> $applyInfo,
			'edit'	=> $edit,
		);
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/apply/star.css?token='.$this->hash,'all');
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/common/boxy.css?token='.$contrller->hash);
		$this->cs->registerScriptFile($this->pipiFrontPath.'/js/area/city_data.js?token='.$contrller->hash);
		$this->cs->registerScriptFile($this->pipiFrontPath.'/js/area/datajs.js?token='.$contrller->hash);
		$this->cs->registerScriptFile($this->pipiFrontPath.'/js/common/jquery.validate.js?token='.$contrller->hash);
		$this->cs->registerScriptFile($this->pipiFrontPath.'/js/common/jquery.boxy.js?token='.$contrller->hash);
		$this->render('apply_'.$type,$data);
	}

	/**
	 * 主播用户协议
	 */
	public function actionAgreement(){
		if(!$this->isLogin) $this->redirect('/');
		$type = Yii::app()->request->getParam('t',1);
		if(!in_array($type,$this->_allowT)){
			throw new CHttpException(404);
		}
		$this->cs->registerCssFile($this->pipiFrontPath.'/css/dotey/apply.css?token='.$this->hash,'all');
		$this->render('agreement_'.$type);
	}

	public function actionUpload(){
		if(!$this->isLogin && !Yii::app()->request->isFlashRequest) exit();
		$type = Yii::app()->request->getParam('type');
		$title = Yii::app()->request->getParam('title');

		$t = Yii::app()->request->getParam('t',1);
		if(!in_array($t,$this->_allowT)){
			throw new CHttpException(404);
		}
		
		$uid = Yii::app()->user->id;
		if(Yii::app()->request->getIsPostRequest()){
			$input = Yii::app()->request->getParam('input');
			$step = Yii::app()->request->getParam('a');

			Yii::app()->detachEventHandler('onEndRequest',array(Yii::app()->log,'processLogs'));
			if($this->getUploadSingleton()->processRequest($input, $step, $type, array($this, 'processImage'))){
				exit();
			}
		}
		$data = array(
			'flashHtml' => $this->getUploadSingleton()->renderHtml($uid),
			'title'		=> $title,
		);
		$this->renderPartial('upload', $data);
	}

	public function processImage($id, $type, $src, $dir, $big){
		$smallFile = $this->getUploadSingleton()->getSaveFile($id, 'small', $type);
		$fp = fopen($smallFile, 'wb');
		fwrite($fp, $big);
		fclose($fp);
		$middleFile = $this->getUploadSingleton()->getSaveFile($id, 'middle', $type);
		$bigFile = $this->getUploadSingleton()->getSaveFile($id, 'big', $type);
		
		if(!is_file($src)){
			$src = $smallFile;
		}
		$srcInfo = getimagesize($src);
		$srcWidth = $srcInfo[0];
		$srcHeight = $srcInfo[1];
		$srcType = strtolower(substr(image_type_to_extension($srcInfo[2]), 1));
		unset($srcInfo);
		
		//middle
		$scale = min(300 / $srcWidth, 300 / $srcHeight);
		$width = (int) ($srcWidth * $scale);
		$height = (int) ($srcHeight * $scale);
		$createFun = 'ImageCreateFrom' . ($srcType == 'jpg' ? 'jpeg' : $srcType);
		$srcImg = $createFun($src);

		if ($srcType != 'gif' && function_exists('imagecreatetruecolor'))
			$thumbImg = imagecreatetruecolor($width, $height);
		else
			$thumbImg = imagecreate($width, $height);
		if (function_exists("ImageCopyResampled"))
			imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
		else
			imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
		if ('gif' == $type || 'png' == $type) {
			$background_color = imagecolorallocate($thumbImg, 0, 255, 0);
			imagecolortransparent($thumbImg, $background_color);
		}
		if ('jpg' == $type || 'jpeg' == $type)
			imageinterlace($thumbImg, 1);
		$imageFun = 'image' . ($srcType == 'jpg' ? 'jpeg' : $srcType);
		$imageFun($thumbImg, $middleFile);
		imagedestroy($thumbImg);

		//big
		$scale = min(500 / $srcWidth, 500 / $srcHeight);
		$width = (int) ($srcWidth * $scale);
		$height = (int) ($srcHeight * $scale);
		$createFun = 'ImageCreateFrom' . ($srcType == 'jpg' ? 'jpeg' : $srcType);
		$srcImg = $createFun($src);
		if ($srcType != 'gif' && function_exists('imagecreatetruecolor'))
			$thumbImg = imagecreatetruecolor($width, $height);
		else
			$thumbImg = imagecreate($width, $height);
		if (function_exists("ImageCopyResampled"))
			imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
		else
			imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
		if ('gif' == $type || 'png' == $type) {
			$background_color = imagecolorallocate($thumbImg, 0, 255, 0);
			imagecolortransparent($thumbImg, $background_color);
		}
		if ('jpg' == $type || 'jpeg' == $type)
			imageinterlace($thumbImg, 1);
		$imageFun = 'image' . ($srcType == 'jpg' ? 'jpeg' : $srcType);
		$imageFun($thumbImg, $bigFile);
		imagedestroy($thumbImg);

		imagedestroy($srcImg);
		
		return array('big' => $bigFile, 'middle' => $middleFile, 'small' => $smallFile);
	}

	protected function getUploadSingleton(){
		if(!self::$upload){
			self::$upload = new PipiFlashUpload();
			self::$upload -> tmpFolder	= 'dotey';
			self::$upload -> realFolder = 'dotey';
			self::$upload -> filePrefix = 'dotey_';
		}
		return self::$upload;
	}

	protected function getImagePath($id, $size, $type = ''){
		return $this->getUploadSingleton()->getFileUrl($id, $size, $type);
	}
	
	protected function renderApplyResult($uid, $applyInfo = null,$type = 1){
		if(!empty($applyInfo)){
			$this->cs->registerCssFile($this->pipiFrontPath.'/css/apply/star.css?token='.$this->hash,'all');
			$data = array(
				'applyInfo'	=> $applyInfo,
			);
			$this->render('success_'.$type, $data);
		}
		$this->redirect('/'.$applyInfo['uid']);
	}

}
?>