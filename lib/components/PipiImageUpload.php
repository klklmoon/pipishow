<?php
error_reporting(0);
/**
 * flash通用图片文件和摄像头上传
 * @author hexin
 * @version $Id: PipiImageUpload.php 894 2010-12-28 07:55:25Z hexin $ 
 * @package
 */
class PipiImageUpload extends CApplicationComponent
{
	/**
	 * @var int 用户uid
	 */
	public $uid = 0;
	/**
	 * @var string 图像目录
	 */
	public $dir = 'dotey';
	/**
	 * @var string 文件类型
	 */
	public $type = 'display_dotey_small';
	/**
	 * 
	 * @var string 文件名后缀，默认缩略图全是.jpg
	 */
	public $ext = '.jpg';
	
	/**
	 * 上传前必须先定义图片目录及图片的类别名
	 * @param int $uid
	 * @param string $dir
	 * @param string $type 可带后缀，如果带文件后缀则指定为文件后缀，如果为带文件后缀则默认.jpg
	 */
	public function setDir($uid, $dir='dotey', $type = 'display_dotey_small'){
		$this->uid = $uid;
		$this->dir = $dir;
		$ext = strrchr($type, '.');
		if($ext){
			$this->ext = $ext;
			$this->type = str_replace($ext, '', $type);
		}else{
			$this->type = $type;
		}
	}
	
	/**
	 * 上传最终的图片文件
	 * @param boolean $temp 是否暂时上传在临时文件内，最后确认后再移动到目标目录
	 * return boolean
	 */
	public function uploadFile($temp = true){
		header("Expires: 0");
		header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
		header("Pragma: no-cache");
		header("Content-type: application/xml; charset=utf-8");

		$data = file_get_contents ( 'php://input' ) ? file_get_contents ( 'php://input' ) : gzuncompress ( $GLOBALS ['HTTP_RAW_POST_DATA'] );
		if (! empty ( $data )) {
			if($temp) $image = $this->getTempFile();
			else $image = $this->getFile();
			@file_put_contents($image, $data);
			//png流转jpg图片
			$img = imagecreatefrompng($image);
			imagejpeg($img, $image);
		}
		//临时解决了下上传后立即显示图片，导致的上传和显示不在同一台服务器的问题，但这个方法有点慢，后面想到好的办法再来优化
		exec('/data/webservice/crontab/letianImgRsync/rsync.sh');
		return $image;
	}
	
	/**
	 * 最后确认把上传的临时文件转移到目标目录
	 * @return string
	 */
	public function storeFile(){
		$file = $this->getFile();
		$tmp = $this->getTempFile();
		@copy($tmp, $file);
		@unlink($tmp);
		return $file;
	}

	/**
	 * 渲染HTML
	 * @param booean $setSize 是否需要定制图片的最终尺寸，flash会自动切到理想的尺寸最后再上传
	 * @param int $width 需要的经切割后，网页展示的宽度
	 * @param int $height 需要的经切割后，网页展示的高度
	 * @return string
	 */
	public function renderHtml($width = 220, $height = 130){
		$flashPath = '/statics/swf/ZPhoto.swf';
		$flash = '
			<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="304" height="380" id="ZPhoto" align="middle">
				<param name="allowScriptAccess" value="always" />
				<param name="scale" value="exactfit" />
				<param name="wmode" value="transparent" />
				<param name="quality" value="high" />
				<param name="bgcolor" value="#ffffff" />
				<param name="movie" value="'.$flashPath.'" />
				<param name="menu" value="false" />
				<embed src="'.$flashPath.'" quality="high" bgcolor="#ffffff" width="304" height="380" name="mycamera" align="middle" allowScriptAccess="always" allowFullScreen="false" scale="exactfit"  wmode="transparent" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
			</object>
			<script type="text/javascript">
			function setplayer(){
				
			}
			function setSize(){
				return ['.$width.', '.$height.'];		
			}
			</script>';
		return $flash;
	}
	
	/**
	 * 获取图片在服务器上的完整文件地址
	 * @return string
	 */
	public function getFile(){
		return IMAGES_PATH.$this->getFilePath(true);
	}
	
	/**
	 * 获取图片在服务器上的完整临时文件地址
	 * @return string
	 */
	public function getTempFile(){
		$dir = IMAGES_PATH.'tmp'.DIR_SEP.$this->dir;
		if(!is_dir($dir)) mkdir($dir,07777,true);
		return $dir.DIR_SEP.$this->uid.DIR_SEP.'_'.$this->type.$this->ext;
	}

	/**
	 * 从网络访问未经过CDN的图片的完整 url
	 * @return string
	 */
	public function getFileUrl(){
		return  $this->getRealSaveUrl().$this->getFilePath();
	}
	
	/**
	 * 刚刚上传未经过CDN的临时图片的 完整url
	 * @return string
	 */
	public function getTempFileUrl(){
		return  $this->getRealSaveUrl().'tmp'.DIR_SEP.$this->dir.DIR_SEP.$this->uid.'_'.$this->type.$this->ext;
	}
	
