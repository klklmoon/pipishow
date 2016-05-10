<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: suqian $>
 * @author lei wei <leiwei@pipi.cn>
 * @version $Id: templates.xml 894 2013-07-25 07:55:25Z leiwei $ 
 * @package
 */
class FaceService extends PipiService {
	
	public function saveFace(array $face){
		if (isset($face['id']) && $face['id'] <= 0) {
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		}
		$faceModel = new FaceModel();
		if (isset($face['id'])) {
			$orgfaceModel = $faceModel->findByPk($face['id']);
			if (empty($orgfaceModel)) {
				return $this->setNotice('face', Yii::t('face', 'The face does not exist'), 0);
			}
			$this->attachAttribute($orgfaceModel, $face);
			if (!$orgfaceModel->validate()) {
				return $this->setNotices($orgfaceModel->getErrors(), 0);
			}
			$orgfaceModel->save();
			$insertId = $face['id'];
		} else {
			$this->attachAttribute($faceModel, $face);
			if (!$faceModel->validate()) {
				return $this->setNotices($faceModel->getErrors(), 0);
			}
			$faceModel->save();
			$insertId = $faceModel->getPrimaryKey();
		}
		if($insertId>0){
			$list=self::getAllFace();
			$otherRedisModel=new OtherRedisModel();
			$otherRedisModel->saveFace($list);
		}
		
		if ($insertId && $this->isAdminAccessCtl()){
			if (isset($face['id'])) {
				$op_desc = '编辑 表情('.$insertId.')';
			}else{
				$op_desc = '新增 表情('.$insertId.')';
			}
			$this->saveAdminOpLog($op_desc);
		}
		return $insertId;
	}
	
	/**
	 * 根据id删除表情
	 * @param array $ids
	 * @return boolean
	 */
	public function delFaceByIds(array $ids){
		if(empty($ids))
			return $this->setError(Yii::t('common', 'Parameter is empty'), 0);
		$faceModel=new FaceModel();
		$flag=$faceModel->delFaceByIds($ids);
		if($flag>0){
			$list=self::getAllFace();
			$otherRedisModel=new OtherRedisModel();
			$otherRedisModel->saveFace($list);
		}
		return $flag;
	}
	
	/**
	 * 获取表情列表
	 * @return array
	 */
	public function getAllFace(){
		$faceModel=new FaceModel();
		$data=$faceModel->getAllFace();
		$list=array();
		return $this->arToArray($data);
	}
	
	/**
	 * 从redis中获取表情
	 * @return array
	 */
	public function getFaceFromCache(){
		$otherRedisModel=new OtherRedisModel();
		$list=$otherRedisModel->getFace();
		return $list?$list:array();
	}
	
	/**
	 * 根据id获取表情
	 * @param array $ids
	 * @return array
	 */
	public function getFaceByIds(array $ids){
		if(empty($ids))
			return $this->setError(Yii::t('common', 'Parameter is empty'), array());
		$faceModel=new FaceModel();
		$data=$faceModel->getFaceByIds($ids);
		$list=array();
		$list=$this->arToArray($data);
		return $this->buildDataByIndex($list, 'id');
	}
	
	
	/**
	 * 获取表情类型
	 * @param string $type
	 * @return array
	 */
	public function getFaceType($type=''){
		$typeList = array('common'=> '普通',
			'vip'=>'VIP',
			'aristocrat' =>'贵族',
		);
		$array=array();
		if($type){
			$array[$type]=$typeList[$type];
		}else{
			$array=$typeList;
		}
		return $array;
	}
	
	public function filterFace($content,$isVip=false){
		$faceList=self::getFaceFromCache();
		$contrller = Yii::app()->getController();
		$facePath=$contrller->pipiFrontPath.'/fontimg/express/';
		$content=' '.$content;
		foreach($faceList as $row){
			if(strpos($content,$row['code'])){
				if($row['type']=='common'||($row['type']=='vip'&&$isVip==true)){
					$content=str_replace($row['code'], '<img src="'.$facePath.$row['type'].'/'.$row['image'].'" />', $content);
				}
			}
		}
		return $content;
	}
	
}

?>