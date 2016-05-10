<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */

class AlbumService extends PipiService{
	private static $instance;
	const PHOTO_DIR="album";
	/**
	 * @var PipiImageUpload $imageUpload
	 */
	private static $imageUpload;
	
	/**
	 * 返回AlbumService对象的单例
	 * @return AlbumService
	 */
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	//创建图片唯一文件名
	private function createPhotoName(){
		return "img_".uniqid();
	}
	
	/**
	 * 获取PipiImageUpload对象单例
	 * @return PipiImageUpload
	 */
	private function getImageUpload(){
		if(!self::$imageUpload){
			self::$imageUpload = new PipiImageUpload();
		}
		return self::$imageUpload;
	}
	
	/**
	 * 获取图片的完整网络访问路径
	 * @param int $uid
	 * @param string $filename 原始文件名
	 * @param stting $type 需要的图片类型，默认原图为空，thumb为缩略图
	 * @return string
	 */
	public function getImageUrl($uid, $filename, $type = ''){
		$imageUpload = $this->getImageUpload();
		if($type == 'thumb'){
			$imageUpload->setDir($uid, self::PHOTO_DIR, strstr($filename, '.', true).'_thumb');
			return $imageUpload->getFileUrl();
		}else{
			$imageUpload->setDir($uid, self::PHOTO_DIR, $filename);
			return $imageUpload->getFileUrl();
		}
	}
	
	/**
	 * 获取图片的完整服务器文件路径
	 * @param int $uid
	 * @param string $filename 原始文件名
	 * @param stting $type 需要的图片类型，默认原图为空，thumb为缩略图
	 * @return string
	 */
	public function getImagePath($uid, $filename, $type = ''){
		$imageUpload = $this->getImageUpload();
		if($type == 'thumb'){
			$imageUpload->setDir($uid, self::PHOTO_DIR, strstr($filename, '.', true).'_thumb');
			return $imageUpload->getFile();
		}else{
			$imageUpload->setDir($uid, self::PHOTO_DIR, $filename);
			return $imageUpload->getFile();
		}
	}
	
	/**
	 * 存储用户照片数据
	 * @param int $uid	用户id
	 * @param string $photo 原图文件名
	 * @param string $title 图片标题，可空
	 * @return number 照片数据id
	 */
	private function saveUserPhoto($uid,$photo,$title=null){
		$albumPhotoModel=new AlbumPhotoModel();
		$albumPhotoModel->uid=$uid;
		$albumPhotoModel->image=$photo;
		if($title){
			$albumPhotoModel->title=$title;
		}
		$albumPhotoModel->create_time=time();
		
		$result=$albumPhotoModel->save()?true:false;
		
		return $result ? $albumPhotoModel->getPrimaryKey() : 0;
	}
	
	/**
	 * 上传相册图或上传动态中的图
	 * @param int $uid
	 * @param string $uploadName
	 * @return string
	 */
	public function uploadPhoto($uid, $uploadName){
		$imgFile = CUploadedFile::getInstanceByName($uploadName);
		if(empty($imgFile)) return $this->setNotice(1, '没有上传照片', '');
		if(!in_array($imgFile->getType(), array('image/png', 'image/jpeg','image/pjpeg','image/gif','application/octet-stream')))
			return $this->setNotice(2, '图片文件格式不合法', '');
		if($imgFile->getSize() > 2*1024*1024) return $this->setNotice(3, '图片文件过大', '');
		$tmpPath = $imgFile->getTempName();
		if(empty($tmpPath)) return $this->setNotice(4, '上传出错', '');
		list($width, $height, $type, $attr) = getimagesize($tmpPath);
		if ( $width < 10 || $height < 10 || $width > 3000 || $height > 3000 || $type == 4 ) {
			unlink($tmpPath);
			return $this->setNotice(4, '图片尺寸不符合要求', '');
		}
		
		$filename = $this->createPhotoName();
		$org_filename = $filename.'.'.$imgFile->getExtensionName();
		$imageUpload = $this->getImageUpload();
		$imageUpload->setDir($uid, self::PHOTO_DIR, $org_filename);
		$src = $imageUpload->getFile();
		$imgFile->saveAs($src);
		//生成缩略图, 尺寸是128*88
		$imageUpload->setDir($uid, self::PHOTO_DIR, $filename.'_thumb');
		$dst = $imageUpload->getFile();
		$imageUpload->makeThumb($src, $dst, 128, 88);
		return $org_filename;
	}
	
	/**
	 * 登陆用户上传相册，目前限制的是只允许主播上传
	 * @param int $uid
	 * @param string $uploadName 表单中图片上传的输入框名称，即$_FILE的名称
	 * @return number 数据库保存的主键id
	 */
	public function uploadAlbum($uid, $uploadName){
		$doteyService = new DoteyService();
		$dotey = $doteyService->getDoteysInUids(array($uid));
		if(empty($dotey)) return $this->setNotice(0, '只有主播才能上传照片', 0);
		
		$org_filename = $this->uploadPhoto($uid, $uploadName);
		if(empty($org_filename)) return 0;
		
		$id = $this->saveUserPhoto($uid, $org_filename);
		if($id){
			DynamicService::getInstance()->dynamic($uid, '上传一张照片', DYNAMIC_SOURCE_ALBUM, $org_filename);
		}
		return $id;
	}
	
	/**
	 * 本人删除照片
	 *
	 * @param int $uid	用户id
	 * @param int $photoId 图片id
	 * @return bool 操作是否成功
	 */
	public function delUserPhoto($uid,$photoId){
		$albumPhotoModel=new AlbumPhotoModel();
		$photoRow=$albumPhotoModel->findByPk($photoId);
		if(isset($photoRow->photo_id)&&$photoRow->photo_id==$photoId && $photoRow->uid==$uid){
			$result=$albumPhotoModel->deleteByPk($photoId);
			return $result;
		}else{
			return false;
		}	
	}
	
	/**
	 * 用户获取可分页的照片列表
	 *
	 * @param int $uid	用户id
	 * @param int $page 页号
	 * @param int $pageSize 页记录数
	 * @return array 照片列表,list为数据、page为页号、page_num为总页数
	 */
	public function getAlbumByUser($uid,$page,$pageSize=10){
		$offset = ($page >= 1 ? ($page-1) : 0 ) * $pageSize;
		$albumPhotoModel=new AlbumPhotoModel();
		$records=$albumPhotoModel->getAlbumByUser($uid,$offset,$pageSize);
		$records['list']=$this->arToArray($records['list']);
		
		foreach($records['list'] as &$list){
			$list['thumb'] = $this->getImageUrl($uid, $list['image'], '_thumb');
			$list['image'] = $this->getImageUrl($uid, $list['image']);
		}
		
		$records['page'] = $page;
		$records['page_num'] = ceil($records['count'] / $pageSize);
		return $records;
	}
	
	/**
	 * 用户获取单一照片的具体信息
	 *
	 * @param int $photoId	图片id
	 * @return array 图片记录
	 */
	public function getPhoto($photoId)
	{
		$albumPhotoModel=new AlbumPhotoModel();
		$photo=$albumPhotoModel->findByPk($photoId);
		if($photo){
			$photo = $photo->getAttributes();
			$photo['thumb'] = $this->getImageUrl($photo['uid'], $photo['image'], '_thumb');
			$photo['image'] = $this->getImageUrl($photo['uid'], $photo['image']);
			return $photo;
		}else return array();
	}
}

?>