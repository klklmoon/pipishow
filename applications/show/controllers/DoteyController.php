<?php

class DoteyController extends PipiController{
	protected static $upload;


	public function actionActxRecord(){
		$this->layout= false;
		$this->render('actxRecordPlay');
	}
	
	public function actionGetLiveNotice(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$type=Yii::app()->request->getParam('type');
		$doteyId=Yii::app()->user->id;
		if($doteyId<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$archivesService=new ArchivesService();
		$data=$archivesService->getArchivesByArchivesId($archives_id);
		$start_time=date('Y-m-d H:i');
		$userService=new UserService();
		$userBase=$userService->getUserFrontsAttributeByCondition($doteyId,true,true);
		$sub_title=isset($data['live_record']['sub_title'])?$data['live_record']['sub_title']:$userBase['nk'].'，欢迎大家围观！';
		exit(json_encode(array('start_time'=>$start_time,'sub_title'=>$sub_title,'type'=>$type)));
	}

	/**
	 * 添加直播公告
	 * @author leiwei
	 */
	public function actionAddLiveNotice(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$start_time=Yii::app()->request->getParam('start_time');
		$liveSubject=Yii::app()->request->getParam('liveSubject');
		$type=Yii::app()->request->getParam('type');
		$live_model=Yii::app()->request->getParam('live_model');
		$doteyId=Yii::app()->user->id;
		if($doteyId<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		if($archives_id<=0||empty($liveSubject)||empty($start_time)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(strlen($liveSubject)>48){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Live theme cannot be more than 16 Chinese characters'))));
		}
		$archivesService=new ArchivesService();
		$start_time=strtotime($start_time);
		$result=$archivesService->createArchivesLive($doteyId,$archives_id,$start_time,$liveSubject);
		if(!$result||$result<=0){
			$msg=$archivesService->getError();
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives',$msg))));
		}else{
			$upload=new PipiFlashUpload();
			$upload->tmpFolder= 'dotey';
			$upload->realFolder = 'dotey';
			$upload->filePrefix = 'dotey_';
			$doteyCover=$upload->getSaveFile($doteyId,'small','display');
			$display=is_file($doteyCover)?0:1;
		}
		if($type=='true'){
			$result=$archivesService->startArchivesLive($doteyId,$archives_id,$live_model);
			if(!$result||$result<=0){
				exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Archives data save failed'))));
			}
			exit(json_encode(array('flag'=>1,'data'=>array('sub_title'=>$liveSubject,'start_time'=>date('H:i',$start_time),'display'=>$display),'message'=>Yii::t('archives','Archives is start'))));
		}else{
			exit(json_encode(array('flag'=>1,'data'=>array('sub_title'=>$liveSubject,'start_time'=>date('H:i',$start_time),'display'=>$display),'message'=>Yii::t('archives','Live notice set successfully'))));
		}
	}

	public function actionReStartLive(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$doteyId=Yii::app()->user->id;
		if($doteyId<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		if($archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByArchivesId($archives_id);
		if($archives['uid']!=$doteyId){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Not the archives owner'))));
		}
		if(isset($archives['live_record'])){
			if($archives['live_record']['status']==0){
				$data['time']=date('H:i',$archives['live_record']['start_time']);
				$data['sub_title']=$archives['live_record']['sub_title'];
				exit(json_encode(array('flag'=>1,'data'=>$data)));
			}else{
				exit(json_encode(array('flag'=>2)));
			}
		}else{
			exit(json_encode(array('flag'=>2)));
		}
	}

	public function actionConfirmStartLive(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$restart_time=Yii::app()->request->getParam('restart_time');
		$sub_title=Yii::app()->request->getParam('sub_title');
		$live_model=Yii::app()->request->getParam('live_model');
		$doteyId=Yii::app()->user->id;
		if($doteyId<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		if($archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(strlen($sub_title)>48){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Live theme cannot be more than 16 Chinese characters'))));
		}
		$archivesService=new ArchivesService();
		$restart_time=empty($restart_time)?time():strtotime($restart_time);
		$archives=$archivesService->getArchivesByArchivesId($archives_id);
		if(isset($archives['live_record'])&&$archives['live_record']){
			if($archives['live_record']['status']!=2&&$archives['live_record']['status']!=-1){
				exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Archives live_record not end'))));
			}
		}
		$result=$archivesService->createArchivesLive($doteyId,$archives_id,$restart_time,$sub_title);
		if(!$result||$result<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Live notice set failure'))));
		}else{
			$archivesService->startArchivesLive($doteyId,$archives_id,$live_model);
			exit(json_encode(array('flag'=>1,'data'=>array('sub_title'=>$sub_title,'start_time'=>date('H:i',$restart_time)),'message'=>Yii::t('archives','Live notice set successfully'))));
		}
	}

	public function actionStopLive(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$doteyId=Yii::app()->user->id;
		if($doteyId<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		if($archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByArchivesId($archives_id);
		if(isset($archives['live_record'])){
			if($archives['live_record']['status']==1){
				$data['live_time']=date('H:i',$archives['live_record']['live_time']);
				$data['sub_title']=$archives['live_record']['sub_title'];
				$data['duration']=$this->changeTimeType($archives['live_record']['duration']);
				exit(json_encode(array('flag'=>1,'data'=>$data)));
			}else{
				exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Live server is gone away'))));
			}
		}else{
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Archives is error'))));
		}
	}

	/**
	 *开始直播状态
	 *@author leiwei
	 */
	public function actionStartLive(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$live_model=Yii::app()->request->getParam('live_model');
		$doteyId=Yii::app()->user->id;
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		if($archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		$upload=new PipiFlashUpload();
		$upload->tmpFolder= 'dotey';
		$upload->realFolder = 'dotey';
		$upload->filePrefix = 'dotey_';
		$doteyCover=$upload->getSaveFile($doteyId,'small','display');
		$display=is_file($doteyCover)?0:1;
// 		if(!is_file($doteyCover)){
// 			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Dotey cover not exits'))));
// 		}
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByArchivesId($archives_id);
		if($archives['uid']!=$doteyId){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Not the archives owner'))));
		}
		if(empty($archives['live_record'])){
			exit(json_encode(array('flag'=>-1,'message'=>Yii::t('archives','Archives not set live notice'))));
		}
		if($archives['live_record']['status']!=0){
			exit(json_encode(array('flag'=>-1,'message'=>Yii::t('archives','Live broadcast has ended, to live'))));
		}
		$result=$archivesService->startArchivesLive($doteyId,$archives_id,$live_model);
		if(!$result||$result<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Archives data save failed'))));
		}
		exit(json_encode(array('flag'=>1,'data'=>array('display'=>$display),'message'=>Yii::t('archives','Archives is start'))));
	}

	/**
	 *结束直播状态
	 *@author leiwei
	 */
	public function actionConfirmStopLive(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$doteyId=Yii::app()->user->id;
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		if($archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByArchivesId($archives_id);
		if($archives['uid']!=$doteyId){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Not the archives owner'))));
		}
		if(empty($archives['live_record'])){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Archives not set live notice'))));
		}
		if($archives['live_record']['status']!=1){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Live broadcast has ended, to live'))));
		}
		$result=$archivesService->stopArchivesLive($doteyId,$archives_id);
		if(!$result||$result<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Archives data save failed'))));
		}
		exit(json_encode(array('flag'=>1,'message'=>Yii::t('archives','Archives is end'))));
	}

	public function actionGetArchivesLiveTime(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByArchivesId($archives_id);
		$time='00时00分';
		if(isset($archives['live_record']['duration'])){
			if($archives['live_record']['status']==1){
				$time=self::changeTimeType($archives['live_record']['duration']);
			}
		}
		$data['time']=$time;
		$data['status']=isset($archives['live_record']['status'])?$archives['live_record']['status']:0;
		exit(json_encode($data));
	}

	/**
	 * 修改通告
	 * @author leiwei
	 */
	public function actionModifyNotice(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$commonNotice=Yii::app()->request->getParam('commonNotice');
		$commonUrl=Yii::app()->request->getParam('commonUrl');
		$privateNotice=Yii::app()->request->getParam('privateNotice');
		$privateUrl=Yii::app()->request->getParam('privateUrl');
		$doteyId=Yii::app()->user->id;
		$commonNotice=$commonNotice=='不超过80个汉字'?'':$commonNotice;
		$commonUrl=($commonUrl=='http://'||$commonUrl=='')?'':$commonUrl;
		$privateNotice=$privateNotice=='不超过80个汉字'?'':$privateNotice;
		$privateUrl=($privateUrl=='http://'||$privateUrl=='')?'':$privateUrl;
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		if($archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(empty($commonNotice)&&empty($privateNotice)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','The public chat and private chat notice notice cannot be empty'))));
		}
		if(strlen($commonNotice)>240||strlen($privateNotice)>240){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','The public chat, private chat announcement or notice of not more than 80 Chinese characters'))));
		}

		$archivesService=new ArchivesService();
		$commonNotice&&$notices['notice']=$commonNotice;
		if($commonUrl&&$commonUrl!='http://'){
			$notices['url']=$commonUrl;
		}
		$privateNotice&&$notices['private_notice']=$privateNotice;
		if($privateUrl&&$privateUrl!='http://'){
			$notices['private_url']=$privateUrl;
		}
		$result=$archivesService->modifyArchivesNotice($archives_id,$notices);
		if(!$result||$result<=0){
			$msg=$archivesService->getError();
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives',$msg))));
		}else{
			$userJson=new UserJsonInfoService();
			$userInfo=$userJson->getUserInfo($doteyId,false);
			$notices['uid']=$doteyId;
			$notices['nickname']=$userInfo['nk'];
			$notices['rank']=$userInfo['dk'];
			exit(json_encode(array('flag'=>1,'data'=>$notices,'message'=>Yii::t('archives','Notice modify success'))));
		}
	}


	/**
	 * 发言控制
	 */
	public function actionChatSet(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$tourist_set=Yii::app()->request->getParam('tourist_set');
		$global_set=Yii::app()->request->getParam('global_set');
		$doteyId=Yii::app()->user->id;
		if($archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}

		$tourist_set=empty($tourist_set)?0:1;
		$global_set=empty($global_set)?0:1;
		$archivesService=new ArchivesService();
		$sets['tourist_set']=$tourist_set;
		$sets['global_set']=$global_set;
		$result=$archivesService->saveChatSet($archives_id,$sets);
		if(!$result){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Failed to set the speech control'))));
		}
		exit(json_encode(array('flag'=>1,'message'=>Yii::t('archives','The control set successfully'))));
	}

	/**
	 *转移观众
	 */
	public function actionMoveViewer(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$target_uid=Yii::app()->request->getParam('target_uid');
		$doteyId=Yii::app()->user->id;
		if($archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}

		$archivesService=new ArchivesService();
		$result=$archivesService->moveViewer($archives_id,$target_uid);
		if(!$result){
			$msg=$archivesService->getErrors();
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives',array_pop($msg)))));
		}else{
			exit(json_encode(array('flag'=>1,'message'=>Yii::t('archives','The audience transfer success'))));
		}

	}
	/**
	 *上传背景图片
	 */
	public function actionUploadCover(){
		//header('content-type:text/html charset:utf-8');
		$imgFile=CUploadedFile::getInstanceByName('avatar_big');
		if($imgFile->getType()==='image/jpeg'||$imgFile->getType()==='image/pjpeg'){
			if($imgFile->getSize()>500*1024){
				$message=Yii::t('common','The image size is less than 500kb');
				exit('<li id="msg"><p class="uping" id="errorMsg">'.$message.'</p></li>');
			}
			$doteyUpload=self::getUploadSingleton();
			if($filename = $imgFile->getName()){
				$extName = $imgFile->getExtensionName();
				$newName = uniqid().'.'.$extName;
				$uploadDir = dirname(dirname($doteyUpload->getTempFile())).DIR_SEP;
				if (!file_exists($uploadDir)){
					mkdir($uploadDir,0777,true);
				}
				$uploadfile = $uploadDir.$newName;
				if($imgFile->saveAs($uploadfile,true)){
					self::thumbImg($uploadfile,218,218);
					exit('<li id="msg">'.$newName.'</li>');
				}
			}else{
				$message=Yii::t('common','Picture upload failed');
				exit('<li id="msg"><p class="uping" id="errorMsg">'.$message.'</p></li>');
			}
		}else{
			$message=Yii::t('common','Must be JPG format');
			exit('<li id="msg"><p class="uping" id="errorMsg">'.$message.'</p></li>');
		}
		
		
	}
	
	public function actionShowCover(){
		$doteyId=Yii::app()->user->id;
		$upload=new PipiFlashUpload();
		$upload->tmpFolder= 'dotey';
		$upload->realFolder = 'dotey';
		$upload->filePrefix = 'dotey_';
		$doteyCover=$upload->getSaveFile($doteyId,'small','display');
		$contrller = Yii::app()->getController();
		$html='';
		if(!is_file($doteyCover)){
			$html.='<form id="cover_from" action="'.$this->createUrl('/dotey/uploadCover').'" target="coverframe" method="post" enctype="multipart/form-data">';
			$html.='<ul class="cover">';
			$html.='<li><label>当前：<em class="black">无封面图</em></label> <input type="file" name="avatar_big" id="coverImg"/></li>';
			$html.='<li>节目封面会出现在网站各推荐版块里支持JPG格式，上传大小不超过500kb</li>';
			$html.='<li id="coverloading" style="display:none"><p class="uping"><img src="'.$contrller->pipiFrontPath.'/fontimg/common/uploading.jpg"></p></li>';
			$html.='</ul>';
			$html.='<iframe id="perviewCover" src="'.$this->createUrl('/dotey/perviewCover').'"  width="220" height="340" frameborder="0" scrolling="no"></iframe>';
			$html.='</from>';
		}else{
			$coverImg=$upload->getFileUrl($doteyId,'small','display');
			$html.='<ul class="cover">';
			$html.='<li><div class="onpic"><img class="editorimg" id="editImg" src="'.$coverImg.'"/></div></li>';
			$html.=' <li>当不满意当前节目封面，请准备清晰靓照发送给你的导师（除初次上传，后续封面更新换由导师完成）</li>';
			$html.='<li><input class="surebtn" type="button" onclick="$.mask.hide(\'Covers\')" value="确&nbsp;&nbsp;&nbsp;&nbsp;定"></li>';
			$html.='</ul>';
		}
		exit($html);
	}
	
	
	public function actionPerviewCover(){
		$coverName=Yii::app()->request->getParam('coverName');
		$coverName=isset($coverName)?$coverName:'';
		$this->layout=false;
		$contrller = Yii::app()->getController();
		$clientScript = Yii::app()->getClientScript();
		$staticPath = $contrller->pipiFrontPath;
		if($coverName){
			$clientScript->registerScriptFile($staticPath.'/js/common/jquery.imgareaselect.pack.js?token='.$contrller->hash,CClientScript::POS_HEAD);
		}
		if($coverName){
			$doteyUpload=self::getUploadSingleton();
			$uploadDir = dirname(dirname($doteyUpload->getTempFile())).DIR_SEP;
			list($width, $height) = getimagesize($uploadDir.$coverName);
			$coverImg=Yii::app()->params['images_server']['url'].'/tmp/dotey/'.$coverName;
			$this->render('/archives/perview',array('coverImg'=>$coverImg,'coverName'=>$coverName,'width'=>$width,'height'=>$height));
		}else{
			$this->render('/archives/perview');
		}
	}

	public function actionConfirmCover(){
		$cover_x=Yii::app()->request->getParam('cover_x');
		$cover_y=Yii::app()->request->getParam('cover_y');
		$cover_w=Yii::app()->request->getParam('cover_w');
		$cover_h=Yii::app()->request->getParam('cover_h');
		$newCoverImg=Yii::app()->request->getParam('newCoverImg');
		$doteyId=Yii::app()->user->id;
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		if($cover_w-$cover_x<0||$cover_h-$cover_y<0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Dotey cover not exits'))));
		}
		$doteyUpload=self::getUploadSingleton();
		$orgImg=dirname(dirname($doteyUpload->getTempFile())).DIR_SEP.$newCoverImg;
		$newImg=$doteyUpload->getFile();
		$new_width = $cover_w-$cover_x;
		$new_height =$cover_h-$cover_y;
		$newSource = imagecreatetruecolor($new_width, $new_height);
		$source = imagecreatefromjpeg($orgImg);
		imagecopyresampled($newSource, $source, 0, 0,$cover_x,$cover_y, $new_width, $new_height, $new_width, $new_height);
		imageinterlace($newSource, 1);
		imagejpeg($newSource, $newImg, 100);
		imagedestroy($newSource);
		imagedestroy($source);
		self::thumbImg($newImg,205,121);
		@unlink($orgImg);
		$doteyService=new DoteyService();
		if($doteyService->getDoteySaveFile($doteyId,'small','display')){
			$dotey['uid']=$doteyId;
			$dotey['update_desc']['display_small'] = time();
			$doteyService->saveUserDoteyBase($dotey);
		}
		exit(json_encode(array('flag'=>1,'message'=>Yii::t('archives','Cover save success'))));
	}

	protected function thumbImg($orgImg,$maxWidth,$maxHeight){
		list($width, $height) = getimagesize($orgImg);
		if($maxWidth&&$width<$maxWidth){
			$width_scale=$maxWidth/$width;
			$with_tag=true;
		}
		if($maxWidth&&$width>=$maxWidth){
			$width_scale=$maxWidth/$width;
			$with_tag=true;
		}
		if($maxHeight&&$height<$maxHeight){
			$heigth_scale=$maxHeight/$height;
			$height_tag=true;
		}
		if($maxHeight&&$height>=$maxHeight){
			$heigth_scale=$maxHeight/$height;
			$height_tag=true;
		}
		if($with_tag&& $height_tag){
			if($width_scale<$heigth_scale){
				$scale=$width_scale;
			}else{
				$scale=$heigth_scale;
			}
		}
		if($with_tag&&!$height_tag){
			$scale=$width_scale;
		}
		if($height_tag&&!$with_tag){
			$scale=$heigth_scale;
		}
		$newWidth=number_format($width*$scale,2);
		$newHeigth=number_format($height*$scale,2);
		$image_p = imagecreatetruecolor($newWidth, $newHeigth);
		$white = imagecolorallocate($image_p, 255, 255, 255);
		imagefill($image_p, 0, 0, $white);
		$image = imagecreatefromjpeg($orgImg);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidth, $newHeigth, $width, $height);
		imagejpeg($image_p, $orgImg, 100);
	}



	/**
	 *上传背景图片
	 */
	public function actionUploadBack(){
		header('content-type:text/html charset:utf-8');
		$imgFile=CUploadedFile::getInstanceByName('backImg');
		if($imgFile->getType()!='image/jpeg'){
			$message=Yii::t('common','Must be JPG format');
			exit('<li id="msg"><p class="uping">'.$message.'</p></li>');
		}
		if($imgFile->getSize()>500*1024){
			$message=Yii::t('common','The image size is less than 500kb');
			exit('<li id="msg"><p class="uping">'.$message.'</p></li>');
		}
		if($filename = $imgFile->getName()){
			$extName = $imgFile->getExtensionName();
			$newName = uniqid().'.'.$extName;
			$uploadDir = ROOT_PATH."images".DIR_SEP.'background'.DIR_SEP;
			if (!file_exists($uploadDir)){
				mkdir($uploadDir,0777,true);
			}
			$uploadfile = $uploadDir.$newName;
			if($imgFile->saveAs($uploadfile,true)){
				$message=Yii::t('common','Picture upload successed');
				exit('<li id="msg"><p class="uping">'.$message.'<input type="hidden" name="bgImg" id="bgImg" value="'.$newName.'"></p></li>');
			}
		}else{
			$message=Yii::t('common','Picture upload failed');
			exit('<li id="msg"><p class="uping">'.$message.'</p></li>');
		}
	}
	
	public function actionShowBgSet(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$archviesService=new ArchivesService();
		$defaultBg=$archviesService->getArchivesBackGround();
		$archives=$archviesService->getArchivesByArchivesId($archives_id);
		if(isset($archives['background'])&&!empty($archives['background'])){
			$background=unserialize($archives['background']);
		}else{
			$background=$defaultBg[0];
		}
		$bgHtml='';
		foreach($defaultBg as $key=>$row){
			if($background['big']==$row['big']){
				$bgHtml.='<li class="bgseted"><div class="bgsetpic"><a href="javascript:void(0)" onclick="Show.DefaultBgSet('.$key.')" title="'.$row['tittle'].'"><img src="'.Yii::app()->params['images_server']['url'].'/background/'.$row['small'].'"><em class="cured"></em></a></div><p>'.$row['title'].'</p></li>';
			}else{
				$bgHtml.='<li><div class="bgsetpic"><a href="javascript:void(0)" onclick="Show.DefaultBgSet('.$key.')" title="'.$row['tittle'].'"><img src="'.Yii::app()->params['images_server']['url'].'/background/'.$row['small'].'"></a></div><p>'.$row['title'].'</p></li>';
			}
		}
		exit($bgHtml);
	}

	public function actionBgSet(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$bgImg=Yii::app()->request->getParam('bgImg');
		if($archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(empty($bgImg)){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Upload the background map JPG format or size more than 500Kb, please check it'))));
		}
		$archivesService=new ArchivesService();
		$archives['archives_id']=$archives_id;
		$archives['background']=serialize(array('top'=>$paddtop,'big'=>$bgImg));
		$result=$archivesService->saveArchives($archives);
		if(!$result){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Archives background set failed'))));
		}
		$data['bgurl']=Yii::app()->params['images_server']['url'].'/background/'.$bgImg;
		$data['top']=$paddtop;
		exit(json_encode(array('flag'=>1,'data'=>$data,'message'=>Yii::t('archives','Archives background set successed'))));
	}

	public function actionDefaultBgSet(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$bgImg_id=Yii::app()->request->getParam('bgImg_id');
		if($bgImg_id<0||$archives_id<=0){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$archivesService=new ArchivesService();
		$background=$archivesService->getArchivesBackGround();
		if(!isset($background[$bgImg_id])){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('common','Parameters are wrong'))));
		}
		$archives['archives_id']=$archives_id;
		$archives['background']=serialize($background[$bgImg_id]);
		$result=$archivesService->saveArchives($archives);
		if(!$result){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('archives','Archives background set failed'))));
		}
		$data['bgurl']=Yii::app()->params['images_server']['url'].'/background/'.$background[$bgImg_id]['big'];
		$data['bgcolor']=$background[$bgImg_id]['bgcolor'];
		exit(json_encode(array('flag'=>1,'data'=>$data,'message'=>Yii::t('archives','Archives background set successed'))));

	}


	/**
	 * 主播控制是否允许点歌
	 */
	public function actionAllowSong(){
		$archives_id=Yii::app()->request->getParam('archives_id');
		$uid=Yii::app()->user->id;
		if(!$this->isLogin){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('user','You are not logged'))));
		}
		$archivesService=new ArchivesService();
		$archives=$archivesService->getArchivesByArchivesId($archives_id);
		if($uid!=$archives['uid']){
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('doteySong','No permission to operate'))));
		}
		$doteySongService=new DoteySongService();
		$allow=$doteySongService->getArchivesAllowSong($archives_id);
		$status=($allow==1)?2:1;
		if($doteySongService->saveArchivesAllowSong($archives_id,$status)){
			if($status==2){
				exit(json_encode(array('flag'=>1,'message'=>Yii::t('doteySong','Archives forbid demand song'))));
			}else{
				exit(json_encode(array('flag'=>2,'message'=>Yii::t('doteySong','Archives allow demand song'))));
			}

		}else{
			exit(json_encode(array('flag'=>0,'message'=>Yii::t('doteySong','Demand song status modify failed'))));
		}
	}

	/**
	 * 主播申请
	 * @author hexin
	 */
	public function actionApply(){
		$contrller = Yii::app()->getController();
		$clientScript = Yii::app()->getClientScript();
		$staticPath = $contrller->pipiFrontPath;
		$clientScript->registerCssFile($staticPath.'/css/dotey/apply.css?token='.$contrller->hash);
		$clientScript->registerCssFile($staticPath.'/css/common/boxy.css?token='.$contrller->hash);
		$clientScript->registerScriptFile($staticPath.'/js/area/city_data.js?token='.$contrller->hash);
		$clientScript->registerScriptFile($staticPath.'/js/area/datajs.js?token='.$contrller->hash);
		$clientScript->registerScriptFile($staticPath.'/js/common/jquery.validate.js?token='.$contrller->hash);
		$clientScript->registerScriptFile($staticPath.'/js/common/jquery.boxy.js?token='.$contrller->hash);
		$clientScript->registerScriptFile($contrller->pipiFrontPath.'/swf/archives/swfobject.js?token='.$contrller->hash,CClientScript::POS_HEAD);

		$uid = Yii::app()->user->id;
		$proxy_uid = intval(Yii::app()->request->getParam('p', 0)); //代理uid
		$finder_uid = intval(Yii::app()->request->getParam('f', 0)); //星探uid
		$tutor_uid = intval(Yii::app()->request->getParam('t', 0)); //导师uid
		$edit = intval(Yii::app()->request->getParam('edit', 0));
		$doteyService = new DoteyService();
		if($this->isLogin){
			if($this->isDotey){
				$this->redirect('/'.$uid);
				Yii::app()->end();
			}
			$applyInfo = $doteyService->getApplyDoteyInfo($uid);
			
			if(!empty($applyInfo) && !($edit && $applyInfo['status'] == APPLY_STATUS_WAITING)){
				$this->renderApplyResult($uid, $applyInfo);
			}
			$proxy_uid = $applyInfo['proxy_uid'] > 0 ? $applyInfo['proxy_uid'] : $proxy_uid;
			$finder_uid = $applyInfo['finder_uid'] > 0 ? $applyInfo['finder_uid'] : $finder_uid;
			$tutor_uid = $tutor_uid > 0 ? $tutor_uid : $applyInfo['tutor_uid'];
			$applyInfo['cover'] = '';
			if(is_file($this->getUploadSingleton()->getFile())){
				$applyInfo['cover'] = $this->getUploadSingleton()->getFileUrl();
			}
		}

		if(Yii::app()->request->getIsPostRequest()){
			if(empty($_POST)){
				try{
					if($this->getUploadSingleton()->uploadFile()){
						exit();
					}
				}catch (Exception $e){
					$filename = DATA_PATH.'runtimes/dotey_apply_upload_error.log';
					error_log(date("Y-m-d H:i:s")."主播申请上传失败：".var_export($e, true)."\n\r",3,$filename);
				}
			}else{
				$doteyApplyForm = new DoteyApplyForm();
				foreach($_POST as $k => $v){
					$doteyApplyForm -> $k = $v;
				}
				if($doteyApplyForm->validate()){
					$data = array(
						'realname'	=> $doteyApplyForm->realname,
						'gender'	=> $doteyApplyForm->gender,
						'mobile'	=> $doteyApplyForm->mobile,
						'qq'		=> $doteyApplyForm->qq,
						'id_card'	=> $doteyApplyForm->id_card,
						'bank_user'	=> $doteyApplyForm->bank_user,
						'bank'		=> $doteyApplyForm->bank,
						'bank_account'	=> $doteyApplyForm->bank_account,
						'type'		=> 4,
						'has_experience'=> $doteyApplyForm->has_experience,
						'live_address'	=> $doteyApplyForm->live_address,
						'proxy_uid'	=> $proxy_uid,
						'tutor_uid'	=> $doteyApplyForm->tutor_uid ? $doteyApplyForm->tutor_uid : $tutor_uid,
						'finder_uid'=> $finder_uid,
					);
					if($doteyService -> doteyApply($uid, $data)){
						if(!empty($doteyApplyForm->cover)){
							$this->getUploadSingleton()->storeFile();
							$dotey = array(
								'uid'	=> $uid,
								'update_desc' => array('display_small' => time()),
							);
							$doteyService->saveUserDoteyBase($dotey);
						}
						$this->renderApplyResult($uid, $data);
					}else{
						$error['system'][] = Yii::t('common','System error');
					}
				}else{
					$error = $doteyApplyForm->getErrors();
				}
			}
		}

		$proxy = $finder = array();

		if($this->isLogin){
			$userService = new UserService();
			$user = $userService->getUserBasicByUids(array($uid));
			$user = $user[$uid];
		}

		$proxyService = new DoteyService();
		if($proxy_uid){
			$proxy = $proxyService -> getProxy($proxy_uid);
		}
		if($finder_uid){
			$finder = $proxyService -> getProxy($finder_uid, DOTEY_MANAGER_STAR);
		}

		$tutors = $proxyService -> getProxyOrTutorList(DOTEY_MANAGER_TUTOR, 1, true);

		$data = array(
			'isLogin' => $this->isLogin,
			'user'	=> $this->isLogin ? $user : array(),
			'proxy'	=> $proxy,
			'finder'=> $finder,
			'tutors'=> $tutors,
			'tutor_uid' =>$tutor_uid,
			'error'	=> $error,
			'applyInfo'	=> $applyInfo,
			'edit'	=> $edit,
			'skills' => $proxyService->getDoteySkill(),
			'flashHtml' => $this->getUploadSingleton()->renderHtml(),
		);
		$this->render('apply', $data);
	}

	private function renderApplyResult($uid, $applyInfo = null){
		if(!empty($applyInfo) && ($applyInfo['status'] == APPLY_STATUS_WAITING)){
			$doteyService = new DoteyService();
			$tutor = $doteyService->getTutor($applyInfo['tutor_uid']);
			$tutors = $doteyService -> getProxyOrTutorList(DOTEY_MANAGER_TUTOR, 1, true);
			$data = array(
				'applyInfo'	=> $applyInfo,
				'tutor'		=> $tutor,
				'tutors'	=> $tutors,
			);
			$this->render('success', $data);
		}elseif(!empty($applyInfo) && ($applyInfo['status'] == APPLY_STATUS_FACE || $applyInfo['status'] == APPLY_STATUS_SUCCESS)){
			$this->redirect('/'.$applyInfo['uid']);
		}elseif(!empty($applyInfo) && $applyInfo['status'] == APPLY_STATUS_REFUES){
			$data = array(
				'applyInfo'	=> $applyInfo,
			);
			$this->render('refuse', $data);
		}elseif(!empty($applyInfo) && $applyInfo['status'] == -1){
			$this->render('refuse', array('forbid' => 1));
		}
		Yii::app()->end();
	}

	/**
	 * 主播用户协议
	 */
	public function actionAgreement(){
		if(!$this->isLogin) $this->redirect('/');

		$contrller = Yii::app()->getController();
		$clientScript = Yii::app()->getClientScript();
		$staticPath = $contrller->pipiFrontPath;
		$clientScript->registerCssFile($staticPath.'/css/dotey/apply.css?token='.$contrller->hash);

		$this->render('agreement');
	}
	
	public function actionUpload(){
		if(!$this->isLogin && !Yii::app()->request->isFlashRequest) exit();
		$title = Yii::app()->request->getParam('title');
		$data = array(
			'flashHtml' => $this->getUploadSingleton()->renderHtml(),
			'title'		=> $title,
		);
		$this->renderPartial('upload', $data);
	}
	
	public function actionDoteyLogout(){
		if(!Yii::app()->user->isGuest)
			Yii::app()->user->logout();
		$returnUrl = Yii::app()->request->getUrlReferrer();
		$returnUrl = $returnUrl ? $returnUrl : Yii::app()->user->returnUrl;
		exit(json_encode(array('flag'=>1,'url'=>$this->getTargetHref($returnUrl,false,true))));
	}

	protected function getUploadSingleton(){
		if(!self::$upload){
			self::$upload = new PipiImageUpload();
			$uid = Yii::app()->user->id;
			self::$upload -> setDir($uid, 'dotey', 'display_dotey_small');
		}
		return self::$upload;
	}

	protected function changeTimeType($seconds){
		if($seconds>86400){
			$hour=intval($seconds/3600);
			$minute=$seconds-$hour*3600;
			$time =$hour.'时'. gmstrftime('%M分', $minute);
		}else{
			$time = gmstrftime('%H时%M分', $seconds);
		}
		return $time;
	}

}
?>