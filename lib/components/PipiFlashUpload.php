<?php
error_reporting(0);
/**
 * ucenter flash文件上传
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: PipiFlashUpload.php 894 2010-12-28 07:55:25Z suqian $ 
 * @package
 */
class PipiFlashUpload extends CApplicationComponent
{
	/**
	 * @var string 临时目录
	 */
	public $tmpFolder = 'avatars';
	
	/**
	 * @var string 头像目录
	 */
	public $realFolder = 'avatars';
	/**
	 * 
	 * @var string 文件前缀
	 */
	public $filePrefix = 'avatar_';
	
	/**
	 * 第一步：上传原始图片文件
	 * @param int $id 按ID规则存储
	 * return boolean
	 */
	public function uploadFile( $id ){
		header("Expires: 0");
		header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		header("Pragma: no-cache");

		if($id <= 0){
			return -1;
		}
		// 检查上传文件的有效性  No photograph be upload!
		if ( empty($_FILES['Filedata']) ) {
			return -3; 
		}

		$tmpPath = $this->getTmpSavePath();
		if (!is_dir( $tmpPath ) ) {
			mkdir( $tmpPath, 0777, true );
		}

		$tmpPath .= $this->filePrefix.$id;
		if (is_file($tmpPath) ) {
			unlink($tmpPath);
		}
		if($_FILES['Filedata']['tmp_name']){
			// 把上传的图片文件保存到预定位置
			if (copy($_FILES['Filedata']['tmp_name'], $tmpPath) || move_uploaded_file($_FILES['Filedata']['tmp_name'], $tmpPath)) {
				unlink($_FILES['Filedata']['tmp_name']);
				list($width, $height, $type, $attr) = getimagesize($tmpPath);
				if ( $width < 10 || $height < 10 || $width > 3000 || $height > 3000 || $type == 4 ) {
					unlink($tmpPath);
					return -2; // Invalid photograph!
				}
			} else {
				unlink($_FILES['Filedata']['tmp_name']);
				return -4; // Can not write to the data/tmp folder!
			}
		}else{
			return -2;
		}

		// 用于访问临时图片文件的 url
		$tmpUrl = $this->getTmpSaveUrl().$this->filePrefix.$id;
		//临时解决了下上传后立即显示图片，导致的上传和显示不在同一台服务器的问题，但这个方法有点慢，后面想到好的办法再来优化
		exec('/data/webservice/crontab/letianImgRsync/rsync.sh');
		return $tmpUrl;
	}

	/**
	 * 第二步：上传分割后的三个图片数据流
	 * 
	 * @edit guoshaobo 为避免出现动态图, 将gif格式的图片同意转换为jpg格式
	 * 
	 * @param int $id 对象ID
	 * @param int $type 图片类型
	 * @param string|array $callback 处理big，middle，small三个图片的自定义处理方法，并返回三个图片的地址
	 * @return stirng 返回xml结果集
	 */
	public function rectFile( $id, $type = '', $callback = ''){
		header("Expires: 0");
		header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		header("Pragma: no-cache");
		header("Content-type: application/xml; charset=utf-8");
		
		if($id <= 0 ) {
			return '<root><message type="error" value="-1" /></root>';
		}
		// 从 $_POST 中提取出三个图片数据流
		$bigavatar    = $this->decodeFlash( Yii::app()->request->getPost('avatar1'));
		$middleavatar = $this->decodeFlash( Yii::app()->request->getPost('avatar2') );
		$smallavatar  = $this->decodeFlash( Yii::app()->request->getPost('avatar3') );
		if ( !$bigavatar || !$middleavatar || !$smallavatar ) {
			return '<root><message type="error" value="-2" /></root>';
		}
		$this->setSaveDirPath($id);
		
		$tmpPath = $this->getTmpSavePath().$this->filePrefix.$id;
		$success = 1;
		if($type != '' && $callback != ''){
			//$callback 必须要返回array('big' => string, 'middle' => string, 'small' => string)格式
			$avatarfiles = call_user_func_array($callback, array('id' => $id, 'type' => $type, 'src' => $tmpPath, 'dir' => $this->getSaveDirPath($id), 'big' => $bigavatar));
			foreach(array('big', 'middle', 'small') as $v){
				if(!isset($avatarfiles[$v]) || empty($avatarfiles[$v])){
					${$v.'avatarfile'} = $this->getSaveFile($id,$v,$type);
					$fp = fopen(${$v.'avatarfile'}, 'wb');
					fwrite($fp, ${$v.'avatar'});
					fclose($fp);
				}else{
					${$v.'avatarfile'} = $avatarfiles[$v];
				}
			}
			
		}else{
			// 保存为图片文件
			$bigavatarfile    = $this->getSaveFile($id,'big',$type);
			$middleavatarfile = $this->getSaveFile($id,'middle',$type);
			$smallavatarfile  = $this->getSaveFile($id,'small',$type);
	
			$fp = fopen($bigavatarfile, 'wb');
			fwrite($fp, $bigavatar);
			fclose($fp);
	
			$fp = fopen($middleavatarfile, 'wb');
			fwrite($fp, $middleavatar);
			fclose($fp);
	
			$fp = fopen($smallavatarfile, 'wb');
			fwrite($fp, $smallavatar);
			fclose($fp);
		}

		// 验证图片文件的正确性
		$biginfo    = getimagesize($bigavatarfile);
		$middleinfo = getimagesize($middleavatarfile);
		$smallinfo  = getimagesize($smallavatarfile);
		if ( !$biginfo || !$middleinfo || !$smallinfo || $biginfo[2] == 4 || $middleinfo[2] == 4 || $smallinfo[2] == 4 ) {
			file_exists($bigavatarfile) && unlink($bigavatarfile);
			file_exists($middleavatarfile) && unlink($middleavatarfile);
			file_exists($smallavatarfile) && unlink($smallavatarfile);
			$success = 0;
		}
		// 如果是GIF格式的图片, 替换成jpg格式, 避免有动态图出现  @edit by guoshaobo
		if($success==1 && $biginfo[2]=='1' && (imagetypes() & IMG_GIF)){
			@imagejpeg(@imagecreatefromgif($smallavatarfile), $smallavatarfile);
			@imagejpeg(@imagecreatefromgif($middleavatarfile), $middleavatarfile);
			@imagejpeg(@imagecreatefromgif($bigavatarfile), $bigavatarfile);
		}

		// 删除临时存储的图片
		if(is_file($tmpPath)){
			unlink($tmpPath);
		}
		return '<?xml version="1.0" ?><root><face success="' . $success . '"/></root>';
	}



