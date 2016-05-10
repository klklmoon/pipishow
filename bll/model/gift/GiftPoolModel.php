<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: lei wei $>
 * @author lei wei <lei wei@pipi.cn>
 * @version $Id: GiftPoolModel.php 8971 2013-04-22 14:30:27Z lei wei $ 
 * @package model
 * @subpackage gift
 */
class GiftPoolModel extends PipiActiveRecord{
	
	/**
	 * @param unknown_type $className
	 * @return GiftPoolModel
	 */
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	
	public function tableName(){
		return '{{gift_pool}}';
	}
	

	public function getDbConnection(){
		return Yii::app()->db_consume;
	}
	
	public function rules(){
		return array(
			array('value,chance','required'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'value'=>'奖池储金值',
			'chance' =>'A值'
		);
	}
	
	
	/**
	 * 根据奖池金额获取A值
	 * @param int $value
	 * @return array
	 */
	public function getGiftPoolByValue($value){
		$criteria = $this->getDbCriteria();
		$criteria->condition='value<=:value';
		$criteria->params=array(':value'=>$value);
		$criteria->order='value DESC';
		return $this->find($criteria);
	}
	
	/**
	 * 根据奖池金额id删除奖池金额
	 * @param array $ids 奖池金额id
	 * @return boolean 0->失败 1->成功
	 */
	public function delGiftPoolByIds(array $ids){
		$criteria = $this->getDbCriteria();
		$criteria->addInCondition('id',$ids);
		return $this->deleteAll($criteria);
	}
	
	public function getGiftPoolList(){
		$criteria = $this->getDbCriteria();
		return $this->findAll($criteria);
	}
}

?>