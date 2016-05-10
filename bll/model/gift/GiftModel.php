<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: supeng $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: GiftModel.php 13929 2013-08-14 09:13:57Z supeng $ 
 * @package model
 * @subpackage gift
 */
class GiftModel extends PipiActiveRecord {
	
	/**
	 * @param unknown_type $className
	 * @return GiftCategoryModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{gift_info}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function rules(){
		return array(
			array('zh_name,en_name','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('zh_name,en_name','required','message'=>'parameter not null'),
			array('zh_name,en_name','unique'),
			array('cat_id,pipiegg,charm,charm_points,dedication,egg_points,sell_nums,sell_grade','numerical','message'=>'parameter must numerical'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'cat_id'=>'礼物分类',
			'zh_name'=>'礼物名称',
			'en_name'=>'礼物英文名称',
			'pipiegg' =>'皮蛋数',
			'charm' =>'魅力值',
			'charm_points' =>'魅力点',
			'dedication' =>'贡献值',
			'egg_points' =>'皮点',
			'sell_nums' =>'出售数量',
			'sell_grade'=>'购买等级'
		);
	}
	
	public function getGiftByIds(array $giftIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('gift_id',$giftIds);
		return $this->findAll($criteria);
	}
	
	public function getGiftByCatIds(array $catIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('cat_id',$catIds);
		return  $this->findAll($criteria);
	}
	
	public function getGiftListByCondition(array $condition){
		$criteria = $this->getDbCriteria();
		if (isset($condition['gift_type']) && (is_numeric($condition['gift_type']) || is_array($condition['gift_type']))){
			$criteria->compare('gift_type', $condition['gift_type']);
			unset($condition['gift_type']);
		}
		if($condition){
			$criteria->addColumnCondition($condition);
		}
		$criteria->order='sort asc,pipiegg asc';
		return $this->findAll($criteria);
	}
	
	public function getGiftInfoByCondition(array $condition){
		$criteria = $this->getDbCriteria();
		if (isset($condition['gift_type']) && (is_numeric($condition['gift_type']) || is_array($condition['gift_type']))){
			$criteria->compare('gift_type', $condition['gift_type']);
			unset($condition['gift_type']);
		}
		$criteria->addColumnCondition($condition);
		return $this->find($criteria);
	}
	
	public function getGiftByCondition($offset=0,$pageSize=10, array $condition=array()){
		$criteria = $this->getDbCriteria();
		if (!empty($condition['zh_name'])){
			$criteria->compare('zh_name', $condition['zh_name'],true);
		}
		
		if (isset($condition['is_display']) && is_numeric($condition['is_display'])){
			$criteria->compare('is_display', $condition['is_display']);
		}
		
		if (isset($condition['shop_type']) && is_numeric($condition['shop_type'])){
			$criteria->compare('shop_type', $condition['shop_type']);
		}
		
		if (isset($condition['gift_type']) && (is_numeric($condition['gift_type']) || is_array($condition['gift_type']))){
			$criteria->compare('gift_type', $condition['gift_type']);
		}
		
		if (isset($condition['cat_id']) && is_numeric($condition['cat_id'])){
			$criteria->compare('cat_id', $condition['cat_id']);
		}
		
		$criteria->limit=$pageSize;
		$criteria->offset = $offset*$pageSize;
		$criteria->order = 'sort asc,update_time DESC';
		return $this->findAll($criteria);
	}
	
	
	public function getGiftCountByCondition(array $condition=array()){
		$criteria = $this->getDbCriteria();
		if (!empty($condition['zh_name'])){
			$criteria->compare('zh_name', $condition['zh_name'],true);
		}
		
		if (isset($condition['is_display']) && is_numeric($condition['is_display'])){
			$criteria->compare('is_display', $condition['is_display']);
		}
		
		if (isset($condition['shop_type']) && is_numeric($condition['shop_type'])){
			$criteria->compare('shop_type', $condition['shop_type']);
		}
		
		if (isset($condition['gift_type']) && (is_numeric($condition['gift_type']) || is_array($condition['gift_type']))){
			$criteria->compare('gift_type', $condition['gift_type']);
		}
		
		if (isset($condition['cat_id']) && is_numeric($condition['cat_id'])){
			$criteria->compare('cat_id', $condition['cat_id']);
		}
		return $this->count($criteria);
	}
	
	public function updateGiftSellNum($gift_id,$sell_nums=1){
		if($gift_id<=0) return false;
		$criteria = $this->getDbCriteria();
		$condition='gift_id=:gift_id';
		$params[':gift_id']=$gift_id;
		return $this->updateCounters(array('sell_nums'=>$sell_nums),$condition,$params);
	}
	
	public function delGiftByGiftIds(array $giftIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('gift_id',$giftIds);
		return $this->deleteAll($criteria);
	}
}

?>