	// 处理 HTTP Request
	// 返回值：如果是可识别的 request，处理后返回 true；否则返回 false。
	public function processRequest($input, $step = '', $type = '', $callback = ''){
		// 从 input 参数里拆解出自定义参数
		if($input){
			$data = array();
			parse_str($input, $data );
			$id = intval($data['uid']);
		}
		
		if ( $step == 'uploadavatar') {
			ob_end_clean();
			// 第一步：上传原始图片文件
			echo $this->uploadFile( $id );
			return true;
			

		} else if ( $step == 'rectavatar') {
			ob_end_clean();
			// 第二步：上传分割后的三个图片数据流
			echo $this->rectFile($id, $type, $callback);
			return true;
		}
		return false;
	}

	/**
	 * 渲染HTML
	 * 
	 * @param string $id 对象ID
	 * @param string $data 自定义参数
	 */
	public function renderHtml( $id, $data = array()){
		// 把需要回传的自定义参数都组装在 input 里
		$input = urlencode( "uid={$id}" . (empty($data) ? '' : http_build_query($data)) );
		$uc_api = urlencode( $this->getApiUrl() );
		$urlCameraFlash = $this->getFlashUrl()."?ucapi={$uc_api}&input={$input}&avatartype=virtual";
		$urlCameraFlash = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="447" height="477" id="mycamera" align="middle">
				<param name="allowScriptAccess" value="always" />
				<param name="scale" value="exactfit" />
				<param name="wmode" value="transparent" />
				<param name="quality" value="high" />
				<param name="bgcolor" value="#ffffff" />
				<param name="movie" value="'.$urlCameraFlash.'" />
				<param name="menu" value="false" />
				<embed src="'.$urlCameraFlash.'" quality="high" bgcolor="#ffffff" width="447" height="477" name="mycamera" align="middle" allowScriptAccess="always" allowFullScreen="false" scale="exactfit"  wmode="transparent" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
			</object>';
		return $urlCameraFlash;
	}

	/**
	 * 取得和flash通信的URL
	 * @return string
	 */
	public function getApiUrl(){
		return  Yii::app()->request->getHostInfo().Yii::app()->request->getRequestUri();
	}

