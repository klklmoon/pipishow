<?php
/**
 * 用户道具背包数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserPropsUseModel.php 10953 2013-05-27 07:47:51Z leiwei $ 
 * @package model
 * @subpackage props 
 */
class UserPropsUseModel extends PipiActiveRecord {
	public function tableName(){
		return '{{user_props_use}}';
	}
	
	/**
	 * @param string $className
	 * @return UserPropsAttributeModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}

	
	/**
	 * 获取用户使用的道具
	 * 
	 * @param int $uid 用户ID
	 * @param int $propId 道具ＩＤ
	 * @param int $validTime 道具有效时间， 传递Unix时间缀
	 * @return array
	 */
	public function getUserValidPropsOfUseByPropId($uid,$propId = 0,$validTime = null){
		if($uid <= 0){
			return array();
		}
		
		$option['condition'] = 'uid = :uid';
		$option['params'][':uid'] = $uid;
		
		if($propId){
			$option['condition'] .= ' AND prop_id = :prop_id';
			$option['params'][':prop_id'] = $propId;
		}
		if($validTime){
			$option['condition'] .= ' AND (valid_time = 0 OR valid_time >= :vtime)';
			$option['params'][':vtime'] = $validTime ? $validTime : time();
		}
		$option['order'] = 'valid_time DESC'; 
		return $this->findAll($option);
	}
	
	/**
	 * 获取用户使用的道具
	 * 
	 * @param int $uid 用户ID
	 * @param int $catId 道具ＩＤ
	 * @param int $validTime 道具有效时间， 传递Unix时间缀
	 * @return array
	 */
	public function getUserValidPropsOfUseByCatId($uid,$catId = 0,$validTime = null){
		if($uid <= 0){
			return array();
		}
		
		$option['condition'] = 'uid = :uid';
		$option['params'][':uid'] = $uid;
		
		if($catId){
			$option['condition'] .= ' AND cat_id = :cat_id';
			$option['params'][':cat_id'] = $catId;
		}
		if($validTime){
			$option['condition'] .= ' AND (valid_time = 0 OR valid_time >= :vtime)';
			$option['params'][':vtime'] = $validTime ? $validTime : time();
		}
		$option['order'] = 'valid_time DESC'; 
		return $this->findAll($option);
	}
	
	/** 
	 * 取得某分类道具下最后一次被使用情况
	 * 
	 * @param int $uid
	 * @param int $catId
	 * @return PipiActiveRecord
	 */
	public function getUserLatestPropsOfUsedByCatId($uid,$catId){
		if($uid <= 0 || $catId <= 0){
			return array();
		}
		$option['select'] = 'use_id,uid,to_uid,prop_id,cat_id,valid_time,create_time';
		$option['condition'] = 'to_uid = :uid AND cat_id = :cat_id ';
		$option['params'] = array(':uid'=>$uid,':cat_id'=>$catId);
		$option['order'] = 'create_time DESC';
		$option['limit'] = 1;
		return $this->find($option);
	}

	
	public function getUserPropsUseCount($uid, $propId, $condition)
	{
		$criteria = $this->getDbCriteria();
		$criteria->select = ' uid,sum(`num`) as nums ';
		$criteria->condition = 'uid = :uid AND prop_id = :prop_id ';
		if(isset($condition['start_time']) && $condition['start_time']>0){
			$criteria->condition .= ' and create_time>= :stime';
		}
		if(isset($condition['end_time']) && $condition['end_time']>0){
			$criteria->condition .= ' and create_time<= :etime';
		}
		$criteria->params = array(':uid'=>$uid,':prop_id'=>$propId,':stime'=>$condition['start_time'],':etime'=>$condition['end_time']);

		$data = $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryRow();
		return $data['num'];
	}

	/**
	 * 根据道具Id获取道具的使用数量
	 * @author leiwei
	 * @param int $uid  用户uid
	 * @param int $catId 道具分类ID
	 * @param array $condition 检索条件
	 * @return int
	 */
	public function getUserPropsUserCountByCatId($uid, $catId, $condition){
		$criteria = $this->getDbCriteria();
		$criteria->select = 'uid,sum(num) as num';
		$criteria->condition='uid = :uid AND cat_id = :cat_id';
		$criteria->params[':uid']=$uid;
		$criteria->params[':cat_id']=$catId;
		if(isset($condition['use_type'])){
			$criteria->condition.=' AND use_type=:use_type';
			$criteria->params[':use_type']=$condition['use_type'];
		}
		if(isset($condition['start_time'])){
			$criteria->condition.=' AND create_time>=:start_time ';
			$criteria->params[':start_time']=strtotime($condition['start_time']);
		}
		if(isset($condition['end_time'])){
			$criteria->condition.=' AND create_time<=:end_time ';
			$criteria->params[':end_time']=strtotime($condition['end_time']);
		}
		$data = $this->getCommandBuilder()->createFindCommand($this->tableName(),$criteria)->queryRow();
		return $data['num'];
	}

}

?>