<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: leiwei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: GiftEffectModel.php 11305 2013-05-30 11:14:00Z leiwei $ 
 * @package model
 * @subpackage gift
 */
class GiftEffectModel extends PipiActiveRecord {
	
	/**
	 * @param unknown_type $className
	 * @return GiftCategoryModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{gift_effect}}';
	}
	
	
	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function rules(){
		return array(
			array('gift_id,num,timeout,position','numerical'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'gift_id'=>'礼物ID',
			'num'=>'礼物效果数量',
			'timeout' =>'礼物显示时长',
			'position'=>'显示位置',
		);
	}
	
	/**
	 * 根据礼物效果Id获取礼物效果
	 * @param array $effectIds 礼物效果Id
	 * @return array
	 */
	public function getGiftEffectByEffectIds(array $effectIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('effect_id',$effectIds);
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据礼物Id获取礼物效果
	 * @param array $giftIds 礼物Id
	 * @return array
	 */
	public function getGiftEffectByGiftIds(array $giftIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('gift_id',$giftIds);
		$criteria->order='num asc';
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据数量获取礼物效果
	 * @param int $giftId 礼物Id
	 * @param int $num 礼物数量
	 * @return array
	 */
	public function getGiftEffectByNum($giftId,$num){
		$criteria = $this->getDbCriteria();
		$criteria->condition='gift_id=:gift_id AND num<=:num';
		$criteria->params=array(':gift_id'=>$giftId,':num'=>$num);
		$criteria->order='num DESC';
		$criteria->limit=1;
		return $this->find($criteria);
	}
	
	/**
	 * 根据礼物Id删除礼物效果
	 * @param array $giftIds 礼物Id
	 * @return int
	 */
	public function delGiftEffectByGiftIds($giftIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('gift_id',$giftIds);
		return $this->deleteAll($criteria);
	}
	
	/**
	 * 根据礼物效果Id删除礼物效果
	 * @param array $effectIds 礼物效果Id
	 * @return int
	 */
	public function delGiftEffectByEffectIds(array $effectIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('effect_id',$effectIds);
		return $this->deleteAll($criteria);
	}


}

?>