	/**
	 * 后台图片访问地址，后台服务器只有一台，这里避免访问问题是直接指定url
	 * @return string
	 */
	public function getAdminFileUrl(){
		$currentPageUrl = Yii::app()->request->getHostInfo().Yii::app()->request->getRequestUri();
		$urls = parse_url($currentPageUrl);
		$hosts = explode('.',$urls['host']);
		return  'http://'.strtolower($hosts[0]).DOMAIN.'/images/'.$this->getFilePath();
	}
	
	/**
	 * 从网络访问经过CDN的图片的完整 url
	 * @param array $updateDesc 图片的最后上传时间，可从redis里获取也可从数据库取
	 * @return string
	 */
	public function getCdnUrl($updateDesc = array()){
		if(Yii::app()->params['images_server']['cdn_open']){
			$timestamp = 0;
			$key = str_replace('_dotey', '', $this->type);
			if($updateDesc && isset($updateDesc[$key])){
				$timestamp = $updateDesc[$key];
			}
			if( $timestamp > 0  && (time() - $timestamp > Yii::app()->params['images_server']['cdn_time'])){
				return trim(Yii::app()->params['images_server']['cdn_url'],DIR_SEP).DIR_SEP.$this->getFilePath();
			}elseif($timestamp == 0){
				return trim(Yii::app()->params['images_server']['cdn_url'],DIR_SEP).DIR_SEP.'default'.DIR_SEP.'dotey'.DIR_SEP.'dotey_display_default_small.png';
			}
			return $this->getFileUrl();
		}else return $this->getFileUrl();
	}
	
	/**
	 * 获取服务器真实存储的URL的images域名目录
	 * @return string
	 */
	private function getRealSaveUrl(){
		return trim(Yii::app()->params['images_server']['url'],DIR_SEP).DIR_SEP;
	}

	
	/**
	 * 获取图片文件相对于images目录下的保存路径，不是完整路径
	 * @param boolean $write 是否需要创建目录
	 * @return string
	 */
	private function getFilePath($write = false){
		$dir = $this->dir.DIR_SEP.substr($this->formatUid(), 0, -3);
		if($write && !is_dir(IMAGES_PATH.$dir)) mkdir(IMAGES_PATH.$dir,07777,true);
		return $dir.substr($this->formatUid(), -3).'_'.$this->type.$this->ext;
	}
	
	/**
	 * 格式化路径，使UID有九位数，按UID位数存储图片路径
	 * @return string 返回格式化后的图片目录根据uid序列化的路径
	 */
	private function formatUid(){
		$id = sprintf("%09d", (int)$this->uid);
		return substr($id, 0, 3).DIR_SEP.substr($id, 3, 2).DIR_SEP.substr($id, 5, 2).DIR_SEP.substr($id, 7, 2);
	}
	
	/**
	 * 生成缩略图
	 * @param string $src 源图
	 * @param string $dst 目标图
	 * @param int $width 目标图宽
	 * @param int $height 目标图高
	 * @param boolean $sync 是否同步图片，主要是给后台用，因为同步图片需要执行服务器shell脚本，这个过程非常慢
	 */
	public function makeThumb($src, $dst, $width = 105, $height = 105, $sync = true){
		if(!is_dir(dirname($dst))){
			mkdir(dirname($dst), 0755, true);
		}
		list($swidth, $sheight, $stype) = getimagesize($src);
		$scaleW = $width/$swidth;
		$scaleH = $height/$sheight;
		if($scaleW < $scaleH){
			$dwidth = $width;
			$dheight = ceil($scaleW * $sheight);
			$w = 0;
			$h = ceil(($height - $dheight) / 2);
		}else{
			$dheight = $height;
			$dwidth = ceil($scaleH * $swidth);
			$w = ceil(($width - $dwidth) / 2);
			$h = 0;
		}
		if (function_exists('imagecreatetruecolor'))
			$image = imagecreatetruecolor($width, $height);
		else $image = imagecreate($width, $height);
		$white = imagecolorallocate($image, 255, 255, 255);
		imagefill($image, 0, 0, $white);
		if($stype == 1) $srcImg = imagecreatefromgif($src);
		elseif($stype == 3) $srcImg = imagecreatefrompng($src);
		else $srcImg = imagecreatefromjpeg($src);
		if (function_exists("imagecopyresampled"))
			imagecopyresampled($image, $srcImg, $w, $h, 0, 0, $dwidth, $dheight, $swidth, $sheight);
		else imagecopyresized($image, $srcImg, $w, $h, 0, 0, $dwidth, $dheight, $swidth, $sheight);
		imagejpeg($image, $dst);
		imagedestroy($srcImg);
		imagedestroy($image);
		
		if($sync){
			exec('/data/webservice/crontab/letianImgRsync/rsync.sh');
		}
	}
}