	/**
	 * 从客户端访问图片的 url
	 * 
	 * @param int $uid
	 * @param int $size
	 * @param string $type 某用户其他非头像用途的图片类型，用来扩展一个用户可以拥有多种用途的图片
	 * @return string
	 */
	public function getScriptUrl($id, $size='middle',$type = ''){
		return $this->getFile($id,$size,$type);
	}
	/**
	 * 从客户端访问图片的 url
	 * 
	 * @param int $uid
	 * @param int $size
	 * @param string $type 某用户其他非头像用途的图片类型，用来扩展一个用户可以拥有多种用途的图片
	 * @return string
	 */
	public function getFileUrl( $id, $size='middle',$type = ''){
		return  $this->getRealSaveUrl().$this->getFile($id,$size,$type);
	}
	
	/**
	 * 获取文件
	 * 
	 * @author supeng
	 * @param unknown_type $id
	 * @param unknown_type $size
	 * @param unknown_type $type
	 * @return string
	 */
	public function getFile($id, $size='middle',$type = ''){
		$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'big';
		$id =$this->formatId($id);
		if(!empty($type)) $typeadd = '_'.$type;
		return  $this->formatDir($id).DIR_SEP.substr($id, -2).$typeadd."_{$this->filePrefix}{$size}.jpg";
	}

	/**
	 * 获取服务器临时存储的URL
	 * 
	 * @return string
	 */
	public function getTmpSaveUrl(){
		return trim(Yii::app()->params['images_server']['url'],DIR_SEP).'/tmp/'.$this->tmpFolder.DIR_SEP;
	}
	
	/**
	 * 获取服务器临时存储路径
	 * 
	 * @return string
	 */
	public function getTmpSavePath(){
		return IMAGES_PATH.'tmp'.DIR_SEP.$this->tmpFolder.DIR_SEP;
	}
	
	/**
	 * 获取服务器真实存储的URL
	 * 
	 * @return string
	 */
	public function getRealSaveUrl(){
		return trim(Yii::app()->params['images_server']['url'],DIR_SEP).DIR_SEP.$this->realFolder.DIR_SEP;
	}
	/**
	 * 获取服务器真实存储路径
	 * 
	 * @return string
	 */
	public function getRealSavePath(){
		return IMAGES_PATH.$this->realFolder.DIR_SEP;
	}
	
	/**
	 * 获取上传的flash文件
	 * 
	 * @return string
	 */
	public function getFlashUrl(){
		return trim(Yii::app()->params['images_server']['url'],DIR_SEP).'/flash/flashUpload/camera.swf';
	}
	/**
	 * 从客户端访问头像图片的 url
	 * 
	 * @param int $uid
	 * @param int $size
	 * @param string $type 某用户其他非头像用途的图片类型，用来扩展一个用户可以拥有多种用途的图片
	 * @return string 返回用户头像
	 */
	public function getSaveFile( $id, $size='middle', $type = '' ){
		$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'big';
		$id =$this->formatId($id);
		if(!empty($type)) $typeadd = '_'.$type;
		return  $this->getSaveDirPath($id).DIR_SEP.substr($this->formatId($id), -2).$typeadd."_{$this->filePrefix}{$size}.jpg";
	}
	
	/**
	 * 按ID存储图片存储路径
	 * 
	 * @param int $id 对象ID
	 * @return boolean true表示成功 false表示失败
	 */
	public function setSaveDirPath($id) {
		$dir = $this->getSaveDirPath($id);
		if(!is_dir($dir))
			return mkdir($dir,07777,true);
		return true;
	}
	/**
	 * 按ID获取图片存储路径
	 * 
	 * @param int $id 对象ID
	 * @return string 返回图片路径
	 */
	public function getSaveDirPath($id){
		return $this->getRealSavePath().$this->formatDir($id);
	}
	/**
	 * 格式化ID，使ID有九位数，按ID位数存储图片路径
	 * 
	 * @param int $id 对象ID
	 * @return string 返回格式化后的图片
	 */
	private function formatId($id){
		return sprintf("%09d", (int)$id);
	}
	
	/**
	 * 格式化路径
	 * 
	 * @param int $id 对象ID
	 * @return string 返回格式化后的图片
	 */
	private function formatDir($id){
		$id = $this->formatId($id);
		return substr($id, 0, 3).DIR_SEP.substr($id, 3, 2).DIR_SEP. substr($id, 5, 2);
	}
	
	private function decodeFlash($s) {
		$r = '';
		$l = strlen($s);
		for($i=0; $i<$l; $i=$i+2) {
			$k1 = ord($s[$i]) - 48;
			$k1 -= $k1 > 9 ? 7 : 0;
			$k2 = ord($s[$i+1]) - 48;
			$k2 -= $k2 > 9 ? 7 : 0;
			$r .= chr($k1 << 4 | $k2);
		}
		return $r;
	}
}