<?php
/**
 * @author zhangzhifan <zhangzhifan@pipi.cn> 2010-11-2
 * @link http://show.pipi.cn
 * @copyright Copyright &copy; 2003-2010 show.pipi.cn
 * @license
 */
class GiftStarImgModel extends PipiActiveRecord {
	/**
	 * @param unknown_type $className
	 * @return GiftStarDoteyModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{long_giftstar_img}}';
	}

	public function getDbConnection(){
		return Yii::app()->db_activity;
	}
	
	public function getImgByCondition($offset=0,$pageSize=10, array $condition=array()){
		$criteria = $this->getDbCriteria();
		if (isset($condition['gift_id'])){
			$criteria->compare('gift_id', $condition['gift_id']);
		}
	
		if (isset($condition['order_number'])){
			$criteria->compare('order_number', $condition['order_number']);
		}
		
		if (isset($condition['summary'])){
			$criteria->compare('summary', $condition['summary']);
		}
	
		$criteria->limit=$pageSize;
		$criteria->offset = $offset*$pageSize;
		$criteria->order = 'img_id DESC';
		return $this->findAll($criteria);
	}
	
	public function getImgCountByCondition(array $condition=array()){
		$criteria = $this->getDbCriteria();
		if (!empty($condition['gift_id'])){
			$criteria->compare('gift_id', $condition['gift_id']);
		}
	
		if (isset($condition['order_number'])){
			$criteria->compare('order_number', $condition['order_number']);
		}
		
		if (isset($condition['summary'])){
			$criteria->compare('summary', $condition['summary']);
		}
		
		return $this->count($criteria);
	}
	
	public function getImgByIds(array $giftImgIds)
	{
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('img_id',$giftImgIds);
		return $this->findAll($criteria);
	}
	
	//根据
	public function getGiftImgByCondition($giftId,$orderNumber)
	{
		$criteria = $this->getDbCriteria();
		$criteria->addColumnCondition(array(
			'gift_id'=>	$giftId,
			'order_number'=>$orderNumber
		));
		return $this->getCommandBuilder()->createFindCommand($this->tableName(), $criteria)->queryRow();
	}
}