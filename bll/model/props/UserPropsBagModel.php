<?php
/**
 * 用户道具背包数据访问层
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: guoshaobo $>
 * @author Su qian <suqian@pipi.cn>
 * @version $Id: UserPropsBagModel.php 11191 2013-05-30 01:38:23Z guoshaobo $ 
 * @package model
 * @subpackage props 
 */
class UserPropsBagModel extends PipiActiveRecord {
	public function tableName(){
		return '{{user_props_bag}}';
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
	 * 获取用户购买所有有效的道具
	 * 
	 * @param int $uid 用户ID
	 * @param int $propId 道具ＩＤ
	 * @param int $validTime 道具有效时间， 传递Unix时间缀
	 * @return array
	 */
	public function getUserValidPropsOfBagByPropId($uid,$propId = 0,$validTime = null){
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
	 * 获取用户购买某分类下所有有效的道具
	 * 
	 * @edit guoshaobo 添加了数量大于0的道具
	 * 
	 * @param int $uid 用户ID
	 * @param int $cateId 道具分类ＩＤ
	 * @param int $validTime 道具有效时间， 传递Unix时间缀
	 * @return array
	 */
	public function getUserValidPropsOfBagByCatId($uid,$cateId = 0,$validTime = null, $selectNum = false){
		if($uid <= 0){
			return array();
		}
		
		$option['condition'] = 'uid = :uid';
		$option['params'][':uid'] = $uid;
		
		if($cateId){
			$option['condition'] .= ' AND cat_id = :cat_id';
			$option['params'][':cat_id'] = $cateId;
		}
		if($validTime){
			$option['condition'] .= ' AND (valid_time = 0 OR valid_time >= :vtime)';
			$option['params'][':vtime'] = $validTime ? $validTime : time();
		}
		if($selectNum){
			$option['condition'] .= ' AND num > 0 ';
		}
		$option['order'] = 'valid_time DESC'; 
		return $this->findAll($option);
	}
}

?>