<?php
/**
 * 2周年庆服务层
 * the last known user to change this file in the repository  <$LastChangedBy: hexin $>
 * @author He xin <hexin@pipi.cn>
 * @version $Id: 2014-01-07 下午2:30:18 hexin $ 
 * @package
 */
class MobileAdvService extends PipiService{
	private static $instance;
	
	/**
	 * 返回MobileAdvService对象的单例
	 * @return MobileAdvService
	 */
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * 保存手机广告地址
	 * @param array $data
	 * @return int
	 */
	public function saveAdv(array $data, $uploadName){
		if(isset($data['adv_id']) && intval($data['adv_id']) < 1){
			return $this->setError(Yii::t('common','Parameter is empty'), 0);
		}
		
		$imgFile = CUploadedFile::getInstanceByName($uploadName);
		if(!empty($imgFile) && $imgFile->getSize() < 2*1024*1024){
			$src = $imgFile->getTempName();
			$dir = 'activites'.DIR_SEP;
			if (!is_dir(IMAGES_PATH.$dir)){
				mkdir(IMAGES_PATH.$dir,0777,true);
			}
			$dst = $dir.uniqid().'.jpg';
			$imageUpload = new PipiImageUpload();
			$imageUpload->makeThumb($src, IMAGES_PATH.$dst, 720, 90);
			$data['image'] = $dst;
		}else{
			if(isset($data['image'])) unset($data['image']);
		}
		
		$model = new MobileAdvModel();
		if(isset($data['adv_id'])){
			$model = $model->findByPk($data['adv_id']);
			if(empty($model)) return $this->setError(Yii::t('common','Data not exists'), 0);
			if(isset($data['image'])) unlink(IMAGES_PATH.$model->image);
		}else{
			if(!isset($data['sort'])) $data['sort'] = 0;
			$data['create_time'] = time();
		}
		$this->attachAttribute($model, $data);
		if(!$model->validate()){
			return $this->setNotices($model->getErrors(), 0);
		}
		$model->save();
		return $model->getPrimaryKey();
	}
	
	/**
	 * 删除手机广告地址
	 * @param max $ids
	 * @return boolean
	 */
	public function deleteAdv($ids){
		$ids = is_array($ids) ? $ids : array(intval($ids));
		if(empty($ids)) return false;
		$mobile = new MobileAdvModel();
		$adv = $mobile->findAll('adv_id in('.implode(',', $ids).')');
		foreach($adv as $ad){
			@unlink(IMAGES_PATH.$ad->image);
		}
		return $mobile->deleteAll('adv_id in('.implode(',', $ids).')');
	}
	
	/**
	 * 获取所有手机广告地址
	 * @return array
	 */
	public function getAllAdv(){
		$model = new MobileAdvModel();
		$cr = $model->getCommandBuilder()->createCriteria();
		$cr -> order = 'sort DESC';
		$all = $model->findAll($cr);
		if($all) return $this->arToArray($all);
		else return array();
	}
}