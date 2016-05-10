<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: GiftAwardModel.php 8971 2013-04-22 14:30:27Z lei wei $ 
 * @package model
 * @subpackage gift
 */
class GiftAwardModel extends PipiActiveRecord{
	
	/**
	 * @param unknown_type $className
	 * @return GiftAwardModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{gift_award}}';
	}
	

	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function rules(){
		return array(
			array('gift_id,type,award,chance','required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'gift_id'=>'礼物ID',
			'type'=>'奖励类型',
			'target_id'=>'奖励Id',
			'chance' =>'概率'
		);
	}
	
	/**
	 * 根据礼物Id获取礼物奖励
	 * @param array $giftId 礼物Id
	 * @return array
	 */
	public function getGiftAwardByGiftId(array $giftIds){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('gift_id',$giftIds);
		return $this->findAll($criteria);
	}
	
	/**
	 * 根据幸运礼物礼物id删除奖品
	 * @param array $ids 幸运礼物礼物id
	 * @return boolean 0->失败 1->成功
	 */
	public function delGiftAwardByGiftId($giftId){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('gift_id',$giftId);
		return $this->deleteAll($criteria);
	}
	
	/**
	 * 根据幸运礼物奖品id删除奖品
	 * @param array $ids 幸运礼物奖品id
	 * @return boolean 0->失败 1->成功
	 */
	public function delGiftAwardByIds(array $ids){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('id',$ids);
		return $this->deleteAll($criteria);
	}
	
	public function searchGiftAwardList(Array $condition = array(),$offset=0,$pageSize=20,$isLimit=true){
		$criteria = $this->getDbCriteria();
		if (!empty($condition['gift_id'])){
			$criteria->compare('gift_id', $condition['gift_id']);
		}
		if (!empty($condition['type'])){
			$criteria->compare('type', $condition['type']);
		}
		if (isset($condition['award']) && $condition['award']>=0){
			$criteria->compare('award', $condition['award']);
		}
		if (isset($condition['target_id']) && $condition['target_id']>=0){
			$criteria->compare('target_id', $condition['target_id']);
		}
		$result['count'] = $this->count($criteria);
		if ($isLimit){
			$criteria->offset = $offset;
			$criteria->limit = $pageSize;
		}
		$result['list'] = $this->findAll($criteria);
		return $result;
	}
}

?>